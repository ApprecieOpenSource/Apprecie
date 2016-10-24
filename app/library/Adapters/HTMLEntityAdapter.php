<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/07/15
 * Time: 09:22
 */

namespace Apprecie\Library\Adapters;

use Apprecie\Library\Model\ApprecieModelBase;

class HTMLEntityAdapter extends BaseGetSetAdapter
{
    function __construct($object, $excludes = null)
    {
        if($object instanceOf ApprecieModelBase) {
            $object->setAutoHtmlEncode(true); //automate toArray() encoding
        }

        parent::__construct($object, $excludes);
    }

    protected function getResult($function, $args, $value)
    {
        return _s($value);
    }

    protected function setResult($function, $args, $value)
    {
        throw new \LogicException('This adapter should be used as read only for converting html entities for output');
    }
}