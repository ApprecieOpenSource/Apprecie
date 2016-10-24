<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/02/15
 * Time: 13:13
 */
class OrganisationManagementPermissions extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $userId, $organisationId;

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
        return 'organisationmanagementpermissions';
    }

    public function Initialize()
    {
        $this->hasOne('organisationId', 'Organisation', 'organisationId');
        $this->hasOne('userId', 'User', 'userId');
    }

    public function getUser($options = null)
    {
        return $this->getRelated('User', $options);
    }

    public function getOrganisation($options = null)
    {
        return $this->getRelated('Organisation', $options);
    }
} 