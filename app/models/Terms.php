<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 25/09/2015
 * Time: 14:51
 */
class Terms extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $termsId, $version, $creationDate, $defaultName, $defaultContent, $state;

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getDefaultName()
    {
        return $this->defaultName;
    }

    /**
     * @param mixed $defaultName
     */
    public function setDefaultName($defaultName)
    {
        $this->defaultName = $defaultName;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getTermsId()
    {
        return $this->termsId;
    }

    /**
     * @param mixed $termsId
     */
    public function setTermsId($termsId)
    {
        $this->termsId = $termsId;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getDefaultContent()
    {
        return $this->defaultContent;
    }

    /**
     * @param mixed $defaultContent
     */
    public function setDefaultContent($defaultContent)
    {
        $this->defaultContent = $defaultContent;
    }

    public function getSource()
    {
        return 'terms';
    }

    public function initialize()
    {
        $this->hasMany('termsId', 'termssettings', 'termsId');
        $this->hasMany('termsId', 'userterms', 'termsId');
    }

    public function onConstruct()
    {
        $this->setDefaultFields(['creationDate', 'state']);
    }
}