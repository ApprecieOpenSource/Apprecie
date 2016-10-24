<?php $address=Address::findFirstBy('addressId',$this->view->event->getAddressId()); ?>
<script type="text/javascript"  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUzUvVTT07M1xZoDGIERc6xfl3x5Rljw"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/library/items.min.js"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/controllers/vault/event.min.js"></script>

<script>
    var eventId = <?= $this->view->event->getEventId(); ?>;
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
<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->event->getTitle(); ?></h2>
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
                        <?php if($this->view->event->getAddressId()!=NULL): ?>
                        <h4>Location</h4>
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
                        <strong>Event Starts:</strong> <?= _hdt($this->view->event->getStartDateTime()); ?><br/>
                        <strong>Event Ends:</strong> <?= _hdt($this->view->event->getEndDateTime()); ?><br/><br/>
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
    <div class="col-sm-12">
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


