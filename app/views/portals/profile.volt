<script>
    $(document).ready(function(){

    })

    function ItemSearchShow(){
        $('#item-search').toggle('fast','linear');
    }
    function RelationshipSearchShow(){
        $('#relationship-search').toggle('fast','linear');
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
            <span style="margin-right: 15px;"><?= $this->view->portal->getPortalName(); ?> Portal Profile</span>
            <div class="pull-right"><?= $this->view->portal->getEdition(); ?></div>
        </h2>
        <p><a href="/portals/edit/<?= $this->view->portal->getPortalId(); ?>"><?= _g('Edit Portal'); ?> ></a></p>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('Portal Performance Snapshot'); ?></h5>
                    <span class="pull-right"><a href="#" style="cursor: pointer"><?= _g('More Reports'); ?> ></a></span>
                </div>
                <div class="ibox-content">
                    <div id="chart_div" style="width: 100%; height: 300px;"></div>
                </div>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= _g('People'); ?></h5>
                    <span class="pull-right"><a onclick="RelationshipSearchShow()" style="cursor: pointer"><?= _g('Search'); ?> ></a></span>
                </div>
                <div class="ibox-content">
                    <div id="relationship-search">
                        <form method="post" enctype="multipart/form-data" action="#" class="form-horizontal">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label for="date-range" class="control-label"><?= _g('Creation Date'); ?></label>
                                    <input type="text" class="form-control" id="date-range" name="date-range">
                                </div>
                                <div class="col-sm-4">
                                    <label for="name" class="control-label"><?= _g('Name'); ?></label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                                <div class="col-sm-4">
                                    <label for="relationship" class="control-label"><?= _g('Role'); ?></label>
                                    <select class="form-control" id="relationship" name="relationship">
                                        <option>Any</option>
                                        <option>Organisation Owner</option>
                                        <option>Manager</option>
                                        <option>Apprecie Supplier</option>
                                        <option>Affiliated Supplier</option>
                                        <option>Client Member</option>
                                        <option>Family Member</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" style="margin-top: 25px;"><?= _g('Search'); ?></button>
                        </form>
                    </div>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><?= _g('Name'); ?></th>
                            <th><?= _g('Role'); ?></th>
                            <th class="hidden-xs"><?= _g('Creation Date'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="3"><?= _g('There are no users in this Portal'); ?></td>
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
                                <td colspan="5"><?= _g('There are no items associated with this Portal'); ?></td>
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
                        <h5><?= _g('Portal Details'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <p><strong><?= _g('Creation Date'); ?></strong><br/> <?= date('d-m-Y H:i:s',strtotime($this->view->portal->getCreatedDate())); ?></p>
                        <p><strong><?= _g('Account Manager'); ?></strong><br/> <a href="#"><?= $this->view->accountManagerProfile->getFirstName().' '.$this->view->accountManagerProfile->getLastName(); ?></a></p>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Primary Contact'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <?php
                        foreach($this->view->portalContacts as $contact):
                            $address=$contact->getAddress();
                            ?>
                            <p><?=$contact->getContactNameAndTitle(); ?></p>
                            <p>
                                <?php
                                if($address!=null){
                                    if($address->getLine1()!=null){echo $address->getLine1().',<br/>';}
                                    if($address->getLine2()!=null){echo $address->getLine2().',<br/>';}
                                    if($address->getLine3()!=null){echo $address->getLine3().',<br/>';}
                                    if($address->getCity()!=null){echo $address->getCity().',<br/>';}
                                    if($address->getPostalCode()!=null){echo $address->getPostalCode();}
                                }
                                ?>
                            </p>

                            <p><?= _g('Tel'); ?>: <?php if($contact->getTelephone()!=null){echo $contact->getTelephone();}else{echo _g('Not provided');}; ?><br/>
                                <?= _g('Mobile'); ?>: <?php if($contact->getMobile()!=null){echo $contact->getMobile();}else{echo _g('Not provided');}; ?><br/>
                                <?= _g('Email'); ?>: <?php if($contact->getEmail()!=null){echo $contact->getEmail();}else{echo _g('Not provided');}; ?></p>
                        <?php endforeach ?>

                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?= _g('Organisations'); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th><?= _g('Name'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($this->view->organisations as $organisation):?>
                                <tr>
                                    <td><a href="/adminorgs/view/<?= $organisation->getOrganisationId(); ?>"><?= $organisation->getOrganisationName(); ?></a></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if(count($this->view->organisations)==0): ?>
                            <tr>
                                <td colspan="3"><?= _g('There are no organisations in this Portal'); ?></td>
                            </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>