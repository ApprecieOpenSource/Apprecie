<script src="/js/compiled/public/js/raw/library/cvault.js"></script>
<script src="/js/compiled/public/js/raw/library/groups.js"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/library/myvault.js"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script type="text/javascript"  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUzUvVTT07M1xZoDGIERc6xfl3x5Rljw"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/controllers/vault/index.min.js"></script>
<?php $this->partial("partials/jparts/vaultOrganisation"); ?>
<?php $this->partial("partials/jparts/vaultSelected"); ?>
<?php $this->partial("partials/jparts/vaulttiles"); ?>
<?php $this->partial("partials/jparts/vaultlist"); ?>
<?php $this->partial("partials/jparts/cusers"); ?>
<?php $this->partial("partials/jparts/vgroups"); ?>

<?php $auth = new \Apprecie\Library\Security\Authentication(); ?>
<script>

</script>
<style> h2{font-size: 24px;}
.option-disabled{
    background-color: #ECECEC;
    cursor: not-allowed;
}
</style>

<img src="<?= Assets::getOrganisationVaultBackground($this->view->organisation->getOrganisationId()); ?>" style="margin-top:15px;" class="img-responsive"/>
<div class="row">
    <div class="col-sm-12 col-lg-6" id="organisation-events" style="display: none;">
        <h2>Latest Events From <?= $this->view->organisation->getOrganisationName(); ?></h2>
        <div id="orgcarousel"></div>
    </div>
    <div class="col-sm-12 col-lg-6" id="selected-events" style="display: none;">
        <h2>Selected For You</h2>
        <div id="selectedcarousel"></div>
    </div>
</div>
<div class="row" id="main-items">
    <div class="col-sm-12">
        <h2>All Your Exclusive Events</h2>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content" style="padding: 0px;">
                        <div class="row" style="text-align: center; margin-left: 0px; margin-right: 0px;">
                            <div class="col-sm-2 vault-filter selected-filter" style="padding: 10px;" id="type-filter">
                                Event Details <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="brands-filter">
                                Brands <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="people-filter">
                                People <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="interests-filter">
                                Event Interests <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="age-filter">
                                Event Audience <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <div id="filters-container" style="padding:15px; padding-bottom: 10px; border-top: 1px solid darkgrey;">
                            <div id="brands-container" class="dont-display filter-container row">
                                <img src="/img/ajax-loader-grey.gif"/>
                            </div>
                            <div id="interests-container" class="dont-display filter-container row">
                                <img src="/img/ajax-loader-grey.gif"/>
                            </div>
                            <div id="type-options" class="filter-container row">
                                <div class="col-sm-3">
                                    <h4>Event Type</h4>
                                    <div class="row">
                                        <div class="col-sm-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddType('confirmed',0)" id="type-confirmed">Confirmed</div></div>
                                        <div class="col-sm-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddType('byarrangement',1)" id="type-byarrangement">By Arrangement</div></div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <h4>Price</h4>
                                    <div class="row">
                                        <div class=" col-sm-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddPrice('fixed')" id="price-fixed">Fixed Price</div></div>
                                        <div class="col-sm-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddPrice('complimentary')" id="price-complimentary">Complimentary</div></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h4>Distance</h4>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" onclick="getBrowserLocation()" style="height:34px;"><i class="fa fa-crosshairs"></i></button>
                                                </span>
                                                <input type="text" class="form-control" id="postcode" name="postcode" placeholder="e.g. Reading, UK"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <select id="distance" name="distance" class="form-control">
                                                <option value="5">5 miles</option>
                                                <option value="10">10 miles</option>
                                                <option value="20">20 miles</option>
                                                <option value="50" selected>50 miles</option>
                                                <option value="100">100 miles</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12" id="georesults">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="age-options" class="dont-display filter-container row">
                                <div class="col-sm-6">
                                    <h4>Age</h4>
                                    <div class="row">
                                        <div class="col-sm-4"><div class="alert alert-plain alert-thin link age-opt" style="margin-bottom: 5px;" onclick="AddAge('targetAge18to34')" id="age-targetAge18to34">18 - 34</div></div>
                                        <div class="col-sm-4"><div class="alert alert-plain alert-thin link age-opt" style="margin-bottom: 5px;" onclick="AddAge('targetAge34to65')" id="age-targetAge34to65">34 - 65</div></div>
                                        <div class="col-sm-4"><div class="alert alert-plain alert-thin link age-opt" style="margin-bottom: 5px;" onclick="AddAge('targetAge65Plus')" id="age-targetAge65Plus">65+</div></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <h4>Gender</h4>
                                    <div class="row">
                                        <div class="col-sm-4"><div class="alert alert-plain alert-thin link gender-opt" style="margin-bottom: 5px;" onclick="AddGender('male')" id="gender-male">Male</div></div>
                                        <div class="col-sm-4"><div class="alert alert-plain alert-thin link gender-opt" style="margin-bottom: 5px;" onclick="AddGender('female')" id="gender-female">Female</div></div>
                                        <div class="col-sm-4"><div class="alert alert-plain alert-thin link gender-opt" style="margin-bottom: 5px;" onclick="AddGender('mixed')" id="gender-mixed">Mixed</div></div>
                                    </div>
                                </div>

                            </div>
                            <div id="people-options" class="dont-display filter-container row">
                                <div class="col-sm-12">
                                    <div class="alert alert-info" role="alert">Please note that searching for events based on people will remove any Interest or Audience filters you have applied</div>
                                    <div style="margin-bottom: 15px;">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-user"></i> Add people
                                        </button>
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#groupSelectModal">
                                            <i class="fa fa-users"></i> Add group
                                        </button>
                                        <?php
                                        switch($auth->getAuthenticatedUser()->getActiveRole()->getName()){
                                            case "Manager":
                                                ?>
                                                <button type="button" class="btn btn-default" onclick="toggleRole(31);">
                                                    <i class="fa fa-cube"></i> Add All Internals
                                                </button>
                                                <button type="button" class="btn btn-default" onclick="toggleRole(51);">
                                                    <i class="fa fa-cube"></i> Add All Clients
                                                </button>
                                                <?php
                                                break;
                                            case "Internal":
                                                ?>
                                                <button type="button" class="btn btn-default" onclick="toggleRole(51);">
                                                    <i class="fa fa-cube"></i> Add All Clients
                                                </button>
                                                <?php
                                                break;
                                        }
                                        ?>
                                        <a class="btn btn-default pull-right" id="clear-selected-people" onclick="clearSelected()">Clear All</a>
                                    </div>
                                    <div id="selected-users">
                                    </div>
                                    <div id="selected-groups">
                                    </div>
                                    <div id="selected-roles">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 20px; border-top: 1px solid #CCCCCC; padding-top: 5px;">
                                <div class="col-sm-12">
                                    <button onclick="setResultsView('#vaulttiles');" class="btn btn-default pull-right" style="margin-top: 10px; margin-bottom: 5px;"><i class="fa fa-square-o"></i> Grid</button>
                                    <button onclick="setResultsView('#vaultlist');" class="btn btn-default pull-right" style="margin-top: 10px; margin-bottom: 5px; margin-right: 5px;"><i class="fa fa-list"></i> List</button>
                                    <span style="float: right; margin-top: 10px; margin-right: 5px;">
                                        <select class="form-control" style="width: 200px;" id="order">
                                            <option value="startDateTimeASC">Event Date Ascending</option>
                                            <option value="startDateTimeDESC">Event Date Descending</option>
                                            <option value="unitPriceDESC">Price High - Low</option>
                                            <option value="unitPriceASC">Price Low - High</option>
                                        </select>
                                    </span>
                                    <button onclick="SearchEvents(1);" id="apply-filters" class="btn btn-primary" style="margin-top: 10px; margin-bottom: 5px;">Apply Filters</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav>
            <div id="all-pagination" style="text-align: center; margin-bottom: 25px;">

            </div>
            <div class="row" id="all-container">

            </div>
            <nav>
                <div id="all-pagination-bottom" style="text-align: center; margin-bottom: 15px;">

                </div>
            </nav>
        </nav>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add individual people</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" role="alert">Please note that only users matching events within your vault are displayed below</div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="roleName" class="control-label"><?= _g('Role'); ?></label>
                                    <select id="roleName" name="roleName" class="form-control search-change">
                                        <option value="All"><?= _g('All'); ?></option>
                                        <?php $roleHierarchy = new \Apprecie\Library\Users\RoleHierarchy($auth->getSessionActiveRole()); ?>
                                        <?php foreach($roleHierarchy->getVisibleRoles() as $roleName => $roleText):?>
                                            <option value="<?= $roleName;?>"><?= $roleText; ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input name="email" id="email" type="text" class="form-control search-text-change" value=""/>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label for="name">Name</label>
                                <input name="name" id="name" type="text" class="form-control search-text-change" value=""/>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="reference">Reference</label>
                                    <input name="reference" id="reference" type="text" class="form-control search-text-change" value=""/>
                                </div>
                            </div>
                            <div class="col-sm-3">
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
                        <input style="display: none;" class="search-click" type="checkbox" id="account-active" name="account-active" <?php if($this->request->get('a')!='false'){echo 'checked';} ?>>
                        <input style="display: none;" class="search-click" type="checkbox" id="account-pending" name="account-pending" <?php if($this->request->get('u')!='false'){echo 'checked';} ?>>
                    </div>
                </div>
                <input type="button" class="btn btn-primary" value="Search People" onclick="inviteSearch(1);" >
                <a onclick="selectAll();" class="btn btn-default pull-right">Select All</a>
                <a onclick="deselectAll();" class="btn btn-default pull-right">Deselect All</a>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="hidden-xs"><?= _g('Name'); ?></th>
                        <th><?= _g('Reference'); ?></th>
                        <th><?= _g('Group'); ?></th>
                        <th><?= _g('Organisation'); ?></th>
                        <th class="hidden-xs"><?= _g('Role'); ?></th>
                        <th><?= _g('Account'); ?></th>
                        <th><?= _g('Login'); ?></th>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="groupSelectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add entire group</h4>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="hidden-xs"><?= _g('Group Name'); ?></th>
                        <th><?= _g('Users'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="group-results">
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination pagination-sm" id="group-search-pagination">

                    </ul>
                </nav>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>