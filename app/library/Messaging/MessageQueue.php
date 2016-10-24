<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/10/14
 * Time: 11:36
 */

namespace Apprecie\Library\Messaging;


interface MessageQueue
{
    public function appendMessage($message);

    public function getMessages();

    public function getMessageCount();

    public function hasMessages();

    public function getMessagesString($seperator = ' , ');
} 