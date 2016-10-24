<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 28/09/2015
 * Time: 10:31
 */

namespace Apprecie\library\Debug;


class Backtrace
{
    /**
     * http://stackoverflow.com/questions/190421/caller-function-in-php-5
     * @param bool|false $completeTrace
     * @return string
     */
    public static function getCallingFunctionName($completeTrace = false)
    {
        $trace = debug_backtrace();
        if ($completeTrace) {
            $str = '';
            foreach ($trace as $caller) {
                $str .= " -- Called by {$caller['function']}";
                if (isset($caller['class'])) {
                    $str .= " From Class {$caller['class']}";
                }
            }
        } else {
            $caller = $trace[2];
            $str = "Called by {$caller['function']}";
            if (isset($caller['class'])) {
                $str .= " From Class {$caller['class']}";
            }
        }
        return $str;
    }
}