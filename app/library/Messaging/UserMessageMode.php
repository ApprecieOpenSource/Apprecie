<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 29/04/15
 * Time: 15:51
 */

namespace Apprecie\Library\Messaging;

use Apprecie\Library\Collections\Enum;

class UserMessageMode extends Enum
{
    const MESSAGE_ONLY = 'messageonly';
    const MESSAGE_AND_ALERT = 'messageandalert';
    const MESSAGE_AND_EMAIL = 'messageandemail';
    const MESSAGE_AND_EMAIL_AND_ALERT = 'messageandemailandalert';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::MESSAGE_ONLY => _g('Message only'),
            static::MESSAGE_AND_ALERT => _g('Message and alert'),
            static::MESSAGE_AND_EMAIL => _g('Message and Email'),
            static::MESSAGE_AND_EMAIL_AND_ALERT => _g('Message, email and alert')
        );
    }
} 