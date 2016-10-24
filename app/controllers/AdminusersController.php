<?php

/**
 * Class AdminusersController
 * User control for System Administrators (Apprecie) allowing the viewing, editing and creation of users across all portals
 */
class AdminusersController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->setAllowPortal('admin');
    }
    /**
     * default action that shows the list of users across all portals
     */
    public function indexAction()
    {
        $this->view->setLayout('application');

        $portals = Portal::query();
        $portals->orderBy('portalName');
        $this->view->portals = $portals->execute();

        $this->view->roles = Role::find(array("name != '" . \Apprecie\Library\Users\UserRole::SYS_ADMIN . "' and name != '" . \Apprecie\Library\Users\UserRole::CONTACT . "'", "order" => "roleId"));
    }

    public function AjaxSearchPortalUsersAction($pageNumber = 1)
    {
        $this->getRequestFilter()
            ->addRequired('portalid', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addFilter('roleid', \Apprecie\Library\Security\ParameterTypes::INT)
            ->addFilter('organisationid', \Apprecie\Library\Security\ParameterTypes::INT)
            ->execute($this->request);

        $this->view->disable();

        // If the search form has been submitted
        $usersArray = [];

        $portalId = $this->getRequestFilter()->get('portalid');
        $role = $this->getRequestFilter()->get('roleid');
        $organisationId = $this->getRequestFilter()->get('organisationid');
        $pageNumber = $this->getRequestFilter()->get('pageNumber');

        $reference = $this->request->getPost('reference');
        $firstname = $this->request->getPost('firstname');
        $lastname = $this->request->getPost('lastname');
        $email = $this->request->getPost('email');

        $sortBy = $this->request->getPost('sortBy');

        if (!is_numeric($role)) {
            $role = null;
        }

        if (!is_numeric($organisationId)) {
            $organisationId = null;
        }

        \Apprecie\Library\Provisioning\PortalStrap::setActivePortal($portalId);
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);

        $allUsers = \Apprecie\Library\SearchFilters\Users\UserSearchFilterUtility::userSearch($portalId, $email, $reference, $organisationId, $role, $sortBy);

        \Apprecie\Library\Provisioning\PortalStrap::setActivePortal('admin');

        foreach ($allUsers as $user) {

            if($user->getIsDeleted() || $user->getUserProfile() == null) {
                continue;
            }

            // do not show contacts to sys admins
            if ($user->hasRole(\Apprecie\Library\Users\UserRole::CONTACT)) {
                continue;
            }

            $userData = $user->getUserProfile();

            if ($firstname) {
                if (stripos($userData->getFirstName(), $firstname) === false) {
                    continue;
                }
            }

            if ($lastname) {
                if (stripos($userData->getLastName(), $lastname) === false) {
                    continue;
                }
            }

            if ($user->getUserReference() == null) {
                $userData->reference = 'None';
            } else {
                $userData->reference = $user->getUserReference();
            }

            $userData->image = Assets::getUserProfileImageContainer($user->getUserId());

            $userData->userId = $user->getUserId();
            $userData->organisationName = $user->getOrganisation()->getOrganisationName();
            $userData->organisationId = $user->getOrganisation()->getOrganisationId();
            $userData->portalId = $user->getPortalId();

            $role = '';
            $firstRole = true;
            foreach ($user->getRoles() as $roleLink) {
                if ($firstRole === true) {
                    $userRole = new \Apprecie\Library\Users\UserRole($roleLink->getRole()->getName());
                    $role .= $userRole->getText();
                    $firstRole = false;
                } else {
                    $userRole = new \Apprecie\Library\Users\UserRole($roleLink->getRole()->getName());
                    $role .= ' / ' . $userRole->getText();
                }
            }

            $userData->role = $role;

            if ($user->getUserLogin()->getPassword() == 'pending') {
                if ($user->getPortalUser()->getRegistrationHash()) {
                    $userData->registrationState = 'Pending';
                } else {
                    $userData->registrationState = 'Not registered';
                }
            } else {
                $userData->registrationState = 'Member';
                $userData->impersonate = 1;
            }
            $usersArray[] = $userData;
        }
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $usersArray,
                "limit" => 10,
                "page" => $pageNumber
            )
        );

        $page = $paginator->getPaginate();

        echo json_encode($page);
    }

    /**
     * Provides functionality for creating new users from the System Administrator account
     */
    public function createAction()
    {
        $this->view->setLayout('application');
        $this->view->portals = Portal::findAll('portalName');
    }

    public function AjaxGenerateRegistrationLinkAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();

        $user = User::resolve($userId);
        $user->clearStaticRoleData();  //@todo GH check if this is still required this was a hack / work around

        $portalId = $user->getPortalId();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);

        if ($user->getStatus() !== \Apprecie\Library\Users\UserStatus::PENDING) {
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
            return;
        }

        if ($user->getPortalUser()->getRegistrationHash()) {
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
            return;
        }

        $organisation = $user->getUser()->getOrganisation();

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        // if the user is a client member we need to remove a licence at this point.
        $quotas = $organisation->getQuotas();
        $quotas->setTransaction($transaction);
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            switch ($role->getRole()->getName()) {
                case \Apprecie\Library\Users\UserRole::PORTAL_ADMIN:
                    $quotas->consumePortalAdministratorQuota(1);
                    $notice = new \Apprecie\Library\Messaging\Notification();
                    $notice->addNotification(
                        $user,
                        _g('Set up your Stripe account'),
                        _g(
                            "Apprecie requires you to set up a Stripe account to allow you to take payments for your Items. Set up your Stripe account now from the Payment Setup page."
                        ),
                        '/payment/connect'
                    );
                    break;
                case \Apprecie\Library\Users\UserRole::MANAGER:
                    $quotas->consumeManagerQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER:
                    $quotas->consumeApprecieSupplierQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::INTERNAL:
                    $quotas->consumeInternalMemberQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER:
                    $quotas->consumeAffiliateSupplierQuota(1);
                    break;
                case \Apprecie\Library\Users\UserRole::CLIENT:
                    $quotas->consumeMemberQuota(1);
                    break;
            }
            if ($quotas->hasMessages() || !$quotas->update()) {
                echo json_encode(
                    array('result' => 'failed', 'message' => 'There are not enough licences available on this portal.')
                );
                return;
            }
        }

        $portalUser = $user->getPortalUser();
        $portalUser->setTransaction($transaction);
        $portalUser->setRegistrationHash(
            (new \Apprecie\Library\Security\Authentication())->generateRegistrationToken()
        );
        $portalUser->update();

        if ($portalUser->hasMessages()) {
            $transaction->rollback();
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
        } else {
            $transaction->commit();
            echo json_encode(
                [
                    'result' => 'success',
                    'registration' => \Apprecie\Library\Request\Url::getConfiguredPortalAddress(
                        $portalId,
                        'signup',
                        'index',
                        [$portalUser->getRegistrationHash()]
                    )
                ]
            );
        }
    }

    public function AjaxRemoveRegistrationLinkAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();

        $user = User::resolve($userId);
        $user->clearStaticRoleData();

        $portalId = $user->getPortalId();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);

        if ($user->getStatus() !== \Apprecie\Library\Users\UserStatus::PENDING) {
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
            return;
        }

        $organisation = $user->getOrganisation();

        $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
        $transaction = $manager->get();

        // if the user is a client member we need to release a licence at this point.
        $quotas = $organisation->getQuotas();
        $quotas->setTransaction($transaction);
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            switch ($role->getRole()->getName()) {
                case \Apprecie\Library\Users\UserRole::PORTAL_ADMIN:
                    $quotas->consumePortalAdministratorQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::MANAGER:
                    $quotas->consumeManagerQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER:
                    $quotas->consumeApprecieSupplierQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::INTERNAL:
                    $quotas->consumeInternalMemberQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER:
                    $quotas->consumeAffiliateSupplierQuota(-1);
                    break;
                case \Apprecie\Library\Users\UserRole::CLIENT:
                    $quotas->consumeMemberQuota(-1);
                    break;
            }
            if (!$quotas->update()) {
                $user->appendMessageEx($quotas);
            }
        }

        $user->setTransaction($transaction);
        $user->getPortalUser()->setRegistrationHash(null);
        $user->getPortalUser()->update();

        if ($user->hasMessages()) {
            $transaction->rollback();
            echo json_encode(
                array('result' => 'failed', 'message' => '')
            );
        } else {
            $transaction->commit();
            echo json_encode(
                array('result' => 'success', 'message' => '')
            );
        }
    }

    public function viewUserAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT)
            ->execute($this->request, true, false);

        $this->view->setLayout('application');

        $user = User::resolve($userId);
        $quotas = $user->getOrganisation()->getQuotas();
        $roles = $user->getRoles();
        $status = $user->getStatus();

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId());

        $portalUser = $user->getPortalUser();

        $showPortalAccessInfo = true;
        if ($status !== \Apprecie\Library\Users\UserStatus::PENDING) {
            $showPortalAccessInfo = false;
        } else {
            foreach ($roles as $role) {
                switch ($role->getRole()->getName()) {
                    case \Apprecie\Library\Users\UserRole::PORTAL_ADMIN:
                        if (!$portalUser->getRegistrationHash() && $quotas->getPortalAdministratorUsed() >= $quotas->getPortalAdministratorTotal()) {
                            $showPortalAccessInfo = false;
                        }

                        break;
                    case \Apprecie\Library\Users\UserRole::MANAGER:
                        if (!$portalUser->getRegistrationHash() && $quotas->getManagerUsed() >= $quotas->getManagerTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER:
                        if (!$portalUser->getRegistrationHash() && $quotas->getApprecieSupplierUsed() >= $quotas->getApprecieSupplierTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::INTERNAL:
                        if (!$portalUser->getRegistrationHash() && $quotas->getInternalMemberUsed() >= $quotas->getInternalMemberTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER:
                        if (!$portalUser->getRegistrationHash() && $quotas->getAffiliateSupplierUsed() >= $quotas->getAffiliateSupplierTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                    case \Apprecie\Library\Users\UserRole::CLIENT:
                        if (!$portalUser->getRegistrationHash() && $quotas->getMemberUsed() >= $quotas->getMemberTotal()) {
                            $showPortalAccessInfo = false;
                        }
                        break;
                }
            }
        }

        $this->view->showPortalAccessInfo = $showPortalAccessInfo;
        $this->view->user = $user;
        $this->view->userProfile = $user->getUserProfile();
        $this->view->portalUser = $user->getPortalUser();
        $this->view->address = Address::findFirstBy('addressId', $user->getUserProfile()->getHomeAddressId());

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        if ($showPortalAccessInfo) {
            $this->view->emailTemplateType = \Apprecie\Library\Mail\EmailTemplateType::getSignupTemplateTypeByRoleName($user->getActiveRole()->getName());
        }
    }

    public function AjaxCreateUserAction()
    {
        $this->getRequestFilter()
            ->addRequired('portal-name', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('role', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('organisationId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('firstname', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('lastname', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('emailaddress', \Apprecie\Library\Security\ParameterTypes::ANY, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();

        $dob = $this->request->getPost('dob-formatted');
        $portalId = $this->request->getPost('portal-name');
        $interests = $this->request->getPost('interests');
        $diet = $this->request->getPost('diet');
        $role = $this->request->getPost('role');
        $communication = $this->request->getPost('communication');

        $portal = Portal::findFirstBy('portalId', $portalId);
        $user = new \Apprecie\Library\Users\UserEx();
        $newUser = $user->createUserWithProfileAndLogin(
            $this->request->getPost('emailaddress'),
            null,
            $this->request->getPost('firstname'),
            $this->request->getPost('lastname'),
            $this->request->getPost('title'),
            $this->request->getPost('organisationId'),
            $this->request->getPost('reference-code'),
            null,
            $portal->getInternalAlias()
        );


        if ($role == -1) {
            $newUser->addRole('Manager');
            $newUser->addRole('PortalAdministrator');

        } elseif ($role == -3) {
            $newUser->addRole('ApprecieSupplier');
            $newUser->addRole('PortalAdministrator');
        } else {
            $newUser->addRole($role);
        }

        $newUser->setTier($this->request->getPost('tier'));

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($portalId);
        if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
            $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
            $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
            $newUser->getUserProfile()->setHomeAddressId($addressId);
        };

        if ($this->request->getPost('gender') == 'female') {
            $newUser->getUserProfile()->setGender(\Apprecie\Library\Users\UserGender::FEMALE);
        } else {
            $newUser->getUserProfile()->setGender(\Apprecie\Library\Users\UserGender::MALE);
        }

        if ($dob != null) {
            $newUser->getUserProfile()->setBirthday(_myd($dob));
        }
        $newUser->getUserProfile()->setPhone($this->request->getPost('phone'));
        $newUser->getUserProfile()->setMobile($this->request->getPost('mobile'));

        $newUser->getUserProfile()->save();

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

        $thisUser = new \Apprecie\Library\Security\Authentication();
        $newUser->setCreatingUser($thisUser->getAuthenticatedUser()->getUserId());

        if (count($diet) != 0) {
            $newUser->addDietaryRequirement($diet);
        }

        if (count($interests) != 0) {
            $newUser->addInterest($interests);
        }
        $contactPreferences = $newUser->getUserContactPreferences();

        if (count($communication) > 0) {
            foreach ($communication as $preference) {
                switch ($preference) {
                    case 'alerts':
                        $contactPreferences->setAlertsAndNotifications(true);
                        break;
                    case 'invitations':
                        $contactPreferences->setInvitations(true);
                        break;
                    case 'suggestions':
                        $contactPreferences->setSuggestions(true);
                        break;
                    case 'partners':
                        $contactPreferences->setPartnerCommunications(true);
                        break;
                    case 'news':
                        $contactPreferences->setUpdatesAndNewsletters(true);
                        break;
                }
            }
            $contactPreferences->save();
        }

        if ($this->request->getPost('user-lookup-value') != null) { //a portal admin might not have an owner
            $newUser->setChildOf($this->request->getPost('user-lookup-value')); //WORKING
        } elseif ($role == '-1' || $role == '-3') { //portal admin and manager -   own parent
            $newUser->setChildOf($newUser);
        }

        $newUser->save();

        echo json_encode(
            [
                'portalId' => $portal->getPortalId(),
                'role' => $newUser->getRoles()[0]->getRole()->getName(),
                'userId' => $newUser->getUserId()
            ]
        );

    }

    public function ImpersonateUserAction($userId)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('userId', $userId, \Apprecie\Library\Security\ParameterTypes::INT)
            ->addRequired('portalid', \Apprecie\Library\Security\ParameterTypes::INT)
            ->execute($this->request, true, false);

        $this->view->disable();
        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($this->request->get('portalid'));
        $user = User::findFirstBy('userId', $userId);
        $auth = new \Apprecie\Library\Security\Authentication();
        $auth->impersonateUser($user);
    }

    public function SendSignupAction()
    {
        $this->getRequestFilter()
            ->addRequired('userId', \Apprecie\Library\Security\ParameterTypes::INT, true)
            ->execute($this->request);

        $user = User::resolve($this->getRequestFilter()->get('userId'));

        $this->view->disable();

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId());

        try {
            $portalUser = $user->getPortalUser();

            if($portalUser->sendRegistrationEmail()) {
                _jm('success', _g('email sent'));
            } else {
                _jm('failed', _ms($portalUser));
            }
        } catch (\Exception $ex) {
            _jm('failed', $ex->getMessage());
        }

        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
    }

    public function AjaxCheckAccountLockAction()
    {
        $this->view->disable();
        if ($this->request->isPost() && $this->request->hasPost('userId')) {
            $user = User::findFirstBy('userId', $this->request->getPost('userId'));
            $accountLockStatus = \Apprecie\Library\Security\AccountLock::checkAccountLock($user);
            if ($accountLockStatus) {
                echo json_encode(array('status' => 'true'));
            } else {
                echo json_encode(array('status' => 'false'));
            }
        }
    }

    /**
     * This should run once only when client quota consumption is moved from the sign-up process to hash generation.
     * It removes all existing pending client user's registration hash because the client quota has not been updated and won't be because the sign-up process no longer consume the client quota.
     * When new registration hash is generated, the client quota will be consumed at the same time.
     * Do not run this if existing client user's hash is generated with the client quota consumed.
     */
    public function RemovePendingClientRegistrationHashAction()
    {
        $this->view->disable();
        $users = \User::getUsersInRole(51);
        foreach ($users as $user) {
            if ($user->getIsDeleted()) {
                _ep('User is deleted');
                continue;
            }

            if ($user->getStatus() !== \Apprecie\Library\Users\UserStatus::PENDING) {
                _ep('User is not pending');
                continue;
            }

            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPOrtalId());
            $portalUser = $user->getPortalUser();
            if ($portalUser->getRegistrationHash()) {
                $portalUser->setRegistrationHash(null);
                $portalUser->update();
                _ep('One removed');
            }
        }
        _ep('Finished');
    }
}

