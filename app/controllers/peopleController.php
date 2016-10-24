<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 21/10/14
 * Time: 16:25
 */

/**
 * Class AdminusersController
 * User control for System Administrators (Apprecie) allowing the viewing, editing and creation of users across all portals
 */
class PeopleController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole('Internal');
        $this->setAllowRole('Manager');
        $this->setAllowRole('PortalAdministrator');
    }
    /**
     * default action that shows the list of users across all portals
     */
    public function indexAction()
    {
        $this->view->setLayout('application');
        $this->view->groups = PortalMemberGroup::findBy('ownerId', $this->getAuthenticatedUser()->getUserId());
    }

    /**
     * Provides functionality for creating new users
     */
    public function createAction()
    {
        $this->view->setLayout('application');

        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanViewOrganisationQuotas($thisUser, $thisUser->getOrganisation());

        $role = $thisUser->getActiveRole()->getName();
        $this->view->roleHierarchy = new \Apprecie\Library\Users\RoleHierarchy($role);
        $this->view->quotas = $thisUser->getOrganisation()->getQuotas();
        $this->view->activeUser = $thisUser;
    }

    public function editAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $user = User::resolve($userId);

        $user->canBeSeenBy($this->getAuthenticatedUser());

        $this->view->setLayout('application');
        $user = User::findFirstBy('userId', $userId);

        $this->view->user = $user;
    }

    public function viewUserAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $user = User::resolve($userId);
        $user->canBeSeenBy($this->getAuthenticatedUser());

        $this->view->setLayout('application');

        $userLogin = $user->getUserLogin();
        if ($this->request->getQuery('suspend') == 'true') {
            $userLogin->setSuspended(true);
        } elseif ($this->request->getQuery('suspend') == 'false') {
            $userLogin->setSuspended(false);
        }
        $userLogin->save();

        $quotas = $user->getOrganisation()->getQuotas();
        $roles = $user->getRoles();
        $status = $user->getStatus();

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId());

        $portalUser = $user->getPortalUser();

        $showPortalAccessInfo = true;
        if ($status !== \Apprecie\Library\Users\UserStatus::PENDING) {
            $showPortalAccessInfo = false;
        } else {
            foreach ($roles as $role) {
                switch ($role->getRole()->getName()) {
                    case \Apprecie\Library\Users\UserRole::PORTAL_ADMIN:
                        if (!$portalUser->getRegistrationHash() && $quotas->getPortalAdministratorUsed() >= $quotas->getPortalAdministratorTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::MANAGER:
                        if (!$portalUser->getRegistrationHash() && $quotas->getManagerUsed() >= $quotas->getManagerTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER:
                        if (!$portalUser->getRegistrationHash() && $quotas->getApprecieSupplierUsed() >= $quotas->getApprecieSupplierTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::INTERNAL:
                        if (!$portalUser->getRegistrationHash() && $quotas->getInternalMemberUsed() >= $quotas->getInternalMemberTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER:
                        if (!$portalUser->getRegistrationHash() && $quotas->getAffiliateSupplierUsed() >= $quotas->getAffiliateSupplierTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::CLIENT:
                        if (!$portalUser->getRegistrationHash() && $quotas->getMemberUsed() >= $quotas->getMemberTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                }
            }
        }

        $this->view->showPortalAccessInfo = $showPortalAccessInfo;
        $this->view->user = $user;
        $this->view->userProfile = $user->getUserProfile();
        $this->view->portalUser = $user->getPortalUser();
        $this->view->address = Address::findFirstBy('addressId', $user->getUserProfile()->getHomeAddressId());

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        $this->view->thisUser = $this->getAuthenticatedUser();
        $this->view->suggestedEvents=\Apprecie\Library\Items\ItemSuggestions::getSuggestedItems($this->view->user->getUserId());

        if ($showPortalAccessInfo) {
            $this->view->emailTemplateType = \Apprecie\Library\Mail\EmailTemplateType::getSignupTemplateTypeByRoleName($user->getActiveRole()->getName());
        }
    }

    public function AjaxEditUserAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = User::resolve($userId);
        \Apprecie\Library\Acl\AccessControl::userCanManageUser($this->getAuthenticatedUser(), $user);

        $this->view->disable();

        $emailAddress = $this->request->getPost('emailaddress');
        $reference=$this->request->getPost('reference-code');
        $dob = $this->request->getPost('dob-formatted');
        $interests = $this->request->getPost('interests');
        $diet = $this->request->getPost('diet');
        $communication = $this->request->getPost('communication');


        $user->getPortalUser()->setReference($reference);
        $user->getPortalUser()->save();
        // save the username and password
        $user->getUserLogin()->setUsername($emailAddress);
        $user->getUserLogin()->save();
        $user->setTier($this->request->getPost('tier'));
        // set all the user profile data
        $userProfile = $user->getUserProfile();
        if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
            $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $userProfile->getUserProfile()->setHomeAddressId($addressId);
        };
        $userProfile->setFirstname($this->request->getPost('firstname'));
        $userProfile->setLastname($this->request->getPost('lastname'));
        $userProfile->setEmail($emailAddress);
        $userProfile->setPhone($this->request->getPost('phone'));
        $userProfile->setMobile($this->request->getPost('mobile'));
        $userProfile->setTitle($this->request->getPost('title'));
        if ($dob != null) {
            $userProfile->setBirthday(_myd($dob));
        } else {
            $userProfile->setBirthday(null);
        }
        if ($this->request->getPost('gender') == 'female') {
            $userProfile->setGender(\Apprecie\Library\Users\UserGender::FEMALE); //WORKING
        } else {
            $userProfile->setGender(\Apprecie\Library\Users\UserGender::MALE); //WORKING
        }
        $userProfile->save();

        // set communication preferences
        $contactPreferences = $user->getUserContactPreferences();
        $contactPreferences->setAlertsAndNotifications(false);
        $contactPreferences->setInvitations(false);
        $contactPreferences->setSuggestions(false);
        $contactPreferences->setPartnerCommunications(false);
        $contactPreferences->setUpdatesAndNewsletters(false);

        if (count($communication) > 0) {
            foreach ($communication as $preference) {
                switch ($preference) {
                    case 'alerts':
                        $contactPreferences->setAlertsAndNotifications(true);
                        break;
                    case 'invitations':
                        $contactPreferences->setInvitations(true);
                        break;
                    case 'suggestions':
                        $contactPreferences->setSuggestions(true);
                        break;
                    case 'partners':
                        $contactPreferences->setPartnerCommunications(true);
                        break;
                    case 'news':
                        $contactPreferences->setUpdatesAndNewsletters(true);
                        break;
                }
            }
            $contactPreferences->save();
        }

        $dietWipe = UserDietaryRequirement::findBy('userId', $user->getUserId());
        $dietWipe->delete();
        if (count($diet) != 0) {
            $user->getUser()->addDietaryRequirement($diet);
        }

        $interestWipe = UserInterest::findBy('userId', $user->getUserId());
        $interestWipe->delete();
        if (count($interests) != 0) {
            $user->getUser()->addInterest($interests);
        }
        $user->save();
        echo json_encode(array('result' => 'success'));
    }

    public function ImpersonateUserAction($userId)
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($this->request->get('portalid'));
        $user = User::findFirstBy('userId', $userId);
        $auth = new \Apprecie\Library\Security\Authentication();
        $auth->endImpersonation();
        $auth->impersonateUser($user);
    }

    public function SendSignupAction()
    {
        $this->getRequestFilter()
            ->addRequired('userId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = User::resolve($this->getRequestFilter()->get('userId'));
        \Apprecie\Library\Acl\AccessControl::userCanManageUser($this->getAuthenticatedUser(), $user);

        $this->view->disable();

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId());

        try {
            $portalUser = $user->getPortalUser();

            if($portalUser->sendRegistrationEmail()) {
                _jm('success', _g('email sent'));
            } else {
                _jm('failed', _ms($portalUser));
            }
        } catch (\Exception $ex) {
            _jm('failed', $ex->getMessage());
        }

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
    }

    public function importAction()
    {
        \Apprecie\Library\Acl\AccessControl::userCanCreateUser($this->getAuthenticatedUser(), $this->getAuthenticatedUser()->getOrganisation(), \Apprecie\Library\Users\UserRole::CLIENT);

        if ($this->request->hasFiles()) {
            foreach ($this->request->getUploadedFiles() as $file) {
                $newFile = Assets::getPortalAssetsDir() . $file->getName();
                $file->moveTo($newFile);

                $generateRegistrationHash = false;
                $email = false;
                if ($this->request->getPost('grant-portal-access') == 1) {
                    $generateRegistrationHash = true;
                    if ($this->request->getPost('send-email') == 1) {
                        $email = true;
                    }
                }

                $csv = new \Apprecie\Library\ImportExport\Import\UserImport($newFile, $email, $generateRegistrationHash);
                if ($csv->validateImport()) {
                    if ($csv->commitImport()) {
                        if (count($csv->getSignupFailures()) > 0) {
                            $buffer = '';
                            foreach ($csv->getSignupFailures() as $failure) {
                                $buffer += $failure['failReason'] . '<br/>';
                            }
                            $this->view->message = $buffer;
                        } else {
                            $this->view->message = true;
                        }
                    } else {
                        $this->view->message = $csv->getMessagesString();
                    }
                } else {
                    $this->view->message = $csv->getMessagesString();
                }
                unlink($newFile);
            }
        }
        $this->view->setLayout('application');
    }

    public function getUserTemplateAction($type)
    {
        if ($type === 'excel') {
            \Apprecie\Library\ImportExport\Import\UserImport::downloadTemplate('template', 'excel');
        } else {
            \Apprecie\Library\ImportExport\Import\UserImport::downloadTemplate('template', 'csv');
        }
    }

    public function AjaxCreateUserAction()
    {
        $this->getRequestFilter()
            ->addRequired('role', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $role = $this->request->getPost('role');

        if ($role == '-2') { //@todo GH deal with dual roles better.
            $roles = ['Manager', 'ApprecieSupplier'];
        } else {
            $roles = [$role];
        }

        $user = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanCreateUser($user, $user->getOrganisationId(), $roles);

        $this->view->disable();

        if ($this->request->isPost()) {
            $dob = $this->request->getPost('dob-formatted');
            $portalId = $user->getPortalId();
            $interests = $this->request->getPost('interests');
            $diet = $this->request->getPost('diet');
            $communication = $this->request->getPost('communication');
            $phone = $this->request->getPost('phone');
            $mobile = $this->request->getPost('mobile');
            $portal = Portal::findFirstBy('portalId', $portalId);
            $userEx = new \Apprecie\Library\Users\UserEx();

            $newUser = $userEx->createUserWithProfileAndLogin(
                $this->request->getPost('emailaddress'),
                null,
                $this->request->getPost('firstname'),
                $this->request->getPost('lastname'),
                $this->request->getPost('title'),
                $user->getOrganisationId(),
                $this->request->getPost('reference-code'),
                null,
                $portal->getInternalAlias()
            );

            $newUser->setTier($this->request->getPost('tier'));

            if ($role == '-2') {
                $newUser->addRole('Manager');
                $newUser->addRole('ApprecieSupplier');
            } else {
                $newUser->addRole($role);
            }

            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);
            if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
                $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
                $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
                $newUser->getUserProfile()->setHomeAddressId($addressId);
            }

            if ($this->request->getPost('gender') == 'female') {
                $newUser->getUserProfile()->setGender(\Apprecie\Library\Users\UserGender::FEMALE);
            } else {
                $newUser->getUserProfile()->setGender(\Apprecie\Library\Users\UserGender::MALE);
            }

            if ($dob != null) {
                $newUser->getUserProfile()->setBirthday(_myd($dob));
            }
            $newUser->getUserProfile()->setPhone($phone);
            $newUser->getUserProfile()->setMobile($mobile);
            $newUser->getUserProfile()->save();
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

            $newUser->setCreatingUser($user->getUserId());

            if (count($diet) != 0) {
                $newUser->addDietaryRequirement($diet);
            }

            if (count($interests) != 0) {
                $newUser->addInterest($interests);
            }
            $contactPreferences = $newUser->getUserContactPreferences();

            if (count($communication) > 0) {
                foreach ($communication as $preference) {
                    switch ($preference) {
                        case 'alerts':
                            $contactPreferences->setAlertsAndNotifications(true);
                            break;
                        case 'invitations':
                            $contactPreferences->setInvitations(true);
                            break;
                        case 'suggestions':
                            $contactPreferences->setSuggestions(true);
                            break;
                        case 'partners':
                            $contactPreferences->setPartnerCommunications(true);
                            break;
                        case 'news':
                            $contactPreferences->setUpdatesAndNewsletters(true);
                            break;
                    }
                }
                $contactPreferences->save();
            }

            if ($this->request->getPost('user-lookup-value') != null) { //a portal admin might not have an owner
                $newUser->setChildOf($this->request->getPost('user-lookup-value'));
            }
            $newUser->save();

            echo json_encode(
                [
                    'portalId' => $portal->getPortalId(),
                    'role' => $newUser->getRoles()[0]->getRole()->getName(),
                    'userId' => $newUser->getUserId()
                ]
            );
        }
    }

    public function AjaxGenerateRegistrationLinkAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = User::resolve($userId);

        \Apprecie\Library\Acl\AccessControl::userCanCreateUser($this->getAuthenticatedUser(), $user->getOrganisationId(), $user->getRoles());

        $this->view->disable();

        $user->clearStaticRoleData();

        $portalId = $user->getPortalId();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);

        if ($user->getStatus() !== \Apprecie\Library\Users\UserStatus::PENDING) {
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
            return;
        }

        if ($user->getPortalUser()->getRegistrationHash()) {
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
            return;
        }

        $organisation = $user->getUser()->getOrganisation();

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        // if the user is a client member we need to remove a licence at this point.
        $quotas = $organisation->getQuotas();
        $quotas->setTransaction($transaction);
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            switch ($role->getRole()->getName()) {
                case \Apprecie\Library\Users\UserRole::PORTAL_ADMIN:
                    $quotas->consumePortalAdministratorQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::MANAGER:
                    $quotas->consumeManagerQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER:
                    $quotas->consumeApprecieSupplierQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::INTERNAL:
                    $quotas->consumeInternalMemberQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER:
                    $quotas->consumeAffiliateSupplierQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::CLIENT:
                    $quotas->consumeMemberQuota(1);
                    break;
            }
            if ($quotas->hasMessages() || !$quotas->update()) {
                try {$transaction->rollback();} catch(\Exception $ex){};

                echo json_encode(
                    array('result' => 'failed', 'message' => 'There are not enough licences available on this portal.')
                );
                return;
            }
        }

        $portalUser = $user->getPortalUser();
        $portalUser->setTransaction($transaction);
        $portalUser->setRegistrationHash(
            (new \Apprecie\Library\Security\Authentication())->generateRegistrationToken()
        );
        $portalUser->update();

        if ($portalUser->hasMessages()) {
            $transaction->rollback();
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
        } else {
            $transaction->commit();
            echo json_encode(
                [
                    'result' => 'success',
                    'registration' => \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                        $portalId,
                        'signup',
                        'index',
                        [$portalUser->getRegistrationHash()]
                    )
                ]
            );
        }
    }

    public function AjaxRemoveRegistrationLinkAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = User::resolve($userId);

        \Apprecie\Library\Acl\AccessControl::userCanCreateUser($this->getAuthenticatedUser(), $user->getOrganisationId(), $user->getRoles());

        $this->view->disable();

        $user->clearStaticRoleData();

        $portalId = $user->getPortalId();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);

        if ($user->getStatus() !== \Apprecie\Library\Users\UserStatus::PENDING) {
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
            return;
        }

        $userEx = new \Apprecie\Library\Users\UserEx();
        if (!$userEx->canDeleteOrDeactivate($user)) {
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
            return;
        }

        $organisation = $user->getOrganisation();

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        // if the user is a client member we need to release a licence at this point.
        $quotas = $organisation->getQuotas();
        $quotas->setTransaction($transaction);
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            switch ($role->getRole()->getName()) {
                case \Apprecie\Library\Users\UserRole::PORTAL_ADMIN:
                    $quotas->consumePortalAdministratorQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::MANAGER:
                    $quotas->consumeManagerQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER:
                    $quotas->consumeApprecieSupplierQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::INTERNAL:
                    $quotas->consumeInternalMemberQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER:
                    $quotas->consumeAffiliateSupplierQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::CLIENT:
                    $quotas->consumeMemberQuota(-1);
                    break;
            }
            if (!$quotas->update()) {
                $user->appendMessageEx($quotas);
            }
        }

        $user->setTransaction($transaction);
        $user->getPortalUser()->setRegistrationHash(null);
        $user->getPortalUser()->update();

        if ($user->hasMessages()) {
            $transaction->rollback();
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
        } else {
            $transaction->commit();
            echo json_encode(
                array('result' => 'success', 'message' => '')
            );
        }
    }

    public function ownerLookupAction($roleName)
    {//@todo GH this needs some thought,  as it is not secure to provide the names of higher roles, or potentially other peoples clients
        $this->getRequestFilter()
            ->addNonRequestRequired('roleName', $roleName, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $thisUser = $this->getAuthenticatedUser();
        $organisation = $thisUser->getOrganisation();

        if($roleName == \Apprecie\Library\Users\UserRole::CLIENT || $roleName == \Apprecie\Library\Users\UserRole::CONTACT) {
            return;
        }

        \Apprecie\Library\Acl\AccessControl::userCanViewOrganisationQuotas($thisUser, $organisation);

        $users = $organisation->getUsersInRole($roleName, $organisation->getOrganisationId());
        $usersArray = [];
        foreach ($users as $user) {
            if (!$user->getIsDeleted()) {
                $userData = $user->getUserProfile();
                $userData->userId = $user->getUserId();
                $usersArray[] = $userData->toArrayEx(array('userId'));
            }
        }
        echo json_encode($usersArray);
    }

    public function AjaxDeleteUserAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = User::resolve($userId);

        \Apprecie\Library\Acl\AccessControl::userCanDeleteUser($this->getAuthenticatedUser(), $user->getOrganisationId(), $user->getRoles());
        \Apprecie\Library\Acl\AccessControl::userCanManageUser($this->getAuthenticatedUser(), $user);

        $status = 'failed';
        $message = _g('Invalid request');

        try {

            $userEx = new \Apprecie\Library\Users\UserEx();
            if (!$userEx->deleteUser($user)) {
                $message = _g('The process failed');
                throw new \Exception($userEx->getMessagesString());
            } else {
                $groups = $user->getRelated('groups');
                if ($groups->count() > 0) {
                    foreach ($groups as $group) {
                        $group->removeUser($userId);
                    }
                }

                $status = 'success';
                $message = _g('The user was deleted');
            }

        } catch (\Exception $ex) {
            $this->logActivity('Failed to delete user', $ex->getMessage());
        }

        _jm($status, $message);
    }

    public function AjaxDeactivateAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = User::resolve($userId);

        \Apprecie\Library\Acl\AccessControl::userCanManageUser($this->getAuthenticatedUser(), $user);

        if ($user->deactivate()) {
            $status = 'success';
            $message = _g('The user was deactivated and the quota was credited');
        } else {
            $status = 'failed';
            $message = _ms($user); //_g('It was not possible to deactivate the user');
        }

        _jm($status, $message);
    }

    public function AjaxActivateAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = User::resolve($userId);

        \Apprecie\Library\Acl\AccessControl::userCanManageUser($this->getAuthenticatedUser(), $user);

        $user = User::resolve($userId);
        $user->setStatus('active');
        $quotas = $user->getOrganisation()->getQuotas();

        $quotaConsume = false;
        switch ($user->getActiveRole()->getName()) {
            case 'Client':
                $quotaConsume = $quotas->consumeMemberQuota();
                break;
            case 'Manager':
                $quotaConsume = $quotas->consumeManagerQuota();
                break;
            case 'Internal':
                $quotaConsume = $quotas->consumeInternalMemberQuota();
                break;
            case 'ApprecieSupplier':
                $quotaConsume = $quotas->consumeApprecieSupplierQuota();
                break;
            case 'AffiliateSupplier':
                $quotaConsume = $quotas->consumeAffiliateSupplierQuota();
                break;
            case 'PortalAdministrator':
                $quotaConsume = $quotas->consumePortalAdministratorQuota();
                break;
            case '-2' :
            {
                $quotaConsume = $quotas->consumeApprecieSupplierQuota(1);
                if ($quotaConsume !== false) {
                    $quotaConsume = $quotas->consumeManagerQuota(1);
                }

                break;
            }
        }

        if ($quotaConsume === false) {
            $status = 'failed';
            $message = _g('Not enough quota to activate this user');
        } else {
            try {
                $user->save();
                $quotas->update();
                $status = 'success';
                $message = _g('The user was successfully activated');
            } catch (Exception $ex) {
                $status = 'failed';
                $message = $ex->getMessage();
            }
        }

        _jm($status, $message);
    }

    public function AjaxSearchAction($pageNumber = 1)
    {
        $usersArray = array();
        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');
        $reference = $this->request->getPost('reference');
        $role = $this->request->getPost('roleName');
        $accountPending = $this->request->getPost('accountPending');
        $accountActive = $this->request->getPost('accountActive');
        $accountDeactivated = $this->request->getPost('accountDeactivated');
        $login = $this->request->getPost('login');
        $group = $this->request->getPost('group');
        $suggestOnly = $this->request->getPost('suggestionsOnly');

        $metricsOnly = false;
        if ($this->request->getPost('metricsOnly') == 'true') {
            $metricsOnly = true;
        }

        $searchUsers = [];
        // If a specific role has been specified
        $thisUser = $this->getAuthenticatedUser();

        //secure because only considers dependents
        if ($thisUser->getActiveRole()->getName() == "PortalAdministrator") {
            $users = \Apprecie\Library\SearchFilters\Users\UserSearchFilterUtility::userSearch(null, null, null, $thisUser->getOrganisationId());
            foreach ($users as $user) {
                if ($role != 'All') {
                    if ($user->hasRole($role)) {
                        $searchUsers[] = $user;
                    }
                } else {
                    $searchUsers[] = $user;
                }
            }
        } else {
            $searchUsers = $thisUser->resolveChildren($role);
        }
        $portalGroup = null;
        if ($group != 'all') {
            $portalGroup = PortalMemberGroup::findFirstBy('groupId', $group);
        }

        foreach ($searchUsers as $result) {
            if (!$result->getIsDeleted()) {
                $suggestedEvents=count(\Apprecie\Library\Items\ItemSuggestions::getSuggestedItems($result->getUserId()));
                if ($result->getUserId() != $thisUser->getUserId() && ($portalGroup == null || $portalGroup->hasUser($result->getUserId())) && (($suggestOnly=='true' && $suggestedEvents!=0) || $suggestOnly=='false')){
                    if ($name) {
                        if (stripos($result->getUserProfile()->getFullName(), $name) === false) {
                            continue;
                        }
                    }

                    // only a client can see their contacts
                    if (!$thisUser->hasRole(\Apprecie\Library\Users\UserRole::CLIENT) && $result->hasRole(\Apprecie\Library\Users\UserRole::CONTACT)) {
                        continue;
                    }

                    $account = [];
                    if ($accountActive == 'true') {
                        $account[] = 'active';
                    }
                    if ($accountDeactivated == 'true') {
                        $account[] = 'deactivated';
                    }
                    if ($accountPending == 'true') {
                        $account[] = 'pending';
                    }

                    if (!in_array($result->getStatus(), $account)) {
                        continue;
                    }

                    if ($login == 'enabled' && ($result->getUserLogin()->getSuspended(
                            ) == true || $result->getUserLogin()->getPassword() == 'pending')
                    ) {
                        continue;
                    }
                    if ($login == 'suspended' && $result->getUserLogin()->getSuspended() == false) {
                        continue;
                    }

                    if ($email) {
                        if (stripos($result->getUserProfile()->getEmail(), $email) === false) {
                            continue;
                        }
                    }
                    if ($reference) {
                        if (stripos($result->getPortalUser()->getReference(), $reference) === false) {
                            continue;
                        }
                    }

                    $results = [];

                    if ($metricsOnly === false) {
                        $results['profile'] = $result->getUserProfile()->toArray();
                        if($results['profile']['firstname']=="" && $results['profile']['lastname']==""){
                            $results['profile']['firstname']=_g("Not Provided");
                        }
                        if($results['profile']['email']==""){
                            $results['profile']['email']=_g("Not Provided");
                        }
                        if ($result->getUserLogin()->getSuspended() == 1) {
                            $results['login'] = _g('Suspended');
                        } elseif ($result->getUserLogin()->getPassword() == 'pending') {
                            if ($result->getPortalUser()->getRegistrationHash()) {
                                $results['login'] = _g('Pending');
                            } else {
                                $results['login'] = _g('Unregistered');
                            }
                        } elseif ($result->getUserLogin()->getSuspended() == null || $result->getUserLogin(
                            )->getSuspended() == '0'
                        ) {
                            $results['login'] = _g('Enabled');
                        }

                        switch ($result->getStatus()) {
                            case 'active':
                                $results['account'] = _g('Active');
                                break;
                            case 'deactivated':
                                $results['account'] = _g('Deactivated');
                                break;
                            case 'pending':
                                if ($result->getPortalUser()->getRegistrationHash()) {
                                    $results['account'] = _g('Pending');
                                } else {
                                    $results['account'] = _g('Unregistered');
                                }
                                break;
                        }
                        $groups = $result->getGroupsMemberOf();
                        $groupstring = '';
                        foreach ($groups as $group) {
                            $groupstring .= '<a style="display:block;" href="/groups/groupusers/' . $group->getGroupId(
                                ) . '">' . $group->getGroupName() . '</a>';
                        }
                        $results['groups'] = $groupstring;
                        $results['suggestedEvents']=$suggestedEvents;
                        $results['userid'] = $result->getUserId();
                        $results['image'] = Assets::getUserProfileImageContainer($result->getUserId());
                        if($result->getUserReference()===null){
                            $results['reference'] = '';
                        }
                        else{
                            $results['reference'] = $result->getUserReference();
                        }
                        $results['organisation'] = $result->getOrganisation()->getOrganisationName();

                        $role = '';
                        $firstRole = true;
                        foreach ($result->getRoles() as $roleLink) {
                            if ($firstRole === true) {
                                $userRole = new \Apprecie\Library\Users\UserRole($roleLink->getRole()->getName());
                                $role .= $userRole->getText();
                                $firstRole = false;
                            } else {
                                $userRole = new \Apprecie\Library\Users\UserRole($roleLink->getRole()->getName());
                                $role .= ' / ' . $userRole->getText();
                            }

                        }

                        $results['role'] = $role;
                    } else {
                        $results['userid'] = $result->getUserId();
                    }

                    $usersArray[] = $results;
                }
            }
        }

        if (count($usersArray) == 0) {
            _jm('failed', _g('No people were found'));
        } else {
            $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
                array(
                    "data" => $usersArray,
                    "limit" => 20,
                    "page" => $pageNumber
                )
            );

            $page = $paginator->getPaginate();
            echo json_encode($page);
        }

    }
}

