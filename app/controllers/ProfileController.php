<?php

class ProfileController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function indexAction()
    {
        $this->view->setLayout('application');
        $this->view->user = $this->getAuthenticatedUser();
        $this->view->userProfile = $this->view->user->getUserProfile();
    }

    public function saveAction()
    {
        $this->getRequestFilter()
            ->addRequired('firstname', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('lastname', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('emailaddress', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();

        $emailAddress = $this->request->getPost('emailaddress');

        $dob = $this->request->getPost('dob-formatted');
        $interests = $this->request->getPost('interests');
        $diet = $this->request->getPost('diet');
        $communication = $this->request->getPost('communication');
        $addressId = $this->request->getPost('address-id');
        $phone = $this->request->getPost('phone');
        $mobile = $this->request->getPost('mobile');

        $user = $this->getAuthenticatedUser();

        // save the username and password
        $user->getUserLogin()->setUsername($emailAddress);
        $user->getUserLogin()->save();
        // set all the user profile data
        $userProfile = $user->getUserProfile();

        if ($addressId != null || $this->request->getPost('addressType') == 'manual') {
            $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $userProfile->setHomeAddressId($addressId);
        };

        $filter = new Phalcon\Filter();
        $userProfile->setFirstname($filter->sanitize($this->request->getPost('firstname'), "string"));
        $userProfile->setLastname($filter->sanitize($this->request->getPost('lastname'), "string"));
        $userProfile->setTitle($filter->sanitize($this->request->getPost('title'), "string"));
        $userProfile->setEmail($emailAddress);

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

    function pictureAction()
    {
        if (!file_exists(Assets::getPortalAssetsDir())) {
            mkdir(Assets::getPortalAssetsDir());
        }

        $auth = new \Apprecie\Library\Security\Authentication();
        $user = $auth->getAuthenticatedUser();

        if ($this->request->hasFiles() == true) { //@todo GH  check Phalcon file handling is secure
            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getType() != 'image/jpeg' && $file->getType() != 'image/pjpeg') {
                    echo json_encode(array('status' => 'failed', 'message' => 'Invalid image type, must be JPG'));
                    return;
                }
                $tempLocation = Assets::getPortalAssetsDir() . $user->getUserId() . '-temp.jpg';
                $location = Assets::getPortalAssetsDir() . $user->getUserId() . '.jpg';
                $resizeLocation = $location . '.resize';
                $file->moveTo($tempLocation);
                $dimensions = getimagesize($tempLocation);
                if ($dimensions[0] < 390 or $dimensions[1] < 390) {
                    echo json_encode(array('status' => 'failed', 'message' => 'Image must be 390x390 or larger'));
                    unlink($tempLocation);
                    return;
                } else {
                    if (Assets::resize_image($tempLocation, $resizeLocation, 390, 390)) {
                        if (file_exists($location)) {
                            unlink($location);
                        }
                        rename($resizeLocation, $location);
                        $rsr_org = imagecreatefromjpeg($tempLocation);
                        $rsr_scl = imagescale($rsr_org, 390, 390, IMG_BICUBIC_FIXED);
                        imagejpeg($rsr_scl, $location);
                        imagedestroy($rsr_org);
                        imagedestroy($rsr_scl);
                        echo json_encode(
                            array('status' => 'success', 'url' => Assets::getUserProfileImage($user->getUserId()))
                        );
                    } else {
                        _jm('failed', 'The image was invalid or of an unexpected type');
                    }
                    unlink($tempLocation);
                }
            }
        } else {
            echo json_encode(array('status' => 'failed', 'message' => 'No image was found'));
        }
    }
}