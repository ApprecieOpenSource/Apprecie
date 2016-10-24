/**
 * Created by hu86 on 03/11/2015.
 */

function preCreateUser() {
    clearErrors('#create-user-success', '#create-user-error');
    validateReferenceOrName($('#reference-code').val(), $('#firstname').val(), $('#lastname').val());
    if ($('#emailaddress').val()) {
        validateEmailAddress($('#emailaddress').val());
    }
    if (errors.length != 0){
        displayErrors('#create-user-error');
    } else {
        if ($('#emailaddress').val()) {
            $.when(getEmailInUse(portalId, $('#emailaddress').val())).then(function(data) {
                if (data.users != 0) {
                    errors.push('This email address is already in use');
                    displayErrors('#create-user-error');
                } else {
                    createUser();
                }
            });
        } else {
            createUser();
        }
    }
}