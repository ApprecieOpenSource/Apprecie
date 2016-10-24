<script>
    function advancedSearch(){
        $('#advanced-search').toggle();
    }

    function showVideo(){
        $('.video-container').toggle('fast');
    }

    function goToEventManagement(){
        window.location='/mycontent/eventmanagement';
    }

    function AdvancedSearch(){
        $('#hidden-search').toggle('fast');
    }

    function getApprovedEvents(page){
        loader(true);
        var tablebody=$('#approved-table');
        tablebody.css('opacity','0.4');
        $.ajax({
            url: "/mycontent/AjaxApprovedEvents/"+page,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {"isByArrangement":"true", 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        }).done(function(data){
                tablebody.empty();

                if(data.items==null){
                    var row='<tr><td colspan="8">There were no matching items</td></tr>';
                    tablebody.append(row);
                }
                $('#activecount').html(data.TotalResultCount);
                $(data.items).each(function(index, value){
                    var startdate= new moment(value.startDateTime,'YYYY/MM/DD HH:mm');
                    var bookingend= new moment(value.bookingEndDate,'YYYY/MM/DD');
                    var row='<tr>'+
                        '<td class="hidden-xs">'+startdate.format('DD/MM/YYYY')+'</td>'+
                        '<td class="hidden-xs">'+bookingend.format('DD/MM/YYYY')+'</td>'+
                        '<td><a href="/mycontent/eventmanagement/'+value.eventId+'">'+value.title+'</a></td>'+
                        '<td class="hidden-xs">'+value.eventStatus+'</td>'+
                        '<td class="hidden-xs">[unavailable]</td>'+
                        '<td class="hidden-xs">'+value.tier+'</td>'+
                        '</tr>';
                    tablebody.append(row);
                    tablebody.css('opacity','1');
                });
                loader(false);
                LoadPagination($('#active-pagination'),data,'getApprovedEvents');
            });
    }

    function getToDoEvents(page){
        loader(true);
        var tablebody=$('#todo-table');
        tablebody.css('opacity','0.4');
        $.ajax({
            url: "/mycontent/AjaxToDoEvents/"+page,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {"isByArrangement":"true", 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        }).done(function(data){
                tablebody.empty();

                if(data.items==null){
                    var row='<tr><td colspan="8">There were no matching items</td></tr>';
                    tablebody.append(row);
                }
                $('#todocount').html(data.TotalResultCount);
                $(data.items).each(function(index, value){
                    var row='<tr>'+
                        '<td class="hidden-xs"><a href="/mycontent/approve/'+value.itemId+'">'+value.title+'</a></td>'+
                        '<td class="hidden-xs">'+value.sourceOrganisationName+'</td>'+
                        '<td>'+value.sourceCreator+'</a></td>'+
                        '<td class="hidden-xs">'+value.state+'</td>'+
                        '<td class="hidden-xs">'+value.tier+'</td>'+
                        '</tr>';
                    tablebody.append(row);
                    tablebody.css('opacity','1');
                });
                loader(false);
                LoadPagination($('#todo-pagination'),data,'getToDoEvents');
            });
    }

    function getApprovingEvents(page){
        loader(true);
        var tablebody=$('#approving-table');
        tablebody.css('opacity','0.4');
        $.ajax({
            url: "/mycontent/AjaxApprovingEvents/"+page,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {"isByArrangement":"true", 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        }).done(function(data){
                tablebody.empty();

                if(data.items==null){
                    var row='<tr><td colspan="8">There were no matching items</td></tr>';
                    tablebody.append(row);
                }
                $('#approvingcount').html(data.TotalResultCount);
                $(data.items).each(function(index, value){
                    var startdate= new moment(value.startDateTime,'YYYY/MM/DD HH:mm');
                    var bookingend= new moment(value.bookingEndDate,'YYYY/MM/DD HH:mm');
                    var row='<tr>'+
                        '<td class="hidden-xs">'+startdate.format('DD/MM/YYYY HH:mm')+'</td>'+
                        '<td class="hidden-xs">'+bookingend.format('DD/MM/YYYY')+'</td>'+
                        '<td><a href="/mycontent/eventmanagement/'+value.eventId+'">'+value.title+'</a></td>'+
                        '<td class="hidden-xs">'+value.state+'</td>'+
                        '<td class="hidden-xs">[unavailable]</td>'+
                        '<td class="hidden-xs">'+value.tier+'</td>'+
                        '</tr>';
                    tablebody.append(row);
                    tablebody.css('opacity','1');
                });
                loader(false);
                LoadPagination($('#approving-pagination'),data,'getApprovingEvents');
            });
    }

    function getDraftEvents(page){
        loader(true);
        var tablebody=$('#items-table');
        tablebody.css('opacity','0.4');
        $.ajax({
            url: "/mycontent/AjaxDraftEvents/"+page,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {"isByArrangement":"true", 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        }).done(function(data){

            tablebody.empty();

            if(data.items==null){
                var row='<tr><td colspan="8">There were no matching items</td></tr>';
                tablebody.append(row);
            }
            $('#draftcount').html(data.TotalResultCount);
            $(data.items).each(function(index, value){
                var startdate= new moment(value.startDateTime,'YYYY/MM/DD HH:mm');
                var bookingend= new moment(value.bookingEndDate,'YYYY/MM/DD HH:mm');
                var row='<tr>'+
                    '<td class="hidden-xs">'+startdate.format('DD/MM/YYYY HH:mm')+'</td>'+
                    '<td class="hidden-xs">'+bookingend.format('DD/MM/YYYY')+'</td>'+
                    '<td><a href="/mycontent/eventmanagement/'+value.eventId+'">'+value.title+'</a></td>'+
                    '<td class="hidden-xs">'+value.state+'</td>'+
                    '<td class="hidden-xs">'+value.tier+'</td>'+
                    '</tr>';
                tablebody.append(row);
                tablebody.css('opacity','1');
            });
            loader(false);
            LoadPagination($('#draft-pagination'),data,'getDraftEvents');
        });
    }

    function getArchivedEvents(page){
        loader(true);
        var tablebody=$('#archived-table');
        tablebody.css('opacity','0.4');
        $.ajax({
            url: "/mycontent/AjaxArchivedEvents/"+page,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {"isByArrangement":"true", 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        }).done(function(data){
                tablebody.empty();

                if(data.items==null){
                    var row='<tr><td colspan="8">There were no matching items</td></tr>';
                    tablebody.append(row);
                }
                $('#archivedcount').html(data.TotalResultCount);
                $(data.items).each(function(index, value){
                    var startdate= new moment(value.startDateTime,'YYYY/MM/DD HH:mm');
                    var bookingend= new moment(value.bookingEndDate,'YYYY/MM/DD');
                    var row='<tr>'+
                        '<td class="hidden-xs">'+startdate.format('DD/MM/YYYY')+'</td>'+
                        '<td class="hidden-xs">'+bookingend.format('DD/MM/YYYY')+'</td>'+
                        '<td><a href="/mycontent/eventmanagement/'+value.eventId+'">'+value.title+'</a></td>'+
                        '<td class="hidden-xs">' + value.eventStatus + '</td>'+
                        '<td class="hidden-xs">[unavailable]</td>'+
                        '<td class="hidden-xs">'+value.tier+'</td>'+
                        '</tr>';
                    tablebody.append(row);
                    tablebody.css('opacity','1');
                });
                loader(false);
                LoadPagination($('#archived-pagination'),data,'getArchivedEvents');
            });
    }

    function getArrangedpEvents(page){
        loader(true);
        var tablebody=$('#arrangedp-table');
        tablebody.css('opacity','0.4');
        $.ajax({
            url: "/mycontent/AjaxArrangedpEvents/"+page,
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {"isByArrangement":"true", 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        }).done(function(data){
            tablebody.empty();

            if(data.items==null){
                var row='<tr><td colspan="8">There were no matching items</td></tr>';
                tablebody.append(row);
            }
            $('#arrangedpcount').html(data.TotalResultCount);
            $(data.items).each(function(index, value){
                var row='<tr>'+
                    '<td><a href="/vault/arrangedp/'+value.itemId+'">'+value.title+'</a></td>'+
                    '<td>' + value.createdDate + '</td>' +
                    '<td>' + value.requester + ' of '+
                    value.reqorganisation + '</td>'
                    '</tr>';
                tablebody.append(row);
                tablebody.css('opacity','1');
            });
            loader(false);
            LoadPagination($('#arrangedp-pagination'),data,'getArrangedpEvents');
        });
    }

    function LoadPagination(container,data,event){
        var pagers='';
        for ( var i = 0; i < data.PageCount; i++ ) {
            var PageNumber=(i+1);
            if(data.ThisPageNumber==PageNumber){
                pagers+='<li class="active pager-button"><a onclick="'+event+'('+PageNumber+')">'+PageNumber+'</a></li>';
            }
            else{
                pagers+='<li class="pager-button"><a onclick="'+event+'('+PageNumber+')">'+PageNumber+'</a></li>';
            }
        }
        container.html(pagers);
    }

    $(document).ready(function(){
        getDraftEvents(1);
        getApprovedEvents(1);
        <?php if(\Phalcon\DI::getDefault()->get('auth')->getSessionActiveRole() != \Apprecie\Library\Users\UserRole::INTERNAL): ?>
        getApprovingEvents(1);
        getToDoEvents(1);
        <?php endif; ?>
        getArchivedEvents(1);
        getArrangedpEvents(1);
        var draftCategorySelect=$('#draft-category');
        var draftSubCategorySelect=$('#draft-subcategory');

        draftCategorySelect.change(function(){
            loader(true);
            draftSubCategorySelect.attr('disabled',true);
            if(draftCategorySelect.val()=='all'){
                draftSubCategorySelect.html('<option value="all">All</option>');
                draftSubCategorySelect.attr('disabled',false);
                loader(false);
                getDraftEvents();
            }
            else{
                $.ajax({
                    url: "/api/categoryPicker/"+draftCategorySelect.val(),
                    type: 'post',
                    dataType: 'json',
                    data : {'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN},
                    cache: true
                }).done(function(data){
                    var buffer='<option value="all">All</option>';
                    $(data).each(function(index, value){
                        buffer+='<option value="'+value.interestId+'">'+value.interest+'</option>';
                    });
                    draftSubCategorySelect.html(buffer);
                    draftSubCategorySelect.attr('disabled',false);
                    loader(false);
                    getDraftEvents();
                });
            }
        })
    })

function approveItem(itemId){
    $('#approval').modal('toggle');
}
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
</script>
<style>
    .item-details{
        display: none;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2>By Arrangement Events</h2>
    </div>
</div>
<a href="/itemcreation/create" class="btn btn-primary" style="margin-top: 15px; margin-bottom: 15px;">Create Content</a>

<div role="tabpanel" id="myTab" style="margin-bottom: 15px;">

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#active" aria-controls="profile" role="tab" data-toggle="tab">Active (<span id="activecount">0</span>)</a></li>
    <li role="presentation"><a href="#arrangedp" aria-controls="arrangedp" role="tab" data-toggle="tab">Arrangement Requests (<span id="arrangedpcount">0</span>)</a></li>
    <?php if(\Phalcon\DI::getDefault()->get('auth')->getSessionActiveRole() != \Apprecie\Library\Users\UserRole::INTERNAL): ?>
    <li role="presentation"><a href="#processing" aria-controls="profile" role="tab" data-toggle="tab">Processing (<span id="approvingcount">0</span>)</a></li>
    <?php endif; ?>
    <li role="presentation"><a href="#draft" aria-controls="profile" role="tab" data-toggle="tab">Draft (<span id="draftcount">0</span>) </a></li>
    <li role="presentation"><a href="#archived" aria-controls="messages" role="tab" data-toggle="tab">Archived (<span id="archivedcount">0</span>)</a></li>
    <?php if(\Phalcon\DI::getDefault()->get('auth')->getSessionActiveRole() != \Apprecie\Library\Users\UserRole::INTERNAL): ?>
    <li role="presentation"><a href="#todo" aria-controls="messages" role="tab" data-toggle="tab">Approval Requests (<span id="todocount">0</span>)</a></li>
    <?php endif; ?>
</ul>

<!-- Tab panes -->
<div class="tab-content" style="background-color: white; padding: 10px;border-left: 1px solid rgb(221, 221, 221);border-bottom: 1px solid rgb(221, 221, 221);border-right: 1px solid rgb(221, 221, 221);">
    <div role="tabpanel" class="tab-pane active" id="active">
        <table class="table table-hover" style="margin-top: 15px;">
            <thead>
            <tr>
                <th class="hidden-xs">Event Date</th>
                <th class="hidden-xs">Booking Ends</th>
                <th>Item</th>
                <th class="hidden-xs">Status</th>
                <th class="hidden-xs">Children</th>
                <th class="hidden-xs">Tier</th>
            </tr>
            </thead>
            <tbody id="approved-table">
            </tbody>
        </table>
        <nav>
            <ul class="pagination pagination-sm" id="active-pagination" style="margin: 0px;">
            </ul>
        </nav>
    </div>
    <div role="tabpanel" class="tab-pane" id="arrangedp">
        <table class="table table-hover" style="margin-top: 15px;">
            <thead>
            <tr>
                <th>Item</th>
                <th>Date</th>
                <th class="hidden-xs">Requester</th>
            </tr>
            </thead>
            <tbody id="arrangedp-table">
            </tbody>
        </table>
        <nav>
            <ul class="pagination pagination-sm" id="arrangedp-pagination" style="margin: 0px;">
            </ul>
        </nav>
    </div>
    <div role="tabpanel" class="tab-pane" id="processing">
        <table class="table table-hover" style="margin-top: 15px;">
            <thead>
            <tr>
                <th class="hidden-xs">Event Date</th>
                <th class="hidden-xs">Booking Ends</th>
                <th>Item</th>
                <th class="hidden-xs">Status</th>
                <th class="hidden-xs">Tier</th>
            </tr>
            </thead>
            <tbody id="approving-table">
            </tbody>
        </table>
        <nav>
            <ul class="pagination pagination-sm" id="approving-pagination" style="margin: 0px;">
            </ul>
        </nav>
    </div>
    <div role="tabpanel" class="tab-pane" id="draft">
    <table class="table table-hover" style="margin-top: 15px;">
        <thead>
        <tr>
            <th class="hidden-xs">Event Date</th>
            <th class="hidden-xs">Booking Ends</th>
            <th>Item</th>
            <th class="hidden-xs">Status</th>
            <th class="hidden-xs">Tier</th>
        </tr>
        </thead>
        <tbody id="items-table">
        </tbody>
    </table>
        <nav>
            <ul class="pagination pagination-sm" id="draft-pagination" style="margin: 0px;">
            </ul>
        </nav>
</div>
    <div role="tabpanel" class="tab-pane" id="todo">
        <table class="table table-hover" style="margin-top: 15px;">
            <thead>
            <tr>
                <th class="hidden-xs">Title</th>
                <th class="hidden-xs">Organisation</th>
                <th>Creator</th>
                <th class="hidden-xs">Status</th>
                <th class="hidden-xs">Tier</th>
            </tr>
            </thead>
            <tbody id="todo-table">
            </tbody>
        </table>
        <nav>
            <ul class="pagination pagination-sm" id="todo-pagination" style="margin: 0px;">
            </ul>
        </nav>
    </div>
<div role="tabpanel" class="tab-pane" id="archived">
    <table class="table table-hover" style="margin-top: 15px;">
        <thead>
        <tr>
            <th class="hidden-xs">Event Date</th>
            <th class="hidden-xs">Booking Ends</th>
            <th>Item</th>
            <th class="hidden-xs">Status</th>
            <th class="hidden-xs">Children</th>
            <th class="hidden-xs">Tier</th>
        </tr>
        </thead>
        <tbody id="archived-table">
        </tbody>
    </table>
    <nav>
        <ul class="pagination pagination-sm" id="archived-pagination" style="margin: 0px;">
        </ul>
    </nav>
</div>
</div>

</div>