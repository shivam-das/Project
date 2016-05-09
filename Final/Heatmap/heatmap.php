<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
   
<head>
<link href="../style.css" rel="stylesheet" type="text/css" />
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />

<!-- CuFon: Enables smooth pretty custom font rendering. 100% SEO friendly. To disable, remove this section -->
<script type="text/javascript" src="js/cufon-yui.js"></script>
<script type="text/javascript" src="js/arial.js"></script>
<script type="text/javascript" src="js/cuf_run.js"></script>
 <div class="header">
    <div class="header_resize">
     <div class="logo"><h1><a href="../index.html" class="nobg">Transit Planner</a></h1></div>
      <div class="menu_nav">
        <ul>
          <li><a href="../index.html">Home</a></li>
          <li ><a href="../Segmentation/segment.php">Segmentation</a></li>
          <li><a href="../ITRA_Testing/client1.html">Transit</a></li>
          <li><a href="../junction/junction.html">Landmark</a></li>
            <li class="active"><a href="heatmap.php">HeatMap</a></li>
        </ul>
      </div>
     
    </div>
 </div>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
    <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery.form.min.js"></script>
      <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCc5_z2Zruh01mevWGVavrIZ6D5N5Q_lqc&sensor=false&libraries=geometry">
    </script>
    <style>
        body{
            font-family: raleway;
        }
        #legend{
            background-color:rgba(0, 0, 0, 0.53);
            padding: 5px;
        }
        #nav{
            height:716px;
        }
     
    </style>
    </head>
<body onload="initialize()">

    <div id = "nav">
        <br>
        <form action="processupload.php" method="post" enctype="multipart/form-data" id="MyUploadedForm">
            <p style="text-decoration:underline; font-size:15px; font-weight:600;">Choose A Trail File:</p>
            <input type="file" id="FileInput" name="FileInput"/>
            <br>
            <input type="submit"  id="submit-btn" value="Upload" />
            <br>
            <div id="output"></div>
        </form>
        <br>
        <form action="upload.php" method="post" enctype="multipart/form-data" id ="UploadedForm">
            <p style="text-decoration:underline; font-size:15px; font-weight:600;">Choose A Heatmap Detail File:</p>
              <input type="file" name="fileinput" id="fileinput"/>
            <br>
            <input type = "submit" id = "submit" value="Upload"/>
            <div id = "output1"></div>
            </form>
        <br>
            <input type="button" onclick="route();" value="Chaosness" id="button" class="button">
    </div>
   
    <div id="legend">
        <div class="le"><img src="red.png"><b> = CHAOTIC ROAD</b>
                    </div>
                    <br>
                    <div class="le"><img src="yellow.png"><b> = NORMAL</b>
                    </div>
                    <br>
                    <div class="le"><img src="blue.png"><b> = NON-CHAOTIC ROAD</b>
                    </div>
                </div>
<div id="map_canvas" style="width: 80%; height:720px; float:right; border: 2px solid #dddddd;"></div>

<script type="text/javascript">



var map;
var Wait;
var color,plo;
var polyline = [];
var linemas = [];

function route(){
     var xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    var a = xmlhttp.responseText;
                    var latlngStr = a.split("\n");
                    console.log(a);
                    var i=1;
                    for (; i < latlngStr.length; i++) {
                        var col  = latlngStr[i].split(",");
                        var prev_color = col[2];
                            while (latlngStr[i] != "end" ){//|| latlngStr[i] != "break") {
                                var latlnga = latlngStr[i].split(",");
                                if(latlnga[2] == prev_color){
                                var slat1 = parseFloat(latlnga[0]);
                                var slng1 = parseFloat(latlnga[1]);
                                color = parseInt(latlnga[2]);
                                var varp = new google.maps.LatLng(slat1, slng1);
                                polyline.push(varp);
                                //kerTrail(varp, color);
                                //temp.push(varp);
                                i++;
                                }
                                else{
                                     addPolyline(polyline, color);
                                     polyline = [];
                                        prev_color = latlnga[2];
                                        
                                }
                            }
                        addPolyline(polyline, color);
                        polyline = [];
                          
                }
            }
            };
                xmlhttp.open("POST", "get-points.php", true);
                xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xmlhttp.send();
}
            function addPolyline(polyline, color) {
            //alert(color);
            if (color == 1)
                l = '#FF0000';
            else if (color == 2)
                l = '#ffbb33';
            else if (color == 3)
                l = '#2129e8';
           plo = new google.maps.Polyline({
                path: polyline,
                //geodesic:true,
                strokeColor: l,
                strokeOpacity: 5.0,
                strokeWeight: 5
            });
            plo.setMap(map);
            //polyline=[ ];
            //linemas.push(plo);
            console.log(polyline);
        }

function initialize() {
	
        geocoder = new google.maps.Geocoder();
 

       var myOptions = {
                center: new google.maps.LatLng(23.542271413405505, 87.29504853487015),
                disableDefaultUI: true,
                zoom: 13,
                zoomControl: true
            };
        map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(document.getElementById('legend'));

}

$(document).ready(function() { 
	var options = { 
			target:   '#output',   // target element(s) to be updated with server response 
			beforeSubmit:  beforeSubmit,  // pre-submit callback 
			success:       afterSuccess,  // post-submit callback 
			uploadProgress: OnProgress, //upload progress callback 
			resetForm: true        // reset the form after successful submit 
		}; 
		
	 $('#MyUploadedForm').submit(function() { 
			$(this).ajaxSubmit(options);  			
			// always return false to prevent standard browser submit and page navigation 
			return false; 
		}); 
		

//function after succesful file upload (when server response)
function afterSuccess()
{
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
	$('#progressbox').delay( 1000 ).fadeOut(); //hide progress bar

}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
	{
		
		if( !$('#FileInput').val()) //check empty input filed
		{
			$("#output").html("Are you kidding me?");
			return false
		}
		
		var fsize = $('#FileInput')[0].files[0].size; //get file size
		var ftype = $('#FileInput')[0].files[0].type; // get file type
		

		//allow file types 
		switch(ftype)
        {
            case 'image/png': 
			case 'image/gif': 
			case 'image/jpeg': 
			case 'image/pjpeg':
			case 'text/plain':
			case 'text/html':
			case 'application/x-zip-compressed':
			case 'application/pdf':
			case 'application/msword':
			case 'application/vnd.ms-excel':
			case 'video/mp4':
                break;
            default:
                $("#output").html("<b>"+ftype+"</b> Unsupported file type!");
				return false
        }
		
		//Allowed file size is less than 5 MB (1048576)
		if(fsize>5242880) 
		{
			$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big file! <br />File is too big, it should be less than 5 MB.");
			return false
		}
				
		$('#submit-btn').hide(); //hide submit button
		$('#loading-img').show(); //hide submit button
		$("#output").html("");  
	}
	else
	{
		//Output error to older unsupported browsers that doesn't support HTML5 File API
		$("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
		return false;
	}
}

//progress bar function
function OnProgress(event, position, total, percentComplete)
{
    //Progress bar
	$('#progressbox').show();
    $('#progressbar').width(percentComplete + '%') //update progressbar percent complete
    $('#statustxt').html(percentComplete + '%'); //update status text
    if(percentComplete>50)
        {
            $('#statustxt').css('color','#000'); //change status text to white after 50%
        }
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

});

    $(document).ready(function() { 
	var options = { 
			target:   '#output1',   // target element(s) to be updated with server response 
			beforeSubmit:  beforeSubmit,  // pre-submit callback 
			success:       afterSuccess,  // post-submit callback 
			uploadProgress: OnProgress, //upload progress callback 
			resetForm: true        // reset the form after successful submit 
		}; 
		
	 $('#UploadedForm').submit(function() { 
			$(this).ajaxSubmit(options);  			
			// always return false to prevent standard browser submit and page navigation 
			return false; 
		}); 
		

//function after succesful file upload (when server response)
function afterSuccess()
{
	$('#submit').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
	$('#progressbox').delay( 1000 ).fadeOut(); //hide progress bar

}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
	{
		
		if( !$('#fileinput').val()) //check empty input filed
		{
			$("#output1").html("Are you kidding me?");
			return false
		}
		
		var fsize = $('#fileinput')[0].files[0].size; //get file size
		var ftype = $('#fileinput')[0].files[0].type; // get file type
		

		//allow file types 
		switch(ftype)
        {
            case 'image/png': 
			case 'image/gif': 
			case 'image/jpeg': 
			case 'image/pjpeg':
			case 'text/plain':
			case 'text/html':
			case 'application/x-zip-compressed':
			case 'application/pdf':
			case 'application/msword':
			case 'application/vnd.ms-excel':
			case 'video/mp4':
                break;
            default:
                $("#output1").html("<b>"+ftype+"</b> Unsupported file type!");
				return false
        }
		
		//Allowed file size is less than 5 MB (1048576)
		if(fsize>5242880) 
		{
			$("#output1").html("<b>"+bytesToSize(fsize) +"</b> Too big file! <br />File is too big, it should be less than 5 MB.");
			return false
		}
				
		$('#submit').hide(); //hide submit button
		$('#loading-img').show(); //hide submit button
		$("#output1").html("");  
	}
	else
	{
		//Output error to older unsupported browsers that doesn't support HTML5 File API
		$("#output1").html("Please upgrade your browser, because your current browser lacks some new features we need!");
		return false;
	}
}

//progress bar function
function OnProgress(event, position, total, percentComplete)
{
    //Progress bar
	$('#progressbox').show();
    $('#progressbar').width(percentComplete + '%') //update progressbar percent complete
    $('#statustxt').html(percentComplete + '%'); //update status text
    if(percentComplete>50)
        {
            $('#statustxt').css('color','#000'); //change status text to white after 50%
        }
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}
    });

</script>
</body>
</html>
