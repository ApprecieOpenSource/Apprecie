<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 18/03/15
 * Time: 20:25
 */

namespace Apprecie\Library\Items;

use Apprecie\Library\Collections\Enum;

class UserItemState extends Enum
{
    const OWNED = 'owned';
    const RESERVED = 'reserved';
    const HELD = 'held';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::OWNED => _g('Owned'),
            static::RESERVED => _g('Reserved'),
            static::HELD => _g('Held')
        );
    }
} 