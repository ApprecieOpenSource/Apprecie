<link href="/css/bootstrap-switch.min.css" rel="stylesheet">
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/terms.min.js"></script>
<script src="/js/bootstrap-switch.min.js"></script>
<script>
    $(document).ready(function() {
        $('#settings-form').on('change', 'input[type="checkbox"]', checkboxToggle);
        $('#state').bootstrapSwitch();
        checkboxToggle();
    });

    function saveSettings() {
        clearErrors('#success-box', '#error-box');
        if (errors.length != 0){
            displayErrors('#error-box');
        } else {
            $('#save-btn').prop('disabled',true);
            $.when(ajaxSaveSettings()).then(function(data){
                $('#save-btn').prop('disabled',false);
                if(data.status == 'true'){
                    displaySuccess('Your changes have been saved', $('#success-box'));
                }
            });
        }
    }

    function ajaxSaveSettings() {
        return $.ajax({
            url: "/legal/ajaxSaveSettings/<?= $this->view->terms->getTermsId(); ?>",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: $('#settings-form').serialize()
        });
    }

    function checkboxToggle() {
        var roleBoxes = $('input[type="checkbox"].role');

        if ($('#checkbox-rsvp')[0].checked || $('#checkbox-public')[0].checked) {
            roleBoxes.prop('checked', false);
            roleBoxes.prop('disabled', true);
        } else {
            roleBoxes.prop('disabled', false);
        }
    }
</script>
<style>
    .form-group .bootstrap-switch .bootstrap-switch-handle-on,
    .form-group .bootstrap-switch .bootstrap-switch-handle-off,
    .form-group .bootstrap-switch .bootstrap-switch-label {
        height: auto;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <a href="/legal/manage" class="btn btn-default" style="margin-top: 15px;">Back to All Documents</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Edit Settings'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-success" role="alert" id="success-box" name="success-box" style="display: none;"></div>
        <div class="alert alert-danger" role="alert" id="error-box" name="error-box" style="display: none;"></div>
    </div>
</div>
<form class="form-horizontal" autocomplete="off" id="settings-form" name="settings-form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Role Settings'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <?php foreach ($this->view->roles as $role): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="roles[]" id="checkbox-<?= $role->getRoleId(); ?>" class="role" value="<?= $role->getRoleId(); ?>"
                                        <?= (in_array($role->getRoleId(), $this->view->checkedRoles)) ? 'checked' : ''; ?>>
                                    <?= $role->getDescription(); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <label class="control-label"><?= _g('Special'); ?></label>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="roles[]" value="rsvp" id="checkbox-rsvp"<?= ($this->view->isRsvp) ? ' checked' : ''; ?>>
                                <?= _g('RSVP'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="roles[]" value="public" id="checkbox-public"<?= ($this->view->isPublic) ? ' checked' : '';?>>
                                <?= _g('Public'); ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Portal Settings'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="portal-id" class="control-label"><?= _g('Portal'); ?></label>
                        <select class="form-control" id="portal-id" name="portal-id">
                            <option value="all"><?= _g('All'); ?></option>
                            <?php foreach($this->view->portals as $portal): ?>
                                <option<?= (count($this->view->settings) && $portal->getPortalId() === $this->view->settings[0]->getPortalId()) ? ' selected' : ''; ?> value="<?= $portal->getPortalId();?>">
                                    <?= $portal->getPortalName(); ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('State Settings'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <input id="state" name="state" type="checkbox"<?= ($this->view->terms->getState()) ? ' checked' : ''; ?>>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-primary pull-right" style="margin-bottom:15px;" id="save-btn" name="save-btn" onclick="saveSettings();">Save</button>
    </div>
</div>