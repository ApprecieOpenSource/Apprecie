<?php $address=Address::findFirstBy('addressId',$this->view->event->getAddressId()); ?>
<script type="text/javascript"  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUzUvVTT07M1xZoDGIERc6xfl3x5Rljw"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/library/items.min.js"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/controllers/vault/event.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<script src="/js/messages.min.js"></script>
<script src="/js/validation/errors.min.js"></script>
<script src="/js/validation/messages.min.js"></script>
<script>
    var eventId = <?= $this->view->event->getEventId(); ?>;
    var itemId = <?= $this->view->event->getItemId(); ?>;
    var postcode= '<?= $address ? $address->getPostalCode() : ''; ?>';
</script>
<script>
    $(document).ready(function(){
        $('.item-tile').hover(function(){
            $( this ).find('.item-tile-desc').stop().animate({
                height: "toggle"
            }, 200, function() {
                // Animation complete.
            });
            //$(this).find('.item-tile-desc').stop().fadeIn('fast','linear');
        },function(){
            $( this ).find('.item-tile-desc').stop().animate({
                height: "toggle"
            }, 200, function() {
                // Animation complete.
            });
        })
    })
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
    google.maps.event.addDomListener(window, 'load', initialize);
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
    .price-th{
        font-weight: normal;
        font-size: 14px;
        color: lightblue;
    }
    .price-td{
        font-weight: normal;
        font-size: 14px;
        color: white;
    }

</style>
<style type="text/css">
    html, body, #map-canvas { height: 350px; margin: 0; padding: 0;}
</style>
<div style="margin-left:-15px; margin-right:-15px; position:relative;    padding-bottom: 15px; background-image: url('<?= Assets::getItemBannerImage($this->view->event->getItem()->getItemId());?>')">
    <div style="width:500px;  top: 15; left: 15; margin-bottom: 15px; background-image: url('/img/titlebg.png'); padding: 20px; z-index: 1; position: relative;">
        <span style="font-size: 26px; font-weight: 200;color: white;"><?= $this->view->event->getTitle(); ?></span>
        <p style="color:white; font-size:14px;">
        <div class="row">
            <div class="col-sm-4" style="color:white;">
                Event Starts
            </div>
            <div class="col-sm-8" style="color:white;">
                <?= _hdt($this->view->event->getStartDateTime()); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4" style="color:white;">
                Event Ends
            </div>
            <div class="col-sm-8" style="color:white;">
                <?= _hdt($this->view->event->getEndDateTime()); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4" style="color:white;">
                Booking Closes
            </div>
            <div class="col-sm-8" style="color:white;">
                <?= _hdt($this->view->event->getBookingEndDate()); ?>
            </div>
        </div>
        </p>
        <table style="width:100%">
            <thead>
            <tr>
                <th class="price-th">Packages available</th>
                <th class="price-th">Spaces per Package</th>
                <th class="price-th">Package price</th>
            </tr>
            </thead>
            <tr>
                <td class="price-td"><?= $this->view->event->getRemainingPackages(); ?></td>
                <td class="price-td">
                    <?php if( $this->view->event->getPackageSize()!=null){
                        echo $this->view->event->getPackageSize();
                    }else{
                        echo _g('Not specified');
                    } ?>
                </td>
                <td class="price-td">
                    <?php if( $this->view->event->getUnitPrice()!=null){
                        echo $this->view->event->getUnitPrice(true,true);
                    }else{
                        echo _g('Not specified');
                    } ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row" style="margin-top: 15px;">
    <div class="col-sm-12" id="col2" style="margin-bottom: 15px;">
        <h2><?= $this->view->event->getSummary(); ?></h2>
        <p style="font-size:14px !important;">
            <?= $this->view->event->getDescription(); ?>
        </p>
        <p>
            <?php if($this->view->event->getBreakfast()==1):?>
                <span class="label label-primary"  style="font-size:12px;">Breakfast included</span>
            <?php endif; ?>
            <?php if($this->view->event->getLunch()==1):?>
                <span class="label label-primary"  style="font-size:12px;">Lunch included</span>
            <?php endif; ?>
            <?php if($this->view->event->getLightRefreshment()==1):?>
                <span class="label label-primary"  style="font-size:12px;">Light refreshments included</span>
            <?php endif; ?>
            <?php if($this->view->event->getAfternoonTea()==1):?>
                <span class="label label-primary"  style="font-size:12px;">Afternoon tea included</span>
            <?php endif; ?>
            <?php if($this->view->event->getDinner()==1):?>
                <span class="label label-primary"  style="font-size:12px;">Dinner included</span>
            <?php endif; ?>
        </p>
        <img src="<?= Assets::getItemPrimaryImage($this->view->event->getItemId()); ?>" class="img-responsive"/>
        <?php if($this->view->event->getAttendanceTerms() != ''): ?>
            <h2>Attendance Terms</h2>
            <p>
                <?= $this->view->event->getAttendanceTerms(); ?>
            </p>
        <?php endif; ?>
        <?php if($address!=null): ?>
            <h2>Location<br/><span style="font-weight: normal; font-size: 12px;"><?= $address->getLabel(); ?></span></h2>
            <div id="map-canvas"></div>
        <?php endif; ?>
    </div>
</div>