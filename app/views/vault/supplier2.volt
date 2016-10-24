<script type="text/javascript" src="/js/compiled/public/js/raw/library/myvault.min.js"></script>
<script type="text/javascript" src="/js/compiled/public/js/raw/library/pagination.min.js"></script>

<?php $this->partial("partials/jparts/vaultOrganisation"); ?>
<?php $this->partial("partials/jparts/vaultSelected"); ?>
<?php $this->partial("partials/jparts/vaultall2"); ?>
<style>
    h2{
        font-size: 24px;;
    }
</style>
<script>
    var search=new SearchVault();
    var guestList=new GetGuestListEvents();
    function AddCategory(categoryId){
        var categorySelector=$('#category-'+categoryId);
        var selected=search.getCategoryId();
        if($.inArray(categoryId,selected)==-1){
            search.addCategoryId(categoryId);
            categorySelector.removeClass('alert-plain').addClass('alert-success');
        }
        else{
            selected = $.grep(selected, function(value) {
                return value != categoryId;
            });
            search.setCategoryId(selected);
            categorySelector.removeClass('alert-success').addClass('alert-plain');
        }
    }

    function AddCatering(option){
        var cateringSelector=$('#catering-'+option);
        var selected=search.getCatering();
        if($.inArray(option,selected)==-1){
            search.addCatering(option);
            cateringSelector.removeClass('alert-plain').addClass('alert-success');
        }
        else{
            selected = $.grep(selected, function(value) {
                return value != option;
            });
            search.setCatering(selected);
            cateringSelector.removeClass('alert-success').addClass('alert-plain');
        }
    }

    function AddAge(option){
        var ageSelector=$('#age-'+option);
        var selected=search.getAge();
        if($.inArray(option,selected)==-1){
            search.addAge(option);
            ageSelector.removeClass('alert-plain').addClass('alert-success');
        }
        else{
            selected = $.grep(selected, function(value) {
                return value != option;
            });
            search.setAge(selected);
            ageSelector.removeClass('alert-success').addClass('alert-plain');
        }
    }

    function AddGender(option){
        var genderSelector=$('#gender-'+option);
        var selected=search.getGender();
        if($.inArray(option,selected)==-1){
            search.addGender(option);
            genderSelector.removeClass('alert-plain').addClass('alert-success');
        }
        else{
            selected = $.grep(selected, function(value) {
                return value != option;
            });
            search.setGender(selected);
            genderSelector.removeClass('alert-success').addClass('alert-plain');
        }
    }

    function AddPrice(option){
        var priceSelector=$('#price-'+option);
        var selected=search.getPrice();
        if($.inArray(option,selected)==-1){
            search.addPrice(option);
            priceSelector.removeClass('alert-plain').addClass('alert-success');
        }
        else{
            selected = $.grep(selected, function(value) {
                return value != option;
            });
            search.setPrice(selected);
            priceSelector.removeClass('alert-success').addClass('alert-plain');
        }
    }

    function AddType(option){
        var typeSelector=$('#type-'+option);
        var selected=search.getType();
        if($.inArray(option,selected)==-1){
            search.addType(option);
            typeSelector.removeClass('alert-plain').addClass('alert-success');
        }
        else{
            selected = $.grep(selected, function(value) {
                return value != option;
            });
            search.setType(selected);
            typeSelector.removeClass('alert-success').addClass('alert-plain');
        }
    }

    function AddBrand(brandId){
        var brandSelector=$('#brand-'+brandId);
        var selected=search.getBrandId();
        if($.inArray(brandId,selected)==-1){
            search.addBrandId(brandId);
            brandSelector.removeClass('alert-plain').addClass('alert-success');
        }
        else{
            selected = $.grep(selected, function(value) {
                return value != brandId;
            });
            search.setBrandId(selected);
            brandSelector.removeClass('alert-success').addClass('alert-plain');
        }
    }

    $(document).ready(function(){
        $.when(guestList.fetch()).then(function(data){
            guestList=data;
            SearchEvents(1,true);
        })

        $('#brands-filter').click(function(){
            $('#filters-container').fadeIn('fast');
            $('.filter-container').hide();
            $('#brands-container').show();
            $('.selected-filter').removeClass('selected-filter');
            $(this).addClass('selected-filter');
        })
        $('#interests-filter').click(function(){
            $('#filters-container').fadeIn('fast');
            $('.filter-container').hide();
            $('#interests-container').show();
            $('.selected-filter').removeClass('selected-filter');
            $(this).addClass('selected-filter');
        })
        $( "#price-filter" ).click(function() {
            $('#filters-container').fadeIn('fast');
            $('.filter-container').hide();
            $('#price-options').show();
            $('.selected-filter').removeClass('selected-filter');
            $(this).addClass('selected-filter');
        });
        $( "#type-filter" ).click(function() {
            $('#filters-container').fadeIn('fast');
            $('.filter-container').hide();
            $('#type-options').show();
            $('.selected-filter').removeClass('selected-filter');
            $(this).addClass('selected-filter');
        });
        $( "#gender-filter" ).click(function() {
            $('#filters-container').fadeIn('fast');
            $('.filter-container').hide();
            $('#gender-options').show();
            $('.selected-filter').removeClass('selected-filter');
            $(this).addClass('selected-filter');
        });
        $( "#age-filter" ).click(function() {
            $('#filters-container').fadeIn('fast');
            $('.filter-container').hide();
            $('#age-options').show();
            $('.selected-filter').removeClass('selected-filter');
            $(this).addClass('selected-filter');
        });
    })

    function SearchEvents(pageNumber,initialLoad){
        $(document.body).animate({
            'scrollTop':   $('#main-items').offset().top
        }, 500);
        $('#filter-container').hide();
        $('#all-container').css('opacity',0.5);
        search.setPageNumber(pageNumber);
        $.when(search.fetch()).then(function(data){
            $('#all-container').css('opacity',1);
            if(data.items.length!=0){
                var template = $.templates("#vaultall2");
                $("#all-container").html(template.render(data));

                $('#all-events').fadeIn('fast');
                VaultPagination(data,'SearchEvents',$('#all-pagination'),'vault');
                VaultPagination(data,'SearchEvents',$('#all-pagination-bottom'),'vault');
                $('.item-tile').hover(function(){
                    $( this ).find('.item-tile-desc').stop().animate({
                        height: "toggle"
                    }, 200, function() {
                        // Animation complete.
                    });
                    //$(this).find('.item-tile-desc').stop().fadeIn('fast','linear');
                },function(){
                    $( this ).find('.item-tile-desc').stop().animate({
                        height: "toggle"
                    }, 200, function() {
                        // Animation complete.
                    });
                })
            }
            else{
                $("#all-container").html(data.noitems);
            }
            if(initialLoad===true){
                loadFilters(data);
                initialLoad=false;
            }

        })
    }

    function loadFilters(data){
        var interestContainer=$('#interests-container');
        var brandContainer=$('#brands-container');
        interestContainer.empty();
        brandContainer.empty();

        $.each(data.brands,function(a,b){
            $.each(b,function(brandId,brand){
                brandContainer.append('<div class="col-lg-3 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddBrand('+brandId+')" id="brand-'+brandId+'">'+brand+'</div></div>');
            })
        });

        var interests=new getInterests();
        $.when(interests.fetch()).then(function(data){
            $.each(data,function(interestId,interest){
                interestContainer.append('<div class="col-lg-3 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" id="category-'+interestId+'" onclick="AddCategory('+interestId+')">'+interest+'</div></div>');
            })
        });
    }

    function getGuestListStatus(itemId){
        console.log(itemId);
    }

</script>
<img src="<?= Assets::getOrganisationVaultBackground($this->view->organisation->getOrganisationId()); ?>" style="margin-top:15px;" class="img-responsive"/>
<div class="row" id="main-items">
    <div class="col-sm-12">
        <h2>All Your Exclusive Events</h2>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content" style="padding: 0px;">
                        <div class="row" style="text-align: center; margin-left: 0px; margin-right: 0px;">
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="brands-filter">
                                Brands <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="interests-filter">
                                Interests <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="price-filter">
                                Price <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="type-filter">
                                Type <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="age-filter">
                                Age <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="col-sm-2 vault-filter" style="padding: 10px;" id="gender-filter">
                                Gender <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <div id="filters-container" style="padding:15px; padding-bottom: 10px; border-top: 1px solid darkgrey; display: none;">
                            <div id="brands-container" class="dont-display filter-container row">
                                <img src="/img/ajax-loader-grey.gif"/>
                            </div>
                            <div id="interests-container" class="dont-display filter-container row">
                                <img src="/img/ajax-loader-grey.gif"/>
                            </div>
                            <div id="price-options" class="dont-display filter-container row">
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddPrice('fixed')" id="price-fixed">Fixed Price</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddPrice('complementary')" id="price-complementary">Complimentary</div></div>
                            </div>
                            <div id="type-options" class="dont-display filter-container row">
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddType('confirmed')" id="type-confirmed">Confirmed</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddType('byarrangement')" id="type-byarrangement">By Arrangement</div></div>
                            </div>
                            <div id="age-options" class="dont-display filter-container row">
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddAge('targetAge18to34')" id="age-targetAge18to34">18 - 34</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddAge('targetAge34to65')" id="age-targetAge34to65">34 - 65</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddAge('targetAge65Plus')" id="age-targetAge65Plus">65+</div></div>
                            </div>
                            <div id="gender-options" class="dont-display filter-container row">
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddGender('male')" id="gender-male">Male</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddGender('female')" id="gender-female">Female</div></div>
                                <div class="col-lg-2 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddGender('mixed')" id="gender-mixed">Mixed</div></div>
                            </div>
                            <button onclick="SearchEvents(1);" class="btn btn-primary" style="margin-top: 10px; margin-bottom: 5px;">Apply Filters</button>
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