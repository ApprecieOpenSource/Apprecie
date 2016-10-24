<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/11/2015
 * Time: 13:04
 */

namespace Apprecie\Library\Security;

use Apprecie\Library\Collections\Enum;

class ParameterAjax extends Enum
{
    const ANY = 'any';
    const AJAX_REQUIRED = 'ajax_require';
    const AJAX_DENIED = 'ajax_denied';
}