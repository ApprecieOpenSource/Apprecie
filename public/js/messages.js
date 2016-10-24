/**
 * Created by hu86 on 19/08/2015.
 */
function sendMessage(){
    clearErrors('#contact-success', '#contact-error');
    validateMessage($('#contact-subject').val(), $('#contact-message').val());
    if (errors.length != 0){
        displayErrors('#contact-error');
    } else {
        $('#contact-send-btn').prop('disabled',true);
        $.when(AjaxSendMessage()).then(function(data){
            $('#contact-send-btn').prop('disabled',false);
            if(data.status=='success'){
                $('#contact').modal('hide');
                displaySuccess('Your message has been sent', '#contact-success');
            }
        });
    }
}

function AjaxSendMessage(){
    return $.ajax({
        url: "/api/sendmessage/",
        type: 'post',
        dataType: 'json',
        cache: false,
        data: $('#contact-form').serialize()
    });
}