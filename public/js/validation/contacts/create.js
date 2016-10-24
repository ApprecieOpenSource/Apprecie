/**
 * Created by huwang on 19/11/2015.
 */

function validateStep(stepID) {
    clearErrors();
    switch (stepID) {
        case 1:
            ValidateReferenceOrName($('#reference-code').val(), $('#firstname').val(), $('#lastname').val());
            ValidateUserEmailAddress($('#emailaddress').val());
            ValidateUserDateOfBirth($('#dob-formatted').val());
            if(!validateTelephoneNumber($('#phone').val())){
                errors.push('Please provide a valid telephone number');
            }
            if(!validateTelephoneNumber($('#mobile').val())){
                errors.push('Please provide a valid mobile number');
            }
            break;
    }
    return (errors.length == 0);
}

function ValidateReferenceOrName(reference, firstName, lastName){
    if(reference=='' && (firstName=='' || lastName=='')) {
        errors.push('You must provide either a Reference or a First Name and Last Name')
    }
}

function ValidateUserEmailAddress(emailAddress){
    if(emailAddress != '' && !validateEmail(emailAddress)) {
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