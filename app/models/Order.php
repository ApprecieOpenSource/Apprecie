<?php

class Order extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $orderId, $createdDate, $customerId, $supplierId, $fulfilled, $status, $statusReason, $currencyId;

    /**
     * return the amount that the customer will pay including tac and fees
     */
    public function getFormattedFullTotal()
    {
        $currency = $this->getCurrency();
        $total = $subTotal = $adminFee = $tax = 0.00;

        if ($this->getSubTotalValue() > 0) {
            $subTotal = round($this->getSubTotalValue() / 100, 2, PHP_ROUND_HALF_UP);
        }

        if ($this->getAdminFee() > 0) {
            $adminFee = round($this->getAdminFee() / 100, 2, PHP_ROUND_HALF_UP);
        }

        if ($this->getTax() > 0) {
            $tax = round($this->getTax() / 100, 2, PHP_ROUND_HALF_UP);
        }

        if ($this->getTotalPrice() > 0) {
            $total = round($this->getTotalPrice() / 100, 2, PHP_ROUND_HALF_UP);
        }

        return _g(
            'Total {symbol}{subtotal} + {symbol}{adminfee} admin fee + {symbol}{tax} vat = {symbol}{total}',
            [
                'symbol' => $currency->getSymbol(),
                'total' => $total,
                'adminfee' => $adminFee,
                'tax' => $tax,
                'subtotal' => $subTotal
            ]
        );
    }

    public function getSubTotalValue()
    {
        $items = $this->getOrderItems();

        $total = 0;

        foreach ($items as $orderItem) {
            if (!$orderItem->getCancelled()) {
                $total += $orderItem->getValue();
            }
        }

        return $total;
    }

    public function getTax()
    {
        $items = $this->getOrderItems();

        $total = 0;

        foreach ($items as $orderItem) {
            if (!$orderItem->getCancelled()) {
                if ($orderItem->getTax() != null) {
                    $total += $orderItem->getTax();
                }
            }
        }

        return $total;
    }

    public function getAdminFee()
    {
        $items = $this->getOrderItems();

        $total = 0;

        foreach ($items as $orderItem) {
            if (!$orderItem->getCancelled()) {
                if ($orderItem->getAdminFee() != null) {
                    $total += $orderItem->getAdminFee();
                }
            }
        }

        return $total;
    }

    public function getTotalPrice()
    {
        $total = $this->getSubTotalValue();
        $total += $this->getTax();
        $total += $this->getAdminFee();

        return $total;
    }

    public function getCommissionTotal()
    {
        $items = $this->getOrderItems();

        $total = 0;

        foreach ($items as $orderItem) {
            if (!$orderItem->getCancelled()) {
                if ($orderItem->getCommissionAmount() != null) {
                    $total += $orderItem->getCommissionAmount();
                }
            }
        }

        return $total;
    }

    public function getTotalAdminCharge()
    {
        $admin = $this->getAdminFee();
        $com = $this->getCommissionTotal();
        return $admin + $com;
    }

    /**
     * @param mixed $currencyId
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
    }

    /**
     * @return mixed
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
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
     * @param mixed $statusReason
     */
    public function setStatusReason($statusReason)
    {
        $this->statusReason = $statusReason;
    }

    /**
     * @return mixed
     */
    public function getStatusReason()
    {
        return $this->statusReason;
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $fulfilled
     */
    public function setFulfilled($fulfilled)
    {
        $this->fulfilled = $fulfilled;
    }

    /**
     * @return mixed
     */
    public function getFulfilled()
    {
        return $this->fulfilled;
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
     * @param mixed $supplierId
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @return Currency
     */
    public function getCurrency($options = null)
    {
        return $this->getRelated('Currency', $options);
    }

    public function getSource()
    {
        return 'orders';
    }

    public function initialize()
    {
        $this->hasMany('orderId', 'OrderItems', 'orderId', ['reusable' => true]);
        $this->belongsTo('customerId', 'User', 'userId', ['alias' => 'customer', 'reusable' => true]);
        $this->hasOne('supplierId', 'User', 'userId', ['alias' => 'supplier', 'reusable' => true]);
        $this->hasOne('currencyId', 'Currency', 'currencyId', ['reusable' => true]);
    }

    /**
     * @return User
     */
    public function getCustomer($options = null)
    {
        return $this->getRelated('customer', $options);
    }

    /**
     * @return User
     */
    public function getSupplierUser($options = null)
    {
        return $this->getRelated('supplier', $options);
    }

    /**
     * @return OrderItem[]
     */
    public function getOrderItems($options = null)
    {
        return $this->getRelated('OrderItems', $options);
    }

    public function onConstruct()
    {
        $this->setDefaultFields(['createdDate']);
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
        parent::onConstruct();
    }

    public function sendConfirmation()
    {
        $user = $this->getCustomer();

        if ($user == null) {
            throw new LogicException('This order is not yet setup');
        }

        $email = new \Apprecie\Library\Mail\EmailUtility();

        $supplierSend = $email->sendPurchaseConfirmationToSupplier($user, $this);

        $customerSend = $email->sendPurchaseConfirmation($user, $this, $this->getSupplierUser()->getOrganisation());

        if (!$supplierSend || !$customerSend) {
            $this->logActivity('Failed to send order confirmation', $email->getMessagesString());
            return false;
        }

        return true;
    }


    /**
     * Will mark this order as cancelled,  if it only contains cancelled orderitems
     */
    public function resolveCancel()
    {
        $items = $this->getOrderItems();

        $cancel = true;

        foreach ($items as $item) {
            if (!$item->getCancelled()) {
                $cancel = false;
                break;
            }
        }

        if ($cancel) {
            $this->setStatus(\Apprecie\Library\Orders\OrderStatus::CANCELLED);
            $this->setStatusReason(_g('All order items are cancelled'));

            if (!$this->update()) {
                $this->logActivity(
                    'Failed to cancel order',
                    'Order ' . $this->getOrderId(
                    ) . ' should have been cancelled as all order items are cancelled :' . _ms($this)
                );
            }
        }
    }

    /**
     * Cancel order and all order items
     */
    public function cancelOrder($cancelReason)
    {
        if ($this->getStatus() == \Apprecie\Library\Orders\OrderStatus::CANCELLED) {
            return;
        }

        $items = $this->getOrderItems();

        foreach ($items as $item) {
            $item->setCancelled(true);
            $item->setCancelledReason($cancelReason);

            if (!$item->update()) {
                $this->appendMessageEx($item);
            }

            //ensure the held items are removed
            $record = UserItems::findFirstBy('orderItemId', $item->getOrderItemId());

            if ($record && $record->getState() == \Apprecie\Library\Items\UserItemState::HELD) {
                if (!$record->delete()) {
                    $this->appendMessageEx($record);
                }
            }
        }

        $this->setStatus(\Apprecie\Library\Orders\OrderStatus::CANCELLED);
        $this->setStatusReason($cancelReason);

        if (!$this->update()) {
            $this->logActivity('Failed to cancel order', 'order : ' . $this->getOrderId() . ' , ' . _ms($this));
            $this->appendMessageEx('Could not update order');
        }

        return !$this->hasMessages();
    }

    /**
     * Will attempt to rehold items if stock is available, or if currently still held.
     *
     * @return bool true is successful
     */
    public function reHoldIfPossible()
    {
        $items = $this->getOrderItems();

        foreach ($items as $item) {
            if (!$item->isHeld()) {
                $stockItem = $item->getItem();
                if ($stockItem->getRemainingPackages() < $item->getPackageQuantity()) {
                    return false;
                }
            }
        }

        $transaction = (new \Phalcon\Mvc\Model\Transaction\Manager())->get();
        $this->setTransaction($transaction);

        foreach ($items as $item) {
            if (!$item->reHold($transaction)) {
                $this->appendMessageEx($item);
            }
        }

        $this->setStatus(\Apprecie\Library\Orders\OrderStatus::PROCESSING);

        try {
            $this->hasMessages() ? $transaction->rollback() : $transaction->commit();
        } catch (\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return true;
    }

    /**
     * @param \Apprecie\Library\Model\ApprecieModelBase|mixed $param
     * @param bool $throw
     * @param \Apprecie\Library\Model\ApprecieModelBase $instance
     * @return Order | null
     */
    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        if ($instance != null && $instance instanceof Order) {
            return $instance;
        }

        return parent::resolve($param, $throw, $instance);
    }

    public function getCalendar($download = false)
    {
        $vCalendar = new \Eluceo\iCal\Component\Calendar('apprecie.com');

        $orderItems = $this->getOrderItems();
        foreach ($orderItems as $orderItem) {
            $orderItem = OrderItems::resolve($orderItem);
            $item = Item::resolve($orderItem->getItem());
            $event = $item->getEvent();
            $vCalendar->addComponent($event->getCalendarEvent());
        }

        if ($download) {
            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment');

            echo $vCalendar->render();
        } else {
            return $vCalendar;
        }

        return true;
    }
} 