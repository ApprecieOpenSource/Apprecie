<script id="vaulttiles" type="text/x-jsrender">
<|for items|>
    <div class="col-md-6 col-lg-6 item-container" >
        <div style="position:relative" class="item-tile">
            <|if isByArrangement=="1"|>
                <a style="text-decoration:none" href="/vault/arranged/<|:itemId|>">
            <|else|>
                <a style="text-decoration:none" href="/vault/event/<|:itemId|>">
            <|/if|>
            </a>
            <div style="position:relative">
                <|if isByArrangement=="1"|>
                <a href="/vault/arranged/<|:itemId|>">
                <|else|>
                <a href="/vault/event/<|:itemId|>">
                <|/if|>
                <img src="<|:image|>" class="img-responsive tile-image" style="width:100%">
                <div class="notice-container">
                    <|if distance != null|>
                        <span class="label label-info" style="font-size:11px"><i class="fa fa-map-marker"></i> <|:distance|> Miles</span>
                    <|/if|>
                    <|if suggestions != 0 && suggestions!=null |>
                        <span class="label label-info" style="font-size:11px"><i class="fa fa-lightbulb-o"></i> <|:suggestions|> People Matches</span>
                    <|/if|>
                    <|if unitPrice=="0"|>
                        <span class="label label-info"  style="font-size:11px"><|:itemType|></span>
                    <|/if|>
                </div>
                <div class="tile-title">
                     <h4 style="font-family: 'Quicksand', sans-serif; margin-left: 10px; font-weight: normal; font-size:16px; color:white;">
                        <|:itemTitle|>
                    </h4>
                    <div style="margin-bottom:10px; margin-left:10px;">
                        <span style="color:white;"><|:startDateTime|></span>
                        <span style="margin-right:10px;color:white;" class="pull-right"><|:creatorOrganisationName|></span>
                    </div>
                    <div class="item-tile-desc">
                        <div style="color:white; margin-bottom:15px; margin-left:5px;" class="hidden-md hidden-xs">
                            <|:shortSummary|>
                        </div>
                    </div>
                </div>
                </a>
            </div>
        </div>
    </div>
<|/for|>
</script>

