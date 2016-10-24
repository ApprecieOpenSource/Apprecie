<script src="/js/validation/errors.min.js"></script>
<script src="/js/validation/messages.min.js"></script>
<script>
    function sendMessage(){
        clearErrors('#contact-success', '#contact-error');
        validateMessage($('#contact-subject').val(), $('#contact-message').val());
        if (errors.length != 0){
            displayErrors('#contact-error');
        } else {
            $('#contact-send-btn').prop('disabled',true);
            $.when(AjaxSendMessage()).then(function(data){
                $('#contact-send-btn').prop('disabled',false);
                if(data.status=='success'){
                    $('#reply').modal('hide');
                    location.reload();
                }
            });
        }
    }

    function AjaxSendMessage(){
        return $.ajax({
            url: "/api/sendMessage/<?= $this->view->threadId; ?>",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: $('#reply-form').serialize()
        });
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <a href="/alertcentre/" class="btn btn-default" style="margin-top: 15px;"><?= _g('Back to Thread List'); ?></a>
        <h2><?= (new \Apprecie\Library\Messaging\MessageThreadType($this->view->type))->getText(); ?></h2>
        <?php if ($this->view->referenceItem != null): ?>
            <p>
                <span style="font-weight: bold;"><?= _g('Referenced Item:'); ?></span>&nbsp;
                <?= $this->view->referenceItem->getTitle(); ?>
            </p>
        <?php endif; ?>
        <p>
            <span style="font-weight: bold;"><?= _g('In this thread:'); ?></span>&nbsp;
            <?php foreach ($this->view->participants as $key => $participant) {

                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($participant->getPortalId());

                if ($this->view->firstParticipantKey === $key) {
                    echo $participant->getIsDeleted() ? _g('Non active user') : $participant->getUserProfile()->getFullName();
                } else {
                    echo ', ' . ($participant->getIsDeleted() ? _g('Non active user') : $participant->getUserProfile()->getFullName());
                }

                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
            }?>
        </p>
    </div>
</div>
<?php
    if(count($this->view->messages) > 0) {
        $latestMessage = $this->view->messages[0];
        $allowReply = !($latestMessage->getRecipientUser()->getIsDeleted() || $latestMessage->getSendingUser()->getIsDeleted());
    }
?>
<div class="row">
    <div class="col-sm-12">
        <?php if ($this->view->referenceItem != null): ?>
            <?php switch ($this->view->type): ?><?php case \Apprecie\Library\Messaging\MessageThreadType::ARRANGEMENT: ?>
                <?php if ($this->view->referenceItem->getCreatorId() === (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->getUserId()): ?>
                    <a class="btn btn-primary" style="margin: 15px 0;" target="_blank" href="/vault/arrangedp/<?= $this->view->referenceItem->getItemId(); ?>"><?= _g('View Request'); ?></a>
                <?php elseif ($this->view->referenceItem->getIsArrangedFor() === (new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->getUserId()): ?>
                    <a class="btn btn-primary" style="margin: 15px 0;" target="_blank" href="/vault/myarranged/<?= $this->view->referenceItem->getItemId(); ?>"><?= _g('View Request'); ?></a>
                    <?php if ((new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->canViewItem($this->view->referenceItem, null) == true): ?>
                        <a class="btn btn-default" style="margin: 15px 0;" target="_blank" href="/vault/event/<?= $this->view->referenceItem->getItemId(); ?>"><?= _g('View Event'); ?></a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php break; ?>
            <?php case \Apprecie\Library\Messaging\MessageThreadType::INVITATION: ?>
                <?php if ($this->view->guestRecord == true): ?>
                    <a class="btn btn-primary" style="margin: 15px 0;" target="_blank" href="/rsvp/event/<?= $this->view->guestRecord->getInvitationHash(); ?>"><?= _g('Respond to RSVP'); ?></a>
                <?php endif; ?>
                <?php break; ?>
            <?php default: ?>
                <?php if ((new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->canViewItem($this->view->referenceItem, null) == true): ?>
                    <a class="btn btn-default" style="margin: 15px 0;" target="_blank" href="/vault/event/<?= $this->view->referenceItem->getItemId(); ?>"><?= _g('View Event'); ?></a>
                <?php endif; ?>
            <?php endswitch; ?>
        <?php endif; ?>

        <?php if($allowReply) : ?>
            <button style="margin: 15px 0;" class="btn btn-default" data-target="#reply" data-toggle="modal">Reply</button>
        <?php else: ?>
            <button style="margin: 15px 0;" class="btn btn-primary" data-target="#reply" data-toggle="modal" disabled="disabled">Reply</button>
            <p><?= _g('It is not possible to reply to this message'); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php foreach($this->view->messages as $message): ?>
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <?php $sendingUser = $message->getSendingUser(); ?>
            <?php $recipientUser = $message->getRecipientUser(); ?>
            <?php if ($this->view->user->getUserId() === $message->getRecipientUser()->getUserId()): ?>
            <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($sendingUser->getPortalId()); ?>
            <div class="col-sm-2 col-md-1 text-center-not-xs" style="padding: 0 15px 0 0;">
                <ul class="list-unstyled">
                    <div style="max-width: 100px;margin-bottom: 5px;padding: 0 0 0 5px;">
                        <img src="<?= Assets::getUserProfileImage($message->getSendingUser()->getUserId()); ?>" id="picture-img" class="img-responsive"/>
                    </div>
                    <li>
                        <p><strong><?= $sendingUser->getIsDeleted() ? _g('Non active user') : $sendingUser->getUserProfile()->getFullName(); ?></strong></p>
                    </li>
                    <li>
                        <p><?= $sendingUser->getOrganisation()->getOrganisationName(); ?></p>
                    </li>
                </ul>
            </div>
            <div class="col-sm-10 col-md-11" style="padding: 0;">
            <?php elseif ($this->view->user->getUserId() === $sendingUser->getUserId()): ?>
            <div class="col-sm-2 col-sm-push-10 col-md-1 col-md-push-11 text-center-not-xs" style="padding: 0;">
                <ul class="list-unstyled">
                    <div style="max-width: 100px;margin-bottom: 5px;padding: 0 0 0 5px;">
                        <img src="<?= Assets::getUserProfileImage($sendingUser->getUserId()); ?>" id="picture-img" class="img-responsive"/>
                    </div>
                    <li>
                        <p><strong><?= _g('You'); ?></strong></p>
                    </li>
                    <li>
                        <p><?= $sendingUser->getOrganisation()->getOrganisationName(); ?></p>
                    </li>
                </ul>
            </div>
            <div class="col-sm-10 col-sm-pull-2 col-md-11 col-md-pull-1" style="padding: 0 15px 0 0;">
                <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries(); ?>
            <?php endif; ?>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _eh($message->getTitle()); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <p><?= nl2br($message->getBody()); ?></p>
                    </div>
                    <div class="panel-footer text-right" style="height: 38px;font-style: italic;">
                        <small><?= _g('Sent on:') . ' ' . date('d-m-Y H:i:s',strtotime($message->getSent())); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<div class="modal fade" id="reply" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Send Reply</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="contact-error" style="display:none;" role="alert"></div>
                <form class="form-horizontal" id="reply-form" name="contact-form">
                    {{csrf()}}
                    <p>Please enter your message below:</p>
                    <div class="form-group">
                        <label for="contact-subject">Subject</label>
                        <?php
                        $subject = $this->view->messages[0]->getTitle();
                        $subjectPrefix = 'Re: ';
                        if (mb_substr($subject, 0, 4,'UTF-8') !== $subjectPrefix) {
                            $subject = $subjectPrefix . $subject;
                        }
                        ?>
                        <input type="text" class="form-control" id="contact-subject" name="contact-subject" maxlength="100" value="<?= $subject; ?>"/>
                    </div>
                    <div class="form-group">
                        <textarea style="width:100%; height:200px;" id="contact-message" name="contact-message"></textarea>
                    </div>
                    <input type="hidden" id="responseTo" name="responseTo" value="<?= $this->view->messages[0]->getMessageId(); ?>"/>
                    <input type="hidden" id="itemId" name="itemId" value="<?= $this->view->messages[0]->getReferenceItem(); ?>"/>
                    <input type="hidden" id="targetUser" name="targetUser" value="<?= $this->view->targetUser; ?>"/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="contact-send-btn" onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>
</div>