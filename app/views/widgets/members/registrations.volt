<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Date', 'Registrations']
            <?php foreach($this->view->data as $data):
                echo ",['".$data['date']."',  ".$data['count']."]";
            endforeach ?>
        ]);

        var options = {
            title: 'Company Performance'
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));

        chart.draw(data, options);
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= _g('User Growth - Last 7 Days'); ?></h5>
            </div>
            <div class="ibox-content">
                <div id="chart_div" style="width: 100%; height: 350px;"></div>
            </div>
        </div>
    </div>
</div>