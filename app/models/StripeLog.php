<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 15/03/15
 * Time: 11:53
 */
class StripeLog extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $stripeEventId, $liveMode, $object, $type, $stripeUserId, $pendingWebhooks, $stripeCreatedDate, $recordedDate, $organisationId, $data;

    /**
     * @param mixed $liveMode
     */
    public function setLiveMode($liveMode)
    {
        $this->liveMode = $liveMode;
    }

    /**
     * @return mixed
     */
    public function getLiveMode()
    {
        return $this->liveMode;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * @param mixed $pendingWebhooks
     */
    public function setPendingWebhooks($pendingWebhooks)
    {
        $this->pendingWebhooks = $pendingWebhooks;
    }

    /**
     * @return mixed
     */
    public function getPendingWebhooks()
    {
        return $this->pendingWebhooks;
    }

    /**
     * @param mixed $recordedData
     */
    public function setRecordedDate($recordedData)
    {
        $this->recordedDate = $recordedData;
    }

    /**
     * @return mixed
     */
    public function getRecordedDate()
    {
        return $this->recordedDate;
    }

    /**
     * @param mixed $stripeCreatedDate
     */
    public function setStripeCreatedDate($stripeCreatedDate)
    {
        $this->stripeCreatedDate = $stripeCreatedDate;
    }

    /**
     * @return mixed
     */
    public function getStripeCreatedDate()
    {
        return $this->stripeCreatedDate;
    }

    /**
     * @param mixed $stripeEventId
     */
    public function setStripeEventId($stripeEventId)
    {
        $this->stripeEventId = $stripeEventId;
    }

    /**
     * @return mixed
     */
    public function getStripeEventId()
    {
        return $this->stripeEventId;
    }

    /**
     * @param mixed $stripeUserId
     */
    public function setStripeUserId($stripeUserId)
    {
        $this->stripeUserId = $stripeUserId;
    }

    /**
     * @return mixed
     */
    public function getStripeUserIdd()
    {
        return $this->stripeUserIdd;
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
     * @param mixed $organisationId
     */
    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    public function getSource()
    {
        return 'stripelog';
    }

    public function onConstruct()
    {
        $this->setDefaultFields('recordedDate');
    }

    public function initialize()
    {
        $this->belongsTo('stripeUserId', 'PaymentSettings', 'stripeUserId', ['reusable' => true]);
        $this->hasOne('organisationId', 'Organisation', 'organisationId', ['reusable' => true]);
    }
} 