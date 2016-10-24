<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 25/09/2015
 * Time: 14:55
 */

namespace Apprecie\library\Users;

use Apprecie\Library\Collections\CanRegister;
use Apprecie\Library\Collections\Registry;

class UserEntityRegistry extends Registry
{
    public function __construct($key)
    {
        parent::__construct($key);
    }

    /**
     * @param \Apprecie\Library\Collections\CanRegister $source
     * @return ApprecieUserBase
     */
    public function getInstance(CanRegister $source)
    {
        $key = $source->getHash($this);
        if (!array_key_exists($key, $this->_entries)) {
            $source->register($this, $key, $this->_name);
        }

        return $this->_entries[$key];
    }
}