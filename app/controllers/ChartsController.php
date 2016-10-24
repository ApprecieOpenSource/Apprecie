<?php
class ChartsController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
    }

    public function indexAction()
    {
        $this->view->setLayout('application');
    }

}

