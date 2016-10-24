<?php
//@todo this model needs a source method to provide a lowercase table name that matches all other tables in our schema
class ChartActiveUsersEventSupply extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $date, $active, $supply;

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getSupply()
    {
        return $this->supply;
    }

    /**
     * @param mixed $supply
     */
    public function setSupply($supply)
    {
        $this->supply = $supply;
    }

    public static function updateSupplyAndDemand()
    {
        $filter = new \Apprecie\Library\Search\SearchFilter('User');
        $filter->addAndEqualFilter('status', 'active');
        $filter->addAndEqualFilter('isDeleted', null);
        $users = $filter->execute();

        $userCount = $users->count();

        $date = date('Y-m-d');
        $filter = new \Apprecie\Library\Search\SearchFilter('Item');
        $filter->addJoin('Event', 'Item.itemId = Event.itemId');
        $filter->addAndEqualFilter('status', 'published', 'Event');
        $filter->addAndEqualOrGreaterThanFilter('bookingEndDate', $date, 'Event');
        $filter->addAndEqualOrLessThanFilter('bookingStartDate', $date, 'Event');

        $events = $filter->execute();
        $unitsAvailable = 0;

        foreach ($events as $event) {
            $unitsAvailable += ($event->getRemainingPackages() * $event->getPackageSize());
        }

        $record = new ChartActiveUsersEventSupply();
        $record->setActive($userCount);
        $record->setDate($date);
        $record->setSupply($unitsAvailable);
        $record->save();
    }
}