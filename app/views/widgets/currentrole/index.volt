<h4>
<?php $auth = new \Apprecie\Library\Security\Authentication(); echo $auth->getAuthenticatedUser()->getActiveRole()->getDescription(); ?>
</h4>
<?php if((new \Apprecie\Library\Security\Authentication())->getRoleHasAutoSwitched()): ?>
<div class="alert alert-warning alert-dismissable">
    <button type="button" class="close" data-dismiss="alert"
            aria-hidden="true">
        &times;
    </button>
    We switched your active role to <?= (new \Apprecie\Library\Users\UserRole((new \Apprecie\Library\Security\Authentication())->getSessionActiveRole()))->getText(); ?> so that you could view this content.
</div>
<?php endif; ?>
