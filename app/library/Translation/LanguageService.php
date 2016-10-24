<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/01/15
 * Time: 13:03
 */

namespace Apprecie\Library\Translation;

use Apprecie\Library\Messaging\PrivateMessageQueue;
use Phalcon\DI;

class LanguageService extends PrivateMessageQueue
{
    public static function getCurrentUILanguage()
    {
        $session = DI::getDefault()->get('session');

        if ($session->has('UI_LANGUAGE')) {
            return $session->get('UI_LANGUAGE');
        }

        return DI::getDefault()->get('config')->environment->defaultLanguageId;
    }

    public static function setCurrentUILanguage($languageId)
    {
        if (is_int($languageId)) {
            $session = DI::getDefault()->get('session');
            $session->set('UI_LANGUAGE', $languageId);
        }
    }

    public static function respondToLanguageChange()
    {
        if (isset($_REQUEST['siteLanguageId'])) {
            static::setCurrentUILanguage($_REQUEST['siteLanguageId']);
        }
    }
} 