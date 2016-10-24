<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/10/14
 * Time: 19:47
 */

namespace Apprecie\Library\Controllers;

use Apprecie\Library\DBConnection;
use Apprecie\Library\Provisioning\PortalStrap;
use Apprecie\Library\Request\Url;
use Apprecie\Library\Security\Authentication;
use Apprecie\Library\Security\CSRFCheckTrait;
use Apprecie\Library\Tracing\ActivityTraceTrait;
use Apprecie\Library\Utility\UtilityTrait;
use Phalcon\Mvc\Controller;

/**
 * Provides a generic base controller with built in and consistent methods and workflow for handling
 * authorisation in derived controllers.
 *
 * Introduces the setupController() method which should be overridden and used to
 *
 *
 * Class ApprecieControllerBase
 * @package Apprecie\Library\Controllers
 */
abstract class ApprecieControllerBase extends Controller
{
    use CSRFCheckTrait;
    use ActivityTraceTrait;
    use DBConnection;
    use UtilityTrait;

    protected $_noSessionRedirectLocation = 'login';
    protected $_allowRoles = array();
    protected $_denyRoles = array();
    protected $_controllerRoleFailRedirect = 'login';
    protected $_paramFilter = null;
    protected $_allowedPortals = array();
    protected $_deniedPortals = array();

    protected function onConstruct()
    {
        $this->view->setVar("t", $this->_getTranslation());
    }

    protected function _getTranslation()
    {
        return $this->getDI()->get('translation');
    }

    protected function beforeExecuteRoute($dispatcher)
    {
        $this->setupController();
        $this->checkControllerLevelPermissions();
    }

    /**
     * Override this method for setting up controller level permissions
     */
    protected function setupController()
    {
    }

    /**
     * If the current user does not have an authenticated session the user will be redirected during initialisation
     * to $location.
     *
     * This property must be set before the parent initialize() event executes so call in onConstruct() or in
     * an overridden initialise() ensuring the parent::initialize() is called after and not before this property is set.
     * @param string $location a valid path / url to pass to redirect method
     */
    public function setNoSessionRedirect($location)
    {
        $this->_noSessionRedirectLocation = $location;
    }

    /**
     * This is location redirected to on a controller level role failure, when an automated role checking
     * method is activated.  Note the internally this defaults to login
     * @param string $location a valid path / url to pass to redirect method
     */
    public function setControllerRoleFailureRedirection($location)
    {
        $this->_controllerRoleFailRedirect = $location;
    }

    /**
     * IMPORTANT - if you provide both allow and deny lists denied roles will be processed first, and then of those
     * remaining only allowed roles will pass.
     *
     * Adds a role to the permissible list.
     * As soon as this list contains at least one role, all other roles will be excluded by virtue of not appearing
     * on this ACL.
     *
     * calls to this method are additive, so multiple calls can be used to add multiple roles.
     * Disallowed roles will be redirected to ControllerRoleFailureRedirection which defaults to login.
     *
     * This property must be set before the parent initialize() event executes, the desired place is within setupController()
     * which will execute before initialize()
     *
     * @param string $role The name of a known role in the system (roles table)
     */
    public function setAllowRole($role)
    {
        $this->_allowRoles[] = $role;
    }

    /**
     * IMPORTANT - if you provide both allow and deny lists denied roles will be processed first, and then of those
     * remaining only allowed roles will pass.
     *
     * Adds a role to the not permissible list.
     * As soon as this list contains at least one role, all other roles will be included by virtue of not appearing
     * on this ACL.
     *
     * calls to this method are additive, so multiple calls can be used to add multiple roles.
     * Disallowed roles will be redirected to ControllerRoleFailureRedirection which defaults to login.
     *
     * This property must be set before the parent initialize() event executes, the desired place is within setupController()
     * which will execute before initialize()
     *
     * @param string $role The name of a known role in the system (roles table)
     */
    public function setDenyRole($role)
    {
        $this->_denyRoles[] = $role;
    }

    public function setAllowPortal($portal)
    {
        $this->_allowedPortals[] = \Portal::resolve($portal)->getPortalGUID();
    }

    public function setDenyPortal($portal)
    {
        $this->_deniedPortals[] = \Portal::resolve($portal)->getPortalGUID();
    }

    protected function checkControllerLevelPermissions()
    {
        $auth = $this->getAuthentication();

        if (count($this->_deniedPortals) > 0) {
            $active = PortalStrap::getActivePortalIdentifier();

            foreach($this->_deniedPortals as $denied)
            {
                if($denied == $active) {
                    $this->response->redirect('error/fourofour');
                    $this->response->send();
                }
            }
        }

        if (count($this->_allowedPortals) > 0) {
            $active = PortalStrap::getActivePortalIdentifier();

            $allow = false;

            foreach($this->_allowedPortals as $allowed)
            {
                if($allowed == $active) {
                    $allow = true;
                    break;
                }
            }

            if(! $allow) {
                $this->response->redirect('error/fourofour');
                $this->response->send();
            }
        }

        if (count($this->_allowRoles) > 0) {
            $pass = $this->isActiveRole($this->_allowRoles, true, false);

            if (!$pass) {
                $this->registerPermissionRequest();
                $this->response->redirect($this->_controllerRoleFailRedirect);
                $this->response->send();
            }
        }


        if ($this->_noSessionRedirectLocation != '') {
            if ($auth->getAuthenticatedUser() === false) {
                $this->registerPermissionRequest();
                $this->response->redirect($this->_noSessionRedirectLocation);
                $this->response->send();
            }
        }

        if (count($this->_denyRoles) > 0) {
            $deny = $this->isActiveRole($this->_denyRoles, true, true);

            if ($deny) {
                $this->response->redirect($this->_controllerRoleFailRedirect);
                $this->response->send();
            }
        }

        if (count($this->_allowRoles) > 0) {
            $pass = $this->isActiveRole($this->_allowRoles, true, false);

            if (!$pass) {
                $this->registerPermissionRequest();
                $this->response->redirect($this->_controllerRoleFailRedirect);
                $this->response->send();
            }
        }
    }

    public function requireRoleOrForward($role, array $notAllowedForward)
    {
        $auth = $this->getAuthentication();

        if (! $auth->sessionHasRole($role)) {
            $this->dispatcher->forward($notAllowedForward);
        }
    }

    public function requireRoleOrRedirect($role, $location = 'login')
    {
        $auth = $this->getAuthentication();
        if (! $auth->sessionHasRole($role)) {
            $this->registerPermissionRequest();
            $this->response->redirect($location);
            $this->response->send();
        }
    }

    protected function registerPermissionRequest()
    {
        if ($this->session->has('PERMISSION_REQUEST_URL') && $this->session->has('PERMISSION_REQUEST_PREVIOUS_URL')) {
            //this same request has already failed,  just clear, so next login will go to dashboard;
            $this->clearPermissionRequest();
            return;
        } elseif (!$this->request->isAjax()) {

            if ($this->session->has('PERMISSION_REQUEST_URL')) {
                $this->session->set('PERMISSION_REQUEST_PREVIOUS_URL', $this->session->get('PERMISSION_REQUEST_URL'));
            }

            $this->session->set('PERMISSION_REQUEST_URL', Url::getRequestURL());
        }
    }

    protected function clearPermissionRequest()
    {
        $this->session->remove('PERMISSION_REQUEST_URL');
        $this->session->remove('PERMISSION_REQUEST_PREVIOUS_URL');
    }

    public function requireRoleOrHTTPResponse(
        $role,
        $code = '401',
        $message = 'You do not have the required permission to complete this request'
    ) {
        $auth = $this->getDI()->get('auth');
        if ($auth->sessionHasRole($role)) {
            return true;
        }

        $this->response->setStatusCode($code, $message);
        $this->response->setContent("");
        $this->response->send();
        exit(0);
    }

    public function requireRoleOrCustom($role, $function, $parameters = null)
    {
        $auth = $this->getDI()->get('auth');
        if ($auth->sessionHasRole($role)) {
            return true;
        }

        $function($parameters);
        return false;
    }


    /**
     * @param $role
     * @param bool|false $canForceChange Allow the users role to switch to one that is valid
     * @param bool $negateForceChange
     * @param null $redirect
     * @return bool
     */
    public function isActiveRole($role, $canForceChange = false, $negateForceChange = false, $redirect = null)
    {
        if (!is_array($role)) {
            $role = array($role);
        }

        $activeRole = $this->getAuthentication()->getSessionActiveRole();

        $pass = false;

        foreach ($role as $rol) {
            if ($activeRole == $rol) {
                $pass = true;
                break;
            }
        }

        //switch role if appropriate
        if(! $pass && $canForceChange && !$negateForceChange) { //@todo ensure only one role change per request, else could get confusing
            foreach ($role as $rol) {
                if ($this->hasRole($rol)) {
                    $this->getAuthenticatedUser()->setActiveRole($rol, true);
                    $pass = true;
                    break;
                }
            }
        } elseif($pass && $negateForceChange && $canForceChange) { //try to set a role not on the deny list
            $roles = $this->getAuthenticatedUser()->getRoles();
            foreach($roles as $rol) {
                if(! in_array($rol->getRole()->getName(), $role)) {
                    if($rol->getRole()->getName() != $this->getAuthenticatedUser()->getActiveRole()->getName()) {
                        $this->getAuthenticatedUser()->setActiveRole($rol->getRole()->getName(), true);
                        $pass = false;
                        break;
                    }
                }
            }
        }


        if(($redirect != null && !$pass && !$negateForceChange) || ($redirect != null && $pass && $negateForceChange)) {
            $this->response->redirect($redirect);
            $this->response->send();
        }

        return $pass;
    }

    /**
     * param filter for this controller,  is lazy loaded.
     * @return \Apprecie\Library\Security\RequestFilter|null
     */
    public function getRequestFilter($requireHTTPS = true)
    {
        if($this->_paramFilter == null) {
            $this->_paramFilter = _rf($requireHTTPS);
        }

        return $this->_paramFilter;
    }
} 