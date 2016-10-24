<?php

/**
 * Users in the Apprecie system are comprised of 4 separate entities.
 * One of these entities the User entity, sits in the global database accessible by all portals but contains no
 * personal data.  This entity contains a guid, a userid,  and details of the portal that the user lives in.
 *
 * All personal data, and by virtue the further 3 user entities are stored under a per portal table isolation scheme.
 *
 * The connection to these private tables is handled automatically, each is prefixed with the portal guid, and the
 * models are injected with this source on hydration.  This means that is is impossible for one portal to access
 * another portals users unless tis si a very deliberate act by the programmer.
 *
 * The structure of the user entities is as follows :
 *
 *                       User
 *          ------------------------------isolated tables below this line-------------
 *                     PortalUser
 *              UserLogin   UserProfile
 *
 * In most cases you will want a PortalUser rather than a user, and as all 4 entities support the ApprecieUser interface
 * navigating between these entities is easy and uniform.
 *
 * User are created top down.  So when a user attempts to login (see Security) they login to a specific portal,
 * as the UserLogin entity is in the private portal tables.  This happens transparently, based on the portal (domain).
 *
 * You can then obtain the global user record using ->getUser() so you move from private to global,  not global to private
 *
 * This prevents the mixing of users from different portals, with one exception.
 *
 * As the developer you are able to use the UserEx::ForceActivePortalForUserQueries() passing the name of the portal
 * that you need to query, from that moment until you call UserEx::ForceActivePortalForUserQueries() with no portal
 * defined (resets to active portal), your user objects will be sourced on the portal that you indicated.
 *
 * <code>
 * \UserEx::ForceActivePortalForUserQueries('myportal');
 * $myportalUsers = \PortalUser::find(); //returns users from myportal
 * \UserEx::ForceActivePortalForUserQueries('');
 * $activePortalUsers = \PortalUser::find(); //returns users from the active portal
 * </code>
 *
 * As soon as you are outside of the force query block, your user objects will no longer relate correctly
 * but will retain already hydrated fields.  So all off portal user operations should occur in such as very
 * explicit block.
 *
 * To login a user or interact with the logged in user see Security.
 *
 * Class User
 */
class User extends \Apprecie\Library\Users\ApprecieUserBase
{
    protected $_activeRole = null;
    protected $_roles = null;
    protected $_interests = null;
    protected $userId, $portalId, $creatingUser, $portalUserId, $userGUID, $creationDate, $status, $isDeleted, $tier, $organisationId;
    protected $_dietaryRequirements = null;

    /**
     * @param mixed $creatingUser
     */
    public function setCreatingUser($creatingUser)
    {
        $this->creatingUser = $creatingUser;
    }

    /**
     * @return mixed
     */
    public function getCreatingUser()
    {
        return $this->creatingUser;
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
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return mixed
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
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

    /**
     * @param mixed $portalUserId
     */
    public function setPortalUserId($portalUserId)
    {
        $this->portalUserId = $portalUserId;
    }

    /**
     * @return mixed
     */
    public function getPortalUserId()
    {
        return $this->portalUserId;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function deactivate($externalTransaction = null)
    {
        if ($this->getStatus() != \Apprecie\Library\Users\UserStatus::ACTIVE) {
            $this->appendMessageEx('This user is already marked as deactivated');
            return false;
        }

        $userEx = new \Apprecie\Library\Users\UserEx();

        if (!$userEx->canDeleteOrDeactivate($this)) {
            $this->appendMessageEx($userEx);
            return false;
        }

        if ($externalTransaction) {
            $transaction = $externalTransaction;
        } else {
            $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
            $transaction = $manager->get();
        }

        $this->releaseQuota($transaction);

        $this->setTransaction($transaction);
        $this->setStatus(\Apprecie\Library\Users\UserStatus::DEACTIVATED);
        $this->update();

        $oldPortal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($this->getPortal());
        $login = $this->getUserLogin();
        $login->setTransaction($transaction);
        $login->setSuspended(true);
        if (!$login->update()) {
            $this->appendMessageEx($login);
        }
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($oldPortal);

        if (!$externalTransaction) {
            try {
                if ($this->hasMessages()) {
                    $transaction->rollback();
                } else {
                    $transaction->commit();
                }
            } catch (\Exception $ex) {
                $this->appendMessageEx($ex);
            }
        }

        return !$this->hasMessages();
    }

    public function releaseQuota($transaction = null)
    {
        $oldPortal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($this->getPortal());
        $portalUser = $this->getPortalUser();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($oldPortal);

        if ($this->getStatus() === \Apprecie\Library\Users\UserStatus::PENDING && !$portalUser->getRegistrationHash()) {
            $this->appendMessageEx('This user does not have portal access.');
            return false;
        }

        $quotas = $this->getOrganisation()->getQuotas();

        if ($transaction) {
            $quotas->setTransaction($transaction);
        }

        foreach ($this->getRoles() as $role) {
            switch ($role->getRole()->getName()) {
                case 'PortalAdministrator' : // Organisation Owner
                {
                    $quotas->consumePortalAdministratorQuota(-1);
                    break;
                }
                case 'Manager' :
                {
                    $quotas->consumeManagerQuota(-1);
                    break;
                }
                case 'ApprecieSupplier' :
                {
                    $quotas->consumeApprecieSupplierQuota(-1);
                    break;
                }
                case 'AffiliateSupplier':
                {
                    $quotas->consumeAffiliateSupplierQuota(-1);
                    break;
                }
                case 'Internal' :
                {
                    $quotas->consumeInternalMemberQuota(-1);
                    break;
                }
                case 'Client' :
                { //@todo discern family member / client member
                    $quotas->consumeMemberQuota(-1);
                    break;
                }
            }
        }

        if (!$quotas->update()) {
            $this->appendMessageEx($quotas);
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $tier
     */
    public function setTier($tier)
    {
        $this->tier = $tier;
    }

    /**
     * @return mixed
     */
    public function getTier()
    {
        return $this->tier;
    }

    /**
     * @param mixed $userGUID
     */
    public function setUserGUID($userGUID)
    {
        $this->userGUID = $userGUID;
    }

    /**
     * @return mixed
     */
    public function getUserGUID()
    {
        return $this->userGUID;
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

    /**
     * Clears static (singleton) role data,  so next to call to role methods will make a database call / refresh
     */
    public function clearStaticRoleData()
    {
        $this->_activeRole = $this->_roles = $this->_interests = null;
    }

    public function getSource()
    {
        return 'users';
    }

    public function onConstruct()
    {
        $this->setDefaultFields('creationDate');
        parent::onConstruct();
    }

    public function initialize()
    {
        $this->hasOne('portalUserId', 'PortalUser', 'portalUserId');
        $this->hasOne('userId', 'UserContactPreferences', 'userId', ['reusable' => true]);
        $this->hasMany('userId', 'UserRole', 'userId', ['reusable' => true]);
        $this->hasMany('userId', 'UserDietaryRequirement', 'userId', ['reusable' => true]);
        $this->hasMany('userId', 'UserNote', 'noteCreatorUserId', ['reusable' => true]);
        $this->hasOne('creatingUser', 'User', 'userId', ['alias' => 'Creator', 'reusable' => true]);
        $this->hasManyToMany(
            'userId',
            'UserParent',
            'childId',
            'parentId',
            'User',
            'userId',
            ['alias' => 'Parents', 'reusable' => true]
        );
        $this->hasManyToMany(
            'userId',
            'UserParent',
            'parentId',
            'childId',
            'User',
            'userId',
            ['alias' => 'Children', 'reusable' => true]
        );
        $this->hasMany('userId', 'UserNotification', 'userId', ['reusable' => true]);
        $this->hasMany('userId', 'UserInterest', 'userId', ['reusable' => true]);
        $this->hasManyToMany(
            'userId',
            'UserInterest',
            'userId',
            'interestId',
            'Interest',
            'interestId',
            ['alias' => 'interests', 'reusable' => true]
        );
        $this->hasOne('portalId', 'Portal', 'portalId', ['reusable' => true]);
        $this->hasMany('userId', 'UserFamily', 'userId', ['reusable' => true]);
        $this->hasManyToMany(
            'userId',
            'UserFamily',
            'userId',
            'relatedUserId',
            'User',
            'userId',
            ['alias' => 'familyMembers', 'reusable' => true]
        );
        $this->hasManyToMany(
            'userId',
            'UserFamily',
            'relatedUserId',
            'userId',
            'User',
            'userId',
            ['alias' => 'familyOf', 'reusable' => true]
        );
        $this->hasManyToMany(
            'userId',
            'PortalMembersInGroups',
            'userId',
            'groupId',
            'PortalMemberGroup',
            'groupId',
            ['alias' => 'groups', 'reusable' => true]
        );
        $this->hasManyToMany(
            'userId',
            'PortalMembersInGroups',
            'ownerId',
            'groupId',
            'PortalMemberGroup',
            'groupId',
            ['alias' => 'groupsOwner', 'reusable' => true]
        );
        $this->hasOne('organisationId', 'organisation', 'organisationId', ['reusable' => true]);
        $this->hasMany('userId', 'OrganisationManagementPermissions', 'userId', ['reusable' => true]);
        $this->hasManyToMany(
            'userId',
            'OrganisationManagementPermissions',
            'userId',
            'organisationId',
            'Organisation',
            'organisationId',
            ['alias' => 'manages', 'reusable' => true]
        );
        $this->hasMany('userId', 'Message', 'targetUser', ['alias' => 'myMessages', 'reusable' => true]);
        $this->hasMany('userId', 'MessageThread', 'startedByUser', ['reusable' => true]);
        $this->hasMany('userId', 'Message', 'sourceUser', ['alias' => 'sentMessages', 'reusable' => true]);
        $this->hasMany('userId', 'Order', 'customerId', ['alias' => 'orders', 'reusable' => true]);
        $this->hasMany('userId', 'OrderItems', 'userId', ['alias' => 'orderItems', 'reusable' => true]);
        $this->hasMany('userId', 'Transaction', 'userId', ['alias' => 'transactions', 'reusable' => true]);
    }


    /**
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getOrderItems($options = null)
    {
        return $this->getRelated('orderItems', $options);
    }

    /**
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getTransactions($options = null)
    {
        return $this->getRelated('transactions', $options);
    }

    /**
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getOrders($options = null)
    {
        return $this->getRelated('orders', $options);
    }

    public function getSentMessages($options = null)
    {
        return $this->getRelated('sentMessages', $options);
    }

    public function getMyMessageThreads($options = null)
    {
        return $this->getRelated('MessageThread', $options);
    }

    public function getMyMessages($options = null)
    {
        return $this->getRelated('myMessages', $options);
    }

    public function getOrganisationManagementLinks($options = null)
    {
        return $this->getRelated('OrganisationManagementPermissions', $options);
    }

    public function getCanManageOrganisations($options = null)
    {
        return $this->getRelated('manages', $options);
    }

    public function getUsersOrganisation($options = null)
    {
        return $this->getRelated('organisation', $options);
    }

    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;
    }

    public function getGroupsMemberOf($options = null)
    {
        return $this->getRelated('groups', $options);
    }

    public function getGroupsOwnerOf($options = null)
    {
        return $this->getRelated('groupsOwner', $options);
    }

    /**
     * @return Portal
     */
    public function getPortal($options = null)
    {
        return $this->getRelated('Portal', $options);
    }

    /**
     * Provides a key unique to this user for encryption.
     */
    public function getUserLevelEncryptionKey()
    {
        if (!isset($this->userGUID)) {
            throw new LogicException('I cannot create a key as the user has no GUID - hydrate|create first');
        }
        return $this->userGUID . $this->getDI()->get('fieldkey');
    }

    /**
     * Users support multiple roles, and the concept of an active role.
     *
     * @param string Role|$role The name of an actual role in the system (roles table)
     * @return bool success - if returns false check messages
     */
    public function addRole($role)
    {
        $role = Role::resolve($role);

        $roleExists = UserRole::find(
                "userId = {$this->getUserId()} AND roleId = {$role->getRoleId()}"
            )->count() > 0;

        if ($roleExists) {
            return true;
        }

        $rolelink = new UserRole();
        $rolelink->setRoleId($role->getRoleId());
        $rolelink->setDisabled(false);
        $rolelink->setUserId($this->userId);

        if (!$rolelink->create()) {
            $this->appendMessageEx($rolelink->getMessages());
            return false;
        }

        $this->_activeRole = $role;
        $this->_roles = null;

        return true;
    }

    public function addToGroup($group)
    {
        $group = PortalMemberGroup::resolve($group);

        if (!$group->addUser($this)) {
            $this->appendMessageEx($group->getMessages());
            return false;
        }

        return true;
    }

    public function removeFromGroup($group)
    {
        $group = PortalMemberGroup::resolve($group);

        if (!$group->removeUser($this)) {
            $this->appendMessageEx($group->getMessages());
            return false;
        }

        return true;
    }

    /**
     * returns the actual link records between a user and interests
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getUserInterests()
    {
        return $this->getRelated('UserInterest');
    }

    /**
     * returns the Interests associated with this user
     */
    public function getInterests()
    {
        return $this->getRelated('interests');
    }

    public function addInterest($interest, $clearExisting = false)
    {
        if ($clearExisting) {
            $links = $this->getUserInterests();
            $this->_interests = null;
            foreach ($links as $link) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }
            }
        }

        if (is_array($interest) || $interest instanceof \ArrayAccess) {
            foreach ($interest as $element) {
                if (!$this->addInterest($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $interest = Interest::resolve($interest);
        }

        //check if already exists if not just cleared all
        if (!$clearExisting) {
            $interestExists = UserInterest::find(
                    "userId = {$this->getUserId()} AND interestId = {$interest->getInterestId()}"
                )->count() > 0;

            if ($interestExists) {
                return true;
            } //just indicate a positive result if requirement already set.
        }

        $userInterest = new UserInterest();
        $userInterest->userId = $this->getUserId();
        $userInterest->interestId = $interest->getInterestId();

        if (!$userInterest->create()) {
            $this->appendMessageEx($userInterest->getMessages());
            return false;
        }

        $this->_interests[$interest->getInterest()] = $interest->getInterest(); //add to static cache
        return true;
    }

    public function hasInterest($interestName)
    {
        if ($this->_interests == null) {
            $this->_interests = array();
            $interests = $this->getInterests();
            if ($interests == null || $interests->count() == 0) {
                return false;
            }

            foreach ($interests as $cat) {
                $this->_interests[$cat->getInterest()] = $cat->getInterest();
            }
        }

        return in_array($interestName, $this->_interests);
    }

    /**
     * You can safely pass this method a list of categories containing interests not assigned to this
     * item, and it will remove the ones that are (bulk ready).
     *
     * A true response will be given so long as no existing category failed to be removed.
     * @param $interest
     * @return bool
     */
    public function removeInterest($interest)
    {
        if (is_array($interest) || $interest instanceof \ArrayAccess) {
            foreach ($interest as $element) {
                if (!$this->removeInterest($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $interest = Interest::resolve($interest);
        }

        $links = $this->getUserInterests();

        foreach ($links as $link) {
            if ($link->getInterestId() == $interest->getInterestId()) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }

                if (array_key_exists($interest->getInterest(), $this->_interests)) {
                    unset($this->_interests[$interest->getInterest()]);
                }
                break;
            }
        }

        return true;
    }

    public function getUserNotes()
    {
        return $this->getRelated('UserNote');
    }

    public function getUserNotesAbout($user)
    {
        $user = \User::resolve($user);

        return UserNote::query()
            ->where("noteAboutUserId=:1:")
            ->andWhere("noteCreatorUserId=:2:")
            ->bind([1 => $user->getUserId(), 2 => $this->userId])
            ->execute();
    }

    public function addUserNote($aboutUser, $body)
    {
        $aboutUser = User::resolve($aboutUser);

        $note = new UserNote();
        $note->portalId = $this->portalId;
        $note->noteCreatorUserId = $this->userId;
        $note->noteAboutUserId = $aboutUser->getUserId();
        $note->body = $body;

        if (!$note->create()) {
            $this->appendMessageEx($note->getMessages());
            return false;
        }

        return true;
    }

    /**
     * Returns the models representation of the link table between users and roles.
     *
     * i.e [] of Role   $user->getRoles()[0]->getRole()->name
     * @return \Phalcon\Mvc\Model\ResultsetInterface of UserRole
     */
    public function getRoles($options = null)
    {
        return $this->getRelated('UserRole', $options);
    }

    /**
     * note that in the case that $role is an array, then this method will return true if user
     * contains any of the roles (i.e is || not &&)
     *
     * @param string $role the name of the role to tests
     * @return bool true if the user has the role, false otherwise
     */
    public function hasRole($role)
    {//@todo gh caching has been removed here,  can we add it back?
        //if ($this->_roles == null) {
            $this->_roles = array();
            $roles = $this->getRoles();
            if ($roles == null || $roles->count() == 0) {
                return false;
            }

            foreach ($roles as $roleItem) {
                $this->_roles[] = $roleItem->getRole()->getName();
            }
        //}

        if(! is_array($role)) {
            $role = [$role];
        }

        foreach($role as $r) {
            if(in_array($r, $this->_roles)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Indicates if the subject grants visibility to $user
     * @param $user
     * @param string $redirect
     * @return bool
     */
    public function canBeSeenBy($user, $redirect = 'error/fourofour')
    {
        return \Apprecie\Library\Acl\AccessControl::userCanBeSeenBy($this, $user, $redirect);
    }

    /**
     * checks a single or array of users for visibility by the subject
     * set second param to null to prevent redirect.
     * @param $userOrUsers
     * @param string $redirect
     * @return bool
     */
    public function canManage($userOrUsers, $redirect = 'error/fourofour')
    {
        return \Apprecie\Library\Acl\AccessControl::userCanManageUser($this, $userOrUsers, $redirect);
    }

    public function canViewItem($item, $redirect = 'error/fourofour')
    {
        return \Apprecie\Library\Acl\AccessControl::userCanViewItem($this, $item, $redirect);
    }

    /**
     * Is the subject the creator of the item
     * @param $item
     * @param string $redirect
     * @return bool
     */
    public function canEditItem($item, $redirect = 'error/fourofour')
    {
        return \Apprecie\Library\Acl\AccessControl::userCanEditItem($this, $item, $redirect);
    }

    /**
     * @param $messageThread
     * @param string $redirect
     * @return bool
     * @throws \Phalcon\Exception
     */
    public function canSeeMessageThread($messageThread, $redirect = 'error/fourofour') {
        return \Apprecie\Library\Acl\AccessControl::userCanSeeMessageThread($this, $messageThread, $redirect);
    }

    /**
     * (CHANGED)
     * If the user has a single role, this role will always be returned.
     * If the user has more than one role and no active role is set will return the non OO role
     * If an active role has been set, will always return that role.
     *
     * if you need the role object use getRoles()
     * @return bool|Role object
     */
    public function getActiveRole()
    {
        if ($this->_activeRole == null) {
            $roles = $this->getRoles();
            if ($roles == null || $roles->count() == 0) {
                $this->_activeRole = false;
            } elseif ($this->getDI()->getDefault()->get('session')->has('AUTHENTICATED_USER_ROLE') &&
                $this->hasRole($this->getDI()->getDefault()->get('session')->get('AUTHENTICATED_USER_ROLE'))
            ) {
                $this->_activeRole = $this->getDI()->getDefault()->get('session')->get('AUTHENTICATED_USER_ROLE');
            } else {
                if($roles->count() > 1){
                    foreach($roles as $role) {
                        if($role->getRole()->getName() == \Apprecie\Library\Users\UserRole::PORTAL_ADMIN) {
                            continue;
                        } else {
                            $this->setActiveRole($role->getRole()->getName());
                            break;
                        }
                    }
                }

                if($this->_activeRole == null) { //only one role,  or multiple OO roles - just set first.
                    $this->setActiveRole($roles[0]->getRole()->getName());
                }
            }
        }

        return $this->_activeRole;
    }

    public function setActiveRole($role, $autoSwitched = false)
    {
        $role = Role::resolve($role);

        if (!$this->hasRole($role)) {
            throw new OutOfBoundsException('Attempt to set role ' . $role . ' this user does not have this role');
        }

        if($autoSwitched) {
            $this->getDI()->get('auth')->registerRoleAutoSwitch();
        }

        $this->_activeRole = $role;

        if($this->getIsAuthenticatedUser()) {
            $this->getDI()->getDefault()->get('session')->set('AUTHENTICATED_USER_ROLE', $role);
        }
    }

    public function getIsAuthenticatedUser()
    {
        $authUser = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser();

        if($authUser == null) {
            return false;
        }

        return $this->getUserGUID() == $authUser->getUserGUID();
    }


    public function addDietaryRequirement($requirement)
    {
        if (is_string($requirement)) {
            $requirement = DietaryRequirement::findFirst("requirement = '{$requirement}'");
        } elseif (is_int($requirement)) {
            $requirement = DietaryRequirement::findFirst("requirementId = '{$requirement}'");
        } elseif (is_array($requirement) || $requirement instanceof \ArrayAccess) {
            foreach ($requirement as $element) {
                if (!$this->addDietaryRequirement($element)) {
                    return false;
                }
            }

            return true;
        }

        if (!$requirement instanceof DietaryRequirement) {
            $this->appendMessageEx(new Phalcon\Validation\Message("Requirement was not found"));
            return false;
        }

        //check if already exists
        $requirementExists = UserDietaryRequirement::find(
                "userId = {$this->getUserId()} AND requirementId = {$requirement->getRequirementId()}"
            )->count() > 0;

        if ($requirementExists) {
            return true;
        } //just indicate a positive result if requirement already set.

        $userRequirement = new UserDietaryRequirement();
        $userRequirement->setUserId($this->userId);
        $userRequirement->setRequirementId($requirement->getRequirementId());

        if (!$userRequirement->create()) {
            $this->appendMessageEx($userRequirement->getMessages());
            return false;
        }

        if (!is_array($this->_dietaryRequirements)) {
            $this->_dietaryRequirements = array();
        }

        if (!in_array($requirement->getRequirement(), $this->_dietaryRequirements)) {
            $this->_dietaryRequirements[] = $requirement->getRequirement();
        }

        return true;
    }

    public function hasDietaryRequirement($requirement)
    {
        if ($this->_dietaryRequirements == null) {
            $this->_dietaryRequirements = array();
            $requirements = $this->getUserDietaryRequirements();
            if ($requirements == null || $requirements->count() == 0) {
                return false;
            }

            foreach ($requirements as $req) {
                $this->_dietaryRequirements[] = $req->getDietaryRequirement()->getRequirement();
            }
        }

        return in_array($requirement, $this->_dietaryRequirements);
    }

    public function getDietaryRequirements($options = null)
    {
        return $this->getRelated('UserDietaryRequirement', $options);
    }

    public function setCreator($user)
    {
        $user = User::resolve($user);

        /*if ($user->portalId != $this->portalId) {
            throw new \LogicException('A users creator must be from the same portal');
        }*/

        $this->creatingUser = $user->userId;

        return $this->update();
    }

    public function getCreator($options)
    {
        return $this->getRelated('Creator', $options);
    }

    public function validation()
    {
        if (isset($this->organisationId) && $this->getOrganisationId() != null) {
            $org = Organisation::resolve($this->getOrganisationId());
            if ($org->getPortalId() != $this->getPortalId()) {
                $this->appendMessageEx('The chosen organisation is not a member of this users portal');
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool true if the user is an interactive user, false otherwise
     */
    public function getIsInteractive()
    {
        $auth = new \Apprecie\Library\Security\Authentication();
        return $auth->userIsInteractive($this);
    }

    /**
     * Looks through parental tree recursively so returns true if this user is a parent of user, or a parent of any
     * parent of user, to any depth.
     * @param $user
     * @param bool $OOParentsAllOrg
     * @param null $subject
     * @return bool
     */
    public function userIsDescendant($user, $OOParentsAllOrg = false, $subject = null)
    {
        $user = User::resolve($user);
        $isParent = false;

        //GH - ensures that an OO can see all users in case of multiple OO or users with different lines of heritage
        if($OOParentsAllOrg && ($this->hasRole('PortalAdministrator') && $user->getOrganisationId() == $this->getOrganisationId())) {
            return true;
        }

        if ($subject == null) {
            $subject = $this;
        }

        $users = $subject->getChildren();

        foreach ($users as $child) {
            if ($child->getUserId() == $user->getUserID()) {
                $isParent = true;
            } else {
                $isParent = $child->userIsDescendant($user, $child);
            }

            if ($isParent) {
                break;
            }
        }

        return $isParent;
    }

    public function resolveChildren($role = 'All', $parentUser = null)
    {
        $children = array();

        if ($parentUser == null) {
            $parentUser = $this;
        }

        $childrenObjs = $parentUser->getChildren();

        foreach ($childrenObjs as $child) {
            $child->clearStaticRoleData();

            if ($role == 'All') {
                $children[] = $child;
            } elseif ($child->hasRole($role)) {
                $children[] = $child;
            }

            $children = array_merge($children, $child->resolveChildren($role));
        }

        return $children;
    }

    /**
     * Note returns records of the link table userparents
     * for actual user objects use getParents()
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getParents($options = null)
    {
        return $this->getRelated('Parents', $options);
    }

    public function getFirstParent()
    {
        $parents = $this->getParents();
        if ($parents->count() > 0) {
            return $parents[0];
        }
    }

    /**
     * Note returns records of the link table userparents
     * for actual user objects use getChildren()
     * @return UserParent This is the link record between this user and another
     */
    public function getChildren($options = null)
    {
        return $this->getRelated('Children', $options);
    }

    public function setParentOf($user)
    {
        $user = User::resolve($user);

        $link = new UserParent();
        $link->parentId = $this->userId;
        $link->childId = $user->userId;

        if (!$link->create()) {
            $this->appendMessageEx($link->getMessages());
            return false;
        }

        return true;
    }

    public function setChildOf($user)
    {
        $user = User::resolve($user);

        $link = new UserParent();
        $link->childId = $this->userId;
        $link->parentId = $user->userId;

        if (!$link->create()) {
            $this->appendMessageEx($link->getMessages());
            return false;
        }

        return true;
    }

    /**
     * @return UserNotification
     */
    public function getNotifications($options = null)
    {
        return $this->getRelated('UserNotification', $options);
    }

    public function getUserFamilyLinks($options = null)
    {
        return $this->getRelated('UserFamily', $options);
    }

    public function getIndicatedFamilyUsers($options = null)
    {
        return $this->getRelated('familyMembers', $options);
    }

    public function getIndicatedAsFamilyByUsers($options = null)
    {
        return $this->getRelated('familyOf', $options);
    }

    public function addFamilyMember($user)
    {
        $user = User::resolve($user);

        //check if already exists
        $alreadyExists = UserFamily::find(
                "userId = {$this->getUserId()} AND relatedUserId = {$user->getUserId()}"
            )->count() > 0;

        if ($alreadyExists) {
            return true;
        }

        $link = new UserFamily();
        $link->setUserId($this->getUserId());
        $link->setRelatedUserId($user->getUserId());

        if (!$link->create()) {
            $this->appendMessageEx($link->getMessages());
            return false;
        }

        return true;
    }

    public function removeFamilyMember($user)
    {
        $user = User::resolve($user);

        $links = $this->getUserFamilyLinks();

        foreach ($links as $link) {
            if ($link->getRelatedUserId() == $user->getUserId()) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }

                break;
            }
        }

        return true;
    }

    /**
     * @return Organisation
     */
    public function getOrganisation($options = null)
    {
        return $this->getRelated('organisation', $options);
    }

    public function getCanManageOrganisation($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        return $organisation->canBeManagedBy($this);
    }

    public function getEventsIamAttending($daysFromStart = null)
    {
        $filter = new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('GuestList', 'Event.itemId = GuestList.itemId')
            ->addAndEqualFilter('status', \Apprecie\Library\Guestlist\GuestListStatus::CONFIRMED, 'GuestList')
            ->addAndEqualFilter('attending', 1)
            ->addAndEqualFilter('userId', $this->getUserId(), 'GuestList')
            ->addAndEqualOrGreaterThanFilter('endDateTime', date("Y-m-d H:i:s"));

        if ($daysFromStart) {
            $date = new DateTime();
            $formattedDate = $date->sub(new DateInterval('P' . $daysFromStart . 'D'))->format('Y-m-d');
            $filter->addAndEqualOrGreaterThanFilter('startDateTime', $formattedDate);
        }

        return Event::findByFilter($filter);
    }

    public function getTotalSpend($mySqlStartDate = null, $mySqlEndDate = null)
    {
        if ($mySqlStartDate == null) {
            $mySqlStartDate = '1970-01-01';
        }

        if ($mySqlEndDate == null) {
            $mySqlEndDate = (new DateTime('tomorrow'))->format('Y-m-d');
        }

        $totalSpend = $this->getModelsManager()
            ->executeQuery(
                'SELECT COALESCE(SUM(value),0) as total from OrderItems WHERE isPaidFull = 1 AND purchaseDate >= :0: AND purchaseDate <= :1: AND userId = :2:',
                [$mySqlStartDate, $mySqlEndDate, $this->getUserId()]
            )->getFirst();

        $total = $totalSpend['total'];

        return $total;
    }

    public function getTopLevelInterests()
    {
        $result = $this->getDbAdapter()->query(
            'SELECT DISTINCT(parentInterestId) FROM intereststree left outer join userinterests on intereststree.interestId = userinterests.interestId WHERE userId = ?',
            [$this->getUserId()]
        );

        $results = [];
        foreach ($result->fetchAll() as $item) {
            $results[] = $item['parentInterestId'];
        }

        return $results;
    }

    /**
     * Returns all items that would be displayed in this users vault.
     * Note if you want to add search criteria set $returnFilter to true, and execute the filter yourself
     * Else returns a recordset of Items.
     *
     * @param bool|false $returnFilter
     * @return \Apprecie\Library\Search\SearchFilter|mixed|null
     */
    public function getVisibleVaultItems($returnFilter = false)
    {
        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('ItemVault', 'Item.itemId = ItemVault.itemId')
            ->addJoin('Event', 'Item.itemId = Event.itemId');

        $filter->addJoin('User', 'Item.creatorId=User.userId')
            ->addJoin('Organisation', 'User.organisationId=Organisation.organisationId');

        switch ($this->getActiveRole()->getName()) {
            case "Manager":
                $filter->addAndIsNullFilter('ownerId');
                $filter->addOrEqualsFilter('ownerId', $this->getUserId());
                break;
            case "Internal":
                $filter->addInFilter('ownerId', [$this->getFirstParent()->getUserId(), $this->getUserId()]);
                $filter->addAndEqualFilter('internalCanSee', true);
                $filter->addOrEqualsFilter('ownerId', $this->getUserId());
                break;
            case "Client" :
                $filter->addAndEqualOrLessThanFilter('tier', $this->getTier(), 'Item');
                $filter->addAndEqualFilter('ownerId', $this->getFirstParent()->getUserId());
                $filter->addAndEqualFilter('clientsCanSee', true);
                $filter->addOrEqualsFilter('ownerId', $this->getUserId());
                break;
            case "ApprecieSupplier":
            case "AffiliateSupplier":
                $filter->addAndEqualFilter('creatorId',$this->getUserId());
                break;
        }

        switch ($this->getActiveRole()->getName()) {
            case "ApprecieSupplier":
            case "AffiliateSupplier":

                break;
            default:
                $filter->addAndEqualFilter(
                    'organisationId',
                    $this->getOrganisationId(),
                    'ItemVault'
                );
                break;

        }

        $filter->addAndEqualOrGreaterThanFilter('bookingEndDate', date('Y-m-d'))
            ->addAndNotEqualFilter('isArranged', true, 'Item')
            ->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING, 'Item');


        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::HELD);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::CLOSED);
        $filter->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING);

        if($returnFilter) {
            return $filter;
        } else {
            return Item::findByFilter($filter);
        }
    }

    /**
     * Provides a ResultSet of users for the the specified role within the specified portal
     *
     * @param $roleid int actual id of the role to return i.e.  Role::roleid
     * @param $portalid int actual portalId to search within i.e.  Portal::getPortalId()
     * @return \Phalcon\Mvc\Model\ResultsetInterface Result set of User
     */
    public static function getUsersInRole($roleId, $portalId = null)
    {
        $query = User::query()->innerJoin('UserRole');

        if ($portalId != null) {
            $query
                ->where("User.portalId=:1:")
                ->andWhere('UserRole.roleId=:2:')
                ->bind(array(1 => $portalId, 2 => $roleId));
        } else {
            $query
                ->where('UserRole.roleId=:2:')
                ->bind(array(2 => $roleId));
        }

        return $query->execute();
    }

    public static function getUsersInOrganisation($organisationId, $portalId)
    {
        return User::query()
            ->where('organisationId = :1:')
            ->andWhere('portalId = :2:')
            ->bind([1 => $organisationId, 2 => $portalId])
            ->execute();
    }

    public static function findByInterests($categories,$users=null)
    {
        $resolved = array();
        if (!is_array($categories)) {
            $categories = array($categories);
        }

        foreach ($categories as $cat) {
            $item = Interest::resolve($cat);
            $resolved[] = $item->getInterestId();
        }

        return User::query()
            ->innerJoin('UserInterest')
            ->inWhere('interestId', $resolved)
            ->execute();
    }
}