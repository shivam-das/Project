<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
    
<head>
     <link href="../style.css" rel="stylesheet" type="text/css" />
<!-- CuFon: Enables smooth pretty custom font rendering. 100% SEO friendly. To disable, remove this section -->
<script type="text/javascript" src="js/cufon-yui.js"></script>
<script type="text/javascript" src="js/arial.js"></script>
<script type="text/javascript" src="js/cuf_run.js"></script>
    <script>
    function reset(){
        var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    var a = xmlhttp.responseText;
                    
                    console.log(a);
                }
        };
        xmlhttp.open("POST", "reset.php", true);
                
                xmlhttp.send();
    }
        
        
    
    </script>
     <div class="header">
    <div class="header_resize">
     <div class="logo"><h1><a href="home.html" class="nobg">Transit Planner</a></h1></div>
      <div class="menu_nav">
        <ul>
          <li><a href="../index.html">Home</a></li>
          <li class="active"><a href="segment.php">Segmentation</a></li>
            <li><a href="../ITRA_Testing/client1.html">Transit</a></li>
            <li><a href="../junction/junction.html">Landmark</a></li>
          <li><a href="../Heatmap/heatmap.php">HeatMap</a></li>
        </ul>
      </div>
     
    </div>
 </div>
 
    <div id="cssmenu">
	<ul>
  <li><a href="segment.php">Cutter</a></li>
  <li><a class="active" href="GroundTruth.php">GroundTruth</a></li>
</ul>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
      <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCc5_z2Zruh01mevWGVavrIZ6D5N5Q_lqc&sensor=false&libraries=geometry">
    </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    </head>
<body onload="initialize()">

    <div id="nav" >
    <h3>The Trail Processing is:</h3>
    
     
    	<?php
     $myFile = $_FILES['fileinput']['name'];
    $myGT = $_FILES['my_file']['name'];
    $GT = str_replace(' ','_',$myGT);
    $FILE = str_replace(' ','_',$myFile);
    $segdir = 'SegmentedTrails/';
    $gt = 'GroundTruth/';
    $uploadfile = $segdir. basename($_FILES['fileinput']['name']);
    $uploadFile = $gt. basename($_FILES['my_file']['name']);
    move_uploaded_file($_FILES['fileinput']['tmp_name'],$uploadfile);
    move_uploaded_file($_FILES['my_file']['tmp_name'],$uploadFile);
    
    
    //echo $myFile." ".$myGT;
    echo 'Done';
        echo passthru('python compare.py'.' '.$GT.' '.$myFile);
    
    ?>
    	
	
   
        <span>
        <hr>
        <h2>Plot BusStops on Segmented Trail: </h2>

        <h3>Choose Segmented Trail:</h3>
            <input type="file" id="trail" onchange="upload()"/>
<div style="color: #555555" id="uploading"></div>
        <h3>Choose Ground Truth file:</h3>
    <input type="file" id="groundtruth" onchange="uploadGT()"/>
        <br>
        <br><div style="color: #555555" id="uploadingGT"></div>
    
    <input type="button" onclick="reset();" value="RESET" class="button">
   
    

</div>

<div id="map_canvas" style="width: 80%; height:800px; float:right; border: 2px solid #dddddd;"></div>

<script type="text/javascript">



var map;
var Wait;
var color="blue";

function upload() {
      document.getElementById("uploading").innerHTML="uploading....";
      var myfile=document.getElementById("trail").files[0];
      //alert(myfile.size);
      var r = new FileReader();
      
      r.onload = function(e) { 
	    var contents = e.target.result;
	    
	    parseContents(contents);
	    //document.getElementById("cont").innerHTML=fileContent;
	    
	    document.getElementById("uploading").innerHTML="File uploaded: "+myfile.name;
      }
   
      r.readAsText(myfile);
}
function uploadGT(){
    document.getElementById("uploadingGT").innerHTML="uploading Ground Truth BusStops....";
    var myfile2=document.getElementById("groundtruth").files[0];
    var r1 = new FileReader();
    r1.onload = function(e1){
        var con = e1.target.result;
        parseGroundTruth(con);
        document.getElementById("uploadingGT").innerHTML="File uploaded:"+myfile2.name;
    }
    r1.readAsText(myfile2);
}
function parseContents(contents)
{
		
	var totalContent=contents.split("\n");
	var lat=new Array();
	var long=new Array();
	var speed=new Array();
	var bearing=new Array();
	var timestamp=new Array();
	var Bearing=new Array();
	var LatLong=new Array()
	var polyOptions = {
	    strokeColor: 'green',
	    strokeOpacity: 1.0,
	    strokeWeight: 3
	  }
	var g=-1;
	var m=0;
	var c=0;

	var f_size=totalContent.length;
	for(var i=1;i<totalContent.length;i++)
	{
		j=i-1;
		if(isNaN(totalContent[i]))
		{	//alert(totalContent[i]);
			var latlong=totalContent[i].split(",");
			//alert(latlong[0]);
			lat[j]=parseFloat(latlong[0]);
			long[j]=parseFloat(latlong[1]);
			speed[j]=parseFloat(latlong[2]);
			bearing[j]=parseFloat(latlong[3]);
			timestamp[j]=parseFloat(latlong[4]);



			//alert(lat[i]);
		if(j>0)
		{
			Bearing[j]=bearingCalc(lat[j-1],long[j-1],lat[j],long[j]);
		}	plotCluster(lat[j],long[j],speed[j],bearing[j],timestamp[j],Bearing[j],color);				
		}
		
	}
		alert(long);
   
    google.maps.event.addListener(map, 'click', clickedOnMap);
    
	
	/*poly1 = new google.maps.Polygon(polyOptions);
		poly1.setMap(map);
		var path=poly1.getPath();
		for(var i=0;i<f_size;i++)
		{
			
			var latlong = new google.maps.LatLng(lat[i],long[i],true);
			path.push(latlong);
		}
        
		alert(poly1.getPath());*/
}
function parseGroundTruth(con){
    var totalCon = con.split("\n");
    var latGT = new Array();
    var lngGT = new Array();
    
    var size = totalCon.length;
    for(var i=1;i<totalCon.length;i++)
        {
            var j=i-1;
            if(isNaN(totalCon[i]))
                {
                    var latlngGT = totalCon[i].split(",");
                    latGT[j] = parseFloat(latlngGT[0]);
                    lngGT[j] = parseFloat(latlngGT[1]);
                    plotMarker(latGT[j],lngGT[j]);
                }
        }
}

function plotCluster(lat,long,speed,bearing,timestamp,Bearing,color)
{

	var latlong = new google.maps.LatLng(lat,long);
	map.setCenter(latlong);
	var contents="Lat: "+ lat+ "Long: "+long+" Bearing: "+bearing+" C Bearing:"+ Bearing;
	var infowindow = new google.maps.InfoWindow({
      content: contents
  });
   
	  var marker = new google.maps.Marker({
		  position:latlong,
		  icon:{ path: google.maps.SymbolPath.CIRCLE, fillColor: color,  fillOpacity: 0.8,scale: 1.5,strokeColor:color},     
		  
		  map:map
	    });
   
     overideMarkerOnClick(marker);
  
}
function plotMarker(latGT,lngGT)
    {
        var latlongGT = new google.maps.LatLng(latGT,lngGT);
        map.setCenter(latlongGT);
        var cont = "Lat: "+latGT + "Long: "+ lngGT;
        var info = new google.maps.InfoWindow({
            content: cont
        });
   var mark = new google.maps.Marker({
            position: latlongGT,
            icon : "bus_1.png",
            map: map
        });
        overideMarkerOnClick(mark);
    }
function bearingCalc(lat1,long1,lat2,long2)
{
	var dLon=long2-long1;
	var y = Math.sin(dLon) * Math.cos(lat2);
	var x = Math.cos(lat1)*Math.sin(lat2) -
     Math.sin(lat1)*Math.cos(lat2)*Math.cos(dLon);
	var brng = Math.atan2(y, x)*57.2957795;
	brng=(brng+360)%360;
	return(brng);
}

function initialize() {
	
        geocoder = new google.maps.Geocoder();
 
 
        var myOptions = {
          center: new google.maps.LatLng(22.546,88.354),
          zoom: 13,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);

}



</script>
    <script src = "Ends.js"></script>
    

</body>
</html>
