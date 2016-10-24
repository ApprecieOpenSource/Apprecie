<?php $address=Address::findFirstBy('addressId',$this->view->event->getAddressId()); ?>
<script type="text/javascript"  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUzUvVTT07M1xZoDGIERc6xfl3x5Rljw"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>

<script>
var eventId = <?= $this->view->event->getEventId(); ?>;
var arrangementId = <?= $this->view->event->getByArrangementSource()->getEvent()->getEventId(); ?>;

$(document).ready(function(){
    var btn = $('#confirmArrangement');
    btn.click(function(){
        confirmModalFromApi('api', 'confirmarrangementpreview', {"eventId":eventId, }, 'confirmArrangement', 'confirmArranged()');
    });

    var btn = $('#rejectArrangementBut');

    btn.click(function(){
        var reason = $('#reject-reason').val();
        if(reason == '') {
            $('#reject-error').show();
        } else {
            $('#reject-error').hide();
            $('#reject').toggle();
            confirmModalFromApi('api', 'rejectarrangementpreview', {"eventId":eventId, "reason":reason, 'CSRF_SESSION_TOKEN':CSRF_SESSION_TOKEN}, 'rejectArrangementBut', 'rejectArranged()');
        }
    });
    google.maps.event.addDomListener(window, 'load', initialize);
})

function confirmArranged() {
    window.location = '/itemcreation/confirm/' + eventId;

    /*var btn=$('#confirmArrangement');
    btn.prop('disabled', true).html("Confirming...");

    $.when(ajaxPostAPI('api', 'confirmarrangement', {"eventId":eventId}).then(function(data){
        var issues = $('#issues');

        if(data.status == 'success') {
            btn.hide();
            $('#rejectArrangement').hide();
            $('#amendArrangement').hide();
            issues.hide();
        } else {
            btn.prop('disabled', false).html('Confirm Event');
            issues.show();
            issues.html(data.message);
        }
    }));*/
}


function rejectArranged() {
    var btn=$('#rejectArrangement');
    var reason = $('#reject-reason').val();
    btn.prop('disabled', true).html("Rejecting...");

    $.when(ajaxPostAPI('api', 'rejectarrangement', {"eventId":eventId, "reason":reason, 'CSRF_SESSION_TOKEN': CSRF_SESSION_TOKEN}).then(function(data){
        var issues = $('#issues');

        if(data.status == 'success') {
            btn.hide();
            $('#confirmArrangement').hide();
            $('#amendArrangement').hide();
            issues.hide();
            $('#reject').modal('hide');
            window.location.reload();
        } else {
            btn.prop('disabled', false).html('Reject Event');
            issues.show();
            issues.html(data.message);
            $('#reject').modal('hide');
        }
    }));
}

function initialize() {
    <?php if($address!=null && $address->getLongitude() != null): ?>
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'address': '<?= $address == null ? '' : $address->getPostalCode(); ?>'
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var myOptions = {
                    zoom: 12,
                    center: results[0].geometry.location,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            }
        });
    <?php endif; ?>
}
</script>

<style>
    .videoWrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 */
        padding-top: 25px;
        height: 0;
    }
    .videoWrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .row div {
        -webkit-transition: width 0.3s ease, margin 0.3s ease;
        -moz-transition: width 0.3s ease, margin 0.3s ease;
        -o-transition: width 0.3s ease, margin 0.3s ease;
        transition: width 0.3s ease, margin 0.3s ease;
    }
    .thumb{
        padding-left:2px;
        padding-right:2px;
    }
</style>
<style type="text/css">
    html, body, #map-canvas { height: 350px; margin: 0; padding: 0;}
</style>
<?php $requestor = User::resolve($this->view->event->getIsArrangedFor()); ?>
<div class="row">
    <div class="col-sm-12">
        <a href="<?= $this->view->backURL; ?>" class="btn btn-default" style="margin-top: 15px;"><?= $this->view->backTitle; ?></a>
        <a href="/vault/arranged/<?= $this->view->event->getSourceByArrangement(); ?>" target="_blank" class="btn btn-default" style="margin-top: 15px;">View Original Event</a>


        <?php if (! $requestor->getIsInteractive() &&  $this->view->event->getState() === \Apprecie\Library\Items\ItemState::ARRANGING): ?>
            <div class="alert alert-danger" style="margin-top: 15px; overflow:auto">
                <div class="pull-right">
                    <a id="rejectArrangement" class="btn btn-danger" onclick="rejectArranged()">Reject Event</a>
                </div>
                <strong>Sorry.  The user arranging this item is no longer active in the system</strong>
            </div>
        <?php elseif ($this->view->event->getState() === \Apprecie\Library\Items\ItemState::ARRANGING): ?>
            <div class="pull-right" style="margin-top: 15px;">
                <button id="confirmArrangement" class="btn btn-default">Confirm Event</button>
                <button id="rejectArrangement" class="btn btn-danger" data-toggle="modal" data-target="#reject">Reject Event</button>
                <a href="/itemcreation/arrange/{{event.getEventId()}}" class="btn btn-default" id="amendArrangement">Amend Event</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div id="issues" class="alert alert-danger pull-right" style="display: none;"></div>
        <h2>
            Arrangement Request
            <?php if ($this->view->event->getState() === \Apprecie\Library\Items\ItemState::APPROVED): ?>
                <span class="label label-success">Approved</span>
            <?php elseif ($this->view->event->getState() === \Apprecie\Library\Items\ItemState::DENIED && $this->view->event->getStatus() === \Apprecie\Library\Items\EventStatus::REJECTED): ?>
                <span class="label label-danger">Rejected</span>
            <?php elseif ($this->view->event->getState() === \Apprecie\Library\Items\ItemState::ARRANGING && $this->view->event->getEvent()->getStatus() === \Apprecie\Library\Items\EventStatus::PUBLISHED): ?>
                <span class="label label-default">Waiting for approval</span>
            <?php else: ?>
                <span class="label label-danger">Expired</span>
            <?php endif; ?>
        </h2>
        <p>
            Request by
            <?php
                if(! $requestor->getIsDeleted()) {
                    \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($requestor->getPortalId());
                    echo $requestor->getUserProfile()->getFullName();
                    \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
                } else {
                    echo "Inactive User";
                }
             ?>
            from <?= $requestor->getOrganisation()->getOrganisationName(); ?> on <?= date('d-m-Y H:i:s',strtotime($this->view->event->getDateCreated())); ?><br/>
            <?php $messageThread = '/alertcentre/view/' . $this->view->event->getArrangementMessageThread(); ?>
            <a target="_blank" href="<?= $messageThread; ?>">View arrangement messages >></a>
        </p>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2>
            <?= $this->view->event->getTitle(); ?>
        </h2>
    </div>
</div>
<div class="row" style="margin-top: 15px;">
    <div class="col-sm-6" id="col1">
        <?=(new ItemMediaWidget('index',array('itemId'=>$this->view->event->getItemId())))->getContent() ?>
    </div>
    <div class="col-sm-6" id="col2">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5 style="width:100%;">About</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12 details">
                        <?= $this->view->event->getSummary(); ?>
                    </div>
                    <div class="col-sm-12 details">
                        <?php if($address!=null): ?>
                        <h4>Suggested Event Location</h4>
                        <p>
                            <?php if($address->getLine1()!=null){ echo $address->getLine1().',<br/>';} ?>
                            <?php if($address->getLine2()!=null){ echo $address->getLine2().',<br/>';} ?>
                            <?php if($address->getLine3()!=null){ echo $address->getLine3().',<br/>';} ?>
                            <?php if($address->getCity()!=null){ echo $address->getCity().',<br/>';} ?>
                            <?php if($address->getPostalCode()!=null){ echo $address->getPostalCode().',<br/>';} ?>
                            <?php if($address->getCountryName()!=null){ echo $address->getCountryName();} ?>
                        </p>
                        <?php endif; ?>
                        <h4>Suggested Event Dates</h4>
                        <strong>Event Starts:</strong> <?= _hdt($this->view->event->getStartDateTime()); ?><br/>
                        <strong>Event Ends:</strong> <?= _hdt($this->view->event->getEndDateTime()); ?><br/><br/>

                        <h4>Pricing & Attendance</h4>
                        <p>Sold in packages of <?= $this->view->event->getPackageSize(); ?> unit(s) @ <?= $this->view->event->getUnitPrice(true, true); ?></p>
                        <p>
                            Requested packages: <?= $this->view->event->getMaxUnits(); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Additional Information</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-6 details2">
                        <h4><strong>Details</strong></h4>
                        <?= $this->view->event->getDescription(); ?>
                    </div>
                    <div class="col-sm-6 details">
                        <h4>Venue</h4>
                        <div id="map-canvas"></div>
                        <h4>What is Included</h4>
                        <p>
                            <?php
                            if($this->view->event->getBreakfast()==1){ echo 'Breakfast<br/>';}
                            if($this->view->event->getLunch()==1){ echo 'Lunch<br/>';}
                            if($this->view->event->getLightRefreshment()==1){ echo 'Light Refreshments<br/>';}
                            if($this->view->event->getAfternoonTea()==1){ echo 'Afternoon Tea<br/>';}
                            if($this->view->event->getDinner()==1){ echo 'Dinner<br/>';}
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Purchase Terms</h5>
            </div>
            <div class="ibox-content">
                <?php if($this->view->event->getPurchaseTerms()!=null): ?>
                    <?= $this->view->event->getPurchaseTerms(); ?>
                <?php else: ?>
                    There are no specific purchasing terms for this event.
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Attendance Terms</h5>
            </div>
            <div class="ibox-content">
                <?php if($this->view->event->getAttendanceTerms()!=null): ?>
                    <?= $this->view->event->getAttendanceTerms(); ?>
                <?php else: ?>
                    There are no specific attendance terms for this event.
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Reject Request</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="reject-error" role="alert" style="display: none;"><strong>Oops!</strong> You must provide a reason for rejecting this arrangement request.</div>
                <form method="post" enctype="multipart/form-data" id="reject-form" name="reject-form">
                    <p>Are you sure you want to reject this request?</p>
                    <p><strong>Reason:</strong></p>
                    <textarea id="reject-reason" name="rejection-reason" class="form-control" style="height:150px;"></textarea>
                    <input type="hidden" name="itemid" id="itemid" value="<?= $this->view->event->getItemId(); ?>"/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="rejectArrangementBut" class="btn btn-danger">Reject</button>
            </div>
        </div>
    </div>
</div>


