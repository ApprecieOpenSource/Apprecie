<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 04/12/14
 * Time: 00:55
 */

namespace Apprecie\Library\Security;


class EncryptionManager
{
    /**
     * @param $keyAsText
     * @param string $cypher
     * @param string $encryptionClass
     * @return EncryptionProvider
     */
    public static function get(
        $keyAsText,
        $cypher = MCRYPT_RIJNDAEL_256,
        $encryptionClass = 'Apprecie\Library\Security\Encryption'
    ) {
        return new $encryptionClass($keyAsText, $cypher);
    }
} 