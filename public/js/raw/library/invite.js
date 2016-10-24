var inviteUsersList = function (itemId) {

    this.role = 'All';
    this.name = null;
    this.email = null;
    this.reference = null;
    this.accountActive = false;
    this.accountDeactivated = false;
    this.accountPending = false;
    this.metricsOnly = false;
    this.group = 'all';
    this.Login = 'All';
    this.pageNumber = 1;
    this.suggestions = false;

    this.setGroup = function (group) {
        this.group = group;
    };

    this.setMetricsOnly = function (state) {
        this.metricsOnly = state;
    };

    this.getMetricsOnly = function () {
        return this.metricsOnly;
    };

    this.setRole = function (role) {
        this.role = role;
    };

    this.getRole = function () {
        return this.role;
    };

    this.setName = function (name) {
        this.name = name;
    };

    this.getName = function () {
        return this.name;
    };

    this.setEmail = function (email) {
        this.email = email;
    };

    this.getEmail = function () {
        return this.email;
    };

    this.setReference = function (reference) {
        this.reference = reference;
    };

    this.getReference = function () {
        return this.reference;
    };

    this.setAccountActive = function (account) {
        this.accountActive = account;
    };

    this.getAccountActive = function () {
        return this.accountActive;
    };

    this.setAccountDeactivated = function (account) {
        this.accountDeactivated = account;
    };

    this.getAccountDeactivated = function () {
        return this.accountDeactivated;
    };

    this.setAccountPending = function (account) {
        this.accountPending = account;
    };

    this.getAccountPending = function () {
        return this.accountPending;
    };

    this.setLogin = function (login) {
        this.login = login;
    };

    this.getLogin = function () {
        return this.login;
    };

    this.setPageNumber = function (pageNumber) {
        this.pageNumber = pageNumber;
    };

    this.getPageNumber = function () {
        return this.pageNumber;
    };

    this.getGroup = function () {
        return this.group;
    };

    this.setSuggestions = function (suggeestions) {
        this.suggestions = suggeestions;
    };

    this.getSuggestions = function () {
        return this.suggestions;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/invite/ajaxGetSuggestedUsers/' + itemId,
            dataType: 'json',
            data: {
                "suggestions": this.getSuggestions(),
                "pageNumber": this.getPageNumber(),
                "group": this.getGroup(),
                "metricsOnly": this.getMetricsOnly(),
                "roleName": this.getRole(),
                "name": this.getName(),
                "email": this.getEmail(),
                "reference": this.getReference(),
                "accountActive": this.getAccountActive(),
                "accountDeactivated": this.getAccountDeactivated(),
                "accountPending": this.getAccountPending(),
                "login": this.getLogin(),
                'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN
            }

        });
    };

    this.fetch = function () {
        return this.ajax();
    };
};

var suggestedUsers = function (unitCap) {

    var users = [];
    var remainingUnits = 0;

    this.setRemainingunits = function (units) {
        remainingUnits = units;
    };

    this.getRemainingUnits = function () {
        return remainingUnits;
    };

    this.toggleUser = function (userId, credit) {
        if ($.inArray(userId, users) == -1) {
            if (remainingUnits > 0 && unitCap === true) {
                this.addUser(userId);
                if (credit !== false) {
                    this.setRemainingunits(remainingUnits - 1);
                }
            } else if (unitCap === false) {
                this.addUser(userId);
            }
        } else {
            this.removeUser(userId);
            if (credit !== false && unitCap === true) {
                this.setRemainingunits(remainingUnits + 1);
            }
        }
    };

    this.addUser = function (userId) {
        if ($.inArray(userId, users) == -1) {
            users.push(userId);
        }
    };

    this.removeUser = function (userId) {
        users = jQuery.grep(users, function (value) {
            return value != userId;
        });
    };

    this.getUsers = function () {
        return users;
    };

    this.clear = function () {
        this.setRemainingunits(users.length + this.getRemainingUnits());
        users = [];
    };
};

var suggestedUsersForInvitation = function (unitCap) {

    var users = {};
    var remainingUnits = 0;

    this.setRemainingUnits = function (units) {
        remainingUnits = units;
    };

    this.getRemainingUnits = function () {
        return remainingUnits;
    };

    this.addSpace = function (userId) {
        if ((userId in users) && remainingUnits > 0 && users[userId] < 4) {
            users[userId]++;
            this.setRemainingUnits(remainingUnits - 1);
        }
    };

    this.removeSpace = function (userId) {
        if (userId in users) {
            users[userId]--;
            this.setRemainingUnits(remainingUnits + 1);
            if (users[userId] === 0) {
                this.removeUser(userId);
            }
        }
    };

    this.toggleUser = function (userId, credit) {
        if (!(userId in users)) {
            if (remainingUnits > 0 && unitCap === true) {
                this.addUser(userId);
                if (credit !== false) {
                    this.setRemainingUnits(remainingUnits - 1);
                }
            } else if (unitCap === false) {
                this.addUser(userId);
            }
        } else {
            if (credit !== false && unitCap === true) {
                this.setRemainingUnits(remainingUnits + users[userId]);
            }
            this.removeUser(userId);
        }
    };

    this.addUser = function (userId) {
        if (!(userId in users)) {
            users[userId] = 1;
        }
    };

    this.removeUser = function (userId) {
        delete users[userId];
    };

    this.getUsers = function () {
        return users;
    };

    this.clear = function () {
        var me = this;
        $.each(users, function (userId, spaces) {
            me.setRemainingUnits(remainingUnits + spaces);
        });
        users = {};
    };
};

var addToGroupUsersList = function (groupId) {

    this.role = 'All';
    this.name = null;
    this.email = null;
    this.reference = null;
    this.accountActive = false;
    this.accountDeactivated = false;
    this.accountPending = false;
    this.metricsOnly = false;
    this.group = 'all';
    this.Login = 'All';
    this.pageNumber = 1;
    this.suggestions = false;

    this.setGroup = function (group) {
        this.group = group;
    };

    this.setMetricsOnly = function (state) {
        this.metricsOnly = state;
    };

    this.getMetricsOnly = function () {
        return this.metricsOnly;
    };

    this.setRole = function (role) {
        this.role = role;
    };

    this.getRole = function () {
        return this.role;
    };

    this.setName = function (name) {
        this.name = name;
    };

    this.getName = function () {
        return this.name;
    };

    this.setEmail = function (email) {
        this.email = email;
    };

    this.getEmail = function () {
        return this.email;
    };

    this.setReference = function (reference) {
        this.reference = reference;
    };

    this.getReference = function () {
        return this.reference;
    };

    this.setAccountActive = function (account) {
        this.accountActive = account;
    };

    this.getAccountActive = function () {
        return this.accountActive;
    };

    this.setAccountDeactivated = function (account) {
        this.accountDeactivated = account;
    };

    this.getAccountDeactivated = function () {
        return this.accountDeactivated;
    };

    this.setAccountPending = function (account) {
        this.accountPending = account;
    };

    this.getAccountPending = function () {
        return this.accountPending;
    };

    this.setLogin = function (login) {
        this.login = login;
    };

    this.getLogin = function () {
        return this.login;
    };

    this.setPageNumber = function (pageNumber) {
        this.pageNumber = pageNumber;
    };

    this.getPageNumber = function () {
        return this.pageNumber;
    };

    this.getGroup = function () {
        return this.group;
    };

    this.setSuggestions = function (suggeestions) {
        this.suggestions = suggeestions;
    };

    this.getSuggestions = function () {
        return this.suggestions;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/invite/ajaxGetSuggestedUsersForGroup/' + groupId,
            dataType: 'json',
            data: {
                "suggestions": this.getSuggestions(),
                "pageNumber": this.getPageNumber(),
                "group": this.getGroup(),
                "metricsOnly": this.getMetricsOnly(),
                "roleName": this.getRole(),
                "name": this.getName(),
                "email": this.getEmail(),
                "reference": this.getReference(),
                "accountActive": this.getAccountActive(),
                "accountDeactivated": this.getAccountDeactivated(),
                "accountPending": this.getAccountPending(),
                "login": this.getLogin(),
                'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN
            }
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
};