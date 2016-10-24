<?php

class DietaryRequirement extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $requirementId, $requirement;

    /**
     * @param mixed $requirement
     */
    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;
    }

    /**
     * @return mixed
     */
    public function getRequirement()
    {
        return $this->requirement;
    }

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

    public function getSource()
    {
        return 'dietaryrequirements';
    }

    public function initialize()
    {
        $this->hasMany('requirementId', 'UserDietaryRequirement', 'requirementId', ['reusable' => true]);
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
    }
} 