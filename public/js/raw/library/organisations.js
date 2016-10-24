/**
 * Created by Daniel Dimmick on 24/03/15.
 */
function AjaxGetPortalOrganisations() {

    this.portalId = null;
    this.hasUsersInRole = null;

    this.getPortalId = function () {
        return this.portalId;
    };

    this.setPortalId = function (portalId) {
        this.portalId = portalId;
    };

    this.setHasUsersInRole = function (role) {
        this.hasUsersInRole = role;
    };

    this.ajax = function () {
        return $.ajax({
            url: "/api/getPortalOrganisations/" + this.getPortalId(),
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {
                CSRF_SESSION_TOKEN: CSRF_SESSION_TOKEN,
                hasUsersInRole: this.hasUsersInRole
            }
        });
    };

    this.fetch = function () {
        return this.ajax();
    }
}

function AjaxAdminSearchOrganisations() {

    this.pageNumber = 1;
    this.postData = null;

    this.setPostData = function (postData) {
        this.postData = postData;
    };

    this.getPostData = function () {
        return this.postData;
    };

    this.getPageNumber = function () {
        return this.pageNumber;
    };

    this.setPageNumber = function (pageNumber) {
        if (typeof pageNumber !== 'undefined') {
            this.pageNumber = pageNumber;
        }
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/items/AjaxSearchOrgs/' + this.getPageNumber(),
            dataType: 'json',
            data: this.getPostData()
        });
    };

    this.fetch = function () {
        return this.ajax();
    }
}