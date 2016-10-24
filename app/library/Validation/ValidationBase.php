<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 27/10/14
 * Time: 11:05
 */

namespace Apprecie\Library\Validation;

use Apprecie\Library\Messaging\MessageQueue;
use Apprecie\Library\Messaging\MessagingTrait;
use Phalcon\Validation;
use Respect\Validation\Validator as v;

abstract class ValidationBase implements MessageQueue
{
    use MessagingTrait;

    protected $_isValid = false;
    protected $_validationRun = false;
    protected $_subject;

    public function __construct($subject) {
        $this->_subject = $subject;
    }

    public function getIsValid()
    {
        return $this->_isValid;
    }

    public function getValidationHasRun()
    {
        return $this->_validationRun;
    }

    protected abstract function doValidation($obj);
} 