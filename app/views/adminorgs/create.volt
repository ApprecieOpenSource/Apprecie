<style>
    #role-selection{
        display: none;
    }
    #user-address{
        display: none;
    }
    #address-select-container,#work-address-select-container{
        display: none;
    }
    #error-box{
        display:none;
    }
</style>
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/users/create.js"></script>
<script src="/js/addressing/lookup.js"></script>
<script src="/js/portals/quotas.js"></script>
<script src="/js/users/userLookupWidget.js"></script>

<script>
    $(document).ready(function(){
        setSteps(7);
        setStep(1);
        $('#portal-name').change(function(){
            findPortalQuotas($('#portal-name').val());
        })
    });

    var steps=0;
    var stepper=0;

    function setSteps(numberOfSteps){
        steps=numberOfSteps;
        stepper=(100/steps);
    }
    function setStep(stepID){
        $('#step-progress-bar').css('width',(stepper*stepID)+'%');
        $('.step').hide();
        $('#step-'+stepID).show();
    }

    function findPortalQuotas(portalId){
        clearErrors();
        $.when(getPortalQuota(portalId)).then(function(data){
            $('#role-selection').fadeOut('fast',function(){
                $('#role').empty();
                $('#quota').fadeOut('fast');
                $('#quota-table').empty();
                $('#quota-table').append('<tr><td>Organisation Owner</td><td>'+data.portalAdministratorTotal+'</td><td>'+data.portalAdministratorUsed+'</td><td>'+(data.portalAdministratorTotal-data.portalAdministratorUsed)+'</td></tr>');
                $('#quota-table').append('<tr><td>Manager</td><td>'+data.managerTotal+'</td><td>'+data.managerUsed+'</td><td>'+(data.managerTotal-data.managerUsed)+'</td></tr>');
                $('#quota-table').append('<tr><td>Internal</td><td>'+data.internalMemberTotal+'</td><td>'+data.internalMemberUsed+'</td><td>'+(data.internalMemberTotal-data.internalMemberUsed)+'</td></tr>');
                $('#quota-table').append('<tr><td>Apprecie Supplier</td><td>'+data.apprecieSupplierTotal+'</td><td>'+data.apprecieSupplierUsed+'</td><td>'+(data.apprecieSupplierTotal-data.apprecieSupplierUsed)+'</td></tr>');
                $('#quota-table').append('<tr><td>Affiliated Supplier</td><td>'+data.affiliateSupplierTotal+'</td><td>'+data.affiliateSupplierUsed+'</td><td>'+(data.affiliateSupplierTotal-data.affiliateSupplierUsed)+'</td></tr>');
                $('#quota-table').append('<tr><td>Client</td><td>'+data.memberTotal+'</td><td>'+data.memberUsed+'</td><td>'+(data.memberTotal-data.memberUsed)+'</td></tr>');

                if((data.portalAdministratorTotal-data.portalAdministratorUsed)>0){
                    $('#role').append('<option value="PortalAdministrator">Organisation Owner</option>');
                }
                if((data.managerTotal-data.managerUsed)>0){
                    $('#role').append('<option value="Manager">Manager</option>');
                }
                if((data.internalMemberTotal-data.internalMemberUsed)>0){
                    $('#role').append('<option value="Internal">Internal Member</option>');
                }
                if((data.apprecieSupplierTotal-data.apprecieSupplierUsed)>0){
                    $('#role').append('<option value="ApprecieSupplier">Apprecie Supplier</option>');
                }
                if((data.affiliateSupplierTotal-data.affiliateSupplierUsed)>0){
                    $('#role').append('<option value="AffiliateSupplier">Affiliated Supplier</option>');
                }
                $('#role').append('<option value="Client">Client</option>');

                if($('#role option').size()==0){
                    errors.push('There is no available quota for any user role on this Portal');
                    displayErrors();
                }
                else{
                    $('#role-selection').fadeIn('fast');
                }
            });
        })
    }

    function ShowQuota(){
        $('#quota').toggle('fast');
    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('New Person Wizard'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="progress">
            <div id="step-progress-bar" class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="alert alert-danger" role="alert" id="error-box" name="error-box"></div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <form class="form-horizontal" id="user-form" name="user-form" method="post" enctype="multipart/form-data">
            <div id="step-1" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Portal Details'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div class="form-group">
                        <label for="portalname" class="col-sm-3 control-label"><?= _g('Portal'); ?></label>
                        <div class="col-sm-9">
                            <select class="form-control" id="portal-name" name="portal-name">
                                <option value="none"><?= _g('Please select'); ?></option>
                                <?php foreach($this->view->portals as $portal):
                                    if($portal->getPortalName()!='admin'):?>
                                        <option value="<?= $portal->getPortalId();?>"><?= $portal->getPortalName(); ?></option>
                                <?php endif;
                                endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="" id="role-selection">
                        <div class="form-group">
                            <label for="role" class="col-sm-3 control-label"><?= _g('Role'); ?></label>
                            <div class="col-sm-9">
                                <select id="role" name="role" class="form-control" style="margin-bottom: 15px;"></select>
                                <a onclick="ShowQuota()" class="btn btn-default"><?= _g('Show quotas'); ?></a>
                            </div>
                        </div>
                        <div style="margin: 20px;">
                            <table class="table table-highlight" style="display: none;" id="quota">
                                <thead>
                                <tr>
                                    <th><?= _g('Role'); ?></th>
                                    <th><?= _g('Quota'); ?></th>
                                    <th><?= _g('Used'); ?></th>
                                    <th><?= _g('Available'); ?></th>
                                </tr>
                                </thead>
                                <tbody id="quota-table">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="validateStep(1);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-2" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Personal Details'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div class="form-group">
                        <label for="user-lookup-value" class="col-sm-3 control-label"><?= _g('Owner'); ?></label>
                        <div class="col-sm-9">
                            {{ widget('UserFinderWidget','index') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reference-code" class="col-sm-3 control-label"><?= _g('Reference Code'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="reference-code" name="reference-code" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="portalname" class="col-sm-3 control-label"><?= _g('Title'); ?></label>
                        <div class="col-sm-9">
                            <select id="title" name="title" class="form-control">
                                <option value="Mr"><?= _g('Mr'); ?></option>
                                <option value="Ms"><?= _g('Ms'); ?></option>
                                <option value="Miss"><?= _g('Miss'); ?></option>
                                <option value="Mrs"><?= _g('Mrs'); ?></option>
                                <option value="Mstr"><?= _g('Mstr'); ?></option>
                                <option value="Dr"><?= _g('Dr'); ?></option>
                                <option value="Prof"><?= _g('Prof'); ?></option>
                                <option value="Sir"><?= _g('Sir'); ?></option>
                                <option value="Lord"><?= _g('Lord'); ?></option>
                                <option value="Lady"><?= _g('Lady'); ?></option>
                                <option value="Dame"><?= _g('Dame'); ?></option>
                                <option value="Duke"><?= _g('Duke'); ?></option>
                                <option value="Earl"><?= _g('Earl'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="col-sm-3 control-label"><?= _g('First Name'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="firstname" name="firstname" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-3 control-label"><?= _g('Last Name'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="lastname" name="lastname" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="emailaddress" class="col-sm-3 control-label"><?= _g('Email Address'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="emailaddress" name="emailaddress" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dob" class="col-sm-3 control-label"><?= _g('Date of birth'); ?></label>
                        <div class="col-sm-3">
                            <input type="text" id="dob-day" name="dob-day" class="form-control" placeholder="dd"/>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" id="dob-month" name="dob-month" class="form-control" placeholder="mm"/>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" id="dob-year" name="dob-year" class="form-control" placeholder="yyyy"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?= _g('Gender'); ?></label>
                        <div class="checkbox col-sm-9">
                            <label class="radio-inline">
                                <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="male"> <?= _g('Male'); ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="female"> <?= _g('Female'); ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(1);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(3);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-3" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Home Address'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div class="form-group">
                        <label for="user-lookup" class="col-sm-3 control-label"><?= _g('Address Lookup'); ?></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" id="user-lookup" name="user-lookup" placeholder="Enter Postcode" class="form-control">
                              <span class="input-group-btn">
                                <button class="btn btn-default" id="find-address" onclick="FindAddress();" type="button"><?= _g('Find Address'); ?></button>
                              </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="address-select-container">
                        <label for="address-id" class="col-sm-3 control-label"><?= _g('Select Address'); ?></label>
                        <div class="col-sm-9">
                            <select id="address-id" class="form-control" name="address-id">

                            </select>
                        </div>
                    </div>
                    <div id="user-address">
                        <div class="form-group">
                            <label for="address-line1" class="col-sm-3 control-label"><?= _g('Address Line 1'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="address-line1" name="address-line1" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address-line2" class="col-sm-3 control-label"><?= _g('Address Line 2'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="address-line1" name="address-line1" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address-line3" class="col-sm-3 control-label"><?= _g('Address Line 3'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="address-line1" name="address-line1" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user-city" class="col-sm-3 control-label"><?= _g('City'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="user-city" name="user-city" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user-country" class="col-sm-3 control-label"><?= _g('Country'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="user-country" name="user-country" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user-postcode" class="col-sm-3 control-label"><?= _g('Postcode'); ?></label>
                            <div class="col-sm-9">
                                <input type="text" id="user-postcode" name="user-postcode" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-title" style="border-top:none">
                    <h5><?= _g('Work Address'); ?></h5>
                </div>
                <div class="ibox-content ibox-content-min">
                    <div class="form-group">
                        <label for="work-lookup" class="col-sm-3 control-label"><?= _g('Address Lookup'); ?></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="text" id="work-lookup" name="work-lookup" placeholder="Enter Postcode" class="form-control">
                              <span class="input-group-btn">
                                <button class="btn btn-default" id="find-work-address" onclick="FindWorkAddress();" type="button"><?= _g('Find Address'); ?></button>
                              </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="work-address-select-container">
                        <label for="work-address-id" class="col-sm-3 control-label"><?= _g('Select Address'); ?></label>
                        <div class="col-sm-9">
                            <select id="work-address-id" class="form-control" name="work-address-id">

                            </select>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(2);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(4);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-4" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Communication Preferences'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked id="enabled" name="enabled"> <?= _g('Alerts & notifications for items that are relevant to me'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked id="enabled" name="enabled"> <?= _g('Can receive Invitations to Items'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked id="enabled" name="enabled"> <?= _g("Can receive Suggestions for Items"); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked id="enabled" name="enabled"> <?= _g('Apprecie Partner Communications'); ?>
                            </label>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" checked id="enabled" name="enabled"> <?= _g('Apprecie Updates and Newsletters'); ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(3);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(5);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-5" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Dietary Requirements'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_halal" value="halal"><?= _g('Halal'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_kosher" value="kosher"><?= _g('Kosher'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_no_alcohol" value="no_alcohol"><?= _g('No Alcohol'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_nut_allergies" value="nut_allergies"><?= _g('Nut Allergies'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_seafood" value="no_seafood"><?= _g('No Seafood'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_vegetarian" value="vegetarian"><?= _g('Vegetarian'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_vegan" value="vegan"><?= _g('Vegan'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_no_gluten" value="no_gluten"><?= _g('No Gluten'); ?></label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="diet_no_dairy_or_lactose" value="no_dairy_or_lactose"><?= _g('No Dairy or Lactose'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(4);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="setStep(6);" class="btn btn-primary pull-right">
                        <?= _g('Next'); ?>
                    </button>
                </div>
            </div>
            <div id="step-6" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('Preferred Categories'); ?></h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_apparel_accessories" value="apparel_accessories">Apparel & Accessories</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_art" value="art">&nbsp;Art</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_watches" value="watches">&nbsp;Watches</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_boats_jets" value="boats_jets">&nbsp;Boats &amp; Jets</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_family_focus" value="family_focus">&nbsp;Family-focus</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_music_culture_intellect" value="music_culture_intellect">&nbsp;Music, Culture &amp; Intellect</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_electronics_gadgetry" value="electronics_gadgetry">&nbsp;Electronics &amp; Gadgetry</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_fine_drinking_dining" value="fine_drinking_dining">&nbsp;Fine Drinking &amp; Dining</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_home" value="home">&nbsp;Home</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_jewellery" value="jewellery">&nbsp;Jewellery</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_motoring_cycling" value="motoring_cycling">&nbsp;Motoring &amp; Cycling</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_professional_sports" value="professional_sports">&nbsp;Professional sports</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_property" value="property">&nbsp;Property</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_recreational_sports_adventure" value="recreational_sports_adventure">&nbsp;Recreational sports/Adventure</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_sustainable_ethical_luxury" value="sustainable_ethical_luxury">&nbsp;Sustainable/Ethical luxury</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_travel_special_hotels" value="travel_special_hotels">&nbsp;Travel &amp; Special hotels</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="prestige_services" value="prestige_services">&nbsp;Prestige Services (eg Security, Removals, Lifestyle, etc)</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_financial_services" value="financial_services">&nbsp;Financial Services</label>
                                </div>
                                <div class="checkbox">
                                    <label><input type="checkbox" name="category_other" value="other">&nbsp;Other</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <button type="button" data-loading-text="Loading..." onclick="setStep(5);" class="btn btn-default pull-left">
                        <?= _g('Previous'); ?>
                    </button>
                    <button type="button" data-loading-text="Loading..." onclick="validateStep(6);" class="btn btn-primary pull-right">
                        <?= _g('Create'); ?>
                    </button>
                </div>
            </div>
            <div id="step-7" class="ibox float-e-margins step">
                <div class="ibox-title">
                    <h5><?= _g('User Created!'); ?></h5>
                </div>
                <div class="ibox-content">
                    <p><?= _g('Your new User has been created.'); ?></p>
                    <p><?= _g('If you want this User to be able to access the system you will need to send a sign-up email. Remember, if a User can access the system they will take a space from your quota for that user type.'); ?></p>
                    <p><button class="btn btn-default"><?= _g('Send sign-up Email'); ?></button></p>
                    <p><?= _g('Alternatively you can send them the link below to complete the sign-up process.'); ?></p>
                    <input type="text" class="form-control" value="http://bentley.phalcondev.com/signup/234SD-ASD43"/>
                </div>
                <div class="panel-footer" style="height:55px;">
                    <a href="/adminusers" class="btn btn-primary pull-right">
                        <?= _g('User Management'); ?>
                    </a>
                    <a href="/adminusers/viewuser/123" class="btn btn-primary pull-left">
                        <?= _g('User Profile'); ?>
                    </a>
                </div>
            </div>
        </form>
    </div>
<div class="col-sm-6" id="progress-message">

</div>
</div>
