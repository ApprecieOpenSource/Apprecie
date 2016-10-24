/**
 * Created by Daniel Dimmick on 31/03/15.
 */
function VaultItemSearch(){
    var categories=[];
    var title=null;
    var pageNumber=1;

    this.ajax= function (){
        return $.ajax({
            url: "/api/AjaxVaultSearch/",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: getFilters()
        });
    }

    this.fetch= function(){
        return this.ajax();
    }

    this.addCategoryFilter= function(categoryId){
        categories.push(categoryId);
    }
    this.setTitleFilter=function(newTitle){
        title=newTitle;
    }

    var getFilters=function(){
        return {"categories":categories,"title":title,"pageNumber":pageNumber};
    }
}

function DisplayItems(data,selector){
    this.grid=function(){
        var buffer='';
        $.each(data.items, function(key, value) {
            switch(value.item.isByArrangement){
                case '1':
                    buffer+='<div class="col-sm-6">' +
                        '<a href="/vault/arranged/'+value.item.itemId+'">'+
                        '<div class="item-container">'+
                            '<div class="item-banner">Booking ends: <span class="pull-right"></span></div>'+
                            '<img src="'+value.image+'" class="img-responsive"/>'+
                            '<div class="item-text">'+value.item.title+' <span class="pull-right">'+value.brand+'</span>'+
                                '<div class="hidden-item">'+value.item.summary+'</div>'+
                            '</div>'+
                        '</div>'+
                    '</a>' +
                    '</div>';
                    break;
                default:
                    buffer+='<div class="col-sm-6">' +
                        '<a href="/vault/event/'+value.item.itemId+'">'+
                        '<div class="item-container">'+
                        '<div class="item-banner">Booking ends: <span class="pull-right"></span></div>'+
                        '<img src="'+value.image+'" class="img-responsive"/>'+
                        '<div class="item-text">'+value.item.title+' <span class="pull-right">'+value.brand+'</span>'+
                        '<div class="hidden-item">'+value.item.summary+'</div>'+
                        '</div>'+
                        '</div>'+
                        '</a>' +
                        '</div>';
                    break;
            }
        });
        selector.fadeOut('fast',function(){
            if(buffer==''){
                selector.html('<div class="alert alert-info" role="alert">'+data.noitems+'</div>').fadeIn('fast');
            }
            else{
                selector.html(buffer).fadeIn('fast');
            }
            selector.css('background-color','transparent');
            $('.item-container').hover(function(){
                $(this).find('.hidden-item').stop().toggle('fast');
            },function(){
                $(this).find('.hidden-item').stop().toggle('fast');
            })
        })
    }
    this.list=function(){
        var buffer='';
        $.each(data.items, function(key, value) {

            switch(value.item.isByArrangement){
                case '1':
                    buffer+='<div class="col-sm-12">' +
                        '<div class="media" style="background-color: white; padding: 10px;">'+
                        '<div class="media-left">'+
                        '<a href="/vault/arranged/'+value.item.itemId+'">'+
                        '<img src="'+value.image+'" style="max-width: 150px;" class="img-responsive"/>'+
                        '</a>'+
                        '</div>'+
                        '<div class="media-body">'+
                        '<h4 class="media-heading">'+
                        '<a href="/vault/arranged/<?= $event->getItemId(); ?>">'+
                        value.item.title+
                        '</a>'+
                        '<span class="pull-right">'+value.brand+'</span></h4>'+
                        'Booking ends:'+
                        '<span class="pull-right">Event Type</span>'+
                        '<p style="margin-top: 5px;">'+value.item.summary+'</p>'+
                        '</div>'+
                        '</div>' +
                        '</div>';
                    break;
                default:
                    buffer+='<div class="col-sm-12">' +
                        '<div class="media" style="background-color: white; padding: 10px;">'+
                        '<div class="media-left">'+
                        '<a href="/vault/event/'+value.item.itemId+'">'+
                        '<img src="'+value.image+'" style="max-width: 150px;" class="img-responsive"/>'+
                        '</a>'+
                        '</div>'+
                        '<div class="media-body">'+
                        '<h4 class="media-heading">'+
                        '<a href="/vault/arranged/<?= $event->getItemId(); ?>">'+
                        value.item.title+
                        '</a>'+
                        '<span class="pull-right">'+value.brand+'</span></h4>'+
                        'Booking ends:'+
                        '<span class="pull-right">Event Type</span>'+
                        '<p style="margin-top: 5px;">'+value.item.summary+'</p>'+
                        '</div>'+
                        '</div>' +
                        '</div>';
                    break;
            }
        });
        selector.fadeOut('fast',function(){
            if(buffer==''){
                selector.html('<div class="alert alert-info" role="alert">'+data.noitems+'</div>').fadeIn('fast');
            }
            else{
                selector.html(buffer).fadeIn('fast');
            }
            selector.css('background-color','transparent');
        })

    }
    this.map=function(){
        var map=new GoogleMap(parseInt(data.latitude),parseInt(data.longitude),10);
        $.each(data.items, function(key, value) {
            if(value.address!=null){
                var contentString = '<div class="media" style="width:350px;border-bottom:none;">'+
                    '<div class="media-body">'+
                    '<h4 class="media-heading"><?= $event->'+value.item.title+'</h4>'+
                    '<p>Date</p>'+
                    '<p>'+value.item.summary+'</p>'+
                    '<a href="/vault/event/'+value.item.itemId+'">Read more ></a>'+
                    '</div>'+
                    '</div>';
                map.addMarker(value.address.latitude,value.address.longitude,contentString);
            }

        });
        map.initialise(selector.attr('id'));
    }
}

function ArrangeItem(itemId,postData){
    this.ajax= function (){
        return $.ajax({
            url: "/api/arrange/"+itemId,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: postData
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function OwnedItems(){

    this.pageNumber=1;
    this.setPageNumber=function(pageNumber){
        this.pageNumber=pageNumber;
    }
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxOwnedItems/"+this.pageNumber,
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function ReservedItems(){

    this.pageNumber=1;
    this.setPageNumber=function(pageNumber){
        this.pageNumber=pageNumber;
    }
    this.ajax= function (){
        return $.ajax({
            url: "/vault/ajaxReservedItems/"+this.pageNumber,
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function AttendingItems(){
    this.pageNumber=1;
    this.setPageNumber=function(pageNumber){
        this.pageNumber=pageNumber;
    }
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxAttendingItems/"+this.pageNumber,
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}

function ArrangingItems(){
    this.pageNumber=1;
    this.setPageNumber=function(pageNumber){
        this.pageNumber=pageNumber;
    }
    this.ajax= function (){
        return $.ajax({
            url: "/vault/AjaxArrangingItems/"+this.pageNumber,
            type: 'get',
            dataType: 'json'
        });
    }

    this.fetch= function(){
        return this.ajax();
    }
}