/**
 * Created by Daniel Dimmick on 30/04/15.
 */
function RsvpCancel(hash) {

    var btn = $('#cancel-btn');
    btn.prop('disabled', true);

    var rsvp = new RSVP();
    rsvp.setHash(hash);

    $.when(rsvp.cancelInvitation()).then(function () {
        location.reload();
    });
}

function RsvpDecline(hash) {

    var btn = $('#decline-btn');
    btn.prop('disabled', true);
    var btn2 = $('#accept-btn');
    btn2.prop('disabled', true);

    var rsvp = new RSVP();
    rsvp.setHash(hash);

    $.when(rsvp.declineInvitation()).then(function () {
        location.reload();
    });
}

function RsvpAccept(hash) {

    var btn = $('#decline-btn');
    btn.prop('disabled', true);
    var btn2 = $('#accept-btn');
    btn2.prop('disabled', true);

    var rsvp = new RSVP();
    if ($('#user-spaces').length) {
        rsvp.setSpaces($('#user-spaces').val());
    }
    rsvp.setHash(hash);

    $.when(rsvp.acceptInvitation()).then(function () {
        location.reload();
    });
}