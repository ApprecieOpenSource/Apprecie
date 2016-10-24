<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/01/15
 * Time: 10:49
 */

class TranslationTwoLoginTest extends \Apprecie\Library\Testing\ApprecieTwoLoginTestBase
{
    public function testContentCreation()
    {
        $content = new Content();
        $content->setContent('I am the king');
        $content->setLanguageId(3);
        $content->setDescription('just a test');
        $content->setSourcePortalId($this->getTestPortal()->getPortalId());
        $this->assertTrue($content->create());
        _epm($content);

        $content = Content::findFirstBy('contentId', $content->getContentId());

        $this->assertTrue($content->getContent() == 'I am the king');
        $content->setContent('I was the king');
        $this->assertTrue($content->update());
        $this->assertTrue($content->getContent() == 'I was the king');
        $this->assertTrue($content->delete());
    }

    public function testContentResolver()
    {
        _cl(3);
        $resolver = new \Apprecie\Library\Translation\ContentResolver();
        $macro = $resolver->createContent('I am the x and the y');

        $content = $resolver->resolveObjectFromMacro($macro);
        _epm($resolver);

        $this->assertTrue($content->getLanguageId() == _l());
        $this->assertTrue($content->getContent() == 'I am the x and the y');

        $resolver->createContent('I am in German', 15, $macro);
        _epm($resolver);

        _cl(15);
        $this->assertTrue(_l() == 15);
        _ep($resolver->resolve($macro));
        $this->assertTrue($resolver->resolve($macro) == 'I am in German');

        $contentGerman = $resolver->resolveObjectFromMacro($macro);

        $this->assertTrue($contentGerman->delete());
        $this->assertTrue($content->delete());
    }

} 