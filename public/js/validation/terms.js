/**
 * Created by hu86 on 28/09/2015.
 */
function validateTitle(element){
    if(element.val().length == 0 || element.val().length > 100){
        errors.push('Please provide a title that is no more than 100 letters long for this document');
    }
}

function validateVersion(element){
    if(element.val().length == 0 || element.val().length > 45){
        errors.push('Please provide a version number that is no more than 45 letters long for this document');
    }
}