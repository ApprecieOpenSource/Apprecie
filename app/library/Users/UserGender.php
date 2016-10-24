<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/12/14
 * Time: 17:27
 */

namespace Apprecie\Library\Users;

use Apprecie\Library\Collections\Enum;

class UserGender extends Enum
{
    const MALE = 'male';
    const FEMALE = 'female';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::MALE => _g('Male'),
            static::FEMALE => _g('Female')
        );
    }
} 