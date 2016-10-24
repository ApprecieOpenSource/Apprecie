<?php


class CalendarWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        return $this->view->getRender('widgets/calendar', 'index');
    }
}