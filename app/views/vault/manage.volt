<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/guestlist.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/vault/manage.min.js"></script>
<script src="/js/messages.min.js"></script>
<script src="/js/validation/errors.min.js"></script>
<script src="/js/validation/messages.min.js"></script>
<script>
    var itemId=<?= $this->view->item->getItemId();?>;
    var deniedForActions=<?= ($this->view->deniedForActions) ? 'true' : 'false'; ?>;
</script>

<script>
    function selfAttend() {
        $.when(ajaxPostAPI('vault', 'ajaxattend/' + itemId, {"CSRF_SESSION_TOKEN":CSRF_SESSION_TOKEN}).then(function(data){
            if(data.status == 'failed') {
                $('#errorbox').display();
                $('#errorbox').html(data.message);
            } else {
                //reload the page
                window.location.reload();
            }
        }));
    }
</script>

<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->item->getTitle(); ?></h2>
    </div>
</div>
<a onclick="window.history.back();" class="btn btn-default" style="margin-bottom: 15px;"><?= _g('Back'); ?></a>
<div class="btn-group" style="margin-bottom: 15px;">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <?= _g('Actions'); ?> <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <?php if(UserItems::getTotalAvailableUnits($this->view->user->getUserId(),$this->view->item->getItemId(),'owned') > 0 && $this->view->deniedForActions === false): ?>
            <li><a href="/invite/index/<?= $this->view->item->getItemId(); ?>"><?= _g('Invite People'); ?></a></li>
            <?php if(! GuestList::userIsInGuestList($this->view->user->getUserId(), $this->view->user->getUserId(), $this->view->item->getItemId())): ?>
                <li><a onclick="selfAttend();"><?= _g('Attend this Event'); ?></a></li>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($this->view->item->getCreatorId() !== $this->view->user->getUserId()): ?>
            <li><a data-target="#contact" data-toggle="modal" href="javascript:void(0)"><?= _g('Contact Host'); ?></a></li>
        <?php endif; ?>
        <li><a target="_blank" href="/vault/event/<?= $this->view->item->getItemId(); ?>"><?= _g('View Item Profile'); ?></a></li>
    </ul>
</div>
    <div class="alert alert-success" role="alert" id="contact-success" style="display: none;margin: 0 0 15px 0;"></div>
    <?php if(! GuestList::userIsInGuestList($this->view->user->getUserId(), $this->view->user->getUserId(), $this->view->item->getItemId())): ?>
        <div class="alert alert-info" style="" role="alert">You can add yourself to the Attending Guest List by selecting "Attend this Event" from the "Actions" menu.</div>
    <?php endif; ?>
<div class="row">
    <div class="col-sm-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= _g('Event Details'); ?></h5>
            </div>
            <div class="ibox-content">
                <p><strong>Event Date</strong> <?= date('d-m-Y H:i:s',strtotime($this->view->item->getEvent()->getStartDateTime())); ?><br/>
                    <strong><?= GuestList::getGuestCount($this->view->item->getItemId(),$this->view->user->getUserId(),'confirmed')+GuestList::getGuestCount($this->view->item->getItemId(),$this->view->user->getUserId(),'invited'); ?>/<?= UserItems::getTotalOwnedUnits($this->view->user->getUserId(),$this->view->item->getItemId());?></strong>&nbsp;Places have been filled<br/></p>
                <a target="_blank" href="/vault/event/<?= $this->view->item->getItemId(); ?>">View event profile ></a>
            </div>
        </div>
        <div class="alert alert-danger" style="display: none;" id="errorbox" role="alert"></div>
        <div class="alert alert-success" style="display: none;" id="successbox" role="alert"></div>
    </div>

    <div class="col-sm-6">
        <?php if(UserItems::getTotalAvailableUnits($this->view->user->getUserId(),$this->view->item->getItemId(),'reserved') > 0 ): ?>
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= _g('Reservations'); ?></h5>
            </div>
            <div class="ibox-content">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Expires</th>
                        <th>Units</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($this->view->reserved as $reservation): ?>
                    <?php $order = $reservation->getSourceOrder(); ?>
                    <tr>
                        <td><a href="/payment/payreserve/<?= $reservation->getOrderItemId(); ?>">Pay Now</a></td>
                        <td><a href="/orders/order/<?= $order->getOrderId(); ?>"><?= $order->getOrderId(); ?></a></td>
                        <td><?= _fd($order->getCreatedDate()); ?></td>
                        <td>
                            <?= _fdt($reservation->getReservationEnd()); ?>
                        </td>
                        <td>
                            <?= $reservation->getUnitsAvailable(); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div role="tabpanel" id="myTab" style="margin-bottom: 15px;">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?= _g('Attending'); ?> (<span id="attending-count"></span>)</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?= _g('Declined'); ?> (<span id="declined-count"></span>)</a></li>
                <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><?= _g('Invited'); ?> (<span id="invited-count"></span>)</a></li>
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
                    <form method="post" enctype="multipart/form-data" name="downloadguests" id="downloadguestsCSV" action="/vault/AjaxGetGuestList/1">
                        {{csrf()}}
                        <input type="hidden" id="itemid" name="itemid" value="<?= $this->view->item->getItemId(); ?>">
                        <input type="hidden" id="attending" name="attending" value="true">
                        <input type="hidden" id="status" name="status" value="confirmed">
                        <input type="hidden" id="format" name="format" value="csv">
                        <input type="hidden" id="download" name="download" value="true">
                    </form>
                    <form method="post" enctype="multipart/form-data" name="downloadguests" id="downloadguestsExcel" action="/vault/AjaxGetGuestList/1">
                        {{csrf()}}
                        <input type="hidden" id="itemid" name="itemid" value="<?= $this->view->item->getItemId(); ?>">
                        <input type="hidden" id="attending" name="attending" value="true">
                        <input type="hidden" id="status" name="status" value="confirmed">
                        <input type="hidden" id="format" name="format" value="excel">
                        <input type="hidden" id="download" name="download" value="true">
                    </form>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><?= _g('Name'); ?></th>
                            <th><?= _g('Reference'); ?></th>
                            <th><?= _g('Role'); ?></th>
                            <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                            <th class="hidden-xs"><?= _g('Date Accepted'); ?></th>
                            <th class="hidden-xs"><?= _g('Status'); ?></th>
                            <th class="hidden-xs"><?= _g('Spaces'); ?></th>
                            <th class="hidden-xs"></th>
                        </tr>
                        </thead>
                        <tbody id="attending-tbl">

                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination" id="attending-pagination">

                        </ul>
                    </nav>

                </div>
                <div role="tabpanel" class="tab-pane" id="profile">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th><?= _g('Name'); ?></th>
                            <th><?= _g('Reference'); ?></th>
                            <th><?= _g('Role'); ?></th>
                            <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                            <th class="hidden-xs"><?= _g('Date Declined'); ?></th>
                            <th class="hidden-xs"><?= _g('Status'); ?></th>
                            <th class="hidden-xs"><?= _g('Spaces'); ?></th>
                            <th class="hidden-xs"></th>
                        </tr>
                        </thead>
                        <tbody id="declined-tbl">

                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination" id="declined-pagination">

                        </ul>
                    </nav>
                </div>
                <div role="tabpanel" class="tab-pane" id="messages">
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="exportMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            Export...
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportMenu">
                            <li>
                                <a onclick="$('#downloadinvitedguestsCSV').submit()">CSV</a>
                            </li>
                            <li>
                                <a onclick="$('#downloadinvitedguestsExcel').submit()">Excel (.xlsx)</a>
                            </li>
                        </ul>
                    </div>
                    <form method="post" enctype="multipart/form-data" name="downloadinvitedguests" id="downloadinvitedguestsCSV" action="/vault/AjaxGetGuestList/1">
                        {{csrf()}}
                        <input type="hidden" id="itemid" name="itemid" value="<?= $this->view->item->getItemId(); ?>">
                        <input type="hidden" id="attending" name="attending" value="false">
                        <input type="hidden" id="status" name="status" value="invited">
                        <input type="hidden" id="format" name="format" value="csv">
                        <input type="hidden" id="download" name="download" value="true">
                    </form>
                    <form method="post" enctype="multipart/form-data" name="downloadinvitedguests" id="downloadinvitedguestsExcel" action="/vault/AjaxGetGuestList/1">
                        {{csrf()}}
                        <input type="hidden" id="itemid" name="itemid" value="<?= $this->view->item->getItemId(); ?>">
                        <input type="hidden" id="attending" name="attending" value="false">
                        <input type="hidden" id="status" name="status" value="invited">
                        <input type="hidden" id="format" name="format" value="excel">
                        <input type="hidden" id="download" name="download" value="true">
                    </form>
                    <table class="table">
                        <thead>
                        <tr>
                            <th><?= _g('Name'); ?></th>
                            <th><?= _g('Reference'); ?></th>
                            <th><?= _g('Role'); ?></th>
                            <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                            <th class="hidden-xs"><?= _g('Date Invited'); ?></th>
                            <th class="hidden-xs"><?= _g('Status'); ?></th>
                            <th class="hidden-xs"><?= _g('Spaces'); ?></th>
                            <th class="hidden-xs"></th>
                        </tr>
                        </thead>
                        <tbody id="invited-tbl">

                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination" id="invited-pagination">

                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<button style="display: none;" id="hidden-btn"></button>
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
                    <input type="hidden" id="itemId" name="itemId" value="<?= $this->view->item->getItemId(); ?>"/>
                    <input type="hidden" id="targetUser" name="targetUser" value="<?= $this->view->item->getCreatorId(); ?>"/>
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
<?= (new EmailWidget('index', array('templateType' => \Apprecie\Library\Mail\EmailTemplateType::INVITATION, 'callback' => 'InviteGuest', 'previewData' => null)))->getContent(); ?>