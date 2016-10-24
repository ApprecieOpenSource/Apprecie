<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 15/01/15
 * Time: 13:46
 */

namespace Apprecie\Library\Items;

use Apprecie\Library\Collections\Enum;

class TargetGender extends Enum
{
    const MALE = 'male';
    const FEMALE = 'female';
    const MIXED = 'mixed';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::MALE => _g('Male'),
            static::FEMALE => _g('Female'),
            static::MIXED => _g('Mixed')
        );
    }
} 