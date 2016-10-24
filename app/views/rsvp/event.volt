<script src="/js/compiled/public/js/raw/library/rsvp.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/rsvp/event.min.js"></script>
<?php if($this->view->user->getIsDeleted()): ?>
    <div class="alert alert-danger" role="alert">
        <p><?= _g('You are no longer allowed to update your attendance to this event.'); ?></p>
        <p><?= _g('If you would like to discuss this further please contact the person who sent you the invitation'); ?></p>
    </div>
<?php else: ?>
<?php $address=$this->view->item->getEvent()->getAddress(); ?>
<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->item->getTitle(); ?></h2>
    </div>
</div>
<?php switch($this->view->guestRecord->getStatus()):?>
<?php case 'invited': ?>
<div class="row" style="margin-bottom: 15px;">
    <div class="col-sm-8">
        <?php if ($this->view->canRsvp): ?>
            <div class="alert alert-info" role="alert">
                <p><?= _g('You have reached this page because you have been invited to an event that requires you to confirm or decline your attendance.'); ?></p>
                <p><?= _g('You can confirm or decline until {date}.', array('date' => $this->view->canRsvpUntil)); ?></p>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                <p><?= _g('You can no longer confirm your attendance. The guest list for this event is now closed.'); ?></p>
            </div>
        <?php endif; ?>

        <h3>Summary</h3>
        <p><?= $this->view->item->getSummary(); ?></p>

        <h3>Attendance Terms</h3>
        <?php if($this->view->item->getEvent()->getAttendanceTerms()!=null): ?>
            <?= $this->view->item->getEvent()->getAttendanceTerms(); ?>
        <?php else: ?>
            <p>There are no specific attendance terms for this event.</p>
        <?php endif; ?>

        <?php if ($this->view->guestRecord->getSpaces() > 1): ?>
            <h3>Additional Guests</h3>
            <p>
                <select name="spaces" id="user-spaces">
                    <option value="1">No Guest</option>
                    <?php
                    $spaces = $this->view->guestRecord->getSpaces();
                    $i = 2;
                    ?>
                    <?php while($i < $spaces): ?>
                        <option value="<?= $i; ?>">Plus <?= (string)($i - 1); ?> Guests</option>
                        <?php $i++; ?>
                    <?php endwhile; ?>
                    <option value="<?= $i; ?>" selected>Plus <?= (string)($i - 1); ?> Guests</option>
                </select>
            </p>
        <?php endif; ?>

        <?php if ($this->view->user->getUserLogin() != null && !$this->view->user->getUserLogin()->getPassword() !== 'pending'): ?>
            <p><strong>Please make sure your details such as dietary requirements are correct on your <a href="/profile" target="_blank">profile</a> so that the host can accommodate you correctly.</strong></p>
        <?php endif; ?>
        <p><strong>By accepting the invitation using the button below, you are agreeing to adhere to the Attendance Terms detailed above, and these additional <a href="/legal/rsvp" target="_blank">Terms and Conditions</a>.</strong></p>

        <a href="/rsvp/viewevent/<?= $this->view->hash;?>" target="_blank" class="btn btn-default">View Event Details</a>
        <?php if ($this->view->canRsvp): ?>
            <button id="accept-btn" class="btn btn-success" onclick="RsvpAccept('<?= $this->view->hash; ?>')">Accept</button>
            <button id="decline-btn" class="btn btn-danger" onclick="RsvpDecline('<?= $this->view->hash; ?>')">Decline</button>
        <?php endif; ?>
    </div>
    <div class="col-sm-4">
        <img class="img-responsive" src="<?= Assets::getItemPrimaryImage($this->view->item->getItemId()); ?>"/>
    </div>
</div>
<?php break;?>
<?php case 'confirmed': ?>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-sm-8">
            <div class="alert alert-success" role="alert">
                <p><?= _g('You are currently attending this event and do not need to perform any other actions.'); ?></p>
                <?php if ($this->view->canRsvp): ?>
                    <p><?= _g('If you can no longer attend this event please click the Cancel button below. Note that if you cancel you will not be able to change you attendance without the organiser sending you a new invitation.'); ?></p>
                    <p><?= _g('You can cancel until {date}.', array('date' => $this->view->canRsvpUntil)); ?></p>
                <?php else: ?>
                    <p><?= _g('You can no longer cancel your attendance. The guest list for this event is now closed.'); ?></p>
                <?php endif; ?>
            </div>

            <h3>Summary</h3>
            <p><?= $this->view->item->getSummary(); ?></p>

            <h3>Attendance Terms</h3>
            <?php if($this->view->item->getEvent()->getAttendanceTerms()!=null): ?>
                <?= $this->view->item->getEvent()->getAttendanceTerms(); ?>
            <?php else: ?>
                <p>There are no specific attendance terms for this event.</p>
            <?php endif; ?>

            <?php if ($this->view->guestRecord->getSpaces() > 1): ?>
                <h3>Additional Guests</h3>
                <p><?= _g('You may bring {guest_number} additional guest(s).', array('guest_number' => $this->view->guestRecord->getSpaces() - 1)); ?></p>
            <?php endif; ?>

            <a href="/rsvp/viewevent/<?= $this->view->hash;?>" target="_blank" class="btn btn-default">View Event Details</a>
            <?php if ($this->view->canRsvp): ?>
                <button id="cancel-btn" onclick="RsvpCancel('<?= $this->view->hash; ?>')" class="btn btn-danger">Cancel</button>
            <?php endif; ?>
        </div>
        <div class="col-sm-4">
            <img class="img-responsive" src="<?= Assets::getItemPrimaryImage($this->view->item->getItemId()); ?>"/>
        </div>
    </div>
<?php break;?>
<?php case 'declined':case 'cancelled': ?>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-sm-8">
            <div class="alert alert-info" role="alert">
                You have declined or cancelled this invitation. If you now wish to attend the event you will need to contact the organiser.
            </div>

            <h3>Summary</h3>
            <p><?= $this->view->item->getSummary(); ?></p>

            <h3>Attendance Terms</h3>
            <?php if($this->view->item->getEvent()->getAttendanceTerms()!=null): ?>
                <?= $this->view->item->getEvent()->getAttendanceTerms(); ?>
            <?php else: ?>
                <p>There are no specific attendance terms for this event.</p>
            <?php endif; ?>

            <a href="/rsvp/viewevent/<?= $this->view->hash;?>" target="_blank" class="btn btn-default">View Event Details</a>
        </div>
        <div class="col-sm-4">
            <img class="img-responsive" src="<?= Assets::getItemPrimaryImage($this->view->item->getItemId()); ?>"/>
        </div>
    </div>
    <?php break;?>
<?php case 'revoked': ?>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-sm-12">
            <div class="alert alert-danger" role="alert">Your invitation to this event has been revoked by the organiser.</div>
        </div>
    </div>
<?php break;?>
<?php endswitch; ?>
<?php endif; ?>
