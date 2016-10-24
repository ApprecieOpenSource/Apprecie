<?php
namespace Apprecie\Library\Http;

use Phalcon\DI;

class HTTPCache
{
    protected static $_headersSent = [];

    public static function privateContent()
    {
        if(count(static::$_headersSent) == 0) {
            $response = DI::getDefault()->get('response');
            $response->setHeader('Cache-Control', 'private, max-age=0, must-revalidate');
            static::$_headersSent[] = 'private';
        }
    }

    public static function allowCache($ttlSeconds) {
        if(count(static::$_headersSent) == 0) {
            $response = DI::getDefault()->get('response');
            $response->setHeader('Cache-Control', 'max-age=' . $ttlSeconds .  '86400');
            static::$_headersSent[] = 'cache';
        }
    }
}