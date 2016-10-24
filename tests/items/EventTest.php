<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 14/01/15
 * Time: 14:48
 */

class EventTwoLoginTest extends  \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testEventCreation()
    {
        $item = new Event();
        $item->setLanguageId(3);
        $item->setCreatorId($this->getTestUserLogin()->getUserId());
        $item->setSourcePortalId($this->getTestPortal()->getPortalId());
        $item->setType(\Apprecie\Library\Items\ItemTypes::EVENT);
        $item->setTitle('Automated test events');
        $item->setSummary('moopy summary');
        $item->setState(\Apprecie\Library\Items\ItemState::DRAFT);
        $item->setSourceOrganisationId($this->getTestPortal()->getOwningOrganisation()->getOrganisationId());

        if(! $item->create()) {
            _epm($item);
        }

        $item->setDescription('moopy');
        $item->setAttendanceTerms('moopy terms');
        $item->setRejectionReason('testing');

        if(! $item->update()) {
            _epm($item);
        }

        $media = new ItemMedia();
        $media->setItemId($item->getItemId());
        $media->setOrder(1);
        $media->setSrc('test source');
        $media->setType('test type');

        $this->assertTrue($media->create());

        $this->assertTrue($item->getItemMedia()->count() == 1);
        $this->assertTrue($item->getItemMedia()[0]->getType() == 'test type');

        $item->getItemMedia()[0]->delete();

        $item->setMaxUnits(100);
        $item->setAttendanceTerms('just some test content');

        $this->assertTrue($item->Update());

        $this->assertTrue($item->getAttendanceTerms() == 'just some test content');
        $this->assertTrue($item->getMaxUnits() == 100);

        $item = Event::FindFirstBy('eventId', $item->getEventId());
        $this->assertTrue($item->getMaxUnits() == 100);

        $this->assertTrue($item->getItemMedia()->count() == 0);
        $item->addGoal(['Charity', 'Education']);
        $this->assertTrue($item->hasGoal('Charity'));
        $this->assertTrue($item->hasGoal('Education'));

        $item->removeGoal('Charity');
        $this->assertFalse($item->hasGoal(['Charity']));

        $this->assertTrue($item->delete());
    }
} 