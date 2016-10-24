<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 02/02/2016
 * Time: 16:19
 */
class ACLTest extends \Apprecie\Library\Testing\ApprecieTestBase
{
    protected function getOneManagerThreeInternals()
    {
        $user1 = $this->getTempTestUser(\Apprecie\Library\Users\UserRole::MANAGER);
        $user2 = $this->getTempTestUser(\Apprecie\Library\Users\UserRole::INTERNAL);
        $user3 = $this->getTempTestUser(\Apprecie\Library\Users\UserRole::INTERNAL);
        $user4 = $this->getTempTestUser(\Apprecie\Library\Users\UserRole::INTERNAL);

        $user2->setCreatingUser($user1);
        $user3->setCreatingUser($user1);
        $user4->setCreatingUser($user1);

        $user1->setParentOf($user2);
        $user1->setParentOf($user3);
        $user3->setParentOf($user4);

        return [$user1, $user2, $user3, $user4];
    }


    public function testCreateAndPopulateProviderGroupACL()
    {
        list($manager, $internal1, $internal2, $internal3) = $this->getOneManagerThreeInternals();
        $this->impersonateUser($manager); //we are now manager
        $accessManager = new \Apprecie\Library\Acl\AccessManager();

        $providerGroup = $accessManager->createProviderGroup('test-provider', 'test-provider-description');

        //manager can create groups
        $this->assertTrue
        (
            $providerGroup != false, 'Failed to create provider group : ' . _epm($providerGroup)
        );

        if ($providerGroup != false) {
            //cannot create duplicate group
            $this->assertFalse
            (
                $accessManager->createProviderGroup('test-provider', 'test-provider-description'), 'created duplicate provider group - should not be possible'
            );
            //manager can add members to group
            $this->assertTrue
            (
                $providerGroup->addUsers([$internal1, $internal2, $internal3]), 'Failed to add users to provider group ' . _epm($providerGroup)
            );

            //count members - expect 3
            $this->assertTrue($providerGroup->getMembers()->count() == 3, 'Wrong number of members in group!!');

            //remove members
            $this->assertTrue($providerGroup->removeUsers($internal2), ' Model reported failure to remove users');

            //count members should be 2
            $this->assertTrue($providerGroup->getMembers()->count() == 2, 'Wrong number of members in group!!');


            //should be internal1 and internal3
            $matchCount = 0;
            foreach($providerGroup->getMembers() as $member) {
                if($member->getUserId() == $internal1->getUserId() || $member->getUserId() == $internal3->getUserId()) {
                    $matchCount++;
                }
            }

            $this->assertTrue($matchCount == 2, 'Group contains the wrong members');

            //delete the group
            $this->assertTrue
            (
                $providerGroup->delete(), 'Failed to delete provider group ' . _epm($providerGroup)
            );
        }
    }

    public function testCreateAndPopulateConsumerGroupACL()
    {
        list($manager, $internal1, $internal2, $internal3) = $this->getOneManagerThreeInternals();
        $this->impersonateUser($manager); //we are now manager
        $accessManager = new \Apprecie\Library\Acl\AccessManager();

        $consumerGroup = $accessManager->createConsumerGroup('test-consumer', 'test-consumer-description');

        //manager can create groups
        $this->assertTrue
        (
            $consumerGroup != false, 'Failed to create consumer group : ' . _epm($consumerGroup)
        );

        if ($consumerGroup != false) {
            //cannot create duplicate group
            $this->assertFalse
            (
                $accessManager->createConsumerGroup('test-consumer', 'test-consumer-description'), 'created duplicate consumer group - should not be possible'
            );
            //manager can add members to group
            $this->assertTrue
            (
                $consumerGroup->addUsers([$internal1, $internal2, $internal3]), 'Failed to add users to consumer group ' . _epm($consumerGroup)
            );

            //count members - expect 3
            $this->assertTrue($consumerGroup->getMembers()->count() == 3, 'Wrong number of members in group!!');

            //remove members
            $this->assertTrue($consumerGroup->removeUsers($internal2), ' Model reported failure to remove users');

            //count members should be 2
            $this->assertTrue($consumerGroup->getMembers()->count() == 2, 'Wrong number of members in group!!');


            //should be internal1 and internal3
            $matchCount = 0;
            foreach($consumerGroup->getMembers() as $member) {
                if($member->getUserId() == $internal1->getUserId() || $member->getUserId() == $internal3->getUserId()) {
                    $matchCount++;
                }
            }

            $this->assertTrue($matchCount == 2, 'Group contains the wrong members');

            //delete the group
            $this->assertTrue
            (
                $consumerGroup->delete(), 'Failed to delete consumer group ' . _epm($consumerGroup)
            );
        }
    }

    public function testSubscribeGroup()
    {
        list($manager, $internal1, $internal2, $internal3) = $this->getOneManagerThreeInternals();
        list($managerx, $internal1x, $internal2x, $internal3x) = $this->getOneManagerThreeInternals();

        $this->impersonateUser($manager); //we are now manager
        $accessManager = new \Apprecie\Library\Acl\AccessManager();

        $consumerGroup = $accessManager->createConsumerGroup('test-consumer', 'test-consumer-description');
        $providerGroup = $accessManager->createProviderGroup('test-provider', 'test-provider-description');

        $this->assertTrue($providerGroup->subscribeConsumerGroups($consumerGroup));
    }
}