<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/02/2016
 * Time: 16:40
 */

namespace Apprecie\Library\Utility;

use Apprecie\Library\Security\Authentication;

trait UtilityTrait
{
    /**
     * @return \User | bool
     */
    public function getAuthenticatedUser()
    {
        return $this->getAuthentication()->getAuthenticatedUser();
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
        return $this->getDI()->get('auth');
    }

    /**
     * @return \Portal
     */
    public function getActivePortal()
    {
        return $this->getDI()->get('portal');
    }

    public function hasRole($role)
    {
        return $this->getAuthentication()->sessionHasRole($role);
    }
}