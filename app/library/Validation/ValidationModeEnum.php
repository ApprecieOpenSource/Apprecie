<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/10/2015
 * Time: 11:57
 */

namespace Apprecie\Library\Validation;
use Apprecie\Library\Collections\Enum;

class ValidationModeEnum extends Enum
{
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
}