<?php


class ItemMediaWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        $params=$this->getParams();
        $itemId=$params['itemId'];
        $item=Item::findFirstBy('itemId',$itemId);
        $this->view->item=$item;
        $itemMedia=ItemMedia::query()
            ->where("itemId=:iid:",array('iid'=>$itemId))
            ->orderBy('[order]')
            ->execute();
        $this->view->itemMedia=$itemMedia;
        return $this->view->getRender('widgets/itemmedia', 'index');

    }

    public function doIndex2()
    {
        $this->view->setLayout('blank');
        $params=$this->getParams();
        $itemId=$params['itemId'];
        $item=Item::findFirstBy('itemId',$itemId);
        $this->view->item=$item;
        $itemMedia=ItemMedia::query()
            ->where("itemId=:iid:",array('iid'=>$itemId))
            ->orderBy('[order]')
            ->execute();
        $this->view->itemMedia=$itemMedia;
        return $this->view->getRender('widgets/itemmedia', 'index2');

    }
}