<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 04/02/15
 * Time: 13:58
 */

namespace Apprecie\Library\Request;

use Phalcon\Http\Request;

class RequestEx extends Request
{
    /**
     * As phalcon implementation but defaultValue is returned for $value == null even after filters
     * note this is not the same as phalcon which provides default only if value is not posted.
     *
     * @param null $name
     * @param null $filters
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getPost($name = null, $filters = null, $defaultValue = null)
    {
        $result = parent::getPost($name, $filters, $defaultValue);

        if ($defaultValue !== null) {
            if ($result == null) {
                $result = strval($defaultValue);
            }
            //intentional loose comparison
        }

        return $result;
    }

    /**
     * As phalcon implementation but defaultValue is returned for $value == null even after filters
     * note this is not the same as phalcon which provides default only if value is not posted.
     *
     * @param null $name
     * @param null $filters
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getQuery($name = null, $filters = null, $defaultValue = null)
    {
        $result = parent::getQuery($name, $filters, $defaultValue);

        if ($defaultValue !== null) {
            if ($result == null) {
                $result = strval($defaultValue);
            }
            //intentional loose comparison
        }

        return $result;
    }

} 