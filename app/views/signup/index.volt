<?php
$address=Address::findFirstBy('addressId',$this->view->userObj->getUserProfile()->getHomeAddressId());
?>
<script src="/js/validation/users/signup.js"></script>
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script src="/js/pwstrength-bootstrap-1.2.7.min.js"></script>
<script src="/js/compiled/public/js/raw/library/passwordstrength.min.js"></script>
<script>
    $(document).ready(function(){
        setSteps(7);
        setStep(1);

        <?php if($address!=null): ?>
        $('#search-term').val(<?= _j($address->getPostalCode()); ?>);
        $('#country').val(<?= _j($address->getCountryIso3()); ?>);
        $('#address-id').val(<?= _j($address->getId()); ?>);
        $('#selected-address-value').html(<?= _j($address->getLine1() .' ' . $address->getLine2() .' '. $address->getPostalCode(), true); ?>);
        $('#selected-address').show();
        <?php endif; ?>

        $('.invited-by').click(function(){
            $('#invited-by').toggle('fast');
        })

        var picker = new Pikaday(
            {
                field: document.getElementById('dob-formatted'),
                firstDay: 1,
                format: 'DD-MM-YYYY',
                yearRange: [<?= (int)date('Y') - 120; ?>, <?= (int)date('Y') - 18; ?>],
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            }
        );

        var passwordStrength = new PasswordStrength($("#password"));
    });

    var steps=0;
    var stepper=0;

    function preValidateStepOne() {
        clearErrors();
        loader(true);
        $.when(verifyUser()).then(function(data) {
            if (data.result === 'success') {
                $('#emailaddress').val(data.email);
                if (data.signUpTerms === null) {
                    $('#terms-list').append(
                        '<li><a target="_blank" href="/legal/public">Terms and Conditions</li><li><a target="_blank" href="/legal/privacy">Privacy Policy</li>'
                    );
                } else {
                    $.each(data.signUpTerms, function(i, terms) {
                        $('#terms-list').append('<li><a target="_blank" href="/legal/view/' + terms.termsId + '">' + terms.title + '</li>');
                    });
                    $('#terms-list').append('<li><a target="_blank" href="/legal/privacy">Privacy Policy</li>');
                }
                validateStep(1);
            } else {
                errors.push('You must provide the email address associated with this sign-up');
                displayErrors();
            }
        });
    }

    function preValidateStepTwo() {
        clearErrors();
        loader(true);
        $.when(getEmailInUse(<?= $this->view->userObj->getPortalId(); ?>, $('#emailaddress').val(), <?= $this->view->userObj->getUserId();?>)).then(function(data) {
            if (data.users!=0) {
                errors.push('This email address is already in use');
                displayErrors();
            } else {
                validateStep(2);
            }
            loader(false);
        });
    }

    function redirect(){
        window.location='/login';
    }
</script>
<style>
    #error-box{
        display:none;
    }
</style>
<div class="pull-left">
    <img id="main-logo" src="<?= Assets::getOrganisationBrandLogo($this->view->organisation); ?>" class="img-responsive brand-img"/>
</div>
<h1 style="clear:both;">Welcome to <?= $this->view->portal->getPortalName(); ?></h1>
<div class="media" style="border-bottom: none; margin-bottom: 15px;">
    <a class="media-left">
        <img src="<?= Assets::getUserProfileImage($this->view->creatorUser->getUserId()); ?>" style="width:100px; border-radius: 50%"/>
    </a>
    <div class="media-body">
        <p>You have reached this page because you have been granted exclusive access by:</p>
        <p>
            <strong>Name:</strong> <?=$this->view->creatorProfile->getFirstName(); ?> <?=$this->view->creatorProfile->getLastName(); ?><br/>
            <strong>Email:</strong> <?=$this->view->creatorProfile->getEmail(); ?><br/>
            <strong>Telephone:</strong> <?php if($this->view->creatorProfile->getPhone()!=null){echo $this->view->creatorProfile->getPhone();}else{echo _g('Not provided');} ?>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger" role="alert" id="error-box" name="error-box"></div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <form method="post" autocomplete="off" enctype="multipart/form-data" action="#" id="user-form" name="user-form" class="form-horizontal">
            {{csrf()}}
            <input type="hidden" name="token" id="token" value="<?= $this->view->token; ?>">
            <div id="step-1" class=" step">
                <p>Once registered, you will only need your email address and password to log in.</p>
                <br />
                <p>As a security precaution please provide the email address that the sign-up link was sent to.</p>
                <br/>
                <div class="form-group">
                    <label for="emailaddressx" class="col-sm-3 control-label">Sign-up email:</label>
                    <div class="col-sm-9">
                        <input type="text" id="emailaddressx" name="emailaddressx" class="form-control" value=""/>
                    </div>
                </div>
                <button type="button" data-loading-text="Loading..." onclick="preValidateStepOne();" class="btn btn-primary" style="margin-top: 15px; margin-bottom: 15px;">
                        Continue to registration
                </button>
            </div>
            <div id="step-2" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5>Your Details</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="portalname" class="col-sm-3 control-label">* Title</label>
                        <div class="col-sm-9">
                            <select id="title" name="title" class="form-control">
                                <option <?php if($this->view->user->getTitle()=='Mr'){echo 'selected';}?> value="Mr">Mr</option>
                                <option <?php if($this->view->user->getTitle()=='Ms'){echo 'selected';}?> value="Ms">Ms</option>
                                <option <?php if($this->view->user->getTitle()=='Miss'){echo 'selected';}?> value="Miss">Miss</option>
                                <option <?php if($this->view->user->getTitle()=='Mrs'){echo 'selected';}?> value="Mrs">Mrs</option>
                                <option <?php if($this->view->user->getTitle()=='Mstr'){echo 'selected';}?> value="Mstr">Mstr</option>
                                <option <?php if($this->view->user->getTitle()=='Dr'){echo 'selected';}?> value="Dr">Dr</option>
                                <option <?php if($this->view->user->getTitle()=='Prof'){echo 'selected';}?> value="Prof">Prof</option>
                                <option <?php if($this->view->user->getTitle()=='Sir'){echo 'selected';}?> value="Sir">Sir</option>
                                <option <?php if($this->view->user->getTitle()=='Lord'){echo 'selected';}?> value="Lord">Lord</option>
                                <option <?php if($this->view->user->getTitle()=='Lady'){echo 'selected';}?> value="Lady">Lady</option>
                                <option <?php if($this->view->user->getTitle()=='Dame'){echo 'selected';}?> value="Dame">Dame</option>
                                <option <?php if($this->view->user->getTitle()=='Duke'){echo 'selected';}?> value="Duke">Duke</option>
                                <option <?php if($this->view->user->getTitle()=='Earl'){echo 'selected';}?> value="Earl">Earl</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="col-sm-3 control-label">* First Name</label>
                        <div class="col-sm-9">
                            <input type="text" id="firstname" name="firstname" class="form-control" value="<?= $this->view->user->getFirstName(); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-3 control-label">* Last Name</label>
                        <div class="col-sm-9">
                            <input type="text" id="lastname" name="lastname" class="form-control" value="<?= $this->view->user->getLastName(); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailaddress" class="col-sm-3 control-label">* Email Address</label>
                        <div class="col-sm-9">
                            <input type="text" id="emailaddress" name="emailaddress" class="form-control" value="<?= $this->view->user->getEmail(); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-sm-3 control-label">Telephone Number</label>
                        <div class="col-sm-9">
                            <input type="text" id="phone" name="phone" class="form-control" value="<?= $this->view->user->getPhone(); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="col-sm-3 control-label">Mobile Number</label>
                        <div class="col-sm-9">
                            <input type="text" id="mobile" name="mobile" class="form-control" value="<?= $this->view->user->getMobile(); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dob" class="col-sm-3 control-label">Date of birth</label>
                        <div class="col-sm-3">
                            <input type="text"  id="dob-formatted" name="dob-formatted" class="form-control" placeholder="dd-mm-yyyy" maxlength="10" value="<?= _fd($this->view->user->getBirthday()); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Gender</label>
                        <div class="checkbox col-sm-9">
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="gender" value="male" <?php if($this->view->user->getGender()=='male'){echo 'checked';}?>> Male
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="gender" value="female" <?php if($this->view->user->getGender()=='female'){echo 'checked';}?>> Female
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ widget('AddressFinderWidget','index') }}
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(1);" class="btn btn-default pull-left">
                        Previous
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="preValidateStepTwo();" class="btn btn-primary pull-right">
                        Next
                    </button>
                </div>
            </div>
            <div id="step-3" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5>Dietary Requirements</h5>
                </div>
                <div class="ibox-content">
                    <p>If you have dietary requirements please tick one or more of the boxes below.</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('Halal')){echo 'checked';} ?> value="halal">Halal</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('Kosher')){echo 'checked';} ?> value="Kosher">Kosher</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('No Alcohol')){echo 'checked';} ?> value="No Alcohol">No Alcohol</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('Nut Allergies')){echo 'checked';} ?> value="Nut Allergies">Nut Allergies</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('No Seafood')){echo 'checked';} ?> value="No Seafood">No Seafood</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('Vegetarian')){echo 'checked';} ?> value="Vegetarian">Vegetarian</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('Vegan')){echo 'checked';} ?> value="Vegan">Vegan</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('No Gluten')){echo 'checked';} ?> value="No Gluten">No Gluten</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if($this->view->userObj->hasDietaryRequirement('No Dairy or Lactose')){echo 'checked';} ?> value="No Dairy or Lactose">No Dairy or Lactose</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(2);" class="btn btn-default pull-left">
                        Previous
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(4);" class="btn btn-primary pull-right">
                        Next
                    </button>
                </div>
            </div>
            <div id="step-4" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5>Preferred Categories</h5>
                </div>
                <div class="ibox-content">
                    <p>Setting your preferred categories will help make sure you see the events most relevant to you.</p>
                    <?=(new CategoryPickerWidget('user',array('userId'=>$this->view->userObj->getUserId())))->getContent() ?>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(3);" class="btn btn-default pull-left">
                        Previous
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(5);" class="btn btn-primary pull-right">
                        Next
                    </button>
                </div>
            </div>
            <div id="step-5" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5>Communication Preferences</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" <?php if($this->view->userObj->getUserContactPreferences()->getAlertsAndNotifications()==1){echo 'checked';} ?> value="alerts" id="enabled" name="enabled"> <?= _g('Alerts & notifications for items that are relevant to me'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" <?php if($this->view->userObj->getUserContactPreferences()->getInvitations()==1){echo 'checked';} ?> value="invitations" id="enabled" name="enabled"> <?= _g('Can receive Invitations to Items'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" <?php if($this->view->userObj->getUserContactPreferences()->getSuggestions()==1){echo 'checked';} ?> value="suggestions" id="enabled" name="enabled"> <?= _g("Can receive Suggestions for Items"); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" <?php if($this->view->userObj->getUserContactPreferences()->getPartnerCommunications()==1){echo 'checked';} ?> value="partners" id="enabled" name="enabled"> <?= _g('Apprecie Partner Communications'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" <?php if($this->view->userObj->getUserContactPreferences()->getUpdatesAndNewsletters()==1){echo 'checked';} ?> value="news" id="enabled" name="enabled"> <?= _g('Apprecie Updates and Newsletters'); ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(4);" class="btn btn-default pull-left">
                        Previous
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(6);" class="btn btn-primary pull-right">
                        Next
                    </button>
                </div>
            </div>
            <div id="step-6" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5>Login Details</h5>
                </div>
                <div class="ibox-content">
                    <p>Finally please set the password for your account below:</p>
                    <p style="margin-bottom: 30px;">A password with medium strength is required at a minimum. It should consist of 8-25 characters, mixing both letters and numbers. We recommend you to include both upper-case and lower-case letters and special characters to make a very strong password.</p>
                    <div class="form-group">
                        <label for="password" class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-9">
                            <input type="password" id="password" name="password" class="form-control" maxlength="25"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password" class="col-sm-3 control-label">Confirm Password</label>
                        <div class="col-sm-9">
                            <input type="password" id="confirm-password" name="confirm-password" class="form-control" maxlength="25"/>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="checkbox col-sm-12">
                            <label class="radio-inline">
                                <input type="checkbox" name="i-agree" id="i-agree" value="iagree"> I agree to the following documents:
                            </label>
                            <ul id="terms-list" style="list-style-type: none;padding-top: 10px;padding-left: 20px;"></ul>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(5);" class="btn btn-default pull-left">
                        Previous
                    </button>
                    <button type="button" data-loading-text="Loading..." id="complete-btn" onclick="validateStep(6);" class="btn btn-primary pull-right">
                        Complete
                    </button>
                </div>
            </div>
            <div id="step-7" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5>Completed</h5>
                </div>
                <div class="ibox-content">
                    <p>You may now login to your account</p>

                </div>
                <div class="panel-footer" style="height:55px;">
                    <a href="/login" class="btn btn-primary">Login</a>
                </div>
            </div>
        </form>
    </div>
</div>