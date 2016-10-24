<?php

class Organisation extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $organisationId, $organisationName, $organisationDescription, $portalId, $isPortalOwner, $isAffiliateSupplierOf, $subDomain, $suspended, $vatNumber;

    /**
     * @param mixed $vatNumber
     */
    public function setVatNumber($vatNumber)
    {
        $this->vatNumber = $vatNumber;
    }

    /**
     * @return mixed
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @param mixed $suspended
     */
    public function setSuspended($suspended)
    {
        $this->suspended = $suspended;
    }

    /**
     * @return mixed
     */
    public function getSuspended()
    {
        return $this->suspended;
    }

    /**
     * @param mixed $isPortalOwner
     */
    public function setIsPortalOwner($isPortalOwner)
    {
        $this->isPortalOwner = $isPortalOwner;
    }

    /**
     * @return mixed
     */
    public function getIsPortalOwner()
    {
        return $this->isPortalOwner;
    }

    public function getSubDomain()
    {
        return $this->subDomain;
    }

    public function setSubDomain($domain)
    {
        $this->subDomain = $domain;
    }

    /**
     * @param mixed $isAffiliateSupplierOf
     */
    public function setIsAffiliateSupplierOf($isAffiliateSupplierOf)
    {
        $organisation = Organisation::resolve($isAffiliateSupplierOf);
        $this->isAffiliateSupplierOf = $organisation->getOrganisationId();
    }

    /**
     * @return mixed
     */
    public function getIsAffiliateSupplierOf()
    {
        return $this->isAffiliateSupplierOf;
    }

    /**
     * @param mixed $organisationDescription
     */
    public function setOrganisationDescription($organisationDescription)
    {
        $this->organisationDescription = $organisationDescription;
    }

    /**
     * @return mixed
     */
    public function getOrganisationDescription()
    {
        return $this->organisationDescription;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    /**
     * @param mixed $organisationName
     */
    public function setOrganisationName($organisationName)
    {
        $this->organisationName = $organisationName;
    }

    /**
     * @return mixed
     */
    public function getOrganisationName()
    {
        return $this->organisationName;
    }

    public function getOrganisationOwner()
    {
        $result = \User::getUsersInRole(1, $this->getPortalId());
        if ($result->count() > 0) {
            return $result[0];
        }

        return null;
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
     * @return Portal
     */
    public function getPortal($options = null)
    {
        return $this->getRelated('Portal', $options);
    }

    /**
     * @return Quotas
     */
    public function getQuotas($options = null)
    {
        return $this->getRelated('Quotas', $options);
    }

    public function getMailSettings($options = null)
    {
        return $this->getRelated('MailSettings', $options);
    }

    public function getContacts($options = null)
    {
        return $this->getRelated('Contact', $options);
    }

    public function getSource()
    {
        return 'organisations';
    }

    public function validation()
    {
        $result = Organisation::query()
            ->where('isPortalOwner = :0:')
            ->andWhere('portalId = :1:')
            ->bind([0 => 1, 1 => $this->getPortalId()])
            ->execute();

        if (count($result) > 0) {
            $record = $result[0];
            if ($record->getOrganisationId() != $this->getOrganisationId() and $this->getIsPortalOwner() === true) {
                $this->appendMessageEx(
                    'This organisation cannot own the portal as another organisation already has ownership'
                );
                return false;
            }
        }

        if ($this->getSubDomain() != null) {
            $portalOrgs = $this->getPortal()->getOrganisations();
            foreach ($portalOrgs as $org) {
                if ($org->getSubDomain() == $this->getSubDomain() and $org->getOrganisationId(
                    ) != $this->getOrganisationId()
                ) {
                    $this->appendMessageEx('The subdomain is already used by an organisation on this portal.');
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Note that the payment settings record will be created the first time it is requested.
     *
     * @return PaymentSettings
     */
    public function getPaymentSettings()
    {
        $settings = $this->getRelated('PaymentSettings');

        if ($settings == null) {
            $settings = new PaymentSettings();
            $settings->setOrganisationId($this->getOrganisationId());

            if (!$settings->create()) {
                $this->appendMessageEx($settings->getMessages());
            }
        }

        return $settings;
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
        $this->setDefaultFields(['isPortalOwner', 'isAffiliateSupplierOf', 'suspended']);
        $this->setIndirectContentFields(['organisationDescription']);
        parent::onConstruct();
    }

    public function initialize()
    {
        $this->hasMany('organisationId', 'Contact', 'organisationId', ['reusable' => true]);
        $this->belongsTo('portalId', 'Portal', 'portalId', ['reusable' => true]);
        $this->hasMany('organisationId', 'Item', 'sourceOrganisationId', ['reusable' => true]);
        $this->hasOne('organisationId', 'MailSettings', 'organisationId', ['reusable' => true]);
        $this->hasOne('organisationId', 'Quotas', 'organisationId');
        $this->hasOne('organisationId', 'PaymentSettings', 'organisationId', ['reusable' => true]);
        $this->hasMany(
            'organisationId',
            'OrganisationParents',
            'organisationId',
            ['alias' => 'Parents', 'reusable' => true]
        );
        $this->hasMany(
            'organisationId',
            'OrganisationParents',
            'parentId',
            ['alias' => 'Children', 'reusable' => true]
        );
        $this->hasManyToMany(
            'organisationId',
            'OrganisationParents',
            'organisationId',
            'parentId',
            'organisation',
            'organisationId',
            ['alias' => 'ParentOrganisations', 'reusable' => true]
        );
        $this->hasManyToMany(
            'organisationId',
            'OrganisationParents',
            'parentId',
            'organisationId',
            'organisation',
            'organisationId',
            ['alias' => 'ChildOrganisations', 'reusable' => true]
        );
        $this->belongsTo(
            'isAffiliateSupplierOf',
            'Organisation',
            'organisationId',
            ['alias' => 'Suppliers', 'reusable' => true]
        );
        $this->hasMany(
            'organisationId',
            'Organisation',
            'isAffiliateSupplierOf',
            ['alias' => 'AffiliateSuppliers', 'reusable' => true]
        );
        $this->hasMany(
            'organisationId',
            'OrganisationManagementPermissions',
            'organisationId',
            ['alias' => 'managerLinks', 'reusable' => true]
        );
        $this->hasManyToMany(
            'organisationId',
            'OrganisationManagementPermissions',
            'organisationId',
            'userId',
            'User',
            'userId',
            ['alias' => 'managers', 'reusable' => true]
        );
        $this->hasOne('organisationId', 'OrganisationStyles', 'organisationId', ['reusable' => true]);
        $this->hasMany('organisationId', 'ItemVault', 'organisationId', ['reusable' => true]);
        $this->hasManyToMany(
            'oraganisationId',
            'ItemVault',
            'organisationId',
            'itemId',
            'Item',
            'itemId',
            ['alias' => 'vaultItems', 'reusable' => true]
        );
        $this->hasMany('organisationId', 'ItemApproval', 'verifyingOrganiationId', ['reusable' => true]);
        $this->hasManyToMany(
            'organisationId',
            'ItemApproval',
            'verifyingOrganiationId',
            'itemId',
            'Item',
            'itemId',
            ['alias' => 'toApproveItems', 'reusable' => true]
        );
    }


    public function getOrganisationStyles($override = true)
    {
        $style = $this->getRelated('OrganisationStyles');

        if ($override && $style == null) {
            if ($style == null && !$this->getIsPortalOwner()) {
                $style = $this->getPortal()->getOwningOrganisation()->getOrganisationStyles(false);
            }

            if ($style == null) {
                $style = \Portal::findFirst("portalSubdomain='admin'")->getOwningOrganisation()->getOrganisationStyles(
                    false
                );

                //admin null - should only happen on install?
                if ($style == null) {
                    $style = new OrganisationStyles();
                    $style->setOrganisationId(
                        \Portal::findFirst("portalSubdomain='admin'")->getOwningOrganisation()->getOrganisationId()
                    );

                    // SET ALL THE STYLES TO THE DEFAULT VALUES
                    $style->setNavigationPrimary('#5C5E62');
                    $style->setNavigationSecondary('#F3713C');
                    $style->setNavigationPrimaryA('#FFFFFF');
                    $style->setNavigationSecondaryA('#FFFFFF');
                    $style->setFontColor('#676a6c');
                    $style->setA('#4494D0');
                    $style->setAhover('#2F4050');
                    $style->setButtonPrimary('#5C5E62');
                    $style->setButtonPrimaryBorder('#5C5E62');
                    $style->setButtonPrimaryHover('#5C5E62');
                    $style->setButtonPrimaryHoverBorder('#5C5E62');
                    $style->setButtonPrimaryColor('#FFFFFF');
                    $style->setProgressBar('#4494D0');

                    if (!$style->create()) {
                        _d('Could not create default styles ' . _ms($style));
                    }

                    return $style;
                }
            }

            if ($style != null) {
                $copy = new OrganisationStyles();
                $copy->assign($style->toArray());
                $copy->setOrganisationId($this->getOrganisationId());
                $copy->create();

                Assets::setInitialOrganisationBackground($this);
                Assets::setInitialOrganisationLogo($this);
            }
        }

        return $style;
    }

    public function getManagerLinks($options = null)
    {
        return $this->getRelated('managerLinks', $options);
    }

    public function getManagers($options = null)
    {
        return $this->getRelated('managers', $options);
    }

    public function getAffiliateSuppliers($options = null)
    {
        return $this->getRelated('AffiliateSuppliers', $options);
    }

    public function getSuppliers($options = null)
    {
        return $this->getRelated('Suppliers', $options);
    }

    /**
     * Note returns records of the link table OrganisationParents
     * for actual user objects use getParents()
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getParentLinks($options = null)
    {
        return $this->getRelated('Parents', $options);
    }

    /**
     * Note returns records of the link table OrganisationParentsd
     * for actual user objects use getChildren()
     * @return UserParent This is the link record between this user and another
     */
    public function getChildrenLinks($options = null)
    {
        return $this->getRelated('Children', $options);
    }

    public function getParents($options = null)
    {
        return $this->getRelated('ParentOrganisations', $options);
    }

    public function getChildren($options = null)
    {
        return $this->getRelated('ChildOrganisations', $options);
    }

    public function setParentOf($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        $link = new OrganisationParents();
        $link->setParentId($this->getOrganisationId());
        $link->setOrganisationId($organisation->getOrganisationId());

        if (!$link->create()) {
            $this->appendMessageEx($link->getMessages());
            return false;
        }

        return true;
    }

    public function setChildOf($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        $link = new OrganisationParents();
        $link->setParentId($organisation->getOrganisationId());
        $link->setOrganisationId($this->getOrganisationId());

        if (!$link->create()) {
            $this->appendMessageEx($link->getMessages());
            return false;
        }

        return true;
    }

    public function hasManagers()
    {
        return $this->getModelsManager()
            ->executeQuery
            (
                'SELECT COALESCE(COUNT(User.userId)) as managers FROM User INNER JOIN UserRole ON User.userId = UserRole.userId WHERE organisationId = :orgId: AND (roleId = 11 OR roleId = 31)',
                ['orgId'=>$this->getOrganisationId()]
            )
            ->getFirst()['managers'] > 0;
    }

    public function resolveChildren($excludeNonManagerPortals = false, $organisation = null)
    {
        $childOrgs = array();

        if($organisation == null) {
            $organisation = $this;
        } else {
            $organisation = Organisation::resolve($organisation);
        }

        foreach ($organisation->getChildren() as $child) {
            if(($excludeNonManagerPortals && $child->hasManagers()) || ! $excludeNonManagerPortals) {
                if(! in_array($child, $childOrgs)) {
                    $childOrgs[] = $child;
                }
            }

            $childOrgs = array_merge($childOrgs, $child->resolveChildren());
        }

        return $childOrgs;
    }

    public function addCanBeManagedBy($user)
    {
        $user = User::resolve($user);

        $link = new OrganisationManagementPermissions();
        $link->setUserId($user->getUserId());
        $link->setOrganisationId($this->getOrganisationId());

        if (!$link->save()) {
            $this->appendMessageEx($link);
            return false;
        }

        return true;
    }

    public function removeCanBeManagedBy($user)
    {
        $user = User::resolve($user);

        $links = $this->getManagerLinks();

        foreach ($links as $link) {
            if ($link->getUserId() == $user->getUserId()) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link);
                    return false;
                }

                break;
            }
        }

        return true;
    }

    public function canBeManagedBy($user)
    {
        $user = User::resolve($user);

        $links = $this->getManagerLinks();

        foreach ($links as $link) {
            if ($link->getUserId() == $user->getUserId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $event
     * @param null $vaultOwner
     * @param bool $visibleToClients
     * @param bool $visibleToInternal
     * @param null $group
     * @throws UnexpectedValueException
     * @return bool
     */
    public function addEventToVault(
        $event,
        $vaultOwner = null,
        $visibleToClients = false,
        $visibleToInternal = false,
        $group = null,
        $suggestedBy = null
    ) {
        $event = Event::resolve($event);
        $update = false;

        if (!$event->getState() == \Apprecie\Library\Items\ItemState::APPROVED) {
            $this->appendMessageEx(_g('This item has not been approved.'));
            return false;
        }

        if ($vaultOwner != null) {
            $vaultOwner = User::resolve($vaultOwner);
        }

        if ($suggestedBy != null) {
            $suggestedBy = User::resolve($suggestedBy);
        }

        if ($group != null) {
            $group = PortalMemberGroup::resolve($group);
        }

        $vaultLink = $this->hasEventInVault($event, $vaultOwner, $group);

        if ($vaultLink->count() > 1) {
            throw new UnexpectedValueException('There is already more than one item for this vault - owner - group - organisation  combination');
        } elseif ($vaultLink->count() == 1) {
            $vault = $vaultLink[0];
            $update = true;
        } else {
            $vault = new ItemVault();
        }

        if ($vaultOwner != null) {
            $vaultOwner = User::resolve($vaultOwner);
        }

        if ($group != null) {
            $vault->setGroupId($group);
        }

        if ($vaultOwner != null) {
            $vault->setOwnerId($vaultOwner->getUserId());
        }

        $vault->setItemId($event->getItemId());
        $vault->setOrganisationId($this->getOrganisationId());
        $vault->setPortalId($this->getPortalId());
        $vault->setClientsCanSee($visibleToClients);
        $vault->setInternalCanSee($visibleToInternal);

        if ($suggestedBy) {
            $vault->setSuggestedBy($suggestedBy->getUserId());
        }

        if ($update) {
            $success = $vault->update();
        } else {
            $success = $vault->create();
        }

        if (!$success) {
            $this->appendMessageEx($vault);
            $this->logActivity('failed in vault add', 'messages ' . _ms($vault));
            return false;
        }

        $event->setStatus(\Apprecie\Library\Items\EventStatus::PUBLISHED);
        $event->update();

        return true;
    }

    public function hasEventInVault($event, $vaultOwner = null, $group = null)
    {
        $query = ItemVault::query();
        $params = array();
        $params['itemId'] = $event->getItemId();
        $params['organisationId'] = $this->getOrganisationId();

        $query->where('itemId=:itemId:');
        $query->andWhere('organisationId=:organisationId:');

        if ($vaultOwner != null) {
            $vaultOwner = User::resolve($vaultOwner);

            $params['ownerId'] = $vaultOwner->getUserId();
            $query->andWhere('ownerId=:ownerId:');
        } else {
            $query->andWhere('ownerId is null');
        }

        if ($group != null) {
            $group = PortalMemberGroup::resolve($group);

            $params['groupId'] = $group->getGroupId();
            $query->andWhere('groupId=:groupId:');
        } else {
            $query->andWhere('groupId is null');
        }

        $query->bind($params);
        return $query->execute();
    }


    public function removeEventFromVaultForGroup($event, $vaultOwner, $group)
    {
        $event = Event::resolve($event);
        $vaultOwner = User::resolve($vaultOwner);
        $group = PortalMemberGroup::resolve($group);

        $vaultItem = ItemVault::query()
            ->where('itemId=:0:')
            ->andWhere('organisationId=:1:')
            ->andWhere('ownerId=:2:')
            ->andWhere('groupId=:3:')
            ->bind
            (
                [
                    0 => $event->getItemId(),
                    1 => $this->getOrganisationId(),
                    2 => $vaultOwner->getUserId(),
                    3 => $group->getGroupId()
                ]
            )
            ->execute();

        foreach ($vaultItem as $item) {
            if (!$item->delete()) {
                $this->appendMessageEx($item);
            }
        }
    }

    public function removeEventFromManager($event, $vaultOwner)
    {
        $event = Event::resolve($event);
        $vaultOwner = User::resolve($vaultOwner);

        $vaultItem = ItemVault::query()
            ->where('itemId=:0:')
            ->andWhere('organisationId=:1:')
            ->andWhere('ownerId=:2:')
            ->bind
            (
                [
                    0 => $event->getItemId(),
                    1 => $this->getOrganisationId(),
                    2 => $vaultOwner->getUserId(),
                ]
            )
            ->execute();

        foreach ($vaultItem as $item) {
            if (!$item->delete()) {
                $this->appendMessageEx($item);
            }
        }
    }

    public function removeEventFromOrganisation($event)
    {
        $event = Event::resolve($event);

        $vaultItem = ItemVault::query()
            ->where('itemId=:0:')
            ->andWhere('organisationId=:1:')
            ->bind
            (
                [
                    0 => $event->getItemId(),
                    1 => $this->getOrganisationId(),

                ]
            )
            ->execute();

        foreach ($vaultItem as $item) {
            if (!$item->delete()) {
                $this->appendMessageEx($item);
            }
        }
    }

    public function addItemForApproval($item)
    {
        $item = Item::resolve($item);

        if ($item->getDestination() == \Apprecie\Library\Items\ItemDestination::PRIVATE_ITEM) {
            $this->appendMessageEx(_g('This item does not require approval it is a private item'));
            return false;
        }

        if ($item->getState() == \Apprecie\Library\Items\ItemState::APPROVED) {
            $this->appendMessageEx(_g('This item is already approved'));
            return false;
        }

        $approval = $item->getRelatedApproval();

        if ($approval == null) {
            $approval = new ItemApproval();
            $approval->setItemId($item->getItemId());
            $approval->setCreatingOrganisationId($item->getSourceOrganisationId());
            $approval->setStatus(\Apprecie\Library\Items\ApprovalState::PENDING);
            $approval->setVerifyingOrganisationId($this->getOrganisationId());

            if (!$approval->save()) {
                $this->appendMessageEx($approval);
                return false;
            }
        }

        return true;
    }

    public function delete()
    {
        if ($this->getIsPortalOwner()) {
            throw new \Phalcon\Exception('You cannot delete the owning portal');
        }

        return parent::delete();
    }

    /**
     * If no user is authenticated will return the current portals default organisation, else will return the current
     * authenticated users Organisation
     * @return Organisation
     */
    public static function getActiveUsersOrganisation()
    {
        $user = \Phalcon\DI::getDefault()->get('auth')->getAuthenticatedUser();

        if ($user == null) {
            return \Phalcon\DI::getDefault()->get('portal')->getOwningOrganisation();
        }

        return $user->getOrganisation();
    }

    public static function getUsersInRole($role, $organisation)
    {
        $role = Role::resolve($role);
        $organisation = Organisation::resolve($organisation);
        return User::query()->innerJoin('UserRole')
            ->where("User.OrganisationId=:1:")
            ->andWhere('UserRole.roleId=:2:')
            ->bind(array(1 => $organisation->getOrganisationId(), 2 => $role->getRoleId()))
            ->execute();
    }

    /**
     * Returns a result set of Organisations that are suppliers of the active users organisation
     * in this case a supplier of means has provided content published in the oprganisation
     */
    public static function getActiveSuppliers($organisation = null)
    {
        if ($organisation == null) {
            $organisation = static::getActiveUsersOrganisation();
        } else {
            $organisation = Organisation::resolve($organisation);
        }

        $query = $organisation->getModelsManager()->createBuilder();

        $query->distinct(true);
        $query->from('Organisation');
        $query->where('Organisation.organisationId = :org:', ['org' => $organisation->getOrganisationId()])->join(
            'ItemVault'
        )->join('Item', 'ItemVault.itemId = Item.itemId');

        return $query->getQuery()->execute();
    }

    /**
     * useful for resolving an id or object to an actual Object.
     *
     * In base form resolves if $param is an instance of this model, if so returns it, else
     * checks if $param could be the id of this model, and will return the look up.
     *
     * @param $param mixed|\Apprecie\Library\Model\ApprecieModelBase an instance of a model or the id for a record of this model
     * @param bool $throw
     * @param \Apprecie\Library\Model\ApprecieModelBase|null $instance If not provided a default of instance of get_called_class() will be used for model meta
     * @throws \InvalidArgumentException
     * @throws \Phalcon\Exception
     * @return Organisation|null return if possible the actual object referenced, else null
     */
    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        return parent::resolve($param, $throw, $instance);
    }
} 