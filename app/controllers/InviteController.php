<?php

class InviteController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function indexAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        if (!\Apprecie\Library\Acl\AccessControl::userCanOperateGuestList($this->getAuthenticatedUser(), $itemId)) {
            throw new \Exception('The user ' . $this->getAuthenticatedUser()->getUserId() . ' can not operate a guest list for item ' . $itemId . ' they own no units.');
        }

        $this->view->setLayout('application');
        $item = Item::resolve($itemId);

        $this->view->groups = PortalMemberGroup::findBy('ownerId', $this->getAuthenticatedUser()->getUserId());

        $this->view->availableUnits = UserItems::getTotalAvailableUnits(
            $this->getAuthenticatedUser()->getUserId(),
            $item->getItemId(),
            \Apprecie\Library\Items\UserItemState::OWNED
        );

        $this->view->item = $item;
    }

    public function suggestAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        //GH  note this is secured on the basis of viewing from the vault not managing a guest list
        if (!\Apprecie\Library\Acl\AccessControl::userCanViewItem($this->getAuthenticatedUser(), $itemId)) {
            throw new \Exception('The user ' . $this->getAuthenticatedUser()->getUserId() . ' does not have visibility of this item so cannot suggest. Item: ' . $itemId);
        }

        $this->view->setLayout('application');
        $item = Item::resolve($itemId);
        $this->view->groups = PortalMemberGroup::findBy('ownerId', $this->getAuthenticatedUser()->getUserId());

        $this->view->availableUnits = UserItems::getTotalAvailableUnits(
            $this->getAuthenticatedUser()->getUserId(),
            $item->getItemId(),
            'owned'
        );

        $this->view->item = $item;
        $this->view->user = $this->getAuthenticatedUser();
    }

    public function groupUsersAction($groupId)
    {
        $this->getRequestFilter()->addNonRequestRequired('groupId', $groupId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $group = PortalMemberGroup::findFirstBy('groupId', $groupId); //@todo add resolve for PortalMemberGroups
        if($group == null) {
            return;
        }

        if (!\Apprecie\Library\Acl\AccessControl::userCanOperateGroup($this->getAuthenticatedUser(), $group)) {
            throw new \Exception('The user ' . $this->getAuthenticatedUser()->getUserId() . ' does not have access to this group. Group: ' . $groupId);
        }

        $this->view->setLayout('application');

        $this->view->groups = PortalMemberGroup::findBy('ownerId', $this->getAuthenticatedUser()->getUserId());
        $this->view->group = $group;
    }

    public function ajaxGetSuggestedUsersAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('roleName', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($itemId);
        $categories = $item->getCategories();

        $pageNumber = $this->request->getPost('pageNumber');
        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');
        $reference = $this->request->getPost('reference');
        $roleName = $this->request->getPost('roleName');
        $role = $roleName != 'All' ? Role::resolve($roleName) : null;
        $accountPending = $this->request->getPost('accountPending');
        $accountActive = $this->request->getPost('accountActive');
        $accountDeactivated = $this->request->getPost('accountDeactivated');
        $login = $this->request->getPost('login');
        $group = $this->request->getPost('group');
        $suggestions = $this->request->getPost('suggestions');

        $categoriesArray = [];
        $usersArray = [];
        $searchUsers = [];

        foreach ($categories as $category) {
            array_push($categoriesArray, $category->getInterestId());
        }

        $thisUser = $this->getAuthenticatedUser();

        if ($thisUser->getActiveRole()->getName() == "PortalAdministrator") {
            $users = \Apprecie\Library\SearchFilters\Users\UserSearchFilterUtility::userSearch(null, null, null, $thisUser->getOrganisationId(), $role->getRoleId());

            foreach ($users as $user) {
                if ($roleName != 'All') {
                    if ($user->hasRole($roleName)) {
                        $searchUsers[] = $user;
                    }
                } else {
                    $searchUsers[] = $user;
                }
            }
        } else {
            $searchUsers = $thisUser->resolveChildren($roleName);
        }

        $portalGroup = null;
        if ($group != 'all') {
            $portalGroup = PortalMemberGroup::findFirstBy('groupId', $group);
        }

        foreach ($searchUsers as $result) {
            if (!$result->getIsDeleted()) {
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

                    if ($result->hasRole(\Apprecie\Library\Users\UserRole::CLIENT) && $result->getTier() < $item->getTier()
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


                    if ($login == 'enabled' && ($result->getUserLogin()->getSuspended() == true || $result->getUserLogin()->getPassword() == 'pending')
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

                    $results['suggested'] = 0;
                    foreach ($result->getInterests() as $interest) {
                        if (in_array($interest->getInterestId(), $categoriesArray)) {
                            $results['suggested'] = 1;
                        }
                    }

                    if ($suggestions == 'true' && $results['suggested'] != 1) {
                        continue;
                    }

                    $results['profile'] = $result->getUserProfile()->toArray();
                    if ($result->getUserLogin()->getSuspended() == 1) {
                        $results['login'] = _g('Suspended');
                    } elseif ($result->getUserLogin()->getPassword() == 'pending') {
                        $results['login'] = _g('Unregistered');
                    } elseif ($result->getUserLogin()->getSuspended() == null || $result->getUserLogin()->getSuspended() == '0'
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
                        $groupstring .= $group->getGroupName() . ' ';
                    }


                    $results['groups'] = $groupstring;
                    $results['userid'] = $result->getUserId();
                    $results['image'] = Assets::getUserProfileImageContainer($result->getUserId());
                    $results['reference'] = $result->getUserReference();
                    $results['organisation'] = $result->getOrganisation()->getOrganisationName();

                    $roleName = '';
                    $firstRole = true;
                    foreach ($result->getRoles() as $roleLink) {
                        if ($firstRole === true) {
                            $userRole = new \Apprecie\Library\Users\UserRole($roleLink->getRole()->getName());
                            $roleName .= $userRole->getText();
                            $firstRole = false;
                        } else {
                            $userRole = new \Apprecie\Library\Users\UserRole($roleLink->getRole()->getName());
                            $roleName .= ' / ' . $userRole->getText();
                        }

                    }

                    $results['role'] = $roleName;

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

    public function ajaxGetSuggestedUsersForGroupAction($groupId)
    {
        $this->getRequestFilter()->addNonRequestRequired('groupId', $groupId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('roleName', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $pageNumber = $this->request->getPost('pageNumber');
        $email = $this->request->getPost('email');
        $name = $this->request->getPost('name');
        $reference = $this->request->getPost('reference');
        $roleName = $this->request->getPost('roleName');
        $role = $roleName != 'All' ? Role::resolve($roleName) : null;
        $accountPending = $this->request->getPost('accountPending');
        $accountActive = $this->request->getPost('accountActive');
        $accountDeactivated = $this->request->getPost('accountDeactivated');
        $login = $this->request->getPost('login');
        $group = $this->request->getPost('group');
        $suggestions = $this->request->getPost('suggestions');

        $usersArray = [];
        $searchUsers = [];

        $thisUser = $this->getAuthenticatedUser();

        if ($thisUser->getActiveRole()->getName() == "PortalAdministrator") {
            $users = \Apprecie\Library\SearchFilters\Users\UserSearchFilterUtility::userSearch(null, null, null, $thisUser->getOrganisationId(), $role->getRoleId());

            foreach ($users as $user) {
                if ($roleName != 'All') {
                    if ($user->hasRole($roleName)) {
                        $searchUsers[] = $user;
                    }
                } else {
                    $searchUsers[] = $user;
                }
            }
        } else {
            $searchUsers = $thisUser->resolveChildren($roleName);
        }

        $portalGroup = null;
        if ($group != 'all') {
            $portalGroup = PortalMemberGroup::findFirstBy('groupId', $group);
        }

        foreach ($searchUsers as $result) {
            if (!$result->getIsDeleted()) {
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

                    if ($login == 'enabled' && ($result->getUserLogin()->getSuspended() == true || $result->getUserLogin()->getPassword() == 'pending')) {
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

                    $results['suggested'] = 0;
                    if ($suggestions == 'true' && $results['suggested'] != 1) {
                        continue;
                    }

                    $results['profile'] = $result->getUserProfile()->toArray();
                    if ($result->getUserLogin()->getSuspended() == 1) {
                        $results['login'] = _g('Suspended');
                    } elseif ($result->getUserLogin()->getPassword() == 'pending') {
                        $results['login'] = _g('Unregistered');
                    } elseif ($result->getUserLogin()->getSuspended() == null || $result->getUserLogin()->getSuspended() == '0'
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
                        $groupstring .= $group->getGroupName() . ' ';
                    }

                    $results['groups'] = $groupstring;
                    $results['userid'] = $result->getUserId();
                    $results['image'] = Assets::getUserProfileImageContainer($result->getUserId());
                    $results['reference'] = $result->getUserReference();
                    $results['organisation'] = $result->getOrganisation()->getOrganisationName();

                    $roleName = '';
                    $firstRole = true;
                    foreach ($result->getRoles() as $roleLink) {
                        if ($firstRole === true) {
                            $userRole = new \Apprecie\Library\Users\UserRole($roleLink->getRole()->getName());
                            $roleName .= $userRole->getText();
                            $firstRole = false;
                        } else {
                            $userRole = new \Apprecie\Library\Users\UserRole($roleLink->getRole()->getName());
                            $roleName .= ' / ' . $userRole->getText();
                        }
                    }

                    $results['role'] = $roleName;

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

