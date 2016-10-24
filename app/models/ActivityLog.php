<?php

class ActivityLog extends \Apprecie\Library\Model\ApprecieModelBase
{
    protected $activityId, $ipAddress, $role, $ident, $activity, $activityDetails, $datetime, $sessionId, $portalId, $userId;
    protected static $_logTable = null;

    public static function setLogTable($table)
    {
        static::$_logTable = $table;
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

    /**
     * @param mixed $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return mixed
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param mixed $activityDetails
     */
    public function setActivityDetails($activityDetails)
    {
        $this->activityDetails = $activityDetails;
    }

    /**
     * @return mixed
     */
    public function getActivityDetails()
    {
        return $this->activityDetails;
    }

    /**
     * @return mixed
     */
    public function getActivityId()
    {
        return $this->activityId;
    }

    /**
     * @param mixed $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $ident
     */
    public function setIdent($ident)
    {
        $this->ident = $ident;
    }

    /**
     * @return mixed
     */
    public function getIdent()
    {
        return $this->ident;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
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
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function getSource()
    {
        if(static::$_logTable != null) {
            return static::$_logTable;
        }

        return 'activitylog';
    }

    public function onConstruct()
    {
        parent::onConstruct();
        $this->setDefaultFields('datetime');
    }

    public function initialize()
    {
        $this->hasOne('userId', 'User', 'userId');
        $this->hasOne('portalId', 'Portal', 'portalId');
    }

    /**
     * @return User
     */
    public function getUser($options = null)
    {
        return $this->getRelated('User', $options);
    }

    /**
     * @return Portal
     */
    public function getPortal($options)
    {
        return $this->getRelated('Portal', $options);
    }
}