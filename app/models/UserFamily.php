<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 11/12/14
 * Time: 13:50
 */
class UserFamily extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $userId, $relatedUserId;

    /**
     * @param mixed $relatedUserId
     */
    public function setRelatedUserId($relatedUserId)
    {
        $this->relatedUserId = $relatedUserId;
    }

    /**
     * @return mixed
     */
    public function getRelatedUserId()
    {
        return $this->relatedUserId;
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
        return 'userfamily';
    }

    public function initialize()
    {
        $this->hasOne('userId', 'User', 'userId', ['alias' => 'firstuser']);
        $this->hasOne('userId', 'User', 'userId', ['alias' => 'relateduser']);
    }

    /**
     * @return User
     */
    public function getFirstUser($options = null)
    {
        return $this->getRelated('firstuser', $options);
    }

    public function getRelatedUser($options = null)
    {
        return $this->getRelated('relateduser', $options);
    }
} 