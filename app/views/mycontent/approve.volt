<?php $address = Address::findFirstBy('addressId',$this->view->event->getAddressId()); ?>
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
<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUzUvVTT07M1xZoDGIERc6xfl3x5Rljw">
</script>
<script>
    $(document).ready(function(){
        $('#enlarge').click(function(){
            $('#col1').toggleClass('col-sm-6 col-sm-12');
            $('#col2').toggleClass('col-sm-6 col-sm-12');
            $('.thumb').toggleClass('col-sm-3 col-sm-2');
        })
    });

    function initialize() {
        <?php if($address != null): ?>
        geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'address': '<?= $address == null ? '' : $address->getPostalCode(); ?>'
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var myOptions = {
                    zoom: 12,
                    center: results[0].geometry.location,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            }
        });
        <?php endif; ?>
    }
    google.maps.event.addDomListener(window, 'load', initialize);

    function approveItem(){
        $.when(sendApproval()).then(function(){
            window.location.replace('<?='https://'.$this->view->portal->getPortalSubdomain().'.'.$this->view->domains['system'].'/mycontent/events/'; ?>');
        })
    }

    function sendApproval(){
        return $.ajax({
            url: "/mycontent/approveItem/<?= $this->view->event->getItemId(); ?>",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: {'CSRF_SESSION_TOKEN' : CSRF_SESSION_TOKEN}
        });
    }

    function rejectItem(){
        if($('#rejection-reason').val()!=''){
            $.when(sendRejection()).then(function(){
                window.location.replace('<?='https://'.$this->view->portal->getPortalSubdomain().'.'.$this->view->domains['system'].'/mycontent/events/'; ?>');
            })
        }
        else{
            $('#reject-error').stop().fadeOut().fadeIn('fast');
        }
    }

    function sendRejection(){
        return $.ajax({
            url: "/mycontent/rejectItem",
            type: 'post',
            dataType: 'json',
            cache: false,
            data: $('#reject-form').serialize()
        });
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <a href="/mycontent/events" class="btn btn-default" style="margin-top: 15px;">Back</a>

        <button class="btn btn-danger" data-toggle="modal" data-target="#reject" style="margin-top: 15px;">Reject</button>

        <button class="btn btn-success" style="margin-top: 15px;" data-target="#approve" data-toggle="modal">Approve</button>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->event->getTitle(); ?><span class="pull-right hidden-xs hidden-md"><?= date('d-m-Y H:i:s',strtotime($this->view->event->getStartDateTime())); ?> - <?= date('d-m-Y H:i:s',strtotime($this->view->event->getEndDateTime())); ?></span></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?php if($this->view->event->getRelatedApproval()->getStatus() == null): ?>
            <div class="alert alert-info">
                <strong><?= _g('This item has no approval history'); ?> ?></strong>
            </div>
        <?php elseif($this->view->event->getRelatedApproval()->getStatus() == \Apprecie\Library\Items\ApprovalState::DENIED): ?>
            <div class="alert alert-danger">
                <strong><?= _g('This item has previously been rejected'); ?></strong> :
                <?=$this->view->event->getRelatedApproval()->getDeniedReason(); ?>
            </div>
        <?php elseif($this->view->event->getRelatedApproval()->getStatus() == \Apprecie\Library\Items\ApprovalState::UNPUBLISHED): ?>
        <div class="alert alert-warning">
            <strong><?= _g('This item has been previously un-published'); ?></strong>
        </div>
        <?php endif; ?>
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
                        <?php $address=Address::findFirstBy('addressId',$this->view->event->getAddressId()); ?>
                        <h4>Location</h4>
                        <?php if($address != null): ?>
                        <p>
                            <?php if($address->getLine1()!=null){ echo $address->getLine1().',<br/>';} ?>
                            <?php if($address->getLine2()!=null){ echo $address->getLine2().',<br/>';} ?>
                            <?php if($address->getLine3()!=null){ echo $address->getLine3().',<br/>';} ?>
                            <?php if($address->getCity()!=null){ echo $address->getCity().',<br/>';} ?>
                            <?php if($address->getPostalCode()!=null){ echo $address->getPostalCode().',<br/>';} ?>
                            <?php if($address->getCountryName()!=null){ echo $address->getCountryName();} ?>
                        </p>
                        <?php endif; ?>
                        <h4>Dates</h4>
                        <strong>Event Starts:</strong> <?= _hdt($this->view->event->getStartDateTime(true)); ?><br/>
                        <strong>Event Ends:</strong> <?= _hdt($this->view->event->getEndDateTime()); ?><br/><br/>
                        <strong>Booking Ends:</strong> <?= _hd($this->view->event->getBookingEndDate()); ?><br/><br/>

                        <h4>Pricing</h4>
                        <?= $this->view->event->getUnitPrice(true, true); ?> <?= $this->view->event->getUnitPrice() == 0 ? '' : _g('per Package'); ?><br/>
                        <strong>Package Size:</strong> <?= $this->view->event->getPackageSize(); ?><br/>
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
                        <h4>Location</h4>
                        <div id="map-canvas"></div>
                        <h4 style="padding-top: 15px;">What is Included</h4>
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
<div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Approve Item</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you would like to approve this item? It will be added to the Vault of all managers in this organisation.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="approveItem()">Approve</button>
                </div>
            </div>
        </div>
</div>
<div class="modal fade" id="reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Reject Item</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="reject-error" role="alert" style="display: none;"><strong>Oops!</strong> You must provide a reason for rejecting this item.</div>
                <form method="post" enctype="multipart/form-data" id="reject-form" name="reject-form">
                    {{csrf()}}
                    <p>Are you sure you want to reject this item? It will not be able to be approved by any other manager of this organisation.</p>
                    <p><strong>Reason:</strong></p>
                    <textarea id="rejection-reason" name="rejection-reason" class="form-control" style="height:150px;"></textarea>
                    <input type="hidden" name="itemid" id="itemid" value="<?= $this->view->event->getItemId(); ?>"/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="rejectItem()">Reject</button>
            </div>
        </div>
    </div>
</div>
