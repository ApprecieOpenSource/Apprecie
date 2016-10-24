/**
 * Created by hu86 on 03/11/2015.
 */

$(document).ready(function() {
    $('#create-user-modal-btn').on('click', preCreateUser);
});

function showCreateUserModal() {
    $('#create-user-modal').modal('show');
}

function createUser() {
    $('#create-user-modal-btn').prop('disabled',true);
    $.when(ajaxCreateUser()).then(function(data) {
        if(data.status === 'success'){
            if (refreshOnSuccess) {
                window.location.reload();
            } else {
                $('#create-user-modal-btn').prop('disabled',false);
                displaySuccess('User Created Successfully', '#create-user-success');
            }
        } else {
            while (errors.length) {
                errors.pop();
            }
            errors.push('An Error Occurred');
            $('#create-user-modal-btn').prop('disabled',false);
            displayErrors('#create-user-error');
        }
    });
}

function ajaxCreateUser() {
    return $.ajax({
        url: "/api/quickCreateUser",
        type: 'post',
        dataType: 'json',
        cache: false,
        data: $('#create-user-form').serialize()
    });
}

function validateReferenceOrName(reference, firstName, lastName) {
    if (reference == '' && (firstName == '' || lastName == '')) {
        errors.push('You must provide either a Reference or a First Name and Last Name');
    }
}

function validateEmailAddress(emailAddress) {
    console.log(emailAddress);
    if (!validateEmail(emailAddress)) {
        errors.push('Please enter a valid email address');
    }
}