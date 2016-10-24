<?php $auth=new \Apprecie\Library\Security\Authentication(); ?>

<script src="/js/compiled/public/js/raw/library/invite.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/library/guestlist.min.js"></script>
<script src="/js/compiled/public/js/raw/library/items.min.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>

<?php $this->partial("partials/jparts/suggestedusers"); ?>
<script>
    var userCollection = new suggestedUsers(false);
    var guestList = new GuestListAll(<?=$this->view->item->getItemId(); ?>);
    var userCanOperateGuestList = <?= json_encode(\Apprecie\Library\Acl\AccessControl::userCanOperateGuestList($this->view->user, $this->view->item, null)); ?>;
    var itemId = <?=$this->view->item->getItemId(); ?>;
    var suggest = new suggestItem(itemId);
    var guests = null;

    $(document).ready(function () {
        inviteSearch(1);
    });

    function previewEmail() {

        var externalEmail = $('#external-emails');

        if (externalEmail.val() != '') {

            emailWidget.templateType = '<?= \Apprecie\Library\Mail\EmailTemplateType::SUGGESTION_OFF_PORTAL; ?>';
            emailWidget.previewData = {
                "event": <?=$this->view->item->getEvent()->getEventId(); ?>,
                "emailType": 'externalSuggestion',
                "email": externalEmail.val()
            };

            emailWidget.modal.modal('show');
        } else if (userCollection.getUsers().length > 0) {

            emailWidget.templateType = '<?= \Apprecie\Library\Mail\EmailTemplateType::SUGGESTION_ON_PORTAL; ?>';
            emailWidget.previewData = {
                "event": <?=$this->view->item->getEvent()->getEventId(); ?>,
                "emailType": 'suggestion',
                "user": userCollection.getUsers()[0]
            };

            emailWidget.modal.modal('show');
        }
    }

    function SuggestSelected() {
        $('#internal-email-failed').stop().fadeOut('fast');
        $('#external-email-success').stop().fadeOut('fast');
        $('#external-email-failed').stop().fadeOut('fast');
        var externalEmail = $('#external-emails');
        externalEmail.css('background-color', 'transparent').css('color', 'inherit');

        if (userCollection.getUsers().length > 0) {
            suggest.setUsers(userCollection.getUsers());

            $('.user-badge').css('background-color', '#5EC15E');
            $('#process-selected').prop('disabled', true);
            $.when(suggest.fetch()).then(function (data) {

                if (data.status == "success") {
                    $.each(userCollection.getUsers(), function (key, userId) {
                        $('#userlist-' + userId).attr('onclick', false).attr('disabled', true);
                        $('#userlist-' + userId).children(":eq(0)").html('<span class="label label-success">Suggestion Sent</span>');
                        $('#selected-' + userId).fadeOut(function () {
                            $(this).remove();
                            toggleRow(userId, false);
                        });
                    });
                } else {
                    $('.user-badge').css('background-color', 'red');
                    $('#internal-email-success').stop().fadeIn('fast');
                }

                $('#process-selected').prop('disabled', false);
            })
        }


        if (externalEmail.val() != '') {
            $('#process-selected').prop('disabled', true);

            var suggestExternal = new SuggestItemExternal(itemId, externalEmail.val());
            externalEmail.css('background-color', '#5EC15E').css('color', 'white');

            $.when(suggestExternal.fetch()).then(function (data) {
                if (data.status == "success") {
                    externalEmail.val('');
                    externalEmail.css('background-color', 'transparent').css('color', 'inherit');
                    $('#external-email-success').stop().fadeIn('fast');
                } else {
                    externalEmail.css('background-color', 'red');
                    $('#external-email-failed').stop().fadeIn('fast');
                }
                $('#process-selected').prop('disabled', false);
            });
        }
    }

    var pageData = null;
    function inviteSearch(pageNumber) {
        var suggested = new inviteUsersList(<?=$this->view->item->getItemId();?>);
        var accountActive = $('#account-active').is(':checked');
        var accountDeactivated = $('#account-deactivated').is(':checked');
        var accountPending = $('#account-pending').is(':checked');

        var loginSuspended = $('#login-suspended').is(':checked');
        var loginEnabled = $('#login-enabled').is(':checked');
        var suggestions = $('#show-suggestions').is(':checked');

        suggested.setAccountActive(accountActive);
        suggested.setAccountDeactivated(accountDeactivated);
        suggested.setAccountPending(accountPending);
        suggested.setPageNumber(pageNumber);
        suggested.setSuggestions(suggestions);
        suggested.setPageNumber(pageNumber);
        if (loginSuspended === true && loginEnabled === true) {
            suggested.setLogin('All');
        } else if (loginSuspended === true && loginEnabled == false) {
            suggested.setLogin('suspended');
        } else if (loginSuspended == false && loginEnabled == true) {
            suggested.setLogin('enabled');
        }

        suggested.setEmail($('#email').val());
        suggested.setName($('#name').val());
        suggested.setReference($('#reference').val());
        suggested.setRole($('#roleName').val());
        suggested.setGroup($('#groupId').val());
        var template = $.templates("#suggestedusers");
        $.when(suggested.fetch()).then(function (data) {
            pageData = data;
            $("#user-results").html(template.render(data));
            Pagination(data, 'inviteSearch', $('#user-search-pagination'));
            refreshHighlight();

            if (userCanOperateGuestList) {
                $.when(guestList.fetch()).then(function (data) {
                    guests = data;
                    $.each(guests, function (index, value) {
                        if (value.attending == 1) {
                            $('#userlist-' + value.userId).attr('onclick', false).attr('disabled', true);
                            $('#userlist-' + value.userId).children(":eq(0)").append('<span class="label label-success">Attending</span>');
                        } else if (value.status == 'invited') {
                            $('#userlist-' + value.userId).attr('onclick', false).attr('disabled', true);
                            $('#userlist-' + value.userId).children(":eq(0)").append('<span class="label label-warning">Invited</span>');
                        } else if (value.status == 'declined') {
                            $('#userlist-' + value.userId).children(":eq(0)").append('<span class="label label-danger">Declined</span>');
                        } else if (value.status == 'revoked') {
                            $('#userlist-' + value.userId).children(":eq(0)").append('<span class="label label-danger">Revoked</span>');
                        }
                    })
                })
            }
        });
    }

    function toggleRow(userId, credit) {
        userCollection.toggleUser(userId, credit);
        refreshHighlight();
        var userName = $('#userlist-' + userId).children(":eq(1)").text();
        if (userName == '') {
            userName = $('#userlist-' + userId).children(":eq(2)").text();
        }
        if ($.inArray(userId, userCollection.getUsers()) != -1) {
            var buffer = '<div class="user-badge" id="selected-' + userId + '" style=" margin-right:10px;background-color:#5bc0de; float:left; margin-bottom:15px;padding: 5px;color: white;border-radius: 4px;"><span class="badge user-badge-cross" style="background-color: white; color:black;cursor:pointer;" onclick="toggleRow(' + userId + ')">X</span> ' + userName + '</div>';
            $('#selected-users').append(buffer);
        } else {
            $('#selected-' + userId).remove();
        }
    }

    function refreshHighlight() {
        $('.highlight').removeClass('highlight');
        $.each(userCollection.getUsers(), function (index, value) {
            $('#userlist-' + value).addClass('highlight');
        });
    }

    function selectAll() {
        var currentUsers = userCollection.getUsers();
        $.each(pageData.items, function (index, value) {
            var userId = parseInt(value.userid);
            if ($.inArray(userId, currentUsers) == -1) {
                var attr = $('#userlist-' + userId).attr('disabled');
                if (typeof attr !== typeof undefined && attr !== false) {

                } else {
                    toggleRow(userId);
                }
            }
        });
    }
    function deselectAll() {
        var currentUsers = userCollection.getUsers();
        $.each(pageData.items, function (index, value) {
            var userId = parseInt(value.userid);
            if ($.inArray(userId, currentUsers) != -1) {
                toggleRow(userId);
            }
        });
    }

    function clearInviteSelected() {
        userCollection.clear();
        $('#selected-users').empty();
        refreshHighlight();
        $('#remainingCount').html(userCollection.getRemainingUnits());
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>Suggest People - <?= $item->getTitle(); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h3><?= _g('Selected People'); ?></h3>
        <div class="alert alert-danger" id="internal-email-failed" role="alert" style="display: none"><?=_g('Failed to send suggestions'); ?></div>
        <p>
            <?php if($this->view->item->getIsByArrangement()==1):?>
            <a href="/vault/arranged/<?=$this->view->item->getItemId(); ?>" class="btn btn-default">Back</a>
            <?php else: ?>
            <a href="/vault/event/<?=$this->view->item->getItemId(); ?>" class="btn btn-default">Back</a>
            <?php endif; ?>
             <a class="btn btn-default" onclick="clearInviteSelected()">Clear Selection</a> <input style="margin-right: 15px;" type="button" class="btn btn-primary" id="process-selected" onclick="previewEmail()" value="Suggest To Selected">
        </p>
        <div id="selected-users">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Suggest To External People'); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="alert alert-success" id="external-email-success" role="alert" style="display: none"><?=_g('Suggestions Sent'); ?></div>
                <div class="alert alert-danger" id="external-email-failed" role="alert" style="display: none"><?=_g('Failed to send suggestions'); ?></div>

                <input type="text" class="form-control" id="external-emails" />
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Suggest People From Your Network'); ?></h5>
                <span class="pull-right"><a style="text-decoration: none; cursor: pointer;" onclick="toggleFilter('#filter-container');"><i class="fa fa-filter"></i> Filter people</a></span>
            </div>
            <div class="ibox-content">
                <div id="filter-container" style="display: none;">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="roleName" class="control-label"><?= _g('Role'); ?></label>
                                        <select id="roleName" name="roleName" class="form-control search-change">
                                            <option value="All"><?= _g('All'); ?></option>
                                            <?php $roleHierarchy = new \Apprecie\Library\Users\RoleHierarchy($auth->getSessionActiveRole()); ?>
                                            <?php foreach($roleHierarchy->getVisibleRoles() as $roleName => $roleText):?>
                                                <option <?= ($roleName === $this->view->selectedRole) ? 'selected' : ''; ?> value="<?= $roleName;?>"><?= $roleText; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input name="email" id="email" type="text" class="form-control search-text-change" value=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="name">Name</label>
                                    <input name="name" id="name" type="text" class="form-control search-text-change" value=""/>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <input name="reference" id="reference" type="text" class="form-control search-text-change" value=""/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="group">Group</label>
                                        <select class="form-control search-change" id="groupId" name="groupId">
                                            <option value="all">All</option>
                                            <?php foreach($this->view->groups as $group): ?>
                                                <option value="<?= $group->getGroupId(); ?>"><?= $group->getGroupName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="checkbox" style="margin-top: 0px;">
                                <label style="font-weight: normal; margin-right: 10px;" for="account-active"><input class="search-click" type="checkbox" id="account-active" name="account-active" <?php if($this->request->get('a')!='false'){echo 'checked';} ?>>Show active & registered people</label><br/>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="account-pending"><input class="search-click" type="checkbox" id="account-pending" name="account-pending" <?php if($this->request->get('u')!='false'){echo 'checked';} ?>> Show unregistered people</label>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="account-deactivated"><input class="search-click" type="checkbox" id="account-deactivated" name="account-deactivated"> Show deactivated people</label>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="login-enabled"><input class="search-click" type="checkbox" id="login-enabled" name="login-enabled"> Show people with login enabled</label>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="login-suspended"><input class="search-click" type="checkbox" id="login-suspended" name="login-suspended"> Show people with login suspended</label>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="show-suggestions"><input class="search-click" type="checkbox" id="show-suggestions" name="show-suggestions"> Show Suggestions only</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="button" class="btn btn-default" value="Search" onclick="inviteSearch(1);" style="margin-bottom: 15px;">
                        </div>
                    </div>
                </div>
                <a onclick="selectAll();" class="btn btn-default">Select All</a>
                <a onclick="deselectAll();" class="btn btn-default">Deselect All</a>
                <button class="btn btn-default" id="create-user-btn" data-target="#create-user-modal" data-toggle="modal">Create New Client</button>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="hidden-xs"><?= _g('Reason'); ?></th>
                        <th class="hidden-xs"><?= _g('Name'); ?></th>
                        <th><?= _g('Reference'); ?></th>
                        <th><?= _g('Group'); ?></th>
                        <th><?= _g('Organisation'); ?></th>
                        <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                        <th class="hidden-xs"><?= _g('Role'); ?></th>
                        <th><?= _g('Account'); ?></th>
                        <th><?= _g('Login'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="user-results">
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination pagination-sm" id="user-search-pagination">

                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<?= (new CreateUserWidget('withOptionalEmail', array('refreshOnSuccess' => true)))->getContent(); ?>
<?= (new EmailWidget('index', array('templateType' => null, 'callback' => 'SuggestSelected', 'previewData' => null)))->getContent(); ?>