var cvaultUsers=function(){
    this.role='All';
    this.name=null;
    this.email=null;
    this.reference=null;
    this.accountActive=false;
    this.accountDeactivated=false;
    this.accountPending=false;
    this.metricsOnly=false;
    this.group='all';
    this.Login='All';
    this.pageNumber=1;
    this.suggestions=false;

    this.setGroup=function(group){
        this.group=group;
    }
    this.setMetricsOnly=function(state){
        this.metricsOnly=state;
    }
    this.getMetricsOnly=function(){
        return this.metricsOnly;
    }
    this.setRole= function(role){
        this.role=role;
    }
    this.getRole= function(){
        return this.role;
    }
    this.setName= function(name){
        this.name=name;
    }
    this.getName= function(){
        return this.name;
    }
    this.setEmail= function(email){
        this.email=email;
    }
    this.getEmail= function(){
        return this.email;
    }
    this.setReference= function(reference){
        this.reference=reference;
    }
    this.getReference= function(){
        return this.reference;
    }
    this.setAccountActive= function(account){
        this.accountActive=account;
    }
    this.getAccountActive= function(){
        return this.accountActive;
    }
    this.setAccountDeactivated= function(account){
        this.accountDeactivated=account;
    }
    this.getAccountDeactivated= function(){
        return this.accountDeactivated;
    }
    this.setAccountPending= function(account){
        this.accountPending=account;
    }
    this.getAccountPending= function(){
        return this.accountPending;
    }
    this.setLogin= function(login){
        this.login=login;
    }
    this.getLogin= function(){
        return this.login;
    }
    this.setPageNumber= function(pageNumber){
        this.pageNumber=pageNumber;
    }
    this.getPageNumber= function(){
        return this.pageNumber;
    }
    this.getGroup=function(){
        return this.group;
    }

    this.setSuggestions=function(suggeestions){
        this.suggestions=suggeestions;
    }

    this.getSuggestions=function(){
        return this.suggestions;
    }

    this.ajax= function (){
        return $.ajax({
            type: 'POST',
            url: '/cvault/ajaxGetCvaultUsers/',
            dataType: 'json',
            data:{"suggestions":this.getSuggestions(),"pageNumber":this.getPageNumber(),"group":this.getGroup(),"metricsOnly":this.getMetricsOnly(),"roleName":this.getRole(),"name":this.getName(),"email":this.getEmail(),"reference":this.getReference(),"accountActive":this.getAccountActive(),"accountDeactivated":this.getAccountDeactivated(),"accountPending":this.getAccountPending(),"login":this.getLogin()}

        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

var cUserCollection=function(){
    var users=[];
    this.toggleUser=function(userId){
        if($.inArray( userId, users)==-1){
                this.addUser(userId);
        }
        else{
            this.removeUser(userId);
        }
    }

    this.addUser=function(userId){
        if($.inArray( userId, users)==-1){
            users.push(userId);
        }
    }
    this.removeUser=function(userId){
        users = jQuery.grep(users, function(value) {
            return value != userId;
        });
    }
    this.getUsers=function(){
        return users;
    }

    this.clear=function(){
        users=[];
    }
}

var cGroupCollection=function(){
    var groups=[];
    this.toggleGroup=function(groupId){
        if($.inArray( groupId, groups)==-1){
            this.addGroup(groupId);
        }
        else{
            this.removeGroup(groupId);
        }
    }

    this.addGroup=function(groupId){
        if($.inArray( groupId, groups)==-1){
            groups.push(groupId);
        }
    }
    this.removeGroup=function(groupId){
        groups = jQuery.grep(groups, function(value) {
            return value != groupId;
        });
    }
    this.getGroups=function(){
        return groups;
    }

    this.clear=function(){
        groups=[];
    }
}

var cRoleCollection=function(){
    var roles=[];
    this.toggleRole=function(roleId){
        if($.inArray( roleId, roles)==-1){
            this.addRole(roleId);
        }
        else{
            this.removeRole(roleId);
        }
    }

    this.addRole=function(roleId){
        if($.inArray( roleId, roles)==-1){
            roles.push(roleId);
        }
    }
    this.removeRole=function(roleId){
        roles = jQuery.grep(roles, function(value) {
            return value != roleId;
        });
    }
    this.getRoles=function(){
        return roles;
    }

    this.clear=function(){
        roles=[];
    }
}