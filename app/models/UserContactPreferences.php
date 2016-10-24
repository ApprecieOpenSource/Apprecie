<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 12/11/14
 * Time: 11:38
 */
class UserContactPreferences extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $alertsAndNotifications;
    protected $invitations;
    protected $suggestions;
    protected $partnerCommunications;
    protected $updatesAndNewsletters;
    protected $intervalInDays;
    protected $lastRun;
    protected $userId;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
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
    public function getIntervalInDays()
    {
        return $this->intervalInDays;
    }

    /**
     * @param mixed $intervalInDays
     */
    public function setIntervalInDays($intervalInDays)
    {
        $this->intervalInDays = $intervalInDays;
    }

    /**
     * @return mixed
     */
    public function getLastRun()
    {
        return $this->lastRun;
    }

    /**
     * @param mixed $lastRun
     */
    public function setLastRun($lastRun)
    {
        $this->lastRun = $lastRun;
    }

    /**
     * @param mixed $alertsAndNotifications
     */
    public function setAlertsAndNotifications($alertsAndNotifications)
    {
        $this->alertsAndNotifications = $alertsAndNotifications;
    }

    /**
     * @return mixed
     */
    public function getAlertsAndNotifications()
    {
        return $this->alertsAndNotifications;
    }

    /**
     * @param mixed $invitations
     */
    public function setInvitations($invitations)
    {
        $this->invitations = $invitations;
    }

    /**
     * @return mixed
     */
    public function getInvitations()
    {
        return $this->invitations;
    }

    /**
     * @param mixed $partnerCommunications
     */
    public function setPartnerCommunications($partnerCommunications)
    {
        $this->partnerCommunications = $partnerCommunications;
    }

    /**
     * @return mixed
     */
    public function getPartnerCommunications()
    {
        return $this->partnerCommunications;
    }

    /**
     * @param mixed $suggestions
     */
    public function setSuggestions($suggestions)
    {
        $this->suggestions = $suggestions;
    }

    /**
     * @return mixed
     */
    public function getSuggestions()
    {
        return $this->suggestions;
    }

    /**
     * @param mixed $updatesAndNewsletters
     */
    public function setUpdatesAndNewsletters($updatesAndNewsletters)
    {
        $this->updatesAndNewsletters = $updatesAndNewsletters;
    }

    /**
     * @return mixed
     */
    public function getUpdatesAndNewsletters()
    {
        return $this->updatesAndNewsletters;
    }

    public function getSource()
    {
        return 'usercontactpreferences';
    }

    public function initialize()
    {
        $this->hasOne('userId', 'User', 'userId');
    }

    public function onConstruct()
    {
        parent::onConstruct();
        $this->setDefaultFields(
            array(
                'alertsAndNotifications',
                'invitations',
                'suggestions',
                'partnerCommunications',
                'updatesAndNewsletters',
                'intervalInDays',
                'lastRun'
            )
        );
    }
} 