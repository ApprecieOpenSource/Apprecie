<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/01/15
 * Time: 11:53
 */

namespace Apprecie\Library\Utility;

use Apprecie\Library\Messaging\PrivateMessageQueue;

trait StringParsing
{
    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    /**
     * parses $string for blocks of content appearing between $starttag and $endtag
     * Will parse all matching blocks and return as array.
     *
     * @return Array The blocks of content parsed from $string
     *
     * @param string $string This is the content to be parsed, for example this could be the HTML from the buffer
     * @param string $starttag This is the start tag, the beginning of a returnable content block i.e <!--customtag  or <img
     * @param string $endtag The end of block of content.
     */
    protected function parseBlocks($string, $starttag, $endtag)
    {
        $pattern = "/" . preg_quote($starttag) . '(.*?)' . preg_quote($endtag) . "/";
        if (preg_match_all($pattern, $string, $matches, PREG_PATTERN_ORDER) === false) {
            if ($this instanceof PrivateMessageQueue) {
                $this->appendMessageEx(preg_last_error());
            }
        }
        return $matches[1];
    }

    /**
     * parses $string for a block of content appearing between $starttag and $endtag
     * Will parse only the first matching block found
     *
     * This method is an alternative to ParseBlocks, using native php functions rather then regexp.
     * This method is faster and more reliable with complex tokens especially if only one block is to be parsed.
     *
     * @param string $string This is the content to be parsed, for example this could be the HTML from the buffer
     * @param string $start This is the start token, the beginning of a returnable content block i.e <!--customtag  or <img
     * @param string $end The end of token of content.
     *
     * @return string The block parsed from $string
     */
    protected function getStringBetween($string, $start, $end)
    {
        $string = " " . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return "";
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
} 