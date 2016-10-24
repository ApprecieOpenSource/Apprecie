<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 24/12/14
 * Time: 09:16
 */

namespace Apprecie\Library\Payments;

use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Orders\OrderStatus;
use Apprecie\Library\Security\CSRFProtection;
use Apprecie\Library\Tracing\ActivityTraceTrait;
use Apprecie\Library\Tracing\CanTrace;
use Phalcon\Exception;
use Stripe_CardError;
use Stripe_Charge;

class StripePayment extends PrivateMessageQueue implements CanTrace
{
    use ActivityTraceTrait;

    protected $_lastTransaction = null;

    public function getLastTransaction()
    {
        return $this->_lastTransaction;
    }

    public function storePayment($amount, $currency, $fee, $orderNumber, $description)
    {
        $this->session->set('USER_PAYMENT_AMOUNT', $amount);
        $this->session->set('USER_PAYMENT_CURRENCY', $currency);
        $this->session->set('USER_PAYMENT_FEE', $fee);
        $this->session->set('USER_PAYMENT_ORDER_NUMBER', $orderNumber);
        $this->session->set('USER_PAYMENT_DESCRIPTION', $description);
    }

    public function clearPaymentState()
    {
        $this->session->remove('USER_PAYMENT_AMOUNT');
        $this->session->remove('USER_PAYMENT_CURRENCY');
        $this->session->remove('USER_PAYMENT_FEE');
        $this->session->remove('USER_PAYMENT_ORDER_NUMBER');
        $this->session->remove('USER_PAYMENT_DESCRIPTION');
    }

    public function getStoredAmount()
    {
        return $this->session->get('USER_PAYMENT_AMOUNT');
    }

    public function getStoredCurrency()
    {
        return $this->session->get('USER_PAYMENT_CURRENCY');
    }

    public function getStoredFee()
    {
        return $this->session->get('USER_PAYMENT_FEE');
    }

    public function getStoredOrderNumber()
    {
        return $this->session->get('USER_PAYMENT_ORDER_NUMBER');
    }

    public function getStoredDescription()
    {
        return $this->session->get('USER_PAYMENT_DESCRIPTION');
    }

    public function getPaymentForm($amount, $fee, $orderNumber, $description = 'Payment due', $currency = 'gbp')
    {
        $form = '<form action="/payment/result" method="post">
            <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="{publishable_key}"
            data-amount="{amount}" data-description="{description}" data-currency="{currency}">
            </script>
            ' . CSRFProtection::csrf() .  '
        </form>';

        $order = \Order::resolve($orderNumber);

        $paymentSettings = $order->getSupplierUser()->getOrganisation()->getPaymentSettings();

        if ($paymentSettings->getPublishableKey() == null) {
            $this->logActivity(
                'Charge Failed',
                'The organisation ' . \Organisation::getActiveUsersOrganisation()->getOrganisationName(
                ) . ' has not configured stripe, we have no publishable key'
            );
            throw new \LogicException('Payment details are not correctly configured.  Impossible to charge.');
        }

        $this->storePayment($amount, $currency, $fee, $orderNumber, $description);

        return str_replace(
            ['{publishable_key}', '{amount}', '{description}', '{currency}'],
            [$paymentSettings->getPublishableKey(), $amount, $description, $currency],
            $form
        );
    }

    public function getStripeAccountDetails($organisation = null)
    {
        $paymentSettings = \Organisation::getActiveUsersOrganisation()->getPaymentSettings();

        if ($paymentSettings->getPublishableKey() == null) {
            $this->logActivity(
                'Account Connection Failed',
                'The organisation ' . \Organisation::getActiveUsersOrganisation()->getOrganisationName(
                ) . ' has not configured stripe, we have no publishable key'
            );
            return false;
        }

        try {
            $account = \Stripe_Account::retrieve($paymentSettings->getAccessToken());
        } catch (\Exception $ex) {
            $this->appendMessageEx($ex);
            return false;
        }

        return $account;
    }

    public function saveConnectDetails($code)
    {
        $token_request_body = [
            'client_secret' => $this->config->stripe->secret_key,
            'grant_type' => 'authorization_code',
            'code' => $code
        ];

        $req = curl_init('https://connect.stripe.com/oauth/token');
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true);
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));

        $resp = curl_exec($req);

        if (curl_errno($req)) {
            $this->appendMessageEx(curl_error($req));
            return false;
        }

        $resp = json_decode($resp, true);
        curl_close($req);

        if (isset($resp['error'])) {
            $this->appendMessageEx($resp['error_description']);
            return false;
        }

        $organisation = \Organisation::getActiveUsersOrganisation();
        $paymentSettings = $organisation->getPaymentSettings();
        $paymentSettings->setAccessToken($resp['access_token']);
        $paymentSettings->setPublishableKey($resp['stripe_publishable_key']);
        $paymentSettings->setRefreshToken($resp['refresh_token']);
        $paymentSettings->setStripeUserId($resp['stripe_user_id']);

        if (!$paymentSettings->save()) {
            $this->appendMessageEx($paymentSettings);
            return false;
        }

        return true;
    }

    public function makeCharge($token, $clientKey = null, $supplyingOranisation)
    {
        $supplyingOrganisation = \Organisation::resolve($supplyingOranisation);

        $paymentSettings = $supplyingOrganisation->getPaymentSettings();

        if ($paymentSettings->getAccessToken() == null) {
            $this->logActivity(
                'Charge Failed',
                'The organisation ' . $supplyingOrganisation->getOrganisationName(
                ) . ' has not configured stripe connect'
            );
            throw new \LogicException('Payment details are not correctly configured.  Impossible to charge.');
        }

        if ($clientKey == null) {
            $clientKey = $paymentSettings->getAccessToken();
        }

        $order = \Order::resolve($this->getStoredOrderNumber());

        $transaction = new \Transaction();

        $order->setStatus(OrderStatus::PROCESSING);
        $order->update();

        $transaction->setOrganisationId($supplyingOrganisation->getOrganisationId());
        $transaction->setUserId($this->getDI()->getDefault()->get('auth')->getAuthenticatedUser()->getUserId());
        $transaction->setStatus(TransactionStatus::PENDING);
        $transaction->setAmount($order->getTotalPrice());
        $transaction->setCurrencyId($order->getCurrencyId());
        $transaction->setTax($order->getTax());
        $transaction->setOrderId($order->getOrderId());
        $transaction->setTotal($transaction->getAmount() + $transaction->getTax());

        try {

            $customer = \Stripe_Customer::create(
                [
                    'email' => $this->getDI()->getDefault()->get('auth')->getAuthenticatedUser()->getUserProfile(
                        )->getEmail(),
                    'card' => $token
                ],
                $clientKey
            );

            $result = Stripe_Charge::create
                (
                    [
                        'customer' => $customer,
                        'amount' => $this->getStoredAmount(),
                        'currency' => $this->getStoredCurrency(),
                        'application_fee' => $this->getStoredFee(),
                        'description' => $this->getStoredDescription(),
                        'metadata' => ['apprecie_order_number' => $this->getStoredOrderNumber()]
                    ],
                    $clientKey
                );

            $transaction->setGatewayData(print_r($result, true));
            $transaction->setStatus(TransactionStatus::APPROVED);
            $transaction->setStatusReason('Successful Payment');
        } catch (Stripe_CardError $e) {
            $transaction->setStatus(TransactionStatus::DECLINED);
            $transaction->setStatusReason($e->getMessage());
            $this->appendMessageEx($e->getMessage());
            $this->logActivity('Declined Charge', $e->getMessage());
            $order->setStatus(OrderStatus::PENDING);
            $order->update();
        } catch (\Exception $e) {
            $transaction->setStatus(TransactionStatus::ERROR);
            $transaction->setStatusReason($e->getMessage());
            $order->setStatus(OrderStatus::PENDING);
            $order->update();
            $this->appendMessageEx($e->getMessage());
            $this->logActivity('Failed charge', $e->getMessage());
        }

        if (!$transaction->create()) {
            //we do not return false from this as we are hoping the items still process - really this must never happen!!
            $this->logActivity('Failed to create transaction', _ms($transaction));
        } else {
            $this->_lastTransaction = $transaction;
        }

        return !$this->hasMessages();
    }

    public function makeRefund()
    {
        throw new Exception('not implemented');
    }
} 