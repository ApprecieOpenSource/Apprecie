<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/02/16
 * Time: 22:35
 */

class ProvidedPermissions extends \Apprecie\Library\Model\ApprecieModelBase
{
    protected $userId, $providerUserId, $ident;

    /**
     * @param mixed $ident
     */
    public function setIdent($ident)
    {
        $this->ident = $ident;
    }

    /**
     * @return mixed
     */
    public function getIdent()
    {
        return $this->ident;
    }

    /**
     * @param mixed $providerUserId
     */
    public function setProviderUserId($providerUserId)
    {
        $this->providerUserId = $providerUserId;
    }

    /**
     * @return mixed
     */
    public function getProviderUserId()
    {
        return $this->providerUserId;
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
        return 'providedpermissions';
    }
} 