<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 18/12/14
 * Time: 09:59
 */
class Transaction extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $transactionId, $userId, $organisationId, $orderId, $currencyId, $amount, $total, $status, $tax, $transactionDate, $statusReason, $gatewayData;

    /**
     * @param mixed $gatewayData
     */
    public function setGatewayData($gatewayData)
    {
        $this->gatewayData = $gatewayData;
    }

    /**
     * @return mixed
     */
    public function getGatewayData()
    {
        return $this->gatewayData;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @param mixed $portalId
     */
    public function setOrganisationId($portalId)
    {
        $this->organisationId = $portalId;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
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
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $transactionDate
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    /**
     * @return mixed
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }

    /**
     * @param mixed $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
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
        return 'transactions';
    }

    public function initialize()
    {
        $this->hasOne('orderId', 'Order', 'orderId', ['reusable' => true]);
    }

    public function onConstruct()
    {
        $this->setDefaultFields(['transactionDate']);
    }

    /**
     * @return Order
     */
    public function getOrder($options = null)
    {
        return $this->getRelated('Order', $options);
    }
} 