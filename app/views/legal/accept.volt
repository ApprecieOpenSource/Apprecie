<script>
    $(document).ready(function() {
        $(document).on('change', '#accept', function() {
            if ($('#accept')[0].checked) {
                $('#submit-btn').prop('disabled', false);
            } else {
                $('#submit-btn').prop('disabled', true);
            }
        });
    });
</script>
<div class="row">
    <div class="col-md-12">
        <img src="<?= Assets::getOrganisationBrandLogo(Organisation::getActiveUsersOrganisation()->getOrganisationId()); ?>" class="img-responsive" style="padding: 15px; padding-left: 0px;"/>
        <form id="login-form" action="" method="post" enctype="multipart/form-data" style="margin-top: 0px;">
            <div class="ibox float-e-margins" style="margin-bottom: 5px;">
                <div class="ibox-title">
                    <h5><?= _g('Before you can continue...'); ?></h5>
                </div>
                <div class="ibox-content">
                    <p><?= _g('Please read and accept the document(s) below:'); ?></p>
                    <ul>
                    <?php foreach ($this->view->termsIds as $termsId): ?>
                        <?php $terms = Terms::findFirstBy('termsId', $termsId); ?>
                        <li><a href="/legal/view/<?= $termsId; ?>" target="_blank"><?= $terms->getDefaultName(); ?></a> (<?= $terms->getVersion(); ?>)</li>
                    <?php endforeach; ?>
                    </ul>
                    <label for="accept-terms" class="form-label">
                        <input type="checkbox" id="accept" value="1" name="accept">
                        <?= _g('I accept'); ?>
                    </label>
                </div>
            </div>
            <div class="panel-footer" style="padding-right: 0; padding-left: 0; padding-bottom:10px;">
                <a href="/login/logout" class="btn btn-default">Logout</a> <button type="submit" id="submit-btn" class="btn btn-primary pull-right" disabled><?= _g('Continue'); ?></button>
            </div>
        </form>
    </div>
</div>