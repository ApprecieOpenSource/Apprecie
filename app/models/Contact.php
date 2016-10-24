<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/12/14
 * Time: 16:33
 */
class Contact extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $contactId, $portalId, $addressId, $isPrimary, $recordName, $contactNameAndTitle, $contactPosition, $telephone, $mobile, $email, $organisationId;

    public function getSource()
    {
        return 'contact';
    }

    public function initialize()
    {
        $this->belongsTo('portalId', 'Portal', 'portalId', ['reusable' => true]);
        $this->belongsTo('organisationId', 'organisation', 'organisationId', ['reusable' => true]);
        $this->hasOne('addressId', 'Address', 'addressId', ['reusable' => true]);
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
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
     * @return Portal
     */
    public function getPortal($options = null)
    {
        return $this->getRelated('Portal', $options);
    }

    /**
     * @return Address
     */
    public function getAddress($options = null)
    {
        return $this->getRelated('Address', $options);
    }

    /**
     * @param mixed $addressId
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    /**
     * @return mixed
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @param mixed $contactId
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * @return mixed
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * @param mixed $contactNameAndTitle
     */
    public function setContactNameAndTitle($contactNameAndTitle)
    {
        $this->contactNameAndTitle = $contactNameAndTitle;
    }

    /**
     * @return mixed
     */
    public function getContactNameAndTitle()
    {
        return $this->contactNameAndTitle;
    }

    /**
     * @param mixed $contactPosition
     */
    public function setContactPosition($contactPosition)
    {
        $this->contactPosition = $contactPosition;
    }

    /**
     * @return mixed
     */
    public function getContactPosition()
    {
        return $this->contactPosition;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $isPrimary
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
    }

    /**
     * @return mixed
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * @param mixed $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
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
     * @param mixed $recordName
     */
    public function setRecordName($recordName)
    {
        $this->recordName = $recordName;
    }

    /**
     * @return mixed
     */
    public function getRecordName()
    {
        return $this->recordName;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }
} 