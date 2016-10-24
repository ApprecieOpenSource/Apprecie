/**
 * Created by Daniel Dimmick on 31/03/15.
 */
function GoogleMap(latitude,longitude,zoom){
    var icons={fashion: { icon: 'https://admin.apprecie.com/img/temp/map/apprecie.png'}};
    var mapOptions=null;
    var markers = [];
    var map=null;

    var setMap=function(element){
        map=new google.maps.Map(document.getElementById(element),getMapOptions());
    }
    var setMapOptions=function(options){
        mapOptions=options;
    }
    var getLatitude=function(){
        return latitude;
    }
    var getLongitude=function(){
        return longitude;
    }
    var getMapOptions=function(){
        return mapOptions;
    }
    var getZoom=function(){
        return zoom;
    }
    var getMap=function(){
        return map;
    }

    this.initialise=function(element){
        setMapOptions({center: { lat: getLatitude(), lng: getLongitude()}, zoom: getZoom()});
        setMap(element);
        loadMarkers();
    }

    this.addMarker=function(latitude,longitude,contentString){
        var myLatlng = new google.maps.LatLng(latitude,longitude);
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            icon: icons['fashion'].icon
        });
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.open(getMap(),marker);
        });
        markers.push(marker);
    }

    loadMarkers=function(){
        var mcOptions = {gridSize: 15, maxZoom: 15};
        var markerCluster = new MarkerClusterer(getMap(), markers,mcOptions);
    }


}