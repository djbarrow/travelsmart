<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Add International Trip Location</title>
<link rel=stylesheet href={CSS_FILE}>
<!-- BEGIN FAST_INTERNET_ACCESS -->
    <script src="{GOOGLE_MAPS_KEY}"
      type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
	var map;
	var gmarker;
	var geocoder;
    function load() {
      set_checkboxes();
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
        map.addControl(new {GMAP_CONTROL}());
        map.addControl(new GMapTypeControl());
	point=new GLatLng(52.574444,-7.550278);
	map.setCenter(point,6);
	gmarker=new GMarker(point);
	map.addOverlay(gmarker);
	GEvent.addListener(map, "click", function(marker, point) {
	pan_to(point.lat(),point.lng());
	});
	geocoder = new GClientGeocoder();	
      }
      modify_address_description();
    }
    //]]>
    </script>
</head>
<body onload="load()" onunload="GUnload()">
{LOGOS}
<table>
<tr>
<td width=50%></td><td>
<div id="map" style="{WIDTH_HEIGHT_STRING}"></div>
</td><td width=50%></td>
</tr>
</table>
<!-- END FAST_INTERNET_ACCESS -->
<!-- BEGIN POST_FAST_INTERNET_ACCESS -->
    <script src="{GOOGLE_MAPS_KEY}"
      type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
	var map;
	var gmarker;
	var geocoder;
    function load() {
      set_checkboxes();	
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
        map.addControl(new {GMAP_CONTROL}());
        map.addControl(new GMapTypeControl());
	point=new GLatLng(52.574444,-7.550278);
	map.setCenter(point,6);
        gmarker=new GMarker(point);
	map.addOverlay(gmarker);
	GEvent.addListener(map, "click", function(marker, point) {
	pan_to(point.lat(),point.lng());
	});
	geocoder = new GClientGeocoder();
	update_latlon();	
      }
    }
    //]]>
    </script>
</head>
<body onload="load()" onunload="GUnload()">
{LOGOS}
<table>
<tr>
<td width=50%></td><td>
<div id="map" style="{WIDTH_HEIGHT_STRING}"></div>
</td><td width=50%></td>
</tr>
</table>
<!-- END POST_FAST_INTERNET_ACCESS -->
<!-- BEGIN SLOW_INTERNET_ACCESS -->
    <script src="{GOOGLE_MAPS_KEY}"
      type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
	var map;
	var gmarker;
	var geocoder;
    function load() {
      set_checkboxes();
      if (GBrowserIsCompatible()) {
	geocoder = new GClientGeocoder();	
      }
      modify_address_description();
    }
    //]]>
    </script>
</head>
<body onload="load()" onunload="GUnload()">
{LOGOS}
<!-- END SLOW_INTERNET_ACCESS -->
<!-- BEGIN POST_SLOW_INTERNET_ACCESS -->
    <script src="{GOOGLE_MAPS_KEY}"
      type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
	var map;
	var gmarker;
	var geocoder;
    function load() {
      set_checkboxes();	
      if (GBrowserIsCompatible()) {
	geocoder = new GClientGeocoder();	
      }
      modify_address_description();
    }
    //]]>
    </script>
</head>
<body onload="load()" onunload="GUnload()">
{LOGOS}
<!-- END POST_SLOW_INTERNET_ACCESS -->
<script>
function goto_address(response)
{
	if (!response || response.Status.code != 200) {
        	return;
      	} else {	
        	place = response.Placemark[0];
		pan_zoom_to(place.Point.coordinates[1],
			place.Point.coordinates[0],13);
		document.add_new_location.location_name.value=place.address;
      }
}

function goto_location()
{
	geocoder.getLocations(document.add_new_location.location_name.value,
				goto_address);
}
</script>
<form name="add_new_location" action="add_new_location_int.php" method=post>
<table align=center>
<!-- BEGIN ENTER_NATIONAL_LOCATION -->
<tr><td colspan=5>
<a href="add_new_location.php{QUERY_STRING}">Use National Location Interface</a>
</td></tr>
<!-- END ENTER_NATIONAL_LOCATION -->
<td colspan=5>I've got a fast internet connection, enable google maps<input type=checkbox value=1 name=fast_internet_connection onclick="set_internet_speed()"></td>
<!-- BEGIN ERROR_EMPTY_LATITUDE_LONGITUDE -->
<tr class=error>
<td colspan=5>
You must enter valid latitude,longitude coordinates.
</td>
</tr>
<!-- END ERROR_EMPTY_LATITUDE_LONGITUDE -->
<tr>
<td>Latitude:<td>
<td><input name=latitude value="{LATITUDE}" type=text size=8 maxsize=8></td>
<td>Longitude:<td>
<td><input name=longitude value="{LONGITUDE}" type=text size=8 maxsize=8></td>
<td>
<input type=button value="update latitude longitude" onclick="update_latlon()">
</td>
</tr>
</table>
<table align=center>
<!-- BEGIN ERROR_EMPTY_ADDRESS -->
<tr class=error>
<td></td>
<td>
You need to include an address.
</td>
</tr>
<!-- END ERROR_EMPTY_ADDRESS -->
<tr>
<td>Address:</td>
<td><input name=location_name value="{LOCATION_NAME}" type=text size=50 maxlength=120 ></td>
<td>
<input type=button value="go to location" onclick="goto_location()">
</td>
</tr>
<!-- BEGIN ADDRESS_DESCRIPTION -->
<tr>
<td>Address description:</td>
<td id="address_description"></td>
</tr>
<!-- END ADDRESS_DESCRIPTION -->
</table>
<!-- BEGIN END_FORM -->
<center>
<input type=submit value="Add New Location">
</center>
<!-- END END_FORM -->
<!-- BEGIN TRIP_ORIGIN_END_FORM -->
<center>
<input type=submit value="Add Trip Origin"><br>
On the next page you'll be asked to click add trip destination.
</center>
<!-- END TRIP_ORIGIN_END_FORM -->
<!-- BEGIN TRIP_DESTINATION_END_FORM -->
<center>
<input type=submit value="Add Trip Destination"><br>
Just add the trip destination then you have search results.
</center>
<!-- END TRIP_DESTINATION_END_FORM -->
<!-- BEGIN BACK_TO_LOGIN_PAGE -->
<center>
<a href="index.php">Back To Login Page</a>
</center>
<!-- END BACK_TO_LOGIN_PAGE -->
<!-- BEGIN BACK_TO_CONTROL_CENTRE -->
<center>
<a href="control_centre.php">Back To Control Centre</a>
</center>
<!-- END BACK_TO_CONTROL_CENTRE -->
</form>
{GOOGLE_ANALYTICS}
</body>
</html>
