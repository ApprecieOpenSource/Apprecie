<?php
/**
 * series of global scope macros for quick debug output, and output encoding / filtering
 */


/**
 * Returns a html encoded $value, safe for html output
 *
 * @param mixed $value
 * @return string
 */
function _s($value)
{
    return \Apprecie\Library\Security\Filters::safeForHTMLOutput($value);
}

/**
 * Outputs a variable safely encoded for insertion into javascript.
 * @param $value
 * @param bool $htmlEncode
 * @return string
 */
function _j($value, $htmlEncode = false)
{
    return \Apprecie\Library\Security\Filters::safeForJavascript($value, $htmlEncode);
}

/**
 * xml encodes the result of UTILS::SafeForHTMLOutput.
 *
 * i.e using for encoding html within xml.
 *
 * @param $value The string to encode
 * @return String  the encoded value
 */
function _xh($value)
{
    return \Apprecie\Library\Security\Filters::xMLEntities(
        \Apprecie\Library\Security\Filters::safeForHTMLOutput($value)
    );
}

/**
 * This is a macro for the following function call
 *
 * die('<pre>'.print_r($value,true).'</pre>');
 *
 * @param mixed $value
 */
function _d($value)
{
    die('<pre>' . print_r($value, true) . '</pre>');
}

/**
 * Captures the output of a var_dump and returns as a string
 *
 * @param mixed $value
 * @return string The HTML of the var_dump
 */
function _var($value)
{
    ob_start();
    var_dump($value);
    return ob_get_clean();
}

/**
 * Echo-Print
 * This is a macro for the following function call
 *
 * echo('<pre>'.print_r($value,true).'</pre>');
 *
 * @param mixed $value
 */
function _ep($value)
{
    echo('<pre>' . print_r($value, true) . '</pre>');
}

/**
 * Message-String
 * Enumerates the passed object containing messages and returns as string
 * @param $messages mixed
 * @return string
 */
function _ms($messages)
{
    return (new \Apprecie\Library\Messaging\PrivateMessageQueue())->appendMessageEx($messages)->getMessagesString();
}

/**
 * A combination of _ep ( _ms ())  echo prints messages
 * @param $messages
 */
function _epm($messages)
{
    _ep(_ms($messages));
}

/**
 * Echoes out moopy and ends execution
 * @return void
 */
function _m()
{
    _d('moopy');
}

/**
 * @param $string
 * @param null $placeHolders
 * @return mixed
 */
function _g($string, $placeHolders = null)
{
    $trans = \Phalcon\DI::getDefault()->get('translation');
    return $trans->query($string, $placeHolders);
}

/**
 * Resolves any content macros inside the string
 * A content macro takes the form {c:nnn}  where nnn is the numerical id of the content.
 * Uses the
 *
 * @param $stringData
 * @param null $languageId
 * @param bool $clearFailedMacros
 * @return
 */
function _c($stringData, $languageId = null, $clearFailedMacros = false)
{
    return \Phalcon\DI::getDefault()->get('contentresolver')->resolve(
        $stringData,
        $languageId == null ? _l() : $languageId,
        $clearFailedMacros
    );
}

/**
 * @return mixed The current active UI languageId
 */
function _l()
{
    return \Apprecie\Library\Translation\LanguageService::getCurrentUILanguage();
}

/**
 * Resolve language code to language id
 * Currently forces everything to default language as we have no translations
 */
function _rl($isoCode)
{
    return \Phalcon\DI::getDefault()->get('config')->environment->defaultLanguageId;
}

/**
 * Set the current UI languageId
 * @param int $languageId The new UI language
 */
function _cl($languageId)
{
    \Apprecie\Library\Translation\LanguageService::setCurrentUILanguage($languageId);
}

/**
 * @return the id of the active portal
 */
function _pid()
{
    return \Phalcon\DI::getDefault()->get('portal')->getPortalId();
}

function _p($content, $style = '')
{
    return '<p style="' . $style . '">' . $content . '</p>';
}

function _a($content, $url, $style = '')
{
    return '<a style="' . $style . '" href="' . $url . '">' . $content . '</a>';
}

function _jm($status, $message, $allowBrowserCache = false)
{
    echo json_encode(['status' => $status, 'message' => $message]);
    if(! $allowBrowserCache) {
        \Apprecie\Library\Http\HTTPCache::privateContent();
    }
}

/**
 * get the local formatted time from a mysqldatetime string
 * @param $dateTimeString
 * @param bool $specialCasesAsNull
 * @return mixed|string
 */
function _ft($dateTimeString, $specialCasesAsNull = false)
{
    return \Apprecie\Library\Localisation\DateTimeHelper::getTimeFromMySQLDateTimeString(
        $dateTimeString,
        $specialCasesAsNull
    );
}

/**
 * get local formatted date from an mysqldatetime string
 * @param $dateTimeString
 * @param bool $specialCasesAsNull
 * @return mixed|string
 */
function _fd($dateTimeString, $specialCasesAsNull = false)
{
    if ($dateTimeString != null) {
        return \Apprecie\Library\Localisation\DateTimeHelper::getDateFromMySQLDateTimeString(
            $dateTimeString,
            $specialCasesAsNull
        );
    }

    return $dateTimeString;
}

function _fdt($dateTimeString, $specialCasesAsNull = false)
{
    return \Apprecie\Library\Localisation\DateTimeHelper::getDateTimeFromMySQLDateTimeString(
        $dateTimeString,
        $specialCasesAsNull
    );
}

function _hdt($dateTimeString, $specialCasesAsNull = false)
{
    return \Apprecie\Library\Localisation\DateTimeHelper::getHumanDateTimeFromMySQLDateTimeString(
        $dateTimeString,
        $specialCasesAsNull
    );
}

function _hd($dateTimeString, $specialCasesAsNull = false)
{
    return \Apprecie\Library\Localisation\DateTimeHelper::getHumanDateFromMySQLDateTimeString(
        $dateTimeString,
        $specialCasesAsNull
    );
}

function _myd($dateString, $format = 'd-m-Y')
{
    return \Apprecie\Library\Localisation\DateTimeHelper::getMySQLDateFromDateString($dateString, $format);
}

/**
 * Escape html output
 * @param $htmlContent
 * @return mixed
 */
function _eh($htmlContent)
{
    return \Phalcon\DI::getDefault()->get('escape')->escapeHtml($htmlContent);
}

/**
 * Escape HTML attribute output
 * @param $htmlAttributeContent
 * @return mixed
 */
function _eha($htmlAttributeContent)
{
    return \Phalcon\DI::getDefault()->get('escape')->escapeHtmlAttr($htmlAttributeContent);
}

/**
 * Escape javascript output
 * @param $jsContent
 * @return mixed
 */
function _ej($jsContent)
{
    return \Phalcon\DI::getDefault()->get('escape')->escapeJs($jsContent);
}

/**
 * Escape css output
 * @param $cssContent
 * @return mixed
 */
function _ec($cssContent)
{
    return \Phalcon\DI::getDefault()->get('escape')->escapeCss($cssContent);
}

function _rf($requireHTTPS = true)
{
    return new \Apprecie\Library\Security\RequestFilter($requireHTTPS);
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
function _u($portal = null,
            $page = null,
            $action = 'index',
            $params = null,
            $protocol = 'https')
{
    return \Apprecie\Library\Request\Url::getConfiguredPortalAddress($portal, $page, $action, $params, $protocol);
}
