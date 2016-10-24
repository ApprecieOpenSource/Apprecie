<?php

class Message extends \Apprecie\Library\Model\CachedApprecieModel
{
    use \Apprecie\Library\DBConnection;

    protected $messageId, $targetUser, $sourceUser, $sourcePortal, $referenceItem, $sourceDescription, $title,
        $body, $sent, $read, $responseToMessage, $deleted, $sourceOrganisation;


    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

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
     * @param mixed $read
     */
    public function setRead($read)
    {
        $this->read = $read;
    }

    /**
     * @return mixed
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @param mixed $referenceItem
     */
    public function setReferenceItem($referenceItem)
    {
        $this->referenceItem = $referenceItem;
    }

    /**
     * @return mixed
     */
    public function getReferenceItem()
    {
        return $this->referenceItem;
    }

    /**
     * @param mixed $responseToMessage
     */
    public function setResponseToMessage($responseToMessage)
    {
        $this->responseToMessage = $responseToMessage;
    }

    /**
     * @return mixed
     */
    public function getResponseToMessage()
    {
        return $this->responseToMessage;
    }

    /**
     * @param mixed $sent
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    /**
     * @return mixed
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param mixed $sourceDescription
     */
    public function setSourceDescription($sourceDescription)
    {
        $this->sourceDescription = $sourceDescription;
    }

    /**
     * @return mixed
     */
    public function getSourceDescription()
    {
        return $this->sourceDescription;
    }

    /**
     * @param mixed $sourceOrganisation
     */
    public function setSourceOrganisation($sourceOrganisation)
    {
        $this->sourceOrganisation = $sourceOrganisation;
    }

    /**
     * @return mixed
     */
    public function getSourceOrganisation()
    {
        return $this->sourceOrganisation;
    }

    /**
     * @param mixed $sourcePortal
     */
    public function setSourcePortal($sourcePortal)
    {
        $this->sourcePortal = $sourcePortal;
    }

    /**
     * @return mixed
     */
    public function getSourcePortal()
    {
        return $this->sourcePortal;
    }

    /**
     * @param mixed $sourceUser
     */
    public function setSourceUser($sourceUser)
    {
        $this->sourceUser = $sourceUser;
    }

    /**
     * @return mixed
     */
    public function getSourceUser()
    {
        return $this->sourceUser;
    }

    /**
     * @param mixed $targetUser
     */
    public function setTargetUser($targetUser)
    {
        $this->targetUser = $targetUser;
    }

    /**
     * @return mixed
     */
    public function getTargetUser()
    {
        return $this->targetUser;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function getSource()
    {
        return 'messages';
    }

    public function initialize()
    {
        $this->hasManyToMany(
            'messageId',
            'MessageInThread',
            'messageId',
            'threadId',
            'MessageThread',
            'threadId',
            ['alias' => 'threads', 'reusable' => true]
        );
        $this->hasOne('sourceUser', 'User', 'userId', ['alias' => 'sender', 'reusable' => true]);
        $this->hasOne('targetUser', 'User', 'userId', ['alias' => 'recipient', 'reusable' => true]);
    }

    public function onConstruct()
    {
        $this->setDefaultFields('sent');
    }

    public function getMessageThreads($options = null)
    {
        return $this->getRelated('threads', $options);
    }

    public function getSendingUser($options = null)
    {
        return $this->getRelated('sender', $options);
    }

    public function getRecipientUser($options = null)
    {
        return $this->getRelated('recipient', $options);
    }
}