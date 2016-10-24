<?php

class LoginController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect('');
    }

    public function indexAction()
    {//public
        $this->view->portal = \Apprecie\Library\Provisioning\PortalStrap::getActivePortal();
        $this->view->setLayout('login');

        if ($this->session->has('useCaptcha') == false) {
            $this->session->set('useCaptcha', false);
        }

        // only uncomment if Qualys scan running
        /*if($this->request->get('username') == 'gavdaman@born2code.co.uk') { // another hack for qualys
            $auth = new \Apprecie\Library\Security\Authentication();
            $auth->loginUser('gavdaman@born2code.co.uk', 'moopy1');
            $this->response->redirect('/dashboard');
            $this->response->send();
        }*/

        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                $this->view->error = _g("Invalid session");
                return;
            }

            $auth = new \Apprecie\Library\Security\Authentication();
            if ($this->session->get('useCaptcha') === true) {
                if ($this->request->hasPost('g-recaptcha-response')) {
                    $captchaIsPassed = $auth->validateCaptcha($this->request->getPost('g-recaptcha-response'));
                    if ($captchaIsPassed === true) {
                        $this->session->set('useCaptcha', false);
                    } else {
                        $this->view->error = _g("Please prove yourself not a robot");
                        return;
                    }
                } else {
                    $this->view->error = _g("Please prove yourself not a robot");
                    return;
                }
            }

            $username = $this->request->getPost('username');
            if (!$auth->loginUser($username, $this->request->getPost('password'))) {
                $this->view->error = _g("The username or password is incorrect");
                if ($auth->useCaptcha() === true) {
                    $this->session->set('useCaptcha', true);
                }
            } else {

				if ($this->request->getPost('remember-me') == 1) {
                    setcookie("apprecie_user", $username, time() + (86400 * 7));
                } else {
                    setcookie("apprecie_user", '', -1);
                }

                if ($this->session->has('PERMISSION_REQUEST_URL')) {
					$destination = $this->session->get('PERMISSION_REQUEST_URL');
                    $this->clearPermissionRequest();
                    $this->response->redirect($destination);
                } else {
					$role = $this->getAuthenticatedUser()->getActiveRole();
                    return $this->response->redirect($role->getDefaultController() . '/' . $role->getDefaultAction());
                }

                return;
            }
        }
    }

    /* indirect assets fixes
    public function fixAction()
    {
        _m();

        $events = Event::find();
        $organisations = Organisation::find();
        $portals = Portal::find();
        $items = Item::find();


        foreach ($events as $event) {

            _ep($event->getTitle());
            $event->setLanguageId(15);

            $event->update();
        }

        foreach ($items as $item) {

            _ep($item->getTitle());
            $item->setLanguageId(15);
            $item->update();
        }


        foreach ($organisations as $org) {
            _ep($org->getOrganisationName());
            $org->setLanguageId(15);
            $org->update();
        }


        foreach($portals as $portal) {
            _ep($portal->getPortalName());
            $portal->setLanguageId(15);
            $portal->update();
        }

        _m();
    }*/


    /*public function fixLocalAction()
    {
        $events = Event::find();
        $organisations = Organisation::find();
        $portals = Portal::find();


        foreach ($events as $event) {
            $event->changeIndirectMacroFormat();
            $event->update();

        }

        foreach ($organisations as $org) {
            $org->changeIndirectMacroFormat();
            $org->update();
        }


        foreach($portals as $portal) {
            $portal->changeIndirectMacroFormat();
            $portal->update();
        }

        _m();
    }*/

    public function logoutAction()
    {
        $this->di->get('auth')->logoutUser();
        $this->response->redirect('login')->send();
    }

    public function recoveryAction()
    {
        $this->view->portal = \Apprecie\Library\Provisioning\PortalStrap::getActivePortal();

        $this->view->setLayout('login');

        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                $this->view->error = _g("Invalid session");
                return;
            }
            $email = $this->request->getPost('username');
            $userLogin = UserLogin::findFirstBy('username', $email);
            $auth = new \Apprecie\Library\Security\Authentication();

            if ($userLogin == null) {
                $this->view->reset = true;
            } elseif ($auth->userIsInteractive($userLogin->getUser())) {
                $userProfile = $userLogin->getPortalUser();
                $userProfile->sendPasswordRecoveryEmail();
                $this->view->reset = true;
            } else {
                $this->view->error = _ms($auth);
            }
        }
    }

    public function showMeAction()
    {
        $cache = $this->getDI()->get('cache');
        _ep($cache->queryKeys());
    }

    public function setupAction()
    {
        (new \Apprecie\Library\Users\UserEx())->createUserWithProfileAndLogin('david.constantine@apprecie.com', '@ppr3c13ADMIN', 'Dave', 'Constantine', 'Mr')
            ->addRole('SystemAdministrator');
    }

    public function resetAction($token)
    {
        $this->view->portal = \Apprecie\Library\Provisioning\PortalStrap::getActivePortal();
        $this->view->token = $token;
        $user = PortalUser::findFirstBy('passwordRecoveryHash', $token);
        if ($user == null) {
            $this->view->error = _g(
                'This link is no longer valid. If you still need to reset your password, please go to your portal and use the "Recover Account" button to request a new password reset.'
            );
            $this->view->badhash = true;
        } elseif (strtotime($user->getPasswordRecoverySent()) < strtotime('now -1 day')) {
            $this->view->error = "Invalid recovery token";
        } else {

            if ($this->request->isPost()) {
                if (!$this->checkCSRF()) {
                    $this->view->error = _g("Invalid session");
                    return;
                }
                $password = $this->request->getPost('password');
                $confirmPassword = $this->request->getPost('confirm-password');

                if ($password != $confirmPassword || strlen($password) < 8 || strlen($password) > 25 || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password)) {
                    $this->view->error = 'Password is invalid.';
                } else {
                    $userLogin = $user->getUserLogin();
                    $userLogin->setAndHashPassword($password);
                    $user->setPasswordRecoveryHash(null);
                    $user->save();
                    $userLogin->save();
                    $mail = new \Apprecie\Library\Mail\EmailUtility();
                    $mail->postResetPasswordEmail(
                        $user->getUserProfile()->getEmail(),
                        $user->getUserProfile()->getFirstName(),
                        $user->getUser()->getOrganisation()
                    );
                    $this->view->success = true;
                }
            }
        }
    }

    public function getAuthenticatedUserAction(){
        $this->view->setLayout('blank');
        $userGuid=false;
        if($this->getAuthenticatedUser()!=null){
            $userGuid=true;
        }
        echo json_encode(['loggedIn'=>$userGuid]);
    }
}