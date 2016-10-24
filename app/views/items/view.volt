<?php $state=$this->view->event->getState();?>
<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->event->getTitle(); ?></h2>
    </div>
</div>
<a href="/mycontent/events" class="btn btn-default" style="margin-bottom: 15px;">Back</a>
<?php if($state=='draft'): ?>
    <a href="/itemcreation/editevent/<?= $this->view->event->getEventId(); ?>" class="btn btn-default" style="margin-bottom: 15px;">Edit Details</a>
<?php endif; ?>
<?php if($state=='approved'): ?>
    <a class="btn btn-default" data-target="#publishModal" data-toggle="modal" style="margin-bottom: 15px;">Unpublish</a>
<?php endif; ?>
<a href="/itemcreation/media/<?= $this->view->event->getItemId(); ?>" class="btn btn-default" style="margin-bottom: 15px;">Media Manager</a>
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
                                    <?= (new \Apprecie\Library\Items\ItemState($state))->getText(); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Creator</td>
                                <?php $user=User::findFirstBy('userId',$this->view->event->getCreatorId());?>
                                <td colspan="2"><a href="/users/profile/<?= $user->getUserId(); ?>">
                                        <?= $user->getUserProfile()->getFirstName().' '.$user->getUserProfile()->getLastName(); ?></a></td>
                            </tr>
                            <tr>
                                <td>Event Date</td>
                                <td><?= date('d-m-Y H:i:s',strtotime($this->view->event->getStartDateTime())); ?><br/><?= date('d-m-Y H:i:s',strtotime($this->view->event->getEndDateTime())); ?></td>
                                <td><i class="fa fa-calendar pull-right" style="cursor: pointer"></i></td>
                            </tr>
                            <tr>
                                <td>Booking Dates</td>
                                <td><?= date('d-m-Y',strtotime($this->view->event->getBookingStartDate())); ?><br/><?= date('d-m-Y',strtotime($this->view->event->getBookingEndDate())); ?></td>
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
                        <h5><?= _g('Attendance'); ?></h5>
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
                                    <td><?= $this->view->event->getUnitPrice(true, true) ?> </td>
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
    </div>
</div>
</div>

<div class="modal fade" id="publishModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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