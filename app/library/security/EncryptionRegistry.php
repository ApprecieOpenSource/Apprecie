<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/12/14
 * Time: 09:42
 */

namespace Apprecie\Library\Security;

use Apprecie\Library\Collections\CanRegister;
use Apprecie\Library\Collections\Registry;

class EncryptionRegistry extends Registry
{
    public function __construct()
    {
        parent::__construct('modelencryption');
    }

    /**
     * @param \Apprecie\Library\Collections\CanRegister $source
     * @return EncryptionProvider
     */
    public function getInstance(CanRegister $source)
    {
        return parent::getInstance($source);
    }
}