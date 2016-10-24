<script src="/js/compiled/public/js/raw/library/vat.min.js"></script>
<script>
    function saveVatNumber(){
        var successbox=$('#success-box');
        var errorbox=$('#error-box');

        var vat=new VAT(successbox,errorbox);
        vat.setVatNumber($('#vatnumber').val());
        vat.save();
    }
</script>
<h2>Payment Settings</h2>
<?php if(! $this->view->connected): ?>
    <p>To enable payments on your portal please connect or create a Stripe account</p>
    <p>Stripe is a payment gateway similar to PayPal that will allow your clients to purchase items and allow you to receive payments for items.</p>
    <a class="btn btn-primary" style="margin-bottom: 15px;" href="https://connect.stripe.com/oauth/authorize?response_type=code&scope=read_write&client_id={{clientid}}&redirect_uri={{returnurl}}&state=<?= (new \Apprecie\Library\Security\CSRFProtection())->getSessionToken(); ?>">Connect to Stripe</a>
<?php else: ?>
        <a class="btn btn-primary" href="https://dashboard.stripe.com" target="_blank" style="margin-bottom: 15px;">Stripe Dashboard</a>
        <?php if(isset($this->view->connectAccount)): ?> <!-- this needs formatting with extra details -->
        <div class="row">
            <div class="col-sm-5">
                <div class="ibox float-e-margins" style="position: relative;">
                    <div class="ibox-title">
                        <h5><?= _g('Stripe Details'); ?></h5>
                    </div>
                    <div class="ibox-content no-padding">
                        <div class="alert alert-success" id="success-box" role="alert" style="display: none; margin: 10px;"><span id="message"></span> </div>
                        <div class="alert alert-danger" id="error-box" role="alert" style="display: none; margin: 10px;"><span id="message"></span></div>
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>Status</td>
                                <td><span style="color:limegreen;">Connected</span> </td>
                            </tr>
                            <tr>
                                <td>ID</td>
                                <td>{{connectAccount.id}}</td>
                            </tr>
                            <tr>
                                <td>Email Address</td>
                                <td>{{connectAccount.email}}</td>
                            </tr>
                            <tr>
                                <td style="min-width: 150px;">VAT Number</td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" id="vatnumber" name="vatnumber" class="form-control" maxlength="45" value="<?= Organisation::getActiveUsersOrganisation()->getVatNumber();?>">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" onclick="saveVatNumber();">Save</button>
                                        </span>
                                    </div>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php if(isset($this->view->connectError)): ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="ibox float-e-margins" style="position: relative;">
                    <div class="ibox-title">
                        <h5><?= _g('Stripe Details'); ?></h5>
                    </div>
                    <div class="ibox-content no-padding">
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>Status</td>
                                <td><span style="color:red;">Disconnected</span> </td>
                            </tr>
                            <tr>
                                <td>Message</td>
                                <td>{{connectError}}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a class="btn btn-default" href="https://connect.stripe.com/oauth/authorize?response_type=code&scope=read_write&client_id={{clientid}}&redirect_uri={{returnurl}}&state=<?= (new \Apprecie\Library\Security\CSRFProtection())->getSessionToken(); ?>">re-connect Stripe</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
<?php endif; ?>
