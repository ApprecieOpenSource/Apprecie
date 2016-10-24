/**
 * Created by Daniel Dimmick on 25/03/15.
 */
function CreateGroup() {

    this.setGroupName = function (groupName) {
        this.groupName = groupName;
    };

    this.getGroupName = function () {
        return this.groupName;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/groups/AjaxCreateGroup',
            dataType: 'json',
            data: {"groupname": this.getGroupName(), "CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

function DeleteGroup(groupId) {

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/groups/AjaxDeleteGroup',
            dataType: 'json',
            data: {"groupId": groupId, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

function EditGroup(groupId) {

    var groupName = null;

    this.setGroupName = function (newGroupName) {
        groupName = newGroupName;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/groups/AjaxEditGroup',
            dataType: 'json',
            data: {"groupId": groupId, "groupName": groupName, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

function GetGroups() {

    this.pageNumber = 1;

    this.setPageNumber = function (pageNumber) {
        this.pageNumber = pageNumber;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/groups/AjaxGetGroups/' + this.pageNumber,
            dataType: 'json',
            data: {'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

function GetUsersInGroup() {

    this.pageNumber = 1;
    this.role = 'All';
    this.groupId = null;

    this.getPageNumber = function () {
        return this.pageNumber;
    };

    this.setPageNumber = function (pageNumber) {
        this.pageNumber = pageNumber;
    };

    this.getRole = function () {
        return this.role;
    };

    this.setRole = function (role) {
        this.role = role;
    };

    this.setGroupId = function (groupId) {
        this.groupId = groupId;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/groups/AjaxGetUsersInGroup/' + this.getPageNumber(),
            dataType: 'json',
            data: {"role": this.getRole(), "groupId": this.groupId, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

function GetAllUsersInGroup(groupIdNumber) {

    this.groupId = groupIdNumber;

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/groups/AjaxGetAllUsersInGroup/',
            dataType: 'json',
            data: {"groupId": this.groupId, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

function AddUsersToGroup() {

    this.groupId = null;
    this.usersArray = [];

    this.setGroupId = function (groupId) {
        this.groupId = groupId;
    };

    this.setUsersArray = function (users) {
        this.usersArray = users;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/groups/AjaxAddUserToGroup/',
            dataType: 'json',
            data: {"users": this.usersArray, "groupId": this.groupId, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}

function RemoveUserFromGroup() {

    this.groupId = null;
    this.userId = null;

    this.setGroupId = function (groupId) {
        this.groupId = groupId;
    };

    this.setUserId = function (userId) {
        this.userId = userId;
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/groups/AjaxRemoveFromGroup/',
            dataType: 'json',
            data: {"users": this.userId, "groupId": this.groupId, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    };

    this.fetch = function () {
        return this.ajax();
    };
}