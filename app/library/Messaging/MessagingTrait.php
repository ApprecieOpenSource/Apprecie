<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/10/14
 * Time: 11:29
 */

namespace Apprecie\Library\Messaging;

use Phalcon\Mvc\Model;
use Phalcon\Validation\Message;

trait MessagingTrait
{
    protected $_validationMessages = array();

    public function appendMessage($message)
    {
        $this->appendMessageEx($message);
    }

    public function appendMessageEx($message)
    {
        if ($message instanceof Message || $message instanceof Model\Message) {
            $this->appendMessageEx($message->getMessage());
        } elseif ($message instanceof MessageQueue) {
            $this->appendMessageEx($message->getMessages());
        } elseif ($message instanceof \Exception) {
            $this->appendMessageEx($message->getMessage());
        } elseif (is_array($message) || $message instanceof \ArrayAccess) {
            foreach ($message as $snip) {
                $this->appendMessageEx($snip);
            }
        } elseif ($message instanceof Model) {
            $this->appendMessageEx($message->getMessages());
        } elseif ($message != null || $message != '') {
            if (!in_array($message, $this->_validationMessages)) {
                $this->_validationMessages[] = $message;
            }
        }

        return $this;
    }

    public function getMessages()
    {
        return $this->_validationMessages;
    }

    public function getMessageCount()
    {
        return count($this->_validationMessages);
    }

    public function hasMessages()
    {
        return $this->getMessageCount() > 0;
    }

    public function getMessagesString($seperator = ' , ')
    {
        if ($this->getMessageCount() == 0) {
            return '';
        }

        return join($seperator, $this->getMessages());
    }
} 