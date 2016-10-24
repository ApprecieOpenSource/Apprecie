<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 18/12/14
 * Time: 09:55
 */
class OrderItems extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $orderItemId, $orderId, $itemId, $userId, $portalId, $isPaidFull,
        $isReserved, $purchaseDate, $status, $reservationExpire, $organisationId,
        $packageQuantity, $value, $description, $tax, $adminFee, $commissionAmount,
        $reservationAmount, $packageSize, $cancelled, $cancelledReason, $fullConversionOfOrderItemId;

    public function getFormattedValue()
    {
        $currency = $this->getOrder()->getCurrency();
        $value = 0.00;

        if ($this->getValue() > 0) {
            $value = round($this->getValue() / 100, 2, PHP_ROUND_HALF_UP);
        }

        return $currency->getSymbol() . $value;
    }

    public function getFormattedCommission()
    {
        $currency = $this->getOrder()->getCurrency();
        $value = 0.00;

        if ($this->getCommissionAmount() > 0) {
            $value = round($this->getCommissionAmount() / 100, 2, PHP_ROUND_HALF_UP);
        }

        return $currency->getSymbol() . $value;
    }

    public function getFormattedTax()
    {
        $currency = $this->getOrder()->getCurrency();
        $value = 0.00;

        if ($this->getTax() > 0) {
            $value = round($this->getTax() / 100, 2, PHP_ROUND_HALF_UP);
        }

        return $currency->getSymbol() . $value;
    }

    public function getFormattedAdminFee()
    {
        $currency = $this->getOrder()->getCurrency();
        $value = 0.00;

        if ($this->getAdminFee() > 0) {
            $value = round($this->getAdminFee() / 100, 2, PHP_ROUND_HALF_UP);
        }

        return $currency->getSymbol() . $value;
    }

    public function getTotalUnits()
    {
        return $this->packageSize * $this->packageQuantity;
    }

    /**
     * @param mixed $cancelled
     */
    public function setCancelled($cancelled)
    {
        $this->cancelled = $cancelled;
    }

    /**
     * @param mixed $fullConversionOfOrderItemId
     */
    public function setFullConversionOfOrderItemId($fullConversionOfOrderItemId)
    {
        $this->fullConversionOfOrderItemId = $fullConversionOfOrderItemId;
    }

    /**
     * @return mixed
     */
    public function getFullConversionOfOrderItemId()
    {
        return $this->fullConversionOfOrderItemId;
    }

    /**
     * @return mixed
     */
    public function getCancelled()
    {
        return $this->cancelled;
    }

    /**
     * @param mixed $cancelledReason
     */
    public function setCancelledReason($cancelledReason)
    {
        $this->cancelledReason = $cancelledReason;
    }

    /**
     * @return mixed
     */
    public function getCancelledReason()
    {
        return $this->cancelledReason;
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
        return $this->packageSize;
    }

    /**
     * @param mixed $reservationAmount
     */
    public function setReservationAmount($reservationAmount)
    {
        $this->reservationAmount = $reservationAmount;
    }

    /**
     * @return mixed
     */
    public function getReservationAmount()
    {
        return $this->reservationAmount;
    }

    /**
     * @param mixed $isReserved
     */
    public function setIsReserved($isReserved)
    {
        $this->isReserved = $isReserved;
    }

    /**
     * @return mixed
     */
    public function getIsReserved()
    {
        return $this->isReserved;
    }

    /**
     * @param mixed $commissionAmount
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
     * @param mixed $adminFee
     */
    public function setAdminFee($adminFee)
    {
        $this->adminFee = $adminFee;
    }

    /**
     * @return mixed
     */
    public function getAdminFee()
    {
        return $this->adminFee;
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
    public function getTax()
    {
        return $this->tax;
    }


    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $packageQuantity
     */
    public function setPackageQuantity($packageQuantity)
    {
        $this->packageQuantity = $packageQuantity;
    }

    /**
     * @return mixed
     */
    public function getPackageQuantity()
    {
        return $this->packageQuantity;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;
    }

    /**
     * @param mixed $isPaid
     */
    public function setIsPaidFull($isPaid)
    {
        $this->isPaidFull = $isPaid;
    }

    /**
     * @return mixed
     */
    public function getIsPaidFull()
    {
        return $this->isPaidFull;
    }

    /**
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
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
    public function getOrderId()
    {
        return $this->orderId;
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
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * @param mixed $portalId
     */
    public function setPortalId($portalId)
    {
        $this->portalId = $portalId;
    }

    /**
     * @return mixed
     */
    public function getPortalId()
    {
        return $this->portalId;
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
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * @param mixed $reservationExpire
     */
    public function setReservationExpire($reservationExpire)
    {
        $this->reservationExpire = $reservationExpire;
    }

    /**
     * @return mixed
     */
    public function getReservationExpire()
    {
        return $this->reservationExpire;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function getSource()
    {
        return 'orderitems';
    }

    public function getConvertedOrderItem($options = null)
    {
        return $this->getRelated('convertedItem', $options);
    }

    public function initialize()
    {
        $this->hasOne('userId', 'User', 'userId', ['reusable' => true]);
        $this->belongsTo('orderId', 'Order', 'orderId', ['reusable' => true]);
        $this->hasOne('organisationId', 'Organisation', 'organisationId', ['reusable' => true]);
        $this->hasOne('itemId', 'Item', 'itemId', ['reusable' => true]);
        $this->hasOne('orderItemId', 'UserItems', 'orderItemId', ['reusable' => true]);
        $this->hasOne('fullConversionOfOrderItemId', 'OrderItem', 'orderItemId', ['alias' => 'convertedItem']);
    }

    /**
     * @return UserItems
     */
    public function getUserItemsRecord($options = null)
    {
        return $this->getRelated('UserItems', $options);
    }

    /**
     * @return User
     */
    public function getCustomer($options = null)
    {
        return $this->getRelated('User', $options);
    }

    /**
     * @return Order
     */
    public function getOrder($options = null)
    {
        return $this->getRelated('Order', $options);
    }

    public function getOrganisation($options = null)
    {
        return $this->getRelated('Organisation', $options);
    }

    /**
     * @return Item
     */
    public function getItem($options = null)
    {
        return $this->getRelated('Item', $options);
    }

    public function isHeld()
    {
        $heldRecords = UserItems::findBy('orderItemId', $this->getOrderItemId());
        return $heldRecords != null;
    }

    public function reHold($transaction = null)
    {
        if ((new \Apprecie\Library\Orders\OrderProcessor())->addItemForUser(
            $this,
            \Apprecie\Library\Items\UserItemState::HELD,
            $transaction,
            true
        )
        ) {
            $this->setCancelled(false);
            $this->setCancelledReason('');

            if ($this->update()) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function getUserItems()
    {

    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
        $this->setDefaultFields(['isPaidFull', 'isReserved', 'purchaseDate', 'reservationExpire', 'cancelled']);
        parent::onConstruct();
    }

    /**
     * @param \Apprecie\Library\Model\ApprecieModelBase|mixed $param
     * @param bool $throw
     * @param \Apprecie\Library\Model\ApprecieModelBase $instance
     * @return OrderItems | null
     */
    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        return parent::resolve($param, $throw, $instance);
    }
} 