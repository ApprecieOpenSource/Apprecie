var suggestItem = function(itemId) {

    var users = [];

    this.addUser = function(userId) {
        users.push(userId);
    };

    var getUsers = function() {
        return users;
    };

    this.setUsers = function(usersArray) {
        users = usersArray;
    };

    this.ajax = function() {
        return $.ajax({
            type: 'POST',
            url: '/api/SendSuggestion/'+itemId,
            dataType: 'json',
            data: {
                "users": getUsers(),
                'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN
            }
        });
    };

    this.fetch = function() {
        return this.ajax();
    };
};

var SuggestItemExternal = function(itemId, email) {

    this.ajax = function() {
        return $.ajax({
            type: 'POST',
            url: '/api/SendExternalSuggestion/'+itemId,
            dataType: 'json',
            data: {
                "email": email,
                'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN
            }
        });
    };

    this.validate = function() {
        if (email == '' || email == null || email.length < 6) {
            return false
        }

        return true;
    };

    this.fetch = function(){
        return this.ajax();
    };
};

var approveItem=function(itemId,administrationFee,reservationFee,reservationPeriod,reservationAllowed){
    this.ajax = function (){
        return $.ajax({
            type: 'POST',
            url: '/api/approveItem/'+itemId,
            dataType: 'json',
            data:{
                "administrationFee":administrationFee,
                "reservationFee":reservationFee,
                "reservationPeriod":reservationPeriod,
                "reservationAllowed":reservationAllowed,
                'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN
            }
        });
    }

    this.fetch = function(){
        return this.ajax();
    }
}

var rejectItem=function(itemId,reason){
    this.ajax = function (){
        return $.ajax({
            type: 'POST',
            url: '/api/rejectItem/'+itemId,
            dataType: 'json',
            data:{"reason":reason,'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN}
        });
    }

    this.fetch = function(){
        return this.ajax();
    }
}

var publishEvent=function(){
    var state=null;
    var eventId=null;
    this.setEventId=function(thisEventId){
        eventId=thisEventId;
    }
    this.setState=function(publishState){
        state=publishState;
    }
    this.ajax = function (){
        return $.ajax({
            type: 'POST',
            url: '/mycontent/publish/'+eventId,
            dataType: 'json',
            data:{"publishState":state, 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        });
    }

    this.fetch = function(){
        return this.ajax();
    }
}

function AjaxAdminSearchItems() {

    this.pageNumber = 1;
    this.postData = null;

    this.setPostData = function (postData) {
        this.postData = postData;
    };

    this.getPostData = function () {
        return this.postData;
    };

    this.getPageNumber = function () {
        return this.pageNumber;
    };

    this.setPageNumber = function (pageNumber) {
        if (typeof pageNumber !== 'undefined') {
            this.pageNumber = pageNumber;
        }
    };

    this.ajax = function () {
        return $.ajax({
            type: 'POST',
            url: '/items/AjaxSearchItems/' + this.getPageNumber(),
            dataType: 'json',
            data: this.getPostData()
        });
    };

    this.fetch = function () {
        return this.ajax();
    }
}