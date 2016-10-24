<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["timeline"]});

    <?php if(count($this->view->activeItems)!=0): ?>
    $(document).ready(function(){
        var container = document.getElementById('timeline');
        var chart = new google.visualization.Timeline(container);
        var dataTable = new google.visualization.DataTable();

        dataTable.addColumn({ type: 'string', id: 'Event' });
        dataTable.addColumn({ type: 'string', id: 'ID' });
        dataTable.addColumn({ type: 'date', id: 'Start' });
        dataTable.addColumn({ type: 'date', id: 'End' });
        dataTable.addRows([
            <?php foreach($this->view->activeItems as $item): ?>
            [ '<?= $item->getTitle(); ?>','<?= $item->getEventId(); ?>',  new Date(<?= date('Y',strtotime($item->getStartDateTime())); ?>, <?= date('m',strtotime($item->getStartDateTime())); ?>, <?= date('d',strtotime($item->getStartDateTime())); ?>,<?= date('H',strtotime($item->getStartDateTime())); ?>,<?= date('i',strtotime($item->getStartDateTime())); ?>),  new Date(<?= date('Y',strtotime($item->getStartDateTime())); ?>, <?= date('m',strtotime($item->getStartDateTime())); ?>, <?= date('d',strtotime($item->getEndDateTime())); ?>,<?= date('H',strtotime($item->getEndDateTime())); ?>,<?= date('i',strtotime($item->getEndDateTime())); ?>) ],
            <?php endforeach; ?>
        ]);
        google.visualization.events.addListener(chart, 'select', selectHandler);

        var options = {
            timeline: { showBarLabels: false },
            tooltip: {trigger: false}
        };

        chart.draw(dataTable,options);

        function selectHandler() {
            var selectedItem = chart.getSelection()[0];
            if (selectedItem) {
                var value = dataTable.getValue(selectedItem.row, 1);
                window.location.href="/mycontent/eventmanagement/"+value;
            }

        }
    })
    <?php endif; ?>
</script>
<div class="ibox float-e-margins" style="position: relative;">
    <div class="ibox-title">
        <h5>Upcoming Events You Are Hosting (28 Days)</h5>
    </div>
    <div class="ibox-content">
        <div id="timeline" style="width: 100%; height: <?= (58*count($this->view->activeItems)); ?>px">
            <div class="alert alert-info" role="alert">You do not have any events that are running in the next 28 days</div>
        </div>
    </div>
</div>