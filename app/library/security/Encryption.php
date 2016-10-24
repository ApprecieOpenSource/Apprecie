<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 18/11/14
 * Time: 16:56
 */

namespace Apprecie\Library\Security;

use Phalcon\DI\Injectable;

class Encryption extends Injectable implements EncryptionProvider
{
    protected $_secureKey;
    protected $_cipher;

    public function __construct($keyAsText, $cipher = MCRYPT_RIJNDAEL_256)
    {
        $this->_secureKey = hash('sha256', $keyAsText, true);
        $this->_cipher = $cipher;
    }

    //@todo  is ECB acceptable?
    public function encrypt($input)
    {
        return base64_encode(mcrypt_encrypt($this->_cipher, $this->_secureKey, $input, MCRYPT_MODE_ECB));
    }

    public function decrypt($input)
    {
        return trim(mcrypt_decrypt($this->_cipher, $this->_secureKey, base64_decode($input), MCRYPT_MODE_ECB));
    }
}