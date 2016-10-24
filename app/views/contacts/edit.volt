<?php
$address = Address::findFirstBy('addressId',$this->view->user->getUserProfile()->getHomeAddressId());
$oldPortal = (new \Apprecie\Library\Users\UserEx())->getActiveQueryPortal();
\Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($this->view->user->getPortalId());
?>
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/validation/contacts/create.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script>
    $(document).ready(function() {
        setSteps(4);
        setStep(1);

        $("#new-contact-form").submit(function(e) {
            e.preventDefault();
        });

        <?php if ($address != null): ?>
        $('#search-term').val('<?= $address->getPostalCode(); ?>');
        $('#country').val('<?= $address->getCountryIso3(); ?>');
        $('#address-id').val('<?= $address->getId(); ?>');
        $('#selected-address-value').html("<?= $address->getLine1() . ' ' . $address->getLine2() . ' ' . $address->getPostalCode(); ?>");
        $('#selected-address').show();
        <?php endif; ?>

        var picker = new Pikaday(
            {
                field: document.getElementById('dob-formatted'),
                yearRange: [<?= (int)date('Y') - 120; ?>, <?= (int)date('Y') - 18; ?>],
                firstDay: 1,
                format: 'DD-MM-YYYY'
            }
        );
    });

    function setSteps(numberOfSteps) {
        steps = numberOfSteps;
        stepper = (100 / steps);
    }

    function setStep(stepID) {
        $('#step-progress-bar').css('width', (stepper*stepID) + '%');
        $('.step').hide();
        $('#step-' + stepID).show();
    }

    function validateStepOne() {
        if (validateStep(1)) {
            loader(true);
            $.when(getEmailInUse($('#portal-name').val(), $('#emailaddress').val(), <?= $this->view->user->getUserId();?>)).then(function(data) {
                loader(false);
                if (data.users != 0) {
                    errors.push('This email address is already in use');
                    displayErrors();
                } else {
                    setStep(2);
                }
            });
        } else {
            displayErrors();
        }
    }

    function EditUser(userId) {
        return $.ajax({
            url: "/contacts/ajaxedituser/" + userId,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: $('#user-form').serialize()
        });
    }

    function EditUserAjax() {
        loader(true);
        $.when(EditUser(<?= $this->view->user->getUserId(); ?>)).then(function(data) {
            setStep(4);
            loader(false);
        });
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Edit Contact Wizard'); ?></h2>
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
        <div class="alert alert-danger" role="alert" id="error-box" style="display: none;"></div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <form class="form-horizontal" autocomplete="off" id="user-form" name="edit-contact-form" method="post" enctype="multipart/form-data">
            {{csrf()}}
            <div id="step-1" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Personal Details'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div style="margin-bottom: 49px;">
                        <div class="form-group">
                            <label for="reference-code" class="col-sm-3 control-label">*&nbsp;<?= _g('Reference Code'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="reference-code" name="reference-code" value="<?= $this->view->user->getUserReference(); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3" style="display: inline-block;">-or-</div>
                        </div>
                        <div class="form-group">
                            <label for="portalname" class="col-sm-3 control-label">*&nbsp;<?= _g('Title'); ?></label>
                            <div class="col-sm-9">
                                <select id="title" name="title" class="form-control">
                                    <option value="Mr" <?php if ($this->view->user->getUserProfile()->getTitle() === "Mr") {echo "selected";} ?>><?= _g('Mr'); ?></option>
                                    <option value="Ms" <?php if ($this->view->user->getUserProfile()->getTitle() === "Ms") {echo "selected";} ?>><?= _g('Ms'); ?></option>
                                    <option value="Miss" <?php if ($this->view->user->getUserProfile()->getTitle() === "Miss") {echo "selected";} ?>><?= _g('Miss'); ?></option>
                                    <option value="Mrs" <?php if ($this->view->user->getUserProfile()->getTitle() === "Mrs") {echo "selected";} ?>><?= _g('Mrs'); ?></option>
                                    <option value="Mstr" <?php if ($this->view->user->getUserProfile()->getTitle() === "Mstr") {echo "selected";} ?>><?= _g('Mstr'); ?></option>
                                    <option value="Dr" <?php if ($this->view->user->getUserProfile()->getTitle() === "Dr") {echo "selected";} ?>><?= _g('Dr'); ?></option>
                                    <option value="Prof" <?php if ($this->view->user->getUserProfile()->getTitle() === "Prof") {echo "selected";} ?>><?= _g('Prof'); ?></option>
                                    <option value="Sir" <?php if ($this->view->user->getUserProfile()->getTitle() === "Sir") {echo "selected";} ?>><?= _g('Sir'); ?></option>
                                    <option value="Lord" <?php if ($this->view->user->getUserProfile()->getTitle() === "Lord") {echo "selected";} ?>><?= _g('Lord'); ?></option>
                                    <option value="Lady" <?php if ($this->view->user->getUserProfile()->getTitle() === "Lady") {echo "selected";} ?>><?= _g('Lady'); ?></option>
                                    <option value="Dame" <?php if ($this->view->user->getUserProfile()->getTitle() === "Dame") {echo "selected";} ?>><?= _g('Dame'); ?></option>
                                    <option value="Duke" <?php if ($this->view->user->getUserProfile()->getTitle() === "Duke") {echo "selected";} ?>><?= _g('Duke'); ?></option>
                                    <option value="Earl" <?php if ($this->view->user->getUserProfile()->getTitle() === "Earl") {echo "selected";} ?>><?= _g('Earl'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="firstname" class="col-sm-3 control-label">*&nbsp;<?= _g('First Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="firstname" name="firstname" value="<?= $this->view->user->getUserProfile()->getFirstName(); ?>" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-3 control-label">*&nbsp;<?= _g('Last Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="lastname" name="lastname"  value="<?= $this->view->user->getUserProfile()->getLastName(); ?>" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailaddress" class="col-sm-3 control-label"><?= _g('Email Address'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="emailaddress" name="emailaddress" value="<?= $this->view->user->getUserProfile()->getEmail(); ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailaddress" class="col-sm-3 control-label"><?= _g('Telephone Number'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="phone" name="phone" value="<?= $this->view->user->getUserProfile()->getPhone(); ?>" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="col-sm-3 control-label"><?= _g('Mobile Number'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="mobile" name="mobile" value="<?= $this->view->user->getUserProfile()->getMobile(); ?>" class="form-control"/>
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
                    <button type="button" data-loading-text="Loading..." onclick="validateStepOne();" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-2" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Dietary Requirements'); ?></h5>
                </div>
                <div class="ibox-content">
                    <p>If the person has any dietary requirements please tick one or more of the boxes below.</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('Halal')) {echo 'checked';} ?> value="halal">Halal</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('Kosher')) {echo 'checked';} ?> value="Kosher">Kosher</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('No Alcohol')) {echo 'checked';} ?> value="No Alcohol">No Alcohol</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('Nut Allergies')) {echo 'checked';} ?> value="Nut Allergies">Nut Allergies</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('No Seafood')) {echo 'checked';} ?> value="No Seafood">No Seafood</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('Vegetarian')) {echo 'checked';} ?> value="Vegetarian">Vegetarian</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('Vegan')) {echo 'checked';} ?> value="Vegan">Vegan</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('No Gluten')) {echo 'checked';} ?> value="No Gluten">No Gluten</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" <?php if ($this->view->user->hasDietaryRequirement('No Dairy or Lactose')) {echo 'checked';} ?> value="No Dairy or Lactose">No Dairy or Lactose</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(1);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(3);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-3" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Preferred Categories'); ?></h5>
                </div>
                <div class="ibox-content">
                    <?= (new CategoryPickerWidget('user', array('userId'=>$this->view->user->getUserId())))->getContent(); ?>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(2);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="EditUserAjax();" class="btn btn-primary pull-right">
                        <?= _g('Save'); ?>
                    </button>
                </div>
            </div>
            <div id="step-4" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Contact Updated!'); ?></h5>
                </div>
                <div class="ibox-content">
                    <p><?= _g('The details of this user have been updated.'); ?></p>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <a href="/contacts" class="btn btn-primary pull-left">
                        <?= _g('Contact Management'); ?>
                    </a>
                    <a href="/contacts/viewuser/<?= $this->view->user->getUserId(); ?>" class="btn btn-primary pull-right">
                        <?= _g('Contact Profile'); ?>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
\Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($oldPortal);
?>