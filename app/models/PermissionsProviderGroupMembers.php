<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/01/2016
 * Time: 13:19
 */

use Apprecie\Library\Model\ApprecieModelBase;

class PermissionsProviderGroupMembers extends ApprecieModelBase
{
    protected $providerGroupId, $providerUserId;

    /**
     * @return mixed
     */
    public function getProviderGroupId()
    {
        return $this->providerGroupId;
    }

    /**
     * @param mixed $providerGroupId
     */
    public function setProviderGroupId($providerGroupId)
    {
        $this->providerGroupId = $providerGroupId;
    }

    /**
     * @return mixed
     */
    public function getProviderUserId()
    {
        return $this->providerUserId;
    }

    /**
     * @param mixed $providerUserId
     */
    public function setProviderUserId($providerUserId)
    {
        $this->providerUserId = $providerUserId;
    }


    public function getSource()
    {
        return 'permissionsprovidergroupmembers';
    }
}