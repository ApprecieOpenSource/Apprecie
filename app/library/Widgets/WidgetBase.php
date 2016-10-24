<?php
namespace Apprecie\Library\Widgets;

use Apprecie\Library\Security\Authentication;
use Apprecie\Library\Security\CSRFCheckTrait;
use Phalcon\DI\Injectable;

abstract class WidgetBase extends Injectable
{
    use CSRFCheckTrait;

    protected $_action = null;
    protected $_params = null;
    protected $_errorMessage = "";
    protected $_doCache = null;
    private $_halt = false;

    /**
     * @param null|string $action
     */
    public function setAction($action)
    {
        $this->_action = $action;
    }

    /**
     * @return null|string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->_errorMessage = $errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * @param boolean $halt
     */
    public function setHalt($halt)
    {
        $this->_halt = $halt;
    }

    /**
     * @return boolean
     */
    public function getHalt()
    {
        return $this->_halt;
    }

    /**
     * @param array|null $params
     */
    public function setParams($params)
    {
        $this->_params = $params;
    }

    /**
     * @return array|null
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * If called during a dispatched widget action method will cache the return of the action (the response)
     * for $seconds
     *
     * Pass null to invalidate within the cache.
     *
     * @param $seconds
     * @throws \InvalidArgumentException
     */
    public function doCacheForSeconds($seconds)
    {
        if ($seconds == null) {
            $this->_doCache = null;
        } elseif (!is_int($seconds)) {
            throw new \InvalidArgumentException('doCacheForSeconds() expected null to cancel caching or an integer');
        }

        $this->_doCache = $seconds;
    }

    // return false to halt execution - called from constructor
    //a halted widget will be invisible but not block - i.e output empty string
    public function initWidget()
    {
    }

    abstract public function doIndex();

    public function _($paramName)
    {
        if (isset($this->_params[$paramName])) {
            return $this->_params[$paramName];
        }

        return "";
    }

    public function __construct($action = 'index', $params = null)
    {
        if ($params == null) {
            $params = array();
        }
        if (!is_array($params)) {
            $params = array($params);
        }
        $this->_params = $params;
        $this->_action = $action;

        if (!$this->canDispatch($action)) {
            $this->_errorMessage = 'Widget ' . get_class($this) . ' action ' . $action . ' not found';
        } else {
            $this->_halt = $this->initWidget() === false;
        }
    }

    protected function dispatch($action)
    {
        //we are looking for a do method i.e.  do$action
        if (!$this->canDispatch($action)) {
            $this->_errorMessage = 'Widget ' . get_class($this) . ' action ' . $action . ' not found';
            return $this->_errorMessage;
        }

        $actionMethod = "do" . ucfirst($action);

        return $this->getCacheOrAction($actionMethod);
    }

    protected function getCacheOrAction($actionMethod)
    {
        $cacheKey = get_class($this) . '_' . $actionMethod;
        $cache = $this->getDI()->get('cache');
        $content = $cache->get($cacheKey);

        if ($content == null) {
            $this->_doCache = null;
            $content = $this->$actionMethod();
            if ($this->_doCache != null && is_int($this->_doCache)) {
                $cache->save($cacheKey, $content, $this->_doCache);
            } elseif ($this->_doCache == null) {
                $cache->delete($cacheKey);
            }
        }

        return $content;
    }

    protected function canDispatch($action)
    {
        $actionMethod = "do" . ucfirst($action);
        return method_exists($this, $actionMethod);
    }

    public function getContent()
    {
        if ($this->_halt) {
            return "";
        }

        if ($this->_errorMessage != "") {
            return $this->_errorMessage;
        } else {
            return $this->dispatch($this->_action);
        }
    }

    /**
     * @return Authentication
     */
    public function getAuth()
    {
        return $this->di->get('auth');
    }
}