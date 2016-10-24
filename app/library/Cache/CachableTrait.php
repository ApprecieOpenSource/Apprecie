<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 31/10/2015
 * Time: 09:32
 */

namespace Apprecie\Library\Cache;

use Phalcon\DI;

trait CachableTrait
{
    protected static $_cache = [];
    protected static $_cacheMode = CachingMode::InMemory;
    protected static $_cacheLifeTime = 3600;

    public function nothing()
    {

    }

    public static function setCachingMode($mode)
    {
        static::$_cacheMode = $mode;
    }

    public static function getCachingMode()
    {
        return static::$_cacheMode;
    }

    public static function setCacheLifetime($seconds)
    {
        static::$_cacheLifeTime = $seconds;
    }

    public static function getCacheLifetime()
    {
        return static::$_cacheLifeTime;
    }

    /**
     * @return Wincache
     */
    public static function getCache()
    {
        return DI::getDefault()->get('cache');
    }

    public static function writeToCache($cacheScope, $key, $content)
    {
        if(static::getCachingMode() == CachingMode::InMemory) {
            if (!array_key_exists($cacheScope, static::$_cache)) {
                static::$_cache[$cacheScope] = [];
            }

            static::$_cache[$cacheScope][$key] = $content;
        } elseif(static::getCachingMode() == CachingMode::Persistent) {
            $cache = static::getCache();
            $cache->save($key, $content, static::getCacheLifetime());
        }
    }

    public static function readFromCache($cacheScope, $key)
    {
        if(static::getCachingMode() == CachingMode::InMemory) {
            if (!array_key_exists($cacheScope, static::$_cache)) {
                return null;
            }

            if (!array_key_exists($key, static::$_cache[$cacheScope])) {
                return null;
            }

            return static::$_cache[$cacheScope][$key];
        } elseif(static::getCachingMode() == CachingMode::Persistent) {
            return static::getCache()->get($key);
        }
    }

    public static function removeFromCache($cacheScope, $key)
    {
        if(static::getCachingMode() == CachingMode::InMemory) {
            if (isset(static::$_cache[$cacheScope][$key])) {
                unset(static::$_cache[$cacheScope][$key]);
                return true;
            }

            return false;
        } elseif(static::getCachingMode() == CachingMode::Persistent) {
            return static::getCache()->delete($key);
        }
    }

    public static function cacheHasKey($cacheScope, $key)
    {
        if(static::getCachingMode() == CachingMode::InMemory) {
            return isset(static::$_cache[$cacheScope][$key]);
        } elseif(static::getCachingMode() == CachingMode::Persistent) {
            return static::getCache()->exists($key);
        }
    }

    public static function clearCache($cacheScope = null)
    {
        if(static::getCachingMode() == CachingMode::InMemory) {
            if ($cacheScope == null) {
                static::$_cache = [];
            } else {
                static::$_cache[$cacheScope] = [];
            }
        } elseif(static::getCachingMode() == CachingMode::Persistent) {
            static::getCache()->flush();
        }
    }

    /**
     * Implement a method that returns a string key
     */
    protected static function createCacheKey($data, $prefix = 'f')
    {
        $uniqueKey = array();
        $class = get_called_class();

        if(! is_array($data)) {
            $data = array($data);
        }

        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $uniqueKey[] = $key . ':' . $value;
            } else {
                if (is_array($value)) {
                    $uniqueKey[] = $key . ':[' . static::createCacheKey($value) .']';
                }
            }
        }

        $detail = join('_', $uniqueKey);
        $cacheKey = $prefix . '_' . $class . '_' . $detail;

        return $cacheKey;
    }
}