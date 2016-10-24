<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 14:50
 */

namespace Apprecie\Library\Portals;

use Apprecie\Library\Collections\Enum;

class PortalEditions extends Enum
{
    const FREEMIUM_PRO = 'FreemiumPro';
    const PROFESSIONAL = 'Professional';
    const ENTERPRISE = 'Enterprise';
    const VIP = 'VIP';
    const SUPPLIER = 'Supplier';
    const SYSTEM = 'System';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::FREEMIUM_PRO => _g('FreemiumPro'),
            static::PROFESSIONAL => _g('Professional'),
            static::ENTERPRISE => _g('Enterprise'),
            static::VIP => _g('VIP'),
            static::SUPPLIER => _g('Supplier'),
            static::SYSTEM => _g('System')
        );
    }
}