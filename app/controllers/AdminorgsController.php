<?php

/**
 * Class AdminusersController
 * User control for System Administrators (Apprecie) allowing the viewing, editing and creation of users across all portals
 */
class AdminorgsController extends \Apprecie\Library\Controllers\ApprecieControllerBase
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

        $this->view->portals = Portal::find('portalName != "Apprecie Administration" and portalName != "admin" and portalSubdomain != "admin" and internalAlias != "admin"');

        // If the search form has been submitted
        if ($this->request->isPost() and $this->request->getPost('portalid') != '' && $this->checkCSRF()) {
            $selectedPortal = Portal::findFirstBy("portalId", $this->request->getPost('portalid'));
            $this->view->organisations = $selectedPortal->getOrganisations();
            $this->view->portalName = $selectedPortal->getPortalName();
            $this->view->selectedPortal = $selectedPortal;
        }
    }

    public function saveOrganisationAction()
    {
        $this->getRequestFilter()
            ->addRequired('edit-organisation-id', \Apprecie\Library\Security\ParameterTypes::INT, true)
            ->addRequired('organisation-name', \Apprecie\Library\Security\ParameterTypes::ANY, true)
            ->execute($this->request);

        $this->view->disable();

        $organisationId = $this->request->getPost('edit-organisation-id');

        $organisation = Organisation::findFirstBy('organisationId', $organisationId);
        $quota = Quotas::findFirstBy('organisationId', $organisationId);
        $organisation->setSubDomain($this->request->getPost('organisation-subdomain'));
        $organisation->setOrganisationName($this->request->getPost('organisation-name'));
        if ($this->request->getPost('suspended') == 1) {
            $organisation->setSuspended(true);
        } else {
            $organisation->setSuspended(false);
        }
        $organisation->save();


        $quota->setPortalAdminTotal($this->request->getPost('quota-portal-administrators'), true);
        $quota->setAffiliateSupplierTotal($this->request->getPost('quota-affiliate-suppliers'), true);
        $quota->setApprecieSupplierTotal($this->request->getPost('quota-apprecie-suppliers'), true);
        $quota->setCommissionPercent($this->request->getPost('quota-commission'), true);
        $quota->setInternalMemberTotal($this->request->getPost('quota-internal-members'), true);
        $quota->setManagerTotal($this->request->getPost('quota-managers'), true);
        $quota->setMemberFamilyTotal($this->request->getPost('quota-family-members'));
        $quota->setMemberTotal($this->request->getPost('quota-members'), true);

        if($quota->getMessages()) {
            _jm('failed', _ms($quota->getMessages()));
        } else {
            $quota->save();
            _jm('success', 'updates saved');
        }

    }

    public function addOrganisationAction()
    {
        $this->getRequestFilter()
            ->addRequired('add-organisation-id', \Apprecie\Library\Security\ParameterTypes::INT, true)
            ->addRequired('add-organisation-name', \Apprecie\Library\Security\ParameterTypes::ANY, true)
            ->execute($this->request);

        $this->view->disable();

        $parentOrganisationId = $this->request->getPost('add-organisation-id');
        $parentOrganisation = Organisation::findFirstBy('organisationId', $parentOrganisationId);

        $organisation = new Organisation();
        $organisation->setOrganisationName($this->request->getPost('add-organisation-name'));
        $organisation->setPortalId($parentOrganisation->getPortalId());
        $organisation->setOrganisationDescription('none');
        $organisation->setIsPortalOwner(false);
        $organisation->setSubDomain($this->request->getPost('add-organisation-subdomain'));
        $organisation->create();

        $organisation->setChildOf($parentOrganisationId);

        if ($this->request->getPost('affiliate-supplier') == 1) {
            $organisation->setIsAffiliateSupplierOf($parentOrganisationId);
        }

        $organisation->save();

        $quota = new Quotas();
        $quota->setOrganisationId($organisation->getOrganisationId());
        $quota->setPortalId($organisation->getPortalId());
        $quota->setPortalAdminTotal($this->request->getPost('add-quota-portal-administrators'));
        $quota->setAffiliateSupplierTotal($this->request->getPost('add-quota-affiliate-suppliers'));
        $quota->setApprecieSupplierTotal($this->request->getPost('add-quota-apprecie-suppliers'));
        $quota->setCommissionPercent($this->request->getPost('add-quota-commission'));
        $quota->setInternalMemberTotal($this->request->getPost('add-quota-internal-members'));
        $quota->setManagerTotal($this->request->getPost('add-quota-managers'));
        $quota->setMemberFamilyTotal($this->request->getPost('add-quota-family-members'));
        $quota->setMemberTotal($this->request->getPost('add-quota-members'));
        $quota->create();

        echo json_encode(array('status' => 'success'));

    }

    /**
     * Provides functionality for creating new users from the System Administrator account
     */
    public function createAction()
    {
        $this->view->setLayout('application');
        $this->view->portals = Portal::find();
    }

    public function ViewAction($organisationId)
    {
        $this->view->setLayout('application');
        $this->view->organisation = Organisation::resolve($organisationId);

        $primaryOrganisation = Organisation::query();
        $primaryOrganisation->where("portalId =:pid:", array('pid' => $this->view->organisation->getPortalId()));
        $primaryOrganisation->andWhere('isPortalOwner=1');
        $resultset = $primaryOrganisation->execute();

        $this->view->primaryOrganisation = $resultset[0];
        $this->view->quotas = Quotas::findFirstBy('organisationId', $organisationId);
        $this->view->portal = Portal::findFirstBy('portalId', $this->view->organisation->getPortalId());
        $this->view->domain = $this->di->get('domains')['system'];
        $parentOrganisation = $this->view->organisation->getParents();
        foreach ($parentOrganisation as $parent) {
            $this->view->parentOrganisation = $parent;
        }

        $this->view->childOrganisations = $this->view->organisation->getChildren();
    }

    public function AjaxCreateUserAction()
    {
        $this->view->disable();
    }

    public function AjaxDeleteAction()
    {
        $this->view->disable();

        $this->getRequestFilter()
            ->addRequired('organisationId', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $organisationId = $this->getRequestFilter()->get('organisationId');
        $organisation = Organisation::resolve($organisationId);

        $users = \Apprecie\Library\SearchFilters\Users\UserSearchFilterUtility::userSearch(null, null, null, $organisationId);
        $organisationParents = $organisation->getParents();

        if ($organisation->getIsPortalOwner() === true) {
            echo json_encode(array('status' => 'failed', 'message' => 'Cannot delete the primary organisation'));
        } elseif (count($users) != 0 and count($organisationParents) != 0) {
            echo json_encode(
                array(
                    'status' => 'failed',
                    'message' => 'You cannot delete an organisation that has children or users belonging to it'
                )
            );
        } else {
            $organisation->delete();
            echo json_encode(array('status' => 'success'));
        }
    }
}

