<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 09/11/14
 * Time: 16:33
 */

namespace Apprecie\Library\Request;

use Phalcon\DI;

/**
 * Simple Utility containg some URL functions
 * Class Url
 * @package Apprecie\Library\Request
 */
class Url
{
    public static function getRequestURL($includeProtocol = true)
    {
        if (!$includeProtocol) {
            return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        $uri_is_absolute = (strpos($_SERVER['REQUEST_URI'], '://') != false);

        if (!$uri_is_absolute) {
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'Http://';
            $uri = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $uri = $_SERVER['REQUEST_URI'];
        }

        return $uri;
    }


    /**
     * Extract the first subdomain from the host
     *
     * @todo GH this si flawed think more complex domains.
     * @param null $host
     * @return mixed
     */
    public static function getSubdomain($host = null)
    {
        if ($host == null) {
            $host = $_SERVER['HTTP_HOST'];
        }

        $parsed = parse_url($host);

        $result = explode('.', $parsed['path']);

        return $result[0];
    }

    /**
     * Returns a url based on the active portal
     *
     * @param null $portal
     * @param null $page
     * @param string $action
     * @param null $params
     * @param string $protocol
     * @return string
     */
    public static function getConfiguredPortalAddress(
        $portal = null,
        $page = null,
        $action = 'index',
        $params = null,
        $protocol = 'https'
    ) {
        if ($portal == null) {
            $portal = DI::getDefault()->get('portal');
        } else {
            $portal = \Portal::resolve($portal);
        }

        $url = $portal->getPortalSubDomain() . '.' .
            DI::getDefault()
                ->get('config')
                ->domains
                ->system;

        if ($page != null) {
            $url .= "/{$page}";
            if ($action != null) {
                $url .= "/{$action}";
            }
        }

        $url = strtolower($url);

        if ($params != null) {
            if (!is_array($params)) {
                $params = array($params);
            }

            if (static::is_assoc($params)) {
                $url .= '?' . http_build_query($params);
            } else {
                foreach ($params as $param) {
                    $url .= '/' . rawurlencode($param);
                }
            }
        }

        return $protocol . '://' . $url;
    }

    public static function is_assoc($array)
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }
} 