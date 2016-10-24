<script id="vaultSelected" type="text/x-jsrender">
<div class="ibox-content no-padding">
    <div id="selected-carousel-container" class="carousel slide" data-ride="carousel">
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
        <|for items|>
            <|if #index==0|>
                <div class="item active">
                    <|if isByArrangement==1|>
                        <a href="/vault/arranged/<|:itemId|>">
                    <|else|>
                        <a href="/vault/event/<|:itemId|>">
                    <|/if|>
                    <img src="<|:image|>" alt="..." class="carousel-image">
                    <div class="carousel-text">
                        <|:itemTitle|>
                        <div>
                            <span style="font-size:16px;"><|:startDateTime|></span>
                            <span class="pull-right"><|:specialStatus|></span>
                        </div>
                    </div>
                    </a>
                </div>
            <|else|>
                <div class="item">
                    <|if isByArrangement==1|>
                        <a href="/vault/arranged/<|:itemId|>">
                    <|else|>
                        <a href="/vault/event/<|:itemId|>">
                    <|/if|>
                    <img src="<|:image|>" alt="..." class="carousel-image">
                    <div class="carousel-text">
                        <|:itemTitle|>
                        <div>
                            <span style="font-size:16px;"><|:startDateTime|></span>
                            <span class="pull-right"><|:specialStatus|></span>
                        </div>
                    </div>

                    </a>
                </div>
            <|/if|>
        <|/for|>
        </div>

        <!-- Controls -->
        <|if items.length>1|>
            <a class="left carousel-control" href="#selected-carousel-container" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#selected-carousel-container" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        <|/if|>
    </div>
</div>
</script>