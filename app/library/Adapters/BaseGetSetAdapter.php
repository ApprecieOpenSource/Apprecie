<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 01/07/15
 * Time: 11:24
 */

namespace Apprecie\Library\Adapters;


abstract class BaseGetSetAdapter
{
    protected $_object = null;
    protected $_excludes = array();

    function __construct($object, $excludes = null)
    {
        if(! is_object($object)) {
            throw new \LogicException('GetSetAdapter needs an object');
        }

        $this->setObject($object);

        if($excludes != null) {
            $this->setExcludes($excludes);
        }
    }

    public function setExcludes(array $excludes)
    {
        $this->_excludes = $excludes;
    }

    public function getObject()
    {
        return $this->_object;
    }

    public function setObject($object)
    {
        $this->_object = $object;
    }

    function __call($func, $args)
    {
        return $this->process($func, $args);
    }

    /**
     * Processes get and set methods out to getResult and setResult which are expected to return the ultimate
     * return value.
     *
     * Override me to implement more specific method intercepts.
     *
     * @param $func
     * @param $args
     * @return mixed
     */
    protected function process($func, $args)
    {
        $result = call_user_func_array(array($this->_object, $func), $args);

        if(in_array($func, $this->_excludes)) {
            return $result;
        }


        if($this->startsWith($func, 'get')) {
            $result = $this->getResult($func, $args, $result);
        } elseif($this->startsWith($func, 'set')) {
            $result = $this->setResult($func, 'set', $result);
        }

        return $result;
    }

    private function startsWith($haystack, $needle)
    {
        return $needle === "" || strripos($haystack, $needle, -strlen($haystack)) !== false;
    }

    abstract protected function getResult($function, $args, $value);
    abstract protected function setResult($function, $args, $value);
} 