<?php

class Item extends \Apprecie\Library\Model\ApprecieModelBase
{
    use \Apprecie\Library\Tracing\ActivityTraceTrait;

    protected $itemId, $creatorId, $sourcePortalId, $sourceByArrangement, $type,
        $title, $state, $summary, $destination, $unitPrice, $taxablePercent, $tier,
        $purchaseTerms, $maxUnits, $rejectionReason, $reservationFee, $reservationEndDate,
        $reservationLength, $packageSize, $commissionAmount, $adminFee, $currencyId, $sourceOrganisationId, $dateCreated,
        $isByArrangement, $isArranged, $isArrangedFor, $arrangementMessageThread;

    protected $_eventCategories = array(), $_outputTBC = false;

    /**
     * Enabled or disables the output of TBC for field values.
     * @param $flag
     */
    public function enableTBCOutput($flag)
    {
        $this->_outputTBC = $flag;
    }

    /**
     * @param mixed $arrangementMessageThread
     */
    public function setArrangementMessageThread($arrangementMessageThread)
    {
        $this->arrangementMessageThread = $arrangementMessageThread;
    }

    /**
     * @return mixed
     */
    public function getArrangementMessageThread()
    {
        return $this->arrangementMessageThread;
    }

    /**
     * @param mixed $isArrangedFor
     */
    public function setIsArrangedFor($isArrangedFor)
    {
        $this->isArrangedFor = $isArrangedFor;
    }

    /**
     * @return mixed
     */
    public function getIsArrangedFor()
    {
        return $this->isArrangedFor;
    }

    /**
     * @param mixed $isArranged
     */
    public function setIsArranged($isArranged)
    {
        $this->isArranged = $isArranged;
    }

    /**
     * @return mixed
     */
    public function getIsArranged()
    {
        return $this->isArranged;
    }

    /**
     * @param mixed $isByArrangement
     */
    public function setIsByArrangement($isByArrangement)
    {
        $this->isByArrangement = $isByArrangement;
    }

    /**
     * @return mixed
     */
    public function getIsByArrangement()
    {
        return $this->isByArrangement;
    }

    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    public function getSourceOrganisationId()
    {
        return $this->sourceOrganisationId;
    }

    public function setSourceOrganisationId($organisationId)
    {
        $this->sourceOrganisationId = $organisationId;
    }

    public function clearStaticState()
    {
        $this->_eventCategories = null;
    }

    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @param mixed $adminFee
     */
    public function setAdminFee($adminFee)
    {
        $this->adminFee = $adminFee;
    }

    /**
     * @param bool $format
     * @param bool $includeSymbol
     * @param bool $emptyNull
     * @return string
     */
    public function getAdminFee($format = false, $includeSymbol = false, $emptyNull = false)
    {
        if($emptyNull && $this->adminFee == null) {
            return '';
        }

        if ($format && $this->adminFee > 0) {
            $formatted = sprintf('%0.2f', round($this->adminFee / 100, 2, PHP_ROUND_HALF_UP));

            if ($includeSymbol) {
                $symbol = $this->getCurrency()->getSymbol();
                return $symbol . $formatted;
            }

            return $formatted;
        }

        return $this->adminFee;
    }

    /**
     * @param mixed $commisionAmount
     */
    public function setCommissionAmount($commissionAmount)
    {
        $this->commissionAmount = $commissionAmount;
    }

    /**
     * @return mixed
     */
    public function getCommissionAmount()
    {
        return $this->commissionAmount;
    }

    /**
     * @param mixed $creatorId
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;
    }

    /**
     * @return mixed
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * @param mixed $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param mixed $maxUnits
     */
    public function setMaxUnits($maxUnits)
    {
        $this->maxUnits = $maxUnits;
    }

    /**
     * @return mixed
     */
    public function getMaxUnits()
    {
        if ($this->_outputTBC && $this->maxUnits == null) {
            return _g('TBC');
        }

        return $this->maxUnits;
    }

    /**
     * @param mixed $packageSize
     */
    public function setPackageSize($packageSize)
    {
        $this->packageSize = $packageSize;
    }

    /**
     * @return mixed
     */
    public function getPackageSize()
    {
        if ($this->_outputTBC && $this->packageSize == null) {
            return _g('TBC');
        }
        return $this->packageSize;
    }

    /**
     * @param mixed $purchaseTerms
     */
    public function setPurchaseTerms($purchaseTerms)
    {
        $this->purchaseTerms = $purchaseTerms;
    }

    /**
     * @return mixed
     */
    public function getPurchaseTerms()
    {
        return $this->purchaseTerms;
    }

    /**
     * @param mixed $rejectionReason
     */
    public function setRejectionReason($rejectionReason)
    {
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * @return mixed
     */
    public function getRejectionReason()
    {
        return $this->rejectionReason;
    }

    /**
     * @param mixed $reservationEndDate
     */
    public function setReservationEndDate($reservationEndDate)
    {
        $this->reservationEndDate = $reservationEndDate;
    }

    /**
     * @return mixed
     */
    public function getReservationEndDate()
    {
        if ($this->_outputTBC && $this->reservationEndDate == null) {
            return _g('TBC');
        }

        return $this->reservationEndDate;
    }

    /**
     * @param mixed $reservationFee
     */
    public function setReservationFee($reservationFee)
    {
        $this->reservationFee = $reservationFee;
    }

    /**
     * @param bool $format
     * @param bool $includeSymbol
     * @param bool $emptyNull
     * @return string
     */
    public function getReservationFee($format = false, $includeSymbol = false, $emptyNull = false)
    {
        if($emptyNull && $this->reservationFee == null) {
            return '';
        }

        if ($format && $this->unitPrice > 0) {
            $formatted = sprintf('%0.2f', round($this->reservationFee / 100, 2, PHP_ROUND_HALF_UP));

            if ($includeSymbol) {
                $symbol = $this->getCurrency()->getSymbol();
                return $symbol . $formatted;
            }

            return $formatted;
        }

        return $this->reservationFee;
    }

    /**
     * @param mixed $reservationLength
     */
    public function setReservationLength($reservationLength)
    {
        $this->reservationLength = $reservationLength;
    }

    /**
     * @return mixed
     */
    public function getReservationLength()
    {
        return $this->reservationLength;
    }

    /**
     * @param mixed $sourceByArrangement
     */
    public function setSourceByArrangement($sourceByArrangement)
    {
        $this->sourceByArrangement = $sourceByArrangement;
    }

    /**
     * @return mixed
     */
    public function getSourceByArrangement()
    {
        return $this->sourceByArrangement;
    }

    /**
     * @param mixed $sourcePortalId
     */
    public function setSourcePortalId($sourcePortalId)
    {
        $this->sourcePortalId = $sourcePortalId;
    }

    /**
     * @return mixed
     */
    public function getSourcePortalId()
    {
        return $this->sourcePortalId;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $taxablePercent
     */
    public function setTaxablePercent($taxablePercent)
    {
        $this->taxablePercent = $taxablePercent;
    }

    /**
     * @return mixed
     */
    public function getTaxablePercent()
    {
        if ($this->_outputTBC && $this->taxablePercent == null) {
            return _g('TBC');
        }

        return $this->taxablePercent;
    }

    /**
     * @param mixed $tier
     */
    public function setTier($tier)
    {
        $this->tier = $tier;
    }

    /**
     * @return mixed
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $unitPrice
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @param bool $format
     * @param bool $includeSymbol
     * @return mixed
     */
    public function getUnitPrice($format = false, $includeSymbol = false)
    {
        if ($this->_outputTBC && $this->unitPrice == null) {
            return _g('TBC');
        } elseif($this->_outputTBC && $this->unitPrice == 0) {
            return _g('Complimentary');
        }

        if ($format && $this->unitPrice > 0) {
            $formatted = sprintf('%0.2f', round($this->unitPrice / 100, 2, PHP_ROUND_HALF_UP));

            if ($includeSymbol) {
                $symbol = $this->getCurrency()->getSymbol();
                return $symbol . $formatted;
            }

            return $formatted;
        }

        return $this->unitPrice;
    }

    public function beforeCreate()
    {
        if ($this->getCommissionAmount() == 0) {
            $this->setCommissionAmount($this->getSourceOrganisation()->getQuotas()->getCommissionPercent());
        }

        parent::beforeCreate();
    }

    public function getSource()
    {
        return 'items';
    }

    public function initialize()
    {
        $this->hasMany('itemId', 'ItemInterest', 'itemId', ['reusable' => true]);
        $this->hasManyToMany(
            'itemId',
            'ItemInterest',
            'itemId',
            'interestId',
            'Interest',
            'interestId',
            ['alias' => 'categories', 'reusable' => true]
        );
        $this->hasMany('itemId', 'ItemMedia', 'itemId', ['reusable' => false]);
        $this->hasOne('currencyId', 'Currency', 'currencyId', ['reusable' => true]);
        $this->hasOne('sourceOrganisationId', 'Organisation', 'organisationId', ['reusable' => true]);
        $this->hasOne('itemId', 'ItemApproval', 'itemId');
        $this->hasMany('itemId', 'ItemVault', 'itemId', ['reusable' => true]);
        $this->hasOne('itemId', 'Event', 'itemId');
        $this->hasOne('sourceByArrangement', 'Item', 'itemId', ['alias' => 'sourceArrangement', 'reusable' => true]);
        $this->hasOne('isArrangedFor', 'User', 'userId', ['alias' => 'arrangedFor', 'reusable' => true]);
        $this->hasOne('creatorId', 'User', 'userId', ['alias' => 'createdBy', 'reusable' => true]);
        $this->hasOne(
            'arrangementMessageThread',
            'MessageThread',
            'ThreadId',
            ['alias' => 'arrangementThread', 'reusable' => true]
        );
    }

    /**
     * @return MessageThread
     */
    public function getArrangementMessageThreadObject($options = null)
    {
        return $this->getRelated('arrangementThread', $options);
    }

    /**
     * @param null $options
     * @return User
     */
    public function getCreatedBy($options = null)
    {
        return $this->getRelated('createdBy', $options);
    }

    /**
     * @return \User
     */
    public function getArrangedFor($options = null)
    {
        return $this->getRelated('arrangedFor', $options);
    }

    /**
     * @return Item
     */
    public function getByArrangementSource($options = null)
    {
        return $this->getRelated('sourceArrangement', $options);
    }

    /**
     * @return Event
     */
    public function getEvent($options = null)
    {
        return $this->getRelated('Event', $options);
    }

    public function onConstruct()
    {
        //static::setCachingMode(\Apprecie\library\Cache\CachingMode::InMemory);
        $this->setIndirectContentFields(['summary', 'purchaseTerms', 'rejectionReason', 'title']);
        $this->setDefaultFields(['dateCreated', 'isByArrangement', 'isArranged']);
    }

    public function getRelatedApproval($options = null)
    {
        return $this->getRelated('ItemApproval', $options);
    }

    /**
     * @return Organisation
     */
    public function getSourceOrganisation($options = null)
    {
        return $this->getRelated('organisation', $options);
    }

    /**
     * @return Currency
     */
    public function getCurrency($options = null)
    {
        return $this->getRelated('currency', $options);
    }

    public function getItemMedia($options = null)
    {
        return $this->getRelated('ItemMedia', $options);
    }

    /**
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getItemInterestLinks($options = null)
    {
        return $this->getRelated('ItemInterest', $options);
    }

    /**
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getCategories($options = null)
    {
        return $this->getRelated('categories', $options);
    }

    /**
     * Pushes this item to Apprecie for approval / curation
     *
     * @return bool true on success else false
     */
    public function pushCuratedApprecie()
    {
        $this->setState(\Apprecie\Library\Items\ItemState::APPROVING);
        $this->setDestination(\Apprecie\Library\Items\ItemDestination::CURATED_ITEM);

        if (!$this->update()) {
            return false;
        }

        $appreciePortal = Portal::findFirst("portalSubdomain='admin'");
        $apprecieOrg = $appreciePortal->getOwningOrganisation();

        if (!$apprecieOrg->addItemForApproval($this)) {
            $this->appendMessageEx($apprecieOrg);
            return false;
        }

        //send a message to admins
        $mail = new \Apprecie\Library\Mail\EmailUtility();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries('admin');
        $adminProfiles = UserProfile::find();

        foreach($adminProfiles as $profile) {
            if(filter_var($profile->getEmail(), FILTER_VALIDATE_EMAIL) != false) {
                $mail->sendGenericEmailMessage
                (
                    $profile->getEmail(),
                    "An Item '{$this->getTitle()}' has been sent to Apprecie for approval  by {$this->getSourceOrganisation()->getOrganisationName()}",
                    'New Item for approval',
                    $this->getSourceOrganisation(),
                    (new \Apprecie\Library\Request\Url())->getConfiguredPortalAddress('admin', 'items', 'viewevent', [$this->getItemId()])
                );
            }
        }

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
        return true;
    }

    /**
     * pushes this item to the parent organisation for approval / curration.
     *
     * Note that the supplier should be a member of an organisation setup as an affiliated supplier to a specific organisation
     *
     * @return bool true on success else false
     */
    public function pushCuratedParent()
    {
        $parentOrg = Organisation::getActiveUsersOrganisation()->getSuppliers();

        if ($parentOrg == null) {
            $this->appendMessageEx(
                _g(
                    'The user creating content belongs to a portal that is mis-configured. The portal that this affiliate supplies has not been set'
                )
            );
            return false;
        }

        $this->setState(\Apprecie\Library\Items\ItemState::APPROVING);
        $this->setDestination(\Apprecie\Library\Items\ItemDestination::PARENT_ITEM);

        if (!$this->update()) {
            return false;
        }

        if (!$parentOrg->addItemForApproval($this)) {
            $this->appendMessageEx($parentOrg);
            return false;
        }

        $notice = new \Apprecie\Library\Messaging\Notification();
        $managers = Organisation::getUsersInRole('Manager', $parentOrg);

        foreach($managers as $manager) {
            $url = \Apprecie\Library\Request\Url::getConfiguredPortalAddress(null, 'mycontent', 'approve', [$this->getItemId()]);
            $notice->addNotification($manager, _g('New approval request'), 'An new Item needs your approval - ' . $this->getTitle(), $url);
        }

        return true;
    }

    public function getHTMLEncodeAdapter($excludes = ['getDescription', 'getPurchaseTerms', 'getAttendanceTerms'])
    {
        return parent::getHTMLEncodeAdapter($excludes);
    }

    public function addCategory($category, $clearExisting = false)
    {
        if ($clearExisting) {
            $links = $this->getItemInterestLinks();
            $this->_eventCategories = array();
            foreach ($links as $link) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }
            }
        }

        if (is_array($category) || $category instanceof \ArrayAccess) {
            foreach ($category as $element) {
                if (!$this->addCategory($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $category = Interest::resolve($category);
        }

        //check if already exists if not just cleared all
        if (!$clearExisting) {
            $interestExists = ItemInterest::find(
                    "itemId = {$this->getItemId()} AND interestId = {$category->getInterestId()}"
                )->count() > 0;

            if ($interestExists) {
                return true;
            } //just indicate a positive result if requirement already set.
        }

        $itemInterest = new ItemInterest();
        $itemInterest->itemId = $this->getItemId();
        $itemInterest->interestId = $category->getInterestId();

        if (!$itemInterest->create()) {
            $this->appendMessageEx($itemInterest->getMessages());
            return false;
        }

        $this->_eventCategories[$category->getInterest()] = $category->getInterest(); //add to static cache
        return true;
    }

    public function hasCategory($categoryName)
    {
        if ($this->_eventCategories == null) {
            $this->_eventCategories = array();
            $categories = $this->getCategories();
            if ($categories == null || $categories->count() == 0) {
                return false;
            }

            foreach ($categories as $cat) {
                $this->_eventCategories[$cat->getInterest()] = $cat->getInterest();
            }
        }

        return in_array($categoryName, $this->_eventCategories);
    }

    /**
     * You can safely pass this method a list of categories containing interests not assigned to this
     * item, and it will remove the ones that are (bulk ready).
     *
     * A true response will be given so long as no existing category failed to be removed.
     * @param $category
     * @return bool
     */
    public function removeCategory($category)
    {
        if (is_array($category) || $category instanceof \ArrayAccess) {
            foreach ($category as $element) {
                if (!$this->removeCategory($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $category = Interest::resolve($category);
        }

        $links = $this->getItemInterestLinks();

        foreach ($links as $link) {
            if ($link->getInterestId() == $category->getInterestId()) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }

                if (array_key_exists($category->getInterest(), $this->_eventCategories)) {
                    unset($this->_eventCategories[$category->getInterest()]);
                }
                break;
            }
        }

        return true;
    }

    public static function findByCategories($categories)
    {
        $resolved = array();
        if (!is_array($categories)) {
            $categories = array($categories);
        }

        foreach ($categories as $cat) {
            $item = Interest::resolve($cat);
            $resolved[] = $item->getInterestId();
        }

        return Item::query()
            ->innerJoin('ItemInterest')
            ->inWhere('interestId', $resolved)
            ->execute();
    }

    /**
     * @param \Apprecie\Library\Model\ApprecieModelBase|mixed $param
     * @param bool $throw
     * @param \Apprecie\Library\Model\ApprecieModelBase $instance
     * @return Item | null
     */
    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        return parent::resolve($param, $throw, $instance);
    }

    public function getRemainingPackages()
    {
        $allocatedUnitRecords = $this->getModelsManager()->executeQuery(
            'SELECT SUM(originalUnits) as units FROM UserItems WHERE itemId = ' . $this->getItemId()
        )->getFirst();
        $allocatedUnits = $allocatedUnitRecords['units'];

        if ($allocatedUnits == 0) {
            return $this->getMaxUnits();
        }

        $usedPackages = ceil($allocatedUnits / $this->getPackageSize());

        return $this->getMaxUnits() - $usedPackages;
    }

    public function getReservedPackages()
    {
        $allocatedUnitRecords = $this->getModelsManager()->executeQuery(
            'SELECT SUM(originalUnits) as units FROM UserItems WHERE itemId = :0: AND state = :1: ',
            [$this->getItemId(), \Apprecie\Library\Items\UserItemState::RESERVED]
        )->getFirst();
        $allocatedUnits = $allocatedUnitRecords['units'];

        if ($allocatedUnits == 0) {
            return 0;
        }

        $usedPackages = ceil($allocatedUnits / $this->getPackageSize());

        return $usedPackages;
    }

    public function getPurchasedPackages()
    {
        $allocatedUnitRecords = $this->getModelsManager()->executeQuery(
            'SELECT SUM(originalUnits) as units FROM UserItems WHERE itemId = :0: AND state = :1: ',
            [$this->getItemId(), \Apprecie\Library\Items\UserItemState::OWNED]
        )->getFirst();
        $allocatedUnits = $allocatedUnitRecords['units'];

        if ($allocatedUnits == 0) {
            return 0;
        }

        $usedPackages = ceil($allocatedUnits / $this->getPackageSize());

        return $usedPackages;
    }

    /**
     * returns the total sold value of this item.
     * For a confirmed item this is all units sold of this exact item
     * For a by arrangement this is the value of all personalised items spawned and sold.
     * For a personalised item it will be treated as a confirmed
     *
     * return values are in units (i.e. pence)  of currency
     */
    public function getTotalValue()
    {
        if ($this->getIsByArrangement()) {
            $totalPrice = $this->getModelsManager()->executeQuery(
                'SELECT COALESCE(SUM(OrderItems.value),0) as total from Item inner Join OrderItems on Item.itemId = OrderItems.itemId WHERE sourceByArrangement = :0: AND isPaidFull = 1 ',
                [$this->getItemId()]
            )->getFirst();
        } else {
            $totalPrice = $this->getModelsManager()->executeQuery(
                'SELECT COALESCE(SUM(value),0) as total FROM OrderItems WHERE itemId = :0: AND isPaidFull = 1 ',
                [$this->getItemId()]
            )->getFirst();
        }

        $total = $totalPrice['total'];

        return $total;
    }

    /**
     * Returns 3 items that match the interests of this item from the perspective of the current user
     * @return array of items randomly ordered
     */
    public function getSimilarItems($quantity=4){
        $categories=[];
        $finalItems=[];
        $user=new \Apprecie\Library\Security\Authentication();
        foreach($this->getCategories() as $category){
            array_push($categories,$category->getInterestId());
        }

        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('ItemVault', 'Item.itemId = ItemVault.itemId')
            ->addJoin('Event', 'Item.itemId = Event.itemId')
            ->addJoin('ItemInterest', 'Event.itemId = ItemInterest.itemId', null, 'left')
            ->addJoin('InterestLink', 'ItemInterest.interestId = InterestLink.interestId', null, 'left');
            $filter->addAndEqualFilter(
                'organisationId',
                Organisation::getActiveUsersOrganisation()->getOrganisationId(),
                'ItemVault'
            )
            ->addAndNotEqualFilter('itemId', $this->getItemId(), 'Item')
            ->addFilter('bookingEndDate', date('Y-m-d'), '', '>=')
            ->addAndNotEqualFilter('status', \Apprecie\Library\Items\EventStatus::FULLY_BOOKED, 'Event')
            ->addAndNotEqualFilter('isArranged', true, 'Item')
            ->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING, 'Item');

            $filter->addInFilter('interestId', $categories, 'InterestLink');
            $items = $filter->execute('RAND()');

        $paginator = new \Phalcon\Paginator\Adapter\Model(
            array(
                "data" => $items,
                "limit" => $quantity,
                "page" => 1
            )
        );
        $page = $paginator->getPaginate();

        foreach ($page->items as $item) {

            /**@var Event $event */
            $event = $item->getEvent();

            $creator = User::findFirstBy('userId', $item->getCreatorId());
            $brand= $creator->getOrganisation();
            $result['item'] = $item->toArrayEx(null, true);
            if(strlen($item->getTitle())>77){
                $result['itemTitle'] = (mb_substr($item->getTitle(),0,77,'UTF-8')."...");
            }
            else{
                $result['itemTitle'] =($item->getTitle());
            }
            if(strlen($item->getSummary())>150){
                $result['shortSummary'] = mb_substr($item->getSummary(),0,150,'UTF-8')."...";
            }
            else{
                $result['shortSummary'] =$item->getSummary();
            }
            $result['brand'] = $creator->getOrganisation()->getOrganisationName();
            $result['image'] = Assets::getItemPrimaryImage($item->getItemId());
            $result['startDateTime'] = _fdt($event->getStartDateTime());
            $result['bookingEndDate'] = _fd($event->getBookingEndDate());
            if ($event->getAddress() != null) {
                $result['address'] = $event->getAddress()->toArray();
            }

            if($item->getIsByArrangement()==true){
                $result['startDate']=_g('Contact to arrange');
            }
            else{
                $result['startDate']=date('l jS \of F Y h:i A',strtotime($event->getStartDateTime()));
            }

            if (strtotime($event->getBookingStartDate()) > time()) {
                $result['specialStatus'] = 'Coming Soon';
            } elseif ($event->getStatus() == \Apprecie\Library\Items\EventStatus::FULLY_BOOKED || $event->getRemainingPackages() == 0) {
                $result['specialStatus'] = 'Fully Booked';
            } else {
                $result['specialStatus'] = '';
            }
            $result['categories'] = $item->getCategories()->toArray();
            $result['brandImage'] =Assets::getOrganisationBrandLogo($brand->getOrganisationId(),true);
            $result['suggestions']=\Apprecie\Library\Items\ItemSuggestions::getSuggestedUsers($this->getItemId());
            $result['suggestionsCount']=count($result['suggestions']['items']);

            $finalItems[] = $result;
        }
        return $finalItems;
    }
} 