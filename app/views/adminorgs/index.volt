<style>
    .orgChart{
        overflow-x: auto;
    }
</style>
<script>
    $(function () {
        processChart();
        $('#portalid').change(function () {
            processChart();
            $.when(getAllOrganisations()).then(function () {

            })
        });
    });

    var organisations = [];

    function getChildren(organisationId) {
        return $.ajax({
            url: "/api/getOrganisationChildren/" + organisationId,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {CSRF_SESSION_TOKEN: CSRF_SESSION_TOKEN}
        });
    }

    function getAllOrganisations() {
        return $.ajax({
            url: "/api/portalOrganisations/",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {
                CSRF_SESSION_TOKEN: CSRF_SESSION_TOKEN,
                portalId: $('#portalid').val()
            }
        });
    }

    function getParentOrganisation() {
        return $.ajax({
            url: "/api/getPrimaryOrganisation/" + $('#portalid').val(),
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {CSRF_SESSION_TOKEN: CSRF_SESSION_TOKEN}
        });
    }

    function processChart() {
        $('#organisation').empty();
        organisations = [];
        $.when(getParentOrganisation()).then(function (data) {
            $(data).each(function (key, value) {
                organisations.push(value);
                $('#organisation').append('<li id="' + value.organisationId + '"><a href="/adminorgs/view/' + value.organisationId + '">' + value.name + '</a><i class="fa fa-pencil-square-o node-details" title="Edit Organisation" onclick="organisationEdit(' + value.organisationId + ')"></i> <i class="fa fa-sitemap node-edit" title="Add Children" onclick="organisationAdd(' + value.organisationId + ')"></i><ul id="' + value.organisationId + '-children"></ul></li>');
                $("#organisation").orgChart({container: $("#main")});
                processChildren(value.organisationId);
            })
        });
    }

    function processChildren(organisationId) {
        $.when(getChildren(organisationId)).then(function (data) {
            $(data).each(function (key, value) {
                organisations.push(value);
                addNode(organisationId, value.organisationId, value.name);
                processChildren(value.organisationId);
            })
        })
    }

    function getOrganisationDetails(organisationId) {
        return $.ajax({
            url: "/api/getOrganisation/" + organisationId,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {CSRF_SESSION_TOKEN: CSRF_SESSION_TOKEN}
        });
    }

    function organisationAdd(orgainsationId) {
        $('#add-organisation-id').val(orgainsationId);
        $('#edit-error-container').html('');
        $('#add-organisation-name').val('');
        $('#add-quota-portal-administrators').val(0);
        $('#add-quota-managers').val(0);
        $('#add-quota-internal-members').val(0);
        $('#add-quota-apprecie-suppliers').val(0);
        $('#add-quota-affiliate-suppliers').val(0);
        $('#add-quota-members').val(0);
        $('#add-quota-family-members').val(0);
        $('#add-quota-commission').val(0)
        $('#add-organisation-subdomain').val('');
        $('#create-error-container').html('<div class="alert alert-danger" role="alert" id="error-box" style="display: none;"></div>');
        $('#add-organisation').modal('show');
    }

    function organisationEdit(orgainsationId) {
        $('#create-error-container').html('');
        $('#edit-error-container').html('<div class="alert alert-danger" role="alert" id="error-box" style="display: none;"></div>');

        $.when(getOrganisationDetails(orgainsationId)).then(function (data) {

            $('#organisation-name').val(data.organisation.organisationName);
            $('#quota-portal-administrators').val(data.quota.portalAdministratorTotal);
            $('#quota-portal-administrators-ex').html(data.quota.portalAdministratorUsed + " Used");
            $('#quota-managers').val(data.quota.managerTotal);
            $('#quota-managers-ex').html(data.quota.managerUsed + " Used");
            /*
             if(data.organisation.isPortalOwner==1){
             $('#organisation-subdomain').prop('disabled',true);
             }
             else{
             $('#organisation-subdomain').prop('disabled',false);
             }
             */

            $('#quota-internal-members').val(data.quota.internalMemberTotal);
            $('#quota-internal-members-ex').html(data.quota.internalMemberUsed + " Used");
            $('#quota-apprecie-suppliers').val(data.quota.apprecieSupplierTotal);
            $('#quota-apprecie-suppliers-ex').html(data.quota.apprecieSupplierUsed + " Used");
            $('#quota-affiliate-suppliers').val(data.quota.affiliateSupplierTotal);
            $('#quota-affiliate-suppliers-ex').html(data.quota.affiliateSupplierUsed + " Used");
            $('#quota-members').val(data.quota.memberTotal);
            $('#quota-members-ex').html(data.quota.memberUsed + " Used");
            $('#quota-family-members').val(data.quota.memberFamilyTotal);
            $('#quota-family-members-ex').html(data.quota.memberFamilyUsed + " Used");
            $('#quota-commission').val(data.quota.commissionPercent);
            $('#edit-organisation-id').val(orgainsationId);
            $('#organisation-subdomain').val(data.organisation.subDomain);
            if (data.organisation.suspended == 1) {
                $('#suspended').prop('checked', true);
            } else {
                $('#suspended').prop('checked', false);
            }
            $('#edit-organisation').modal('show');
        })
    }

    function createOrganisation() {
        clearErrors();
        validateQuota($('#add-quota-portal-administrators'), 'Organisation Owner');
        validateQuota($('#add-quota-managers'), 'Managers');
        validateQuota($('#add-quota-internal-members'), 'Internal');
        validateQuota($('#add-quota-apprecie-suppliers'), 'Apprecie Suppliers');
        validateQuota($('#add-quota-affiliate-suppliers'), 'Affiliated Suppliers');
        validateQuota($('#add-quota-members'), 'Members');
        validateQuota($('#add-quota-family-members'), 'Family Members');
        validateQuotaPercentage($('#add-quota-commission'), 'Commission');
        validateOrganisationName($('#add-organisation-name').val());
        validateOrganisationSubdomain($('#add-organisation-subdomain'));

        if (errors.length != 0) {
            displayErrors();
        } else {
            var createBtn = $('#create-btn');
            createBtn.prop('disabled', true);
            $.when(addOrganisation()).then(function () {
                createBtn.prop('disabled', false);
                $('#add-organisation').modal('hide');
                processChart();
            });
        }
    }

    function updateOrganisation() {
        clearErrors();
        validateQuota($('#quota-portal-administrators'), 'Organisation Owner');
        validateQuota($('#quota-managers'), 'Managers');
        validateQuota($('#quota-internal-members'), 'Internal Members');
        validateQuota($('#quota-apprecie-suppliers'), 'Apprecie Suppliers');
        validateQuota($('#quota-affiliate-suppliers'), 'Affiliated Suppliers');
        validateQuota($('#quota-members'), 'Members');
        validateQuota($('#quota-family-members'), 'Family Members');
        validateOrganisationSubdomain($('#organisation-subdomain'), $('#edit-organisation-id').val());
        validateQuotaPercentage($('#quota-commission'), 'Commission');
        validateOrganisationName($('#organisation-name').val());
        if (errors.length != 0) {
            displayErrors();
        } else {
            $.when(saveOrganisation()).then(function (data) {
                if (data.status != 'success') {
                    $('#edit-error-container').html('<div class="alert alert-danger" role="alert" id="error-box">' + data.message + '</div>');
                } else {
                    $('#edit-organisation').modal('hide');
                    processChart();
                }
            });
        }
    }

    function validateOrganisationSubdomain(element, organisationId) {

    }


    function saveOrganisation() {
        return $.ajax({
            url: "/adminorgs/saveOrganisation/",
            type: 'post',
            dataType: 'json',
            data: $('#edit-organisation-form').serialize(),
            cache: false
        });
    }

    function deleteOrganisation() {
        clearErrors();
        organisationId = $('#edit-organisation-id').val();
        $.when(getChildren(organisationId)).then(function (data) {
            if ($(data).length != 0) {
                errors.push('cannot delete organisation as it has children');
                displayErrors();
            } else {
                $.when(getOrganisationUsers(organisationId)).then(function (data) {
                    if ($(data).length != 0) {
                        errors.push('cannot delete organisation as it has users associated with it');
                        displayErrors();
                    } else {
                        $.when(confirmDeleteOrganisation(organisationId)).then(function (data) {
                            $('#edit-organisation').modal('hide');
                            processChart();
                        })
                    }
                })
            }
        });
    }

    function getOrganisationUsers(organisationId) {
        return $.ajax({
            url: "/api/getOrganisationUsers/" + organisationId,
            type: 'post',
            dataType: 'json',
            data: {'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN},
            cache: false
        });
    }

    function confirmDeleteOrganisation(organisationId) {
        return $.ajax({
            url: "/adminorgs/ajaxDelete/",
            type: 'post',
            dataType: 'json',
            data: {'organisationId': organisationId, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN},
            cache: false
        });
    }

    function addOrganisation() {
        return $.ajax({
            url: "/adminorgs/addOrganisation/",
            type: 'post',
            dataType: 'json',
            data: $('#add-organisation-form').serialize(),
            cache: false
        });
    }


    function validateQuota(element, name) {
        if (isNaN((element.val())) || element.val() < 0) {
            errors.push('Quota for ' + name + ' is invalid');
        }
    }

    function validateOrganisationName(element) {
        if (element.length < 3 || element.length > 45) {
            errors.push('The organisation name must be between 3 and 45 characters');
        }
    }

    function validateQuotaPercentage(element, name) {
        if (isNaN((element.val())) || element.val() < 0 || element.val() > 100) {
            errors.push('Quota for ' + name + ' is invalid');
        }
    }

    function addNode(parentId, childId, childName) {
        $('#' + parentId + '-children').append('<li id="' + childId + '"><a href="/adminorgs/view/' + childId + '">' + childName + '</a><i class="fa fa-pencil-square-o node-details" title="Edit Organisation" onclick="organisationEdit(' + childId + ')"></i> <i class="fa fa-sitemap node-edit" title="Add Children" onclick="organisationAdd(' + childId + ')"></i><ul id="' + childId + '-children"></ul></li>');
        $("#organisation").orgChart({container: $("#main")});
    }
</script>
<script src="/js/orgchart.js"></script>
<script src="/js/validation/errors.js"></script>

<link href="/css/orgchart.css" rel="stylesheet">

<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Organisation Management'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5><?= _g('Organisational Structure of'); ?>
                &nbsp;
                <select id="portalid" name="portalid">
                    <?php foreach($this->view->portals as $portal): ?>
                        <option <?php if(isset($this->view->selectedPortal) && $portal->getPortalId()==$this->view->selectedPortal->getPortalId()){echo 'selected';} ?> value="<?= $portal->getPortalId();?>"><?= $portal->getPortalName(); ?></option>
                    <?php endforeach ?>
                </select>
            </h5>
        </div>
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-content">
                <ul id="organisation" style="display: none;"></ul>
                <div id="main"></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="edit-organisation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Organisation</h4>
            </div>
            <div class="modal-body">
                <p class="alert alert-info">All indicated license totals are correct at the time of page load, but do not represent changes since this dialog was opened.</p>
                <div id="edit-error-container">
                </div>
                <form class="form-horizontal" id="edit-organisation-form">
                    <?php echo Apprecie\Library\Security\CSRFProtection::csrf(); ?>
                    <div class="form-group">
                        <label for="organisation-name" class="col-sm-4 control-label">Organisation Name</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="organisation-name" id="organisation-name" placeholder="organisation name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="organisation-name" class="col-sm-4 control-label">Subdomain</label>
                        <div class="col-sm-8">
                            <input class="form-control" disabled name="organisation-subdomain" id="organisation-subdomain" placeholder="organisation subdomain">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota-portal-administrators" class="col-sm-4 control-label">Organisation Owners</label>
                        <div class="col-sm-8">
                             <input type="text" class="form-control number-field pull-left" id="quota-portal-administrators" name="quota-portal-administrators" value="0"> <span id="quota-portal-administrators-ex" style="margin-left: 10px;" class="alert-warning pull-left"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota-managers" class="col-sm-4 control-label">Managers</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field pull-left" id="quota-managers" name="quota-managers" value="0"> <span id="quota-managers-ex" style="margin-left: 10px;" class="alert-warning pull-left"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota-internal-members" class="col-sm-4 control-label">Internal Members</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field pull-left" id="quota-internal-members" name="quota-internal-members" value="0">  <span id="quota-internal-members-ex" style="margin-left: 10px;" class="alert-warning pull-left"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota-apprecie-suppliers" class="col-sm-4 control-label">Apprecie Suppliers</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field pull-left" id="quota-apprecie-suppliers" name="quota-apprecie-suppliers" value="0"> <span id="quota-apprecie-suppliers-ex" style="margin-left: 10px;" class="alert-warning pull-left"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota-affiliate-suppliers" class="col-sm-4 control-label">Affiliated Suppliers</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field pull-left" id="quota-affiliate-suppliers" name="quota-affiliate-suppliers" value="0"> <span id="quota-affiliate-suppliers-ex" style="margin-left: 10px;" class="alert-warning pull-left"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota-members" class="col-sm-4 control-label pull-left">Client Members</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field pull-left" id="quota-members" name="quota-members" value="0"> <span id="quota-members-ex" class="alert-warning pull-left" style="margin-left: 10px;"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota-family-members" class="col-sm-4 control-label">Family Members Per Client</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="quota-family-members" name="quota-family-members" value="5">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quota-commission" class="col-sm-4 control-label">Commission %</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="quota-commission" name="quota-commission" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="suspended" class="col-sm-4 control-label">Suspended</label>
                        <div class="col-sm-8">
                            <input type="checkbox" id="suspended" name="suspended" value="1">
                        </div>
                    </div>
                    <input type="hidden" id="edit-organisation-id" name="edit-organisation-id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" onclick="deleteOrganisation()">Delete</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateOrganisation()">Save changes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="add-organisation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Organisation</h4>
            </div>
            <div class="modal-body">
                <div id="create-error-container">
                </div>
                <form class="form-horizontal" id="add-organisation-form">
                    <?php echo Apprecie\Library\Security\CSRFProtection::csrf(); ?>
                    <div class="form-group">
                        <label for="affiliate-supplier" class="col-sm-4 control-label">Affiliated Supplier</label>
                        <div class="col-sm-8">
                            <input type="checkbox" name="affiliate-supplier" id="affiliate-supplier" value="1"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="organisation-language" class="col-sm-4 control-label">Organisation Language</label>
                        <div class="col-sm-8">
                            <select name="organisation-language" id="organisation-language" class="form-control">
                                <?php foreach(Languages::findBy('enabled', 1) as $language): ?>
                                    <option value="<?= $language->languageId; ?>"><?= $language->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-organisation-name" class="col-sm-4 control-label">Organisation Name</label>
                        <div class="col-sm-8">
                            <input class="form-control" name="add-organisation-name" id="add-organisation-name" placeholder="Organisation Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="organisation-name" class="col-sm-4 control-label">Subdomain</label>
                        <div class="col-sm-8">
                            <input class="form-control" disabled name="add-organisation-subdomain" id="add-organisation-subdomain" placeholder="Organisation Subdomain">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-quota-portal-administrators" class="col-sm-4 control-label">Organisation Owner</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="add-quota-portal-administrators" name="add-quota-portal-administrators" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-quota-managers" class="col-sm-4 control-label">Managers</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="add-quota-managers" name="add-quota-managers" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-quota-internal-members" class="col-sm-4 control-label">Internal Members</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="add-quota-internal-members" name="add-quota-internal-members" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-quota-apprecie-suppliers" class="col-sm-4 control-label">Apprecie Suppliers</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="add-quota-apprecie-suppliers" name="add-quota-apprecie-suppliers" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-quota-affiliate-suppliers" class="col-sm-4 control-label">Affiliated Suppliers</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="add-quota-affiliate-suppliers" name="add-quota-affiliate-suppliers" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-quota-members" class="col-sm-4 control-label">Client Member</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="add-quota-members" name="add-quota-members" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-quota-family-members" class="col-sm-4 control-label">Family Members Per Client</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="add-quota-family-members" name="add-quota-family-members" value="5">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add-quota-commission" class="col-sm-4 control-label">Commission %</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control number-field" id="add-quota-commission" name="add-quota-commission" value="0">
                        </div>
                    </div>
                    <input type="hidden" id="add-organisation-id" name="add-organisation-id" value=""/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="createOrganisation()" id="create-btn">Create</button>
            </div>
        </div>
    </div>
</div>