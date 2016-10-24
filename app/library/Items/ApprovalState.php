<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/02/15
 * Time: 18:36
 */

namespace Apprecie\Library\Items;

use Apprecie\Library\Collections\Enum;

class ApprovalState extends Enum
{
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const DENIED = 'denied';
    const UNPUBLISHED = 'unpublished';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::PENDING => _g('Pending'),
            static::APPROVED => _g('Approved'),
            static::DENIED => _g('Denied'),
            static::UNPUBLISHED => _g('Unpublished')
        );
    }
} 