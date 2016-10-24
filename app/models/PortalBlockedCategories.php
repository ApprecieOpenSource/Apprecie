<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 10/12/14
 * Time: 21:44
 */
class PortalBlockedCategories extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $portalId, $interestId;

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
     * @param mixed $portalId
     */
    public function setPortalId($portalId)
    {
        $this->portalId = $portalId;
    }

    /**
     * @return mixed
     */
    public function getPortalId()
    {
        return $this->portalId;
    }

    public function getSource()
    {
        return 'portalblockedcategories';
    }

    public function initialize()
    {
        $this->hasOne('portalId', 'Portal', 'portalId');
        $this->hasOne('interestId', 'Interest', 'interestId');
    }

    /**
     * @return Interest
     */
    public function getCategory($options = null)
    {
        return $this->getRelated('Interest', $options);
    }

    /**
     * @return Portal
     */
    public function getPortal($options = null)
    {
        return $this->getRelated('Portal', $options);
    }
} 