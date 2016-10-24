<?php
class AlertTimelineWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');
        $this->view->notices = UserNotification::getActiveNotificationForUser($this->getAuth()->getAuthenticatedUser()->getUserId());
        return $this->view->getRender('widgets/alerttimeline', 'index');
    }

    public function doItem($noticeId = null)
    {
        if($noticeId == null) {
            $noticeId = $this->_('noticeId');
        }

        $notice = UserNotification::resolve($noticeId);
        $this->view->setLayout('blank');
        $this->view->notice = $notice;
        $this->view->dismissText = _g('dismiss');

        if($notice->getUrl() != null) {
            return $this->view->getRender('widgets/alerttimeline', 'timelineLinkItem');
        } else {
            return $this->view->getRender('widgets/alerttimeline', 'timelineItem');
        }
    }
}