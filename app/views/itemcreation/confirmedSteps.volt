<script type="text/javascript" src="/js/tinymce/tinymce.min.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script src="/js/validation/items/confirmed.js"></script>
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
                    minDate: new Date(),
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
                    minDate: new Date(),
                    onSelect: function() {
                        var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                        document.getElementById('selected').appendChild(date);
                    }
                });
        var picker3 = new Pikaday(
            {
                field: document.getElementById('confirmed-bookingstart'),
                firstDay: 1,
                format: 'DD/MM/YYYY',
                minDate: new Date(),
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });
        var picker4 = new Pikaday(
            {
                field: document.getElementById('confirmed-bookingend'),
                firstDay: 1,
                format: 'DD/MM/YYYY',
                minDate: new Date(),
                onSelect: function() {
                    var date = document.createTextNode(this.getMoment().format('Do MMMM YYYY') + ' ');
                    document.getElementById('selected').appendChild(date);
                }
            });

        $('#processTitle').html('<?= _g('Vault Item Wizard - Confirmed Event'); ?>');

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
    });
</script>
<div class="row step" id="step-2">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>2. <?= _g('Basic Details'); ?></h5>
                <span class="pull-right" style="font-weight: 600"><?= _g('CREATE - CONFIRMED EVENT'); ?></span>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Title'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="confirmed-title" id="confirmed-title">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Short Description'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="confirmed-short-description" name="confirmed-short-description">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Full Description'); ?></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="confirmed-description" name="confirmed-description" style="height:150px;"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Booking Start Date'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" id="confirmed-bookingstart" name="confirmed-bookingstart" value="" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Booking End Date'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" id="confirmed-bookingend" name="confirmed-bookingend" value="" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('Start Date & Time'); ?><br/><?= _g('(24 hour format)'); ?></label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" id="confirmed-startdate" name="confirmed-startdate" value="" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                            <input type="text" value="" id="confirmed-starttime" name="confirmed-starttime" class="form-control" style="max-width: 120px; display: inline" placeholder="HH:MM">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">*&nbsp;<?= _g('End Date & Time'); ?><br/><?= _g('(24 hour format)'); ?></label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" id="confirmed-enddate" name="confirmed-enddate" value="" class="form-control" style="max-width: 120px; display: inline" placeholder="DD/MM/YYYY">
                            <input type="text" id="confirmed-endtime" name="confirmed-endtime" value="" class="form-control" style="max-width: 120px; display: inline" placeholder="HH:MM">
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(1)"><?= _g('Previous'); ?></a>  <a class="btn btn-primary pull-right" onclick="validateStep(2)"><?= _g('Next'); ?></a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g('Help');?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Let's start with some basic details:"); ?></p>
                <p><strong><?= _g('Title'); ?></strong> - <?= _g("What is your Item called? The title will appear wherever your Item is, including email messages and invitations, and all around the Vault on the Portal. Be sure to choose a meaningful, well-crafted title that is catchy and descriptive, to represent your vault item and catch people's attention. No more than 100 characters long (including spaces)."); ?></p>
                <p><strong><?= _g('Short Description'); ?></strong> - <?= _g("Your Short Description should be a quick summary of your Item, to give people a flavour of what it is about, including any key highlights. A good Summary will get people really excited and make them want to check out your Item in more detail, so make sure to let them know what's special about your Vault Item and why they don't want to miss it. The Short Description will be found on the Vault quick views, and in messages sent in regards to the Item."); ?></p>
                <p><strong><?= _g('Full Description'); ?></strong> – <?= _g("You can use the Full Description to give your Item more detail. This would usually include a full detailed account of what your Item entails, and perhaps an itinerary of the day. State exactly what's included in the price, as this will help you avoid any unnecessary misunderstandings."); ?></p>
                <p><strong><?= _g('Booking Start and End Dates'); ?></strong> – <?= _g("Now it's time to set the listing duration. Please select when do you wish to open and close the bookings for this vault item. It's good to set the Booking End Date with enough time before the Event Start Date so that you can prepare yourself to accommodate the confirmed number of attendees. Booking opens once the Start Date is reached, and before then the Item will be marked as 'Coming soon'. Once the End Date is reached, the Item will no longer be visible within the Vault."); ?></p>
                <p><strong><?= _g('Event Start and End Date and Time'); ?></strong> – <?= _g("Here you can mark exactly when your event is going to take place and how long it is going to last (in 24 hour format). If your event is over the course of a number of days, please put the starting time of the first day and the closing time of the final day, for the event to span across the whole period."); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-3">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>3. <?= _g("Venue"); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <?= (new AddressFinderWidget('index', array('showFieldMarkings' => true)))->getContent(); ?>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label"><?= _g("Catering"); ?></label>
                    <div class="col-sm-9">
                        <div style="float:left;"><input type="checkbox" value="breakfast" id="catering-breakfast" name="catering-breakfast"><label class="checkbox" style="font-weight:normal; display: inline; margin-right: 10px" for="catering-breakfast"> <?= _g("Breakfast"); ?></label></div>
                        <div style="float:left;"><input type="checkbox" value="lunch" id="catering-lunch" name="catering-lunch"> <label class="checkbox" for="catering-lunch" style="font-weight:normal; display: inline; margin-right: 10px"> <?= _g("Lunch");?></label></div>
                        <div style="float:left;"><input type="checkbox" value="dinner" id="catering-dinner" name="catering-dinner"><label class="checkbox" for="catering-dinner" style="font-weight:normal; display: inline; margin-right: 10px"> <?= _g("Dinner"); ?></label></div>
                        <div style="float:left;"><input type="checkbox" value="refreshments" id="catering-refresh" name="catering-refresh"><label class="checkbox" for="catering-refresh" style="font-weight:normal; display: inline; margin-right: 10px"> <?= _g("Light Refreshments/Drinks"); ?></label></div>
                        <div style="float:left;"><input type="checkbox" value="tea" id="catering-tea" name="catering-tea"><label class="checkbox" for="catering-tea" style="font-weight:normal; display: inline; margin-right: 10px"> <?= _g("Afternoon Tea"); ?></label></div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(2)"><?= _g("Previous"); ?></a>  <a class="btn btn-primary pull-right" onclick="validateStep(3)"><?= _g("Next"); ?></a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Now it's time to tell your guests where the event is going to take place, and whether any catering is included."); ?></p>
                <p><strong><?= _g("Address"); ?></strong> - <?= _g("Find the address of the venue by using the Search tool to look-up partial or full addresses. You can search on post-code, or street numbers and names. Then just select the relevant address from the list below. If you make a mistake, just re-type into the Search tool and Search again."); ?></p>
                <p><strong><?= _g("Catering"); ?></strong> – <?= _g("Will you be providing any catering? If so, please select what type of catering you will be serving during your event (select as many as apply). If you wish to give more description of the catering, use the 'Previous' button to return to the previous page and add more detail to the Full Description."); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-4">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>4. <?= _g("Attendance"); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="package-size" class="col-sm-3 control-label">*&nbsp;<?= _g("Spaces per Package"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" id="package-size" maxlength="9" name="package-size">
                    </div>
                </div>
                <div class="form-group">
                    <label for="max-units" class="col-sm-3 control-label">*&nbsp;<?= _g("Maximum Packages"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" id="max-units" maxlength="9" name="max-units">
                    </div>
                </div>
                <div class="form-group">
                    <label for="currency" class="col-sm-3 control-label">*&nbsp;<?= _g("Currency"); ?></label>
                    <div class="col-sm-4">
                        <select class="form-control" id="currency" name="currency">
                            <?php foreach($this->view->currencies as $currency): ?>
                                <option value="<?= $currency->getCurrencyId(); ?>"><?= $currency->getCurrency(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tax-rate" class="col-sm-3 control-label">*&nbsp;<?= _g("Sales Tax Rate"); ?></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="tax-rate" maxlength="6" name="tax-rate">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="price-per-unit" class="col-sm-3 control-label">*&nbsp;<?= _g("Price per Package"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="price-per-unit" maxlength="9" name="price-per-unit">
                    </div>
                </div>
                <div class="ibox-title">
                    <h5>4.1 <?= _g("ROI"); ?></h5>
                </div>
                <div class="form-group">
                    <label for="min-units" class="col-sm-3 control-label">*&nbsp;<?= _g("Minimum Spaces"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="min-units" maxlength="9" name="min-units">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cost-per-unit" class="col-sm-3 control-label">*&nbsp;<?= _g("Cost per Attendee"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" id="cost-per-unit" maxlength="9" name="cost-per-unit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="cost-to-deliver" class="col-sm-3 control-label">*&nbsp;<?= _g("Static Costs"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control totalCalc" id="cost-to-deliver" maxlength="9" name="cost-to-deliver">
                    </div>
                </div>
                <div class="form-group">
                    <label for="estimate-total-cost" class="col-sm-3 control-label"><?= _g("Estimated Total Cost"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="estimate-total-cost" readonly="readonly" maxlength="9" name="estimate-total-cost">
                    </div>
                </div>
                <div class="form-group">
                    <label for="market-value" class="col-sm-3 control-label">*&nbsp;<?= _g("Compliance Value"); ?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="market-value" maxlength="9" name="market-value">
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(3)"><?= _g("Previous"); ?></a>  <a class="btn btn-primary pull-right" onclick="validateStep(4)"><?= _g("Next"); ?></a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <h5><?= _g("Attendance");?></h5>
                <p><strong><?= _g("Spaces per Package"); ?></strong> – <?= _g("This is the number of spaces you're packaging up into bundles. This could be a single purchase of the whole event (in which case, set the Package Size to match the maximum capacity of your event), individual sales (in which case, set the Spaces per Package to '1'), or grouping tickets together (for example, packages of 10 if your event had 10 attendees per table). Purchasers may buy more than one package if available."); ?></p>
                <p><strong><?= _g("Maximum Packages"); ?></strong> - <?= _g("This is the maximum number of packages you are offering to fill the capacity of your event (the maximum number of spaces you are providing to the Portal, broken down into the packages as per Package Size). Your total event capacity should equal Maximum Packages x Spaces per Package; For example if you're catering for 20 people, this could be 2 packages of 10, 4 packages of 5, 5 packages of 4, 1 package of 20, or 20 packages of 1."); ?></p>
                <p><strong><?= _g("Currency"); ?></strong> - <?= _g("We currently support transactions in GBP, EUR and USD."); ?></p>
                <p><strong><?= _g("Sales Tax Rate"); ?></strong> - <?= _g("If your event is to include a Tax value, please enter the rate here (in %)."); ?></p>
                <p><strong><?= _g("Price per Package"); ?></strong> – <?= _g("This is the price you will be charging per Package."); ?></p>
                <h5><?= _g("ROI"); ?></h5>
                <p><?= _g("The system is designed in order to allow you to measure the success of your efforts. Therefore, in order to measure your ROI and for other reporting purposes, please also provide the following details (these values will only be visible to yourselves and Apprecie):"); ?></p>
                <p><strong><?= _g("Minimum Spaces"); ?></strong> - <?= _g("This is the minimum number of attendees that you are happy to proceed with. If the minimum number of participants is not reached, you will have the right to cancel all or parts of the event on a short-term notice."); ?></p>
                <p><strong><?= _g("Cost per Attendee"); ?></strong> - <?= _g("How much it is costing you to cater the event per attendee?"); ?></p>
                <p><strong><?= _g("Static Costs"); ?></strong> - <?= _g("Static costs are ones that are not determined by your attendance figures, such as venue hire or entertainment."); ?></p>
                <p><strong><?= _g("Estimated Total Cost"); ?></strong> - <?= _g("This will automatically calculate based on Static Costs + (Cost per Attendee x (Spaces per Package x Maximum Packages)). This will give you the Estimated Total Cost for you based on your maximum capacity attendance. Following the event, a more accurate calculation will be done on the actual number of attendees."); ?></p>
                <p><strong><?= _g("Compliance Value"); ?></strong> - <?= _g("How much you would expect the item to charge if it were placed on the open market or sold elsewhere?"); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-5">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>5. <?= _g("Categories"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Please select a primary interest from below to begin"); ?></p>
                {{ widget('CategoryPickerWidget','index') }}
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(4)"><?= _g("Previous"); ?></a> <a class="btn btn-primary pull-right" onclick="setStep(6)"><?= _g("Next"); ?></a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("All content in the Vault is grouped into Categories, which make the Items easy to find, and means that we can provide the most relevant content to the User."); ?></p>
                <p><?= _g("Choose the Categories and Subcategories (one or more) most relevant for your item, to make it easy for the Users to find your listing. To maximize the exposure of your event, you should select all relevant categories that fit your Item's profile."); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-6">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>6. <?= _g("Goals"); ?></h5>
            </div>
            <div class="ibox-content">
                <label style="font-weight: normal"><input type="checkbox" value="Product Launch" name="goal[]"> <?= _g("Product Launch"); ?></label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Charity" name=goal[]"> <?= _g("Charity"); ?></label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Education" name="goal[]"> <?= _g("Education"); ?></label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="General Company Promotion" name="goal[]"> <?= _g("General Company Promotion"); ?></label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Networking" name="goal[]"> <?= _g("Networking"); ?></label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Investment Related" name="goal[]"> <?= _g("Investment Related"); ?></label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Passive Event (party/talk/performance)" name="goal[]"> <?= _g("Passive Event (party/talk/performance)"); ?></label><br/>
                <label style="font-weight: normal"><input type="checkbox" value="Active Event (adventure/activity/doing)" name="goal[]"> <?= _g("Active Event (adventure/activity/doing)"); ?></label>
            </div>
            <div class="ibox-title" style="border-top: none;">
                <h5><?= _g("Target Demographics"); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-4">
                        <h5>*&nbsp;<?= _g("Gender"); ?></h5>
                        <label style="font-weight: normal"><input name="gender" checked type="radio" value="mixed"> <?= _g("Mixed"); ?></label><br/>
                        <label style="font-weight: normal"><input name="gender" type="radio" value="male"> <?= _g("Male"); ?></label><br/>
                        <label style="font-weight: normal"><input name="gender" type="radio" value="female"> <?= _g("Female"); ?></label><br/>
                    </div>
                    <div class="col-sm-4">
                        <h5><?= _g("Age"); ?></h5>
                        <label style="font-weight: normal"><input type="checkbox" value="18-34" name="age18to34"> 18-34 </label><br/>
                        <label style="font-weight: normal"><input type="checkbox" value="35-65" name="age34to65"> 35-65</label><br/>
                        <label style="font-weight: normal"><input type="checkbox" value=">65" name="age65over"> <?= _g("Over"); ?> 65</label>
                    </div>
                    <div class="col-sm-4">
                        <h5>*&nbsp;<?= _g("Member Tier"); ?></h5>
                        <label style="font-weight: normal"><input type="radio" value="<?= \Apprecie\Library\Users\Tier::THREE; ?>" name="tier">&nbsp;<?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::THREE))->getExplanatoryText(); ?></label><br>
                        <label style="font-weight: normal"><input type="radio" value="<?= \Apprecie\Library\Users\Tier::TWO; ?>" name="tier">&nbsp;<?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::TWO))->getExplanatoryText(); ?></label><br>
                        <label style="font-weight: normal"><input type="radio" value="<?= \Apprecie\Library\Users\Tier::ONE; ?>" name="tier">&nbsp;<?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::ONE))->getExplanatoryText(); ?></label><br>
                        <label style="font-weight: normal"><input type="radio" checked value="<?= \Apprecie\Library\Users\Tier::CORPORATE; ?>" name="tier">&nbsp;<?= (new \Apprecie\Library\Users\Tier(\Apprecie\Library\Users\Tier::CORPORATE))->getExplanatoryText(); ?></label>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(5)"><?= _g("Previous"); ?></a> <a class="btn btn-primary pull-right" onclick="setStep(7)"><?= _g("Next");?></a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Here you may set further demographic and type of Item details. This information can help the users decide who would be ideally suited for your Item."); ?></p>
                <p><?= _g("Of particular importance is the Tier setting, as this will determine exactly who can or can't see your Item, based on their wealth level*"); ?></p>
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
                <h5>7. <?= _g("Custom Terms"); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label"><?= _g('Purchase Terms'); ?></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="purchase-terms" name="purchase-terms" style="height:150px;"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label"><?= _g('Attendance Terms'); ?></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" id="attendance-terms" name="attendance-terms" style="height:150px;"></textarea>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(6)"><?= _g("Previous"); ?></a> <a class="btn btn-primary pull-right" onclick="setStep(8)"><?= _g("Next"); ?></a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Here you will set the Terms and Conditions that will be seen on your Vault Items."); ?></p>
                <p><strong><?= _g("Purchase Terms"); ?></strong> <?= _g("will be for anyone Purchasing the Event. Here you should specify any extra information you require from guests (e.g. Postcode or Driving License Number) in order to attend. Moreover, if the event is suitable only for the specific group of clients, please add this information here."); ?></p>
                <p><strong><?= _g("Attendance Terms"); ?></strong> <?= _g("is for any useful information to anyone attending the event as a guest. Here you may provide some more information such as the dress code, the facilities, accessibility information on the venue or anything that is good to know before attending. NOTE - This section is seen by all clients, so do not put any information in here that you would not want them to see (such as required financial status)."); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-8">
    <div class="col-sm-8">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>8. <?= _g("Publishing"); ?></h5>
            </div>
            <div class="ibox-content">
                <div id="publish-warning" class="alert-warning" style="display: none;">
                    Publish options are disabled because your Organisation is not connected to Stripe. To publish priced Vault Items, please contact your Organisation Owner. For now, you may save this item as draft only.
                </div>
                <br />
                <input type="hidden" id="stripe-configured" name="stripe-configured" value="<?= \Organisation::getActiveUsersOrganisation()->getPaymentSettings()->getPublishableKey() != null ? 'true' : 'false' ?>" />
                <input type="radio" checked id="publishstate" name="publishstate" value="draft"/><label style="font-weight: normal; margin-left: 5px;"><?= _g("Save as Draft"); ?></label><br/>
                <?php if(Organisation::getActiveUsersOrganisation()->getIsAffiliateSupplierOf()): ?>
                    <input type="radio" id="publishstate" name="publishstate" class="publish-option" value="parent"/><label class="publish-option" style="font-weight: normal; margin-left: 5px;"><?= _g("Publish to Parent Organisation"); ?></label><br/>
                <?php else: ?>
                    <?php
                    $auth= new \Apprecie\Library\Security\Authentication();
                    $user=$auth->getAuthenticatedUser();

                    switch($user->getActiveRole()->getName()){
                        case "Manager":
                            ?>
                            <input type="radio" id="publishstate" class="publish-option" name="publishstate" value="organisation"/><label class="publish-option" style="font-weight: normal; margin-left: 5px;"><?= _g("Publish to this Organisation"); ?></label><br/>
                            <?php
                            break;
                        case "Internal":
                            ?>
                            <!--<input type="radio" id="publishstate" name="publishstate" value="organisation"/><label style="font-weight: normal; margin-left: 5px;">Publish to this Organisation</label><br/>-->
                            <input type="radio" id="publishstate" class="publish-option" name="publishstate" value="vault"/><label class="publish-option" style="font-weight: normal; margin-left: 5px;"><?= _g('Publish to my Vault'); ?></label><br/>
                            <?php
                            break;
                        case "ApprecieSupplier":
                            ?>
                            <input type="radio" id="publishstate" class="publish-option" name="publishstate" value="curation"/><label class="publish-option" style="font-weight: normal; margin-left: 5px;"><?= _g("Send to Apprecie for curation"); ?></label><br/>
                            <?php
                            break;
                        case "AffiliateSupplier":
                            ?>
                            <input type="radio" id="publishstate" class="publish-option" name="publishstate" value="parent"/><label class="publish-option" style="font-weight: normal; margin-left: 5px;"><?= _g("Publish to Parent Organisation"); ?></label><br/>
                            <?php
                            break;
                    }
                    ?>
                <?php endif; ?>
            </div>
            <div class="panel-footer" style="height:55px; text-align: center"><a class="btn btn-primary pull-left" onclick="setStep(7)"><?= _g("Previous"); ?></a> <a class="btn btn-primary pull-right" id="create-btn" onclick="validateStep(8)"><?= _g("Create"); ?></a></div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="ibox float-e-margins hidden-xs">
            <div class="ibox-title">
                <h5><?= _g("Help"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Depending on the nature of your role, you may see a variety of different options here."); ?></p>
                <p><strong><?= _g("PLEASE NOTE:"); ?></strong> <?= _g("Your Item will only be saved AFTER you action one of the options below. Media can be added to your Item after one of the following options has been chosen."); ?></p>
                <p><strong><?= _g("Save as Draft"); ?></strong> - <?= _g("If you wish to continue working on this Vault Item at a later stage, you can save it as a Draft an get back to it later. Once saved as Draft, this item can be accessed via the 'My Content' section of your Portal."); ?></p>
                <p><strong><?= _g("PLEASE NOTE:"); ?></strong> <?= _g("Once an Item has been sent for curation or published to an Organisation, you will have to unpublish the Item and remove it from all recipients' Vaults in order to make any further amendments. If you are in doubt, please choose Save as Draft above."); ?></p>
                <?php
                $auth= new \Apprecie\Library\Security\Authentication();
                $user=$auth->getAuthenticatedUser();
                switch($user->getActiveRole()->getName()){
                    case "Manager":
                        ?>
                        <p><strong><?= _g("Publish to My Organisation"); ?></strong> - <?= _g("If you are creating an Internal Item to only be used within your own Organisation,  and you think the details of this Item are now complete and ready to be published, you can publish this Item directly out into your own Vault. Once you publish this Item, it will be available directly to you and all other users at the Management level of your Organisation. Once there, you may then choose to curate it down to other users, such as your Clients, using the 'Share' options found on the Item Profile."); ?></p>                        <?php
                        break;
                    case "Internal":
                        ?>
                        <p><strong><?= _g("Publish to My Organisation"); ?></strong> - <?= _g("If you are creating an Internal Item to only be used within your own Organisation,  and you think the details of this Item are now complete and ready to be published, you can publish this Item directly out into your own Vault. Once you publish this Item, it will be available directly to you and all other users at the Management level of your Organisation. Once there, you may then choose to curate it down to other users, such as your Clients, using the 'Share' options found on the Item Profile."); ?></p>
                        <?php
                        break;
                    case "ApprecieSupplier":
                        ?>
                        <p><strong><?= _g("Send to Apprecie for curation"); ?></strong> – <?= _g("If you are creating an Item to be sent out to other Portals and Organisations, and you think the details of this Item are now complete and ready to be published, you can send this Item to Apprecie for curation. Your activation request will be passed to us at Apprecie for review, and you will be informed if the Item is approved or rejected (with appropriate feedback). Once approved, Apprecie will forward the item to the appropriate recipients."); ?></p>
                        <?php
                        break;
                    case "AffiliateSupplier":
                        ?>
                        <p><strong><?= _g("Send to Apprecie for curation"); ?></strong> – <?= _g("If you are creating an Item to be sent out to other Portals and Organisations, and you think the details of this Item are now complete and ready to be published, you can send this Item to Apprecie for curation. Your activation request will be passed to us at Apprecie for review, and you will be informed if the Item is approved or rejected (with appropriate feedback). Once approved, Apprecie will forward the item to the appropriate recipients."); ?></p>
                        <p><strong><?= _g("Publish to My Organisation"); ?></strong> - <?= _g("If you are creating an Internal Item to only be used within your own Organisation,  and you think the details of this Item are now complete and ready to be published, you can publish this Item directly out into your own Vault. Once you publish this Item, it will be available directly to you and all other users at the Management level of your Organisation. Once there, you may then choose to curate it down to other users, such as your Clients, using the 'Share' options found on the Item Profile."); ?></p>
                        <?php
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</div>
<div class="row step" id="step-9">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>9. <?= _g("Completed"); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g("Great! Your Event has been created"); ?></p>
                <p><?= _g("To add media to your event such as images and videos please go to the Media Manager below"); ?></p>
            </div>
            <div class="panel-footer" style="height:55px;"> <a href="/mycontent/media" id="media-manager" class="btn btn-default pull-left"><?= _g("Media Manager"); ?></a><a href="/mycontent/events" class="btn btn-primary pull-right"><?= _g("Go to My Events"); ?></a></div>
        </div>
    </div>
</div>