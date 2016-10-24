function getSuggestedUsersForEvent(eventId){
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxItemProfileSuggestedUsers/"+eventId,
            type: 'post',
            dataType: 'json',
            data: {"CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}