<?php

/**
 * Class DashboardController displays the dashboard for the active user role
 */
class SignupController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect('');
    }

    public function indexAction($token)
    {
        $this->getRequestFilter()->addNonRequestRequired('token', $token, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $this->view->setLayout('signup');
        $this->view->portal = \Apprecie\Library\Provisioning\PortalStrap::getActivePortal();

        $user = PortalUser::findFirstBy('registrationHash', $token);

        if ($user == null) {
            $this->response->redirect('signup/invalid');
            $this->response->send();
        }

        $creator = $user->getUser()->getFirstParent();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($creator->getPortalId());
        $this->view->creatorProfile = $creator->getUserProfile();
        $this->view->creatorUser = $creator->getUser();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        $this->view->user = $user->getUserProfile();
        $this->view->userObj = $user->getUser();
        $this->view->dob = explode('-', $user->getUserProfile()->getBirthday());
        $this->view->token = $token;
        $this->view->organisation = $user->getUser()->getOrganisation();
    }

    public function invalidAction()
    {
        $this->view->setLayout('signup');
    }

    public function ajaxUpdateUserAction()
    {
        $this->getRequestFilter()->addRequired('token', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);


        $token = $this->request->getPost('token');
        $user = PortalUser::findFirstBy('registrationHash', $token);

        if ($user == null) {
            $this->response->redirect('signup/invalid');
            $this->response->send();
        }

        $this->view->disable();

        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm-password');
        $emailAddress = $this->request->getPost('emailaddress');
        $phone = $this->request->getPost('phone');
        $mobile = $this->request->getPost('mobile');
        $dob = $this->request->getPost('dob-formatted');
        $interests = $this->request->getPost('interests');
        $diet = $this->request->getPost('diet');
        $communication = $this->request->getPost('communication');
        $iAgree = $this->request->getPost('i-agree');

        if (!$iAgree) {
            echo json_encode(
                array('result' => 'failed', 'message' => 'Listed documents are not agreed.')
            );
            return;
        }

        if ($password !== $confirmPassword || strlen($password) < 8 || strlen($password) > 25 || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password)) {
            echo json_encode(
                array('result' => 'failed', 'message' => 'Password is invalid.')
            );
            return;
        }

        $user = PortalUser::findFirstBy('registrationHash', $token);

        if ($user == null) {
            echo json_encode(array('result' => 'failed', 'message' => 'Could not find the user in this portal.'));
            return;
        }

        // save the username and password
        $login = $user->getUserLogin();
        $login->setAndHashPassword($password);
        $login->setUsername($emailAddress);
        $login->save();

        // set all the user profile data+
        $userProfile = $user->getUserProfile();
        $userProfile->setFirstname($this->request->getPost('firstname'));
        $userProfile->setLastname($this->request->getPost('lastname'));
        $userProfile->setEmail($emailAddress);
        $userProfile->setTitle($this->request->getPost('title'));
        $userProfile->setPhone($phone);
        $userProfile->setMobile($mobile);
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

        if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
            $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $userProfile->setHomeAddressId($addressId);
        };

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
        $user->setRegistrationHash('NULL');
        $user->save();

        try {
            $organisation = $user->getUser()->getOrganisation();
            //$user->getPortalUser()->sendWelcomeEmail();
            $mail = new \Apprecie\Library\Mail\EmailUtility();
            $message = 'The user ' . $userProfile->getFullName() . ' has completed sign-up into the organisation ' . $organisation->getOrganisationName() . ' on portal ' . $this->getActivePortal()->getPortalName();
            $message .= _p('This message was generated in the ' . APPLICATION_ENV . ' environment');

            $mail->sendGenericEmailMessage($this->config->mail->adminNotifications, $message, 'Signup notification', $organisation);
        } catch (\Exception $ex) {
            //do nothing user has no email address  -  note GH - perhaps this should not cause an exception.
        }

        $userRecord = User::findFirstBy('userId', $user->getUser()->getUserId());
        $userRecord->setStatus('active');
        $userRecord->save();

        $signUpTerms = $this->session->get('TERMS_SIGN_UP');
        if ($signUpTerms) {
            foreach ($signUpTerms as $termsId) {
                $userTerms = new UserTerms();
                $userTerms->setTermsId($termsId);
                $userTerms->setUserId($user->getUser()->getUserId());
                $userTerms->save();
            }
            $this->session->remove('TERMS_SIGN_UP');
        }

        $this->view->disable();
        echo json_encode(array('result' => 'success'));
    }

    public function ajaxVerifyUserAction()
    {
        $this->getRequestFilter()->addRequired('token', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('emailaddressx', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $token = $this->request->getPost('token');
        $user = PortalUser::findFirstBy('registrationHash', $token);

        if ($user == null) {
            $this->response->redirect('signup/invalid');
            $this->response->send();
        }

        $this->view->disable();

        $emailX = trim(strtolower($this->request->getPost('emailaddressx')));

        $portalUser = PortalUser::findFirstBy('registrationHash', $token);
        $userProfile = $portalUser->getUserProfile();
        $email = trim(strtolower($userProfile->getEmail()));

        if ($emailX === $email) {
            $unacceptedLegalDocuments = \Apprecie\Library\Security\Authentication::getUnacceptedLegalDocuments($portalUser->getUser());
            $this->session->set('TERMS_SIGN_UP', $unacceptedLegalDocuments);

            if ($unacceptedLegalDocuments) {
                foreach ($unacceptedLegalDocuments as $termsId) {
                    $terms = Terms::resolve($termsId);
                    $signUpTerms[] = array(
                        'termsId' => $termsId,
                        'title' => $terms->getDefaultName()
                    );
                }
            } else {
                $signUpTerms = null;
            }

            echo json_encode(array('result' => 'success', 'email' => $userProfile->getEmail(), 'signUpTerms' => $signUpTerms));
        } else {
            echo json_encode(array('result' => 'failed'));
        }
    }
}

