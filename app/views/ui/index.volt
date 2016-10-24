<script src="/js/compiled/public/js/raw/library/fileUpload.min.js"></script>
<script>
    $(document).ready(function () {

        $('.picker').colpick({
            layout: 'hex',
            submit: 0,
            onChange: function (hsb, hex, rgb, el, bySetColor) {
                var preview = $('#' + $(el).attr('id') + '-preview');
                preview.css('background-color', '#' + hex);
                $(el).val('#' + hex);
                if (!bySetColor) $(el).val('#' + hex);
            }
        }).keyup(function () {
            $(this).colpickSetColor(this.value);
        });

        var brandingError = $('#branding-error');

        $('#logo').change(function () {

            loader(true);
            brandingError.fadeOut();

            var profileImageUpload = new FileUpload();
            profileImageUpload.setFileInput($(this));
            profileImageUpload.validateFile();
            profileImageUpload.validateImageType();

            if (profileImageUpload.errors.length > 0) {
                brandingError.html(profileImageUpload.getErrorHTML());
                brandingError.fadeIn();
                loader(false);
            } else {
                $('#branding-form').submit();
            }
        });

        $('#logo-iframe').load(function () {

            brandingError.fadeOut();

            if ($('#logo-iframe').contents().text() != '') {
                var result = $.parseJSON($('#logo-iframe').contents().text());
                if (result.status == 'success') {
                    d = new Date();
                    $('#logo-img').attr('src', result.url + '?' + d.getTime());
                    $('#main-logo').attr('src', result.url + '?' + d.getTime());
                }
                else {
                    brandingError.html(result.message);
                    brandingError.fadeIn();
                }
            }

            loader(false);
        });

        var bgError = $('#background-error');

        $('#background').change(function () {

            loader(true);
            bgError.fadeOut();

            var profileImageUpload = new FileUpload();
            profileImageUpload.setFileInput($(this));
            profileImageUpload.validateFile();
            profileImageUpload.validateImageType();

            if (profileImageUpload.errors.length > 0) {
                bgError.html(profileImageUpload.getErrorHTML());
                bgError.fadeIn();
                loader(false);
            } else {
                $('#background-form').submit();
            }
        });

        $('#background-iframe').load(function () {

            bgError.fadeOut();
            if ($('#background-iframe').contents().text() != '') {
                var result = $.parseJSON($('#background-iframe').contents().text());
                if (result.status == 'success') {
                    d = new Date();
                    $('#background-img').attr('src', result.url + '?' + d.getTime());
                }
                else {
                    bgError.html(result.message);
                    bgError.fadeIn();
                }
            }

            loader(false);
        });

        var bannerError = $('#vault-error');

        $('#vault').change(function () {

            loader(true);
            bannerError.fadeOut();

            var profileImageUpload = new FileUpload();
            profileImageUpload.setFileInput($(this));
            profileImageUpload.validateFile();
            profileImageUpload.validateImageType();

            if (profileImageUpload.errors.length > 0) {
                bannerError.html(profileImageUpload.getErrorHTML());
                bannerError.fadeIn();
                loader(false);
            } else {
                $('#vault-form').submit();
            }
        });

        $('#vault-iframe').load(function () {

            bannerError.fadeOut();

            if ($('#vault-iframe').contents().text() != '') {
                var result = $.parseJSON($('#vault-iframe').contents().text());
                if (result.status == 'success') {
                    d = new Date();
                    $('#vault-img').attr('src', result.url + '?' + d.getTime());
                }
                else {
                    bannerError.html(result.message);
                    bannerError.fadeIn();
                }
            }

            loader(false);
        });
    });

    function setColor() {

    }
</script>
<style>
    .picker {
        border:0px;
        width:100px;
    }
    .colpick{
        z-index: 100;
    }
    .color-preview{
        height:25px; width:25px;
    }
</style>
<h2>Look and Feel</h2>
<div class="row wrapper-content">
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <form class="form-horizontal" action="/ui" method="post" enctype="multipart/form-data">
                {{csrf()}}
                <div class="ibox-title">
                    <h5>Navigation</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Menu Background</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="menu-background" name="menu-background" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getNavigationPrimary();}; ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getNavigationPrimary(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getNavigationPrimary(); ?> " id="menu-background-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Menu Active Background</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="menu-active-background" name="menu-active-background" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getNavigationSecondary();} ?>"/>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getNavigationSecondary(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getNavigationSecondary(); ?> " id="menu-active-background-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Menu Link Colour</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="menu-link-color" name="menu-link-color" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getNavigationPrimaryA();} ?>"/>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getNavigationPrimaryA(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getNavigationPrimaryA(); ?> " id="menu-link-color-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Menu Active Link Colour</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="menu-active-link-color" name="menu-active-link-color" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getNavigationSecondaryA();} ?>"/>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getNavigationSecondaryA(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getNavigationSecondaryA(); ?> " id="menu-active-link-color-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-title">
                    <h5>Fonts and Links</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Font</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <select class="form-control" id="font" name="font">
                                    <option>Default</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Font Colour</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="font-color" name="font-color" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getFontColor();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getFontColor(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getFontColor(); ?> " id="font-color-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Link</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="link" name="link" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getA();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getA(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getA(); ?> " id="link-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Link Hover</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="link-hover" name="link-hover" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getAhover();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getAhover(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getAhover(); ?> " id="link-hover-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-title">
                    <h5>Buttons</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Primary Background Colour</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="primary-button-background" name="primary-button-background" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getButtonPrimary();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getButtonPrimary(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getButtonPrimary(); ?> " id="primary-button-background-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Primary Border Colour</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="primary-button-border-colour" name="primary-button-border-colour" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getButtonPrimaryBorder();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getButtonPrimaryBorder(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getButtonPrimaryBorder(); ?> " id="primary-button-border-colour-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Primary Hover Background Colour</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="primary-button-hover-colour" name="primary-button-hover-colour" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getButtonPrimaryHover();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getButtonPrimaryHover(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getButtonPrimaryHover(); ?> " id="primary-button-hover-colour-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Primary Hover Border Colour</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="primary-button-hover-border" name="primary-button-hover-border" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getButtonPrimaryHoverBorder();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getButtonPrimaryHoverBorder(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getButtonPrimaryHoverBorder(); ?> " id="primary-button-hover-border-preview"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Primary Text Colour</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="primary-button-colour" name="primary-button-colour" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getButtonPrimaryColor();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getButtonPrimaryColor(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getButtonPrimaryColor(); ?> " id="primary-button-colour-preview"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-title">
                    <h5>Miscellaneous</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="title" class="col-sm-6 control-label">Progress Bar Background</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" id="progress-bar" name="progress-bar" class="form-control picker" value="<?php if($this->view->styles!=null){echo $this->view->styles->getProgressBar();} ?>"/>
                                <div class="input-group-addon color-preview" style="background-color:<?= $this->view->styles->getProgressBar(); ?> "></div>
                                <div class="input-group-addon" style="background-color:<?= $this->view->styles->getProgressBar(); ?> " id="progress-bar-preview"></div>
                            </div>
                        </div>
                    </div>
                    <input type="submit" value="Save" class="btn btn-primary"/>
                </div>
            </form>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Logo</h5>
            </div>
            <div class="ibox-content">
                <p>Must be JPG 150x70 or greater</p>
                <div class="alert alert-danger" id="branding-error" style="display: none;" role="alert"></div>
                <form method="post" enctype="multipart/form-data" action="/ui/branding" id="branding-form" name="branding-form" target="logo-iframe">
                    {{csrf()}}
                    <img src="<?= Assets::getOrganisationBrandLogo(Organisation::getActiveUsersOrganisation()->getOrganisationId()); ?>" style=" margin-bottom: 15px; margin-top: 15px;" id="logo-img" class="img-responsive">
                    <input type="file" id="logo" name="logo"/>
                    <iframe id="logo-iframe" name="logo-iframe" style="width: 100%; height: 1px; display: none;"></iframe>
                </form>
            </div>
            <div class="ibox-title">
                <h5>Background</h5>
            </div>
            <div class="ibox-content">
                <p>Must be JPG 1920x1080 or greater</p>
                <div class="alert alert-danger" id="background-error" style="display: none;" role="alert"></div>
                <form method="post" enctype="multipart/form-data" action="/ui/background" id="background-form" name="background-form" target="background-iframe">
                    {{csrf()}}
                    <img src="<?= Assets::getOrganisationBackground(Organisation::getActiveUsersOrganisation()->getOrganisationId()); ?>" style=" margin-bottom: 15px; margin-top: 15px;" id="background-img" class="img-responsive">
                    <input type="file" id="background" name="background"/>
                    <iframe id="background-iframe" name="background-iframe" style="width: 100%; height: 1px; display: none;"></iframe>
                </form>
            </div>
            <div class="ibox-title">
                <h5>Vault Banner</h5>
            </div>
            <div class="ibox-content">
                <p>Must be JPG 1140x312 or greater</p>
                <div class="alert alert-danger" id="vault-error" style="display: none;" role="alert"></div>
                <form method="post" enctype="multipart/form-data" action="/ui/vault" id="vault-form" name="vault-form" target="vault-iframe">
                    {{csrf()}}
                    <img src="<?= Assets::getOrganisationVaultBackground(Organisation::getActiveUsersOrganisation()->getOrganisationId()); ?>" style=" margin-bottom: 15px; margin-top: 15px;" id="vault-img" class="img-responsive">
                    <input type="file" id="vault" name="vault"/>
                    <iframe id="vault-iframe" name="vault-iframe" style="width: 100%; height: 1px; display: none;"></iframe>
                </form>
            </div>
        </div>
    </div>
</div>