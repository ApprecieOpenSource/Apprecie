<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 26/03/15
 * Time: 09:12
 */

namespace Apprecie\Library\Messaging;

use Apprecie\Library\Http\Client\Exception;
use Apprecie\Library\Mail\EmailUtility;
use Apprecie\Library\Users\UserEx;
use Apprecie\Library\Users\UserRole;

class Notification extends PrivateMessageQueue
{
    public function addNotification($user, $title, $body = null, $url = null, $transaction = null, $sendEmail = false)
    {
        $user = \User::resolve($user);

        if($user->getIsDeleted()) {
            $this->appendMessageEx('The recipient account have been deleted');
            return false;
        }

        if ($user->hasRole(UserRole::CONTACT)) {
            return true; // skip contacts as they will never have portal access
        }

        $notice = new \UserNotification();

        if ($transaction != null) {
            $notice->setTransaction($transaction);
        }

        $notice->setUserId($user->getUserId());
        $notice->setTitle($title);
        $notice->setBody($body);
        $notice->setUrl($url);

        $user->clearStaticCache();

        $lastPortal = (new UserEx())->getActiveQueryPortal();
        UserEx::ForceActivePortalForUserQueries($user->getPortalId());
        $profile = $user->getUserProfile();
        $toEmail = $profile->getEmail();
        UserEx::ForceActivePortalForUserQueries($lastPortal);

        if ($sendEmail && $body != null) {
            if ($toEmail == null) {
                $this->appendMessageEx(_g('The user has no available email address to send to'));
            } else {
                $email = new EmailUtility();
                $body .= _p(_g('On behalf of {org}', ['org' => $user->getOrganisation()->getOrganisationName()]));

                try{
                    if (!$email->sendGenericEmailMessage($toEmail, $body, $title, $user->getOrganisation(), $url)) {
                        $this->appendMessageEx($email);
                        return false;
                    }
                } catch(\Exception $ex){
                    $this->appendMessage($ex->getMessage());
                    return false;
                }
            }
        }

        if (!$notice->create()) {
            $this->appendMessageEx($notice);
            $log = new \ActivityLog();
            $log->logActivity('Failed to save a notification', _ms($notice));
            return false;
        }

        return true;
    }
} 