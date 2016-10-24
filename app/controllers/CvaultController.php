<?php
class CvaultController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function indexAction() {
        $this->view->setLayout('application');
    }

    public function ajaxGetCvaultUsersAction()
    {
        $pageNumber = $this->request->getPost('pageNumber');
        $categoriesArray = array();
        $usersArray = array();

        //echo json_encode($usersArray);

        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');
        $reference = $this->request->getPost('reference');
        $role = $this->request->getPost('roleName');
        $accountPending = $this->request->getPost('accountPending');
        $accountActive = $this->request->getPost('accountActive');
        $accountDeactivated = $this->request->getPost('accountDeactivated');
        $login = $this->request->getPost('login');
        $group = $this->request->getPost('group');
        $suggestions = $this->request->getPost('suggestions');
        $searchUsers = [];

        // If a specific role has been specified
        $thisUser = $this->getAuthenticatedUser();

        if ($thisUser->getActiveRole()->getName() == "PortalAdministrator") {
            foreach (User::getUsersInOrganisation($thisUser->getOrganisationId(), $thisUser->getPortalId()) as $user) {
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
            $suggestedEvents=\Apprecie\Library\Items\ItemSuggestions::getSuggestedItems($result->getUserId());
            if (!$result->getIsDeleted() and count($suggestedEvents)!=0) {
                if ($result->getUserId() != $thisUser->getUserId() && ($portalGroup == null || $portalGroup->hasUser(
                            $result->getUserId()
                        ))
                ) {
                    if ($name) {
                        if (stripos($result->getUserProfile()->getFullName(), $name) === false) {
                            continue;
                        }
                    }

                    // only a client can see their contacts
                    if (!$thisUser->hasRole(\Apprecie\Library\Users\UserRole::CLIENT) && $result->hasRole(
                            \Apprecie\Library\Users\UserRole::CONTACT
                        )
                    ) {
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

                    $results['profile'] = $result->getUserProfile()->toArray();
                    if ($result->getUserLogin()->getSuspended() == 1) {
                        $results['login'] = _g('Suspended');
                    } elseif ($result->getUserLogin()->getPassword() == 'pending') {
                        $results['login'] = _g('Unregistered');
                    } elseif ($result->getUserLogin()->getSuspended() == null || $result->getUserLogin()->getSuspended(
                        ) == '0'
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
                            $results['account'] = _g('Unregistered');
                            break;
                    }
                    $groups = $result->getGroupsMemberOf();
                    $groupstring = '';
                    foreach ($groups as $group) {
                        if($groupstring!=''){
                            $groupstring .= ', '.$group->getGroupName();
                        }
                        else{
                            $groupstring .= $group->getGroupName();
                        }
                    }
                    $groupstring=str_replace('"','',$groupstring);
                    $results['groupsCount']=count($groups);
                    $results['groups'] = $groupstring;
                    $results['userid'] = $result->getUserId();
                    $results['image'] = Assets::getUserProfileImageContainer($result->getUserId());
                    $results['reference'] = $result->getUserReference();
                    $results['organisation'] = $result->getOrganisation()->getOrganisationName();
                    $results['interests']=[];
                    foreach($result->getInterests() as $interest){
                        $parents=$interest->getParents();
                        foreach($parents as $parentInterest){
                            if(!in_array($parentInterest->getInterestId(),$results['interests'])){
                                array_push($results['interests'],$parentInterest->getInterestId());
                            }
                        }
                    }

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
                    "limit" => 10,
                    "page" => $pageNumber
                )
            );

            $page = $paginator->getPaginate();
            echo json_encode($page);
        }
    }
}