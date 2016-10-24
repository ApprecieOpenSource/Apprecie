<script src="/js/compiled/public/js/raw/controllers/login/index.min.js"></script>
<script src="/js/compiled/public/js/raw/library/utils.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="row">
    <div class="col-md-12">
        <img src="<?= Assets::getOrganisationBrandLogo(Organisation::getActiveUsersOrganisation()->getOrganisationId()); ?>" class="img-responsive" style="padding: 15px; padding-left: 0px; max-height:150px;"/>
        <?php if(isset($this->view->error)): ?>
            <div class="alert alert-danger" role="alert" style="margin-top: 15px;">
                <strong><?= _g('Oh no!'); ?></strong> <?=$this->view->error; ?>
            </div>
        <?php endif ?>
        <form id="login-form" action="/login" method="post" enctype="multipart/form-data" style="margin-top: 0px;">
            <?php echo Apprecie\Library\Security\CSRFProtection::csrf(); ?>
            <div class="ibox float-e-margins" style="margin-bottom: 5px;">
                <div class="ibox-title">
                    <h5><?= $this->view->portal->getPortalName()?> <?= _g('Login'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="username" class="control-label"><?= _g('Email'); ?></label>
                        <input class="form-control" id="username" name="username" value="<?php if(isset($_COOKIE['apprecie_user'])){echo $_COOKIE['apprecie_user'];} ?>">
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label"><?= _g('Password'); ?></label>
                        <input type="password" autocomplete="off" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="remember-me" value="1" <?php if(isset($_COOKIE['apprecie_user'])){echo 'checked';} ?> name="remember-me">
                        <label for="remember-me" class="form-label"><?= _g('Remember my username'); ?></label>
                    </div>
                    <?php if ($this->session->get('useCaptcha') === true): ?>
                        <div class="g-recaptcha" data-sitekey="6LdF4QwTAAAAAPrgZFV7ipurJXXzoa3hbl5jA6-M"></div>
                    <?php endif; ?>
                </div>
                <div class="panel-footer" style="padding-right: 0px; padding-left: 0px; padding-bottom:0px;">
                    <a href="/login/recovery" class="btn btn-default">Recover Account</a> <button type="submit" class="btn btn-primary pull-right"><?= _g('Login'); ?></button>
                    <p style="margin-top: 15px;"><a href="/legal/public" target="_blank">Terms and Conditions</a>  <a class="pull-right" href="/legal/privacy" target="_blank">Privacy Policy</a></p>
                </div>
            </div>
        </form>
    </div>
</div>


