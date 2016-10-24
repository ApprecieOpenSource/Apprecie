<script src="/js/compiled/public/js/raw/library/organisations.min.js"></script>
<script src="/js/compiled/public/js/raw/library/items.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script>
    var itemIdsFromSearchResults;
    var orgIdsFromSearchResults;
    var CSRF_SESSION_TOKEN = '<?= (new \Apprecie\Library\Security\CSRFProtection())->getSessionToken(); ?>';

    $(document).ready(function () {

        var addItemCol = $('#add-item-col');
        var addOrgCol = $('#add-org-col');
        var itemListCol = $('#item-list-col');
        var orgListCol = $('#org-list-col');
        var curateCol = $('#curate-col');

        var addItemBtn = $('#add-item-btn');
        var clearItemBtn = $('#clear-item-btn');
        var closeItemSearchBtn = $('#close-item-search-btn');
        var itemSearchSelectAllBtn = $('#item-search-select-all-btn');
        var itemSearchDeselectAllBtn = $('#item-search-deselect-all-btn');
        var addOrgBtn = $('#add-org-btn');
        var clearOrgBtn = $('#clear-org-btn');
        var closeOrgSearchBtn = $('#close-org-search-btn');
        var orgSearchSelectAllBtn = $('#org-search-select-all-btn');
        var orgSearchDeselectAllBtn = $('#org-search-deselect-all-btn');

        var curateBtn = $('#curate-btn');

        var successMsg = $('#success-msg');
        var warningMsg = $('#warning-msg');
        var errorMsg = $('#error-msg');

        curateBtn.on('click', function () {

            $(this).prop('disabled', true);
            $(this).text('Processing...');
            successMsg.hide();
            warningMsg.hide();
            errorMsg.hide();

            $.when(ajaxCurate()).then(function (data) {

                if (data.status === 'success') {
                    if (data.messages.length) {
                        $.each(data.messages, function (index, value) {
                            $('#warning-msg-list').append('<li>' + value + '</li>');
                        });
                        warningMsg.show();
                    } else {
                        successMsg.show();
                    }
                } else if (data.status === 'failed') {
                    errorMsg.show();
                }

                curateBtn.prop('disabled', false);
                curateBtn.html('<?= _g('Curate'); ?>&nbsp;<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>');
            });
        });

        addItemBtn.on('click', function () {
            orgListCol.toggle();
            curateCol.toggle();
            addItemCol.toggle();
        });

        clearItemBtn.on('click', function () {
            $.when(ajaxClearItems()).then(function (data) {
                if (data.status === 'success') {
                    $('.item-search-result-row input:checkbox:checked').prop('checked', false);
                    $('.item-row, .item-org-row').remove();
                    $('#item-message').show();

                    canCurate(false);
                }
            });
        });

        closeItemSearchBtn.on('click', function () {
            orgListCol.show();
            curateCol.show();
            addItemCol.hide();
        });

        addOrgBtn.on('click', function () {
            itemListCol.toggle();
            curateCol.toggle();
            addOrgCol.toggle();
        });

        clearOrgBtn.on('click', function () {
            $.when(ajaxClearOrgs()).then(function (data) {
                if (data.status === 'success') {
                    $('.org-search-result-row input:checkbox:checked').prop('checked', false);
                    $('.org-row, .org-portal-row').remove();
                    $('#org-message').show();

                    canCurate(false);
                }
            });
        });

        closeOrgSearchBtn.on('click', function () {
            itemListCol.show();
            curateCol.show();
            addOrgCol.hide();
        });

        $('#item-search-portal-id').change(function () {
            populateOrganisations($('#item-search-org-id'), $(this).val());
        });

        $('#item-search-btn').on('click', function () {
            itemSearch(1);
        });

        $('#org-search-btn').on('click', function () {
            orgSearch(1);
        });

        itemSearchSelectAllBtn.on('click', function () {
            if (itemIdsFromSearchResults.length) {
                $.when(ajaxAddOrRemoveItem(itemIdsFromSearchResults, 'add')).then(function (data) {
                    if (data.status === 'success') {
                        $.each(data.items, function (key, item) {

                            $('#item-list-message').hide();
                            if (!$('#itemListRow' + item.itemId).length) {
                                $('#item-list').append(generateItemListRowHtml(item));
                            }

                            var checkbox = $('#itemCheckbox' + item.itemId);
                            if (checkbox.length) {
                                checkbox.prop('checked', true);
                            }

                            $('#org-search-results').empty();
                            $('#org-search-result-div').hide();
                        });
                    }
                });
            }
        });

        orgSearchSelectAllBtn.on('click', function () {
            if (orgIdsFromSearchResults.length) {
                $.when(ajaxAddOrRemoveOrg(orgIdsFromSearchResults, 'add')).then(function (data) {
                    if (data.status === 'success') {
                        $.each(data.orgs, function (key, org) {

                            $('#org-list-message').hide();
                            if (!$('#orgListRow' + org.orgId).length) {
                                $('#org-list').append(generateOrgListRowHtml(org));
                            }

                            var checkbox = $('#orgCheckbox' + org.orgId);
                            if (checkbox.length) {
                                checkbox.prop('checked', true);
                            }

                            $('#item-search-results').empty();
                            $('#item-search-result-div').hide();
                        });
                    }
                });
            }
        });

        itemSearchDeselectAllBtn.on('click', function () {
            if (itemIdsFromSearchResults.length) {
                $.when(ajaxAddOrRemoveItem(itemIdsFromSearchResults, 'remove')).then(function (data) {
                    if (data.status === 'success') {
                        $.each(data.items, function (key, item) {
                            removeItemFromUI(item);
                        });
                    }
                });
            }
        });

        orgSearchDeselectAllBtn.on('click', function () {
            if (orgIdsFromSearchResults.length) {
                $.when(ajaxAddOrRemoveOrg(orgIdsFromSearchResults, 'remove')).then(function (data) {
                    if (data.status === 'success') {
                        $.each(data.orgs, function (key, org) {
                            removeOrgFromUI(org);
                        });
                    }
                });
            }
        });

        $('#item-search-pricing-type').on('change', function () {
            if ($(this).val() === 'fixed') {
                $('.price-range').show();
            } else {
                $('.price-range').hide();
            }
        });
    });

    function canCurate(canCurate) {
        if (canCurate === true) {
            $('#curate-btn').prop('disabled', false);
        } else {
            $('#curate-btn').prop('disabled', true);
        }
    }

    function populateOrganisations(orgIdElement, portalId) {

        orgIdElement.html('<option selected value="all">All</option>');

        if (portalId !== 'all') {

            orgIdElement.prop('disabled', true);

            var organisations = new AjaxGetPortalOrganisations();
            organisations.setPortalId(portalId);
            organisations.setHasUsersInRole('<?= \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER; ?>');

            $.when(organisations.fetch()).then(function (data) {
                $.each(data, function (key, value) {
                    orgIdElement.append('<option value="' + value.organisationId + '">' + value.organisationName + '</option>');
                });
                orgIdElement.prop('disabled', false);
            })
        }
    }

    function itemSearch(pageNumber) {

        if (typeof pageNumber === 'undefined') {
            pageNumber = 1;
        }

        var ajaxSearchItems = new AjaxAdminSearchItems();
        ajaxSearchItems.setPageNumber(pageNumber);
        ajaxSearchItems.setPostData($('#item-search-form').serialize());

        var itemSearchResults = $('#item-search-results');

        $.when(ajaxSearchItems.fetch()).then(function (data) {

            itemSearchResults.empty();
            itemIdsFromSearchResults = [];

            if (data.items.length > 0) {

                $('#item-search-no-results-found').hide();

                $.each(data.items, function (key, value) {

                    itemIdsFromSearchResults.push(value.item.itemId);

                    var buffer = '<tr class="item-search-result-row">';
                    buffer += '<td></td>';
                    buffer += '<td><input type="checkbox" id="itemCheckbox' + value.item.itemId + '" onchange="addOrRemoveItemFromSearch(' + value.item.itemId + ');"' + value.checked + '></td>';
                    buffer += '<td><a href="/items/viewEvent/' + value.item.itemId + '">' + value.item.title + '</a></td>';
                    buffer += '<td>' + value.organisationName + '</td>';
                    buffer += '<td>' + value.price + '</td>';
                    buffer += '<td>' + value.eventType + '</td>';
                    buffer += '<td></td>';
                    buffer += '</tr>';

                    itemSearchResults.append(buffer);
                });

                Pagination(data, 'itemSearch', $('#item-pagination'));
            } else {
                $('#item-search-no-results-found').show();
            }

            $('#item-search-result-div').show();
        });
    }

    function orgSearch(pageNumber) {

        if (typeof pageNumber === 'undefined') {
            pageNumber = 1;
        }

        var ajaxSearchOrgs = new AjaxAdminSearchOrganisations();
        ajaxSearchOrgs.setPageNumber(pageNumber);
        ajaxSearchOrgs.setPostData($('#org-search-form').serialize());

        var orgSearchResults = $('#org-search-results');

        $.when(ajaxSearchOrgs.fetch()).then(function (data) {

            orgSearchResults.empty();
            orgIdsFromSearchResults = [];

            if (data.items.length > 0) {

                $('#org-search-no-results-found').hide();

                $.each(data.items, function (key, value) {

                    orgIdsFromSearchResults.push(value.org.organisationId);

                    var buffer = '<tr class="org-search-result-row">';
                    buffer += '<td></td>';
                    buffer += '<td><input type="checkbox" id="orgCheckbox' + value.org.organisationId + '" onchange="addOrRemoveOrgFromSearch(' + value.org.organisationId + ');"' + value.checked + '></td>';
                    buffer += '<td>' + value.org.organisationName + '</td>';
                    buffer += '<td>' + value.portalName + '</td>';
                    buffer += '<td>' + value.portalEdition + '</td>';
                    buffer += '<td></td>';
                    buffer += '</tr>';

                    orgSearchResults.append(buffer);
                });

                Pagination(data, 'orgSearch', $('#org-pagination'));
            } else {
                $('#org-search-no-results-found').show();
            }

            $('#org-search-result-div').show();
        });
    }

    function addOrRemoveItemFromSearch(itemId) {

        var checkbox = $('#itemCheckbox' + itemId);
        var action;

        if (checkbox.is(':checked')) {
            action = 'add';
        } else {
            action = 'remove';
        }

        $.when(ajaxAddOrRemoveItem(itemId, action)).then(function (data) {
            if (data.status === 'success') {

                if (data.action === 'add') {
                    $('#item-list-message').hide();
                    $('#item-list').append(generateItemListRowHtml(data.items[0]));
                } else if (data.action === 'remove') {
                    $('#itemListRow' + data.items[0].itemId).remove();
                    if ($('#item-list').children().length === 1) {
                        $('#item-list-message').show();
                    }
                }

                $('#org-search-results').empty();
                $('#org-search-result-div').hide();

                if (data.canCurate === 'true') {
                    canCurate(true);
                } else {
                    canCurate(false);
                }
            } else if (checkbox.is(':checked')) {
                checkbox.prop('checked', false);
            } else {
                checkbox.prop('checked', true);
            }
        });
    }

    function addOrRemoveOrgFromSearch(orgId) {

        var checkbox = $('#orgCheckbox' + orgId);
        var action;

        if (checkbox.is(':checked')) {
            action = 'add';
        } else {
            action = 'remove';
        }

        $.when(ajaxAddOrRemoveOrg(orgId, action)).then(function (data) {
            if (data.status === 'success') {

                if (data.action === 'add') {
                    $('#org-list-message').hide();
                    $('#org-list').append(generateOrgListRowHtml(data.orgs[0]));
                } else if (data.action === 'remove') {
                    $('#orgListRow' + data.orgs[0].orgId).remove();
                    if ($('#org-list').children().length === 1) {
                        $('#org-list-message').show();
                    }
                }

                $('#item-search-results').empty();
                $('#item-search-result-div').hide();

                if (data.canCurate === 'true') {
                    canCurate(true);
                } else {
                    canCurate(false);
                }
            } else if (checkbox.is(':checked')) {
                checkbox.prop('checked', false);
            } else {
                checkbox.prop('checked', true);
            }
        });
    }

    function removeItem(itemId) {

        $.when(ajaxAddOrRemoveItem(itemId, 'remove')).then(function (data) {
            if (data.status === 'success') {
                removeItemFromUI(data.items[0]);
                if (data.canCurate === 'true') {
                    canCurate(true);
                } else {
                    canCurate(false);
                }
            }
        });
    }

    function removeItemFromUI(data) {

        $('#itemListRow' + data.itemId).remove();
        if ($('#item-list').children().length === 1) {
            $('#item-list-message').show();
        }

        var checkbox = $('#itemCheckbox' + data.itemId);
        if (checkbox.length) {
            checkbox.prop('checked', false);
        }

        $('#org-search-results').empty();
        $('#org-search-result-div').hide();
    }

    function removeOrg(orgId) {

        $.when(ajaxAddOrRemoveOrg(orgId, 'remove')).then(function (data) {
            if (data.status === 'success') {
                removeOrgFromUI(data.orgs[0]);
                if (data.canCurate === 'true') {
                    canCurate(true);
                } else {
                    canCurate(false);
                }
            }
        });
    }

    function removeOrgFromUI(data) {
        $('#orgListRow' + data.orgId).remove();
        if ($('#org-list').children().length === 1) {
            $('#org-list-message').show();
        }

        var checkbox = $('#orgCheckbox' + data.orgId);
        if (checkbox.length) {
            checkbox.prop('checked', false);
        }

        $('#item-search-results').empty();
        $('#item-search-result-div').hide();
    }

    function ajaxAddOrRemoveItem(itemId, action) {
        return $.ajax({
            url: "/items/AjaxEditCurateItemList",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {
                itemId: itemId,
                action: action,
                'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN
            }
        });
    }

    function ajaxAddOrRemoveOrg(orgId, action) {
        return $.ajax({
            url: "/items/AjaxEditCurateOrgList",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {
                orgId: orgId,
                action: action,
                'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN
            }
        });
    }

    function ajaxClearItems() {
        return $.ajax({
            url: "/items/AjaxClearItems",
            type: 'post',
            dataType: 'json',
            cache: false,
            data : {'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    }

    function ajaxClearOrgs() {
        return $.ajax({
            url: "/items/AjaxClearOrgs",
            type: 'post',
            dataType: 'json',
            cache: false,
            data : {'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}
        });
    }

    function ajaxCurate() {
        return $.ajax({
            url: "/items/AjaxCurate",
            type: 'post',
            dataType: 'json',
            data: {'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN},
            cache: false
        });
    }

    function generateItemListRowHtml(data) {
        var buffer = '<tr id="itemListRow' + data.itemId + '" class="item-row"><td>';
        buffer += '<span>&nbsp;</span><a href="/items/viewEvent/' + data.itemId + '" target="_blank">' + data.title + '</a>';
        buffer += '<a href="javascript:void(0);"><span class="glyphicon glyphicon-remove pull-right" aria-hidden="true" onclick="removeItem(' + data.itemId + ');"></span></a>';
        buffer += '</td></tr>';
        return buffer;
    }

    function generateOrgListRowHtml(data) {
        var buffer = '<tr id="orgListRow' + data.orgId + '" class="org-row"><td>';
        buffer += '<span>&nbsp;</span>' + data.name;
        buffer += '<a href="javascript:void(0);"><span class="glyphicon glyphicon-remove pull-right" aria-hidden="true" onclick="removeOrg(' + data.orgId + ');"></span></a>';
        buffer += '</td></tr>';
        return buffer;
    }
</script>
<style>
    .ibox-content {
        min-height: 150px;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2>Item Curation</h2>

        <div class="alert alert-success" id="success-msg" role="alert" style="display: none;">
            <strong><?= _g('Success!'); ?></strong>&nbsp;<?= _g('All selected items have been curated to all selected organisations.'); ?>
        </div>
        <div class="alert alert-warning" id="warning-msg" role="alert" style="display: none;">
            <strong><?= _g('Warning!'); ?></strong>&nbsp;<?= _g('Not all items were processed successfully.'); ?>
            <ul id="warning-msg-list"></ul>
        </div>
        <div class="alert alert-danger" id="error-msg" role="alert" style="display: none;">
            <strong><?= _g('Failed!'); ?></strong>&nbsp;<?= _g('Please select at least one item and one organisation.'); ?>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-sm-4" id="item-list-col">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= _g('Items'); ?></h5>
                <a class="pull-right" href="javascript:void(0);" id="add-item-btn"
                   style="padding: 0 5px;"><?= _g('Add'); ?></a>
                <a class="pull-right" href="javascript:void(0);" id="clear-item-btn"
                   style="padding: 0 5px;"><?= _g('Clear'); ?></a>
            </div>
            <div class="ibox-content no-padding" style="max-height: 1100px; overflow-y: auto;">
                <table class="table">
                    <tbody id="item-list">
                    <?php if ($this->view->items): ?>
                        <?php foreach ($this->view->items as $item): ?>
                            <tr id="itemListRow<?= $item->getItemId(); ?>" class="item-row">
                                <td>
                                    <span>&nbsp;</span>
                                    <a href="/items/viewEvent/<?= $item->getItemId(); ?>"
                                       target="_blank"><?= $item->getTitle(); ?></a>
                                    <a href="javascript:void(0);"><span class="glyphicon glyphicon-remove pull-right"
                                                                        aria-hidden="true"
                                                                        onclick="removeItem(<?= $item->getItemId(); ?>);"></span></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr id="item-list-message" style="<?= $this->view->items ? 'display: none;' : ''; ?>">
                        <td><?= _g('Please click Add button to search and add items to this list.'); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-sm-8" id="add-item-col" style="display: none;">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Search'); ?></h5>
                <a class="pull-right" href="javascript:void(0);" id="close-item-search-btn"
                   style="padding: 0 5px;"><?= _g('Close'); ?></a>
            </div>
            <div class="ibox-content">
                <form method="post" enctype="multipart/form-data" action="" id="item-search-form"
                      name="item-search-form" class="form-horizontal">
                    {{csrf()}}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="portal-name" class="control-label"><?= _g('Portal'); ?></label>
                                <select class="form-control" id="item-search-portal-id" name="portalId">
                                    <option value="all" selected><?= _g('All'); ?></option>
                                    <?php foreach ($this->view->itemSearchPortals as $portal): ?>
                                        <option
                                            value="<?= $portal->getPortalId(); ?>"><?= $portal->getPortalName(); ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="item-search-org-id" class="control-label"><?= _g('Organisation'); ?></label>
                                <select id="item-search-org-id" name="organisationId" class="form-control">
                                    <option value="all"><?= _g('All'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="item-search-pricing-type" class="control-label"><?= _g('Pricing Type'); ?></label>
                                <select id="item-search-pricing-type" name="pricingType" class="form-control">
                                    <option value="all"><?= _g('All'); ?></option>
                                    <option value="tbc"><?= _g('TBC'); ?></option>
                                    <option value="complimentary"><?= _g('Complimentary Event'); ?></option>
                                    <option value="fixed"><?= _g('Priced'); ?></option>
                                </select>
                            </div>
                            <div class="form-group price-range" style="display: none;">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><?= _g('MIN'); ?></span>
                                    <input id="item-search-price-min" name="priceMin" type="number" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group price-range" style="display: none;">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><?= _g('MAX'); ?></span>
                                    <input id="item-search-price-max" name="priceMax" type="number" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="item-search-event-type" class="control-label"><?= _g('Event Type'); ?></label>
                                <select id="item-search-event-type" name="eventType" class="form-control">
                                    <option value="all"><?= _g('All'); ?></option>
                                    <option value="confirmed"><?= _g('Confirmed Event'); ?></option>
                                    <option value="ba"><?= _g('By Arrangement Event'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <button class="btn btn-primary" id="item-search-btn" name="submit-btn">Search</button>
            </div>
            <div class="ibox-content" style="display: none;margin-bottom: 15px;" id="item-search-result-div">
                <div class="btn-group" role="group" aria-label="..." style="margin-bottom: 15px;">
                    <button class="btn btn-default" id="item-search-select-all-btn"
                            name="submit-btn"><?= _g('Select All'); ?></button>
                    <button class="btn btn-default" id="item-search-deselect-all-btn"
                            name="submit-btn"><?= _g('Deselect All'); ?></button>
                </div>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th><?= _g('Title'); ?></th>
                        <th><?= _g('Organisation'); ?></th>
                        <th><?= _g('Price'); ?></th>
                        <th class="hidden-xs"><?= _g('Event'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="item-search-results">

                    </tbody>
                </table>
                <div style="display: none;" class="alert alert-info" id="item-search-no-results-found" role="alert">
                    <strong><?= _g('No Items.'); ?></strong>&nbsp;<?= _g("We couldn't find any items that match your search criteria."); ?>
                </div>
                <nav>
                    <ul class="pagination" id="item-pagination">

                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="col-sm-4" id="curate-col">
        <p>Please create a list of items that you want to curate and a list of target organisations. Once you are happy,
            click "Curate".</p>
        <button type="button" class="btn btn-primary btn-block" id="curate-btn" disabled><?= _g('Curate'); ?>&nbsp;<span
                class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span></button>
    </div>

    <div class="col-sm-8" id="add-org-col" style="display: none;">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Search'); ?></h5>
                <a class="pull-right" href="javascript:void(0);" id="close-org-search-btn"
                   style="padding: 0 5px;"><?= _g('Close'); ?></a>
            </div>
            <div class="ibox-content">
                <form method="post" enctype="multipart/form-data" action="" id="org-search-form" name="org-search-form"
                      class="form-horizontal">
                    {{csrf()}}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="org-name" class="control-label"><?= _g('Organisation Name'); ?></label>
                                <input id="org-name" name="name" type="text" class="form-control" maxlength="100"/>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="portal-name" class="control-label"><?= _g('Portal'); ?></label>
                                <select class="form-control" id="org-search-portal-id" name="portalId">
                                    <option value="all" selected><?= _g('All'); ?></option>
                                    <?php foreach ($this->view->orgSearchPortals as $portal): ?>
                                        <option
                                            value="<?= $portal->getPortalId(); ?>"><?= $portal->getPortalName(); ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <button class="btn btn-primary" id="org-search-btn" name="submit-btn">Search</button>
            </div>
            <div class="ibox-content" style="display: none;margin-bottom: 15px;" id="org-search-result-div">
                <div class="btn-group" role="group" aria-label="..." style="margin-bottom: 15px;">
                    <button class="btn btn-default" id="org-search-select-all-btn"
                            name="submit-btn"><?= _g('Select All'); ?></button>
                    <button class="btn btn-default" id="org-search-deselect-all-btn"
                            name="submit-btn"><?= _g('Deselect All'); ?></button>
                </div>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th><?= _g('Name'); ?></th>
                        <th><?= _g('Portal'); ?></th>
                        <th><?= _g('Portal Edition'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="org-search-results">

                    </tbody>
                </table>
                <div style="display: none;" class="alert alert-info" id="org-search-no-results-found" role="alert">
                    <strong><?= _g('No Organisations.'); ?></strong>&nbsp;<?= _g("We couldn't find any organisations that match your search criteria."); ?>
                </div>
                <nav>
                    <ul class="pagination" id="org-pagination">

                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="col-sm-4" id="org-list-col">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Organisations'); ?></h5>
                <a class="pull-right" href="javascript:void(0);" id="add-org-btn"
                   style="padding: 0 5px;"><?= _g('Add'); ?></a>
                <a class="pull-right" href="javascript:void(0);" id="clear-org-btn"
                   style="padding: 0 5px;"><?= _g('Clear'); ?></a>
            </div>
            <div class="ibox-content no-padding" style="max-height: 1100px; overflow-y: auto;">
                <table class="table">
                    <tbody id="org-list">
                    <?php if ($this->view->orgs): ?>
                        <?php foreach ($this->view->orgs as $org): ?>
                            <tr id="orgListRow<?= $org->getOrganisationId(); ?>" class="org-row">
                                <td>
                                    <span>&nbsp;</span>
                                    <?= $org->getOrganisationName(); ?>
                                    <a href="javascript:void(0);"><span class="glyphicon glyphicon-remove pull-right"
                                                                        aria-hidden="true"
                                                                        onclick="removeOrg(<?= $org->getOrganisationId(); ?>);"></span></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr id="org-list-message" style="<?= $this->view->orgs ? 'display: none;' : ''; ?>">
                        <td><?= _g('Please click Add button to search and add organisations to this list.'); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
