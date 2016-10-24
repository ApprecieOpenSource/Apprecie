<script id="attendingitem" type="text/x-jsrender">
<|for items|>
<div class="col-sm-6" style="height: 250px;">
    <div class="ibox float-e-margins highlight-ibox-title">
        <div class="ibox-title" style="height: auto;">
            <div style="width: 80%;">
                <h5>
                    <|:title|>
                </h5>
            </div>
            <div style="width: 20%;" class="pull-right">
                 <span class="pull-right"><a href="/rsvp/viewevent/<|:invitationHash|>" target="_blank">View Event</a></span>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-sm-5">
                    <img src="<|:image|>" class="img-responsive">
                </div>
                <div class="col-sm-7">
                    <table class="table table-striped">
                        <tbody>
                        <tr><td>Start Date:</td><td><|:start|></td></tr>
                        <tr><td>End Date:</td><td><|:end|></td></tr>
                        <tr><td>Guests:</td><td><|:additionalGuests|></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<|/for|>
</script>