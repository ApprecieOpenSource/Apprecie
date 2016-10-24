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
    })

    function initialize() {
        var mapOptions = {
            center: { lat: 51.5736739, lng: -0.7743399},
            zoom: 12
        };
        var myLatlng = new google.maps.LatLng(51.5736739,-0.7743399);

        // To add the marker to the map, use the 'map' property
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title:"Hello World!"
        });

        var map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);
        marker.setMap(map);
    }
    google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php if(! isset($this->view->postdata['confirmed-description'])) {
    echo _g('Invalid Preview Data');
    return;
}
?>
<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->postdata['confirmed-title']; ?>
            <?php if(isset($this->view->postdata['confirmed-startdate'])): ?>
            <span class="pull-right hidden-xs hidden-md"><?= $this->view->postdata['confirmed-startdate']; ?> - <?= $this->view->postdata['confirmed-enddate']; ?></span>
            <?php endif; ?>
        </h2>
    </div>
</div>
<div class="row" style="margin-top: 15px;">
    <div class="col-sm-6" id="col1">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Media Gallery</h5>
            </div>
            <div class="ibox-content">
                <img src="/img/no-item-image.jpg" class="img-responsive"/>
                <div class="row hidden-xs" style="padding-left:13px; padding-right: 13px; margin-top: 10px;">
                    <div class="col-sm-3 thumb">
                        <img src="/img/no-item-image.jpg" class="img-responsive"/>
                    </div>
                    <div class="col-sm-3 thumb">
                        <img src="/img/no-item-image.jpg" class="img-responsive"/>
                    </div>
                    <div class="col-sm-3 thumb">
                        <img src="/img/no-item-image.jpg" class="img-responsive"/>
                    </div>
                    <div class="col-sm-3 thumb">
                        <img src="/img/no-item-image.jpg" class="img-responsive"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6" id="col2">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5 style="width:100%;">About</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12 details">
                        <?= $this->view->postdata['confirmed-short-description']; ?>
                    </div>
                    <div class="col-sm-12 details">
                        <h4>Location</h4>
                        <p>Address Line 1,<br/>
                            Address Line 2,<br/>
                            City,<br/>
                            Postcode,<br/>
                            Country<br/>
                        </p>
                        <h4>Dates</h4>
                        <?php if(isset($this->view->postdata['confirmed-startdate'])): ?>
                            <strong>Event Starts:</strong> <?= $this->view->postdata['confirmed-startdate']; ?><br/>
                            <strong>Event Ends:</strong> <?= $this->view->postdata['confirmed-enddate']; ?><br/><br/>
                        <?php endif; ?>
                        <?php if(isset($this->view->postdata['confirmed-bookingend'])): ?>
                            <strong>Booking Ends:</strong> <?= $this->view->postdata['confirmed-bookingend']; ?><br/><br/>
                        <?php endif; ?>

                        <span style="font-size:18px;"><?= Currency::findFirstBy('currencyId', $this->view->postdata['currency'])->getSymbol(); ?> <?= $this->view->postdata['price-per-unit'] == 0 ? 'Complimentary' : $this->view->postdata['price-per-unit'] . ' ' . _g('Per package'); ?> </span><br/>
                        <div class="input-group" style="margin-top: 15px;">
                            <input type="text" class="form-control" style="width:80px;" value="1">
                            <button class="btn btn-primary" type="button">Buy</button>
                        </div>
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
                        <?= $this->view->postdata['confirmed-description']; ?>
                    </div>
                    <div class="col-sm-6 details">
                        <h4>Venue (Demo map)</h4>
                        <div id="map-canvas"></div>
                        <h4>What is Included</h4>
                        <p>
                            <?php
                            if(isset($this->view->postdata['catering-breakfast'])){ echo 'Breakfast<br/>';}
                            if(isset($this->view->postdata['catering-lunch'])){ echo 'Lunch<br/>';}
                            if(isset($this->view->postdata['catering-refresh'])){ echo 'Light Refreshments<br/>';}
                            if(isset($this->view->postdata['catering-tea'])){ echo 'Afternoon Tea<br/>';}
                            if(isset($this->view->postdata['catering-dinner'])){ echo 'Dinner<br/>';}
                            ?>
                        </p>
                        <h4>Contact Host</h4>
                        <p></p>
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
                <?php if($this->view->postdata['purchase-terms']!=null): ?>
                    <?= $this->view->postdata['purchase-terms']; ?>
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
                <?php if($this->view->postdata['attendance-terms']!=null): ?>
                    <?= $this->view->postdata['attendance-terms']; ?>
                <?php else: ?>
                    There are no specific attendance terms for this event.
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>