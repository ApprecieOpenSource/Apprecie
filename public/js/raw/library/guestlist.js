// expects postdata: itemid,attending string (true),status,
function GuestList() {
    this.pageNumber = 1;
    this.postData = null;

    this.setPostData = function (postData) {
        this.postData = postData;
    }
    this.getPostData = function () {
        return this.postData;
    }
    this.getPageNumber = function () {
        return this.pageNumber;
    }
    this.setPageNumber = function (pageNumber) {
        this.pageNumber = pageNumber;
    }

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/vault/AjaxGetGuestList/' + this.getPageNumber(),
            dataType: 'json',
            data: this.getPostData()
        });
    }

    this.fetch = function () {
        return this.ajax();
    }
}

function GuestListAll(itemId) {
    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/vault/AjaxGuestListAll/' + itemId,
            dataType: 'json',
            data: {"CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN}
        });
    }

    this.fetch = function () {
        return this.ajax();
    }
}

function GetUsersNotGuests() {
    this.pageNumber = 1;
    this.itemId = null;

    this.setPageNumber = function (pageNumber) {
        this.pageNumber = pageNumber;
    }

    this.setItemId = function (itemId) {
        this.itemId = itemId;
    }

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/vault/AjaxUsersNotGuests/' + this.pageNumber,
            dataType: 'json',
            data: {'itemId': this.itemId, "CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN}
        });
    }

    this.fetch = function () {
        return this.ajax();
    }

}
function SupplierGuestList() {
    this.pageNumber = 1;
    this.postData = null;

    this.setPostData = function (postData) {
        this.postData = postData;
    }
    this.getPostData = function () {
        return this.postData;
    }
    this.getPageNumber = function () {
        return this.pageNumber;
    }
    this.setPageNumber = function (pageNumber) {
        this.pageNumber = pageNumber;
    }

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/mycontent/AjaxCreatorGuestList/' + this.getPageNumber(),
            dataType: 'json',
            data: this.getPostData()
        });
    }

    this.fetch = function () {
        return this.ajax();
    }
}
function DeclineGuestListUser() {
    this.userId = null;
    this.itemId = null;

    this.setUserId = function (setUserId) {
        this.setUserId = setUserId;
    }
    this.getUserId = function () {
        return this.setUserId;
    }
    this.setItemId = function (itemId) {
        this.itemId = itemId;
    }
    this.getItemId = function () {
        return this.itemId;
    }

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/vault/AjaxDeclineGuestListUser/',
            dataType: 'json',
            data: {'userId': this.getUserId(), 'itemId': this.getItemId(), "CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN}
        });
    }

    this.fetch = function () {
        return this.ajax();
    }
}

function AttendGuestListUser() {
    this.userId = null;
    this.itemId = null;

    this.setUserId = function (setUserId) {
        this.setUserId = setUserId;
    }
    this.getUserId = function () {
        return this.setUserId;
    }
    this.setItemId = function (itemId) {
        this.itemId = itemId;
    }
    this.getItemId = function () {
        return this.itemId;
    }

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/vault/AjaxAttendGuestListUser/',
            dataType: 'json',
            data: {'userId': this.getUserId(), 'itemId': this.getItemId(), "CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN}
        });
    }

    this.fetch = function () {
        return this.ajax();
    }
}

function InviteGuestListUser() {
    this.userId = null;
    this.spaces = 1;
    this.itemId = null;
    this.sendEmail = false;

    this.setUserId = function (setUserId) {
        this.userId = setUserId;
    };

    this.getUserId = function () {
        return this.userId;
    };

    this.setSpaces = function (setUserId) {
        this.spaces = setUserId;
    };

    this.getSpaces = function () {
        return this.spaces;
    };

    this.setItemId = function (itemId) {
        this.itemId = itemId;
    };

    this.getItemId = function () {
        return this.itemId;
    };

    this.setSendEmail = function (sendEmail) {
        this.sendEmail = sendEmail;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/vault/AjaxInviteGuestListUser/',
            dataType: 'json',
            data: {
                'userId': this.getUserId(),
                'spaces': this.getSpaces(),
                'itemId': this.getItemId(),
                'sendEmail': this.sendEmail,
                'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN
            }
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

