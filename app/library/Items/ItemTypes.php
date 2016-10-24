<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 13:29
 */

namespace Apprecie\Library\Items;

use Apprecie\Library\Collections\Enum;

class ItemTypes extends Enum
{
    const EVENT = 'event';
    const OFFER = 'offer';
    const BY_ARRANGEMENT = 'by-arrangement';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::EVENT => _g('Event'),
            static::OFFER => _g('Offer'),
            static::BY_ARRANGEMENT => _g('By-arrangement')
        );
    }
} 