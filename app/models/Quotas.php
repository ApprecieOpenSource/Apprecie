<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/12/14
 * Time: 18:51
 */
class Quotas extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $portalId, $portalAdministratorTotal, $managerTotal, $internalMemberTotal, $apprecieSupplierTotal,
        $affiliateSupplierTotal, $memberTotal, $memberFamilyTotal, $commissionPercent,
        $lastTenancyPaidAmount, $tenancyEnd, $portalAdministratorUsed, $managerUsed,
        $internalMemberUsed, $apprecieSupplierUsed, $affiliateSupplierUsed, $memberUsed, $familyMemberUsed, $organisationId;

    public function setAffiliateSupplierUsed($quantity)
    {
        if (!is_int($quantity)) {
            throw new InvalidArgumentException('Quantity is expected to be an integer');
        }

        if ($quantity > $this->getAffiliateSupplierTotal()) {
            $this->appendMessageEx(
                new \Phalcon\Mvc\Model\Message('Cannot set new figure as would be higher then current quota')
            );
            return false;
        }

        $this->affiliateSupplierUsed = $quantity > 0 ? $quantity : 0;
        return true;
    }

    public function consumeAffiliateSupplierQuota($quantity = 1)
    {
        if (!is_int($quantity)) {
            throw new InvalidArgumentException('Quantity is expected to be an integer');
        }

        return $this->setAffiliateSupplierUsed($this->getAffiliateSupplierUsed() + $quantity);
    }

    /**
     * @return mixed
     */
    public function getAffiliateSupplierUsed()
    {
        return $this->affiliateSupplierUsed;
    }


    public function setApprecieSupplierUsed($quantity)
    {
        if ($quantity > $this->getApprecieSupplierTotal()) {
            $this->appendMessageEx(
                new \Phalcon\Mvc\Model\Message('Cannot set new figure as would be higher then current quota')
            );
            return false;
        }

        $this->apprecieSupplierUsed = $quantity > 0 ? $quantity : 0;
        return true;
    }

    public function consumeApprecieSupplierQuota($quantity = 1)
    {
        if (!is_int($quantity)) {
            throw new InvalidArgumentException('Quantity is expected to be an integer');
        }

        return $this->setApprecieSupplierUsed($this->getApprecieSupplierUsed() + $quantity);
    }

    /**
     * @return mixed
     */
    public function getApprecieSupplierUsed()
    {
        return $this->apprecieSupplierUsed;
    }

    public function setInternalMemberUsed($quantity)
    {
        if ($quantity > $this->getInternalMemberTotal()) {
            $this->appendMessageEx(
                new \Phalcon\Mvc\Model\Message('Cannot set new figure as would be higher then current quota')
            );
            return false;
        }

        $this->internalMemberUsed = $quantity > 0 ? $quantity : 0;
        return true;
    }

    public function consumeInternalMemberQuota($quantity = 1)
    {
        if (!is_int($quantity)) {
            throw new InvalidArgumentException('Quantity is expected to be an integer');
        }

        return $this->setInternalMemberUsed($this->getInternalMemberUsed() + $quantity);
    }


    /**
     * @return mixed
     */
    public function getInternalMemberUsed()
    {
        return $this->internalMemberUsed;
    }

    public function setManagerUsed($quantity)
    {
        if ($quantity > $this->getManagerTotal()) {
            $this->appendMessageEx(
                new \Phalcon\Mvc\Model\Message('Cannot set new figure as would be higher then current quota')
            );
            return false;
        }

        $this->managerUsed = $quantity > 0 ? $quantity : 0;
        return true;
    }

    public function consumeManagerQuota($quantity = 1)
    {
        if (!is_int($quantity)) {
            throw new InvalidArgumentException('Quantity is expected to be an integer');
        }

        return $this->setManagerUsed($this->getManagerUsed() + $quantity);
    }

    /**
     * @return mixed
     */
    public function getManagerUsed()
    {
        return $this->managerUsed;
    }

    public function setFamilyMemberUsed($quantity)
    {
        if ($quantity > $this->getMemberFamilyTotal()) {
            $this->appendMessageEx(
                new \Phalcon\Mvc\Model\Message('Cannot set new figure as would be higher then current quota')
            );
            return false;
        }

        $this->familyMemberUsed = $quantity > 0 ? $quantity : 0;
        return true;
    }

    public function consumeMemberFamilyQuota($quantity = 1)
    {
        if (!is_int($quantity)) {
            throw new InvalidArgumentException('Quantity is expected to be an integer');
        }

        return $this->setFamilyMemberUsed($this->getFamilyMemberUsed() + $quantity);
    }

    /**
     * @return mixed
     */
    public function getFamilyMemberUsed()
    {
        return $this->familyMemberUsed;
    }

    public function setMemberUsed($quantity)
    {
        if ($quantity > $this->getMemberTotal()) {
            $this->appendMessageEx(
                new \Phalcon\Mvc\Model\Message('Cannot set new figure as would be higher then current quota')
            );
            return false;
        }

        $this->memberUsed = $quantity > 0 ? $quantity : 0;
        return true;
    }

    public function consumeMemberQuota($quantity = 1)
    {
        if (!is_int($quantity)) {
            throw new InvalidArgumentException('Quantity is expected to be an integer');
        }

        return $this->setMemberUsed($this->getMemberUsed() + $quantity);
    }

    /**
     * @return mixed
     */
    public function getMemberUsed()
    {
        return $this->memberUsed;
    }

    public function setPortalAdministratorUsed($quantity)
    {
        if ($quantity > $this->getPortalAdministratorTotal()) {
            $this->appendMessageEx(
                new \Phalcon\Mvc\Model\Message('Cannot set new figure as would be higher then current quota')
            );
            return false;
        }

        $this->portalAdministratorUsed = $quantity;
        return true;
    }

    public function consumePortalAdministratorQuota($quantity = 1)
    {
        if (!is_int($quantity)) {
            throw new InvalidArgumentException('Quantity is expected to be an integer');
        }

        return $this->setPortalAdministratorUsed($this->getPortalAdministratorUsed() + $quantity);
    }

    /**
     * @return mixed
     */
    public function getPortalAdministratorUsed()
    {
        return $this->portalAdministratorUsed;
    }


    /**
     * @param $adminTotal
     * @param bool|false $preventOverSubscription
     * @return bool
     */
    public function setPortalAdminTotal($adminTotal, $preventOverSubscription = false)
    {
        if($preventOverSubscription) {
            if($adminTotal < $this->getPortalAdministratorUsed()) {
                $this->appendMessageEx(
                    new \Phalcon\Mvc\Model\Message('You cannot set the quota below the current number of assigned licenses')
                );
                return false;
            }
        }

        $this->portalAdministratorTotal = $adminTotal;
        return true;
    }

    /**
     * @return mixed
     */
    public function getPortalAdministratorTotal()
    {
        return $this->portalAdministratorTotal;
    }

    public function setAffiliateSupplierTotal($affiliateSupplierTotal, $preventOverSubscription = false)
    {
        if($preventOverSubscription) {
            if($affiliateSupplierTotal < $this->getAffiliateSupplierUsed()) {
                $this->appendMessageEx(
                    new \Phalcon\Mvc\Model\Message('You cannot set the quota below the current number of assigned licenses')
                );
                return false;
            }
        }

        $this->affiliateSupplierTotal = $affiliateSupplierTotal;
        return true;
    }

    /**
     * @return mixed
     */
    public function getAffiliateSupplierTotal()
    {
        return $this->affiliateSupplierTotal;
    }

    /**
     * @param $apprecieSupplierTotal
     * @param bool|false $preventOverSubscription
     * @return bool
     */
    public function setApprecieSupplierTotal($apprecieSupplierTotal, $preventOverSubscription = false)
    {
        if($preventOverSubscription) {
            if($apprecieSupplierTotal < $this->getApprecieSupplierUsed()) {
                $this->appendMessageEx(
                    new \Phalcon\Mvc\Model\Message('You cannot set the quota below the current number of assigned licenses')
                );
                return false;
            }
        }

        $this->apprecieSupplierTotal = $apprecieSupplierTotal;
        return true;
    }

    /**
     * @return mixed
     */
    public function getApprecieSupplierTotal()
    {
        return $this->apprecieSupplierTotal;
    }

    /**
     * @param mixed $commissionPercent
     */
    public function setCommissionPercent($commissionPercent)
    {
        $this->commissionPercent = $commissionPercent;
    }

    /**
     * @return mixed
     */
    public function getCommissionPercent()
    {
        return $this->commissionPercent;
    }

    /**
     * @param $internalMemberTotal
     * @param bool|false $preventOverSubscription
     * @return bool
     */
    public function setInternalMemberTotal($internalMemberTotal, $preventOverSubscription = false)
    {
        if($preventOverSubscription) {
            if($internalMemberTotal < $this->getInternalMemberUsed()) {
                $this->appendMessageEx(
                    new \Phalcon\Mvc\Model\Message('You cannot set the quota below the current number of assigned licenses')
                );
                return false;
            }
        }

        $this->internalMemberTotal = $internalMemberTotal;
        return true;
    }

    /**
     * @return mixed
     */
    public function getInternalMemberTotal()
    {
        return $this->internalMemberTotal;
    }

    /**
     * @param mixed $lastTenancyPaidAmount
     */
    public function setLastTenancyPaidAmount($lastTenancyPaidAmount)
    {
        $this->lastTenancyPaidAmount = $lastTenancyPaidAmount;
    }

    /**
     * @return mixed
     */
    public function getLastTenancyPaidAmount()
    {
        return $this->lastTenancyPaidAmount;
    }

    /**
     * @param $managerTotal
     * @param bool|false $preventOverSubscription
     * @return bool
     */
    public function setManagerTotal($managerTotal, $preventOverSubscription = false)
    {
        if($preventOverSubscription) {
            if($managerTotal < $this->getManagerUsed()) {
                $this->appendMessageEx(
                    new \Phalcon\Mvc\Model\Message('You cannot set the quota below the current number of assigned licenses')
                );
                return false;
            }
        }

        $this->managerTotal = $managerTotal;
        return true;
    }

    /**
     * @return mixed
     */
    public function getManagerTotal()
    {
        return $this->managerTotal;
    }

    /**
     * @param mixed $memberFamilyTotal
     */
    public function setMemberFamilyTotal($memberFamilyTotal)
    {
        $this->memberFamilyTotal = $memberFamilyTotal;
    }

    /**
     * @return mixed
     */
    public function getMemberFamilyTotal()
    {
        return $this->memberFamilyTotal;
    }

    /**
     * @param $memberTotal
     * @param bool|false $preventOverSubscription
     * @return bool
     */
    public function setMemberTotal($memberTotal, $preventOverSubscription = false)
    {
        if($preventOverSubscription) {
            if($memberTotal < $this->getMemberUsed()) {
                $this->appendMessageEx(
                    new \Phalcon\Mvc\Model\Message('You cannot set the quota below the current number of assigned licenses')
                );
                return false;
            }
        }

        $this->memberTotal = $memberTotal;
        return true;
    }

    /**
     * @return mixed
     */
    public function getMemberTotal()
    {
        return $this->memberTotal;
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
     * @param mixed $tenancyEnd
     */
    public function setTenancyEnd($tenancyEnd)
    {
        $this->tenancyEnd = $tenancyEnd;
    }

    /**
     * @return mixed
     */
    public function getTenancyEnd()
    {
        return $this->tenancyEnd;
    }

    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;
    }

    public function getOrganisation($options = null)
    {
        return $this->getRelated('organisation', $options);
    }

    public function getSource()
    {
        return 'quotas';
    }

    public function initialize()
    {
        $this->setDefaultFields(
            [
                'portalAdministratorTotal',
                'managerTotal',
                'internalMemberTotal',
                'apprecieSupplierTotal',
                'affiliateSupplierTotal',
                'memberTotal',
                'memberFamilyTotal',
                'commissionPercent',
                'lastTenancyPaidAmount',
                'tenancyEnd',
                'portalAdministratorUsed',
                'managerUsed',
                'internalMemberUsed',
                'apprecieSupplierUsed',
                'affiliateSupplierUsed',
                'memberUsed',
                'familyMemberUsed'
            ]
        );
        $this->belongsTo('portalId', 'Portal', 'portalId');
        $this->hasOne('organisationId', 'organisation', 'organisationId');
    }

    public function delete()
    {
        throw new LogicException('Do not delete me.  Delete the Portal or organisation and the DB will cascade');
    }
} 