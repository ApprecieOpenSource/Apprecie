<?php

/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 21/10/14
 * Time: 16:25
 */
class EventManagementController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setDenyRole('ApprecieSupplier');
        $this->setDenyRole('AffiliateSupplier');
        $this->setDenyRole('PortalAdministrator');
    }

    public function indexAction()
    {
        $this->view->setLayout('application');
    }

    public function publishAction($eventId)
    {//@todo GH needs more thought around who can publish
        $this->getRequestFilter()
            ->addNonRequestRequired('eventId', $eventId, \Apprecie\Library\Security\ParameterTypes::INT)
            ->addRequired(
                'publishState',
                \Apprecie\Library\Security\ParameterTypes::ANY,
                \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED
            )
            ->execute($this->request);

        $event = Event::resolve($eventId);

        \Apprecie\Library\Acl\AccessControl::userCanPublishItem($this->getAuthenticatedUser(), $event->getItem());

        switch ($this->request->getPost('publishState')) {
            case "parent":
                $event->pushCuratedParent();
                break;
            case "curation":
                $event->pushCuratedApprecie();
                break;
            case "organisation":
                $event->publishPrivate();
                break;
            case "vault":
                if ($event->publishPrivate()) {
                    Organisation::getActiveUsersOrganisation()->addEventToVault(
                        $event,
                        $this->getAuthenticatedUser()->getUserId(),
                        true,
                        true
                    );
                }
                break;
        }
        if ($event->hasMessages()) {
            _epm($event);
        }

        $pdf = new Apprecie\Library\Pdf\Pdf();
        $pdf->generate($event->getItemId());

        echo json_encode(array('status' => 'success', 'message' => 'Event published successfully'));
    }

    public function purchasesAction()
    {
        $this->view->setLayout('application');
    }

    public function reservationsAction()
    {
        $this->view->setLayout('application');
    }

    public function arrangingAction()
    {
        $this->view->setLayout('application');
    }

    public function attendingAction()
    {
        $this->view->setLayout('application');
    }


}

