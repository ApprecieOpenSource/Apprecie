<script src="/js/compiled/public/js/raw/controllers/items/viewevent.min.js"></script>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/items.min.js"></script>
<script>var itemId=<?= $this->view->event->getItemId(); ?>;</script>
<script>
    $(document).ready(function(){
        var approveModal = new Modal(false);
        var approvalBody='<?= _g('Are you sure you want to approve this item?'); ?>'+
                            '<div class="alert alert-danger" role="alert" id="approval-failed-x" style="display: none;"></div>' +
                            '<form class="form-horizontal">'+
                            '<div class="row" style="margin-bottom: 10px; margin-top: 15px;"><div class="form-group">'+
                            '<label for="title" class="col-sm-6 control-label"><?= _g('Administration Fee (Fixed fee per package)'); ?></label>'+
                            '<div class="col-sm-6">'+
                            '<input type="text" id="administration-fee" name="administration-fee" value="0" class="form-control">'+
                            '</div>'+
                            '</div></div>'+
                            <?php if(! $this->view->event->getIsByArrangement()): ?>
                            '<div class="row" style="margin-bottom: 10px;"><div class="form-group">'+
                            '<label for="title" class="col-sm-6 control-label"><?= _g('Allow Reservation'); ?></label>'+
                            '<div class="col-sm-6"><div class="checkbox"><label>'+
                            '<input type="checkbox" id="reservation-toggle" name="reservation-toggle">'+
                            '</label></div></div>'+
                            '</div></div>'+
                            '<div class="row" style="margin-bottom: 10px;"><div class="form-group">'+
                            '<label for="title" class="col-sm-6 control-label"><?= _g('Reservation Fee (Fixed fee per package)'); ?></label>'+
                            '<div class="col-sm-6">'+
                            '<input type="text" id="reservation-fee" name="reservation-fee" value="" class="form-control" disabled>'+
                            '</div>'+
                            '</div></div>'+
                            '<div class="row" style="margin-bottom: 10px;"><div class="form-group">'+
                            '<label for="title" class="col-sm-6 control-label"><?= _g('Reservation Period (days)'); ?></label>'+
                            '<div class="col-sm-6">'+
                            '<input type="text" id="reservation-period" name="reservation-period" value="" class="form-control" disabled>'+
                            '</div>'+
                            '</div></div>'+
                            <?php endif; ?>
                            '</form>';
        approveModal.confirm('<?= _g('Approve Item'); ?>',approvalBody,'Approve(<?= $this->view->event->getItem()->getItemId(); ?>)','#approve-btn','approve-item');

        var rejectModal = new Modal(false);
        rejectModal.confirmWithMessage('<?= _g('Reject Item'); ?>','<?= _g('Please give a reason for rejecting this item below'); ?>','Reject(<?= $this->view->event->getItem()->getItemId(); ?>)','#reject-btn','reject-item','reject-reason');

        var editApprovalModal = new Modal(false);
        var editApprovalBody='<?= _g('Are you sure you want to change these settings?'); ?>'+
            '<div class="alert alert-danger" role="alert" id="approval-failed-x-edit" style="display: none;"></div>' +
            '<form class="form-horizontal">'+
            '<div class="row" style="margin-bottom: 10px; margin-top: 15px;"><div class="form-group">'+
            '<label for="title" class="col-sm-6 control-label"><?= _g('Administration Fee (Fixed fee per package)'); ?></label>'+
            '<div class="col-sm-6">'+
            '<input type="text" id="administration-fee-edit" name="administration-fee-edit" value="<?= $this->view->event->getAdminFee(true, false, true); ?>" class="form-control">'+
            '</div>'+
            '</div></div>'+
            <?php if(! $this->view->event->getIsByArrangement()): ?>
            '<div class="row" style="margin-bottom: 10px;"><div class="form-group">'+
            '<label for="title" class="col-sm-6 control-label"><?= _g('Allow Reservation'); ?></label>'+
            '<div class="col-sm-6"><div class="checkbox"><label>'+
            '<input type="checkbox" id="reservation-toggle-edit" name="reservation-toggle-edit">'+
            '</label></div></div>'+
            '</div></div>'+
            '<div class="row" style="margin-bottom: 10px;"><div class="form-group">'+
            '<label for="title" class="col-sm-6 control-label"><?= _g('Reservation Fee (Fixed fee per package)'); ?></label>'+
            '<div class="col-sm-6">'+
            '<input type="text" id="reservation-fee-edit" name="reservation-fee-edit" value="<?= $this->view->event->getReservationFee(true, false, true); ?>" class="form-control" disabled>'+
            '</div>'+
            '</div></div>'+
            '<div class="row" style="margin-bottom: 10px;"><div class="form-group">'+
            '<label for="title" class="col-sm-6 control-label"><?= _g('Reservation Period (days)'); ?></label>'+
            '<div class="col-sm-6">'+
            '<input type="text" id="reservation-period-edit" name="reservation-period-edit" value="<?= $this->view->event->getReservationLength(); ?>" class="form-control" disabled>'+
            '</div>'+
            '</div></div>'+
            <?php endif; ?>
            '</form>';
        editApprovalModal.confirm('<?= _g('Reservation and Admin Fee'); ?>', editApprovalBody, 'ApproveEdit(<?= $this->view->event->getItem()->getItemId(); ?>)', '#approve-edit-btn', 'approve-edit-item');

        var packageSize = $('#package-size').text().replace(/[^\d.-]/g, '');
        var costPer = $('#cost-per-unit').text().replace(/[^\d.-]/g, '');
        var maxPackages = $('#max-units').text().replace(/[^\d.-]/g, '');
        var staticCosts = $('#cost-to-deliver').text().replace(/[^\d.-]/g, '');

        var total = Number(packageSize * costPer * maxPackages) + Number(staticCosts);

        if(isNaN(total)) {
            $('#estimate-total-cost').text('');
        }  else if(total == 0) {
            $('#estimate-total-cost').text('TBC');
        } else {
            $('#estimate-total-cost').text(total);
        }

        $('#reservation-toggle').on('change', function() {
            if($(this).prop('checked')) {
                $('#reservation-fee').attr('disabled', false);
                $('#reservation-fee').val(0);
                $('#reservation-period').attr('disabled', false);
                $('#reservation-period').val(1);
            } else {
                $('#reservation-fee').attr('disabled', true);
                $('#reservation-fee').val(null);
                $('#reservation-period').attr('disabled', true);
                $('#reservation-period').val(null);
            }
        });

        $('#reservation-toggle-edit').on('change', function() {
            if($(this).prop('checked')) {
                $('#reservation-fee-edit').attr('disabled', false);
                $('#reservation-period-edit').attr('disabled', false);
            } else {
                $('#reservation-fee-edit').attr('disabled', true);
                $('#reservation-fee-edit').val(null);
                $('#reservation-period-edit').attr('disabled', true);
                $('#reservation-period-edit').val(null);
            }
        });

        var feeEditHasReservation = <?= $this->view->event->getReservationFee() != null ? 'true' : 'false';  ?>;

        if(feeEditHasReservation) {
            $('#reservation-toggle-edit').prop('checked', true);
            $('#reservation-fee-edit').attr('disabled', false);
            $('#reservation-period-edit').attr('disabled', false);
        }
    });
</script>
<?php $state=$this->view->event->getState();?>
<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->event->getTitle(); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <?php if($this->view->event->getRelatedApproval() == null): ?>
        <div class="alert alert-info">
            <strong><?= _g('This item has no approval history'); ?></strong>
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
<a href="/items" class="btn btn-default btn-bottom-margin">Back</a>
<a href="/items/eventprofile/<?= $this->view->event->getItemId(); ?>" class="btn btn-default" style="margin-bottom: 15px;">View Profile</a>
<?php if($this->view->event->getItem()->getState()=='approving'): ?>
    <div class="btn-group" id="approval-group" style="margin-bottom: 15px;">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            Approval <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li id="approve-btn"><a data-target="#approve" data-toggle="modal">Approve</a></li>
            <li id="reject-btn"><a data-target="#reject" data-toggle="modal">Reject</a></li>
        </ul>
    </div>
<?php elseif($this->view->event->getItem()->getState() == \Apprecie\Library\Items\ItemState::APPROVED): ?>
    <div class="btn-group" id="approval-edit-group" style="margin-bottom: 15px;">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            Fee Settings <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li id="approve-edit-btn"><a data-target="#approve-edit" data-toggle="modal">Reservation & Admin Fee</a></li>
        </ul>
    </div>
<?php endif; ?>

<?php
if(!($this->view->event->getItem()->getItemApproval() != null && $this->view->event->getItem()->getDestination() == 'curated' && $this->view->event->getItem()->getItemApproval()->getStatus() != 'approved')
    && $this->view->event->getItem()->getSourceByArrangement() == null): ?>
<div id="curation-group" class="btn-group btn-bottom-margin">
<?php else: ?>
<div id="curation-group" class="btn-group btn-bottom-margin" style="display: none;">
<?php endif; ?>
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        Curate <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li><a data-target="#roles" data-toggle="modal">Organisations</a></li>
    </ul>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger" role="alert" id="approval-failed" style="display: none;"></div>
        <div class="alert alert-success" role="alert" id="approval-success" style="display: none;"></div>
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
                                <td>Tier</td>
                                <td colspan="2">
                                    <?= (new \Apprecie\Library\Users\Tier($this->view->event->getTier()))->getText(); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Publish State</td>
                                <td colspan="2">
                                    <?= (new \Apprecie\Library\Items\ItemState($state))->getText(); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Creator</td>
                                <?php $user=User::findFirstBy('userId',$this->view->event->getCreatorId());?>
                                <td colspan="2"><a href="/adminusers/viewuser/<?= $user->getUserId(); ?>">
                                <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($user->getPortalId()); ?>
                                    <?= $user->getUserProfile()->getFirstName().' '.$user->getUserProfile()->getLastName(); ?></a></td>
                                <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries(); ?>

                            </tr>
                            <tr>
                                <td>Event Date</td>
                                <td>{{fdt(event.getStartDateTime())}}<br/>{{fdt(event.getEndDateTime())}}</td>
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
            <div class="col-sm-4">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Suggested Interests</h5>
                    </div>
                    <div class="ibox-content no-padding">
                        <table class="table table-hover">
                            <tbody>
                            <?php
                            $interests=$this->view->event->getCategories();
                            foreach($interests as $interest):?>
                                <tr>
                                    <td><?= $interest->getInterest(); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Item Visibility</h5>
                    </div>
                    <div class="ibox-content no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Portal</th>
                                <th>Organisation</th>
                                <th>Curated By</th>
                                <th>Internal</th>
                                <th>Clients</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $vaults=ItemVault::findBy('itemId', $this->view->event->getItemId());
                            if($vaults->count()!=0){
                                foreach($vaults as $vault):?>
                                    <?php
                                    $portal=Portal::findFirstBy('portalId',$vault->getPortalId());
                                    $organisation=Organisation::findFirstBy('organisationId',$vault->getOrganisationId());
                                    $ownerName = 'N/A';

                                    if($vault->getOwnerId()!=null){
                                        $owner=User::findFirstBy('userId',$vault->getOwnerId());
                                        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($owner->getPortalId());
                                        $ownerName = $owner->getIsDeleted() ? _g('N/A') : $owner->getUserProfile()->getFullName();
                                        \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries();
                                    }

                                    ?>
                                    <tr>
                                        <td><?= $portal->getPortalName(); ?></td>
                                        <td><?= $organisation->getOrganisationName(); ?></td>
                                        <td><?= $ownerName?></td>
                                        <td><?php if($vault->getInternalCanSee()==1){echo 'Yes';}else{echo 'No';}; ?></td>
                                        <td><?php if($vault->getClientsCanSee()==1){echo 'Yes';}else{echo 'No';}; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php
                            }
                            else{
                                echo '<tr><td colspan="5">This item has not been shared</td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php if($this->view->event->getIsByArrangement()==true): ?>
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
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($this->view->linkedEvents as $event): ?>
                                <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries($event->getArrangedFor()->getPortalId()); ?>
                                <tr>
                                    <td><?= date('d-m-Y H:i:s',strtotime($event->getEvent()->getStartDateTime())); ?></td>
                                    <td><a href="/items/viewevent/<?= $event->getEvent()->getEventId(); ?>"><?= _eh($event->getTitle()); ?></a></td>
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
                                </tr>
                                <?php \Apprecie\Library\Users\UserEx::ForceActivePortalForUserQueries(); ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="modal fade" id="roles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Curate To Organisation</h4>
            </div>
            <div class="modal-body">
                <form id="curate-roles-form">
                    {{csrf()}}
                    <p>Please select a Portal from below to search for organisations. All Managers in the Organisation will see the item in their Vault.</p>
                    <p><strong>Portal</strong></p>
                    <p>
                        <select name="share-portal" id="share-portal" class="form-control">
                            <option disabled selected>Please select...</option>
                            <?php
                            $portals=Portal::query()->where('portalSubdomain!="admin"')->orderBy('portalName')->execute();
                            foreach($portals as $portal){
                                if(! $portal->hasManagers()) {
                                    continue;
                                }
                                ?>
                                <option value="<?= $portal->getPortalId(); ?>"><?= $portal->getPortalName(); ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </p>
                    <table  class="table table-highlight" id="curate-organisation-role-table" style="display: none">
                        <thead>
                        <tr>
                            <th>
                                Organisations
                            </th>
                        </tr>
                        </thead>
                        <tbody id="curate-organisation-role">

                        </tbody>
                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="curate-roles-btn" onclick="CurateToRolesInOrganisation();" class="btn btn-primary">Add</button>
            </div>
        </div>
    </div>
</div>