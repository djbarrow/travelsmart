<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/latlonfunc.php";
require_once "include/tripplan_func.php";
require_once "include/time.php";
require_once "include/logos.php";
require_once "include/validate.php";

$validate_error=0;
$connection=db_connect();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("get_directions.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
if($_GET)
{
   if($_GET["trip1_id"]&&$_GET["trip2_id"])
   {
      $trip1_id=mysqlclean($_GET,"trip1_id",id_len,$connection);
      $trip2_id=mysqlclean($_GET,"trip2_id",id_len,$connection);
   }
   else
      die("process_trip_map couldn't get trip_id's\n");
}
else if($_POST)
{
   if($_POST["trip1_id"]&&$_POST["trip2_id"])
   {
      $trip1_id=mysqlclean($_POST,"trip1_id",id_len,$connection);
      $trip2_id=mysqlclean($_POST,"trip2_id",id_len,$connection);
   }
   else
      die("process_trip_map couldn't get trip_id's\n");
   $option=0;
   if($_POST["option"])
      $option=mysqlclean($_POST,"option",get_directions_option_len,$connection);
   if($option<1||$option>8)
      show_error_block($template,"OPTION_NOT_SELECTED");
}
else
   die("No get or post variables");
$t=get_trip_info_from_trip_id($trip1_id,$connection);
$c=get_trip_info_from_trip_id($trip2_id,$connection);
if($t->is_driver&&$c->is_passenger)
   check_trip_compat($t,$c,$c);
if($c->is_driver&&$t->is_passenger)
   check_trip_compat($c,$t,$c);
$row=get_userinfo_from_user_id($connection,$t->user_id);
$name1=xss_encode($row["name"]);
$row=get_userinfo_from_user_id($connection,$c->user_id);
$name2=xss_encode($row["name"]);
$origin1=xss_encode(get_start_of_address($t->trip_origin_latlon->name));
$dest1=xss_encode(get_start_of_address($t->trip_destination_latlon->name));
$origin2=xss_encode(get_start_of_address($c->trip_origin_latlon->name));
$dest2=xss_encode(get_start_of_address($c->trip_destination_latlon->name));
if($_GET||($_POST&&$validate_error))
{
   for($i=1;$i<=8;$i++)
   {
      switch($i)
      {
	case 1:
	    $display_radio=$t->driver_outward_passenger_outward;
	 break;
       	 case 2:
	    $display_radio=$t->driver_return_passenger_outward;
	    break;
	 case 3:
	    $display_radio=$t->driver_return_passenger_return;
	    break;
	 case 4:
	    $display_radio=$t->driver_outward_passenger_return;
	    break;
	 case 5:
	    $display_radio=$c->driver_outward_passenger_outward;
	    break;
	 case 6:
	    $display_radio=$c->driver_return_passenger_outward;
	    break;
	 case 7:
	    $display_radio=$c->driver_return_passenger_return;
	    break;
	 case 8:
	    $display_radio=$c->driver_outward_passenger_return;
	    break;
      }
      if($display_radio)
      {
	 $template->setCurrentBlock("OPTION_" .$i);
	 $template->setVariable("NAME1",$name1);
	 $template->setVariable("NAME2",$name2);
	 $template->setVariable("ORIGIN1",$origin1);
	 $template->setVariable("DEST1",$dest1);
	 $template->setVariable("ORIGIN2",$origin2);
	 $template->setVariable("DEST2",$dest2);
	 set_check($template,"option",$i,false);
	 $template->parseCurrentBlock();
      }
   }
   $template->setCurrentBlock("__global__");
   $template->setVariable("TRIP1_ID",$trip1_id);
   $template->setVariable("TRIP2_ID",$trip2_id);
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->show();
}
else 
if($_POST&&!$validate_error)
{
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("get_directions2.tpl",true,true);
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str());
   $jleg=array();
   switch($option)
   {
      case 1:
	 $jleg[0]=$t->trip_origin_latlon;
	 $jleg[1]=$c->trip_origin_latlon;
	 $jleg[2]=$c->trip_destination_latlon;
	 $jleg[3]=$t->trip_destination_latlon;
      break;
      case 2:
	 $jleg[0]=$t->trip_destination_latlon;
	 $jleg[1]=$c->trip_origin_latlon;
	 $jleg[2]=$c->trip_destination_latlon;
	 $jleg[3]=$t->trip_origin_latlon;
	 break;
      case 3:
	 $jleg[0]=$t->trip_destination_latlon;
	 $jleg[1]=$c->trip_destination_latlon;
	 $jleg[2]=$c->trip_origin_latlon;
	 $jleg[3]=$t->trip_origin_latlon;
	 break;
      case 4:
	 $jleg[0]=$t->trip_origin_latlon;
	 $jleg[1]=$c->trip_destination_latlon;
	 $jleg[2]=$c->trip_origin_latlon;
	 $jleg[3]=$t->trip_destination_latlon;
	 break;
      case 5:
	 $jleg[0]=$c->trip_origin_latlon;
	 $jleg[1]=$t->trip_origin_latlon;
	 $jleg[2]=$t->trip_destination_latlon;
	 $jleg[3]=$c->trip_destination_latlon;
	 break;
      case 6:
	 $jleg[0]=$c->trip_destination_latlon;
	 $jleg[1]=$t->trip_origin_latlon;
	 $jleg[2]=$t->trip_destination_latlon;
	 $jleg[3]=$c->trip_origin_latlon;
	 break;
      case 7:
	 $jleg[0]=$c->trip_destination_latlon;
	 $jleg[1]=$t->trip_destination_latlon;
	 $jleg[2]=$t->trip_origin_latlon;
	 $jleg[3]=$c->trip_origin_latlon;
	 break;
      case 8:
	 $jleg[0]=$c->trip_origin_latlon;
	 $jleg[1]=$t->trip_destination_latlon;
	 $jleg[2]=$t->trip_origin_latlon;
	 $jleg[3]=$c->trip_destination_latlon;
	 break;
   }
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("get_directions2.tpl",true,true);
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str());
   $template->setVariable("GOOGLE_MAPS_KEY",get_google_maps_key());
   $template->setVariable("JLEG0_LAT",$jleg[0]->lat);
   $template->setVariable("JLEG0_LON",$jleg[0]->lon);
   $template->setVariable("JLEG1_LAT",$jleg[1]->lat);
   $template->setVariable("JLEG1_LON",$jleg[1]->lon);
   $template->setVariable("JLEG2_LAT",$jleg[2]->lat);
   $template->setVariable("JLEG2_LON",$jleg[2]->lon);
   $template->setVariable("JLEG3_LAT",$jleg[3]->lat);
   $template->setVariable("JLEG3_LON",$jleg[3]->lon);
   $template->setVariable("JLEG0_NAME",
			  xss_encode(get_start_of_address($jleg[0]->name)));
   $template->setVariable("JLEG1_NAME",
			  xss_encode(get_start_of_address($jleg[1]->name)));
   $template->setVariable("JLEG2_NAME",
			  xss_encode(get_start_of_address($jleg[2]->name)));
   $template->setVariable("JLEG3_NAME",
			  xss_encode(get_start_of_address($jleg[3]->name)));
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->show();
}


