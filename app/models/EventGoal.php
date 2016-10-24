<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 14/01/15
 * Time: 14:00
 */
class EventGoal extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $eventId, $goalId;

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
     * @param mixed $goalId
     */
    public function setGoalId($goalId)
    {
        $this->goalId = $goalId;
    }

    /**
     * @return mixed
     */
    public function getGoalId()
    {
        return $this->goalId;
    }

    public function getSource()
    {
        return 'eventgoals';
    }

    public function intialize()
    {
        $this->hasOne('goalId', 'Goal', 'goalId', ['reusable' => true]);
        $this->hasOne('eventId', 'Event', 'eventId', ['reusable' => true]);
    }
} 