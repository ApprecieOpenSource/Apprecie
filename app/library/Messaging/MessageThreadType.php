<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 13:35
 */

namespace Apprecie\Library\Messaging;

use Apprecie\Library\Collections\Enum;

class MessageThreadType extends Enum
{
    const ARRANGEMENT = 'arrangement';
    const INVITATION = 'invitation';
    const HOST = 'host';
    const SUGGESTION = 'suggestion';
    const GENERIC = 'generic';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::ARRANGEMENT => _g('Event Arrangement'),
            static::INVITATION => _g('Event Invitation'),
            static::HOST => _g('Host Conversation'),
            static::SUGGESTION => _g('Event Suggestion'),
            static::GENERIC => _g('Conversation')
        );
    }
}