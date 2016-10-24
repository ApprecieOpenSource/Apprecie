var errors = [];
function validate()
{
    errors = [];
    validateStartDate($('#confirmed-startdate'),$('#confirmed-starttime'));
    validateEndDate($('#confirmed-enddate'),$('#confirmed-startdate'),$('#confirmed-starttime'),$('#confirmed-endtime'));
    validateNumberOfPackages($('#number-packages').val());
    validateNumberOfAttendees($('#package-size').val());
    validateAddress($('#address-id'));
    return errors.length == 0;
}

function validateStartDate(element,stime){
        var timepattern= new RegExp('^(2[0-3]|1[0-9]|0[0-9]|[^0-9][0-9]):([0-5][0-9]|[0-9])$');
        if(!timepattern.test(stime.val())){
            errors.push('The event start time is not valid');
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
            errors.push('The event end time is not valid');
        }

        var edate=new moment(enddate.val()+' '+etime.val(),'DD/MM/YYYY HH:mm');
        var sdate=new moment(startdate.val()+' '+stime.val(),'DD/MM/YYYY HH:mm');
        if(edate.isValid() && sdate.isValid() ){
            if(edate.isBefore(sdate,'minute')){
                errors.push('End date cannot be before the start date');
            }
        }
        else{
            errors.push('You must provide a valid start and end date');
        }

}

function validateAddress(element){
    if(element.val()==''){
        errors.push('Please select an address');
    }
}

function validateNumberOfAttendees(packageSize){
        if (isNaN(packageSize)  || packageSize<1 || packageSize % 1 != 0) {
            errors.push('Number of attendees must be a whole number and greater than 0');
        }
}

function validateNumberOfPackages(packageSize){
    if (isNaN(packageSize)  || packageSize<1 || packageSize % 1 != 0) {
        errors.push('Number of packages must be a whole number and greater than 0');
    }
}