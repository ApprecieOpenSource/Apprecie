<?php


class AdminSupplyDemandWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        $filter=new \Apprecie\Library\Search\SearchFilter('ChartActiveUsersEventSupply');
        $filter->addAndEqualOrGreaterThanFilter('date',date('Y-m-d', strtotime('now -90 days')));
        $this->view->data=$filter->execute();
        return $this->view->getRender('widgets/adminsupplydemand', 'index');

    }
}