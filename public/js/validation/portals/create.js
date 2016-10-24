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
    $('.progress-bar').css('width',(stepper*stepID)+'%');
    $('.step').hide();
    $('#step-'+stepID).show();
}

function validateStep(stepID){
    clearErrors();
    switch(stepID){
        case 1:
            validatePortalName($('#portal-name'));
            validatePortalSubdomain($('#portal-subdomain'));
            nextStep(1);
            break;
        case 2:
            validateQuota($('#quota-portal-administrators'),'organisation Owner');
            validateQuota($('#quota-managers'),'Managers');
            validateQuota($('#quota-internal-members'),'Internal Members');
            validateQuota($('#quota-apprecie-suppliers'),'Apprecie Suppliers');
            validateQuota($('#quota-affiliate-suppliers'),'Affiliated Suppliers');
            validateQuota($('#quota-members'),'Members');
            validateQuota($('#quota-family-members'),'Family Members');
            validateQuotaPercentage($('#quota-commission'),'Commission');
            nextStep(2);
            break;
        case 3:
            $('#create-portal-button').addClass('disabled');
            validatePrimaryContactFirstName($('#contact-firstname'));
            validatePrimaryContactLastName($('#contact-lastname'));
            validatePrimaryContactAddress($('#address-id'));
            validatePrimaryContactEmail($('#contact-email'));
            validatePrimaryContactTelephone($('#contact-telephone'));
            validatePrimaryContactMobile($('#contact-mobile'));
            if(errors.length==0){
                loader(true);
                $.when(createPortal()).then(function(data){
                    loader(false);
                    if(data.result==false){
                        errors.push(data.message);
                    }
                    else{
                        nextStep(3);
                    }
                    $('#create-portal-button').removeClass('disabled');
                });
            }
            else{
                $('#create-portal-button').removeClass('disabled');
                displayErrors();
            }
        break;
    }

}

function nextStep(stepID){
    if(errors.length==0){
        setStep(stepID+1);
    }
    else{
        displayErrors();
    }
}

function validateQuota(element,name){
        if(isNaN((element.val())) || element.val()<0){
            errors.push('Quota for '+name+' is invalid');
        }
}

function validateQuotaPercentage(element,name){
    if(isNaN((element.val())) || element.val()<0 || element.val()>100){
        errors.push('Quota for '+name+' is invalid');
    }
}

function validatePrimaryContactEmail(element){
    if(!validateEmail(element.val())){
        errors.push('Please enter a valid email address');
    }
}

function validatePrimaryContactAddress(element){
    var addressType=$("input:radio[name ='addressType']:checked").val();

    if(addressType == 'manual'){
        var address1 = $('#address1').val();
        var city = $('#city').val();
        var postcode = $('#postcode').val();

        if(address1.length < 4 || city.length < 2 || postcode.length < 2) {
            errors.push('Please provide valid address1, city, and postcode');
        }
    } else {
        if (element.val() == '') {
            errors.push('Please select an address');
        }
    }
}

function validatePrimaryContactFirstName(element){
    var valueLength=element.val().length;
    if(valueLength<2 || valueLength>45){
        errors.push('The contact\'s first name must be between 2 and 45 characters');
    }
}

function validatePrimaryContactLastName(element){
    var valueLength=element.val().length;
    if(valueLength<2 || valueLength>45){
        errors.push('The contact\'s last name must be between 2 and 45 characters');
    }
}

function validatePrimaryContactTelephone(element){
    var valueLength=element.val().length;
    if(valueLength<8 || valueLength>15){

        errors.push('The contact\'s telephone number must be between 8 and 15 numbers');
    }
    else if(isNaN(element.val())){
        errors.push('The contact\'s telephone number must be a number');
    }

}

function validatePrimaryContactMobile(element){
    var valueLength=element.val().length;
    if(valueLength>0){
        if(valueLength<8 || valueLength>15){

            errors.push('The contact\'s mobile number must be between 8 and 15 numbers');
        }
        else if(isNaN(element.val())){
            errors.push('The contact\'s mobile number must be a number');
        }
    }
}

function validatePortalName(element){
    var valueLength=element.val().length;
    if(valueLength<3 || valueLength>45){
        errors.push('The Portal Name must be between and 3 and 45 characters long');
    }
    else{
        $.ajax({
            url: "/portals/AjaxPortalNameInUse",
            type: 'post',
            dataType: 'json',
            cache: false,
            data:{ portalName: element.val(),CSRF_SESSION_TOKEN:CSRF_SESSION_TOKEN},
            success: function(data){
                if(data.result==true){
                    errors.push(data.message);
                }
            },
            async: false
        });
    }
}

function validatePortalSubdomain(element){
    var valueLength=element.val().length;
    if(valueLength<3 || valueLength>45){
        errors.push('The Portal Subdomain must be between and 3 and 45 characters long');
    }
    else if(!/^[a-zA-Z()0-9-]+$/.test(element.val())){
        errors.push('The Portal Subdomain can only contain characters from the ISO basic Latin alphabet and dashes');
    }
    else if(/^-/.test(element.val()) || /-$/.test(element.val())){
        errors.push('The Portal Subdomain cannot start or end with a dash');
    }
    else{
        $.ajax({
            url: "/portals/AjaxPortalSubDomainInUse",
            type: 'post',
            dataType: 'json',
            cache: false,
            data:{ portalSubdomain: element.val(),CSRF_SESSION_TOKEN:CSRF_SESSION_TOKEN},
            success: function(data){
                if(data.result==true){
                    errors.push(data.message);
                }
            },
            async: false
        });
    }
}

function createPortal(){
    loader(true);
    return $.ajax({
        url: "/portals/AjaxCreatePortal",
        type: 'post',
        dataType: 'json',
        cache: false,
        data:$('#create-portal').serialize()
    });
}