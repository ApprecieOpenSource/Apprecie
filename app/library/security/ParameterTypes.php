<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/11/2015
 * Time: 12:59
 */

namespace Apprecie\Library\Security;

use Apprecie\Library\Collections\Enum;

class ParameterTypes extends Enum
{
    const ANY = 'any';
    const INT = 'int';
    const FLOAT = 'float';
    const NUMBER = 'number';
}