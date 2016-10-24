<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 23/10/14
 * Time: 12:21
 */

namespace Apprecie\Library\Users;


trait UserRoleTrait
{
    public function hasRole($role)
    {
        return $this->getUser()->hasRole($role);
    }

    public function addRole($role)
    {
        return $this->getUser()->setRole($role);
    }

    public function getActiveRole()
    {
        return $this->getUser()->getActiveRole();
    }
} 