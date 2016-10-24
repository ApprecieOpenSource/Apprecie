<?php

/**
 * Created by PhpStorm.
 * User: huwang
 * Date: 19/11/2015
 * Time: 09:55
 */
class ContactsController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController() {
        $this->setAllowRole('Client');
    }

    public function createAction() {
        $this->view->setLayout('application');
    }

    public function indexAction() {
        $this->view->setLayout('application');
        $groups = PortalMemberGroup::query();
        $groups->where('ownerId=:1:');
        $groups->bind([1 => $this->getAuthenticatedUser()->getUserId()]);
        $this->view->groups = $groups->execute();
    }

    public function AjaxCreateUserAction()
    {
        $this->getRequestFilter()->execute($this->request);

        $this->view->disable();
        $auth = new \Apprecie\Library\Security\Authentication();
        $thisUser = $auth->getAuthenticatedUser();

        $dob = $this->request->getPost('dob-formatted');
        $portalId = $thisUser->getPortalId();
        $interests = $this->request->getPost('interests');
        $diet = $this->request->getPost('diet');
        $phone = $this->request->getPost('phone');
        $mobile = $this->request->getPost('mobile');
        $portal = Portal::resolve($portalId);
        $user = new \Apprecie\Library\Users\UserEx();
        $newUser = $user->createUserWithProfileAndLogin(
            $this->request->getPost('emailaddress'),
            null,
            $this->request->getPost('firstname'),
            $this->request->getPost('lastname'),
            $this->request->getPost('title'),
            $thisUser->getOrganisationId(),
            $this->request->getPost('reference-code'),
            null,
            $portal->getInternalAlias()
        );
        $newUser->setTier($thisUser->getTier());
        $newUser->addRole(\Apprecie\Library\Users\UserRole::CONTACT);

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);
        if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
            $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $newUser->getUserProfile()->setHomeAddressId($addressId);
        };

        if ($this->request->getPost('gender') == 'female') {
            $newUser->getUserProfile()->setGender(\Apprecie\Library\Users\UserGender::FEMALE); //WORKING
        } else {
            $newUser->getUserProfile()->setGender(\Apprecie\Library\Users\UserGender::MALE); //WORKING
        }

        if ($dob != null) {
            $newUser->getUserProfile()->setBirthday(_myd($dob)); //WORKING
        }

        $newUser->getUserProfile()->setPhone($phone);
        $newUser->getUserProfile()->setMobile($mobile);
        $newUser->getUserProfile()->save();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        $newUser->setCreatingUser($thisUser->getUserId()); //NOT WORKING

        if (count($diet) != 0) {
            $newUser->addDietaryRequirement($diet);
        }

        if (count($interests) != 0) {
            $newUser->addInterest($interests);
        }

        $newUser->setChildOf($thisUser->getUser());
        $newUser->save();

        echo json_encode(
            [
                'portalId' => $portal->getPortalId(),
                'role' => $newUser->getRoles()[0]->getRole()->getDescription(),
                'userId' => $newUser->getUserId()
            ]
        );
    }

    public function AjaxEditUserAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT)
            ->execute($this->request);

        $user = User::resolve($userId);

        // Secure Ajax Request
        if (!$user->canBeSeenBy($this->getAuthenticatedUser(), null)) {
            _jm('failed',_g('Authentication failed'));
            return;
        }

        $this->view->disable();

        $emailAddress = $this->request->getPost('emailaddress');
        $reference = $this->request->getPost('reference-code');
        $dob = $this->request->getPost('dob-formatted');
        $interests = $this->request->getPost('interests');
        $diet = $this->request->getPost('diet');

        if ($user == null) {
            echo json_encode(array('result' => 'failed', 'message' => 'Could not find the user in this portal.'));
            return;
        }

        $oldPortal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId());

        $portalUser = $user->getPortalUser();
        $portalUser->setReference($reference);
        $portalUser->save();

        // save the username and password
        $userLogin = $user->getUserLogin();
        $userLogin->setUsername($emailAddress);
        $userLogin->save();

        // set all the user profile data
        $userProfile = $user->getUserProfile();
        $userProfile->setFirstname($this->request->getPost('firstname'));
        $userProfile->setLastname($this->request->getPost('lastname'));
        $userProfile->setEmail($emailAddress);
        $userProfile->setPhone($this->request->getPost('phone'));
        $userProfile->setMobile($this->request->getPost('mobile'));
        $userProfile->setTitle($this->request->getPost('title'));
        if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
            $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $userProfile->setHomeAddressId($addressId);
        };
        if ($dob != null) {
            $userProfile->setBirthday(_myd($dob));
        }
        if ($this->request->getPost('gender') === 'female') {
            $userProfile->setGender(\Apprecie\Library\Users\UserGender::FEMALE); //WORKING
        } else {
            $userProfile->setGender(\Apprecie\Library\Users\UserGender::MALE); //WORKING
        }
        $userProfile->save();

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
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($oldPortal);
        echo json_encode(array('result' => 'success'));
    }

    public function AjaxSearchAction($pageNumber = 1)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request, true, false);

        $this->view->disable();

        $usersArray = array();
        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');
        $reference = $this->request->getPost('reference');
        $role = $this->request->getPost('roleName');
        $group = $this->request->getPost('group');
        $metricsOnly = false;
        if ($this->request->getPost('metricsOnly') === 'true') {
            $metricsOnly = true;
        }

        $thisUser = $this->getAuthenticatedUser();
        $searchUsers = $thisUser->resolveChildren($role);
        $portalGroup = null;
        if ($group != 'all') {
            $portalGroup = PortalMemberGroup::findFirstBy('groupId', $group);
        }

        foreach ($searchUsers as $result) {
            if ($result->getIsDeleted()) {
                continue;
            }

            if ($result->getUserId() === $thisUser->getUserId() || ($portalGroup != null && !$portalGroup->hasUser($result->getUserId()))) {
                continue;
            }

            $account[] = 'pending';

            if (!in_array($result->getStatus(), $account)) {
                continue;
            }

            if ($name && stripos($result->getUserProfile()->getFullName(), $name) === false) {
                continue;
            }

            if ($email && stripos($result->getUserProfile()->getEmail(), $email) === false) {
                continue;
            }

            if ($reference && stripos($result->getPortalUser()->getReference(), $reference) === false) {
                continue;
            }

            $results = [];

            if ($metricsOnly === false) {
                $results['profile'] = $result->getUserProfile()->toArray();
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
                $results['userid'] = $result->getUserId();
                $results['image'] = Assets::getUserProfileImageContainer($result->getUserId());
                if($result->getUserReference()===null){
                    $results['reference'] = '';
                }
                else{
                    $results['reference'] = $result->getUserReference();
                }
                $results['organisation'] = $result->getOrganisation()->getOrganisationName();
                $results['role'] = (new \Apprecie\Library\Users\UserRole($role))->getText();
            } else {
                $results['userid'] = $result->getUserId();
            }

            $usersArray[] = $results;
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

    public function AjaxDeleteContactAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = User::resolve($userId);

        $this->view->disable();
        if (!$user->canBeSeenBy($this->getAuthenticatedUser(), null)) {
            _jm('failed',_g('Authentication failed'));
            return;
        }

        $status = 'failed';
        $message = _g('Invalid request');

        try {
            if ($this->getAuthenticatedUser()->userIsDescendant($user)) {
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
            }
        } catch (\Exception $ex) {
            $this->logActivity('Failed to delete user', $ex->getMessage());
        }

        _jm($status, $message);
    }

    public function viewUserAction($userId)
    {
        $this->view->setLayout('application');
        $user = User::resolve($userId);
        $user->canBeSeenBy($this->getAuthenticatedUser());

        $portal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId());
        $userProfile = $user->getUserProfile();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portal);

        $this->view->user = $user;
        $this->view->userProfile = $userProfile;
        $this->view->portalUser = $user->getPortalUser();
        $this->view->address = Address::findFirstBy('addressId', $user->getUserProfile()->getHomeAddressId());
        $this->view->thisUser = $this->getAuthenticatedUser();
        $this->view->suggestedEvents = \Apprecie\Library\Items\ItemSuggestions::getSuggestedItems($this->view->user->getUserId());
    }

    public function editAction($userId)
    {
        $this->view->setLayout('application');

        $user = User::resolve($userId);
        // Secure User Request
        $user->canBeSeenBy($this->getAuthenticatedUser());
        $this->view->user = $user;
    }
}