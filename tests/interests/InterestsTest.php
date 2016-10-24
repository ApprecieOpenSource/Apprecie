<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 06/12/14
 * Time: 13:33
 */

class InterestsTest extends UnitTestCase
{
    public function testInterests()
    {
        $interests = Interest::getTopLevel();
        $this->assertTrue($interests->count() == 25);

        $links = $interests[0]->getChildrenLinks();

        $this->assertTrue($links->count() == $interests[0]->getChildren()->count());

        $int1 = $interests[0]->getChildren()[0]->getParents()[0]->getInterest();
        $int2 = $interests[0]->getInterest();

        $this->assertTrue($int1 == $int2);
    }
} 