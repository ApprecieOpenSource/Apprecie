<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/06/15
 * Time: 09:17
 */

namespace Apprecie\Library\Validation;

use Phalcon\DI\Injectable;

class NumericHelper extends Injectable
{
    public function moreThanNDecimals($number, $decimals = 2)
    {
        return (preg_match('/\.[0-9]{' . $decimals . ',}[1-9][0-9]*$/', (string)$number) > 0);
    }

    public function isWholeNumber($number)
    {
        if (!is_numeric($number)) {
            return false;
        }

        if (abs($number - round($number)) < 0.0001) {
            return true;
        }

        return false;
    }
} 