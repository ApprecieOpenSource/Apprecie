<style>
    .loading{
        opacity: 0.2;
    }

    .pager-button{
        cursor: pointer;
    }

    .Online{
        color: green;
    }

    .Offline{
        color: red;
    }
</style>
<script>
    $(document).ready(function(){
        SearchPortals();

        $("#portal-search").submit(function(e){
            SearchPortals();
            return false;
        });
    })
    function getPortalsAjax(){
        return $.ajax({
            url: "/portals/AjaxSearchPortals",
            type: 'post',
            dataType: 'json',
            cache: false,
            data:$('#portal-search').serialize()
        });
    }

    function SearchPortals(){
        var tableContainer=$('#portal-list-body');
        var table=$('#portal-list-table');

        loader(true);
        table.addClass('loading');

        $.when(getPortalsAjax()).then(function(data){
            tableContainer.empty();
            if(data.items==null){
                var row='<tr><td colspan="4">There were no matching Portals</td></tr>';
                tableContainer.append(row);
            }
            $(data.items).each(function(index, value){
                var status='Online';
                if(value.suspended==1){status='Offline';}
                var row=
                    '<tr><td><a href="/portals/profile/'+value.portalId+'">'+value.portalName+'</a></td>'+
                    '<td class="hidden-xs"><a target="_blank" href="https://'+value.portalSubdomain+'.<?=$this->view->domains['system']?>">'+value.portalSubdomain+'.<?=$this->view->domains['system']?></a></td>'+
                    '<td>'+value.edition+'</td>'+
                    '<td class="'+status+'">'+status+'</td></tr>';
                tableContainer.append(row);

            });
            LoadPagination(data);
            loader(false);
            table.removeClass('loading');
        })
    }

    function goToPage(pageNumber){
        $('#pageNumber').val(pageNumber);
        SearchPortals();
    }

    function LoadPagination(data){
        var pagers='';
        for ( var i = 0; i < data.PageCount; i++ ) {
            var PageNumber=(i+1);
            if(data.ThisPageNumber==PageNumber){
                pagers+='<li class="active pager-button"><a onclick="goToPage('+PageNumber+')">'+PageNumber+'</a></li>';
            }
            else{
                pagers+='<li class="pager-button"><a onclick="goToPage('+PageNumber+')">'+PageNumber+'</a></li>';
            }
        }
        $('#portal-search-pagination').html(pagers);
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('All Portals'); ?></h2>
    </div>
</div>
<a href="/portals/create" class="btn btn-primary" style="margin-bottom: 15px;"><?= _g('New Portal'); ?></a>
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Search'); ?></h5>
            </div>
            <div class="ibox-content">
                <form method="post" enctype="multipart/form-data" id="portal-search" name="portal-search" class="form-horizontal">
                    {{csrf()}}
                    <input type="hidden" id="pageNumber" name="pageNumber" value="1"/>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="portalName" class="control-label"><?= _g('Portal Name'); ?></label>
                                <input type="text" class="form-control" id="portalName" name="portalName">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="tag" class="control-label"><?= _g('Edition'); ?></label>
                                <select class="form-control" id="edition" name="edition">
                                    <option value="Any"><?= _g('Any'); ?></option>
                                    <option value="FreemiumPro"><?= _g('Freemium Pro'); ?></option>
                                    <option value="Professional"><?= _g('Professional'); ?></option>
                                    <option value="Enterprise"><?= _g('Enterprise'); ?></option>
                                    <option value="VIP"><?= _g('VIP'); ?></option>
                                    <option value="Supplier"><?= _g('Supplier'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="status" class="control-label"><?= _g('Status'); ?></label>
                                <select class="form-control" id="suspended" name="suspended">
                                    <option value="Any"><?= _g('Any'); ?></option>
                                    <option value="0" selected><?= _g('Online'); ?></option>
                                    <option value="1"><?= _g('Offline'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="button" onclick="SearchPortals()" class="btn btn-primary" value="<?= _g('Search'); ?>"/>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-content">
                <table class="table table-hover" id="portal-list-table">
                    <thead>
                    <tr>
                        <th><?= _g('Name'); ?></th>
                        <th class="hidden-xs"><?= _g('Domain'); ?></th>
                        <th><?= _g('Edition'); ?></th>
                        <th><?= _g('Status'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="portal-list-body">

                    </tbody>
                </table>
                <nav>
                    <ul class="pagination" id="portal-search-pagination">

                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
