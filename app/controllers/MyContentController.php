<?php

/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 21/10/14
 * Time: 16:25
 */
class MyContentController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole('ApprecieSupplier');
        $this->setAllowRole('Internal');
        $this->setAllowRole('Manager');
        $this->setAllowRole('AffiliateSupplier');
    }

    public function eventsAction()
    {
        $this->view->setLayout('application');
        $interests = new Interest();
        $this->view->parentCategories = $interests->getTopLevel();
    }

    public function arrangedAction()
    {
        $this->view->setLayout('application');
        $interests = new Interest();
        $this->view->parentCategories = $interests->getTopLevel();
    }

    public function publishAction($eventId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT)
            ->addRequired(
                'publishState',
                \Apprecie\Library\Security\ParameterTypes::ANY,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $event = Event::resolve($eventId);

        \Apprecie\Library\Acl\AccessControl::userCanPublishItem($this->getAuthenticatedUser(), $event->getItem());

        switch ($this->request->getPost('publishState')) {
            case "parent":
                $event->pushCuratedParent();
                break;
            case "curation":
                $event->pushCuratedApprecie();
                break;
            case "organisation":
                $event->publishPrivate();
                break;
            case "vault":
                $event->publishPrivate();
                break;
        }
        if ($event->hasMessages()) {
            _epm($event);
        }
        echo json_encode(array('status' => 'success', 'message' => 'Event published successfully'));
    }

    public function eventManagementAction($eventId)
    {
        $this->getRequestFilter()->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $event = Event::resolve($eventId);
        $this->getAuthenticatedUser()->canEditItem($event);

        $this->view->calLink = $event->getCalendarDownloadUrl();

        if ($this->request->getQuery('unpublish') == 'true') {
            $event->unPublishEvent();
        }

        if ($this->request->getQuery('delete') == 'true') {
            if(Event::canDeleteEvent($event)) {
                if($event->deleteEvent()) {

                    if ($event->getIsByArrangement() == true) {
                        $this->response->redirect('mycontent/arranged');
                    } else {
                        $this->response->redirect('mycontent/events');
                    }

                    $this->response->send();
                } else {
                    $this->view->warning = 'Failed to delete event ' . _ms($event);
                }
            }
        }

        $this->view->setLayout('application');

        $event->enableTBCOutput(true);
        $this->view->event = $event;

        if ($this->view->event->getIsByArrangement() == true) {
            $this->view->linkedEvents = Item::query()
                ->where('sourceByArrangement=:1:')
                ->bind([1 => $event->getItemId()])
                ->execute();
        }
    }

    public function AjaxDraftEventsAction($pageNumber)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $resultsPerPage = 10;

        $searchMode = $this->request->getPost('isByArrangement') == 'true' ?
            \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::BY_ARRANGEMENT :
            \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::CONFIRMED;

        $activeItems = \Apprecie\Library\SearchFilters\Items\ItemSearchFilterUtility::creatorEventsByStatus
        (
            $this->getAuthenticatedUser(),
            $searchMode,
            null,
            null,
            [\Apprecie\Library\Items\ItemState::DRAFT, \Apprecie\Library\Items\ItemState::DENIED]
        );

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $activeItems,
                "limit" => $resultsPerPage,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $returnArray['ThisPageNumber'] = $pageNumber;
        $returnArray['PageCount'] = $page->total_pages;
        $returnArray['PageResultCount'] = count($page->items);
        $returnArray['TotalResultCount'] = count($activeItems);

        foreach ($page->items as $item) {
            $itemArray = $item->toArrayEx(null, true);
            $itemArray['tier'] = (new \Apprecie\Library\Users\Tier($item->getTier()))->getText();
            $itemArray['state'] = (new \Apprecie\Library\Items\ItemState($itemArray['state']))->getText();
            $returnArray['items'][] = $itemArray;
        }

        echo json_encode($returnArray);
    }

    public function AjaxApprovingEventsAction($pageNumber)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $resultsPerPage = 10;

        $searchMode = $this->request->getPost('isByArrangement') == 'true' ?
            \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::BY_ARRANGEMENT :
            \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::CONFIRMED;

        $activeItemsQuery = \Apprecie\Library\SearchFilters\Items\ItemSearchFilterUtility::creatorEventsByStatus
        (
            $this->getAuthenticatedUser(),
            $searchMode,
            null,
            null,
            \Apprecie\Library\Items\ItemState::APPROVING,
            null,
            true
        );

        if (! $this->request->getPost('isByArrangement') == 'true') {
            // fudge to stop spawned by arrangement events from showing up as a confirmed event that are approving
            $activeItemsQuery->addAndIsNullFilter('sourceByArrangement');
        }

        $activeItemsQuery = $activeItemsQuery->execute();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $activeItemsQuery,
                "limit" => $resultsPerPage,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $returnArray['ThisPageNumber'] = $pageNumber;
        $returnArray['PageCount'] = $page->total_pages;
        $returnArray['PageResultCount'] = count($page->items);
        $returnArray['TotalResultCount'] = count($activeItemsQuery);

        foreach ($page->items as $item) {
            $itemArray = $item->toArrayEx(null, true);
            $itemArray['tier'] = (new \Apprecie\Library\Users\Tier($item->getTier()))->getText();
            $itemArray['state'] = (new \Apprecie\Library\Items\ItemState($itemArray['state']))->getText();
            $returnArray['items'][] = $itemArray;
        }

        echo json_encode($returnArray);
    }

    public function AjaxArrangedpEventsAction($pageNumber)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $resultsPerPage = 10;

        $searchMode = \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::CONFIRMED;

        $arrangedpItemsQuery = \Apprecie\Library\SearchFilters\Items\ItemSearchFilterUtility::creatorEventsByStatus
        (
            $this->getAuthenticatedUser(),
            $searchMode,
            null,
            null,
            \Apprecie\Library\Items\ItemState::ARRANGING,
            null,
            true
        );

        $arrangedpItemsQuery->addAndIsNotNullFilter('sourceByArrangement');
        $arrangedpQuery = $arrangedpItemsQuery->execute();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $arrangedpQuery,
                "limit" => $resultsPerPage,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $returnArray['ThisPageNumber'] = $pageNumber;
        $returnArray['PageCount'] = $page->total_pages;
        $returnArray['PageResultCount'] = count($page->items);
        $returnArray['TotalResultCount'] = count($arrangedpQuery);

        foreach ($page->items as $item) {
            $itemArray = $item->toArrayEx(null, true);

            $itemArray['requester'] = _g('unknown');
            $itemArray['reqorganisation'] = _g('unknown');

            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries(($item->getArrangedFor()->getPortalId()));
            if ($item->getArrangedFor()->getUserProfile() != null) {
                $itemArray['requester'] = $item->getArrangedFor()->getUserProfile()->getFullName();
                $itemArray['reqorganisation'] = $item->getArrangedFor()->getOrganisation()->getOrganisationName();
            }
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

            $itemArray['createdDate'] = _fd($item->getDateCreated());
            $returnArray['items'][] = $itemArray;
        }

        echo json_encode($returnArray);
    }

    public function AjaxToDoEventsAction($pageNumber)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $resultsPerPage = 10;

        $activeItemsQuery = Item::query()
            ->join('ItemApproval')
            ->where('status=:0: OR status=:1: OR status = :4:')
            ->andWhere('verifyingOrganisationId=:2:')
            ->andWhere('type="event"')
            ->andWhere('state=:3:');

        if ($this->request->getPost('isByArrangement') == 'true') {
            $activeItemsQuery->andWhere('isByArrangement=1');
        } else {
            $activeItemsQuery->andWhere('isByArrangement=0');
        }

        $activeItemsQuery = $activeItemsQuery
            ->bind(
                [
                    0 => \Apprecie\Library\Items\ApprovalState::PENDING,
                    1 => \Apprecie\Library\Items\ApprovalState::DENIED,
                    2 => Organisation::getActiveUsersOrganisation()->getOrganisationId(),
                    3 => \Apprecie\Library\Items\ItemState::APPROVING,
                    4 => \Apprecie\Library\Items\ApprovalState::UNPUBLISHED
                ]
            )
            ->execute();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $activeItemsQuery,
                "limit" => $resultsPerPage,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $returnArray['ThisPageNumber'] = $pageNumber;
        $returnArray['PageCount'] = $page->total_pages;
        $returnArray['PageResultCount'] = count($page->items);
        $returnArray['TotalResultCount'] = count($activeItemsQuery);

        foreach ($page->items as $item) {
            $item->sourceOrganisationName = $item->getSourceOrganisation()->getOrganisationName();

            $creator = User::findFirstBy('userId', $item->getCreatorId());

            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($creator->getPortalId());
            $creatorProfile = $creator->getUserProfile();
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

            $item->sourceCreator = $creatorProfile->getFirstName() . ' ' . $creatorProfile->getLastName();

            $itemArray = $item->toArrayEx(array('sourceCreator', 'sourceOrganisationName'));
            $itemArray['tier'] = (new \Apprecie\Library\Users\Tier($item->getTier()))->getText();
            $itemArray['state'] = (new \Apprecie\Library\Items\ItemState($itemArray['state']))->getText();
            $returnArray['items'][] = $itemArray;
        }

        echo json_encode($returnArray);
    }

    public function AjaxArchivedEventsAction($pageNumber)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $resultsPerPage = 10;

        $searchMode = $this->request->getPost('isByArrangement') == 'true' ?
            \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::BY_ARRANGEMENT :
            \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::CONFIRMED;

        $activeItems = \Apprecie\Library\SearchFilters\Items\ItemSearchFilterUtility::creatorEventsByStatus
        (
            $this->getAuthenticatedUser(),
            $searchMode,
            date('Y-m-d H:i:s'),
            null,
            null,
            null
        );

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $activeItems,
                "limit" => $resultsPerPage,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $returnArray['ThisPageNumber'] = $pageNumber;
        $returnArray['PageCount'] = $page->total_pages;
        $returnArray['PageResultCount'] = count($page->items);
        $returnArray['TotalResultCount'] = count($activeItems);

        foreach ($page->items as $item) {
            $itemArray = $item->toArrayEx(null, true);
            $itemArray['tier'] = (new \Apprecie\Library\Users\Tier($item->getTier()))->getText();

            $event = $item->getEvent();
            if ($event != null) {
                $itemArray['eventStatus'] = (new \Apprecie\Library\Items\EventStatus($event->getStatus()))->getText();
            } else {
                $itemArray['eventStatus'] = _g('n/a');
            }

            $returnArray['items'][] = $itemArray;
        }

        echo json_encode($returnArray);
    }

    public function AjaxApprovedEventsAction($pageNumber)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $resultsPerPage = 10;

        $searchMode = $this->request->getPost('isByArrangement') == 'true' ?
            \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::BY_ARRANGEMENT :
            \Apprecie\Library\SearchFilters\Items\UserItemSearchMode::CONFIRMED;

        $activeItemsQuery = \Apprecie\Library\SearchFilters\Items\ItemSearchFilterUtility::creatorEventsByStatus
        (
            $this->getAuthenticatedUser(),
            $searchMode,
            null,
            null,
            \Apprecie\Library\Items\ItemState::APPROVED,
            null,
            true
        );

        if ($this->request->getPost('isByArrangement') == 'true') {
            $activeItemsQuery->addAndEqualOrGreaterThanFilter('bookingEndDate', date('Y-m-d H:i:s', strtotime('today midnight')));
        } else {
            $activeItemsQuery->addAndEqualOrGreaterThanFilter('endDateTime', date('Y-m-d H:i:s', strtotime('today midnight')));
        }

        $activeItemsQuery = $activeItemsQuery->execute();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $activeItemsQuery,
                "limit" => $resultsPerPage,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        $returnArray['ThisPageNumber'] = $pageNumber;
        $returnArray['PageCount'] = $page->total_pages;
        $returnArray['PageResultCount'] = count($page->items);
        $returnArray['TotalResultCount'] = count($activeItemsQuery);

        foreach ($page->items as $item) {
            $itemArray = $item->toArrayEx(null, true);
            $itemArray['tier'] = (new \Apprecie\Library\Users\Tier($item->getTier()))->getText();

            $event = $item->getEvent();
            if ($event != null) {
                $itemArray['eventStatus'] = (new \Apprecie\Library\Items\EventStatus($event->getStatus()))->getText();
            } else {
                $itemArray['eventStatus'] = _g('n/a');
            }

            $returnArray['items'][] = $itemArray;
        }

        echo json_encode($returnArray);
    }

    public function approveAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = \Item::resolve($itemId);

        \Apprecie\Library\Acl\AccessControl::userCanApproveItem($this->getAuthenticatedUser(), $item);

        $this->view->setLayout('application');
        $event = $item->getEvent();
        $this->view->portal = \Apprecie\Library\Provisioning\PortalStrap::getActivePortal();
        $event->enableTBCOutput(true);
        $this->view->event = $event;
        $this->view->domains = $this->di->get('domains');
    }

    public function approveItemAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = \Item::resolve($itemId);

        \Apprecie\Library\Acl\AccessControl::userCanApproveItem($this->getAuthenticatedUser(), $item);

        $this->view->disable();
        $item = Item::findFirstBy('itemId', $itemId);

        $approval = $item->getRelatedApproval();
        if (!$approval->approveItem()) {
            $this->logActivity('Error approving event', 'Any messages ' . _ms($approval));
        }
        echo json_encode(['status' => 'Success']);

    }

    public function rejectItemAction()
    {
        $this->getRequestFilter()->addRequired('itemid', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = \Item::resolve($this->getRequestFilter()->get('itemid'));

        \Apprecie\Library\Acl\AccessControl::userCanApproveItem($this->getAuthenticatedUser(), $item);

        $this->view->disable();
        $item = Item::findFirstBy('itemId', $this->request->getPost('itemid'));
        $item->getRelatedApproval()->denyItem($this->request->getPost('rejection-reason'));
        echo json_encode(['status' => 'Success']);

    }

    public function AjaxCreatorGuestListAction($pageNumber)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::ANY)
            ->addRequired('itemid', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::ANY)
            ->execute($this->request);

        $item = Item::resolve($this->getRequestFilter()->get('itemid'));

        \Apprecie\Library\Acl\AccessControl::userCanSeeGuestList($this->getAuthenticatedUser(), $item);

        $this->view->disable();
        $itemId = $item->getItemId();

        $usersArray = [];
        $spaceCount = 0;

        $attending = false;
        if ($this->request->getPost('attending') == 'true') {
            $attending = true;
        }

        $params = [1 => $attending, 2 => $this->request->getPost('itemid')];
        $status = $this->request->getPost('status');

        try {
            $guests = GuestList::query()
                ->where('attending=:1:')
                ->andWhere('itemId=:2:');

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
                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId());
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
                $guestRecord = [
                    'profile' => $user->getUserProfile()->toArray(),
                    'guest' => $guest->toArray(),
                    'diet' => $dietStr,
                    'organisation' => $user->getOrganisation()->getOrganisationName(),
                    'role' => $user->getRoles()[0]->getRole()->getName()
                ];
                $usersArray[] = $guestRecord;
                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
            }

            if ($this->request->getPost('download') === 'true') {
                $event = Item::resolve($itemId)->getEvent();
                $exportContent = array(
                    array($event->getTitle()),
                    array(''),
                    array("First Name", "Last Name", "Title", "Email Address", "Organisation", "Spaces", "Notes")
                );
                foreach ($usersArray as $user) {
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

    }
}



