<script src="/js/compiled/public/js/raw/library/roi.min.js"></script>
<?php $this->partial("partials/jparts/roiByArrangement"); ?>
<script>
    var report=null;
    $(document).ready(function(){
        var picker = new Pikaday(
            {
                field: document.getElementById('date'),
                firstDay: 1,
                format: 'DD/MM/YYYY',
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });
        var picker2 = new Pikaday(
            {
                field: document.getElementById('date2'),
                firstDay: 1,
                format: 'DD/MM/YYYY',
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });
        report=new RoiMyEventsReport('AjaxMyByArrangements');
    })

    function fetchReport(){
        var template = $.templates("#roiByArrangement");
        var requestData=$('#roi-form').serialize();
        $('#error-box').stop().hide();

        var error=null;

        var sdate=new moment($('#date').val(),'DD/MM/YYYY HH:mm');
        var edate=new moment($('#date2').val(),'DD/MM/YYYY HH:mm');
        if(edate.isValid() && sdate.isValid()){
            if(edate.isBefore(sdate,'day')){
                error=('End date cannot be before the start date');
            }
            if(sdate.isAfter(edate,'day')){
                error=('Start date cannot be after the end date');
            }
        }
        else{
            error=('Invalid date range');
        }
        if(error==null){
            $('#generate-btn').prop('disabled',true);
            $('#report-tbl').hide();
            report.setDate(requestData);
            $.when(report.fetch()).then(function(data){
                report.setResultSet(data);
                $('#generate-btn').prop('disabled',false);
                $("#report-body").html(template.render(report.getResultSet()));
                $('#report-tbl').show();
            })
        }
        else{
            $('#error-box').html(error);
            $('#error-box').stop().fadeIn('fast');
        }
    }

    function orderReport(order,orderBy){
        var template = $.templates("#roiByArrangement");
        var resultSet=report.getResultSet();

        var orderedResult=resultSet.records.sort(function(a,b) {
            if(isNaN(a[orderBy])){
                var aOrder = a[orderBy].toLowerCase();
                var bOrder = b[orderBy].toLowerCase();
            }
            else{
                aOrder=a[orderBy];
                bOrder=b[orderBy];
            }
            if(order=='asc'){
                return ((aOrder < bOrder) ? -1 : ((aOrder > bOrder) ? 1 : 0));
            }
            else{
                return ((aOrder > bOrder) ? -1 : ((aOrder < bOrder) ? 1 : 0));
            }
        } );

        resultSet.records=orderedResult;
        report.setResultSet(resultSet);

        $("#report-body").html(template.render(report.getResultSet()));
    }
</script>
<style>
    .table-order-btn{
        padding:5px 5px 5px 0px; cursor: pointer;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('By Arrangement ROI Reporting'); ?></h2>
    </div>
</div>
<form class="form-horizontal" id="roi-form">
    {{csrf()}}
<div class="row">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Options</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label">From Date</label>
                            <div class="col-sm-8">
                                <input type="text" id="date" name="date" class="form-control" value="<?= date('d/m/Y',strtotime('now -7 days')) ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label">To Date</label>
                            <div class="col-sm-8">
                                <input type="text" id="date2" name="date2" class="form-control" value="<?= date('d/m/Y',strtotime('now')) ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <a onclick="fetchReport();" id="generate-btn" class="btn btn-default">Generate</a>
            </div>
        </div>
    </div>
</div>
</form>
<div class="alert alert-danger" id="error-box" role="alert" style="display:none;"></div>
<div class="row" id="report-tbl" style="display: none;">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Report</h5>
            </div>
            <div class="ibox-content">
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="exportMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Export...
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportMenu">
                        <li>
                            <a href="/roi/export/csv">CSV</a>
                        </li>
                        <li>
                            <a href="/roi/export/excel">Excel (.xlsx)</a>
                        </li>
                    </ul>
                </div>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="roi-column" id="name-column">
                            <span class="table-order-btn" style="" onclick="orderReport('desc','title')"><i class="fa fa-caret-down"></i></span>
                            <span class="table-order-btn" onclick="orderReport('asc','title')"><i class="fa fa-caret-up"></i></span>
                            Name
                        </th>
                        <th class="roi-column" id="start-date-column">
                            <span class="table-order-btn" onclick="orderReport('desc','bookingStartDateStamp')"><i class="fa fa-caret-down"></i></span>
                            <span class="table-order-btn" onclick="orderReport('asc','bookingStartDateStamp')"><i class="fa fa-caret-up"></i></span>
                            Booking Start Date
                        </th>
                        <th class="roi-column">
                            <span class="table-order-btn" onclick="orderReport('desc','tier')"><i class="fa fa-caret-down"></i></span>
                            <span class="table-order-btn" onclick="orderReport('asc','tier')"><i class="fa fa-caret-up"></i></span>
                            Tier
                        </th>
                        <th class="roi-column">
                            <span class="table-order-btn" onclick="orderReport('desc','linkedEvents')"><i class="fa fa-caret-down"></i></span>
                            <span class="table-order-btn" onclick="orderReport('asc','linkedEvents')"><i class="fa fa-caret-up"></i></span>
                            Linked Events
                        </th>
                        <th class="roi-column">
                            <span class="table-order-btn" onclick="orderReport('desc','revenue')"><i class="fa fa-caret-down"></i></span>
                            <span class="table-order-btn" onclick="orderReport('asc','revenue')"><i class="fa fa-caret-up"></i></span>
                            Revenue
                        </th>
                        <th class="roi-column">
                            <span class="table-order-btn" onclick="orderReport('desc','guests')"><i class="fa fa-caret-down"></i></span>
                            <span class="table-order-btn" onclick="orderReport('asc','guests')"><i class="fa fa-caret-up"></i></span>
                            Attendance
                        </th>
                    </tr>
                    </thead>
                    <tbody id="report-body">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>