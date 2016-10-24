<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 14/01/15
 * Time: 13:58
 */
class Goal extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $goalId, $label;

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

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function getSource()
    {
        return 'goals';
    }

    public function initialize()
    {
        $this->hasMany('goalId', 'EventGoal', 'goalId', ['reusable' => true]);
        $this->hasManyToMany(
            'goalId',
            'EventGoal',
            'goalId',
            'eventId',
            'Event',
            'eventId',
            ['alias' => 'event', 'reusable' => true]
        );
    }

    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        $goal = null;

        if (is_string($param)) {
            $goal = Goal::findFirstBy('label', $param, $instance);
        } else {
            $goal = Parent::resolve($param, $throw, $instance);
        }

        return $goal;
    }
} 