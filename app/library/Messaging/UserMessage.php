<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 29/04/15
 * Time: 13:38
 */

namespace Apprecie\Library\Messaging;

use Apprecie\Library\Items\ItemState;
use Apprecie\Library\Mail\EmailUtility;
use Apprecie\Library\Users\UserEx;

class UserMessage extends PrivateMessageQueue
{
    /**
     * on success will return the thread,  on failure bollean false.
     * errors and warnings will be set to the internal messages and should be checked if false is returned.
     *
     * @param string $userMessageMode one of the UserMessageMode constants
     * @param \User|int $userFrom a user object, or userid
     * @param \User|int $userTo
     * @param string $body The actual body of the message / email and or alert
     * @param string $title The title of the message
     * @param null|int|\MessageThread $thread the thread object or id of an existing thread to add to
     * @param null|\Item|int $item If a related item, then this is the id or Item object
     * @param null|\Transaction $transaction If passed this transaction will be used for each data operation
     * @param string $type
     * @return bool|\MessageThread|null|object
     */
    public function sendMessage(
        $userMessageMode,
        $userFrom,
        $userTo,
        $body,
        $title,
        $thread = null,
        $item = null,
        $transaction = null,
        $type = MessageThreadType::GENERIC
    ) {
        $userFrom = \User::resolve($userFrom);
        $userTo = \User::resolve($userTo);

        $message = new \Message();

        if ($transaction != null) {
            $message->setTransaction($transaction);
        }

        if ($thread != null) {
            $thread = \MessageThread::resolve($thread);
        } else {
            $thread = new \MessageThread();
            $thread->setType($type);
        }

        if ($item != null) {
            $item = \Item::resolve($item);
            $message->setReferenceItem($item->getItemId());
        }

        $message->setTargetUser($userTo->getUserId());
        $message->setSourceUser($userFrom->getUserId());

        $message->setBody($body);
        $message->setTitle($title);
        $message->setSourcePortal($userFrom->getPortalId());

        $message->setSourceDescription
            (
                $userFrom->getUserProfile()->getFullName()
            );

        $message->setSent(date('Y-m-d H:i:s'));
        $message->setSourceOrganisation($userFrom->getOrganisationId());

        if (!$message->save()) {
            $this->appendMessageEx($message);
        } else {
            if ($transaction != null) {
                $thread->setTransaction($transaction);
            }

            $thread->setStartedByUser($message->getSourceUser());
            $thread->setFirstRecipientUser($message->getTargetUser());

            if ($item != null && $item->getState() == ItemState::ARRANGING && $thread->getByArrangementId() == null) {
                $thread->setByArrangementId($item->getItemId());
            }

            if (!$thread->save()) {
                $this->appendMessageEx($thread);
            } else {
                $thread->addMessage($message);
            }
        }

        if ($this->hasMessages()) {
            return false;
        }

        if ($userMessageMode != UserMessageMode::MESSAGE_ONLY) {
            if ($userMessageMode == UserMessageMode::MESSAGE_AND_ALERT
                || $userMessageMode == UserMessageMode::MESSAGE_AND_EMAIL_AND_ALERT
            ) {
                $notice = new Notification();
                if (!$notice->addNotification(
                    $userTo,
                    $title,
                    $body,
                    '/alertcentre/view/' . $thread->getThreadId(),
                    $transaction
                )
                ) {
                    $this->appendMessageEx($notice);
                    return false;
                }
            }

            if ($userMessageMode == UserMessageMode::MESSAGE_AND_EMAIL_AND_ALERT
                || $userMessageMode == UserMessageMode::MESSAGE_AND_EMAIL
            ) {

                UserEx::ForceActivePortalForUserQueries($userTo->getPortalId());
                $profile = $userTo->getUserProfile();
                $toEmail = $profile->getEmail();
                UserEx::ForceActivePortalForUserQueries();
                if ($toEmail == null) {
                    $this->appendMessageEx(_g('The user has no available email address to send to'));
                } else {
                    $email = new EmailUtility();
                    if (!$email->sendGenericEmailMessage($toEmail, $body, $title, $userFrom->getOrganisation())) {
                        $this->appendMessageEx($email);
                        return false;
                    }
                }
            }
        }

        return $thread;
    }
} 