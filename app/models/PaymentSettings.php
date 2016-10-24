<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 22/01/15
 * Time: 09:08
 */
class PaymentSettings extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $organisationId, $accessToken, $refreshToken, $publishableKey, $stripeUserId;

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
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

    /**
     * @param mixed $publishableKey
     */
    public function setPublishableKey($publishableKey)
    {
        $this->publishableKey = $publishableKey;
    }

    /**
     * @return mixed
     */
    public function getPublishableKey()
    {
        return $this->publishableKey;
    }

    /**
     * @param mixed $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return mixed
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
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
    public function getStripeUserId()
    {
        return $this->stripeUserId;
    }

    public function getSource()
    {
        return 'paymentsettings';
    }

    public function initialize()
    {
        $this->belongsTo('organisationId', 'organisation', 'organisationId');
        $this->hasMany('stripeUserId', 'StripeLog', 'stripeUserId');
    }
} 