<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/12/14
 * Time: 12:47
 */

class ItemTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testItemCreation()
    {
        $item = new Item();
        $item->setCreatorId($this->getTestUserLogin()->getUserId());
        $item->setSourcePortalId($this->getTestPortal()->getPortalId());
        $item->setType(\Apprecie\Library\Items\ItemTypes::EVENT);
        $item->setTitle('Automated test events');
        $item->setState(\Apprecie\Library\Items\ItemState::DRAFT);
        $item->setSummary('A nice little item for automated testing');
        $item->setSourceOrganisationId($this->getTestPortal()->getOwningOrganisation()->getOrganisationId());

        $this->assertTrue($item->create());
        _epm($item);

        $this->assertTrue($item->addCategory(['Air Racing', 'Boat Racing']));
        _epm($item);

        $this->assertTrue($item->hasCategory('Air Racing'));
        $this->assertFalse($item->hasCategory('smoking and drinking'));

        $this->assertTrue($item->removeCategory('Air Racing'));
        $this->assertTrue($item->getItemInterestLinks()->count() == 1);
        $this->assertFalse($item->hasCategory('Air Racing'));

        $this->assertTrue($item->addCategory(['Air Racing'], true));
        $this->assertTrue($item->hasCategory('Air Racing'));
        $this->assertTrue($item->getCategories()->count() == 1);

        $foundItem = Item::findByCategories('Air Racing');
        $this->assertTrue($foundItem->count() > 0);

        $item->delete();
    }

    public function testItemMedia()
    {
        $item = new Item();
        $item->setCreatorId($this->getTestUserLogin()->getUserId());
        $item->setSourcePortalId($this->getTestPortal()->getPortalId());
        $item->setType(\Apprecie\Library\Items\ItemTypes::EVENT);
        $item->setTitle('Automated test events');
        $item->setState(\Apprecie\Library\Items\ItemState::DRAFT);
        $item->setSummary('A nice little item for automated testing');
        $item->setSourceOrganisationId($this->getTestPortal()->getOwningOrganisation()->getOrganisationId());

        $item->create();

        $this->assertTrue($item->getSummary() == 'A nice little item for automated testing');
        $media = new ItemMedia();
        $media->setItemId($item->getItemId());
        $media->setOrder(1);
        $media->setSrc('test source');
        $media->setType('test type');

        $this->assertTrue($media->create());
        _epm($media);

        $this->assertTrue($item->getItemMedia()->count() == 1);
        $this->assertTrue($item->getItemMedia()[0]->getType() == 'test type');

        $item->getItemMedia()[0]->delete();

        $this->assertTrue($item->getItemMedia()->count() == 0);
        $item->delete();
    }

    public function testByArrangementCreation()
    {
        $item = new ByArrangement();
        $item->setCreatorId($this->getTestUserLogin()->getUserId());
        $item->setSourcePortalId($this->getTestPortal()->getPortalId());
        $item->setType(\Apprecie\Library\Items\ItemTypes::EVENT);
        $item->setTitle('Automated test events');
        $item->setState(\Apprecie\Library\Items\ItemState::DRAFT);
        $item->setSummary('A nice little item for automated testing');
        $item->setSourceOrganisationId($this->getTestPortal()->getOwningOrganisation()->getOrganisationId());

        if(! $item->create()) {
            _epm($item);
        }

        $media = new ItemMedia();
        $media->setItemId($item->getItemId());
        $media->setOrder(1);
        $media->setSrc('test source');
        $media->setType('test type');

        $this->assertTrue($media->create());
        _epm($media);

        $this->assertTrue($item->getItemMedia()->count() == 1);
        $this->assertTrue($item->getItemMedia()[0]->getType() == 'test type');

        $item->getItemMedia()[0]->delete();

        $item->setMaxUnits(100);
        $item->setStatus(\Apprecie\Library\Items\ByArrangementStatus::ON_HOLD);
        $this->assertTrue($item->Update());

        _epm($item);

        $this->assertTrue($item->getStatus() == \Apprecie\Library\Items\ByArrangementStatus::ON_HOLD);
        $this->assertTrue($item->getMaxUnits() == 100);

        $item = ByArrangement::FindFirstBy('byArrangementId', $item->getByArrangementId());
        $this->assertTrue($item->getMaxUnits() == 100);

        $this->assertTrue($item->getItemMedia()->count() == 0);
        $this->assertTrue($item->delete());
    }
} 