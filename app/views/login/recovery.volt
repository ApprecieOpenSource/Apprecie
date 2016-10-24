<div class="row">
    <div class="col-md-12">
        <img src="<?= Assets::getOrganisationBrandLogo(Organisation::getActiveUsersOrganisation()->getOrganisationId()); ?>" class="img-responsive" style="max-height: 150px; padding: 15px; padding-left: 0px;"/>
        <?php if(isset($this->view->error)): ?>
            <div class="alert alert-danger" role="alert" style="margin-top: 15px;">
                <strong><?= _g('Oh no!'); ?></strong> <?=$this->view->error; ?>
            </div>
        <?php endif ?>
        <?php if(!$this->view->reset): ?>
        <form id="login-form" action="/login/recovery" method="post" enctype="multipart/form-data" style="margin-top: 0px;">
            <?php echo Apprecie\Library\Security\CSRFProtection::csrf(); ?>
            <div class="ibox float-e-margins" style="margin-bottom: 5px;">
                <div class="ibox-title">
                    <h5><?= $this->view->portal->getPortalName()?> <?= _g('Account Recovery'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="username" class="control-label"><?= _g('Email Address'); ?></label>
                        <input class="form-control" id="username" name="username" value="">
                    </div>
                </div>
                <div class="panel-footer" style="height:55px; padding-right: 0px;">
                    <button type="submit" class="btn btn-primary pull-right"><?= _g('Send Reset Email'); ?></button>
                </div>
            </div>
        </form>
        <?php else: ?>
            <div class="alert alert-info" role="alert" style="margin-top: 15px;">
                <strong><?= _g('Password Reset'); ?></strong>
                <p><?= _g('To get back into your account, follow the instructions that we\'ve sent to your email address. Didn\'t receive the password reset email? Check your spam folder for an email from us. Should this have happened, please add our noreply@apprecie.com email alias to a Safe Senders list.'); ?></p>
                <p><a href="/login" class="btn btn-primary">Login</a> </p>
            </div>
        <?php endif; ?>
    </div>
</div>