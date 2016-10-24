<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/10/14
 * Time: 14:15
 */

namespace Apprecie\Library\Security;


use Phalcon\DI\Injectable;
use Phalcon\Security;

/**
 * Provides simple csrf prevention.
 * A session level token is generated and stored in the active session.
 * This same token can then be output in forms and tested when a post back occurs.
 *
 * note that this is not as strong as a nonce, but is less restrictive (can use multiple tabs in browser for example)
 *
 * <code>
 * HTML volt
 * <form ... >
 * {{csrf()}}
 * ...
 * </form>
 *
 * HTML php
 * <form ... >
 * <?= CSRFProtection::csrf(); ?>
 * ...
 * </form>
 *
 * //server side
 *
 * if(! (new CSRFProtection())->checkSessionToken())
 * {
 *      //forged request
 * }
 *
 * </code>
 *
 * Class CSRFProtection
 * @package Apprecie\Library\Security
 */
class CSRFProtection extends Injectable
{
    /**
     * Returns a session token (unqiue id)  and will generate the token if required.
     * Note that the token will last for the users session, so subsequent calls will return the same token.
     * @return string The existing session token
     */
    public function getSessionToken()
    {
        if (!$this->session->has('CSRF_SESSION_TOKEN')) {
            $this->createSessionToken();
        }

        return $this->session->get('CSRF_SESSION_TOKEN');
    }

    /**
     * Returns a html hidden field containing the csrf token under the name CSRF_SESSION_TOKEN
     * Note there is also a volt function {{csrf()}} for use in Templates.
     * You can check that the submitted token is valid by calling checkSessionToken()
     * @return string a html hidden filed,  complete tag, for input into a form
     */
    public static function csrf()
    {
        $token = (new CSRFProtection())->getSessionToken();
        return "<input type='hidden' name='CSRF_SESSION_TOKEN' value='{$token}' />";
    }

    /**
     * Generates a session token and stores it.
     * note that this method will replace (regenerate) and existing token
     * @return $this
     */
    public function createSessionToken()
    {
        $this->session->set('CSRF_SESSION_TOKEN', uniqid(rand(), true));
        return $this;
    }

    /**
     * Checks that the provided request value for CSRF_SESSION_TOKEN matches the stored session token
     * value.
     *
     * @return bool validity of the submitted token
     */
    public function checkSessionToken()
    {
        if ($this->request->get('CSRF_SESSION_TOKEN') == $this->session->get('CSRF_SESSION_TOKEN') ||$this->request->getJsonRawBody()->CSRF_SESSION_TOKEN == $this->session->get('CSRF_SESSION_TOKEN')) {
            return true;
        }
        return false;
    }
} 