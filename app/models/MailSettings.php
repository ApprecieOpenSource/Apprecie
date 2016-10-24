<?php

class MailSettings extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $organisationId, $smtpAddress, $smtpUser, $smtpPassword, $smtpPort;

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    /**
     * @param mixed $smtpAddress
     */
    public function setSmtpAddress($smtpAddress)
    {
        $this->smtpAddress = $smtpAddress;
    }

    /**
     * @return mixed
     */
    public function getSmtpAddress()
    {
        return $this->smtpAddress;
    }

    /**
     * @param mixed $smtpPassword
     */
    public function setSmtpPassword($smtpPassword)
    {
        $this->smtpPassword = $smtpPassword;
    }

    /**
     * @return mixed
     */
    public function getSmtpPassword()
    {
        return $this->smtpPassword;
    }

    /**
     * @param mixed $smtpPort
     */
    public function setSmtpPort($smtpPort)
    {
        $this->smtpPort = $smtpPort;
    }

    /**
     * @return mixed
     */
    public function getSmtpPort()
    {
        return $this->smtpPort;
    }

    /**
     * @param mixed $smtpUser
     */
    public function setSmtpUser($smtpUser)
    {
        $this->smtpUser = $smtpUser;
    }

    /**
     * @return mixed
     */
    public function getSmtpUser()
    {
        return $this->smtpUser;
    }

    public function getSource()
    {
        return 'mailsettings';
    }

    public function onConstruct()
    {
        parent::onConstruct();
        $this->setEncryptedFields('smtpPassword');
    }

    public function getOrganisation($options = null)
    {
        return $this->getRelated('organisation', $options);
    }

    public function initialize()
    {
        $this->belongsTo('organisationId', 'organisation', 'organisationId', ['reusable' => true]);
    }
} 