<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 09/12/14
 * Time: 09:45
 */

namespace Apprecie\Library\Tracing;

use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Security\Authentication;
use Apprecie\Library\Security\IPTools;
use Apprecie\Library\Users\UserEx;

class ActivityTrace extends PrivateMessageQueue implements CanTrace
{
    public function logActivity($activity, $activityDetails, $logTable = null)
    {
        $lastActivePortal = null;
        $user = (new Authentication())->getAuthenticatedUser(true);
        $ident = 'unknown';

        if ($user != false) {
            $lastActivePortal = (new UserEx())->getActiveQueryPortal();

            UserEx::ForceActivePortalForUserQueries($user->getPortalId());

            $userId = $user->getUserId();
            $ident = $user->getUserLogin()->getUsername();
            $userPortal = $user->getuser()->getPortal();

            if ($userPortal != null) {
                $ident .= '[' . $userPortal->getPortalName() . ']';
            }
        } else {
            $userId = null;
        }

        if (is_array($activityDetails) || is_object($activityDetails)) {
            $activityDetails = print_r($activityDetails, true);
        }

        $ipAddress = IPTools::getClientIPAddress();
        $role = (new Authentication())->getSessionActiveRole();
        $sessionId = $this->getDI()->get('session')->getId();
        $portalId = $this->getDI()->get('portal')->getPortalId();

        if($logTable != null) {
            \ActivityLog::setLogTable($logTable);
        }

        $activityLog = new \ActivityLog();

        $activityLog->setPortalId($portalId);
        $activityLog->setActivity($activity);
        $activityLog->setActivityDetails($activityDetails);
        $activityLog->setUserId($userId);
        $activityLog->setIdent($ident);
        $activityLog->setSessionId($sessionId);
        $activityLog->setRole($role);
        $activityLog->setIpAddress($ipAddress);

        if ($lastActivePortal != null) {
            UserEx::ForceActivePortalForUserQueries($lastActivePortal);
        }

        if (!$activityLog->create()) {
            $this->appendMessageEx($activityLog);
            \ActivityLog::setLogTable(null);
            return false;
        }

        \ActivityLog::setLogTable(null);
        return $activityLog->getActivityId();
    }

    public function logSecurityEvent($event, $details)
    {
        return $this->logActivity($event, $details, 'securitylog');
    }
} 