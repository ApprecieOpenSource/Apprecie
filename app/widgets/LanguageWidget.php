<?php
class LanguageWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        $languages=Languages::query()->where("enabled=:en:",array('en'=>true))->orderBy('nativeName')->execute();
        $this->view->languages=$languages;
        return $this->view->getRender('widgets/language', 'index');
    }
}