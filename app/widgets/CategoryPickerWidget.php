<?php


class CategoryPickerWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        $this->view->toplevel=Interest::getTopLevel();
        return $this->view->getRender('widgets/categorypicker', 'index');
    }

    public function doUser(){
        $this->view->setLayout('blank');
        $this->view->toplevel=Interest::getTopLevel();
        $user=User::findFirstBy('userId',$this->getParams()['userId']);
        $this->view->interests=$user->getInterests();
        return $this->view->getRender('widgets/categorypicker', 'user');
    }

    public function doEvent(){
        $this->view->setLayout('blank');
        $this->view->toplevel = Interest::getTopLevel();
        $event = Event::resolve($this->_('eventId'));
        $this->view->interests = $event->getCategories();
        return $this->view->getRender('widgets/categorypicker', 'event');
    }

    protected function replaceTokens($tokens)
    {

    }
}