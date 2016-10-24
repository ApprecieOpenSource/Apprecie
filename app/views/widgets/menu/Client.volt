<?php $auth=new \Apprecie\Library\Security\Authentication();?>
<nav class="navbar navbar-default" role="navigation">
    <div>
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only"><?= _g('Toggle navigation'); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="dropdown <?php if($this->view->getControllerName()=="vault"){echo 'class="active"';} ?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= _g('The Vault'); ?> <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="/vault/"><?= _g('Browse'); ?></a></li>
                    <li><a href="/eventmanagement/reservations"><?= _g('Reservations'); ?></a></li>
                    <li><a href="/eventmanagement/purchases"><?= _g('Purchased'); ?></a></li>
                    <li><a href="/eventmanagement/attending"><?= _g('Attending'); ?></a></li>
                    <li><a href="/eventmanagement/arranging"><?= _g('Arranging'); ?></a></li>
                </ul>
                </li>
                <li class="dropdown <?php if ($this->view->getControllerName() === "contacts" || $this->view->getControllerName() === "groups" || ($this->view->getControllerName() === "invite" && $this->view->getActionName() === 'groupusers')) {echo 'active';} ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= _g('Contacts'); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/contacts/index"><?= _g('View All'); ?></a></li>
                        <li><a href="/contacts/create"><?= _g('New Contact'); ?></a></li>
                        <li><a href="/groups/index"><?= _g('Group Management'); ?></a></li>
                    </ul>
                </li>
                <li <?php if($this->view->getControllerName()=="alertcentre"){echo 'class="active"';} ?>><a href="/alertcentre"><?= _g('Alert Centre'); ?>&nbsp; <span class="badge"><?= MessageThread::getCountOfNewContent(); ?> </span></a></li>
                <li <?php if($this->view->getControllerName()=="orders"){echo 'class="active"';} ?>><a href="/orders"><?= _g('Order History'); ?></a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown <?php if($this->view->getControllerName()=="profile"){echo 'active';} ?>">
                    <a href="#" class="dropdown-toggle user-dropdown" data-toggle="dropdown"><?= Assets::getUserProfileImageContainer($auth->getAuthenticatedUser()->getUserId()); ?> <span style="margin-left: 10px;"><?= _eh($this->view->userProfile->firstname).' '._eh($this->view->userProfile->lastname); ?></span> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/profile/"><?= _g('My Profile'); ?></a></li>
                        <li><a href="/login/logout"><?= _g('Logout'); ?></a></li>
                        <li><a href="/support" target="_blank"><?= _g('Support'); ?></a></li>
                        <?php if($this->view->userroles->count() > 1): ?>
                        <li class="divider"></li>
                        <li class="dropdown-header">Change Role</li>
                        <?php foreach($this->view->userroles as $role): ?>
                        <li><a href="/callback/changeRole/<?= $role->getRole()->getName(); ?>" ><?= $role->getRole()->getDescription(); ?></a></li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    {{ widget('LoaderWidget','index') }}
</nav>