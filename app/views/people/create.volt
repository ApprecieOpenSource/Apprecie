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

    #address-table{
        margin-left: 15px;
        margin-right:15px;
    }
</style>
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/people/create.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script src="/js/portals/quotas.js"></script>
<script src="/js/users/userLookupWidgetAdvanced.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<script src="/js/validation/general.min.js"></script>
<script>
    var portalId = 0;
    var userId = 0;
    var steps = 0;
    var stepper = 0;

    var hasEmail = null;

    var quota = [];
    <?php
    foreach($this->view->roleHierarchy->getVisibleRoles() as $roleName => $roleText){
        switch($roleName){
            case (\Apprecie\library\Users\UserRole::PORTAL_ADMIN):
                ?>quota['<?= $roleName; ?>'] = <?= $this->view->quotas->getPortalAdministratorTotal() - $this->view->quotas->getPortalAdministratorUsed(); ?>;<?php
                break;
            case (\Apprecie\library\Users\UserRole::AFFILIATE_SUPPLIER):
                ?>quota['<?= $roleName; ?>'] = <?= $this->view->quotas->getAffiliateSupplierTotal() - $this->view->quotas->getAffiliateSupplierUsed(); ?>;<?php
                break;
            case (\Apprecie\library\Users\UserRole::APPRECIE_SUPPLIER):
                ?>quota['<?= $roleName; ?>'] = <?= $this->view->quotas->getApprecieSupplierTotal() - $this->view->quotas->getApprecieSupplierUsed(); ?>;<?php
                break;
            case (\Apprecie\library\Users\UserRole::MANAGER):
                ?>quota['<?= $roleName; ?>'] = <?= $this->view->quotas->getManagerTotal() - $this->view->quotas->getManagerUsed(); ?>;<?php
                break;
            case (\Apprecie\library\Users\UserRole::INTERNAL):
                if($this->view->quotas->getManagerTotal() > 0){
                    ?>quota['<?= $roleName; ?>'] = <?= $this->view->quotas->getInternalMemberTotal() - $this->view->quotas->getInternalMemberUsed(); ?>;<?php
                }
                break;
            case (\Apprecie\library\Users\UserRole::CLIENT):
                if($this->view->quotas->getManagerTotal() > 0){
                    ?>quota['<?= $roleName; ?>'] = <?= $this->view->quotas->getMemberTotal() - $this->view->quotas->getMemberUsed(); ?>;<?php
                }
                break;
        }
    }
    ?>
    
    var emailTemplates = [];
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::CLIENT; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_CLIENT; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::INTERNAL; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_INTERNAL; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::MANAGER; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_MANAGER; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_AFFILIATE_SUPPLIER; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_APPRECIE_SUPPLIER; ?>';
    emailTemplates['<?= \Apprecie\Library\Users\UserRole::PORTAL_ADMIN; ?>'] = '<?= \Apprecie\Library\Mail\EmailTemplateType::SIGNUP_PORTAL_ADMIN; ?>';

    $(document).ready(function () {
        var signUpBtn = $('#send-registration');
        var generateBtn = $('#generate-link');
        var removeBtn = $('#remove-link');
        var registrationLink = $("#registration-link");
        var registrationLinkContainer = $('#registration-link-container');

        var successMsg = $('#portal-access-success');
        var errorMsg = $('#portal-access-error');

        setSteps(6);
        setStep(1);

        $("#user-form").submit(function (e) {
            e.preventDefault();
        });

        generateBtn.click(generateSignUp);

        removeBtn.click(removeSignUp);

        registrationLink.on("click", function () {
            $(this).select();
        });

        UserLookupAdvancedInit($('#role').val(),<?= $this->view->activeUser->getUserId(); ?>);
        if (quota[$('#role').val()] < 1) {
            $('#quota-alert').show();
        }
        if ($('#role option').size() != 1) {
            $('#user-lookup-search').prop('disabled', false);
        }

        $('#role').change(function () {
            UserLookupAdvancedInit($(this).val(),<?= $this->view->activeUser->getUserId(); ?>);
            $('#user-lookup-name').val('');
            $('#user-lookup-value').val('');
            $('#user-lookup-results-table').empty();

            if (quota[$(this).val()] < 1) {
                $('#quota-alert').show();
            } else {
                $('#quota-alert').hide();
            }
        });

        var picker = new Pikaday(
            {
                field: document.getElementById('dob-formatted'),
                yearRange: [<?= (int)date('Y') - 120; ?>, <?= (int)date('Y') - 18; ?>],
                firstDay: 1,
                format: 'DD-MM-YYYY'
            }
        );

        function generateSignUp() {
            successMsg.hide();
            errorMsg.hide();

            generateBtn.prop('disabled', true).html("Processing...");
            $.ajax({
                url: "/people/AjaxGenerateRegistrationLink/" + userId,
                type: 'post',
                dataType: 'json',
                data: {"CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN},
                cache: false
            }).done(function (data) {
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
                url: "/people/AjaxRemoveRegistrationLink/" + userId,
                type: 'post',
                dataType: 'json',
                data: {"CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN},
                cache: false
            }).done(function (data) {
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

    function sendSignUp() {
        var successMsg = $('#portal-access-success');
        var errorMsg = $('#portal-access-error');
        successMsg.hide();
        errorMsg.hide();

        var signUpBtn = $('#send-registration');
        signUpBtn.prop('disabled', true).html("Sending...");
        $.ajax({
            url: "/people/sendsignup",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {"portalId": portalId, "userId": userId, "CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN}
        }).done(function () {
            signUpBtn.prop('disabled', false).html('Send Sign-up Email');
            successMsg.html('Email sent.').show();
        });
    }

    function setSteps(numberOfSteps) {
        steps = numberOfSteps;
        stepper = (100 / steps);
    }

    function setStep(stepID) {
        $('#step-progress-bar').css('width', (stepper * stepID) + '%');
        $('.step').hide();
        $('#step-' + stepID).show();
    }

    function getPortalOrganisations(portalId) {
        return $.ajax({
            url: "/api/portalorganisations",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {"portalId": portalId, "onlyManaged": true, CSRF_SESSION_TOKEN: CSRF_SESSION_TOKEN}
        });
    }

    function CreateUserAjax() {
        loader(true);
        var btn = $('#create-btn');
        btn.prop('disabled', true);
        $.when(CreateUser()).then(function (data) {
            userId = data.userId;
            portalId = data.portalId;
            if ((data.role in quota) && quota[data.role] < 1) {
                $('.signup-info').remove();
            } else {
                emailWidget.templateType = emailTemplates[data.role];
                emailWidget.previewData = {
                    "portalId": null,
                    "user": userId,
                    "emailType": 'signup'
                };
            }
            setStep(6);
            loader(false);
            btn.prop('disabled', false);
        })
    }

    function preValidateStepOne() {

        var emailLabel = $('#emailaddress-label');
        var refLabel = $('#ref-label');
        var tierGroup = $('#tier-group');
        var orGroup = $('#or-group');
        var refAndName = $('#refAndName');

        if ($('#role').val() !== '<?= \Apprecie\Library\Users\UserRole::CLIENT; ?>') {
            emailLabel.text('* Email Address');
            refLabel.text('Reference Code');
            tierGroup.hide();
            orGroup.hide();
            refAndName.css('margin-bottom', '0')
        } else {
            emailLabel.text('Email Address');
            refLabel.text('* Reference Code');
            tierGroup.show();
            orGroup.show();
            refAndName.css('margin-bottom', '49px')
        }

        validateStep(1);
    }

    function preValidateStepTwo() {
        clearErrors();
        loader(true);
        $('#send-registration').hide();
        $.when(getEmailInUse($('#portal-name').val(), $('#emailaddress').val())).then(function (data) {
            if (data.users != 0) {
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
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('New Person Wizard'); ?></h2>
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
        <div class="alert alert-danger" role="alert" id="error-box"></div>
    </div>
</div>

<form class="form-horizontal" autocomplete="off" id="user-form" name="user-form" method="post" enctype="multipart/form-data">
{{csrf()}}
    <div class="row step" id="step-1">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= _g('Role Details'); ?></h5>
            </div>
            <div class="ibox-content ibox-content-min">
                <div>
                    <div class="form-group">
                        <label for="role" class="col-sm-3 control-label"><?= _g('Role'); ?></label>
                        <div class="col-sm-9">
                            <select id="role" name="role" class="form-control full-width" style="margin-bottom: 15px;">
                                <option disabled value="none">Please select</option>
                                    <?php
                                    foreach($this->view->roleHierarchy->getVisibleRoles() as $roleName => $roleText){
                                        switch($roleName){
                                            case (\Apprecie\library\Users\UserRole::PORTAL_ADMIN):
                                                if($this->view->quotas->getPortalAdministratorTotal() > 0) {
                                                    ?><option value="<?= $roleName; ?>"><?= $roleText; ?></option><?php
                                                }
                                                break;
                                            case (\Apprecie\library\Users\UserRole::AFFILIATE_SUPPLIER):
                                                if($this->view->quotas->getAffiliateSupplierTotal() > 0) {
                                                    ?><option value="<?= $roleName; ?>"><?= $roleText; ?></option><?php
                                                }
                                                break;
                                            case (\Apprecie\library\Users\UserRole::APPRECIE_SUPPLIER):
                                                if($this->view->quotas->getApprecieSupplierTotal() > 0) {
                                                    ?><option value="<?= $roleName; ?>"><?= $roleText; ?></option><?php
                                                }
                                                break;
                                            case (\Apprecie\library\Users\UserRole::MANAGER):
                                                if($this->view->quotas->getManagerTotal() > 0) {
                                                    ?><option value="<?= $roleName; ?>"><?= $roleText; ?></option><?php
                                                }
                                                break;
                                            case (\Apprecie\library\Users\UserRole::INTERNAL):
                                                if($this->view->quotas->getInternalMemberTotal() > 0 && $this->view->quotas->getManagerTotal() > 0){
                                                    ?><option value="<?= $roleName; ?>"><?= $roleText; ?></option><?php
                                                }
                                                break;
                                            case (\Apprecie\library\Users\UserRole::CLIENT):
                                                if($this->view->quotas->getMemberTotal() > 0 && $this->view->quotas->getManagerTotal() > 0){
                                                    ?><option value="<?= $roleName; ?>"><?= $roleText; ?></option><?php
                                                }
                                                break;
                                        }
                                    }

                                if((new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->getActiveRole()->getName() == 'PortalAdministrator') {
                                    if(($this->view->quotas->getManagerTotal() - $this->view->quotas->getManagerUsed()) > 0
                                        && ($this->view->quotas->getApprecieSupplierTotal()-$this->view->quotas->getApprecieSupplierUsed())>0) {
                                        ?><option disabled>____________Dual Roles_____________</option><option value="-2">Manager and Supplier</option><?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="alert alert-info" role="alert" style="display: none;" id="quota-alert">
                                <p><?= _g('You have no quota left for this role - you may create the user but you will not be able to send them a sign-up to activate their account.'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="owner" class="col-sm-3 control-label"><?= _g('Owner'); ?></label>
                        <div class="col-sm-9">
                            {{ widget('UserFinderWidget','advanced') }}
                        </div>
                    </div>
                    <div style="margin: 20px;">
                        <table class="table table-highlight" id="quota">
                            <thead>
                            <tr>
                                <th><?= _g('Role'); ?></th>
                                <th><?= _g('Quota'); ?></th>
                                <th><?= _g('Used'); ?></th>
                                <th><?= _g('Available'); ?></th>
                            </tr>
                            <?php
                            foreach($this->view->roleHierarchy->getVisibleRoles() as $roleName => $roleText){
                                switch($roleName){
                                    case (\Apprecie\library\Users\UserRole::PORTAL_ADMIN):
                                        if($this->view->quotas->getPortalAdministratorTotal() > 0) {
                                            ?><tr><td><?= $roleText; ?></td><td><?= $this->view->quotas->getPortalAdministratorTotal(); ?></td><td><?= $this->view->quotas->getPortalAdministratorUsed(); ?></td><td><?= $this->view->quotas->getPortalAdministratorTotal()-$this->view->quotas->getPortalAdministratorUsed(); ?></td></tr><?php
                                        }
                                        break;
                                    case (\Apprecie\library\Users\UserRole::AFFILIATE_SUPPLIER):
                                        if($this->view->quotas->getAffiliateSupplierTotal() > 0) {
                                            ?><tr><td><?= $roleText; ?></td><td><?= $this->view->quotas->getAffiliateSupplierTotal(); ?></td><td><?= $this->view->quotas->getAffiliateSupplierUsed(); ?></td><td><?= $this->view->quotas->getAffiliateSupplierTotal()-$this->view->quotas->getAffiliateSupplierUsed(); ?></td></tr><?php
                                        }
                                        break;
                                    case (\Apprecie\library\Users\UserRole::APPRECIE_SUPPLIER):
                                        if($this->view->quotas->getApprecieSupplierTotal() > 0) {
                                            ?><tr><td><?= $roleText; ?></td><td><?= $this->view->quotas->getApprecieSupplierTotal(); ?></td><td><?= $this->view->quotas->getApprecieSupplierUsed(); ?></td><td><?= $this->view->quotas->getApprecieSupplierTotal()-$this->view->quotas->getApprecieSupplierUsed(); ?></td></tr><?php
                                        }
                                        break;
                                    case (\Apprecie\library\Users\UserRole::MANAGER):
                                        if($this->view->quotas->getManagerTotal() > 0) {
                                            ?><tr><td><?= $roleText; ?></td><td><?= $this->view->quotas->getManagerTotal(); ?></td><td><?= $this->view->quotas->getManagerUsed(); ?></td><td><?= $this->view->quotas->getManagerTotal()-$this->view->quotas->getManagerUsed(); ?></td></tr><?php
                                        }
                                        break;
                                    case (\Apprecie\library\Users\UserRole::INTERNAL):
                                        if($this->view->quotas->getInternalMemberTotal() > 0 && $this->view->quotas->getManagerTotal() > 0){
                                            ?><tr><td><?= $roleText; ?></td><td><?= $this->view->quotas->getInternalMemberTotal(); ?></td><td><?= $this->view->quotas->getInternalMemberUsed(); ?></td><td><?= $this->view->quotas->getInternalMemberTotal()-$this->view->quotas->getInternalMemberUsed(); ?></td></tr><?php
                                        }
                                        break;
                                    case (\Apprecie\library\Users\UserRole::CLIENT):
                                        if($this->view->quotas->getMemberTotal() > 0 && $this->view->quotas->getManagerTotal() > 0){
                                            ?><tr><td><?= $roleText; ?></td><td><?= $this->view->quotas->getMemberTotal(); ?></td><td><?= $this->view->quotas->getMemberUsed(); ?></td><td><?= $this->view->quotas->getMemberTotal()-$this->view->quotas->getMemberUsed(); ?></td></tr><?php
                                        }
                                        break;
                                }
                            }
                            ?>
                            </thead>
                            <tbody id="quota-table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px;">
                <button type="button" data-loading-text="Loading..." onclick="preValidateStepOne();" class="btn btn-primary pull-right">
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
                <p><?= _g("Welcome to the User Creation wizard. This process is designed to guide you step-by-step through the creation of a single User."); ?>
                <p><?= _g("On this initial page you are required to choose the type of User you want to create. Depending on your role, you will have a specific selection of the user types below:"); ?>
                <?php
                $auth= new \Apprecie\Library\Security\Authentication();
                $user=$auth->getAuthenticatedUser();
                switch($user->getActiveRole()->getName()){
                    case "Manager":
                        ?>
                        <p><strong><?= _g("Internal: "); ?></strong><?= _g("The Internal would be an employee situated below the Manager in the hierarchy. In most cases this will be the Relationship Manager who has the direct relationship with the Clients, but may also be other department employees, administrators, analysts, or agents. The Internal is an optional role, and not all Organisations may have them. Internals will only see content that is curated down to them by the Managers, and can create their own internal Items as well as more Users in the hierarchy below them."); ?>
                        <p><strong><?= _g("Client: "); ?></strong><?= _g("The Client is the ultimate end-user, and is usually the customer of the bank or Wealth Manager, though in some set-ups this role can also be used for further subsidiary internal roles, such as salespeople. The Client is the final consumer, and will be able to take advantage of all the Vault content provided to them by the Managers and Internals above. Clients can be classed as either Members (with login access to the Portal themselves), or Non-Members, who will remain in an off-line capacity and will be interacted with through remote processes."); ?>
                        <p><strong><?= _g("Family Member: "); ?></strong><?= _g("Some Clients may be able to invite their Family Members to also take advantage of the Portal benefits, and so can create Family Member roles to provide them with direct access. These roles follow the same structure and functionality as the Client role, and can consume Vault content that is curated down for their enjoyment."); ?>
                    <?php
                        break;
                    case "Internal":
                        ?>
                        <p><strong><?= _g("Client: "); ?></strong><?= _g("The Client is the ultimate end-user, and is usually the customer of the bank or Wealth Manager, though in some set-ups this role can also be used for further subsidiary internal roles, such as salespeople. The Client is the final consumer, and will be able to take advantage of all the Vault content provided to them by the Managers and Internals above. Clients can be classed as either Members (with login access to the Portal themselves), or Non-Members, who will remain in an off-line capacity and will be interacted with through remote processes."); ?>
                        <p><strong><?= _g("Family Member: "); ?></strong><?= _g("Some Clients may be able to invite their Family Members to also take advantage of the Portal benefits, and so can create Family Member roles to provide them with direct access. These roles follow the same structure and functionality as the Client role, and can consume Vault content that is curated down for their enjoyment."); ?>
                    <?php
                        break;
                    case "PortalAdministrator":
                       ?>
                        <p><strong><?= _g("Manager: "); ?></strong><?= _g("The Manager will usually be responsible for the day-to-day running of the Portal, and will be the one to monitor and control all incoming Vault content from other sources. In a real world environment this could range from a CEO or Department Head, to a Wealth Manager in a smaller Wealth Management company. Managers have access to most aspects of the Portal and can create Users and internal Items for their Organisation. It is mandatory to have at least 1 Manager in any Organisation."); ?>
                        <p><strong><?= _g("Internal: "); ?></strong><?= _g("The Internal would be an employee situated below the Manager in the hierarchy. In most cases this will be the Relationship Manager who has the direct relationship with the Clients, but may also be other department employees, administrators, analysts, or agents. The Internal is an optional role, and not all Organisations may have them. Internals will only see content that is curated down to them by the Managers, and can create their own internal Items as well as more Users in the hierarchy below them."); ?>
                        <p><strong><?= _g("Client: "); ?></strong><?= _g("The Client is the ultimate end-user, and is usually the customer of the bank or Wealth Manager, though in some set-ups this role can also be used for further subsidiary internal roles, such as salespeople. The Client is the final consumer, and will be able to take advantage of all the Vault content provided to them by the Managers and Internals above. Clients can be classed as either Members (with login access to the Portal themselves), or Non-Members, who will remain in an off-line capacity and will be interacted with through remote processes."); ?>
                        <p><strong><?= _g("Family Member: "); ?></strong><?= _g("Some Clients may be able to invite their Family Members to also take advantage of the Portal benefits, and so can create Family Member roles to provide them with direct access. These roles follow the same structure and functionality as the Client role, and can consume Vault content that is curated down for their enjoyment."); ?>
                    <?php
                    break;
                }
                ?>
                <p><?= _g("You will also now need to select who the owner of your created User is. Only the owner of a user has access to some specific interactions with that user and is ultimately the one 'responsible' for that user, so the owner of the user should usually be either the Manager or Relationship Manager (if it's a Client), or the Manager of the Organisation (if it's an Internal). After clicking 'Find User', you can search for a specific named person, or just a user type, to find the person you need. The available quotas for roles are shown below the fields."); ?>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-2">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= _g('Personal Details'); ?></h5>
            </div>
            <div class="ibox-content ibox-content-min">
                <div id="refAndName">
                    <div class="form-group">
                        <label for="reference-code" class="col-sm-3 control-label" id="ref-label"><?= _g('Reference Code'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="reference-code" name="reference-code" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group" id="or-group" style="display: none;">
                        <div class="col-sm-9 col-sm-offset-3" style="display: inline-block;">-or-</div>
                    </div>
                    <div class="form-group">
                        <label for="portalname" class="col-sm-3 control-label">* <?= _g('Title'); ?></label>
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
                        <label for="firstname" class="col-sm-3 control-label">* <?= _g('First Name'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="firstname" name="firstname" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-3 control-label">* <?= _g('Last Name'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="lastname" name="lastname" class="form-control"/>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="tier-group">
                    <label for="tier" class="col-sm-3 control-label">* <?= _g('Tier'); ?></label>
                    <div class="col-sm-9">
                        <select id="tier" name="tier" class="form-control full-width">
                            <option value="<?= \Apprecie\Library\Users\Tier::CORPORATE; ?>" selected><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::CORPORATE))->getExplanatoryText(); ?></option>
                            <option value="<?= \Apprecie\Library\Users\Tier::ONE; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::ONE))->getExplanatoryText(); ?></option>
                            <option value="<?= \Apprecie\Library\Users\Tier::TWO; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::TWO))->getExplanatoryText(); ?></option>
                            <option value="<?= \Apprecie\Library\Users\Tier::THREE; ?>"><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::THREE))->getExplanatoryText(); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="emailaddress" class="col-sm-3 control-label" id="emailaddress-label"><?= _g('Email Address'); ?></label>
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
                <button type="button" data-loading-text="Loading..." onclick="setStep(1);" class="btn btn-default pull-left">
                    <?= _g('Previous'); ?>
                </button>
                <button type="button" data-loading-text="Loading..." onclick="preValidateStepTwo();" class="btn btn-primary pull-right">
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
<div class="row step" id="step-3">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
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
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Here you can determine what sort of notifications and messages the user should or shouldn't receive. The User can confirm/edit these options later from their own profile."); ?>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-4">
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
                <button type="button" data-loading-text="Loading..." onclick="setStep(3);" class="btn btn-default pull-left">
                    <?= _g('Previous'); ?>
                </button>
                <button type="button" data-loading-text="Loading..." onclick="setStep(5);" class="btn btn-primary pull-right">
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
<div class="row step" id="step-5">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
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
<div class="row step" id="step-6">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
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
                <a href="/people" class="btn btn-primary pull-right">
                    <?= _g('User Management'); ?>
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
                <p><?= _g("Now that you have created your user (who will currently be marked as 'Unregistered') you have a few options to take.");?>
                <p><?= _g("If you intend to sign-up the user for access right away (if the user is an employee user or a Client Member), you can send them a sign-up email directly from the system by clicking Send sign-up Email. This will automatically send them a fully branded email informing them of you granting them access to the system, with a link to take them through the step-by-step sign-up wizard. Upon completing this, the User will be marked as 'Member' in the People page.");?>
                <p><?= _g("If you intend to sign-up the user, but wish to contact them in a more personal manner, you may copy the sign-up link as provided below the Send sign-up Email button, and paste it into your own email from your own email software. This is also the way to go if the user you have added does not have an email address entered (especially for anonymous users).");?>
                <p><?= _g("Finally, if you don't wish to sign-up the user yet, or you have already taken one of the options above, you can click on 'User Management' to return to the People page and view the users you currently have on the Portal.");?>
            </div>
        </div>
    </div>
</div>
</form>
<div class="col-sm-6" id="progress-message"></div>
<?= (new EmailWidget('index', array('templateType' => null, 'callback' => 'sendSignUp', 'previewData' => null)))->getContent(); ?>