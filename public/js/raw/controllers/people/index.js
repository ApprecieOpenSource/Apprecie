$(document).ready(function(){
    $('.search-click').click(function(){
        peopleSearch(1);
    })
    $('.search-change').change(function(){
        peopleSearch(1);
    })
    peopleSearch(1);

    var searchTimeout=null;
    $('.search-text-change').keyup(function(){
        clearTimeout(searchTimeout);
        searchTimeout=setTimeout(function() {
            peopleSearch(1);
        }, 700);
    })
})

function peopleSearch(pageNumber){
    var accountActive=$('#account-active').is(':checked');
    var accountDeactivated=$('#account-deactivated').is(':checked');
    var accountPending=$('#account-pending').is(':checked');

    var loginSuspended=$('#login-suspended').is(':checked');
    var loginEnabled=$('#login-enabled').is(':checked');
    var suggestionsOnly=$('#suggestions-only').is(':checked');

    var search= new SearchUsers();

    search.setAccountActive(accountActive);
    search.setAccountDeactivated(accountDeactivated);
    search.setAccountPending(accountPending);
    search.setPageNumber(pageNumber);
    search.setSuggestionsOnly(suggestionsOnly);

    if(loginSuspended===true && loginEnabled===true){
        search.setLogin('All');
    }
    else if(loginSuspended===true && loginEnabled==false){
        search.setLogin('suspended');
    }
    else if(loginSuspended==false && loginEnabled==true){
        search.setLogin('enabled');
    }

    search.setEmail($('#email').val());
    search.setName($('#name').val());
    search.setReference($('#reference').val());
    search.setRole($('#roleName').val());
    search.setGroup($('#groupId').val());

    $.when(search.fetch()).then(function(data){
        if(data.status=='failed'){
            buffer='<tr><td colspan="8">'+data.message+'</td></tr>'
        }
        else{
            var buffer='';
            $.each(data.items, function(key, value) {
                buffer+='<tr>' +
                    '<td>'+value.image+'</td>'+
                    '<td class="hidden-xs"><a href="/people/viewuser/'+value.userid+'">'+value.profile.firstname+' '+value.profile.lastname+'</a></td>'+
                    '<td><a href="/people/viewuser/'+value.userid+'">'+value.reference+'</a></td>'+
                    '<td>'+value.groups+'</td>'+
                    '<td>'+value.organisation+'</td>'+
                    '<td class="hidden-xs">'+value.profile.email+'</td>'+
                    '<td class="hidden-xs">'+value.role+'</td>'+
                    '<td>'+value.account+'</td>'+
                    '<td>'+value.login+'</td>' +
                    '<td>'+value.suggestedEvents+'</td>' +
                    '</tr>';
            });
            Pagination(data,'peopleSearch',$('#user-search-pagination'));
        }
        $('#user-results').html(buffer);
    })
}