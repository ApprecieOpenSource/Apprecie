<?php


class AddressFinderWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        $this->view->params = $this->getParams();
        return $this->view->getRender('widgets/addressfinder', 'index2');

    }

    public function doGoogle()
    {
        $this->view->setLayout('blank');
        return $this->view->getRender('widgets/addressfinder', 'google');
    }
}