<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 22/10/14
 * Time: 18:01
 */

namespace Apprecie\Library\Users;


use Phalcon\DI;

trait PortalSourcePrefixTrait
{
    protected static $_forcedSource = '';

    public static function setForcedSource($portalId)
    {
        static::$_forcedSource = $portalId;
    }

    public static function getSourcePrefix()
    {
        if (static::$_forcedSource != '') {
            $prefix = static::$_forcedSource;
        } else {
            $prefix = DI::getDefault()->get('portalid');
        }

        return $prefix;
    }

    protected function getEncryptionKey()
    {
        $key1 = static::getSourcePrefix();
        $key2 = $this->getDI()->get('fieldkey');
        return $key1 . $key2;
    }
}