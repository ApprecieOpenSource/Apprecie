<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/12/14
 * Time: 19:36
 */
class UserInterest extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $userId, $interestId, $userIndicated, $managerIndicated, $systemIndicated;

    /**
     * @param mixed $interestId
     */
    public function setInterestId($interestId)
    {
        $this->interestId = $interestId;
    }

    /**
     * @return mixed
     */
    public function getInterestId()
    {
        return $this->interestId;
    }

    /**
     * @param mixed $managerIndicated
     */
    public function setManagerIndicated($managerIndicated)
    {
        $this->managerIndicated = $managerIndicated;
    }

    /**
     * @return mixed
     */
    public function getManagerIndicated()
    {
        return $this->managerIndicated;
    }

    /**
     * @param mixed $systemIndicated
     */
    public function setSystemIndicated($systemIndicated)
    {
        $this->systemIndicated = $systemIndicated;
    }

    /**
     * @return mixed
     */
    public function getSystemIndicated()
    {
        return $this->systemIndicated;
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
     * @param mixed $userIndicated
     */
    public function setUserIndicated($userIndicated)
    {
        $this->userIndicated = $userIndicated;
    }

    /**
     * @return mixed
     */
    public function getUserIndicated()
    {
        return $this->userIndicated;
    }

    public function getSource()
    {
        return 'userinterests';
    }

    public function initialize()
    {
        $this->hasOne('userId', 'User', 'userId');
        $this->hasOne('interestId', 'Interest', 'interestId');
    }

    public function getUser($options = null)
    {
        return $this->getRelated('User', $options);
    }

    public function getInterest($options = null)
    {
        return $this->getRelated('Interest', $options);
    }
} 