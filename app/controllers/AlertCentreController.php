<?php

class AlertCentreController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole('ApprecieSupplier');
        $this->setAllowRole('Internal');
        $this->setAllowRole('Manager');
        $this->setAllowRole('Client');
        $this->setAllowRole('AffiliateSupplier');
    }

    public function indexAction()
    {//only exposes data through secured actions below
        $this->view->setLayout('application');
    }

    public function ajaxGetThreadsAction($pageNumber = null)
    {
        $this->getRequestFilter()
            ->addNonRequestRequired('pageNumber', $pageNumber, \Apprecie\Library\Security\ParameterTypes::INT, \Apprecie\Library\Security\ParameterAjax::AJAX_REQUIRED)
            ->execute($this->request);

        $threads = MessageThread::findAllThreadsForUser($this->getAuthenticatedUser());

        $paginator = new \Phalcon\Paginator\Adapter\NativeArray(
            array(
                "data" => $threads->toArray(),
                "limit" => 20,
                "page" => $pageNumber
            )
        );
        $pagedItems = $paginator->getPaginate();

        foreach ($pagedItems->items as $key => $thread) {
            if($this->getAuthenticatedUser()->canSeeMessageThread($thread['threadId'])) {

                $threadData = MessageThread::findFirstBy('threadId', $thread['threadId']);
                if ($threadData->getThreadMessages()->count() == 0) {
                    continue;
                }

                $firstMessage = $threadData->getThreadMessages()[0];
                $recipientUser = $firstMessage->getRecipientUser();
                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($recipientUser->getPortalId());
                $recipientName = $recipientUser->getIsDeleted() ? _g(
                    'Non active user'
                ) : $recipientUser->getUserProfile()->getFullName();

                $sendingUser = $firstMessage->getSendingUser();
                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($sendingUser->getPortalId());


                $senderName = $sendingUser->getIsDeleted() ? _g(
                    'Non active user'
                ) : $sendingUser->getUserProfile()->getFullName();

                $firstMessage->setSent(date('d-m-Y H:i:s', strtotime($firstMessage->getSent())));

                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();

                if ((!$threadData->getSeen()) && $threadData->getThreadMessages()[0]->getTargetUser(
                    ) == $this->getAuthenticatedUser()->getUserId()
                ) {
                    $pagedItems->items[$key]['bold'] = true;
                }

                $pagedItems->items[$key]['firstMessage'] = $firstMessage->toArray();
                $pagedItems->items[$key]['senderName'] = $senderName;
                $pagedItems->items[$key]['recipientName'] = $recipientName;
                $pagedItems->items[$key]['recipientName'] = $recipientName;

                if ($firstMessage->getReferenceItem() != null) {
                    $item = Item::findFirstBy('itemId', $firstMessage->getReferenceItem());
                    $pagedItems->items[$key]['item']['title'] = $item->getTitle();
                    $pagedItems->items[$key]['item']['itemId'] = $item->getItemId();
                }
            }
        }

        echo json_encode($pagedItems);
    }

    /**
     * @param $threadId
     * @throws Exception
     * @throws \Phalcon\Exception
     */
    public function viewAction($threadId)
    {
        $this->view->setLayout('application');

        $this->getRequestFilter()
            ->addNonRequestRequired('threadId', $threadId, \Apprecie\Library\Security\ParameterTypes::INT)
            ->execute($this->request, true, false);

        $auth = new \Apprecie\Library\Security\Authentication();

        $thread = MessageThread::resolve($threadId);
        $user = $this->getAuthenticatedUser();
        $user->canSeeMessageThread($thread);
        $this->view->user = $user;

        $messages = $thread->getThreadMessages();

        if ($messages[0]->getSendingUser()->getUserId() == $auth->getAuthenticatedUser()->getUserId()) {
            $this->view->targetUser = $messages[0]->getRecipientUser()->getUserId();
        } else {
            $this->view->targetUser = $messages[0]->getSendingUser()->getUserId();
        }

        if ($thread->getThreadMessages()[0]->getTargetUser() == $this->getAuthenticatedUser()->getUserId()) {
            $thread->setSeen(true);
            $thread->update();
        }

        $this->view->messages = $messages;
        $this->view->threadId = $threadId;

        $type = $thread->getType();
        if ($type == null) {
            $type = \Apprecie\Library\Messaging\MessageThreadType::GENERIC;
        }
        $this->view->type = $type;

        $participants = array(
            $thread->getStartingUser(),
            $thread->getFirstReceivingUser()
        );
        reset($participants);
        $firstParticipantKey = key($participants);
        $this->view->participants = $participants;
        $this->view->firstParticipantKey = $firstParticipantKey;

        $referenceItem = $messages[0]->getReferenceItem();
        if ($referenceItem != null) {
            $referenceItem = Event::findFirstBy('itemId', $referenceItem);
            switch ($type) {
                case \Apprecie\Library\Messaging\MessageThreadType::INVITATION:
                    $this->view->guestRecord = false;
                    $guestRecord = GuestList::query()
                        ->where('owningUserId=:1:')
                        ->andWhere('itemId=:2:')
                        ->andWhere('userId=:3:')
                        ->bind(
                            [
                                1 => $thread->getStartedByUser(),
                                2 => $referenceItem->getItemId(),
                                3 => $this->getAuthenticatedUser()->getUserId()
                            ]
                        )
                        ->execute();
                    if ($guestRecord->count() != 0 && $thread->getStartedByUser() !== $this->getAuthenticatedUser(
                        )->getUserId()
                    ) {
                        $this->view->guestRecord = $guestRecord[0];
                    }
                    break;
                default:
            }
        }
        $this->view->referenceItem = $referenceItem;
    }
}