function Modal(removeWhenClosed) {

    if (typeof removeWhenClosed === 'undefined') {
        removeWhenClosed = true;
    }

    if (removeWhenClosed) {
        $(document).on('hide.bs.modal', this, function () {
            $(this).remove();
        });
    }

    this.confirm = function (title, body, confirmCallback, selector, modalName, buttonType) {
        $(selector).attr('data-toggle', 'modal');
        $(selector).attr('data-target', '#' + modalName);
        if (typeof buttonType === 'undefined') {
            buttonType = 'btn-primary';
        }
        var buffer =
            '<div class="modal fade" id="' + modalName + '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title" id="myModalLabel">' + title + '</h4>' +
            '</div>' +
            '<div class="modal-body">' +
            body +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '<button type="button" data-dismiss="modal" id="' + modalName + '-confirm-btn" onclick="' + confirmCallback + '" class="btn ' + buttonType + '">Confirm</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        $(document.body).append(buffer);
    };

    this.confirmWithMessage = function (title, body, confirmCallback, selector, modalName, textareaName, buttonType) {
        $(selector).attr('data-toggle', 'modal');
        $(selector).attr('data-target', '#' + modalName);
        if (typeof buttonType === 'undefined') {
            buttonType = 'btn-primary';
        }
        var buffer =
            '<div class="modal fade" id="' + modalName + '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
            '<div class="modal-dialog">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h4 class="modal-title" id="myModalLabel">' + title + '</h4>' +
            '</div>' +
            '<div class="modal-body">' +
            body +
            '<textarea class="form-control" style="height:150px; margin-top: 15px;" id="' + textareaName + '"></textarea>' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
            '<button type="button" id="' + modalName + '-confirm-btn" onclick="' + confirmCallback + '" class="btn ' + buttonType + '">Confirm</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        $(document.body).append(buffer);
    }
}