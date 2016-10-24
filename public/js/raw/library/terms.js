/**
 * Created by hu86 on 29/09/2015.
 */
function Terms(){
    this.pageNumber = 1;
    this.postData = null;

    this.setPostData = function(postData) {
        this.postData = postData;
    };

    this.getPostData = function() {
        return this.postData;
    };

    this.getPageNumber = function() {
        return this.pageNumber;
    };

    this.setPageNumber = function(pageNumber) {
        this.pageNumber=pageNumber;
    };

    this.ajax = function() {
        return $.ajax({
            type: 'POST',
            url: '/legal/ajaxSearchDocuments/' + this.getPageNumber(),
            dataType: 'json',
            data: this.getPostData()
        });
    };

    this.fetch = function() {
        return this.ajax();
    }
}