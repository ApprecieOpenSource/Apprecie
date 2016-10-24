var userLookupVisibilityRoleId=null;
var userLookupRoleId=null;
var userLookupPortalId=null;
var userLookupFirstName=null;
var userLookupLastName=null;
var userLookupOrganisationId=null;
/**
 *
 * @param roleId The role we want to look down from
 * @param portalId the portal to look in
 */
function initialiseUserLookup(roleName,portalId,organisationId){
    setUserLookupPortalId(portalId);
    setUserLookupVisibilityRoleId(roleName);
    setUserLookupVisibilityOrganisationId(organisationId);
}
function setUserLookupVisibilityRoleId(roleId){
    userLookupVisibilityRoleId=roleId;
    //populateRoleSelection();
}
function setUserLookupVisibilityOrganisationId(organisationId){
    userLookupOrganisationId=organisationId;
}

function setUserLoopupRoleID(roleId){
    userLookupRoleId=roleId;
}

function setUserLookupPortalId(portalId){
    userLookupPortalId=portalId;
}

function setUserLookupFirstName(firstName){
    userLookupFirstName=firstName;
}

function setUserLookupLastName(lastName){
    userLookupLastName=lastName;
}

function getUserLookupResults(){
    console.log(userLookupPortalId);
    return $.ajax({
        url: "/api/userLookup",
        type: 'post',
        dataType: 'json',
        cache: false,
        data:{roleId:userLookupRoleId,portalId:userLookupPortalId,firstName:userLookupFirstName,lastName:userLookupLastName,organisationId:userLookupOrganisationId, CSRF_SESSION_TOKEN:CSRF_SESSION_TOKEN}
    });
}

function populateRoleSelection(){
    $('#user-lookup-role').empty();
    var filterRoles=getRoleVisibility();
    $(filterRoles).each(function(key, value){
        $('#user-lookup-role').append('<option value="'+value+'">'+value+'</option>');
    })
}

function performUserLookup(){
    setUserLoopupRoleID($('#user-lookup-role').val());
    setUserLookupFirstName($('#user-lookup-first-name').val());
    setUserLookupLastName($('#user-lookup-last-name').val());
    loader(true);
    $.when(getUserLookupResults()).then(function(data){
        $('#user-lookup-results-table').empty();
        $(data).each(function(key, value){
            var row=
                '<tr onclick="selectUser('+value.userId+',\''+escapeHtml(value.firstname+' '+value.lastname)+'\')">'+
                    '<td>'+value.reference+'</td>'+
                    '<td>'+value.firstname+'</td>'+
                    '<td>'+value.lastname+'</td>'+
                    '<td class="hidden-xs">'+value.email+'</td>'+
                '</tr>';
            $('#user-lookup-results-table').append(row);
            $('#user-lookup-table').show();
        })
        loader(false);
    })
}

function selectUser(userId,name){
    $('#user-lookup-value').val(userId);
    $('#user-lookup-name').val(name);
    $('#myModal').modal('toggle');
}

function getRoleVisibility(){
    switch(userLookupVisibilityRoleId){
        case 'SystemAdministrator':
            return [["SystemAdministrator"]];
            break;
        case 'PortalAdministrator':
            return ["SystemAdministrator","PortalAdministrator"];
            break;
        case 'Manager':
            return ["SystemAdministrator", "PortalAdministrator"];
            break;
        case 'Internal':
            return ["SystemAdministrator", "PortalAdministrator", "Manager"];
            break;
        case 'ApprecieSupplier':
            return ["SystemAdministrator", "PortalAdministrator"];
            break;
        case 'AffiliateSupplier':
            return ["SystemAdministrator", "PortalAdministrator"];
            break;
        case 'Client':
            return ["SystemAdministrator", "PortalAdministrator", "Manager","Internal"];
            break;
    }
}