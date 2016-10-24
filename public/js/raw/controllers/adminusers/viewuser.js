/**
 * Created by Hu on 25/05/2015.
 */
$(document).ready(function(){

    var signUpBtn = $('#send-registration');
    var generateBtn = $('#generate-link');
    var removeBtn = $('#remove-link');
    var registrationLink = $("#registration-link");
    var registrationLinkContainer = $('#registration-link-container');

    var successMsg = $('#portal-access-success');
    var errorMsg = $('#portal-access-error');
    successMsg.hide();
    errorMsg.hide();

    generateBtn.click(generateSignUp);

    removeBtn.click(removeSignUp);

    registrationLink.on("click", function () {
        $(this).select();
    });

    function generateSignUp() {
        successMsg.hide();
        errorMsg.hide();

        generateBtn.prop('disabled', true).html("Processing...");
        $.ajax({
            url: "/adminusers/AjaxGenerateRegistrationLink/" + userId,
            type: 'post',
            dataType: 'json',
            data: {'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN},
            cache: false
        }).done(function(data) {
            if (data.result === 'success') {

                generateBtn.hide();
                generateBtn.html("Grant Portal Access");
                generateBtn.prop('disabled', false);

                removeBtn.show();

                if (hasEmail) {
                    signUpBtn.show();
                }

                registrationLink.val(data.registration);
                registrationLinkContainer.show();

            } else if (data.result === 'failed' && data.message) {
                generateBtn.html("Grant Portal Access");
                errorMsg.html(data.message).show();
            }
        });
    }

    function removeSignUp() {
        successMsg.hide();
        errorMsg.hide();

        removeBtn.prop('disabled', true).html("Removing...");
        $.ajax({
            url: "/adminusers/AjaxRemoveRegistrationLink/" + userId,
            type: 'post',
            dataType: 'json',
            data: {'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN},
            cache: false
        }).done(function(data) {
            if (data.result === 'success') {

                removeBtn.hide();
                removeBtn.html("Remove Pending Portal Access");
                removeBtn.prop('disabled', false);

                generateBtn.show();

                if (hasEmail) {
                    signUpBtn.hide();
                }

                registrationLinkContainer.hide();
                registrationLink.val('');

            }
        });
    }
});

function sendSignUp() {
    var successMsg = $('#portal-access-success');
    var errorMsg = $('#portal-access-error');
    successMsg.hide();
    errorMsg.hide();

    var signUpBtn = $('#send-registration');
    signUpBtn.prop('disabled', true).html("Sending...");
    $.ajax({
        url: "/adminusers/sendsignup",
        type: 'post',
        dataType: 'json',
        cache: false,
        data:{"portalId": portalId, "userId": userId, "CSRF_SESSION_TOKEN" : CSRF_SESSION_TOKEN}
    }).done(function(){
        signUpBtn.prop('disabled', false).html('Send Sign-up Email');
        successMsg.html('Email sent.').show();
    });
}

function ItemSearchShow(){
    $('#item-search').toggle('fast','linear');
}

function RelationshipSearchShow(){
    $('#relationship-search').toggle('fast','linear');
}