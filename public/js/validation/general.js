/**
 * Created by Daniel on 08/12/14.
 */
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
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

function validateTelephoneNumber(number){
    if(number!=''){
        var reg = /^\d+$/;
        return reg.test(number)
    }
    return true;
}

function escapeHtml(string) {
    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
    };
    return String(string).replace(/[&<>"'\/]/g, function (s) {
        return entityMap[s];
    });
}