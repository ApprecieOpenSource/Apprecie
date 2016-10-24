<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 26/01/15
 * Time: 09:23
 */
class PaymentController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole('Internal');
        $this->setAllowRole('Manager');
        $this->setAllowRole('Client');
        $this->setAllowRole('PortalAdministrator');
    }

    public function indexAction($orderNumber)
    {
        $this->view->setLayout('application');

        $this->getRequestFilter()->addNonRequestRequired('orderNumber', $orderNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $order = \Order::resolve($orderNumber, false);
        $this->view->order = $order;

        if ($order == null) {
            $this->view->noorder = true;
        } else {
            if (! \Apprecie\Library\Acl\AccessControl::userCanSeeOrder($this->getAuthenticatedUser(), $order, false)) {
                $this->logActivity(
                    'Illegal access of order',
                    'The order ' . $orderNumber . 'was accessed by the session owner on the payment / index action.  Access was blocked.'
                );
                $this->view->noorder = true;
            } elseif ($order->getStatus() != \Apprecie\Library\Orders\OrderStatus::PENDING) {
                $this->view->wrongstatus = true;
            } elseif ($order->getTotalPrice() == 0 && $order->getAdminFee() == 0) {
                $this->view->zeroPriceOrder = true;
                $this->view->canPay = true;
            }
            else {
                $this->view->canPay = true;
                try {
                    $this->view->stripe = (new \Apprecie\Library\Payments\StripePayment())->getPaymentForm(
                        $order->getTotalPrice(),
                        $order->getTotalAdminCharge(),
                        $order->getOrderId(),
                        'Card Payment',
                        $order->getCurrency()->getAlphabeticCode()
                    );
                } catch (\Exception $ex) {
                    $this->logActivity('Payment gateway not configured', $ex->getMessage());
                    $this->view->canPay = false;
                }
            }
        }
    }

    /**
     * Payment button posts back to here makecharge occurs via web service.
     */
    public function resultAction()
    {
        $this->getRequestFilter()->addRequired('stripeToken', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request);

        $stripePayment = new \Apprecie\Library\Payments\StripePayment();
        $order = Order::resolve($stripePayment->getStoredOrderNumber());

        \Apprecie\Library\Acl\AccessControl::userCanSeeOrder($this->getAuthenticatedUser(), $order);

        $this->view->setLayout('application');
        $this->view->state = ["state" => "failed", "message" => "Invalid token"];

        $this->view->order = $order;
        if ($order->getStatus() == \Apprecie\Library\Orders\OrderStatus::CANCELLED && !$order->reHoldIfPossible()) {
            //order cancelled during processing to taking to long, and stock no longer available.
            $this->view->state = [
                "state" => "failed",
                "message" => _g(
                    'Order cancelled, and cannot rehold stock, reason : {reason} ',
                    ['reason' => $order->getStatusReason()]
                )
            ];

        } else {
            if ($stripePayment->makeCharge(
                $this->request->getPost('stripeToken'),
                null,
                $order->getSupplierUser()->getOrganisation()
            )
            ) {
                //successful charge here
                $processor = new \Apprecie\Library\Orders\OrderProcessor();
                $processor->completeOrder($stripePayment->getLastTransaction()->getOrder());
                $this->view->state = ["state" => "success"];
                $this->view->itemId = $order->getOrderItems()[0]->getItemId();
            } else {
                $this->view->state = [
                    "state" => "failed",
                    "message" => $stripePayment->getLastTransaction()->getStatusReason()
                ];
            }
        }
    }

    public function complimentaryAction()
    {
        $this->getRequestFilter()->addRequired('orderId', \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request);

        $order = \Order::resolve($this->getRequestFilter()->get('orderId'));
        $this->view->order = $order;

        $this->view->setLayout('application');
        $this->view->state = ["state" => "failed", "message" => "Invalid token"];

        $state = 'failed';

        if(! $order->getStatus() == \Apprecie\Library\Orders\OrderStatus::PENDING) {
            $message = _g('Bad order status');
        } elseif(! $order->getTotalPrice() == 0) {
            $message = _g('This is not a complimentary order');
        } elseif(! \Apprecie\Library\Acl\AccessControl::userCanSeeOrder($this->getAuthenticatedUser(), $order, false)) {
            $message =  _g('You do not have access to this order');
        } else {
            $order->setStatus(\Apprecie\Library\Orders\OrderStatus::PROCESSING);
            $order->update();
            $processor = new \Apprecie\Library\Orders\OrderProcessor();
            $processor->completeOrder($order);
            $state = 'success';
            $message = '';
            $this->view->itemId = $order->getOrderItems()[0]->getItemId();
        }

        $this->view->state = [
            "state" => $state,
            "message" => $message
        ];
    }

    public function payreserveAction($orderItem)
    {
        $this->getRequestFilter()->addNonRequestRequired('orderItem', $orderItem, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $orderItem = OrderItems::resolve($orderItem);

        \Apprecie\Library\Acl\AccessControl::userCanSeeOrder($this->getAuthenticatedUser(), $orderItem->getOrderId());

        $paymentProcessor = new \Apprecie\Library\Orders\OrderProcessor();
        $order = $paymentProcessor->buyReservedInFull($orderItem);

        $this->response->redirect('/payment/index/' . $order);
        $this->response->send();
    }

    public function connectAction()
    {
        $this->requireRoleOrRedirect('PortalAdministrator');

        \Apprecie\Library\Acl\AccessControl::userCanManagePortal($this->getAuthenticatedUser(), \Apprecie\Library\Provisioning\PortalStrap::getActivePortal());

        $this->view->setLayout('application');
        $stripe = new \Apprecie\Library\Payments\StripePayment();

        if ($this->request->hasQuery('code') && $this->request->getQuery(
                'state'
            ) == (new \Apprecie\Library\Security\CSRFProtection())->getSessionToken()
        ) {
            $stripe->saveConnectDetails($this->request->getQuery('code'));
        }

        $paymentSettings = \Organisation::getActiveUsersOrganisation()->getPaymentSettings();

        $this->view->returnurl = urlencode(
            \Apprecie\Library\Request\Url::getConfiguredPortalAddress('admin', 'callback', 'return')
        );
        $this->view->clientid = urlencode($this->config->stripe->client_id);
        if ($paymentSettings->getAccessToken() == null) {
            $this->view->connected = false;
        } else {
            $this->view->connected = true;
            $account = $stripe->getStripeAccountDetails();
            if ($account === false) {
                $this->view->connectError = $stripe->getMessagesString();
            } else {
                $this->view->connectAccount = $account;
            }
        }
    }

    public function vatAction()
    {
        $this->getRequestFilter()->addRequired('vatnumber', \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('PortalAdministrator');
        \Apprecie\Library\Acl\AccessControl::userCanManagePortal($this->getAuthenticatedUser(), \Apprecie\Library\Provisioning\PortalStrap::getActivePortal());

        if ($this->hasRole('PortalAdministrator')) {
            if ($this->request->isPost() and $this->request->getPost('vatnumber') != null and $this->request->getPost(
                    'vatnumber'
                ) != ''
            ) {
                $organisation = Organisation::getActiveUsersOrganisation();
                $organisation->setVatNumber($this->request->getPost('vatnumber'));
                try {
                    $organisation->save();
                    if ($organisation->hasMessages()) {
                        echo json_encode(['status' => 'failed', 'message' => _ms($organisation)]);
                    } else {
                        echo json_encode(
                            ['status' => 'success', 'message' => _g('Your VAT number was saved successfully')]
                        );
                    }
                } catch (Exception $ex) {
                    echo json_encode(['status' => 'failed', 'message' => $ex->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'failed', 'message' => _g('You did not provide a valid VAT number')]);
            }
        } else {
            echo json_encode(['status' => 'failed', 'message' => _g('Unauthorised Access')]);
        }
    }
} 