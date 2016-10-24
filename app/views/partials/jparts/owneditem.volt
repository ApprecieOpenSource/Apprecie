<script id="owneditem" type="text/x-jsrender">
<|for items|>
<div class="col-sm-6" style="height: 340px;">
<|for item|>
    <div class="ibox float-e-margins highlight-ibox-title">
<|/for|>
        <div class="ibox-title" style="height: auto;">
            <div style="width: 80%;">
                <h5>
                    <|:item.title|>
                </h5>
            </div>
            <div style="width: 20%;" class="pull-right">
                <span class="pull-right"><a style="text-decoration:none; cursor:pointer;" onclick="manageItem(<|:item.itemId|>)">Manage <i class="fa fa-cog"></i></a></span>
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
                            <|if reserved>0|>
                                <tr><td colspan="2"><span style="color:red">You have <|:reserved|> unpaid reservations</span></td></tr>
                            <|/if|>
                            <tr><td>Event Date:</td><td><|:event.startDateTime|></td></tr>
                            <tr><td>Spaces Available:</td><td><|:guests.available|></td></tr>
                            <tr><td>People Invited:</td><td><|:guests.invited|></td></tr>
                            <tr><td>People Attending:</td><td><|:guests.confirmed|></td></tr>
                            <|if canEditGuestList==1|>
                                <tr><td colspan="2"><span style="color:green"><|:message|></span></td></tr>
                            <|else|>
                                <tr><td colspan="2"><span style="color:red"><|:message|></span></td></tr>
                            <|/if|>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<|/for|>
</script>