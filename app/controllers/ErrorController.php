<?php

class ErrorController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect('');
    }

    public function fourofourAction()
    {
        $this->view->reset();
        $this->view->setLayout('login');
        $this->response->setStatusCode(404, 'Page not Found');
    }

    public function exceptionAction()
    {
        $this->view->reset();
        $this->view->setLayout('login');
        $this->response->setStatusCode(500, 'Internal Server Error');
    }

    public function ipChangeAction()
    {
        $this->view->reset();
        $this->view->setLayout('login');
    }

    public function accountLockAction()
    {
        $this->view->reset();
        $this->view->setLayout('login');
    }
}

