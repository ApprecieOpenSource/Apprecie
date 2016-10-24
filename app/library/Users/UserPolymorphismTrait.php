<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 23/10/14
 * Time: 17:43
 */

namespace Apprecie\Library\Users;

use Phalcon\DI;

Trait UserPolymorphismTrait
{
    protected $_contactPreferences = null;
    protected $_userDietaryRequirements = null;
    protected $_lastPortal = null;
    protected $_registry = null;

    /**
     * Removes all static cache (singleton) state, and allows the next call to each user entity
     * type to rehydrate from source.
     */
    public function clearStaticCache()
    {
        $this->_contactPreferences = $this->_userDietaryRequirements = null;
    }

    /**
     * The user guid takes the format [portal::internalName]_microtimestamp
     * The guid should be used in any public / client side linking
     * All users will have a guid
     * @return string the user guid.
     */
    public function getUserGUID()
    {
        if ($this instanceof \User) {
            return $this->getUserGUID();
        }
        return $this->getUser()->getUserGUID();
    }

    /**
     * The reference is used in some cases in place of personal details.
     * All users will have a reference, but it might not be identifying, such as 'not set'
     * @return string The users reference
     */
    public function getUserReference()
    {
        if ($this instanceof \PortalUser) {
            return $this->getReference();
        }
        return $this->getPortalUser()->getReference();
    }

    /**
     * Returns the users contact preferences
     * @return null|\UserContactPreferences
     */
    public function getUserContactPreferences()
    {
        $user = $this->getUser();
        if ($user == null) {
            return null;
        }

        $this->_contactPreferences = $user->getRelated('UserContactPreferences');

        return $this->_contactPreferences;
    }

    public function getUserDietaryRequirements()
    {
        $user = $this->getUser();
        if ($user == null) {
            return null;
        }

        $this->_userDietaryRequirements = $user->getRelated('UserDietaryRequirement');


        return $this->_userDietaryRequirements;
    }

    public function getUserId()
    {
        if ($this instanceof \User) {
            return $this->getUserId();
        }
        return $this->getUser()->getUserId();
    }
} 