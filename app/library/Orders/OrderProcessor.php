<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 17/03/15
 * Time: 09:26
 */

namespace Apprecie\Library\Orders;

use Apprecie\Library\Items\EventStatus;
use Apprecie\Library\Items\UserItemState;
use Apprecie\Library\Messaging\Notification;
use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Request\Url;
use Apprecie\Library\Search\SearchFilter;
use Apprecie\Library\Tracing\ActivityTraceTrait;
use Phalcon\Mvc\Model\Transaction\Manager;

class OrderProcessor extends PrivateMessageQueue
{
    use ActivityTraceTrait;

    /**
     * This method will create an order based on a existing order item, that is for a reservation, and will
     * create the
     * @param $orderItem
     * @return bool
     */
    public function buyReservedInFull($orderItem)
    {
        $orderItem = \OrderItems::resolve($orderItem);

        if (($itemRecord = $orderItem->getUserItemsRecord()) != null) { //check we have a valid reserved items record
            if ($itemRecord->getState() == UserItemState::RESERVED) {
                $conversionOrderItem = \OrderItems::findFirstBy(
                    'fullConversionOfOrderItemId',
                    $orderItem->getOrderItemId()
                );
                if ($conversionOrderItem == null) { //no existing order
                    return $this->buyItem(
                        $itemRecord->getItem(),
                        $orderItem->getPackageQuantity(),
                        null,
                        false,
                        $orderItem
                    );
                } else {
                    return $conversionOrderItem->getOrder()->getOrderId();
                }
            } else {
                $this->appendMessageEx(_g('The suggested item is not in a reserved state'));
            }
        } else {
            $this->appendMessageEx(
                _g(
                    'No unit record was found that is associated with this order.  If the items were reserved they have expired or paid in full.'
                )
            );
        }

        return false;
    }

    /**
     * @param $item
     * @param int $packageQuantity
     * @param null $existingOrder to add an item to an order.
     * @param bool $reserve
     * @param null $conversionOfOrderItem
     * @param bool $selfConsume
     * @return bool|mixed
     */
    public function buyItem(
        $item,
        $packageQuantity = 1,
        $existingOrder = null,
        $reserve = false,
        $conversionOfOrderItem = null,
        $selfConsume = false
    ) {
        $item = \Item::resolve($item); //@todo  pay balance on reservation / free items /  other purchase requirements

        if ($packageQuantity > $item->getRemainingPackages() && $conversionOfOrderItem == null) {
            $this->appendMessageEx(_g('There are not enough packages available'));
            $this->logActivity(
                'Not enough units for order',
                'The item ' . $item->getItemId(
                ) . ' was requested for an order of quantity ' . $packageQuantity . ' but only ' . $item->getRemainingPackages(
                ) . ' packages are available'
            );
            return false;
        } elseif ($reserve && ($item->getReservationFee() == null || $item->getReservationLength() < 1)) {
            throw new \LogicException('This item cannot be reserved it has incomplete reservation details');
        } elseif ($reserve && (time() + (86400 * $item->getReservationLength()) > strtotime(
                    $item->getEvent()->getBookingEndDate()
                ))
        ) {
            $this->appendMessageEx(
                _g('I cannot reserve this item as the reservation period would extend beyond the booking end date')
            );
        }

        if ($conversionOfOrderItem != null) {
            $conversionOfOrderItem = \OrderItems::resolve($conversionOfOrderItem);
        }

        $transaction = (new Manager())->get();

        if ($existingOrder != null) {
            $existingOrder = \Order::resolve($existingOrder);

            if ($existingOrder->getStatus() == OrderStatus::PENDING) {
                $this->appendMessageEx('It is not possible to add to this order as it has already started processing');
                return false;
            }

            $order = $existingOrder;
            $order->setTransaction($transaction);

            //refresh all held items - so all items have 10 minutes again
            $existingItems = $order->getOrderItems();
            foreach ($existingItems as $itm) {
                $this->addItemForUser($itm, UserItemState::HELD, $transaction);
            }
        } else {
            $order = new \Order();
            $order->setCustomerId($this->getDI()->getDefault()->get('auth')->getAuthenticatedUser()->getUserId());
            $order->setStatus(OrderStatus::PENDING);
            $order->setSupplierId($item->getCreatorId());
            $order->setStatusReason(_g('Order created'));
            $order->setCurrencyId($item->getCurrencyId());
            $order->setTransaction($transaction);

            if (!$order->create()) {
                $this->appendMessageEx($order);
            }
        }

        if (!$this->hasMessages()) {
            $orderItem = new \OrderItems();
            $orderItem->setTransaction($transaction);
            $orderItem->setOrderId($order->getOrderId());
            $orderItem->setItemId($item->getItemId());
            $orderItem->setUserId($this->getDI()->getDefault()->get('auth')->getAuthenticatedUser()->getUserId());
            $orderItem->setPortalId($this->getDI()->getDefault()->get('portal')->getPortalId());
            $orderItem->setOrganisationId(\Organisation::getActiveUsersOrganisation()->getOrganisationId());
            $orderItem->setPackageQuantity($packageQuantity);
            $orderItem->setPackageSize($item->getPackageSize());

            if ($reserve) { // reserve item
                $orderItem->setStatus(OrderItemStatus::RESERVATION);
                $orderItem->setReservationAmount(ceil($item->getReservationFee() * $packageQuantity));
                $orderItem->setValue($orderItem->getReservationAmount());
                $orderItem->setReservationExpire(
                    date('Y-m-d H:i:s', time() + (86400 * $orderItem->getItem()->getReservationLength()))
                );
                $orderItem->setAdminFee(0);
                $orderItem->setDescription(
                    _g(
                        '{reservationPeriod} Day reservation for {quantity} packages of {packageSize} of {item}',
                        [
                            'reservationPeriod' => $item->getReservationLength(),
                            'quantity' => $packageQuantity,
                            'item' => _eh($item->getTitle()),
                            'packageSize' => $item->getPackageSize()
                        ]
                    )
                );
                $orderItem->setCommissionAmount(0);
            } elseif ($conversionOfOrderItem != null) { //upgrade reserved to full
                $orderItem->setStatus(OrderItemStatus::FULL);
                $orderItem->setValue(ceil($item->getUnitPrice() * $packageQuantity));
                $orderItem->setDescription(
                    _g(
                        'Full purchase : {quantity} packages of {packageSize} reserved item : {item}',
                        [
                            'quantity' => $packageQuantity,
                            'item' => $item->getTitle(),
                            'packageSize' => $item->getPackageSize()
                        ]
                    )
                );
                $orderItem->setFullConversionOfOrderItemId($conversionOfOrderItem->getOrderItemId());
                $orderItem->setReservationAmount(0);
                $orderItem->setAdminFee(0);
            } else { // buy full outright
                $orderItem->setStatus(OrderItemStatus::FULL);

                if($selfConsume) {
                    $orderItem->setValue(0);
                } else {
                    $orderItem->setValue(ceil($item->getUnitPrice() * $packageQuantity));
                }

                $orderItem->setDescription(
                    _g(
                        'Full purchase : {quantity} packages of {packageSize} of {item}',
                        [
                            'quantity' => $packageQuantity,
                            'item' => $item->getTitle(),
                            'packageSize' => $item->getPackageSize()
                        ]
                    )
                );

                if ($item->getAdminFee() != null && ! $selfConsume) {
                    $orderItem->setAdminFee(ceil($item->getAdminFee() * $packageQuantity));
                }
            }

            if ($orderItem->getValue() > 0) {
                if(! $reserve) {
                    $orderItem->setCommissionAmount(ceil(($orderItem->getValue() / 100) * $item->getCommissionAmount()));
                }

                if ($item->getTaxablePercent() > 0) {
                    $orderItem->setTax(
                        ceil
                        (
                                (
                                    ($orderItem->getValue() + $orderItem->getAdminFee())
                                    / 100
                                )
                                * $item->getTaxablePercent()
                        )
                    );
                }
            }

            if (!$orderItem->create()) {
                $this->appendMessageEx($orderItem);
            }
        }

        if (!$this->hasMessages() && $conversionOfOrderItem == null) {
            $this->addItemForUser(
                $orderItem,
                UserItemState::HELD,
                $transaction
            ); //dont add conversion units, they are already present
        }

        try {
            $this->hasMessages() ? $transaction->rollback() : $transaction->commit();
        } catch (\Exception $ex) {
            $this->appendMessageEx($ex);
        }

        if ($this->hasMessages()) {
            $this->logActivity('Could not add item to order', $this->getMessagesString());
            return false;
        }

        return $order->getOrderId();
    }

    public function getPendingOrders($user = null)
    {
        if ($user == null) {
            $user = $this->getDI()->getDefault()->get('auth')->getAuthenticatedUser();
        } else {
            $user = \User::resolve($user);
        }

        $filter = new SearchFilter('Order');
        $filter->addAndEqualFilter('customerId', $user->getUserId())
            ->addAndEqualFilter('status', OrderStatus::PENDING);

        return $filter->execute();
    }

    public function cancelPendingOrder($order)
    {
        $order = \Order::resolve($order);

        if (!$order->getStatus() == OrderStatus::PENDING) {
            throw new \LogicException('This order cannot be cancelled.  Only pending orders can be cancelled.');
        }

        $order->setStatus(OrderStatus::CANCELLED);

        if (!$order->update()) {
            $this->appendMessageEx($order);
            return false;
        }

        return true;
    }

    public function completeOrder($order)
    {
        $order = \Order::resolve($order);

        if ($order->getStatus() != OrderStatus::PROCESSING) {
            throw new \LogicException('I can only complete an order that is processing');
        }

        $items = $order->getOrderItems();

        $transaction = (new Manager())->get();
        $order->setTransaction($transaction);

        foreach ($items as $item) {
            $item->setTransaction($transaction);
            $unitsStatus = UserItemState::RESERVED;

            if ($item->getStatus() == OrderItemStatus::RESERVATION) {
                $item->setIsReserved(true);
            } else {
                $item->setIsPaidFull(true);
                $unitsStatus = userItemState::OWNED;

                $event = $item->getItem()->getEvent();
                $event->setTransaction($transaction);

                if ($event->getIsArranged() || $event->getPurchasedPackages() == ($event->getMaxUnits(
                        ) - $item->getPackageQuantity())
                ) {

                    $event->setStatus(EventStatus::FULLY_BOOKED);

                    if (!$event->update()) {
                        $this->appendMessageEx($event);
                    }
                }

                if ($event->getStatus() == EventStatus::FULLY_BOOKED) {
                    $notice = new Notification();

                    if (!$event->getIsArranged()) {
                        if (!$notice->addNotification
                            (
                                $event->getCreatedBy(),
                                _g('One of your events is fully booked'),
                                _g('Your event {item} is now fully booked', ['item' => _eh($event->getTitle())]),
                                Url::getConfiguredPortalAddress(
                                    $event->getCreatedBy()->getPortalId(),
                                    'mycontent',
                                    'eventmanagement',
                                    [$event->getEventId()]
                                ),
                                null,
                                true
                            )
                        ) {
                            $this->appendMessageEx($notice);
                        }
                    } else {
                        if (!$notice->addNotification
                            (
                                $event->getCreatedBy(),
                                _g('An event you arranged has now been consumed'),
                                _g(
                                    'You arranged a personalised event {item} for {person} it has now been consumed',
                                    [
                                        'item' => _eh($event->getTitle()),
                                        'person' => $item->getCustomer()->getUserProfile()->getFullName()
                                    ]
                                ),
                                Url::getConfiguredPortalAddress(
                                    $event->getCreatedBy()->getPortalId(),
                                    'mycontent',
                                    'eventmanagement',
                                    [$event->getEventId()]
                                ),
                                null,
                                true
                            )
                        ) {
                            $this->appendMessageEx($notice);
                        }
                    }
                }
            }

            $item->setCancelled(false); //just in case has been flipped during processing

            if (!$this->addItemForUser($item, $unitsStatus, $transaction)) {
                $this->appendMessageEx(_g('Failed to process order.  Units have not been added.'));
            }

            if (!$item->update()) {
                $this->appendMessageEx($item);
            }
        }

        if (!$this->hasMessages()) {
            $order->setStatus(OrderStatus::COMPLETE);
            $order->save();
            try {
                $transaction->commit();
                $order->sendConfirmation();
            } catch (\Exception $ex) {
                $this->appendMessageEx($ex);
            }
        }

        if ($this->hasMessages()) {
            $order->setStatus(OrderStatus::ERROR);
            $this->logActivity('Failed to process order', $this->getMessages());
            $order->save();
            return false;
        }

        return true;
    }

    public function reserveItem($item, $packageQuantity = 1, $existingOrder = null)
    {
        return $this->buyItem($item, $packageQuantity, $existingOrder, true);
    }

    public function addItemForUser($orderItem, $status, $transaction = null, $reHold = false)
    {
        $orderItem = \OrderItems::resolve($orderItem);

        if ($orderItem->getFullConversionOfOrderItemId() != null) {
            $userItemRecord = \UserItems::resolve($orderItem->getFullConversionOfOrderItemId(), false);
            if ($userItemRecord == null) {
                $this->appendMessageEx('We have no record of the reserved items that this order refers to');
                return false;
            }
        } else {
            $userItemRecord = \UserItems::resolve($orderItem->getOrderItemId(), false);
        }

        if ($userItemRecord == null) {
            $userItemRecord = new \UserItems();
            $userItemRecord->setOrderItemId($orderItem->getOrderItemId());
        }

        if ($transaction != null) {
            $userItemRecord->setTransaction($transaction);
        }

        $userItemRecord->setState($status);

        if ($userItemRecord->getOriginalUnits() == 0) {
            $userItemRecord->setUnitsAvailable($orderItem->getPackageSize() * $orderItem->getPackageQuantity());
            $userItemRecord->setOriginalUnits($userItemRecord->getUnitsAvailable());
        }

        if ($reHold) {
            $userItemRecord->setState(UserItemState::HELD);
        }

        if ($status == UserItemState::HELD) {
            $userItemRecord->setHoldEnd(date('Y-m-d H:i:s', time() + 600)); //add 10 mins
        } elseif ($status == UserItemState::RESERVED) {
            $userItemRecord->setReservationEnd(
                date('Y-m-d H:i:s', time() + (86400 * $orderItem->getItem()->getReservationLength()))
            ); //86400 = 1 day
            $userItemRecord->setHoldEnd(null);
            if ($orderItem->getItem()->getReservationLength() < 3) {
                $userItemRecord->setReservation3dayNoticeSent(true);
            }
        } else {
            $userItemRecord->setReservationEnd(null);
            $userItemRecord->setHoldEnd(null);
        }

        $userItemRecord->setUserId($orderItem->getUserId());
        $userItemRecord->setOrganisationId($orderItem->getOrganisationId());
        $userItemRecord->setItemId($orderItem->getItemId());

        if (!$userItemRecord->save()) {
            $this->appendMessageEx($userItemRecord);
            $this->logActivity('Error activating items', _ms($userItemRecord));
            return false;
        } else {
            $this->logActivity(
                'User items added or updated',
                $orderItem->getDescription(
                ) . ' status ' . $status . ' : order item ' . $userItemRecord->getOrderItemId()
            );
        }

        return true;
    }
} 