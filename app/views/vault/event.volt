<?php $address = Address::findFirstBy('addressId',$this->view->event->getAddressId()); ?>
<?php
/* visibility logic */
$purchasable=false;
$reservationLength=false;
$bookingClosed=false;
$order=false;
$fullyBooked=false;
$unavailable=false;
$arranged=false;
$commingSoon=false;
$supplier=false;

if($this->view->event->getReservationLength() > 0){
    $reservationLength=$this->view->event->getReservationLength();
}

if($this->view->event->getStatus() === \Apprecie\Library\Items\EventStatus::CLOSED || $this->view->event->getStatus() === \Apprecie\Library\Items\EventStatus::OPEN || $this->view->event->getStatus() === \Apprecie\Library\Items\EventStatus::EXPIRED){
    $bookingClosed=true;
}
elseif(UserItems::getTotalAvailableUnits($this->view->getDI()->get('auth')->getAuthenticatedUser()->getUserId(), $this->view->event->getItemId(),\Apprecie\Library\Items\UserItemState::HELD) > 0){
    $order = UserItems::getBy($this->view->getDI()->get('auth')->getAuthenticatedUser()->getUserId(), $this->view->event->getItemId(),\Apprecie\Library\Items\UserItemState::HELD)[0]->getSourceOrder();
    $orderItem = $order->getOrderItems()[0];
}
elseif($this->view->event->getStatus() == \Apprecie\Library\Items\EventStatus::FULLY_BOOKED){
    $fullyBooked=true;
}
elseif($this->view->event->getStatus() == \Apprecie\Library\Items\EventStatus::CLOSED || $this->view->event->getStatus() == \Apprecie\Library\Items\EventStatus::EXPIRED){

}
elseif($this->view->event->getRemainingPackages() == 0){
    $unavailable=true;
}
else{
    if(strtotime($this->view->event->getBookingStartDate()) > time()){
        $commingSoon=true;
    }
    elseif($this->view->event->getIsArranged()){
        $arranged=true;
    }
    elseif($this->view->getDI()->get('auth')->getAuthenticatedUser()->getActiveRole()->getName() === \Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER){

    }
    elseif($this->view->getDI()->get('auth')->getAuthenticatedUser()->getActiveRole()->getName() === \Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER){

    }
    elseif($this->view->event->getCreatorId() == $this->view->getDI()->get('auth')->getAuthenticatedUser()->getUserId()){
        $supplier=true;
    }
    else{
        $purchasable=true;
    }
}

?>
<script type="text/javascript"  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUzUvVTT07M1xZoDGIERc6xfl3x5Rljw"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/library/items.min.js"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/controllers/vault/event.min.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<script src="/js/compiled/public/js/raw/library/suggestedusers.min.js"></script>
<script src="/js/messages.min.js"></script>
<script src="/js/validation/errors.min.js"></script>
<script src="/js/validation/messages.min.js"></script>
<?php $this->partial("partials/jparts/itemSuggestedUsersTable"); ?>

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
        loadItemSuggestions(1);
    })

    function loadItemSuggestions(pageNumber){
        var ajax=new getSuggestedUsersForEvent(itemId);

        $.when(ajax.fetch()).then(function(data){
            displayUserSuggestions(data,pageNumber);
        })
    }

    var thisPage=null;
    function displayUserSuggestions(data,pageNumber){
        var template = $.templates("#itemSuggestedUsersTable");
        thisPage=new Pager(data,10,pageNumber,'thisPage',$("#pagerContainer"),template);
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
    <?php
    $auth=new \Apprecie\Library\Security\Authentication();
    if(($auth->getAuthenticatedUser()->getActiveRole()->getName()=='Manager' or $auth->getAuthenticatedUser()->getActiveRole()->getName()=='Internal') and $this->view->event->getStatus() === \Apprecie\Library\Items\EventStatus::PUBLISHED):?>
        <?php if(! $this->view->event->getIsArranged()) : ?>
            <button data-target="#share" data-toggle="modal" class="btn btn-default pull-right" style="margin-right: 10px; margin-top: 10px;">Share</button>
        <?php endif ?>
        <a href="/invite/suggest/<?= $this->view->event->getItemId(); ?>" class="btn btn-default pull-right" style="margin-right: 10px; margin-top: 10px;">Suggest</a>
    <?php endif; ?>
    <?php if($this->view->event->getCreatorId() != $this->view->getDI()->get('auth')->getAuthenticatedUser()->getUserId()): ?>
        <button data-target="#contact" data-toggle="modal" class="btn btn-default pull-right" style="margin-right: 10px; margin-top: 10px;">Contact Host</button>
    <?php endif; ?>
    <a class="btn btn-default pull-right" href="/pdf/get/<?= $this->view->event->getItemId(); ?>" style="margin-top: 10px; margin-right: 10px;"><i class="fa fa-file-pdf-o"></i>&nbsp;<?= _g('Download Brochure'); ?></a>
    <div style="width:500px;  top: 15px; left: 15px; margin-bottom: 15px; background-image: url('/img/titlebg.png'); padding: 20px; z-index: 1; position: relative;">
        <span style="font-size: 26px; font-weight: 200;color: white;"><?= $this->view->event->getTitle(); ?></span>
        <?php if($this->view->getDI()->get('auth')->getAuthenticatedUser()->getActiveRole()->getName()=='Internal' || $this->view->getDI()->get('auth')->getAuthenticatedUser()->getActiveRole()->getName()=='Manager'): ?>
        <p>
            <i class="fa fa-briefcase" style="color: gold;"></i>
            <?php for($i=1;$i<4;$i++): ?>
            <?php if($i<=$this->view->event->getTier()): ?>
                <i class="fa fa-trophy" title="<?= (new \Apprecie\Library\Users\Tier($this->view->event->getTier()))->getText(); ?>" style="color: gold"></i>
            <?php else: ?>
                <i class="fa fa-trophy"  title="<?= (new \Apprecie\Library\Users\Tier($this->view->event->getTier()))->getText(); ?>"></i>
            <?php endif; ?>
        <?php endfor; ?>
        </p>
        <?php endif; ?>
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
                <div class="col-sm-4" style="color:white;"></div>
                <div class="col-sm-8" style="color:white;">
                    <a href="<?= $this->view->calLink; ?>" style="color: #4494D0;">
                        Add to Calendar
                    </a>
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
                <td class="price-td"><?= $this->view->event->getPackageSize(); ?></td>
                <td class="price-td">
                    <?= $this->view->event->getUnitPrice(true, true); ?>
                </td>
            </tr>
            <?php if ($this->view->event->getAdminFee() && !$this->view->getDI()->get('auth')->getAuthenticatedUser()->hasRole(\Apprecie\Library\Users\UserRole::APPRECIE_SUPPLIER) && !$this->view->getDI()->get('auth')->getAuthenticatedUser()->hasRole(\Apprecie\Library\Users\UserRole::AFFILIATE_SUPPLIER)): ?>
                <tr>
                    <td class="price-td"></td>
                    <td class="price-td"></td>
                    <td class="price-td" style="">
                        <?= _g('+ {amount} (Admin Fee)', ['amount' => $this->view->event->getAdminFee(true, true)]); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
        <?php if($purchasable===true && $this->view->getDI()->get('auth')->getAuthenticatedUser()->getActiveRole()->getName()!='ApprecieSupplier' && $this->view->getDI()->get('auth')->getAuthenticatedUser()->getActiveRole()->getName()!='AffiliateSupplier'):?>
            <div class="row" style="margin-top: 25px;">
                <div class="col-sm-4" style="color:white; padding-top: 5px;">
                    <span style="color:white">Number of packages</span>
                </div>
                <div class="col-sm-8">
                    <div class="input-group col-sm-12">
                        <select id="package-quantity" class="form-control">
                            <?php for($i=0; $i<$this->view->event->getRemainingPackages();$i++): ?>
                            <option value="<?=($i+1); ?>"><?=($i+1); ?></option>
                            <?php endfor; ?>
                        </select>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Purchase <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li><a onclick="PlaceOrder(false,<?= $this->view->event->getItem()->getItemId(); ?>)">Buy Now</a></li>
                                <?php if ($reservationLength !== false): ?>
                                    <li><a onclick="PlaceOrder(true,<?= $this->view->event->getItem()->getItemId(); ?>)">Reserve</a></li>
                                <?php endif; ?>
                            </ul>
                        </div><!-- /btn-group -->
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if($order!==false): ?>
    <div class="alert alert-warning" role="alert" style="margin-top: 15px;">
        <strong>You have an open order for this item</strong>
        <p><?= $orderItem->getDescription(); ?></p>
        <p><?= $order->getFormattedFullTotal(); ?></p>
        <p>
            <a class="btn btn-primary dropdown-toggle" href="/payment/index/<?= $order->getOrderId(); ?>">Continue Order</a>
            <button type="button" class="btn btn-primary dropdown-toggle" id="cancel-order-btn" name="cancel-order-btn" onclick="CancelOrder(<?= $order->getOrderId(); ?>)">Cancel Order</button>
        </p>
    </div>
<?php endif; ?>
<?php if($bookingClosed===true): ?>
    <div class="alert alert-warning" role="alert" style="margin-top: 15px;">
        <strong>Booking for this event has now closed</strong>
    </div>
<?php endif; ?>
<?php if($fullyBooked===true): ?>
    <div class="alert alert-warning" role="alert" style="margin-top: 15px;">
        <strong>This event is now fully booked</strong>
    </div>
<?php endif; ?>
<?php if($unavailable===true): ?>
    <div class="alert alert-warning" role="alert" style="margin-top: 15px;">
        <strong><?= _g('No packages available at the moment'); ?></strong>
        <p><?= _g('Please note: Some existing bookings may not yet be fully completed and may return to the market'); ?></p>
    </div>
<?php endif; ?>
<?php if($commingSoon===true): ?>
    <div class="alert alert-info" role="alert" style="margin-top: 15px;">
        <strong><?= _g('Coming Soon'); ?></strong>
        <p><?= _g('Booking opens on {date}',['date'=>_hd($this->view->event->getBookingStartDate())]); ?>  </p>
    </div>
<?php endif; ?>
<?php if($this->view->event->getCreatorId() == $this->view->getDI()->get('auth')->getAuthenticatedUser()->getUserId()): ?>
    <div class="alert alert-info" role="alert" style="margin-top: 15px;">
        <strong><?= _g('Coming Soon'); ?></strong>
        <p><?= _g('Booking opens on {date}',['date'=>_hd($this->view->event->getBookingStartDate())]); ?>  </p>
    </div>
<?php endif; ?>
<?php if($this->view->event->getIsArranged() && $this->view->event->getCreatorId() != $this->view->getDI()->get('auth')->getAuthenticatedUser()->getUserId()): ?>
    <div class="alert alert-info" role="alert" style="margin-top: 15px;">
        <div class="input-group col-sm-12">
            <strong>You have arranged this event</strong>
            <input value="<?= $this->view->event->getMaxUnits() ?>" type="hidden" id="package-quantity">
            <p>You have agreed to purchase <?= $this->view->event->getMaxUnits() ?> unit(s)</p>
            <div class="input-group-btn">
                <button type="button" class="btn btn-primary dropdown-toggle" onclick="PlaceOrder(false,<?= $this->view->event->getItem()->getItemId(); ?>)">Purchase Now</button>
            </div><!-- /btn-group -->
        </div>
    </div>
<?php endif; ?>
<?php if($this->view->event->getCreatorId() == $this->view->getDI()->get('auth')->getAuthenticatedUser()->getUserId() && !$this->view->event->getIsArranged()): ?>
    <div class="alert alert-info" role="alert" style="margin-top: 15px;">
        <p>As you are the supplier of this item, if you consume this item it will be complimentary and no fees will be charged</p>
        Number of packages:
        <div class="input-group col-sm-6">
            <input value="1" type="text" id="package-quantity" class="form-control" aria-label="...">
            <div class="input-group-btn">
                <button type="button" class="btn btn-primary dropdown-toggle" onclick="PlaceOrder(false,<?= $this->view->event->getItem()->getItemId(); ?>)">Consume Now</button>
            </div><!-- /btn-group -->
        </div>
    </div>
<?php endif; ?>
<div class="row" style="display:none;">
    <div class="col-sm-12">
        <?php
        $auth=new \Apprecie\Library\Security\Authentication();
        if(($auth->getAuthenticatedUser()->getActiveRole()->getName()=='Manager' or $auth->getAuthenticatedUser()->getActiveRole()->getName()=='Internal') and $this->view->event->getStatus() === \Apprecie\Library\Items\EventStatus::PUBLISHED):?>

            <a href="/invite/suggest/<?= $this->view->event->getItemId(); ?>" class="btn btn-default" >Suggest</a>
        <?php endif; ?>

        <a class="btn btn-default" href="/pdf/get/<?= $this->view->event->getItemId(); ?>"><i class="fa fa-file-pdf-o"></i> Download Brochure</a>
        <div class="alert alert-success" role="alert" id="contact-success" style="display: none;margin: 15px 0 0 0;"></div>
    </div>
</div>
<div class="row" style="margin-top: 15px;">
    <div class="col-sm-8" id="col2" style="margin-bottom: 15px;">
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
        <p><a href="#" data-toggle="modal" data-target="#purchase-terms-modal">Purchase Terms</a><br/><a href="#" data-toggle="modal" data-target="#attendance-terms-modal">Attendance Terms</a></p>
        <h2>Media Gallery</h2>
        <?=(new ItemMediaWidget('index2',array('itemId'=>$this->view->event->getItemId())))->getContent() ?>
        <?php if($address!=null): ?>
        <h2>Location<br/><span style="font-weight: normal; font-size: 12px;"><?= $address->getLabel(); ?></span></h2>
        <div id="map-canvas"></div>
        <?php endif; ?>
        <?php if(count($this->view->suggestionCount)!=0): ?>
        <h2>All Suggested People</h2>
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-content">
                <div id="pagerContainer">

                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-sm-4" id="col1" style="margin-bottom: 15px;">
        <?php if(count($this->view->suggestionCount['totalItems'])!=0): ?>
            <h2>Top Suggested People</h2>
            <?php
            $limit=6;
            $loop=1;
            foreach($this->view->suggestionCount['items'] as $userId=>$data){
                ?>
                <div class="media" style="background-color: white; padding-bottom:0px;">
                    <div class="media-left">
                        <a href="/people/viewuser/<?=$data['userId']; ?>">
                            <img class="media-object" style="width:85px;" src="<?= Assets::getUserProfileImage($data['userId']); ?>">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 style="margin-top: 5px;" class="media-heading"><?= $data['firstName'].' '.$data['lastName']; ?></h4>
                        <p>
                            <?= $data['organisation']; ?>
                        </p>
                        <p>
                            <?= $data['interestMatch']; ?> Interest Matches
                        </p>
                    </div>
                </div>
                <?php
                if($loop==$limit){
                    break;
                }
                else{
                    $loop++;
                }
            }
            ?>
        <?php endif; ?>
        <?php if(count($this->view->similar) !=0):?>
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-sm-12">
                <h2>Similar Events</h2>
                <div class="row">
                    <?php foreach($this->view->similar as $item): ?>
                        <div class="col-md-12 col-lg-12 item-container" >
                            <div style="position:relative" class="item-tile">
                                <?php if($item['item']['isByArrangement']==1): ?>
                                <a style="text-decoration:none" href="/vault/arranged/<?=$item['item']['itemId'];?>">
                                    <?php else: ?>
                                    <a style="text-decoration:none" href="/vault/event/<?=$item['item']['itemId'];?>">
                                        <?php endif; ?>
                                    </a>
                                    <div style="position:relative">
                                        <?php if($item['item']['isByArrangement']==1): ?>
                                        <a style="text-decoration:none" href="/vault/arranged/<?=$item['item']['itemId'];?>">
                                            <?php else: ?>
                                            <a style="text-decoration:none" href="/vault/event/<?=$item['item']['itemId'];?>">
                                                <?php endif; ?>
                                                <img src="<?=$item['image'];?>" class="img-responsive tile-image" style="width:100%">
                                                <?php if($item['suggestionsCount']!=0): ?>
                                                    <span class="label label-info"  style="font-size:11px; position:absolute; top:5px; right:5px;"><i class="fa fa-user"></i> <?=$item['suggestionsCount']; ?> People Matches</span>
                                                <?php endif; ?>
                                                <div class="tile-title">
                                                    <h4 style="font-family: 'Quicksand', sans-serif; margin-left: 10px; font-weight: normal; font-size:16px; color:white;">
                                                        <?=$item['itemTitle'];?>
                                                    </h4>
                                                    <div style="margin-bottom:10px; margin-left:10px;">
                                                        <span style="color:white;"><?= $item['startDate'];?></span>
                                                        <span style="margin-right:10px;color:white;" class="pull-right"><?= $item['brand'];?></span>
                                                    </div>
                                                    <div class="item-tile-desc">
                                                        <div style="color:white; margin-bottom:15px; margin-left:5px;" class="hidden-md hidden-xs">
                                                            <?= $item['shortSummary'];?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                    </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

    </div>
</div>
<?php endif; ?>
<?php if($auth->getAuthenticatedUser()->getActiveRole()->getName()=='Manager' or $auth->getAuthenticatedUser()->getActiveRole()->getName()=='Internal'):?>
    <div class="modal fade" id="share" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Share Item</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success" id="share-success" style="display:none;" role="alert">
                        This item has been shared.
                    </div>
                    <div class="alert alert-danger" id="share-error" style="display:none;" role="alert">
                        Please select who you would like to share the item with.
                    </div>
                    <form method="post" enctype="multipart/form-data" id="share-form" name="share-form">
                        {{csrf()}}
                        <?php if($this->view->organisationChildren!=null): ?>
                            <h5>Specific Organisations</h5>
                            <?php if($auth->getAuthenticatedUser()->getActiveRole()->getName()=='Manager'): ?>
                                <p>Please select the organisations you would like to share this item with from below:</p>
                                <p>
                                    <?php foreach($this->view->organisationChildren as $organisation): ?>
                                        <input type="checkbox" id="org-share" name="org-share[]" value="<?= $organisation->getOrganisationId(); ?>"> <?= $organisation->getOrganisationName(); ?><br/>
                                    <?php endforeach; ?>
                                </p>
                                <p>Note that this item will appear in the Vault for all managers in the selected organisations.</p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <h5>My Members Vault</h5>
                        <?php if($auth->getAuthenticatedUser()->getActiveRole()->getName()=='Manager'): ?>
                            <input type="checkbox" <?= $internalShared ? 'checked="checked"' : ''; ?> id="internal-share" name="internal-share" value="1"> Internal Members<br/>
                            <br />
                            <div class="alert alert-warning">
                                <p>Note that if you share an item with the clients of your internal members, your internal members will receive this item in a way that cannot currently be un-shared.</p>
                                <br />
                                <input type="checkbox" id="internal-client-share" name="internal-client-share" value="1">Clients of my Internal Members<br/>
                            </div>
                        <?php endif; ?>
                        <?php if($auth->getAuthenticatedUser()->getActiveRole()->getName()=='Internal' or $auth->getAuthenticatedUser()->getActiveRole()->getName()=='Manager'): ?>
                            <input type="checkbox" id="client-share" <?= $clientsShared ? 'checked="checked"' : ''; ?> name="client-share" value="1"> Client Members<br/>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="shareItem()">Share</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Contact Host</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="contact-error" style="display:none;" role="alert"></div>
                <form class="form-horizontal" id="contact-form" name="contact-form">
                    {{csrf()}}
                    <p>Please enter your message below:</p>
                    <div class="form-group">
                        <label for="contact-subject">Subject</label>
                        <input type="text" class="form-control" id="contact-subject" name="contact-subject"/>
                    </div>
                    <div class="form-group">
                        <textarea style="width:100%; height:200px;" id="contact-message" name="contact-message"></textarea>
                    </div>
                    <input type="hidden" id="itemId" name="itemId" value="<?= $this->view->event->getItemId(); ?>"/>
                    <input type="hidden" id="targetUser" name="targetUser" value="<?= $this->view->event->getCreatorId(); ?>"/>
                    <input type="hidden" id="messageThreadType" name="messageThreadType" value="<?= \Apprecie\Library\Messaging\MessageThreadType::HOST; ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="contact-send-btn" onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="suggest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Suggest this event</h4>
            </div>
            <ul class="nav nav-tabs">
                <li><a data-toggle="tab" href="#internal">Internal</a></li>
                <li><a data-toggle="tab" href="#external">External</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="internal">
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert" id="suggest-error-box" style="display: none;"></div>
                        <div class="alert alert-success" role="alert" id="suggest-success-box" style="display: none;"></div>
                        {{ widget('UserFinderWidget','multiselect') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="suggest-btn">Send</button>
                    </div>
                </div>
                <div class="tab-pane fade in" id="external">
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert" id="suggest-ext-error-box" style="display: none;"></div>
                        <div class="alert alert-success" role="alert" id="suggest-ext-success-box" style="display: none;"></div>
                        <form class="form-inline" role="form">
                            <div class="form-group">
                                <p><?= _g('Seperate multiple addresses with ;'); ?></p>
                                <label for="suggest-email">Email address:</label>
                                <input type="email" class="form-control" id="suggest-email" name="suggest-email">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="suggest-ext-btn">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="purchase-terms-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Purchase Terms</h4>
            </div>
            <div class="modal-body">
                <?php if($this->view->item->getPurchaseTerms()!=null || $this->view->item->getPurchaseTerms()!=''): ?>
                    <?= $this->view->item->getPurchaseTerms(); ?>
                <?php else: ?>
                    There are no specific purchase terms for this event
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="attendance-terms-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Attendance Terms</h4>
            </div>
            <div class="modal-body">
                <?php if($this->view->event->getAttendanceTerms()!=null || $this->view->event->getAttendanceTerms()!=''): ?>
                    <?= $this->view->event->getAttendanceTerms(); ?>
                <?php else: ?>
                    There are no specific attendance terms for this event
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>