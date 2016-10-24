<script src="/js/compiled/public/js/raw/library/vault.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/vault/attending.min.js"></script>
<?php $this->partial("partials/jparts/attendingitem"); ?>

<script>
    $(document).ready(function(){
        render(1);
    })

    function render(pageNumber){
        var template = $.templates("#attendingitem");
        var items=new AttendingItems();
        items.setPageNumber(pageNumber);
        $.when(items.fetch()).then(function(data){
            if(data.total_items == 0) {
                $("#no-items").show();
            } else {
                $("#no-items").hide();
                $("#items-container").html(template.render(data));
                Pagination(data,'render',$('#attending-pagination'));
            }
        })
    }

    function manageItem(itemId){
        window.location.href="/vault/manage/"+itemId;
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>Events I'm Attending</h2>
    </div>
</div>
<nav>
    <ul class="pagination pagination-sm" id="attending-pagination">

    </ul>
</nav>
<div class="row" id="items-container"></div>
<div class="alert alert-info" id='no-items' style="display:none;">
    You are not currently attending any events. We kindly encourage you to browse the vault and check what opportunities may be available for you to enjoy.
</div>