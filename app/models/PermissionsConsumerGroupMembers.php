<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/01/2016
 * Time: 13:11
 */

use Apprecie\Library\Model\ApprecieModelBase;

class PermissionsConsumerGroupMembers extends ApprecieModelBase
{
    protected $consumerGroupId, $userId;

    /**
     * @return mixed
     */
    public function getConsumerGroupId()
    {
        return $this->consumerGroupId;
    }

    /**
     * @param mixed $consumerGroupId
     */
    public function setConsumerGroupId($consumerGroupId)
    {
        $this->consumerGroupId = $consumerGroupId;
    }

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


    public function getSource()
    {
        return 'permissionsconsumergroupmembers';
    }
}