<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 15/12/14
 * Time: 13:19
 */

class GroupTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testUserGroup()
    {
        $portal = $this->getTestPortal();
        $user = $this->getTestUserLogin();
        $user2 = $this->getSecondTestUserLogin();

        $group = new PortalMemberGroup();
        $group->setGroupname('Test Group');
        $group->setPortalId($portal->getPortalId());
        $group->setOwner($user);
        $this->assertTrue($group->create());
        _epm($group);

        $this->assertTrue($group->addUser($user));
        $this->assertTrue($group->addUser($user2));
        _epm($group);

        $this->assertTrue($group->hasUser($user));
        $this->assertTrue($group->hasUser($user2));

        $this->assertTrue($group->removeUser($user));
        $this->assertFalse($group->hasUser($user));

        $group->delete();
    }
} 