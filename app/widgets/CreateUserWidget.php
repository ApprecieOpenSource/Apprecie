<?php


class CreateUserWidget extends \Apprecie\Library\Widgets\WidgetBase
{
    public function doIndex()
    {
        $this->view->setLayout('blank');

        $params = $this->getParams();
        $this->view->refreshOnSuccess = (isset($params['refreshOnSuccess']) && $params['refreshOnSuccess']) ? true : false;

        return $this->view->getRender('widgets/createuser', 'index');
    }

    public function doWithEmail()
    {
        $this->view->setLayout('blank');

        $params = $this->getParams();
        $this->view->refreshOnSuccess = (isset($params['refreshOnSuccess']) && $params['refreshOnSuccess']) ? true : false;

        $this->view->portalId = $this->getAuth()->getAuthenticatedUser()->getPortalId();

        return $this->view->getRender('widgets/createuser', 'withemail');
    }

    public function doWithOptionalEmail()
    {
        $this->view->setLayout('blank');

        $params = $this->getParams();
        $this->view->refreshOnSuccess = (isset($params['refreshOnSuccess']) && $params['refreshOnSuccess']) ? true : false;

        $this->view->portalId = $this->getAuth()->getAuthenticatedUser()->getPortalId();

        return $this->view->getRender('widgets/createuser', 'withoptionalemail');
    }
}
