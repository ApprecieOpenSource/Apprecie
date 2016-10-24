/**
 * Created by Daniel Dimmick on 31/03/15.
 */
function GetOrganisationEvents(){
    this.pageNumber=1;
    this.setPageNumber=function(pageNumber){
        this.pageNumber=pageNumber;
    }
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxGetOrganisationEvents/",
            type: 'post',
            dataType: 'json',
            data: {"CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function GetSelectedEvents(){
    this.pageNumber=1;
    this.setPageNumber=function(pageNumber){
        this.pageNumber=pageNumber;
    }
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxGetSelectedEvents/",
            type: 'post',
            dataType: 'json',
            data: {"CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function GetGuestListEvents(){
    this.pageNumber=1;
    this.setPageNumber=function(pageNumber){
        this.pageNumber=pageNumber;
    }
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxGetGuestListEvents/",
            type: 'post',
            dataType: 'json',
            data: {"CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function getInterests(){
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxGetAllEventInterests/",
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function getBrands(){
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxGetAllEventBrands/",
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function SearchVault(){
    this.pageNumber=1;
    this.brandId=[];
    this.categoryId=[];
    this.catering=[];
    this.age=[];
    this.gender=[];
    this.price=[];
    this.type=[];
    this.distance='none';
    this.latitude=null;
    this.longitude=null;
    this.order=null;

    this.setOrder=function(order){
        this.order=order;
    }

    this.getOrder=function(){
        return this.order;
    }

    this.setPageNumber=function(pageNumber){
        this.pageNumber=pageNumber;
    }

    this.addPrice=function(option){
        this.price.push(option);
    }

    this.addType=function(option){
        this.type.push(option);
    }

    this.addBrandId=function(brandId){
        this.brandId.push(brandId);
    }

    this.addCatering=function(option){
        this.catering.push(option);
    }
 
    this.addAge=function(option){
        this.age.push(option);
    }

    this.addGender=function(option){
        this.gender.push(option);
    }

    this.addCategoryId=function(categoryId){
        this.categoryId.push(categoryId);
    }

    this.getBrandId=function(){
        return this.brandId;
    }

    this.getCatering=function(){
        return this.catering;
    }

    this.getAge=function(){
        return this.age;
    }

    this.getGender=function(){
        return this.gender;
    }

    this.getPrice=function(){
        return this.price;
    }

    this.getType=function(){
        return this.type;
    }

    this.getCategoryId=function(){
        return this.categoryId;
    }
    this.setType=function(typeStr){
        this.type=typeStr;
    }
    this.setPrice=function(priceArray){
        this.price=priceArray;
    }
    this.setCategoryId=function(categoryId){
        this.categoryId=categoryId;
    }
    this.setBrandId=function(brandId){
        this.brandId=brandId;
    }
    this.setCatering=function(catering){
        this.catering=catering;
    }
    this.setAge=function(age){
        this.age=age;
    }
    this.setGender=function(gender){
        this.gender=gender;
    }

    this.setDistance=function(distance){
        this.distance=distance;
    }
    this.setSearchLatitude=function(latitude){
        this.latitude=latitude;
    }
    this.setSearchLongitude=function(longitude){
        this.longitude=longitude;
    }

    this.getUserCollection=function(){
        if(typeof userCollection === 'object'){
            return userCollection.getUsers();
        }
        return [];
    }
    this.getRoleCollection=function(){
        if(typeof roleCollection === 'object'){
            return roleCollection.getRoles();
        }
        return [];
    }
    this.getGroupCollection=function(){
        if(typeof groupCollection === 'object'){
            return groupCollection.getGroups();
        }
        return [];
    }
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxGetAllEvents/"+this.pageNumber,
            type: 'post',
            dataType: 'json',
            data:{"order":this.order,"price":this.price,"type":this.type,"brandId":this.brandId,"categoryId":this.categoryId,"catering":this.catering,"age":this.age,"gender":this.gender,distance:this.distance,latitude:this.latitude,longitude:this.longitude,"users":this.getUserCollection(),"groups":this.getGroupCollection(),"roles":this.getRoleCollection(),"CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

