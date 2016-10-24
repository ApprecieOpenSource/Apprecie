<script src="/js/compiled/public/js/raw/library/vault.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/vault/owned.min.js"></script>
<?php $this->partial("partials/jparts/owneditem"); ?>

<script>
    $(document).ready(function(){
        render(1);
    })

    function render(pageNumber){
        var template = $.templates("#owneditem");
        var items=new OwnedItems();
        items.setPageNumber(pageNumber);
        $.when(items.fetch()).then(function(data){
            if(data.total_items == 0) {
                $("#no-items").show();
            } else {
                $("#items-container").html(template.render(data));
                Pagination(data,'render',$('#owned-pagination'));
                $("#no-items").hide();
            }
        })
    }

    function manageItem(itemId){
        window.location.href="/vault/manage/"+itemId;
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Purchased Events'); ?></h2>
    </div>
</div>
<nav>
    <ul class="pagination pagination-sm" id="owned-pagination">

    </ul>
</nav>

<div class="row" id="items-container"></div>
<div class="alert alert-info" id='no-items' style="display:none;">
    You have no Purchased Items to display. We kindly encourage you to browse the vault and check what opportunities may be available for you to enjoy.
</div>