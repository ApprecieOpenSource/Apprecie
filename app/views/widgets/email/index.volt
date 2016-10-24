<script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
<div class="modal fade multi-step" id="sendEmailModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title step-1" data-step="1">Edit</h4>
                <h4 class="modal-title step-2" data-step="2">Preview</h4>
                <h4 class="modal-title step-3" data-step="3">Options</h4>
            </div>
            <div class="modal-body step step-1">
                <p>
                    You can use <a data-toggle="collapse" href="#available-macros-wrapper" aria-expanded="false" aria-controls="available-macros">macros</a> while editing the email template.
                </p>
                <div class="collapse" id="available-macros-wrapper">
                    <div class="well"></div>
                </div>
                <form id="user-email-content-form" name="user-email-content-form">
                    {{csrf()}}
                    <div id="content-field-wrapper"></div>
                </form>
            </div>
            <div class="modal-body step step-2">
                <div id="loading-indicator" style="display: none; text-align: center;">
                    <img src="/img/ajax-loader-grey.gif"/>
                </div>
                <div id="preview-wrapper"></div>
            </div>
            <div class="modal-body step step-3">
                <form class="form-horizontal" id="user-email-options-form" name="user-email-options-form">
                    {{csrf()}}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="cc" name="options[cc]"><strong>Include me on this email</strong>
                        </label>
                    </div>
                    <p>By choosing this option, we will cc you in the email, meaning that you will receive a copy for your own records, and that the recipient will be able to contact you back should they have any further queries.</p>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="sendToSelf" name="options[sendToSelf]"><strong>Send this email to myself only</strong>
                        </label>
                    </div>
                    <p>By choosing this option, this email will be sent to you instead, meaning that you can forward the message yourself.</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary step step-1" data-step="1" onclick="emailWidget.goToEdit(true)">Reset To Default</button>
                <button type="button" class="btn btn-primary step step-1" data-step="1" onclick="emailWidget.setContent()">Preview</button>
                <button type="button" class="btn btn-primary step step-2" data-step="2" onclick="emailWidget.goToEdit()">Edit</button>
                <button type="button" class="btn btn-primary step step-2" data-step="2" onclick="emailWidget.goToOptions()">Continue</button>
                <button type="button" class="btn btn-primary step step-3" data-step="3" onclick="emailWidget.goToPreview()">Back</button>
                <button type="button" class="btn btn-primary step step-3" data-step="3" onclick="emailWidget.callbackEmailMessage()">Send</button>
            </div>
        </div>
    </div>
</div>
<script src="/js/multi-step-modal.js"></script>
<script>
    EmailWidget = function () {

        this.modal = $('#sendEmailModal');
        this.contentForm = $('#user-email-content-form');
        this.optionsForm = $('#user-email-options-form');
        this.previewLoadingIndicator = $('#loading-indicator');
        this.previewWrapper = $('#preview-wrapper');

        this.templateType = '<?= $this->view->templateType;?>';
        this.callback = <?= $this->view->callback; ?>;
        this.previewData = <?= json_encode($this->view->previewData); ?>;

        var me = this;

        this.modal.on('show.bs.modal', function () {
            me.goToPreview();
        });

        this.goToPreview = function () {

            this.previewWrapper.html('');
            this.previewLoadingIndicator.show();
            this.modal.trigger('next.m.2');

            $.ajax({
                url: "/api/emailpreview",
                type: 'post',
                dataType: 'json',
                data: me.previewData,
                cache: false
            }).done(function (data) {
                if (data.status === 'success') {
                    me.previewLoadingIndicator.hide();
                    me.previewWrapper.html(data.message);
                }
            });
        };

        this.goToEdit = function (reset) {

            if (typeof reset === 'undefined') {
                reset = false;
            }

            $.ajax({
                url: "/api/AjaxGetUserEmailContent/" + this.templateType,
                type: 'post',
                dataType: 'json',
                data: {
                    "CSRF_SESSION_TOKEN": CSRF_SESSION_TOKEN,
                    "reset": reset
                },
                cache: false
            }).done(function (data) {
                if (data.status === 'success') {

                    $('#user-email-content-form textarea').each(function () {
                        tinymce.EditorManager.execCommand('mceRemoveEditor',true, $(this).attr('id'));
                    });

                    var macroBuffer = '<ul style="list-style-type: none;padding: 0;">';
                    $.each(data.macros, function (index, value) {
                        if (value) {
                            macroBuffer += '<li style="margin-top: 10px;">' + index + '<br><i>' + value + '</i></li>';
                        } else {
                            macroBuffer += '<li>' + index + '</li>';
                        }
                    });
                    macroBuffer += '</ul>';
                    $('#available-macros-wrapper .well').html(macroBuffer);

                    var buffer = '';
                    $.each(data.content, function (index, value) {
                        buffer += '<div class="form-group">';
                        buffer += '<label for="' + index + '" class="control-label">' + value.description + '</label>';
                        buffer += '<textarea class="form-control" id="' + index + '" name="content[' + index + ']" style="height:150px;">' + value.content + '</textarea>';
                        buffer += '</div>';
                    });
                    $('#content-field-wrapper').html(buffer);

                    tinymce.init({
                        menubar: "format insert edit",
                        plugins: 'link',
                        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
                        selector: '#user-email-content-form textarea'
                    });

                    me.modal.trigger('next.m.1');
                }
            });
        };

        this.setContent = function () {

            tinyMCE.triggerSave();

            $.ajax({
                url: "/api/AjaxSetUserEmailContent/" + this.templateType,
                type: 'post',
                dataType: 'json',
                data: this.contentForm.serialize(),
                cache: false
            }).done(function () {
                me.goToPreview();
            });
        };

        this.goToOptions = function () {
            this.modal.trigger('next.m.3');
        };

        this.callbackEmailMessage = function () {
            $.ajax({
                url: "/api/AjaxSetUserEmailOptions/" + this.templateType,
                type: 'post',
                dataType: 'json',
                data: this.optionsForm.serialize(),
                cache: false
            }).done(function () {
                me.modal.modal('toggle');
                me.callback();
            });
        };
    };

    var emailWidget = new EmailWidget();

    $('#sendToSelf').on('change', function () {
        if (this.checked) {
            $('#cc').prop('checked', false).prop('disabled', true);
        } else {
            $('#cc').prop('disabled', false);
        }
    });
</script>