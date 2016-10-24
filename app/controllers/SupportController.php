<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 26/02/15
 * Time: 12:30
 */
class SupportController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect('');
        $this->view->setLayout('legal');
    }

    public function indexAction()
    {

    }
} 