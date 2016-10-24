$(document).ready(function(){
    $('#share-portal').change(function(){
        getPortalOrganisations($(this).val());
    })
})
function getPortalOrganisations(portalId){
    $('#curate-organisation-role').empty();
    $.when(AjaxGetPortalOrganisations(portalId)).then(function(data){
        $.each(data, function(key, value) {
            var buffer='<tr><td><input type="checkbox" name="organisations[]" value="'+value.organisationId+'"/> '+value.organisationName+'</td></tr>';
            $('#curate-organisation-role').append(buffer);
        });
        $('#curate-organisation-role-table').show();
    })
}

function AjaxGetPortalOrganisations(portalId){
    return $.ajax({
        url: "/api/getPortalOrganisations/"+portalId+'/true',
        type: 'post',
        dataType: 'json',
        cache: false,
        data:{CSRF_SESSION_TOKEN:CSRF_SESSION_TOKEN}
    });
}

function CurateToRolesInOrganisation(){
    $('#curate-roles-btn').prop('disabled',true).html('Adding...');
    $.when(AjaxCurateToRolesInOrganisation()).then(function(data){
        $('#curate-roles-btn').prop('disabled',false).html('Add');
        location.reload();
    })
}

function AjaxCurateToRolesInOrganisation(){
    return $.ajax({
        url: "/items/AjaxCurateToRolesInOrganisation/"+itemId,
        type: 'post',
        dataType: 'json',
        cache: false,
        data: $('#curate-roles-form').serialize()
    });
}

function Approve(itemId){
    $('#approve-item-confirm-btn').prop('disabled',true);
    var approval=new approveItem(itemId,$('#administration-fee').val(),$('#reservation-fee').val(),$('#reservation-period').val(),$('#reservation-toggle').is(':checked'));
    $.when(approval.fetch()).then(function(data){
        if(data.status !== 'success'){
            $('#approval-success').stop().hide();
            $('#approval-failed-x').stop().hide().html(data.message).fadeIn('fast');
            $('#approve-item-confirm-btn').prop('disabled',false);
        }
        else{
            $('#approval-failed-x').stop().hide();
            $('#approval-success').stop().hide().html(data.message).fadeIn('fast');
            $('#approval-group').hide();
            $('#curation-group').show();
            $('#approve-item').modal('hide');
        }
        $('#approve-item-confirm-btn').prop('disabled',false);
    })
}

function ApproveEdit(itemId) {
    $('#approve-edit-item-confirm-btn').prop('disabled',true);
    var approval=new approveItem(itemId,$('#administration-fee-edit').val(),$('#reservation-fee-edit').val(),$('#reservation-period-edit').val(),$('#reservation-toggle-edit').is(':checked'));
    $.when(approval.fetch()).then(function(data){
        if(data.status !== 'success'){
            $('#approval-success').stop().hide();
            $('#approval-failed-x-edit').stop().hide().html(data.message).fadeIn('fast');
            $('#approve-edit-btn').prop('disabled',false);
        }
        else{
            $('#approval-failed-x-edit').stop().hide();
            $('#approval-success').stop().hide().html(data.message).fadeIn('fast');
            $('#curation-group').show();
            $('#approve-edit-item').modal('hide');
        }
        $('#approve-edit-item-confirm-btn').prop('disabled',false);
    })
}

function Reject(itemId){
    $('#reject-item-confirm-btn').prop('disabled',true);
    var reject=new rejectItem(itemId,$('#reject-reason').val());
    $.when(reject.fetch()).then(function(data){
        if(data.status=='failed'){
            $('#approval-success').stop().hide();
            $('#approval-failed').stop().hide().html(data.message).fadeIn('fast');
        }
        else{
            $('#approval-failed').stop().hide();
            $('#approval-success').stop().hide().html(data.message).fadeIn('fast');
            $('#approval-group').hide();
        }
        $('#reject-item').modal('toggle');
        $('#reject-item-confirm-btn').prop('false',true);
        console.log(data);
    })
}