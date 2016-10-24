<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 19/10/2015
 * Time: 18:34
 */

use Apprecie\Library\Model\ApprecieModelBase;

class ItemNotification extends ApprecieModelBase
{
    protected $itemNotificationId, $itemId, $userId, $datetime, $isSent;

    /**
     * @return mixed
     */
    public function getItemNotificationId()
    {
        return $this->itemNotificationId;
    }

    /**
     * @param mixed $itemNotificationId
     */
    public function setItemNotificationId($itemNotificationId)
    {
        $this->itemNotificationId = $itemNotificationId;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
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

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
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
    public function getIsSent()
    {
        return $this->isSent;
    }

    /**
     * @param mixed $isSent
     */
    public function setIsSent($isSent)
    {
        $this->isSent = $isSent;
    }

    public function getSource()
    {
        return 'itemnotifications';
    }

    public function initialize()
    {
        $this->hasOne('userId', 'users', 'userId');
        $this->hasOne('itemId', 'items', 'itemId');
    }

    public function onConstruct()
    {
        $this->setDefaultFields(['datetime', 'isSent']);
    }
}