<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/02/15
 * Time: 13:31
 */
class MessageInThread extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $threadId, $messageId;

    /**
     * @param mixed $messageId
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
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

    public function getSource()
    {
        return 'messagesinthread';
    }

    public function initialiaze()
    {
        $this->hasMany('messageId', 'Message', 'messageId');
        $this->belongsTo('threadId', 'MessageThread', 'threadId');
    }
} 