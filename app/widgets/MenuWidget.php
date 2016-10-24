<?php


class MenuWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $lastPortal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($this->getAuth()->getAuthenticatedUser()->getPortalId());

        $this->view->setLayout('blank');

        $menu = $this->getAuth()->getSessionActiveRole();
        $this->view->userProfile=$this->getAuth()->getAuthenticatedUser()->getUserProfile();

        $this->view->user = $this->getAuth()->getAuthenticatedUser();
        $this->view->userroles = $this->getAuth()->getAuthenticatedUser()->getRoles();
        $this->view->activeRole = $this->getAuth()->getAuthenticatedUser()->getActiveRole();
        $result = $this->view->getRender('widgets/menu', $menu);

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($lastPortal);
        return $result;
    }
}
