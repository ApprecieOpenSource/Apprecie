/**
 * Created by Daniel on 06/12/14.
 */
var steps=0;
var stepper=0;

function setSteps(numberOfSteps){
    steps=numberOfSteps;
    stepper=(100/steps);
}
function setStep(stepID){
    clearErrors();

    if(stepID==steps){
        loader(true);
        $('#complete-btn').prop('disabled',true);
        $('#complete-btn').html('Loading...');
        $.when(updateUser()).then(function(data){
            loader(false);
            $('.step').hide();
            $('#step-'+stepID).show();
        })
    }
    else{
        $('.step').hide();
        $('#step-'+stepID).show();
    }
}

function validateStep(stepID){
    clearErrors();
    switch(stepID){
        case 1:

            break;
        case 2:
            ValidateFullUser($('#firstname').val(),$('#lastname').val(),$('#emailaddress').val());
            ValidateUserDateOfBirth($('#dob-formatted').val());
            if(!validateTelephoneNumber($('#phone').val())){
                errors.push('The telephone number can only contain digits');
            }
            if(!validateTelephoneNumber($('#mobile').val())){
                errors.push('The mobile number can only contain digits');
            }
            break;
        case 3:

            break;
        case 5:

            break;
        case 6:
            validatePassword($('#password').val(),$('#confirm-password').val());
            validateTerms($('#i-agree'));
            break;
    }
    if(errors.length==0){
        setStep(stepID+1);
    }
    else{
        displayErrors();
    }
}

function validatePassword(password,confirmPassword){
    if(password=='' || password==null || confirmPassword=='' || confirmPassword==null){
        errors.push('You must provide a password');
    }
    else if(password!=confirmPassword){
        errors.push('Password and confirm password do not match');
    }
    else if(password.length<8 || password.length>25){
        errors.push('Password must be between 8 and 25 characters');
    }
    else if(!password.match(/([a-zA-Z])/) || !password.match(/([0-9])/)) {
        errors.push('Password must include at least one number and one letter');
    }
}

function validateTerms(element){
    if (!element.is(':checked')) {
        errors.push('You must agree to the terms and conditions and privacy policy');
    }
}
function ValidateFullUser(firstName, lastName, emailAddress){

    if(firstName=='' || firstName==null){
        errors.push('You must provide your first name');
    }

    if(lastName=='' || lastName==null){
        errors.push('You must provide your last name');
    }

    if(!validateEmail(emailAddress)){
        errors.push('Please enter a valid email address');
    }
}

function ValidateUserDateOfBirth(date){
    if(date != ''){
        var dateObj = new moment(date,'DD-MM-YYYY');

        if(dateObj.isValid()){
            if(moment().diff(dateObj, 'years') < 18 || moment().diff(dateObj, 'years') > 120){
                errors.push('User must be aged 18 or over');
            }
        } else {
            errors.push('Invalid date of birth entered (DD/MM/YYYY)');
        }
    }
}

function updateUser() {
    return $.ajax({
        url: "/signup/ajaxupdateuser",
        type: 'post',
        dataType: 'json',
        cache: false,
        data: $('#user-form').serialize()
    });
}

function verifyUser() {
    return $.ajax({
        url: "/signup/ajaxverifyuser",
        type: 'post',
        dataType: 'json',
        cache: false,
        data: {'emailaddressx':$('#emailaddressx').val(),'token':$('#token').val(), "CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
    });
}