<?php

/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 08/12/14
 * Time: 13:19
 */
class ApiexController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect('');
        $this->view->setLayout('blank');
    }

    public function indexAction()
    {
        _jm('success','You got me!');
    }

    /**
     * Fetch portal specific details required by the front end
     */
    public function portalInitAction(){

        $assetsDir=Assets::getPortalAssetsDir();
        $csrf=(new \Apprecie\Library\Security\CSRFProtection())->getSessionToken();
        $logo=Assets::getOrganisationBrandLogo(Organisation::getActiveUsersOrganisation()->getOrganisationId());
        $styles=$this->getDI()->get('portal')->getPortalStyles();
        $loginBg=Assets::getOrganisationBackground(Organisation::getActiveUsersOrganisation()->getOrganisationId());
        echo json_encode(['assetsDir'=>$assetsDir,'csrf'=>$csrf,'logo'=>$logo,'styles'=>$styles,'loginBg'=>$loginBg]);
    }

    public function loginAction(){
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                _jm('failed',_g("Invalid session"));
                return;
            }

            $auth = new \Apprecie\Library\Security\Authentication();

            $username = $this->request->getJsonRawBody()->emailAddress;
            if (!$auth->loginUser($username, $this->request->getJsonRawBody()->password)) {
                _jm('failed',_g("The email address or password is incorrect"));
                if ($auth->useCaptcha() === true) {
                    $this->session->set('useCaptcha', true);
                }
            } else {
                if (isset($this->request->getJsonRawBody()->remember) && $this->request->getJsonRawBody()->remember == 1) {
                    setcookie("apprecie_user", $username, time() + (86400 * 7));
                } else {
                    setcookie("apprecie_user", '', -1);
                }

                $userArray=[];
                $userArray['status']="success";
                $userArray['profile']=$auth->getAuthenticatedUser()->getUserProfile()->toArray();
                $userArray['organisation']=$auth->getAuthenticatedUser()->getOrganisation()->toArray();
                $userArray['activeRole']=['roleId'=>$auth->getAuthenticatedUser()->getActiveRole()->getRoleId(),'roleName'=>$auth->getAuthenticatedUser()->getActiveRole()->getName(),'roleDescription'=>$auth->getAuthenticatedUser()->getActiveRole()->getDescription()];
                $userArray['userRoles']=$auth->getAuthenticatedUser()->getRoles()->toArray();
                $userArray['roles']=Role::find()->toArray();
                echo json_encode($userArray);
            }
        }
    }
}