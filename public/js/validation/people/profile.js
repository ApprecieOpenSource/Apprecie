/**
 * Created by Daniel on 06/12/14.
 */
function validateProfile(){
    clearErrors();
    ValidateFullUser($('#firstname').val(),$('#lastname').val(),$('#emailaddress').val());
    ValidateUserDateOfBirth($('#dob-formatted').val());
    if(!validateTelephoneNumber($('#phone').val())){
        errors.push('The telephone number can only contain digits');
    }
    if(!validateTelephoneNumber($('#mobile').val())){
        errors.push('The mobile number can only contain digits');
    }
    $('#success-box').fadeOut('fast');
    $('#error-box').fadeOut('fast');
    if(errors.length==0){
        $.when(SaveProfile()).then(function(data){
            $('#success-box').fadeIn('fast');
            window.scrollTo(0, 0);
        });
    }
    else{
        displayErrors();
        window.scrollTo(0, 0);
    }
}

function ValidateFullUser(firstName, lastName, emailAddress){

    if(firstName=='' || firstName==null){
        errors.push('You must provide the first name for this user');
    }

    if(lastName=='' || lastName==null){
        errors.push('You must provide the last name for this user');
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

function getEmailInUse(portalId,email,userId){
    return $.ajax({
        url: "/callback/portalEmailInUse",
        type: 'post',
        dataType: 'json',
        cache: false,
        data:{"portalId":portalId,"email":email,"userId":userId}
    });
}

function SaveProfile(){
    return $.ajax({
        url: "/profile/save",
        type: 'post',
        dataType: 'json',
        cache: false,
        data:$('#user-profile-form').serialize()
    });
}
