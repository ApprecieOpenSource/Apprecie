<script type="text/javascript"
        src="https://www.google.com/jsapi?autoload={
            'modules':[{
              'name':'visualization',
              'version':'1',
              'packages':['corechart']
            }]
          }"></script>

<script type="text/javascript">

    google.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Date', 'Active Users', 'Available Units'],
            <?php foreach($this->view->data as $record): ?>
                ['<?= $record->getDate(); ?>',  <?= $record->getActive(); ?>,      <?= $record->getSupply(); ?>],
            <?php endforeach; ?>
        ]);

        var options = {
            title: 'Active Users vs Event Supply',
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
    }
</script>
<div id="curve_chart" style="width: 100%; height: 500px; margin-bottom: 15px;"></div>