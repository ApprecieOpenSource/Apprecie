<?php
namespace Apprecie\Library\Security;

/**
 * Utility class provides some filter and encoding methods.
 *
 * Class Filters
 * @package Apprecie\Library
 */
class Filters
{
    /**
     * list of constants and reference from here : Http://stackoverflow.com/questions/12062146/is-json-encode-sufficient-xss-protection
     *
     * @param $value
     * @param bool $htmlEncode
     * @return string
     */
    public static function safeForJavascript($value, $htmlEncode = false)
    {
        if ($htmlEncode == false) {
            return json_encode($value, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        }

        return static::safeForJavascript(static::safeForHTMLOutput($value), false);
    }

    /**
     *  removes all html tags, converts all html entities to text equivalents
     * and returns a utf8 string.
     *
     * @param string $html The text to be stripped of html.
     * @return string
     */
    public static function htmlToTextUTF8($html)
    {
        return html_entity_decode(static::stripHTMLTags($html), ENT_QUOTES, "UTF-8");
    }

    /**
     * Simple applies html entities to the $value, in the most secure way.
     *
     * Note that there is a global shortcut to this method _s($value);
     *
     * Assumes and returns utf8
     *
     * @param mixed $value
     * @return string
     */
    public static function safeForHTMLOutput($value)
    {
        return htmlentities($value, ENT_QUOTES, 'utf-8');
    }


    /**
     * strips all html tags including hidden ones, does not remove or convert html entities
     * @param mixed $text The txt to strip
     * @return string
     */
    public static function stripHTMLTags($text)
    {
        $text = preg_replace(
            array( // Remove invisible content
                '@<head[^>]*?>.*?</head>@siu',
                '@<style[^>]*?>.*?</style>@siu',
                '@<script[^>]*?.*?</script>@siu',
                '@<object[^>]*?.*?</object>@siu',
                '@<embed[^>]*?.*?</embed>@siu',
                '@<applet[^>]*?.*?</applet>@siu',
                '@<noframes[^>]*?.*?</noframes>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
                '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks
                '@</?((address)|(blockquote)|(center)|(del))@iu',
                '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                '@</?((table)|(th)|(td)|(caption))@iu',
                '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                '@</?((frameset)|(frame)|(iframe))@iu',
            ),
            array(
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
            ),
            $text
        );
        return strip_tags($text);
    }

    /**
     * escape a string for input into an XML document
     * @param $string
     * @return mixed
     */
    public static function xmlEntities($string)
    {
        return str_replace(
            array(
                "&",
                "<",
                ">",
                "\"",
                "'"
            ),
            array(
                "&amp;",
                "&lt;",
                "&gt;",
                "&quot;",
                "&apos;"
            ),
            $string
        );
    }
} 