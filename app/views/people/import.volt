<script>
    $(document).ready(function() {
        $('#grant-portal-access, #send-email').on('change', validateCheckboxes);
        $('#file').on('change', function () {
            if ($(this).val()) {
                $('#upload-btn').prop('disabled', false);
            }
        });
    });

    function validateCheckboxes() {
        var grantPortalAccess = $('#grant-portal-access');
        var sendEmail = $('#send-email');

        if (grantPortalAccess.is(':checked')) {
            sendEmail.prop('disabled', false);
        } else {
            sendEmail.prop('disabled', true);
            sendEmail.prop('checked', false);
        }
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Bulk Import'); ?></h2>
    </div>
</div>
<div class="dropdown" style="margin-bottom: 15px;">
    <button class="btn btn-default dropdown-toggle" type="button" id="exportMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        Download Template
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="exportMenu">
        <li>
            <a href="/people/getUserTemplate/csv">CSV</a>
        </li>
        <li>
            <a href="/people/getUserTemplate/excel">Excel (.xlsx)</a>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Upload Completed Template'); ?></h5>
            </div>
            <div class="ibox-content">
                <?php if($this->view->message===true): ?>
                    <div class="alert alert-success" id="no-results" role="alert"><strong><?= _g('Import Successful!'); ?></strong> <?= _g('Your users have been imported successfully.'); ?></div>
                <?php elseif($this->view->message!=null): ?>
                    <div class="alert alert-danger" id="no-results" role="alert"><strong><?= _g('Import Failed!'); ?></strong> <?= $this->view->message; ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data" action="/people/import">
                    <p><?= _g('Please use the template provided above. Note that we only accept CSV (.csv) and Excel (.xls, .xlsx) formats.'); ?></p>
                    <p>
                        <label for="file">File location</label>
                        <input type="file" id="file" name="file" accept=".csv,.xls,.xlsx"/>
                    </p>
                    <div class="alert alert-info" style="margin: 20px 0 0 0;">
                        <div class="checkbox">
                            <label for="grant-portal-access">
                                <input type="checkbox" id="grant-portal-access" name="grant-portal-access" value="1">
                                <strong>Grant Portal Access</strong>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label for="send-email">
                                <input type="checkbox" id="send-email" name="send-email" value="1" disabled/>
                                <strong>Send Sign-up Emails</strong>
                            </label>
                        </div>
                        <p>
                            <?= _g("If you tick Grant Portal Access option, a Sign-up URL will be generated for each imported client and a client quota will be consumed. The Sign-up URL can be viewed on the client's profile after the import."); ?>
                        </p>
                        <p>
                            <?= _g('If you tick Send Sign-up Emails option, a system email containing the Sign-up URL will be sent to each user.'); ?>
                        </p>
                    </div>
                    <input type="submit" value="Upload" style="margin-top: 15px;" class="btn btn-primary" id="upload-btn" disabled>
                </form>
            </div>
        </div>
    </div>
</div>
