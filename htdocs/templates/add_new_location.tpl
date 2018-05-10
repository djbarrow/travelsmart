<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Add Trip Location</title>
<link rel=stylesheet href={CSS_FILE}>
<!-- BEGIN FAST_INTERNET_ACCESS -->
    <script src="{GOOGLE_MAPS_KEY}"
      type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
	var map;
	var gmarker;
    function load() {
      set_checkboxes(); 	
      make_country_list("{COUNTRY}");
      set_cc_adm1_region("{COUNTRY}",0,true);
      make_direction_from_nearby_location_list(-100);
      make_distance_from_nearby_location_list(-1);	
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
        map.addControl(new {GMAP_CONTROL}());
        map.addControl(new GMapTypeControl());
	point=new GLatLng(52.574444,-7.550278);
	map.setCenter(point,6);
	gmarker=new GMarker(point);
	map.addOverlay(gmarker);
	GEvent.addListener(map, "click", function(marker, point) {
	set_table_info_from_lat_lon(point.lat(),point.lng());
	});
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
<!-- END FAST_INTERNET_ACCESS -->
<!-- BEGIN POST_FAST_INTERNET_ACCESS -->
    <script src="{GOOGLE_MAPS_KEY}"
      type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
	var map;
	var gmarker;
    function load() {
      set_checkboxes();	
      make_country_list("{COUNTRY}");
      set_cc_adm1_region("{COUNTRY}",{ADM1_REGION_UFI},false);
      set_nearby_location({ADM1_REGION_UFI},{NEARBY_LOCATION_UFI});	
      make_direction_from_nearby_location_list({DIRECTION_FROM_NEARBY_LOCATION});
      make_distance_from_nearby_location_list({DISTANCE_FROM_NEARBY_LOCATION});
      if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
        map.addControl(new {GMAP_CONTROL}());
        map.addControl(new GMapTypeControl());
	point=new GLatLng(52.574444,-7.550278);
	map.setCenter(point,6);
        gmarker=new GMarker(point);
	map.addOverlay(gmarker);
	GEvent.addListener(map, "click", function(marker, point) {
	set_table_info_from_lat_lon(point.lat(),point.lng());
	});
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
    <script type="text/javascript">
    //<![CDATA[
    function load() {
      set_checkboxes();
      make_country_list("{COUNTRY}");
      set_cc_adm1_region("{COUNTRY}",0,true);
      make_direction_from_nearby_location_list(-100);
      make_distance_from_nearby_location_list(-1);
    }
    //]]>
    </script>
</head>
<body onload="load()">
{LOGOS}
<!-- END SLOW_INTERNET_ACCESS -->
<!-- BEGIN POST_SLOW_INTERNET_ACCESS -->
    <script type="text/javascript">
    //<![CDATA[
    function load() {
      set_checkboxes();
      make_country_list("{COUNTRY}");
      set_cc_adm1_region("{COUNTRY}",{ADM1_REGION_UFI},false);
      set_nearby_location({ADM1_REGION_UFI},{NEARBY_LOCATION_UFI});
      make_direction_from_nearby_location_list({DIRECTION_FROM_NEARBY_LOCATION});
      make_distance_from_nearby_location_list({DISTANCE_FROM_NEARBY_LOCATION});
     
    }
    //]]>
    </script>
</head>
<body onload="load()">
{LOGOS}
<!-- END POST_SLOW_INTERNET_ACCESS -->
<form name="add_new_location" action="add_new_location.php" method=post>
<table align=center>
<!-- BEGIN ENTER_INTERNATIONAL_LOCATION -->
<tr><td colspan=5>
<a href="add_new_location_int.php{QUERY_STRING}">Use International Location Interface</a>
</td></tr>
<!-- END ENTER_INTERNATIONAL_LOCATION -->
<td colspan=5>I've got a fast internet connection, enable google maps<input type=checkbox value=1 name=fast_internet_connection onclick="set_internet_speed()"></td>
<!-- BEGIN ERROR_EMPTY_LATITUDE_LONGITUDE -->
<tr class=error>
<td colspan=5>
For an unknown country you must enter valid latitude,longitude coordinates.
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
<!-- BEGIN ERROR_EMPTY_PARTIAL_ADDRESS -->
<tr class=error>
<td></td>
<td>
You need to include a partial address if distance from nearby location is not zero.
</td>
</tr>
<!-- END ERROR_EMPTY_PARTIAL_ADDRESS -->
<!-- BEGIN PARTIAL_ADDRESS -->
<tr>
<td>Beginning of address:</td>
<td><input name=location_name value="{LOCATION_NAME}" type=text size=50 maxlength=120 onChange="modify_address_description()"></td>
</tr>
<!-- END PARTIAL_ADDRESS -->
<!-- BEGIN ERROR_COUNTRY_NOT_SELECTED -->
<tr class=error>
<td></td>
<td>
country not selected
</td>
</tr>
<!-- END ERROR_COUNTRY_NOT_SELECTED -->
<!-- BEGIN COUNTRY_SELECT -->
<tr>
<td>Country:</td>
<td>
<select name="country" onChange="set_country(this.value)" >
</td>
</tr>
<!-- END COUNTRY_SELECT -->
<!-- BEGIN COUNTRY_HIDDEN -->
<input type="hidden" name="country" >
<tr><td colspan=2>
<b>Tip:</b> if you need to reset the map to {SINGLE_COUNTRY_NAME},hit the reload button at the top of the browser window.
</td></tr>
<!-- END COUNTRY_HIDDEN -->
<!-- BEGIN ERROR_ADM1_REGION_NOT_SELECTED -->
<tr class=error>
<td></td>
<td>
region not selected
</td>
</tr>
<!-- END ERROR_ADM1_REGION_NOT_SELECTED -->
<tr>
<td>Region:</td>
<td>
<select name="adm1_region_ufi" onChange="set_adm1_region(this.value)" >
<option value=0>--------------------   
</select>
</td>
</tr>
<!-- BEGIN ERROR_NEARBY_LOCATION_NOT_SELECTED -->
<tr class=error>
<td></td>
<td>
nearby location not selected
</td>
</tr>
<!-- END ERROR_NEARBY_LOCATION_NOT_SELECTED -->
<tr><td colspan=2><b>Tip:</b> Typing the first few letters of the nearby location will speed up selection e.g. type mal for Mallow.
</td></tr>
<tr>
<td>Nearby location in region:</td>
<td>
<select name="nearby_location_ufi" onChange="set_dir_distance_from_nearby_location()"" >
<option value=0>--------------------   
</select>
</td>
</tr>
<tr>
<td>Direction from nearby location:</td>
<td>
<select name="direction_from_nearby_location" onChange="set_dir_distance_from_nearby_location()">
</select>
</td>
</tr>
<tr>
<td>Distance from nearby location in km:</td>
<td>
<select name="distance_from_nearby_location" onchange="set_dir_distance_from_nearby_location()">
</select>
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
