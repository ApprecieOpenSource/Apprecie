/**
 * Created by hu86 on 03/11/2015.
 */

function preCreateUser() {
    clearErrors('#create-user-success', '#create-user-error');
    validateReferenceOrName($('#reference-code').val(), $('#firstname').val(), $('#lastname').val());
    validateEmailAddress($('#emailaddress').val());
    if (errors.length != 0){
        displayErrors('#create-user-error');
    } else {
        $.when(getEmailInUse(portalId, $('#emailaddress').val())).then(function(data) {
            if (data.users != 0) {
                errors.push('This email address is already in use');
                displayErrors('#create-user-error');
            } else {
                createUser();
            }
        });
    }
}