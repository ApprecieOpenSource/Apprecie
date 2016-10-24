<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 30/01/15
 * Time: 09:52
 */
class OrganisationParents extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $organisationId, $parentId;

    /**
     * @param mixed $organisationId
     */
    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
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
        return 'organisationparents';
    }

    public function initialize()
    {
        $this->hasOne('parentId', 'organisation', 'parentId', ['alias' => 'parent']);
        $this->hasOne('organisationId', 'organisation', 'organisationId', ['alias' => 'child']);
    }
} 