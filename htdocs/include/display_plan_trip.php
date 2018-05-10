<?php
require_once "include/select.php";
require_once "include/logos.php";
require_once "include/tripplan_func.php";
require_once "Date/Calc.php";

function set_check($template,$checkname,$var,$checked)
{
   $template->setVariable("CHECKED_". $checkname . "_${var}" 
			  ,$checked ? "checked" :"");
}

function set_radio_check($template,$checkname,$check_array,$checkval)
{
   	$count=count($check_array);
	for($i=0;$i<$count;$i++)
	{
	   set_check($template,$checkname,
		     $check_array[$i],$check_array[$i]==$checkval);
	}
}

function set_selected($template,$str,$var1,$var2)
{
   $template->setVariable($str,($var1==$var2 ? "selected":""));  
}

/*
function fix_textarea($textarea_str)
{
   $textarea_str=str_replace("\\r","\r",$textarea_str);
   $textarea_str=str_replace("\\n","\n",$textarea_str);
   return $textarea_str;
}
*/

function fill_user_location_select($template,$connection,$select_prefix_str,$selected_id)
{
   $template->setCurrentBlock($select_prefix_str);
   $template->setVariable($select_prefix_str ."_LOCATION_ID",0);
   $template->setVariable($select_prefix_str ."_LOCATION_NAME","--------------------");
   $template->parseCurrentBlock();
   if(empty($_SESSION["user_id"]))
      return;
   $connection=db_connect_if_neccessary($connection);
   $query = "SELECT * FROM user_location WHERE user_id= ${_SESSION["user_id"]} AND visible=TRUE";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   while($row=mysql_fetch_array($result))
   {
      $template->setCurrentBlock($select_prefix_str);
      $template->setVariable($select_prefix_str . "_LOCATION_ID",$row["location_id"]);
      set_selected($template,$select_prefix_str . "_LOCATION_ID_SELECTED",
		   $row["location_id"],$selected_id);
      $template->setVariable($select_prefix_str . "_LOCATION_NAME",xss_encode(get_start_of_address($row["name"])));
      $template->parseCurrentBlock();
   }

}


function load_plan_trip_template()
{
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("plan_trip.tpl",true,true);
   return $template;
}

function display_plan_trip(
   $connection,
   $template,
   $driver_passenger,
   $regular_trip,
   $day1,$day2,$day3,$day4,$day5,$day6,$day7,
   $trip_date_day,$trip_date_month,$trip_date_year,
   $earliest_possible_trip_departure_time,
   $latest_possible_trip_arrival_time,
   $expected_trip_duration_mins,
   $is_return_trip,
   $return_trip_earliest_possible_trip_departure_time,
   $return_trip_latest_possible_trip_arrival_time,
   $return_trip_expected_trip_duration_mins,
   $trip_origin_location_id,
   $trip_destination_location_id,
   $vehicle_id,
   $comments
)
{
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str(1,1));
   fill_user_location_select($template,$connection,"TRIP_ORIGIN",$trip_origin_location_id);
   fill_user_location_select($template,$connection,"TRIP_DESTINATION",$trip_destination_location_id);
   $query = "SELECT * FROM vehicle WHERE user_id= ${_SESSION["user_id"]} AND visible=TRUE";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $template->setCurrentBlock("VEHICLE");
   $template->setVariable("VEHICLE_ID",0);
   $template->setVariable("VEH_DES","-------------------");
   $template->parseCurrentBlock();
   while($row=mysql_fetch_array($result))
   {
      $template->setCurrentBlock("VEHICLE");
      $template->setVariable("VEHICLE_ID",$row["vehicle_id"]);
      set_selected($template,"VEHICLE_ID_SELECTED",
		   $row["vehicle_id"],$vehicle_id);
      $template->setVariable("VEH_DES",xss_encode(get_vehicle_description_str($row)));
      $template->parseCurrentBlock();
   }
   $template->setCurrentBlock("__global__");
   $trip_date_day_select=output_integer_select("trip_date_day",1,31,$trip_date_day);
   $template->setVariable("TRIP_DATE_DAY_SELECT",$trip_date_day_select);
   $month_array=array(
      array(1,"January"),
      array(2,"February"),
      array(3,"March"),
      array(4,"April"),
      array(5,"May"),
      array(6,"June"),
      array(7,"July"),
      array(8,"August"),
      array(9,"September"),
      array(10,"October"),
      array(11,"November"),
      array(12,"December")
      );
   $trip_date_month_select=output_select("trip_date_month",$month_array,$trip_date_month);
   $template->setVariable("TRIP_DATE_MONTH_SELECT",$trip_date_month_select);
   $curr_year=Date_Calc::dateNow('%Y');
   $trip_date_year_select=output_integer_select("trip_date_year",$curr_year,$curr_year+5,$trip_date_year);
   $template->setVariable("TRIP_DATE_YEAR_SELECT",$trip_date_year_select);
   $earliest_possible_trip_departure_time_array=array(
      array(0,"Midnight"),
      array(15,"12:15 AM"),
      array(30,"12:30 AM"),
      array(45,"12:45 AM"),
      array(60,"1:00 AM"),
      array(75,"1:15 AM"),
      array(90,"1:30 AM"),
      array(105,"1:45 AM"),
      array(120,"2:00 AM"),
      array(135,"2:15 AM"),
      array(150,"2:30 AM"),
      array(165,"2:45 AM"),
      array(180,"3:00 AM"),
      array(195,"3:15 AM"),
      array(210,"3:30 AM"),
      array(225,"3:45 AM"),
      array(240,"4:00 AM"),
      array(255,"4:15 AM"),
      array(270,"4:30 AM"),
      array(285,"4:45 AM"),
      array(300,"5:00 AM"),
      array(315,"5:15 AM"),
      array(330,"5:30 AM"),
      array(345,"5:45 AM"),
      array(360,"6:00 AM"),
      array(375,"6:15 AM"),
      array(390,"6:30 AM"),
      array(405,"6:45 AM"),
      array(420,"7:00 AM"),
      array(435,"7:15 AM"),
      array(450,"7:30 AM"),
      array(465,"7:45 AM"),
      array(480,"8:00 AM"),
      array(495,"8:15 AM"),
      array(510,"8:30 AM"),
      array(525,"8:45 AM"),
      array(540,"9:00 AM"),
      array(555,"9:15 AM"),
      array(570,"9:30 AM"),
      array(585,"9:45 AM"),
      array(600,"10:00 AM"),
      array(615,"10:15 AM"),
      array(630,"10:30 AM"),
      array(645,"10:45 AM"),
      array(660,"11:00 AM"),
      array(675,"11:15 AM"),
      array(690,"11:30 AM"),
      array(705,"11:45 AM"),
      array(720,"Noon"),
      array(735,"12:15 PM"),
      array(750,"12:30 PM"),
      array(765,"12:45 PM"),
      array(780,"1:00 PM"),
      array(795,"1:15 PM"),
      array(810,"1:30 PM"),
      array(825,"1:45 PM"),
      array(840,"2:00 PM"),
      array(855,"2:15 PM"),
      array(870,"2:30 PM"),
      array(885,"2:45 PM"),
      array(900,"3:00 PM"),
      array(915,"3:15 PM"),
      array(930,"3:30 PM"),
      array(945,"3:45 PM"),
      array(960,"4:00 PM"),
      array(975,"4:15 PM"),
      array(990,"4:30 PM"),
      array(1005,"4:45 PM"),
      array(1020,"5:00 PM"),
      array(1035,"5:15 PM"),
      array(1050,"5:30 PM"),
      array(1065,"5:45 PM"),
      array(1080,"6:00 PM"),
      array(1095,"6:15 PM"),
      array(1110,"6:30 PM"),
      array(1125,"6:45 PM"),
      array(1140,"7:00 PM"),
      array(1155,"7:15 PM"),
      array(1170,"7:30 PM"),
      array(1185,"7:45 PM"),
      array(1200,"8:00 PM"),
      array(1215,"8:15 PM"),
      array(1230,"8:30 PM"),
      array(1245,"8:45 PM"),
      array(1260,"9:00 PM"),
      array(1275,"9:15 PM"),
      array(1290,"9:30 PM"),
      array(1305,"9:45 PM"),
      array(1320,"10:00 PM"),
      array(1335,"10:15 PM"),
      array(1350,"10:30 PM"),
      array(1365,"10:45 PM"),
      array(1380,"11:00 PM"),
      array(1395,"11:15 PM"),
      array(1410,"11:30 PM"),
      array(1425,"11:45 PM")
      );
   $earliest_possible_trip_departure_time_select=
      output_select("earliest_possible_trip_departure_time",
		    $earliest_possible_trip_departure_time_array,
		    $earliest_possible_trip_departure_time);
   $template->setVariable("EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME_SELECT",
			  $earliest_possible_trip_departure_time_select);
   $latest_possible_trip_arrival_time_array=array(
      array(0,"Midnight"),
      array(15,"12:15 AM"),
      array(30,"12:30 AM"),
      array(45,"12:45 AM"),
      array(60,"1:00 AM"),
      array(75,"1:15 AM"),
      array(90,"1:30 AM"),
      array(105,"1:45 AM"),
      array(120,"2:00 AM"),
      array(135,"2:15 AM"),
      array(150,"2:30 AM"),
      array(165,"2:45 AM"),
      array(180,"3:00 AM"),
      array(195,"3:15 AM"),
      array(210,"3:30 AM"),
      array(225,"3:45 AM"),
      array(240,"4:00 AM"),
      array(255,"4:15 AM"),
      array(270,"4:30 AM"),
      array(285,"4:45 AM"),
      array(300,"5:00 AM"),
      array(315,"5:15 AM"),
      array(330,"5:30 AM"),
      array(345,"5:45 AM"),
      array(360,"6:00 AM"),
      array(375,"6:15 AM"),
      array(390,"6:30 AM"),
      array(405,"6:45 AM"),
      array(420,"7:00 AM"),
      array(435,"7:15 AM"),
      array(450,"7:30 AM"),
      array(465,"7:45 AM"),
      array(480,"8:00 AM"),
      array(495,"8:15 AM"),
      array(510,"8:30 AM"),
      array(525,"8:45 AM"),
      array(540,"9:00 AM"),
      array(555,"9:15 AM"),
      array(570,"9:30 AM"),
      array(585,"9:45 AM"),
      array(600,"10:00 AM"),
      array(615,"10:15 AM"),
      array(630,"10:30 AM"),
      array(645,"10:45 AM"),
      array(660,"11:00 AM"),
      array(675,"11:15 AM"),
      array(690,"11:30 AM"),
      array(705,"11:45 AM"),
      array(720,"Noon"),
      array(735,"12:15 PM"),
      array(750,"12:30 PM"),
      array(765,"12:45 PM"),
      array(780,"1:00 PM"),
      array(795,"1:15 PM"),
      array(810,"1:30 PM"),
      array(825,"1:45 PM"),
      array(840,"2:00 PM"),
      array(855,"2:15 PM"),
      array(870,"2:30 PM"),
      array(885,"2:45 PM"),
      array(900,"3:00 PM"),
      array(915,"3:15 PM"),
      array(930,"3:30 PM"),
      array(945,"3:45 PM"),
      array(960,"4:00 PM"),
      array(975,"4:15 PM"),
      array(990,"4:30 PM"),
      array(1005,"4:45 PM"),
      array(1020,"5:00 PM"),
      array(1035,"5:15 PM"),
      array(1050,"5:30 PM"),
      array(1065,"5:45 PM"),
      array(1080,"6:00 PM"),
      array(1095,"6:15 PM"),
      array(1110,"6:30 PM"),
      array(1125,"6:45 PM"),
      array(1140,"7:00 PM"),
      array(1155,"7:15 PM"),
      array(1170,"7:30 PM"),
      array(1185,"7:45 PM"),
      array(1200,"8:00 PM"),
      array(1215,"8:15 PM"),
      array(1230,"8:30 PM"),
      array(1245,"8:45 PM"),
      array(1260,"9:00 PM"),
      array(1275,"9:15 PM"),
      array(1290,"9:30 PM"),
      array(1305,"9:45 PM"),
      array(1320,"10:00 PM"),
      array(1335,"10:15 PM"),
      array(1350,"10:30 PM"),
      array(1365,"10:45 PM"),
      array(1380,"11:00 PM"),
      array(1395,"11:15 PM"),
      array(1410,"11:30 PM"),
      array(1425,"11:45 PM"),
      array(1440,"Midnight Following Day"),
      array(1455,"12:15 AM Following Day"),
      array(1470,"12:30 AM Following Day"),
      array(1485,"12:45 AM Following Day"),
      array(1500,"1:00 AM Following Day"),
      array(1515,"1:15 AM Following Day"),
      array(1530,"1:30 AM Following Day"),
      array(1545,"1:45 AM Following Day"),
      array(1560,"2:00 AM Following Day"),
      array(1575,"2:15 AM Following Day"),
      array(1590,"2:30 AM Following Day"),
      array(1605,"2:45 AM Following Day"),
      array(1620,"3:00 AM Following Day"),
      array(1635,"3:15 AM Following Day"),
      array(1650,"3:30 AM Following Day"),
      array(1665,"3:45 AM Following Day"),
      array(1680,"4:00 AM Following Day"),
      array(1695,"4:15 AM Following Day"),
      array(1710,"4:30 AM Following Day"),
      array(1725,"4:45 AM Following Day"),
      array(1740,"5:00 AM Following Day"),
      array(1755,"5:15 AM Following Day"),
      array(1770,"5:30 AM Following Day"),
      array(1785,"5:45 AM Following Day"),
      array(1800,"6:00 AM Following Day"),
      array(1815,"6:15 AM Following Day"),
      array(1830,"6:30 AM Following Day"),
      array(1845,"6:45 AM Following Day"),
      array(1860,"7:00 AM Following Day"),
      array(1875,"7:15 AM Following Day"),
      array(1890,"7:30 AM Following Day"),
      array(1905,"7:45 AM Following Day"),
      array(1920,"8:00 AM Following Day"),
      array(1935,"8:15 AM Following Day"),
      array(1950,"8:30 AM Following Day"),
      array(1965,"8:45 AM Following Day"),
      array(1980,"9:00 AM Following Day"),
      array(1995,"9:15 AM Following Day"),
      array(2010,"9:30 AM Following Day"),
      array(2025,"9:45 AM Following Day"),
      array(2040,"10:00 AM Following Day"),
      array(2055,"10:15 AM Following Day"),
      array(2070,"10:30 AM Following Day"),
      array(2085,"10:45 AM Following Day"),
      array(2100,"11:00 AM Following Day"),
      array(2115,"11:15 AM Following Day"),
      array(2130,"11:30 AM Following Day"),
      array(2145,"11:45 AM Following Day"),
      array(2160,"Noon Following Day")
      );
   $latest_possible_trip_arrival_time_select=
      output_select("latest_possible_trip_arrival_time",
		    $latest_possible_trip_arrival_time_array,
		    $latest_possible_trip_arrival_time);
   $template->setVariable("LATEST_POSSIBLE_TRIP_ARRIVAL_TIME_SELECT",
			  $latest_possible_trip_arrival_time_select);
   $expected_trip_duration_mins_array=array(
      array(5,"5 minutes"),
      array(10,"10 minutes"),
      array(15,"15 minutes"),
      array(20,"20 minutes"),
      array(25,"25 minutes"),
      array(30,"30 minutes"),
      array(35,"35 minutes"),
      array(40,"40 minutes"),
      array(45,"45 minutes"),
      array(50,"50 minutes"),
      array(55,"55 minutes"),
      array(60,"1 hour"),
      array(70,"1 hour 10 minutes"),
      array(80,"1 hour 20 minutes"),
      array(90,"1 hour 30  minutes"),
      array(100,"1 hour 40 minutes"),
      array(110,"1 hour 50 minutes"),
      array(120,"2 hours"),
      array(135,"2 hours 15 minutes"),
      array(150,"2 hours 30 minutes"),
      array(165,"2 hours 45 minutes"),
      array(180,"3 hours"),
      array(195,"3 hours 15 minutes"),
      array(210,"3 hours 30 minutes"),
      array(225,"3 hours 45 minutes"),
      array(240,"4 hours"),
      array(255,"4 hours 15 minutes"),
      array(270,"4 hours 30 minutes"),
      array(285,"4 hours 45 minutes"),
      array(300,"5 hours"),
      array(315,"5 hours 15 minutes"),
      array(330,"5 hours 30 minutes"),
      array(345,"5 hours 45 minutes"),
      array(360,"6 hours"),
      array(390,"6 hours 30 minutes"),
      array(420,"7 hours"),
      array(450,"7 hours 30 minutes"),
      array(480,"8 hours"),
      array(510,"8 hours 30 minutes"),
      array(540,"9 hours"),
      array(600,"10 hours"),
      array(660,"11 hours"),
      array(720,"12 hours"),
      );
   $expected_trip_duration_mins_select=
      output_select("expected_trip_duration_mins",
		    $expected_trip_duration_mins_array,
		    $expected_trip_duration_mins);
   $template->setVariable("EXPECTED_TRIP_DURATION_MINS_SELECT",
			  $expected_trip_duration_mins_select);
   $return_trip_earliest_possible_trip_departure_time_select=
      output_select("return_trip_earliest_possible_trip_departure_time",
		    $earliest_possible_trip_departure_time_array,
		    $return_trip_earliest_possible_trip_departure_time);
   $template->setVariable("RETURN_TRIP_EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME_SELECT",
			  $return_trip_earliest_possible_trip_departure_time_select);
   $return_trip_latest_possible_trip_arrival_time_select=
      output_select("return_trip_latest_possible_trip_arrival_time",
		    $latest_possible_trip_arrival_time_array,
		    $return_trip_latest_possible_trip_arrival_time);
   $template->setVariable("RETURN_TRIP_LATEST_POSSIBLE_TRIP_ARRIVAL_TIME_SELECT",
			  $return_trip_latest_possible_trip_arrival_time_select);
   $return_trip_expected_trip_duration_mins_select=
      output_select("return_trip_expected_trip_duration_mins",
		    $expected_trip_duration_mins_array,
		    $return_trip_expected_trip_duration_mins);
   $template->setVariable("RETURN_TRIP_EXPECTED_TRIP_DURATION_MINS_SELECT",
			  $return_trip_expected_trip_duration_mins_select);

   set_radio_check($template,"driver_passenger",array("d","p","b"),$driver_passenger);
   set_radio_check($template,"regular_trip",array("n","y"),$regular_trip);
   set_radio_check($template,"is_return_trip",array("n","y"),$is_return_trip);
   for($i=1;$i<=7;$i++)
   {
      $dayi="day{$i}";
      set_check($template,$dayi,1,$$dayi);
   }
   $template->setVariable("COMMENTS",xss_encode($comments));
   if(is_user_permanent($connection))
      show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}

function display_saved_plan_trip($connection,$saved_plan_trip)
{

   $template=load_plan_trip_template();
    display_plan_trip(
       $connection,
       $template,
       $saved_plan_trip["driver_passenger"],
       $saved_plan_trip["regular_trip"],
       $saved_plan_trip["day1"],
       $saved_plan_trip["day2"],
       $saved_plan_trip["day3"],
       $saved_plan_trip["day4"],
       $saved_plan_trip["day5"],
       $saved_plan_trip["day6"],
       $saved_plan_trip["day7"],
       $saved_plan_trip["trip_date_day"],
       $saved_plan_trip["trip_date_month"],
       $saved_plan_trip["trip_date_year"],
       $saved_plan_trip["earliest_possible_trip_departure_time"],
       $saved_plan_trip["latest_possible_trip_arrival_time"],
       $saved_plan_trip["expected_trip_duration_mins"],
       $saved_plan_trip["is_return_trip"],
       $saved_plan_trip["return_trip_earliest_possible_trip_departure_time"],
       $saved_plan_trip["return_trip_latest_possible_trip_arrival_time"],
       $saved_plan_trip["return_trip_expected_trip_duration_mins"],
       $saved_plan_trip["trip_origin_location_id"],
       $saved_plan_trip["trip_destination_location_id"],
       $saved_plan_trip["vehicle_id"],
       $saved_plan_trip["comments"]
       );
}

function display_trip_class_plan_trip($connection,$t)
{
   $template=load_plan_trip_template();
   
   if($t->is_driver)
   {
      if($t->is_passenger)
	 $driver_passenger='b';
      else
	 $driver_passenger='d';
   }
   else
   {
      if($t->is_passenger)
	 $driver_passenger='p';
      else
	 die("display_trip_class_plan_trip illegal driver_passenger\n");
   }
   $regular_trip=($t->regular_trip ? 'y' : 'n');
   $is_return_trip=($t->is_return_trip ? 'y' : 'n' );
   dashed_year_month_day($t->trip_date,$trip_date_year,$trip_date_month,
		  $trip_date_day);
   display_plan_trip(
   $connection,
   $template,
   $driver_passenger,
   $regular_trip,
   $t->trip_day[0],$t->trip_day[1],$t->trip_day[2],$t->trip_day[3],
   $t->trip_day[4],$t->trip_day[5],$t->trip_day[6],
   $trip_date_day,$trip_date_month,$trip_date_year,
   $t->outward->earliest_possible_trip_departure_time,
   $t->outward->latest_possible_trip_arrival_time,
   $t->outward->expected_trip_duration_mins,
   $is_return_trip,
   $t->return->earliest_possible_trip_departure_time,
   $t->return->latest_possible_trip_arrival_time,
   $t->return->expected_trip_duration_mins,
   $t->trip_origin_location_id,
   $t->trip_destination_location_id,
   $t->vehicle_id,
   $t->comments);
}


?>