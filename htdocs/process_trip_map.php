<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/latlonfunc.php";
require_once "include/tripplan_func.php";
require_once "include/time.php";
require_once "include/logos.php";

if($_GET["trip1_id"]&&$_GET["trip2_id"])
{
   $connection=db_connect();
   $trip1_id=mysqlclean($_GET,"trip1_id",id_len,$connection);
   $trip2_id=mysqlclean($_GET,"trip2_id",id_len,$connection);
}
else
   die("process_trip_map couldn't get trip_id's\n");
$trip1=get_trip_info_from_trip_id($trip1_id,$connection);
$trip2=get_trip_info_from_trip_id($trip2_id,$connection);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_map.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$template->setCurrentBlock("GOOGLE_MAPS_KEY");
$template->setVariable("GOOGLE_MAPS_KEY",get_google_maps_key());
$template->parseCurrentBlock();
$template->setCurrentBlock("__global__");
$template->setVariable("GMAP_CONTROL",
		       get_google_maps_mapsize_control_string());
$marker=array();
$marker[0]=$trip1->trip_origin_latlon;
$marker[1]=$trip1->trip_destination_latlon;
$marker[2]=$trip2->trip_origin_latlon;
$marker[3]=$trip2->trip_destination_latlon;
for($i=0;$i<4;$i++)
{
   $template->setCurrentBlock("SET_MARKERS");
   $template->setVariable("INDEX",$i);
   $template->setVariable("TEXT","");
   $template->setVariable("LAT",$marker[$i]->lat);
   $template->setVariable("LON",$marker[$i]->lon);
   $template->parseCurrentBlock();
}
$template->setCurrentBlock("SET_LATLON");

$lat=($marker[0]->lat+
      $marker[1]->lat+
      $marker[2]->lat+
      $marker[3]->lat)/4;
$lon=($marker[0]->lon+
      $marker[1]->lon+
      $marker[2]->lon+
      $marker[3]->lon)/4;
$template->setVariable("LAT",$lat);
$template->setVariable("LON",$lon);
$template->parseCurrentBlock();

for($i=0;$i<4;$i++)
{  $template->setCurrentBlock("SET_MARKER_TEXT");
   if(($i&1)==0)
      $row=get_userinfo_from_user_id($connection,(($i>>1)==0 ? $trip1->user_id : $trip2->user_id));
   $text= $marker[$i]->name . ", " .$row["name"] ."'s trip " . (($i&1)==0 ? "origin" : "destination") . ".";
   $template->setVariable("INDEX",$i);
   $template->setVariable("TEXT",$text);
   $template->parseCurrentBlock();
}
$template->setCurrentBlock("__global__");
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();