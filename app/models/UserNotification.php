<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/12/14
 * Time: 19:42
 */
class UserNotification extends \Apprecie\Library\Model\CachedApprecieModel
{
    protected $userId, $noticeId;
    public $title, $date, $body, $url, $urlClicked, $dismissed;

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
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $dismissed
     */
    public function setDismissed($dismissed)
    {
        $this->dismissed = $dismissed;
    }

    /**
     * @return mixed
     */
    public function getDismissed()
    {
        return $this->dismissed;
    }

    /**
     * @return mixed
     */
    public function getNoticeId()
    {
        return $this->noticeId;
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

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $urlClicked
     */
    public function setUrlClicked($urlClicked)
    {
        $this->urlClicked = $urlClicked;
    }

    /**
     * @return mixed
     */
    public function getUrlClicked()
    {
        return $this->urlClicked;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }


    public function getSource()
    {
        return 'usernotifications';
    }

    public function initialize()
    {
        $this->belongsTo('userId', 'User', 'userId');
    }

    public function onConstruct()
    {
        $this->setEncryptedFields('body');
        $this->setDefaultFields(['date', 'body', 'url', 'urlClicked', 'dismissed']);
        parent::onConstruct();
    }

    /**
     * @return User
     */
    public function getUser($options = null)
    {
        return $this->getRelated('User', $options);
    }

    public function getEncryptionKey()
    {
        if (!isset($this->userId)) {
            throw new LogicException('cannot encrypt until an owner user is indicated');
        }

        return $this->getUser()->getUserLevelEncryptionKey();
    }

    public static function getActiveNotificationForUser($user)
    {
        $user = User::resolve($user);

        return UserNotification::query()
            ->where('dismissed is null')
            ->andWhere('userId = :user:')
            ->bind(['user' => $user->getUserId()])
            ->execute();
    }

    /**
     * @param \Apprecie\Library\Model\ApprecieModelBase|mixed $param
     * @param bool $throw
     * @param \Apprecie\Library\Model\ApprecieModelBase $instance
     * @return UserNotification
     */
    public static function resolve($param, $throw = true, \Apprecie\Library\Model\ApprecieModelBase $instance = null)
    {
        if ($param instanceof UserNotification) {
            return $param;
        }

        return parent::resolve($param, $throw, $instance);
    }
} 