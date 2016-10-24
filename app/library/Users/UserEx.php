<?php
namespace Apprecie\Library\Users;

use Apprecie\Library\Guestlist\GuestListStatus;
use Apprecie\Library\Items\EventStatus;
use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Phql\PhqlCacheCleaner;
use Phalcon\DI;
use Phalcon\Exception;
use Phalcon\Mvc\Model\Transaction\Manager;
use Phalcon\Security;

class UserEx extends PrivateMessageQueue
{
    protected static $_activeQueryPortal = null;
    private static $_profileWhitelist =
        [
            'firstname',
            'lastname',
            'title',
            'email',
            'phone',
            'mobile',
            'birthday',
            'gender',
            'address1',
            'address2',
            'address3',
            'town',
            'postcode',
            'countryId',
            'occupationId'
        ];

    public function getActiveQueryPortal()
    {
        if (static::$_activeQueryPortal == null) {
            return DI::getDefault()->get('portal');
        }

        return static::$_activeQueryPortal;
    }

    /**
     * @param $email
     * @param $password
     * @param $firstName
     * @param $lastName
     * @param $title
     * @param $organisationId
     * @param string $reference
     * @param null $profileBucket
     * @param null $portalInternalAlias
     * @param bool $externalTrans
     * Creates a new user comprising private data in $portal  or current portal if undefined
     *
     * This method exists to simplify the process of creating Users, as a user is spread across as many
     * as 4 tables, 4 of which are based on isolated table name prefixes for a given portal.
     *
     * Once you have used this method to create a user, a user should be managed and updated using the
     * existing user model  see \User  \PortalUser
     *
     * <code>
     * if(UserEx::createUserWithProfileAndLogin( ... ))
     * {
     *      $portalUser = \PortalUser::findFirst(...);
     *      $portalUser->getUserLogin()->username = 'newusername';
     *      $portalUser->getUserLogin()->save();
     *
     *      //etc
     * </code>
     * @return bool|\User On success will return the created user, on failure will return false
     */
    public function createUserWithProfileAndLogin(
        $email,
        $password,
        $firstName,
        $lastName,
        $title,
        $organisationId = null,
        $reference = null,
        $profileBucket = null,
        $portalInternalAlias = null,
        $externalTrans = false
    ) {
        if ($portalInternalAlias == null) {
            $portal = DI::getDefault()->get('portal');
        } else { //check portal exists
            $portal = \Portal::findFirst("internalAlias='{$portalInternalAlias}'");
        }

        if (!$portal) {
            $this->appendMessageEx("could not resolve portal");
            return false;
        }

        if ($organisationId == null) {
            $organisationId = $portal->getOwningOrganisation()->getOrganisationId();
        }

        $transaction = null;

        if (!$externalTrans) {
            $manager = new Manager();
            $transaction = $manager->get(); //we have 4 entities - lets be sure the chain completes
        }

        $currentQueryPortal = UserEx::getActiveQueryPortal();
        UserEx::ForceActivePortalForUserQueries($portal);

        $userProfile = new \UserProfile();
        $userProfile->firstname = $firstName;
        $userProfile->lastname = $lastName;
        $userProfile->title = $title;
        $userProfile->email = $email == null ? '' : $email;

        if ($transaction != null) {
            $userProfile->setTransaction($transaction);
        }

        if (!$userProfile->create($profileBucket, static::$_profileWhitelist)) {
            $this->appendMessageEx($userProfile);
        }

        if (!$this->hasMessages()) {
            $userLogin = new \UserLogin();
            $userLogin->setUsername($email == null ? 'pending' : $email);
            $userLogin->setPassword($password != null ? (new Security())->hash($password) : 'pending');

            if ($transaction != null) {
                $userLogin->setTransaction($transaction);
            }

            if (!$userLogin->create()) {
                $this->appendMessageEx($userLogin);
            }
        }

        if (!$this->hasMessages()) {
            $portalUser = new \PortalUser();
            $portalUser->setProfileId($userProfile->getProfileId());
            $portalUser->setLoginId($userLogin->getLoginId());

            if ($reference != null) {
                $portalUser->setReference($reference);
            }

            if ($transaction != null) {
                $portalUser->setTransaction($transaction);
            }

            if (!$portalUser->create()) {
                $this->appendMessageEx($portalUser);
            }
        }

        if (!$this->hasMessages()) {
            $user = new \User();
            $user->setPortalUserId($portalUser->getPortalUserId());
            $user->setUserGUID(str_replace('.', '_', uniqid($portal->getInternalAlias(), true)));
            $user->setPortalId($portal->getPortalId());
            $user->setOrganisationId($organisationId);
            $user->setStatus(UserStatus::PENDING);

            if ($transaction != null) {
                $user->setTransaction($transaction);
            }

            if (!$user->create()) {
                $this->appendMessageEx($user);
            }
        }

        if (!$this->hasMessages()) {
            $contact = new \UserContactPreferences();
            $contact->setUserId($user->getUserId());

            if ($transaction != null) {
                $contact->setTransaction($transaction);
            }

            if (!$contact->create()) {
                $this->appendMessageEx($contact);
            }
        }

        if ($transaction != null) {
            try {
                if (!$this->hasMessages()) {
                    $transaction->commit();
                } else {
                    $transaction->rollback('Could not create user');
                }
            } catch (Exception $ex) {
                $this->appendMessageEx($ex);
            }
        }

        UserEx::ForceActivePortalForUserQueries($currentQueryPortal);
        return $this->hasMessages() == false ? $user : false;
    }

    /**
     * Removes a user from the system in one of two ways.
     * A soft delete,  removes the portal user, but maintains the global user records marking the user as deleted.
     * A non soft delete removes all records from the database.
     *
     * If you do not set portal, then the active portal is used
     *
     * @param $user int|ApprecieUser The userId, or ApprecieUser to delete
     * @param null $portal
     * @param bool $softDelete
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function deleteUser($user, $portal = null, $softDelete = true)
    { //@todo HW $portal here doesn't quite make sense. Can get the portal from the user itself
        if ($portal == null) {
            $portal = DI::getDefault()->get('portal');
        } else {
            $portal = \Portal::resolve($portal);
        }

        $currentQueryPortal = static::getActiveQueryPortal();
        UserEx::ForceActivePortalForUserQueries($portal);

        $user = \User::resolve($user);

        if ($user->getPortalId() != $portal->getPortalId()) {
            UserEx::ForceActivePortalForUserQueries($currentQueryPortal);
            throw new \LogicException('The user to be deleted is not from the indicated portal.');
        }

        if (!$this->canDeleteOrDeactivate($user)) {
            UserEx::ForceActivePortalForUserQueries($currentQueryPortal);
            return false;
        }

        $manager = new Manager();
        $transaction = $manager->get();

        //users might not have all private entities
        $user->clearStaticCache();
        $portalLogin = $user->getUserLogin();
        $user->clearStaticCache();
        $portalProfile = $user->getUserProfile();
        $user->clearStaticCache();
        $portalUser = $user->getPortalUser();

        //release any licenses if user has active/pending portal access
        if ($user->getStatus() === UserStatus::ACTIVE) {
            if (!$user->deactivate($transaction)) {
                $this->appendMessageEx($user);
            }
        } elseif ($user->getStatus() === UserStatus::PENDING && $portalUser->getRegistrationHash()) {
            if (!$user->releaseQuota($transaction)) {
                $this->appendMessageEx($user);
            }
        }

        //remove user from any guest list that is still open
        $guestRecords = \GuestList::query()
            ->where('userId=:1:')
            ->bind([1 => $user->getUserId()])
            ->execute();
        if ($guestRecords->count() > 0) {
            foreach ($guestRecords as $guestRecord) {
                $guestRecord = \GuestList::resolve($guestRecord);
                $item = \Item::resolve($guestRecord->getItemId());
                if (!$item->getEvent()->getIsGuestListClosed()) {
                    if (\UserItems::creditUnit($item->getItemId(), $this->getDI()->getDefault()->get('auth')->getAuthenticatedUser()->getUserId(), $transaction, $guestRecord->getSpaces())) {
                        $guestRecord->setTransaction($transaction);
                        if (!$guestRecord->delete()) {
                            $this->appendMessageEx($guestRecord);
                        }
                    } else {
                        $this->appendMessageEx('Could not credit a unit');
                    }
                }
            }
        }

        $privateEntities = array();
        if ($portalLogin != null) {
            $privateEntities['portalLogin'] = $portalLogin;
        }
        if ($portalProfile != null) {
            $privateEntities['portalProfile'] = $portalProfile;
        }
        if ($portalUser != null) {
            $privateEntities['portalUser'] = $portalUser;
        }
        if (!$softDelete) {
            $privateEntities['user'] = $user;
        } //cascades will do the rest

        foreach ($privateEntities as $key => $entity) {
            $entity->setTransaction($transaction);

            if (!$entity->delete()) {
                $this->appendMessageEx($user);
                break;
            }
        }

        try {
            if (!$this->hasMessages()) {
                if ($transaction->commit()) {
                    if ($softDelete) {
                        $user->setIsDeleted(true);
                        $user->update();
                    }
                }
            } else {
                $transaction->rollback('Could not delete user - all intermediate steps have been rolled back');
            }
        } catch (Exception $ex) {
            $this->appendMessageEx($ex);
        }

        UserEx::ForceActivePortalForUserQueries($currentQueryPortal);

        return $this->hasMessages() == false ? true : false;
    }

    public function canDeleteOrDeactivate($user)
    {
        // parent of any other user,  creator of any published item,
        // supplier of any item ever sold or consumed,  or owner of an active guest list.
        $user = \User::resolve($user);
        $canDeactivate = true;

        $currentQueryPortal = static::getActiveQueryPortal();
        UserEx::ForceActivePortalForUserQueries($user->getPortalId());

        if ($user->getChildren()->count() > 0) {
            $this->appendMessageEx(_g('The user cannot be deactivated as they have child users'));
            $canDeactivate = false;
        }

        $events = \Event::findByCreator($user);

        if ($events->count() > 0) {
            $this->appendMessageEx(_g('The user cannot be deactivated as they have Items in the system.'));
            $canDeactivate = false;
        }

        UserEx::ForceActivePortalForUserQueries($currentQueryPortal);

        return $canDeactivate;
    }

    /**
     * Forces user entity based queries to be honed to the suggested portal rather than the current active portal
     * Note that all user operations that are off portal should take place after a call to this method
     * which enables database function against the suggested portal, including user internal table traversal.
     *
     * One you have finished operating with the off portal users call this method again with no param (null) to
     * switch back to the active portal.
     *
     * <code>
     * //use active portal users
     * $users = \User::find(...);
     * foreach($users as $activePortalUser)
     * {
     *   $activePortalUser-> ...
     *
     * }
     *
     * //use off portal users
     * UserEx:ForceActivePortalForUserQueries('myportal');
     * $users = \User::find(...);
     * foreach($users as $myPortalUser)
     * {
     *   $myPortalUser-> ...
     * }
     *
     * UserEx:ForceActivePortalForUserQueries(); //return to active portal
     * </code>
     *
     * @param $portal string|null|Portal if null active portal will be used. if string must = internalAlias
     */
    public static function ForceActivePortalForUserQueries($portal = null)
    {
        if ($portal == null) {
            $portal = DI::getDefault()->get('portal');
        }

        $portal = \Portal::resolve($portal);

        if (static::$_activeQueryPortal != null && static::$_activeQueryPortal->getPortalId() == $portal->getPortalId()
        ) {
            return;
        }

        static::$_activeQueryPortal = $portal;

        PhqlCacheCleaner::clean();
        $portalUser = new \PortalUser();
        $mgr = $portalUser->getModelsManager();
        //$mgr->__destruct() ; //@todo gav  not needed for Phalcon 2
        $mgr->setModelSource($portalUser, $portalUser->getSource());

        $user = new \User();
        $mgr = $user->getModelsManager();
        //$mgr->__destruct();
        $mgr->setModelSource($user, $user->getSource());

        $userProfile = new \UserProfile();
        $mgr = $userProfile->getModelsManager();
        //$mgr->__destruct();
        $mgr->setModelSource($userProfile, $userProfile->getSource());

        $userLogin = new \UserLogin();
        $mgr = $userLogin->getModelsManager();
        //$mgr->__destruct();
        $mgr->setModelSource($userLogin, $userLogin->getSource());


        \UserProfile::setForcedSource($portal->getPortalGUID());
        \PortalUser::setForcedSource($portal->getPortalGUID());
        \UserLogin::setForcedSource($portal->getPortalGUID());
        \User::setForcedSource($portal->getPortalGUID());
    }
} 