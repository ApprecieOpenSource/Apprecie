<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 17:25
 */

namespace Apprecie\Library\Items;

use Apprecie\Library\Collections\Enum;

class ItemDestination extends Enum
{
    const PRIVATE_ITEM = 'private';
    const PARENT_ITEM = 'parent';
    const CURATED_ITEM = 'curated';

    protected $_name = null;
    protected $_strings = array();

    function __construct($name)
    {
        $this->_name = $name;
        $this->_strings = array(
            static::PRIVATE_ITEM => _g('Private'),
            static::PARENT_ITEM => _g('Parent'),
            static::CURATED_ITEM => _g('Curated')
        );
    }
} 