<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/01/15
 * Time: 11:09
 */

namespace Apprecie\Library;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\DI;

trait DBConnection
{
    /**
     * @return Mysql
     */
    public static function getDbAdapter()
    {
        return DI::getDefault()->get('db');
    }
} 