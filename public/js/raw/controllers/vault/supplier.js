var search=new SearchVault();
var guestList=new GetGuestListEvents();

var geocoder;
var geocodeResults=null;
var searchResults=null;
var resultView='#vaulttiles';

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

function peopleClearOptions(){
    search.setAge([]);
    search.setCategoryId([]);
    search.setGender([]);

    $('.age-opt').removeClass('alert-success').addClass('alert-plain');
    $('.gender-opt').removeClass('alert-success').addClass('alert-plain');
    $('.interest-opt').removeClass('alert-success').addClass('alert-plain');

    $('#age-filter').addClass('option-disabled');
    $('#interests-filter').addClass('option-disabled');

}

function peopleEnableOptions(){
    $('#age-filter').removeClass('option-disabled');
    $('#interests-filter').removeClass('option-disabled');
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
    var priceFixedSelector=$('#price-fixed');
    var priceComplimentarySelector=$('#price-complimentary');

    priceFixedSelector.removeClass('alert-success').addClass('alert-plain');
    priceComplimentarySelector.removeClass('alert-success').addClass('alert-plain');

    if(search.getPrice()!==option || search.getPrice()==null){
        search.setPrice(option);
        var typeSelector=$('#price-'+option);
        typeSelector.removeClass('alert-plain').addClass('alert-success');
    }
    else{
        search.setPrice(null);
    }
}

function AddType(option,isByArrangement){
    var typeConfirmedSelector=$('#type-confirmed');
    var typeByArrangementSelector=$('#type-byarrangement');

    typeConfirmedSelector.removeClass('alert-success').addClass('alert-plain');
    typeByArrangementSelector.removeClass('alert-success').addClass('alert-plain');

    if(search.getType()!==isByArrangement || search.getType()==null){
        search.setType(isByArrangement);
        var typeSelector=$('#type-'+option);
        typeSelector.removeClass('alert-plain').addClass('alert-success');
    }
    else{
        search.setType(null);
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
    geocoder = new google.maps.Geocoder();

    var timer=null;

    $.when(guestList.fetch()).then(function(data){
        guestList=data;
        GetOrgEvents();
        GetSelEvents();
        SearchEvents(1,true);
    })


    $('#postcode').keyup(function(){
        if($(this).val()!=''){
            $('#apply-filters').prop('disabled', true);
            clearTimeout(timer);
            timer=setTimeout(function(){
                getGeolocation();
            }, 800);
        }
        else{
            $('#georesults').empty();
            $('#apply-filters').prop('disabled', false);
            search.setSearchLatitude(null);
            search.setSearchLongitude(null);
        }

    })

    $('#brands-filter').click(function(){
        $('#filters-container').fadeIn('fast');
        $('.filter-container').hide();
        $('#brands-container').show();
        $('.selected-filter').removeClass('selected-filter');
        $(this).addClass('selected-filter');
    })
    $('#interests-filter').click(function(){
        if(!$(this).hasClass('option-disabled')){
            $('#filters-container').fadeIn('fast');
            $('.filter-container').hide();
            $('#interests-container').show();
            $('.selected-filter').removeClass('selected-filter');
            $(this).addClass('selected-filter');
        }

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
        if(!$(this).hasClass('option-disabled')) {
            $('#filters-container').fadeIn('fast');
            $('.filter-container').hide();
            $('#age-options').show();
            $('.selected-filter').removeClass('selected-filter');
            $(this).addClass('selected-filter');
        }
    });
    $( "#people-filter" ).click(function() {
        $('#filters-container').fadeIn('fast');
        $('.filter-container').hide();
        $('#people-options').show();
        $('.selected-filter').removeClass('selected-filter');
        $(this).addClass('selected-filter');
    });
})

function getBrowserLocation(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(geocodeLatLng);
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

function getGeolocation(){
    if($('#postcode').val()!=''){
        var address = $("#postcode").val();
        geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                formatGeocodeResults(results);
            } else {
                $('#georesults').empty();
                $('#georesults').append('<div class="alert alert-warning" role="alert" style="margin-top:5px;">No results were found please refine your search</div>');
            }
        });
    }
}

function geocodeLatLng(position) {
    var latlng = {lat: parseFloat(position.coords.latitude), lng: parseFloat(position.coords.longitude)};
    geocoder.geocode({'location': latlng}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            $('#apply-filters').prop('disabled', true);
            formatGeocodeResults(results);
        } else {
            $('#georesults').empty();
            $('#georesults').append('<div class="alert alert-warning" role="alert" style="margin-top:5px;">No results were found please refine your search</div>');
        }
    });
}

function formatGeocodeResults(results){
    $('#georesults').empty();
    var validAddresses=0;
    if(results.length>0){
        geocodeResults=results;

        $.each(results,function(index,value){
            var valid=true;
            /*
            $.each(value.address_components,function(index,value){
                if($.inArray('postal_town',value.types)!=-1){
                    valid=true;
                }
            })
            */
            if(valid===true){
                validAddresses++;
                $('#georesults').append('<div class="georesult" onclick="setSearchLocation('+index+')">'+value.formatted_address+'</div>');
            }
        })
        if(validAddresses!=0){
            $('#georesults').prepend('<p style="margin-top: 15px; font-weight: bold">Please select a location:</p>');
        }
        else{
            $('#georesults').empty();
            $('#georesults').append('<div class="alert alert-warning" role="alert" style="margin-top:5px;">No results were found please refine your search</div>');
        }
    }
}

function setSearchLocation(index){
    $('#apply-filters').prop('disabled', false);
    search.setSearchLatitude(geocodeResults[index].geometry.location.lat());
    search.setSearchLongitude(geocodeResults[index].geometry.location.lng());
    $('#postcode').val(geocodeResults[index].formatted_address);
    $('#georesults').empty();
}

function displayResults(){
    var template = $.templates(resultView);
    $("#all-container").html(template.render(searchResults));
    BindTiles();
}

function GetOrgEvents(){
    var events=new GetOrganisationEvents();
    $.when(events.fetch()).then(function(data){
        if(data.items!=null){
            if(data.items.length!=0){
                var template = $.templates("#vaultOrganisation");
                $("#orgcarousel").html(template.render(data));
                $('#organisation-events').fadeIn('fast');
            }
        }
    })
}
function GetSelEvents(){
    var events=new GetSelectedEvents();
    $.when(events.fetch()).then(function(data){
        if(data.items!=null) {
            if (data.items.length != 0) {
                var template = $.templates("#vaultSelected");
                $("#selectedcarousel").html(template.render(data));
                $('#selected-events').fadeIn('fast');
            }
        }
    })
}
function SearchEvents(pageNumber,initialLoad){
    $(document.body).animate({
        'scrollTop':   $('#main-items').offset().top
    }, 500);
    $('#filter-container').hide();
    $('#all-container').css('opacity',0.5);

    search.setPageNumber(pageNumber);
    search.setDistance($('#distance').val());
    search.setOrder($('#order').val());
    performSearch(initialLoad);
}

function performSearch(initialLoad){
    $.when(search.fetch()).then(function(data){
        searchResults=data;
        $('#all-container').css('opacity',1);
        if(data.items.length!=0 && data.items!=null){
            displayResults();
            $('#all-events').fadeIn('fast');
            VaultPagination(data,'SearchEvents',$('#all-pagination'),'vault');
            VaultPagination(data,'SearchEvents',$('#all-pagination-bottom'),'vault');
        }
        else{
            $('#all-pagination').empty();
            $('#all-pagination-bottom').empty();
            $("#all-container").html('<div class="alert alert-warning" style="margin-bottom: 5px; margin-left:15px; margin-right: 15px;" >'+data.noitems+'</div>');
        }
        if(initialLoad===true){
            loadFilters(data);
            initialLoad=false;
        }
    })
}

function BindTiles(){
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

function setResultsView(view){
    resultView=view;
    displayResults();
}
function loadFilters(data){
    var interestContainer=$('#interests-container');
    var brandContainer=$('#brands-container');
    interestContainer.empty();
    brandContainer.empty();

    var brands=new getBrands();
    $.when(brands.fetch()).then(function(data){
        $.each(data,function(brandId,brand){
            brandContainer.append('<div class="col-lg-3 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link" style="margin-bottom: 5px;" onclick="AddBrand('+brandId+')" id="brand-'+brandId+'">'+brand.name+' <span class="badge pull-right">'+brand.items.length+'</div></div>');
        })
    });

    var interests=new getInterests();
    $.when(interests.fetch()).then(function(data){
        $.each(data,function(interestId,interest){
            interestContainer.append('<div class="col-lg-3 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link interest-opt" style="margin-bottom: 5px;" id="category-'+interestId+'" onclick="AddCategory('+interestId+')">'+interest.name+' <span class="badge pull-right">'+interest.items.length+'</span></div></div>');
        })
    });
}