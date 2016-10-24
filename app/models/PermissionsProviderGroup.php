<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/01/2016
 * Time: 13:22
 */

use Apprecie\Library\Acl\AccessControl;
use Apprecie\Library\Model\ApprecieModelBase;

class PermissionsProviderGroup extends ApprecieModelBase
{
    protected $providerGroupId , $portalId, $providerGroupName, $providerGroupDescription;

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
    public function getProviderGroupName()
    {
        return $this->providerGroupName;
    }

    /**
     * @param mixed $providerGroupName
     */
    public function setProviderGroupName($providerGroupName)
    {
        $this->providerGroupName = $providerGroupName;
    }

    /**
     * @return mixed
     */
    public function getProviderGroupDescription()
    {
        return $this->providerGroupDescription;
    }

    /**
     * @param mixed $providerGroupDescription
     */
    public function setProviderGroupDescription($providerGroupDescription)
    {
        $this->providerGroupDescription = $providerGroupDescription;
    }


    public function getSource()
    {
        return 'permissionsprovidergroups';
    }

    public function addUsers($user)
    {
        if(! is_array($user)) {
            $user = [$user];
        }

        $resolvedUsers = [];

        foreach($user as $u) {
            $resolvedUsers[] = \User::resolve($u);
        }

        $allowed = AccessControl::userCanManageUser($this->getAuthenticatedUser(), $resolvedUsers, null);

        if(! $allowed) {
            $this->appendMessageEx('You do not have permission to manage all of the users to be added to the group');
            return false;
        }

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        try {
            foreach ($resolvedUsers as $u) {
                $link = new PermissionsProviderGroupMembers();
                $link->setProviderUserId($u->getUserId());
                $link->setProviderGroupId($this->getProviderGroupId());
                $link->setTransaction($transaction);

                if (!$link->save()) {
                    $transaction->rollback('It was not possible to update group members ' . _ms($link->getMessages()));
                }
            }

            if(! $transaction->commit()) {
                throw new Exception('Failed to commit transaction');
            }

        } catch (\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return true;
    }

    public function removeUsers($user)
    {
        if(! is_array($user)) {
            $user = [$user];
        }

        $resolvedUsers = [];

        foreach($user as $u) {
            $resolvedUsers[] = \User::resolve($u);
        }

        $allowed = AccessControl::userCanManageUser($this->getAuthenticatedUser(), $resolvedUsers, null);

        if(! $allowed) {
            $this->appendMessageEx('You do not have permission to manage all of the users to be added to the group');
            return false;
        }

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        try {
            foreach ($resolvedUsers as $u) {
                $link = new PermissionsProviderGroupMembers();
                $link->setProviderUserId($u->getUserId());
                $link->setProviderGroupId($this->getProviderGroupId());
                $link->setTransaction($transaction);

                if (!$link->delete()) {
                    $transaction->rollback('It was not possible to remove all group members ' . _ms($link->getMessages()));
                }
            }

            if(! $transaction->commit()) {
                throw new \Exception('Failed to commit transaction');
            }

        } catch (\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return true;
    }

    public function getMembers($options = null)
    {
        return $this->getRelated('members', $options);
    }

    public function initialize()
    {
        $this->hasManyToMany(
            'providerGroupId',
            'PermissionsProviderGroupMembers',
            'providerGroupId',
            'providerUserId',
            'User',
            'userId',
            ['alias' => 'members', 'reusable' => true]
        );
    }

    public function subscribeConsumerGroups($consumerGroups)
    {
        if(! is_array($consumerGroups)) {
            $consumerGroups = [$consumerGroups];
        }

        $resolvedGroups = [];

        foreach($consumerGroups as $u) {
            $resolvedGroups[] = \PermissionsConsumerGroup::resolve($u);
        }

        $members = $this->getMembers()->toArray();

        foreach($resolvedGroups as $group) {
            $members[] = $group->getMembers();
        }

        $allowed = AccessControl::userCanManageUser($this->getAuthenticatedUser(), $members, null);

        if(! $allowed) {
            $this->appendMessageEx('Affected groups contain one or more users that you cannot manage');
            return false;
        }

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        try {
            foreach ($resolvedGroups as $g) {
                $link = new PermissionsProvidersXConsumers();
                $link->setConsumerGroupId($g->getConsumerGroupId());
                $link->setProviderGroupId($this->getProviderGroupId());
                $link->setTransaction($transaction);

                if (!$link->save()) {
                    $transaction->rollback('It was not possible to subscribe all groups.  All intermediate actions have been reversed. ' . _ms($link->getMessages()));
                }
            }

            if(! $transaction->commit()) {
                throw new Exception('Failed to commit transaction');
            }

        } catch (\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return true;
    }

    public function unSubscribeConsumerGroups($consumerGroups)
    {
        if(! is_array($consumerGroups)) {
            $consumerGroups = [$consumerGroups];
        }

        $resolvedGroups = [];

        foreach($consumerGroups as $u) {
            $resolvedGroups[] = \PermissionsConsumerGroup::resolve($u);
        }

        $members = $this->getMembers()->toArray();

        foreach($resolvedGroups as $group) {
            $members[] = $group->getMembers();
        }

        $allowed = AccessControl::userCanManageUser($this->getAuthenticatedUser(), $members, null);

        if(! $allowed) {
            $this->appendMessageEx('Affected groups contain one or more users that you cannot manage');
            return false;
        }

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        try {
            foreach ($resolvedGroups as $g) {
                $link = new PermissionsProvidersXConsumers();
                $link->setConsumerGroupId($g->getConsumerGroupId());
                $link->setProviderGroupId($this->getProviderGroupId());
                $link->setTransaction($transaction);

                if (!$link->delete()) {
                    $transaction->rollback('It was not possible to subscribe all groups.  All intermediate actions have been reversed. ' . _ms($link->getMessages()));
                }
            }

            if(! $transaction->commit()) {
                throw new Exception('Failed to commit transaction');
            }

        } catch (\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return true;
    }
}