<?php

class OrdersController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole('Internal');
        $this->setAllowRole('Manager');
        $this->setAllowRole('Client');
    }

    public function indexAction()
    {
        $this->view->setLayout('application');
        $options = \Apprecie\Library\Model\FindOptionsHelper::prepareFindOptions('orderId DESC');
        $this->view->orders = $this->getAuthenticatedUser()->getOrders($options);
    }

    public function orderAction($orderId)
    {
        $this->getRequestFilter()->addNonRequestRequired('eventId', $orderId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $order = Order::resolve($orderId);

        \Apprecie\Library\Acl\AccessControl::userCanSeeOrder($this->getAuthenticatedUser(), $order);

        $this->view->setLayout('application');
        $order = Order::findBy('orderId', $orderId);
        $this->view->order = $order;
    }
}

