<?php
namespace Apprecie\Library\Users;

class RoleHierarchy extends UserRole
{
    function __construct($roleName)
    {
        parent::__construct($roleName);
    }

    public function getVisibleRoles()
    {
        $data = [];

        switch ($this->_name) {
            case (static::SYS_ADMIN):
                $data = array(
                    static::SYS_ADMIN => $this->getTextByName(static::SYS_ADMIN),
                    static::PORTAL_ADMIN => $this->getTextByName(static::PORTAL_ADMIN),
                    static::MANAGER => $this->getTextByName(static::MANAGER),
                    static::INTERNAL => $this->getTextByName(static::INTERNAL),
                    static::CLIENT => $this->getTextByName(static::CLIENT),
                    static::APPRECIE_SUPPLIER => $this->getTextByName(static::APPRECIE_SUPPLIER),
                    static::AFFILIATE_SUPPLIER => $this->getTextByName(static::AFFILIATE_SUPPLIER)
                );
                break;
            case (static::PORTAL_ADMIN):
                $data = array(
                    static::PORTAL_ADMIN => $this->getTextByName(static::PORTAL_ADMIN),
                    static::MANAGER => $this->getTextByName(static::MANAGER),
                    static::INTERNAL => $this->getTextByName(static::INTERNAL),
                    static::CLIENT => $this->getTextByName(static::CLIENT),
                    static::APPRECIE_SUPPLIER => $this->getTextByName(static::APPRECIE_SUPPLIER),
                    static::AFFILIATE_SUPPLIER => $this->getTextByName(static::AFFILIATE_SUPPLIER)
                );
                break;
            case (static::MANAGER):
                $data = array(
                    static::INTERNAL => $this->getTextByName(static::INTERNAL),
                    static::CLIENT => $this->getTextByName(static::CLIENT)
                );
                break;
            case (static::INTERNAL):
                $data = array(
                    static::CLIENT => $this->getTextByName(static::CLIENT)
                );
                break;
            case (static::APPRECIE_SUPPLIER):
                break;
            case (static::AFFILIATE_SUPPLIER):
                break;
            case (static::CLIENT):
                break;
        }

        return $data;
    }
}