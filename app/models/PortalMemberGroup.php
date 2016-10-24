<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 15/12/14
 * Time: 09:44
 */
class PortalMemberGroup extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $groupId, $portalId, $ownerId, $groupname;

    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $groupname
     */
    public function setGroupName($groupname)
    {
        $this->groupname = $groupname;
    }

    /**
     * @return mixed
     */
    public function getGroupName()
    {
        return $this->groupname;
    }

    /**
     * @param mixed $ownerId
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    /**
     * @return mixed
     */
    public function getOwnerId()
    {
        return $this->ownerId;
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
        return 'portalmembergroups';
    }

    public function initialize()
    {
        $this->hasOne('ownerId', 'User', 'userId', ['alias' => 'owner', 'reusable' => true]);
        $this->hasOne('portalId', 'Portal', 'portalId', ['reusable' => true]);
        $this->hasMany('groupId', 'PortalMembersInGroups', 'groupId', ['reusable' => true]);
        $this->hasManyToMany(
            'groupId',
            'PortalMembersInGroups',
            'groupId',
            'userId',
            'User',
            'userId',
            ['alias' => 'members', 'reusable' => true]
        );
    }

    public function getOwner($options = null)
    {
        return $this->getRelated('owner', $options);
    }

    public function getMembers($options = null)
    {
        return $this->getRelated('members', $options);
    }

    public function setOwner($user)
    {
        $user = User::resolve($user);

        $this->setOwnerId($user->getUserId());
    }

    public function getPortal($options = null)
    {
        return $this->getRelated('Portal', $options);
    }

    public function getMemberLinks($options = null)
    {
        return $this->getRelated('PortalMembersInGroups', $options);
    }

    public function removeUser($user)
    {
        if (is_array($user) || $user instanceof \ArrayAccess) {
            foreach ($user as $element) {
                if (!$this->removeUser($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $user = User::resolve($user);
        }

        $links = $this->getMemberLinks();

        foreach ($links as $link) {
            if ($link->getUserId() == $user->getUserId()) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }

                break;
            }
        }

        return true;
    }

    public function hasUser($user)
    {
        $user = User::resolve($user);

        $links = $this->getMemberLinks();

        foreach ($links as $link) {
            if ($link->getUserId() == $user->getUserId()) {
                return true;
            }
        }

        return false;
    }

    public function addUser($user, $clearExisting = false)
    {
        if ($clearExisting) {
            $links = $this->getMemberLinks();

            foreach ($links as $link) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }
            }
        }

        if (is_array($user) || $user instanceof \ArrayAccess) {
            foreach ($user as $element) {
                if (!$this->addUser($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $user = User::resolve($user);
        }

        //check if already exists if not just cleared all
        if (!$clearExisting) {
            $userExists = PortalMembersInGroups::find(
                    "userId = {$user->getUserId()} AND groupId = {$this->getGroupId()}"
                )->count() > 0;

            if ($userExists) {
                return true;
            } //just indicate a positive result if requirement already set.
        }

        $userLink = new PortalMembersInGroups();
        $userLink->setUserId($user->getUserId());
        $userLink->setGroupId($this->getGroupId());

        if (!$userLink->create()) {
            $this->appendMessageEx($userLink->getMessages());
            return false;
        }

        return true;
    }

    public static function findByOwner($owner)
    {
        $owner = User::resolve($owner);

        return PortalMemberGroup::findFirstBy('ownerId', $owner->getUserId());
    }

    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        $group = null;

        if (is_string($param) && !is_numeric($param)) {
            $group = PortalMemberGroup::findFirstBy('groupname', $param, $instance);
            if ($group == null) {
                throw new \Phalcon\Exception('It was not possible to resolve the string ' . $param . 'to a group');
            }
        } elseif (is_int($param)) {
            $group = PortalMemberGroup::findFirstBy('groupId', $param, $instance);
            if ($group == null) {
                throw new \Phalcon\Exception('It was not possible to resolve the number ' . $param . 'to a group');
            }
        }
        else {
            $group = Parent::resolve($param, $throw, $instance);
        }

        return $group;
    }
} 