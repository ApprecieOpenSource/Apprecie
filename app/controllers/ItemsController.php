<?php

class ItemsController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole(\Apprecie\Library\Users\UserRole::SYS_ADMIN);
        $this->setAllowPortal('admin');
    }

    /**
     * default action that shows the list of users across all portals
     */
    public function indexAction()
    {
        $this->view->setLayout('application');
    }


    public function ajaxItemsAction()
    {
        $this->getRequestFilter()->addRequired('draw', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('length', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('start', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');

        $events = Event::query()
            ->innerJoin('Item', 'Item.itemId = Event.itemId')
            ->innerJoin('Organisation', 'Item.sourceOrganisationId = Organisation.organisationId')
            ->where('destination="curated"');

        if($this->request->getPost('portal') != null) {
            $portal = Portal::resolve($this->request->getPost('portal'));
            $events->andWhere('portalId = :portal:', ['portal'=>$portal->getPortalId()]);
        }

        if($this->request->getPost('type') != null) {
            if($this->request->getPost('type') == \Apprecie\Library\Items\ItemTypes::BY_ARRANGEMENT) {
                $events->andWhere('isByArrangement = 1');
            } elseif($this->request->getPost('type') == \Apprecie\Library\Items\ItemTypes::EVENT) {
                $events->andWhere('isByArrangement = 0');
            }
        }

        if($this->request->getPost('status') != null) {
            if($this->request->getPost('status') == 'pending') {
                $events->andWhere('status is null');
            } else {
                $events->andWhere('status = :status:', ['status'=>$this->request->getPost('status')]);
            }
        }

        if($this->request->getPost('tier') != null) {
            $events->andWhere('tier = :tier:', ['tier'=>$this->request->getPost('tier')]);
        }

        if($this->request->getPost('startDate') != null) {
            $events->andWhere('dateCreated >= :start:', ['start'=>_myd($this->request->getPost('startDate'), 'd/m/Y')]);
        }

        if($this->request->getPost('endDate') != null) {
            $events->andWhere('dateCreated <= :end:', ['end'=>_myd($this->request->getPost('endDate'), 'd/m/Y')]);
        }

        $events = $events->andWhere("isArranged = 0 and (state = 'approving' or state = 'approved')");
        $orderBy = $this->request->getPost('order');
        $orderField = $orderBy[0]['column'];
        $orderDirection = $orderBy[0]['dir'];

        $fieldWhiteList = [0=>'Item.itemId', 2=>'organisationName', 4=>'isByArrangement', 5=>'tier', 6=>'bookingEndDate', 16=>'status'];

        if(isset($fieldWhiteList[$orderField])) {
            $direction = $orderDirection == 'desc' ? 'DESC' : 'ASC';
            $events = $events->orderBy($fieldWhiteList[$orderField] . ' ' . $direction)
                ->execute();
        }

        $page = $this->getRequestFilter()->get('start') == 0 ? 0 : $this->getRequestFilter()->get('start') / $this->getRequestFilter()->get('length');

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $events,
                "limit" => $this->getRequestFilter()->get('length'),
                "page" => $page + 1
            )
        );

        $db = $this->getDbAdapter();
        $dbResult = $db->query("select coalesce(count(itemId)) as total from items where destination = 'curated' and isArranged = 0 and (state = 'approving' or state = 'approved')");
        $totalCount = $dbResult->fetchArray()['total'];

        $page = $paginator->getPaginate();

        $ja = [];
        $ja['draw'] = $this->getRequestFilter()->get('draw');
        $ja['recordsTotal'] = $totalCount;
        $ja['recordsFiltered'] = count($events);
        $jaData = [];

        $thisPortal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();

        foreach ($page->items as $event) {
            /** @var $item Event*/

            //Item id
            //Item Name
            //Supplier
            //Spaces Available
            //Type - "By Arrangement"; "Confirmed Event"
            //Tier - "Corporate"; "Tier 1"; "Tier 2"; "Tier 3"
            //Booking End Date
            //Event Start Date
            //Event End Date
            //Packages
            //Spaces Per Package
            //Supplier Contact - event creator
            //Price
            //Administration Fee
            //Commission
            //VAT
            //Status - "Awaiting Approval"; "Published"; "Closed"; "Expired"
            //Count of Portals Published To

            $availablePackages = $event->getRemainingPackages();
            $packageSize = $event->getPackageSize();

            $data = [];
            $event = $event->getHTMLEncodeAdapter();
            $data[] = $event->getItemId();
            $data[] = $event->getTitle();
            $data[] = $event->getSourceOrganisation()->getOrganisationName();
            $data[] = $packageSize * $availablePackages;


            $data[] = $event->getIsByArrangement() ? 'BA Event' : 'Confirmed Event';
            $data[] = (new \Apprecie\Library\Users\Tier('whatever'))->getTextByName($event->getTier());
            $data[] = $event->getBookingEndDate(true);
            $data[] = $event->getStartDateTime(true);
            $data[] = $event->getEndDateTime(true);
            $data[] = $availablePackages;
            $data[] = $packageSize;


            $creator = $event->getCreatedBy();

            if($creator == null || $creator->getIsDeleted()) {
                $data[] = 'Inactive User';
            } else {
                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($creator->getPortalId());
                $data[] = $creator->getUserProfile()->getFullName();
                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($thisPortal);
            }

            $data[] = $event->getUnitPrice(true, true);
            $data[] = $event->getAdminFee(true, true);
            $data[] = $event->getCommissionAmount() . '%';
            $data[] = $event->getTaxablePercent() . '%';

            if ($event != null) {
                if ($event->getStatus() == null) {
                    $data[] = _g('pending');
                } else {
                    $data[] = $event->getStatus();
                }
            }

            $data[] = ItemVault::getOrganisationPublishedCount($event->getItem());

            $data[1] = _a($data[1], '/items/viewevent/'. $event->getItemId());

            $jaData[] = $data;
        }

        $ja['data'] = $jaData;
        echo json_encode($ja);
    }

    public function AjaxCurationItemsAction($pageNumber = 1)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');

        $items = Item::query()
            ->where('destination="curated"')
            ->andWhere('state="approved"')
            ->andWhere('isArranged = 0')
            ->orderBy('itemId DESC')
            ->execute();

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $items,
                "limit" => 10,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();
        $itemsArrray = [];
        $itemsArrray['ThisPageNumber'] = $pageNumber;
        $itemsArrray['PageCount'] = $page->total_pages;
        $itemsArrray['PageResultCount'] = count($page->items);
        $itemsArrray['TotalResultCount'] = count($items);

        foreach ($page->items as $item) {
            $sourceOrganisation = Organisation::findFirstBy('organisationId', $item->getSourceOrganisationId());

            $item->sourceOrganisationName = $sourceOrganisation->getOrganisationName();
            $event = $item->getEvent();

            if ($event != null) {
                $item->fullStatus = $item->getEvent()->getStatus();
                if ($item->fullStatus == null) {
                    $item->fullStatus = _g('pending');
                }
            }

            $itemsArrray['items'][] = $item->toArrayEx(array('sourceOrganisationName', 'fullStatus'), true);
        }
        echo json_encode($itemsArrray);
    }

    public function AjaxApprovalItemsAction($pageNumber = 1)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');

        $items = Item::query()
            ->join('Event')
            ->where('destination="curated"')
            ->andWhere('state="approving"')
            ->orderBy('Item.itemId DESC')
            ->execute();
        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $items,
                "limit" => 10,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();
        $itemsArrray = [];
        $itemsArrray['ThisPageNumber'] = $pageNumber;
        $itemsArrray['PageCount'] = $page->total_pages;
        $itemsArrray['PageResultCount'] = count($page->items);
        $itemsArrray['TotalResultCount'] = count($items);

        foreach ($page->items as $item) {
            $sourceOrganisation = Organisation::findFirstBy('organisationId', $item->getSourceOrganisationId());

            $item->sourceOrganisationName = $sourceOrganisation->getOrganisationName();
            $event = $item->getEvent();

            if ($event != null) {
                $item->fullStatus = $item->getEvent()->getStatus();
                if ($item->fullStatus == null) {
                    $item->fullStatus = _g('pending');
                }
            }

            $itemsArrray['items'][] = $item->toArrayEx(array('sourceOrganisationName', 'fullStatus'), true);
        }
        echo json_encode($itemsArrray);
    }

    public function viewEventAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $this->requireRoleOrRedirect('SystemAdministrator');
        $item = Item::resolve($itemId);

        $event = $item->getEvent();
        if ($event == null) {
            _d('No such event');
        }
        $event->enableTBCOutput(true);
        $this->view->setLayout('application');

        if ($event->getIsByArrangement() == true) {
            $this->view->linkedEvents = Item::query()
                ->where('sourceByArrangement=:1:')
                ->bind([1 => $event->getItemId()])
                ->execute();
        }

        $this->view->event = $event->getHTMLEncodeAdapter();
    }

    public function eventProfileAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $this->requireRoleOrRedirect('SystemAdministrator');
        $item = Item::resolve($itemId);

        $this->view->setLayout('application');
        $event = $item->getEvent();

        $event->enableTBCOutput(true);
        $this->view->event = $event->getHTMLEncodeAdapter();
    }

    public function AjaxCurateToRolesInOrganisationAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');
        $item = Item::resolve($itemId);

        switch ($item->getType()) {
            case 'event':
                $event = $item->getEvent();
                foreach ($this->request->getPost('organisations') as $organisationId) {
                    $org = Organisation::resolve($organisationId);
                    $org->addEventToVault($event->getEventId(), null, null, null);
                }
                $item->setState('approved');
                $item->save();

                break;
        }
        echo json_encode(array('result' => 'success'));
    }

    public function curateAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');

        $includedPortalIdsForItemSearch = array();
        $includedPortalIdsForOrgSearch = array();
        $orgs = Organisation::query()->execute();
        foreach ($orgs as $org) {

            $apprecieSuppliers = Organisation::getUsersInRole(\Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER, $org);
            if ($apprecieSuppliers->count() >= 1 && !in_array($org->getPortalId(), $includedPortalIdsForItemSearch)) {
                $includedPortalIdsForItemSearch[] = $org->getPortalId();
            }

            $managers = Organisation::getUsersInRole(\Apprecie\Library\Users\UserRole::MANAGER, $org);
            if ($managers->count() >= 1 && !in_array($org->getPortalId(), $includedPortalIdsForOrgSearch)) {
                $includedPortalIdsForOrgSearch[] = $org->getPortalId();
            }
        }

        $portals = Portal::query()->orderBy('portalName')->execute();
        $itemSearchPortals = array();
        $orgSearchPortals = array();
        foreach ($portals as $portal) {
            if (in_array($portal->getPortalId(), $includedPortalIdsForItemSearch)) {
                $itemSearchPortals[] = $portal;
            }

            if (in_array($portal->getPortalId(), $includedPortalIdsForOrgSearch)) {
                $orgSearchPortals[] = $portal;
            }
        }
        $this->view->orgSearchPortals = $orgSearchPortals;
        $this->view->itemSearchPortals = $itemSearchPortals;

        if ($this->session->has('curateItemList')) {
            $this->view->items = $this->session->get('curateItemList');
        }

        if ($this->session->has('curateOrgList')) {
            $this->view->orgs = $this->session->get('curateOrgList');
        }
    }

    public function AjaxCurateAction()
    {
        if (!$this->request->isPost()) {
            return false;
        }

        if (!$this->request->isAjax()) {
            return false;
        }

        if(! $this->checkCSRF(true)) {
            return false;
        }

        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();

        if (!$this->session->has('curateItemList') || !$this->session->has('curateOrgList')) {
            _jm('failed', '');
            return false;
        }

        $items = $this->session->get('curateItemList');
        $orgs = $this->session->get('curateOrgList');

        if (!$items || !$orgs) {
            _jm('failed', '');
            return false;
        }

        $messages = array();
        foreach ($items as $item) {
            switch ($item->getType()) {
                case \Apprecie\Library\Items\ItemTypes::EVENT:
                    foreach ($orgs as $org) {
                        if (!$org->addEventToVault($item->getEvent()->getEventId(), null, null, null)) {
                            $messages[] = 'Item "' . $item->getTitle() . '" failed to be curated to organisation "' . $org->getOrganisationName() . '".';
                        }
                    }
                    break;
            }
        }

        echo json_encode([
            'status' => 'success',
            'messages' => $messages
        ]);
    }

    public function AjaxSearchItemsAction($pageNumber)
    {
        $this->getRequestFilter()
            ->addRequired('portalId', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('organisationId', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('pricingType', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('eventType', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();

        list($portalId, $organisationId, $pricingType, $eventType, $pageNumber) = $this->getRequestFilter()->getAll();

        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('ItemApproval', 'ItemApproval.itemId = Item.itemId');
        $filter->addAndEqualFilter('state', \Apprecie\Library\Items\ItemState::APPROVED, 'Item');
        $filter->addAndEqualFilter('destination', 'curated', 'Item');
        $filter->addAndEqualFilter('isArranged', '0', 'Item');

        if ($portalId === 'all') {

        } else {
            if ($organisationId === 'all') {

                $portalOrgs = Organisation::query()
                    ->where('portalId=:1:')
                    ->bind([1 => $portalId])
                    ->execute();

                $portalOrgIds = array();
                foreach ($portalOrgs as $org) {
                    $portalOrgIds[] = $org->getOrganisationId();
                }

                $filter->addInFilter('sourceOrganisationId', $portalOrgIds, 'Item');
            } else {
                $filter->addAndEqualFilter('sourceOrganisationId', $organisationId, 'Item');
            }
        }

        switch ($pricingType) {
            case 'all':
                break;
            case 'fixed':

                if ($this->request->hasPost('priceMin')) {
                    $min = $this->request->getPost('priceMin') * 100;
                } else {
                    $min = 1;
                }

                if ($this->request->hasPost('priceMax')) {
                    $max = $this->request->getPost('priceMax') * 100;
                } else {
                    $max = null;
                }

                $filter->addAndEqualOrGreaterThanFilter('unitPrice', $min, 'Item');

                if ($max) {
                    $filter->addAndEqualOrLessThanFilter('unitPrice', $max, 'Item');
                }

                $filter->addAndNotEqualFilter('unitPrice', '0', 'Item');
                $filter->addAndIsNotNullFilter('unitPrice', 'Item');

                break;
            case 'complimentary':
                $filter->addAndEqualFilter('unitPrice', '0', 'Item');
                break;
            case 'tbc':
                $filter->addAndIsNullFilter('unitPrice', 'Item');
                break;
        }

        switch ($eventType) {
            case 'all':
                break;
            case 'confirmed':
                $filter->addAndEqualFilter('isByArrangement', '0', 'Item');
                break;
            case 'ba':
                $filter->addAndEqualFilter('isByArrangement', '1', 'Item');
                break;
        }

        $items = $filter->execute('ItemApproval.lastProcessed DESC');

        if ($this->session->has('curateOrgList')) {
            $orgList = $this->session->get('curateOrgList');
        } else {
            $orgList = array();
        }

        $filteredItems = array();
        foreach ($items as $item) {

            $excludeItem = false;

            if (!$excludeItem && $orgList) {

                $vaultOrgs = ItemVault::query()
                    ->addWhere('itemId=:1:')
                    ->bind(array(1 => $item->getItemId()))
                    ->execute();

                $vaultOrgIds = array();
                foreach ($vaultOrgs as $vaultOrg) {
                    $vaultOrgIds[] = $vaultOrg->getOrganisationId();
                }

                foreach ($orgList as $org) {
                    if (in_array($org->getOrganisationId(), $vaultOrgIds)) {
                        $excludeItem = true;
                        break;
                    }
                }
            }

            if (!$excludeItem) {
                $filteredItems[] = $item;
            }
        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $filteredItems,
                "limit" => 20,
                "page" => $pageNumber
            )
        );
        $page = $paginator->getPaginate();

        $responseItems = array();
        if ($this->session->has('curateItemList')) {
            $selectedItems = $this->session->get('curateItemList');
        }

        foreach ($page->items as $item) {

            $row['item'] = $item->toArrayEx(null, true);

            switch ($item->getUnitPrice()) {
                case null:
                    $row['price'] = _g('TBC');
                    break;
                case 0:
                    $row['price'] = _g('Complimentary');
                    break;
                default:
                    $row['price'] = $item->getUnitPrice(true, true);
                    break;
            }

            if ($item->getIsByArrangement()) {
                $row['eventType'] = _g('BA');
            } else {
                $row['eventType'] = _g('Confirmed');
            }

            $organisation = Organisation::resolve($item->getSourceOrganisationId());
            $row['organisationName'] = $organisation->getOrganisationName();

            if (isset($selectedItems[$item->getItemId()])) {
                $row['checked'] = ' checked';
            } else {
                $row['checked'] = '';
            }

            $responseItems[] = $row;
        }

        $page->items = $responseItems;
        echo json_encode($page);
    }

    public function AjaxSearchOrgsAction($pageNumber)
    {
        $this->getRequestFilter()
            ->addRequired('portalId', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();

        list($portalId, $pageNumber) = $this->getRequestFilter()->getAll();

        $filter = new \Apprecie\Library\Search\SearchFilter('Organisation');
        $filter->addAndEqualFilter('suspended', '0', 'Organisation');

        if ($portalId === 'all') {

        } else {
            $filter->addAndEqualFilter('portalId', $portalId, 'Organisation');
        }

        if ($this->request->hasPost('name')) {
            $searchName = $this->request->getPost('name');
            if ($searchName) {
                $filter->addAndLikeFilter('organisationName', '%' . $searchName . '%', 'Organisation');
            }
        }

        $orgs = $filter->execute('portalId DESC, organisationId DESC');

        if ($this->session->has('curateItemList')) {
            $itemList = $this->session->get('curateItemList');
        } else {
            $itemList = array();
        }

        $filteredOrgs = array();
        foreach ($orgs as $org) {

            $excludeOrg = false;

            $managers = Organisation::getUsersInRole(\Apprecie\Library\Users\UserRole::MANAGER, $org);
            if (count($managers) < 1) {
                $excludeOrg = true;
            }

            if (!$excludeOrg && $itemList) {

                $vaultItems = ItemVault::query()
                    ->addWhere('organisationId=:1:')
                    ->bind(array(1 => $org->getOrganisationId()))
                    ->execute();

                $vaultItemIds = array();
                foreach ($vaultItems as $vaultItem) {
                    $vaultItemIds[] = $vaultItem->getItemId();
                }

                foreach ($itemList as $item) {
                    if (in_array($item->getItemId(), $vaultItemIds)) {
                        $excludeOrg = true;
                        break;
                    }
                }
            }

            if (!$excludeOrg) {
                $filteredOrgs[] = $org;
            }
        }

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $filteredOrgs,
                "limit" => 20,
                "page" => $pageNumber
            )
        );
        $page = $paginator->getPaginate();

        $responseOrgs = array();
        if ($this->session->has('curateOrgList')) {
            $selectedOrgs = $this->session->get('curateOrgList');
        }

        foreach ($page->items as $org) {

            $row['org'] = $org->toArrayEx(null, true);

            $portal = Portal::resolve($org->getPortalId());
            $row['portalName'] = $portal->getPortalName();
            $row['portalEdition'] = $portal->getEdition();

            if (isset($selectedOrgs[$org->getOrganisationId()])) {
                $row['checked'] = ' checked';
            } else {
                $row['checked'] = '';
            }

            $responseOrgs[] = $row;
        }

        $page->items = $responseOrgs;
        echo json_encode($page);
    }

    public function AjaxEditCurateItemListAction()
    {
        $this->getRequestFilter()
            ->addRequired('itemId', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('action', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();

        if ($this->session->has('curateItemList')) {
            $itemList = $this->session->get('curateItemList');
        } else {
            $itemList = array();
        }

        if ($this->session->has('curateOrgList')) {
            $orgList = $this->session->get('curateOrgList');
        } else {
            $orgList = array();
        }

        list($itemId, $action) = $this->getRequestFilter()->getAll();
        if (is_array($itemId)) {
            $itemIds = $itemId;
        } else {
            $itemIds = array($itemId);
        }

        $responseItems= array();
        foreach ($itemIds as $itemId) {

            $item = Item::resolve($itemId);
            if (!$item) {
                _jm('failed', 'Item does not exist.');
                return false;
            }

            if ($action === 'add') {
                $itemList[$item->getItemId()] = $item;
            } elseif ($action === 'remove') {
                if (isset($itemList[$item->getItemId()])) {
                    unset($itemList[$item->getItemId()]);
                }
            }

            $responseItems[] = array(
                'itemId' => $itemId,
                'title' => $item->getTitle()
            );
        }

        $this->session->set('curateItemList', $itemList);

        if (!count($itemList) || !count($orgList)) {
            $canCurate = 'false';
        } else {
            $canCurate = 'true';
        }

        echo json_encode([
            'status' => 'success',
            'action' => $action,
            'items' => $responseItems,
            'canCurate' => $canCurate
        ]);
    }

    public function AjaxEditCurateOrgListAction()
    {
        $this->getRequestFilter()
            ->addRequired('orgId', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('action', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();

        if ($this->session->has('curateItemList')) {
            $itemList = $this->session->get('curateItemList');
        } else {
            $itemList = array();
        }

        if ($this->session->has('curateOrgList')) {
            $orgList = $this->session->get('curateOrgList');
        } else {
            $orgList = array();
        }

        list($orgId, $action) = $this->getRequestFilter()->getAll();
        if (is_array($orgId)) {
            $orgIds = $orgId;
        } else {
            $orgIds = array($orgId);
        }

        $responseOrgs = array();
        foreach ($orgIds as $orgId) {

            $org = Organisation::resolve($orgId);
            if (!$org) {
                _jm('failed', 'Organisation does not exist.');
                return false;
            }

            if ($action === 'add') {
                $orgList[$org->getOrganisationId()] = $org;
            } elseif ($action === 'remove') {
                if (isset($orgList[$org->getOrganisationId()])) {
                    unset($orgList[$org->getOrganisationId()]);
                }
            }

            $responseOrgs[] = array(
                'orgId' => $orgId,
                'name' => $org->getOrganisationName()
            );
        }

        $this->session->set('curateOrgList', $orgList);

        if (!count($itemList) || !count($orgList)) {
            $canCurate = 'false';
        } else {
            $canCurate = 'true';
        }

        echo json_encode([
            'status' => 'success',
            'action' => $action,
            'orgs' => $responseOrgs,
            'canCurate' => $canCurate
        ]);
    }

    public function AjaxClearItemsAction()
    {
        if (!$this->request->isPost()) {
            return false;
        }

        if (!$this->request->isAjax()) {
            return false;
        }

        if(! $this->checkCSRF(true)) {
            return false;
        }

        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();

        if ($this->session->has('curateItemList')) {
            $this->session->remove('curateItemList');
        }

        _jm('success', '');
    }

    public function AjaxClearOrgsAction()
    {
        if (!$this->request->isPost()) {
            return false;
        }

        if (!$this->request->isAjax()) {
            return false;
        }

        if(! $this->checkCSRF(true)) {
            return false;
        }

        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();

        if ($this->session->has('curateOrgList')) {
            $this->session->remove('curateOrgList');
        }

        _jm('success', '');
    }
}