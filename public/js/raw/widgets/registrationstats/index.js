var totalUsersCount=0;
var unregistered=0;
var totalComplete=false;
var unregisteredComplete=false;

$(document).ready(function(){
    getTotalUsers();
    getUnregisteredUsers();
})

function getTotalUsers(){
    var totalUsers=new SearchUsers();
    totalUsers.setAccountActive(true);
    totalUsers.setAccountPending(true);
    totalUsers.setMetricsOnly(true);
    $.when(totalUsers.fetch()).then(function(data){
        totalUsersCount= parseInt(data.total_items);
        totalComplete=true;
        readyCheck();
    })
}
function getUnregisteredUsers(){
    var totalUsers=new SearchUsers();
    totalUsers.setAccountActive(false);
    totalUsers.setAccountPending(true);
    totalUsers.setMetricsOnly(true);
    $.when(totalUsers.fetch()).then(function(data){
        unregistered= parseInt(data.total_items);
        unregisteredComplete=true;
        readyCheck();
    })
}

function readyCheck(){
    if(totalComplete==true && unregisteredComplete==true){
        var percentage=Math.round(100-((100/totalUsersCount)*unregistered));
        $('#user-registration-bar').css('width',percentage+'%').attr('aria-valuenow',percentage).html(percentage+'%');
    }
}