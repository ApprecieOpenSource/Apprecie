<script>
    $(document).ready(function(){
        LoadOrganisationUsers();
    })

    function ItemSearchShow(){
        $('#item-search').toggle('fast','linear');
    }

    function LoadOrganisationUsers(){
        var tableContainer=$('#user-list-body');
        var table=$('#user-list-table');
        $.when(getOrganisationUsersFull()).then(function(data){
            tableContainer.empty();
            if(data.items==null){
                var row='<tr><td colspan="4">There were no matching People</td></tr>';
                tableContainer.append(row);
            }
            $(data.items).each(function(index, value){
                var status='Online';
                if(value.suspended==1){status='Offline';}
                var row=
                    '<tr><td><a href="/adminusers/viewuser/'+value.userid+'">'+value.firstname+' '+value.lastname+'</a></td>'+
                        '<td>'+value.role+'</td>'+
                        '<td>'+value.email+'</td></tr>';
                tableContainer.append(row);

            });
            LoadUsersPagination(data);
            loader(false);
            table.removeClass('loading');
        });
    }
    function getOrganisationUsersFull(){
        return $.ajax({
            url: "/api/getOrganisationUsersFull/<?= $this->view->organisation->getOrganisationId(); ?>",
            type: 'post',
            dataType: 'json',
            cache: false,
            data : {'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}
        });
    }

    function LoadUsersPagination(data){
        var pagers='';
        for ( var i = 0; i < data.PageCount; i++ ) {
            var PageNumber=(i+1);
            if(data.ThisPageNumber==PageNumber){
                pagers+='<li class="active pager-button"><a onclick="goToUsersPage('+PageNumber+')">'+PageNumber+'</a></li>';
            }
            else{
                pagers+='<li class="pager-button"><a onclick="goToUsersPage('+PageNumber+')">'+PageNumber+'</a></li>';
            }
        }
        $('#users-search-pagination').html(pagers);
    }

    function goToUsersPage(pageNumber){
        $('#pageNumber').val(pageNumber);
        LoadOrganisationUsers();
    }
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([

            ['Month', 'Sales (£)'],
            ['January',  0],
            ['February',  0],
            ['March',  0],
            ['April',  0],
            ['May',  0],
            ['June',  0],
            ['July',  0],
            ['August',  0],
            ['September',  0],
            ['October',  0],
            ['November',  0],
            ['December',  0]
        ]);

        var options = {
            curveType: 'function',
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

        chart.draw(data, options);
    }
    $(window).resize(function(){
        drawChart();
    });

</script>
<style>
    #item-search, #relationship-search{
        display: none;
        padding-bottom: 15px;
        border-bottom: 2px solid #ddd;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2>
            <span style="margin-right: 15px;"><?=$this->view->primaryOrganisation->getOrganisationName(); ?><?php if($this->view->organisation->getOrganisationName()!=$this->view->primaryOrganisation->getOrganisationName()){echo ' - '.$this->view->organisation->getOrganisationName();}  ?> Organisation Profile</span>
        </h2>
    </div>
</div>
<div class="row">
<div class="col-sm-12">
<div class="row">
<div class="col-sm-8">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5><?= _g('Organisation Performance Snapshot'); ?></h5>
            <span class="pull-right"><a href="#" style="cursor: pointer"><?= _g('More Reports'); ?> ></a></span>
        </div>
        <div class="ibox-content">
            <div id="chart_div" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5><?= _g('People'); ?></h5>
        </div>
        <div class="ibox-content">
            <table class="table table-hover" id="user-list-table">
                <thead>
                <tr>
                    <th><?= _g('Name'); ?></th>
                    <th><?= _g('Role'); ?></th>
                    <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                </tr>
                </thead>
                <tbody id="user-list-body">

                </tbody>
            </table>
            <nav>
                <nav>
                    <ul class="pagination" id="users-search-pagination">

                    </ul>
                </nav>
                <span class="pull-right">
                    <a style="cursor: pointer"><i class="fa fa-file-excel-o"></i> Export</a>
                </span>
            </nav>
        </div>
    </div>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5><?= _g('Item History'); ?></h5>
            <span class="pull-right"><a onclick="ItemSearchShow()" style="cursor: pointer"><?= _g('Search'); ?> ></a></span>
        </div>
        <div class="ibox-content">
            <div id="item-search">
                <form method="post" enctype="multipart/form-data" action="#" class="form-horizontal">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="date-range" class="control-label"><?= _g('Date Range'); ?></label>
                            <input type="text" class="form-control" id="date-range" name="date-range">
                        </div>
                        <div class="col-sm-8">
                            <label for="item-name" class="control-label"><?= _g('Item Name'); ?></label>
                            <input type="text" class="form-control" id="item-name" name="item-name">
                        </div>
                        <div class="col-sm-4">
                            <label for="supplier" class="control-label"><?= _g('Supplier'); ?></label>
                            <select class="form-control" id="supplier" name="supplier">
                                <option>Any</option>
                                <option>Bentley</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="status" class="control-label"><?= _g('Consumer'); ?></label>
                            <select class="form-control" id="status" name="status">
                                <option>Any</option>
                                <option>Barclays</option>
                                <option>Canacord</option>
                                <option>UBS</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="type" class="control-label"><?= _g('Type'); ?></label>
                            <select class="form-control" id="type" name="type">
                                <option>Any</option>
                                <option>Event</option>
                                <option>Offer</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-primary" style="margin-top: 25px;"><?= _g('Search'); ?></button>
                </form>
            </div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th><?= _g('Date'); ?></th>
                    <th><?= _g('Item'); ?></th>
                    <th class="hidden-xs"><?= _g('Type'); ?></th>
                    <th class="hidden-xs"><?= _g('Supplier'); ?></th>
                    <th><?= _g('Consumer'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5"><?= _g('There are no items associated with this Organisation'); ?></td>
                </tr>
                </tbody>
            </table>
            <nav>
                <ul class="pagination pagination-sm" style="margin: 0px;">
                    <li class="active"><a href="#">1</a></li>
                </ul>
                            <span class="pull-right">
                                <a style="cursor: pointer"><i class="fa fa-file-excel-o"></i> Export</a>
                            </span>
            </nav>
        </div>
    </div>
</div>
<div class="col-sm-4">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5><?= _g('Organisation Details'); ?></h5>
        </div>
        <div class="ibox-content">
            <p><strong>Portal</strong><br/><a href="/portals/profile/<?= $this->view->portal->getPortalId(); ?>"><?= $this->view->portal->getPortalName(); ?></a></p>
            <p><strong><?= _g('URL'); ?></strong><br/>
                <?php
                if($this->view->primaryOrganisation->getOrganisationId()==$this->view->organisation->getOrganisationId()){
                    $url="https://".$this->view->primaryOrganisation->getSubdomain().".".$this->view->domain;
                }
                else{
                    $url="https://".$this->view->primaryOrganisation->getSubdomain().".".$this->view->domain;
                }
                ?>
            <a href="<?= $url;?>" target="_blank"><?= $url;?></a>
            </p>
            <p><strong>Parent Organisation</strong><br/>
                <?php
                if(isset($this->view->parentOrganisation)){
                    echo '<a href="/adminorgs/view/'.$this->view->parentOrganisation->getOrganisationId().'">'.$this->view->parentOrganisation->getOrganisationName().'</a>';
                }
                else{
                    echo _g('None');
                }
                ?></p>
            <p><strong>Direct Child Organisations</strong><br/>
                <?php
                if(count($this->view->childOrganisations)>0){
                    foreach($this->view->childOrganisations as $organisation){
                        echo '<a href="/adminorgs/view/'.$organisation->getOrganisationId().'">'.$organisation->getOrganisationName().'</a><br/>';
                    }

                }
                else{
                    echo _g('None');
                }
                ?></p>
        </div>
    </div>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5><?= _g('Quotas'); ?></h5>
        </div>
        <div class="ibox-content">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th><?= _g('Role'); ?></th>
                    <th><?= _g('Used'); ?></th>
                    <th><?= _g('Available'); ?></th>
                </tr>
                </thead>
                <tbody id="quota-table">
                <tr>
                    <td><?= _g('Organisation Owners'); ?></td>
                    <td><?=$this->view->quotas->getPortalAdministratorUsed(); ?></td>
                    <td><?=($this->view->quotas->getPortalAdministratorTotal()-$this->view->quotas->getPortalAdministratorUsed()); ?></td>
                </tr>
                <tr>
                    <td><?= _g('Managers'); ?></td>
                    <td><?=$this->view->quotas->getManagerUsed(); ?></td>
                    <td><?=($this->view->quotas->getManagerTotal()-$this->view->quotas->getManagerUsed()); ?></td>
                </tr>
                <tr>
                    <td><?= _g('Internal Members'); ?></td>
                    <td><?=$this->view->quotas->getInternalMemberUsed(); ?></td>
                    <td><?=($this->view->quotas->getInternalMemberTotal()-$this->view->quotas->getInternalMemberUsed()); ?></td>
                </tr>
                <tr>
                    <td><?= _g('Apprecie Suppliers'); ?></td>
                    <td><?=$this->view->quotas->getApprecieSupplierUsed(); ?></td>
                    <td><?=($this->view->quotas->getApprecieSupplierTotal()-$this->view->quotas->getApprecieSupplierUsed()); ?></td>
                </tr>
                <tr>
                    <td><?= _g('Affiliated Suppliers'); ?></td>
                    <td><?=$this->view->quotas->getAffiliateSupplierUsed(); ?></td>
                    <td><?=($this->view->quotas->getAffiliateSupplierTotal()-$this->view->quotas->getAffiliateSupplierUsed()); ?></td>
                </tr>
                <tr>
                    <td><?= _g('Client Members'); ?></td>
                    <td><?=$this->view->quotas->getMemberUsed(); ?></td>
                    <td><?=($this->view->quotas->getMemberTotal()-$this->view->quotas->getMemberUsed()); ?></td>
                </tr>
                </tbody>
            </table>
            <p>
                <strong><?= _g('Family Members Per Client'); ?>:</strong>  <?=$this->view->quotas->getMemberFamilyTotal(); ?>
            </p>
            <p>
                <strong><?= _g('Commission Rate'); ?>:</strong>  <?=$this->view->quotas->getCommissionPercent(); ?>
            </p>
        </div>
    </div>
</div>
</div>
</div>
</div>