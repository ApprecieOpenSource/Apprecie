<script>
    var userId=<?= $this->view->userId; ?>;
    $(document).ready(function(){
        getStats();
        getMyStats();
    })
    function AjaxGetStats(){
        return $.ajax({
            url: "/api/getGenstats",
            type: 'post',
            dataType: 'json',
            cache: false
        });
    }

    function AjaxGetMyStats(){
        return $.ajax({
            url: "/api/getMyGenstats",
            type: 'post',
            dataType: 'json',
            cache: false
        });
    }

    function getStats(){
        $.when(AjaxGetStats()).then(function(data){
            if(data.length!=0){
                $.each(data,function(key, value){
                    var buffer='';
                    if(value.userId==userId){
                        buffer+='<tr style="background-color:#d9edf7;"><td>'+value.user.firstname+' '+value.user.lastname+'</td><td>'+value.kpi+'</td><td>'+value.sales+'</td><td>'+(key+1)+'</td></tr>';
                    }
                    else{
                        buffer+='<tr><td>'+value.user.firstname+' '+value.user.lastname+'</td><td>'+value.kpi+'</td><td>'+value.sales+'</td><td>'+(key+1)+'</td></tr>';
                    }
                    $('#leaderboard-stats').append(buffer);
                })
            }
            else{
                $('#leaderboard-stats').append('<tr><td colspan="4"><div class="alert alert-info" style="margin: 10px;" id="upload-error" role="alert">There are currently no statistics available</div></td></tr>');
            }
        })
    }
    function getMyStats(){
        $.when(AjaxGetMyStats()).then(function(data){
            if(data.length!=0){
                var chartData=[['Week', 'Sales']];
                $.each(data,function(key, value){
                    chartData.push([value.weekNumber,parseInt(value.sales)]);
                })
                drawChart(chartData);
            }
        })
    }
</script>
<script type="text/javascript"
        src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>
<script type="text/javascript">
    function drawChart(chartData) {
        var data = google.visualization.arrayToDataTable(chartData);

        var options = {
            curveType: 'function',
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
    }
</script>
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5>Performance Leaderboard</h5>
            </div>
            <div class="ibox-content no-padding">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th><?= _g('Name'); ?></th>
                        <th><?= _g('KPI %'); ?></th>
                        <th><?= _g('Agg. Sales'); ?></th>
                        <th><?= _g('Position'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="leaderboard-stats">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5>Your Performance </h5>
            </div>
            <div class="ibox-content">
                <div id="curve_chart" style="width: 100%; height: 300px"> <div class="alert alert-info"  id="upload-error" role="alert">No performance data available</div></div>
            </div>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        {{ widget('VaultFeedWidget','genworth') }}
    </div>
    <div class="col-sm-6">
        <img src="/img/ph2.png" class="img-responsive"/>
    </div>
</div>

