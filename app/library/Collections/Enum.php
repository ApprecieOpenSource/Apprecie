<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 22/12/14
 * Time: 09:11
 */

namespace Apprecie\Library\Collections;


abstract class Enum implements LocalisedEnum
{
    protected static $_reflection = null;

    protected $_name = null;
    protected $_strings = array();

    protected static function getReflection()
    {
        //if (static::$_reflection == null) {
            static::$_reflection = new \ReflectionClass(get_called_class());
        //}

        return static::$_reflection;
    }

    /**
     * @return array an associative array of the constant defined on this enum.
     */
    public static function getArray()
    {
        return static::getReflection()->getConstants();
    }

    /**
     * @param $value
     * @return string|bool Either the string name of the constant that holds value, or False.
     */
    public function getKeyByValue($value)
    {
        return array_search($value, static::getArray());
    }

    public function getText()
    {
        if(defined('static::' . $this->getKeyByValue($this->_name)) === false || array_key_exists($this->_name, $this->_strings) === false) {
            return '';
        } else {
            return $this->_strings[$this->_name];
        }
    }

    public function getTextByName($name)
    {
        if(defined('static::' . $this->getKeyByValue($name)) === false || array_key_exists($name, $this->_strings) === false) {
            return '';
        } else {
            return $this->_strings[$name];
        }
    }
}