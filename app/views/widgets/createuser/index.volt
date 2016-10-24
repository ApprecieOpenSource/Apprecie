<script src="/js/validation/errors.min.js"></script>
<script src="/js/validation/general.min.js"></script>
<script src="/js/compiled/public/js/raw/widgets/createuser/general.min.js"></script>
<script src="/js/compiled/public/js/raw/widgets/createuser/index.min.js"></script>
<script>
    var refreshOnSuccess = <?= $this->view->refreshOnSuccess ? 'true' : 'false'; ?>;
</script>
<div class="modal fade" id="create-user-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?= ((new \Apprecie\Library\Security\Authentication())->getAuthenticatedUser()->hasRole(\Apprecie\Library\Users\UserRole::CLIENT)) ? _g('New Contact') : _g('New Client'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" id="create-user-success" style="display:none;" role="alert"></div>
                <div class="alert alert-danger" id="create-user-error" style="display:none;" role="alert"></div>
                <p><?= _g('Please provide a First Name and a Last Name. Alternatively you can use a Reference Code instead.'); ?></p>
                <form class="form-horizontal" id="create-user-form" name="create-user-form">
                    {{csrf()}}
                    <div class="form-group">
                        <label for="reference-code" class="col-sm-3 control-label">*&nbsp;<?= _g('Reference Code'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="reference-code" name="reference-code" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-3" style="display: inline-block;">-or-</div>
                    </div>
                    <div class="form-group">
                        <label for="firstname" class="col-sm-3 control-label">*&nbsp;<?= _g('First Name'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="firstname" name="firstname" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-3 control-label">*&nbsp;<?= _g('Last Name'); ?></label>
                        <div class="col-sm-9">
                            <input type="text" id="lastname" name="lastname" class="form-control"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="create-user-modal-btn">Create</button>
            </div>
        </div>
    </div>
</div>