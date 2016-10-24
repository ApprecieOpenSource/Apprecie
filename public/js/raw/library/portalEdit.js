var EditPortal=function(portalId){
    clearErrors();
    var newSubDomain=null;
    var newPortalName=null;
    var newContactName=null;
    var newTelephoneNumber=null;
    var newMobileNumber=null;
    var newEmailAddress=null;
    var newEdition=null;
    var newAccountManager=null;
    var newEnabled=null;
    var newAddressId=null;

    this.setSubSomain=function(subDomain){
        newSubDomain=subDomain;
    };

    this.setPortalName=function(portalName){
        newPortalName=portalName;
    };

    this.setContactName=function(contactName){
        newContactName=contactName;
    };

    this.setTelephoneNumber=function(telephoneNumber){
        newTelephoneNumber=telephoneNumber;
    };

    this.setMobileNumber=function(mobileNumber){
        newMobileNumber=mobileNumber;
    };

    this.setEmailAddress=function(emailAddress){
        newEmailAddress=emailAddress;
    };

    this.setAccountManager=function(accountManager){
        newAccountManager=accountManager;
    };

    this.setEdition=function(edition){
        newEdition=edition;
    };

    this.setEnabled=function(enabled){
        newEnabled=enabled;
    }

    this.setAddressId=function(addressId){
        newAddressId=addressId;
    }

    this.validate=function(){

        // Sub domain validation
        var valueLength=newSubDomain.length;
        if(valueLength<3 || valueLength>45){
            errors.push('The Portal Subdomain must be between and 3 and 45 characters long');
        }
        else if(!/^[a-zA-Z()0-9-]+$/.test(newSubDomain)){
            errors.push('The Portal Subdomain can only contain characters from the ISO basic Latin alphabet and dashes');
        }
        else if(/^-/.test(newSubDomain) || /-$/.test(newSubDomain)){
            errors.push('The Portal Subdomain cannot start or end with a dash');
        }

        // portal name validation
        valueLength=newPortalName.length;
        if(valueLength<3 || valueLength>45){
            errors.push('The Portal Name must be between and 3 and 45 characters long');
        }

        // contact name validation
        valueLength=newContactName.length;
        if(valueLength==0 || newContactName==' '){
            errors.push('The contact name cannot be empty');
        }

        // telephone validation
        if(!validateTelephoneNumber(newTelephoneNumber)){
            console.log(newTelephoneNumber);
            errors.push('The telephone number is invalid');
        }

        // mobile validation
        if(!validateTelephoneNumber(newMobileNumber)){
            errors.push('The mobile number is invalid');
        }

        // email address validation
        if(!validateEmail(newEmailAddress)){
            errors.push('The email address is invalid');
        }

        if($('#selected-address-value').text() == '') {

            var addressType = $("input:radio[name ='addressType']:checked").val();

            if (addressType == 'manual') {
                var address1 = $('#address1').val();
                var city = $('#city').val();
                var postcode = $('#postcode').val();

                if (address1.length < 4 || city.length < 2 || postcode.length < 2) {
                    errors.push('Please provide valid address1, city, and postcode');
                }
            } else {
                if ($('#address-id').val() == '') {
                    errors.push('Please select an address');
                }
            }
        }

        if(errors.length==0){
            $.when(save()).then(function(data){
                if(data.status=='success'){
                    displaySuccess(data.message);
                }
                else{
                    errors.push(data.message[0]);
                    displayErrors();
                }
            })
        }
        else{
            displayErrors();
        }
    };

    function save(){
        var addressData = $('#manual-address :input').serializeObject();
        var addressType=$("input:radio[name ='addressType']:checked").val();
        var data = {addressType:addressType,addressId:newAddressId,enabled:newEnabled,edition:newEdition,accountManager:newAccountManager,portalId:portalId,portalSubdomain: newSubDomain,portalName:newPortalName,contactName:newContactName,telephoneNumber:newTelephoneNumber,mobileNumber:newMobileNumber,emailAddress:newEmailAddress};
        var finalData = $.extend(data, addressData);

        return $.ajax({
            url: "/portals/AjaxUpdatePortal/"+portalId,
            type: 'post',
            dataType: 'json',
            data: finalData
        });
    }
};