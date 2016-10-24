<?php
/**
 * Created by PhpStorm.
 * User: huwang
 * Date: 23/06/2015
 * Time: 21:28
 */

namespace Apprecie\Library\Users;

use Apprecie\Library\Collections\Enum;

class UserRole extends Enum
{
    const SYS_ADMIN = 'SystemAdministrator';
    const PORTAL_ADMIN = 'PortalAdministrator';
    const MANAGER = 'Manager';
    const INTERNAL = 'Internal';
    const CLIENT = 'Client';
    const APPRECIE_SUPPLIER = 'ApprecieSupplier';
    const AFFILIATE_SUPPLIER = 'AffiliateSupplier';
    const CONTACT = 'Contact';

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::SYS_ADMIN => _g('System Administrator'),
            static::PORTAL_ADMIN => _g('Organisation Owner'),
            static::MANAGER => _g('Manager'),
            static::INTERNAL => _g('Internal Member'),
            static::CLIENT => _g('Client'),
            static::APPRECIE_SUPPLIER => _g('Apprecie Supplier'),
            static::AFFILIATE_SUPPLIER => _g('Affiliated Supplier'),
            static::CONTACT => _g('Contact')
        );
    }
}