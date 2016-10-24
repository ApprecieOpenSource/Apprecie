<?php


class GenworthLeaderboardWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        $this->view->userId=$this->getParams()['userId'];
        return $this->view->getRender('widgets/genworthleaderboard', 'index');
    }

    public function doUpload()
    {
        $this->view->setLayout('blank');
        return $this->view->getRender('widgets/genworthleaderboard', 'upload');
    }
}