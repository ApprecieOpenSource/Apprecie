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
    #organisation-selection{
        display: none;
    }
</style>
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/users/create.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script src="/js/portals/quotas.js"></script>
<script src="/js/users/userLookupWidget.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>

<script>
    var portalId = 0;
    var userId = 0;
    var steps = 0;
    var stepper = 0;

    var hasEmail = null;

    var quota = null;

    var emailTemplates = [];
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::CLIENT; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_CLIENT; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::INTERNAL; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_INTERNAL; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::MANAGER; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_MANAGER; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_AFFILIATE_SUPPLIER; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_APPRECIE_SUPPLIER; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::PORTAL_ADMIN; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_PORTAL_ADMIN; ?>';

    $(document).ready(function(){
        var signUpBtn = $('#send-registration');
        var generateBtn = $('#generate-link');
        var removeBtn = $('#remove-link');
        var registrationLink = $("#registration-link");
        var registrationLinkContainer = $('#registration-link-container');

        var successMsg = $('#portal-access-success');
        var errorMsg = $('#portal-access-error');

        setSteps(6);
        setStep(1);

        $('#portal-name').change(function(){
            findPortalOrganisations($('#portal-name').val());
        });

        $('#organisationId').change(function(){
            findPortalQuotas($('#portal-name').val(),$('#organisationId').val());
        });

        $('#role').change(function(){
            showOrHideQuotaAlert($(this).val());
        });

        $("#user-form").submit(function(e){
            e.preventDefault();
        });

        generateBtn.click(generateSignUp);

        removeBtn.click(removeSignUp);

        registrationLink.on("click", function () {
            $(this).select();
        });

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

        function generateSignUp() {
            successMsg.hide();
            errorMsg.hide();

            generateBtn.prop('disabled', true).html("Processing...");
            $.ajax({
                url: "/adminusers/AjaxGenerateRegistrationLink/" + userId,
                type: 'post',
                dataType: 'json',
                data: {'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN},
                cache: false
            }).done(function(data) {
                if (data.result === 'success') {

                    generateBtn.hide();
                    generateBtn.html("Grant Portal Access");
                    generateBtn.prop('disabled', false);

                    removeBtn.show();

                    if (hasEmail) {
                        signUpBtn.show();
                    }

                    registrationLink.val(data.registration);
                    registrationLinkContainer.show();

                } else if (data.result === 'failed' && data.message) {
                    generateBtn.html("Grant Portal Access");
                    errorMsg.html(data.message).show();
                }
            });
        }

        function removeSignUp() {
            successMsg.hide();
            errorMsg.hide();

            removeBtn.prop('disabled', true).html("Removing...");
            $.ajax({
                url: "/adminusers/AjaxRemoveRegistrationLink/" + userId,
                type: 'post',
                dataType: 'json',
                data: {'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN},
                cache: false
            }).done(function(data) {
                if (data.result === 'success') {

                    removeBtn.hide();
                    removeBtn.html("Remove Pending Portal Access");
                    removeBtn.prop('disabled', false);

                    generateBtn.show();

                    if (hasEmail) {
                        signUpBtn.hide();
                    }

                    registrationLinkContainer.hide();
                    registrationLink.val('');

                }
            });
        }
    });

    function showOrHideQuotaAlert(role) {
        var showAlert = false;

        switch (role) {
            case '<?= \Apprecie\Library\Users\UserRole::PORTAL_ADMIN; ?>':
                if ((quota.portalAdministratorTotal - quota.portalAdministratorUsed) < 1) {
                    showAlert = true;
                }
                break;
            case '-1':
                if ((quota.portalAdministratorTotal - quota.portalAdministratorUsed) < 1 || (quota.managerTotal - quota.managerUsed) < 1) {
                    showAlert = true;
                }
                break;
            case '-3':
                if ((quota.portalAdministratorTotal - quota.portalAdministratorUsed) < 1 || (quota.apprecieSupplierTotal - quota.apprecieSupplierUsed) < 1) {
                    showAlert = true;
                }
                break;
        }

        if (showAlert) {
            $('#quota-alert').show();
            $('.signup-info').hide();
        } else {
            $('#quota-alert').hide();
            $('.signup-info').show();
        }
    }

    function sendSignUp() {
        var successMsg = $('#portal-access-success');
        var errorMsg = $('#portal-access-error');
        successMsg.hide();
        errorMsg.hide();

        var signUpBtn = $('#send-registration');
        signUpBtn.prop('disabled', true).html("Sending...");
        $.ajax({
            url: "/adminusers/sendsignup",
            type: 'post',
            dataType: 'json',
            cache: false,
            data:{"portalId": portalId, "userId": userId, "CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN}
        }).done(function(){
            signUpBtn.prop('disabled', false).html('Send Sign-up Email');
            successMsg.html('Email sent.').show();
        });
    }

    function setSteps(numberOfSteps){
        steps=numberOfSteps;
        stepper=(100/steps);
    }
    function setStep(stepID){
        $('#step-progress-bar').css('width',(stepper*stepID)+'%');
        $('.step').hide();
        $('#step-'+stepID).show();
    }

    function findPortalQuotas(portalId,organisationId){
        $.when(getPortalQuota(portalId,organisationId)).then(function(data){
            quota = data;
            $('#role-selection').fadeOut('fast',function(){
                $('#quota').fadeOut('fast');

                $('#quota-table').empty();
                if (data.portalAdministratorTotal > 0) {
                    $('#quota-table').append('<tr><td>Organisation Owners</td><td>'+data.portalAdministratorTotal+'</td><td>'+data.portalAdministratorUsed+'</td><td>'+(data.portalAdministratorTotal-data.portalAdministratorUsed)+'</td></tr>');
                }
                if (data.managerTotal > 0) {
                    $('#quota-table').append('<tr><td>Managers</td><td>'+data.managerTotal+'</td><td>'+data.managerUsed+'</td><td>'+(data.managerTotal-data.managerUsed)+'</td></tr>');
                }
                if (data.internalMemberTotal > 0) {
                    $('#quota-table').append('<tr><td>Internal Members</td><td>'+data.internalMemberTotal+'</td><td>'+data.internalMemberUsed+'</td><td>'+(data.internalMemberTotal-data.internalMemberUsed)+'</td></tr>');
                }
                if (data.apprecieSupplierTotal > 0) {
                    $('#quota-table').append('<tr><td>Apprecie Suppliers</td><td>'+data.apprecieSupplierTotal+'</td><td>'+data.apprecieSupplierUsed+'</td><td>'+(data.apprecieSupplierTotal-data.apprecieSupplierUsed)+'</td></tr>');
                }
                if (data.affiliateSupplierTotal > 0) {
                    $('#quota-table').append('<tr><td>Affiliated Suppliers</td><td>'+data.affiliateSupplierTotal+'</td><td>'+data.affiliateSupplierUsed+'</td><td>'+(data.affiliateSupplierTotal-data.affiliateSupplierUsed)+'</td></tr>');
                }
                if (data.memberTotal > 0) {
                    $('#quota-table').append('<tr><td>Clients</td><td>'+data.memberTotal+'</td><td>'+data.memberUsed+'</td><td>'+(data.memberTotal-data.memberUsed)+'</td></tr>');
                }

                $('#role').empty();
                if (data.portalAdministratorTotal > 0) {
                    $('#role').append('<option value="PortalAdministrator">Organisation Owner</option>');
                }
                if (data.portalAdministratorTotal > 0 && data.managerTotal > 0) {
                    $('#role').append('<option value="-1">Dual Role - Organisation Owner & Manager</option>');
                }
                if (data.portalAdministratorTotal > 0 && data.apprecieSupplierTotal > 0) {
                    $('#role').append('<option value="-3">Dual Role - Organisation Owner & Apprecie Supplier</option>');
                }

                showOrHideQuotaAlert($('#role').val());

                $('#role-selection').fadeIn('fast');
            });
        })
    }

    function getPortalOrganisations(portalId){
        return $.ajax({
            url: "/api/portalorganisations",
            type: 'post',
            dataType: 'json',
            cache: false,
            data:{"portalId":portalId, "onlyManaged":false}
        });
    }

    function CreateUserAjax(){
        loader(true);
        var btn=$('#create-btn');
        btn.prop('disabled',true);
        $.when(CreateUser()).then(function(data){
            userId=data.userId;
            portalId=data.portalId;

            emailWidget.templateType = emailTemplates[data.role];
            emailWidget.previewData = {
                "portalId": null,
                "user": userId,
                "emailType": 'signup'
            };

            setStep(6);
            loader(false);
            btn.prop('disabled',false);
        })
    }

    function preValidateStepTwo(){
        clearErrors();
        loader(true);
        $.when(getEmailInUse($('#portal-name').val(),$('#emailaddress').val())).then(function(data){
            if (data.users!=0) {
                errors.push('This email address is already in use');
                displayErrors();
            } else {
                if ($('#emailaddress').val() != '') {
                    hasEmail = true;
                }
                validateStep(2);
            }
            loader(false);
        })
    }

    function findPortalOrganisations(portalId){
        loader(true);
        $('#organisation-selection').stop().fadeOut('fast');
        $.when(getPortalOrganisations(portalId)).then(function(data){
            $('#organisationId').html('<option disabled selected>Please select</option>');
            $.each(data,function(key, value){
                $('#organisationId').append('<option value="'+value.organisationId+'">'+value.organisationName+'</option>');
            })
            $('#organisation-selection').stop().fadeIn('fast');
            loader(false);
        })
    }

    function ShowQuota(){
        $('#quota').toggle('fast');
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('New Person Wizard'); ?></h2>
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
            <div id="step-1" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Portal Details'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div class="form-group">
                        <label for="portalname" class="col-sm-3 control-label"><?= _g('Portal'); ?></label>
                        <div class="col-sm-9">
                            <select class="form-control" id="portal-name" name="portal-name">
                                <option value="none"><?= _g('Please select'); ?></option>
                                <?php foreach($this->view->portals as $portal):
                                    if($portal->getPortalName()!='admin'):?>
                                        <option value="<?= $portal->getPortalId();?>"><?= $portal->getPortalName(); ?></option>
                                <?php endif;
                                endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="" id="organisation-selection">
                        <div class="form-group">
                            <label for="role" class="col-sm-3 control-label"><?= _g('Organisation'); ?></label>
                            <div class="col-sm-9">
                                <select id="organisationId" name="organisationId" class="form-control" style="margin-bottom: 15px;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="" id="role-selection">
                        <div class="form-group">
                            <label for="role" class="col-sm-3 control-label"><?= _g('Role'); ?></label>
                            <div class="col-sm-9">
                                <select id="role" name="role" class="form-control" style="margin-bottom: 15px;"></select>
                                <div class="alert alert-info" role="alert" style="display: none;" id="quota-alert">
                                    <p><?= _g('You have no quota left for this role - you may create the user but you will not be able to send them a sign-up to activate their account.'); ?></p>
                                </div>
                                <a onclick="ShowQuota()" class="btn btn-default"><?= _g('Show quotas'); ?></a>
                            </div>
                        </div>
                        <div style="margin: 20px;">
                            <table class="table table-highlight" style="display: none;" id="quota">
                                <thead>
                                <tr>
                                    <th><?= _g('Role'); ?></th>
                                    <th><?= _g('Quota'); ?></th>
                                    <th><?= _g('Used'); ?></th>
                                    <th><?= _g('Available'); ?></th>
                                </tr>
                                </thead>
                                <tbody id="quota-table">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="validateStep(1);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-2" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Personal Details'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div class="form-group">
                        <label for="user-lookup-value" class="col-sm-3 control-label">*&nbsp;<?= _g('Owner'); ?></label>
                        <div class="col-sm-9">
                            {{ widget('UserFinderWidget','index') }}
                        </div>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label for="tier" class="col-sm-3 control-label" ><?= _g('Tier'); ?></label>
                        <div class="col-sm-9">
                            <select id="tier" name="tier" class="form-control">
                                <option value="<?= \Apprecie\Library\Users\Tier::CORPORATE; ?>" selected><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::CORPORATE))->getExplanatoryText(); ?></option>
                                <option value="<?= \Apprecie\Library\Users\Tier::ONE; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::ONE))->getExplanatoryText(); ?></option>
                                <option value="<?= \Apprecie\Library\Users\Tier::TWO; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::TWO))->getExplanatoryText(); ?></option>
                                <option value="<?= \Apprecie\Library\Users\Tier::THREE; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::THREE))->getExplanatoryText(); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reference-code" class="col-sm-3 control-label"><?= _g('Reference Code'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="reference-code" name="reference-code" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="portalname" class="col-sm-3 control-label">*&nbsp;<?= _g('Title'); ?></label>
                        <div class="col-sm-9">
                            <select id="title" name="title" class="form-control">
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
                    <div class="form-group">
                        <label for="emailaddress" class="col-sm-3 control-label">*&nbsp;<?= _g('Email Address'); ?></label>
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
                            <input type="text"  id="dob-formatted" name="dob-formatted" class="form-control" placeholder="dd-mm-yyyy" maxlength="10" />
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
                    <button type="button" data-loading-text="Loading..." onclick="setStep(1);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
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
                                <input type="checkbox" name="communication[]" value="alerts" checked id="enabled" name="enabled"> <?= _g('Alerts & notifications for items that are relevant to me'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" value="invitations" checked id="enabled" name="enabled"> <?= _g('Can receive Invitations to Items'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" value="suggestions" checked id="enabled" name="enabled"> <?= _g("Can receive Suggestions for Items"); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" value="partners" checked id="enabled" name="enabled"> <?= _g('Apprecie Partner Communications'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="communication[]" value="news" checked id="enabled" name="enabled"> <?= _g('Apprecie Updates and Newsletters'); ?>
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
                    {{ widget('CategoryPickerWidget','index') }}
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(4);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="CreateUserAjax();" id="create-btn" class="btn btn-primary pull-right">
                        <?= _g('Create'); ?>
                    </button>
                </div>
            </div>
            <div id="step-6" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('User Created!'); ?></h5>
                </div>
                <div class="ibox-content">

                    <p class="signup-info">
                        <?= _g('This person does not have access to the Portal without going through the sign-up process. By granting portal access, a Sign-up URL will be generated and a client quota will be consumed if applicable. You can then pass the Sign-up URL to the user yourself or request a system email containing the Sign-up URL sent to the user.'); ?>
                    </p>

                    <div class="signup-info btn-group" style="padding-top: 15px;">
                        <button class="btn btn-default" id="generate-link" type="button"><?= _g('Grant Portal Access'); ?></button>
                        <button class="btn btn-danger" id="remove-link" type="button" style="display: none;"><?= _g('Remove Pending Portal Access'); ?></button>
                        <button class="btn btn-default" id="send-registration" type="button" style="display: none;" data-toggle="modal" data-target="#sendEmailModal"><?= _g('Send Sign-up Email'); ?></button>
                        <span style="display: inline-block;padding: 7px 12px;" class="text-success" id="portal-access-success"></span>
                        <span style="display: inline-block;padding: 7px 12px;" class="text-danger" id="portal-access-error"></span>
                    </div>

                    <div class="signup-info input-group" style="padding-top: 15px;display: none;" id="registration-link-container">
                        <span class="input-group-addon"><?= _g('Sign-up URL'); ?></span>
                        <input type="text" class="form-control" id="registration-link" name="registration-link" value="">
                    </div>

                </div>
                <div class="panel-footer" style="height:55px;">
                    <a href="/adminusers" class="btn btn-primary pull-right">
                        <?= _g('User Management'); ?>
                    </a>
                    <a href="/adminusers/create" class="btn btn-primary pull-left">
                        <?= _g('Add Another'); ?>
                    </a>
                </div>
            </div>
        </form>
    </div>
    <div class="col-sm-6" id="progress-message"></div>
</div>
<?= (new EmailWidget('index', array('templateType' => null, 'callback' => 'sendSignUp', 'previewData' => null)))->getContent(); ?>