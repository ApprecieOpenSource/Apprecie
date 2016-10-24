<script src="/js/validation/errors.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/validation/contacts/create.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script>
    var portalId = 0;
    var userId = 0;

    $(document).ready(function() {
        setSteps(4);
        setStep(1);

        $("#new-contact-form").submit(function(e) {
            e.preventDefault();
        });

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
        stepper = (100/steps);
    }

    function setStep(stepID) {
        $('#step-progress-bar').css('width',(stepper*stepID)+'%');
        $('.step').hide();
        $('#step-'+stepID).show();
    }

    function validateStepOne() {
        if (validateStep(1)) {
            loader(true);
            $.when(getEmailInUse($('#portal-name').val(), $('#emailaddress').val())).then(function(data) {
                loader(false);
                if (data.users != 0) {
                    errors.push('This email address is already in use');
                    displayErrors();
                } else {
                    setStep(2);
                }
            })
        } else {
            displayErrors();
        }
    }

    function CreateUserAjax(){
        loader(true);
        var btn = $('#create-btn');
        btn.prop('disabled',true);
        $.when(CreateUser()).then(function(data) {
            userId = data.userId;
            portalId = data.portalId;
            setStep(4);
            loader(false);
            btn.prop('disabled',false);
        })
    }

    function CreateUser(){
        return $.ajax({
            url: "/contacts/ajaxcreateuser",
            type: 'post',
            dataType: 'json',
            cache: false,
            data:$('#new-contact-form').serialize()
        });
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('New Contact Wizard'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="progress">
            <div id="step-progress-bar" class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger" role="alert" id="error-box" style="display: none;"></div>
    </div>
</div>
<form class="form-horizontal" autocomplete="off" id="new-contact-form" name="new-contact-form" method="post" enctype="multipart/form-data">
    {{csrf()}}
    <div class="row step" id="step-1">
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Personal Details'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div style="margin-bottom: 49px;">
                        <div class="form-group">
                            <label for="reference-code" class="col-sm-3 control-label">*&nbsp;<?= _g('Reference Code'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="reference-code" name="reference-code" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3" style="display: inline-block;">-or-</div>
                        </div>
                        <div class="form-group">
                            <label for="portalname" class="col-sm-3 control-label">*&nbsp;<?= _g('Title'); ?></label>
                            <div class="col-sm-9">
                                <select id="title" name="title" class="form-control full-width">
                                    <option value="Mr"><?= _g('Mr'); ?></option>
                                    <option value="Ms"><?= _g('Ms'); ?></option>
                                    <option value="Miss"><?= _g('Miss'); ?></option>
                                    <option value="Mrs"><?= _g('Mrs'); ?></option>
                                    <option value="Mstr"><?= _g('Mstr'); ?></option>
                                    <option value="Dr"><?= _g('Dr'); ?></option>
                                    <option value="Prof"><?= _g('Prof'); ?></option>
                                    <option value="Sir"><?= _g('Sir'); ?></option>
                                    <option value="Lord"><?= _g('Lord'); ?></option>
                                    <option value="Lady"><?= _g('Lady'); ?></option>
                                    <option value="Dame"><?= _g('Dame'); ?></option>
                                    <option value="Duke"><?= _g('Duke'); ?></option>
                                    <option value="Earl"><?= _g('Earl'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="firstname" class="col-sm-3 control-label">*&nbsp;<?= _g('First Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="firstname" name="firstname" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="lastname" class="col-sm-3 control-label">*&nbsp;<?= _g('Last Name'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="lastname" name="lastname" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailaddress" class="col-sm-3 control-label"><?= _g('Email Address'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="emailaddress" name="emailaddress" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-sm-3 control-label"><?= _g('Telephone Number'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="phone" name="phone" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="col-sm-3 control-label"><?= _g('Mobile Number'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="mobile" name="mobile" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dob" class="col-sm-3 control-label"><?= _g('Date of birth'); ?></label>
                        <div class="col-sm-3">
                            <input type="text"  id="dob-formatted" name="dob-formatted" class="form-control" placeholder="dd-mm-yyyy" maxlength="10"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?= _g('Gender'); ?></label>
                        <div class="checkbox col-sm-9">
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="gender-male" value="male"> <?= _g('Male'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="gender" id="gender-female" value="female"> <?= _g('Female'); ?>
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
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins hidden-xs">
                <div class="ibox-title">
                    <h5><?= _g("Help"); ?></h5>
                </div>
                <div class="ibox-content">
                    <p><?= _g("Now you can begin to fill out the full details of the user. Highlighted below are some specific details:"); ?>
                    <p><strong><?= _g("Reference Code: "); ?></strong><?= _g("This is an optional field that can be used to record an important reference against the user. This is primarily for use when a user wishes to remain more anonymous and not have their personal details recorded. In these instances, you could use the Reference Code to store their customer number, account number, or some other obscure data that personally identifies that user to you, but does not say anything about who they actually are. Alternatively, you may use this field just to record Reference Numbers for reconciliation use around the Portal and your other systems."); ?>
                    <p><?= _g("You must provide EITHER a Reference Code OR a First and Last Name. If you have provided a Reference Number, you may leave the remaining fields all blank to create a completely anonymised user."); ?>
                    <p><strong><?= _g("Address: "); ?></strong><?= _g("Find the user's address by using the Search tool to look-up partial or full addresses. You can search on post-code, or street numbers and names. Then just select the relevant address from the list below. If you make a mistake, just re-type into the Search tool and Search again."); ?>
                    <p><?= _g("If there are any aspects of the user's details that you do not know, you may leave them blank and the user can complete them upon sign-up at a later stage (if an employee user or online Member)."); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row step" id="step-2">
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Dietary Requirements'); ?></h5>
                </div>
                <div class="ibox-content">
                    <p>If the person has any dietary requirements please tick one or more of the boxes below.</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="Halal"><?= _g('Halal'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="Kosher"><?= _g('Kosher'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="No Alcohol"><?= _g('No Alcohol'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="Nut Allergies"><?= _g('Nut Allergies'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="No Seafood"><?= _g('No Seafood'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="Vegetarian"><?= _g('Vegetarian'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="Vegan"><?= _g('Vegan'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="No Gluten"><?= _g('No Gluten'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet[]" value="No Dairy or Lactose"><?= _g('No Dairy or Lactose'); ?></label>
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
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins hidden-xs">
                <div class="ibox-title">
                    <h5><?= _g("Help"); ?></h5>
                </div>
                <div class="ibox-content">
                    <p><?= _g("Here you can set the dietary requirements for the user. The User can confirm/edit these options later from their own profile, or when attending an event."); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row step" id="step-3">
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Preferred Categories'); ?></h5>
                </div>
                <div class="ibox-content">
                    {{ widget('CategoryPickerWidget','index') }}
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(2);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." id="create-btn" onclick="CreateUserAjax();" class="btn btn-primary pull-right">
                        <?= _g('Create'); ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins hidden-xs">
                <div class="ibox-title">
                    <h5><?= _g("Help"); ?></h5>
                </div>
                <div class="ibox-content">
                    <p><?= _g("If you know your user's likes and dislikes, you can use the drop-down menus here to select their preferred interests. Use the initial drop-down to select a Primary category, and then select from the list of available sub-categories to add them to the user's profile. The User can set these themselves on sign-up or from their own profile at a later date, and the categories will also be adapted based on the user's interactions around the Portal. These selected categories determine the preferential order that Vault Content will appear to the user, so that they see Items of interest to them before anything else in the Vault."); ?>
                    <p><?= _g("Clicking Create on this page will finally create the user, and provide you with your next step options."); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row step" id="step-4">
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Contact Created!'); ?></h5>
                </div>
                <div class="ibox-content">
                    <p><?= _g('Your contact has been created. You can see all your contacts on the Contact Management page.'); ?></p>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <a href="/contacts" class="btn btn-primary pull-right">
                        <?= _g('Contact Management'); ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="ibox float-e-margins hidden-xs">
                <div class="ibox-title">
                    <h5><?= _g("Help"); ?></h5>
                </div>
                <div class="ibox-content">

                </div>
            </div>
        </div>
    </div>
</form>