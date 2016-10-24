<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/02/15
 * Time: 18:04
 */
class ItemApproval extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $itemId, $creatingOrganisationId, $verifyingOrganisationId, $verifiedByUserId, $status, $deniedReason, $lastProcessed;

    /**
     * @param mixed $creatingOrganisationId
     */
    public function setCreatingOrganisationId($creatingOrganisationId)
    {
        $this->creatingOrganisationId = $creatingOrganisationId;
    }

    public function getLastProcessed()
    {
        return $this->lastProcessed;
    }

    public function setLastProcessed($timestamp)
    {
        $this->lastProcessed = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getCreatingOrganisationId()
    {
        return $this->creatingOrganisationId;
    }

    /**
     * @param mixed $deniedReason
     */
    public function setDeniedReason($deniedReason)
    {
        $this->deniedReason = $deniedReason;
    }

    /**
     * @return mixed
     */
    public function getDeniedReason()
    {
        return $this->deniedReason;
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
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $verifiedByUserId
     */
    public function setVerifiedByUserId($verifiedByUserId)
    {
        $this->verifiedByUserId = $verifiedByUserId;
    }

    /**
     * @return mixed
     */
    public function getVerifiedByUserId()
    {
        return $this->verifiedByUserId;
    }

    /**
     * @param mixed $verifyingOrganiationId
     */
    public function setVerifyingOrganisationId($verifyingOrganiationId)
    {
        $this->verifyingOrganisationId = $verifyingOrganiationId;
    }

    /**
     * @return mixed
     */
    public function getVerifyingOrganisationId()
    {
        return $this->verifyingOrganisationId;
    }

    public function getSource()
    {
        return 'itemapproval';
    }

    public function initialize()
    {
        $this->hasOne(
            'creatingOrganisationId',
            'organisation',
            'organisationId',
            ['alias' => 'creator', 'reusable' => true]
        );
        $this->hasOne(
            'verifyingOrganisationId',
            'organisation',
            'organisationId',
            ['alias' => 'verfiyingOrganisation', 'reusable' => true]
        );
        $this->hasOne('itemId', 'Item', 'itemId');
        $this->hasOne('verifiedByUserId', 'User', 'userId', ['alias' => 'verifyingUser', 'reusable' => true]);
    }

    public function getItemOwner($options = null)
    {
        return $this->getRelated('creator', $options);
    }

    public function getVerifyingOrganisation($options = null)
    {
        return $this->getRelated('verifyingOrganisation', $options);
    }

    public function getItem($options = null)
    {
        return $this->getRelated('Item', $options);
    }

    public function getVerifyingUser($options = null)
    {
        return $this->getRelated('verifyingUser', $options);
    }

    /**
     * Marks the item approval record as approved,  changes the item status to approved, and adds the item to the global
     * vault (for all managers) of the verifiers organisation.
     *
     * NOTE  this method will cause this record to save state.
     * @return bool
     * @throws Phalcon\Exception
     */
    public function approveItem()
    {
        if ($this->getItem()->getType() == \Apprecie\Library\Items\ItemTypes::EVENT) {
            if ($this->getStatus() == \Apprecie\Library\Items\ApprovalState::APPROVED) {
                return true;
            }

            $org = Organisation::getActiveUsersOrganisation();
            $event = Event::findFirstBy('itemId', $this->getItemId());

            $event->setState(\Apprecie\Library\Items\ItemState::APPROVED);
            $event->update();

            if (!$org->addEventToVault($event)) {
                $this->appendMessageEx($org);
                $this->logActivity('Failed xx addEventToVault() inside approve method', 'messages ' . _ms($org));
                return false;
            } //@todo gh  use transaction

            $this->setStatus(\Apprecie\Library\Items\ApprovalState::APPROVED);
            $this->setVerifiedByUserId($this->getDI()->getDefault()->get('auth')->getAuthenticatedUser()->getUserId());
            $this->update();
        } else {
            throw new \Phalcon\Exception('approveItem work flow not implemented for non events');
        }
    }

    public function beforeUpdate()
    {
        $this->setLastProcessed(date('Y-m-d G:i:s'));
        parent::beforeUpdate();
    }

    public function beforeCreate()
    {
        $this->setLastProcessed(date('Y-m-d G:i:s'));
        parent::beforeCreate();
    }

    /**
     * Marks the approval record as denied, marks the items as denied, and updates rejection reason on both records.
     *
     * NOTE  this method will cause this record to save state.
     * @param $reason
     * @return bool
     */
    public function denyItem($reason)
    {
        $item = $this->getItem();

        $item->setState(\Apprecie\Library\Items\ItemState::DENIED);
        $item->setRejectionReason($reason);
        $item->update();

        $this->setStatus(\Apprecie\Library\Items\ApprovalState::DENIED);
        $this->setDeniedReason($reason);
        $this->setVerifiedByUserId($this->getDI()->getDefault()->get('auth')->getAuthenticatedUser()->getUserId());
        return $this->update();
    }

    public static function findItemsRequiringVerificationByOrganisation($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        return Item::query()
            ->join('ItemApproval')
            ->where('status=:0:')
            ->andWhere('verifyingOrganisationId=:1:')
            ->bind([0 => \Apprecie\Library\Items\ApprovalState::PENDING, 1 => $organisation->getOrganisationId()])
            ->execute();
    }

    public static function getNeedApprovalCountForOrg($organisation = null, $includeConfirmed = true, $includeByArrangement = true)
    {
        if($organisation == null) {
            $organisation = Organisation::getActiveUsersOrganisation();
        }

        $organisation = Organisation::resolve($organisation);

        $items = Item::query()
            ->join('ItemApproval')
            ->where('status=:0: OR status=:1: OR status = :2:')
            ->andWhere('state=:3:')
            ->andWhere('verifyingOrganisationId=:4:')
            ->andWhere('type="event"');


        if($includeByArrangement == true && !$includeConfirmed) {
            $items->andWhere('isByArrangement=1');
        } elseif($includeConfirmed == true && ! $includeByArrangement) {
            $items->andWhere('isByArrangement=0');
        }

        $items->bind([
            0 => \Apprecie\Library\Items\ApprovalState::PENDING,
            1 => \Apprecie\Library\Items\ApprovalState::DENIED,
            2 => \Apprecie\Library\Items\ApprovalState::UNPUBLISHED,
            3 => \Apprecie\Library\Items\ItemState::APPROVING,
            4 => $organisation->getOrganisationId()
        ]);

        $result = $items->execute();

        return $result->count();
    }

    public static function findItemsApprovedByOrganisation($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        return Item::query()
            ->join('ItemApproval')
            ->where('status=:0:')
            ->andWhere('verifyingOrganisationId=:1:')
            ->bind([0 => \Apprecie\Library\Items\ApprovalState::APPROVED, 1 => $organisation->getOrganisationId()])
            ->execute();
    }

    public static function findItemsDeniedByOrganisation($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        return Item::query()
            ->join('ItemApproval')
            ->where('status=:0:')
            ->andWhere('verifyingOrganisationId=:1:')
            ->bind([0 => \Apprecie\Library\Items\ApprovalState::DENIED, 1 => $organisation->getOrganisationId()])
            ->execute();
    }

    public static function findItemsVerifiedByUser($user)
    {
        $user = User::resolve($user);

        return User::query()
            ->join('ItemApproval')
            ->where('verifyingUserId=:0:')
            ->bind($user->getUserId())
            ->execute();
    }

    public static function findItemsDeniedForOrganisation($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        return Item::query()
            ->join('ItemApproval')
            ->where('status=:0:')
            ->andWhere('creatingOrganisationId=:1:')
            ->bind([0 => \Apprecie\Library\Items\ApprovalState::DENIED, 1 => $organisation->getOrganisationId()])
            ->execute();
    }
} 