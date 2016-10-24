<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/10/2015
 * Time: 12:00
 */

namespace Apprecie\Library\Validation;


abstract class RecordValidator extends ValidationBase
{
    protected $_validationMode = null;

    public function validate(ValidationModeEnum $mode)
    {
        $this->_validationMode = $mode;
        $this->_validationRun = false;
        $this->_isValid = $this->doValidation($this->_subject);
        $this->_validationRun = true;
        return $this;
    }
}