<?php


class UserFinderWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        return $this->view->getRender('widgets/userfinder', 'index');

    }

    public function doAdvanced()
    {
        $this->view->setLayout('blank');
        return $this->view->getRender('widgets/userfinder', 'advanced');

    }

    public function doMultiSelect()
    {
        $this->view->setLayout('blank');
        return $this->view->getRender('widgets/userfinder', 'multiselect');

    }
}