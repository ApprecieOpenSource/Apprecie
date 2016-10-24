<?php $auth = new \Apprecie\Library\Security\Authentication(); ?>

<script src="/js/compiled/public/js/raw/library/invite.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/library/guestlist.min.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<?php $this->partial("partials/jparts/suggestedusers"); ?>
<script>
    var userCollection = new suggestedUsersForInvitation(true);
    userCollection.setRemainingUnits(<?=$this->view->availableUnits;?>);
    var guestList = new GuestListAll(<?=$this->view->item->getItemId(); ?>);
    var itemId =<?=$this->view->item->getItemId(); ?>;
    var guests = null;
    $(document).ready(function () {
        inviteSearch(1);
    });

    var pageData = null;
    function inviteSearch(pageNumber) {
        $.when(guestList.fetch()).then(function (data) {
            guests = data;

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
            var page = null;
            $.when(suggested.fetch()).then(function (data) {
                pageData = data;
                $("#user-results").html(template.render(data));
                Pagination(data, 'inviteSearch', $('#user-search-pagination'));
                refreshHighlight();
                $.each(guests, function (index, value) {
                    if (value.attending == 1) {
                        $('#userlist-' + value.userId).attr('onclick', false).attr('disabled', true);
                        $('#userlist-' + value.userId).children(":eq(0)").append('<span class="label label-success">Attending&nbsp;(' + value.spaces + '&nbsp;' + (value.spaces == 1 ? 'space' : 'spaces') + ')</span>');
                    } else if (value.status == 'invited') {
                        $('#userlist-' + value.userId).attr('onclick', false).attr('disabled', true);
                        $('#userlist-' + value.userId).children(":eq(0)").append('<span class="label label-warning">Invited&nbsp;(' + value.spaces + '&nbsp;' + (value.spaces == 1 ? 'space' : 'spaces') + ')</span>');
                    } else if (value.status == 'declined') {
                        $('#userlist-' + value.userId).children(":eq(0)").append('<span class="label label-danger">Declined</span>');
                    } else if (value.status == 'revoked') {
                        $('#userlist-' + value.userId).children(":eq(0)").append('<span class="label label-danger">Revoked</span>');
                    }
                })
            });
        })
    }

    function toggleRow(userId, credit) {
        userCollection.toggleUser(userId, credit);
        refreshHighlight();
        var userName = $('#userlist-' + userId).children(":eq(1)").text();
        if (userName.trim() == '') {
            userName = $('#userlist-' + userId).children(":eq(2)").text();
        }
        if (userId in userCollection.getUsers()) {
            var buffer = '<div id="selected-' + userId + '" class="col-sm-4">';
            buffer += '<div style="width: 100%;background-color: #5bc0de;float: left;margin-bottom: 15px;padding: 10px;color: white;border-radius: 4px;line-height: 1;">';
            buffer += '<i class="fa fa-close" onclick="toggleRow(' + userId + ')" style="cursor: pointer;"></i>&nbsp;&nbsp;' + userName;
            buffer += '<span class="pull-right">';
            buffer += '<i class="fa fa-plus user-spaces-plus" id="user-spaces-plus-' + userId + '" style="cursor: pointer;" onclick="plusSpace(' + userId + ')"></i>';
            buffer += '<span id="user-spaces-' + userId + '" style="display: inline-block;padding: 0 15px;font-weight: bold;">1&nbsp;space</span>';
            buffer += '<i class="fa fa-minus user-spaces-minus" id="user-spaces-minus-' + userId + '" style="cursor: pointer;" onclick="minusSpace(' + userId + ')"></i>';
            buffer += '</span>';
            buffer += '</div>';
            buffer += '</div>';
            $('#selected-users').append(buffer);
        } else {
            $('#selected-' + userId).remove();
        }
        var remainingUnits = userCollection.getRemainingUnits();
        $('#remainingCount').html(remainingUnits);
        if (remainingUnits == 0) {
            $('.user-spaces-plus').hide();
        }
    }

    function plusSpace(userId) {

        userCollection.addSpace(userId);

        var remainingUnits = userCollection.getRemainingUnits();
        $('#remainingCount').html(remainingUnits);

        if (remainingUnits == 0) {
            $('.user-spaces-plus').hide();
        }

        var userUnits = userCollection.getUsers()[userId];
        $('#user-spaces-' + userId).text(userUnits + ' ' + (userUnits == 1 ? 'space' : 'spaces'));

        if (userUnits > 3) {
            $('#user-spaces-plus-' + userId).hide();
        }
    }

    function minusSpace(userId) {

        userCollection.removeSpace(userId);

        var remainingUnits = userCollection.getRemainingUnits();
        $('#remainingCount').html(remainingUnits);

        if (remainingUnits > 0) {
            $('.user-spaces-plus').show();
        }

        if (userId in userCollection.getUsers()) {

            var userUnits = userCollection.getUsers()[userId];
            $('#user-spaces-' + userId).text(userUnits + ' ' + (userUnits == 1 ? 'space' : 'spaces'));

            if (userUnits < 4) {
                $('#user-spaces-plus-' + userId).show();
            }
        } else {
            $('#selected-' + userId).remove();
        }
    }

    function refreshHighlight() {
        $('.highlight').removeClass('highlight');
        $.each(userCollection.getUsers(), function (userId, spaces) {
            $('#userlist-' + userId).addClass('highlight');
        });
    }

    function selectAll() {
        var currentUsers = userCollection.getUsers();
        $.each(pageData.items, function (index, value) {
            var userId = parseInt(value.userid);
            if (!(userId in currentUsers)) {
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
            if (userId in currentUsers) {
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

    function sendInvitations() {
        var users = userCollection.getUsers();
        if (Object.keys(users).length != 0) {
            if ($('#invitation-send').is(":checked")) {

                for (var firstUser in users) if (users.hasOwnProperty(firstUser)) break;

                emailWidget.previewData = {
                    "event": <?=$this->view->item->getEvent()->getEventId(); ?>,
                    "emailType": 'invitation',
                    "user": firstUser
                };

                emailWidget.modal.modal('show');
            } else {
                doSend(false);
            }
        }
    }

    function doSend(sendEmail) {
        if (typeof sendEmail === 'undefined') {
            sendEmail = true;
        }

        var users = userCollection.getUsers();

        if (Object.keys(users).length != 0) {
            $.each(users, function (userId, spaces) {
                var inviteList = new InviteGuestListUser();
                inviteList.setSendEmail(sendEmail);
                inviteList.setItemId(itemId);
                inviteList.setUserId(userId);
                inviteList.setSpaces(spaces);

                $('#selected-' + userId).find('span').remove();
                $('#selected-' + userId).css('background-color', '#5EC15E');
                $('#process-selected').prop('disabled', true);

                $.when(inviteList.fetch()).then(function (data) {
                    $('#userlist-' + userId).attr('onclick', false).attr('disabled', true);
                    $('#userlist-' + userId).children(":eq(0)").html('<span class="label label-warning">Invited&nbsp;(' + spaces + '&nbsp;' + (spaces == 1 ? 'space' : 'spaces') + ')</span>');
                    $('#selected-' + userId).fadeOut(function () {
                        $(this).remove();
                        toggleRow(userId, false);
                    });

                    if ($('#invitation-show').is(":checked")) {
                        $('#invitation-links').append('<p>' + data.userName + '<br/><input type="text" class="form-control" value="' + data.url + '"></p>');
                        $('#invite-links-container').fadeIn('fast');
                    }
                    $('#success-invite').stop().fadeOut('fast').html('Person added to guest list').fadeIn('fast');
                    $('#process-selected').prop('disabled', false);
                })
            })
        }
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>Invite People - <?= $item->getTitle(); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h3><?= _g('Selected People'); ?> (<span id="remainingCount"><?=$this->view->availableUnits;?></span> spaces available)</h3>
        <p>
            <a href="/vault/manage/<?=$this->view->item->getItemId(); ?>" class="btn btn-default">Back</a>
            <a class="btn btn-default" onclick="clearInviteSelected()">Clear Selection</a>
            <input style="margin-right: 15px;" type="button" class="btn btn-primary" id="process-selected" onclick="sendInvitations()" value="Invite Selected">
            <input type="radio" id="invitation-show" name="invitation-option" checked value="show"/> <label style="font-weight: normal;margin-right: 15px;" for="invitation-show" >Show invitation links</label>
            <input type="radio" id="invitation-send" name="invitation-option" value="send"/> <label style="font-weight: normal; margin-right: 15px;" for="invitation-send">Send invitation emails</label>
        </p>
    </div>
</div>
<div class="row" id="selected-users">

</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Invite People From Your Network'); ?></h5>
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
                <button class="btn btn-default" id="create-user-btn" data-target="#create-user-modal" data-toggle="modal">
                    <?= ($auth->getAuthenticatedUser()->hasRole(\Apprecie\Library\Users\UserRole::CLIENT)) ? _g('Create New Contact') : _g('Create New Client'); ?>
                </button>
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
        <div class="ibox float-e-margins" id="invite-links-container" style="display:none;">
            <div class="ibox-title">
                <h5>Invitation Links</h5>
            </div>
            <div class="ibox-content">
                <div id="invitation-links">

                </div>
            </div>
        </div>
    </div>
</div>
<?= (new CreateUserWidget('withOptionalEmail', array('refreshOnSuccess' => true)))->getContent(); ?>
<?= (new EmailWidget('index', array('templateType' => \Apprecie\Library\Mail\EmailTemplateType::INVITATION, 'callback' => 'doSend', 'previewData' => null)))->getContent(); ?>