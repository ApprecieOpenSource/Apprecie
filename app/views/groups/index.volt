<script src="/js/compiled/public/js/raw/library/groups.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<?php $this->partial("partials/jparts/userGroups"); ?>
<script>
    $(document).ready(function () {

        $('#new-group').click(function () {
            var btn = $(this);
            var success = $('#group-create-success');
            var failed = $('#group-create-failed');

            btn.prop('disabled', true);

            var group = new CreateGroup();
            group.setGroupName($('#groupname').val());
            $.when(group.fetch()).then(function (data) {
                if (data.status === 'success') {
                    failed.stop().hide();
                    success.stop().fadeOut('fast').html(data.message).fadeIn('fast');
                    getGroups(1);
                } else {
                    success.stop().hide();
                    failed.stop().fadeOut('fast').html(data.message).fadeIn('fast');
                }
                btn.prop('disabled', false);
            });
        });

        getGroups(1);
    });

    function editGroup(groupId) {

        var groupName = $('#editGroupName');
        var deleteBtn = $('#deleteGroupBtn');
        var saveBtn = $('#editSave');
        var modal = $('#editGroupModal');
        var modalError = $('#modalError');
        var editGroup = new EditGroup(groupId);

        modalError.empty().hide();
        groupName.val('');
        modal.modal('show');

        deleteBtn.click(function () {
            saveBtn.prop('disabled', true);
            deleteBtn.prop('disabled', true);
            var deletion = new DeleteGroup(groupId);
            $.when(deletion.fetch()).then(function (data) {
                if (data.status == "success") {
                    modal.modal('hide');
                    getGroups(1);
                }
                else {
                    modalError.html(data.message).show();
                }
                saveBtn.prop('disabled', false);
                deleteBtn.prop('disabled', false);
            });
        });

        saveBtn.click(function () {
            saveBtn.prop('disabled', true);
            deleteBtn.prop('disabled', true);
            editGroup.setGroupName(groupName.val());
            $.when(editGroup.fetch()).then(function (data) {
                if (data.status == "success") {
                    modal.modal('hide');
                    getGroups(1);
                }
                else {
                    modalError.html(data.message).show();
                }
                saveBtn.prop('disabled', false);
                deleteBtn.prop('disabled', false);
            });
        });

    }

    function getGroups(pageNumber) {

        var groups = new GetGroups();
        groups.setPageNumber(pageNumber);

        $.when(groups.fetch()).then(function (data) {
            var template = $.templates("#userGroups");
            $("#group-results").html(template.render(data));
            Pagination(data, 'getGroups', $('#groups-pagination'));
        });
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>
            <?= _g('Group Management'); ?>
            <div class="pull-right">
                <a class="btn btn-primary" href="/people/">Back</a>
            </div>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-8">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Groups in your network'); ?></h5>
            </div>
            <div class="ibox-content">

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th><?= _g('Group Name'); ?></th>
                        <th><?= _g('People'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="group-results">

                    </tbody>
                </table>
                <nav>
                    <ul class="pagination pagination-sm" id="groups-pagination">

                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('New Group'); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="alert alert-success" role="alert" style="display: none;" id="group-create-success"></div>
                <div class="alert alert-danger" role="alert" style="display: none;" id="group-create-failed"></div>

                <div class="input-group">
                    <input type="text" name="groupname" class="form-control" id="groupname">
                    <span class="input-group-btn">
                        <button class="btn btn-default" id="new-group" type="button">Create</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-labelledby="editGroupModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Group</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert" style="display: none" id="modalError"></div>
                <p>To change the name of this group please enter a new name in the box below</p>

                <div class="form-group">
                    <input type="text" id="editGroupName" name="editGroupName" class="form-control" value=""/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editSave">Save changes</button>
                <button type="button" class="btn btn-danger pull-left" id="deleteGroupBtn">Delete Group</button>
            </div>
        </div>
    </div>
</div>
