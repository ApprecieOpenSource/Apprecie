<style>
    #error-box{
        display:none;
    }
    #address-select-container{
        display: none;
    }
</style>
<script>
    $(document).ready(function(){
        setSteps(4);
        setStep(1);
        $("#create-portal").submit(function(e){
            e.preventDefault();
        });
    });
</script>
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/validation/portals/create.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('Portal Creation Wizard'); ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="alert alert-danger" role="alert" id="error-box" name="error-box"></div>
    </div>
</div>
<form method="post" enctype="multipart/form-data" autocomplete="off" action="/portals/create" id="create-portal" name="create-portal" class="form-horizontal">
    <?php echo Apprecie\Library\Security\CSRFProtection::csrf(); ?>
    <div class="row">
    <div class="col-sm-6">
        <div id="step-1" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5><?= _g('Portal Details'); ?></h5>
            </div>
            <div class="ibox-content">
                <?php if(isset($this->view->messages) and count($this->view->messages)!=0): ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Oops!</strong>
                        <?php foreach($this->view->messages as $message):
                            echo '<br/>'.$message;
                        endforeach ?>
                    </div>
                <?php endif ?>
                <div class="form-group">
                    <label for="portal-name" class="col-sm-3 control-label"><?= _g('Portal Name'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="portal-name" name="portal-name" <?php if($this->request->getPost('portal-name')){ echo 'value="'.$this->request->getPost('portal-name').'"';} ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="portal-subdomain" class="col-sm-3 control-label"><?= _g('Portal Subdomain'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="portal-subdomain" name="portal-subdomain" <?php if($this->request->getPost('portal-subdomain')){ echo 'value="'.$this->request->getPost('portal-subdomain').'"';} ?>>
                    </div>
                </div>
                <div class="form-group">
                    <label for="tag" class="col-sm-3 control-label"><?= _g('Edition'); ?></label>
                    <div class="col-sm-9">
                        <select class="form-control" id="tag" name="tag">
                            <option value="FreemiumPro">Freemium Pro</option>
                            <option value="Professional">Professional</option>
                            <option value="Enterprise">Enterprise</option>
                            <option value="VIP">VIP</option>
                            <option value="Supplier">Supplier</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="account-manager" class="col-sm-3 control-label"><?= _g('Account Manager'); ?></label>
                    <div class="col-sm-9">
                        <select class="form-control" id="account-manager" name="account-manager">
                                <?php foreach($this->view->accountManagers as $user):
                                    if(!isset($user->firstname)){$user->firstname='Unknown';}
                                    if(!isset($user->lastname)){$user->lastname='Unknown';}?>
                                    <option value="<?= $user->getUserId(); ?>"><?= $user->getFirstName().' '.$user->getLastName(); ?></option>
                                <?php endforeach ?>
                        </select>
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
                <h5><?= _g('Quotas'); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="quota-portal-administrators" class="col-sm-6 control-label"><?= _g('Organisation Owners'); ?></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="quota-portal-administrators" name="quota-portal-administrators" value="0"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quota-managers" class="col-sm-6 control-label"><?= _g('Managers'); ?></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="quota-managers" name="quota-managers" value="0"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quota-internal-members" class="col-sm-6 control-label"><?= _g('Internal Members'); ?></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="quota-internal-members" name="quota-internal-members" value="0"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quota-apprecie-suppliers" class="col-sm-6 control-label"><?= _g('Apprecie Suppliers'); ?></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="quota-apprecie-suppliers" name="quota-apprecie-suppliers" value="0"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quota-affiliate-suppliers" class="col-sm-6 control-label"><?= _g('Affiliated Suppliers'); ?></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="quota-affiliate-suppliers" name="quota-affiliate-suppliers" value="0"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quota-members" class="col-sm-6 control-label"><?= _g('Client Members'); ?></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="quota-members" name="quota-members" value="0"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="quota-family-members" class="col-sm-6 control-label"><?= _g('Family Members Per Client'); ?></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="quota-family-members" name="quota-family-members" value="5"/>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="quota-commission" class="col-sm-6 control-label"><?= _g('Commission'); ?></label>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="quota-commission" name="quota-commission" value="0"/>
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="height:55px;">
                <button type="button" data-loading-text="Loading..." onclick="setStep(1);" class="btn btn-primary pull-left">
                    <?= _g('Previous'); ?>
                </button>
                <button type="button" data-loading-text="Loading..." onclick="validateStep(2);" class="btn btn-primary pull-right">
                    <?= _g('Next'); ?>
                </button>
            </div>
        </div>
        <div id="step-3" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5><?= _g('Primary Contact'); ?></h5>
            </div>
            <div class="ibox-content">
                <div class="form-group">
                    <label for="contact-firstname" class="col-sm-3 control-label"><?= _g('First Name'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" maxlength="45" id="contact-firstname" name="contact-firstname"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact-lastname" class="col-sm-3 control-label"><?= _g('Last Name'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" maxlength="45" id="contact-lastname" name="contact-lastname"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact-telephone" class="col-sm-3 control-label"><?= _g('Telephone'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" maxlength="15" id="contact-telephone" name="contact-telephone"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact-mobile" class="col-sm-3 control-label"><?= _g('Mobile (optional)'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" maxlength="15" id="contact-mobile" name="contact-mobile"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="contact-email" class="col-sm-3 control-label"><?= _g('Email'); ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" maxlength="45" id="contact-email" name="contact-email"/>
                    </div>
                </div>
                {{ widget('AddressFinderWidget','index') }}
            </div>
            <div class="panel-footer" style="height:55px;">
                <button type="button" data-loading-text="Loading..." onclick="setStep(2);" class="btn btn-primary pull-left">
                    <?= _g('Previous'); ?>
                </button>
                <button type="button" id="create-portal-button" data-loading-text="Loading..." onclick="validateStep(3);" class="btn btn-primary pull-right">
                    <?= _g('Create'); ?>
                </button>
            </div>
        </div>
        <div id="step-4" class="ibox float-e-margins step">
            <div class="ibox-title">
                <h5><?= _g('Portal Created!'); ?></h5>
            </div>
            <div class="ibox-content">
                <p><?= _g('Your new Portal has been created and is ready to use.'); ?></p>
                <p><?= _g('In order to create Managers and Members, edit the look and feel or allow the customer to use the Portal you should first create an Organisation Owner for this Portal.'); ?></p>
            </div>
            <div class="panel-footer" style="height:55px;">
                <a href="/portals" class="pull-right btn btn-primary"><?= _g('Return to Portal Management'); ?></a>
            </div>
        </div>
    </div>
</div>
</form>