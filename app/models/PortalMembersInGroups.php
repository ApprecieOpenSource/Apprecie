<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 15/12/14
 * Time: 10:46
 */
class PortalMembersInGroups extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $userId, $groupId;

    /**
     * @param mixed $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
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
        return 'portalmembersingroups';
    }

    public function initialize()
    {
        $this->belongsTo('groupId', 'PortalMemberGroup', 'groupId');
        $this->belongsTo('userId', 'User', 'userId');
    }

    public function getUser($options = null)
    {
        return $this->getRelated('User', $options);
    }

    public function getGroup($options = null)
    {
        return $this->getRelated('PortalMemberGroup', $options);
    }
} 