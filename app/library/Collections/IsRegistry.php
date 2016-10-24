<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 10/12/14
 * Time: 23:04
 */
namespace Apprecie\Library\Collections;


/**
 * Simple registry supports setInstance and getInstance.
 *
 * getInstance expects an object supporting CanRegister interface, so if the instance requested does not exist
 * in the registry it will be created by calling the subjects register() method which must in turn call setInstance
 * on the registry instance.
 *
 * Class Registry
 * @package Apprecie\Library\Collections
 */
interface IsRegistry
{
    public function getName();

    public function getInstance(CanRegister $source);

    public function setInstance($key, $item);
}