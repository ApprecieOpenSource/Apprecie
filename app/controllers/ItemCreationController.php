<?php

class ItemCreationController extends \Apprecie\Library\Controllers\ApprecieControllerBase
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
    }

    public function previewEventAction()
    {//gh pass through does not need securing as only echos out existing post
        $this->view->setLayout('application');
        $this->view->postdata = $this->request->getPost();
    }

    public function createAction()
    {
        $this->view->setLayout('application');
    }

    public function getCreateStepsAction()
    {
        $this->getRequestFilter()->addRequired('type', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        switch ($_REQUEST['type']) {
            case 'confirmed':
                $this->dispatcher->setActionName('confirmedSteps');
                break;
            case 'arranged':
                $this->dispatcher->setActionName('arrangedSteps');
                break;
        }
        $this->dispatcher->dispatch();
    }

    public function confirmedStepsAction()
    {
        $this->getRequestFilter()->addRequired('type', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->currencies = Currency::find("enabled=1");
    }

    public function arrangedStepsAction()
    {
        $this->getRequestFilter()->addRequired('type', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->currencies = Currency::find("enabled=1");
    }

    public function editEventAction($eventId)
    {
        $this->getRequestFilter()->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $event = Event::resolve($eventId);
        $this->getAuthenticatedUser()->canEditItem($event->getItemId());

        $this->view->setLayout('application');
        $this->view->event = $event->getHTMLEntitiesAdapter();
        $this->view->currencies = Currency::find("enabled=1");
    }

    public function editArrangedAction($eventId)
    {
        $this->getRequestFilter()->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $event = Event::resolve($eventId);
        $this->getAuthenticatedUser()->canEditItem($event->getItemId());

        $this->view->setLayout('application');

        $this->view->event = $event->getHTMLEntitiesAdapter();
        $this->view->currencies = Currency::find("enabled=1");
    }

    public function arrangeAction($eventId)
    {
        $this->getRequestFilter()->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $this->view->setLayout('application');
        $event = Event::resolve($eventId);

        $this->getAuthenticatedUser()->canEditItem($event->getItemId());

        if (!$event->getState() == \Apprecie\Library\Items\ItemState::ARRANGING) {
            throw new \LogicException('This item is not in the correct state');
        }

        $this->view->event = $event->getHTMLEntitiesAdapter();
        $this->view->currencies = Currency::find("enabled=1");
    }

    public function confirmAction($eventId)
    {
        $this->getRequestFilter()->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $event = Event::resolve($eventId);
        $this->getAuthenticatedUser()->canEditItem($event->getItemId());

        $this->view->setLayout('application');

        if (!$event->getState() == \Apprecie\Library\Items\ItemState::ARRANGING) {
            throw new \LogicException('This item is not in the correct state');
        }

        $this->view->event = $event->getHTMLEntitiesAdapter();
        $this->view->currencies = Currency::find("enabled=1");

        $originalBookingEndDate = new DateTime($event->getBookingEndDate());
        $earliestBookingEndDate = new DateTime('today');
        if ($originalBookingEndDate->getTimestamp() >= $earliestBookingEndDate->getTimestamp(
            ) && $originalBookingEndDate->getTimestamp() < \Phalcon\DI::getDefault()->get(
                'config'
            )->environment->timestampmax
        ) {
            $this->view->newBookingEndDate = $originalBookingEndDate;
        } else {
            $this->view->newBookingEndDate = $earliestBookingEndDate;
        }
        $this->view->earliestBookingEndDate = $earliestBookingEndDate;
    }

    public function ajaxCreateEventAction()
    {
        $this->getRequestFilter()->addRequired('confirmed-title', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('languageid', \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-short-description', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-description', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-bookingstart', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $startDate = explode('/', $this->request->getPost('confirmed-startdate'));
        $endDate = explode('/', $this->request->getPost('confirmed-enddate'));
        $startBooking = explode('/', $this->request->getPost('confirmed-bookingstart'));
        $endBooking = explode('/', $this->request->getPost('confirmed-bookingend'));
        $interests = $this->request->getPost('interests');
        $goals = $this->request->getPost('goal');

        $thisUser = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser();

        $event = new Event();
        $event->setLanguageId($this->request->getPost('languageid'));
        $event->setTitle($this->request->getPost('confirmed-title'));
        $event->setSummary($this->request->getPost('confirmed-short-description'));
        $event->setDescription($this->request->getPost('confirmed-description'));
        $event->setStartDateTime(
            $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0] . ' ' . $this->request->getPost(
                'confirmed-starttime'
            )
        );
        $event->setEndDateTime(
            $endDate[2] . '-' . $endDate[1] . '-' . $endDate[0] . ' ' . $this->request->getPost('confirmed-endtime')
        );

        $event->setBookingStartDate($startBooking[2] . '-' . $startBooking[1] . '-' . $startBooking[0]);
        $event->setBookingEndDate($endBooking[2] . '-' . $endBooking[1] . '-' . $endBooking[0]);
        $event->setSourceOrganisationId($thisUser->getOrganisationId());

        $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
        $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
        $event->setAddressId($addressId);
        $event->setState('draft');
        $event->save();

        if ($this->request->getPost('catering-lunch') != null) {
            $event->setLunch(true);
        }
        if ($this->request->getPost('catering-breakfast') != null) {
            $event->setBreakfast(true);
        }
        if ($this->request->getPost('catering-dinner') != null) {
            $event->setDinner(true);
        }
        if ($this->request->getPost('catering-refresh') != null) {
            $event->setLightRefreshment(true);
        }
        if ($this->request->getPost('catering-tea') != null) {
            $event->setAfternoonTea(true);
        }

        $event->setMinUnits($this->request->getPost('min-units', null, 0));
        $event->setMaxUnits($this->request->getPost('max-units', null, 0));
        $event->setPackageSize($this->request->getPost('package-size', null, 1));
        $event->setCurrencyId($this->request->getPost('currency'));
        $event->setUnitPrice($this->request->getPost('price-per-unit') * 100);
        $event->setPricePerAttendee($this->request->getPost('cost-per-unit') * 100);
        $event->setTaxablePercent($this->request->getPost('tax-rate'));
        $event->setCostToDeliver($this->request->getPost('cost-to-deliver') * 100);
        $event->setMarketValue($this->request->getPost('market-value') * 100);

        if (count($interests) != 0) {
            $event->addCategory($interests);
        }

        if (count($goals) != 0) {
            $event->addGoal($goals);
        }

        $event->setGender($this->request->getPost('gender'));

        if ($this->request->getPost('age18to34') != null) {
            $event->setTargetAge18to34(true);
        }
        if ($this->request->getPost('age34to65') != null) {
            $event->setTargetAge34to65(true);
        }
        if ($this->request->getPost('age65over') != null) {
            $event->setTargetAge65Plus(true);
        }

        $event->setTier($this->request->getPost('tier'));


        $event->setCreatorId($thisUser->getUserId());
        $event->setPurchaseTerms($this->request->getPost('purchase-terms'));
        $event->setAttendanceTerms($this->request->getPost('attendance-terms'));

        $event->update();

        switch ($this->request->getPost('publishstate')) {
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
                if ($event->publishPrivate()) {
                    Organisation::getActiveUsersOrganisation()->addEventToVault($event, $thisUser);
                }
                break;
        }

        $pdf = new \Apprecie\Library\Pdf\Pdf();
        $pdf->generate($event->getItemId());

        echo json_encode(array('status' => 'created', 'itemId' => $event->getItemId()));
    }

    public function ajaxCreateArrangedAction()
    {
        $this->getRequestFilter()->addRequired('confirmed-title', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('languageid', \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-short-description', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-description', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-bookingstart', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $startDate = explode('/', $this->request->getPost('confirmed-startdate'));
        $endDate = explode('/', $this->request->getPost('confirmed-enddate'));
        $startBooking = explode('/', $this->request->getPost('confirmed-bookingstart'));
        $endBooking = explode('/', $this->request->getPost('confirmed-bookingend'));
        $interests = $this->request->getPost('interests');
        $goals = $this->request->getPost('goal');

        $thisUser = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser();

        $event = new Event();
        $event->setLanguageId($this->request->getPost('languageid'));
        $event->setTitle($this->request->getPost('confirmed-title'));
        $event->setSummary($this->request->getPost('confirmed-short-description'));
        $event->setDescription($this->request->getPost('confirmed-description'));
        $event->setIsByArrangement(true);
        $event->setBookingStartDate($startBooking[2] . '-' . $startBooking[1] . '-' . $startBooking[0]);

        if (count($endBooking) == 3) {
            $event->setBookingEndDate($endBooking[2] . '-' . $endBooking[1] . '-' . $endBooking[0]);
        } else { //open booking end date - set at max
            $event->setBookingEndDate(
                date('Y-m-d H:i:s', \Phalcon\DI::getDefault()->get('config')->environment->timestampmax)
            );
        }

        $event->setSourceOrganisationId($thisUser->getOrganisationId());

        if (count($startDate) == 3) {
            $event->setStartDateTime(
                $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0] . ' ' . $this->request->getPost(
                    'confirmed-starttime'
                )
            );
            $event->setEndDateTime(
                $endDate[2] . '-' . $endDate[1] . '-' . $endDate[0] . ' ' . $this->request->getPost('confirmed-endtime')
            );
        }

        $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
        if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $event->setAddressId($addressId);
        }

        $event->setState('draft');
        $event->save();

        if ($this->request->getPost('catering-lunch') != null) {
            $event->setLunch(true);
        }
        if ($this->request->getPost('catering-breakfast') != null) {
            $event->setBreakfast(true);
        }
        if ($this->request->getPost('catering-dinner') != null) {
            $event->setDinner(true);
        }
        if ($this->request->getPost('catering-refresh') != null) {
            $event->setLightRefreshment(true);
        }
        if ($this->request->getPost('catering-tea') != null) {
            $event->setAfternoonTea(true);
        }

        if ($this->request->getPost('min-units') != '') {
            $event->setMinUnits($this->request->getPost('min-units', null));
        }

        $event->setMaxUnits(1);

        if ($this->request->getPost('package-size') != '') {
            $event->setPackageSize($this->request->getPost('package-size', null));
        }
        $event->setCurrencyId($this->request->getPost('currency'));
        if ($this->request->getPost('price-per-unit') != '') {
            $event->setUnitPrice($this->request->getPost('price-per-unit') * 100);
        }
        if ($this->request->getPost('cost-per-unit') != '') {
            $event->setPricePerAttendee($this->request->getPost('cost-per-unit') * 100);
        }
        if ($this->request->getPost('tax-rate') != '') {
            $event->setTaxablePercent($this->request->getPost('tax-rate'));
        }
        if ($this->request->getPost('cost-to-deliver') != '') {
            $event->setCostToDeliver($this->request->getPost('cost-to-deliver') * 100);
        }
        if ($this->request->getPost('market-value') != '') {
            $event->setMarketValue($this->request->getPost('market-value') * 100);
        }

        if (count($interests) != 0) {
            $event->addCategory($interests);
        }

        if (count($goals) != 0) {
            $event->addGoal($goals);
        }

        $event->setGender($this->request->getPost('gender'));

        if ($this->request->getPost('age18to34') != null) {
            $event->setTargetAge18to34(true);
        }
        if ($this->request->getPost('age34to65') != null) {
            $event->setTargetAge34to65(true);
        }
        if ($this->request->getPost('age65over') != null) {
            $event->setTargetAge65Plus(true);
        }

        $event->setTier($this->request->getPost('tier'));


        $event->setCreatorId($thisUser->getUserId());
        $event->setPurchaseTerms($this->request->getPost('purchase-terms'));
        $event->setAttendanceTerms($this->request->getPost('attendance-terms'));

        $event->update();

        switch ($this->request->getPost('publishstate')) {
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
                if ($event->publishPrivate()) {
                    Organisation::getActiveUsersOrganisation()->addEventToVault($event, $thisUser);
                }
                break;
        }

        $pdf = new \Apprecie\Library\Pdf\Pdf();
        $pdf->generate($event->getItemId());

        echo json_encode(array('status' => 'created', 'itemId' => $event->getItemId()));
    }

    public function ajaxEditEventAction($eventId)
    {
        $this->getRequestFilter()->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-title', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-short-description', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-description', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $event = Event::resolve($eventId);
        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($thisUser, $event->getItemId());

        $status = 'success';
        $message = '';

        $startDate = explode('/', $this->request->getPost('confirmed-startdate'));
        $endDate = explode('/', $this->request->getPost('confirmed-enddate'));
        $interests = $this->request->getPost('interests');
        $goals = $this->request->getPost('goal');

        $event->setTitle($this->request->getPost('confirmed-title'));
        $event->setSummary($this->request->getPost('confirmed-short-description'));
        $event->setDescription($this->request->getPost('confirmed-description'));
        $event->setStartDateTime(
            $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0] . ' ' . $this->request->getPost(
                'confirmed-starttime'
            )
        );
        $event->setEndDateTime(
            $endDate[2] . '-' . $endDate[1] . '-' . $endDate[0] . ' ' . $this->request->getPost('confirmed-endtime')
        );

        $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
        if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $event->setAddressId($addressId);
        }

        if ($event->getState() != \Apprecie\Library\Items\ItemState::ARRANGING) {
            $event->setState('draft');
            $startBooking = explode('/', $this->request->getPost('confirmed-bookingstart'));
            $endBooking = explode('/', $this->request->getPost('confirmed-bookingend'));
            $event->setBookingStartDate(
                $startBooking[2] . '-' . $startBooking[1] . '-' . $startBooking[0] . ' 00:00:00'
            );
            $event->setBookingEndDate($endBooking[2] . '-' . $endBooking[1] . '-' . $endBooking[0] . ' 23:59:59');
        } else {
            $endBooking = explode('/', $this->request->getPost('confirmed-bookingend'));
            $event->setBookingStartDate(date("Y-m-d H:i:s"));
            $event->setBookingEndDate($endBooking[2] . '-' . $endBooking[1] . '-' . $endBooking[0] . ' 23:59:59');
        }

        $event->save();

        $event->setLunch(false);
        $event->setBreakfast(false);
        $event->setDinner(false);
        $event->setLightRefreshment(false);
        $event->setAfternoonTea(false);

        if ($this->request->getPost('catering-lunch') != null) {
            $event->setLunch(true);
        }
        if ($this->request->getPost('catering-breakfast') != null) {
            $event->setBreakfast(true);
        }
        if ($this->request->getPost('catering-dinner') != null) {
            $event->setDinner(true);
        }
        if ($this->request->getPost('catering-refresh') != null) {
            $event->setLightRefreshment(true);
        }
        if ($this->request->getPost('catering-tea') != null) {
            $event->setAfternoonTea(true);
        }

        $event->setMinUnits($this->request->getPost('min-units', null, 0));
        $event->setMaxUnits($this->request->getPost('max-units', null, 0));
        $event->setPackageSize($this->request->getPost('package-size', null, 1));
        $event->setCurrencyId($this->request->getPost('currency'));
        $event->setUnitPrice($this->request->getPost('price-per-unit') * 100);
        $event->setPricePerAttendee($this->request->getPost('cost-per-unit') * 100);
        $event->setTaxablePercent($this->request->getPost('tax-rate'));
        $event->setCostToDeliver($this->request->getPost('cost-to-deliver') * 100);
        $event->setMarketValue($this->request->getPost('market-value') * 100);

        $event->removeCategory($event->getCategories());

        if (count($interests) != 0) {
            $event->addCategory($interests);
        }

        $event->removeGoal($event->getGoals());

        if (count($goals) != 0) {
            $event->addGoal($goals);
        }

        $event->setGender($this->request->getPost('gender'));

        $event->setTargetAge18to34(false);
        $event->setTargetAge34to65(false);
        $event->setTargetAge65Plus(false);

        if ($this->request->getPost('age18to34') != null) {
            $event->setTargetAge18to34(true);
        }
        if ($this->request->getPost('age34to65') != null) {
            $event->setTargetAge34to65(true);
        }
        if ($this->request->getPost('age65over') != null) {
            $event->setTargetAge65Plus(true);
        }

        $event->setTier($this->request->getPost('tier'));

        $event->setCreatorId($thisUser->getUserId());
        $event->setPurchaseTerms($this->request->getPost('purchase-terms'));
        $event->setAttendanceTerms($this->request->getPost('attendance-terms'));

        $event->update();

        switch ($this->request->getPost('publishstate')) {
            case "parent":
                $event->pushCuratedParent();
                break;
            case "curation":
                $event->pushCuratedApprecie();
                break;
            case "organisation":
                $event->publishPrivate();
                break;
            case "vault": {
                if ($event->publishPrivate()) {
                    Organisation::getActiveUsersOrganisation()->addEventToVault($event, $thisUser, true, true);
                }

                break;
            }
            case 'confirm' : {
                if (!$event->confirmArrangement()) {
                    $status = 'failed';
                    $message = _ms($event);
                }
                break;
            }
            case 'confirm-unpublish' : {
                if (!$event->confirmArrangement()) {
                    $status = 'failed';
                    $message = _ms($event);
                } else {
                    $parent = $event->getByArrangementSource();
                    $parent = $parent->getEvent();
                    $parent->unPublishEvent();
                }

                break;
            }
        }

        $pdf = new \Apprecie\Library\Pdf\Pdf();
        $pdf->generate($event->getItemId());

        echo _jm($status, $message);
    }

    public function ajaxEditArrangedAction($eventId)
    {
        $this->getRequestFilter()->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-title', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-short-description', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('confirmed-description', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $event = Event::resolve($eventId);
        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($thisUser, $event->getItemId());

        $startDate = explode('/', $this->request->getPost('confirmed-startdate'));
        $endDate = explode('/', $this->request->getPost('confirmed-enddate'));
        $startBooking = explode('/', $this->request->getPost('confirmed-bookingstart'));
        $endBooking = explode('/', $this->request->getPost('confirmed-bookingend'));
        $interests = $this->request->getPost('interests');
        $goals = $this->request->getPost('goal');

        $event->setTitle($this->request->getPost('confirmed-title'));
        $event->setSummary($this->request->getPost('confirmed-short-description'));
        $event->setDescription($this->request->getPost('confirmed-description'));

        if ($event->getState() != \Apprecie\Library\Items\ItemState::ARRANGING) {
            $event->setBookingStartDate($startBooking[2] . '-' . $startBooking[1] . '-' . $startBooking[0]);

            if (count($endBooking) == 3) {
                $event->setBookingEndDate($endBooking[2] . '-' . $endBooking[1] . '-' . $endBooking[0]);
            } else { //open booking end date - set at max
                $event->setBookingEndDate(
                    date('Y-m-d H:i:s', \Phalcon\DI::getDefault()->get('config')->environment->timestampmax)
                );
            }
        }

        if (count($startDate) == 3) {
            $event->setStartDateTime(
                $startDate[2] . '-' . $startDate[1] . '-' . $startDate[0] . ' ' . $this->request->getPost(
                    'confirmed-starttime'
                )
            );
        } else {
            $event->setStartDateTime(null);
        }

        if (count($endDate) == 3) {
            $event->setEndDateTime(
                $endDate[2] . '-' . $endDate[1] . '-' . $endDate[0] . ' ' . $this->request->getPost('confirmed-endtime')
            );
        } else {
            $event->setEndDateTime(null);
        }

        $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
        if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $event->setAddressId($addressId);
        }

        if (!$event->getState() == \Apprecie\Library\Items\ItemState::ARRANGING) {
            $event->setState('draft');
        }

        $event->save();

        $event->setLunch(false);
        $event->setBreakfast(false);
        $event->setDinner(false);
        $event->setLightRefreshment(false);
        $event->setAfternoonTea(false);

        if ($this->request->getPost('catering-lunch') != null) {
            $event->setLunch(true);
        }
        if ($this->request->getPost('catering-breakfast') != null) {
            $event->setBreakfast(true);
        }
        if ($this->request->getPost('catering-dinner') != null) {
            $event->setDinner(true);
        }
        if ($this->request->getPost('catering-refresh') != null) {
            $event->setLightRefreshment(true);
        }
        if ($this->request->getPost('catering-tea') != null) {
            $event->setAfternoonTea(true);
        }

        if ($this->request->getPost('min-units') != '') {
            $event->setMinUnits($this->request->getPost('min-units', null));
        }
        if ($this->request->getPost('package-size') != '') {
            $event->setPackageSize($this->request->getPost('package-size', null));
        }
        if ($this->request->getPost('max-units') != '') {
            $event->setMaxUnits($this->request->getPost('max-units', null)); //on personal this is actual units
        }

        $event->setCurrencyId($this->request->getPost('currency'));
        if ($this->request->getPost('price-per-unit') != '') {
            $event->setUnitPrice($this->request->getPost('price-per-unit') * 100);
        }
        if ($this->request->getPost('cost-per-unit') != '') {
            $event->setPricePerAttendee($this->request->getPost('cost-per-unit') * 100);
        }
        if ($this->request->getPost('tax-rate') != '') {
            $event->setTaxablePercent($this->request->getPost('tax-rate'));
        }
        if ($this->request->getPost('cost-to-deliver') != '') {
            $event->setCostToDeliver($this->request->getPost('cost-to-deliver') * 100);
        }
        if ($this->request->getPost('market-value') != '') {
            $event->setMarketValue($this->request->getPost('market-value') * 100);
        }

        $event->removeCategory($event->getCategories());

        if (count($interests) != 0) {
            $event->addCategory($interests);
        }

        $event->removeGoal($event->getGoals());

        if (count($goals) != 0) {
            $event->addGoal($goals);
        }

        $event->setGender($this->request->getPost('gender'));

        $event->setTargetAge18to34(false);
        $event->setTargetAge34to65(false);
        $event->setTargetAge65Plus(false);

        if ($this->request->getPost('age18to34') != null) {
            $event->setTargetAge18to34(true);
        }
        if ($this->request->getPost('age34to65') != null) {
            $event->setTargetAge34to65(true);
        }
        if ($this->request->getPost('age65over') != null) {
            $event->setTargetAge65Plus(true);
        }

        $event->setTier($this->request->getPost('tier'));


        $event->setCreatorId($thisUser->getUserId());
        $event->setPurchaseTerms($this->request->getPost('purchase-terms'));
        $event->setAttendanceTerms($this->request->getPost('attendance-terms'));

        $event->update();


        switch ($this->request->getPost('publishstate')) {
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
                if ($event->publishPrivate()) {
                    Organisation::getActiveUsersOrganisation()->addEventToVault($event, $thisUser, true, true);
                }
                break;
        }

        $pdf = new \Apprecie\Library\Pdf\Pdf();
        $pdf->generate($event->getItemId());

        echo json_encode(array('status' => 'created'));
    }

    function mediaAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = Item::resolve($itemId);
        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($thisUser, $item);

        $this->view->setLayout('application');

        // if not your item
        if ($item->getCreatorId() != $thisUser->getUserId()) {
            $this->response->redirect('/error/fourofour');
            $this->response->send();
            return;
        }
        Assets::createItemAssetDirectory($itemId);
        $this->view->item = $item;
        $itemMedia = ItemMedia::query()
            ->where("itemId=:iid:", array('iid' => $itemId))
            ->orderBy('[order]')
            ->execute();
        $this->view->itemMedia = $itemMedia;
    }

    function uploadBannerAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request);

        $item = Item::resolve($itemId);
        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($thisUser, $item);

        $this->view->disable();
        $imageName = $itemId . '-banner';
        if ($this->request->hasFiles()) {
            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getType() != 'image/jpeg') {
                    echo json_encode(array('status' => 'failed', 'message' => 'Invalid image type, must be JPG'));
                    return;
                }
                $tempLocation = Assets::getItemAssetDirectory($itemId) . '/' . $imageName . '-temp.jpg';;
                $location = Assets::getItemAssetDirectory($itemId) . '/' . $imageName . '.jpg';
                $resizeLocation = $location . '.resize';
                $file->moveTo($tempLocation);
                $dimensions = getimagesize($tempLocation);
                if ($dimensions[0] < 1170 or $dimensions[1] < 350) {
                    echo json_encode(
                        array(
                            'status' => 'failed',
                            'message' => 'Image must be 1170 x 350 or larger'
                        )
                    );
                    unlink($tempLocation);
                    return;
                } else {
                    if (Assets::resize_image($tempLocation, $resizeLocation, 1170, 350)) {
                        if (file_exists($location)) {
                            unlink($location);
                        }
                        rename($resizeLocation, $location);
                        echo json_encode(
                            array(
                                'status' => 'success',
                                'url' => '/assets/items/' . $itemId . '/' . $imageName . '.jpg'
                            )
                        );
                    } else {
                        _jm('failed', 'The image was invalid or of an unexpected type');
                    }
                    unlink($tempLocation);
                }
            }
        } else {
            echo json_encode(array('status' => 'failed', 'message' => 'No media file was received'));
        }

    }

    function uploadImageAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request);

        $item = Item::resolve($itemId);
        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($thisUser, $item);

        $this->view->disable();
        $itemMedia = ItemMedia::findBy('itemId', $itemId);
        $thisMediaItemNumber = ($itemMedia->count() + 1);
        $imageName = uniqid();
        if ($itemMedia->count() >= 8) {
            echo json_encode(
                array(
                    'status' => 'failed',
                    'message' => 'You have uploaded the maximum number of media items, please remove an item and try again.'
                )
            );
        } else {
            if ($this->request->hasFiles()) {
                foreach ($this->request->getUploadedFiles() as $file) {
                    if ($file->getType() != 'image/jpeg') {
                        echo json_encode(array('status' => 'failed', 'message' => 'Invalid image type, must be JPG'));
                        return;
                    }
                    $tempLocation = Assets::getItemAssetDirectory($itemId) . '/' . $imageName . '-temp.jpg';;
                    $location = Assets::getItemAssetDirectory($itemId) . '/' . $imageName . '.jpg';
                    $resizeLocation = $location . '.resize';
                    $file->moveTo($tempLocation);
                    $dimensions = getimagesize($tempLocation);
                    if ($dimensions[0] < 877 or $dimensions[1] < 493) {
                        echo json_encode(
                            array(
                                'status' => 'failed',
                                'message' => 'Image must be 877x493 or larger'
                            )
                        );
                        unlink($tempLocation);
                        return;
                    } else {
                        if (Assets::resize_image($tempLocation, $resizeLocation, 877, 493)) {
                            if (file_exists($location)) {
                                unlink($location);
                            }
                            rename($resizeLocation, $location);
                            $thisItem = new ItemMedia();
                            $thisItem->setItemId($itemId);
                            $thisItem->setOrder($thisMediaItemNumber);
                            $thisItem->setType('image');
                            $thisItem->setSrc('/assets/items/' . $itemId . '/' . $imageName . '.jpg');
                            $thisItem->save();

                            $pdf = new Apprecie\Library\Pdf\Pdf();
                            $pdf->generate($itemId);

                            echo json_encode(array('status' => 'success'));
                        } else {
                            _jm('failed', 'The image was invalid or of an unexpected type');
                        }
                        unlink($tempLocation);
                    }
                }
            } else {
                echo json_encode(array('status' => 'failed', 'message' => 'No media file was received'));
            }
        }
    }

    function addVideoAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::ANY)
            ->execute($this->request, true, false);

        $item = Item::resolve($itemId);
        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($thisUser, $item);

        $this->view->disable();
        $type = $this->request->getPost('type');
        $videoId = $this->request->getPost('id');
        $thumbnail = $this->request->getPost('thumbnail');

        $itemMedia = ItemMedia::findBy('itemId', $itemId);
        $thisMediaItemNumber = ($itemMedia->count() + 1);
        if ($itemMedia->count() >= 8) {
            echo json_encode(
                array(
                    'status' => 'failed',
                    'message' => 'You have uploaded the maximum number of media items, please remove an item and try again.'
                )
            );
        } else {
            $thisItem = new ItemMedia();
            $thisItem->setItemId($itemId);
            $thisItem->setOrder($thisMediaItemNumber);
            $thisItem->setType($type);
            $thisItem->setSrc($videoId);
            $thisItem->setThumbnail($thumbnail);
            $thisItem->save();

            $pdf = new Apprecie\Library\Pdf\Pdf();
            $pdf->generate($itemId);

            echo json_encode(array('status' => 'success'));
        }


    }

    function ajaxUpdateOrderAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $item = Item::resolve($itemId);
        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($thisUser, $item);

        $this->view->disable();
        $index = 1;
        foreach ($this->request->getPost('images') as $image) {
            $mediaRecord = ItemMedia::query()
                ->addWhere('mediaId=:1:')
                ->andWhere('itemId=:2:')
                ->bind([1 => $image, 2 => $itemId])
                ->execute()[0];
            $mediaRecord->setOrder($index);
            $mediaRecord->update();
            $index++;
        }

        $pdf = new Apprecie\Library\Pdf\Pdf();
        $pdf->generate($itemId);

        echo json_encode(array('status' => 'success'));
    }

    function AjaxDeleteMediaAction($mediaId)
    {
        $this->getRequestFilter()->addNonRequestRequired('mediaId', $mediaId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $media = ItemMedia::findFirstBy('mediaId', $mediaId);
        $thisUser = $this->getAuthenticatedUser();

        \Apprecie\Library\Acl\AccessControl::userCanEditItem($thisUser, $media->getItemId());

        $this->view->disable();

        if ($media->getType() == 'image') {
            $file = __DIR__ . '\..\..\public/' . $media->getSrc();
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $itemId = $media->getItemId();
        $media->delete();

        $itemMedia = ItemMedia::query()
            ->where('itemId=:1:')
            ->orderBy('[order]')
            ->bind([1 => $itemId])
            ->execute();

        $index = 1;
        if ($itemMedia->count() != 0) {
            foreach ($itemMedia as $mediaItem) {
                $mediaItem->setOrder($index);
                $mediaItem->save();
                $index++;
            }
        }

        $pdf = new Apprecie\Library\Pdf\Pdf();
        $pdf->generate($itemId);

        echo json_encode(array('status' => 'success'));
    }
}

