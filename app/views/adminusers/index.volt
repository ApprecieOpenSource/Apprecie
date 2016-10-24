<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/library/organisations.min.js"></script>
<script src="/js/compiled/public/js/raw/library/portals.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/adminusers/index.min.js"></script>

<div class="row">
    <div class="col-sm-12">
        <h2><?= _g('All People'); ?></h2>
    </div>
</div>
<a href="/adminusers/create" class="btn btn-default" style="margin-bottom: 15px;"><?= _g('New Person'); ?></a>
<div class="row">
    <div class="col-sm-8">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-title">
                <h5><?= _g('Search'); ?></h5>
            </div>
            <div class="ibox-content">
                <form method="post" enctype="multipart/form-data" action="/adminusers" id="user-search-form" name="user-search-form" class="form-horizontal">
                    {{csrf()}}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="portal-name" class="control-label"><?= _g('Portal'); ?></label>
                                <select class="form-control" id="portalid" name="portalid">
                                    <option value="" disabled selected><?= _g('Please select...'); ?></option>
                                    <?php foreach($this->view->portals as $portal): ?>
                                        <option <?php if($portal->getPortalId()==$this->view->selectedPortalId){echo 'selected';} ?> value="<?= $portal->getPortalId();?>"><?= $portal->getPortalName(); ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="organisationId" class="control-label"><?= _g('Organisation'); ?></label>
                                <select id="organisationId" name="organisationId" class="form-control">
                                    <option value="all"><?= _g('All'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="roleid" class="control-label"><?= _g('Role'); ?></label>
                                <select id="roleid" name="roleid" class="form-control">
                                    <option value="all"><?= _g('All'); ?></option>
                                    <?php foreach($this->view->roles as $role): ?>
                                        <option value="<?= $role->getRoleId();?>"><?= $role->getDescription(); ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <label for="firstname">First Name</label>
                                <input name="firstname" id="firstname" type="text" class="form-control"/>
                            </div>

                            <div class="col-sm-3">
                                <label for="lastname">Last Name</label>
                                <input name="lastname" id="lastname" type="text" class="form-control"/>
                            </div>

                            <div class="col-sm-3">
                                <label for="email">Email</label>
                                <input name="email" id="email" type="text" class="form-control"/>
                            </div>

                            <div class="col-sm-3">
                                <label for="reference">Reference</label>
                                <input name="reference" id="reference" type="text" class="form-control" />
                            </div>
                        </div>
                    </div>
                </form>
                <button class="btn btn-primary" id="submit-btn" onclick="SearchPortalUsers();" name="submit-btn">Search</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins" style="position: relative;">
            <div class="ibox-content">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?= _g('Name'); ?></th>
                            <th class="hidden-xs"><?= _g('Reference'); ?></th>
                            <th><?= _g('Email Address'); ?></th>
                            <th class="hidden-xs"><?= _g('Organisation'); ?></th>
                            <th class="hidden-xs"><?= _g('Role'); ?></th>
                            <th><?= _g('Status'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="user-search-results">

                    </tbody>
                </table>
                <nav>
                    <ul class="pagination" id="user-pagination">

                    </ul>
                </nav>
                <div class="alert alert-info" id="no-results" role="alert"><strong><?= _g('No Users!'); ?></strong> <?= _g('Please select a portal to search for users.'); ?></div>
                <div style="display: none;" class="alert alert-info" id="no-results-found" role="alert"><strong><?= _g('No Users!'); ?></strong> <?= _g("We couldn't find any users that match your search criteria."); ?></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="impersonate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Impersonate A Person</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="account-locked" role="alert" style="display: none;">
                    This user is currently logged in. You can continue but the user may perform operations in their account at the same time.
                </div>
                <p>Before you continue, please make sure that you have obtained permission from the end user to impersonate their account:</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="impersonate-btn">Continue</button>
            </div>
        </div>
    </div>
</div>