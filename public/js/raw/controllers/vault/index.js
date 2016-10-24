var vaultLoader=null;
var vaultPage=null;
var geocoder;
var geocodeResults=null;
var timer=null;
var resultView='#vaulttiles';

$(document).ready(function(){
    geocoder = new google.maps.Geocoder();
    var pageContent=$('#pageContent');
    vaultLoader=new vaultSPA(null,'#vaultLoadIndicator','doneLoading');
    $('#resetData').click(function(){
        $('#searchFiltersContainer').fadeOut(1000);
        $('#vaultImageBanner').fadeOut(1000);
        pageContent.empty();
        $('#interests-container').empty();
        $('#brands-container').empty();
        vaultLoader.reset();
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
            vaultLoader.setSearchLatLng(null,null);
            $('.distanceOrder').prop('disabled',true);
            displayResults();
        }

    })

    $('#order').change(function(){
        if($(this).val()=='distanceASC'){
            vaultLoader.setSort('distance',false);
        }
        if($(this).val()=='distanceDESC'){
            vaultLoader.setSort('distance',true);
        }
        if($(this).val()=='suggestionsDESC'){
            vaultLoader.setSort('suggestions',true);
        }
        if($(this).val()=='suggestionsASC'){
            vaultLoader.setSort('suggestions',false);
        }
        if($(this).val()=='eventDateDESC'){
            vaultLoader.setSort('startDateTimeEpoc',true);
        }
        if($(this).val()=='eventDateASC'){
            vaultLoader.setSort('startDateTimeEpoc',false);
        }
        if($(this).val()=='priceASC'){
            vaultLoader.setSort('unitPrice',false);
        }
        if($(this).val()=='priceDESC'){
            vaultLoader.setSort('unitPrice',true);
        }
        displayResults();
    })

    $('#distance').change(function(){
        vaultLoader.setDistance($(this).val());
        displayResults();
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

function doneLoading(){
    var pageContent=$('#pageContent');
    var template = $.templates('#vaulttiles');
    vaultLoader.setSort('startDateTimeEpoc',false);
    vaultPage=new Pager(vaultLoader.getItems(),6,1,'vaultPage',pageContent,template,'bindTiles');
    $('#searchFiltersContainer').fadeIn(1000);
    $('#vaultImageBanner').fadeIn(1000);

    displayBrands();
    displayInterests();
}

function displayResults(){
    var pageContent=$('#pageContent');
    vaultLoader.setDistance($('#distance').val());
    if(vaultLoader.getFilteredResults().length!=0){
        var template = $.templates(resultView);
        vaultPage=new Pager({'items':vaultLoader.getFilteredResults(),'totalItems':vaultLoader.getFilteredResults().length},6,1,'vaultPage',pageContent,template,'bindTiles');
    }
    else{
        pageContent.html('<div class="col-sm-12"><div class="alert alert-info" role="alert">We could not find any events that match your search criteria</div></div>');
    }

}

function displayBrands(){
    $.each(vaultLoader.getBrands(),function(){
        $('#brands-container').append('<div class="col-lg-3 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link selectableItem" style="margin-bottom: 5px;" onclick="addOrFilter(\'brand\',\''+this.organisationId+'\')" id="brand'+this.organisationId+'">'+this.name+'</div></div>');
    })

}

function displayInterests(){
    $.each(vaultLoader.getInterests(),function(){
        $('#interests-container').append('<div class="col-lg-3 col-md-4 col-md-6"><div class="alert alert-plain alert-thin link interest-opt selectableItem" style="margin-bottom: 5px;" id="interest'+this.interestId+'" onclick="addOrFilter(\'interest\',\''+this.interestId+'\')">'+this.name+'</div></div>');
    })

}

function resetFilters(){
    $('.selectableItem').removeClass('alert-success').addClass('alert-plain');
    vaultLoader.resetFilters();
    displayResults();
}

function bindTiles(){
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

function addXorFilter(property,value){
    var filterGroup=$('.'+property);
    var thisFilter=$('#'+property+value);
    if(thisFilter.hasClass('alert-success')){
        filterGroup.removeClass('alert-success').addClass('alert-plain');
        vaultLoader.addFilter(property,null);
    }
    else{
        filterGroup.removeClass('alert-success').addClass('alert-plain');
        thisFilter.removeClass('alert-plain').addClass('alert-success');
        vaultLoader.addFilter(property,value);
    }

    displayResults();
}

function addSingleFilter(property,value){
    var thisFilter=$('#'+property+value);
    if(thisFilter.hasClass('alert-success')){
        thisFilter.removeClass('alert-success').addClass('alert-plain');
        vaultLoader.addFilter(property,null);
    }
    else{
        thisFilter.removeClass('alert-plain').addClass('alert-success');
        vaultLoader.addFilter(property,value);
    }
    displayResults();
}

function addOrFilter(property,value){
    var currentFilters=vaultLoader.getFilters();
    var currentProperty=currentFilters[property];

    var thisFilter=$('#'+property+value);
    if(currentProperty==null){
        currentProperty=[];
    }

    if(thisFilter.hasClass('alert-success')){
        thisFilter.removeClass('alert-success').addClass('alert-plain');
        currentProperty.splice(currentProperty.indexOf(value),1);
        vaultLoader.addFilter(property,currentProperty);
    }
    else{
        thisFilter.removeClass('alert-plain').addClass('alert-success');
        currentProperty.push(value);
        vaultLoader.addFilter(property,currentProperty);
    }
    displayResults();
}

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
    vaultLoader.setSearchLatLng(geocodeResults[index].geometry.location.lat(),geocodeResults[index].geometry.location.lng());
    $('#postcode').val(geocodeResults[index].formatted_address);
    $('#georesults').empty();
    $('.distanceOrder').prop('disabled',false);
    displayResults();
}

function setResultsView(view){
    resultView=view;
    displayResults();
}