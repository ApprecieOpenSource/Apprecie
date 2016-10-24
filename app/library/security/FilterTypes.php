<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/11/2015
 * Time: 12:42
 */

namespace Apprecie\Library\Security;

use Apprecie\Library\Collections\Enum;

class FilterTypes extends Enum
{
    const STRING = 'string';
    const EMAIL = 'email';
    const INT = 'int';
    const FLOAT = 'float';
    const APLHANUM = 'alphanum';
    const STRIPTAGS = 'striptags';
    const TRIM = 'trim';
    const LOWER = 'lower';
    const UPPER = 'upper';
}