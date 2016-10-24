<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/01/2016
 * Time: 14:35
 */

use Apprecie\Library\Model\ApprecieModelBase;

class PermissionsProvidersXConsumers extends ApprecieModelBase
{
    protected $providerGroupId, $consumerGroupId;

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
    public function getConsumerGroupId()
    {
        return $this->consumerGroupId;
    }

    /**
     * @param mixed $consumerGroupId
     */
    public function setConsumerGroupId($consumerGroupId)
    {
        $this->consumerGroupId = $consumerGroupId;
    }

    public function getSource()
    {
        return 'permissionsprovidersxconsumers';
    }
}