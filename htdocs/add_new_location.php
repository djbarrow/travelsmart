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
				    $country,
				    $adm1_region_ufi,
				    $nearby_location_ufi,
				    $direction_from_nearby_location,
				    $distance_from_nearby_location,
				    $latitude,
				    $longitude)
{
   global $compass_direction,$fisher_price;

   if($fisher_price)
      return "";
   $connection=db_connect();
   $latlon=new latlon();
   $latlon->country_name=$adm1_region_name=$nearby_location_name="";
   $direction_from_nearby_location=(round($direction_from_nearby_location/22.5))%16;
   $latlon->nearby_location_direction=$compass_direction[$direction_from_nearby_location];
   $latlon->nearby_location_distance=$distance_from_nearby_location;
   $latlon->name=$location_name;
    if(empty($country))
      $country=0;
   if(empty($adm1_region_ufi))
      $adm1_region_ufi=0;
   if(empty($nearby_location_ufi))
      $nearby_location_ufi=0;
   if(empty($latitude))
      $latitude=0;
   if(empty($longitude))
      $longitude=0;
   $latlon->lat=$latitude;
   //return "alert('hello2');";
   $latlon->lon=$longitude;
   if(MULTIPLE_COUNTRIES)
   {
      if($country==unknown_country_code)
	 $latlon->country_name="Unknown Country";
      else
      {
	 $query = "SELECT * FROM country where cc='${country}'";
	 if (!$result = @ mysql_query ($query, $connection))
	    showerror();
	 $numrows=mysql_num_rows($result);
	 if($numrows==1)
	 {
	    $row=mysql_fetch_array($result);
	    $latlon->country_name=$row["name"];
	 }
      }
   }
   if(!MULTIPLE_COUNTRIES||$country!=unknown_country_code)
   {
      $query = "SELECT name FROM adm1_region WHERE "
	 . "ufi=${adm1_region_ufi}";
      if(!($result=@mysql_query($query,$connection)))
	 showerror();
      $numrows=mysql_num_rows($result);
      if($numrows==1)
      {
	 $row=mysql_fetch_array($result);
	 $latlon->adm1_region_name=$row["name"];
      }
      $query = "SELECT * FROM location WHERE "
	 . "ufi=${nearby_location_ufi}";
      if(!($result=@mysql_query($query,$connection)))
	 showerror();
      $numrows=mysql_num_rows($result);
      if($numrows==1)
      {
	 $row=mysql_fetch_array($result);
	 $latlon->nearby_location_name=$row["name"];
	 getnewlatlong($row["latitude"],$row["longitude"],
		       $distance_from_nearby_location,
		       $direction_from_nearby_location,
		       $latlon->lat,$latlon->lon);
      }
   }
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

    //$retstr="document.add_new_location.latitude.setAttribute('value',{$lat});" .
    //"document.add_new_location.longitude.setAttribute('value',{$lon});";
    $retstr="document.add_new_location.latitude.value={$lat};" .
    "document.add_new_location.longitude.value={$lon};";
    return $retstr;
}



function pan_to($lat,$lon)
{
   $retstr=set_latlon_textfields($lat,$lon);
   if(fast_internet_connection())
   {
      $retstr.="var point=new GLatLng({$lat},{$lon});"
      . "map.panTo(point);"
      . "gmarker.setPoint(point);";
   }
   return $retstr;

}

function pan_zoom_map($connection,$query)
{
   global $db_hostname,$db_username,$db_password,$databasename;

   if(fast_internet_connection())
   {
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      if (($numrows=mysql_num_rows($result))!=1)
	 die("Duplicate country codes numrows=" . $numrows);
      $row=mysql_fetch_array($result);
      $sw_latitude=$row["sw_latitude"];
      $ne_latitude=$row["ne_latitude"];
      $sw_longitude=$row["sw_longitude"];
      $ne_longitude=$row["ne_longitude"];
      $lat=($sw_latitude+$ne_latitude)/2;
      $lon=($sw_longitude+$ne_longitude)/2;
      $retstr = "if (GBrowserIsCompatible()) {"
	 . "var rectbounds = new GLatLngBounds("
	 . "new GLatLng({$sw_latitude},{$sw_longitude}),"
	 . "new GLatLng({$ne_latitude},{$ne_longitude}));"
	 . "var zoom_level=map.getBoundsZoomLevel(rectbounds);"
	 . "map.setZoom(zoom_level);"
	 . pan_to($lat,$lon) 
	 . "}";
   }
   else 
      $retstr="";
   return $retstr;
}

function set_dir_distance_from_nearby_location($nearby_location_ufi,
					       $direction_from_nearby_location,
					       $distance_from_nearby_location)
{
   $connection=db_connect();
   $query="SELECT latitude,longitude FROM location WHERE ufi={$nearby_location_ufi}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   if (($numrows=mysql_num_rows($result))!=1)
      die("Duplicate country codes numrows=" . $numrows);
   $row=mysql_fetch_array($result);
   getnewlatlong($row["latitude"],$row["longitude"],
		 $distance_from_nearby_location,
		 $direction_from_nearby_location,
		 $outlat,$outlon);
   if(fast_internet_connection())
 {
      $retstr = "if (GBrowserIsCompatible()) {"
	 . pan_to($outlat,$outlon)
	 . "}";
 }
 else
    $retstr= set_latlon_textfields($outlat,$outlon);
 return $retstr;
}


function get_nearby_location_list($adm1_region_ufi,$selected_ufi,$pan)
{
   $retstr="var nl_list=document.add_new_location.nearby_location_ufi;"
      . "nl_list.options.length=0;"
      . "nl_list.options[0]=new Option(\"--------------------\",0);";
  
   if($adm1_region_ufi)
   {
      $connection=db_connect();
      $query = "SELECT ufi,name FROM location WHERE adm1_region_ufi={$adm1_region_ufi}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $rownum=1;
      while($row=mysql_fetch_array($result))
      {
	 $name=$row["name"];
	 $ufi=$row["ufi"];
	 $select=($ufi==$selected_ufi ? 1:0);
	 $retstr=$retstr . 
	    "nl_list.options[{$rownum}]=new Option(\"{$name}\",{$ufi},0,{$select});";
	 $rownum++;
      }
      if($pan)
	 $retstr= $retstr
	    . pan_zoom_map($connection,
			   "SELECT * FROM adm1_region WHERE ufi='{$adm1_region_ufi}'");
   }
      return $retstr;
}

function get_adm1_region_list($country_code,
			      $selected_ufi,$reset_nearby_location_list) 
{
   global $db_hostname,$db_username,$db_password,$databasename;

   //$retstr= "alert('hello world');";
   //return $retstr;
   $retstr="var adm1_list=document.add_new_location.adm1_region_ufi;"
      . "adm1_list.options.length=0;"
      . "adm1_list.options[0]=new Option(\"-------------------\",0);";
   if($reset_nearby_location_list)
	 $retstr=$retstr . get_nearby_location_list(0,0,false);
   if($country_code)
   {
      if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
	 die("Cannot connect");
      if(!mysql_select_db($databasename,$connection))
	 showerror();
      $query = "SELECT * from adm1_region WHERE cc1='{$country_code}'";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $rownum=1;
      while($row=mysql_fetch_array($result))
      {
	 $ufi=$row["ufi"];
	 $name=$row["name"];
	 $select=($ufi==$selected_ufi ? 1:0);
	 $retstr=$retstr . 
	    "adm1_list.options[{$rownum}]=new Option(\"{$name}\",{$ufi},0,{$select});";
	 $rownum++;
      }
      if($reset_nearby_location_list)
	 $retstr= $retstr
	    . pan_zoom_map($connection,
			   "SELECT * FROM country WHERE cc='{$country_code}'");
   }
   //$retstr = "alert('{$retstr}');";
   return $retstr;
}


function make_country_list($select_cc)
{
   global $db_hostname,$db_username,$db_password,$databasename,$single_country;
   //$retstr="alert('hello');";
   $retstr="var country_list=document.add_new_location.country;";
   if(!MULTIPLE_COUNTRIES)
      $retstr.="country_list.value=\"{$select_cc}\";";
   else
   {
      $selected=($select_cc==null_country_code ? 1:0);
      $retstr.="country_list.options.length=0;"
	 . "country_list.options[0]=new Option(\"--------------------\",\"" . null_country_code . "\",0,{$selected});";
      $selected=($select_cc==unknown_country_code ? 1:0);
      $retstr.="country_list.options[1]=new Option(\"Unknown Country\",\"" . unknown_country_code . "\",0,{$selected});";
      if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
	 die("Cannot connect");
      if(!mysql_select_db($databasename,$connection))
	 showerror();
      $query = "SELECT * FROM country";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $rownum=2;
      while($row=mysql_fetch_array($result))
      {
	 $cc=$row["cc"];
	 $name=$row["name"];
	 $selected=($cc==$select_cc ? 1:0);
	 $retstr=$retstr . 
	    "country_list.options[{$rownum}]=new Option(\"{$name}\",\"{$cc}\",0,{$selected});";
	 $rownum++;
      }
      
   }
   //$retstr.="alert('{$retstr}');";
   return $retstr;
}

function make_direction_from_nearby_location_list($selected_angle)
{
   global $compass_direction;

   $retstr="var direction_from_nearby_location_list=document.add_new_location."
      ."direction_from_nearby_location;"
      . "direction_from_nearby_location_list.options.length=0;";
   $selected_angle=(round($selected_angle/22.5))%16;

   for($i=0;$i<16;$i++)
   {
      $selected=($i==$selected_angle ? 1:0);
      $angle=$i*22.5;
      $direction=$compass_direction[$i];
      $retstr=$retstr .
      "direction_from_nearby_location_list.options[{$i}]="
	 . "new Option(\"{$direction}\",\"{$angle}\",0,{$selected});";
   }
   return $retstr;
}

function make_distance_from_nearby_location_list($selected_dist)
{
   $retstr="var distance_from_nearby_location_list=document.add_new_location."
      ."distance_from_nearby_location;"
      . "distance_from_nearby_location_list.options.length=0;";
   for($i=0;$i<=max_nearest_distance;$i++)
   {
      $selected=(round($selected_dist)==$i ? 1:0); 
      $retstr=$retstr . 
	 "distance_from_nearby_location_list.options[{$i}]="
	 . "new Option(\"{$i}\",\"{$i}\",0,{$selected});";
    
      
   }
   return $retstr;
}



function set_table_info_from_lat_lon($lat,$lon)
{
   global $db_hostname,$db_username,$db_password,$databasename;

   $nearest_distance=EARTH_RADIUS*2;
   $nearest_ufi=0;
   if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
      die("Cannot connect");
   if(!mysql_select_db($databasename,$connection))
      showerror();
   $query="SELECT * FROM country WHERE sw_latitude<={$lat} AND ne_latitude>={$lat}"
      . " AND sw_longitude<=${lon} AND ne_longitude>={$lon}";
   $loopcnt=0;
   //return "alert(\"hello there1\");";
   //$query="hello";
   //return "alert('{$query}');";
   if (!$result1 = @ mysql_query ($query, $connection))
      showerror();
   
   while($row1=mysql_fetch_array($result1))
   {
      $query="SELECT * FROM adm1_region WHERE cc1='${row1["cc"]}' AND "
	 . "sw_latitude<={$lat} AND ne_latitude>={$lat}"
	 . " AND sw_longitude<=${lon} AND ne_longitude>={$lon}";
      //return "alert('{$query}');";
      if (!$result2 = @ mysql_query ($query, $connection))
	 showerror();
       while($row2=mysql_fetch_array($result2))
       {
	  //return "alert('hello there');";
	  $query="SELECT * FROM location WHERE adm1_region_ufi=${row2["ufi"]}";
	  if (!$result3 = @ mysql_query ($query, $connection))
	     showerror();
	  while($row3=mysql_fetch_array($result3))
	  {
	     //return "alert('hello there3');";
	     $distance=getlatlondist($lat,$lon,
				     $row3["latitude"],$row3["longitude"],EARTH_RADIUS);
	     if($distance<$nearest_distance)
	     {
		$nearest_distance=$distance;
		$nearest_ufi=$row3["ufi"];
		//$loopcnt++;
		//$retstr= $retstr . "alert('hello there4' + ' ' + {$distance} + ' ' + {$nearest_distance});";
	     }
	  }
       }
   }
  
   if($nearest_distance>max_nearest_distance)
   {
     
      $retstr=make_country_list(unknown_country_code) .
      get_adm1_region_list(0,0,false) .
      get_nearby_location_list(0,$nearest_ufi,0) .   
      make_direction_from_nearby_location_list(-100) .
      make_distance_from_nearby_location_list(-1) .
      pan_to($lat,$lon);
      return $retstr;
   }
   $query="SELECT * FROM location WHERE ufi={$nearest_ufi}";
   if (!$result3 = @ mysql_query ($query, $connection))
      showerror();
   if (mysql_num_rows($result3)!=1)
      die("set_table_info_from_lat_lon illegal number of rows for location");
   $row3=mysql_fetch_array($result3);
   
   $query="SELECT * FROM adm1_region WHERE ufi=${row3["adm1_region_ufi"]}";
   if (!$result2 = @ mysql_query ($query, $connection))
      showerror();
   if (mysql_num_rows($result2)!=1)
      die("set_table_info_from_lat_lon illegal number of rows for adm1_region");
   $row2=mysql_fetch_array($result2);
   //$retstr= $retstr . "alert({$loopcnt});";
   //$retstr="document.add_new_location.country.value=${row2["cc1"]};";
   //$retstr="document.add_new_location.country.selectedIndex=2";
   //return "alert('${retstr}');";
   $angle=getlatlonangle($row3["latitude"],$row3["longitude"],
			  $lat,$lon);
   $retstr=make_country_list($row2["cc1"]) .
      get_adm1_region_list($row2["cc1"],$row3["adm1_region_ufi"],false) .
      get_nearby_location_list($row3["adm1_region_ufi"],$nearest_ufi,0) .
      make_direction_from_nearby_location_list($angle) .
      make_distance_from_nearby_location_list($nearest_distance) .
      pan_to($lat,$lon);
   //return "alert('${retstr}');";
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

if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
   die("Cannot connect");
if(!mysql_select_db($databasename,$connection))
   showerror();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("add_new_location.tpl",true,true);
if($_POST)
{
   $country=mysqlclean($_POST,"country",cc_len,$connection);
   if(empty($country))
      show_error_block($template,"COUNTRY_NOT_SELECTED");
   if($country==unknown_country_code)
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
   }
   else
   {
      $adm1_region_ufi=mysqlclean($_POST,"adm1_region_ufi",ufi_len,$connection);
      if(empty($adm1_region_ufi))
	 show_error_block($template,"ADM1_REGION_NOT_SELECTED");
      $nearby_location_ufi=mysqlclean($_POST,"nearby_location_ufi",id_len,$connection);
      if(empty($nearby_location_ufi))
	 show_error_block($template,"NEARBY_LOCATION_NOT_SELECTED");   
      $direction_from_nearby_location=mysqlclean($_POST,"direction_from_nearby_location",
						 direction_from_nearby_location_len,
						 $connection)+0;
      $distance_from_nearby_location=mysqlclean($_POST,"distance_from_nearby_location",
						distance_from_nearby_location_len,
						$connection)+0;
      $query = "SELECT name,latitude,longitude FROM location WHERE ufi = {$nearby_location_ufi}";
      // Execute the query
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $row=mysql_fetch_array($result);
      $latitude=$row["latitude"];
      $longitude=$row["longitude"];
   }
   $user_id=$_SESSION["user_id"];
   $location_name=xss_decode($_POST["location_name"]);
   $mysql_location_name=mysqlclean_str($location_name,location_name_len,$connection);
   if(empty($location_name))
   {
      if($distance_from_nearby_location!=0||$country==unknown_country_code)
	 show_error_block($template,"EMPTY_PARTIAL_ADDRESS");
      else
      {
	 $location_name=$row["name"];
	 $mysql_location_name=mysqlclean($row,"name",location_name_len,$connection);
      }
   }
   if(!$validate_error)
   {
      if($country!=unknown_country_code)
      {
	 getnewlatlong($latitude,$longitude,$distance_from_nearby_location,
		       $direction_from_nearby_location,
		       $outlat,$outlon);
	 $mysql_outlat=$outlat;
	 $mysql_outlon=$outlon;
      }
      
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
	    url_forward("add_new_location.php");
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
   if(MULTIPLE_COUNTRIES)
      $country=0;
   else
      $country=$single_country;
   $location_name="";
   $outlat="";
   $outlon="";
}
require_once "include/Sajax.php";
sajax_init();
//$sajax_debug_mode = 1;
sajax_export(
   "set_internet_speed_cookie","get_internet_speed_cookie",
   "get_adm1_region_list","get_nearby_location_list",
   "set_dir_distance_from_nearby_location",
   "set_table_info_from_lat_lon","make_country_list",
   "make_direction_from_nearby_location_list",
   "make_distance_from_nearby_location_list",
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
      add_new_location.country.value,
      add_new_location.adm1_region_ufi.value,
      add_new_location.nearby_location_ufi.value,
      add_new_location.direction_from_nearby_location.value,
      add_new_location.distance_from_nearby_location.value,
      add_new_location.latitude.value,
      add_new_location.longitude.value,
      generic_cb);
}

function modify_cb(evalstr)
{
   eval(evalstr);
   modify_address_description();
}

function set_country(country_code)
{
   x_get_adm1_region_list(country_code,0,true,modify_cb);
} 

function set_cc_adm1_region(country_code,adm1_region_ufi,reset_nearby_location_list)
{
   x_get_adm1_region_list(country_code,adm1_region_ufi,reset_nearby_location_list,modify_cb);
} 

function set_adm1_region(adm1_region_ufi)
{
   x_get_nearby_location_list(adm1_region_ufi,0,true,modify_cb);
}

function set_nearby_location(adm1_region_ufi,nearby_location_ufi)
{
   x_get_nearby_location_list(adm1_region_ufi,nearby_location_ufi,true,modify_cb);
}

function make_country_list(select_cc)
{
   x_make_country_list(select_cc,modify_cb);
}

function make_distance_from_nearby_location_list(select_dist)
{
   x_make_distance_from_nearby_location_list(select_dist,modify_cb);
}

function make_direction_from_nearby_location_list(select_direction)
{
   x_make_direction_from_nearby_location_list(select_direction,modify_cb);
}

function set_dir_distance_from_nearby_location()
{
   var add_new_location=document.add_new_location;
      x_set_dir_distance_from_nearby_location(
      add_new_location.nearby_location_ufi.value,
      add_new_location.direction_from_nearby_location.value,
      add_new_location.distance_from_nearby_location.value,
      modify_cb);
}

function set_table_info_from_lat_lon(latitude,longitude)
{
   x_set_table_info_from_lat_lon(latitude,longitude,modify_cb);
}

function update_latlon()
{
   var add_new_location=document.add_new_location;
   latitude=add_new_location.latitude.value;
   longitude=add_new_location.longitude.value;
   if(latitude>=-90&&latitude<=90&&longitude>=-180&&longitude<=180)
   {
      set_table_info_from_lat_lon(latitude,longitude);
   }
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
   }
   if(!INTERNATIONAL_EDITION)
   {
      $query_string=($_GET ? "?" . $_SERVER["QUERY_STRING"]:"");
      show_nested_variable_block($template,"ENTER_INTERNATIONAL_LOCATION","QUERY_STRING",$query_string);
   }
   $template->setVariable("COUNTRY",$country);
   if($_POST)
   {
      $template->setVariable("LATITUDE",$outlat);
      $template->setVariable("LONGITUDE",$outlon);
      $template->setVariable("ADM1_REGION_UFI",$adm1_region_ufi);
      $template->setVariable("NEARBY_LOCATION_UFI",$nearby_location_ufi);
      $template->setVariable("DIRECTION_FROM_NEARBY_LOCATION",$direction_from_nearby_location);
      $template->setVariable("DISTANCE_FROM_NEARBY_LOCATION",$distance_from_nearby_location);
   }
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str(1,1));
   $template->parseCurrentBlock();
   if(!MULTIPLE_COUNTRIES)
      show_nested_variable_block($template,"COUNTRY_HIDDEN",
				 "SINGLE_COUNTRY_NAME",$single_country_name);
   else
      show_nested_block($template,"COUNTRY_SELECT");
   if($fisher_price||$quick_search||!empty($_SESSION["quick_trip"]))
      show_nested_block($template,"BACK_TO_LOGIN_PAGE");
   if(!$fisher_price)
   {
      $template->setCurrentBlock("PARTIAL_ADDRESS");
      if(empty($location_name))
	 $location_name="";
      //$template->setVariable("LOCATION_NAME",clean_name($location_name));
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