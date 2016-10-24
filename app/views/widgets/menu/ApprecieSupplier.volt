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
                <li <?php if($this->view->getControllerName()=="vault"){echo 'class="active"';} ?>><a href="/vault"><?= _g('The Vault'); ?></a></li>
                <li class="dropdown <?php if($this->view->getControllerName()=="eventmanagement" || $this->view->getControllerName()=="mycontent" || $this->view->getControllerName()=="itemcreation"){echo 'active';} ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= _g('Event Management'); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/mycontent/events"><?= _g('Confirmed Events'); ?></a></li>
                        <li><a href="/mycontent/arranged"><?= _g('By Arrangement Events'); ?></a></li>
                        <li><a href="/itemcreation/create"><?= _g('Create New Event'); ?></a></li>
                    </ul>
                </li>
                <li <?php if($this->view->getControllerName()=="alertcentre"){echo 'class="active"';} ?>><a href="/alertcentre"><?= _g('Alert Centre'); ?>&nbsp; <span class="badge"><?= MessageThread::getCountOfNewContent(); ?> </span></a></li>
                <li class="dropdown <?php if($this->view->getControllerName()=="roi"){echo 'active';} ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= _g('Reporting'); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/roi"><?= _g('ROI'); ?></a></li>
                    </ul>
                </li>
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
                                <li <?= $role->getRole()->getName() == $auth->getSessionActiveRole() ? 'class="active"' : ''; ?>><a href="/callback/changeRole/<?= $role->getRole()->getName(); ?>" ><?= $role->getRole()->getDescription(); ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    {{ widget('LoaderWidget','index') }}
</nav>