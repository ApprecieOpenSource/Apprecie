<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 22/12/14
 * Time: 09:57
 */

namespace Apprecie\Library\Users;

use Apprecie\Library\Collections\CanRegister;
use Apprecie\Library\Collections\IsRegistry;
use Apprecie\Library\Collections\Registry;
use Apprecie\Library\Model\ApprecieModelBase;
use Apprecie\Library\Model\CachedApprecieModel;
use Apprecie\Library\Tracing\ActivityTraceTrait;
use Phalcon\DI;

abstract class ApprecieUserBase extends ApprecieModelBase implements ApprecieUser, CanRegister
{
    private $_lastSource = null;

    use PortalSourcePrefixTrait;
    use UserRoleTrait;
    use UserPolymorphismTrait;
    use ActivityTraceTrait;

    public function register(IsRegistry $register, $key, $name)
    {
        switch($name) {
            case 'ent_user' : {
                $register->setInstance($key, $this->getUserInstance());
                break;
            }
            case 'ent_user_profile' : {
                $register->setInstance($key, $this->getUserProfileInstance());
                break;
            }
            case 'ent_portal_user' : {
                $register->setInstance($key, $this->getPortalUserInstance());
                break;
            }
            case 'ent_user_login' : {
                $register->setInstance($key, $this->getUserLoginInstance());
                break;
            }
            default : {
                parent::register($register, $key, $name);
            }
        }
    }

    public function getUser()
    {
        $entity = DI::getDefault()->get('userRegistry')->getInstance($this);
        $this->checkJoin($this, $entity);
        return $entity;
    }

    /**
     * @return \UserProfile
     */
    public function getUserProfile()
    {
        $entity = DI::getDefault()->get('userProfileRegistry')->getInstance($this);
        $this->checkJoin($this, $entity);
        return $entity;
    }

    public function getPortalUser()
    {
        $entity = DI::getDefault()->get('userPortalRegistry')->getInstance($this);
        $this->checkJoin($this, $entity);
        return $entity;
    }

    public function getUserLogin()
    {
        $entity = DI::getDefault()->get('userLoginRegistry')->getInstance($this);
        $this->checkJoin($this, $entity);
        return $entity;
    }

    public function checkJoin($thisOne, $thatOne)
    {
        if($thisOne == null || $thatOne == null) {
            return;
        }

        $thisGuid = $this->getPortalGUIDFromEntity($thisOne);
        $thatGuid = $this->getPortalGUIDFromEntity($thatOne);

        if($thisGuid != $thatGuid) {
            throw new \OutOfBoundsException('PRAGMA HARD STOP.  You attempted to join user entities from different isolation sets. Did you forget UserEx:::ForceActivePortalForUserQueries() ' . $thisGuid . ' != ' . $thatGuid);
        }
    }

    public function getPortalGUIDFromEntity($entity)
    {
        $source = '';

        if($entity instanceOf \User) {
            $source = $entity->getPortal()->getPortalGUID();
        } elseif($entity instanceOf \PortalUser || $entity instanceOf \UserProfile || $entity instanceOf \UserLogin) {
            $source = $entity->getOriginalSource();
            $source = substr($source, 1);
            $source = substr($source,0, strrpos($source, '_'));
        }

        return $source;
    }

    /**
     * Returns the PortalUser part of the user.  This is the private user object that sits inside a specific
     * portals user table.  All users have a PortalUser.
     * @return \PortalUser
     */
    public function getPortalUserInstance()
    {
        $entity = null;

        if ($this instanceof \PortalUser) {
            $this->setPortalId();
            $entity =  $this;
        } else {
            $entity = $this->getRelated('PortalUser');

            if ($entity != null) {
                $entity->setPortalId();
            }
        }

        $this->checkJoin($this, $entity);

        return $entity;
    }

    /**
     * Returns the user profile for this user.  note that some users will not have a profile!!
     * @return null|\UserProfile null if the user has no profile, else the UserProfile object
     */
    public function getUserProfileInstance()
    {
        $entity = null;

        if ($this instanceof \UserProfile) {
            $entity = $this;
        } elseif ($this instanceof \PortalUser) {
            $entity = $this->getRelated('UserProfile');
        } else {
            $portalUser = $this->getPortalUser();
            if ($portalUser == null) {
                return null;
            }
            $entity = $portalUser->getRelated('UserProfile');
        }

        $this->checkJoin($this, $entity);

        return $entity;
    }

    /**
     * Returns the User entity of this user.  Note that this part of a user is in the global space.
     * All users have a User entity, which contains their guid, and links to roles, and tracking data.
     * This user entity contains no personally identifying data.     *
     * In almost all cases you will be interested in the PortalUser  i.e.  ->getPortalUser()
     * @throws \LogicException
     * @return \User
     */
    public function getUserInstance()
    {
        $entity = null;

        if ($this instanceof \User) {
            $entity =  $this;
        } elseif ($this instanceof \PortalUser) {
            $this->setPortalId();
            $entity = $this->getRelated('User');
        } else {
            $entity = $this->getPortalUser()->getRelated('User');
        }

        $this->checkJoin($this, $entity);

        return $entity;
    }

    /**
     * Returns the UserLogin part of the user.  Note that not all users have a login.
     * Will return null in the case of no UserLogin existing for this user.
     *
     * Note that in most cases unless you are updating login details you want the PortalUser
     * i.e. ->getPortalUser()
     *
     * @return \UserLogin
     */
    public function getUserLoginInstance()
    {
        $entity = null;

        if ($this instanceof \UserLogin) {
            $entity = $this;
        }elseif ($this instanceof \PortalUser) {
            $entity = $this->getRelated('UserLogin');
        } else {
            $portalUser = $this->getPortalUser();
            if ($portalUser == null) {
                return null;
            }
            $entity = $portalUser->getRelated('UserLogin');
        }

        $this->checkJoin($this, $entity);

        return $entity;
    }

    /**
     * useful for resolving an id or object to an actual UserObject.
     *
     * @param $apprecieUser ApprecieUser|int $user userId, an ApprecieUser, or a User
     * @param bool $throw
     * @param ApprecieModelBase $instance
     * @return null|\User return if podssible the actual User object referenced, else null
     */
    public static function resolve(
        $apprecieUser,
        $throw = true,
        ApprecieModelBase $instance = null
    ) {
        if ($apprecieUser instanceof ApprecieUser) {
            $getMethod = 'get' . ucfirst(get_called_class());
            return $apprecieUser->$getMethod();
        } elseif (is_string($apprecieUser)) {
            $user = null;

            if (filter_var($apprecieUser, FILTER_VALIDATE_EMAIL)) { //try profile email
                $user = \UserProfile::findFirstBy('email', $apprecieUser, $instance);
            }

            if ($user == null) { //try login user name
                $user = \UserLogin::findFirstBy('username', $apprecieUser, $instance);
            }

            if ($user != null) {
                $apprecieUser = $user;
            }
        }

        return Parent::resolve($apprecieUser, $throw, $instance);
    }

    public function onConstruct()
    {
        $this->_lastSource = $this->getSource();
        parent::onConstruct();
    }

    public function afterFetch()
    {
        $this->_lastSource = $this->getSource();
        parent::afterFetch();
    }

    public function getOriginalSource()
    {
        return $this->_lastSource;
    }
} 