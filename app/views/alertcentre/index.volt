<style>
    .tab-content{
        background-color: white;
        padding: 5px;;
    }
    #messages-tabpanel{
        margin-bottom: 15px;;
    }
</style>
<script src="/js/compiled/public/js/raw/library/alertcentre.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>

<?php $this->partial("partials/jparts/alertmessage"); ?>

<script>
    $(document).ready(function(){
        getMessagePage(1);
    })

    function getMessagePage(pageNumber){
        $.when(ajaxGetThreads(pageNumber)).then(function(data){
            var template = $.templates("#alertmessage");
            $("#received-messages").html(template.render(data));
            Pagination(data,'getMessagePage',$('#message-pagination'));
        })
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Alert Centre'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div role="tabpanel" id="messages-tabpanel">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#item" aria-controls="home" role="tab" data-toggle="tab">Threads</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="item">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Sent</th>
                            <th>Item</th>
                        </tr>
                        </thead>
                        <tbody id="received-messages">

                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination pagination-sm" id="message-pagination">

                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>