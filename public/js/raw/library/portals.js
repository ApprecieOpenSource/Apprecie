/**
 * Created by Daniel Dimmick on 24/03/15.
 */
function AjaxPortalUsers(){
    this.pageNumber=1;
    this.postData=null;

    this.setPostData=function(postData){
        this.postData=postData;
    }
    this.getPostData=function(){
        return this.postData;
    }
    this.getPageNumber=function(){
        return this.pageNumber;
    }
    this.setPageNumber=function(pageNumber){
        if(pageNumber != undefined) {
            this.pageNumber=pageNumber;
        }
    }

    this.ajax= function (){
        return $.ajax({
            type: 'POST',
            url: '/adminusers/AjaxSearchPortalUsers/'+this.getPageNumber(),
            dataType: 'json',
            data: this.getPostData()
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}