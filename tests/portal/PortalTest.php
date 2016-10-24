<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/12/14
 * Time: 16:39
 */

class PortalTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testPortalContact()
    {
        $contact = new Contact();
        $portal = $this->getTestPortal();

        $address = new Address();
        $address->setLabel('test address');
        $address->setPostalCode('test');
        $this->assertTrue($address->create());

        $contact->setPortalId($portal->getPortalId());
        $contact->setAddressId($address->getAddressId());
        $contact->setIsPrimary(true);
        $contact->setRecordName('test contact');
        $contact->setContactNameAndTitle('my test');

        $this->assertTrue($contact->create());
        _ep(_ms($contact));

        $contacts = $portal->getContacts();

        $this->assertTrue($contacts->count() == 1);
        $this->assertTrue($contacts[0]->getPortal()->getPortalName() == $portal->getPortalName());
        $this->assertTrue($contacts[0]->getAddress()->getPostalCode() == 'test');
        $this->assertTrue($contacts[0]->getContactNameAndTitle() == 'my test');

        $address->delete();
        $contact->delete();

        $this->assertTrue($portal->getContacts()->count() == 0);
    }

    public function testPortalAccountManager()
    {
        $portal = $this->getTestPortal();
        $user = $this->getTestUserLogin()->getUser();

        $portal->setAccountManager($user->getUserId());
        $this->assertTrue($portal->getPortalAccountManager()->getUserId() == $user->getUserId());
    }

    public function testPortalQuotas()
    {
        $portal = $this->getTestPortal();
        $quotas = new Quotas();
        $quotas->setPortalId($portal->getPortalId());
        $quotas->setOrganisationId($portal->getOwningOrganisation()->getOrganisationId());

        $this->assertTrue($quotas->create());
        _epm($quotas);

        $quotas = $portal->getOwningOrganisation()->getQuotas();

        $this->assertTrue($quotas->getManagerTotal() == 1);

        //cannot delete
        try
        {
            $quotas->delete();
            $this->fail('Deleting a portals quotas should have thrown an exception');
        }
        catch(\Exception $ex)
        {//swallow

        }

        $quotas->setManagerTotal(5);
        $this->assertTrue($quotas->update());
        _epm($quotas);
        _ep($quotas->getManagerTotal());

        $this->assertTrue($quotas->getManagerTotal() == 5);
        $this->assertTrue($quotas->consumeManagerQuota(2));
        $this->assertTrue($quotas->update());
        _epm($quotas);

        $this->assertTrue($quotas->getManagerUsed() == 2);
        $this->assertFalse($quotas->consumeManagerQuota(4));
        $this->assertTrue($quotas->getManagerUsed() == 2);
        $this->assertTrue($quotas->consumeManagerQuota(-2));
        $this->assertTrue($quotas->update());
        $this->assertTrue($quotas->getManagerUsed() == 0);

        $connection = \Phalcon\DI::getDefault()->get('db');

        $sql = "DELETE FROM quotas WHERE portalId = {$portal->getPortalId()}";
        $connection->execute($sql);
    }

    public function testPortalBlockedCategories()
    {
        $portal = $this->getTestPortal();
        $this->assertTrue($portal->addBlockedCategory(['Air Racing', 'Boat Racing']));
        _ep(_ms($portal));

        $this->assertTrue($portal->hasBlockedCategory('Air Racing'));
        $this->assertFalse($portal->hasBlockedCategory('smoking and drinking'));

        $this->assertTrue($portal->removeBlockedCategory('Air Racing'));
        $this->assertTrue($portal->getPortalBlockedCategories()->count() == 1);
        $this->assertFalse($portal->hasBlockedCategory('Air Racing'));

        $this->assertTrue($portal->addBlockedCategory(['Air Racing'], true));
        $this->assertTrue($portal->hasBlockedCategory('Air Racing'));
        $this->assertTrue($portal->getBlockedCategories()->count() == 1);

        $this->assertTrue($portal->removeBlockedCategory('Air Racing'));
    }
} 