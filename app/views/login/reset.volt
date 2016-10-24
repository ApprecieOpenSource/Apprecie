<script src="/js/validation/errors.js"></script>
<script src="/js/validation/users/recovery.min.js"></script>
<script src="/js/pwstrength-bootstrap-1.2.7.min.js"></script>
<script src="/js/compiled/public/js/raw/library/passwordstrength.min.js"></script>
<script>
    $(document).ready(function(){
        var passwordStrength = new PasswordStrength($("#password"));
    });

    function validateResetForm() {
        clearErrors();
        validatePassword($('#password').val(), $('#confirm-password').val());
        if (errors.length == 0) {
            $('#reset-form').submit();
        } else {
            displayErrors();
        }
    }
</script>
<div class="row">
    <div class="col-md-12">
        <img src="<?= Assets::getOrganisationBrandLogo(Organisation::getActiveUsersOrganisation()->getOrganisationId()); ?>" class="img-responsive" style="max-height: 150px; padding: 15px; padding-left: 0px;"/>
        <div class="alert alert-danger" role="alert" style="margin-top: 15px;<?= $this->view->error ? '' : 'display: none;'; ?>" id="error-box">
            <?= $this->view->error ? $this->view->error : ''; ?>
        </div>
        <?php if(!$this->view->reset && ! isset($this->view->badhash)): ?>
            <div class="ibox float-e-margins" style="margin-bottom: 5px;">
                <div class="ibox-title">
                    <h5><?= $this->view->portal->getPortalName()?> <?= _g('Account Recovery'); ?></h5>
                </div>
                <div class="ibox-content">
                    <?php if($this->view->success!=null): ?>
                        <div class="alert alert-success" role="alert" style="margin-top: 15px;">
                            <strong><?= _g('Password Reset!'); ?></strong> Your password has been reset and you can now login.
                        </div>
                    <?php else: ?>
                    <form id="reset-form" action="/login/reset/<?=$this->view->token; ?>" method="post" enctype="multipart/form-data" style="margin-top: 0px;">
                        <?php echo Apprecie\Library\Security\CSRFProtection::csrf(); ?>
                        <p style="margin-bottom: 30px;">A password with medium strength is required at a minimum. It should consist of 8-25 characters, mixing both letters and numbers. We recommend you to include both upper-case and lower-case letters and special characters to make a very strong password.</p>
                        <div class="form-group">
                            <label for="username" class="control-label"><?= _g('New Password'); ?></label>
                            <input type="password" class="form-control" id="password" name="password" value="">
                        </div>
                        <div class="form-group">
                            <label for="username" class="control-label"><?= _g('Confirm Password'); ?></label>
                            <input type="password" class="form-control" id="confirm-password" name="confirm-password" value="">
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
                <div class="panel-footer" style="height:55px; padding-right: 0px;">
                    <?php if($this->view->success!=null): ?>
                        <a href="/login" class="btn btn-primary pull-right">Login</a>
                    <?php else: ?>
                        <button class="btn btn-primary pull-right" onclick="validateResetForm();"><?= _g('Save'); ?></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif(! isset($this->view->badhash)): ?>
            <div class="alert alert-info" role="alert" style="margin-top: 15px;">
                <strong><?= _g('Password Reset'); ?></strong> Instructions on how to reset your password have been sent to your registered email address.
            </div>
        <?php endif; ?>
    </div>
</div>