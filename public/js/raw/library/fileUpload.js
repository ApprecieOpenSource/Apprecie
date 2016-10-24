/**
 * Created by hu86 on 18/12/2015.
 */

function FileUpload() {
    this.fileInput = null;
    this.file = null;
    this.errors = [];

    this.setFileInput = function (fileInput) {
        this.fileInput = fileInput;
        this.file = this.fileInput[0].files[0];
    };

    this.validateFile = function () {
        if (this.file.size > 3000000) {
            this.errors.push('"' + this.file.name + '" is too big. Please use a file that is smaller than 3 MB.')
        }
    };

    this.validateImageType = function () {
        if (this.file.type !== 'image/jpeg') {
            this.errors.push('"' + this.file.name + '" is not in the right format. Please use a JPEG image.')
        }
    };

    this.getErrorHTML = function () {
        var buffer = '';
        $.each(this.errors, function (index, value) {
            if (index === 0) {
                buffer += value;
            } else {
                buffer += '<br>' + value;
            }
        });
        return buffer;
    };
}