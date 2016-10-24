<?php
$address=Address::findFirstBy('addressId',$this->view->event->getAddressId());
?>
<script src="/js/validation/items/arranged-arrange.js"></script>
<script src="/js/validation/errors.js"></script>
<script>
    var eventId=<?= $this->view->event->getEventId(); ?>;
    $(document).ready(function(){
        setStep(2);
        <?php if($address InstanceOf Address): ?>
        $('#search-term').val(<?= _j($address->getPostalCode()); ?>);
        $('#country').val(<?= _j($address->getCountryIso3()); ?>);
        $('#address-id').val(<?= _j($address->getId()); ?>);
        <?php endif; ?>
        $("#item-creation-form").submit(function(e){
            e.preventDefault();
        });

    });

    function previewEvent(){
        var form=$('#item-creation-form');
        form.unbind('submit').submit();
        form.attr('target','_blank').attr('method','post').attr('action','/itemcreation/previewevent');
        form.submit();
        form.removeAttr('target').removeAttr('method').removeAttr('action');
        $("#item-creation-form").submit(function(e){
            e.preventDefault();
        });
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
<div class="col-sm-12">
    <div class="alert alert-danger" role="alert" id="error-box" style="display: none;"></div>
</div>
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
                $('#estimate-total-cost').val(total);
            }
        });

        $( ".totalCalc" ).trigger( "change" );
    });
</script>
<div class="row step" id="step-2">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>2. <?= _g('Basic Details'); ?></h5>
                <span class="pull-right" style="font-weight: 600"><?= _g('AMEND - BY ARRANGEMENT EVENT'); ?></span>
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
                    <label for="title" class="col-sm-3 control-label">Start Date & Time<br/>(24 hour format)</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" id="confirmed-startdate" name="confirmed-startdate" value="<?= (new DateTime($this->view->event->getStartDateTime()))->format('d-m-Y'); ?>" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                            <input type="text" value="<?= _ft($this->view->event->getStartDateTime(), true); ?>" id="confirmed-starttime" name="confirmed-starttime" class="form-control" style="max-width: 120px; display: inline" placeholder="HH:MM">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">End Date & Time<br/>(24 hour format)</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" id="confirmed-enddate" name="confirmed-enddate" value="<?= (new DateTime($this->view->event->getEndDateTime()))->format('d-m-Y'); ?>" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                            <input type="text" id="confirmed-endtime" name="confirmed-endtime" value="<?= _ft($this->view->event->getEndDateTime(), true); ?>" class="form-control" style="max-width: 120px; display: inline" placeholder="HH:MM">
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center;"><a class="btn btn-primary pull-right" onclick="validateStep(2)">Next</a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><strong><?=_g("Title: "); ?></strong><?=_g("What is your Item called? The title will appear wherever your Item is, including email messages and invitations, and all around the Vault on the Portal. Be sure to choose a meaningful, well-crafted title that is catchy and descriptive, to represent your vault item and catch people's attention. No more than X characters long (including spaces)."); ?></p>
                <p><strong><?=_g("Short Description: "); ?></strong><?=_g("Your Short Description should be a quick summary of your Item, to give people a flavour of what it is about, including any key highlights. A good Summary will get people really excited and make them want to check out your Item in more detail, so make sure to let them know what's special about your Vault Item and why they don't want to miss it. The Short Description will be found on the Vault quick views, and in messages sent in regards to the Item."); ?></p>
                <p><strong><?=_g("Full Description: "); ?></strong><?=_g("You can use the Full Description to give your Item more detail. This would usually include a full detailed account of what your Item entails, and perhaps an itinerary of the day. State exactly what's included in the price, as this will help you avoid any unnecessary misunderstandings."); ?></p>
                <p><strong><?=_g("Event Start and End Date and Time: "); ?></strong><?=_g("Here you can mark exactly when your event is going to take place and how long it is going to last (in 24 hour format). If your event is over the course of a number of days, please put the starting time of the first day and the closing time of the final day, for the event to span across the whole period."); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-3">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>3. Venue</h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    {{ widget('AddressFinderWidget','index') }}
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
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <?= _g("Now it's time to confirm where the event is going to take place, and whether any catering is included.");?>
                <strong><?= _g("Address: "); ?></strong><?= _g("This is where you need to enter (or double-check if already filled in) the venue address as requested by the requester or as predetermined by yourself. Please make sure that these are correct before continuing.");?>
                <strong><?= _g("Catering: "); ?></strong><?= _g("Will you be providing any catering? If so, please select what type of catering you will be serving during your event (select as many as apply). If you wish to give more description of the catering, use the 'Previous' button to return to the previous page and add more detail to the Full Description.");?>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-4">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>4. Attendance</h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="package-size" class="col-sm-3 control-label">Spaces per Package</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" value="<?= $this->view->event->getPackageSize(); ?>" id="package-size" maxlength="9" name="package-size">
                    </div>
                </div>
                <div class="form-group">
                    <label for="max-units" class="col-sm-3 control-label"><?= _g("Number of Packages"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" value="<?= $this->view->event->getMaxUnits(); ?>" id="max-units" maxlength="9" name="max-units">
                    </div>
                </div>
                <div class="form-group">
                    <label for="currency" class="col-sm-3 control-label">Currency</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="currency" name="currency">
                            <?php foreach($this->view->currencies as $currency): ?>
                            <option  <?php if($this->view->event->getCurrencyId()==$currency->getCurrencyId()){echo 'selected';} ?>  value="<?= $currency->getCurrencyId(); ?>"><?= $currency->getCurrency(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tax-rate" class="col-sm-3 control-label">Sales Tax Rate</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" value="<?= $this->view->event->getTaxablePercent(); ?>" id="tax-rate" maxlength="6" name="tax-rate">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="price-per-unit" class="col-sm-3 control-label"><?= _g('Price per Package'); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" value="<?= $this->view->event->getUnitPrice(true); ?>" id="price-per-unit" maxlength="9" name="price-per-unit">
                    </div>
                </div>
                <div class="ibox-title">
                    <h5>4.1 ROI</h5>
                </div>
                <div class="form-group">
                    <label for="min-units" class="col-sm-3 control-label">Minimum Spaces</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" value="<?= $this->view->event->getMinUnits(); ?>" id="min-units" maxlength="9" name="min-units">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cost-per-unit" class="col-sm-3 control-label">Cost per Attendee</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" value="<?= $this->view->event->getPricePerAttendee(true); ?>" id="cost-per-unit" maxlength="9" name="cost-per-unit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cost-to-deliver" class="col-sm-3 control-label">Static Costs</label>
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
                    <label for="market-value" class="col-sm-3 control-label">Compliance Value</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="market-value" maxlength="9" value="<?= $this->view->event->getMarketValue(true); ?>" name="market-value">
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(3)">Previous</a>  <a class="btn btn-primary pull-right" onclick="validateStep(4)">Next</a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><strong><?=_g("Spaces per Package: "); ?></strong><?=_g("This is the number of spaces you're packaging up into bundles. This could be a single purchase of the whole event (in which case, set the Package Size to match the maximum capacity of your event), individual sales (in which case, set the Spaces per Package to '1'), or grouping tickets together (for example, packages of 10 if your event had 10 attendees per table). Purchasers may buy more than one package if available."); ?></p>
                <p><strong><?=_g("Maximum Packages: "); ?></strong><?=_g("This is the maximum number of packages you are offering to fill the capacity of your event (the maximum number of spaces you are providing to the Portal, broken down into the packages as per Package Size). Your total event capacity should equal Maximum Packages x Spaces per Package; For example if you're catering for 20 people, this could be 2 packages of 10, 4 packages of 5, 1 package of 20, or 20 packages of 1."); ?></p>
                <p><strong><?=_g("Currency: "); ?></strong><?=_g("We currently support transactions in GBP, EUR and USD."); ?></p>
                <p><strong><?=_g("Sales Tax Rate: "); ?></strong><?=_g("If your event is to include a Tax value, please enter the rate here (in %)."); ?></p>
                <p><strong><?=_g("Price per Package: "); ?></strong><?=_g("This is the price you will be charging per Package."); ?></p>
                <p><strong><?=_g("ROI"); ?></strong></p>
                <p><?=_g("The system is designed in order to allow you to measure the success of your efforts. Therefore, in order to measure your ROI and for other reporting purposes, please also provide the following details (These values will only be visible to yourselves and Apprecie):"); ?></p>
                <p><strong><?=_g("Minimum Spaces: "); ?></strong><?=_g("This is the minimum number of attendees that you are happy to proceed with. If the minimum number of participants is not reached, you will have the right to cancel all or parts of the event on a short-term notice."); ?></p>
                <p><strong><?=_g("Cost per Attendee: "); ?></strong><?=_g("How much it is costing you to cater the event per attendee?"); ?></p>
                <p><strong><?=_g("Static Costs: "); ?></strong><?=_g("Static costs are ones that are not determined by your attendance figures, such as venue hire or entertainment"); ?></p>
                <p><strong><?=_g("Estimated Total Cost: "); ?></strong><?=_g("This will automatically calculate based on Static Costs + (Cost per Attendee x (Spaces per Package x Maximum Packages)). This will give you the Estimated Total Cost for you based on your maximum capacity attendance. Following the event, a more accurate calculation will be done on the actual number of attendees."); ?></p>
                <p><strong><?=_g("Compliance Value: "); ?></strong><?=_g("How much you would expect the item to charge if it were placed on the open market or sold elsewhere?"); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-5">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>5. Categories</h5>
            </div>
            <div class="ibox-content">
                <p>Please select a primary interest from below to begin</p>
                <?=(new CategoryPickerWidget('event',array('eventId'=>$this->view->event->getEventId())))->getContent() ?>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(4)">Previous</a> <a class="btn btn-primary pull-right" onclick="setStep(6)">Next</a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("All content in the Vault is grouped into Categories, which make the Items easy to find, and means that we can provide the most relevant content to the user."); ?>
                <p><?= _g("Choose the Categories and Subcategories (one or more) most relevant for your item, to make it easy for the Users to find your listing. To maximize the exposure of your event, you should select all relevant categories that fit your Item's profile."); ?>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-6">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
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
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(5)">Previous</a> <a class="btn btn-primary pull-right" onclick="setStep(7)">Next</a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Here you may set further demographic and type of Item details. This information can help the users decide who would be ideally suited for your Item."); ?></p>
                <p><?= _g("Of particular importance is the Tier setting, as this will determine exactly who can or can't see your Item, based on their wealth level."); ?></p>
                <p><strong><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::CORPORATE))->getText(); ?>:&nbsp;</strong><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::CORPORATE))->getHelpText(); ?></p>
                <p><strong><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::ONE))->getText(); ?>:&nbsp;</strong><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::ONE))->getHelpText(); ?></p>
                <p><strong><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::TWO))->getText(); ?>:&nbsp;</strong><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::TWO))->getHelpText(); ?></p>
                <p><strong><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::THREE))->getText(); ?>:&nbsp;</strong><?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::THREE))->getHelpText(); ?></p>
                <p><?= _g("This tiering information will NOT be visible to the Clients, but will be visible to other users (Managers or other Internal Employees) who manage Client accounts."); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-7">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
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
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(6)">Previous</a> <a class="btn btn-primary pull-right" onclick="setStep(8)">Next</a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Here you will set the Terms and Conditions that will be seen on your Vault Items."); ?></p>
                <p><?= _g("Purchase Terms will be for anyone Purchasing the Event. Here you should specify any extra information you require from guests (e.g. Postcode or Driving License Number) in order to attend. Moreover, if the event is suitable only for the specific group of clients, please add this information here."); ?></p>
                <p><?= _g("Attendance Terms is for any useful information to anyone attending the event as a guest. Here you may provide some more information such as the dress code, the facilities, accessibility information on the venue or anything that is good to know before attending. NOTE - This section is seen by all clients, so do not put any information in here that you would not want them to see (such as required financial status)."); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-8">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>8. Update</h5>
            </div>
            <div class="ibox-content">
                <input type="radio" checked id="publishstate" name="publishstate" value="arranging"/><label style="font-weight: normal; margin-left: 5px;">Save</label><br/>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(7)">Previous</a>  <a class="btn btn-primary pull-right" onclick="validateStep(8)" id="create-btn">Save</a></div>
        </div>
    </div>
</div>
<div class="row step" id="step-9">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>9. Completed</h5>
            </div>
            <div class="ibox-content">
                <p>Great! Your update was successful</p>
            </div>
            <div class="panel-footer" style="height:55px;"> <a href="/vault/arrangedp/{{event.getItemId()}}" class="btn btn-primary pull-right">Return to Arrangement Summary</a></div>
        </div>
    </div>
</div>
</div>
</form>