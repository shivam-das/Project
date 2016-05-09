            var apikeys = ['AIzaSyCqi5sXaHEQeNutMf2EpOb87mVyiWW5ndY', 'AIzaSyAAe-scFiDk6a1tjn2LuGhbr1-rQU4XXw0', 'AIzaSyB-CAwY9h4-BcUJFc0L9vkd-XVXs8GSC_Y', 'AIzaSyAr1Emq9B_NURCjfAlEGQSyw1juILocuUM', 'AIzaSyAvdyTWT0ooWAYwR9OkmgAPZZ0dv57_A5Y', 'AIzaSyDYTu3VJkCrp9kDDm0w1pDMx22JHf6g7VM', 'AIzaSyBlr7sqnUd-QRkG3roJNiasjM8JAfb1SUs'
            , 'AIzaSyAUsJNklSoGphEkHduRIuGBqlYWSBw0DE4', 'AIzaSyDjiVBQ6NOX1mJsHJYdyzbYsjPYvKJMX4A', 'AIzaSyDrCvsa61ih2IP3KwnF9PqW--B9_OjHtMo', 'AIzaSyDeawCIk6UMuNb2SAdF9iVQX735YWfMX9g'];
            var apikeyindex = 0;

            // var points = <?php echo json_encode($points) ?>;

            Number.prototype.toRad = function () {
                return this * Math.PI / 180;
            }

            Number.prototype.toDeg = function () {
                return this * 180 / Math.PI;
            }

            var initMapCenter = new google.maps.LatLng(23.5491169, 87.2909367);

            //var s = new google.maps.LatLng(23.547346602830377, 87.2829008102417),
            //d = new google.maps.LatLng(23.551418415492808, 87.28616237640381);

            var map;
            var directionsService;

            var junctionsDone = false,
                landmarksDone = false;

            function initialize() {
                var mapOptions = {
                    center: initMapCenter,
                    zoom: 15,
                    scaleControl: true
                };

                map_canvas = document.getElementById('map-canvas');
                map = new google.maps.Map(map_canvas, mapOptions);

                directionsService = new google.maps.DirectionsService();

                //reqDir(s, d);
            }

            function start() {
                var ps = [];
                for (var i = 0; i < points.length; i++) {
                    var point = points[i],
                        p = new google.maps.LatLng(point[0], point[1]);
                    ps.push(p);

                    //placeColorMarker(p, 'black');
                    //var radiuskm = 0.05;
                    //var netll = p.destinationPoint(120, radiuskm);
                }
                points = ps;

                places = {};
                for (var i = 0; i < typesToCheck.length; i++) {
                    var type = typesToCheck[i];
                    places[type] = {};
                }

                lastintervalPoint = points[0];
                junctionPoints(points, 1);
                checkpoints(points, 1);
            }

            var lastintervalPoint,
                lastJunctionPoint,
                intervalm = 1000,
                junctionDistance = 1000,
                distance = 0,
                dist = 0,
                km = 0;

            var junctions = {},
                landmarks = {};

            var total_intersection = 0,
                total_landmarks = 0;

            var landmarksCount = 0;

            var intersections = 0,
                leftMarkers = [],
                rightMarkers = [];
            var str="",
                rows="";
            var typesToCheck = [];
            /*var typesToCheck = [
            'atm',
            'bank',
            'bus_station',
            'book_store',
            'clothing_store',
            'convenience_store',
            'department_store',
            'furniture_store',
            'grocery_or_supermarket',
            'hardware_store',
            'home_goods_store',
            'hospital',
            'jewelry_store',
            'shoe_store',
            'shopping_mall',
            'store',
            'school',
            'hindu_temple',
            'mosque',
            'place_of_worship',
            ];*/

            var priorityValue = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1];

            function getlandmarks() {
                var checkboxes = document.getElementsByName('landmarks[]');
                //typesToCheck = [];
                var j = 0;
                for (var i = 0, n = checkboxes.length; i < n; i++) {
                    if (checkboxes[i].checked) {
                        typesToCheck[j++] = checkboxes[i].value;

                    }
                }
                /*    if(vals)
                        vals=vals.substring(1);
                    console.log(vals);*/
                console.log(typesToCheck);
                //echo json_encode(vals);
            }

            function write_all() {
                if (junctionsDone && landmarksDone) {
                    var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
                    xmlhttp.onreadystatechange = function () {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                            console.log(xmlhttp.responseText);
                            alert(xmlhttp.responseText);
                        }
                    }
                    xmlhttp.open("POST", "write_all.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    //var rows = "";
                    // for (var km in junctions) {
                    //     if (junctions.hasOwnProperty(km))
                    //         // rows += km + "," + landmarks[km] + "," + junctions[km] + "\n";
                    rows = str + "\n" + "Total Junction points: " + total_intersection + "\n";
                    // }
                    var params = "rows=" + rows;
                    xmlhttp.send(params);
                    write_screen();
                }
            }

            function removeCluster(markers, pi) {
                for (var i = markers.length - 1; i >= 1; i--) {
                    var x = markers[i].getPosition(),
                        y = markers[i - 1].getPosition();
                    var d = calcDistanceInMeters(x.lat(), x.lng(), y.lat(), y.lng());
                    if (d < 7) {
                        markers[i].setMap(null);
                        markers.splice(i, 1);
                        continue;
                    }

                    for (var j = 0; j <= pi; j++) {
                        y = points[j];
                        var d = calcDistanceInMeters(x.lat(), x.lng(), y.lat(), y.lng());
                        if (d < 7) {
                            markers[i].setMap(null);
                            markers.splice(i, 1);
                            break;
                        }
                    }
                }

                var x = markers[0].getPosition();
                for (var j = 0; j <= pi; j++) {
                    y = points[j];
                    var d = calcDistanceInMeters(x.lat(), x.lng(), y.lat(), y.lng());
                    if (d < 7) {
                        markers[0].setMap(null);
                        markers.splice(0, 1);
                        break;
                    }
                }

                return markers;
            }

            var intervalIndex = 0,
                places = {};

            function junctionPoints(roadpoints, i) {
                if (i >= roadpoints.length) {
                    console.log(junctions);
                    junctionsDone = true;
                    write_all();
                    return;
                }

                //console.log(roadpoints[i].lat() + " " + roadpoints[i].lng());

                var p1 = roadpoints[i - 1],
                    p2 = roadpoints[i];

                dist += calcDistanceInMeters(p1.lat(), p1.lng(), p2.lat(), p2.lng());
                if (dist >= intervalm) {
                    dist = 0;
                    km += intervalm / 1000;

                    if (leftMarkers.length >= 2) {
                        leftMarkers = removeCluster(leftMarkers, i);
                    }
                    if (rightMarkers.length >= 2) {
                        rightMarkers = removeCluster(rightMarkers, i);
                    }

                    intersections = leftMarkers.length + rightMarkers.length;

                    total_intersection += intersections;

                    console.log(km + " km intersections: " + intersections);
                    //writeIntersection(km + "," + lastJunctionPoint.lat() + "," + lastJunctionPoint.lng() + "," + p2.lat() + "," + p2.lng() + "," + intersections);
                    junctions[km] = intersections;

                    intersections = 0;
                    leftMarkers = [];
                    rightMarkers = [];

                    placeMarker(p2, km + " km");
                    lastJunctionPoint = p2;
                }

                if (p1.lat() == p2.lat() && p1.lng() == p2.lng()) {
                    junctionPoints(roadpoints, i + 1);
                } else {
                    map.setCenter(p2);
                    placeColorMarker(p2, 'black');
                    var newp = sidePoint(p1, p2),
                        rp = newp.right,
                        lp = newp.left;

                    //placeColorMarker(rp, 'blue');
                    //placeColorMarker(lp, 'blue');
                    $.ajax({
                        url: "https://roads.googleapis.com/v1/snapToRoads?interpolate=true" + "&key=" + apikeys[apikeyindex] + "&path=" + rp.toUrlValue() + "|" + lp.toUrlValue(),
                        statusCode: {
                            429: function (response) {
                                //console.log("ohhhh nooooooo");
                                apikeyindex = (apikeyindex + 1) % apikeys.length;
                                junctionPoints(roadpoints, i);
                            },
                            400: function (response) {
                                junctionPoints(roadpoints, i + 1);
                            }
                        },
                        success: function (data) {
                            if (!jQuery.isEmptyObject(data)) {
                                //console.log(data);
                                for (var j = 0; j < data.snappedPoints.length; j++) {

                                    var location = data.snappedPoints[j].location;

                                    var lat_new = Number(location.latitude),
                                        lng_new = Number(location.longitude);
                                    //placeColorMarker(new google.maps.LatLng(lat_new,lng_new), 'orange');

                                    var d = calcDistanceInMeters(lat_new, lng_new, rp.lat(), rp.lng());
                                    var count = 0;
                                    //console.log("right " + d);
                                    if (d < 3.5) {
                                        rightMarkers.push(placeColorMarker(rp, 'red'));
                                        count = 1;
                                    }
                                    d = calcDistanceInMeters(lat_new, lng_new, lp.lat(), lp.lng());
                                    //console.log("left " + d);
                                    if (d < 3.5) {
                                        leftMarkers.push(placeColorMarker(lp, 'red'));
                                        count = 1;
                                        //reqDir(lp,points[i]);
                                    }
                                    //intersections+=count;
                                }
                            }
                            junctionPoints(roadpoints, i + 1);
                        },
                        complete: function (e) {
                            //console.log(e.status);
                        }
                    });
                }
            }

            var radius = 50,
                newRadius = 0,
                count=0;

            function write_custom() {
                //var str = "";
                for (type in places) {
                    if (places.hasOwnProperty(type)) {
                        //console.log(type);
                        str+=type+": "
                        var array = places[type];
                        for (index in array) {
                            if (array.hasOwnProperty(index)){
                                count+=array[index];
                            }
                        }
                                str+=count;
                                str+=",\n";
                        count=0;
                    }
                }
                
                console.log(str);
            }

            function checkpoints(roadpoints, i) {
                if (i >= roadpoints.length) {
                    //console.log(places);
                    //writeNearbys();
                    console.log(places);

                    write_custom();
                    landmarksDone = true;
                    write_all();
                    //junctionPoints(points, 1);
                    return;
                }

                //writeNearbys();
                var p2 = roadpoints[i];

                placeColorMarker(p2, "black");
                map.setCenter(p2);
                var dp = calcDistanceInMeters(lastintervalPoint.lat(), lastintervalPoint.lng(), p2.lat(), p2.lng());
                distance += dp;
                //newRadius += d;
                lastintervalPoint = p2;
                if (distance >= intervalm) {
                    intervalIndex += 1;

                    landmarks[intervalIndex] = landmarksCount;
                    total_landmarks += landmarksCount;
                    landmarksCount = 0;
                    //}

                    //if (newRadius >= radius * 2) {
                    //newRadius = 0;

                    for (var p = 0; p < typesToCheck.length; p++) {
                        places[typesToCheck[p]][intervalIndex] = 0;
                    }

                    lastintervalPoint = p2;
                    distance = 0;
                    // Specify location, radius and place types for your Places API search.
                    var request = {
                        location: p2,
                        radius: intervalm / 2,
                        //radius: radius,
                        types: typesToCheck
                    };


                    // Create the PlaceService and send the request.
                    // Handle the callback with an anonymous function.
                    var service = new google.maps.places.PlacesService(map);
                    service.nearbySearch(request, function (results, status) {
                        if (status == google.maps.places.PlacesServiceStatus.OK) {
                            //console.log(results);
                            for (var j = 0; j < results.length; j++) {
                                var place = results[j],
                                    types = place.types;

                                for (var p = 0; p < typesToCheck.length; p++) {
                                    if (types.indexOf(typesToCheck[p]) > -1) {
                                        places[typesToCheck[p]][intervalIndex] += 1;
                                        // landmarksCount += 1;
                                        landmarksCount += priorityValue[p];
                                        console.log(typesToCheck[p] + " found");
                                        placeMarker(place.geometry.location, place.name);
                                        break;
                                    }
                                }
                            }
                        }
                        checkpoints(roadpoints, i + 1);
                    });
                } else {
                    checkpoints(roadpoints, i + 1);
                }
                //console.log(c_atm + " " + c_bank + " " + c_book_store);
                //if (p1.lat() == p2.lat() && p1.lng() == p2.lng()) {
            }

            function calcDistanceInMeters(lat1, lng1, lat2, lng2) {
                //alert("in calDiatance");
                var R = 6371000; // Radius of the earth in metres

                var dLat = (lat2 - lat1).toRad(); // deg2rad below
                var dLon = (lng2 - lng1).toRad();
                //console.log("lat "+(lat_new - lat1));
                //console.log("lng "+(lng_new - lng1));
                var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(lat1.toRad()) * Math.cos(lat2.toRad()) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                var d = Number(R * c); // Distance in metres
                return d;
            }


            function reqDir(source, dest) {
                var request = {
                    origin: source,
                    destination: dest,
                    provideRouteAlternatives: true,
                    travelMode: google.maps.TravelMode.WALKING
                };

                directionsService.route(request, function (response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        var directionsDisplay = new google.maps.DirectionsRenderer({
                            map: map,
                            directions: response,
                            preserveViewport: true,
                            routeIndex: 0,
                            suppressMarkers: true,
                            suppressPolylines: false
                        });

                        /*
                        var dirroute = response.routes[0].overview_path;
                        for (var j = 0, n = dirroute.length; j < n; j++) {
                            var latlng = dirroute[j];

                            var radiuskm = 0.05;
                            var netll = latlng.destinationPoint(120, radiuskm);
                            reqDir(latlng, netll);
                            //placeColorMarker(latlng, 'black');
                            //placeColorMarker(netll, 'red');
                        }*/
                    }
                });
            }

            function placeColorMarker(latlng, color) {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        fillColor: color,
                        fillOpacity: 0.8,
                        scale: 1.5,
                        strokeColor: color
                    },
                    animation: google.maps.Animation.DROP,
                    visible: true
                });
                return marker;
            }

            function placeMarker(latlng, title) {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: title
                });
                marker.setIcon("http://chart.apis.google.com/chart?chst=d_map_spin&chld=0.9|0|FFFF42|11|b|" + title);
                return marker;
            }

            function sidePoint(point1, point2) {
                var lng1 = point1.lng(),
                    lat1 = point1.lat(),
                    lng2 = point2.lng(),
                    lat2 = point2.lat();

                var N = 0.00035;

                var dlat = lat1 - lat2,
                    dlng = lng1 - lng2,
                    d = Math.sqrt(dlat * dlat + dlng * dlng);

                var ulat = dlat / d,
                    ulng = dlng / d;

                var nlat1 = lat1 + (N / 2) * ulng,
                    nlng1 = lng1 - (N / 2) * ulat,

                    nlat2 = lat1 - (N / 2) * ulng,
                    nlng2 = lng1 + (N / 2) * ulat;

                var l = new google.maps.LatLng(nlat2, nlng2),
                    r = new google.maps.LatLng(nlat1, nlng1);

                //console.log("dist "+calcDistanceInMeters(l.lat(),l.lng(),r.lat(),r.lng()));
                return {
                    right: r,
                    left: l
                };
            }
        function write_screen(){
            var Str = "";
            Str+="<h3><font color='#ffffff'>"+rows+"</font></h3>";
            document.getElementById("uploading").innerHTML = Str;
        }
function uploading(){
    $('#upload').on('click',function(){
                var file_data = $('fileinput').prop('files')[0];
                var form_data = new FormData();
                form_data.append('file',file_data);
                alert(form_data);
                $.ajax({
                        url:'landmark.php',
                        dataType: 'text',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (php_script_response){
                            alert(php_script_response);
                        }
                });
            });
}

            google.maps.event.addDomListener(window, 'load', initialize);