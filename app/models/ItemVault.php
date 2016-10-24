<?php

class ItemVault extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $itemId, $ownerId, $portalId, $groupId, $organisationId, $vaultItemId, $clientsCanSee, $internalCanSee, $suggestedBy;

    /**
     * @param mixed $suggestedBy
     */
    public function setSuggestedBy($suggestedBy)
    {
        $this->suggestedBy = $suggestedBy;
    }

    /**
     * @return mixed
     */
    public function getSuggestedBy()
    {
        return $this->suggestedBy;
    }

    /**
     * @param mixed $clientsCanSee
     */
    public function setClientsCanSee($clientsCanSee)
    {
        $this->clientsCanSee = $clientsCanSee;
    }

    /**
     * @return mixed
     */
    public function getClientsCanSee()
    {
        return $this->clientsCanSee;
    }

    /**
     * @param mixed $internalCanSee
     */
    public function setInternalCanSee($internalCanSee)
    {
        $this->internalCanSee = $internalCanSee;
    }

    /**
     * @return mixed
     */
    public function getInternalCanSee()
    {
        return $this->internalCanSee;
    }

    public function getVaultItemId()
    {
        return $this->vaultItemId;
    }

    /**
     * @param mixed $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    public function setOrganisationId($orgId)
    {
        $this->organisationId = $orgId;
    }

    /**
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
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
        return 'itemvault';
    }

    public function initialize()
    {
        $this->hasOne('itemId', 'Item', 'itemId', ['reusable' => true]);
    }

    public function onConstruct()
    {
        $this->setDefaultFields(['clientsCanSee', 'internalCanSee']);
        parent::onConstruct();
    }

    public static function findGlobalItemsForOrganisation($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        return \Item::query()->join('ItemVault')->where('ownerId is null')->andWhere('organisationId = :1:')->bind(
            [1 => $organisation->getOrganisationId()]
        )->execute();
    }

    /**
     * returns the number of portals published to.
     *
     * @param $item
     */
    public static function getPortalPublishedCount($item)
    {
        $item = Item::resolve($item);
        $db = \Phalcon\DI::getDefault()->get('db');
        $adminPortal = Portal::resolve('admin');
        $dbResult = $db->query("select coalesce(count(distinct portalId)) as total from itemvault where itemId = ? and portalId != ?", [$item->getItemId(), $adminPortal->getPortalId()]);
        return $dbResult->fetchArray()['total'];
    }

    public static function getOrganisationPublishedCount($item)
    {
        $item = Item::resolve($item);
        $db = \Phalcon\DI::getDefault()->get('db');
        $adminPortal = Portal::resolve('admin');
        $dbResult = $db->query("select coalesce(count(distinct organisationId)) as total from itemvault where itemId = ? and portalId != ?", [$item->getItemId(), $adminPortal->getPortalId()]);
        return $dbResult->fetchArray()['total'];
    }
} 