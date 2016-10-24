/**
 * Created by Daniel Dimmick on 30/04/15.
 */
function RSVP() {

    var hash = null;
    var spaces = 1;

    this.setHash = function (newhash) {
        hash = newhash;
    };

    this.setSpaces = function (newspaces) {
        spaces = newspaces;
    };

    this.cancelInvitation = function () {
        if (hash != null) {
            return $.ajax({
                type: 'POST',
                url: '/rsvp/AjaxCancelInvitation/' + hash,
                dataType: 'json',
                data: {'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
            });
        }
        else {
            return {"status": 'failed', "message": 'No guest list item to work on'};
        }
    };

    this.acceptInvitation = function () {
        if (hash != null) {
            return $.ajax({
                type: 'POST',
                url: '/rsvp/AjaxAcceptInvitation/' + hash,
                dataType: 'json',
                data: {
                    'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN,
                    'spaces': spaces
                }
            });
        }
        else {
            return {"status": 'failed', "message": 'No guest list item to work on'};
        }
    };

    this.declineInvitation = function () {
        if (hash != null) {
            return $.ajax({
                type: 'POST',
                url: '/rsvp/AjaxDeclineInvitation/' + hash,
                dataType: 'json',
                data: {'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
            });
        }
        else {
            return {"status": 'failed', "message": 'No guest list item to work on'};
        }
    };
}