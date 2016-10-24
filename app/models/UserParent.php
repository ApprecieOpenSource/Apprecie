<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/11/14
 * Time: 15:42
 */
class UserParent extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $parentId, $childId, $ignoredForVisibility;

    /**
     * @param mixed $childId
     */
    public function setChildId($childId)
    {
        $this->childId = $childId;
    }

    /**
     * @return mixed
     */
    public function getChildId()
    {
        return $this->childId;
    }

    /**
     * @param mixed $ignoredForVisibility
     */
    public function setIgnoredForVisibility($ignoredForVisibility)
    {
        $this->ignoredForVisibility = $ignoredForVisibility;
    }

    /**
     * @return mixed
     */
    public function getIgnoredForVisibility()
    {
        return $this->ignoredForVisibility;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    public function getSource()
    {
        return 'userparents';
    }

    public function initialize()
    {
        $this->hasOne('childId', 'User', 'userId', ['alias' => 'parent']);
        $this->hasOne('parentId', 'User', 'userId', ['alias' => 'child']);
    }

    public function getParent($options = null)
    {
        return $this->getRelated('parent', $options);
    }

    public function getChild($options = null)
    {
        return $this->getRelated('child', $options);
    }
} 