<script src="/js/compiled/public/js/raw/library/vaultSPA.min.js"></script>
<script src="/js/compiled/public/js/raw/library/user.min.js"></script>
<script src="/js/compiled/public/js/raw/library/brands.min.js"></script>
<script src="/js/compiled/public/js/raw/library/interests.min.js"></script>
<script src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script src="/js/compiled/public/js/raw/library/utils.min.js"></script>
<script src="/js/compiled/public/js/raw/library/components/progressBar.min.js"></script>
<script src="/js/compiled/public/js/raw/controllers/vault/index.min.js"></script>
<script type="text/javascript"  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUzUvVTT07M1xZoDGIERc6xfl3x5Rljw"></script>

<?php $this->partial("partials/jparts/vaultOrganisation"); ?>
<?php $this->partial("partials/jparts/vaultSelected"); ?>
<?php $this->partial("partials/jparts/vaulttiles"); ?>
<?php $this->partial("partials/jparts/vaultlist"); ?>
<?php $auth = new \Apprecie\Library\Security\Authentication(); ?>
<div class="row">
    <div class="col-sm-6 col-sm-offset-3">
        <div id="vaultLoadIndicator" style="display: none;">
            <h2 class="progress-title">Getting some things ready...</h2>
            <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="60" id="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row" id="vaultImageBanner" style="display: none;">
    <div class="col-sm-12">
        <img src="<?= Assets::getOrganisationVaultBackground($auth->getAuthenticatedUser()->getOrganisationId()); ?>" style="margin-top:15px;" class="img-responsive"/>
    </div>
</div>
<div class="row" id="searchFiltersContainer"  style="display: none;">
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
                    <div class="col-sm-2 vault-filter" style="padding: 10px;" id="interests-filter">
                        Event Interests <i class="fa fa-caret-down"></i>
                    </div>
                    <div class="col-sm-2 vault-filter" style="padding: 10px;" id="age-filter">
                        Event Audience <i class="fa fa-caret-down"></i>
                    </div>
                </div>
                <div id="filters-container" style="padding:15px; padding-bottom: 10px; border-top: 1px solid darkgrey;">
                    <div id="brands-container" class="dont-display filter-container row">

                    </div>
                    <div id="interests-container" class="dont-display filter-container row">

                    </div>
                    <div id="type-options" class="filter-container row">
                        <div class="col-sm-3">
                            <h4>Event Type</h4>
                            <div class="row">
                                <div class="col-sm-6"><div class="alert alert-plain alert-thin link isByArrangement selectableItem" style="margin-bottom: 5px;" onclick="addXorFilter('isByArrangement','0')" id="isByArrangement0">Confirmed</div></div>
                                <div class="col-sm-6"><div class="alert alert-plain alert-thin link isByArrangement selectableItem" style="margin-bottom: 5px;" onclick="addXorFilter('isByArrangement','1')" id="isByArrangement1">By Arrangement</div></div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <h4>Price</h4>
                            <div class="row">
                                <div class=" col-sm-6"><div class="alert alert-plain alert-thin link pricePerAttendee selectableItem" style="margin-bottom: 5px;" onclick="addXorFilter('pricePerAttendee','fixed')" id="pricePerAttendeefixed">Fixed Price</div></div>
                                <div class="col-sm-6"><div class="alert alert-plain alert-thin link pricePerAttendee selectableItem" style="margin-bottom: 5px;" onclick="addXorFilter('pricePerAttendee','complimentary')" id="pricePerAttendeecomplimentary">Complimentary</div></div>
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
                                        <option value="any" selected>Any</option>
                                        <option value="5">5 miles</option>
                                        <option value="10">10 miles</option>
                                        <option value="20">20 miles</option>
                                        <option value="50">50 miles</option>
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
                                <div class="col-sm-4"><div class="alert alert-plain alert-thin link age-opt selectableItem" style="margin-bottom: 5px;" onclick="addOrFilter('age','targetAge18to34')" id="agetargetAge18to34">18 - 34</div></div>
                                <div class="col-sm-4"><div class="alert alert-plain alert-thin link age-opt selectableItem" style="margin-bottom: 5px;" onclick="addOrFilter('age','targetAge34to65')" id="agetargetAge34to65">34 - 65</div></div>
                                <div class="col-sm-4"><div class="alert alert-plain alert-thin link age-opt selectableItem" style="margin-bottom: 5px;" onclick="addOrFilter('age','targetAge65Plus')" id="agetargetAge65Plus">65+</div></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <h4>Gender</h4>
                            <div class="row">
                                <div class="col-sm-4"><div class="alert alert-plain alert-thin link gender-opt selectableItem" style="margin-bottom: 5px;" onclick="addOrFilter('gender','male')" id="gendermale">Male</div></div>
                                <div class="col-sm-4"><div class="alert alert-plain alert-thin link gender-opt selectableItem" style="margin-bottom: 5px;" onclick="addOrFilter('gender','female')" id="genderfemale">Female</div></div>
                                <div class="col-sm-4"><div class="alert alert-plain alert-thin link gender-opt selectableItem" style="margin-bottom: 5px;" onclick="addOrFilter('gender','mixed')" id="gendermixed">Mixed</div></div>
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
                            <button onclick="resetFilters()" class="btn btn-default" style="margin-top: 10px; margin-bottom: 5px;">Reset Filters</button>
                            <button onclick="setResultsView('#vaulttiles');" class="btn btn-default pull-right" style="margin-top: 10px; margin-bottom: 5px;"><i class="fa fa-square-o"></i> Grid</button>
                            <button onclick="setResultsView('#vaultlist');" class="btn btn-default pull-right" style="margin-top: 10px; margin-bottom: 5px; margin-right: 5px;"><i class="fa fa-list"></i> List</button>

                            <span style="float: right; margin-top: 10px; margin-right: 5px;">
                                <select class="form-control" style="width: 200px;" id="order">
                                    <option value="eventDateASC">Event Date - Soonest</option>
                                    <option value="eventDateDESC">Event Date - Furthest</option>
                                    <option value="priceDESC">Price High - Low</option>
                                    <option value="priceASC">Price Low - High</option>
                                    <option value="suggestionsDESC">Suggestions High - Low</option>
                                    <option value="suggestionsASC">Suggestions Low - High</option>
                                    <option value="distanceASC" disabled class="distanceOrder">Distance - Nearest</option>
                                    <option value="distanceDESC" disabled class="distanceOrder">Distance - Furthest</option>
                                </select>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="pageContent" class="row" style="min-height:1091px;">

</div>