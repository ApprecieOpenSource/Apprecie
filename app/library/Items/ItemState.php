<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 13:35
 */

namespace Apprecie\Library\Items;

use Apprecie\Library\Collections\Enum;

class ItemState extends Enum
{
    const DRAFT = 'draft';
    const APPROVING = 'approving';
    const APPROVED = 'approved';
    const DENIED = 'denied';
    const ARRANGING = 'arranging';
    const HELD = 'held';
    const CLOSED = 'closed';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::DRAFT => _g('Draft'),
            static::APPROVING => _g('Approving'),
            static::APPROVED => _g('Approved'),
            static::DENIED => _g('Denied'),
            static::ARRANGING => _g('Arranging'),
            static::HELD => _g('Held'),
            static::CLOSED => _g('Closed')
        );
    }
}