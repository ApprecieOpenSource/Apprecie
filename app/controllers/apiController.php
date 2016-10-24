<?php

/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 08/12/14
 * Time: 13:19
 */
class ApiController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    /**
     * default action that shows the list of users across all portals
     */
    public function indexAction()
    {
        $this->view->setLayout('blank');
    }

    public function portalQuotaAction()
    {
        $this->getRequestFilter()
            ->addRequired('organisationId', \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request, true, false);

        \Apprecie\Library\Acl\AccessControl::userCanViewOrganisationQuotas($this->getAuthenticatedUser(), $this->getRequestFilter()->get('organisationId'));

        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
        if ($this->request->isPost() and $this->request->getPost('organisationId')) {
            $quota = Quotas::findFirstBy('organisationId', $this->request->getPost('organisationId'));
            if (count($quota) == 0) {
                echo json_encode(array('Error' => _g('No quotas set on this organisation! Should be impossible!')));
            } else {
                $quotas = $quota->toArray();
                echo json_encode($quotas);
            }

        } else {
            echo json_encode(array('Error' => _g('Missing Portal ID')));
        }
    }

    public function portalOrganisationsAction()
    {
        $this->getRequestFilter()
            ->addRequired('portalId', \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request, true, false);

        if(! \Apprecie\Library\Acl\AccessControl::userCanViewPortalOrganisations($this->getAuthenticatedUser(), $this->getRequestFilter()->get('portalId'))){
            return;
        }

        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');

        $organisations = Organisation::findBy('portalId', $this->request->getPost('portalId'));
        echo json_encode($organisations->toArray());
    }

    public function userLookupAction()
    {
        $this->getRequestFilter()
            ->addFilter('portalId', \Apprecie\Library\Security\ParameterTypes::INT)
            ->execute($this->request);

        if(! $this->request->isAjax()) {
            return;
        }

        $this->view->disable();
        $roleName = $this->request->getPost('roleId');
        $portalId = $this->request->getPost('portalId');
        $firstName = $this->request->getPost('firstName');
        $lastName = $this->request->getPost('lastName');
        $organisationId = $this->request->getPost('organisationId');

        if ($roleName != 'SystemAdministrator') {
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);
        }

        if($organisationId == 'All') {
            $organisationId = null;
        }

        if($roleName == 'all') {
            $roleId = null;
        } else {
            $role = Role::resolve($roleName);
            $roleId = $role->getRoleId();
        }

        $users = \Apprecie\Library\SearchFilters\Users\UserSearchFilterUtility::userSearch(null, null, null, $roleName != 'SystemAdministrator'?  $organisationId : null, $roleId );

        $userArray = array();
        foreach ($users as $user) {
            if($user->getIsDeleted()) {
                continue;
            }

            if ($firstName != '') {
                if (strpos(strtolower($user->getUserProfile()->getFirstname()), strtolower($firstName)) !== false) {
                    $userDetails = $user->getUserProfile()->toArray();
                    $userDetails['userId'] = $user->getUserId();
                    $userDetails['reference'] = $user->getUserReference();
                    array_push($userArray, $userDetails);
                }
            } elseif ($lastName != '') {
                if (strpos(strtolower($user->getUserProfile()->getLastname()), strtolower($lastName)) !== false) {
                    $userDetails = $user->getUserProfile()->toArray();
                    $userDetails['userId'] = $user->getUserId();
                    $userDetails['reference'] = $user->getUserReference();
                    array_push($userArray, $userDetails);
                }
            } else {
                $userDetails = $user->getUserProfile()->toArray();
                $userDetails['userId'] = $user->getUserId();
                $userDetails['reference'] = $user->getUserReference();
                array_push($userArray, $userDetails);
            }

        }
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
        echo json_encode($userArray);
    }

    public function getOrganisationChildrenAction($organisationId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('organisationId', $organisationId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $organisation = Organisation::resolve($organisationId);

        if(! \Apprecie\Library\Acl\AccessControl::userCanViewPortalOrganisations($this->getAuthenticatedUser(), $organisation->getPortalId())){
            return;
        }

        $organisationArray = [];
        $organisations = OrganisationParents::findBy('parentId', $organisationId);
        foreach ($organisations as $organisation) {
            $data = Organisation::findFirstBy('organisationId', $organisation->getOrganisationId());
            $organisationArray[] = array(
                'organisationId' => $data->getOrganisationId(),
                'name' => $data->getOrganisationName()
            );
        }
        echo json_encode($organisationArray);
    }

    public function getPortalOrganisationsAction($portalId, $onlyManaged = false)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('portalId', $portalId, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        if(! \Apprecie\Library\Acl\AccessControl::userCanViewPortalOrganisations($this->getAuthenticatedUser(), $portalId)){
            return;
        }

        $this->view->disable();

        if ($this->request->hasPost('hasUsersInRole')) {
            $hasUsersInRole = $this->request->getPost('hasUsersInRole');
        } else {
            $hasUsersInRole = null;
        }

        $organisations = Organisation::findBy('portalId', $portalId);
        $orgsResult = array();

        foreach($organisations as $org) {

            if (($onlyManaged && !$org->hasManagers())) {
                continue;
            }

            if ($hasUsersInRole) {
                $usersInRole = Organisation::getUsersInRole($hasUsersInRole, $org);
                if ($usersInRole->count() < 1) {
                    continue;
                }
            }

            $orgsResult[] = $org->toArray();
        }

        echo json_encode($orgsResult);
    }

    public function getPrimaryOrganisationAction($portalId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('portalId', $portalId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        if(! \Apprecie\Library\Acl\AccessControl::userCanViewPortalOrganisations($this->getAuthenticatedUser(), $portalId)){
            return;
        }

        $organisationArray = [];

        $data = Organisation::query();
        $data->andWhere("portalId =:pid:", array('pid' => $portalId));
        $data->andWhere('isPortalOwner =:powner:', array('powner' => 1));
        $resultset = $data->execute();

        foreach ($resultset as $result) {
            $organisationArray[] = array(
                'organisationId' => $result->getOrganisationId(),
                'name' => $result->getOrganisationName()
            );
        }
        echo json_encode($organisationArray);
    }

    public function getOrganisationAction($organisationId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('organisationId', $organisationId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $organisation = Organisation::resolve($organisationId);

        if(! \Apprecie\Library\Acl\AccessControl::userCanViewPortalOrganisations($this->getAuthenticatedUser(), $organisation->getPortalId())){
            return;
        }

        $data = Organisation::findFirstBy('organisationId', $organisationId);
        $quotas = Quotas::findFirstBy('organisationId', $organisationId);
        echo json_encode(array('organisation' => $data->toArray(), 'quota' => $quotas->toArray()));
    }

    public function getOrganisationUsersAction($organisationId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('organisationId', $organisationId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $users = \Apprecie\Library\SearchFilters\Users\UserSearchFilterUtility::userSearch(null, null, null, $organisationId);

        echo json_encode($users->toArray());
    }

    public function getOrganisationUsersFullAction($organisationId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('organisationId', $organisationId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $organisation = Organisation::resolve($organisationId);


        $users = \Apprecie\Library\SearchFilters\Users\UserSearchFilterUtility::userSearch(null, null, null, $organisation->getOrganisationId());

        $usersArray = [];

        $resultsPerPage = 10;
        $pageNumber = 1;

        if ($this->request->getPost('pageNumber') <> null) {
            $pageNumber = $this->request->getPost('pageNumber');
        }

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $users,
                "limit" => $resultsPerPage,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $usersArray['ThisPageNumber'] = $pageNumber;
        $usersArray['PageCount'] = $page->total_pages;
        $usersArray['PageResultCount'] = count($page->items);
        $usersArray['TotalResultCount'] = count($users);

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($organisation->getPortalId());
        foreach ($page->items as $user) {
            if (!$user->getIsDeleted()) {
                $user->reference = $user->getUserReference();
                $user->role = $user->getRoles()[0]->getRole()->getDescription();
                $user->profile = $user->getUserProfile()->toArray();
                $user->details = $user->getUser()->toArray();
                $usersArray['items'][] = $user;
            }
        }
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        echo json_encode($usersArray);
    }

    /* GH  I cannot find a link up to this method anymore.  Lets delete if we confirm not used
    public function getEventAction($eventId)
    {
        $event = Event::query();
        $event->join('Item');
        $event->where('eventId=:1:', [1 => $eventId]);
        $result = $event->execute();
        echo json_encode($result->toArray());
    }*/


    public function sendMessageAction($threadId = null)
    {
        $this->getRequestFilter()
            ->addRequired('targetUser', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('contact-message', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('contact-subject', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('targetUser', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addFilter('itemId',\Apprecie\Library\Security\FilterTypes::INT)
            ->addFilter('responseTo',\Apprecie\Library\Security\FilterTypes::INT)
            ->execute($this->request);

        list($targetUser, $contactMessage, $contactSubject, $itemId, $responseTo) = $this->getRequestFilter()->getAll();

        $user = $this->getAuthenticatedUser();
        $targetUser = User::resolve($targetUser);

        $message = new Message();

        if ($itemId != null) {
            $message->setReferenceItem($itemId);
        }
        if ($responseTo != null) {
            $message->setResponseToMessage($responseTo);
        }

        $message->setTargetUser($targetUser->getUserId());
        $message->setSourceUser($user->getUserId());
        $message->setBody($contactMessage);
        $message->setTitle($contactSubject);
        $message->setSourcePortal($user->getPortalId());
        $message->setSourceDescription(
            $user->getUserProfile()->getFullName()
        );
        $message->setSent(date('Y-m-d H:i:s'));
        $message->setSourceOrganisation($user->getOrganisationId());
        $message->save();

        if ($threadId == null) {
            $thread = new MessageThread();
            $thread->setStartedByUser($message->getSourceUser());
            $thread->setFirstRecipientUser($message->getTargetUser());
            if ($this->request->getPost('messageThreadType') != null && (new \Apprecie\Library\Messaging\MessageThreadType($this->request->getPost('messageThreadType')))->getKeyByValue($this->request->getPost('messageThreadType')) != false) {
                $thread->setType($this->request->getPost('messageThreadType'));
            } else {
                $thread->setType(\Apprecie\Library\Messaging\MessageThreadType::GENERIC);
            }
            $thread->create();
            $thread->addMessage($message);
        } else {
            $thread = MessageThread::resolve($threadId);
            $thread->addMessage($message);
        }

        echo json_encode(array('status' => 'success'));
    }

    public function AjaxGetCurrentUserChildrenAction($pageNumber)
    {
        $this->getRequestFilter()
            ->addRequired('role', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();

        //GH secure as only provides active users children.
        $results = $this->getAuthenticatedUser()->resolveChildren($this->request->getPost('role'));

        $registrationState = $this->request->getPost('registrationState');

        $resultsArray = [];
        foreach ($results as $result) {
            if ($registrationState == null) {
                $user = $result->toArray();
                $user['profile'] = $result->getUserProfile();
                $user['role'] = $result->getRoles()[0]->getRole()->getName();
                $resultsArray[] = $user;
            } else {
                switch ($registrationState) {
                    case 'unregistered':
                        if ($result->getUserLogin()->getPassword() == 'pending') {
                            $user = $result->toArray();
                            $user['profile'] = $result->getUserProfile();
                            $user['role'] = $result->getRoles()[0]->getRole()->getName();
                            $resultsArray[] = $user;
                        }
                        break;
                }
            }

        }
        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $resultsArray,
                "limit" => 10,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();
        if (count($resultsArray) == 0) {
            $page->message = 'No people were found';
        }
        echo json_encode($page);
    }

    public function SendSuggestionAction($itemId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('users', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($itemId);

        switch ($item->getType()) {
            case "event":
                $users = $this->request->getPost('users');
                foreach ($users as $userId) {
                    $user = User::resolve($userId, false);

                    if ($user != null) {
                        if($user->getIsDeleted()) continue;

                        $user->clearStaticCache();

                        if ($user->getPortalUser()->sendEventSuggestion($item->getEvent())) {
                            $results = ['status' => 'success', 'message' => 'Suggestion sent successfully'];
                        } else {
                            $results = ['status' => 'success', 'message' => _ms($user->getPortalUser()->getMessages())];
                        }
                    }
                }
                break;
        }

        if (count($users) == 0) {
            $results['status'] = 'failed';
            $results['message'] = 'No users could be found to send the suggestion to';
        }

        echo json_encode($results);
    }

    public function SendExternalSuggestionAction($itemId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = \Item::resolve($itemId);

        $status = 'failed';
        $message = '';

        if ($this->request->getPost('email') == null) {
            $message = _g('You must provide an email address');
        }

        $email = $this->request->getPost('email');
        $emails = explode(';', $email);

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = _g('A valid email address is required');
            }
        }

        if ($message == '') {

            $sender = $this->getAuthenticatedUser();
            $emailUtil = new \Apprecie\Library\Mail\EmailUtility();

            foreach ($emails as $toEmail) {

                if (!$emailUtil->sendUserEmail(
                    \Apprecie\Library\Mail\EmailTemplateType::SUGGESTION_OFF_PORTAL,
                    $sender,
                    null,
                    $item->getEvent(),
                    array('recipientEmail' => $toEmail)
                )) {
                    $message = _ms($emailUtil);
                } else {
                    $status = 'success';
                    $message = _g('Email sent');
                }
            }
        }

        _jm($status, $message);
    }

    public function ApproveItemAction($itemId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($itemId);

        if(! \Apprecie\Library\Acl\AccessControl::userCanApproveItem($this->getAuthenticatedUser(), $item)) {
            _jm('failed', _g('You do not have the required permission to approve this item'));
            return;
        }

        $approval = $item->getRelatedApproval();
        $administrationFee = $this->request->getPost('administrationFee', "float", 0);
        $reservationAllowed = $this->request->getPost('reservationAllowed', "string", 'false');
        $reservationFee = $this->request->getPost('reservationFee', array("float"), null);
        $reservationPeriod = $this->request->getPost('reservationPeriod', array("int"), null);
        if ($reservationAllowed === 'false') {
            $reservationFee = null;
            $reservationPeriod = null;
        } elseif ($reservationFee === '') {
            $reservationFee = 0;
        }

        $numVal = new \Apprecie\Library\Validation\NumericHelper();

        if ((!is_numeric($this->request->getPost('administrationFee')) && $this->request->hasPost('administrationFee'))
            || ($reservationAllowed == 'true' && !is_numeric($this->request->getPost('reservationFee')))
            || ($reservationAllowed == 'true' && !is_numeric($this->request->getPost('reservationPeriod')))

        ) {
            _jm(
                'failed',
                _g('Input was invalid.  Expected numeric.')
            );
        } else {
            if ($numVal->moreThanNDecimals($reservationFee) || $numVal->moreThanNDecimals($administrationFee)) {
                _jm(
                    'failed',
                    _g('Reservation fee and Administration fee should contain no more than two decimal places (X.XX)')
                );
            } elseif ($reservationAllowed == 'true' && ($reservationFee < 0)) {
                _jm('failed', _g('Reservation fee must be a positive number'));
            } elseif (is_numeric($administrationFee) && $administrationFee < 0) {
                _jm('failed', _g('Administration Fee should be a whole positive number'));
            } elseif ($reservationAllowed == 'true' && ($reservationPeriod < 0 || !$numVal->isWholeNumber(
                        $this->request->getPost('reservationPeriod')
                    ))
            ) {
                _jm('failed', _g('Reservation period should be a whole positive number'));
            } elseif ($reservationAllowed == 'true' && ($reservationFee !== null && $reservationPeriod === null)) {
                _jm('failed', _g('With a reservation fee set, the reservation period must be 1 day or more'));
            } elseif ($reservationAllowed == 'true' && ($reservationPeriod !== null && $reservationFee === null)) {
                _jm('failed', _g('With a reservation period set, a reservation fee must also be set'));
            } elseif ($reservationPeriod > 0 && (strtotime(
                        $approval->getItem()->getEvent()->getBookingEndDate()
                    ) < (time() + ($reservationPeriod * 86000)))
            ) {
                _jm('failed', _g('Reservation period exceeds the booking period'));
            } else {
                if ($approval == null) {
                    $approval = new ItemApproval();
                    $approval->setItemId($itemId);
                    $approval->setCreatingOrganisationId($item->getSourceOrganisationId());
                    $approval->setVerifyingOrganisationId($this->getAuthenticatedUser()->getOrganisationId());
                    $approval->create();
                }

                $approval->approveItem();

                if ($approval->hasMessages()) {
                    _jm('failed', _ms($approval));
                } else {
                    $itemRecord = $approval->getItem();
                    $itemRecord->setAdminFee($administrationFee > 0 ? $administrationFee * 100 : 0);
                    if($reservationAllowed == 'true') {
                        $itemRecord->setReservationFee($reservationFee > 0 ? $reservationFee * 100 : 0);
                        $itemRecord->setReservationLength($reservationPeriod);
                    } else {
                        $itemRecord->setReservationFee(null);
                        $itemRecord->setReservationLength(null);
                    }
                    $itemRecord->save();
                    if ($itemRecord->hasMessages()) {
                        _jm('failed', _ms($itemRecord));

                    } else {
                        _jm('success', _g('Item was approved successfully'));
                    }
                }
            }
        }
    }

    public function RejectItemAction($itemId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($itemId);

        if(! \Apprecie\Library\Acl\AccessControl::userCanApproveItem($this->getAuthenticatedUser(), $item)) {
            _jm('failed', _g('You do not have the required permission to reject this item'));
            return;
        }

        $reason = $this->request->get('reason');
        $item = ItemApproval::findFirstBy('itemId', $itemId);
        $item->denyItem($reason);
        if ($item->hasMessages()) {
            _jm('failed', _ms($item));
        } else {
            _jm('success', _g('Item was rejected successfully'));
        }
    }

    /**
     * Captures the email body without sending the email and returns in message if success, else message contains the
     * error
     *
     */
    public function emailPreviewAction()
    {
        $status = 'success';
        $message = '';

        $this->view->disable();

        try {

            $portalId = $this->request->getPost('portalId');

            if ($portalId == null) {
                $portal = $this->getDI()->getDefault()->get('portal');
            } else {
                $portal = Portal::resolve($portalId);
            }
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portal);

            $emailType = $this->request->getPost('emailType');
            $event = $this->request->getPost('event', 'int', null);

            \Apprecie\Library\Mail\Templates\EmailTemplate::setBlockSend(true);

            switch ($emailType) {
                case 'signup' :
                {
                    $user = User::resolve($this->request->getPost('user'));
                    $oldPortal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();
                    \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortal());
                    $portalUser = $user->getPortalUser();
                    $result = $portalUser->sendRegistrationEmail();
                    \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($oldPortal);
                    if (!$result) {
                        throw new \Exception(_ms($portalUser));
                    }
                    break;
                }
                case 'suggestion' :
                {
                    $event = Event::resolve($event);
                    $user = User::resolve($this->request->getPost('user'));
                    $portalUser = $user->getPortalUser();
                    if (!$portalUser->sendEventSuggestion($event)) {
                        throw new \Exception(_ms($portalUser));
                    }
                    break;
                }
                case 'externalSuggestion' :
                {
                    $sender = $this->getAuthenticatedUser();
                    $event = Event::resolve($event);

                    $emailUtil = new \Apprecie\Library\Mail\EmailUtility();
                    $email = explode(';', $this->request->getPost('email'));

                    $emailUtil->sendUserEmail(
                        \Apprecie\Library\Mail\EmailTemplateType::SUGGESTION_OFF_PORTAL,
                        $sender,
                        null,
                        $event,
                        array('recipientEmail' => $email[0])
                    );

                    break;
                }
                case 'invitation' :
                {
                    $event = Event::resolve($event);
                    $sender = $this->getAuthenticatedUser();
                    $user = User::resolve($this->request->getPost('user'));

                    $emailUtil = new \Apprecie\Library\Mail\EmailUtility();

                    $emailUtil->sendUserEmail(
                        \Apprecie\Library\Mail\EmailTemplateType::INVITATION,
                        $sender,
                        $user,
                        $event,
                        array(
                            'rsvpLink' => \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                                null,
                                'rsvp',
                                'event',
                                ['#']
                            )
                        )
                    );
                }
            }
            $message = \Apprecie\Library\Mail\Templates\EmailTemplate::getLastEmailBody();
        } catch (\Exception $ex) {
            $status = 'failed';
            $message = $ex->getMessage();
        } finally {
            \Apprecie\Library\Mail\Templates\EmailTemplate::setBlockSend(false);
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
        }

        echo json_encode(
            array(
                'status' => $status,
                'message' => $message,
                'confirm' => _g('Please confirm sending the following email.')
            )
        );
    }

    public function dismissNoticeAction()
    {
        $this->getRequestFilter()
            ->addRequired('id', \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $status = 'failure';
        $message = 'no permission';

        $this->view->disable();

        if ($this->getAuthenticatedUser() != false) {

            $noticeId = $this->request->getPost('id', 'int', null);

            try {
                if ($noticeId) {
                    $userNotice = UserNotification::resolve($noticeId);

                    if ($userNotice->getUserId() == $this->getAuthenticatedUser()->getUserId()) {
                        $userNotice->setDismissed(date("Y-m-d H:i:s"));
                        if ($userNotice->update()) {
                            $status = 'success';
                            $message = 'Notice dismissed';
                        } else {
                            $status = 'failed';
                            $message = _ms($userNotice);
                        }
                    }
                }
            } catch (\Exception $ex) {
                $status = 'failed';
                $message = $ex->getMessage();
            }
        }

        _jm($status, $message);
    }

    public function ArrangeAction($itemId)
    {
        $this->getRequestFilter()
        ->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
        ->execute($this->request);

        $item = Item::resolve($itemId);

        if(! \Apprecie\Library\Acl\AccessControl::userCanViewItem($this->getAuthenticatedUser(), $item)) {
            _jm('failed', _g('You do not have the required permission to arrange this item'));
            return;
        }

        $newEventId = -1;
        $message = '';

        if (!$this->request->hasPost('package-size') || $this->request->getPost('package-size') == null) {
            $message = _g('Number of attendees is required');
            $status = 'failed';
        } else {

            try {
                $byArrangement = Item::resolve($itemId);
                $byArrangement = $byArrangement->getEvent();

                if (strpos($this->request->getPost('address-id'), '|') !== false) {
                    $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
                    $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
                } else {
                    $addressId = $this->request->getPost('address-id');
                }

                $startDate = explode('/', $this->request->getPost('confirmed-startdate'));
                $endDate = explode('/', $this->request->getPost('confirmed-enddate'));
                $startDateTime = $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0] . ' ' . $this->request->getPost(
                        'confirmed-starttime'
                    );
                $endDateTime = $endDate[2] . '-' . $endDate[1] . '-' . $endDate[0] . ' ' . $this->request->getPost(
                        'confirmed-endtime'
                    );

                $event = $byArrangement->beginArrange
                    (
                        $startDateTime,
                        $endDateTime,
                        $this->getAuthenticatedUser(),
                        $addressId,
                        $this->request->getPost('request-notes'),
                        $this->request->getPost('package-size'),
                        $this->request->getPost('number-packages')
                    );

                if (!$event) {
                    $status = 'failed';
                    $message = _ms($byArrangement);
                } else {
                    $status = 'created';
                    $newEventId = $event->getItemId();
                }
            } catch (\Exception $ex) {
                $status = 'failed';
                $message = $ex->getMessage() . ' ' . $ex->getTraceAsString();
            }
        }
        echo json_encode(array('status' => $status, 'itemId' => $newEventId, 'message' => $message));
    }

    public function confirmArrangementPreviewAction()
    {
        $message = _g(
            'To confirm the Arrangement of this Event you will now be taken through the Vault Item Wizard to ensure that all details are valid. If you do not wish to arrange any more packages of this Event after this one, please unpublish the original Personalised Item from the Item Management page or at the end of this confirmation process.'
        );

        echo json_encode(
            array(
                'status' => 'success',
                'message' => $message,
                'confirm' => _g('Would you like to confirm this arrangement?')
            )
        );
    }

    public function confirmArrangementMessagePreviewAction()
    {
        $event = Event::resolve($this->request->getPost('eventId'));

        $target = $event->getArrangedFor();

        $bookingEndDateParts = explode('/', $this->request->getPost('bookingEndDate'));
        $bookingEndDate = $bookingEndDateParts[0] . '-' . $bookingEndDateParts[1] . '-' . $bookingEndDateParts[2] . ' 23:59';

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($target->getPortalId());
        $buffer = _p(
            _g('Congratulations! Your arrangement request for {item} has been approved.', ['item' => _eh($event->getTitle())])
        );
        $buffer .= _p(
            _g('In order to complete the transaction, please click on the Referenced Item above and check all of the details. After revising, click on the Purchase button to complete the payment.')
        );
        $buffer .= _p(
            _g('Please note that the Item will be available for you to purchase till: {paymentExpirationDate}. Afterwards, the Purchase option will be disabled.', array('paymentExpirationDate' => _eh($bookingEndDate)))
        );
        $buffer .= _p(
            _g('Should you have any questions regarding this item, please respond to this message using the Reply button above.')
        );
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        echo json_encode(
            array(
                'status' => 'success',
                'message' => $buffer,
                'confirm' => _g('Please confirm the Approval of the following event.')
            )
        );
    }

    public function rejectArrangementPreviewAction()
    {
        $reason = $this->request->getPost('reason');
        $event = Event::resolve($this->request->getPost('eventId'));

        $target = $event->getArrangedFor();

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($target->getPortalId());
        $buffer = $buffer = _p(_g('Dear {person},', ['person' => $target->getUserProfile()->getFullName()]));
        $buffer .= _p(
            _g(
                'Sorry, your arrangement request for {itemName} has been declined. {supplier} have given this reason:',
                ['itemName' => $event->getTitle(), 'supplier' => $event->getSourceOrganisation()->getOrganisationName()]
            )
        );
        $buffer .= _p($reason);
        $buffer .= _p(
            _g(
                'If you wish to dispute this reason, please respond to this message using the Reply button above, else we encourage you to check your vault for other opportunities that may be available for you to enjoy.'
            )
        );
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        echo json_encode(
            array(
                'status' => 'success',
                'message' => $buffer,
                'confirm' => _g('Please confirm the rejection of the following event.')
            )
        );
    }

    public function rejectArrangementAction()
    {
        $this->getRequestFilter()
            ->addRequired('eventId', \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $event = Event::resolve($this->request->get('eventId'));

        if(! \Apprecie\Library\Acl\AccessControl::userCanEditItem($this->getAuthenticatedUser(), $event->getItemId())) {
            _jm('failed', _g('You do not have the required permission to reject this item'));
        }

        $message = '';
        $status = 'success';

        try {

            if (!$event->rejectArrangement($this->request->getPost('reason'))) {
                $message = _ms($event);
                $status = 'failed';
            }
        } catch (\Exception $ex) {
            $status = 'failure';
            $message = $ex->getMessage();
        } finally {

        }

        _jm($status, $message);
    }

    public function confirmArrangementAction()
    {
        $this->getRequestFilter()
            ->addRequired('eventId', \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $event = Event::resolve($this->request->get('eventId'));

        if(! \Apprecie\Library\Acl\AccessControl::userCanEditItem($this->getAuthenticatedUser(), $event->getItemId())) {
            _jm('failed', _g('You do not have the required permission to confirm this item'));
        }

        $message = '';
        $status = 'success';

        try {
            $event = Event::resolve($this->request->get('eventId'));

            if (!$event->confirmArrangement()) {
                $message = _ms($event);
                $status = 'failed';
            }
        } catch (\Exception $ex) {
            $status = 'failure';
            $message = $ex->getMessage();
        } finally {

        }

        _jm($status, $message);
    }

    public function quickCreateUserAction()
    {
        if (!$this->request->isPost()) {
            return false;
        }

        if (!$this->request->isAjax()) {
            return false;
        }

        $this->checkCSRF(true, true, true);

        $email = null;
        if ($this->request->hasPost('emailaddress')) {
            $email = $this->request->getPost('emailaddress');
        }

        $user = new \Apprecie\Library\Users\UserEx();
        $newUser = $user->createUserWithProfileAndLogin(
            $email,
            null,
            $this->request->getPost('firstname'),
            $this->request->getPost('lastname'),
            null,
            null,
            $this->request->getPost('reference-code')
        );

        if (!$newUser) {
            _jm('failed', '');
            return;
        }

        $thisUser = $this->getAuthenticatedUser();

        $newUser->addRole(($thisUser->hasRole(\Apprecie\Library\Users\UserRole::CLIENT)) ? \Apprecie\Library\Users\UserRole::CONTACT : \Apprecie\Library\Users\UserRole::CLIENT);
        $newUser->setTier(($thisUser->getTier()) ? $thisUser->getTier() : 1);
        $newUser->setCreatingUser(
            $thisUser->getUserId()
        );
        $newUser->setChildOf(
            $thisUser
        );
        $newUser->setOrganisationId(
            $thisUser->getOrganisationId()
        );

        if (!$newUser->update()) {
            _jm('failed', '');
            return;
        }

        if (!$thisUser->hasRole(\Apprecie\Library\Users\UserRole::CLIENT)) {
            $contactPreferences = $newUser->getUserContactPreferences();
            $contactPreferences->setAlertsAndNotifications(true);
            $contactPreferences->setInvitations(true);
            $contactPreferences->setSuggestions(true);
            $contactPreferences->setPartnerCommunications(true);
            $contactPreferences->setUpdatesAndNewsletters(true);

            if (!$contactPreferences->update()) {
                _jm('failed', '');
                return;
            }
        }

        _jm('success', '');
    }

    /**
     * Returns the posted users data with their interests (top level)
     */
    public function AjaxGetUserAction(){
        if($this->request->isAjax()){
            $userId=$this->request->getPost('userId');

            if($userId==null){
                $userId=$this->getAuthenticatedUser()->getUserId();
            }

            $user=User::findFirstBy('userId',$userId);

            if($user->getUserId()==$this->getAuthenticatedUser()->getUserId() || $user->canBeSeenBy($this->getAuthenticatedUser())){
                $returnArray=[];
                $returnArray['userId']=$user->getUserId();
                $returnArray['record']=$user->toArray();
                $returnArray['organisation']['id']=$user->getOrganisation()->getOrganisationId();
                $returnArray['organisation']['name']=$user->getOrganisation()->getOrganisationName();
                $returnArray['interests']=[];

                $userInterestResults=$user->getInterests();
                foreach ($userInterestResults as $userInterestResult) {
                    $parentInterests = $userInterestResult->getParents();
                    foreach ($parentInterests as $parent) {
                        if (!in_array($parent->getInterestId(), $returnArray['interests'])) {
                            $returnArray['interests'][] = $parent->getInterestId();
                        }
                    }
                }
                echo json_encode($returnArray);
            }
        }
    }

    /**
     * Returns the posted users children with their interests (top level)
     */
    public function AjaxGetUserChildrenAction(){
        if($this->request->isAjax()){
            $userId=$this->request->getPost('userId');

            if($userId==null){
                $userId=$this->getAuthenticatedUser()->getUserId();
            }

            $user=User::findFirstBy('userId',$userId);

            if($user->getUserId()==$this->getAuthenticatedUser()->getUserId() || $user->canBeSeenBy($this->getAuthenticatedUser())){

                $children=$user->resolveChildren();
                $returnArray=[];
                foreach($children as $child){
                    $returnArray[$child->getUserId()]['userId']=$user->getUserId();
                    $returnArray[$child->getUserId()]['record']=$user->toArray();
                    $returnArray[$child->getUserId()]['interests']=[];
                    $returnArray[$child->getUserId()]['organisation']['id']=$user->getOrganisation()->getOrganisationId();
                    $returnArray[$child->getUserId()]['organisation']['name']=$user->getOrganisation()->getOrganisationName();

                    $userInterestResults=$child->getInterests();
                    foreach ($userInterestResults as $userInterestResult) {
                        $parentInterests = $userInterestResult->getParents();
                        foreach ($parentInterests as $parent) {
                            if (!in_array($parent->getInterestId(), $returnArray[$child->getUserId()]['interests'])) {
                                $returnArray[$child->getUserId()]['interests'][] = $parent->getInterestId();
                            }
                        }
                    }

                }
                echo json_encode($returnArray);
            }
        }
    }

    /**
     * Returns the json of all the top level interests for the current users vault
     */
    public function AjaxGetVaultEventInterestsAction()
    {//not secure data

        $user = $this->getAuthenticatedUser();

        $filter = new \Apprecie\Library\Search\SearchFilter('VwVaultInterests');

        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::HELD);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::CLOSED);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);

        switch ($user->getActiveRole()->getName()) {
            case "Manager":
                $filter->addAndIsNullFilter('vaultOwnerId');
                $filter->addOrEqualsFilter('vaultOwnerId', $user->getUserId());
                break;
            case "Internal":
                $filter->addInFilter('vaultOwnerId', [$user->getFirstParent()->getUserId(), $user->getUserId()]);
                $filter->addAndEqualFilter('internalCanSee', true);
                $filter->addOrEqualsFilter('vaultOwnerId', $user->getUserId());
                break;
            case "Client" :
                $filter->addAndEqualOrLessThanFilter('tier', $user->getTier());
                $filter->addAndEqualFilter('vaultOwnerId', $user->getFirstParent()->getUserId());
                $filter->addAndEqualFilter('clientCanSee', true);
                $filter->addOrEqualsFilter('vaultOwnerId', $user->getUserId());
                break;
            case "ApprecieSupplier":
            case "AffiliateSupplier":
                $filter->addAndEqualFilter('creatorId', $user->getUserId());
                break;
        }

        switch ($user->getActiveRole()->getName()) {
            case "ApprecieSupplier":
            case "AffiliateSupplier":

                break;
            default:
                $filter->addAndEqualFilter(
                    'vaultOrganisationId',
                    Organisation::getActiveUsersOrganisation()->getOrganisationId()
                );
                break;

        }

        $filter->addAndEqualOrGreaterThanFilter('bookingEndDate', date('Y-m-d'))
            ->addAndNotEqualFilter('isArranged', true)
            ->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);

        $items = $filter->execute();

        $interests = [];

        foreach ($items as $item) {
            if (!array_key_exists($item->parentInterestId, $interests)) {
                $interests[$item->parentInterestId] = [];
                $interests[$item->parentInterestId]['interestId'] = $item->parentInterestId;
                $interests[$item->parentInterestId]['name'] = $item->parentInterest;
                $interests[$item->parentInterestId]['items'][] = $item->itemId;
            } else {
                if (!in_array($item->itemId, $interests[$item->parentInterestId]['items'])) {
                    $interests[$item->parentInterestId]['items'][] = $item->itemId;
                }

            }
        }

        echo json_encode($interests);
    }

    /**
     * Only returns the interests that match items in the current users vault (Top level)
     */
    public function AjaxGetVaultEventBrandsAction()
    {//not secure data
        $user = $this->getAuthenticatedUser();

        $filter = new \Apprecie\Library\Search\SearchFilter('VwVault');

        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::HELD);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::CLOSED);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);

        switch ($user->getActiveRole()->getName()) {
            case "Manager":
                $filter->addAndIsNullFilter('vaultOwnerId');
                $filter->addOrEqualsFilter('vaultOwnerId', $user->getUserId());
                break;
            case "Internal":
                $filter->addInFilter('vaultOwnerId', [$user->getFirstParent()->getUserId(), $user->getUserId()]);
                $filter->addAndEqualFilter('internalCanSee', true);
                $filter->addOrEqualsFilter('vaultOwnerId', $user->getUserId());
                break;
            case "Client" :
                $filter->addAndEqualOrLessThanFilter('tier', $user->getTier());
                $filter->addAndEqualFilter('vaultOwnerId', $user->getFirstParent()->getUserId());
                $filter->addAndEqualFilter('clientCanSee', true);
                $filter->addOrEqualsFilter('vaultOwnerId', $user->getUserId());
                break;
            case "ApprecieSupplier":
            case "AffiliateSupplier":
                $filter->addAndEqualFilter('creatorId', $user->getUserId());
                break;
        }

        switch ($user->getActiveRole()->getName()) {
            case "ApprecieSupplier":
            case "AffiliateSupplier":

                break;
            default:
                $filter->addAndEqualFilter(
                    'vaultOrganisationId',
                    Organisation::getActiveUsersOrganisation()->getOrganisationId()
                );
                break;

        }

        $filter->addAndEqualOrGreaterThanFilter('bookingEndDate', date('Y-m-d'))
            ->addAndNotEqualFilter('isArranged', true)
            ->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);

        $items = $filter->execute();

        $brands = [];

        foreach ($items as $item) {
            if (!array_key_exists($item->creatorOrganisationId, $brands)) {
                $brands[$item->creatorOrganisationId] = [];
                $brands[$item->creatorOrganisationId]['name'] = $item->creatorOrganisationName;
                $brands[$item->creatorOrganisationId]['organisationId'] = $item->creatorOrganisationId;
                $brands[$item->creatorOrganisationId]['items'][] = $item->itemId;
            } else {
                if (!in_array($item->itemId, $brands[$item->creatorOrganisationId]['items'])) {
                    $brands[$item->creatorOrganisationId]['items'][] = $item->itemId;
                }

            }
        }
        echo json_encode($brands);
    }

    public function AjaxSetUserEmailContentAction($templateType)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('templateType', $templateType, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('content', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        list($templateType, $content) = $this->getRequestFilter()->getAll();

        $userEmail = new \Apprecie\Library\Mail\UserEmail($templateType);
        $userEmailSessionData = $userEmail->getDefaultContent();

        foreach ($content as $key => $value) {
            $userEmailSessionData[$key]['content'] = $value;
        }

        $userEmail->setContent($userEmailSessionData);

        _jm('success', '');
    }

    public function AjaxSetUserEmailOptionsAction($templateType)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('templateType', $templateType, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        list($templateType) = $this->getRequestFilter()->getAll();

        if ($this->request->hasPost('options')) {
            $options = $this->request->getPost('options');
        } else {
            $options = null;
        }

        $userEmail = new \Apprecie\Library\Mail\UserEmail($templateType);
        $userEmail->setOptions($options);

        _jm('success', '');
    }

    public function AjaxGetUserEmailContentAction($templateType)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('templateType', $templateType, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('reset', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        list($templateType, $reset) = $this->getRequestFilter()->getAll();

        $userEmail = new \Apprecie\Library\Mail\UserEmail($templateType);
        if ($reset === 'true') {
            $userEmail->setContent(null);
        }
        $content = $userEmail->getContent();

        echo json_encode(array(
            'status' => 'success',
            'message' => '',
            'content' => $content,
            'macros' => $userEmail->getAvailableMacros()
        ));
    }

    public function downloadCalendarAction($eventId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        list($eventId) = $this->getRequestFilter()->getAll();
        $event = Event::resolve($eventId);
        \Apprecie\Library\Acl\AccessControl::userCanViewItem($this->getAuthenticatedUser(), $event->getItem());

        $event->getCalendar(true);
    }
}