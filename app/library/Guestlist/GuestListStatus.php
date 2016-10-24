<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 21/12/14
 * Time: 15:50
 */

namespace Apprecie\Library\Guestlist;

use Apprecie\Library\Collections\Enum;

class GuestListStatus extends Enum
{
    const CONFIRMED = 'confirmed';
    const DECLINED = 'declined';
    const CANCELLED = 'cancelled';
    const REVOKED = 'revoked';
    const PENDING = 'invited';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::CONFIRMED => _g('Confirmed'),
            static::DECLINED => _g('Declined'),
            static::CANCELLED => _g('Cancelled'),
            static::REVOKED => _g('Revoked'),
            static::PENDING => _g('Invited')
        );
    }
}