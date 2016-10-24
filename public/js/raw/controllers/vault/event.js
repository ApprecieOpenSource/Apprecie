function shareItem(){
    $('#share-error').stop().fadeOut('fast');
        $.when(shareItemAjax()).then(function(data){
            $('#share-success').stop().fadeOut('fast').fadeIn('fast');
        });
}

function shareItemAjax(){
    return $.ajax({
        url: "/vault/share/" + eventId,
        type: 'post',
        dataType: 'json',
        cache: false,
        data: $('#share-form').serialize()
    });
}

function PlaceOrder(reserve,itemId){
    $('#place-order-btn').prop('disabled',true);
    $.when(AjaxPlaceOrder($('#package-quantity').val(),reserve,itemId)).then(function(data){
        if(data.status=='success'){
            window.location.href = "/payment/index/"+data.orderid;
        }
        else{
            alert(data.message);
            $('#place-order-btn').prop('disabled',false);
        }
    })
}

function CancelOrder(orderId){
    $('#cancel-order-btn').prop('disabled',true);
    $.when(AjaxCancelOrder(orderId)).then(function(data){
        if(data.status=='success'){
            window.location.reload();
        }
        else{
            alert(data.message);
            $('#cancel-order-btn').prop('disabled',false);
        }
    });
}

function AjaxCancelOrder(orderId){
    return $.ajax({
        url: "/vault/AjaxCancelOrder/",
        type: 'post',
        dataType: 'json',
        cache: false,
        data: {"orderId":orderId, "CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
    });
}

function AjaxPlaceOrder(quantity,reserve,itemId){
    return $.ajax({
        url: "/vault/AjaxPurchaseItem/"+itemId,
        type: 'post',
        dataType: 'json',
        cache: false,
        data: {"reserve":reserve,"quantity":quantity, "CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
    });
}

function sendSuggestion(itemId){
    var suggestion=new suggestItem(itemId);
    var successBox=$('#suggest-success-box');
    var errorBox=$('#suggest-error-box');

    $('#suggest-btn').prop('disabled',true);
    if(usersearch.getSelectedUsers().length!=0){
        $.each(usersearch.getSelectedUsers(), function(key, value) {
            suggestion.addUser(value.userId);
        });

        $.when(suggestion.fetch()).then(function(data){
            if(data.status=='success'){
                usersearch.reset();
                errorBox.stop().hide();
                successBox.stop().hide().html(data.message).fadeIn('fast');
            }
            else{
                successBox.stop().hide();
                errorBox.stop().hide().html(data.message).fadeIn('fast');
            }
            $('#suggest-btn').prop('disabled',false);

        })
    }
    else{
        $('#suggest-btn').prop('disabled',false);
    }
}

function sendExternalSuggestion(itemId) {
    var suggestion = new SuggestItemExternal(itemId, $('#suggest-email').val());
    var successBox = $('#suggest-ext-success-box');
    var errorBox = $('#suggest-ext-error-box');

    $('#suggest-ext-btn').prop('disabled',true);

    if(! suggestion.validate()) {
        successBox.stop().hide();
        errorBox.stop().hide().html('You must provide an email address').fadeIn('fast'); //where to get localised strings from?
        $('#suggest-ext-btn').prop('disabled',false);
    } else {

        $.when(suggestion.fetch()).then(function(data){
            if(data.status=='success'){
                usersearch.reset();
                errorBox.stop().hide();
                successBox.stop().hide().html(data.message).fadeIn('fast');
            }
            else{
                successBox.stop().hide();
                errorBox.stop().hide().html(data.message).fadeIn('fast');
            }
            $('#suggest-ext-btn').prop('disabled',false);
        })
    }
}