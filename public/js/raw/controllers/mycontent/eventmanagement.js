/**
 * Created by Daniel Dimmick on 29/04/15.
 */
$(document).ready(function () {
    var packageSize = $('#package-size').text().replace(/[^\d.-]/g, '');
    ;
    var costPer = $('#cost-per-unit').text().replace(/[^\d.-]/g, '');
    ;
    var maxPackages = $('#max-units').text().replace(/[^\d.-]/g, '');
    ;
    var staticCosts = $('#cost-to-deliver').text().replace(/[^\d.-]/g, '');
    ;

    var total = Number(packageSize * costPer * maxPackages) + Number(staticCosts);

    if (isNaN(total)) {
        $('#estimate-total-cost').text('');
    } else if (total == 0) {
        $('#estimate-total-cost').text('TBC');
    } else {
        $('#estimate-total-cost').text(total.toFixed(2));
    }
    getGuestList(1);
});

function getGuestList(pageNumber) {

    var guestlist = new SupplierGuestList();

    guestlist.setPageNumber(pageNumber);
    guestlist.setPostData({
        "itemid": itemId,
        "attending": 'true',
        "status": 'confirmed',
        'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN
    });

    $.when(guestlist.fetch()).then(function (data) {
        if (data.total_items == 0) {
            buffer = '<tr><td colspan="6">' + data.message + '</td></tr>';
        } else {
            var buffer = '';
            $.each(data.items, function (key, value) {
                buffer += '<tr><td>' + value.profile.firstname + ' ' + value.profile.lastname + '</td><td>' + value.role + '</td><td>' + value.organisation + '</td><td class="hidden-xs"><a href="mailto:' + value.profile.email + '">' + value.profile.email + '</a></td><td class="hidden-xs">' + value.guest.spaces + '</td><td>' + value.diet + '</td></tr>';
            })
        }
        $('#attending-tbl').html(buffer);
        $('#guest-count').html(data.spaceCount);
        Pagination(data, 'getGuestList', $('#attending-pagination'));
    })
}

