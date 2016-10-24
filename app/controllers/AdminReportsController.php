<?php

class AdminReportsController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->setAllowPortal('admin');
    }

    public function indexAction()
    {
        $this->view->setLayout('application');
    }

    public function ordersAction()
    {
        $this->view->setLayout('application');
    }
}