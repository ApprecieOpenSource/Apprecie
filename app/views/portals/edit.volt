<?php
$address=Address::findFirstBy('addressId',$this->view->contact->getAddressId());
?>
<script src="/js/validation/errors.js"></script>
<script src="/js/validation/general.js"></script>
<script src="/js/addressing/lookupWidget.js"></script>
<script src="/js/raw/library/portalEdit.js"></script>
<script>
    $(document).ready(function(){
        <?php if($address!=null): ?>
        $('#search-term').val(<?= _j($address->getPostalCode()); ?>);
        $('#country').val(<?= _j($address->getCountryIso3()); ?>);
        $('#address-id').val(<?= _j($address->getId()); ?>);
        $('#selected-address-value').html(<?= _j($address->getLine1() .' ' . $address->getLine2() .' '. $address->getPostalCode(), true); ?>);
        $('#selected-address').show();
        <?php endif; ?>
    })

    function PortalEdit(){
        var edit=new EditPortal(<?= $this->view->portal->getPortalId();?>);
        edit.setSubSomain($('#portalsubdomain').val());
        edit.setPortalName($('#portalname').val());
        edit.setContactName($('#contactname').val());
        edit.setTelephoneNumber($('#telephonenumber').val());
        edit.setMobileNumber($('#mobilenumber').val());
        edit.setEmailAddress($('#emailaddress').val());
        edit.setAccountManager($('#account-manager').val());
        edit.setEdition($('#edition').val());
        edit.setAddressId($('#address-id').val());

        if($('#enabled').is(':checked')){
            edit.setEnabled(1);
        }
        else{
            edit.setEnabled(0);
        }
        edit.validate();

    }
</script>
<div class="row">
    <div class="col-sm-12">
        <h2>Edit Portal - <?= $this->view->portal->getPortalName(); ?></h2>
    </div>
</div>
<div class="alert alert-danger" id="error-box" style="display:none;" role="alert"></div>
<div class="alert alert-success" id="success-box" style="display:none;" role="alert"></div>

<?php if(isset($this->view->success)): ?>
    <div class="alert alert-success" role="alert">
        <?= $this->view->success; ?>
    </div>
<?php endif ?>
<form method="post" class="form-horizontal" autocomplete="off" enctype="multipart/form-data" action="/portals/edit/<?= $this->view->portal->getPortalId(); ?>">
<div class="row">
<div class="col-sm-6">
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>Portal Details</h5>
    </div>
    <div class="ibox-content">
            <div class="form-group">
                <label for="portalname" class="col-sm-3 control-label">Portal Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="portalname" id="portalname" value="<?= $this->view->portal->getPortalName(); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="portalsubdomain" class="col-sm-3 control-label">Portal Subdomain</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="portalsubdomain" id="portalsubdomain" value="<?= $this->view->portal->getPortalSubDomain(); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="tag" class="col-sm-3 control-label">Tag</label>
                <div class="col-sm-9">
                    <select class="form-control" id="edition" name="edition">
                        <option value="FreemiumPro" <?php if($this->view->portal->getEdition()=="FreemiumPro"){echo 'selected';} ?>>Freemium Pro</option>
                        <option value="Professional" <?php if($this->view->portal->getEdition()=="Professional"){echo 'selected';} ?>>Professional</option>
                        <option value="Enterprise" <?php if($this->view->portal->getEdition()=="Enterprise"){echo 'selected';} ?>>Enterprise</option>
                        <option value="VIP" <?php if($this->view->portal->getEdition()=="VIP"){echo 'selected';} ?>>VIP</option>
                        <option value="Supplier" <?php if($this->view->portal->getEdition()=="Supplier"){echo 'selected';} ?>>Supplier</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="contact-email" class="col-sm-3 control-label">Account Manager</label>
                <div class="col-sm-9">
                    <select class="form-control" id="account-manager" name="account-manager">
                        <?php foreach($this->view->accountManagers as $user):?>
                            <option <?php if($user->getUserId()==$this->view->portal->getAccountManager()){echo 'selected';} ?> value="<?= $user->getUserId(); ?>"><?= $user->getFirstName().' '.$user->getLastName(); ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="checkbox col-sm-9 col-sm-offset-3">
                    <label>
                        <input type="checkbox" id="enabled" name="enabled" value="1" <?php if($this->view->portal->getSuspended()==0){echo 'checked';} ?>> Online
                    </label>
                </div>
            </div>
    </div>
</div>
</div>
<div class="col-sm-6">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Primary Contact</h5>
        </div>
        <div class="ibox-content">
            <div class="form-group">
                <label for="contact-name" class="col-sm-3 control-label">Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="contactname"  value="<?=  $this->view->contact->getContactNameAndTitle(); ?>" name="contactname"/>
                </div>
            </div>
            <div class="form-group">
                {{ widget('AddressFinderWidget','index') }}
            </div>
            <div class="form-group">
                <label for="contact-telephone" class="col-sm-3 control-label">Telephone</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="telephonenumber" value="<?= $this->view->contact->getTelephone() ?>" name="telephonenumber"/>
                </div>
            </div>
            <div class="form-group">
                <label for="contact-mobile" class="col-sm-3 control-label">Mobile</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" maxlength="15" id="mobilenumber" name="mobilenumber" value="<?= $this->view->contact->getMobile() ?>"/>
                </div>
            </div>
            <div class="form-group">
                <label for="contact-email" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="emailaddress" name="emailaddress" value="<?= $this->view->contact->getEmail() ?>"/>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
</div>
<div class="row">
    <div class="col-sm-12">
        <button onclick="PortalEdit();" class="btn btn-primary" style="margin-bottom: 15px;">Save</button>
    </div>
</div>

