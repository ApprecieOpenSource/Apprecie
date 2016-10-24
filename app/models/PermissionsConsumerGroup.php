<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/01/2016
 * Time: 13:16
 */


use Apprecie\Library\Acl\AccessControl;
use Apprecie\Library\Http\Client\Exception;
use Apprecie\Library\Model\ApprecieModelBase;

class PermissionsConsumerGroup extends ApprecieModelBase
{
    protected $consumerGroupId, $portalId, $consumerGroupName, $consumerGroupDescription;

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
    public function getConsumerGroupName()
    {
        return $this->consumerGroupName;
    }

    /**
     * @param mixed $consumerGroupName
     */
    public function setConsumerGroupName($consumerGroupName)
    {
        $this->consumerGroupName = $consumerGroupName;
    }

    /**
     * @return mixed
     */
    public function getConsumerGroupDescription()
    {
        return $this->consumerGroupDescription;
    }

    /**
     * @param mixed $consumerGroupDescription
     */
    public function setConsumerGroupDescription($consumerGroupDescription)
    {
        $this->consumerGroupDescription = $consumerGroupDescription;
    }

    public function getSource()
    {
        return 'permissionsconsumergroups';
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
                $link = new PermissionsConsumerGroupMembers();
                $link->setUserId($u->getUserId());
                $link->setConsumerGroupId($this->getConsumerGroupId());
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
                $link = new PermissionsConsumerGroupMembers();
                $link->setUserId($u->getUserId());
                $link->setConsumerGroupId($this->getConsumerGroupId());
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
            'consumerGroupId',
            'PermissionsConsumerGroupMembers',
            'consumerGroupId',
            'userId',
            'User',
            'userId',
            ['alias' => 'members', 'reusable' => true]
        );
    }
}