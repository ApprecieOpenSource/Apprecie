<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/12/14
 * Time: 13:07
 */
class Interest extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $interest;
    protected $interestId;
    protected $isTop;

    public function getSource()
    {
        return 'interests';
    }

    public function Initialize()
    {
        $this->setDefaultFields('isTop');
        $this->hasMany('interestId', 'InterestLink', 'interestId', ['alias' => 'toparents', 'reusable' => true]);
        $this->hasMany('interestId', 'InterestLink', 'parentInterestId', ['alias' => 'tochildren', 'reusable' => true]);
        $this->hasManyToMany(
            'interestId',
            'InterestLink',
            'interestId',
            'parentInterestId',
            'Interest',
            'interestId',
            ['alias' => 'parentinterests', 'reusable' => true]
        );
        $this->hasManyToMany(
            'interestId',
            'InterestLink',
            'parentInterestId',
            'interestId',
            'Interest',
            'interestId',
            ['alias' => 'childinterests', 'reusable' => true]
        );
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
    }

    public function getParentLinks($options = null)
    {
        return $this->getRelated('toparents', $options);
    }

    public function getChildrenLinks($options = null)
    {
        return $this->getRelated('tochildren', $options);
    }

    /**
     * if drilling through, do not getChildren for any child that is marked as isTop and is in a child position
     * else you are going to loop forever.  i.e  if(child and isTop)  then STOP
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getChildren($options = null)
    {
        return $this->getRelated('childinterests', $options);
    }

    public function getParents($options = null)
    {
        return $this->getRelated('parentinterests', $options);
    }

    /**
     * @param mixed $interest
     */
    public function setInterest($interest)
    {
        $this->interest = $interest;
    }

    /**
     * @return mixed
     */
    public function getInterest()
    {
        return $this->interest;
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
     * @param mixed $isTop
     */
    public function setIsTop($isTop)
    {
        $this->isTop = $isTop;
    }

    /**
     * @return mixed
     */
    public function getIsTop()
    {
        return $this->isTop;
    }

    public static function getTopLevel()
    {
        return Interest::find('isTop = 1');
    }

    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        $interest = null;

        if (is_string($param) && !is_numeric($param)) {
            $interest = Interest::findFirstBy('interest', $param, $instance);
            if ($interest == null) {
                throw new \Phalcon\Exception('It was not possible to resolve the string ' . $param . 'to an interest name');
            }
        } else {
            $interest = Parent::resolve($param, $throw, $instance);
        }

        return $interest;
    }

    public function delete()
    {
        throw new \Exception('No way,  consider yourself told No!');
    }
} 