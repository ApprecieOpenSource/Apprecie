<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 31/10/14
 * Time: 08:27
 */

namespace Apprecie\Library\Security;

use Phalcon\DI;
use Phalcon\Exception;

trait CSRFCheckTrait
{
    public function checkCSRF($requirePost = true, $allowAjax = true, $raiseException = false)
    {
        $request = DI::getDefault()->get('request');
        $legit = true;

        if($requirePost && !$request->isPost()) {
            $legit = false;
        }

        if(! $allowAjax && $request->isAjax()) {
            $legit = false;
        }

        if($legit) {
            $legit = (new CSRFProtection())->checkSessionToken();
        }

        if(! $legit && $raiseException) {
            throw new Exception('The request failed a csrf check.');
        }

        return $legit;
    }
}