<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/adminusers/viewuser.min.js"></script>
<script>
    var portalId = <?= $this->view->user->getPortalId(); ?>;
    var userId = <?= $this->view->user->getUserId(); ?>;
    var hasEmail = <?= ($this->view->user->getUserProfile()->getEmail()) ? 'true' : 'false'; ?>;
</script>
<style>
    #item-search, #relationship-search{
        display: none;
        padding-bottom: 15px;
        border-bottom: 2px solid #ddd;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2>
            <?php if ($this->view->userProfile->getFirstname() || $this->view->userProfile->getLastname()): ?>
                <span style="margin-right: 15px;"><?= $this->view->userProfile->getTitle(); ?> <?= $this->view->userProfile->getFirstname(); ?> <?= $this->view->userProfile->getLastname(); ?></span>
            <?php else: ?>
                <span style="margin-right: 15px;"><?= $this->view->portalUser->getReference(); ?></span>
            <?php endif; ?>
            <?php if ($this->view->user->getRoles()[0]->getRole()->getDescription() === \Apprecie\Library\Users\UserRole::CLIENT): ?>
                <span style="display: inline-block">
                    <i class="fa fa-briefcase" style="color: gold;"></i>
                    <?php for($i=1;$i<4;$i++): ?>
                        <?php if ($i <= $this->view->user->getTier()): ?>
                            <i class="fa fa-trophy" style="color: gold"></i>
                        <?php else: ?>
                            <i class="fa fa-trophy"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </span>
            <?php endif; ?>
            <div class="pull-right"><?= $this->view->user->getRoles()[0]->getRole()->getDescription(); ?></div>
        </h2>
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
                            <b><?= _g('Organisation'); ?>:&nbsp;</b>
                            <a href="/adminorgs/view/<?= $this->view->user->getOrganisation()->getOrganisationId();?>"><?= $this->view->user->getOrganisation()->getOrganisationName();?></a>
                        </p>
                        <p>
                            <?php
                            if ($this->view->address != null){
                                echo '<b>' . _g('Address') . '</b><br>';
                                if($this->view->address->getLine1()!=null){echo $this->view->address->getLine1().',<br/>';}
                                if($this->view->address->getLine2()!=null){echo $this->view->address->getLine2().',<br/>';}
                                if($this->view->address->getLine3()!=null){echo $this->view->address->getLine3().',<br/>';}
                                if($this->view->address->getLine4()!=null){echo $this->view->address->getLine4().',<br/>';}
                                if($this->view->address->getLine5()!=null){echo $this->view->address->getLine5().',<br/>';}
                                if($this->view->address->getCity()!=null){echo $this->view->address->getCity().',<br/>';}
                                if($this->view->address->getProvince()!=null){echo $this->view->address->getProvince().',<br/>';}
                                if($this->view->address->getPostalCode()!=null){echo $this->view->address->getPostalCode().'<br/>';}
                            }
                            ?>
                        </p>
                        <p>
                            <b><?= _g('Tel'); ?>:&nbsp;</b><?php if($this->view->userProfile->getPhone()!=null){echo $this->view->userProfile->getPhone();}else{echo _g('Not provided');}; ?><br/>
                            <b><?= _g('Mobile'); ?>:&nbsp;</b><?php if($this->view->userProfile->getMobile()!=null){echo $this->view->userProfile->getMobile();}else{echo _g('Not provided');}; ?><br/>
                            <b><?= _g('Email'); ?>:&nbsp;</b><?php if($this->view->userProfile->getEmail()!=null){echo $this->view->userProfile->getEmail();}else{echo _g('Not provided');}; ?>
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
                        <p><strong><?= _g('Birthday'); ?></strong><br/>
                            <?php if($this->view->user->getUserProfile()->getBirthday()!=null): ?>
                                <?= _fd($this->view->user->getUserProfile()->getBirthday()); ?>
                            <?php else: ?>
                                <?php echo _g('Not provided'); ?>
                            <?php endif; ?>
                        </p>
                        <p><strong><?= _g('Dietary Requirements'); ?></strong><br/>
                            <?php foreach($this->view->user->getUserDietaryRequirement() as $requirement):
                                if(isset($loop)){ echo ', ';}
                                $loop=1;
                                echo $requirement->getDietaryRequirement()->getRequirement();
                            endforeach; ?>
                            <?php if(!isset($loop)){echo _g('Not provided');}?>
                        </p>
                        <p><strong><?= _g('Interests'); ?></strong><br/>
                            <?php foreach($this->view->user->getUserInterests() as $interest):
                                if(isset($loop2)){ echo ', ';}
                                $loop2=1;
                                echo $interest->getInterest()->getInterest();
                            endforeach; ?>
                            <?php if(!isset($loop2)){echo _g('Not provided');}?>
                        </p>
                        <p><strong><?= _g('Communication Preferences'); ?></strong><br/>
                            <?php $contactPreferences=$user->getUserContactPreferences(); ?>

                            <?php if($contactPreferences->getAlertsAndNotifications()): ?>
                                <i class="fa fa-check"></i>
                            <?php else: ?>
                                <i class="fa fa-close"></i>
                            <?php endif; ?>
                            <?php echo _g('Alerts & notifications for items that are relevant to me');?>
                            <br/>
                            <?php if($contactPreferences->getInvitations()): ?>
                                <i class="fa fa-check"></i>
                            <?php else: ?>
                                <i class="fa fa-close"></i>
                            <?php endif; ?>
                            <?php echo _g('Can receive Invitations to Items');?>
                            <br/>
                            <?php if($contactPreferences->getSuggestions()): ?>
                                <i class="fa fa-check"></i>
                            <?php else: ?>
                                <i class="fa fa-close"></i>
                            <?php endif; ?>
                            <?php echo _g('Can receive Suggestion\'s for Items');?>
                            <br/>
                            <?php if($contactPreferences->getPartnerCommunications()): ?>
                                <i class="fa fa-check"></i>
                            <?php else: ?>
                                <i class="fa fa-close"></i>
                            <?php endif; ?>
                            <?php echo _g('Apprecie Partner Communications');?>
                            <br/>
                            <?php if($contactPreferences->getUpdatesAndNewsletters()): ?>
                                <i class="fa fa-check"></i>
                            <?php else: ?>
                                <i class="fa fa-close"></i>
                            <?php endif; ?>
                            <?php echo _g('Apprecie Updates and Newsletters');?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($this->view->user->getPortalId()); ?>
                <?php if($this->view->showPortalAccessInfo):?>
                    <div class="alert alert-info" role="alert">
                        <p><strong><?= _g('Portal Access'); ?></strong></p>
                        <p>
                            <?= _g('This person does not have access to the Portal without going through the sign-up process. By granting portal access, a Sign-up URL will be generated and a client quota will be consumed if applicable. You can then pass the Sign-up URL to the user yourself or request a system email containing the Sign-up URL sent to the user.'); ?>
                        </p>

                        <div class="btn-group" style="padding-top: 15px;">
                            <button class="btn btn-default" id="generate-link" type="button" style="<?= ($this->view->user->getPortalUser()->getRegistrationHash()) ? 'display: none;' : ''; ?>">
                                <?= _g('Grant Portal Access'); ?>
                            </button>
                            <button class="btn btn-danger" id="remove-link" type="button" style="<?= (!$this->view->user->getPortalUser()->getRegistrationHash()) ? 'display: none;' : ''; ?>">
                                <?= _g('Remove Pending Portal Access'); ?>
                            </button>
                            <button class="btn btn-default" id="send-registration" type="button" style="<?= (!$this->view->user->getUserProfile()->getEmail() || !$this->view->user->getPortalUser()->getRegistrationHash()) ? 'display: none;' : ''; ?>" data-toggle="modal" data-target="#sendEmailModal">
                                <?= _g('Send Sign-up Email'); ?>
                            </button>
                            <span style="display: inline-block;padding: 7px 12px;" class="text-success" id="portal-access-success"></span>
                            <span style="display: inline-block;padding: 7px 12px;" class="text-danger" id="portal-access-error"></span>
                        </div>

                        <div class="input-group" style="padding-top: 15px;<?= (!$this->view->user->getPortalUser()->getRegistrationHash()) ? 'display: none;' : ''; ?>" id="registration-link-container">
                            <span class="input-group-addon"><?= _g('Sign-up URL'); ?></span>
                            <input type="text" class="form-control" id="registration-link" name="registration-link"
                                   value="<?= ($this->view->user->getPortalUser()->getRegistrationHash()) ? \Apprecie\Library\Request\Url::getConfiguredPortalAddress($this->view->user->getPortal(), 'signup') . '/' . $this->view->user->getPortalUser()->getRegistrationHash() : ''; ?>">
                        </div>
                    </div>
                <?php endif; ?>
                <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries(); ?>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Relationships'); ?></h5>
                    <span class="pull-right"><a onclick="RelationshipSearchShow()" style="cursor: pointer">Search ></a></span>
                </div>
                <div class="ibox-content">
                    <div id="relationship-search">
                        <form method="post" enctype="multipart/form-data" action="#" class="form-horizontal">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="date-range" class="control-label">Creation Date</label>
                                    <input type="text" class="form-control" id="date-range" name="date-range">
                                </div>
                                <div class="col-sm-4">
                                    <label for="name" class="control-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                                <div class="col-sm-4">
                                    <label for="relationship" class="control-label">Relationship</label>
                                    <select class="form-control" id="relationship" name="relationship">
                                        <option>Any</option>
                                        <option>Family Member</option>
                                        <option>Contact</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" style="margin-top: 25px;">Search</button>
                        </form>
                    </div>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Relationship</th>
                            <th class="hidden-xs">Creation Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="3">No relationships available</td>
                        </tr>

                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination pagination-sm" style="margin: 0px;">
                            <li><a href="#">«</a></li>
                            <li><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">»</a></li>
                        </ul>
                        <span class="pull-right">
                            <a style="cursor: pointer"><i class="fa fa-file-excel-o"></i> Export</a>
                        </span>
                    </nav>
                </div>
            </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">`
                        <h5><?= _g('Item History'); ?></h5>
                        <span class="pull-right"><a onclick="ItemSearchShow()" style="cursor: pointer">Search ></a></span>
                    </div>
                    <div class="ibox-content">
                        <div id="item-search">
                            <form method="post" enctype="multipart/form-data" action="#" class="form-horizontal">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label for="date-range" class="control-label">Date Range</label>
                                        <input type="text" class="form-control" id="date-range" name="date-range">
                                    </div>
                                    <div class="col-sm-8">
                                        <label for="item-name" class="control-label">Item Name</label>
                                        <input type="text" class="form-control" id="item-name" name="item-name">
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="supplier" class="control-label">Supplier</label>
                                        <select class="form-control" id="supplier" name="supplier">
                                            <option>Any</option>
                                            <option>Bentley</option>
                                            <option>Blue Duck Gallery</option>
                                            <option>Jaguar</option>
                                            <option>Royal Albert Hall</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="status" class="control-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option>Any</option>
                                            <option>Invited</option>
                                            <option>Attending</option>
                                            <option>Attended</option>
                                            <option>Declined</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="type" class="control-label">Type</label>
                                        <select class="form-control" id="type" name="type">
                                            <option>Any</option>
                                            <option>Event</option>
                                            <option>Offer</option>
                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-primary" style="margin-top: 25px;">Search</button>
                            </form>
                        </div>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th class="hidden-xs">Type</th>
                                <th class="hidden-xs">Supplier</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="5">No item history available</td>
                            </tr>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination pagination-sm" style="margin: 0px;">
                                <li><a href="#">«</a></li>
                                <li><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">»</a></li>
                            </ul>
                            <span class="pull-right">
                                <a style="cursor: pointer"><i class="fa fa-file-excel-o"></i> Export</a>
                            </span>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Ownership'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <p><strong>Portal & Organisation</strong><br/><a href="/portals/profile/<?= $this->view->user->getPortalId();?>">
                            <?php $portal=Portal::findFirstBy('portalId',$this->view->user->getPortalId());
                                echo $portal->getPortalName();
                            ?></a> - <a href="/adminorgs/view/<?= $this->view->user->getOrganisation()->getOrganisationId();?>"><?= $this->view->user->getOrganisation()->getOrganisationName();?></a>
                        </p>
                        <p><strong>Creator</strong><br/>
                            <?php
                                $creator=User::findFirstBy('userId',$this->view->user->getCreatingUser());
                                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($creator->getPortalId());
                                echo '<a href="/adminusers/viewuser/'.$creator->getUserId().'">';
                                echo $creator->getUserProfile()->getFirstname().' '.$creator->getUserProfile()->getLastname();
                                echo '</a>';
                            ?>
                         (<?= $creator->getRoles()[0]->getRole()->getDescription(); ?>)</p>
                        <?php
                            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
                        ?>
                        <p><strong>Owner</strong><br/>
                            <?php
                            $parents=$this->view->user->getParents();
                            foreach($parents as $parent){
                                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($parent->getPortalId());
                                echo '<a href="/adminusers/viewuser/'.$parent->getUserId().'">';
                                echo $parent->getUserProfile()->getFirstName().' '.$parent->getUserProfile()->getLastName();
                                echo '</a>';
                                echo ' ('.$parent->getRoles()[0]->getRole()->getDescription().')';
                                \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
                            }
                            ?>
                        </p>
                        <p><strong>Creation Date</strong><br/> 21/07/2014</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if ($this->view->showPortalAccessInfo) {
    echo (new EmailWidget(
        'index',
        array(
            'templateType' => $this->view->emailTemplateType,
            'callback' => 'sendSignUp',
            'previewData' => array(
                'portalId' => null,
                'user' => $this->view->user->getUserId(),
                'emailType' => 'signup'
            )
        )
    ))->getContent();
}
?>