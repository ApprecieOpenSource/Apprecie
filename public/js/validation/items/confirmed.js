/**
 * Created by Daniel on 06/01/15.
 */
var inprogress=false;
function validateStep(stepID){
    clearErrors();
    switch(stepID){
        case 1:
            nextStep(1);
            break;
        case 2:
            validateTitle($('#confirmed-title'));
            validateShortDescription($('#confirmed-short-description'));
            validateFullDescription($('#confirmed-description'));
            validateStartDate($('#confirmed-startdate'),$('#confirmed-starttime'));
            validateEndDate($('#confirmed-enddate'),$('#confirmed-startdate'),$('#confirmed-starttime'),$('#confirmed-endtime'));
            validateBookingStartDate($('#confirmed-bookingstart'),$('#confirmed-enddate'),$('#confirmed-bookingend'));
            validateBookingEndDate($('#confirmed-bookingstart'),$('#confirmed-startdate'),$('#confirmed-bookingend'));
            nextStep(2);
            break;
        case 3:
            validateAddress($('#address-id'));
            nextStep(3);
            break;
        case 4:
            validatePackageSize($('#package-size').val());
            validateMaxPlaces($('#max-units').val());
            validateTaxRate($('#tax-rate'));
            validatePricePerUnit($('#price-per-unit'));
            validateMinPlaces($('#min-units'));
            validateCostPerUnit($('#cost-per-unit'));
            validateCostToDeliver($('#cost-to-deliver'));
            validateMarketValue($('#market-value'));
            nextStep(4);

            if($('#stripe-configured').val() == 'false' && $('#price-per-unit').val() > 0) {
                $('.publish-option').prop('disabled', true);
                $('#publish-warning').show();
            } else {
                $('.publish-option').prop('disabled', false);
                $('#publish-warning').hide();
            }

            break;
        case 8:
            if(inprogress===false){
                inprogress=true;
                $('#create-btn').prop('disabled',true);
                $('#create-btn').html('Creating...');
                $.when(createEvent()).then(function(data){
                    $('#media-manager').prop('href','/itemcreation/media/'+data.itemId);
                    $('#create-btn').prop('disabled',false);
                    nextStep(8);
                })
            }
            break;
    }
}

function createEvent(){
    loader(true);
    return $.ajax({
        url: "/itemcreation/ajaxCreateEvent",
        type: 'post',
        dataType: 'json',
        cache: false,
        data:$('#item-creation-form').serialize()
    });
}

function validateTitle(element){
    var valueLength=element.val().length;
    if(valueLength<5 || valueLength>100){
        errors.push('The title must be between 5 and 100 characters');
    }
}

function validateShortDescription(element){
    var valueLength=element.val().length;
    if(valueLength<5 || valueLength>300){
        errors.push('The short description must be between 5 and 300 characters');
    }
}

function validateFullDescription(element){
    tinyMCE.triggerSave();
    var valueLength=element.val().length;
    if(valueLength<5 || valueLength>10000){
        errors.push('The description must be greater than 5 characters');
    }
}

function validateStartDate(element,stime){
    var timepattern= new RegExp('^(2[0-3]|1[0-9]|0[0-9]|[^0-9][0-9]):([0-5][0-9]|[0-9])$');
    if(!timepattern.test(stime.val())){
        errors.push('The event start time is not valid, 24 hour format is required e.g. 09:00 or 23:00');
    }

    var startdate= new moment(element.val()+' '+stime.val(),'DD/MM/YYYY HH:mm');
    if(startdate.isValid()){
        var today = new moment();
        if(startdate.isBefore(today,'day')){
            errors.push('Start date cannot be in the past');
        }
    }
    else{
        errors.push('You must provide a valid start date');
    }
}

function validateEndDate(enddate,startdate,stime,etime){
    var timepattern= new RegExp('^(2[0-3]|1[0-9]|0[0-9]|[^0-9][0-9]):([0-5][0-9]|[0-9])$');
    if(!timepattern.test(etime.val())){
        errors.push('The event end time is not valid, 24 hour format is required e.g. 09:00 or 23:00');
    }


    var edate=new moment(enddate.val()+' '+etime.val(),'DD/MM/YYYY HH:mm');
    var sdate=new moment(startdate.val()+' '+stime.val(),'DD/MM/YYYY HH:mm');
    if(edate.isValid()){
        if(edate.isBefore(sdate,'minute')){
            errors.push('End date cannot be before the start date');
        }
    }
    else{
        errors.push('You must provide a valid end date');
    }
}

function validateBookingStartDate(bstart,estart,bend){
    var bookingstart= new moment(bstart.val(),'DD/MM/YYYY');
    var bookingend= new moment(bend.val(),'DD/MM/YYYY');
    var eventstart= new moment(estart.val(),'DD/MM/YYYY');
    if(bookingstart.isValid()){
        if(bookingstart.isSame(eventstart,'day') || bookingstart.isAfter(eventstart,'day')){
            errors.push('The booking start date must be before the event start date');
        }
        if(bookingstart.isAfter(bookingend,'day')){
            errors.push('The booking start date must be before the booking end date');
        }
    }
    else{
        errors.push('You must provide a valid booking start date');
    }
}

function validateBookingEndDate(bstart,estart,bend){
    var bookingstart= new moment(bstart.val(),'DD/MM/YYYY');
    var bookingend= new moment(bend.val(),'DD/MM/YYYY');
    var eventstart= new moment(estart.val(),'DD/MM/YYYY');
    if(bookingend.isValid()){
        if(bookingend.isSame(eventstart,'day') || bookingend.isAfter(eventstart,'day')){
            errors.push('The booking end date must be before the event start date');
        }
        if(bookingend.isBefore(bookingstart,'day')){
            errors.push('The booking end date must be after the booking start date');
        }
    }
    else{
        errors.push('You must provide a valid booking end date');
    }
}

function validateAddress(element){
    var addressType=$("input:radio[name ='addressType']:checked").val();

    if(addressType == 'manual'){
        var emptyCount = $('.manual-address').filter(function(){
            return !$(this).val();
        }).length;

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

function validateMinPlaces(element){
    if ((element.val() % 1 != 0)|| isNaN(element.val()) || element.val()=='') {
        errors.push('Minimum Spaces must be a whole number');
    }
}
//note this is actually maximum packages
function validateMaxPlaces(max){
    if (isNaN(max) || max=='' || max<1) {
        errors.push('Maximum Packages must be a whole number and greater than 0');
    }
    else if (max % 1 != 0) {
        errors.push('Maximum Packages must be a whole number');
    }
}

function validatePackageSize(packageSize){
    if (isNaN(packageSize) || packageSize=='' || packageSize<1) {
        errors.push('Spaces per Package must be a whole number and greater than 0');
    }
    else if (packageSize % 1 != 0) {
        errors.push('Spaces per Package must be a whole number');
    }
}
//this is actually price per package
function validatePricePerUnit(element){
    if (isNaN(element.val()) || element.val()=='') {
        errors.push('The Price per Package must be a number');
    } else if(element.val() > 0 && element.val() < .50) {
        errors.push('The Price per Package must be 0 or higher than 0.50');
    }
}

function validateCostPerUnit(element){
    if (isNaN(element.val()) || element.val()=='') {
        errors.push('The Cost per Attendee must be a number');
    }
}

function validateCostToDeliver(element){
    if (isNaN(element.val()) || element.val()=='') {
        errors.push('The Static Costs must be a number');
    }
}
function validateMarketValue(element){
    if (isNaN(element.val()) || element.val()=='') {
        errors.push('The Compliance Value must be a number');
    }
}

function validateTaxRate(element) {
    var num = parseFloat(element.val());

    if(isNaN(num) || num.precision() > 3 || num < 0 || num > 100) {
        errors.push('Tax rate must be a valid percentage between 0 and 100 with a maximum precision of 3 decimal places');
    }
}
