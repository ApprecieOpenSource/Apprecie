<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 24/11/14
 * Time: 16:35
 */

class UserTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testUserCreation()
    {
        $userEx = new \Apprecie\Library\Users\UserEx();
        $user = $userEx->createUserWithProfileAndLogin($this->getTestUserEmail(), 'moopyqR1!v3K8d*', 'Tester', 'Tester', 'Mr', null, 'not set', null, 'test');
        $user2 = $userEx->createUserWithProfileAndLogin($this->getSecondTestUserEmail(), 'moopyqR1!v3K8d*', 'Tester2', 'Tester', 'Mr', null, 'not set', null, 'test');

        $this->assertTrue($user instanceof \Apprecie\Library\Users\ApprecieUser, 'The user was not created');
        $this->assertTrue($user2 instanceof \Apprecie\Library\Users\ApprecieUser, 'The second user was not created');

        //prevent recreation
        $this->_firstUserLogin = $user;
        $this->_secondUserLogin = $user2;
    }

    public function testUserRole()
    {
        $user = $this->getTestUserLogin()->getUser();
        $user->addRole('ApprecieSupplier');
        _epm($user);
        $this->assertTrue($user->getActiveRole()->getName() == 'ApprecieSupplier');
        $user->getRoles()[0]->delete();
        $user->clearStaticRoleData();
        $this->assertTrue($user->getActiveRole() == null);
    }

    public function testUserDietaryRequirements()
    {
        $user = $this->getTestUserLogin()->getUser();

        $result = $user->addDietaryRequirement(array('Halal', 'Kosher', 'Seafood'));
        $this->assertTrue($result, 'The dietary requirement was not added to the user.  details follow');
        $requirements = array('Halal', 'Kosher', 'Seafood');

        $this->assertTrue($user->addDietaryRequirement($requirements));

        $this->assertTrue($user->getUserDietaryRequirements()->count() == 3, 'Wrong number of dietary requirements');
        $this->assertTrue($user->hasDietaryRequirement('Halal'));

        foreach($user->getUserDietaryRequirements() as $requirement)
        {
            $this->assertTrue(in_array($requirement->getDietaryRequirement()->getRequirement(), $requirements));
            $requirements = array_filter($requirements, function($e) use ($requirement) {
                    return ($e !== $requirement);
            });
        }

        $user->getUserDietaryRequirements()[0]->delete();
        $user->clearStaticCache();  //you need to call this after deleting in the same request else you wont see the change
        $this->assertTrue($user->getUserDietaryRequirements()->count() == 2, 'Wrong number of dietary requirements after delete');
    }

    public function testUserContactPreferences()
    {
        $user = $this->getTestUserLogin()->getUser();

        $currentPreference = $user->getUserContactPreferences()->getAlertsAndNotifications();

        $user->getUserContactPreferences()->setAlertsAndNotifications(!$user->getUserContactPreferences()->getAlertsAndNotifications());
        $user->getUserContactPreferences()->save();

        $this->assertTrue($user->getUserContactPreferences()->getAlertsAndNotifications() != $currentPreference);
    }

    public function testUserChildAndParent()
    {
        $user = $this->getTestUserLogin()->getUser();
        $user2 = $this->getSecondTestUserLogin()->getUser();

        $this->assertTrue($user->setChildOf($user2));
        $this->assertTrue($user->getParents()->count() == 1, 'wrong number of parents');
        $this->assertTrue($user2->getChildren()->count() == 1, 'wrong number of children');
        $child = $user2->getChildren()[0];
        $child->clearStaticCache();
        $profile = $child->getUserProfile();
        $this->assertTrue($profile->firstname == 'Tester');
    }

    public function testUserOwner()
    {
        $user = $this->getTestUserLogin()->getUser();
        $user2 = $this->getSecondTestUserLogin()->getUser();

        $user->setCreator($user2);

        $this->assertTrue($user->getCreatingUser() == $user2->getUserId());
        $this->assertTrue($user->getCreator() instanceof User);
        $user->clearStaticCache();
        $userLogin = $user->getUserLogin();
        $userLogin->clearStaticCache();
        $this->assertTrue($userLogin->getUser()->getCreator() instanceof User);
    }

    public function testUserFamily()
    {
        $user = $this->getTestUserLogin()->getUser();
        $user2 = $this->getSecondTestUserLogin()->getUser();

        $user->addFamilyMember($user2);
        $this->assertTrue($user->getUserFamilyLinks()->count() == 1);
        $this->assertTrue($user->getIndicatedFamilyUsers()[0]->getUserId() == $user2->getUserId());
        $this->assertTrue($user2->getIndicatedAsFamilyByUsers()[0]->getUserId() == $user->getUserId());
    }


    /**
     * A note should have an encrypted body
     * A user may create many notes
     * A note is about another user
     */
    public function testUserNote()
    {
        $user = $this->getTestUserLogin()->getUser();
        $user2 = $this->getSecondTestUserLogin()->getUser();

        $this->assertTrue($user2->addUserNote($user, 'user 2 is awesome'));
        _ep(_ms($user2));

        $this->assertTrue($user->addUserNote($user2, 'user 2 is awesome'));
        _ep(_ms($user));

        $note1 = $user->getUserNotes()[0];

        $note2 = $user2->getUserNotes()[0];

        $this->assertTrue($note1->getBody() == 'user 2 is awesome');
        $this->assertTrue($note2->getBody() == 'user 2 is awesome');

        $note1Enc = spl_object_hash($note1->getEncryptionProvider());
        $note2Enc = spl_object_hash($note2->getEncryptionProvider());

        $this->assertTrue($note1Enc != $note2Enc, 'The encryption objects should be different');
        $this->assertTrue($note1->getHash() != $note2->getHash(), 'The hashs should be different');

        $note1->encryptFields();
        $note2->encryptFields();

        //notes are encrypted by user
        $this->assertTrue($note1->getBody() != $note2->getBody());

        $note1->delete();
        $note2->delete();

        $notes = $user->getUserNotesAbout($user2);
        $this->assertTrue($notes->count() == 0);
    }

    public function testUserNotifcations()
    {
        $user = $this->getTestUserLogin()->getUser();

        $notification = new UserNotification();
        $notification->setUserId($user->getUserId());
        $notification->setTitle('test notification');
        $notification->setBody('test notification');

        $this->assertTrue($notification->create());
        _ep(_ms($notification));

        $notifications = $user->getNotifications();
        $this->assertTrue($notifications->count() == 1);
        $this->assertTrue($notifications[0]->getTitle() == 'test notification');
        $this->assertTrue($notifications[0]->getBody() == 'test notification');

        $notification->delete();

        $this->assertTrue($user->getNotifications()->count() == 0);

    }

    public function testUserAddress()
    {
        $address = new Address();
        $address->setLabel('test address');
        $address->setPostalCode('test');
        $this->assertTrue($address->create());
        _ep(_ms($address));

        $user = $this->getTestUserLogin();
        $profile = $user->getUserProfile();

        $profile->setHomeAddressId($address->getAddressId());
        $profile->setWorkAddressId($address->getAddressId());
        $profile->setDeliveryAddressId($address->getAddressId());

        $this->assertEqual($profile->getHomeAddress()->getPostalCode(), $profile->getDeliveryAddress()->getPostalCode());
        $address->delete();

        $this->assertTrue($profile->getDeliveryAddress() == null);
    }

    public function testUserInterests()
    {
        $user = $this->getTestUserLogin()->getUser();
        $this->assertTrue($user->addInterest(['Air Racing', 'Boat Racing']));
        _ep(_ms($user));

        $this->assertTrue($user->hasInterest('Air Racing'));
        $this->assertFalse($user->hasInterest('smoking and drinking'));

        $this->assertTrue($user->removeInterest('Air Racing'));
        $this->assertTrue($user->getUserInterests()->count() == 1);
        $this->assertFalse($user->hasInterest('Air Racing'));

        $this->assertTrue($user->addInterest(['Air Racing'], true));
        $this->assertTrue($user->hasInterest('Air Racing'));
        $this->assertTrue($user->getUserInterests()->count() == 1);

        $foundItem = User::findByInterests('Air Racing');
        $this->assertTrue($foundItem->count() > 0);
    }

    public function testUserEncryption()
    {
        /**
         * @var $userProfile UserProfile
         */
        $userProfile = $this->getTestUserLogin()->getUserProfile();

        $this->assertTrue($userProfile->firstname == 'Tester', 'Wrong user name');
        $this->assertTrue($userProfile->getIsDecrypted(), 'The profile should have been decrypted after fetch');
        $this->assertTrue($userProfile->isEncryptionField('firstname'));
        $this->assertTrue($userProfile->isEncryptionField('lastname'));

        $userProfile->encryptFields();

        $this->assertTrue($userProfile->getIsDecrypted() == false);
        $this->assertTrue($userProfile->firstname != ' Tester');

        $userProfile->decryptFields();

        $this->assertTrue($userProfile->firstname == 'Tester', 'Wrong user name');
        $userProfile->firstname = 'molly';
        $userProfile->save();

        /**
         * @var $userProfile UserProfile
         */
        $userProfile = $this->getTestUserLogin()->getUserProfile();
        $this->assertTrue($userProfile->firstname == 'molly', 'Wrong user name');

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries('admin');
        $userProfile->encryptFields();
        $userProfile->decryptFields();

        //user should retain keys after portal change
        $this->assertTrue($userProfile->firstname == 'molly', 'Wrong user name');

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries('test');
    }

    public function testPrivateTables()
    {
        $user = $this->getTestUserLogin()->getUser();
        $testEmail = $this->getTestUserEmail();
        $userId = $user->getUserId();

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries('admin');
        //not traversable
        $this->assertFalse(\UserLogin::findFirst("username='{$testEmail}'"));
        $userObj = User::findFirst("userId = {$userId}");
        $userObj->clearStaticCache();

        $this->assertNull($userObj->getUserProfile());

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries('test');
        //is traversable
        $this->assertTrue($userObj->getUserProfile() instanceof UserProfile);

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries('admin');
        //results cached in object - still holds valid data off portal
        $this->assertTrue($userObj->getUserProfile() instanceof UserProfile);

        //off portal update for already hydrated objects impossible
        $userObj->getUserProfile()->firstname = 'norman';
        $profile = $userObj->getUserProfile();
        $profile->firstname = 'norman';
        $profile->update();

        $userObj->clearStaticCache();

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries('test');

        //persistence confirmed as failed
        $this->assertTrue($this->getTestUserLogin()->getUserProfile()->firstname != 'norman');

        //on portal update for already hydrated object
        $userObj->clearStaticCache();
        $profile = $userObj->getUserProfile();
        $profile->firstname = 'norman';
        $profile->update();
        // _ep((new \Apprecie\Library\Messaging\PrivateMessageQueue())->appendMessage($userObj->getUserProfile())->getMessagesString());
        $userObj->clearStaticCache();

        //persistence confirmed as successful
        $this->assertTrue($this->getTestUserLogin()->getUserProfile()->firstname == 'norman');
    }

    public function testUserDelete()
    {
        $userEx = new \Apprecie\Library\Users\UserEx();
        $user = $this->getTestUserLogin()->getUser();

        if($user == null) {
            $this->fail("Unable to find expected test user {$this->getTestUserEmail()}, cannot possibly delete");
            return;
        }

        $userId = $user->getUserId();

        $this->assertTrue($userEx->deleteUser($user, 'test'), $userEx->getMessagesString());
        _epm($userEx);

        $user = \UserLogin::findFirst("username='{$this->getTestUserEmail()}'");
        if($user != null)
        {
            $this->fail('Queries in the portal still return the user');
            return;
        }

        $user = User::findFirst("userId = {$userId}");
        if($user == null) {
            $this->fail("Unable to find expected test user by id {$userId} this global user record should exist as delete was soft");
            return;
        }

        $this->assertTrue($user->getIsDeleted(), 'The user isDeleted flag is not set');

        //clean up - hard delete
        $this->assertTrue($userEx->deleteUser($user, 'test', false), 'The user delete method returned false');
        _epm($userEx);

        $user = User::findFirst("userId = {$userId}");
        if($user != null) {
            $this->fail("Still able to find expected test user by id {$userId} in global user record should not exist as delete was hard");
            return;
        }

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries('test');
        (new \Apprecie\Library\Users\UserEx())->deleteUser($this->getSecondTestUserLogin(), 'test', false);
    }
}