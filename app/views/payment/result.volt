<h2>
    <?php if($this->view->state['state']=='failed'): ?>
        Payment Failed
    <?php endif; ?>
    <?php if($this->view->state['state']=='success'): ?>
        Payment Successful
    <?php endif; ?>
</h2>
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5>
            Payment Confirmation
        </h5>
    </div>
    <div class="ibox-content">
        <?php if($this->view->state['state']=='failed'): ?>
            <p>Unfortunately your payment has failed. The reason given was:</p>
            <p>
                <?= $this->view->state['message']; ?>
            </p>
            <p><a class="btn btn-primary" href="/vault">Return To Vault</a> </p>
        <?php endif; ?>
        <?php if($this->view->state['state']=='success'): ?>
            <p>Your payment was successful and you can now manage your event.</p>
            <p><a class="btn btn-primary" href="/vault/manage/{{itemId}}">View My Item</a></p>
        <?php endif; ?>
    </div>
</div>