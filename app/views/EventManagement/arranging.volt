<script src="/js/compiled/public/js/raw/library/vault.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>

<?php $this->partial("partials/jparts/arrangingitem"); ?>

<script>
    $(document).ready(function(){
        render(1);
    })

    function render(pageNumber){
        var template = $.templates("#arrangingitem");
        var items=new ArrangingItems();
        items.setPageNumber(pageNumber);
        $.when(items.fetch()).then(function(data){
            if(data.total_items == 0) {
                $("#no-items").show();
            } else {
                $("#items-container").html(template.render(data));
                Pagination(data,'render',$('#arranging-pagination'));
            }
        })
    }

    function manageItem(itemId){
        window.location.href="/vault/event/"+itemId;
    }
    function manageArrangement(itemId){
        window.location.href="/vault/myarranged/"+itemId;
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>Arranging Events</h2>
    </div>
</div>
<nav>
    <ul class="pagination pagination-sm" id="arranging-pagination">

    </ul>
</nav>
<div class="row" id="items-container"></div>
<div class="alert alert-info" id='no-items' style="display:none;">
    You are not currently arranging any items. We kindly encourage you to browse the vault and check what opportunities may be available for you to enjoy.
</div>