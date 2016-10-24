<?php

class RsvpController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect('');
    }

    public function indexAction()
    {
        $this->view->setLayout('application');
    }

    public function eventAction($hash)
    {
        $this->getRequestFilter()->addNonRequestRequired('hash', $hash, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $this->view->setLayout('rsvp');
        $guestRecord = GuestList::findFirstBy('invitationHash', $hash); //GH secure hash / filter

        if($guestRecord == null) {
            $this->dispatcher->forward(
                [
                    'controller' => 'error',
                    'action'     => 'fourofour'
                ]
            );

            return;
        }

        $item = Item::resolve($guestRecord->getItemId());
        $event = $item->getEvent();

        $this->view->canRsvp = !$event->getIsGuestListClosed();
        $this->view->canRsvpUntil = $event->getGuestListClosedDateTime(true);
        $this->view->item = $item->getHTMLEncodeAdapter();
        $this->view->hash = $hash;
        $this->view->guestRecord = $guestRecord;
        $this->view->user = User::resolve($guestRecord->getUserId());
    }

    public function vieweventAction($hash)
    {
        $this->getRequestFilter()->addNonRequestRequired('hash', $hash, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $this->view->setLayout('rsvp');
        $guestRecord = GuestList::findFirstBy('invitationHash', $hash);

        if($guestRecord == null) {
            $this->dispatcher->forward(
                [
                    'controller' => 'error',
                    'action'     => 'fourofour'
                ]
            );

            return;
        }

        $event = Event::findFirstBy('itemId', $guestRecord->getItemId());
        $this->view->calLink = _u(null, 'rsvp', 'downloadCalendarByRsvpHash', array($hash, $event->getTitle() . '.ics'));
        $this->view->event = $event->getHTMLEncodeAdapter();
        $this->view->hash = $hash;
    }

    public function AjaxAcceptInvitationAction($hash)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('hash', $hash, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->addRequired('spaces', \Apprecie\Library\Security\ParameterTypes::INT, true, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        list($hash, $spaces) = $this->getRequestFilter()->getAll();

        $this->view->disable();
        $guestRecord = GuestList::findFirstBy('invitationHash', $hash);

        if($guestRecord == null || $guestRecord->getSpaces() < $spaces) {
            $this->dispatcher->forward(
                [
                    'controller' => 'error',
                    'action'     => 'fourofour'
                ]
            );

            return;
        }

        if ($guestRecord->getSpaces() > $spaces) {
            $result = UserItems::creditUnit($guestRecord->getItemId(), $guestRecord->getOwningUserId(), null, $guestRecord->getSpaces() - $spaces);
            if ($result) {
                $guestRecord->setSpaces($spaces);
            } else {
                _jm('failed', 'Could not credit a unit');
                return;
            }
        }

        $guestRecord->setStatus('confirmed');
        $guestRecord->setConfirmationSent(date('Y-m-d'));
        $guestRecord->setAttending(true);
        $guestRecord->update();
        _jm('success', 'You have successfully accepted you invitation');
    }

    public function AjaxDeclineInvitationAction($hash)
    {
        $this->getRequestFilter()->addNonRequestRequired('hash', $hash, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $guestRecord = GuestList::findFirstBy('invitationHash', $hash);

        if($guestRecord == null) {
            $this->dispatcher->forward(
                [
                    'controller' => 'error',
                    'action'     => 'fourofour'
                ]
            );

            return;
        }

        if ($guestRecord->getStatus() == 'invited') {
            if (!UserItems::creditUnit($guestRecord->getItemId(), $guestRecord->getOwningUserId(), null, $guestRecord->getSpaces())) {
                _jm('failed', 'Could not credit a unit');
            } else {
                $guestRecord->setStatus('declined');
                $guestRecord->setConfirmationSent(date('Y-m-d'));
                $guestRecord->setAttending(false);
                $guestRecord->update();
                _jm('success', 'You have successfully declined the invitation');
            }
        }
    }

    public function AjaxCancelInvitationAction($hash)
    {
        $this->getRequestFilter()->addNonRequestRequired('hash', $hash, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $this->view->disable();
        $guestRecord = GuestList::findFirstBy('invitationHash', $hash);

        if($guestRecord == null) {
            $this->dispatcher->forward(
                [
                    'controller' => 'error',
                    'action'     => 'fourofour'
                ]
            );

            return;
        }

        if ($guestRecord->getStatus() == 'confirmed') {
            if (!UserItems::creditUnit($guestRecord->getItemId(), $guestRecord->getOwningUserId(), null, $guestRecord->getSpaces())) {
                _jm('failed', 'Could not credit a unit');
            } else {
                $guestRecord->setStatus('cancelled');
                $guestRecord->setConfirmationSent(date('Y-m-d'));
                $guestRecord->setAttending(false);
                $guestRecord->update();
                _jm('success', 'You have successfully canceled your invitation');
            }
        }
    }

    public function downloadCalendarByRsvpHashAction($hash)
    {
        $this->view->disable();

        $this->getRequestFilter()
            ->addNonRequestRequired('hash', $hash, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        list($hash) = $this->getRequestFilter()->getAll();
        $guestRecord = GuestList::findFirstBy('invitationHash', $hash);

        if($guestRecord == null) {
            $this->dispatcher->forward(
                [
                    'controller' => 'error',
                    'action'     => 'fourofour'
                ]
            );

            return;
        }

        $event = Event::findFirstBy('itemId', $guestRecord->getItemId());
        $event->getCalendar(true);
    }
}