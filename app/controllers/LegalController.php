<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 23/02/15
 * Time: 12:18
 */
class LegalController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect('');
        $this->view->setLayout('legal');
    }

    public function termsAction()
    {//public

    }

    public function publicAction()
    {//public
        $this->view->setLayout('legal');

        $termsSettings = TermsSettings::query()
            ->join('Terms')
            ->where('isPublic=1')
            ->andWhere('state=1')
            ->andWhere('portalId=:1:')
            ->orderBy('creationDate desc')
            ->bind([1 => $this->getActivePortal()->getPortalId()])
            ->execute();
        if (count($termsSettings)) {
            $terms = Terms::findFirstBy('termsId', $termsSettings[0]->getTermsId());
            $this->view->content = $terms->getDefaultContent();
        } else {
            $termsSettings = TermsSettings::query()
                ->join('Terms')
                ->where('isPublic=1')
                ->andWhere('state=1')
                ->andWhere('portalId is null')
                ->orderBy('creationDate desc')
                ->execute();
            if (count($termsSettings)) {
                $terms = Terms::findFirstBy('termsId', $termsSettings[0]->getTermsId());
                $this->view->content = $terms->getDefaultContent();
            } else {
                $this->dispatcher->forward(
                    array(
                        'controller' => 'legal',
                        'action' => 'terms'
                    )
                );
            }
        }
    }

    public function rsvpAction()
    {//gh presumed public
        $this->view->setLayout('legal');

        $termsSettings = TermsSettings::query()
            ->join('Terms')
            ->where('isRsvp=1')
            ->andWhere('state=1')
            ->andWhere('portalId=:1:')
            ->orderBy('creationDate desc')
            ->bind([1 => $this->getActivePortal()->getPortalId()])
            ->execute();
        if (count($termsSettings)) {
            $terms = Terms::findFirstBy('termsId', $termsSettings[0]->getTermsId());
            $this->view->content = $terms->getDefaultContent();
        } else {
            $termsSettings = TermsSettings::query()
                ->join('Terms')
                ->where('isRsvp=1')
                ->andWhere('state=1')
                ->andWhere('portalId is null')
                ->orderBy('creationDate desc')
                ->execute();
            if (count($termsSettings)) {
                $terms = Terms::findFirstBy('termsId', $termsSettings[0]->getTermsId());
                $this->view->content = $terms->getDefaultContent();
            } else {
                $this->dispatcher->forward(
                    array(
                        'controller' => 'legal',
                        'action' => 'terms'
                    )
                );
            }
        }
    }

    public function privacyAction()
    {//public

    }

    public function manageAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');
    }

    public function editAction($termsId)
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');

        $terms = Terms::findFirstBy('termsId', $termsId);

        $this->view->terms = $terms;
    }

    public function viewAction($termsId)
    {
        $this->getRequestFilter()->addNonRequestRequired('termsId', $termsId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $terms = Terms::resolve($termsId);

        $this->view->setLayout('legal');
        $this->view->content = $terms->getDefaultContent();

        if (!$terms->getState()) {
            $this->requireRoleOrRedirect('SystemAdministrator');
            return true;
        }

        $user = $this->getAuthenticatedUser();
        if ($user) {

            $activeRole = $user->getActiveRole();
            if ($activeRole->getName() === \Apprecie\Library\Users\UserRole::SYS_ADMIN) {
                return true; //system administrators can view any document
            }

            $roles = $user->getRoles();
            foreach ($roles as $role) {
                $roleIds[] = $role->getRoleId();
            }

            $settings = TermsSettings::query()
                ->where('termsId=:1:')
                ->andWhere('portalId=:3:')
                ->bind([1 => $termsId, 3 => $user->getPortalId()])
                ->execute();
            if (count($settings)) {
                foreach ($settings as $setting) {
                    if (in_array($setting->getRoleId(), $roleIds)) {
                        return true;
                    }
                }
            }

            $settings = TermsSettings::query()
                ->where('termsId=:1:')
                ->andWhere('portalId is null')
                ->bind([1 => $termsId])
                ->execute();
            if (count($settings)) {
                foreach ($settings as $setting) {
                    if (in_array($setting->getRoleId(), $roleIds)) {
                        return true;
                    }
                }
            }

            $this->response->redirect('login');

        } elseif ($this->session->has('TERMS_SIGN_UP')) {
            $signUpTerms = $this->session->get('TERMS_SIGN_UP');
            if ($signUpTerms && in_array($termsId, $signUpTerms)) {
                return true;
            } else {
                $this->response->redirect('login');
            }
        } else {
            $this->response->redirect('login');
        }
    }

    public function settingsAction($termsId)
    {
        $this->getRequestFilter()->addNonRequestRequired('termsId', $termsId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->terms = Terms::resolve($termsId);

        $this->view->setLayout('application');

        $this->view->roles = Role::query()->execute();
        $this->view->settings = $this->view->terms->getRelated('TermsSettings');
        $this->view->portals = Portal::find(array("portalName <> 'Apprecie Administration'"));

        $checkedRoles = array();
        foreach ($this->view->settings as $setting) {
            if ($setting->getRoleId()) {
                $checkedRoles[] = $setting->getRoleId();
            }
            if ($setting->getIsRsvp()) {
                $this->view->isRsvp = true;
            }
            if ($setting->getIsPublic()) {
                $this->view->isPublic = true;
            }
        }
        $this->view->checkedRoles = $checkedRoles;
    }

    public function acceptAction()
    {
        $this->view->setLayout('login');

        $user = $this->getAuthenticatedUser();
        if (!$user) {
            $this->response->redirect('login');
        }

        $this->view->portal = \Apprecie\Library\Provisioning\PortalStrap::getActivePortal();
        $this->view->termsIds = $this->session->get('TERMS_UNACCEPTED');

        if ($this->request->isPost() && $this->request->hasPost('accept') && $this->request->getPost('accept')) {
            $success = true;
            foreach ($this->view->termsIds as $termsId) {
                $userTerms = new UserTerms();
                $userTerms->setTermsId($termsId);
                $userTerms->setUserId($user->getUserId());
                if (!$userTerms->save()) {
                    $success = false;
                }
            }
            if ($success) {
                $this->session->remove('TERMS_UNACCEPTED');
                $role = $this->getAuthenticatedUser()->getActiveRole();
                $this->response->redirect($role->getDefaultController() . '/' . $role->getDefaultAction());
            }
        }
    }

    public function myTermsAction()
    {
        $this->view->setLayout('application');

        $user = $this->getAuthenticatedUser();
        if (!$user) {
            $this->response->redirect('login');
        }

        $myTerms = array();
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            //look for portal-specific terms first
            $termsSettings = \TermsSettings::query()
                ->join('Terms')
                ->where('portalId=:1:')
                ->andWhere('roleId=:2:')
                ->andWhere('state=1')
                ->bind([1 => $user->getPortalId(), 2 => $role->getRoleId()])
                ->execute();

            if (!count($termsSettings)) { //if no portal-specific terms is available, continue looking for global terms
                $termsSettings = \TermsSettings::query()
                    ->join('Terms')
                    ->where('portalId is null')
                    ->andwhere('roleId=:1:')
                    ->andWhere('state=1')
                    ->bind([1 => $role->getRoleId()])
                    ->execute();
            }

            if (!count($termsSettings)) {
                $this->dispatcher->forward(array('controller' => 'legal', 'action' => 'terms'));
            } else {
                foreach ($termsSettings as $termsSetting) {
                    $userTerms = \UserTerms::query()
                        ->where('userId=:1:')
                        ->andWhere('termsId=:2:')
                        ->bind([1 => $user->getUserId(), 2 => $termsSetting->getTermsId()])
                        ->execute();

                    if (count($userTerms) && !array_key_exists($termsSetting->getTermsId(), $myTerms)) {
                        $myTerms[$termsSetting->getTermsId()]['terms'] = Terms::findFirstBy('termsId', $termsSetting->getTermsId());
                        $myTerms[$termsSetting->getTermsId()]['userTerms'] = $userTerms[0];
                    }
                }
            }
        }

        $this->view->myTerms = $myTerms;
    }

    public function statusAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->setLayout('application');

        $statusList = array();

        $termsSettings = TermsSettings::query()
            ->join('Terms')
            ->where('isPublic=1')
            ->andWhere('state=1')
            ->andWhere('portalId is null')
            ->orderBy('creationDate desc')
            ->execute();
        if (count($termsSettings)) {
            $terms = Terms::findFirstBy('termsId', $termsSettings[0]->getTermsId());
            $statusList[] = array('type' => 'info', 'role' => _g('Public'), 'portal' => _g('All Portals'), 'terms' => $terms);
        } else {
            $statusList[] = array('type' => 'danger', 'role' => _g('Public'), 'portal' => _g('All Portals'), 'terms' => null);
        }

        $termsSettings = TermsSettings::query()
            ->join('Terms')
            ->where('isPublic=1')
            ->andWhere('state=1')
            ->andWhere('portalId is not null')
            ->orderBy('creationDate desc')
            ->execute();
        if (count($termsSettings)) {
            foreach ($termsSettings as $termsSetting) {
                $terms = Terms::findFirstBy('termsId', $termsSetting->getTermsId());
                $portal = Portal::findFirstBy('portalId', $termsSetting->getPortalId());
                $statusList[] = array('type' => 'warning', 'role' => _g('Public'), 'portal' => $portal->getPortalName(), 'terms' => $terms);
            }
        }

        $roles = Role::query()->execute();
        foreach ($roles as $role) {
            $termsSettings = \TermsSettings::query()
                ->join('Terms')
                ->where('roleId=:1:')
                ->andWhere('state=1')
                ->andWhere('portalId is null')
                ->bind([1 => $role->getRoleId()])
                ->execute();
            if (count($termsSettings)) {
                foreach ($termsSettings as $termsSetting) {
                    $terms = Terms::findFirstBy('termsId', $termsSetting->getTermsId());
                    $statusList[] = array('type' => 'info', 'role' => $role->getDescription(), 'portal' => _g('All Portals'), 'terms' => $terms);
                }
            }
        }

        $this->view->statusList = $statusList;
    }

    public function ajaxCreateAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();
        $status = true;
        $termsId = null;

        if ($this->request->isPost()) {

            if (!$this->request->hasPost('document-title') || !$this->request->hasPost('document-version')) {
                $status = false;
            } else {
                $title = $this->request->getPost('document-title');
                $version = $this->request->getPost('document-version');

                $terms = new Terms();
                $terms->setDefaultName($title);
                $terms->setVersion($version);

                if (!$terms->save()) {
                    $status = false;
                } else {
                    $termsId = $terms->getTermsId();
                }
            }
        } else {
            $status = false;
        }

        if ($status) {
            echo json_encode(array('status' => 'true', 'termsId' => $termsId));
        } else {
            echo json_encode(array('status' => 'false'));
        }
    }

    public function ajaxUpdateAction($termsId)
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();
        $status = true;

        if ($this->request->isPost()) {
            $terms = Terms::findFirstBy('termsId', $termsId);

            if ($this->request->hasPost('document-title') && $this->request->getPost('document-title')) {
                $terms->setDefaultName($this->request->getPost('document-title'));
            }

            if ($this->request->hasPost('document-version') && $this->request->getPost('document-version')) {
                $terms->setVersion($this->request->getPost('document-version'));
            }

            if ($this->request->hasPost('document-content') && $this->request->getPost('document-content')) {
                $terms->setDefaultContent($this->request->getPost('document-content'));
            }

            if (!$terms->update()) {
                $status = false;
            }
        } else {
            $status = false;
        }

        if ($status) {
            echo json_encode(array('status' => 'true', 'termsId' => $termsId));
        } else {
            echo json_encode(array('status' => 'false', 'termsId' => $termsId));
        }
    }

    public function ajaxSaveSettingsAction($termsId)
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();
        $status = true;

        if ($this->request->isPost()) {
            $terms = Terms::findFirstBy('termsId', $termsId);
            if ($this->request->hasPost('state') && $this->request->getPost('state')) {
                $terms->setState(1);
            } else {
                $terms->setState(0);
            }
            if (!$terms->update()) {
                $status = false;
            }

            $oldSettings = $terms->getRelated('TermsSettings');
            $oldSettings->delete();

            $portalId = $this->request->getPost('portal-id');
            $roles = $this->request->getPost('roles');

            if (count($roles)) {
                foreach ($roles as $roleId) {
                    $settings = new TermsSettings();
                    $settings->setTermsId($termsId);

                    switch ($roleId) {
                        case 'rsvp':
                            $settings->setIsRsvp(1);
                            break;
                        case 'public':
                            $settings->setIsPublic(1);
                            break;
                        default:
                            $settings->setRoleId($roleId);
                    }

                    if ($portalId !== 'all') {
                        $settings->setPortalId($portalId);
                    }

                    if (!$settings->save()) {
                        $status = false;
                    }
                }
            }
        } else {
            $status = false;
        }

        if ($status) {
            echo json_encode(array('status' => 'true', 'termsId' => $termsId));
        } else {
            echo json_encode(array('status' => 'false', 'termsId' => $termsId));
        }
    }

    public function ajaxSearchDocumentsAction($pageNumber = 1)
    {
        $this->getRequestFilter()->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->requireRoleOrRedirect('SystemAdministrator');

        $this->view->disable();

        if ($this->request->isPost()) {

            $docArray = array();
            $documents = Terms::query()->execute();

            foreach ($documents as $document) {
                $docData = array();
                $docData['title'] = $document->getDefaultName();
                $docData['version'] = $document->getVersion();
                $docData['creationDate'] = _fdt($document->getCreationDate());
                $docData['termsId'] = $document->getTermsId();
                $docData['state'] = $document->getState();

                $termsSettings = $document->getRelated('TermsSettings');
                $settings = 'Portal: ';

                if (!count($termsSettings)) {
                    $settings .= 'None';
                } elseif ($termsSettings[0]->getPortalId() === null) {
                    $settings .= 'All';
                } else {
                    $portal = Portal::findFirstBy('portalId', $termsSettings[0]->getPortalId());
                    $settings .= $portal->getPortalName();
                }

                $settings .= '<br>Role: ';

                if (!count($termsSettings)) {
                    $settings .= 'None';
                } else {
                    foreach ($termsSettings as $termsSetting) {
                        if (!$termsSetting->getRoleId() && $termsSetting->getIsRsvp()) {
                            $settings .= 'RSVP';
                        } elseif (!$termsSetting->getRoleId() && $termsSetting->getIsPublic()) {
                            $settings .= 'Public';
                        } else {
                            $role = Role::findFirstBy('roleId', $termsSetting->getRoleId());
                            $settings .= '<br>' . $role->getDescription();
                        }
                    }
                }

                $docData['settings'] = $settings;
                $docArray[] = $docData;
            }

            $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
                array(
                    "data" => $docArray,
                    "limit" => 10,
                    "page" => $pageNumber
                )
            );

            $page = $paginator->getPaginate();

            echo json_encode($page);
        }
    }
} 