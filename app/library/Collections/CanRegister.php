<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/12/14
 * Time: 17:50
 */

namespace Apprecie\Library\Collections;


interface CanRegister
{
    public function getHash(Registry $registry = null);

    public function register(IsRegistry $register, $key, $name);
} 