<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 21/12/14
 * Time: 15:58
 */

namespace Apprecie\Library\Orders;

use Apprecie\Library\Collections\Enum;

class OrderItemStatus extends Enum
{
    const FULL = 'full';
    const RESERVATION = 'reservation';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::FULL => _g('Full'),
            static::RESERVATION => _g('Reservation')
        );
    }
}