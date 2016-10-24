<?php

class RoiController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole('ApprecieSupplier');
        $this->setAllowRole('Internal');
        $this->setAllowRole('Manager');
        $this->setAllowRole('AffiliateSupplier');
    }

    public function indexAction()
    {
        $this->view->setLayout('application');
        $this->view->role = $this->getAuthenticatedUser()->getActiveRole()->getName();
    }

    public function myEventsAction()
    {
        $this->view->setLayout('application');
    }

    public function complianceAction()
    {
        $this->isActiveRole(['Internal', 'Manager'], false, false, 'error/fourofour');
        $this->view->setLayout('application');
    }

    public function exportAction($type)
    {
        $this->view->disable();
        if ($this->session->has('ROIExport')) { //GH secure by session - nice use of session :)
            $data = $this->session->get('ROIExport');
            if ($type === 'excel') {
                $array = $data['content'];
                array_unshift($array, $data['headings']);
                $excel = new \Apprecie\Library\ImportExport\ExcelFile();
                $excel->setActiveSheetCells($array);
                $excel->download('Report');
            } else {
                $csv = new \Apprecie\Library\ImportExport\Export\DelimitedFileExport($data['content'], $data['headings'], ',', 'csv', false, true);
                $csv->download('Report');
            }
        }
    }

    public function AjaxMyEventsAction()
    {
        $this->getRequestFilter()->addRequired('date', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('Event', 'Event.itemId=Item.itemId'); //GH secure by filter
        $filter->addAndEqualFilter('creatorId', $this->getAuthenticatedUser()->getUser()->getUserId());
        $filter->addAndEqualFilter('isByArrangement', '0');

        if ($this->request->getPost('date')) {
            $startDate = explode('/', $this->request->getPost('date'));
            $filter->addAndEqualOrGreaterThanFilter(
                'startDateTime',
                $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0] . ' 00:00:01'
            );
        }
        if ($this->request->getPost('date2')) {
            $endDate = explode('/', $this->request->getPost('date2'));
            $filter->addAndEqualOrLessThanFilter(
                'startDateTime',
                $endDate[2] . '-' . $endDate[1] . '-' . $endDate[0] . ' 23:59:59'
            );
        }
        $results = $filter->execute();

        $resultArray['records'] = [];
        $gueststotal = 0;
        $pricetotal = 0;
        foreach ($results as $result) {
            $event = $result->getEvent()->getHTMLEncodeAdapter();
            $record = [];

            $guestList = new \Apprecie\Library\Search\SearchFilter('GuestList');
            $guestList->addAndEqualFilter('itemId', $event->getItemId());
            $guestList->addAndEqualFilter('attending', 1);

            $guests = $guestList->execute();
            $guestCount = 0;
            if (count($guests)) {
                foreach ($guests as $guest) {
                    $guestCount += $guest->getSpaces();
                }
            }
            $record['guests'] = $guestCount;

            if ($event->getIsArrangedFor() != null) {
                $record['type'] = 'Event (By-Arrangement)';
            } else {
                $record['type'] = 'Event';
            }

            $record['title'] = $event->getTitle();
            $record['itemId'] = $event->getItemId();
            $record['eventId'] = $event->getEventId();
            $record['startDateTime'] = _fd($event->getStartDateTime());
            $record['startDateTimeStamp'] = strtotime($event->getStartDateTime());
            $record['tier'] = (new \Apprecie\Library\Users\Tier($event->getTier()))->getText();
            $record['revenue'] = sprintf('%0.2f', round($event->getTotalValue() / 100, 2, PHP_ROUND_HALF_UP));
            $record['url'] = '/mycontent/eventmanagement/' . $event->getEventId();

            $gueststotal += $record['guests'];
            $pricetotal += $record['revenue'];
            array_push($resultArray['records'], $record);

        }
        $resultArray['pricetotal'] = number_format($pricetotal, 2);
        $resultArray['guesttotal'] = $gueststotal;

        $exportHeadings = array('Name', 'Start Date', 'Tier', 'Type', 'Revenue', 'Attendance');
        $exportContent = array();
        foreach ($resultArray['records'] as $record) {
            $exportContent[] = array(
                html_entity_decode($record['title']),
                $record['startDateTime'],
                $record['tier'],
                $record['type'],
                $record['revenue'],
                $record['guests']
            );
        }
        $exportContent[] = array('', '', '', '', 'Total Revenue', 'Total Attendance');
        $exportContent[] = array('', '', '', '', $resultArray['pricetotal'], $resultArray['guesttotal']);
        $this->session->set('ROIExport', array(
            'headings' => $exportHeadings,
            'content' => $exportContent
        ));

        echo json_encode($resultArray);
    }

    public function myByArrangementsAction()
    {
        $this->view->setLayout('application');
    }

    public function AjaxMyByArrangementsAction()
    {
        $this->getRequestFilter()->addRequired('date', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('Event', 'Event.itemId=Item.itemId'); //secure by filter
        $filter->addAndEqualFilter('creatorId', $this->getAuthenticatedUser()->getUser()->getUserId());
        $filter->addAndEqualFilter('isByArrangement', '1');

        if ($this->request->getPost('date')) {
            $startDate = explode('/', $this->request->getPost('date'));
            $filter->addAndEqualOrGreaterThanFilter(
                'bookingStartDate',
                $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0] . ' 00:00:01'
            );
        }
        if ($this->request->getPost('date2')) {
            $endDate = explode('/', $this->request->getPost('date2'));
            $filter->addAndEqualOrLessThanFilter(
                'bookingStartDate',
                $endDate[2] . '-' . $endDate[1] . '-' . $endDate[0] . ' 23:59:59'
            );
        }
        $results = $filter->execute();

        $resultArray['records'] = [];
        $gueststotal = 0;
        $pricetotal = 0;
        foreach ($results as $result) {
            $event = $result->getEvent();
            $record = [];

            $linkedEvents = Item::query()
                ->where('sourceByArrangement=:1:')
                ->bind([1 => $event->getItemId()])
                ->execute();
            $record['linkedEvents'] = $linkedEvents->count();

            $guestCount = 0;
            $revenue = 0;
            foreach ($linkedEvents as $linkedEvent) {
                $guestCount += GuestList::getGuestCount(
                    $linkedEvent->getItemId(),
                    $linkedEvent->getIsArrangedFor(),
                    'confirmed'
                );
                $revenue += $linkedEvent->getTotalValue();
            }
            $record['revenue'] = sprintf('%0.2f', round($revenue / 100, 2, PHP_ROUND_HALF_UP));
            $record['guests'] = $guestCount;

            $record['title'] = _eh($event->getTitle());
            $record['eventId'] = $event->getItemId();
            $record['bookingStartDate'] = _fd($event->getBookingStartDate()) . ' - ' . _fd($event->getBookingEndDate());
            $record['bookingStartDateStamp'] = strtotime($event->getBookingStartDate());
            $record['tier'] = (new \Apprecie\Library\Users\Tier($event->getTier()))->getText();

            $record['url'] = '/mycontent/eventmanagement/' . $event->getEventId();

            $gueststotal += $record['guests'];
            $pricetotal += $revenue;
            array_push($resultArray['records'], $record);

        }
        $resultArray['pricetotal'] = sprintf('%0.2f', round($pricetotal / 100, 2, PHP_ROUND_HALF_UP));
        $resultArray['guesttotal'] = $gueststotal;

        $exportHeadings = array('Name', 'Booking Start Date', 'Tier', 'Linked Events', 'Revenue', 'Attendance');
        $exportContent = array();
        foreach ($resultArray['records'] as $record) {
            $exportContent[] = array(
                html_entity_decode($record['title']),
                $record['bookingStartDate'],
                $record['tier'],
                $record['linkedEvents'],
                $record['revenue'],
                $record['guests']
            );
        }
        $exportContent[] = array('', '', '', '', 'Total Revenue', 'Total Attendance');
        $exportContent[] = array('', '', '', '', $resultArray['pricetotal'], $resultArray['guesttotal']);
        $this->session->set('ROIExport', array(
            'headings' => $exportHeadings,
            'content' => $exportContent
        ));

        echo json_encode($resultArray);
    }

    public function AjaxComplianceAction()
    {
        $this->getRequestFilter()->addRequired('date', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();

        $users = $this->getAuthenticatedUser()->resolveChildren(); //secure by filter
        $usersArray['records'] = [];
        $giftstotal = 0;
        $engagementtotal = 0;

        $filter = new \Apprecie\Library\Search\SearchFilter('GuestList');
        $filter->addJoin('UserRole', 'UserRole.userId=GuestList.owningUserId');
        $filter->addJoin('User', 'User.userId=GuestList.owningUserId');
        $filter->addJoin('Event', 'Event.itemId=GuestList.itemId');
        $filter->addAndEqualFilter('organisationId', $this->getAuthenticatedUser()->getOrganisationId());
        $filter->addAndEqualFilter('User.isDeleted', 0);

        if ($this->request->getPost('date')) {
            $startDate = explode('/', $this->request->getPost('date'));
            $filter->addAndEqualOrGreaterThanFilter(
                'startDateTime',
                $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0] . ' 00:00:01'
            );
        }
        if ($this->request->getPost('date2')) {
            $endDate = explode('/', $this->request->getPost('date2'));
            $filter->addAndEqualOrLessThanFilter(
                'startDateTime',
                $endDate[2] . '-' . $endDate[1] . '-' . $endDate[0] . ' 23:59:59'
            );
        }
        $filter->addAndEqualFilter('status', 'confirmed', 'GuestList');
        $filter->addInFilter('roleId', [11, 31, 51]);

        $managerGuestLists = $filter->execute();
        foreach ($users as $user) {
            if(!$user->getIsDeleted()){
                $userProfile = $user->getUserProfile();
                $userArray = [];
                if ($this->request->getPost('date')) {

                    $startDate = explode('/', $this->request->getPost('date'));
                    $endDate = explode('/', $this->request->getPost('date2'));

                    $userArray['reference'] = $user->getPortalUser()->getReference();
                    $userArray['firstname'] = $userProfile->getFirstName();
                    $userArray['lastname'] = $userProfile->getLastName();
                    $userArray['emailaddress'] = $userProfile->getEmail();
                    $userArray['organisation'] = $user->getOrganisation()->getOrganisationName();
                    $parent = $user->getFirstParent()->getUserProfile();
                    $userArray['owner'] = $parent->getFirstName() . ' ' . $parent->getLastName();
                    $userArray['tier'] = (new \Apprecie\Library\Users\Tier($user->getTier()))->getText();
                    $userArray['role'] = $user->getRoles()[0]->getRole()->getName();
                    $userArray['engagement'] = 0;
                    $userArray['gifts'] = 0;
                    $guard = [];
                    foreach ($managerGuestLists as $guestList) {
                        $key = $guestList->getItemId() . '_' . $guestList->getOwningUserId();
                        if (in_array($key, $guard)) {
                            continue;
                        }
                        if ($guestList->getUserId() != $user->getUserId()) {
                            continue;
                        }
                        $userAttended = GuestList::userIsInGuestList(
                            $user->getUserId(),
                            $guestList->getOwningUserId(),
                            $guestList->getItemId(),
                            'confirmed'
                        );
                        $managerAttended = GuestList::userIsInGuestList(
                            $guestList->getOwningUserId(),
                            $guestList->getOwningUserId(),
                            $guestList->getItemId(),
                            'confirmed'
                        );

                        $item = Item::resolve($guestList->getItemId());

                        if ($item->getUnitPrice() != 0) {
                            if ($userAttended && $managerAttended) {
                                $userArray['engagement'] += ($item->getUnitPrice() / $item->getPackageSize()) * $guestList->getSpaces();
                            } elseif ($userAttended && !$managerAttended) {
                                $userArray['gifts'] += ($item->getUnitPrice() / $item->getPackageSize()) * $guestList->getSpaces();
                            }
                        }
                        $guard[] = $key;
                    }

                    $giftstotal += $userArray['gifts'];
                    $engagementtotal += $userArray['engagement'];
                    $userArray['engagement'] = sprintf(
                        '%0.2f',
                        round($userArray['engagement'] / 100, 2, PHP_ROUND_HALF_UP)
                    );
                    $userArray['gifts'] = sprintf('%0.2f', round($userArray['gifts'] / 100, 2, PHP_ROUND_HALF_UP));
                    array_push($usersArray['records'], $userArray);

                }
            }
        }

        $usersArray['giftstotal'] = sprintf('%0.2f', round($giftstotal / 100, 2, PHP_ROUND_HALF_UP));;
        $usersArray['engagementtotal'] = sprintf('%0.2f', round($engagementtotal / 100, 2, PHP_ROUND_HALF_UP));;

        $exportHeadings = array('Reference', 'First Name', 'Last Name', 'Email Address', 'Owner', 'Tier', 'Gifts', 'Engagement');
        $exportContent = array();
        foreach ($usersArray['records'] as $record) {
            $exportContent[] = array(
                $record['reference'],
                $record['firstname'],
                $record['lastname'],
                $record['emailaddress'],
                $record['owner'],
                $record['tier'],
                $record['gifts'],
                $record['engagement']
            );
        }
        $exportContent[] = array('', '', '', '', '', '', 'Total Gifts', 'Total Engagement');
        $exportContent[] = array('', '', '', '', '', '', $usersArray['giftstotal'], $usersArray['engagementtotal']);
        $this->session->set('ROIExport', array(
            'headings' => $exportHeadings,
            'content' => $exportContent
        ));

        echo json_encode($usersArray);
    }
}