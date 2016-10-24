/**
 * Created by Daniel on 08/12/14.
 */
var errors=[];

function displayErrors(errorBox){
    errorBox = typeof errorBox !== 'undefined' ? errorBox : '#error-box';
    var errorString='';

    $(errors).each(function(key,value){
        errorString=errorString+value+'<br/>';
    });
    $(errorBox).stop().fadeOut().html(errorString).fadeIn();
}

function displaySuccess(message, successBox){
    successBox = typeof successBox !== 'undefined' ? successBox : '#success-box';
    $(successBox).stop().fadeOut().html(message).fadeIn();
}

function clearErrors(successBox, errorBox){
    successBox = typeof successBox !== 'undefined' ? successBox : '#success-box';
    errorBox = typeof errorBox !== 'undefined' ? errorBox : '#error-box';
    errors=[];
    $(successBox).fadeOut('fast');
    $(errorBox).fadeOut('fast');
}