<?php

class GuestList extends \Apprecie\Library\Model\ApprecieModelBase
{
    protected $itemId, $userId, $suggestedBy, $attending, $invitationSent, $status,
        $confirmingUserId, $paid, $confirmationSent, $owningUserId, $invitationHash, $fiveDayAttendingNoticeSent, $fiveDayNoResponseNoticeSent, $followUpEmailSent, $spaces;

    /**
     * @return mixed
     */
    public function getFollowUpEmailSent()
    {
        return $this->followUpEmailSent;
    }

    /**
     * @param mixed $followUpEmailSent
     */
    public function setFollowUpEmailSent($followUpEmailSent)
    {
        $this->followUpEmailSent = $followUpEmailSent;
    }

    /**
     * @param mixed $invitationHash
     */
    public function setInvitationHash($invitationHash)
    {
        $this->invitationHash = $invitationHash;
    }

    /**
     * Note that if the user does not yet have an invitation hash generated, this method will generate one and save the record
     * to the database.
     *
     * @return mixed
     */
    public function getInvitationHash()
    {
        if ($this->invitationHash == null) {
            $this->invitationHash = (new \Apprecie\Library\Security\Authentication())->generateRegistrationToken();
            $this->update();
        }

        return $this->invitationHash;
    }

    /**
     * @return mixed
     */
    public function getSpaces()
    {
        return $this->spaces;
    }

    /**
     * @param mixed $spaces
     */
    public function setSpaces($spaces)
    {
        $this->spaces = $spaces;
    }

    /**
     * @param mixed $fiveDayAttendingNoticeSent
     */
    public function setFiveDayAttendingNoticeSent($fiveDayAttendingNoticeSent)
    {
        $this->fiveDayAttendingNoticeSent = $fiveDayAttendingNoticeSent;
    }

    /**
     * @return mixed
     */
    public function getFiveDayAttendingNoticeSent()
    {
        return $this->fiveDayAttendingNoticeSent;
    }

    /**
     * @param mixed $fiveDayNoResponseNoticeSent
     */
    public function setFiveDayNoResponseNoticeSent($fiveDayNoResponseNoticeSent)
    {
        $this->fiveDayNoResponseNoticeSent = $fiveDayNoResponseNoticeSent;
    }

    /**
     * @return mixed
     */
    public function getFiveDayNoResponseNoticeSent()
    {
        return $this->fiveDayNoResponseNoticeSent;
    }

    /**
     * @param mixed $attending
     */
    public function setAttending($attending)
    {
        $this->attending = $attending;
    }

    /**
     * @return mixed
     */
    public function getAttending()
    {
        return $this->attending;
    }

    /**
     * @param mixed $confirmationSent
     */
    public function setConfirmationSent($confirmationSent)
    {
        $this->confirmationSent = $confirmationSent;
    }

    /**
     * @return mixed
     */
    public function getConfirmationSent()
    {
        return $this->confirmationSent;
    }

    /**
     * @param mixed $confirmingUserId
     */
    public function setConfirmingUserId($confirmingUserId)
    {
        $this->confirmingUserId = $confirmingUserId;
    }

    /**
     * @return mixed
     */
    public function getConfirmingUserId()
    {
        return $this->confirmingUserId;
    }

    /**
     * @param mixed $eventId
     */
    public function setItemId($eventId)
    {
        $this->itemId = $eventId;
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param mixed $invitationSent
     */
    public function setInvitationSent($invitationSent)
    {
        $this->invitationSent = $invitationSent;
    }

    /**
     * @return mixed
     */
    public function getInvitationSent()
    {
        return $this->invitationSent;
    }

    /**
     * @param mixed $paid
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
    }

    /**
     * @return mixed
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param mixed $purchasingUserId
     */
    public function setOwningUserId($purchasingUserId)
    {
        $this->owningUserId = $purchasingUserId;
    }

    /**
     * @return mixed
     */
    public function getOwningUserId()
    {
        return $this->owningUserId;
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

    public function getSource()
    {
        return 'guestlist';
    }

    public static function getGuestCount($itemId, $ownerUserId, $status = null)
    {
        $instance = new GuestList();
        if ($status != null) {
            /*
            $allocatedUnitRecords = $instance->getModelsManager()->executeQuery(
                "SELECT COUNT(userId) as units FROM GuestList WHERE owningUserId = :ownerid: AND itemId = :itemid: AND status = :status:",
                ['ownerid' => $ownerUserId, 'itemid' => $itemId, 'status' => $status]
            )->getFirst();
            */
            $allocatedUnitRecords = $instance->getModelsManager()->executeQuery(
                "SELECT COALESCE(SUM(spaces), 0) as units FROM GuestList WHERE owningUserId = :ownerid: AND itemId = :itemid: AND status = :status:",
                ['ownerid' => $ownerUserId, 'itemid' => $itemId, 'status' => $status]
            )->getFirst();
        } else {
            /*
            $allocatedUnitRecords = $instance->getModelsManager()->executeQuery(
                "SELECT COUNT(userId) as units FROM GuestList WHERE owningUserId = :ownerid: AND itemId = :itemid:",
                ['ownerid' => $ownerUserId, 'itemid' => $itemId]
            )->getFirst();
            */
            $allocatedUnitRecords = $instance->getModelsManager()->executeQuery(
                "SELECT COALESCE(SUM(spaces), 0) as units FROM GuestList WHERE owningUserId = :ownerid: AND itemId = :itemid:",
                ['ownerid' => $ownerUserId, 'itemid' => $itemId]
            )->getFirst();
        }

        return $allocatedUnitRecords['units'];
    }

    /**
     * Sends out emails and notifications for people attending events that start within 5 days
     */
    public static function processFiveDayAttendingWarnings()
    {
        $filter = new \Apprecie\Library\Search\SearchFilter('GuestList');
        $filter->addJoin('Event', 'Event.itemId = GuestList.itemId')
            ->addAndEqualFilter('status', \Apprecie\Library\Guestlist\GuestListStatus::CONFIRMED, 'GuestList')
            ->addAndEqualFilter('attending', 1)
            ->addAndIsNullFilter('fiveDayAttendingNoticeSent')
            ->addAndEqualOrGreaterThanFilter('endDateTime', date("Y-m-d H:i:s"));

        $date = new DateTime();
        $formattedDate = $date->sub(new DateInterval('P5D'))->format('Y-m-d');
        $filter->addAndEqualOrGreaterThanFilter('startDateTime', $formattedDate);

        $results = GuestList::findByFilter($filter);

        $notice = new \Apprecie\Library\Messaging\Notification();

        foreach ($results as $guestRecord) {
            $event = Event::findByItem($guestRecord->getItemId());

            if ($notice->addNotification
                (
                    $guestRecord->getUserId(),
                    _g('An event that you are attending is starting soon!'),
                    _g(
                        'This is just a friendly reminder of your attendance of {item} hosted by {supplier} on {date}',
                        [
                            'item' => _s($event->getTitle()),
                            'supplier' => $event->getSourceOrganisation()->getOrganisationName(),
                            'date'=>_fd($event->getStartDateTime())
                        ]
                    ),
                    \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                        User::resolve($guestRecord->getUserId())->getPortalId(),
                        'rsvp',
                        'event',
                        [$guestRecord->getInvitationHash()]
                    ),
                    null,
                    true
                )
            ) {
                $guestRecord->setFiveDayAttendingNoticeSent(true);
                $guestRecord->update();
            }
        }
    }

    public static function ProcessFiveDayNonResponseToInviteWarnings()
    {
        $filter = new \Apprecie\Library\Search\SearchFilter('GuestList');
        $filter->addJoin('Event', 'Event.itemId = GuestList.itemId')
            ->addAndEqualFilter('status', \Apprecie\Library\Guestlist\GuestListStatus::PENDING, 'GuestList')
            ->addAndEqualFilter('attending', 0)
            ->addAndIsNullFilter('fiveDayNoResponseNoticeSent')
            ->addAndEqualOrGreaterThanFilter('endDateTime', date("Y-m-d H:i:s"));

        $date = new DateTime();
        $formattedDate = $date->sub(new DateInterval('P5D'))->format('Y-m-d');
        $filter->addAndEqualOrLessThanFilter('invitationSent', $formattedDate);

        $results = GuestList::findByFilter($filter);

        $notice = new \Apprecie\Library\Messaging\Notification();

        foreach ($results as $guestRecord) {
            $event = Event::findByItem($guestRecord->getItemId());

            if ($notice->addNotification
                (
                    $guestRecord->getUserId(),
                    _g('You need to respond to an invite'),
                    _g(
                        'You still have a pending invite to {item}.  Please make sure you respond as soon as posible to guarantee your attendance',
                        ['item' => $event->getTitle()]
                    ),
                    \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                        \User::resolve($guestRecord->getUserId())->getPortalId(),
                        'rsvp',
                        'event',
                        [$guestRecord->getInvitationHash()]
                    ),
                    null,
                    true
                )
            ) {
                $guestRecord->setFiveDayNoResponseNoticeSent(true);
                $guestRecord->update();
            }
        }
    }

    public static function userIsInGuestList($userId, $ownerId, $itemId, $status = false)
    {
        $instance = new GuestList();
        if ($status !== false) {
            $presence = $instance->getModelsManager()->executeQuery(
                "SELECT COALESCE( COUNT(userId)) as units FROM GuestList WHERE owningUserId = :ownerid: AND itemId = :itemid: AND userId = :userId: AND status=:status:",
                ['ownerid' => $ownerId, 'itemid' => $itemId, 'userId' => $userId, 'status' => $status]
            )->getFirst();
        } else {
            $presence = $instance->getModelsManager()->executeQuery(
                "SELECT COALESCE( COUNT(userId)) as units FROM GuestList WHERE owningUserId = :ownerid: AND itemId = :itemid: AND userId = :userId:",
                ['ownerid' => $ownerId, 'itemid' => $itemId, 'userId' => $userId]
            )->getFirst();
        }

        return $presence['units'] > 0;
    }

} 