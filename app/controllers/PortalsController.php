<?php

class PortalsController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    /**
     * Override this method for setting up controller level permissions
     */
    protected function setupController()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->setAllowPortal('admin');
    }


    public function indexAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');
        $this->view->domains = $this->di->get('domains');
    }

    public function createAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');
        // Get all Portal Users
        $users = \PortalUser::find();

        // Get the profiles for each user
        foreach ($users as $u) {
            $usersArray[] = $u->getUserProfile();
        }

        $this->view->accountManagers = $usersArray;

        if ($this->request->isPost()) {
            $portalName = $this->request->getPost('portalname');
            $subDomain = $this->request->getPost('portalsubdomain');
            $validation = new \Apprecie\Library\Validation\Portal\PortalValidation();

            if ($validation->newPortal($portalName, $subDomain)) {
                $portalFactory = new \Apprecie\Library\Provisioning\PortalFactory();
                $portalFactory->provisionPortal($portalName, $subDomain);
                $validation->appendMessageEx($portalFactory);
            }

            //deal with outcome of operations above
            if (count($validation->getMessages()) > 0) { //failure tell the view
                $this->view->messages = $validation->getMessages();
            } else { //success redirect
                return $this->response->redirect('/portals');
            }
        }
    }

    public function AjaxUpdatePortalAction($portalID)
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('blank');

        if ($this->request->isPost()) {
            $portalName = $this->request->getPost('portalName');
            $subDomain = $this->request->getPost('portalSubdomain');
            $validation = new \Apprecie\Library\Validation\Portal\PortalValidation();

            if ($validation->updatePortal($portalName, $subDomain, $portalID)) {
                $portal = Portal::findFirst('portalId=' . $portalID);
                $portal->setPortalName($this->request->getPost('portalName'));
                $portal->setPortalSubdomain($this->request->getPost('portalSubdomain'));
                $portal->setAccountManager($this->request->getPost('accountManager'));
                $portal->setEdition($this->request->getPost('edition'));

                if ($this->request->getPost('enabled') == 1) {
                    $suspended = false;
                } else {
                    $suspended = true;
                }

                $portal->setSuspended($suspended);
                $portal->update();
                $validation->appendMessageEx($portal);

                $contact = $portal->getContacts()[0];
                $contact->setContactNameAndTitle($this->request->getPost('contactName'));


                if (($this->request->getPost('addressId') != 'null' && $this->request->getPost('addressId') != null) || $this->request->getPost('addressType') == 'manual') {
                    $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
                    $addressId = $saveAddress->addByRequestId($this->request->getPost('addressId'));
                    $contact->setAddressId($addressId);
                };


                $contact->setTelephone($this->request->getPost('telephoneNumber'));
                $contact->setEmail($this->request->getPost('emailAddress'));
                $contact->setMobile($this->request->getPost('mobileNumber'));
                $contact->save();
            }

            //deal with outcome of operations above
            if ($validation->hasMessages()) { //failure tell the view
                _jm('failed',$validation->getMessages());
            } else {
                _jm('success',"Portal was updated successfully");
            }
        }
    }

    public function editAction($portalID)
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');
        // Get all Portal Users
        $users = \PortalUser::find();

        // Get the profiles for each user
        foreach ($users as $u) {
            $usersArray[] = $u->getUserProfile();
        }

        $this->view->accountManagers = $usersArray;

        if ($this->request->isPost()) {
            $portalName = $this->request->getPost('portalname');
            $subDomain = $this->request->getPost('portalsubdomain');
            $validation = new \Apprecie\Library\Validation\Portal\PortalValidation();

            if ($validation->updatePortal($portalName, $subDomain, $portalID)) {
                $portal = Portal::findFirst('portalId=' . $portalID);
                $portal->setPortalName($this->request->getPost('portalname'));
                $portal->setPortalSubdomain($this->request->getPost('portalsubdomain'));
                $portal->setAccountManager($this->request->getPost('account-manager'));
                $portal->setEdition($this->request->getPost('tag'));

                if ($this->request->getPost('enabled') == 1) {
                    $suspended = false;
                } else {
                    $suspended = true;
                }

                $portal->setSuspended($suspended);
                $portal->update();
                $validation->appendMessageEx($portal);

                $contact = $portal->getContacts()[0];
                $contact->setContactNameAndTitle($this->request->getPost('contact-name'));


                if ($this->request->getPost('address-id') != null || $this->request->getPost('addressType') == 'manual') {
                    $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
                    $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));
                    $contact->setAddressId($addressId);
                };


                $contact->setTelephone($this->request->getPost('contact-telephone'));
                $contact->setEmail($this->request->getPost('contact-email'));
                $contact->setMobile($this->request->getPost('contact-mobile'));
                $contact->save();
            }

            //deal with outcome of operations above
            if ($validation->hasMessages()) { //failure tell the view
                $this->view->messages = $validation->getMessages();
            } else {
                $this->view->success = _g("Portal was updated successfully");
            }
        }
        $this->view->portal = Portal::findFirstBy('portalId', $portalID);


        $this->view->contact = $this->view->portal->getContacts()[0];
    }

    public function profileAction($portalID)
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');

        $this->view->portal = Portal::findFirstBy('portalId', $portalID);

        $this->view->quotas = $this->view->portal->getPortalQuotas();
        $accountManagerId = $this->view->portal->getAccountManager();
        $accountManager = User::findFirstBy('userId', $accountManagerId);
        $this->view->accountManagerProfile = $accountManager->getUserProfile();

        $this->view->portalContacts = $this->view->portal->getContacts();

        $this->view->organisations = Organisation::findBy('portalId', $portalID);
    }

    function AjaxPortalNameInUseAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('blank');
        $this->view->portalExists = 'failed';
        if ($this->request->isPost()) {
            if(!$this->checkCSRF()){
                $this->view->portalExists = 'true';
                $this->view->errorMessage = _g('Invalid session');
                return;
            }
            $portal = Portal::findFirstBy("portalName", $this->request->getPost('portalName'));
            $this->view->portalExists = 'false';
            if ($portal != null) {
                $this->view->portalExists = 'true';
                $this->view->errorMessage = _g('The Portal Name is in use');
            }
        }
    }

    function AjaxGetPortalAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('blank');
        if ($this->request->isPost()) {
            $portal = Portal::findFirstBy("portalId", $this->request->getPost('portalId'));
            echo json_encode($portal->toArray());
        }
    }

    function AjaxPortalSubdomainInUseAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('blank');
        $this->view->subdomainExists = 'failed';
        if ($this->request->isPost()) {
            if(!$this->checkCSRF()){
                $this->view->subdomainExists = 'true';
                $this->view->errorMessage = _g('Invalid session');
                return;
            }
            $portal = Portal::findFirst(
                "portalSubdomain='" . $this->request->getPost(
                    'portalSubdomain'
                ) . "' or internalAlias='" . $this->request->getPost('portalSubdomain') . "'"
            );
            $this->view->subdomainExists = 'false';
            if ($portal != null) {
                $this->view->subdomainExists = 'true';
                $this->view->errorMessage = _g('The Portal Subdomain is in use or is being used by an alias');
            }
        }
    }

    function AjaxCreatePortalAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('blank');
        if ($this->request->isPost()) {
            if(!$this->checkCSRF()){
                _jm('failed','invalid session');
                return;
            }
            try {
                $manager = new \Phalcon\Mvc\Model\Transaction\Manager();
                $transaction = $manager->get();

                $portal = new \Apprecie\Library\Provisioning\PortalFactory();
                $newPortal = $portal->provisionPortal(
                    $this->request->getPost('portal-name'),
                    $this->request->getPost('portal-subdomain'),
                    $this->request->getPost('tag'),
                    $transaction
                );

                if ($newPortal === false) {
                    $this->view->createPortal = 'false';
                    $this->view->errorMessage = _ms($portal);
                } else {
                    $newPortal->setSuspended(false);
                    // Set account manager
                    $newPortal->setTransaction($transaction);
                    $newPortal->setAccountManager($this->request->getPost('account-manager'));
                    $newPortal->update();

                    $organisationId = $newPortal->getOwningOrganisation()->getOrganisationId();
                    $organisation = $newPortal->getOwningOrganisation();
                    $organisation->setSubDomain($this->request->getPost('portal-subdomain'));
                    $organisation->save();
                    // Set the portal Quotas
                    $quotas = new Quotas();
                    $quotas->setPortalId($newPortal->getPortalId());
                    $quotas->setAffiliateSupplierTotal($this->request->getPost('quota-affiliate-suppliers', 'int', 0));
                    $quotas->setApprecieSupplierTotal($this->request->getPost('quota-apprecie-suppliers', 'int', 0));
                    $quotas->setManagerTotal($this->request->getPost('quota-managers'));
                    $quotas->setPortalAdminTotal($this->request->getPost('quota-portal-administrators', 'int', 0));
                    $quotas->setMemberTotal($this->request->getPost('quota-members', 'int', 0));
                    $quotas->setMemberFamilyTotal($this->request->getPost('quota-family-members', 'int', 0));
                    $quotas->setInternalMemberTotal($this->request->getPost('quota-internal-members', 'int', 0));
                    $quotas->setCommissionPercent($this->request->getPost('quota-commission', 'float', 0));
                    $quotas->setTransaction($transaction);
                    $quotas->setOrganisationId($organisationId);
                    $quotas->create();

                    if (count($quotas->getMessages()) > 0) {
                        $transaction->rollback('quota creation failed : ' . _ms($quotas));
                    }

                    // Get contact address id
                    $saveAddress = new \Apprecie\Library\Addresses\HydrateAddress();
                    $addressId = $saveAddress->addByRequestId($this->request->getPost('address-id'));

                    // Create the portal contact
                    $contact = new Contact();
                    $contact->setTransaction($transaction);

                    $contact->setPortalId($newPortal->getPortalId());
                    $contact->setAddressId($addressId);
                    $contact->setTelephone($this->request->getPost('contact-telephone'));
                    $contact->setMobile($this->request->getPost('contact-mobile'));
                    $contact->setContactNameAndTitle(
                        $this->request->getPost('contact-firstname') . ' ' . $this->request->getPost('contact-lastname')
                    );
                    $contact->setEmail($this->request->getPost('contact-email'));

                    $contact->setIsPrimary(true);
                    if (!$contact->save()) {
                        $transaction->rollback(_ms($contact));
                    }

                    $portal->confirmPortal($newPortal, $transaction, true);
                    $this->view->createPortal = 'true';
                }
            } catch (Exception $ex) {
                $this->view->createPortal = 'false';
                $this->view->errorMessage = _g('An internal error prevented portal creation.  A log has been created');
                $this->logActivity('Fatal Error', _ms($ex));
            }
        } else {
            $this->view->createPortal = 'false';
            $this->view->errorMessage = _g('Server Error: You no play fair');
        }
        // CREATE PORTAL HERE
        //$this->view->createPortal='true';
    }

    public function AjaxSearchPortalsAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();
        if ($this->request->isPost()) {
            if(!$this->checkCSRF()){
                _jm('failed','invalid session');
                return;
            }
            $resultsPerPage = 10;
            $pageNumber = 1;

            if ($this->request->getPost('pageNumber') <> null) {
                $pageNumber = $this->request->getPost('pageNumber');
            }

            $portalName = $this->request->getPost('portalName');
            $edition = $this->request->getPost('edition');
            $suspended = $this->request->getPost('suspended');

            $resultset = Portal::query();

            if ($portalName <> '') {
                $portalName = '%' . $portalName . '%';
                $resultset->andWhere("portalName like :pname:", array('pname' => $portalName));
            }

            if ($edition <> 'Any') {
                $resultset->andWhere("edition=:pedition:", array('pedition' => $edition));
            }

            if ($suspended <> 'Any') {
                $resultset->andWhere("suspended=:psuspended:", array('psuspended' => $suspended));
            }
            $resultset->orderBy('portalName');
            $portals = $resultset->execute();

            $paginator = new \Phalcon\Paginator\Adapter\Model(
                array(
                    "data" => $portals,
                    "limit" => $resultsPerPage,
                    "page" => $pageNumber
                )
            );

            $page = $paginator->getPaginate();

            $returnArray['ThisPageNumber'] = $pageNumber;
            $returnArray['PageCount'] = $page->total_pages;
            $returnArray['PageResultCount'] = count($page->items);
            $returnArray['TotalResultCount'] = count($portals);

            foreach ($page->items as $portal) {
                $returnArray['items'][] = $portal->toArray();
            }

            echo json_encode($returnArray);
        }
    }
} 