<script src="/js/compiled/public/js/raw/library/people.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/people/index.min.js"></script>
<?php $auth=new \Apprecie\Library\Security\Authentication(); ?>
<div class="row">
    <div class="col-sm-12">
        <h2>
            <div class="pull-right dropdown">
                <span class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true" style="margin-right: 10px;cursor: pointer;">
                    <button class="btn" style="margin-top: 5px; margin-right: -10px;"><i class="fa fa-ellipsis-v"></i> Actions</button>
                </span>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                    <li><a role="menuitem" tabindex="-1" href="/people/create">New Person</a></li>
                    <?php if($auth->getAuthenticatedUser()->getActiveRole()->getName()!="PortalAdministrator"): ?>
                        <li><a role="menuitem"a tabindex="-1" href="/people/import">Import People</a></li>
                    <?php endif; ?>
                    <li><a role="menuitem" tabindex="-1" href="/groups/index">Group Management</a></li>
                </ul>
            </div>
            <?= _g('All People'); ?>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('People in your network'); ?></h5>
                <span class="pull-right"><a style="text-decoration: none; cursor: pointer;" onclick="toggleFilter('#filter-container');"><i class="fa fa-filter"></i> Filter people</a></span>
            </div>
            <div class="ibox-content">
                <div id="filter-container" style="display: none;">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="roleName" class="control-label"><?= _g('Role'); ?></label>
                                        <select id="roleName" name="roleName" class="form-control search-change">
                                            <option value="All"><?= _g('All'); ?></option>
                                            <?php $roleHierarchy = new \Apprecie\Library\Users\RoleHierarchy($auth->getSessionActiveRole()); ?>
                                            <?php foreach($roleHierarchy->getVisibleRoles() as $roleName => $roleText):?>
                                                <option <?= ($roleName === $this->view->selectedRole) ? 'selected' : ''; ?> value="<?= $roleName;?>"><?= $roleText; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input name="email" id="email" type="text" class="form-control search-text-change" value=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="name">Name</label>
                                    <input name="name" id="name" type="text" class="form-control search-text-change" value=""/>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="reference">Reference</label>
                                        <input name="reference" id="reference" type="text" class="form-control search-text-change" value=""/>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="group">Group</label>
                                        <select class="form-control search-change" id="groupId" name="groupId">
                                            <option value="all">All</option>
                                            <?php foreach($this->view->groups as $group): ?>
                                                <option value="<?= $group->getGroupId(); ?>"><?= $group->getGroupName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="checkbox" style="margin-top: 0px;">
                                <label style="font-weight: normal; margin-right: 10px;" for="account-active"><input class="search-click" type="checkbox" id="account-active" name="account-active" <?php if($this->request->get('a')!='false'){echo 'checked';} ?>>Show active & registered people</label><br/>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="account-pending"><input class="search-click" type="checkbox" id="account-pending" name="account-pending" <?php if($this->request->get('u')!='false'){echo 'checked';} ?>> Show unregistered people</label>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="account-deactivated"><input class="search-click" type="checkbox" id="account-deactivated" name="account-deactivated"> Show deactivated people</label>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="login-enabled"><input class="search-click" type="checkbox" id="login-enabled" name="login-enabled"> Show people with login enabled</label>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="login-suspended"><input class="search-click" type="checkbox" id="login-suspended" name="login-suspended"> Show people with login suspended</label>
                            </div>
                            <div class="checkbox">
                                <label style="font-weight: normal; margin-right: 10px;" for="suggestions-only"><input class="search-click" type="checkbox" id="suggestions-only" name="suggestions-only"> Has suggestions only</label>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="hidden-xs"><?= _g('Name'); ?></th>
                            <th><?= _g('Reference'); ?></th>
                            <th><?= _g('Group'); ?></th>
                            <th><?= _g('Organisation'); ?></th>
                            <th class="hidden-xs"><?= _g('Email Address'); ?></th>
                            <th class="hidden-xs"><?= _g('Role'); ?></th>
                            <th><?= _g('Account'); ?></th>
                            <th><?= _g('Login'); ?></th>
                            <th><?= _g('Suggested Events'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="user-results">

                    </tbody>
                </table>
                <nav>
                    <ul class="pagination pagination-sm" id="user-search-pagination">

                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>