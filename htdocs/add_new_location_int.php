<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/latlonfunc.php";
require_once "include/config.php";
require_once "include/tripplan_func.php";
require_once "include/logos.php";
require_once "include/validate.php";
require_once "include/display_plan_trip.php";
require_once "include/temp_user.php";
require_once "include/time.php";
require_once "include/internet_speed_cookie.php";
require_once "include/tripmatch_logic.php";



function modify_address_description($location_name,
				    $latitude,
				    $longitude)
{
   if(empty($latitude))
      $latitude=0;
   if(empty($longitude))
      $longitude=0;
   $latlon=new latlon();
   $latlon->lat=$latitude;
   $latlon->lon=$longitude;
   $latlon->country_name="Unknown Country";
   $latlon->adm1_region_name=$latlon->nearby_location_name="";
   $latlon->nearby_location_direction=$compass_direction[0];
   $latlon->nearby_location_distance=0;
   $latlon->name=$location_name;
   $str=make_address_string_from_latlon($latlon);
   $str=str_replace("'","\'",$str);
   //$str=xss_encode(make_address_string_from_latlon($latlon));
   return "var address_description=document.getElementById('address_description');" .
      "var newText=document.createTextNode('{$str}');" .
      "while (address_description.hasChildNodes())" .
      "{" .
      " address_description.removeChild(address_description.lastChild);" .
      "}" .
      "address_description.appendChild(newText);";     
}

function set_latlon_textfields($lat,$lon)
{
    $lat=round($lat,5);
    $lon=round($lon,5);

     $retstr="document.add_new_location.latitude.value={$lat};" .
    "document.add_new_location.longitude.value={$lon};";
    return $retstr;
}


function move_marker($lat,$lon)
{
   $retstr="";
   if(fast_internet_connection())
   {
      $retstr.="var point=new GLatLng({$lat},{$lon});"
	 . "map.panTo(point);"
	 . "gmarker.setPoint(point);";
   }
   return $retstr;
}

function pan_to($lat,$lon)
{
   $retstr=set_latlon_textfields($lat,$lon);
   $retstr.=move_marker($lat,$lon);
   return $retstr; 
}

function pan_zoom_to($lat,$lon,$zoom_level)
{
   $retstr="";
   if(fast_internet_connection())
   {
      $retstr.="if (GBrowserIsCompatible()) {"
	 . "map.setZoom({$zoom_level});"
	 . "}";
   }
   $retstr.=pan_to($lat,$lon);
   return $retstr;
}


if($_GET)
{
   if(empty($_GET["fisher_price"]))
      $fisher_price=0;
   else
      $fisher_price=$_GET["fisher_price"];
   if(empty($_GET["quick_search"]))
      $quick_search=0;
   else
      $quick_search=$_GET["quick_search"];
}
else
{
   $fisher_price=0;
   $quick_search=0;
   if(empty($_SESSION["saved_add_edit_user"])&&empty($_SESSION["quick_trip"]))
      session_authenticate(empty($_SESSION["saved_plan_trip"]) ? 0:-1);
}
$validate_error=0;
$connection=db_connect();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("add_new_location_int.tpl",true,true);

if($_POST)
{
   $adm1_region_ufi=0;
   $nearby_location_ufi=unknown_nearby_location_ufi;
   $direction_from_nearby_location=0;
   $distance_from_nearby_location=0;
   $outlat=xss_decode($_POST["latitude"]);
   $mysql_outlat=mysqlclean_str($outlat,latitude_longitude_len,$connection);
   $outlon=xss_decode($_POST["longitude"]);
   $mysql_outlon=mysqlclean_str($outlon,latitude_longitude_len,$connection);
   if(empty($outlat)||empty($outlon))
      show_error_block($template,"EMPTY_LATITUDE_LONGITUDE");
   $user_id=$_SESSION["user_id"];
   $location_name=xss_decode($_POST["location_name"]);
   $mysql_location_name=mysqlclean_str($location_name,location_name_len,$connection);
   if(empty($location_name))
   {
      show_error_block($template,"EMPTY_ADDRESS");
   }
   if(!$validate_error)
   {
      
      $query = "INSERT INTO user_location VALUES (NULL,${user_id},${nearby_location_ufi},'${mysql_location_name}',${mysql_outlat},${mysql_outlon},TRUE)";
      if(!(@mysql_query($query,$connection)))
	 showerror();
      $location_id=mysql_insert_id($connection);
      if(!empty($_SESSION["quick_trip"]))
      {
	 $t=unserialize($_SESSION["quick_trip"]);
	 if($t->trip_origin_location_id==0)
	 {
	    $t->trip_origin_location_id=$location_id;
	    $_SESSION["quick_trip"]=serialize($t);
	    url_forward("add_new_location_int.php");
	 }
	 else
	 {
	    $t->trip_destination_location_id=$location_id;
	    $_SESSION["quick_trip"]="";
	    insert_trip($connection,$t,0);
	    tripmatch_logic($connection,$t);
	 }
      }
      else
      {
	 
	 if(!empty($_SESSION["saved_add_edit_user"]))
	 {
	    $saved_add_edit_user=unserialize($_SESSION["saved_add_edit_user"]);
	    $saved_add_edit_user["address_location_id"]=$location_id;
	    $_SESSION["saved_add_edit_user"]=serialize($saved_add_edit_user);
	    url_forward("add_edit_user.php");
	 }
	 if(empty($_SESSION["saved_plan_trip"]))
	 {
	    url_forward("control_centre.php");
	 }
	 else
	 {
	    $saved_plan_trip=unserialize($_SESSION["saved_plan_trip"]);
	    $saved_plan_trip[
	       ( $saved_plan_trip["add_new_trip_origin"] ?
		 "trip_origin_location_id" :
		 "trip_destination_location_id" )]=$location_id;
	    display_saved_plan_trip($connection,$saved_plan_trip);
	 }
      }
   }
}
else 
{
   $location_name="";
   $outlat="";
   $outlon="";
}
require_once "include/Sajax.php";
sajax_init();
//$sajax_debug_mode = 1;
sajax_export(
   "set_internet_speed_cookie","get_internet_speed_cookie",
   "move_marker",
   "pan_to",
   "pan_zoom_to",
   "modify_address_description");
sajax_handle_client_request();
	
?>

<script language="JavaScript">
<?php
sajax_show_javascript();
?>
function generic_cb(evalstr)
{
   eval(evalstr);
}

function speed_cb(evalstr)
{
   eval(evalstr);
   location.reload(1);
}


function set_checkboxes()
{
   x_get_internet_speed_cookie("add_new_location",generic_cb);
}

function set_internet_speed()
{  
   speed=document.add_new_location.fast_internet_connection.checked;
   if(speed)
      speed=1;
   else
      speed=0;
   x_set_internet_speed_cookie(speed,speed_cb);
}



function modify_address_description()
{
   var add_new_location=document.add_new_location;

   x_modify_address_description(
      add_new_location.location_name.value,
      add_new_location.latitude.value,
      add_new_location.longitude.value,
      generic_cb);
}

function modify_cb(evalstr)
{
   eval(evalstr);
   modify_address_description();
}

function move_marker(latitude,longitude)
{
   x_move_marker(latitude,longitude,modify_cb);
}

function update_latlon()
{
   var add_new_location=document.add_new_location;
   latitude=add_new_location.latitude.value;
   longitude=add_new_location.longitude.value;
   if(latitude>=-90&&latitude<=90&&longitude>=-180&&longitude<=180)
   {
      move_marker(latitude,longitude);
   }
}

function pan_to(latitude,longitude)
{
   x_pan_to(latitude,longitude,modify_cb);
}

function pan_zoom_to(latitude,longitude,zoom_level)
{
   x_pan_zoom_to(latitude,longitude,zoom_level,modify_cb);
}

</script>

<?php
if(!$_POST||$validate_error)
{
   $poststr=($_POST ? "POST_":"");
   if(fast_internet_connection())
   {
      $template->setCurrentBlock($poststr . "FAST_INTERNET_ACCESS");
      $template->setVariable("GOOGLE_MAPS_KEY",get_google_maps_key());
      $template->setVariable("WIDTH_HEIGHT_STRING",
			     get_google_maps_width_height_string());
      $template->setVariable("GMAP_CONTROL",
			     get_google_maps_mapsize_control_string());
   }
   else
   {
      $template->setCurrentBlock($poststr . "SLOW_INTERNET_ACCESS");
      $template->setVariable("GOOGLE_MAPS_KEY",get_google_maps_key());
   }
   if(!INTERNATIONAL_EDITION)
   {
      $query_string=($_GET ? "?" . $_SERVER["QUERY_STRING"]:"");
      show_nested_variable_block($template,"ENTER_NATIONAL_LOCATION","QUERY_STRING",$query_string);  
   }
   if($_POST)
   {
      $template->setVariable("LATITUDE",$outlat);
      $template->setVariable("LONGITUDE",$outlon);
   }
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str(1,1));
   $template->parseCurrentBlock();
   if($fisher_price||$quick_search||!empty($_SESSION["quick_trip"]))
      show_nested_block($template,"BACK_TO_LOGIN_PAGE");
   if(!$fisher_price)
   {
      if(empty($location_name))
	 $location_name="";
      $template->setVariable("LOCATION_NAME",xss_encode($location_name));
      $template->parseCurrentBlock();
      if(empty($_SESSION["quick_trip"]))
      {
	 $trip_str=$quick_search ? "TRIP_ORIGIN_":"";
      }
      else
      {
	 $t=unserialize($_SESSION["quick_trip"]);
	 $trip_str=$t->trip_origin_location_id==0 ? "TRIP_ORIGIN_" :
		    "TRIP_DESTINATION_";
      }
      $template->setCurrentBlock($trip_str ."END_FORM");
      $template->touchBlock($trip_str ."END_FORM");
      $template->parseCurrentBlock();
   }
   if(!$fisher_price)
      show_nested_block($template,"ADDRESS_DESCRIPTION");
   $template->setCurrentBlock("__global__");
   if(is_user_permanent($connection,0,1))
      show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
   if($quick_search&&empty($_SESSION["quick_trip"]))
   {
      require_once "include/time.php";
      
      create_temporary_user($connection);
      $t=new trip();
      $t->is_driver=$t->is_passenger=1;
      $t->regular_trip=1;
      $t->trip_day[0]=$t->trip_day[1]=$t->trip_day[2]=$t->trip_day[3]=
	 $t->trip_day[4]=$t->trip_day[5]=$t->trip_day[6]=1;
      $t->undashed_trip_date=Date_Calc::dateNow('%Y%m%d');
      $t->trip_date=dash_date($t->undashed_trip_date);
      $t->outward->earliest_possible_trip_departure_time=0;
      $t->outward->latest_possible_trip_arrival_time=1440;
      $t->outward->expected_trip_duration_mins=5;
      $t->is_return_trip=1;
      $t->return->earliest_possible_trip_departure_time=0;
      $t->return->latest_possible_trip_arrival_time=1440;
      $t->return->expected_trip_duration_mins=5;
      $t->trip_origin_location_id=
      $t->trip_destination_location_id=0; 
      $t->vehicle_id=0;
      $t->comments="";
      $_SESSION["quick_trip"]=serialize($t);
   }
}
?>