<?php

class Event extends Item
{
    protected $eventId, $itemId, $description, $status, $attendanceTerms;
    protected $_eventGoals = array();
    protected $targetAge18to34, $targetAge34to65, $targetAge65Plus, $gender, $marketValue, $costToDeliver,
        $pricePerAttendee, $minUnits, $endDateTime, $startDateTime, $bookingEndDate, $bookingStartDate,
        $addressId, $afternoonTea, $lunch, $dinner, $breakfast, $lightRefreshment, $bookingEndNoticeSent;

    /**
     * @param mixed $bookingEndNoticeSent
     */
    public function setBookingEndNoticeSent($bookingEndNoticeSent)
    {
        $this->bookingEndNoticeSent = $bookingEndNoticeSent;
    }

    /**
     * @return mixed
     */
    public function getBookingEndNoticeSent()
    {
        return $this->bookingEndNoticeSent;
    }


    /**
     * @param mixed $addressId
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    /**
     * @return mixed
     */
    public function getAddressId()
    {
    return $this->addressId;
    }

    /**
     * @param mixed $afternoonTea
     */
    public function setAfternoonTea($afternoonTea)
    {
        $this->afternoonTea = $afternoonTea;
    }

    /**
     * @return mixed
     */
    public function getAfternoonTea()
    {
        return $this->afternoonTea;
    }

    /**
     * @param mixed $bookingEndDate
     */
    public function setBookingEndDate($bookingEndDate)
    {
        $this->bookingEndDate = $bookingEndDate;
    }

    /**
     * @return mixed
     */
    public function getBookingEndDate($autoFormat = false)
    {
        if ($this->_outputTBC && $this->bookingEndDate == null) {
            return _g('TBC');
        }

        if ($autoFormat) {
            return _fd($this->bookingEndDate);
        }

        return $this->bookingEndDate;
    }

    /**
     * @param mixed $bookingStartDate
     */
    public function setBookingStartDate($bookingStartDate)
    {
        $this->bookingStartDate = $bookingStartDate;
    }

    /**
     * @return mixed
     */
    public function getBookingStartDate($autoFormat = false)
    {
        if ($this->_outputTBC && $this->bookingStartDate == null) {
            return _g('TBC');
        }

        if ($autoFormat) {
            return _fd($this->bookingStartDate);
        }

        return $this->bookingStartDate;
    }

    /**
     * @param mixed $breakfast
     */
    public function setBreakfast($breakfast)
    {
        $this->breakfast = $breakfast;
    }

    /**
     * @return mixed
     */
    public function getBreakfast()
    {
        return $this->breakfast;
    }

    /**
     * @param mixed $costToDeliver
     */
    public function setCostToDeliver($costToDeliver)
    {
        $this->costToDeliver = $costToDeliver;
    }


    /**
     * @param bool $format
     * @param bool $includeSymbol
     * @return mixed|string
     */
    public function getCostToDeliver($format = false, $includeSymbol = false)
    {
        if ($this->_outputTBC && $this->costToDeliver == null) {
            return _g('TBC');
        }

        if ($format && $this->costToDeliver > 0) {
            $formatted = sprintf('%0.2f', round($this->costToDeliver / 100, 2, PHP_ROUND_HALF_UP));

            if ($includeSymbol) {
                $symbol = $this->getCurrency()->getSymbol();
                return $symbol . $formatted;
            }

            return $formatted;
        }

        return $this->costToDeliver;
    }

    /**
     * @param mixed $dinner
     */
    public function setDinner($dinner)
    {
        $this->dinner = $dinner;
    }

    /**
     * @return mixed
     */
    public function getDinner()
    {
        return $this->dinner;
    }

    /**
     * @param mixed $endDateTime
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    /**
     * @return mixed
     */
    public function getEndDateTime($autoFormat = false, $dateOnly = false, $timeOnly = false)
    {
        if ($this->_outputTBC && $this->endDateTime == null) {
            return _g('TBC');
        }

        if ($autoFormat) {
            if ($dateOnly) {
                return _fd($this->endDateTime);
            } elseif ($timeOnly) {
                return _ft($this->endDateTime);
            }

            return _fdt($this->endDateTime);
        }

        return $this->endDateTime;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $lightRefreshment
     */
    public function setLightRefreshment($lightRefreshment)
    {
        $this->lightRefreshment = $lightRefreshment;
    }

    /**
     * @return mixed
     */
    public function getLightRefreshment()
    {
        return $this->lightRefreshment;
    }

    /**
     * @param mixed $lunch
     */
    public function setLunch($lunch)
    {
        $this->lunch = $lunch;
    }

    /**
     * @return mixed
     */
    public function getLunch()
    {
        return $this->lunch;
    }

    /**
     * @param mixed $marketValue
     */
    public function setMarketValue($marketValue)
    {
        $this->marketValue = $marketValue;
    }

    /**
     * @return mixed
     */
    public function getMarketValue($format = false, $includeSymbol = false)
    {
        if ($this->_outputTBC && $this->marketValue == null) {
            return _g('TBC');
        }

        if ($format && $this->marketValue > 0) {
            $formatted = sprintf('%0.2f', round($this->marketValue / 100, 2, PHP_ROUND_HALF_UP));

            if ($includeSymbol) {
                $symbol = $this->getCurrency()->getSymbol();
                return $symbol . $formatted;
            }

            return $formatted;
        }

        return $this->marketValue;
    }

    /**
     * @param mixed $minUnits
     */
    public function setMinUnits($minUnits)
    {
        $this->minUnits = $minUnits;
    }

    /**
     * @return mixed
     */
    public function getMinUnits()
    {
        if ($this->_outputTBC && $this->minUnits == null) {
            return _g('TBC');
        }

        return $this->minUnits;
    }

    /**
     * @param mixed $pricePerAttendee
     */
    public function setPricePerAttendee($pricePerAttendee)
    {
        $this->pricePerAttendee = $pricePerAttendee;
    }

    /**
     * @return mixed
     */
    public function getPricePerAttendee($format = false, $includeSymbol = false)
    {
        if ($this->_outputTBC && $this->pricePerAttendee == null) {
            return _g('TBC');
        }

        if ($format && $this->pricePerAttendee > 0) {
            $formatted = sprintf('%0.2f', round($this->pricePerAttendee / 100, 2, PHP_ROUND_HALF_UP));

            if ($includeSymbol) {
                $symbol = $this->getCurrency()->getSymbol();
                return $symbol . $formatted;
            }

            return $formatted;
        }

        return $this->pricePerAttendee;
    }

    /**
     * @param mixed $startDateTime
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * @return mixed
     */
    public function getStartDateTime($autoFormat = false, $dateOnly = false, $timeOnly = false)
    {
        if ($this->_outputTBC && $this->startDateTime == null) {
            return _g('TBC');
        }

        if ($autoFormat) {
            if ($dateOnly) {
                return _fd($this->startDateTime);
            } elseif ($timeOnly) {
                return _ft($this->startDateTime);
            }

            return _fdt($this->startDateTime);
        }

        return $this->startDateTime;
    }

    /**
     * @param mixed $targetAge18to34
     */
    public function setTargetAge18to34($targetAge18to34)
    {
        $this->targetAge18to34 = $targetAge18to34;
    }

    /**
     * @return mixed
     */
    public function getTargetAge18to34()
    {
        return $this->targetAge18to34;
    }

    /**
     * @param mixed $targetAge34to65
     */
    public function setTargetAge34to65($targetAge34to65)
    {
        $this->targetAge34to65 = $targetAge34to65;
    }

    /**
     * @return mixed
     */
    public function getTargetAge34to65()
    {
        return $this->targetAge34to65;
    }

    /**
     * @param mixed $targetAge65Plus
     */
    public function setTargetAge65Plus($targetAge65Plus)
    {
        $this->targetAge65Plus = $targetAge65Plus;
    }

    /**
     * @return mixed
     */
    public function getTargetAge65Plus()
    {
        return $this->targetAge65Plus;
    }


    /**
     * @param mixed $attendanceTerms
     */
    public function setAttendanceTerms($attendanceTerms)
    {
        $this->attendanceTerms = $attendanceTerms;
    }

    /**
     * @return mixed
     */
    public function getAttendanceTerms()
    {
        return $this->attendanceTerms;
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
     * @param mixed $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
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

    public function getItem($options = null)
    {
        return $this->getRelated('Item', $options);
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

    public function getSource()
    {
        return 'events';
    }

    public function initialize()
    {
        $this->hasMany('eventId', 'EventGoal', 'eventId', ['reusable' => true]);
        $this->hasManyToMany(
            'eventId',
            'EventGoal',
            'eventId',
            'goalId',
            'Goal',
            'goalId',
            ['alias' => 'eventgoals', 'reusable' => true]
        );
        $this->hasOne('itemId', 'Item', 'itemId');
        $this->hasOne('addressId', 'Address', 'addressId', ['reusable' => true]);

        parent::initialize();
    }

    public function getAddress($options = null)
    {
        return $this->getRelated('Address', $options);
    }

    public function onConstruct()
    {
        $this->setParentIsTableBase(true);
        $this->setType(\Apprecie\Library\Items\ItemTypes::EVENT);
        parent::onConstruct();

        $this->setIndirectContentFields
            (
                array_merge
                (
                    $this->getIndirectContentFields(), //keep parent entries
                    ['attendanceTerms', 'description']
                )
            );
    }

    public function getGoals($options = null)
    {
        return $this->getRelated('eventgoals', $options);
    }

    /**
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getEventGoalLinks($options = null)
    {
        return $this->getRelated('EventGoal', $options);
    }

    public function addGoal($goal, $clearExisting = false)
    {
        if ($clearExisting) {
            $links = $this->getEventGoalLinks();
            $this->_eventGoals = array();
            foreach ($links as $link) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }
            }
        }

        if (is_array($goal) || $goal instanceof \ArrayAccess) {
            foreach ($goal as $element) {
                if (!$this->addGoal($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $goal = Goal::resolve($goal);
        }

        //check if already exists if not just cleared all
        if (!$clearExisting) {
            $goalExists = EventGoal::find(
                    "eventId = {$this->getEventId()} AND goalId = {$goal->getGoalId()}"
                )->count() > 0;

            if ($goalExists) {
                return true;
            } //just indicate a positive result if requirement already set.
        }

        $eventGoal = new EventGoal();
        $eventGoal->setEventId($this->getEventId());
        $eventGoal->setGoalId($goal->getGoalId());

        if (!$eventGoal->create()) {
            $this->appendMessageEx($eventGoal->getMessages());
            return false;
        }

        $this->_eventGoals[$goal->getLabel()] = $goal->getLabel(); //add to static cache
        return true;
    }

    public function hasGoal($goalLabel)
    {
        if ($this->_eventGoals == null) {
            $this->_eventGoals = array();
            $goals = $this->getGoals();
            if ($goals == null || $goals->count() == 0) {
                return false;
            }

            foreach ($goals as $goal) {
                $this->_eventGoals[$goal->getLabel()] = $goal->getLabel();
            }
        }

        return in_array($goalLabel, $this->_eventGoals);
    }


    public function removeGoal($goal)
    {
        if (is_array($goal) || $goal instanceof \ArrayAccess) {
            foreach ($goal as $element) {
                if (!$this->removeGoal($element)) {
                    return false;
                }
            }

            return true;
        } else {
            $goal = Goal::resolve($goal);
        }

        $links = $this->getEventGoalLinks();

        foreach ($links as $link) {
            if ($link->getGoalId() == $goal->getGoalId()) {
                if (!$link->delete()) {
                    $this->appendMessageEx($link->getMessages());
                    return false;
                }

                if (array_key_exists($goal->getLabel(), $this->_eventGoals)) {
                    unset($this->_eventGoals[$goal->getLabel()]);
                }
                break;
            }
        }

        return true;
    }

    public function publishPrivate()
    {
        $this->setState(\Apprecie\Library\Items\ItemState::APPROVED);
        $this->setDestination(\Apprecie\Library\Items\ItemDestination::PRIVATE_ITEM);

        if (!$this->update()) {
            return false;
        }

        $organisation = $this->getSourceOrganisation();

        if (!$organisation->addEventToVault($this, null)) {
            $this->appendMessageEx($organisation);
            return false;
        }

        return true;
    }

    /**
     * Will remove the event from all vaults and set it status back to draft so long as the event has not been consumed
     * or reserved
     */
    public function unPublishEvent()
    {
        if ($this->getStatus() == \Apprecie\Library\Items\EventStatus::LOCKED) {
            $this->appendMessageEx(_g('It is not possible to un-publish this event as it is locked'));
            return false;
        }

        //@todo check all unpublish conditions once we have consumption work flow.
        $this->setStatus(null);
        $this->setState(\Apprecie\Library\Items\ItemState::DRAFT);

        //revoke the item from all vaults
        $this->getModelsManager()->executeQuery(
            'DELETE FROM ItemVault WHERE itemId = :id:',
            ['id' => $this->getItemId()]
        );

        //unpublish the event on any approval records
        $this->getModelsManager()->executeQuery(
            'UPDATE ItemApproval SET status= :state: WHERE itemId = :id:',
            ['id' => $this->getItemId(), 'state' => \Apprecie\Library\Items\ApprovalState::UNPUBLISHED]
        );

        if (!$this->update()) {
            return false;
        }

        return true;
    }


    public static function canDeleteEvent($event)
    {
        $event = Event::resolve($event);
        //ensure not purchased
        if($event->getRemainingPackages() == $event->getMaxUnits()) {
            $orders = OrderItems::findBy('itemId', $event->getItemId());

            if(count($orders) == 0) {
                //check linked messages
                $messages = Message::findBy('referenceItem', $event->getItemId());

                if(count($messages) == 0) {
                    $arrangements = Item::findBy('sourceByArrangement', $event->getItemId());

                    if(count($arrangements) == 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function deleteEvent()
    {
        if(Event::canDeleteEvent($this)) {

            if (!$this->unPublishEvent()) {
                return false;
            }

            if(! $this->delete()) {
                $this->appendMessageEx('Unable to delete.');
            }
        }

        return $this->hasMessages();
    }

    public function confirmArrangement()
    {
        if ($this->getSourceByArrangement() == null || $this->getIsArranged()) {
            $this->appendMessageEx(_g('This event is already arranged or is not a By Arrangement'));
        }

        $customer = $this->getArrangedFor();

        $this->setIsArranged(true);
        if ($this->getMaxUnits() == 0) {
            $this->setMaxUnits(1);
        }
        $this->setState(\Apprecie\Library\Items\ItemState::APPROVED);

        if ($this->update()) {
            $organisation = $customer->getOrganisation();

            if (!$organisation->addEventToVault($this, $customer->getUserId())) {
                $this->appendMessageEx($organisation);
            }

            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($customer->getPortalId());
            $buffer = _p(
                _g('Congratulations! Your arrangement request for {item} has been approved.', ['item' => _eh($this->getTitle())])
            );
            $buffer .= _p(
                _g('In order to complete the transaction, please click on the Referenced Item above and check all of the details. After revising, click on the Purchase button to complete the payment.')
            );
            $buffer .= _p(
                _g('Please note that the Item will be available for you to purchase till: {paymentExpirationDate}. Afterwards, the Purchase option will be disabled.', array('paymentExpirationDate' => _eh(_fdt($this->getBookingEndDate()))))
            );
            $buffer .= _p(
                _g('Should you have any questions regarding this item, please respond to this message using the Reply button above.')
            );
            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

            $message = new \Apprecie\Library\Messaging\UserMessage();
            $message->sendMessage(
                \Apprecie\Library\Messaging\UserMessageMode::MESSAGE_AND_EMAIL_AND_ALERT,
                $this->getCreatorId(),
                $customer,
                $buffer,
                _g('Your Arrangement Request has been approved'),
                $this->getArrangementMessageThread(),
                $this->getItemId()
            );

            /*
            $notice = new \Apprecie\Library\Messaging\Notification();

            if (!$notice->addNotification
                (
                    $customer,
                    _g('Arrangement Request'),
                    _g('Your request to arrange {item} has been approved', ['item' => _eh($this->getTitle())]),
                    \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                        $customer->getPortalId(),
                        'vault',
                        'event',
                        [$this->getItemId()]
                    ),
                    null,
                    true
                )
            ) {
                $this->appendMessage($notice);
            }
            */
        }

        //GH  Hack.. not sure why this exists but it seems to have not caused any issue,  so lets pull the message off the stack
        if ($this->hasMessages() && count($this->getMessages() == 1)) {
            foreach ($this->getMessages() as $message) {
                if ($message == "Failed to commit purge transactions") {
                    return true;
                }
            }
        }

        return !$this->hasMessages();
    }

    public function rejectArrangement($body)
    {
        if ($this->getSourceByArrangement() == null || $this->getIsArranged()) {
            $this->appendMessageEx(_g('This event is already arranged or is not a By Arrangement'));
        }

        $target = $this->getArrangedFor();

        $this->setState(\Apprecie\Library\Items\ItemState::DENIED);
        $this->setStatus(\Apprecie\Library\Items\EventStatus::REJECTED);

        if ($this->update()) {
            if($target->getIsInteractive()) {
                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($target->getPortalId());
                $buffer = $buffer = _p(_g('Dear {person},', ['person' => $target->getUserProfile()->getFullName()]));
                $buffer .= _p(
                    _g(
                        'Sorry, your arrangement request for {itemName} has been declined. {supplier} have given this reason:',
                        ['itemName' => _eh($this->getTitle()), 'supplier' => $this->getSourceOrganisation()->getOrganisationName()]
                    )
                );
                $buffer .= _p($body);
                $buffer .= _p(
                    _g(
                        'If you wish to dispute this reason, please respond to this message using the Reply button above, else we encourage you to check your vault for other opportunities that may be available for you to enjoy.'
                    )
                );
                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

                $message = new \Apprecie\Library\Messaging\UserMessage();
                $message->sendMessage(
                    \Apprecie\Library\Messaging\UserMessageMode::MESSAGE_AND_EMAIL_AND_ALERT,
                    $this->getCreatorId(),
                    $this->getArrangedFor(),
                    $buffer,
                    _g('Your Arrangement Request has been rejected'),
                    $this->getArrangementMessageThread(),
                    $this->getItemId()
                );
            }
        }

        //GH  Hack.. not sure why this exists but it seems to have not caused any issue,  so lets pull the message off the stack
        if ($this->hasMessages() && count($this->getMessages() == 1)) {
            foreach ($this->getMessages() as $message) {
                if ($message == "Failed to commit purge transactions") {
                    return true;
                }
            }
        }

        return !$this->hasMessages();
    }

    public function beginArrange(
        $startDateTime,
        $endDateTime,
        $user,
        $addressId,
        $notes,
        $packageSize,
        $numberOfPackages = 1
    ) {
        if (!$this->getIsByArrangement()) {
            throw new LogicException('This event cannot be arranged, it is not a By Arrangement');
        }

        $user = User::resolve($user);

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        $copy = new Event();
        $copy->setTransaction($transaction);

        $items = $this->toArrayEx(); //remove keys from the data
        unset($items['itemId']);
        unset($items['eventId']);
        unset($items['dateCreated']);

        $copy->assignEx($items);
        $copy->setItemId(null);
        $copy->setStartDateTime($startDateTime);
        $copy->setEndDateTime($endDateTime);
        $copy->setAddressId($addressId);
        $copy->setPackageSize($packageSize);
        $copy->setMaxUnits($numberOfPackages);
        $copy->setIsArranged(false);
        $copy->setIsByArrangement(false);
        $copy->setState(\Apprecie\Library\Items\ItemState::ARRANGING);
        $copy->setSourceByArrangement($this->getItemId());
        $copy->setIsArrangedFor($user->getUserId());

        if (!$copy->create()) {
            $this->appendMessageEx($copy);
        } else {
            if (!$copy->addCategory($this->getCategories()) || !$copy->addGoal($this->getGoals())) {
                $this->appendMessageEx($copy);
            } elseif (!$copy->update()) {
                $this->appendMessageEx($copy);
            } else {
                Assets::copyItemAssets($this->getItemId(), $copy->getItemId());

                //delete copied BA brochure
                $brochure = Assets::getItemAssetDirectory($copy->getItemId()) . '/' . $this->getItemId() . '.pdf';
                if (file_exists($brochure)) {
                    unlink($brochure);
                }

                //rename banner file with copy's item ID
                $banner = Assets::getItemAssetDirectory($copy->getItemId()) . '/' . $this->getItemId() . '-banner.jpg';
                if (file_exists($banner)) {
                    $newBanner = Assets::getItemAssetDirectory($copy->getItemId()) . '/' . $copy->getItemId() . '-banner.jpg';
                    rename($banner, $newBanner);
                }

                //copy media record
                $media = $this->getItemMedia();
                foreach ($media as $m) {
                    $newMedia = new ItemMedia();
                    $newMedia->setTransaction($transaction);
                    $newMedia->assign($m->toArray());
                    $newMedia->setItemId($copy->getItemId());
                    if ($newMedia->save()) {
                        $this->appendMessageEx($newMedia);
                    }
                }

                $message = new Message();
                $message->setTransaction($transaction);

                $message->setReferenceItem($copy->getItemId());
                $message->setTargetUser($copy->getCreatorId());
                $message->setSourceUser($user->getUserId());
                $message->setBody(
                    _g(
                        "An arrangement for this event has been requested, any additional information provided by the requester can be seen below:"
                    ) . '<br/>' . $notes
                );
                $message->setTitle(_g("By Arrangement Request"));
                $message->setSourcePortal($user->getPortalId());
                $message->setSourceDescription
                    (
                        $user->getUserProfile()->getFirstname() . ' ' . $user->getUserProfile()->getLastName()
                    );
                $message->setSent(date('Y-m-d H:i:s'));
                $message->setSourceOrganisation($user->getOrganisationId());

                if (!$message->save()) {
                    $this->appendMessageEx($message);
                } else {
                    $thread = new MessageThread();
                    $thread->setTransaction($transaction);
                    $thread->setStartedByUser($message->getSourceUser());
                    $thread->setFirstRecipientUser($message->getTargetUser());
                    $thread->setByArrangementId($this->getItemId());
                    $thread->setType(\Apprecie\Library\Messaging\MessageThreadType::ARRANGEMENT);

                    if (!$thread->save()) {
                        $this->appendMessageEx($thread);
                    } else {
                        $copy->setArrangementMessageThread($thread->getThreadId());
                        if (!$copy->update()) {
                            $this->appendMessageEx($copy);
                        } else {
                            $thread->addMessage($message);

                            $notice = new \Apprecie\Library\Messaging\Notification();
                            if(! $notice->addNotification
                                (
                                    $copy->getCreatorId(),
                                    _g('By Arrangement Request'),
                                    _g(
                                        '{person} has requested an arrangement of {item}',
                                        [
                                            'person' => $user->getUserProfile()->getFullName(),
                                            'item' => _eh($copy->getTitle())
                                        ]
                                    ),
                                    \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                                        $copy->getCreatedBy()->getPortalId(),
                                        'vault',
                                        'arrangedp',
                                        [$copy->getItemId()]
                                    ),
                                    $transaction,
                                    true
                                )) {
                                $this->appendMessageEx($notice);
                            }
                        }
                    }
                }
            }
        }

        try {
            if (!$this->hasMessages()) {
                $transaction->commit();
            } else {
                $transaction->rollback();
                return false;
            }
        } catch (\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return $copy;
    }

    /**
     * @param $item
     * @return Event
     */
    public static function findByItem($item)
    {
        $item = Item::resolve($item);
        return Event::findFirstBy('itemId', $item->getItemId());
    }

    public static function findByGoals($goals)
    {
        $resolved = array();
        if (!is_array($goals)) {
            $goals = array($goals);
        }

        foreach ($goals as $goal) {
            $goal = Goal::resolve($goal);
            $resolved[] = $goal->getGoalId();
        }

        return Event::query()
            ->innerJoin('EventGoal')
            ->inWhere('goalId', $resolved)
            ->execute();
    }

    public static function findByCreator($creatorUser)
    {
        $creatorUser = User::resolve($creatorUser);

        return Event::query()
            ->innerJoin('Item')
            ->where('creatorId=:1:')
            ->bind([1 => $creatorUser->getUserId()])
            ->execute();
    }

    public static function processClosedEvents()
    {
        //events that have reached booking end date
        $filter = new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('Item', 'Item.itemId = Event.itemId')
            ->addAndEqualFilter('state', \Apprecie\Library\Items\ItemState::APPROVED)
            ->addAndEqualFilter('status', \Apprecie\Library\Items\EventStatus::CLOSED)
            ->addAndIsNullFilter('bookingEndNoticeSent');

        $expiredEvents = Event::findByFilter($filter);

        $notice = new \Apprecie\Library\Messaging\Notification();

        foreach ($expiredEvents as $event) {
            $user = User::resolve($event->getCreatorId(), false);

            if ($user != null) { //there are some broken events with no creator
                if (!$event->getIsArranged()) {
                    $notice->addNotification
                        (
                            $user,
                            _g('The booking period has expired'),
                            _g(
                                'The event : {eventTitle} now has an expired booking period.  You can review the event here.',
                                ['eventTitle' => $event->getTitle()]
                            ),
                            '/mycontent/eventmanagement/' . $event->getEventId()
                        );
                } else {
                    $consumer = $event->getArrangedFor();

                    if ($consumer != null) {
                        $notice->addNotification
                            (
                                $consumer,
                                _g('Your arranged event has lapsed without payment'),
                                _g(
                                    'You arranged the event : {eventTitle} but failed to consume the event within the agreed 48 hours.  It has now been unconfirmed for review by the supplier.',
                                    ['eventTitle' => $event->getTitle()]
                                )
                            );

                        $notice->addNotification
                            (
                                $user,
                                _g('An event you arranged has lapsed without payment'),
                                _g(
                                    'You arranged the event : {eventTitle} but the customer failed to consume the event within the agreed 48 hours.  It has now been unconfirmed for your review.',
                                    ['eventTitle' => $event->getTitle()]
                                ),
                                '/vault/arrangedp/' . $event->getItemId()
                            );

                        $event->setIsArranged(false);
                        $event->setState(\Apprecie\Library\Items\ItemState::ARRANGING);
                    }
                }
            }

            $event->setBookingEndNoticeSent(true);
            $event->setStatus(\Apprecie\Library\Items\EventStatus::CLOSED);
            $event->update();
        }
    }

    public static function updateEventStatus()
    {
        //we are looking for expired events  expired
        $filter = new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('Item', 'Item.itemId = Event.itemId')
            ->addAndLessThanFilter('endDateTime', date("Y-m-d H:i:s"))
            ->addAndNotEqualFilter('state', \Apprecie\Library\Items\ItemState::ARRANGING)
            ->addAndNotEqualFilter('status', \Apprecie\Library\Items\EventStatus::EXPIRED);

        $expiredEvents = Event::findByFilter($filter);

        foreach ($expiredEvents as $event) {
            $event->setStatus(\Apprecie\Library\Items\EventStatus::EXPIRED);
            $event->update();
        }

        //we are looking for in progress events, to mark them open
        $filter = new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('Item', 'Item.itemId = Event.itemId')
            ->addAndEqualFilter('state', \Apprecie\Library\Items\ItemState::APPROVED)
            ->addAndNotEqualFilter('status', \Apprecie\Library\Items\EventStatus::OPEN)
            ->addAndNotEqualFilter('isByArrangement', 1)
            ->addAndLessThanFilter('startDateTime', date("Y-m-d H:i:s"))
            ->addAndGreaterThanFilter('endDateTime', date("Y-m-d H:i:s"));

        $runningEvents = Event::findByFilter($filter);

        foreach ($runningEvents as $event) {
            $event->setStatus(\Apprecie\Library\Items\EventStatus::OPEN);
            $event->update();
        }

        //we are looking for not yet running events with closed booking to mark them closed
        $filter = new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('Item', 'Item.itemId = Event.itemId')
            ->addAndEqualFilter('state', \Apprecie\Library\Items\ItemState::APPROVED)
            ->addAndNotEqualFilter('status', \Apprecie\Library\Items\EventStatus::CLOSED)
            ->addAndGreaterThanFilter('startDateTime', date("Y-m-d H:i:s"))
            ->addAndLessThanFilter('bookingEndDate', date("Y-m-d H:i:s"));

        $closedEvents = Event::findByFilter($filter);

        foreach ($closedEvents as $event) {
            $event->setStatus(\Apprecie\Library\Items\EventStatus::CLOSED);
            $event->update();
        }

        //continue to close events that do not have start/end date/time (TBC) with closed booking
        $filter = new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('Item', 'Item.itemId = Event.itemId')
            ->addAndEqualFilter('state', \Apprecie\Library\Items\ItemState::APPROVED)
            ->addAndNotEqualFilter('status', \Apprecie\Library\Items\EventStatus::CLOSED)
            ->addAndIsNullFilter('startDateTime')
            ->addAndIsNullFilter('endDateTime')
            ->addAndLessThanFilter('bookingEndDate', date("Y-m-d H:i:s"));

        $closedEvents = Event::findByFilter($filter);

        foreach ($closedEvents as $event) {
            $event->setStatus(\Apprecie\Library\Items\EventStatus::CLOSED);
            $event->update();
        }

        /* fix to broken approval data
        $events = Event::find();

        foreach($events as $event) {
            if($event->getIsByArrangement()) {
                $event->setStatus(null);
                $event->update();
            } else {
                if($event->getStatus() != null && $event->getState() == \Apprecie\Library\Items\ItemState::APPROVING) {
                    $event->setState(\Apprecie\Library\Items\ItemState::APPROVED);
                    $event->update();
                }
            }
        }

        */

        /* approve items with a valid approval record
        $events = Event::find();

        foreach($events as $event) {
            if($event->getState() == \Apprecie\Library\Items\ItemState::APPROVING) {
                $approval = $event->getRelatedApproval();

                if($approval != null) {
                    if($approval->getStatus() == \Apprecie\Library\Items\ApprovalState::APPROVED) {
                        $event->setState(\Apprecie\Library\Items\ItemState::APPROVED);
                        $event->update();
                    }
                }
            }
        }*/
    }

    /**
     * @param \Apprecie\Library\Model\ApprecieModelBase|mixed $param
     * @param bool $throw
     * @param \Apprecie\Library\Model\ApprecieModelBase $instance
     * @return Event | null
     */
    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        return parent::resolve($param, $throw, $instance);
    }

    public function getIsGuestListClosed()
    {
        $guestListClosedDateTime = $this->getGuestListClosedDateTime();
        if (time() <= $guestListClosedDateTime->getTimestamp()) {
            return false;
        } else {
            return true;
        }
    }

    public function getGuestListClosedDateTime($format = false)
    {
        $eventStartDateTime = new DateTime($this->getStartDateTime());
        $guestListClosedDateTime = $eventStartDateTime
            ->setTime(0, 0)
            ->modify('-1 day -1 second');

        if ($format) {
            return _fd($guestListClosedDateTime->format('Y-m-d H:i:s'));
        } else {
            return $guestListClosedDateTime;
        }
    }

    public function getCalendar($download = false)
    {
        $vCalendar = new \Eluceo\iCal\Component\Calendar('apprecie.com');
        $vCalendar->addComponent($this->getCalendarEvent());

        if ($download) {
            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment');

            echo $vCalendar->render();
        } else {
            return $vCalendar;
        }

        return true;
    }

    public function getCalendarEvent()
    {
        $vEvent = new \Eluceo\iCal\Component\Event();

        $vEvent->setDtStart(new DateTime($this->getStartDateTime()))
            ->setDtEnd(new DateTime($this->getEndDateTime()))
            ->setNoTime(false)
            ->setUseUtc(false)
            ->setLocation(str_replace("\n", ", ", $this->getAddress()->getLabel()))
            ->setSummary($this->getTitle())
            ->setDescription($this->getSummary());

        return $vEvent;
    }

    public function getCalendarDownloadUrl()
    {
        return _u(
            null,
            'api',
            'downloadCalendar',
            array(
                $this->getEventId(),
                $this->getTitle() . '.ics'
            )
        );
    }
} 