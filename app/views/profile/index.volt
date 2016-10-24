<script src="/js/validation/errors.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/validation/people/profile.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script src="/js/compiled/public/js/raw/library/fileUpload.min.js"></script>
<?php
$address=$this->view->userProfile->getHomeAddress();
?>
<script>
    function validateEmailInUse() {
        clearErrors();
        loader(true);
        $.when(getEmailInUse(<?= $this->view->user->getPortalId(); ?>, $('#emailaddress').val(),<?= $this->view->user->getUserId();?>)).then(function (data) {
            if (data.users != 0) {
                errors.push('This email address is already in use');
                displayErrors();
            }
            else {
                validateProfile();
            }
            loader(false);
        })
    }

    $(document).ready(function () {

        var imageError = $('#picture-error');

        $('#picture').change(function () {

            loader(true);
            imageError.fadeOut();

            var profileImageUpload = new FileUpload();
            profileImageUpload.setFileInput($(this));
            profileImageUpload.validateFile();
            profileImageUpload.validateImageType();

            if (profileImageUpload.errors.length > 0) {
                imageError.html(profileImageUpload.getErrorHTML());
                imageError.fadeIn();
                loader(false);
            } else {
                $('#picture-form').submit();
            }
        });

        $('#picture-iframe').load(function () {

            imageError.fadeOut();
            var result = $.parseJSON($('#picture-iframe').contents().text());

            if (result.status == 'success') {
                d = new Date();
                $('#picture-img').attr('src', result.url + '?' + d.getTime());
                $('#profile-img-modal').modal('toggle');
            } else {
                imageError.html(result.message);
                imageError.fadeIn();
            }

            loader(false);
        });

        <?php if($address != null): ?>
        $('#search-term').val('<?= $address->getPostalCode(); ?>');
        $('#country').val('<?= $address->getCountryIso3(); ?>');
        $('#address-id').val('<?= $address->getId(); ?>');
        $('#selected-address-value').html("<?= $address->getLine1() . ' ' . $address->getLine2() . ' ' . $address->getPostalCode(); ?>");
        $('#selected-address').show();
        <?php endif; ?>

        var picker = new Pikaday(
            {
                field: document.getElementById('dob-formatted'),
                firstDay: 1,
                format: 'DD-MM-YYYY',
                yearRange: [<?= (int)date('Y') - 120; ?>, <?= (int)date('Y') - 18; ?>],
                onSelect: function () {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });
    });
</script>
<style>
.change-img-button-container{
    position: absolute;
    bottom:10px;
    left:15px;
}

.profile-image-container{
    position: relative;
    margin-bottom: 15px;
}

</style>
<div class="row">
    <div class="col-sm-12">
        <h2>
            <span style="margin-right: 15px;"><?= $this->view->userProfile->getTitle(); ?> <?= _eh($this->view->userProfile->getFirstname()); ?> <?= _eh($this->view->userProfile->getLastname()); ?></span>
            <div class="pull-right"><?php foreach($this->view->user->getRoles() as $role): ?> <?= $role->getRole()->getDescription(); ?> &nbsp; <?php endforeach; ?></div>
        </h2>
    </div>
</div>
<form id="user-profile-form" class="form-horizontal">
    {{csrf()}}
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-4">
               <div class="profile-image-container">
                   <img src="<?= Assets::getUserProfileImage($this->view->user->getUserId()); ?>" id="picture-img" class="img-responsive"/>
                   <div class="change-img-button-container"><a class="btn btn-default" id="profile-img-button" data-toggle="modal" data-target="#profile-img-modal">Change Image</a></div>
               </div>
                <?php if($this->view->user->getActiveRole()->getName()=="PortalAdministrator"): ?>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Your Apprecie Account Manager'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <?php
                            $accountManagerId=$this->view->user->getPortal()->getAccountManager();
                            $accountUser=User::findFirstBy('userId',$accountManagerId);
                            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($accountUser->getPortalId());
                                $accountUser->clearStaticCache();
                                $accountManager=$accountUser->getUserProfile();
                            \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
                        ?>
                        <p><strong>Name</strong><br/>
                            <?= _eh($accountManager->getFirstName()).' '. _eh($accountManager->getLastName()); ?>
                        </p>
                        <p><strong>Email Address</strong><br/>
                            <?= $accountManager->getEmail(); ?>
                        </p>
                        <p><strong>Telephone Number</strong><br/>
                            <?= $accountManager->getPhone(); ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-danger" role="alert" style="display: none;" id="error-box"></div>
                        <div class="alert alert-success" role="alert" style="display: none;" id="success-box">Profile updated successfully</div>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Contact Details'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label for="portalname" class="col-sm-3 control-label">*&nbsp;<?= _g('Title'); ?></label>
                            <div class="col-sm-9">
                                <select id="title" name="title" class="form-control" style="max-width: 100%;">
                                    <option value="Mr" <?php if($this->view->user->getUserProfile()->getTitle()=="Mr"){ echo "selected";} ?>><?= _g('Mr'); ?></option>
                                    <option value="Ms" <?php if($this->view->user->getUserProfile()->getTitle()=="Ms"){ echo "selected";} ?>><?= _g('Ms'); ?></option>
                                    <option value="Miss" <?php if($this->view->user->getUserProfile()->getTitle()=="Miss"){ echo "selected";} ?>><?= _g('Miss'); ?></option>
                                    <option value="Mrs" <?php if($this->view->user->getUserProfile()->getTitle()=="Mrs"){ echo "selected";} ?>><?= _g('Mrs'); ?></option>
                                    <option value="Mstr" <?php if($this->view->user->getUserProfile()->getTitle()=="Mstr"){ echo "selected";} ?>><?= _g('Mstr'); ?></option>
                                    <option value="Prof" <?php if($this->view->user->getUserProfile()->getTitle()=="Prof"){ echo "selected";} ?>><?= _g('Prof'); ?></option>
                                    <option value="Dr" <?php if($this->view->user->getUserProfile()->getTitle()=="Dr"){ echo "selected";} ?>><?= _g('Dr'); ?></option>
                                    <option value="Sir" <?php if($this->view->user->getUserProfile()->getTitle()=="Sir"){ echo "selected";} ?>><?= _g('Sir'); ?></option>
                                    <option value="Lord" <?php if($this->view->user->getUserProfile()->getTitle()=="Lord"){ echo "selected";} ?>><?= _g('Lord'); ?></option>
                                    <option value="Lady" <?php if($this->view->user->getUserProfile()->getTitle()=="Lady"){ echo "selected";} ?>><?= _g('Lady'); ?></option>
                                    <option value="Dame" <?php if($this->view->user->getUserProfile()->getTitle()=="Dame"){ echo "selected";} ?>><?= _g('Dame'); ?></option>
                                    <option value="Duke" <?php if($this->view->user->getUserProfile()->getTitle()=="Duke"){ echo "selected";} ?>><?= _g('Duke'); ?></option>
                                    <option value="Earl" <?php if($this->view->user->getUserProfile()->getTitle()=="Earl"){ echo "selected";} ?>><?= _g('Earl'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="firstname" class="col-sm-3 control-label">*&nbsp;<?= _g('First Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="firstname" name="firstname" value="<?=_eh($this->view->user->getUserProfile()->getFirstName()); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-3 control-label">*&nbsp;<?= _g('Last Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="lastname" name="lastname"  value="<?=_eh($this->view->user->getUserProfile()->getLastName()); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="emailaddress" class="col-sm-3 control-label"><?= _g('Telephone Number'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="phone" name="phone" value="<?=_eh($this->view->user->getUserProfile()->getPhone()); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="emailaddress" class="col-sm-3 control-label"><?= _g('Mobile Number'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="mobile" name="mobile" value="<?=_eh($this->view->user->getUserProfile()->getMobile()); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="emailaddress" class="col-sm-3 control-label">*&nbsp;<?= _g('Email Address'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="emailaddress" name="emailaddress" value="<?=_eh($this->view->user->getUserProfile()->getEmail()); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="dob" class="col-sm-3 control-label"><?= _g('Date of birth'); ?></label>
                            <div class="col-sm-3">
                                <input type="text"  id="dob-formatted" name="dob-formatted" class="form-control" placeholder="dd-mm-yyyy" maxlength="10" value="<?= _fd($this->view->user->getUserProfile()->getBirthday()); ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">*&nbsp;<?= _g('Gender'); ?></label>
                            <div class="checkbox col-sm-9">
                                <label class="radio-inline">
                                    <input type="radio" name="gender" id="gender-male" <?php if($this->view->user->getUserProfile()->getGender()=="male"){ echo "checked";} ?> value="male"> <?= _g('Male'); ?>
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="gender" id="gender-female" <?php if($this->view->user->getUserProfile()->getGender()=="female"){ echo "checked";} ?> value="female"> <?= _g('Female'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <?= (new AddressFinderWidget('index', array('showFieldMarkings' => false)))->getContent(); ?>
                        </div>

                        <?php
                            if($this->view->address!=null){
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
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Interests'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <?=(new CategoryPickerWidget('user',array('userId'=>$this->view->user->getUserId())))->getContent() ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-8 col-sm-offset-4">

            </div>
            <div class="col-sm-4 col-sm-offset-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Communication Preferences'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="communication[]" <?php if($this->view->user->getUserContactPreferences()->getAlertsAndNotifications()==1){echo 'checked';} ?> value="alerts" id="enabled" name="enabled"> <?= _g('Alerts & notifications for items relevant to me'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="communication[]" <?php if($this->view->user->getUserContactPreferences()->getInvitations()==1){echo 'checked';} ?> value="invitations" id="enabled" name="enabled"> <?= _g('Can receive Invitations to Items'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="communication[]" <?php if($this->view->user->getUserContactPreferences()->getSuggestions()==1){echo 'checked';} ?> value="suggestions" id="enabled" name="enabled"> <?= _g("Can receive Suggestions for Items"); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="communication[]" <?php if($this->view->user->getUserContactPreferences()->getPartnerCommunications()==1){echo 'checked';} ?> value="partners" id="enabled" name="enabled"> <?= _g('Apprecie Partner Communications'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="communication[]" <?php if($this->view->user->getUserContactPreferences()->getUpdatesAndNewsletters()==1){echo 'checked';} ?> value="news" id="enabled" name="enabled"> <?= _g('Apprecie Updates and Newsletters'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Dietary Requirements'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('Halal')){echo 'checked';} ?> value="halal">Halal</label>
                                    </div>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('Kosher')){echo 'checked';} ?> value="Kosher">Kosher</label>
                                    </div>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('No Alcohol')){echo 'checked';} ?> value="No Alcohol">No Alcohol</label>
                                    </div>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('Nut Allergies')){echo 'checked';} ?> value="Nut Allergies">Nut Allergies</label>
                                    </div>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('No Seafood')){echo 'checked';} ?> value="No Seafood">No Seafood</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('Vegetarian')){echo 'checked';} ?> value="Vegetarian">Vegetarian</label>
                                    </div>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('Vegan')){echo 'checked';} ?> value="Vegan">Vegan</label>
                                    </div>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('No Gluten')){echo 'checked';} ?> value="No Gluten">No Gluten</label>
                                    </div>
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="diet[]" <?php if($this->view->user->hasDietaryRequirement('No Dairy or Lactose')){echo 'checked';} ?> value="No Dairy or Lactose">No Dairy or Lactose</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <a class="btn btn-primary pull-right" id="save-profile-btn" style="margin-bottom: 15px;" onclick="validateEmailInUse()">Save Changes</a> <a href="/login/recovery" class="btn btn-default pull-right" style="margin-bottom: 15px; margin-right:15px;">Change Password</a>
            </div>
        </div>
    </div>
</div>
</form>
<div class="modal fade" id="profile-img-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Profile Picture</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="picture-error" style="display: none;" role="alert"></div>
                <p>Must be in JPEG format, 390 px x 390 px or greater in dimension, and with a maximum size of 3 MB</p>
                <p><strong>Try to pick a square image as your image will be resized to fit</strong></p>
                <iframe id="picture-iframe" name="picture-iframe" style="width: 100%; height: 2px; display: none;"></iframe>
                <form method="post" enctype="multipart/form-data" action="/profile/picture" id="picture-form" name="picture-form" target="picture-iframe">
                    <input type="file" id="picture" name="picture"/>
                </form>
            </div>
        </div>
    </div>
</div>