<?php
class StyleWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        $this->view->styles = $this->getDI()->get('portal')->getPortalStyles();
        if($this->view->styles!=null){
            return $this->view->getRender('widgets/style', 'index');
        }
        return false;
    }
}