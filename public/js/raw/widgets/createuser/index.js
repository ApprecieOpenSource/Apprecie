/**
 * Created by hu86 on 30/10/2015.
 */

function preCreateUser() {
    clearErrors('#create-user-success', '#create-user-error');
    validateReferenceOrName($('#reference-code').val(), $('#firstname').val(), $('#lastname').val());
    if (errors.length != 0){
        displayErrors('#create-user-error');
    } else {
        createUser();
    }
}