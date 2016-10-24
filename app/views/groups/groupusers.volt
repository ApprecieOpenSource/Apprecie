<script src="/js/compiled/public/js/raw/library/groups.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<?php $this->partial("partials/jparts/userGroupUsers"); ?>
<script>
    var groupId =<?= $this->view->groupId; ?>;

    $(document).ready(function () {
        getUsers(1);
    });

    function getUsers(pageNumber) {

        var users = new GetUsersInGroup();
        users.setGroupId(groupId);
        users.setPageNumber(pageNumber);

        $.when(users.fetch()).then(function (data) {
            var template = $.templates("#userGroupUsers");
            $("#user-results").html(template.render(data));
            Pagination(data, 'getUsers', $('#user-pagination'));
        });
    }

    function removeUser(userId) {

        var removal = new RemoveUserFromGroup();
        removal.setGroupId(groupId);
        removal.setUserId(userId);

        $.when(removal.fetch()).then(function () {
            getUsers(1);
        });
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>
            <?= _g('Group Users'); ?>
            <div class="pull-right">
                <a class="btn btn-primary" href="/groups/index">Back</a>
                <a class="btn btn-primary" href="/invite/groupusers/<?= $this->view->groupId; ?>">Add people to
                    group</a>
            </div>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Members of this group'); ?></h5>
            </div>
            <div class="ibox-content">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th></th>
                        <th><?= _g('Name'); ?></th>
                        <th><?= _g('Reference'); ?></th>
                        <th><?= _g('Organisation'); ?></th>
                        <th><?= _g('Email Address'); ?></th>
                        <th><?= _g('Role'); ?></th>
                        <th><?= _g('Account'); ?></th>
                        <th><?= _g('Login'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="user-results">

                    </tbody>
                </table>
                <nav>
                    <ul class="pagination pagination-sm" id="user-pagination">

                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>