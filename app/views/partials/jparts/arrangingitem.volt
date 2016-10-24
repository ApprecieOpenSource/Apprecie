<script id="arrangingitem" type="text/x-jsrender">
<|for items|>
<div class="col-sm-6" style="height: 275px;">
<|for item|>
    <div class="ibox float-e-margins highlight-ibox-title">
<|/for|>
        <div class="ibox-title" style="height: auto;">
            <div style="width: 80%;">
                <h5>
                <|for item|>
                    <|:title|>
                <|/for|>
                </h5>
            </div>
            <div style="width: 20%;" class="pull-right">
                <|if item.isArranged==1|>
                    <span class="pull-right"><a style="text-decoration:none; cursor:pointer;" onclick="manageItem(<|:item.itemId|>)">Manage <i class="fa fa-cog"></i></a></span>
                <|else|>
                    <span class="pull-right"><a style="text-decoration:none; cursor:pointer;" onclick="manageArrangement(<|:item.itemId|>)">Manage <i class="fa fa-cog"></i></a></span>
                <|/if|>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="ibox-content" style="height: 180px;">
            <div class="row">
                <div class="col-sm-5">
                    <img src="<|:image|>" class="img-responsive">
                </div>
                <div class="col-sm-7">
                    <table class="table table-striped">
                        <tbody>
                            <tr><td><?= _g('Hosted by'); ?></td><td><|:brand|></td></tr>
                            <|if viewState=='expired'|>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr><td colspan="2" style="color:red;">This item was expired</td></tr>
                            <|else viewState=='closed'|>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr><td colspan="2" style="color:red;">You failed to book this item as agreed by <|:bookingEnd|></td></tr>
                            <|else viewState=='approved'|>
                                <tr><td>Balance</td><td><|:price|></td></tr>
                                <tr><td colspan="2" style="color:orange;">This item was confirmed but must be paid for by <|:bookingEnd|></td></tr>
                            <|else viewState=='rejected'|>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr><td colspan="2" style="color:red;">This Item was rejected</td></tr>
                            <|else viewState=='waiting'|>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr><td colspan="2" style="color:green;">Waiting for approval</td></tr>
                            <|else viewState=='other'|>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                            <|/if|>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<|/for|>
</script>