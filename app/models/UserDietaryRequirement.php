<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 12/11/14
 * Time: 15:11
 */
class UserDietaryRequirement extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $userId, $requirementId;

    /**
     * @param mixed $requirementId
     */
    public function setRequirementId($requirementId)
    {
        $this->requirementId = $requirementId;
    }

    /**
     * @return mixed
     */
    public function getRequirementId()
    {
        return $this->requirementId;
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
        return "userdietaryrequirements";
    }

    public function initialize()
    {
        $this->useDynamicUpdate(true);
        $this->hasMany('userId', 'User', 'userId');
        $this->hasMany('requirementId', 'DietaryRequirement', 'requirementId');
        $this->belongsTo('userId', 'User', 'userId');
        $this->belongsTo('requirementId', 'DietaryRequirement', 'requirementId');
    }

    public function getDietaryRequirement($options = null)
    {
        return $this->getRelated('DietaryRequirement', $options);
    }

    public function getUser($options = null)
    {
        return $this->getRelated('User', $options);
    }
} 