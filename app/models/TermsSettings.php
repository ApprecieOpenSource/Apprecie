<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 28/09/2015
 * Time: 10:52
 */
//@todo  GH  this cannot work as expected as the extended model is hidden by this models source
//note the Phalcon ORM does not support inheritance but the ApprecieModelBase can makes its parent sync CRUD
//by ensuring that $this->setParentIsTableBase(true);  in onConstruct()
class TermsSettings extends Terms
{
    protected $termsSettingsId, $termsId, $roleId, $portalId, $isRsvp, $isPublic;

    /**
     * @return mixed
     */
    public function getIsRsvp()
    {
        return $this->isRsvp;
    }

    /**
     * @param mixed $isRsvp
     */
    public function setIsRsvp($isRsvp)
    {
        $this->isRsvp = $isRsvp;
    }

    /**
     * @return mixed
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param mixed $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @return mixed
     */
    public function getTermsSettingsId()
    {
        return $this->termsSettingsId;
    }

    /**
     * @param mixed $termsSettingsId
     */
    public function setTermsSettingsId($termsSettingsId)
    {
        $this->termsSettingsId = $termsSettingsId;
    }

    /**
     * @return mixed
     */
    public function getPortalId()
    {
        return $this->portalId;
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
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @param mixed $roleId
     */
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;
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

    public function getSource()
    {
        return 'termssettings';
    }

    public function initialize()
    {
        $this->belongsTo('termsId', 'terms', 'termsId');
        $this->belongsTo('roleId', 'roles', 'roleId');
        $this->belongsTo('portalId', 'portals', 'portalId');
    }

    public function onConstruct()
    {
        $this->setDefaultFields(['portalId', 'roleId', 'isRsvp', 'isPublic']);
    }
}