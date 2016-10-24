<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 12/06/15
 * Time: 16:02
 */

namespace Apprecie\Library\Response;

use Phalcon\Http\Response;

class ResponseEx extends Response
{
    public function send()
    {
        parent::send();
        exit(0); //force exit after send.
    }

    public function redirect($location = null, $externalRedirect = null, $statusCode = null)
    {
        $request = $this->getDI()->getDefault()->get('request');
        $isAjax = $request->isAjax();
        if ($isAjax) {
            if ($location) {
                $this->setStatusCode(287, 'Redirect');
                $this->setHeader('X-Redirect-URL', $location);
            }
        } else {
            return parent::redirect($location, $externalRedirect, $statusCode);
        }
    }
}