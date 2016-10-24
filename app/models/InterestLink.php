<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/12/14
 * Time: 13:10
 */
class InterestLink extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $interestId;
    protected $parentInterestId;

    public function getSource()
    {
        return 'intereststree';
    }

    public function initialize()
    {
        $this->hasOne('interestId', 'Interest', 'interestId', ['alias' => 'child']);
        $this->hasOne('parentInterestId', 'Interest', 'interestId', ['alias' => 'parent']);
    }

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
     * @param mixed $parentInterestId
     */
    public function setParentInterestId($parentInterestId)
    {
        $this->parentInterestId = $parentInterestId;
    }

    /**
     * @return mixed
     */
    public function getParentInterestId()
    {
        return $this->parentInterestId;
    }

    public function getParentInterest($options = null)
    {
        return $this->getRelated('parent', $options);
    }

    public function getChildInterest($options = null)
    {
        return $this->getRelated('child', $options);
    }
} 