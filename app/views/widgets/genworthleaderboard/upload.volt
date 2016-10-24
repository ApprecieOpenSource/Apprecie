<script>
    $(document).ready(function(){
        getStats();
        $('#upload').change(function(){
            loader(true);
            $('#upload-form').submit();
        })

        $('#upload-iframe').load(function(){
            $('#upload-iframe').ready(function(){
                $('#upload-error').fadeOut();
                $('#upload-success').fadeOut();
                var result= $.parseJSON($('#upload-iframe').contents().text());
                if(result.status=='success'){
                    $('#upload-success').fadeIn();
                }
                else{
                    $('#upload-error').html(result.message);
                    $('#upload-error').fadeIn();
                }
                loader(false);
            })
        });
    });
    function AjaxGetStats(){
        return $.ajax({
            url: "/api/getGenstats",
            type: 'post',
            dataType: 'json',
            cache: false
        });
    }
    function getStats(){
        $.when(AjaxGetStats()).then(function(data){
            var loop=1;
            $.each(data,function(key, value){
                var buffer='<tr><td>'+value.user.firstname+' '+value.user.lastname+'</td><td>'+value.kpi+'</td><td>'+value.sales+'</td><td>'+(key+1)+'</td></tr>';
                $('#leaderboard-stats').append(buffer);
            })
        })
    }
</script>
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5>Performance Leaderboard</h5>
            </div>
            <div class="ibox-content">
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
                <h5>Performance Statistics Upload</h5>
            </div>
            <div class="ibox-content">
                <a href="/api/genworthCsv" class="btn btn-default" style="margin-bottom: 15px;">Download Template</a>
                <p><strong>Please select the CSV you would like to upload</strong></p>
                <p>Note that only one set of statistics can be added per week, if you try to add more it will overwrite the existing statistics for this week.</p>
                <div class="alert alert-success" id="upload-success" style="display: none;" role="alert">Your statistics have been applied</div>
                <div class="alert alert-danger" id="upload-error" style="display: none;" role="alert"></div>
                <form method="post" enctype="multipart/form-data" action="/api/genstats" id="upload-form" name="upload-form" target="upload-iframe">
                    <input type="file" id="upload" name="upload"/>
                    <iframe id="upload-iframe" name="upload-iframe" style="width: 100%; display: none;"></iframe>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <img src="/img/ph1.png" class="img-responsive"/>
    </div>
    <div class="col-sm-6">
        <img src="/img/ph2.png" class="img-responsive"/>
    </div>;
</div>

