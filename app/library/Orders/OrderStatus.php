<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 17/03/15
 * Time: 10:23
 */

namespace Apprecie\Library\Orders;

use Apprecie\Library\Collections\Enum;

class OrderStatus extends Enum
{
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const COMPLETE = 'complete';
    const CANCELLED = 'cancelled';
    const HELD = 'held';
    const ERROR = 'error';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::PENDING => _g('Pending'),
            static::PROCESSING => _g('Processing'),
            static::COMPLETE => _g('Complete'),
            static::CANCELLED => _g('Cancelled'),
            static::HELD => _g('Held'),
            static::ERROR => _g('Error')
        );
    }
}