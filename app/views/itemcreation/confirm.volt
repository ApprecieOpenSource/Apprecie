<?php
$address=Address::findFirstBy('addressId',$this->view->event->getAddressId());
?>
<script src="/js/compiled/public/js/raw/library/modal.min.js"></script>
<script src="/js/compiled/public/js/raw/library/generic.ajax.min.js"></script>
<script src="/js/validation/items/confirmed-arranged.js"></script>
<script src="/js/validation/errors.js"></script>
<script>
    var eventId = <?= $this->view->event->getEventId(); ?>;
    var earliestBookingEndDate = '<?= $this->view->earliestBookingEndDate->format('d/m/Y'); ?>';
    var bookingStartDate = '<?= date('d/m/Y', strtotime($this->view->event->getBookingStartDate())); ?>';
    $(document).ready(function(){
        setStep(2);
        <?php if($address!=null): ?>
        $('#search-term').val(<?= _j($address->getPostalCode()); ?>);
        $('#country').val(<?= _j($address->getCountryIso3()); ?>);
        $('#address-id').val(<?= _j($address->getId()); ?>);
        $('#selected-address-value').html(<?= _j($address->getLine1() .' ' . $address->getLine2() .' '. $address->getPostalCode(), true); ?>);
        $('#selected-address').show();
        <?php endif; ?>

        $('#create-btn').click(function() {
            var publishState = $('input:radio[name=publishstate]:checked').val();
            if (publishState === 'save') {
                validateStep(8);
            } else if (publishState === 'confirm' || publishState === 'confirm-unpublish') {
                confirmModalFromApi('api', 'confirmArrangementMessagePreview', {"eventId":eventId, "bookingEndDate":$('#confirmed-bookingend').val()}, 'create-btn', 'validateStep(8)');
            }
        });
    });

    function previewEvent(){
        var form=$('#item-creation-form');
        form.unbind('submit').submit();
        form.attr('target','_blank').attr('method','post').attr('action','/itemcreation/previewevent');
        form.submit();
        form.removeAttr('target').removeAttr('method').removeAttr('action');
    }
</script>
<style>
    .btn-loading{
        background-color: black;
        border-color: black;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <h2><?= $this->view->event->getTitle(); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 1%;">
            </div>
        </div>
    </div>
</div>
<form id="item-creation-form"  autocomplete="off" name="item-creation-form" class="form-horizontal">
    {{csrf()}}
    <div class="row">
    <div class="col-sm-8">
        <div class="alert alert-danger" role="alert" id="error-box" style="display: none;"></div>

        <div id="steps">
        <script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
        <script src="/js/addressing/lookupWidget.js"></script>

        <script>
            setSteps(9);
            $(document).ready(function(){
                tinymce.init({
                    menubar: "format insert edit",
                    plugins: 'link',
                    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
                    selector: '#confirmed-description'
                });
                tinymce.init({
                    menubar: "format insert edit",
                    plugins: 'link',
                    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
                    selector: '#attendance-terms'
                });
                tinymce.init({
                    menubar: "format insert edit",
                    plugins: 'link',
                    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
                    selector: '#purchase-terms'
                });

                $('#confirmed-starttime').clockpicker({
                    placement: 'bottom',
                    align: 'left',
                    autoclose: true,
                    'default': ''
                });
                $('#confirmed-endtime').clockpicker({
                    placement: 'bottom',
                    align: 'left',
                    autoclose: true,
                    'default': ''
                });

                var picker1 = new Pikaday(
                    {
                        field: document.getElementById('confirmed-startdate'),
                        firstDay: 1,
                        format: 'DD/MM/YYYY',
                        minDate: new Date('01/01/2015'),
                        onSelect: function() {
                            var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                            document.getElementById('selected').appendChild(date);
                        }
                    });
                var picker2 = new Pikaday(
                    {
                        field: document.getElementById('confirmed-enddate'),
                        firstDay: 1,
                        format: 'DD/MM/YYYY',
                        minDate: new Date('01/01/2015'),
                        onSelect: function() {
                            var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                            document.getElementById('selected').appendChild(date);
                        }
                    });
                var picker3 = new Pikaday(
                    {
                        field: document.getElementById('confirmed-bookingend'),
                        firstDay: 1,
                        format: 'DD/MM/YYYY',
                        minDate: new Date('01/01/2015'),
                        onSelect: function() {
                            var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                            document.getElementById('selected').appendChild(date);
                        }
                    });


                $('.totalCalc').change(function ()
                {
                    var packageSize = $('#package-size').val();
                    var costPer = $('#cost-per-unit').val();
                    var maxPackages = $('#max-units').val();
                    var staticCosts = $('#cost-to-deliver').val();

                    var total = Number(packageSize * costPer * maxPackages) + Number(staticCosts);

                    if(isNaN(total)) {
                        $('#estimate-total-cost').val('');
                    } else {
                        $('#estimate-total-cost').val(total.toFixed(2));
                    }
                });

                $( ".totalCalc" ).trigger( "change" );
            });
        </script>
        <div id="step-2" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5>2. <?= _g('Basic Details'); ?></h5>
                <span class="pull-right" style="font-weight: 600"><?= _g('CONFIRM BY ARRANGEMENT EVENT'); ?></span>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Title'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="confirmed-title" value="<?= $this->view->event->getTitle(); ?>" id="confirmed-title">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Short Description'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="confirmed-short-description" value="<?= $this->view->event->getSummary(); ?>" name="confirmed-short-description">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Full Description'); ?></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="confirmed-description" name="confirmed-description" style="height:150px;"><?= $this->view->event->getDescription(); ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Booking End Date'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" id="confirmed-bookingend" name="confirmed-bookingend" value="<?= $this->view->newBookingEndDate->format('d-m-Y'); ?>" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                        <p style="padding-top: 3px;">
                            <?= _g('To give the consumer enough time to pay for the item, we recommend you set the booking end date to be at least 3 days before the event start date.'); ?>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;Start Date & Time<br/>(24 hour format)</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" id="confirmed-startdate" name="confirmed-startdate" value="<?= _fd($this->view->event->getStartDateTime(), true); ?>" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                            <input type="text" value="<?= _ft($this->view->event->getStartDateTime(), true); ?>" id="confirmed-starttime" name="confirmed-starttime" class="form-control" style="max-width: 120px; display: inline" placeholder="HH:MM">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;End Date & Time<br/>(24 hour format)</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" id="confirmed-enddate" name="confirmed-enddate" value="<?= _fd($this->view->event->getEndDateTime(), true); ?>" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                            <input type="text" id="confirmed-endtime" name="confirmed-endtime" value="<?= _ft($this->view->event->getEndDateTime(), true); ?>" class="form-control" style="max-width: 120px; display: inline" placeholder="HH:MM">
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center">  <a class="btn btn-primary pull-right" onclick="validateStep(2)">Next</a></div>
        </div>
        <div id="step-3" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5>3. Venue</h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <?= (new AddressFinderWidget('index', array('showFieldMarkings' => true)))->getContent(); ?>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">Catering</label>
                    <div class="col-sm-9">
                        <div style="float:left;"><input type="checkbox" value="breakfast" <?php if($this->view->event->getBreakfast()==1){ echo 'checked';} ?> id="catering-breakfast" name="catering-breakfast"><label class="checkbox" style="font-weight:normal; display: inline; margin-right: 10px" for="catering-breakfast"> Breakfast</label></div>
                        <div style="float:left;"><input type="checkbox" value="lunch" <?php if($this->view->event->getLunch()==1){ echo 'checked';} ?> id="catering-lunch" name="catering-lunch"> <label class="checkbox" for="catering-lunch" style="font-weight:normal; display: inline; margin-right: 10px"> Lunch</label></div>
                        <div style="float:left;"><input type="checkbox" value="dinner" <?php if($this->view->event->getDinner()==1){ echo 'checked';} ?> id="catering-dinner" name="catering-dinner"><label class="checkbox" for="catering-dinner" style="font-weight:normal; display: inline; margin-right: 10px"> Dinner</label></div>
                        <div style="float:left;"><input type="checkbox" value="refreshments" <?php if($this->view->event->getLightRefreshment()==1){ echo 'checked';} ?> id="catering-refresh" name="catering-refresh"><label class="checkbox" for="catering-refresh" style="font-weight:normal; display: inline; margin-right: 10px"> Light Refreshments/Drinks</label></div>
                        <div style="float:left;"><input type="checkbox" value="tea" <?php if($this->view->event->getAfternoonTea()==1){ echo 'checked';} ?> id="catering-tea" name="catering-tea"><label class="checkbox" for="catering-tea" style="font-weight:normal; display: inline; margin-right: 10px"> Afternoon Tea</label></div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(2)">Previous</a>  <a class="btn btn-primary pull-right" onclick="validateStep(3)">Next</a></div>
        </div>
        <div id="step-4" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5>4. Attendance</h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="package-size" class="col-sm-3 control-label">*&nbsp;Spaces per Package</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" value="<?= $this->view->event->getPackageSize(); ?>" id="package-size" maxlength="9" name="package-size">
                    </div>
                </div>
                <div class="form-group">
                    <label for="max-units" class="col-sm-3 control-label">*&nbsp;<?= _g("Number of Packages"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" value="<?= $this->view->event->getMaxUnits(); ?>" id="max-units" maxlength="9" name="max-units">
                    </div>
                </div>
                <div class="form-group">
                    <label for="currency" class="col-sm-3 control-label">*&nbsp;Currency</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="currency" name="currency">
                            <?php foreach($this->view->currencies as $currency): ?>
                            <option  <?php if($this->view->event->getCurrencyId()==$currency->getCurrencyId()){echo 'selected';} ?>  value="<?= $currency->getCurrencyId(); ?>"><?= $currency->getCurrency(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tax-rate" class="col-sm-3 control-label">*&nbsp;Sales Tax Rate</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?= $this->view->event->getTaxablePercent(); ?>" id="tax-rate" maxlength="6" name="tax-rate">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="price-per-unit" class="col-sm-3 control-label">*&nbsp;<?= _g('Price per Package'); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" value="<?= $this->view->event->getUnitPrice(true); ?>" id="price-per-unit" maxlength="9" name="price-per-unit">
                    </div>
                </div>
                <div class="ibox-title">
                    <h5>4.1 ROI</h5>
                </div>
                <div class="form-group">
                    <label for="min-units" class="col-sm-3 control-label">*&nbsp;Minimum Spaces</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" value="<?= $this->view->event->getMinUnits(); ?>" id="min-units" maxlength="9" name="min-units">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cost-per-unit" class="col-sm-3 control-label">*&nbsp;Cost per Attendee</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" value="<?= $this->view->event->getPricePerAttendee(true); ?>" id="cost-per-unit" maxlength="9" name="cost-per-unit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cost-to-deliver" class="col-sm-3 control-label">*&nbsp;Static Costs</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" id="cost-to-deliver" value="<?= $this->view->event->getCostToDeliver(true); ?>" maxlength="9" name="cost-to-deliver">
                    </div>
                </div>
                <div class="form-group">
                    <label for="estimate-total-cost" class="col-sm-3 control-label">Estimated Total Cost</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="estimate-total-cost" readonly="readonly" maxlength="9" name="estimate-total-cost">
                    </div>
                </div>
                <div class="form-group">
                    <label for="market-value" class="col-sm-3 control-label">*&nbsp;Compliance Value</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="market-value" maxlength="9" value="<?= $this->view->event->getMarketValue(true); ?>" name="market-value">
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(3)">Previous</a>  <a class="btn btn-primary pull-right" onclick="validateStep(4)">Next</a></div>
        </div>
        <div id="step-5" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5>5. Categories</h5>
            </div>
            <div class="ibox-content">
                <p>Please select a primary interest from below to begin</p>
                <?=(new CategoryPickerWidget('event',array('eventId'=>$this->view->event->getEventId())))->getContent() ?>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(4)">Previous</a>  <a class="btn btn-primary pull-right" onclick="setStep(6)">Next</a></div>
        </div>
        <div id="step-6" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5>6. Goals</h5>
            </div>
            <div class="ibox-content">
                <label style="font-weight: normal"><input type="checkbox" value="Product Launch" <?php if($this->view->event->hasGoal('Product Launch')){echo 'checked';}?> name="goal[]"> Product Launch</label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Charity" <?php if($this->view->event->hasGoal('Charity')){echo 'checked';}?> name=goal[]"> Charity</label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Education" <?php if($this->view->event->hasGoal('Education')){echo 'checked';}?> name="goal[]"> Education</label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="General Company Promotion" <?php if($this->view->event->hasGoal('General Company Promotion')){echo 'checked';}?> name="goal[]"> General Company Promotion</label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Networking" <?php if($this->view->event->hasGoal('Networking')){echo 'checked';}?> name="goal[]"> Networking</label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Investment Related" <?php if($this->view->event->hasGoal('Investment Related')){echo 'checked';}?> name="goal[]"> Investment Related</label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Passive Event (party/talk/performance)" <?php if($this->view->event->hasGoal('Passive Event (party/talk/performance)')){echo 'checked';}?> name="goal[]"> Passive Event (party/talk/performance)</label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Active Event (adventure/activity/doing)" <?php if($this->view->event->hasGoal('Active Event (adventure/activity/doing)')){echo 'checked';}?> name="goal[]"> Active Event (adventure/activity/doing)</label>
            </div>
            <div class="ibox-title" style="border-top: none;">
                <h5>Target Demographics</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-4">
                        <h5>*&nbsp;Gender</h5>
                        <label style="font-weight: normal"><input name="gender" type="radio" <?php if($this->view->event->getGender()=='mixed'){echo 'checked';} ?> value="mixed"> Mixed</label><br/>
                        <label style="font-weight: normal"><input name="gender" type="radio" <?php if($this->view->event->getGender()=='male'){echo 'checked';} ?> value="male"> Male</label><br/>
                        <label style="font-weight: normal"><input name="gender" type="radio" <?php if($this->view->event->getGender()=='female'){echo 'checked';} ?> value="female"> Female</label><br/>
                    </div>
                    <div class="col-sm-4">
                        <h5>Age</h5>
                        <label style="font-weight: normal"><input type="checkbox" <?php if($this->view->event->getTargetAge18to34()==1){echo 'checked';} ?>  value="18-34" name="age18to34"> 18-34 </label><br/>
                        <label style="font-weight: normal"><input type="checkbox" <?php if($this->view->event->getTargetAge34to65()==1){echo 'checked';} ?> value="34-65" name="age34to65"> 34-65</label><br/>
                        <label style="font-weight: normal"><input type="checkbox" <?php if($this->view->event->getTargetAge65Plus()==1){echo 'checked';} ?> value=">65" name="age65over"> Over 65</label>
                    </div>
                    <div class="col-sm-4">
                        <h5>*&nbsp;Member Tier</h5>
                        <label style="font-weight: normal"><input type="radio" <?= ((int)$this->view->event->getTier() === \Apprecie\Library\Users\Tier::THREE) ? 'checked' : ''; ?> value="<?= \Apprecie\Library\Users\Tier::THREE; ?>" name="tier">&nbsp;<?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::THREE))->getExplanatoryText(); ?></label><br>
                        <label style="font-weight: normal"><input type="radio" <?= ((int)$this->view->event->getTier() === \Apprecie\Library\Users\Tier::TWO) ? 'checked' : ''; ?> value="<?= \Apprecie\Library\Users\Tier::TWO; ?>" name="tier">&nbsp;<?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::TWO))->getExplanatoryText(); ?></label><br>
                        <label style="font-weight: normal"><input type="radio" <?= ((int)$this->view->event->getTier() === \Apprecie\Library\Users\Tier::ONE) ? 'checked' : ''; ?> value="<?= \Apprecie\Library\Users\Tier::ONE; ?>" name="tier">&nbsp;<?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::ONE))->getExplanatoryText(); ?></label><br>
                        <label style="font-weight: normal"><input type="radio" <?= ((int)$this->view->event->getTier() === \Apprecie\Library\Users\Tier::CORPORATE) ? 'checked' : ''; ?> value="<?= \Apprecie\Library\Users\Tier::CORPORATE; ?>" name="tier">&nbsp;<?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::CORPORATE))->getExplanatoryText(); ?></label>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(5)">Previous</a>  <a class="btn btn-primary pull-right" onclick="setStep(7)">Next</a></div>
        </div>
        <div id="step-7" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5>7. Custom Terms</h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label"><?= _g('Purchase Terms'); ?></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="purchase-terms" name="purchase-terms" style="height:150px;"><?= $this->view->event->getPurchaseTerms(); ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label"><?= _g('Attendance Terms'); ?></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="attendance-terms" name="attendance-terms" style="height:150px;"><?= $this->view->event->getAttendanceTerms(); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(6)">Previous</a>  <a class="btn btn-primary pull-right" onclick="setStep(8)">Next</a></div>
        </div>
        <div id="step-8" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5>8. Publishing</h5>
            </div>
            <div class="ibox-content">
                <p>Once you confirm this Arrangement, it will be available for purchase in the requester's Vault for the next 48 hours. It is expected that the Item will be consumed during this period. If 48 hours has passed and the Item has not been consumed, it will return to an arranging state where you will be able to decide if you wish to continue the Arrangement process, confirm the item again, or cancel the Arrangement.</p>
                <div id="publish-warning" class="alert-warning" style="display: none;">
                    Publish options are disabled because your Organisation is not connected to Stripe. To publish priced Vault Items, please contact your Organisation Owner. For now, you may save this item as draft only.
                </div>
                <br />
                <input type="hidden" id="stripe-configured" name="stripe-configured" value="<?= \Organisation::getActiveUsersOrganisation()->getPaymentSettings()->getPublishableKey() != null ? 'true' : 'false' ?>" />
                <input type="radio" checked id="publishstate" name="publishstate" value="save"/><label style="font-weight: normal; margin-left: 5px;">Save - do not confirm</label><br/>
                <input type="radio" class="publish-option" id="publishstate" name="publishstate" value="confirm"/><label class="publish-option" style="font-weight: normal; margin-left: 5px;">Confirm</label><br/>
                <input type="radio" class="publish-option" id="publishstate" name="publishstate" value="confirm-unpublish"/><label class="publish-option" style="font-weight: normal; margin-left: 5px;">Confirm this Arrangement and unpublish the original By Arrangement item</label><br/>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center">
                <a class="btn btn-primary pull-left" onclick="setStep(7)">Previous</a>
                <a class="btn btn-primary pull-right" id="create-btn">Save</a>
            </div>
        </div>
        <div id="step-9" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5>9. Completed</h5>
            </div>
            <div class="ibox-content">
                <p>Great! Your update was successful</p>
            </div>
            <div class="panel-footer" style="height:55px;"><a href="/mycontent/arranged/" class="btn btn-primary pull-right">My By Arrangement Events</a></div>
        </div>
        <?php $this->partial("partials/modals/videofinder"); ?>
        <?php $this->partial("partials/modals/imagecropper"); ?>
        </div>
    </div>
    <div class="col-sm-4">
        <div id="step1" class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5>Help</h5>
            </div>
            <div class="ibox-content">
                <h1>:'( <span style="font-size:14px;">No help available</span> </h1>
            </div>
        </div>
        <div id="step2-help" class="ibox float-e-margins" style="display: none;">
            <div class="ibox-title">
                <h5>Help</h5>
            </div>
            <div class="ibox-content">
                Details innit
            </div>
        </div>
    </div>
</div>
</form>