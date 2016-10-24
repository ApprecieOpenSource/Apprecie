/**
 * Created by hu86 on 23/11/2015.
 */

function SearchContacts() {

    this.role = 'Contact';
    this.name = null;
    this.email = null;
    this.reference = null;
    this.accountActive = false;
    this.accountDeactivated = false;
    this.accountPending = true;
    this.metricsOnly = false;
    this.group = 'all';
    this.Login = 'enabled';
    this.pageNumber = 1;

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

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/contacts/AjaxSearch/' + this.getPageNumber(),
            dataType: 'json',
            data: {
                "group": this.getGroup(),
                "metricsOnly": this.getMetricsOnly(),
                "roleName": this.getRole(),
                "name": this.getName(),
                "email": this.getEmail(),
                "reference": this.getReference(),
                "accountActive": this.getAccountActive(),
                "accountDeactivated": this.getAccountDeactivated(),
                "accountPending": this.getAccountPending(),
                "login": this.getLogin()
            }
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

function DeleteContact() {

    this.userId = null;

    this.setUserId = function (userId) {
        this.userId = userId;
    };

    this.getUserId = function () {
        return this.userId;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/contacts/AjaxDeleteContact/' + this.getUserId(),
            dataType: 'json',
            data:{'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}