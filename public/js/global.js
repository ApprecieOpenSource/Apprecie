/**
 * Created by Daniel on 12/11/14.
 */
/**
 * Start the loading animation automatically when internal ajax calls are made
 */
$(document).ajaxStart(function () {
    $('#loading-div').stop().fadeIn('fast');
}).ajaxStop(function () {
        $('#loading-div').stop().fadeOut('fast');
});

/**
 * Function to manually control the loading animation, this is needed for making cross site JSONP calls
 * as it does not trigger .ajaxStart
 * @param state bool start or stop the loading animation
 */
function loader(state){
    switch (state){
        case true:
            $('#loading-div').stop().fadeIn('fast');
            break;
        case false:
            $('#loading-div').stop().fadeOut('fast');
            break;
    }
}

function validatePostcode(postcode) {
    postcode = postcode.replace(/\s/g, "");
    var regex = /^[A-Z]{1,2}[0-9]{1,2} ?[0-9][A-Z]{2}$/i;
    return regex.test(postcode);
}

function toggleFilter(element){
    $(element).toggle('fast','linear');
}

Number.prototype.precision = function () {
    if ((this.valueOf() % 1) != 0)
        return this.toString().split(".")[1].length;
    return 0;
};

$(document).ajaxComplete(function(event, xhr, settings) {
    switch (xhr.status) {
        case 200:
            break;
        case 287: //custom redirect
            window.location.replace('/' + xhr.getResponseHeader('X-Redirect-URL'));
            break;
        case 404:
            window.location.replace('/error/fourofour');
            break;
        case 0:
            break;
        default:
            window.location.replace('/error/exception');
    }
});

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function()
    {
        if (o[this.name] !== undefined)
        {
            if (!o[this.name].push)
            {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        }
        else
        {
            o[this.name] = this.value || '';
        }
    });
    return o;
};