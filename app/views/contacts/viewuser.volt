<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<script src="/js/compiled/public/js/raw/library/contacts.min.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script>
    var portalId = <?= $this->view->user->getPortalId(); ?>;
    var userId = <?= $this->view->user->getUserId(); ?>;

    $(document).ready(function() {
        $('#delete-btn').on('click', deleteModal);
    });

    function deleteModal() {
        var deleteModal = new Modal();
        var buffer = '<p><?= _g("By deleting this User they will be removed completely from your portal, and will no longer be visible on any of the people lists. You will be removing their access to the portal, as well as all their profile data (anonymous data will be kept for reporting purposes)."); ?></p>';
        buffer += '<p><?= _g("Once deleted, a user can only be re-added as a brand new user with no records of their previous activity."); ?></p>';
        buffer += '<p><?= _g("Are you sure you wish to proceed with deleting this user?"); ?></p>';
        deleteModal.confirm(
            '<?= _g('Confirm Deletion of User') ?>',
            buffer,
            'AjaxDeleteContact(<?= $this->view->user->getUserId(); ?>)',
            '#delete-btn',
            'delete-modal',
            'btn-danger'
        );
    }

    function AjaxDeleteContact(userId) {
        var kill = new DeleteContact();
        kill.setUserId(userId);
        $.when(kill.fetch()).then(function(data) {
            var deleteError = $('#delete-error');

            if(data.status === "success"){
                window.location.href = '/contacts';
            } else {
                deleteError.stop().hide().html(data.message).fadeIn('fast');
            }
        })
    }
</script>
<div class="row" style="margin-bottom: 10px;">
    <div class="col-sm-12">
        <h2>
            <?php if ($this->view->userProfile->getFirstname() || $this->view->userProfile->getLastname()): ?>
                <span style="margin-right: 15px;"><?= $this->view->userProfile->getTitle(); ?> <?= $this->view->userProfile->getFirstname(); ?> <?= $this->view->userProfile->getLastname(); ?></span>
            <?php else: ?>
                <span style="margin-right: 15px;"><?= $this->view->portalUser->getReference(); ?></span>
            <?php endif; ?>
            <div class="pull-right"><?= $this->view->user->getActiveRole()->getDescription(); ?></div>
        </h2>
        <a class="btn btn-default" href="/contacts/edit/<?= $this->view->user->getUserId(); ?>"><?= _g('Edit User Profile'); ?></a>
        <?php if ($this->view->thisUser->userIsDescendant($this->view->user)): ?>
            <a class="btn btn-default" id="delete-btn"><?= _g('Delete Account'); ?></a>
        <?php endif; ?>
        <div class="alert alert-danger" role="alert" id="delete-error" style="display: none;"></div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-3">
                <img src="<?= Assets::getUserProfileImage($this->view->user->getUserId()); ?>" class="img-responsive"/>
            </div>
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Contact Details'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <p>
                            <?php if ($this->view->userProfile->getFirstname() || $this->view->userProfile->getLastname()): ?>
                                <b><?= _g('Name'); ?>:&nbsp;</b>
                                <?= $this->view->userProfile->getTitle(); ?> <?= $this->view->userProfile->getFirstname(); ?> <?= $this->view->userProfile->getLastname(); ?>
                                <br>
                            <?php endif; ?>
                            <?php if ($this->view->portalUser->getReference()): ?>
                                <b><?= _g('Reference'); ?>:&nbsp;</b>
                                <?= $this->view->portalUser->getReference(); ?>
                                <br>
                            <?php endif; ?>
                            <b><?= _g('Organisation'); ?>:&nbsp;</b><?= $this->view->user->getOrganisation()->getOrganisationName();?>
                        </p>
                        <p>
                            <?php
                            if ($this->view->address != null) {
                                echo '<b>' . _g('Address') . '</b><br>';
                                if ($this->view->address->getLine1() != null) {echo $this->view->address->getLine1() . ',<br/>';}
                                if ($this->view->address->getLine2() != null) {echo $this->view->address->getLine2() . ',<br/>';}
                                if ($this->view->address->getLine3() != null) {echo $this->view->address->getLine3() . ',<br/>';}
                                if ($this->view->address->getLine4() != null) {echo $this->view->address->getLine4() . ',<br/>';}
                                if ($this->view->address->getLine5() != null) {echo $this->view->address->getLine5() . ',<br/>';}
                                if ($this->view->address->getCity() != null) {echo $this->view->address->getCity() . ',<br/>';}
                                if ($this->view->address->getProvince() != null) {echo $this->view->address->getProvince() . ',<br/>';}
                                if ($this->view->address->getPostalCode() != null) {echo $this->view->address->getPostalCode() . '<br/>';}
                            }
                            ?>
                        </p>
                        <p>
                            <b><?= _g('Tel'); ?>:&nbsp;</b><?php if ($this->view->userProfile->getPhone() != null) {echo $this->view->userProfile->getPhone();} else {echo _g('Not provided');} ?><br/>
                            <b><?= _g('Mobile'); ?>:&nbsp;</b><?php if ($this->view->userProfile->getMobile() != null) {echo $this->view->userProfile->getMobile();} else {echo _g('Not provided');} ?><br/>
                            <b><?= _g('Email'); ?>:&nbsp;</b><?php if ($this->view->userProfile->getEmail() != null) {echo $this->view->userProfile->getEmail();} else {echo _g('Not provided');} ?><br/>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Personal Details'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <p>
                            <strong><?= _g('Birthday'); ?></strong>
                            <br/>
                            <?php if ($this->view->user->getUserProfile()->getBirthday() != null): ?>
                                <?= _fd($this->view->user->getUserProfile()->getBirthday()); ?>
                            <?php else: ?>
                                <?php echo _g('Not provided'); ?>
                            <?php endif; ?>
                        </p>
                        <p>
                            <strong><?= _g('Dietary Requirements'); ?></strong>
                            <br/>
                            <?php foreach ($this->view->user->getUserDietaryRequirement() as $requirement):
                                if (isset($loop)) {echo ', ';}
                                $loop = 1;
                                echo $requirement->getDietaryRequirement()->getRequirement();
                            endforeach; ?>
                            <?php if (!isset($loop)) {echo _g('Not provided');}?>
                        </p>
                        <p>
                            <strong><?= _g('Interests'); ?></strong>
                            <br/>
                            <?php foreach ($this->view->user->getUserInterests() as $interest):
                                if (isset($loop2)) { echo ', ';}
                                $loop2 = 1;
                                echo $interest->getInterest()->getInterest();
                            endforeach; ?>
                            <?php if (!isset($loop2)) {echo _g('Not provided');} ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Suggested Items'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Event</th>
                                <th class="hidden-xs">Start Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(count($this->view->suggestedEvents)!=0): ?>
                                <?php foreach($this->view->suggestedEvents as $item): ?>
                                    <tr>
                                        <td>
                                            <a href="/vault/<?php if($item->getIsByArrangement()==0){echo 'event';}else{echo 'arranged';}?>/<?=$item->getItemId(); ?>">
                                                <?=mb_substr($item->getTitle(),0,79,'UTF-8'); ?>
                                            </a>
                                        </td>
                                        <td><?=_fdt($item->getEvent()->getStartDateTime()); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2">
                                        <?= _g("No items were found that match this person"); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>