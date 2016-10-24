<?php

/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 24/11/2015
 * Time: 15:45
 */
class GroupsController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole('Client');
        $this->setAllowRole('Internal');
        $this->setAllowRole('Manager');
        $this->setAllowRole('PortalAdministrator');
    }

    public function indexAction()
    {
        $this->view->setLayout('application');
    }

    public function groupUsersAction($groupId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired(
                'groupId',
                $groupId,
                \Apprecie\Library\Security\ParameterTypes::INT,
                \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED
            )
            ->execute($this->request, true, false);

        $this->view->setLayout('application');

        $group = PortalMemberGroup::findFirstBy('groupId', $groupId);
        if ($group == null or $group->getOwnerId() != $this->getAuthenticatedUser()->getUserId()) {
            $this->response->redirect('error/fourofour');
            $this->response->send();
            return;
        }
        $this->view->groupId = $groupId;
    }

    public function AjaxCreateGroupAction()
    {
        $this->getRequestFilter()
            ->addRequired(
                'groupname',
                \Apprecie\Library\Security\ParameterTypes::ANY,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $groupName = $this->request->getPost('groupname');
        if ($groupName != null && $groupName != '' && $groupName != ' ') {
            $groupExists = PortalMemberGroup::query();
            $groupExists->where('groupname=:1:');
            $groupExists->andWhere('ownerId=:2:');
            $groupExists->bind([1 => $groupName, 2 => $this->getAuthenticatedUser()->getUserId()]);
            $result = $groupExists->execute();

            if ($result->count() == 0) {
                try {
                    $group = new PortalMemberGroup();
                    $group->setGroupName($groupName);
                    $group->setPortalId($this->getAuthenticatedUser()->getPortalId());
                    $group->setOwner($this->getAuthenticatedUser()->getUserId());
                    $group->save();
                    _jm('success', _g('The group was created successfully'));
                } catch (\Phalcon\Exception $ex) {
                    _jm('failed', $ex->getMessage());
                }

            } else {
                _jm('failed', _g('This group already exists'));
            }

        } else {
            _jm('failed', _g('The group could not be created as the group name was invalid'));
        }
    }

    public function AjaxDeleteGroupAction()
    {
        $this->getRequestFilter()
            ->addRequired(
                'groupId',
                \Apprecie\Library\Security\ParameterTypes::INT,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $groupId = $this->request->getPost('groupId');
        $group = PortalMemberGroup::findFirstBy('groupId', $groupId);

        if (\Apprecie\Library\Acl\AccessControl::userCanOperateGroup($this->getAuthenticatedUser(), $group)) {
            _jm('failed', 'Authentication Failed');
            return;
        }

        foreach (PortalMembersInGroups::find("groupId=" . $group->getGroupId()) as $member) {
            $member->delete();
        }

        $group->delete();

        _jm('success', 'The group was successfully deleted');
    }

    public function AjaxEditGroupAction()
    {
        $this->getRequestFilter()
            ->addRequired(
                'groupId',
                \Apprecie\Library\Security\ParameterTypes::INT,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->addRequired(
                'groupName',
                \Apprecie\Library\Security\ParameterTypes::ANY,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $groupId = $this->request->getPost('groupId');
        $group = PortalMemberGroup::findFirstBy('groupId', $groupId);
        $groupName = $this->request->getPost('groupName');


        if (!\Apprecie\Library\Acl\AccessControl::userCanOperateGroup($this->getAuthenticatedUser(), $group)) {
            _jm('failed', _g('Authentication Failed'));
            return;
        }

        if (strlen($groupName) > 0 and $groupName != '') {
            $group->setGroupname($this->request->getPost('groupName'));
            $group->save();
            _jm('success', 'The group was edited successfully');
        } else {
            _jm('failed', 'The group name was invalid');
        }
    }

    public function AjaxGetUsersInGroupAction($pageNumber = 1)
    {
        $this->getRequestFilter()
            ->addRequired(
                'groupId',
                \Apprecie\Library\Security\ParameterTypes::INT,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->addNonRequestRequired(
                'pageNumber',
                $pageNumber,
                \Apprecie\Library\Security\ParameterTypes::INT,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $group = PortalMemberGroup::findFirstBy('groupId', $this->getRequestFilter()->get('groupId'));

        if (!\Apprecie\Library\Acl\AccessControl::userCanOperateGroup($this->getAuthenticatedUser(), $group)) {
            _jm('failed', _g('Authentication Failed'));
            return;
        }

        $returnArray = [];
        foreach ($group->getMembers() as $member) {
            $user = [];

            //remove deleted user from this group
            $userRecord = $member->getUser();
            if ($userRecord->getIsDeleted()) {
                $group->removeUser($member->getUserId());
                continue;
            }

            $user['image'] = Assets::getUserProfileImage($member->getUserId());
            $user['userId'] = $member->getUser()->getUserProfile()->getUserId();
            $user['firstName'] = $member->getUser()->getUserProfile()->getFirstName();
            $user['lastName'] = $member->getUser()->getUserProfile()->getLastName();
            $user['emailAddress'] = $member->getUser()->getUserProfile()->getEmail();
            $user['role'] = $member->getUser()->getRoles()[0]->getRole()->getDescription();
            $user['organisation'] = $member->getUser()->getOrganisation()->getOrganisationName();
            $user['reference'] = $member->getPortalUser()->getReference();

            if ($member->getUserLogin()->getSuspended() == 1) {
                $user['login'] = _g('Suspended');
            } elseif ($member->getUserLogin()->getPassword() == 'pending') {
                $user['login'] = _g('Unregistered');
            } elseif ($member->getUserLogin()->getSuspended() == null || $member->getUserLogin()->getSuspended() == '0'
            ) {
                $user['login'] = _g('Enabled');
            }

            switch ($member->getStatus()) {
                case 'active':
                    $user['account'] = _g('Active');
                    break;
                case 'deactivated':
                    $user['account'] = _g('Deactivated');
                    break;
                case 'pending':
                    $user['account'] = _g('Unregistered');
                    break;
            }
            array_push($returnArray, $user);
        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $returnArray,
                "limit" => 15,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();
        echo json_encode($page);
    }

    public function AjaxGetAllUsersInGroupAction()
    {
        $this->getRequestFilter()
            ->addRequired(
                'groupId',
                \Apprecie\Library\Security\ParameterTypes::INT,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $groupId = $this->request->getPost('groupId');
        $group = PortalMemberGroup::findFirstBy('groupId', $groupId);

        if (!\Apprecie\Library\Acl\AccessControl::userCanOperateGroup($this->getAuthenticatedUser(), $group)) {
            _jm('failed', 'Authentication Failed');
            return;
        }

        $returnArray = [];
        foreach ($group->getMembers() as $member) {
            $returnArray[] = $member->getUser()->getUserProfile()->getUserId();
        }

        echo json_encode($returnArray);
    }

    public function AjaxGetGroupsAction($pageNumber = 1)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired(
                'pageNumber',
                $pageNumber,
                \Apprecie\Library\Security\ParameterTypes::INT,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);


        $groupsQuery = PortalMemberGroup::query(
        ); //@todo gh this is secure but might need central group control for sharing
        $groupsQuery->where('ownerId=:1:');
        $groupsQuery->orderBy('groupname');
        $groupsQuery->bind([1 => $this->getAuthenticatedUser()->getUserId()]);

        $results = $groupsQuery->execute();

        $returnArray['items'] = [];
        foreach ($results as $result) {
            $group['groupId'] = $result->getGroupId();
            $group['groupName'] = $result->getGroupName();
            $group['users'] = $result->getMembers()->count();
            array_push($returnArray['items'], $group);
        }

        if (count($returnArray['items']) == 0) {
            _jm('failed', _g('No groups were found'));
        } else {
            $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
                array(
                    "data" => $returnArray['items'],
                    "limit" => 20,
                    "page" => $pageNumber
                )
            );

            $page = $paginator->getPaginate();
            echo json_encode($page);
        }
    }

    public function AjaxAddUserToGroupAction()
    {
        $this->getRequestFilter()
            ->addRequired(
                'groupId',
                \Apprecie\Library\Security\ParameterTypes::INT,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->addRequired(
                'users',
                \Apprecie\Library\Security\ParameterTypes::ANY,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $groupId = $this->request->getPost('groupId');
        $group = PortalMemberGroup::findFirstBy('groupId', $groupId);

        if (!\Apprecie\Library\Acl\AccessControl::userCanOperateGroup($this->getAuthenticatedUser(), $group)) {
            _jm('failed', 'Authentication Failed');
            return;
        }

        $this->view->disable();
        $users = $this->request->getPost('users');

        // Secure Ajax Request
        foreach ($users as $user) {
            $user = User::resolve($user);
            if (!$user->canBeSeenBy($this->getAuthenticatedUser(), null)) {
                _jm('failed', _g('Authentication failed'));
                $this->response->send();
            }
        }

        $group = PortalMemberGroup::findFirstBy('groupId', $this->request->getPost('groupId'));
        if ($group->getOwnerId() == $this->getAuthenticatedUser()->getUserId()) {
            $group->addUser($this->request->getPost('users'));
            _jm('success', _g('User was added successfully'));
        }
    }

    public function AjaxRemoveFromGroupAction()
    {
        $this->getRequestFilter()
            ->addRequired(
                'groupId',
                \Apprecie\Library\Security\ParameterTypes::INT,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->addRequired(
                'users',
                \Apprecie\Library\Security\ParameterTypes::ANY,
                true,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $groupId = $this->request->getPost('groupId');
        $group = PortalMemberGroup::findFirstBy('groupId', $groupId);

        if (!\Apprecie\Library\Acl\AccessControl::userCanOperateGroup($this->getAuthenticatedUser(), $group)) {
            _jm('failed', 'Authentication Failed');
            return;
        }

        $group->removeUser($this->request->getPost('users'));
        _jm('success', _g('User was removed successfully'));

    }
}