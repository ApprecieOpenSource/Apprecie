<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/11/2015
 * Time: 12:39
 */

namespace Apprecie\Library\Security;

use Apprecie\Library\Messaging\PrivateMessageQueue;

class RequestFilter extends PrivateMessageQueue
{
    protected $_filters = [];
    protected $_requiredArguments = [];
    protected $_filteredValues = [];
    protected $_hasRun = false;
    protected $_requireHTTPS = true;

    public function __construct($requireHTTPS = true)
    {
        $this->_requireHTTPS = $requireHTTPS;
    }

    public function addFilter($argument, $filter)
    {
        $this->_filters[$argument] = $filter;
        return $this;
    }

    public function addRequired($argument, $type = 'any', $postOnly = false, $ajax = 'false')
    {
        $this->_requiredArguments[$argument] = [true, $type, $postOnly, $ajax, null];
        return $this;
    }

    public function addNonRequestRequired($name, $value, $type = 'any', $ajax = 'false')
    {
        $this->_requiredArguments[$name] = [false, $type, false, $ajax, $value];
        return $this;
    }

    /**
     * processes required first, and then processes filters.
     *
     * If $exceptionOnFailRequired is true will throw an exception on csrf or https failure before processing
     * parameters, else check for return false, and extract messages.
     *
     * @param \Phalcon\Http\RequestInterface $request
     * @param bool|true $exceptionOnFailRequired fires exception on first failure, dont handle for error redirect
     * @return $this
     * @throws \Exception
     */
    public function execute(\Phalcon\Http\RequestInterface $request, $exceptionOnFailRequired = true, $checkCSRF = true)
    {
        $failedCsrf = false;
        $failedHTTPS = false;

        if($this->_requireHTTPS) {
            if(! $request->isSecureRequest()) {
                $this->appendMessageEx('The request did not come from a secure channel');

                if($exceptionOnFailRequired) {
                    throw new \Exception('Expected secure request');
                }
                $failedHTTPS = true;
            }
        }

        if($checkCSRF) {
            if(! (new CSRFProtection())->checkSessionToken()) {
                if($exceptionOnFailRequired) {
                    throw new \Exception('CSRF check failed');
                } else {
                    $failedCsrf = true;
                    $this->appendMessageEx('The CSRF check failed');
                }
            }
        }

        foreach($this->_requiredArguments as $required=>$value) {
            list($fromRequest, $type, $postOnly, $ajax, $val) = $this->_requiredArguments[$required];

            $val = $fromRequest == false ? $val : $request->get($required);

            if($postOnly && ($request->getPost($required) == null || $request->getQuery($required) != null)) {
                $this->appendMessageEx("Expected required param {$required} to be in post array only. Invalid Route or not provided.");
                continue;
            }

            if($ajax == ParameterAjax::AJAX_DENIED && $request->isAjax()) {
                $this->appendMessageEx("Required param {$required} is blocked to Ajax.");
                continue;
            } elseif($ajax == ParameterAjax::AJAX_REQUIRED && ! $request->isAjax()) {
                $this->appendMessageEx("Required param {$required} is open only to Ajax.");
                continue;
            }

            if(! $this->typeTest($val, $type)) {
                $this->appendMessageEx("Required param {$required} should have been of type {$type}.");
                continue;
            }

            //we passed add the value to the filter for retrieval
            $this->_filteredValues[$required] = $val;
        }

        if($exceptionOnFailRequired) {
            if($this->hasMessages()) {
                throw new \Exception('Action parameters failed filtering : ' . _ms($this));
            }
        }

        //run filters
        foreach($this->_filters as $name=>$filter) {
            if($request->has($name)) {
                $this->_filteredValues[$name] = $request->get($name, $filter);
            } else {
                $this->_filteredValues[$name] = null;
            }
        }

        $this->_hasRun = true;

        if($failedCsrf || $failedHTTPS) {
            return false;
        }

        return !$this->hasMessages();
    }

    public function get($argument)
    {
        if($this->_hasRun) {
            if(array_key_exists($argument, $this->_filteredValues)) {
                return $this->_filteredValues[$argument];
            } else {
                return null;
            }
        } else {
            throw new \LogicException('Please run before fetching filtered values');
        }
    }

    /**
     * return all params in an array in the order that they were added
     * i.e.  list($param1, $param2, $param3) = $filter->getAll();
     * @return array
     */
    public function getAll()
    {
        if($this->_hasRun) {
            return array_values($this->_filteredValues);
        } else {
            throw new \LogicException('Please run before fetching filtered values');
        }
    }

    protected function typeTest($value, $type)
    {
        switch($type)
        {
            case ParameterTypes::INT : {
                return filter_var($value, FILTER_VALIDATE_INT) != false;
                break;
            }

            case ParameterTypes::FLOAT : {
                return is_float($value);
                break;
            }

            case ParameterTypes::NUMBER : {
                return is_numeric($value);
                break;
            }

            case ParameterTypes::ANY : {
                return $value != null;
                break;
            }

            default : {
                throw new \InvalidArgumentException('Unrecognised parameter type ' . $type . ' in ParamFilter');
            }
        }
    }
}