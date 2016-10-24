<?php

use Apprecie\Library\Model\CachedApprecieModel;
use Apprecie\Library\Model\FindOptionsHelper;
use Apprecie\Library\Users\UserEx;

class AdminOrdersReport extends CachedApprecieModel
{
    protected $orderItemId, $orderId, $purchasingPortal, $purchasingOrganisation, $purhcasingPerson, $supplierName, $itemName,
        $eventDate, $spacesPurchased, $price, $adminFee, $commission, $tax, $status, $purchaseDate, $itemStatus;

    /**
     * @return mixed
     */
    public function getItemStatus()
    {
        return $this->itemStatus;
    }

    /**
     * @param mixed $itemStatus
     */
    public function setItemStatus($itemStatus)
    {
        $this->itemStatus = $itemStatus;
    }

    /**
     * @return mixed
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * @param mixed $orderItemId
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;
    }

    /**
     * @return mixed
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * @param mixed $purchaseDate
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return mixed
     */
    public function getPurchasingPortal()
    {
        return $this->purchasingPortal;
    }

    /**
     * @param mixed $purchasingPortal
     */
    public function setPurchasingPortal($purchasingPortal)
    {
        $this->purchasingPortal = $purchasingPortal;
    }

    /**
     * @return mixed
     */
    public function getPurchasingOrganisation()
    {
        return $this->purchasingOrganisation;
    }

    /**
     * @param mixed $purchasingOrganisation
     */
    public function setPurchasingOrganisation($purchasingOrganisation)
    {
        $this->purchasingOrganisation = $purchasingOrganisation;
    }

    /**
     * @return mixed
     */
    public function getPurhcasingPerson()
    {
        return $this->purhcasingPerson;
    }

    /**
     * @param mixed $purhcasingPerson
     */
    public function setPurhcasingPerson($purhcasingPerson)
    {
        $this->purhcasingPerson = $purhcasingPerson;
    }

    /**
     * @return mixed
     */
    public function getSupplierName()
    {
        return $this->supplierName;
    }

    /**
     * @param mixed $supplierName
     */
    public function setSupplierName($supplierName)
    {
        $this->supplierName = $supplierName;
    }

    /**
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @param mixed $itemName
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     * @return mixed
     */
    public function getEventDate()
    {
        return $this->eventDate;
    }

    /**
     * @param mixed $eventDate
     */
    public function setEventDate($eventDate)
    {
        $this->eventDate = $eventDate;
    }

    /**
     * @return mixed
     */
    public function getSpacesPurchased()
    {
        return $this->spacesPurchased;
    }

    /**
     * @param mixed $spacesPurchased
     */
    public function setSpacesPurchased($spacesPurchased)
    {
        $this->spacesPurchased = $spacesPurchased;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getAdminFee()
    {
        return $this->adminFee;
    }

    /**
     * @param mixed $adminFee
     */
    public function setAdminFee($adminFee)
    {
        $this->adminFee = $adminFee;
    }

    /**
     * @return mixed
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @param mixed $commission
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;
    }

    /**
     * @return mixed
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param mixed $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getSource()
    {
        return 'adminordersreport';
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
        parent::onConstruct();
    }

    public static function buildReport($refresh = false)
    {
        $orderId = 0;
        $startPortal = (new UserEx())->getActiveQueryPortal();

        if($refresh == false) { //update to last item in table
            $lastRecord = AdminOrdersReport::findBySql('1=1 order by orderId DESC limit 1');

            if($lastRecord->count() == 0) {
                AdminOrdersReport::buildReport(true);
                return;
            }

            $orderId = $lastRecord[0]->getOrderId();
        } else { //clear the table
            AdminOrdersReport::findAll()->delete();
        }

        $options = FindOptionsHelper::prepareFindOptions('orderId', null, null, 'orderId > ?1', [1=>$orderId]);
        $orderItems = \OrderItems::find($options);

        foreach($orderItems as $orderItem) {
            /** @var $orderItem \OrderItems */
            /** @var $order \Order */
            $order = $orderItem->getOrder();

            $customer = $order->getCustomer();
            $supplier = $order->getSupplierUser();

            $orderReport = new AdminOrdersReport();
            $orderReport->setOrderId($order->getOrderId());
            $orderReport->setPurchasingPortal($customer->getPortal()->getPortalName());
            $orderReport->setPurchasingOrganisation($customer->getOrganisation()->getOrganisationName());
            $orderReport->setSupplierName($supplier->getOrganisation()->getOrganisationName());
            $orderReport->setItemStatus($orderItem->getStatus());
            $orderReport->setStatus($order->getStatus());
            $orderReport->setPrice($orderItem->getFormattedValue());
            $orderReport->setCommission($orderItem->getFormattedCommission());
            $orderReport->setAdminFee($orderItem->getFormattedAdminFee());
            $orderReport->setTax($orderItem->getFormattedTax());
            $orderReport->setOrderItemId($orderItem->getOrderItemId());
            $orderReport->setPurchaseDate($order->getCreatedDate());

            $item = $orderItem->getItem();
            $orderReport->setItemName($item->getTitle());
            $event = $item->getEvent();

            if($event != null) {
                $orderReport->setEventDate($event->getStartDateTime());
                $orderReport->setSpacesPurchased($orderItem->getTotalUnits());
            }

            /**  Customer security context */
            UserEx::ForceActivePortalForUserQueries($customer->getPortalId());
            if($customer->getIsDeleted()) {
                $orderReport->setPurhcasingPerson('Deleted User');
            } else {
                $orderReport->setPurhcasingPerson($customer->getUserProfile()->getFullName());
            }

            $orderReport->save();
        }

        UserEx::ForceActivePortalForUserQueries($startPortal);
    }
}