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
        var items=new ReservedItems();
        items.setPageNumber(pageNumber);
        $.when(items.fetch()).then(function(data){
            if(data.total_items == 0) {
                $("#no-items").show();
            } else {
                $("#no-items").hide();
                $("#items-container").html(template.render(data));
                Pagination(data,'render',$('#owned-pagination'));
            }
        })
    }

    function manageItem(itemId){
        window.location.href="/vault/manage/"+itemId;
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Reserved Events'); ?></h2>
    </div>
</div>
<div class="alert alert-info" id="no-items" style="display: none;">
    You have no Reserved Items to display. We kindly encourage you to browse the vault and check what opportunities may be available for you to enjoy.
</div>
<nav>
    <ul class="pagination pagination-sm" id="owned-pagination">

    </ul>
</nav>
<div class="row" id="items-container">

</div>