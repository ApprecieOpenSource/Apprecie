<?php

/**
 * The UserLogin entity, sits in the private portal tables and contains the login details
 * of a given user.  not all users will have a UserLogin
 *
 * To obtain the UserLogin execute the ->getUserLogin() method of any user entity.
 *
 * Class UserLogin
 */
class UserLogin extends \Apprecie\Library\Users\ApprecieUserBase
{
    public $username, $password, $suspended;
    protected $loginId;

    /**
     * @param mixed $loginId
     */
    public function setLoginId($loginId)
    {
        $this->loginId = $loginId;
    }

    /**
     * @return mixed
     */
    public function getLoginId()
    {
        return $this->loginId;
    }

    /**
     * @param mixed $password sets password to the password field without hashing or any alteration.
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param string $password hashs $password before storage
     */
    public function setAndHashPassword($password)
    {
        $this->password = (new \Phalcon\Security())->hash($password);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $suspended
     */
    public function setSuspended($suspended)
    {
        $this->suspended = $suspended;
    }

    /**
     * @return mixed
     */
    public function getSuspended()
    {
        return $this->suspended;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function getSource()
    {
        return '_' . static::getSourcePrefix() . '_userlogins';
    }

    public function initialize()
    {
        $this->belongsTo('loginId', 'PortalUser', 'loginId');
    }
}