<script src="/js/compiled/public/js/raw/library/cvault.js"></script>
<script src="/js/compiled/public/js/raw/library/groups.js"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/library/myvault.js"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/library/pagination.min.js"></script>
<script type="text/javascript"  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAFUzUvVTT07M1xZoDGIERc6xfl3x5Rljw"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/controllers/vault/supplier.min.js"></script>
<?php $this->partial("partials/jparts/cusers"); ?>
<?php $this->partial("partials/jparts/vgroups"); ?>
<?php $this->partial("partials/jparts/vaultOrganisation"); ?>
<?php $this->partial("partials/jparts/vaultSelected"); ?>
<?php $this->partial("partials/jparts/vaulttiles"); ?>
<?php $this->partial("partials/jparts/vaultlist"); ?>
<style> h2{font-size: 24px;}</style>

<img src="<?= Assets::getOrganisationVaultBackground($this->view->organisation->getOrganisationId()); ?>" style="margin-top:15px;" class="img-responsive"/>
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
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="interests-filter">
                                Interests <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="age-filter">
                                Audience <i class="fa fa-caret-down"></i>
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
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddAge('targetAge18to34')" id="age-targetAge18to34">18 - 34</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddAge('targetAge34to65')" id="age-targetAge34to65">34 - 65</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddAge('targetAge65Plus')" id="age-targetAge65Plus">65+</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddGender('male')" id="gender-male">Male</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddGender('female')" id="gender-female">Female</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddGender('mixed')" id="gender-mixed">Mixed</div></div>
                            </div>
                            <button onclick="setResultsView('#vaulttiles');" class="btn btn-default pull-right" style="margin-top: 10px; margin-bottom: 5px;"><i class="fa fa-square-o"></i> Grid</button> <button onclick="setResultsView('#vaultlist');" class="btn btn-default pull-right" style="margin-top: 10px; margin-bottom: 5px; margin-right: 5px;"><i class="fa fa-list"></i> List</button> <button onclick="SearchEvents(1);" id="apply-filters" class="btn btn-primary" style="margin-top: 10px; margin-bottom: 5px;">Apply Filters</button>
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