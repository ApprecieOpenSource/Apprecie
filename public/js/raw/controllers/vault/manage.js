$(document).ready(function () {
    refreshGuestList();
})

function refreshGuestList() {
    getAttendingGuestList(1);
    getDeclinedGuestList(1);
    getInvitedGuestList(1);
}

function getAttendingGuestList(pageNumber) {

    var guestList = new GuestList();

    guestList.setPageNumber(pageNumber);
    guestList.setPostData({'attending': 'true', 'itemid': itemId, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN});

    $.when(guestList.fetch()).then(function (data) {

        var buffer = '';

        $.each(data.items, function (key, value) {
            buffer += '<tr><td>' + value.profile.firstname + ' ' + value.profile.lastname + '</td><td>' + value.reference + '</td><td>' + value.role + '</td><td><a href="mailto:' + value.profile.email + '">' + value.profile.email + '</a></td><td class="hidden-xs">' + value.confirmDate + '</td><td class="hidden-xs">' + value.status + '</td><td class="hidden-xs">' + value.guest.spaces + '</td><td>';
            if (!value.isOwner) {
                if (value.userIsDeleted === false) {
                    buffer += '<div class="btn-group pull-right"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Options <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';
                    buffer += '<li><a href="/people/viewuser/' + value.guest.userId + '">View profile</a></li>';
                    if (deniedForActions === false) {
                        buffer += '<li><a style="cursor:pointer;" onclick="DeclineGuest(' + value.guest.userId + ')">Change to Declined</a></li>';
                    }
                    buffer += '</ul></div>';
                }
            } else {
                if (deniedForActions === false) {
                    buffer += '<div class="btn-group pull-right">';
                    buffer += '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Options <span class="caret"></span></button>';
                    buffer += '<ul class="dropdown-menu" role="menu">';
                    buffer += '<li><a style="cursor:pointer;" onclick="DeclineGuest(' + value.guest.userId + ')">Do not attend</a></li>';
                    buffer += '</ul>';
                    buffer += '</div>';
                }
            }
            buffer += '</td></tr>';
        });

        if (data.items.length == 0) {
            buffer = '<tr><td colspan="8">' + data.message + '</td></tr>';
        }

        $('#attending-count').html(data.spaceCount);
        $('#attending-tbl').html(buffer);
        Pagination(data, 'getAttendingGuestList', $('#attending-pagination'));
    })
}
function getDeclinedGuestList(pageNumber) {

    var guestList = new GuestList();

    guestList.setPageNumber(pageNumber);
    guestList.setPostData({
        'attending': 'false',
        'itemid': itemId,
        'status': ['declined', 'revoked', 'cancelled'],
        'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN
    });

    $.when(guestList.fetch()).then(function (data) {

        var buffer = '';

        $.each(data.items, function (key, value) {
            buffer += '<tr><td>' + value.profile.firstname + ' ' + value.profile.lastname + '</td><td>' + value.reference + '</td><td>' + value.role + '</td><td><a href="mailto:' + value.profile.email + '">' + value.profile.email + '</a></td><td class="hidden-xs">' + value.confirmDate + '</td><td class="hidden-xs">' + value.status + '</td><td class="hidden-xs">' + value.guest.spaces + '</td><td>';
            if (!value.isOwner) {
                if (value.userIsDeleted === false) {
                    buffer += '<div class="btn-group pull-right"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Options <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';
                    buffer += '<li><a href="/people/viewuser/' + value.guest.userId + '">View profile</a></li>';
                    if (deniedForActions === false) {
                        buffer += '<li><a style="cursor:pointer;" onclick="AttendGuest(' + value.guest.userId + ')">Change to Attending</a></li>';
                        buffer += '<li><a style="cursor:pointer;" onclick="ShowPreview(' + value.guest.userId + ', ' + value.guest.spaces + ')">Resend Invitation</a></li>';
                    }
                    buffer += '</ul></div>';
                }
            } else {
                if (deniedForActions === false) {
                    buffer += '<div class="btn-group pull-right"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Options <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';
                    buffer += '<li><a style="cursor:pointer;" onclick="AttendGuest(' + value.guest.userId + ')">Change to Attending</a></li>';
                    buffer += '<li><a style="cursor:pointer;" onclick="ShowPreview(' + value.guest.userId + ', ' + value.guest.spaces + ')">Resend Invitation</a></li>';
                    buffer += '</ul></div>';
                }
            }
            buffer += '</td></tr>';
        });

        if (data.items.length == 0) {
            buffer = '<tr><td colspan="8">' + data.message + '</td></tr>';
        }

        $('#declined-count').html(data.spaceCount);
        $('#declined-tbl').html(buffer);
        Pagination(data, 'getDeclinedGuestList', $('#declined-pagination'));
    })
}
function getInvitedGuestList(pageNumber) {

    var guestList = new GuestList();

    guestList.setPageNumber(pageNumber);
    guestList.setPostData({
        'attending': 'false',
        'itemid': itemId,
        'status': 'invited',
        'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN
    });

    $.when(guestList.fetch()).then(function (data) {

        var buffer = '';

        $.each(data.items, function (key, value) {
            buffer += '<tr><td>' + value.profile.firstname + ' ' + value.profile.lastname + '</td><td>' + value.reference + '</td><td>' + value.role + '</td><td><a href="mailto:' + value.profile.email + '">' + value.profile.email + '</a></td><td class="hidden-xs">' + value.inviteDate + '</td><td class="hidden-xs">' + value.status + '</td><td class="hidden-xs">' + value.guest.spaces + '</td><td rowspan="2">';
            if (value.userIsDeleted === false) {
                buffer += '<div class="btn-group pull-right"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Options <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';
                buffer += '<li><a href="/people/viewuser/' + value.guest.userId + '">View profile</a></li>';
                if (deniedForActions === false) {
                    buffer += '<li><a style="cursor:pointer;" onclick="AttendGuest(' + value.guest.userId + ')">Change to Attending</a></li>';
                    buffer += '<li><a style="cursor:pointer;" onclick="DeclineGuest(' + value.guest.userId + ')">Change to Declined</a></li>';
                    buffer += '<li><a style="cursor:pointer;" onclick="ShowPreview(' + value.guest.userId + ', ' + value.guest.spaces + ')">Resend Invitation</a></li>';
                }
                buffer += '</ul></div>';
            }
            buffer += '</td></tr><tr><td colspan="8" style="border-top: none; padding-top: 0;">' + value.invite + '</td></tr>';
        });

        if (data.items.length == 0) {
            buffer = '<tr><td colspan="8">' + data.message + '</td></tr>';
        }

        $('#invited-count').html(data.spaceCount);
        $('#invited-tbl').html(buffer);
        Pagination(data, 'getInvitedGuestList', $('#invited-pagination'));
    })
}
function DeclineGuest(userId) {
    var decline = new DeclineGuestListUser();
    decline.setUserId(userId);
    decline.setItemId(itemId);
    $.when(decline.fetch()).then(function (data) {
        OutputStatus(data);
    })
}

function AttendGuest(userId) {
    var attend = new AttendGuestListUser();
    attend.setUserId(userId);
    attend.setItemId(itemId);
    $.when(attend.fetch()).then(function (data) {
        OutputStatus(data);
    })
}

function InviteGuest(userId, spaces) {
    var invite = new InviteGuestListUser();
    invite.setUserId(userId);
    invite.setItemId(itemId);
    invite.setSpaces(spaces);
    invite.setSendEmail(true);
    $.when(invite.fetch()).then(function (data) {
        OutputStatus(data);
    })
}

function ShowPreview(userId, spaces) {
    emailWidget.previewData = {
        "event": itemId,
        "emailType": 'invitation',
        "user": userId
    };

    if (!Function.prototype.bind) {
        Function.prototype.bind = function (oThis) {
            if (typeof this !== 'function') {
                // closest thing possible to the ECMAScript 5
                // internal IsCallable function
                throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
            }

            var aArgs = Array.prototype.slice.call(arguments, 1),
                fToBind = this,
                fNOP = function () {
                },
                fBound = function () {
                    return fToBind.apply(this instanceof fNOP && oThis
                            ? this
                            : oThis,
                        aArgs.concat(Array.prototype.slice.call(arguments)));
                };

            fNOP.prototype = this.prototype;
            fBound.prototype = new fNOP();

            return fBound;
        };
    }

    emailWidget.callback = InviteGuest.bind(undefined, userId, spaces);

    emailWidget.modal.modal('show');
}

function OutputStatus(data) {
    if (data.status == 'success') {
        refreshGuestList();
        $('#errorbox').hide();
        $('#successbox').stop().fadeOut('fast').fadeIn('fast').html(data.message);
    }
    else {
        $('#successbox').hide();
        $('#errorbox').stop().fadeOut('fast').fadeIn('fast').html(data.message);
    }
}