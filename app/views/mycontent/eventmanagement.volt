<script>var itemId=<?= $this->view->event->getItemId(); ?>;</script>
<script src="/js/compiled/public/js/raw/library/guestlist.min.js"></script>
<script src="/js/compiled/public/js/raw/library/items.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/mycontent/eventmanagement.min.js"></script>
<?php $state=$this->view->event->getState();?>
<div class="row">
    <div class="col-sm-12">
        <?php if($this->view->warning): ?>
            <div class="alert alert-danger">
                <strong>Warning</strong> <?= $this->view->warning; ?>
            </div>
        <?php endif; ?>
        <h2><?= _eh($this->view->event->getTitle()); ?>
            <?php if($this->view->event->getIsByArrangement()==false): ?>
                <div class="pull-right dropdown">
                <span class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" style="margin-right: 10px;cursor: pointer;">
                    <button class="btn" style="margin-top: 5px; margin-right: -10px;"><i class="fa fa-ellipsis-v"></i> Actions</button>
                </span>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li><a role="menuitem" tabindex="-1"  href="/vault/event/<?= $this->view->event->getItemId(); ?>">View Profile</a></li>
                        <?php if ($this->view->event->getSourceByArrangement() != null): ?>
                            <li><a role="menuitem" tabindex="-1" href="/vault/arrangedp/<?= $this->view->event->getItemId(); ?>"><?= _g('View Request'); ?></a></li>
                        <?php endif; ?>
                        <?php if ($state =='draft' || $state == 'denied'): ?>
                        <li><a role="menuitem" tabindex="-1"  href="/itemcreation/editevent/<?= $this->view->event->getEventId(); ?>">Edit Details</a></li>
                            <?php if ($this->view->event->getStatus() != 'closed' && $this->view->event->getStatus() != 'expired'): ?>
                                <?php if (! ($this->view->event->getUnitPrice() > 0 && \Organisation::getActiveUsersOrganisation()->getPaymentSettings()->getPublishableKey() == null)): ?>
                                    <li><a role="menuitem" tabindex="-1" data-target="#publishModal" data-toggle="modal">Publish</a></li>
                                <?php endif; ?>
                                <?php if (Event::canDeleteEvent($this->view->event)): ?>
                                    <li><a role="menuitem" tabindex="-1" data-target="#deleteModal" data-toggle="modal"><?= _g('Delete'); ?></a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if($state=='approved' && $this->view->event->getRemainingPackages() == $this->view->event->getMaxUnits()): ?>
                        <li><a role="menuitem" tabindex="-1" data-target="#unpublishModal" data-toggle="modal">Unpublish</a></li>
                        <?php endif; ?>
                        <li><a role="menuitem" tabindex="-1"  href="/itemcreation/media/<?= $this->view->event->getItemId(); ?>">Media Manager</a></li>
                        <li><a role="menuitem" tabindex="-1"  href="/pdf/get/<?= $this->view->event->getItemId(); ?>">Download Brochure</a></li>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if($this->view->event->getIsByArrangement()==true): ?>
                <div class="pull-right dropdown">
                <span class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" style="margin-right: 10px;cursor: pointer;">
                    <button class="btn" style="margin-top: 5px; margin-right: -10px;"><i class="fa fa-ellipsis-v"></i> Actions</button>
                </span>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        <li><a href="/vault/arranged/<?= $this->view->event->getItemId(); ?>">View Profile</a></li>
                        <?php if($state =='draft' || $state == 'denied'): ?>
                        <li><a href="/itemcreation/editarranged/<?= $this->view->event->getEventId(); ?>">Edit Details</a></li>
                            <?php if($this->view->event->getStatus()!='closed' && $this->view->event->getStatus()!='expired'): ?>
                                <li><a role="menuitem" tabindex="-1" data-target="#publishModal" data-toggle="modal">Publish</a></li>
                            <?php endif; ?>
                            <?php if(Event::canDeleteEvent($this->view->event)): ?>
                                <li><a role="menuitem" tabindex="-1" data-target="#deleteModal" data-toggle="modal"><?= _g('Delete'); ?></a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if($state=='approved'): ?>
                        <li><a class="btn btn-default" data-target="#unpublishModal" data-toggle="modal">Unpublish</a></li>
                        <?php endif; ?>
                        <li><a href="/itemcreation/media/<?= $this->view->event->getItemId(); ?>">Media Manager</a></li>
                        <li><a role="menuitem" tabindex="-1"  href="/pdf/get/<?= $this->view->event->getItemId(); ?>">Download Brochure</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </h2>
    </div>
</div>
<?php if($this->view->event->getIsByArrangement()==false): ?>
    <div class="row">
    <div class="col-sm-12">
    <div class="row">
    <div class="col-sm-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Event Details</h5>
            </div>
            <div class="ibox-content no-padding">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td>Publish State</td>
                        <td colspan="2">
                            <?php if ($this->view->event->getSourceByArrangement() == null): ?>
                                <?= (new \Apprecie\Library\Items\ItemState($state))->getText(); ?>
                            <?php else: ?>
                                <a href="/vault/arrangedp/<?= $this->view->event->getItemId(); ?>"><?= (new \Apprecie\Library\Items\ItemState($state))->getText(); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if($state == 'denied'): ?>
                    <tr>
                        <td>Rejection Reason</td>
                        <td colspan="2">
                            <?=$this->view->event->getRejectionReason(); ?>
                        </td>
                    </tr>
                    <?php endif ?>
                    <tr>
                        <td>Creator</td>
                        <?php $user=User::findFirstBy('userId',$this->view->event->getCreatorId());?>
                        <td colspan="2">
                                <?= $user->getUserProfile()->getFullName(); ?></td>
                    </tr>
                    <tr>
                        <td>Event Date</td>
                        <td>
                            {{fdt(event.getStartDateTime())}}<br/>{{fdt(event.getEndDateTime())}}
                            <br>
                            <a href="<?= $this->view->calLink; ?>">
                                Add to Calendar
                            </a>
                        </td>
                        <td><i class="fa fa-calendar pull-right" style="cursor: pointer"></i></td>
                    </tr>
                    <tr>
                        <td>Booking Dates</td>
                        <td>{{fd(event.getBookingStartDate())}}<br/>{{fd(event.getBookingEndDate())}}</td>
                        <td><i class="fa fa-calendar pull-right" style="cursor: pointer"></i></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Package Details</h5>
            </div>
            <div class="ibox-content no-padding">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td>Spaces per Package</td>
                        <td id="package-size"><?= $this->view->event->getPackageSize() ?></td>
                    </tr>
                    <tr>
                        <td>Maximum Packages</td>
                        <td id="max-units"><?= $this->view->event->getMaxUnits() ?></td>
                    </tr>
                    <tr>
                        <td>Currency</td>
                        <td><?= $this->view->event->getCurrency()->getAlphabeticCode() ?></td>
                    </tr>
                    <tr>
                        <td>Sales Tax Rate</td>
                        <td><?= $this->view->event->getTaxablePercent() ?>%</td>
                    </tr>
                    <tr>
                        <td><?= _g('Price per Package'); ?></td>
                        <td><?= $this->view->event->getUnitPrice(true, true) ?></td>
                    </tr>
                    <tr>
                        <td>Packages Available</td>
                        <td><?= $this->view->event->getRemainingPackages() ?></td>
                    </tr>
                    <tr>
                        <td>Packages Purchased</td>
                        <td><?= $this->view->event->getPurchasedPackages() ?></td>
                    </tr>
                    <tr>
                        <td>Packages Reserved</td>
                        <td><?= $this->view->event->getReservedPackages() ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Estimated ROI</h5>
            </div>
            <div class="ibox-content no-padding">
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <td>Minimum Spaces</td>
                        <td id="min-units"><?= $this->view->event->getMinUnits(); ?></td>
                    </tr>
                    <tr>
                        <td>Cost per Attendee</td>
                        <td id="cost-per-unit"><?= $this->view->event->getPricePerAttendee(true, true); ?></td>
                    </tr>
                    <tr>
                        <td>Static Costs</td>
                        <td id="cost-to-deliver"><?= $this->view->event->getCostToDeliver(true, true); ?></td>
                    </tr>
                    <tr>
                        <td>Estimated Total Cost</td>
                        <td id="estimate-total-cost">Unknown</td>
                    </tr>
                    <tr>
                        <td>Compliance Value</td>
                        <td><?= $this->view->event->getMarketValue(true, true); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
        <div role="tabpanel" id="myTab" style="margin-bottom: 15px;">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?= _g('Guest List'); ?> (<span id="guest-count">0</span>)</a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" style="background-color: white; padding: 10px;border-left: 1px solid rgb(221, 221, 221);border-bottom: 1px solid rgb(221, 221, 221);border-right: 1px solid rgb(221, 221, 221);">
        <div role="tabpanel" class="tab-pane active" id="home">
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="exportMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    Export...
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportMenu">
                    <li>
                        <a onclick="$('#downloadguestsCSV').submit()">CSV</a>
                    </li>
                    <li>
                        <a onclick="$('#downloadguestsExcel').submit()">Excel (.xlsx)</a>
                    </li>
                </ul>
            </div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th><?= _g('Name'); ?></th>
                    <th><?= _g("Role"); ?></th>
                    <th><?= _g('Organisation'); ?></th>
                    <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                    <th class="hidden-xs"><?= _g('Spaces'); ?></th>
                    <th><?= _g('Notes'); ?></th>
                </tr>
                </thead>
                <tbody id="attending-tbl">
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <form method="post" enctype="multipart/form-data" name="downloadguests" id="downloadguestsCSV" action="/mycontent/AjaxCreatorGuestList/1">
                            <input type="hidden" id="itemid" name="itemid" value="<?= $this->view->event->getItemId(); ?>">
                            <input type="hidden" id="attending" name="attending" value="true">
                            <input type="hidden" id="status" name="status" value="confirmed">
                            <input type="hidden" id="format" name="format" value="csv">
                            <input type="hidden" id="download" name="download" value="true">
                            {{csrf()}}
                        </form>
                        <form method="post" enctype="multipart/form-data" name="downloadguests" id="downloadguestsExcel" action="/mycontent/AjaxCreatorGuestList/1">
                            <input type="hidden" id="itemid" name="itemid" value="<?= $this->view->event->getItemId(); ?>">
                            <input type="hidden" id="attending" name="attending" value="true">
                            <input type="hidden" id="status" name="status" value="confirmed">
                            <input type="hidden" id="format" name="format" value="excel">
                            <input type="hidden" id="download" name="download" value="true">
                            {{csrf()}}
                        </form>
                    </td>
                </tr>
                </tfoot>
            </table>
            <nav>
                <ul class="pagination" id="attending-pagination">

                </ul>
            </nav>
        </div>
        </div>
        </div>
        </div>
    </div>
    </div>
    </div>
<?php endif; ?>
<?php if($this->view->event->getIsByArrangement()==true): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-4">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Event Details</h5>
                        </div>
                        <div class="ibox-content no-padding">
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <td>Event Type</td>

                                    <td colspan="2">
                                        By Arrangement
                                    </td>
                                </tr>
                                <tr>
                                    <td>Publish State</td>

                                    <td colspan="2">
                                        <?= (new \Apprecie\Library\Items\ItemState($state))->getText(); ?>
                                    </td>
                                </tr>
                                <?php if($state == 'denied'): ?>
                                    <tr>
                                        <td>Rejection Reason</td>

                                        <td colspan="2">
                                            <?=$this->view->event->getRejectionReason(); ?>
                                        </td>
                                    </tr>
                                <?php endif ?>
                                <tr>
                                    <td>Creator</td>
                                    <?php $user=User::findFirstBy('userId',$this->view->event->getCreatorId());?>
                                    <td colspan="2">
                                            <?= $user->getUserProfile()->getFirstName().' '.$user->getUserProfile()->getLastName(); ?></td>
                                </tr>
                                <tr>
                                    <td>Event Date</td>
                                    <td>
                                        {{fdt(event.getStartDateTime())}}<br/>{{fdt(event.getEndDateTime())}}
                                        <?php if ($this->view->event->getStartDateTime() !== 'TBC' && $this->view->event->getEndDateTime() !== 'TBC'): ?>
                                            <br>
                                            <a href="<?= $this->view->calLink; ?>">
                                                Add to Calendar
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><i class="fa fa-calendar pull-right" style="cursor: pointer"></i></td>
                                </tr>
                                <tr>
                                    <td>Booking Dates</td>
                                    <td>{{fd(event.getBookingStartDate())}}<br/>{{fd(event.getBookingEndDate())}}</td>
                                    <td><i class="fa fa-calendar pull-right" style="cursor: pointer"></i></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Package Details</h5>
                        </div>
                        <div class="ibox-content no-padding">
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <td>Spaces per Package</td>
                                    <td id="package-size"><?= $this->view->event->getPackageSize() ?></td>
                                </tr>
                                <tr>
                                    <td>Maximum Packages</td>
                                    <td id="max-units"><?= $this->view->event->getMaxUnits() ?></td>
                                </tr>
                                <tr>
                                    <td>Currency</td>
                                    <td><?= $this->view->event->getCurrency()->getAlphabeticCode() ?></td>
                                </tr>
                                <tr>
                                    <td>Sales Tax Rate</td>
                                    <td><?= $this->view->event->getTaxablePercent() ?>%</td>
                                </tr>
                                <tr>
                                    <td><?= _g('Price per Package'); ?></td>
                                    <td><?= $this->view->event->getUnitPrice(true, true) ?></td>
                                </tr>
                                <tr>
                                    <td>Packages Available</td>
                                    <td><?= $this->view->event->getRemainingPackages() ?></td>
                                </tr>
                                <tr>
                                    <td>Packages Purchased</td>
                                    <td><?= $this->view->event->getPurchasedPackages() ?></td>
                                </tr>
                                <tr>
                                    <td>Packages Reserved</td>
                                    <td><?= $this->view->event->getReservedPackages() ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Estimated ROI</h5>
                        </div>
                        <div class="ibox-content no-padding">
                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <td>Minimum Spaces</td>
                                    <td id="min-units"><?= $this->view->event->getMinUnits(); ?></td>
                                </tr>
                                <tr>
                                    <td>Cost per Attendee</td>
                                    <td id="cost-per-unit"><?= $this->view->event->getPricePerAttendee(true, true); ?></td>
                                </tr>
                                <tr>
                                    <td>Static Costs</td>
                                    <td id="cost-to-deliver"><?= $this->view->event->getCostToDeliver(true, true); ?></td>
                                </tr>
                                <tr>
                                    <td>Estimated Total Cost</td>
                                    <td id="estimate-total-cost">Unknown</td>
                                </tr>
                                <tr>
                                    <td>Compliance Value</td>
                                    <td><?= $this->view->event->getMarketValue(true, true); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Linked Events</h5>
                        </div>
                        <div class="ibox-content">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Start Date</th>
                                        <th>Title</th>
                                        <th>Organisation</th>
                                        <th><?= _g('Manager'); ?></th>
                                        <th><?= _g('Manager Email Address'); ?></th>
                                        <th>Attendance</th>
                                        <th>Arrangement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($this->view->linkedEvents as $event): ?>
                                    <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($event->getArrangedFor()->getPortalId()); ?>
                                    <tr>
                                        <td><?= date('d-m-Y H:i:s',strtotime($event->getEvent()->getStartDateTime())); ?></td>
                                        <td><a href="/mycontent/eventmanagement/<?= $event->getEvent()->getEventId(); ?>"><?= _eh($event->getTitle()); ?></a></td>
                                        <td><?= $event->getArrangedFor()->getOrganisation()->getOrganisationName(); ?></td>
                                        <td>
                                            <?php
                                                if(! $event->getArrangedFor()->getIsDeleted()) {
                                                    $ManagerProfile = $event->getArrangedFor()->getUserProfile();
                                                    echo $ManagerProfile->getFullName();
                                                } else {
                                                    echo _g('Inactive User');
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if(! $event->getArrangedFor()->getIsDeleted()) {
                                                echo $event->getArrangedFor()->getUserProfile()->getEmail();
                                            }  else {
                                               echo _g('Inactive User');
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?= GuestList::getGuestCount($event->getItemId(),$event->getIsArrangedFor(),'confirmed');?>
                                        </td>
                                        <td>
                                            <?php if ($event->getState() === \Apprecie\Library\Items\ItemState::APPROVED): ?>
                                                <a href="/vault/arrangedp/<?= $event->getItemId(); ?>"><?= _g('Approved'); ?></a>
                                            <?php elseif ($event->getState() === \Apprecie\Library\Items\ItemState::DENIED && $event->getEvent()->getStatus() === \Apprecie\Library\Items\EventStatus::REJECTED): ?>
                                                <a href="/vault/arrangedp/<?= $event->getItemId(); ?>"><?= _g('Rejected'); ?></a>
                                            <?php elseif ($event->getState() === \Apprecie\Library\Items\ItemState::ARRANGING && $event->getEvent()->getStatus() === \Apprecie\Library\Items\EventStatus::PUBLISHED): ?>
                                                <a href="/vault/arrangedp/<?= $event->getItemId(); ?>"><?= _g('Pending'); ?></a>
                                            <?php else: ?>
                                                <a href="/vault/arrangedp/<?= $event->getItemId(); ?>"><?= _g('Expired'); ?></a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries(); ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="modal fade" id="unpublishModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Unpublish Event</h4>
            </div>
            <div class="modal-body">
                <p>Unpublishing this item will cause it to be removed from all Vaults and return it to Draft state.</p>
                <p>Are you sure you want to unpublish this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a style="color:white;" href="/mycontent/eventmanagement/<?= $this->view->event->getEventId();?>/?unpublish=true" class="btn btn-danger">Confirm</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= _g('Delete Event'); ?></h4>
            </div>
            <div class="modal-body">
                <p>Deleting this item will remove it from our systems and the data will be un-recoverable.</p>
                <p>Are you sure you want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <a style="color:white;" href="/mycontent/eventmanagement/<?= $this->view->event->getEventId();?>/?delete=true" class="btn btn-danger">Confirm</a>
            </div>
        </div>
    </div>
</div>
<script>
    function publishThisEvent(type,id){
        switch(type){
            case 'confirmed':

                if($('input[name=publishstate]:checked').val()!=null){
                    var publish=new publishEvent();
                    publish.setEventId(id);
                    publish.setState($('input[name=publishstate]:checked').val());
                    $('#publish-btn').prop('disabled',true);
                    $.when(publish.fetch()).then(function(){
                        window.location.replace("/mycontent/eventmanagement/"+id);
                    });
                }
                break;
        }
    }
</script>
<div class="modal fade" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Publish Event</h4>
            </div>
            <div class="modal-body">
                <?php if(Organisation::getActiveUsersOrganisation()->getIsAffiliateSupplierOf()): ?>
                    <input type="radio" id="publishstate" name="publishstate" value="parent"/><label style="font-weight: normal; margin-left: 5px;"><?= _g("Publish to Parent Organisation"); ?></label><br/>
                <?php else: ?>
                    <?php
                    $auth= new \Apprecie\Library\Security\Authentication();
                    $user=$auth->getAuthenticatedUser();
                    switch($user->getActiveRole()->getName()){
                        case "Manager":
                            ?>
                            <input type="radio" id="publishstate" name="publishstate" value="organisation"/><label style="font-weight: normal; margin-left: 5px;"><?= _g("Publish to this Organisation"); ?></label><br/>
                            <?php
                            break;
                        case "Internal":
                            ?>
                            <!--<input type="radio" id="publishstate" name="publishstate" value="organisation"/><label style="font-weight: normal; margin-left: 5px;">Publish to this Organisation</label><br/>-->
                            <input type="radio" id="publishstate" name="publishstate" value="vault"/><label style="font-weight: normal; margin-left: 5px;"><?= _g('Publish to my Vault'); ?></label><br/>
                            <?php
                            break;
                        case "ApprecieSupplier":
                            ?>
                            <input type="radio" id="publishstate" name="publishstate" value="curation"/><label style="font-weight: normal; margin-left: 5px;"><?= _g("Send to Apprecie for curation"); ?></label><br/>
                            <?php
                            break;
                        case "AffiliateSupplier":
                            ?>
                            <input type="radio" id="publishstate" name="publishstate" value="parent"/><label style="font-weight: normal; margin-left: 5px;"><?= _g("Publish to Parent Organisation"); ?></label><br/>
                            <?php
                            break;
                    }
                    ?>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button onclick="publishThisEvent('confirmed',<?= $this->view->event->getEventId(); ?>)" id="publish-btn" class="btn btn-success">Publish</button>
            </div>
        </div>
    </div>
</div>