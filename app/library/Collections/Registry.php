<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/12/14
 * Time: 11:38
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
class Registry implements IsRegistry
{
    protected $_entries = [];
    protected $_name = null;

    public function getName()
    {
        return $this->_name;
    }

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function getInstance(CanRegister $source)
    {
        $key = $source->getHash($this);
        if (!array_key_exists($key, $this->_entries)) {
            $source->register($this, $key, $this->_name);
        }

        return $this->_entries[$key];
    }

    public function setInstance($key, $item)
    {
        $this->_entries[$key] = $item;
    }
} 