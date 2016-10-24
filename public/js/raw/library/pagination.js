/**
 * Created by Daniel Dimmick on 24/03/15.
 */
function Pagination(data,callback,selector){
    var pagers='';
    for ( var i = 0; i < data.total_pages; i++ ) {
        var PageNumber=(i+1);
        if(data.current==PageNumber){
            pagers+='<li class="active pager-button"><a onclick="'+callback+'('+PageNumber+')">'+PageNumber+'</a></li>';
        }
        else{
            pagers+='<li class="pager-button"><a onclick="'+callback+'('+PageNumber+')">'+PageNumber+'</a></li>';
        }
    }
    selector.html(pagers);
}
function Pager(dataset,resultsPerPage,pageNumber,callback,container,template,onComplete,onCompleteArgs){
    var numberOfPages=Math.ceil((parseInt(dataset.totalItems))/resultsPerPage);
    if(pageNumber>numberOfPages || pageNumber==null){
        pageNumber=1;
    }
    this.getPageData=function(){
        var thisPageIndex=((resultsPerPage*pageNumber)-resultsPerPage);
        var items = $.map(dataset.items, function(el) { return el });
        return items.slice(thisPageIndex,(thisPageIndex+resultsPerPage));
    }

    this.setPage=function(newPage){
        pageNumber=newPage;
        container.empty();


        container.append(template.render({"items":this.getPageData()}));
        container.append(this.getPagerButtons());
        container.prepend(this.getPagerButtons());
        if(onComplete!=null){
            window[onComplete](onCompleteArgs)
        }
    }

    this.getPagerButtons=function(){
        var buffer='<div class="col-sm-12" style="margin-bottom:15px;">';
        for (i = 0; i < numberOfPages; i++) {
            buffer+='<button class="btn btn-primary" style="margin-right:5px;" onclick="'+callback+'.setPage('+(i+1)+')">'+(i+1)+'</button>';
        }
        buffer+="</div>";
        return buffer;
    }

    this.setPage(pageNumber);
}