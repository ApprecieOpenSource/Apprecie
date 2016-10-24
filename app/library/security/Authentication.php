<?php

namespace Apprecie\Library\Security;

use Apprecie\Library\DBConnection;
use Apprecie\Library\Http\Client\Request;
use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Request\Url;
use Apprecie\Library\Tracing\ActivityTraceTrait;
use Apprecie\Library\Users\ApprecieUser;
use Apprecie\Library\Users\UserEx;
use Apprecie\Library\Users\UserRole;
use Apprecie\Library\Users\UserStatus;
use Phalcon\DI;
use Phalcon\Security;

/**
 * Provides the basic login / logout / authorise functionality for web users
 * Class Security
 * @package Apprecie\Library\Security
 */
class Authentication extends PrivateMessageQueue
{
    use ActivityTraceTrait;
    use DBConnection;

    private static $_previousRole = null;

    public static function requestSourceSafe()
    {
        $auth = DI::getDefault()->get('auth');
        if($auth->getAuthenticatedUser(true) && !$auth->isImpersonating()) {
            $session = DI::getDefault()->get('session');

            if($session->get('AUTHENTICATED_USER_IP') != IPTools::getClientIPAddress()) {
                $log = new \ActivityLog();
                $log->logSecurityEvent('Session IP mismatch', 'The IP address of this session has changed and the user will be asked to re-authenticate');
                $auth->logoutUser();
                $response = DI::getDefault()->get('response');
                $response->redirect('/error/ipchange');
                $response->send();
            }
        }
    }

    public function throttleLogin($periodInSeconds = 180, $threshold = 10, $delaySeconds = 5)
    {
        $fails = $this->getFailedLoginCount($periodInSeconds);
        if($fails >= $threshold) {
            sleep($delaySeconds);
        }
    }

    public function getFailedLoginCount($periodInSeconds = 180)
    {
        $result = $this->getDbAdapter()->query("SELECT COALESCE(COUNT(activity)) AS failed from securitylog WHERE activity = 'login failed' AND datetime > DATE_SUB(NOW(), INTERVAL " . $periodInSeconds . " second)");

        return $result->fetchArray()['failed'];
    }

    public function useCaptcha($periodInSeconds = null, $threshold = null)
    {
        $config = DI::getDefault()->get('config');
        if ($periodInSeconds == null) {
            $periodInSeconds = $config->captcha->failedPeriod;
        }
        if ($threshold == null) {
            $threshold = $config->captcha->failedThreshold;
        }

        $fails = $this->getFailedLoginCount($periodInSeconds);
        if ($fails >= $threshold) {
            return true;
        }

        return false;
    }

    public function validateCaptcha($response)
    {
        $provider = Request::getProvider();
        $provider->setBaseUri('https://www.google.com/recaptcha/api/siteverify');
        $provider->header->set('Accept', 'application/json');
        $result = $provider->post('', array(
            'secret' => '6LdF4QwTAAAAAA41WG3R3GSCFNn2S1YOAvNRl4mp',
            'response' => $response
        ));

        if (isset(json_decode($result->body)->success)) {
            $success = json_decode($result->body)->success;
        } else {
            $success = false;
        }

        if ($success === false) {
            $this->logSecurityEvent('login failed', 'CAPTCHA failed');
        }

        return $success;
    }

    /**
     * Checks the provided authentication details.
     * If username in found, in active portal, and hash(password)  == $password
     * the UserLogin is returned.
     *
     * Expecting a PortalUser?   try loginUser() instead
     * or do $login->getPortalUser();
     *
     * @param $username
     * @param $password
     * @return bool|\UserLogin  false on failure, else the associated login entity for username
     */
    public function authoriseUser($username, $password)
    {
        $login = \UserLogin::findFirstBy('username', $username);
        if ($login != null) {
            if ((new Security())->checkHash($password, $login->password)) {
                return $login;
            }
        } else {
            $this->appendMessageEx('The username or password is incorrect');
        }

        return false;
    }

    /**
     * Makes $user the active authenticated user, keeping a restorable reference to the current active session.
     * The actual session owner can always be obtained  by calling getSessionOwner();
     *
     *
     * @param $user ApprecieUser|int The user to impersonate
     * @return bool
     */
    public function impersonateUser($user, $noRedirect = false)
    {
        if ($this->isImpersonating()) {
            $this->appendMessageEx('An impersonation is already in progress.  Please end.');
            return false;
        }

        $user = \User::resolve($user);

        $this->logSecurityEvent('Impersonation Starting', 'The authenticated user is about to start impersonating user id ' . $user->getUserId());
        $this->session->remove('AUTHENTICATED_USER_ROLE');

        $this->session->set('SESSION_OWNER', $this->getAuthenticatedUser(true)); //GH allows impersonation operations off portal
        $this->session->set('AUTHENTICATED_USER', $user);

        $roles = $user->getRoles();
        if ($roles->count() > 1) {
            foreach($roles as $role) {
                if($role->getRole()->getName() == UserRole::PORTAL_ADMIN) {
                    continue;
                } else {
                    $defaultRole = $role->getRole();
                    break;
                }
            }
        } else {
            $defaultRole = $roles[0]->getRole();
        }

        if(! $noRedirect) {
            $this->response->redirect(Url::getConfiguredPortalAddress($user->getPortalId(), $defaultRole->getDefaultController() . '/' . $defaultRole->getDefaultAction()));
            $this->response->send();
            exit(0);
        }
    }

    public function endImpersonation($noRedirect = false)
    {
        if ($this->isImpersonating()) {
            $this->session->set('AUTHENTICATED_USER', $this->session->get('SESSION_OWNER'));
            $this->session->remove('SESSION_OWNER');
            $this->session->remove('AUTHENTICATED_USER_ROLE');
            $this->logSecurityEvent('Impersonation Ending', 'The authenticated user is about to stop impersonating');

            if(! $noRedirect) {
                $this->response->redirect(
                    Url::getConfiguredPortalAddress($this->session->get('AUTHENTICATED_USER')->getPortalId(), 'dashboard')
                );
                $this->response->send();
                exit(0);
            }
        }
    }

    public function isImpersonating()
    {
        return $this->session->has('SESSION_OWNER');
    }

    /**
     * Note that this method returns the user underlying an impersonation (i.e the owner of the session)
     * Or simply the authenticated user if not impersonating.
     *
     * Note that this is in the case of no impersonation the same as calling getAuthenticatedUser(true) passing
     * the blind requirement to still return user if the source portal is wrong. As such this method must never be
     * used for automated security resolution.
     */
    public function getSessionOwner()
    {
        if ($this->session->has('SESSION_OWNER')) {
            $owner = $this->session->get('SESSION_OWNER');

            if($owner instanceof \User) {
                return $owner;
            }
        }

        return $this->getAuthenticatedUser(true); //as we want the owner directly, likely off portal, allow blind
    }

    /**
     * Authenticates the username / password pair, returning false if invalid.
     *
     * On success stores the PortalUser as the active authenticated user and returns PortalUser
     *
     * @param $username
     * @param $password
     * @return bool|\User
     */
    public function loginUser($username, $password)
    {
        $this->logoutUser(false);

        $config = DI::getDefault()->get('config');
        $this->throttleLogin
        (
            $config->authentication->failedPeriod,
            $config->authentication->failedThreshold,
            $config->authentication->failedDelay
        );

        if ($this->getDI()->get('portal')->getSuspended()) {
            $this->appendMessageEx(_g('This portal is suspended'));
            $this->logSecurityEvent('login failed', 'This portal is suspended');
            return false;
        }

        $login = $this->authoriseUser($username, $password);
        if (!$login) {
            $this->appendMessageEx(_g('The username or password is incorrect'));
            $this->logSecurityEvent('login failed', 'The username or password is incorrect');
            return false;
        }

        if ($login->suspended == true) {
            $this->appendMessageEx(_g('The login was authenticated but the login is suspended'));
            $this->logSecurityEvent('login failed', 'The login was authenticated but the login is suspended');
            return false;
        }

        $user = $login->getUser();
        if (!$user instanceof \User) {
            return false;
        }

        if ($user->getOrganisation()->getSuspended()) {
            $this->appendMessageEx(_g('This Organisation is suspended'));
            $this->logSecurityEvent('login failed', 'This Organisation is suspended');
            return false;
        }

        if ($user->getStatus() == UserStatus::DEACTIVATED) {
            $this->appendMessageEx(_g('The login is deactivated'));
            $this->logSecurityEvent('login failed', 'The login is deactivated');
            return false;
        }

        if ($user->getIsDeleted()) {
            $this->appendMessageEx(_g('This user has been deleted'));
            $this->logSecurityEvent('login failed', 'This user has been deleted');
            return false; //should not be possible as login should have been removed,  better safe.
        }

        $accountLockState = AccountLock::checkAccountLock($user);
        if ($accountLockState) { //account lock exists
            $this->logSecurityEvent('login failed', 'This user is logged in elsewhere');
            $response = DI::getDefault()->get('response');
            $response->redirect(
                Url::getConfiguredPortalAddress(
                    $user->getPortal(),
                    'error',
                    'accountlock'
                )
            );
            $response->send();
        }

        session_regenerate_id(); //make session fixation tricky.
        $this->session->remove('AUTHENTICATED_USER_ROLE');
        $this->session->set('AUTHENTICATED_USER', $user);
        $this->session->set('AUTHENTICATED_USER_IP', IPTools::getClientIPAddress());
        $this->logSecurityEvent('login', 'The user has successfully logged in');
        AccountLock::addAccountLock();

        $this->session->set('TERMS_UNACCEPTED', static::getUnacceptedLegalDocuments($this->getAuthenticatedUser()));

        return $user;
    }

    public function userIsInteractive($user)
    {
        $user = \User::resolve($user);

        if ($user->getOrganisation()->getSuspended()) {
            $this->appendMessageEx(_g('This Organisation is suspended'));
            return false;
        }

        if ($user->getStatus() == UserStatus::DEACTIVATED) {
            $this->appendMessageEx(_g('The login is deactivated'));
            return false;
        }

        if ($user->getStatus() == UserStatus::PENDING) {
            $this->appendMessageEx(_g('The user is not registered'));
            return false;
        }

        if ($user->getIsDeleted()) {
            $this->appendMessageEx(_g('This user has been deleted'));
            return false; //should not be possible as login should have been removed,  better safe.
        }

        $lastPortal = (new UserEx())->getActiveQueryPortal();
        UserEx::ForceActivePortalForUserQueries($user->getPortalId());
        if ($user->getUserLogin() != null) {
            if ($user->getUserLogin()->suspended == true) {
                $this->appendMessageEx(_g('The login is suspended'));
                UserEx::ForceActivePortalForUserQueries($lastPortal);
                return false;
            }
        } else {
            $this->appendMessageEx(_g('This user does not have an interactive login)'));
            UserEx::ForceActivePortalForUserQueries($lastPortal);
            return false;
        }

        UserEx::ForceActivePortalForUserQueries($lastPortal);
        return true;
    }

    /**
     * Removes the stored login from the session.
     */
    public function logoutUser($destroySession = true)
    {
        if ($this->isImpersonating()) {
            $this->endImpersonation();
        } else {
            AccountLock::removeAccountLock(); //remove account lock if not impersonating
            $this->logSecurityEvent('logout', 'This user has logged out');
            $this->session->remove('AUTHENTICATED_USER');
            $this->session->remove('AUTHENTICATED_USER_IP');
            $this->session->remove('TERMS_UNACCEPTED');
            $this->session->remove('AUTHENTICATED_USER_ROLE');
            session_regenerate_id($destroySession); //make session fixation tricky.
        }
    }

    /**
     * Returns the active (first) role of the currently logged in user, else null if no user or no role.
     * @return null|string null if no role found, else the name of the role
     */
    public function getSessionActiveRole()
    {
        $sessionUser = $this->getAuthenticatedUser();
        if ($sessionUser === false) {
            return null;
        }

        return $sessionUser->getActiveRole()->getName();
    }

    /**
     * return the logged in PortalUser
     *
     * cheat sheet  ->getProfile()   ->getLogin()  ->getReference()
     *
     * You almost always want the profile
     * but an anonymous user will have no profile,  and no login, and some user might have a profile but no login
     * all users with a login should have profile.
     *     *
     * @param bool $blind set to true to prevent on correct portal checks.
     * @return \User|bool
     */
    public function getAuthenticatedUser($blind = false)
    {
        if (($activeUser = $this->session->get('AUTHENTICATED_USER')) == null) {
            return false;
        }

        if (!$blind) {
            if (!$activeUser instanceof \User) {
                die("HARD FAULT -  User is not a User???");
            }

            if ($activeUser->getPortalId() != DI::getDefault()->get('portal')->getPortalId()) {
                $this->appendMessageEx('The authenticated user does not belong to this portal');
                return false;
            }
        }

        return $activeUser;
    }

    /**
     * checks role membership for the ApprecieUser
     *
     * @param ApprecieUser $userElement any user element supporting the ApprecieUser interface
     * @param string $role the name of an actual defined role
     * @return bool true or false
     */
    public function userHasRole(ApprecieUser $userElement, $role)
    {
        return $userElement->getUser()->hasRole($role);
    }

    /**
     * Checks role membership for the current authenticated user  v
     *
     * @param string $role the name of an actual defined role
     * @return bool true or false
     */
    public function sessionHasRole($role)
    {
        $user = $this->getAuthenticatedUser();
        if ($user === false) {
            return false;
        }

        return $this->userHasRole($user, $role);
    }

    public function generateRegistrationToken()
    {
        return md5(uniqid(rand(), true));
    }

    public static function getUnacceptedLegalDocuments($user)
    {
        $user = \User::resolve($user);

        $unacceptedTermsArray = array();

        $roles = $user->getRoles();
        foreach ($roles as $role) {
            //look for portal-specific terms first
            $termsSettings = \TermsSettings::query()
                ->join('Terms')
                ->where('portalId=:1:')
                ->andWhere('roleId=:2:')
                ->andWhere('state=1')
                ->bind([1 => $user->getPortalId(), 2 => $role->getRoleId()])
                ->execute();

            if (!count($termsSettings)) { //if no portal-specific terms is available, continue looking for global terms
                $termsSettings = \TermsSettings::query()
                    ->join('Terms')
                    ->where('portalId is null')
                    ->andwhere('roleId=:1:')
                    ->andWhere('state=1')
                    ->bind([1 => $role->getRoleId()])
                    ->execute();
            }

            if (!count($termsSettings)) {
                //no role-specific terms document is available
            } else {
                foreach ($termsSettings as $termsSetting) {
                    $userTerms = \UserTerms::query()
                        ->where('userId=:1:')
                        ->andWhere('termsId=:2:')
                        ->bind([1 => $user->getUserId(), 2 => $termsSetting->getTermsId()])
                        ->execute();

                    if (!count($userTerms) && !in_array($termsSetting->getTermsId(), $unacceptedTermsArray)) {
                        $unacceptedTermsArray[] = $termsSetting->getTermsId();
                    }
                }
            }
        }

        if (count($unacceptedTermsArray)) {
            return $unacceptedTermsArray;
        } else {
            return null;
        }
    }

    /**
     * To be called before any system forced role change for the active session.
     * Stores the current role, and marks the chnage so that the user can be informed
     */
    public function registerRoleAutoSwitch()
    {
        if(static::$_previousRole == null) { //enforce we always store first,  in case of multiple changes
            static::$_previousRole = (new UserRole($this->getSessionActiveRole()))->getText();
        }
    }

    /**
     * @return bool If a system forced role change has occurred true
     */
    public function getRoleHasAutoSwitched()
    {
        return static::$_previousRole != null;
    }
}