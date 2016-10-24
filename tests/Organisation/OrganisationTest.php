<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 20/01/15
 * Time: 14:37
 */

class OrganisationTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testOrganisation()
    {
        $cleanOrg = $this->getTestPortal()->getOwningOrganisation();
        $cleanOrg->setIsPortalOwner(false);
        $cleanOrg->delete();

        $org = new Organisation();
        $org->setOrganisationDescription('test organisation');
        $org->setOrganisationName('test inc');
        $org->setPortalId($this->getTestPortal()->getPortalId());
        $this->assertTrue($org->create());

        //test default owning organisation creation
        $org2 = $this->getTestPortal()->getOwningOrganisation();

        $this->assertTrue($org2 != false);
        $this->assertTrue($org2->getOrganisationName() == $this->getTestPortal()->getPortalName());

        //add a second organisation as owner
        $org->setIsPortalOwner(true);
        $this->assertTrue($org->update() == false);

        //relationship
        $org2->setParentOf($org);
        $this->assertTrue($org2->getChildren()->count() == 1);
        $org2->setIsAffiliateSupplierOf($org);
        $org2->setSubDomain('moopy');
        $org2->update();

        //_ep($org->getAffiliateSuppliers());
        $this->assertTrue($org->getAffiliateSuppliers()->count() == 1);
        $this->assertTrue($org->getAffiliateSuppliers()[0]->getOrganisationName() == $this->getTestPortal()->getPortalName());
        $this->assertTrue($org2->getSubDomain() == 'moopy');

        $org2->addCanBeManagedBy($this->getSecondTestUserLogin());
        $this->assertTrue($org2->canBeManagedBy($this->getSecondTestUserLogin()));

        $this->assertTrue($this->getSecondTestUserLogin()->getUser()->getCanManageOrganisation($org2));
        $this->assertTrue($this->getSecondTestUserLogin()->getUser()->getCanManageOrganisations()->count() == 1);
        $this->assertTrue($org2->removeCanBeManagedBy($this->getSecondTestUserLogin()));
        $this->assertTrue($org2->canBeManagedBy($this->getSecondTestUserLogin()) == false);

        $org->setIsPortalOwner(false);
        $this->assertTrue($org->delete());
        $org2->setIsPortalOwner(false);
        $this->assertTrue($org2->delete());
    }
} 