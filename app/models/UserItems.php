<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 18/12/14
 * Time: 10:00
 */

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
class UserItems extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $itemId, $userId, $organisationId, $unitsAvailable, $state, $orderItemId, $reservationEnd, $holdEnd, $originalUnits, $reservation1dayNoticeSent, $reservation3dayNoticeSent;

    /**
     * @param mixed $reservation1dayNoticeSent
     */
    public function setReservation1dayNoticeSent($reservation1dayNoticeSent)
    {
        $this->reservation1dayNoticeSent = $reservation1dayNoticeSent;
    }

    /**
     * @return mixed
     */
    public function getReservation1dayNoticeSent()
    {
        return $this->reservation1dayNoticeSent;
    }

    /**
     * @param mixed $reservation3dayNoticeSent
     */
    public function setReservation3dayNoticeSent($reservation3dayNoticeSent)
    {
        $this->reservation3dayNoticeSent = $reservation3dayNoticeSent;
    }

    /**
     * @return mixed
     */
    public function getReservation3dayNoticeSent()
    {
        return $this->reservation3dayNoticeSent;
    }

    /**
     * @param mixed $originalUnits
     */
    public function setOriginalUnits($originalUnits)
    {
        $this->originalUnits = $originalUnits;
    }

    /**
     * @return mixed
     */
    public function getOriginalUnits()
    {
        return $this->originalUnits;
    }

    /**
     * @param mixed $holdEnd
     */
    public function setHoldEnd($holdEnd)
    {
        $this->holdEnd = $holdEnd;
    }

    /**
     * @return mixed
     */
    public function getHoldEnd()
    {
        return $this->holdEnd;
    }

    /**
     * @param mixed $reservationEnd
     */
    public function setReservationEnd($reservationEnd)
    {
        $this->reservationEnd = $reservationEnd;
    }

    /**
     * @return mixed
     */
    public function getReservationEnd()
    {
        return $this->reservationEnd;
    }

    /**
     * @param mixed $orderItemId
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;
    }

    /**
     * @return mixed
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
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
     * @param $organisationId
     */
    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    /**
     * @param mixed $unitsAvailable
     */
    public function setUnitsAvailable($unitsAvailable)
    {
        $this->unitsAvailable = $unitsAvailable;
    }

    /**
     * @return mixed
     */
    public function getUnitsAvailable()
    {
        return $this->unitsAvailable;
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

    public function getItem($options = null)
    {
        return $this->getRelated('Item', $options);
    }

    public function getSource()
    {
        return 'useritems';
    }

    public function getUser($options = null)
    {
        return $this->getRelated('User', $options);
    }

    public function initialize()
    {
        $this->hasOne('itemId', 'Item', 'itemId');
        $this->hasOne('userId', 'User', 'userId');
        $this->hasOne('orderItemId', 'OrderItems', 'orderItemId');
    }

    /**
     * @return OrderItems
     */
    public function getOrderItem($options = null)
    {
        return $this->getRelated('OrderItems', $options);
    }

    public function getSourceOrder()
    {
        return $this->getOrderItem()->getOrder();
    }

    public static function getUserItemsOfStatus($user, $state)
    {
        $filter = new \Apprecie\Library\Search\SearchFilter('UserItems');
        $filter->addAndEqualFilter('state', $state)
            ->addAndEqualFilter('userId', $user->getUserId());


        return UserItems::findByFilter($filter);
    }

    public static function sendReservationWarnings()
    {
        //3 day and 1 day warnings
        $filter = new \Apprecie\Library\Search\SearchFilter('UserItems');
        $filter->addAndEqualFilter('state', \Apprecie\Library\Items\UserItemState::RESERVED)
            ->addAndEqualOrGreaterThanFilter('reservationEnd', date("Y-m-d H:i:s", time() - (86400 * 3)))
            ->addAndIsNullFilter('reservation3dayNoticeSent')
            ->addAndIsNotNullFilter('reservationEnd');

        $endingReserved = UserItems::findByFilter($filter);

        $oneDayWarning = date("Y-m-d H:i:s", time() - (86400));
        $threeDayWarning = date("Y-m-d H:i:s", time() - (86400 * 3));

        $notice = new \Apprecie\Library\Messaging\Notification();

        foreach ($endingReserved as $item) {
            $user = $item->getUser();
            $stockItem = $item->getItem();

            if (strtotime($item->getReservationEnd()) < $oneDayWarning && $item->getReservation1dayNoticeSent() == null
            ) {
                $notice->addNotification
                    (
                        $user->getUserId(),
                        _g('Your reservation expires in 1 day'),
                        _g(
                            'Your reservation for {item} will expire in {days} day.  Please make payment',
                            ['item' => $stockItem->getTitle(), 'days' => 1]
                        )

                    );

                $item->setReservation1dayNoticeSent(true);
            } elseif (strtotime($item->getReservationEnd()) < $threeDayWarning && $item->getReservation1dayNoticeSent()
            ) {
                $notice->addNotification(
                    $user->getUserId(),
                    _g('Your reservation expires in 3 days'),
                    _g(
                        'Your reservation for {item} will expire in {days} days.  Please make payment',
                        ['item' => $stockItem->getTitle(), 'days' => 3]
                    )
                );
                $item->setReservation3dayNoticeSent(true);
            }

            if (!$item->update()) {
                $log = new ActivityLog();
                $log->logActivity(
                    'Failed to save user item',
                    'Failed to save reservation warning state against a user item.  Notices will continue to be generated'
                );
            }
        }
    }

    public static function lapseHeldAndReserved()
    {
        //held for 10 mins,  reservation on reservation date
        $filter = new \Apprecie\Library\Search\SearchFilter('UserItems');
        $filter->addAndEqualFilter('state', \Apprecie\Library\Items\UserItemState::RESERVED)
            ->addAndEqualOrLessThanFilter('reservationEnd', date("Y-m-d H:i:s"))
            ->addAndIsNotNullFilter('reservationEnd');

        $lapsedReserved = UserItems::findByFilter($filter);

        $filter = new \Apprecie\Library\Search\SearchFilter('UserItems');

        $filter->addAndEqualFilter('state', \Apprecie\Library\Items\UserItemState::HELD)
            ->addAndEqualOrLessThanFilter('holdEnd', date("Y-m-d H:i:s"))
            ->addAndIsNotNullFilter('holdEnd');

        $lapsedHeld = UserItems::findByFilter($filter);
        $log = new ActivityLog();
        $notice = new \Apprecie\Library\Messaging\Notification();

        foreach ($lapsedHeld as $items) {
            $orderItem = OrderItems::resolve($items->getOrderItemId(), false);
            $order = $orderItem->getOrder();
            $user = $order->getCustomer();

            if ($items->delete()) {
                $log->logActivity(
                    'Released held items',
                    ' The item ' . $items->getItemId() . ' held for user ' . $items->getUserId(
                    ) . ' was returned to the market due to time lapse'
                );
            } else {
                $log->logActivity(
                    'Failed to release held items',
                    ' The item ' . $items->getItemId() . ' held for user ' . $items->getUserId(
                    ) . ' met time lapse but release failed : ' . _ms($items)
                );
            }

            if ($orderItem) {
                $orderItem->getOrder()->cancelOrder(
                    _g('The hold on this item has lapsed and your order has been cancelled')
                );
                $url = \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                    $user->getPortalId(),
                    'orders',
                    'order',
                    [$order->getOrderId()]
                );
                $notice->addNotification(
                    $items->getUserId(),
                    _g('An order has been cancelled'),
                    'We held items for one of your orders and the hold period has now lapsed.  The order has been cancelled and the items returned to the market.  You are welcome to start another order.',
                    $url
                );
            }
        }

        foreach ($lapsedReserved as $items) {
            if ($items->delete()) {
                $log->logActivity(
                    'Released reserved items',
                    ' The item ' . $items->getItemId() . ' reserved for user ' . $items->getUserId(
                    ) . ' was returned to the market due to time lapse'
                );
                $orderItem = OrderItems::resolve($items->getOrderItemId(), false);
                $order = $orderItem->getOrder();
                $conversionOrder = OrderItems::findBy('fullConversionOfOrderItemId', $orderItem->getOrderItemId());
                $user = $order->getCustomer();

                $url = \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                    $user->getPortalId(),
                    'vault',
                    'event',
                    [$items->getItemId()]
                );
                $notice->addNotification(
                    $items->getUserId(),
                    _g('A reservation has lapsed'),
                    _g(
                        'Your reservation of the event {event}, has expired.  The reserved units have been returned to the market',
                        ['event' => _eh($items->getItem()->getTitle())]
                    ),
                    $url
                );

                if ($conversionOrder->count() > 0) {
                    foreach ($conversionOrder as $item) {
                        $item->getOrder()->cancelOrder(
                            _g('The reservation on this item has lapsed and your order has been cancelled')
                        );
                        $url = \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                            $user->getPortalId(),
                            'orders',
                            'order',
                            [$order->getOrderId()]
                        );
                        $notice->addNotification(
                            $items->getUserId(),
                            _g('An order has been cancelled'),
                            'We reserved items for one of your orders and the reservation period has now lapsed.  The order has been cancelled and the items returned to the market.  You are welcome to start another order.',
                            $url
                        );

                    }
                }

            } else {
                $log->logActivity(
                    'Failed to release reserved items',
                    ' The item ' . $items->getItemId() . ' reserved for user ' . $items->getUserId(
                    ) . ' met time lapse but release failed : ' . _ms($items)
                );
            }
        }
    }

    public static function findDistinctByUser($user)
    {
        $user = User::resolve($user);

        $sql = "SELECT DISTINCT(itemId) FROM useritems WHERE userId = ?";
        $items = new UserItems();
        return new \Phalcon\Mvc\Model\Resultset\Simple(null, $items, $items->getReadConnection()->query(
            $sql,
            [$user->getUserId()]
        ));
    }

    public static function getTotalAvailableUnits($userId, $itemId, $state)
    {
        $instance = new UserItems();
        $allocatedUnitRecords = $instance->getModelsManager()->executeQuery(
            "SELECT SUM(unitsAvailable) as units FROM UserItems WHERE userId = :userid: AND itemId = :itemid: AND state = :state:",
            ['userid' => $userId, 'itemid' => $itemId, 'state' => $state]
        )->getFirst();
        $allocatedUnits = $allocatedUnitRecords['units'];

        return $allocatedUnits;
    }

    public static function getTotalOwnedUnits($userId, $itemId)
    {
        $instance = new UserItems();
        $allocatedUnitRecords = $instance->getModelsManager()->executeQuery(
            "SELECT SUM(originalUnits) as units FROM UserItems WHERE userId = :userid: AND itemId = :itemid: AND state = 'owned'",
            ['userid' => $userId, 'itemid' => $itemId]
        )->getFirst();
        $allocatedUnits = $allocatedUnitRecords['units'];

        return $allocatedUnits;
    }

    public static function getBy($user, $item, $state)
    {
        $item = Item::resolve($item);
        $user = User::resolve($user);

        $filter = new \Apprecie\Library\Search\SearchFilter('UserItems');

        $filter->addAndEqualFilter('userId', $user->getUserId())
            ->addAndEqualFilter('itemId', $item->getItemId())
            ->addInFilter('state', $state);

        return UserItems::findByFilter($filter);
    }

    /**
     * return true if the unit was deallocated, else returns false
     *
     * @param $itemId
     * @param $userId
     * @param null $transaction
     * @param int $units
     * @return bool
     */
    public static function consumeUnit($itemId, $userId, $transaction = null, $units = 1)
    {
        //_ep('registered lock');
        $filter = new \Apprecie\Library\Search\SearchFilter('UserItems');

        $filter->addAndEqualFilter('state', \Apprecie\Library\Items\UserItemState::OWNED)
            ->addAndEqualFilter('userId', $userId)
            ->addAndEqualFilter('itemId', $itemId)
            ->addAndEqualOrGreaterThanFilter('unitsAvailable', $units);

        $lock = new \Apprecie\Library\CriticalSection\Lock('unitConsumeCritical');
        $lock->getLock(20);

        $userItemRecords = \UserItems::findByFilter($filter, 'orderItemId');

        if ($userItemRecords->count() == 0) {
            $lock->releaseLock();
            return false;
        }

        $firstRecord = $userItemRecords->getFirst();

        $localTrans = null;

        if($transaction != null) {
            $firstRecord->setTransaction($transaction);
        }

        $firstRecord->setUnitsAvailable($firstRecord->getUnitsAvailable() - $units);

        if (!$firstRecord->update()) {
            $lock->releaseLock();
            $log = new ActivityLog();
            $log->logActivity('Failed to consume user item unit', _ms($firstRecord));
            return false;
        }

        $lock->releaseLock();
        return true;
    }

    public static function creditUnit($itemId, $userId, $transaction = null, $units = 1)
    {
        $filter = new \Apprecie\Library\Search\SearchFilter('UserItems');

        $filter->addAndEqualFilter('state', \Apprecie\Library\Items\UserItemState::OWNED)
            ->addAndEqualFilter('userId', $userId)
            ->addAndEqualFilter('itemId', $itemId);

        $userItemRecords = \UserItems::findByFilter($filter);

        if ($userItemRecords->count() == 0) {
            return false;
        }

        $firstRecord = $userItemRecords->getFirst();

        if ($transaction) {
            $firstRecord->setTransaction($transaction);
        }

        $firstRecord->setUnitsAvailable($firstRecord->getUnitsAvailable() + $units);

        if (!$firstRecord->update()) {
            $log = new ActivityLog();
            $log->logActivity('Failed to credit user item unit', _ms($firstRecord));
            return false;
        }

        return true;
    }
} 