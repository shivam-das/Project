var gtds = [];

function gtd(marker, busstop) {
    this.marker = marker;
    this.busstop = busstop;
}

var busstopid = 1;

function clickedOnMap(event) {
    //return;
    getBusStop(event.latLng);
}

function getBusStop(latlng) {

    var busstop = prompt("Marker Number?");
    busstop = busstop == null ? "" : busstop;

    var marker = new google.maps.Marker({
        position: latlng,
        //icon:{ path: google.maps.SymbolPath.CIRCLE, fillColor: "red",  fillOpacity: 0.8,scale: 3.5,strokeColor:color},     
        icon: 'http://google-maps-icons.googlecode.com/files/bus.png',
        map: map,
        title: busstop
    });

    var infowindow = new google.maps.InfoWindow({
        content: busstop
    });

    infowindow.open(map, marker);

    google.maps.event.addListener(marker, 'click', function () {
        marker.setMap(null);
        for (var i = 0; i < gtds.length; i++) {
            if (gtds[i].marker == marker) {
                gtds.splice(i, 1);
                break;
            }
        }
    });

    gtds.push(new gtd(marker, busstop));
}

function writetofile() {

    var lines = [];
    for (var i = 0; i < gtds.length; i++) {
        var latlng = gtds[i].marker.getPosition(),
            busstop = gtds[i].busstop;
        lines.push(latlng.lat() + "," + latlng.lng() + "," + gtds[i].busstop);
    }
    //console.log(JSON.stringify(lines));
    //return;

    var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            console.log(xmlhttp.responseText);
        }
    }
    xmlhttp.open("POST", "info.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    /*var params = "busstopid=" + (busstopid++);
    params += "&lat=" + latlng.lat();
    params += "&lng=" + latlng.lng();
    params += "&busstop=" + encodeURIComponent(busstop);*/
    var params="data="+JSON.stringify(lines);
    console.log(params);
    xmlhttp.send(params);
}


function overideMarkerOnClick(marker) {
    google.maps.event.addListener(marker, 'click', function () {
        getBusStop(this.getPosition());
    });

}