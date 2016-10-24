<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 16/11/2015
 * Time: 10:59
 */

namespace Apprecie\library\Phql;


class PhqlCacheCleaner extends \Phalcon\Mvc\Model\Query{
    public static function clean(){
        self::$_irPhqlCache = array();
    }
}