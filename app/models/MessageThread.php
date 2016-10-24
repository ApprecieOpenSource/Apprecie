<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/02/15
 * Time: 13:27
 */
class MessageThread extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $threadId, $startDate, $startedByUser, $firstRecipientUser, $archived, $byArrangementId, $seen, $type;

    public function getSeen()
    {
        return $this->seen;
    }

    public function setSeen($value)
    {
        $this->seen = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $archived
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    }

    /**
     * @param mixed $byArrangementId
     */
    public function setByArrangementId($byArrangementId)
    {
        $this->byArrangementId = $byArrangementId;
    }

    /**
     * @return mixed
     */
    public function getByArrangementId()
    {
        return $this->byArrangementId;
    }

    /**
     * @return mixed
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * @param mixed $firstRecipientUser
     */
    public function setFirstRecipientUser($firstRecipientUser)
    {
        $this->firstRecipientUser = $firstRecipientUser;
    }

    /**
     * @return mixed
     */
    public function getFirstRecipientUser()
    {
        return $this->firstRecipientUser;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startedByUser
     */
    public function setStartedByUser($startedByUser)
    {
        $this->startedByUser = $startedByUser;
    }

    /**
     * @return mixed
     */
    public function getStartedByUser()
    {
        return $this->startedByUser;
    }

    /**
     * @param mixed $threadId
     */
    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;
    }

    /**
     * @return mixed
     */
    public function getThreadId()
    {
        return $this->threadId;
    }

    public function onConstruct()
    {
        $this->setDefaultFields(['startDate']);
    }

    public function initialize()
    {
        $this->hasMany('threadId', 'MessageInThread', 'threadId', ['reusable' => true]);
        $this->hasManyToMany(
            'threadId',
            'MessageInThread',
            'threadId',
            'messageId',
            'Message',
            'messageId',
            ['alias' => 'messages', 'reusable' => true]
        );
        $this->hasOne('startedByUser', 'User', 'userId', ['reusable' => true]);
        $this->hasOne('firstRecipientUser', 'User', 'userId', ['alias' => 'firstRecipient', 'reusable' => true]);
    }

    public function getStartingUser($options = null)
    {
        return $this->getRelated('User', $options);
    }

    public function getFirstReceivingUser($options = null)
    {
        return $this->getRelated('firstRecipient', $options);
    }

    public function getThreadMessages($options = null)
    {//@todo gh  find usage and make it uses options
        return $this->getRelated('messages', ['order' => 'Message.messageId DESC']);
    }

    public function getSource()
    {
        return 'messagethreads';
    }

    public function addMessage($message)
    {
        $message = Message::resolve($message);

        $link = new MessageInThread();
        $link->setMessageId($message->getMessageId());
        $link->setThreadId($this->getThreadId());

        if (!$link->create()) {
            $this->appendMessageEx($link);
            return false;
        } else {
            /*$notice = new \Apprecie\Library\Messaging\Notification();
            $notice->addNotification
                (
                    $message->getRecipientUser()->getUserId(),
                    _g('You have a new message on your portal'),
                    _g('Please check your alert centre.'),
                    \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                        $message->getRecipientUser()->getPortalId(),
                        'alertcentre',
                        'view',
                        [$this->getThreadId()]
                    ),
                    null,
                    true
                );*/

            $this->setSeen(false);
            $this->update();
        }

        return true;
    }

    public static function findAllThreadsForUser($user)
    {
        $user = User::resolve($user);
        return MessageThread::query()->where(
            'startedByUser= :1: or firstRecipientUser = :2:',
            [1 => $user->getUserId(), 2 => $user->getUserId()]
        )->orderBy('threadId DESC')->execute();
    }

    public static function getCountOfNewContent($user = null)
    {
        if ($user == null) {
            $user = \Phalcon\DI::getDefault()->get('auth')->getAuthenticatedUser();
        } else {
            $user = User::resolve($user);
        }

        $q = \Phalcon\DI::getDefault()->get('db');
        $userId = $user->getUserId();

        $result = $q->query('SELECT  COALESCE(count(m.messageId)) as total FROM messagethreads t inner join messagesinthread i on t.threadId = i.threadId inner join messages m on m.messageId = i.messageId where (t.startedByUser = ? or t.firstRecipientUser = ?) and t.seen = 0 and m.targetUser = ?', [$userId, $userId, $userId])->fetchArray();

        return $result['total'];
    }
} 