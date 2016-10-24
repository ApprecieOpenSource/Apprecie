<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/10/14
 * Time: 16:47
 */

namespace Apprecie\Library\Users;


interface ApprecieUser
{
    public function getUserProfile();

    public function getUser();

    public function getUserLogin();

    public function getPortalUser();

    public function getUserGUID();

    public function getUserReference();

    public function getActiveRole();

    public function hasRole($role);

    public function addRole($role);

    public function getUserContactPreferences();

    public function getUserDietaryRequirements();

    public function getUserId();
} 