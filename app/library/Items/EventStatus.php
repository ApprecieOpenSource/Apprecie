<?php
namespace Apprecie\Library\Items;

use Apprecie\Library\Collections\Enum;

class EventStatus extends Enum
{
    const TBC = 'tbc';
    const OPEN = 'open';
    const CANCELLED = 'cancelled';
    const FULLY_BOOKED = 'fully-booked';
    const PUBLISHED = 'published';
    const LOCKED = 'locked';
    const CLOSED = 'closed';
    const EXPIRED = 'expired';
    const REJECTED = 'rejected';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::TBC => _g('TBC'),
            static::OPEN => _g('Open'),
            static::CANCELLED => _g('Cancelled'),
            static::FULLY_BOOKED => _g('Fully-booked'),
            static::PUBLISHED => _g('Published'),
            static::LOCKED => _g('Locked'),
            static::CLOSED => _g('Closed'),
            static::EXPIRED => _g('Expired'),
            static::REJECTED => _g('Rejected')
        );
    }
}