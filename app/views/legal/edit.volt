<script src="/js/validation/errors.js"></script>
<script src="/js/validation/terms.min.js"></script>
<script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
<script>
    $(document).ready(function() {
        tinymce.init({
            menubar: "format insert edit",
            plugins: 'link',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
            selector: '#document-content'
        });
    });

    function updateDocument() {
        clearErrors('#success-box', '#error-box');
        validateTitle($('#document-title'));
        validateVersion($('#document-version'));
        if (errors.length != 0){
            displayErrors('#error-box');
        } else {
            tinyMCE.triggerSave();
            $('#save-btn').prop('disabled',true);
            $('#continue-btn').prop('disabled',true);
            $.when(ajaxUpdate()).then(function(data){
                $('#save-btn').prop('disabled',false);
                $('#continue-btn').prop('disabled',false);
                if(data.status == 'true'){
                    displaySuccess('Your changes have been saved', $('#success-box'));
                }
            });
        }
    }

    function ajaxUpdate() {
        return $.ajax({
            url: "/legal/ajaxUpdate/<?= $this->view->terms->getTermsId(); ?>",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: $('#document-form').serialize()
        });
    }

    function continueToSettings() {
        clearErrors('#success-box', '#error-box');
        validateTitle($('#document-title'));
        validateVersion($('#document-version'));
        if (errors.length != 0){
            displayErrors('#error-box');
        } else {
            tinyMCE.triggerSave();
            $('#save-btn').prop('disabled',true);
            $('#continue-btn').prop('disabled',true);
            $.when(ajaxUpdate()).then(function(data){
                $('#save-btn').prop('disabled',false);
                $('#continue-btn').prop('disabled',false);
                if(data.status == 'true'){
                    location.replace('/legal/settings/<?= $this->view->terms->getTermsId(); ?>');
                }
            });
        }
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <a href="/legal/manage" class="btn btn-default" style="margin-top: 15px;">Back to All Documents</a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Edit Document'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-success" role="alert" id="success-box" name="success-box" style="display: none;"></div>
        <div class="alert alert-danger" role="alert" id="error-box" name="error-box" style="display: none;"></div>
    </div>
</div>
<form class="form-horizontal" autocomplete="off" id="document-form" name="document-form" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-sm-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Document Details'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label for="document-title" class="col-sm-3 control-label"><?= _g('Title'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="document-title" name="document-title" class="form-control" maxlength="100" value="<?= $this->view->terms->getDefaultName(); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="document-version" class="col-sm-3 control-label"><?= _g('Version'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="document-version" name="document-version" class="form-control" maxlength="45" value="<?= $this->view->terms->getVersion(); ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6" id="progress-message"></div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Document Content</h5>
                </div>
                <div class="ibox-content">
                    <textarea class="form-control" id="document-content" name="document-content" style="height:150px;"><?= $this->view->terms->getDefaultContent(); ?></textarea>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-sm-12">
        <button class="btn btn-primary" style="margin-bottom:15px;" id="save-btn" name="save-btn" onclick="updateDocument();">Save</button>
        <button class="btn btn-primary pull-right" style="margin-bottom:15px;" id="continue-btn" name="continue-btn" onclick="continueToSettings();">Continue to Edit Settings</button>
    </div>
</div>