<?php
$address=Address::findFirstBy('addressId',$this->view->user->getUserProfile()->getHomeAddressId());
?>
<style>
    #role-selection{
        display: none;
    }
    #user-address{
        display: none;
    }
    #address-select-container,#work-address-select-container{
        display: none;
    }
    #error-box{
        display:none;
    }
</style>
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/people/edit.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script src="/js/portals/quotas.js"></script>
<script src="/js/users/userLookupWidget.js"></script>
<script>
    $(document).ready(function () {
        setSteps(6);
        setStep(2);
        <?php if($address != null): ?>
        $('#search-term').val('<?= $address->getPostalCode(); ?>');
        $('#country').val('<?= $address->getCountryIso3(); ?>');
        $('#address-id').val('<?= $address->getId(); ?>');
        $('#selected-address-value').html("<?= $address->getLine1() . ' ' . $address->getLine2() . ' ' . $address->getPostalCode(); ?>");
        $('#selected-address').show();
        <?php endif; ?>

        $('#portal-name').change(function () {
            findPortalQuotas($('#portal-name').val());
            findPortalOrganisations($('#portal-name').val());
        });

        $("#user-form").submit(function (e) {
            e.preventDefault();
        });

        var signupBtn = $('#send-registration');
        signupBtn.click(function () {
            var $this = $(this);
            $this.attr('disabled', 'disabled').html("Sending...");
            $.ajax({
                url: "/people/sendsignup",
                type: 'post',
                dataType: 'json',
                cache: false,
                data: {"portalId": portalId, "userId": userId, "CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN}
            }).done(function () {
                $this.html('E-mail Sent');
            });
        });

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

        if (role === '<?= \Apprecie\Library\Users\UserRole::CLIENT; ?>') {
            $('#ref-label').text('* Reference Code');
            $('#emailaddress-label').text('Email Address');
            $('#or-group').show();
            $('#refAndName').css('margin-bottom', '49px');
        }
    });

    var portalId = <?= $this->view->user->getPortalId(); ?>;
    var userId = <?= $this->view->user->getUserId(); ?>;
    var role = '<?= $this->view->user->getActiveRole()->getName(); ?>';
    var steps = 0;
    var stepper = 0;

    function setSteps(numberOfSteps) {
        steps = numberOfSteps;
        stepper = (100 / steps);
    }

    function setStep(stepID) {
        $('#step-progress-bar').css('width', (stepper * stepID) + '%');
        $('.step').hide();
        $('#step-' + stepID).show();
    }

    function EditUserAjax() {
        loader(true);
        $.when(EditUser(userId)).then(function (data) {
            setStep(6);
            loader(false);
        })
    }

    function preValidateStepTwo() {
        clearErrors();
        loader(true);
        $.when(getEmailInUse(portalId, $('#emailaddress').val(), userId)).then(function (data) {
            if (data.users != 0) {
                errors.push('This email address is already in use');
                displayErrors();
            }
            else {
                validateStep(2);
            }
            loader(false);
        })
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Edit Person Wizard'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="progress">
            <div id="step-progress-bar" class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="alert alert-danger" role="alert" id="error-box" name="error-box"></div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <form class="form-horizontal" autocomplete="off" id="user-form" name="user-form" method="post" enctype="multipart/form-data">
            {{csrf()}}
            <div id="step-2" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Personal Details'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div id="refAndName">
                        <div class="form-group">
                            <label for="reference-code" class="col-sm-3 control-label" id="ref-label"><?= _g('Reference Code'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="reference-code" name="reference-code" value="<?=$this->view->user->getUserReference(); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group" id="or-group" style="display: none;">
                            <div class="col-sm-9 col-sm-offset-3" style="display: inline-block;">-or-</div>
                        </div>
                        <div class="form-group">
                            <label for="portalname" class="col-sm-3 control-label">* <?= _g('Title'); ?></label>
                            <div class="col-sm-9">
                                <select id="title" name="title" class="form-control">
                                    <option value="Mr" <?php if($this->view->user->getUserProfile()->getTitle()=="Mr"){ echo "selected";} ?>><?= _g('Mr'); ?></option>
                                    <option value="Ms" <?php if($this->view->user->getUserProfile()->getTitle()=="Ms"){ echo "selected";} ?>><?= _g('Ms'); ?></option>
                                    <option value="Miss" <?php if($this->view->user->getUserProfile()->getTitle()=="Miss"){ echo "selected";} ?>><?= _g('Miss'); ?></option>
                                    <option value="Mrs" <?php if($this->view->user->getUserProfile()->getTitle()=="Mrs"){ echo "selected";} ?>><?= _g('Mrs'); ?></option>
                                    <option value="Mstr" <?php if($this->view->user->getUserProfile()->getTitle()=="Mstr"){ echo "selected";} ?>><?= _g('Mstr'); ?></option>
                                    <option value="Dr" <?php if($this->view->user->getUserProfile()->getTitle()=="Dr"){ echo "selected";} ?>><?= _g('Dr'); ?></option>
                                    <option value="Prof" <?php if($this->view->user->getUserProfile()->getTitle()=="Prof"){ echo "selected";} ?>><?= _g('Prof'); ?></option>
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
                            <label for="firstname" class="col-sm-3 control-label">* <?= _g('First Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="firstname" name="firstname" value="<?=$this->view->user->getUserProfile()->getFirstName(); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-3 control-label">* <?= _g('Last Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="lastname" name="lastname"  value="<?=$this->view->user->getUserProfile()->getLastName(); ?>" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" <?php if($this->view->user->getRoles()[0]->getRole()->getDescription()!="Client"){echo 'style="display:none;"';}?>>
                        <label for="tier" class="col-sm-3 control-label">* <?= _g('Tier'); ?></label>
                        <div class="col-sm-9">
                            <select id="tier" name="tier" class="form-control">
                                <option <?= ((int)$this->view->user->getTier() === \Apprecie\Library\Users\Tier::CORPORATE) ? "selected" : ''; ?> value="<?= \Apprecie\Library\Users\Tier::CORPORATE; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::CORPORATE))->getExplanatoryText(); ?></option>
                                <option <?= ((int)$this->view->user->getTier() === \Apprecie\Library\Users\Tier::ONE) ? "selected" : ''; ?> value="<?= \Apprecie\Library\Users\Tier::ONE; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::ONE))->getExplanatoryText(); ?></option>
                                <option <?= ((int)$this->view->user->getTier() === \Apprecie\Library\Users\Tier::TWO) ? "selected" : ''; ?> value="<?= \Apprecie\Library\Users\Tier::TWO; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::TWO))->getExplanatoryText(); ?></option>
                                <option <?= ((int)$this->view->user->getTier() === \Apprecie\Library\Users\Tier::THREE) ? "selected" : ''; ?> value="<?= \Apprecie\Library\Users\Tier::THREE; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::THREE))->getExplanatoryText(); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailaddress" class="col-sm-3 control-label" id="emailaddress-label">* <?= _g('Email Address'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="emailaddress" name="emailaddress" value="<?=$this->view->user->getUserProfile()->getEmail(); ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailaddress" class="col-sm-3 control-label"><?= _g('Telephone Number'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="phone" name="phone" value="<?=$this->view->user->getUserProfile()->getPhone(); ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="col-sm-3 control-label"><?= _g('Mobile Number'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="mobile" name="mobile" value="<?=$this->view->user->getUserProfile()->getMobile(); ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dob" class="col-sm-3 control-label"><?= _g('Date of birth'); ?></label>
                        <div class="col-sm-3">
                            <input type="text"  id="dob-formatted" name="dob-formatted" class="form-control" placeholder="dd-mm-yyyy" maxlength="10" value="<?= _fd($this->view->user->getUserProfile()->getBirthday()); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?= _g('Gender'); ?></label>
                        <div class="checkbox col-sm-9">
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="gender-male" <?php if($this->view->user->getUserProfile()->getGender()=="male"){ echo "checked";} ?> value="male"> <?= _g('Male'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="gender-female" <?php if($this->view->user->getUserProfile()->getGender()=="female"){ echo "checked";} ?> value="female"> <?= _g('Female'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="ibox-title" style="border-top:none">
                        <h5><?= _g('Address'); ?></h5>
                    </div>
                    <div class="ibox-content ibox-content-min">
                        {{ widget('AddressFinderWidget','index') }}
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="preValidateStepTwo();" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-3" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Communication Preferences'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" <?php if($this->view->user->getUserContactPreferences()->getAlertsAndNotifications()==1){echo 'checked';} ?> value="alerts" id="enabled" name="enabled"> <?= _g('Alerts & notifications for items that are relevant to me'); ?>
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
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(2);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(4);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-4" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Dietary Requirements'); ?></h5>
                </div>
                <div class="ibox-content">
                    <p>If the person has any dietary requirements please tick one or more of the boxes below.</p>
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
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(3);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(5);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-5" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Preferred Categories'); ?></h5>
                </div>
                <div class="ibox-content">
                    <?=(new CategoryPickerWidget('user',array('userId'=>$this->view->user->getUserId())))->getContent() ?>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(4);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="EditUserAjax();" class="btn btn-primary pull-right">
                        <?= _g('Save'); ?>
                    </button>
                </div>
            </div>
            <div id="step-6" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('User Updated!'); ?></h5>
                </div>
                <div class="ibox-content">
                    <p><?= _g('The details of this user have been updated.'); ?></p>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <a href="/people" class="btn btn-primary pull-left">
                        <?= _g('People Management'); ?>
                    </a>
                    <a href="/people/viewuser/<?= $this->view->user->getUserId(); ?>" class="btn btn-primary pull-right">
                        <?= _g('User Profile'); ?>
                    </a>
                </div>
            </div>
        </form>
    </div>
<div class="col-sm-6" id="progress-message">

</div>
</div>
