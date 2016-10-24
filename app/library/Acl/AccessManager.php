<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/02/2016
 * Time: 16:30
 */

namespace Apprecie\Library\Acl;

use Apprecie\Library\Http\Client\Exception;
use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Model\FindOptionsHelper;
use Apprecie\Library\Provisioning\PortalStrap;

class AccessManager extends PrivateMessageQueue
{
    /**
     * @param string $name A descriptive name for the group up to 45 characters
     * @param string $description A longer description describing the purpose of the group
     * @param null|\Portal $portal If null will create the group on the active portal
     */
    public function createProviderGroup($name, $description, $portal = null)
    {
        if($portal == null) {
            $portal = PortalStrap::getActivePortal();
        } else {
            $portal = \Portal::resolve($portal);
        }


        $group = new \PermissionsProviderGroup();
        $group->setPortalId($portal->getPortalId());
        $group->setProviderGroupDescription($description);
        $group->setProviderGroupName($name);

        try {
            if (!$group->create()) {
                $this->appendMessageEx($group);
                return false;
            }
        } catch(\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return $group;
    }


    public function createConsumerGroup($name, $description, $portal = null)
    {
        if($portal == null) {
            $portal = PortalStrap::getActivePortal();
        } else {
            $portal = \Portal::resolve($portal);
        }

        $group = new \PermissionsConsumerGroup();
        $group->setPortalId($portal->getPortalId());
        $group->setConsumerGroupDescription($description);
        $group->setConsumerGroupName($name);

        try {
            if (!$group->create()) {
                $this->appendMessageEx($group);
                return false;
            }
        } catch(\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return $group;
    }

    /**
     * @param $subjectUser
     * @param $securityIdent
     */
    public function permissionGrants($subjectUserId, $securityIdent)
    {
        $options = FindOptionsHelper::prepareFindOptions(null, null, null, 'userId=?1 and ident = ?2', [1=>$subjectUserId, 2=>$securityIdent]);
        $grants = \ProvidedPermissions::find($options);

        $providerIds = [];

        foreach($grants as $grant) {
            $providerIds[] = $grant->getProviderUserId();
        }

        return count($providerIds) == 0 ? false : $providerIds;
    }
}