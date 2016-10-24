<?php
/**
 * Created by PhpStorm.
 * User: hu86
 * Date: 21/09/2015
 * Time: 18:55
 */

namespace Apprecie\Library\Security;

use Apprecie\Library\Users\UserEx;
use Phalcon\DI;
use Apprecie\Library\Tracing\ActivityTraceTrait;
use Apprecie\Library\DBConnection;

class AccountLock
{
    use ActivityTraceTrait;
    use DBConnection;

    public static function updateAccountLock()
    {
        $config = DI::getDefault()->get('config');
        if (!$config->accountLock->enabled) {
            return;
        }

        $session = DI::getDefault()->get('session');
        $auth =  new  Authentication();

        if ($auth->isImpersonating()) { //maintain impersonator's account lock
            $user = $session->get('SESSION_OWNER');
        } else {
            $user = DI::getDefault()->get('auth')->getAuthenticatedUser();
        }

        if (!$user) {
            return;
        }

        $portalId = $user->getPortalId();

        UserEx::ForceActivePortalForUserQueries($portalId);
        $loginId = $user->getPortalUser()->getLoginId();
        UserEx::ForceActivePortalForUserQueries();

        $accountLock =  DBConnection::getDbAdapter()->query("SELECT * from accountlocks where loginId=" . $loginId . " and portalId=" . $portalId)->fetch();

        if ($accountLock) {
            if ($accountLock['sessionId'] === $session->getId()) { //account lock exists for current session
                DBConnection::getDbAdapter()->query("update accountlocks set lastActive=now() where loginId=" . $loginId . " and portalId=" . $portalId . " and sessionId='" . $session->getId() . "'");
            } else { //login attempt from elsewhere has succeeded due to inactivity
                $auth->logoutUser();
            }
        } else {
            static::addAccountLock(); //still logged in but inactive for more than 5 minutes
        }
    }

    public static function expireAccountLocks()
    {
        DBConnection::getDbAdapter()->query("delete from accountlocks where DATE_ADD(lastActive, INTERVAL 5 MINUTE) < CURRENT_TIMESTAMP");
    }

    public static function addAccountLock()
    {
        $config = DI::getDefault()->get('config');
        if (!$config->accountLock->enabled) {
            return;
        }

        $session = DI::getDefault()->get('session');
        $auth =  new  Authentication();

        if ($auth->isImpersonating()) { //maintain impersonator's account lock
            $user = $session->get('SESSION_OWNER');
        } else {
            $user = DI::getDefault()->get('auth')->getAuthenticatedUser();
        }

        if (!$user) {
            return;
        }

        $portalId = $user->getPortalId();

        UserEx::ForceActivePortalForUserQueries($portalId);
        $loginId = $user->getPortalUser()->getLoginId();
        UserEx::ForceActivePortalForUserQueries();

        DBConnection::getDbAdapter()->query("insert into accountlocks values (" . $loginId . "," . $portalId . ",'" . $session->getId() . "',CURRENT_TIMESTAMP)");
    }

    public static function checkAccountLock($user)
    {
        $config = DI::getDefault()->get('config');
        if (!$config->accountLock->enabled) {
            return false;
        }

        $portalId = $user->getPortalId();

        UserEx::ForceActivePortalForUserQueries($portalId);
        $loginId = $user->getPortalUser()->getLoginId();
        UserEx::ForceActivePortalForUserQueries();

        $accountLock = DBConnection::getDbAdapter()->query("select * from accountlocks where loginId=" . $loginId . " and portalId=" . $portalId);

        if ($accountLock->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    public static function removeAccountLock()
    {
        $config = DI::getDefault()->get('config');
        if (!$config->accountLock->enabled) {
            return;
        }

        $user = DI::getDefault()->get('auth')->getAuthenticatedUser(true); //only remove account lock of current user, not the impersonator
        $session = DI::getDefault()->get('session');

        if (!$user) { //not logged in
            return;
        }

        $portalId = $user->getPortalId();

        UserEx::ForceActivePortalForUserQueries($portalId);
        $loginId = $user->getPortalUser()->getLoginId();
        UserEx::ForceActivePortalForUserQueries();

        $accountLock =  DBConnection::getDbAdapter()->query("SELECT * from accountlocks where loginId=" . $loginId . " and portalId=" . $portalId . " and sessionId='" . $session->getId() . "'");

        if ($accountLock->fetch()) {
            DBConnection::getDbAdapter()->query("delete from accountlocks where loginId=" . $loginId . " and portalId=" . $portalId . " and sessionId='" . $session->getId() . "'");
        } else { //logged in elsewhere

        }
    }
}