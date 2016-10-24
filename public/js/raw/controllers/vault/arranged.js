$(document).ready(function(){
    $('#enlarge').click(function(){
        $('#col1').toggleClass('col-sm-6 col-sm-12');
        $('#col2').toggleClass('col-sm-6 col-sm-12');
        $('.thumb').toggleClass('col-sm-3 col-sm-2');
    })
})


function initialize(postcode) {
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        'address': postcode
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            var myOptions = {
                zoom: 12,
                center: results[0].geometry.location,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);

            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
        }
    });
    google.maps.event.addDomListener(window, 'load', initialize);
}

function shareItem(){
    $('#share-error').stop().fadeOut('fast');
    $.when(shareItemAjax()).then(function(data){
    $('#share-success').stop().fadeOut('fast').fadeIn('fast');
    });
}

function shareItemAjax(){
    return $.ajax({
    url: "/vault/share/"+eventId,
    type: 'post',
    dataType: 'json',
    cache: false,
    data: $('#share-form').serialize()
    });
}