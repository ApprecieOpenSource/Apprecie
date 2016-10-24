var multiselect= function(){
    var Children=null;
    var selectedUsers=[];
    this.role='All';
    var currentChildren=null;

    this.setRole=function(role){
        this.role=role;
    }
    this.getRole=function(){
        return this.role;
    }

    this.initialise=function(){
        currentChildren=new CurrentUserChildren();
        currentChildren.setRole(this.getRole());
        $.when(currentChildren.fetch()).then(function(data){
            Children=data;
            RenderAvailable();
        })
    }

    this.reset=function(){
        $('#selected-tbl').empty();
        $('#available-tbl').empty();
        selectedUsers=[];
        RenderAvailable();
    }
    var RenderAvailable= function(){
        var buffer='';
        $.each(Children.items, function(key, thisChild) {
            if(thisChild.status=='active'){
                buffer+=
                    '<tr class="clickable" id="available-user-'+thisChild.userId+'" onclick="usersearch.selectUser('+key+',\'#available-user-'+thisChild.userId+'\')">'+
                        '<td>'+thisChild.profile.firstname+' '+thisChild.profile.lastname+'</td>'+
                        '<td>'+thisChild.role+'</td>'+
                    '</tr>';
            }
        })
        $('#available-tbl').html(buffer);
    }

    this.selectUser= function(key,element){
        if($.inArray(key,selectedUsers)==-1){
            selectedUsers.push(key);
            var value=Children.items[key];
            var buffer=
                '<tr class="clickable" style="display:none;" id="user-'+value.userId+'" onclick="usersearch.removeUser('+key+',\'#user-'+value.userId+'\')">'+
                    '<td>'+value.profile.firstname+' '+value.profile.lastname+'</td>'+
                    '<td>'+value.role+'</td>'+
                '</tr>';
            $('#selected-tbl').append(buffer);
            $('#user-'+value.userId).fadeIn('fast');
        }
        $(element).fadeOut('fast');
    }

    this.removeUser= function(key,element){
        var value=Children.items[key];
        selectedUsers = $.grep(selectedUsers, function(value) {
            return value != key;
        });
        $('#user-'+value.userId).fadeOut('fast',function(){
            $('#user-'+value.userId).remove();
            $('#available-user-'+value.userId).fadeIn('fast');
        });
    }

    this.getSelectedUsers=function(){
        var results=[];
        $.each(selectedUsers, function(key, value) {
            results.push(Children.items[value]);
        })
        return results;
    }
}