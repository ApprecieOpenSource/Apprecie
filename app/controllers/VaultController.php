<?php

class VaultController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setDenyRole('PortalAdministrator');
    }

    public function indexAction()
    {
        $this->isActiveRole(['Manager', 'Internal', 'Client','ApprecieSupplier', 'AffiliateSupplier'], false, false, 'login');
        $this->view->setLayout('application');
    }

    public function AjaxGetAllEventsAction()
    {
        $user=$this->getAuthenticatedUser();
        $filter = new \Apprecie\Library\Search\SearchFilter('VwVault');

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
                $filter->addAndEqualOrLessThanFilter('tier', $user->getTier(), 'VwVault');
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
                    Organisation::getActiveUsersOrganisation()->getOrganisationId(),
                    'VwVault'
                );
                break;
        }
        $filter->addAndEqualOrGreaterThanFilter('bookingEndDate', date('Y-m-d'))
            ->addAndNotEqualFilter('isArranged', true, 'VwVault')
            ->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING, 'VwVault');

        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::HELD);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::CLOSED);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);

        $order = 'startDateTime ASC';

        $items = $filter->execute($order,'itemId');

        $returnItems=[];
        $returnItems['items']=[];
        $returnItems['totalItems']=count($items);

        $contentArray=[];
        foreach($items as $item){
            /*$title= str_replace('{c:','',$item->title);
            $title= str_replace('}','',$title);
            $summary= str_replace('{c:','',$item->summary);
            $summary= str_replace('}','',$summary);*/

            $contentArray[]=$item->title;
            $contentArray[]=$item->summary;
        }

        $filter = new \Apprecie\Library\Search\SearchFilter('Content');
        $filter->addInFilter('contentId',$contentArray);
        $contentStrings=$filter->execute();

        $itemContent=[];
        foreach($contentStrings as $string){
            $itemContent[$string->contentId]=$string->content;
        }
        foreach($items as $key=>$item){
            $itemRecord=$item->toArray();
            if($item->startDateTime!=null && $item->endDateTime!=null){
                $itemRecord['startDateTimeEpoc']=strtotime($item->startDateTime);
                $itemRecord['endDateTimeEpoc']=strtotime($item->endDateTime);
                $itemRecord['startDateTime']=date('d-m-Y H:i:s',strtotime($item->startDateTime));
                $itemRecord['endDateTime']=date('d-m-Y H:i:s',strtotime($item->endDateTime));
            }
            else{
                $itemRecord['startDateTimeEpoc']=10000000000000;
                $itemRecord['endDateTimeEpoc']=10000000000000;
                $itemRecord['startDateTime']='TBC';
                $itemRecord['endDateTime']="TBC";
            }
            $itemRecord['image']=Assets::getItemPrimaryImage($item->itemId);
            if (strlen($item->title > 49)) {
                $itemRecord['itemTitle'] = mb_strtoupper(mb_substr($itemContent[$item->title], 0, 49, 'UTF-8') . "...", 'UTF-8');
            } else {
                $itemRecord['itemTitle'] = mb_strtoupper($itemContent[$item->title], 'UTF-8');
            }
            if (strlen($item->summary) > 150) {
                $itemRecord['shortSummary'] = mb_substr($itemContent[$item->summary], 0, 150, 'UTF-8') . "...";
            } else {
                $itemRecord['shortSummary'] = $itemContent[$item->summary];
            }

            if ($item->unitPrice == 0) {
                $itemRecord['itemType'] = _g('Complimentary Event');
            } else {
                $itemRecord['itemType'] = _g('Fixed Price Event');
            }
            $returnItems['items'][]=$itemRecord;
        }
        echo json_encode($returnItems);
    }

    public function supplierAction()
    {
        $this->requireRoleOrRedirect(['ApprecieSupplier', 'AffiliateSupplier']);

        $this->view->setLayout('application');
        $this->view->organisation = $this->getAuthenticatedUser()->getOrganisation();

    }

    public function supplier2Action()
    {
        $this->requireRoleOrRedirect([\Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER, \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER]);

        $this->view->setLayout('application');
        $this->view->organisation = $this->getAuthenticatedUser()->getOrganisation();
    }

    public function eventAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));

        if ($item->getIsByArrangement()) {
            $this->response->redirect('vault/arranged/' . $itemId);
        }

        $this->getAuthenticatedUser()->canViewItem($item);

        $this->view->setLayout('application');
        $event = $item->getEvent();

        $this->view->calLink = $event->getCalendarDownloadUrl();

        $event->enableTBCOutput(true);
        $this->view->event = $event->getHTMLEncodeAdapter(['getDescription', 'getPurchaseTerms', 'getAttendanceTerms']);

        $this->view->organisationChildren = Organisation::getActiveUsersOrganisation()->resolveChildren(true);

        //fetch the current vault record
        $vaultRecord = Organisation::getActiveUsersOrganisation()->hasEventInVault(
            $event,
            $this->getAuthenticatedUser(),
            null
        );

        $clientsVisible = false;
        $internalVisible = false;

        if ($vaultRecord->count() == 1) {
            $clientsVisible = $vaultRecord[0]->getClientsCanSee();
            $internalVisible = $vaultRecord[0]->getInternalCanSee();
        }

        $this->view->clientsShared = $clientsVisible;
        $this->view->internalShared = $internalVisible;

        $this->view->guestList = GuestList::query()
            ->where('userId=:1:')
            ->andWhere('itemId=:2:')
            ->bind([1 => $this->getAuthenticatedUser()->getUserId(), 2 => $itemId])
            ->execute();

        $this->view->similar = $event->getItem()->getSimilarItems();
        $this->view->suggestionCount = \Apprecie\Library\Items\ItemSuggestions::getSuggestedUsers($event->getItemId());
    }

    public function arrangedAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));

        if ($item->getIsByArrangement() == false) {
            $this->response->redirect('vault/event/' . $itemId);
        }

        $this->getAuthenticatedUser()->canViewItem($item);

        $this->view->setLayout('application');
        $event = Event::findFirstBy('itemId', $itemId);

        $event->enableTBCOutput(true);
        $this->view->event = $event->getHTMLEncodeAdapter();

        $this->view->calLink = $event->getCalendarDownloadUrl();

        $this->view->organisationChildren = Organisation::getActiveUsersOrganisation()->resolveChildren(true);

        //fetch the current vault record
        $vaultRecord = Organisation::getActiveUsersOrganisation()->hasEventInVault(
            $event,
            $this->getAuthenticatedUser(),
            null
        );

        $clientsVisible = false;
        $internalVisible = false;

        if ($vaultRecord->count() == 1) {
            $clientsVisible = $vaultRecord[0]->getClientsCanSee();
            $internalVisible = $vaultRecord[0]->getInternalCanSee();
        }

        $this->view->clientsShared = $clientsVisible;
        $this->view->internalShared = $internalVisible;

        $warningDate = new DateTime('tomorrow +3 days');
        if ($event->getStartDateTime() == _g('TBC')) {
            $startDateTime = new DateTime();
            $startDateTime->setTimestamp(\Phalcon\DI::getDefault()->get('config')->environment->timestampmax);
        } else {
            $startDateTime = new DateTime($event->getStartDateTime());
        }
        if ($warningDate->getTimestamp() > $startDateTime->getTimestamp()) {
            $showStartWarning = true;
        } else {
            $showStartWarning = false;
        }
        $this->view->showStartWarning = $showStartWarning;
        $this->view->similar = $event->getItem()->getSimilarItems();
        $this->view->suggestionCount = \Apprecie\Library\Items\ItemSuggestions::getSuggestedUsers($event->getItemId());
    }

    public function arrangeAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));
        $this->getAuthenticatedUser()->canViewItem($item);

        $this->view->setLayout('application');
        $event = $item->getEvent();
        $this->getAuthenticatedUser()->canViewItem($item);
        $this->view->event = $event->getHTMLEncodeAdapter();
    }

    public function shareAction($eventId)
    {
        $this->requireRoleOrRedirect([\Apprecie\Library\Users\UserRole::MANAGER, \Apprecie\Library\Users\UserRole::INTERNAL]);

        $this->getRequestFilter()->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $event = Event::resolve($eventId);

        $orgs = $this->request->getPost('org-share');
        if ($orgs != null) {
            foreach ($orgs as $org) {
                $org = Organisation::findFirstBy('organisationId', $org);
                $org->addEventToVault($eventId);
            }
        }

        $internalShare = $this->request->getPost('internal-share');
        $clientShare = $this->request->getPost('client-share');
        $internalClientsShare = $this->request->getPost('internal-client-share');

        if ($internalShare == 1 or $clientShare == 1) {
            if ($internalShare == 1) {
                $internalShare = true;
            } else {
                $internalShare = false;
            }
            if ($clientShare == 1) {
                $clientShare = true;
            } else {
                $clientShare = false;
            }
        }

        $org = $this->getAuthenticatedUser()->getOrganisation();
        $org->addEventToVault($eventId, $this->getAuthenticatedUser()->getUserId(), $clientShare, $internalShare);

        if ($internalClientsShare == 1) {
            $internals = $this->getAuthenticatedUser()->resolveChildren('Internal');
            foreach ($internals as $internal) {
                if (!$internal->getIsDeleted() && $internal->getStatus() == \Apprecie\Library\Users\UserStatus::ACTIVE) {
                    $org->addEventToVault($eventId, $internal->getUserId(), true, false);
                }

            }
        }
        echo json_encode(array('status' => 'success'));
    }

    public function AjaxPurchaseItemAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($itemId);

        \Apprecie\Library\Acl\AccessControl::userCanViewItem($this->getAuthenticatedUser(), $item);

        $this->view->disable();

        $order = new \Apprecie\Library\Orders\OrderProcessor();
        $reserve = false;
        if ($this->request->getPost('reserve') == 'true') {
            $reserve = true;
        }

        try {
            $orderId = $order->buyItem($itemId, $this->request->getPost('quantity'), null, $reserve, null, $this->getAuthenticatedUser()->getUserId() == Item::resolve($itemId)->getCreatedBy()->getUserId());
            if ($orderId === false) {
                $errors = $order->getMessagesString(); //@todo consider if this information should be sent to the UI - useful for debug now though
            }
        } catch (Exception $ex) {
            $errors = $ex->getMessage();
            $orderId = false;
        }


        if ($orderId !== false) {
            echo json_encode(array('orderid' => $orderId, 'status' => 'success'));
        } else {
            echo json_encode(array('message' => $errors, 'status' => 'failed'));
        }
    }

    public function AjaxCancelOrderAction()
    {
        $this->getRequestFilter()->addRequired('orderId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $order = Order::resolve($this->getRequestFilter()->get('orderId'));

        \Apprecie\Library\Acl\AccessControl::userCanSeeOrder($this->getAuthenticatedUser(), $order);

        $order = Order::resolve($this->request->getPost('orderId'));

        $status = 'success';
        $message = '';

        if (!$order->cancelOrder(_g('Customer cancelled order'))) {
            $status = 'failed';
            $message = _ms($order);
        }

        _jm($status, $message);
    }

    public function ajaxReservedItemsAction($pageNumber = 1)
    {
        $this->view->disable();
        $user = $this->getAuthenticatedUser(); //secure on current user - already secure
        $items = UserItems::findDistinctByUser($user->getUserId());

        $itemArray = [];
        foreach ($items as $item) {
            $records = UserItems::query();
            $records->where('userId=:1:')
                ->andWhere('itemId=:2:')
                ->andWhere('state=:3:')
                ->bind([1 => $user->getUserId(), 2 => $item->getItemId(), 'reserved']);

            $reserved = $records->execute()->count();

            if ($reserved == 0) {
                continue;
            }

            $confirmed = GuestList::getGuestCount($item->getItemId(), $user->getUserId(), 'confirmed');
            $invited = GuestList::getGuestCount($item->getItemId(), $user->getUserId(), 'invited');
            $available = UserItems::getTotalAvailableUnits($user->getUserId(), $item->getItemId(), 'owned');


            $itemArray[] = [
                "reserved" => $reserved,
                "image" => Assets::getItemPrimaryImage($item->getItemId()),
                "useritem" => $item->toArrayEx(null, true),
                "item" => $item->getItem()->toArrayEx(null, true),
                "event" => $item->getItem()->getEvent()->toArrayEx(null, true),
                "guests" => ['confirmed' => $confirmed, 'invited' => $invited, 'available' => $available]
            ];

        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $itemArray,
                "limit" => 10,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();
        if (count($itemArray) == 0) {
            $page->message = 'No people were found';
        }
        echo json_encode($page);
    }

    public function AjaxOwnedItemsAction($pageNumber = 1)
    {
        $this->view->disable();
        $user = $this->getAuthenticatedUser(); //secure on current user - already secure
        $items = UserItems::findDistinctByUser($user->getUserId());

        $itemArray = [];
        foreach ($items as $item) {
            $confirmed = GuestList::getGuestCount($item->getItemId(), $user->getUserId(), 'confirmed');
            $invited = GuestList::getGuestCount($item->getItemId(), $user->getUserId(), 'invited');
            $available = UserItems::getTotalAvailableUnits($user->getUserId(), $item->getItemId(), 'owned');

            if ($invited + $available + $confirmed == 0) {
                continue; //this is a reservation only -  dont display here
            }

            $records = UserItems::query();
            $records->where('userId=:1:')
                ->andWhere('itemId=:2:')
                ->andWhere('state=:3:')
                ->bind([1 => $user->getUserId(), 2 => $item->getItemId(), 'reserved']);

            $reserved = $records->execute()->count();

            $event = Event::resolve($item->getItem()->getEvent());
            $canEditGuestList = !$event->getIsGuestListClosed();
            $canEditGuestListUntil = $event->getGuestListClosedDateTime(true);
            if ($canEditGuestList) {
                $message = _g('You can edit your guest list until {date}', array('date' => $canEditGuestListUntil));
            } else {
                $message = _g('You can no longer edit your guest list');
            }

            $itemArray[] = [
                "reserved" => $reserved,
                "image" => Assets::getItemPrimaryImage($item->getItemId()),
                "useritem" => $item->toArrayEx(null, true),
                "item" => $item->getItem()->toArrayEx(null, true),
                "event" => $item->getItem()->getEvent()->toArrayEx(null, true),
                "guests" => ['confirmed' => $confirmed, 'invited' => $invited, 'available' => $available],
                "canEditGuestList" => $canEditGuestList,
                "message" => $message
            ];
        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $itemArray,
                "limit" => 10,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();
        if (count($itemArray) == 0) {
            $page->message = 'No people were found';
        }
        echo json_encode($page);
    }

    public function AjaxAttendingItemsAction($pageNumber = 1)
    {
        $this->view->disable();
        //secure on current user - already secure
        $filter = new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('GuestList', 'Event.itemId = GuestList.itemId');
        $filter->addAndEqualFilter('userId', $this->getAuthenticatedUser()->getUserId(), 'GuestList');
        $filter->addAndEqualOrGreaterThanFilter('startDateTime', date('Y-m-d'));
        $filter->addAndEqualFilter('attending', true, 'GuestList');
        $filter->addAndEqualFilter('status', 'confirmed', 'GuestList');
        $events = $filter->execute('Event.startDateTime');

        $itemArray = [];
        foreach ($events as $event) {
            $guestList = GuestList::query();
            $guestList->where('userId=:1:');
            $guestList->andWhere('itemId=:2:');
            $guestList->bind([1 => $this->getAuthenticatedUser()->getUserId(), 2 => $event->getItemId()]);
            $guestListDetails = $guestList->execute();
            $itemArray[] = array(
                "invitationHash" => $guestListDetails[0]->getInvitationHash(),
                "image" => Assets::getItemPrimaryImage($event->getItemId()),
                "title" => _eh($event->getTitle()),
                "start" => _fdt($event->getStartDateTime()),
                "end" => _fdt($event->getEvent()->getEndDateTime()),
                "additionalGuests" => _g('{number} additional guest(s)', ['number' => ($guestListDetails[0]->getSpaces() - 1)])
            );
        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $itemArray,
                "limit" => 10,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        echo json_encode($page);
    }

    public function AjaxArrangingItemsAction($pageNumber = 1)
    {
        if ($this->request->getPost('pageNumber') != null) {
            $pageNumber = $this->request->getPost('pageNumber');
        }

        $user = $this->getAuthenticatedUser();  //secure on current user - already secure

        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('Event', 'Item.itemId = Event.itemId');

        $filter->addAndEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);
        $filter->addOrEqualsFilter('state', \Apprecie\Library\Items\ItemState::APPROVED);
        $filter->addOrEqualsFilter('state', \Apprecie\Library\Items\ItemState::DENIED);
        $filter->addAndEqualFilter('isArrangedFor', $user->getUserId());

        $items = $filter->execute('startDateTime');

        $finalItems = array();

        foreach ($items as $item) {
            $creator = User::findFirstBy('userId', $item->getCreatorId());
            $result['item'] = $item->toArrayEx(null, true);
            $result['brand'] = $creator->getOrganisation()->getOrganisationName();
            $result['image'] = Assets::getItemPrimaryImage($item->getItemId());
            $result['lapsed'] = time() > strtotime($item->getEvent()->getBookingEndDate()) ? 1 : 0;
            $result['price'] = sprintf(
                '%0.2f',
                round(($item->getUnitPrice() * $item->getMaxUnits()) / 100, 2, PHP_ROUND_HALF_UP)
            );

            if ((!$item->getState() == \Apprecie\Library\Items\ItemState::ARRANGING || $result['lapsed']) || $item->getIsArranged() && !$result['lapsed']) { //time
                $result['bookingEnd'] = _fdt($item->getEvent()->getBookingEndDate());
            } else { //time insensitive
                $result['bookingEnd'] = $item->getEvent()->getBookingEndDate(true, true);
            }

            $orderItem = OrderItems::findFirstBy(
                'itemId',
                $item->getItemId()
            );
            if ($orderItem != null && $orderItem->getIsPaidFull() === '1') {
                continue;
            }

            if ($result['lapsed'] === 1) {
                $result['viewState'] = 'closed';
            } elseif ($item->getState() === \Apprecie\Library\Items\ItemState::DENIED && $item->getEvent()->getStatus() === \Apprecie\Library\Items\EventStatus::REJECTED) {
                $result['viewState'] = 'rejected';
            } elseif ($item->getState() === \Apprecie\Library\Items\ItemState::ARRANGING && $item->getEvent()->getStatus() === \Apprecie\Library\Items\EventStatus::PUBLISHED) {
                $result['viewState'] = 'waiting';
            } elseif ($item->getState() === \Apprecie\Library\Items\ItemState::APPROVED && $item->getEvent()->getStatus() === \Apprecie\Library\Items\EventStatus::PUBLISHED) {
                $result['viewState'] = 'approved';
            } else {
                $result['viewState'] = 'other';
            }

            if ($item->getEvent()->getAddress() != null) {
                $result['address'] = $item->getEvent()->getAddress()->toArray();
                $result['eventId'] = $item->getEvent()->getEventId();
            }

            $finalItems[] = $result;
        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $finalItems,
                "limit" => 20,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $page->noitems = _g('There are no items that match your search criteria');
        echo json_encode($page);
    }

    public function manageAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = Item::resolve($itemId);
        $user = $this->getAuthenticatedUser();
        \Apprecie\Library\Acl\AccessControl::userCanManageUserItem($user, $item);

        $this->view->setLayout('application');

        $this->view->user = $user;

        $eventStatus = $item->getEvent()->getStatus();
        $statusesDeniedForActions = [
            \Apprecie\Library\Items\EventStatus::EXPIRED
        ];

        if (in_array($eventStatus, $statusesDeniedForActions) === true || $item->getEvent()->getIsGuestListClosed()) {
            $deniedForActions = true;
        } else {
            $deniedForActions = false;
        }
        $this->view->deniedForActions = $deniedForActions;

        $this->view->item = $item->getHTMLEncodeAdapter();
        $this->view->reserved = UserItems::getUserItemsOfStatus(
            $this->getAuthenticatedUser(),
            \Apprecie\Library\Items\UserItemState::RESERVED
        );
    }

    public function AjaxGetGuestListAction($pageNumber = 1)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::ANY)
            ->addRequired('itemid', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::ANY)
            ->execute($this->request);

        $item = Item::resolve($this->getRequestFilter()->get('itemid'));
        $user = $this->getAuthenticatedUser();

        $this->view->disable();

        \Apprecie\Library\Acl\AccessControl::userCanManageUserItem($user, $item);

        $itemId = $this->request->getPost('itemid');
        if ($this->request->isPost() and $itemId != '') {
            $usersArray = [];
            $spaceCount = 0;
            $attending = false;
            if ($this->request->getPost('attending') == 'true') {
                $attending = true;
            }
            $params = [
                1 => $this->getAuthenticatedUser()->getUser()->getUserId(),
                2 => $attending,
                3 => $this->request->getPost('itemid')
            ];
            $status = $this->request->getPost('status');
            try {
                $guests = GuestList::query()
                    ->where('owningUserId=:1:')
                    ->andWhere('attending=:2:')
                    ->andWhere('itemId=:3:');

                if ($status != null) {
                    if (is_array($status)) {
                        $loop = 0;
                        $string = '';
                        foreach ($status as $state) {
                            if ($loop == 0) {
                                $string .= 'status=:' . (count($params) + 1) . ':';
                                $loop++;
                            } else {
                                $string .= ' or status=:' . (count($params) + 1) . ':';
                            }
                            $params[] = $state;
                        }
                        $guests->andWhere('(' . $string . ')');
                    } else {
                        $guests->andWhere('status=:' . (count($params) + 1) . ':');
                        $params[] = $status;
                    }
                }

                $guests->bind($params);
                $results = $guests->execute();
                foreach ($results as $guest) {
                    $spaceCount += $guest->getSpaces();
                    $user = User::findFirstBy('userId', $guest->getUserId());
                    $userDiet = $user->getDietaryRequirements();
                    $dietStr = '';
                    if (count($userDiet) != 0) {
                        foreach ($userDiet as $requirement) {
                            if ($dietStr != '') {
                                $dietStr .= ', ';
                            }
                            $dietStr .= ($requirement->getDietaryRequirement()->getRequirement());
                        }
                    }
                    if ($user->getIsDeleted()) {
                        $guestRecord = [
                            'inviteDate' => _fd($guest->getInvitationSent()),
                            'confirmDate' => _fd($guest->getConfirmationSent()),
                            'isOwner' => $user->getUserId() == $this->getAuthenticatedUser()->getUser()->getUserId(),
                            'role' => $user->getRoles()[0]->getRole()->getName(),
                            'profile' => array(
                                'firstname' => '(User Deleted)',
                                'lastname' => '',
                                'title' => '',
                                'email' => ''
                            ),
                            'userIsDeleted' => true,
                            'reference' => '',
                            'guest' => $guest->toArray(),
                            'diet' => $dietStr,
                            'organisation' => $user->getOrganisation()->getOrganisationName(),
                            'invite' => \Apprecie\Library\Request\Url::getConfiguredPortalAddress() . '/rsvp/event/' . $guest->getInvitationHash(),
                            'status' => (new \Apprecie\Library\Guestlist\GuestListStatus($guest->getStatus()))->getText()
                        ];
                    } else {
                        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId());
                        if ($user->getPortalUser()->getReference() === null) {
                            $user->getPortalUser()->setReference('');
                        }
                        $guestRecord = [
                            'inviteDate' => _fd($guest->getInvitationSent()),
                            'confirmDate' => _fd($guest->getConfirmationSent()),
                            'isOwner' => $user->getUserId() == $this->getAuthenticatedUser()->getUser()->getUserId(),
                            'role' => $user->getRoles()[0]->getRole()->getName(),
                            'profile' => $user->getUserProfile()->toArray(),
                            'userIsDeleted' => false,
                            'reference' => $user->getPortalUser()->getReference(),
                            'guest' => $guest->toArray(),
                            'diet' => $dietStr,
                            'organisation' => $user->getOrganisation()->getOrganisationName(),
                            'invite' => \Apprecie\Library\Request\Url::getConfiguredPortalAddress() . '/rsvp/event/' . $guest->getInvitationHash(),
                            'status' => (new \Apprecie\Library\Guestlist\GuestListStatus($guest->getStatus()))->getText()
                        ];
                        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
                    }
                    $usersArray[] = $guestRecord;
                }

                if ($this->request->getPost('download') == 'true') {
                    $event = Item::resolve($itemId)->getEvent();
                    $exportContent = array(
                        array($event->getTitle()),
                        array('')
                    );
                    if ($status === \Apprecie\Library\Guestlist\GuestListStatus::PENDING || (is_array($status) && in_array(\Apprecie\Library\Guestlist\GuestListStatus::PENDING, $status))) {
                        $exportContent[] = array("First Name", "Last Name", "Title", "Email Address", "Organisation", "Spaces", "Notes", "Invitation URL");
                    } else {
                        $exportContent[] = array("First Name", "Last Name", "Title", "Email Address", "Organisation", "Spaces", "Notes");
                    }
                    foreach ($usersArray as $user) {
                        if ($status === \Apprecie\Library\Guestlist\GuestListStatus::PENDING || (is_array($status) && in_array(\Apprecie\Library\Guestlist\GuestListStatus::PENDING, $status))) {
                            $exportContent[] = array(
                                $user['profile']['firstname'],
                                $user['profile']['lastname'],
                                $user['profile']['title'],
                                $user['profile']['email'],
                                $user['organisation'],
                                $user['guest']['spaces'],
                                $user['diet'],
                                $user['invite']
                            );
                        } else {
                            $exportContent[] = array(
                                $user['profile']['firstname'],
                                $user['profile']['lastname'],
                                $user['profile']['title'],
                                $user['profile']['email'],
                                $user['organisation'],
                                $user['guest']['spaces'],
                                $user['diet']
                            );
                        }
                    }
                    if ($this->request->getPost('format') === 'excel') {
                        $excel = new \Apprecie\Library\ImportExport\ExcelFile();
                        $excel->setActiveSheetCells($exportContent);
                        $excel->download('guest-list');
                    } else {
                        $csv = new \Apprecie\Library\ImportExport\Export\DelimitedFileExport($exportContent, null, ',', 'csv', false, true);
                        $csv->download('guest-list');
                    }
                } else {
                    $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
                        array(
                            "data" => $usersArray,
                            "limit" => 10,
                            "page" => $pageNumber
                        )
                    );

                    $page = $paginator->getPaginate();
                    $page->spaceCount = $spaceCount;
                    if ($results->count() == 0) {
                        $page->message = 'No people were found';
                    }
                    echo json_encode($page);
                }
            } catch (Exception $ex) {
                echo json_encode(['status' => 'failed', 'message' => $ex->getMessage()]);
            }
        } else {
            _jm('failed', 'Invalid request');
        }
    }

    public function AjaxGuestListAllAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));
        $user = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanOperateGuestList($user, $item);

        $search = \Apprecie\Library\Model\FindOptionsHelper::prepareFindOptions
        (
            null, null, null, 'owningUserId = ?1 AND itemId = ?2', [1=>$user->getUserId(),2=>$itemId]
        );
        $guests = GuestList::find($search);

        echo json_encode($guests->toArray());
    }

    public function AjaxDeclineGuestListUserAction()
    {
        $this->getRequestFilter()->addRequired('itemId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
                ->addRequired('userId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
                ->execute($this->request);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));

        \Apprecie\Library\Acl\AccessControl::userCanOperateGuestList($this->getAuthenticatedUser(), $item);

        $this->view->disable();
        $itemId = $this->request->getPost('itemId');
        $userId = $this->request->getPost('userId');
        $result = ['status' => 'unknown'];

        if ($this->request->isPost() and $itemId != '' and $userId != '') {
            try {
                $user = GuestList::query()
                    ->where('owningUserId=:1:')
                    ->andWhere('itemId=:2:')
                    ->andWhere('userId=:3:')
                    ->bind([1 => $this->getAuthenticatedUser()->getUserId(), 2 => $itemId, 3 => $userId])
                    ->execute();

                if ($user[0]->getStatus() == 'confirmed' or $user[0]->getStatus() == 'invited') {
                    if (!UserItems::creditUnit($itemId, $this->getAuthenticatedUser()->getUserId(), null, $user[0]->getSpaces())) {
                        $result = ['status' => 'failed', 'message' => 'Could not credit a unit'];
                    }
                }

                if ($result['status'] != 'failed') {
                    if ($userId == $this->getAuthenticatedUser()->getUserId()) { //is owner
                        $user[0]->delete();
                    } else {
                        $user[0]->setStatus('revoked');
                        $user[0]->setAttending(0);
                        $user[0]->setConfirmationSent(date('Y-m-d'));
                        $user[0]->update();
                    }

                    if ($user[0]->hasMessages()) {
                        $result = ['status' => 'failed', 'message' => 'Fatal Error, could not update guest record.'];
                    } elseif ($userId == $this->getAuthenticatedUser()->getUserId()) {
                        $result = [
                            'status' => 'success',
                            'message' => 'You were successfully removed from the guest list.'
                        ];
                    } else {
                        $result = ['status' => 'success', 'message' => 'The guest was successfully declined.'];
                    }
                }

                echo json_encode($result);
            } catch (Exception $ex) {
                echo json_encode(['status' => 'failed', 'message' => $ex->getMessage()]);

            }
        }
    }

    public function AjaxAttendGuestListUserAction()
    {
        $this->getRequestFilter()->addRequired('itemId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('userId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));

        \Apprecie\Library\Acl\AccessControl::userCanOperateGuestList($this->getAuthenticatedUser(), $item);

        $this->view->disable();
        $itemId = $this->request->getPost('itemId');
        $userId = $this->request->getPost('userId');
        $result = ['status' => 'unknown'];

        if ($this->request->isPost() and $itemId != '' and $userId != '') {
            try {
                $user = GuestList::query()
                    ->where('owningUserId=:1:')
                    ->andWhere('itemId=:2:')
                    ->andWhere('userId=:3:')
                    ->bind([1 => $this->getAuthenticatedUser()->getUserId(), 2 => $itemId, 3 => $userId])
                    ->execute();

                if ($user[0]->getStatus() == 'declined' or $user[0]->getStatus() == 'revoked') {
                    if (!UserItems::consumeUnit($itemId, $this->getAuthenticatedUser()->getUserId(), null, $user[0]->getSpaces())) {
                        $result = [
                            'status' => 'failed',
                            'message' => 'There are not enough units available to add the guest.'
                        ];
                    }
                }

                if ($result['status'] != 'failed') {
                    $user[0]->setStatus('confirmed');
                    $user[0]->setAttending(1);
                    $user[0]->setConfirmationSent(date('Y-m-d'));
                    $user[0]->update();
                    if ($user[0]->hasMessages()) {
                        $result = ['status' => 'failed', 'message' => 'Fatal Error, could not update guest record.'];
                    } else {
                        $result = ['status' => 'success', 'message' => 'The guest was successfully confirmed.'];
                    }
                }

                echo json_encode($result);
            } catch (Exception $ex) {
                echo json_encode(['status' => 'failed', 'message' => $ex->getMessage()]);

            }
        }
    }

    public function AjaxInviteGuestListUserAction()
    {
        $this->getRequestFilter()->addRequired('itemId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('userId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('spaces', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        list($itemId, $userId, $spaces) = $this->getRequestFilter()->getAll();
        $item = Item::resolve($itemId);

        \Apprecie\Library\Acl\AccessControl::userCanOperateGuestList($this->getAuthenticatedUser(), $item);

        $this->view->disable();
        $result = ['status' => 'unknown'];
        $thisUser = $this->getAuthenticatedUser()->getUserProfile();

        $this->checkCSRF();
        $this->request->isPost();

        if ($this->request->isPost() and $itemId != '' and $userId != '') {
            try {
                $guestRecord = GuestList::query()
                    ->where('owningUserId=:1:')
                    ->andWhere('itemId=:2:')
                    ->andWhere('userId=:3:')
                    ->bind([1 => $this->getAuthenticatedUser()->getUserId(), 2 => $itemId, 3 => $userId])
                    ->execute();

                if ($guestRecord->count() == 0) {
                    if (!UserItems::consumeUnit($itemId, $this->getAuthenticatedUser()->getUserId(), null, $spaces)) {
                        $result = [
                            'status' => 'failed',
                            'message' => 'There are not enough units available to add the guest.'
                        ];
                    } else {
                        $guestRecord = new GuestList();
                        $guestRecord->setAttending(0);
                        $guestRecord->setItemId($itemId);
                        $guestRecord->setOwningUserId($this->getAuthenticatedUser()->getUserId());
                        $guestRecord->setStatus('invited');
                        $guestRecord->setPaid(1);
                        $guestRecord->setUserId($userId);
                        $guestRecord->setSpaces($spaces);
                        $guestRecord->create();
                        $guestRecord = GuestList::query()
                            ->where('owningUserId=:1:')
                            ->andWhere('itemId=:2:')
                            ->andWhere('userId=:3:')
                            ->bind([1 => $this->getAuthenticatedUser()->getUserId(), 2 => $itemId, 3 => $userId])
                            ->execute();
                    }
                } elseif ($guestRecord[0]->getStatus() == \Apprecie\Library\Guestlist\GuestListStatus::DECLINED || $guestRecord[0]->getStatus() == \Apprecie\Library\Guestlist\GuestListStatus::REVOKED) {
                    if (!UserItems::consumeUnit($itemId, $this->getAuthenticatedUser()->getUserId(), null, $spaces)) {
                        $result = [
                            'status' => 'failed',
                            'message' => 'There are not enough units available to add the guest.'
                        ];
                    } else {
                        $guestRecord[0]->setSpaces($spaces);
                        $guestRecord[0]->update();
                    }
                }

                if ($result['status'] != 'failed') {
                    $guestRecord[0]->setStatus(\Apprecie\Library\Guestlist\GuestListStatus::PENDING); //pending = invited
                    $guestRecord[0]->setAttending(0);
                    $guestRecord[0]->setInvitationSent(date('Y-m-d H:i:s'));
                    $guestRecord[0]->update();
                    if ($guestRecord[0]->hasMessages()) {
                        $result = ['status' => 'failed', 'message' => 'Fatal Error, could not update guest record.'];
                    } else {
                        $thread = new MessageThread();
                        $thread->setFirstRecipientUser($userId);
                        $thread->setStartedByUser($thisUser->getUserId());
                        $thread->setType(\Apprecie\Library\Messaging\MessageThreadType::INVITATION);
                        $thread->create();

                        $rsvpUrl = \Apprecie\Library\Request\Url::getConfiguredPortalAddress() . '/rsvp/event/' . $guestRecord[0]->getInvitationHash();

                        $message = new Message();
                        $message->setBody(
                            'You have been invited to the event - ' . $item->getTitle() . '. Please click here to <a href="' . $rsvpUrl . '">RSVP</a>'
                        );
                        $message->setReferenceItem($itemId);
                        $message->setTargetUser($userId);
                        $message->setTitle('Event invitation');
                        $message->setSourceDescription($thisUser->getFullName());
                        $message->setSourceUser($thisUser->getUserId());
                        $message->setSourcePortal($this->getAuthenticatedUser()->getPortalId());

                        $message->create();
                        $thread->addMessage($message);

                        $userRecord = User::resolve($userId);
                        $userRecord->clearStaticCache();
                        $toUser = $userRecord->getUserProfile();
                        $emailUtil = new \Apprecie\Library\Mail\EmailUtility();

                        if ($this->request->getPost('sendEmail') === 'true') {
                            $emailUtil->sendUserEmail(
                                \Apprecie\Library\Mail\EmailTemplateType::INVITATION,
                                $thisUser->getUser(),
                                $userRecord,
                                $item->getEvent(),
                                array(
                                    'rsvpLink' => \Apprecie\Library\Request\Url::getConfiguredPortalAddress() . '/rsvp/event/' . $guestRecord[0]->getInvitationHash()
                                )
                            );
                        }

                        $result = [
                            'status' => 'success',
                            'message' => 'The guest was successfully invited.',
                            'userName' => $toUser->getFullName(),
                            'url' => \Apprecie\Library\Request\Url::getConfiguredPortalAddress() . '/rsvp/event/' . $guestRecord[0]->getInvitationHash()
                        ];
                    }
                }

                echo json_encode($result);
            } catch (Exception $ex) {
                echo json_encode(['status' => 'failed', 'message' => $ex->getMessage()]);

            }
        }
    }

    public function ajaxAttendAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));

        \Apprecie\Library\Acl\AccessControl::userCanOperateGuestList($this->getAuthenticatedUser(), $item);

        $message = '';
        $status = 'failed';

        if (GuestList::userIsInGuestList(
            $this->getAuthenticatedUser()->getUserId(),
            $this->getAuthenticatedUser()->getUserId(),
            $itemId
        )
        ) {
            $message = _g('You are already on the guest list');
        } elseif (UserItems::getTotalAvailableUnits(
                $this->getAuthenticatedUser()->getUserId(),
                $item->getItemId(),
                'owned') == 0
        ) {
            $message = _g('You do not have enough available units to attend this event');
        } elseif (!UserItems::consumeUnit($itemId, $this->getAuthenticatedUser()->getUserId())) {
            $message = _g('You do not have enough available units to attend this event');
        } else {
            $guestList = new GuestList();
            $guestList->setAttending(1);
            $guestList->setItemId($itemId);
            $guestList->setOwningUserId($this->getAuthenticatedUser()->getUserId());
            $guestList->setStatus(\Apprecie\Library\Guestlist\GuestListStatus::CONFIRMED);
            $guestList->setPaid(1);
            $guestList->setConfirmationSent(date('Y-m-d'));
            $guestList->setUserId($this->getAuthenticatedUser()->getUserId());
            if (!$guestList->save()) {
                $message = _ms($guestList);
            } else {
                $message = _g('You are now marked as attending this event');
                $status = _g('success');
            }
        }

        _jm($status, $message);
    }

    public function ArrangedpAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($this->getAuthenticatedUser(), $item);

        $this->view->setLayout('application');
        $item = Item::resolve($itemId, false);

        if ($item->getIsArrangedFor() != $this->getAuthenticatedUser()->getUserId()) {
            $this->getAuthenticatedUser()->canViewItem($item);
        }

        if ($this->getAuthenticatedUser()->getUserId() != $item->getCreatorId() &&
            ($this->getAuthenticatedUser()->getUserId() == $item->getIsArrangedFor())
        ) {
            $this->response->redirect('vault/myarranged/' . $itemId);
            $this->response->send();
        } else {
            $event = $item->getEvent();
            $event->enableTBCOutput(true);
        }

        $this->view->event = $event->getHTMLEncodeAdapter();

        $httpReferer = $this->request->getHTTPReferer();
        $fromPath = parse_url($httpReferer, PHP_URL_PATH);
        if (strpos($fromPath, '/alertcentre/view') !== false) {
            $backTitle = _g('Back to Alert Centre');
            $backURL = $httpReferer;
        } elseif (strpos($fromPath, '/mycontent/arranged') !== false) {
            $backTitle = _g('Back to My By Arrangement Events');
            $backURL = $httpReferer;
        } else {
            $backTitle = _g('My By Arrangement Events');
            $backURL = '/mycontent/arranged';
        }
        $this->view->backTitle = $backTitle;
        $this->view->backURL = $backURL;
    }

    public function myArrangedAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = Item::resolve($this->getRequestFilter()->get('itemId'));

        \Apprecie\Library\Acl\AccessControl::userCanViewItem($this->getAuthenticatedUser(), $item);

        $this->view->setLayout('application');

        if ($item->getIsArrangedFor() != $this->getAuthenticatedUser()->getUserId()) {
            $this->response->redirect('error/fourofour');
            $this->response->send();
        }

        $event = $item->getEvent();
        $event->enableTBCOutput(true);


        $this->view->event = $event->getHTMLEncodeAdapter();
        $this->view->user = $this->getAuthenticatedUser();

        $httpReferer = $this->request->getHTTPReferer();
        $fromPath = parse_url($httpReferer, PHP_URL_PATH);
        if (strpos($fromPath, '/alertcentre/view') !== false) {
            $backTitle = _g('Back to Alert Centre');
            $backURL = $httpReferer;
        } elseif (strpos($fromPath, '/eventmanagement/arranging') !== false) {
            $backTitle = _g('Back to Arranging Events');
            $backURL = $httpReferer;
        } else {
            $backTitle = _g('Arranging Events');
            $backURL = '/eventmanagement/arranging';
        }
        $this->view->backTitle = $backTitle;
        $this->view->backURL = $backURL;
    }

    /* GH I dont think this is active- please uncomment if I wrong, no front end removed.
    public function AjaxUsersNotGuestsAction($pageNumber = 1)
    {
        $this->getParamFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('itemId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('userId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = $this->getAuthenticatedUser();
        $item = Item::resolve($this->getParamFilter()->get('itemId'));

        \Apprecie\Library\Acl\AccessControl::canOperateGuestList($user, $item);

        $children = $user->resolveChildren();
        $available = [];
        foreach ($children as $child) {
            if (!$child->getIsInteractive() || $child->getUserProfile() == null) { //GH file is invisible to git???
                continue;
            }

            if (!GuestList::userIsInGuestList(
                $child->getUserId(),
                $user->getUserId(),
                $this->request->getPost('itemId')
            )
            ) {
                $childArray = [];
                $childArray['firstName'] = $child->getUserProfile()->getFirstName();
                $childArray['lastName'] = $child->getUserProfile()->getLastName();
                $childArray['emailAddress'] = $child->getUserProfile()->getEmail();
                $childArray['role'] = $child->getRoles()[0]->getRole()->getName();
                $childArray['userId'] = $child->getUserId();
                array_push($available, $childArray);
            }
        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $available,
                "limit" => 15,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();
        if (count($children) == 0) {
            $page->message = 'No people were found';
        }
        echo json_encode($page);
    }*/


    /* GH   think not used cant find front end hooks.
    function AjaxGetAllCategoryEventsAction($pageNumber = 1)
    {
        $this->getParamFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $user = $this->getAuthenticatedUser();

        $searchCategories = $this->request->getPost('categories');
        $searchBrands = $this->request->getPost('brands');

        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('ItemVault', 'Item.itemId = ItemVault.itemId')
            ->addJoin('Event', 'Item.itemId = Event.itemId')
            ->addJoin('ItemInterest', 'Event.itemId = ItemInterest.itemId', null, 'left')
            ->addJoin('InterestLink', 'ItemInterest.interestId = InterestLink.interestId', null, 'left');

        switch ($user->getActiveRole()->getName()) {
            case "Manager": {
                $filter->addAndIsNullFilter('ownerId');
                $filter->addOrEqualsFilter('ownerId', $user->getUserId());
                break;
            }
            case "Internal": {
                $filter->addInFilter('ownerId', [$user->getFirstParent()->getUserId(), $user->getUserId()]);
                $filter->addAndEqualFilter('internalCanSee', true);
                $filter->addOrEqualsFilter('ownerId', $user->getUserId());
                break;
            }
            case "Client" : {
                $filter->addInFilter('ownerId', [$user->getFirstParent()->getUserId(), $user->getUserId()]);
                $filter->addAndEqualFilter('clientsCanSee', true);
                $filter->addOrEqualsFilter('ownerId', $user->getUserId());
                $filter->addAndEqualOrLessThanFilter('tier', $user->getTier());
                break;
            }
        }

        $filter->addAndEqualFilter(
            'organisationId',
            Organisation::getActiveUsersOrganisation()->getOrganisationId(),
            'ItemVault'
        )
            ->addFilter('bookingEndDate', date('Y-m-d'), '', '>=')
            ->addAndNotEqualFilter('status', \Apprecie\Library\Items\EventStatus::FULLY_BOOKED, 'Event')
            ->addAndNotEqualFilter('isArranged', true, 'Item')
            ->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING, 'Item');

        if ($this->request->getPost('categories')[0] != 'All') {
            $filter->addInFilter('parentInterestId', $this->request->getPost('categories'), 'InterestLink');
        }

        $items = $filter->execute('startDateTime');
        $finalItems = array();

        foreach ($items as $item) {
            $creator = User::findFirstBy('userId', $item->getCreatorId());
            $result['item'] = $item->toArrayEx(null, true);
            $result['brand'] = $creator->getOrganisation()->getOrganisationName();
            $result['image'] = Assets::getItemPrimaryImage($item->getItemId());

            if ($item->getEvent()->getAddress() != null) {
                $result['address'] = $item->getEvent()->getAddress()->toArray();
            }

            $finalItems[] = $result;
        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $finalItems,
                "limit" => 20,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $page->noitems = _g('There are no items that match your search criteria');
        echo json_encode($page);
    }*/

    function AjaxItemProfileSuggestedUsersAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($itemId);

        \Apprecie\Library\Acl\AccessControl::userCanViewItem($this->getAuthenticatedUser(), $item->getItemId());

        echo json_encode(\Apprecie\Library\Items\ItemSuggestions::getSuggestedUsers($item->getItemId()));
    }
}

