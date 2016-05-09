function clickedOnMap(event)
{
    getlatlng(event.latLng,map);
}

function getlatlng(location,map){
     var marker = new google.maps.Marker({
        position: location,
        //icon:{ path: google.maps.SymbolPath.CIRCLE, fillColor: "red",  fillOpacity: 0.8,scale: 3.5,strokeColor:color},     
        icon: 'http://google-maps-icons.googlecode.com/files/bus.png',
        //label: labels[labelindex++ % labels.length],
         map: map
    });
    
    var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            console.log(xmlhttp.responseText);
        }
    }
    xmlhttp.open("POST", "info.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    var params = "lat=" + location.lat();
    params += "&lng=" + location.lng();
    console.log(params);
    xmlhttp.send(params);
    
}
function overideMarkerOnClick(marker) {
    google.maps.event.addListener(marker, 'click', function() {
        getlatlng(this.getPosition());
    });
    
}