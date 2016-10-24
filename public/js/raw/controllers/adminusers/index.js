/**
 * Created by Daniel Dimmick on 24/03/15.
 */
$(document).ready(function () {

    var portalSelect = $('#portalid');
    var searchBtn = $('#submit-btn');

    if (portalSelect.val() != null) {
        SearchPortalUsers(1);
        populateOrganisations();
    } else {
        searchBtn.prop('disabled', true);
    }

    portalSelect.change(function () {
        populateOrganisations();
        searchBtn.prop('disabled', false);
    });

    $('#impersonate').on('show.bs.modal', function (e) {

        var userId = $(e.relatedTarget).data('user-id');
        var portalId = $(e.relatedTarget).data('portal-id');

        $('#impersonate-btn').prop('disabled', true);
        $.when(AjaxCheckAccountLock(userId)).then(function (data) {
            $('#impersonate-btn').prop('disabled', false);
            if (data.status == 'true') {
                $('#account-locked').show();
            }
        });

        $("#impersonate-btn").off().on('click', function () {
            window.location.replace("/adminusers/ImpersonateUser/" + userId + "?portalid=" + portalId);
        });
    });

    $('#impersonate').on('hide.bs.modal', function (e) {
        $('#account-locked').css("display", "none");
    });
});

function AjaxCheckAccountLock(userId) {
    return $.ajax({
        url: "/adminusers/ajaxCheckAccountLock",
        type: 'post',
        dataType: 'json',
        cache: false,
        data: 'userId=' + userId
    });
}

function advancedSearch() {
    $('#advanced-search').toggle();
}

function SearchPortalUsers(pageNumber) {
    var portalUsers = new AjaxPortalUsers();
    portalUsers.setPageNumber(pageNumber);
    portalUsers.setPostData($('#user-search-form').serialize());

    $('#user-search-results').css('opacity', 0.4);
    $.when(portalUsers.fetch()).then(function (data) {
        $('#no-results').css('display', 'none');
        $('#no-results-found').css('display', 'none');
        $('#user-search-results').empty();
        $('#user-search-results').css('opacity', 1);
        if (data.items.length > 0) {
            $.each(data.items, function (key, value) {
                var buffer = '<tr>' +
                    '<td>' + value.image + '</td>' +
                    '<td><a href="/adminusers/viewuser/' + value.userId + '">' + value.firstname + ' ' + value.lastname + '</a></td>' +
                    '<td class="hidden-xs"><a href="/adminusers/viewuser/' + value.userId + '">' + value.reference + '</a></td>' +
                    '<td class="hidden-xs">' + value.email + '</td>' +
                    '<td class="hidden-xs"><a href="/adminorgs/view/' + value.organisationId + '">' + value.organisationName + '</a></td>' +
                    '<td class="hidden-xs">' + value.role + '</td>' +
                    '<td>' + value.registrationState + '</td>';
                if (value.impersonate == 1) {
                    buffer += '<td>';
                    buffer += '<a href="#" data-target="#impersonate" data-toggle="modal" data-user-id="' + value.userId + '" data-portal-id="' + value.portalId + '">';
                    buffer += '<span class="label label-success label-lg" title="Active"><i class="fa fa-power-off"></i></span>';
                    buffer += '</a></td>';
                }
                else {
                    buffer += '<td></td>';
                }
                buffer += '</tr>';
                $('#user-search-results').append(buffer);

            });
            Pagination(data, 'SearchPortalUsers', $('#user-pagination'));
        }
    })
}

function populateOrganisations() {
    var currentOrg = $('#organisationId');
    currentOrg.prop('disabled', true);
    currentOrg.html('<option selected value="All">All</option>');

    var organisations = new AjaxGetPortalOrganisations();
    organisations.setPortalId($('#portalid').val());

    $.when(organisations.fetch()).then(function (data) {
        currentOrg.prop('disabled', false);
        $.each(data, function (key, value) {
            if (currentOrg.val() == value.organisationId) {
                $('#organisationId').append('<option selected value="' + value.organisationId + '">' + value.organisationName + '</option>');
            }
            else {
                $('#organisationId').append('<option value="' + value.organisationId + '">' + value.organisationName + '</option>');
            }
        })
    })
}