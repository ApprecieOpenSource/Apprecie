var lookupUsers=[];
function UserLookupAdvancedInit(newUserRole,organisationId){
    populateOwnerRoleSelection(newUserRole);
}

function getOwnerRules(newUserRole){
    switch(newUserRole){
        case "PortalAdministrator":
                return [["PortalAdministrator","Organisation Owner"]];
            break;
        case "Manager":
            return [["PortalAdministrator","Organisation Owner"]];
            break;
        case "Internal":
            return [["Manager","Manager"]];
            break;
        case "Client":
            return [["Manager","Manager"],["Internal","Internal"]];
            break;
        case "ApprecieSupplier":
            return [["PortalAdministrator","Organisation Owner"]];
            break;
        case "AffiliateSupplier":
            return [["PortalAdministrator","Organisation Owner"]];
            break;
    }
}

function populateOwnerRoleSelection(newUserRole){
    if(newUserRole == -2) {
        newUserRole = 'Manager';    //make the dual supplier and Manager role,  detect owner as manager
    }
    var ownerRoles=getOwnerRules(newUserRole);
    $('#user-lookup-role').empty();
    $.each(ownerRoles, function( index, value ) {
        $('#user-lookup-role').append('<option value="'+value[0]+'">'+value[1]+'</option>');
    });
}

function UserLookupAdvancedSearch(role){
    $('#user-lookup-results-table').empty();
    var userBtn=$('#search-widget-btn');

    userBtn.prop('disabled',true);
    $.when(AjaxUserLookupAdvancedSearch(role)).then(function(data){
        lookupUsers=data;
        $.each(data, function( index, value ) {
            var row=
                '<tr onclick="selectUser('+value.userId+','+index+')">'+
                    '<td>'+value.reference+'</td>'+
                    '<td>'+value.firstname+'</td>'+
                    '<td>'+value.lastname+'</td>'+
                    '<td class="hidden-xs">'+value.email+'</td>'+
                    '</tr>';
            $('#user-lookup-results-table').append(row);
        });
        if(data.length==0){
            var row=
                '<tr>'+
                '<td colspan="4">No users were found</td>'+
                '</tr>';
            $('#user-lookup-results-table').append(row);
        }
        userBtn.prop('disabled', false);
        $('#user-lookup-table').show();
    })
}

function AjaxUserLookupAdvancedSearch(role){
    return $.ajax({
        url: "/people/ownerlookup/"+role,
        type: 'post',
        dataType: 'json',
        cache: false,
        data:{"user-lookup-first-name":$('#user-lookup-first-name').val(),"user-lookup-last-name":$('#user-lookup-last-name').val(),"user-lookup-role":$('#user-lookup-role').val(), "CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
    });
}

function selectUser(userId,index){
    var user=lookupUsers[index];
    $('#user-lookup-value').val(userId);
    $('#user-lookup-name').val(user.firstname+' '+user.lastname);
    $('#user-lookup-modal').modal('toggle');
}