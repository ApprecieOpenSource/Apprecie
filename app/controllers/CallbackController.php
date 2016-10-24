<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 15/03/15
 * Time: 15:28
 */
class CallbackController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{//@todo gh  Most of these are external consider security carefully.
    public function setupController()
    {
        $this->setNoSessionRedirect('');
    }

    public function getAddressesAction($postcode)
    {
        $this->view->disable();
        $service = new \Apprecie\Library\Addresses\PostcodeService();
        echo $service->findAddressByPostcode($postcode);
    }

    public function getAddressByAddressIdAction($id)
    {
        echo \Apprecie\Library\Addresses\HydrateAddress::getAddressByAddressIdAction($id);
    }

    public function stripehookAction()
    {
        Stripe::setApiKey($this->config->stripe->secret_key);
        $input = @file_get_contents("php://input");

        if (!$input) {
            $this->logActivity('Stripe callback failure ', 'contains no input');
            $this->response->setStatusCode(400, 'Bad request');
            $this->response->send();
            return;
        }

        $event = json_decode($input);

        if (!$event) {
            $this->logActivity('Stripe callback failure - Content does not decode to JSON', $input);
            $this->response->setStatusCode(400, 'Bad request');
            $this->response->send();
            return;
        }

        $this->logActivity('stripe call back - raw', $input);
        $this->logActivity('stripe call back - php', print_r($event, true));

        $log = new StripeLog();
        $log->setStripeEventId($event->id);
        $log->setLiveMode($event->livemode);
        $log->setObject($event->object);
        $log->setPendingWebhooks($event->pending_webhooks);
        $log->setStripeCreatedDate(date("Y-m-d H:i:s", $event->created));
        $log->setType($event->type);
        $log->setData(print_r($event->data, true));

        if (isset($event->user_id)) {
            $paymentSettings = PaymentSettings::findFirstBy('stripeUserId', $event->user_id);

            $organisationId = $paymentSettings ? $paymentSettings->getOrganisationId() : null;

            $log->setOrganisationId($organisationId);
            $log->setStripeUserId($event->user_id);

            if ($log->getType() == 'account.application.deauthorized' && $paymentSettings != null) {
                $paymentSettings->setAccessToken(null);
                $paymentSettings->save();
                $this->logActivity(
                    'Stripe account disconnect',
                    'The organisation ' . $organisationId . ' has disconnected its stripe account'
                );
            }
        }

        if (!$log->create()) {
            $this->logActivity('Stripe callback failure - create log failed', _ms($log));
        }

        $this->response->setStatusCode(200, 'All Good - OK');
        $this->response->send();
    }

    public function returnAction()
    {
        $user = $this->getAuthentication()->getAuthenticatedUser(true); //all stripe configs come back to admin.

        if ($user) {
            if ($user->getPortalId() != \Phalcon\DI::getDefault()->get('portal')->getPortalId()) {
                $this->response->redirect(
                    \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                        $user->getPortal(),
                        'payment',
                        'connect',
                        [
                            'scope' => $this->request->getQuery('scope'),
                            'code' => $this->request->getQuery('code'),
                            'state' => $this->request->getQuery('state')
                        ]
                    )
                );

                $this->response->send();
            } else {
                $this->view->disable();
            }
        } else {
            $this->logActivity(
                'connect return with no user',
                'The stripe connect callback received a call but no user was authenticated'
            );
        }
    }

    /**
     * every minute
     */
    public function taskAction()
    {
        UserItems::lapseHeldAndReserved();
        UserItems::sendReservationWarnings();
        Event::updateEventStatus();
        \Apprecie\Library\Security\AccountLock::expireAccountLocks();
    }

    /**
     * every 10 minutes
     */
    public function longTaskAction()
    {
        Event::processClosedEvents();
        GuestList::processFiveDayAttendingWarnings();
        GuestList::ProcessFiveDayNonResponseToInviteWarnings();
        AdminOrdersReport::buildReport();
    }

    public function buildAdminReportsAction()
    {
        AdminOrdersReport::buildReport();
    }

    /**
     * every day at 23:50
     */
    public function dailyTaskAction()
    {
        ChartActiveUsersEventSupply::updateSupplyAndDemand();
    }

    public function categoryPickerAction($id)
    {
        $this->getRequestFilter()->addNonRequestRequired('id', $id, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $interest = Interest::resolve($id);

        $this->view->disable();
        echo json_encode($interest->getChildren()->toArray());
    }

    public function portalEmailInUseAction()
    {
        $this->view->disable();

        $portalId = $this->request->getPost('portalId');
        $email = $this->request->getPost('email');
        $userId = $this->request->getPost('userId');

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);

        if ($userId != "" and UserLogin::findFirstBy('username', $email) != null) {
            $users = UserLogin::findFirstBy('username', $email);
            if ($users->getUserId() != $userId) {
                echo json_encode(array('users' => 1));
            } else {
                echo json_encode(array('users' => 0));
            }
        } else {
            $users = UserLogin::findBy('username', $email);
            echo json_encode(array('users' => count($users)));
        }
    }


    public function updatesAndNewslettersTaskAction()
    {
        \Apprecie\Library\Mail\UpdatesAndNewsletters::sendVaultUpdates();
    }

    public function changeRoleAction($roleId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('roleId', $roleId, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        list($roleId) = $this->getRequestFilter()->getAll();

        $user = $this->getAuthenticatedUser();
        if ($user) {
            $newRole = Role::resolve($roleId);
            if ($user->hasRole($newRole->getName())) {
                $user->setActiveRole($newRole->getName());
                return $this->response->redirect($newRole->getDefaultController() . '/' . $newRole->getDefaultAction());
            }
        }

        return $this->response->redirect('login');
    }
}