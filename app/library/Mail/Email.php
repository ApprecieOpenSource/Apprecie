<?php

namespace Apprecie\Library\Mail;

use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Security\Filters;
use Phalcon\DI;
use SendGrid;

class Email extends PrivateMessageQueue
{
    /**
     * @param ApprecieUser|string|int $userFrom The user, an email address, or the userId.
     * @param $usersTo
     * @param $subject
     * @param $html
     * @param null $text
     * @param null $usersCC
     * @param null $attachmentFile
     * @param null $attachmentName
     * @return \stdClass
     * @throws SendGrid\Exception
     */
    public function sendEmail($userFrom, $usersTo, $subject, $html, $text = null, $usersCC = null, $attachmentFile = null, $attachmentName = null)
    {
        $config = DI::getDefault()->getConfig();
        $sendgrid = new SendGrid($config->mail->user, $config->mail->pass);

        $email = new SendGrid\Email();
        if (!is_array($usersTo)) {
            $usersTo = array($usersTo);
        }

        foreach ($usersTo as $to) {
            if (!is_string($to)) {
                $to = \UserProfile::resolve($to);

                if ($to->email != '') {
                    $to = $to->email;
                }
            }

            if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $email->addTo($to);
            } else {
                $this->appendMessageEx('Invalid email ' . $to);
            }
        }

        if (!is_string($userFrom)) {
            $from = \UserProfile::resolve($userFrom);
            $from = $from->getEmail();
        } else {
            $from = $userFrom;
        }

        if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('The sender does not have a valid email address on their profile');
        }

        if ($text == null) {
            $text = Filters::htmlToTextUTF8($html);
        }

        if (!is_array($usersCC) && $usersCC != null) {
            $usersCC = array($usersCC);
        }

        if (count($usersCC) > 0) {
            foreach ($usersCC as $cc) {
                if (!is_string($cc)) {
                    $cc = \UserProfile::resolve($cc);

                    if ($cc->getEmail() != '') {
                        $cc = $cc->getEmail();
                    }
                }

                if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                    $email->addCc($cc);
                } else {
                    $this->appendMessageEx('Invalid email ' . $cc);
                }
            }
        }

        if ($attachmentFile) {
            $email->setAttachment($attachmentFile, $attachmentName);
        }

        $email->setFrom($from)
            ->setSubject($subject)
            ->setText($text)
            ->setHtml($html);

        return $sendgrid->send($email);
    }
} 