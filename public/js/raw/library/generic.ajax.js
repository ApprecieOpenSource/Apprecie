/**
 * Created by Gavin on 21/04/15.
 */
/**
 * removes the suggested item #idPrefix + elementId from the DOM and sends the id to
 * portal/apiController/apiAction   as id
 * @param apiController
 * @param apiAction
 * @param idPrefix
 * @param elementId
 */
function dismissAndNotify(apiController, apiAction, idPrefix, elementId) {
    var elementHandle = idPrefix + elementId;
    var element = $('#' + elementHandle);

    if(element) {
        $.when(ajaxPostAPI(apiController, apiAction, {"id": elementId, 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}).then(function(data){
            if(data.status == 'success') {
                element.toggle();
            } //perhaps handle a failure by notifying the user
        }));
    }
}

function ajaxPostAPI(api, action, jsonData){
    return $.ajax({
        url: "/" + api + "/" + action,
        type: 'post',
        dataType: 'json',
        data: jsonData,
        cache: false
    });
}

function confirmModalFromApi(controller, action, jsonData, srcButtonId, confirmAction, removeWhenClosed)
{
    if (typeof removeWhenClosed === 'undefined') {
        removeWhenClosed = true;
    }

    $.when(ajaxPostAPI(controller, action, jsonData)).then(function(data) {
        if(data.status == 'success') {
            var srcButtonSelector = '#'+ srcButtonId;
            $(srcButtonSelector).off('click');

            var myModal = new Modal(removeWhenClosed);
            myModal.confirm(data.confirm, data.message, confirmAction, srcButtonSelector, 'modal' + srcButtonId);
            $(srcButtonSelector).trigger('click');
        }
    })
}

