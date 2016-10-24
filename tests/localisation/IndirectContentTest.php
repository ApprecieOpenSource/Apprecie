<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 13/01/15
 * Time: 14:22
 */

class IndirectContentTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testIndirectContentCreation()
    {
        $portal = $this->getTestPortal();
        $portal->setLanguageId(3);
        $portal->setDescription('some description in the English language');
        $portal->update();
        _epm($portal);

        $portal->setLanguageId(35);  //random
        $portal->setDescription('I am content in languageID 35');
        $portal->update();
        _epm($portal);

        $portal->setLanguageId(3);
        $this->assertTrue($portal->getDescription() == 'some description in the English language');
        $portal->setLanguageId(35);
        $this->assertTrue($portal->getDescription() == 'I am content in languageID 35');
    }
} 