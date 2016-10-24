<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/11/14
 * Time: 09:40
 */
namespace Apprecie\Library\Security;

interface EncryptionProvider
{
    function encrypt($input);

    function decrypt($input);
}