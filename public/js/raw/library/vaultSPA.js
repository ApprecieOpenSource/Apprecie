function vaultSPA(currentUserId,progressSelector,onComplete){
    var progressbar=null;
    var user=$.parseJSON(sessionStorage.getItem('currentUser'));
    var children=$.parseJSON(sessionStorage.getItem('currentUserChildren'));
    var brands=$.parseJSON(sessionStorage.getItem('vaultBrands'));
    var interests=$.parseJSON(sessionStorage.getItem('vaultInterests'));
    var items= $.parseJSON(sessionStorage.getItem('vaultItems'));
    var latitude=null;
    var longitude=null;
    var distance=null;
    var sortDirection=false;
    var sortProperty=null;

    var filteredResults=[];
    var filters={};


    if(user==null || children==null || brands==null || interests==null || items==null){
        resetSessionData();
    }
    else{
        // ALWAYS RESET SESSION DATA FOR NOW....
        resetSessionData();
        /*
        progressbar=new progressBar(progressSelector,1,onComplete);
        setTimeout(function(){
            progressbar.completeStep();
        }, 400);
        */
    }

    this.reset=function(){
        var i = sessionStorage.length;
        while(i--) {
            var key = sessionStorage.key(i);
            sessionStorage.removeItem(key);
        }
        resetSessionData();
    };

    function resetSessionData(){
        var AjaxUser=new AjaxGetUser(currentUserId);
        var AjaxChildren=new AjaxGetUserChildren(currentUserId);
        var AjaxBrands=new getVaultBrands();
        var AjaxInterests=new getVaultInterests();
        var AjaxEvents=new AjaxGetAllEvents();
        progressbar=new progressBar(progressSelector,5,onComplete);
        progressbar.setTitle('Looking at your interests');
        $.when(AjaxUser.fetch()).then(function(data){
            sessionStorage.setItem("currentUser", JSON.stringify(data));
            user=data;
            progressbar.completeStep();

            progressbar.setTitle('Learning about your People...');
            $.when(AjaxChildren.fetch()).then(function(data){
                sessionStorage.setItem("currentUserChildren", JSON.stringify(data));
                children=data;
                progressbar.completeStep();

                progressbar.setTitle('Filtering event brands...');
                $.when(AjaxBrands.fetch()).then(function(data){
                    sessionStorage.setItem("vaultBrands", JSON.stringify(data));
                    brands=data;
                    progressbar.completeStep();

                    progressbar.setTitle('Filtering event interests...');
                    $.when(AjaxInterests.fetch()).then(function(data){
                        sessionStorage.setItem("vaultInterests", JSON.stringify(data));
                        interests=data;
                        progressbar.completeStep();

                        progressbar.setTitle('Preparing your events...');
                        $.when(AjaxEvents.fetch()).then(function(data){
                            sessionStorage.setItem("vaultItems", JSON.stringify(data));
                            items=data;
                            postProcess();
                            progressbar.completeStep();
                        })
                    })
                })
            })
        })
    }

    this.setSort=function(property,direction){
        sortDirection=direction;
        sortProperty=property;
        this.refineSearch();
    }

    function addFilteredResult(record){
        filteredResults.push(record);
    }

    this.getFilteredResults=function(){
        return filteredResults;
    }

    this.getUser=function(){
        return user;
    }
    this.getChildren=function(){
        return children;
    }

    this.getItems=function(){
        return items;
    }

    this.addFilter=function(property,value){
        filters[property]=value;
        this.refineSearch();
    }

    this.getBrands=function(){
        return brands;
    }

    this.getInterests=function(){
        return interests;
    }

    this.getFilters=function(){
        return filters;
    }

    this.setDistance=function(miles){
        distance=miles;
        this.refineSearch();
    }

    this.setSearchLatLng=function(lat,lng){
        latitude=lat;
        longitude=lng;
        this.refineSearch();
    }

    this.resetFilters=function(){
        filters=[];
    }

    function postProcess(){

        // Tie up all items with their interests
        var vaultItemInterests=[];
        $.each(interests,function(){
            var interestId=this.interestId;
            $.each(this.items,function(){
                if(vaultItemInterests[this]!=null){
                    vaultItemInterests[this].push(interestId);
                }
                else{
                    vaultItemInterests[this]=[];
                    vaultItemInterests[this].push(interestId);
                }
            })
        })
        var timeToCompare=((new Date).getTime() / 1000);
        items.items = items.items.filter(function( obj ) {
            return timeToCompare<=obj.startDateTimeEpoc;
        });
        items.totalItems=items.items.length;
        $.each(items.items,function(){
            var itemId=this.itemId;
            var itemIndex=findWithAttr(items.items,'itemId',itemId);
            items.items[itemIndex].interests=vaultItemInterests[itemId];
            items.items[itemIndex].suggestions=getSuggestsCount(itemId);
        })

        sessionStorage.setItem("vaultItems", JSON.stringify(items));
    }

    function getSuggestsCount(itemId){
        var count=0;
        var itemIndex=findWithAttr(items.items,'itemId',itemId);
        var itemInterests=items.items[itemIndex].interests;
        if(itemInterests!=null){
            $.each(children, function(){
                var userInterests=this.interests;
                if(userInterests!=null){
                    if(anyMatchInArray(itemInterests,userInterests)){
                        count++;
                    }
                }
            })
        }
        return count;
    }

    this.refineSearch=function(){
        filteredResults=this.getItems().items;
        if(filters.isByArrangement!=null){
            filteredResults = filteredResults.filter(function( obj ) {
                return obj.isByArrangement == filters.isByArrangement;
            });
        }

        if(filters.pricePerAttendee!=null){
            filteredResults = filteredResults.filter(function( obj ) {
                if(filters.pricePerAttendee=='fixed'){
                    return obj.unitPrice >= 1;
                }
                else{
                    return obj.unitPrice ==0;
                }
            });
        }

        if(filters.brand!=null  && filters.brand.length!=0){
            filteredResults = filteredResults.filter(function( obj ) {
                return $.inArray(obj.creatorOrganisationId,filters.brand)!=-1;
            });
        }
        if(filters.interest!=null  && filters.interest.length!=0){
            var itemMatches=[];
            $.each(filters.interest,function(){
                itemMatches=$.merge(itemMatches,interests[this].items);
            })
            itemMatches=unique(itemMatches);

            filteredResults = filteredResults.filter(function( obj ) {
                return $.inArray(obj.itemId,itemMatches)!=-1;
            });
        }

        if(filters.age!=null && filters.age.length!=0){
            filteredResults = filteredResults.filter(function( obj ) {
                if(($.inArray('targetAge18to34',filters.age)!=-1) && obj.targetAge18to34=='1'){
                    return true;
                }
                if(($.inArray('targetAge34to65',filters.age)!=-1) && obj.targetAge34to65=='1'){
                    return true;
                }
                if(($.inArray('targetAge65Plus',filters.age)!=-1) && obj.targetAge65Plus=='1'){
                    return true;
                }
            });
        }

        if(filters.gender!=null && filters.gender.length!=0){
            filteredResults = filteredResults.filter(function( obj ) {
                if(($.inArray('male',filters.gender)!=-1) && obj.gender=='male'){
                    return true;
                }
                if(($.inArray('female',filters.gender)!=-1) && obj.gender=='female'){
                    return true;
                }
                if(($.inArray('mixed',filters.gender)!=-1) && obj.gender=='mixed'){
                    return true;
                }
            });
        }
        if(distance!=null && latitude!=null && longitude!=null){
            var latmod = parseFloat(0.012 * distance);
            var longmod = parseFloat(0.02 * distance);

            $.each(filteredResults,function(){
                var eventLatitude=parseFloat(this.latitude);
                var eventLongitude=parseFloat(this.longitude);
                if(this.latitude!=null && this.longitude!=null){
                    this.distance=Math.ceil(calculateDistance(latitude,longitude,eventLatitude,eventLongitude,'N'))
                }
            })

            filteredResults = filteredResults.filter(function( obj ) {
                var eventLatitude=parseFloat(obj.latitude);
                var eventLongitude=parseFloat(obj.longitude);
                if((eventLatitude>(latitude-latmod)) && (eventLatitude<(latitude+latmod)) && (eventLongitude<(longitude+longmod)) && (eventLongitude>(longitude-longmod))){
                    return true;
                }
                else if(distance=='any' && obj.distance!=null){
                    return true;
                }
            });
        }
        else{
            $.each(filteredResults,function(){
                this.distance=null;
            })
        }

        if(sortProperty!=null){
            filteredResults.sort(dynamicSort(sortProperty));
        }

        if(sortDirection!=false){
            filteredResults.reverse();
        }
    }

}
function AjaxGetAllEvents(){
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxGetAllEvents/",
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

var anyMatchInArray = function (target, toMatch) {
    "use strict";

    var found, targetMap, i, j, cur;

    found = false;
    targetMap = {};

    // Put all values in the `target` array into a map, where
    //  the keys are the values from the array
    for (i = 0, j = target.length; i < j; i++) {
        cur = target[i];
        targetMap[cur] = true;
    }

    // Loop over all items in the `toMatch` array and see if any of
    //  their values are in the map from before
    for (i = 0, j = toMatch.length; !found && (i < j); i++) {
        cur = toMatch[i];
        found = !!targetMap[cur];
        // If found, `targetMap[cur]` will return true, otherwise it
        //  will return `undefined`...that's what the `!!` is for
    }

    return found;
};

function unique(list) {
    var result = [];
    $.each(list, function(i, e) {
        if ($.inArray(e, result) == -1) result.push(e);
    });
    return result;
}

function calculateDistance(lat1, lon1, lat2, lon2, unit) {
    var radlat1 = Math.PI * lat1/180;
    var radlat2 = Math.PI * lat2/180;
    var radlon1 = Math.PI * lon1/180;
    var radlon2 = Math.PI * lon2/180;
    var theta = lon1-lon2;
    var radtheta = Math.PI * theta/180;
    var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
    dist = Math.acos(dist);
    dist = dist * 180/Math.PI;
    dist = dist * 60 * 1.1515;
    if (unit=="K") { dist = dist * 1.609344 }
    if (unit=="N") { dist = dist * 0.8684 }
    return dist
}

function findWithAttr(array, attr, value) {
    for(var i = 0; i < array.length; i += 1) {
        if(array[i][attr] === value) {
            return i;
        }
    }
}

function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}