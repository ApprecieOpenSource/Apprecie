<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 09/12/14
 * Time: 09:56
 */

namespace Apprecie\Library\Security;

class IPTools
{
    protected static $_envVars = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    );

    /**
     * Uses several environment vars as fallbacks to consider aol users and proxy users.
     *
     * Might return null.
     *
     * @static
     * @param null $sources
     * @return strong|null The IP address or null if it could not be determined
     */
    public static function getClientIPAddress($sources = null)
    {
        if ($sources == null) {
            $sources = static::$_envVars;
        }

        foreach ($sources as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return null;
    }

    public static function getHostFromIp($ip = null)
    {
        if ($ip == null) {
            $ip = static::getClientIPAddress();
        }
        return gethostbyaddr($ip);
    }

    public static function getBrowser()
    {
        return getenv("HTTP_USER_AGENT");
    }

    public static function getServerReportedIP()
    {
        return static::getClientIPAddress(array('REMOTE_ADDR'));
    }

    public static function getClientReportedIP()
    {
        return static::getClientIPAddress(array('HTTP_CLIENT_IP'));
    }

    public static function getForwardedIP()
    {
        return static::getClientIPAddress(
            array(
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
            )
        );
    }
}