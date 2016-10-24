<?php

class PdfController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setNoSessionRedirect('');
    }

    public function getAction($itemId)
    {
        $this->getRequestFilter()->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $item = Item::resolve($itemId);
        \Apprecie\Library\Acl\AccessControl::userCanViewItem($this->getAuthenticatedUser(), $item);

        $this->view->disable();

        $this->response->setContentType('application/Pdf');
        $this->response->setHeader("Content-Disposition", 'attachment; filename="Brochure '.$itemId.'.Pdf"');

        if(file_exists(Assets::getItemAssetDirectory($itemId).'/'.$itemId.'.Pdf')){
            echo file_get_contents(Assets::getItemAssetDirectory($itemId).'/'.$itemId.'.Pdf');
        } else {
            $pdf = new \Apprecie\Library\Pdf\Pdf();
            $pdf->generate($itemId);

            if($pdf->getMessageCount() < 1) {
                echo file_get_contents(Assets::getItemAssetDirectory($itemId) . '/' . $itemId . '.Pdf');
            } else {
                throw new \Phalcon\Exception('Sorry there was an issue preparing the pdf file.  Please try again later. ' . $pdf->getMessagesString());
            }
        }
    }

    public function rsvpAction($itemId, $invitationHash)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('itemId', $itemId, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->addNonRequestRequired('invitationHash', $invitationHash, \Apprecie\Library\Security\ParameterTypes::ANY, \Apprecie\Library\Security\ParameterAjax::AJAX_DENIED)
            ->execute($this->request, true, false);

        $guestRecord = GuestList::findFirstBy('invitationHash', $invitationHash);

        if (!$guestRecord || $guestRecord->getItemId() !== $itemId) {
            $response = $this->response;
            $response->redirect('error/fourofour');
            $response->send();
        }

        $this->view->disable();

        $this->response->setContentType('application/Pdf');
        $this->response->setHeader("Content-Disposition", 'attachment; filename="Brochure '.$itemId.'.Pdf"');

        if(file_exists(Assets::getItemAssetDirectory($itemId).'/'.$itemId.'.Pdf')){
            echo file_get_contents(Assets::getItemAssetDirectory($itemId).'/'.$itemId.'.Pdf');
        } else {
            $pdf = new \Apprecie\Library\Pdf\Pdf();
            $pdf->generate($itemId);

            if($pdf->getMessageCount() < 1) {
                echo file_get_contents(Assets::getItemAssetDirectory($itemId) . '/' . $itemId . '.Pdf');
            } else {
                throw new \Phalcon\Exception('Sorry there was an issue preparing the pdf file.  Please try again later. ' . $pdf->getMessagesString());
            }
        }
    }

    public function viewEventAction($itemId = null, $sharedKey = null)
    {
        if($sharedKey != $this->config->security->apiSharedKey) {
            $this->response->setStatusCode(404, 'Not Found');
            $this->response->send();
        } else {
            try {
                $item = Item::resolve($itemId);
                $this->view->setLayout('rsvp');
                $this->view->event = $item->getEvent();
            } catch(\Exception $ex) {
                echo "Sorry the requested item was not found on this server";
            }
        }
    }
}