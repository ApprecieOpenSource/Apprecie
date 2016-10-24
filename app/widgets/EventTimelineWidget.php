<?php


class EventTimelineWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {

    }
    public function doHosting()
    {
        $this->view->setLayout('blank');
        $this->view->activeItems=Event::query()
            ->innerJoin('Item')
            ->where('creatorId=:1:')
            ->andWhere('startDateTime>=:2:')
            ->andWhere('state="approved"')
            ->andWhere('isByArrangement=0')
            ->orderBy('startDateTime')
            ->bind([1=>(new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->getUserId(),2=>date('Y-m-d H:i:s',strtotime('now +28 days'))])->execute();
        return $this->view->getRender('widgets/eventtimeline', 'hosting');
    }
    public function doAcquired()
    {
        $this->view->setLayout('blank');

        $filter=new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('UserItems','Event.itemId=UserItems.itemId')
            ->addAndEqualOrLessThanFilter('endDateTime',date('Y-m-d H:i:s',strtotime('now +28 days')),'Event')
            ->addAndEqualFilter('userId',(new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->getUserId(),'UserItems');

        $this->view->activeItems=Event::findByFilter($filter,'startDateTime');

        return $this->view->getRender('widgets/eventtimeline', 'acquired');
    }
    public function doAttending()
    {
        $this->view->setLayout('blank');

        $filter=new \Apprecie\Library\Search\SearchFilter('Event');
        $filter->addJoin('GuestList','Event.itemId=GuestList.itemId')
            ->addAndEqualFilter('userId',(new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->getUserId(),'GuestList');

        $this->view->activeItems=Event::findByFilter($filter,'startDateTime');

        return $this->view->getRender('widgets/eventtimeline', 'attending');
    }
}