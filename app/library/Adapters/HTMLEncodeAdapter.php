<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 01/07/15
 * Time: 18:37
 */

namespace Apprecie\Library\Adapters;


use Apprecie\Library\Model\ApprecieModelBase;

class HTMLEncodeAdapter extends BaseGetSetAdapter
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
        if(is_string($value)) {
            return _eh($value);
        }

        return $value;
    }

    protected function setResult($function, $args, $value)
    {
        throw new \LogicException('This adapter should be used as read only for encoding html output');
    }
}