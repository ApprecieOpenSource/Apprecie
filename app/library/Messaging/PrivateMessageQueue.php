<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/10/14
 * Time: 11:35
 */

namespace Apprecie\Library\Messaging;

use Phalcon\DI\Injectable;

class PrivateMessageQueue extends Injectable implements MessageQueue
{
    use MessagingTrait;
} 