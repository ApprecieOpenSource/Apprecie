<?php

/**
 * The portal is a virtually isolated application instance.
 *
 * Portals should be loaded via the sub domain of the request.
 * The application has an important concept of the active portal,  and this is used to regulate styling and
 * portal assets, but also to control access to a portal users which are table isolated, using portal prefixes
 * to private tables.
 *
 * Generally speaking the application should continue to be written such that controllers are generic to all portals
 * and the application layer should support this invisibly.  Specific or specialised content is normally specific to
 * a Role, and not a portal.
 *
 * If you need to write portal specific functionality think very carefully before breaking the existing generics, and
 * consider a per portal folder that could contain specific controllers.
 *
 * The active portal should have been configured during bootstrap, and available from the dic.
 * See
 * <code>
 * $portal = Apprecie\Library\Provisioning\PortalStrap::getActivePortal();
 * //or
 * $portal = $di->get('portal');
 * </code>
 *
 * Class Portal
 */
class Portal extends \Apprecie\Library\Model\CachedApprecieModel
{
    use \Apprecie\Library\Tracing\ActivityTraceTrait;

    protected $portalId;
    protected $portalName;
    protected $portalSubdomain;
    protected $portalGUID;
    protected $suspended;
    protected $internalAlias;
    protected $accountManager;
    protected $paymentDisabled;
    protected $description;
    protected $edition;
    protected $createdDate;
    private $_blockedCategories = null;

    public function getSource()
    {
        return 'portals';
    }

    public function initialize()
    {
        $this->skipAttributesOnUpdate(array('portalGUID'));
        $this->hasOne('portalId', 'PortalStyle', 'portalId', ['reusable' => true]);
        $this->hasMany('portalId', 'Contact', 'portalId', ['reusable' => true]);
        $this->hasOne('accountManager', 'User', 'userId', ['alias' => 'accountmanager', 'reusable' => true]);
        $this->hasMany('portalId', 'Quotas', 'portalId');
        $this->hasMany('portalId', 'PortalBlockedCategories', 'portalId', ['reusable' => true]);
        $this->hasManyToMany(
            'portalId',
            'PortalBlockedCategories',
            'portalId',
            'interestId',
            'Interest',
            'interestId',
            ['alias' => 'categories', 'reusable' => true]
        );
    }

    public function getPortalBlockedCategories($options = null)
    {
        return $this->getRelated('PortalBlockedCategories', $options);
    }

    public function onConstruct()
    {
        static::setCachingMode(\Apprecie\library\Cache\CachingMode::Persistent);
        $this->setDefaultFields('createdDate');
        $this->setIndirectContentFields(['description']);
        parent::onConstruct();
    }

    public function getOrganisations()
    {
        return Organisation::findBy('portalId', $this->getPortalId());
    }

    /**
     * Note that the organisation will be created if it does not exist.
     * @return Organisation | bool The owning organisation or false on error
     */
    public function getOwningOrganisation()
    {
        $portals = Organisation::query()
            ->where('isPortalOwner = :0:')
            ->andWhere('portalId = :1:')
            ->bind([0 => 1, 1 => $this->getPortalId()])
            ->execute();

        if ($portals->count() == 0) {
            return $this->createOwningOrganisation();
        } elseif ($portals->count() == 1) {
            return $portals[0];
        } else {
            _d('HARD STOP - portal has more than one owning organisation - 1 + 1 = 3');
        }
    }

    /**
     * Creates a default owning organisation based on the portal name.
     * @return bool|Organisation
     */
    protected function createOwningOrganisation()
    {
        $organisation = new Organisation();
        $organisation->setOrganisationName($this->getPortalName());
        $organisation->setPortalId($this->getPortalId());
        $organisation->setIsPortalOwner(true);

        if (!$organisation->create()) {
            $this->appendMessageEx($organisation->getMessages());
            return false;
        }

        return $organisation;
    }

    public function validation()
    {
        $this->validate(
            new \Phalcon\Mvc\Model\Validator\Uniqueness(
                array('field' => 'portalName')
            )
        );

        $this->validate(
            new \Phalcon\Mvc\Model\Validator\Uniqueness(
                array('field' => 'portalGUID')
            )
        );

        $this->validate(
            new \Phalcon\Mvc\Model\Validator\Uniqueness(
                array('field' => 'portalSubdomain')
            )
        );

        return ($this->validationHasFailed() != true);
    }

    /**
     * @param mixed $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }


    public function getEdition()
    {
        return $this->edition;
    }

    public function setEdition($edition)
    {
        $this->edition = $edition;
    }

    /**
     * @param mixed $portalName
     */
    public function setPortalName($portalName)
    {
        $this->portalName = $portalName;
    }

    /**
     * @return mixed
     */
    public function getPortalName()
    {
        return $this->portalName;
    }

    /**
     * @return mixed
     */
    public function getPortalGUID()
    {
        return $this->portalGUID;
    }

    public function setPortalGUID($portalGUID)
    {
        $this->portalGUID = $portalGUID;
    }

    /**
     * @return mixed
     */
    public function getPortalId()
    {
        return $this->portalId;
    }

    /**
     * @param mixed $portalSubdomain
     */
    public function setPortalSubdomain($portalSubdomain)
    {
        $this->portalSubdomain = $portalSubdomain;
        if ($this->internalAlias == null) {
            $this->internalAlias = $portalSubdomain;
        }
    }

    public function getInternalAlias()
    {
        return $this->internalAlias;
    }

    /**
     * @return mixed
     */
    public function getPortalSubdomain()
    {
        return $this->portalSubdomain;
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
     * @param mixed $accountManager
     */
    public function setAccountManager($accountManager)
    {
        $accountManager = \User::resolve($accountManager);

        $this->accountManager = $accountManager->getUserId();
    }

    /**
     * @return mixed
     */
    public function getAccountManager()
    {
        return $this->accountManager;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $paymentDisabled
     */
    public function setPaymentDisabled($paymentDisabled)
    {
        $this->paymentDisabled = $paymentDisabled;
    }

    /**
     * @return mixed
     */
    public function getPaymentDisabled()
    {
        return $this->paymentDisabled;
    }

    /**
     * Currently returns the styles of the owning organisation
     *
     * @return PortalStyle
     */
    public function getPortalStyles()
    {
        $organisation = Organisation::getActiveUsersOrganisation();
        return $organisation->getOrganisationStyles();
    }

    public function getContacts($options = null)
    {
        return $this->getRelated('Contact', $options);
    }

    /**
     * @return User
     */
    public function getPortalAccountManager($options = null)
    {
        return $this->getRelated('accountmanager', $options);
    }

    public function getPortalOwner()
    {
        return \User::getUsersInRole(1, $this->getPortalId());
    }

    /**
     * Returns the owning organisations quotas.  note that if none exists this will create an oqning organisation.
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getPortalQuotas()
    {
        return $this->getOwningOrganisation()->getQuotas();
    }

    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        $portal = null;

        if (is_string($param)) {
            $portal = \Portal::findFirst("internalAlias='{$param}'");
        }

        if ($portal == null) {
            $portal = parent::resolve($param, $throw, $instance);
        }

        return $portal;
    }

    public function getBlockedCategories($options = null)
    {
        return $this->getRelated('categories', $options);
    }

    public function addBlockedCategory($category, $clearExisting = false)
    {
        if ($clearExisting) {
            $links = $this->getPortalBlockedCategories();
            $this->_blockedCategories = null;
            foreach ($links as $link) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }
            }
        }

        if (is_array($category) || $category instanceof \ArrayAccess) {
            foreach ($category as $element) {
                if (!$this->addBlockedCategory($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $category = Interest::resolve($category);
        }

        //check if already exists if not just cleared all
        if (!$clearExisting) {
            $interestExists = PortalBlockedCategories::find(
                    "portalId = {$this->getPortalId()} AND interestId = {$category->getInterestId()}"
                )->count() > 0;

            if ($interestExists) {
                return true;
            } //just indicate a positive result if requirement already set.
        }

        $blockedCategoryLink = new PortalBlockedCategories();
        $blockedCategoryLink->setPortalId($this->getPortalId());
        $blockedCategoryLink->setInterestId($category->getInterestId());

        if (!$blockedCategoryLink->create()) {
            $this->appendMessageEx(new \Phalcon\Mvc\Model\Message(_ms(($blockedCategoryLink->getMessages()))));
            return false;
        }

        $this->_blockedCategories[$category->getInterest()] = $category->getInterest(); //add to static cache

        return true;
    }

    public function hasBlockedCategory($categoryName)
    {
        if ($this->_blockedCategories == null) {
            $this->_blockedCategories = array();
            $categories = $this->getBlockedCategories();
            if ($categories == null || $categories->count() == 0) {
                return false;
            }

            foreach ($categories as $cat) {
                $this->_blockedCategories[$cat->getInterest()] = $cat->getInterest();
            }
        }

        return in_array($categoryName, $this->_blockedCategories);
    }

    /**
     * You can safely pass this method a list of categories containing interests not assigned to this
     * item, and it will remove the ones that are (bulk ready).
     *
     * A true response will be given so long as no existing category failed to be removed.
     * @param $category
     * @return bool
     */
    public function removeBlockedCategory($category)
    {
        if (is_array($category) || $category instanceof \ArrayAccess) {
            foreach ($category as $element) {
                if (!$this->removeBlockedCategory($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $category = Interest::resolve($category);
        }

        $links = $this->getPortalBlockedCategories();

        foreach ($links as $link) {
            if ($link->getInterestId() == $category->getInterestId()) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }

                if (array_key_exists($category->getInterest(), $this->_blockedCategories)) {
                    unset($this->_blockedCategories[$category->getInterest()]);
                }
                break;
            }
        }

        return true;
    }

    /**
     * @return bool true if portal contains any organisations with managers
     */
    public function hasManagers()
    {
        return $this->getModelsManager()
            ->executeQuery
            (
                'SELECT COALESCE(COUNT(User.userId)) as managers FROM User INNER JOIN UserRole ON User.userId = UserRole.userId WHERE portalId = :portalId: AND (roleId = 11 OR roleId = 31)',
                ['portalId'=>$this->getPortalId()]
            )
            ->getFirst()['managers'] > 0;
    }
}