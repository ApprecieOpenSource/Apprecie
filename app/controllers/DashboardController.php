<?php

/**
 * Class DashboardController displays the dashboard for the active user role
 */
class DashboardController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $user = $this->getAuthenticatedUser();
        if ($user) {
            switch ($user->getActiveRole()) {
                case \Apprecie\Library\Users\UserRole::CLIENT:
                case \Apprecie\Library\Users\UserRole::MANAGER:
                case \Apprecie\Library\Users\UserRole::INTERNAL:
                case \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER:
                case \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER:
                    $this->response->redirect('vault');
                    $this->response->send();
                    break;
            }
        } else {
            $this->response->redirect("login");
            $this->response->send();
        }
    }

    /**
     * Determine the active role and forward to associated action
     * @return Dispatcher forward to role dashboard
     */
    public function indexAction($newRole = null)
    {
        if ($newRole != null) {
            $this->getAuthenticatedUser()->setActiveRole($newRole);
        }

        if ($this->isActiveRole('SystemAdministrator', false, false)) {
            return $this->dispatcher->forward(array('controller' => 'dashboard', 'action' => 'SystemAdministrator'));
        } elseif ($this->isActiveRole('PortalAdministrator', false, false)) {
            return $this->dispatcher->forward(array('controller' => 'dashboard', 'action' => 'PortalAdministrator'));
        } elseif ($this->isActiveRole('Manager', false, false)) {
            return $this->dispatcher->forward(array('controller' => 'dashboard', 'action' => 'Manager'));
        } elseif ($this->isActiveRole('Internal', false, false)) {
            return $this->dispatcher->forward(array('controller' => 'dashboard', 'action' => 'Internal'));
        } elseif ($this->isActiveRole('Client', false, false)) {
            return $this->dispatcher->forward(array('controller' => 'dashboard', 'action' => 'Client'));
        } elseif ($this->isActiveRole('ApprecieSupplier', false, false)) {
            return $this->dispatcher->forward(array('controller' => 'dashboard', 'action' => 'ApprecieSupplier'));
        } elseif ($this->isActiveRole('AffiliateSupplier', false, false)) {
            return $this->dispatcher->forward(array('controller' => 'dashboard', 'action' => 'AffiliateSupplier'));
        } else {
            $this->response->redirect("login");
        }
    }

    /**
     * The System Administrator dashboard
     */
    public function SystemAdministratorAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');
        $this->view->userProfile = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser(
        )->getUserProfile();
    }

    public function PortalAdministratorAction()
    {
        $this->requireRoleOrRedirect('PortalAdministrator');
        $this->view->setLayout('application');
        $this->view->userProfile = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser(
        )->getUserProfile();
    }

    public function ManagerAction()
    {
        $this->requireRoleOrRedirect('Manager');
        $this->view->setLayout('application');
        $this->view->userProfile = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser(
        )->getUserProfile();
    }

    public function InternalAction()
    {
        $this->requireRoleOrRedirect('Internal');
        $userEx = new \Apprecie\Library\Users\UserEx();
        $this->view->setLayout('application');
        $this->view->userProfile = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser(
        )->getUserProfile();
    }

    public function ApprecieSupplierAction()
    {
        $this->requireRoleOrRedirect('ApprecieSupplier');
        $this->view->setLayout('application');
        $this->view->userProfile = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser(
        )->getUserProfile();
    }

    public function AffiliateSupplierAction()
    {
        $this->requireRoleOrRedirect('AffiliateSupplier');
        $this->view->setLayout('application');
        $this->view->userProfile = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser(
        )->getUserProfile();
    }

    public function ClientAction()
    {
        $this->requireRoleOrRedirect('Client');
        $this->view->setLayout('application');
        $this->view->userProfile = (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser(
        )->getUserProfile();
    }
}

