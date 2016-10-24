function AjaxGetUser(userId){
    this.ajax = function() {
        return $.ajax({
            type: 'POST',
            url: '/api/AjaxGetUser/',
            dataType: 'json',
            data: {
                "userId": userId,
                'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN
            }
        });
    };

    this.fetch = function() {
        return this.ajax();
    };
}

function AjaxGetUserChildren(userId){
    this.ajax = function() {
        return $.ajax({
            type: 'POST',
            url: '/api/AjaxGetUserChildren/',
            dataType: 'json',
            data: {
                "userId": userId,
                'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN
            }
        });
    };

    this.fetch = function() {
        return this.ajax();
    };
}