<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/11/14
 * Time: 12:31
 */
class UserNote extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $noteId, $portalId, $noteCreatorUserId, $noteAboutUserId, $body;

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
     * @param mixed $noteAboutUserId
     */
    public function setNoteAboutUserId($noteAboutUserId)
    {
        $this->noteAboutUserId = $noteAboutUserId;
    }

    /**
     * @return mixed
     */
    public function getNoteAboutUserId()
    {
        return $this->noteAboutUserId;
    }

    /**
     * @param mixed $noteCreatorUserId
     */
    public function setNoteCreatorUserId($noteCreatorUserId)
    {
        $this->noteCreatorUserId = $noteCreatorUserId;
    }

    /**
     * @return mixed
     */
    public function getNoteCreatorUserId()
    {
        return $this->noteCreatorUserId;
    }

    /**
     * @param mixed $noteId
     */
    public function setNoteId($noteId)
    {
        $this->noteId = $noteId;
    }

    /**
     * @return mixed
     */
    public function getNoteId()
    {
        return $this->noteId;
    }

    /**
     * @param mixed $portalId
     */
    public function setPortalId($portalId)
    {
        $this->portalId = $portalId;
    }

    /**
     * @return mixed
     */
    public function getPortalId()
    {
        return $this->portalId;
    }


    public function getSource()
    {
        return 'usernotes';
    }

    public function initialize()
    {
        $this->hasOne('noteCreatorUserId', 'User', 'userId', array('alias' => 'creator'));
        $this->belongsTo('noteAboutUserIdId', 'User', 'userId', array('alias' => 'about'));
    }

    public function onConstruct()
    {
        $this->setEncryptedFields('body');
        parent::onConstruct();
    }

    public function getUserCreator($options = null)
    {
        return $this->getRelated('creator', $options);
    }

    public function getUserAbout($options = null)
    {
        return $this->getRelated('about', $options);
    }

    public function getEncryptionKey()
    {
        if (!isset($this->noteCreatorUserId)) {
            throw new LogicException('cannot encrypt until an owner user is indicated');
        }

        return $this->getUserCreator()->getUserLevelEncryptionKey();
    }

    public function afterFetch()
    {
        parent::afterFetch();
    }
} 