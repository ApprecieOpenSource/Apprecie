<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 15/09/2015
 * Time: 16:43
 */

namespace Apprecie\library\CriticalSection;

use Apprecie\Library\DBConnection;

class Lock
{
    use DBConnection;

    private $_lockName = '';

    public function __construct($name)
    {
        $this->_lockName = $name;
    }

    public function getLock($timeoutSeconds = 10)
    {
        $db = $this->getDbAdapter();
        $db->query("SELECT GET_LOCK('" . $this->_lockName . "', " . $timeoutSeconds . ")")->fetch();
    }

    public function releaseLock()
    {
        $db = $this->getDbAdapter();
        $db->query("SELECT RELEASE_LOCK('" . $this->_lockName . "')")->fetch();
    }
}