<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 26/01/15
 * Time: 16:25
 */

namespace Apprecie\Library\Users;

use Apprecie\Library\Collections\Enum;

class UserStatus extends Enum
{
    const ACTIVE = 'active';
    const PENDING = 'pending';
    const DEACTIVATED = 'deactivated';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::ACTIVE => _g('Active'),
            static::PENDING => _g('Pending'),
            static::DEACTIVATED => _g('Deactivated')
        );
    }
}