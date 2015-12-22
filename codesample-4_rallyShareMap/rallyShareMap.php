<!DOCTYPE html >
<html>

<head>
<title>Rally for America</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>

<body class="rally">

<?php 
//The last user id: $uid
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 
<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/tags/markerclusterer/1.0/src/markerclusterer_packed.js"></script>
<script type="text/javascript" src="http://www.geocodezip.com/scripts/downloadxml.js"></script>
<script type="text/javascript"> 
var markers = [];
var infowindows = []; 
var map;
var mc;
var openWindow = false;
var id = "";
function infoWindowEvent(map, infowindow, marker) {
	return function() {
		if(openWindow) {
			openWindow.close();
		}
		openWindow = infowindow;
		infowindow.open(map, marker);
	};  
}
function initialize() {
  // create the map
  var myOptions = {
    zoom: 3,
    center: new google.maps.LatLng(49.625073,-112.917969),
    mapTypeControl: true,
    navigationControl: true,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  map = new google.maps.Map(document.getElementById("map"), myOptions);
	downloadUrl("/supportersExample.xml", function(doc) {
		var xmlDoc = xmlParse(doc);
		var supporters = xmlDoc.documentElement.getElementsByTagName("supporter");
		for (var i = 0; i < supporters.length; i++) {
			// obtain location for each marker
			var lat = parseFloat(supporters[i].getElementsByTagName("latitude")[0].childNodes[0].nodeValue);
			var lng = parseFloat(supporters[i].getElementsByTagName("longitude")[0].childNodes[0].nodeValue);
			var point = new google.maps.LatLng(lat,lng);
			// info window
			var name = (supporters[i].getElementsByTagName("first_name")[0].textContent||supporters[i].getElementsByTagName("first_name")[0].text);		
			if(!name||name===''||name==undefined){
				name = parseFloat(supporters[i].getElementsByTagName("id")[0].childNodes[0].nodeValue);
			}
			var mContent = "<div style=\"\">" + name + " is Rallying for America</div>";
			infowindows[i] = new google.maps.InfoWindow({
				content: mContent,
				maxWidth: 210
			});
			// create the marker
			markers[i] = new google.maps.Marker({
				position: point
			});
			google.maps.event.addListener(markers[i], 'click', infoWindowEvent(map, infowindows[i], markers[i]));
		}
		var mcOptions = {maxZoom: 9}
		mc = new MarkerClusterer(map, markers, mcOptions);
	});
	google.maps.event.addListener(map,'click',function() {
		if(openWindow) {openWindow.close();}
	});
}
google.maps.event.addDomListener(window, 'load', initialize);
</script> 

<div id="map-container">
	<div id="map" style="width: 620px; height:478px;"></div>
</div>

</body>
</html>
