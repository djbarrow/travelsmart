<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/select.php";
require_once "HTML/Template/IT.php";
require_once "Date/Calc.php";
require_once "include/latlonfunc.php";
require_once "include/time.php";
require_once "include/tripplan_func.php";
require_once "include/config.php";
require_once "include/display_plan_trip.php";
require_once "include/validate.php";
require_once "include/temp_user.php";
require_once "include/tripmatch_logic.php";

session_authenticate(-1); 
$validate_error=0;

$connection=db_connect();
$template= load_plan_trip_template();
$t= new trip();
if ($_POST)
{
   if(!empty($_POST["find_trip_matches"]))
      unset($_SESSION["saved_plan_trip"]);
   else
   {
      $_SESSION["saved_plan_trip"]=serialize($_POST); 
      if(!empty($_POST["add_new_vehicle"]))
	 url_forward("add_new_vehicle.php");
      else
	 url_forward("add_new_location.php");
   }
   $driver_passenger=mysqlclean($_POST,"driver_passenger"
				,radio_button_len,$connection);
   if(empty($driver_passenger))
      show_error_block($template,"DRIVER_PASSENGER_UNSELECTED");
   $t->is_driver=(($driver_passenger=='d' || $driver_passenger=='b') ? 1:0);
   $t->is_passenger=(($driver_passenger=='p' || $driver_passenger=='b') ? 1:0);
   $regular_trip=mysqlclean($_POST,"regular_trip"
			    ,radio_button_len,$connection);
   if(empty($regular_trip))
      show_error_block($template,"TRIP_TYPE_UNSELECTED");
   $t->regular_trip=($regular_trip=='y' ? 1:0); 
   $t->trip_day[0]=(empty($_POST["day1"]) ? 0:1);
   $t->trip_day[1]=(empty($_POST["day2"]) ? 0:1);
   $t->trip_day[2]=(empty($_POST["day3"]) ? 0:1);
   $t->trip_day[3]=(empty($_POST["day4"]) ? 0:1);
   $t->trip_day[4]=(empty($_POST["day5"]) ? 0:1);
   $t->trip_day[5]=(empty($_POST["day6"]) ? 0:1);
   $t->trip_day[6]=(empty($_POST["day7"]) ? 0:1);
   $trip_date_day=mysqlclean($_POST,"trip_date_day"
			     ,day_len,$connection);
   $trip_date_month=mysqlclean($_POST,"trip_date_month"
			       ,month_len,$connection);
   $trip_date_year=mysqlclean($_POST,"trip_date_year"
			      ,year_len,$connection);
   if(empty($trip_date_day)||
      empty($trip_date_month)||
      empty($trip_date_year)||
      (!checkdate($trip_date_month,$trip_date_day,$trip_date_year)))
      show_error_block($template,"DATE_INVALID");
   if(!$t->regular_trip&&Date_Calc::isPastDate($trip_date_day,$trip_date_month,$trip_date_year))
      show_error_block($template,"DATE_IS_IN_PAST");
   $t->undashed_trip_date=zero_pad_trip_date($trip_date_year,
					     $trip_date_month,
					     $trip_date_day);
   $t->trip_date=dash_date($t->undashed_trip_date);
   $t->trip_origin_location_id=mysqlclean($_POST,"trip_origin_location_id"
					  ,id_len,$connection);
   if(empty($t->trip_origin_location_id))
      show_error_block($template,"TRIP_ORIGIN_UNSELECTED");
   $t->trip_destination_location_id=mysqlclean($_POST,"trip_destination_location_id"
					       ,id_len,$connection);
   if(empty($t->trip_destination_location_id))
      show_error_block($template,"TRIP_DESTINATION_UNSELECTED");
   $t->outward->earliest_possible_trip_departure_time=mysqlclean($_POST,"earliest_possible_trip_departure_time",time_mins_len,$connection);
   $t->outward->latest_possible_trip_arrival_time=mysqlclean($_POST,"latest_possible_trip_arrival_time",time_mins_len,$connection);

   $t->outward->expected_trip_duration_mins=mysqlclean($_POST,"expected_trip_duration_mins",duration_mins_len,$connection);
   if((($t->outward->earliest_possible_trip_departure_time+$t->outward->expected_trip_duration_mins)>
      $t->outward->latest_possible_trip_arrival_time)||
      empty($t->outward->expected_trip_duration_mins))
      show_error_block($template,"INCONSISTENT_TRIP_TIMES");
   $is_return_trip=mysqlclean($_POST,"is_return_trip"
			    ,radio_button_len,$connection);
   if(empty($is_return_trip))
      show_error_block($template,"IS_RETURN_UNSELECTED");
   $t->is_return_trip=($is_return_trip=='y' ? 1:0);
   $t->return->earliest_possible_trip_departure_time=mysqlclean($_POST,"return_trip_earliest_possible_trip_departure_time",time_mins_len,$connection);
   $t->return->latest_possible_trip_arrival_time=mysqlclean($_POST,"return_trip_latest_possible_trip_arrival_time",time_mins_len,$connection);

   $t->return->expected_trip_duration_mins=mysqlclean($_POST,"return_trip_expected_trip_duration_mins",duration_mins_len,$connection);
   if((($t->return->earliest_possible_trip_departure_time+$t->return->expected_trip_duration_mins)>
      $t->return->latest_possible_trip_arrival_time)||
      empty($t->return->expected_trip_duration_mins))
      show_error_block($template,"RETURN_TRIP_INCONSISTENT_TRIP_TIMES");
   if($t->is_driver)
   {
      $t->vehicle_id=mysqlclean($_POST,"vehicle_id",id_len,$connection);
      if(empty($t->vehicle_id))
      {
	 if(is_user_permanent($connection))
	    show_error_block($template,"VEHICLE_UNSELECTED");
	 else
	    $t->vehicle_id=0;
      }
   }
   else
      $t->vehicle_id=0;
   if(empty($_POST["comments"]))
      $t->comments="";
   else
      $t->comments=xss_decode($_POST["comments"]);

mysqlclean($_POST,"comments"
			      ,comments_len,$connection);
   if(!$validate_error)
   {
      if(!empty($_SESSION["delete_trip_id"]))
      {
	 $query = "DELETE FROM trip WHERE trip_id = ${_SESSION["delete_trip_id"]}";
	 if (!$result = @ mysql_query ($query, $connection))
	    showerror();
	 unset($_SESSION["delete_trip_id"]);
      } 
      if(!empty($_SESSION["make_unsearchable_trip_id"]))
      {
	 change_trip_matchable_state($connection,$_SESSION["make_unsearchable_trip_id"],0);
	 unset($_SESSION["make_unsearchable_trip_id"]);
      }
      insert_trip($connection,$t,(is_user_permanent($connection,0,1) ? 1:0));
      tripmatch_logic($connection,$t);
   }
}
else
{
   $driver_passenger="";
   $regular_trip="";
   $t->trip_day[1]=$t->trip_day[2]=$t->trip_day[3]=
      $t->trip_day[4]=$t->trip_day[5]=1;
   $t->trip_day[0]=$t->trip_day[6]=0;
   $trip_date_year=Date_Calc::dateNow('%Y');
   $trip_date_month=Date_Calc::dateNow('%m');
   $trip_date_day=Date_Calc::dateNow('%d');
   $t->outward->earliest_possible_trip_departure_time=480;
   $t->outward->latest_possible_trip_arrival_time=540;
   $t->outward->expected_trip_duration_mins=30;
   $is_return_trip="";
   $t->return->earliest_possible_trip_departure_time=1020;
   $t->return->latest_possible_trip_arrival_time=1080;
   $t->return->expected_trip_duration_mins=30;
   if(is_user_permanent($connection))
   {
      $query = "SELECT address_location_id FROM user WHERE user_id=${_SESSION["user_id"]}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $numrows=mysql_num_rows($result);
      if($numrows!=1)
	 die("plan_trip getting home address num_rows!=1 ${numrows}\n");
      $row=mysql_fetch_array($result);
      $t->trip_origin_location_id=$row["address_location_id"];
   }
   else
      $t->trip_origin_location_id=0;
   $t->trip_destination_location_id=0;
   $t->vehicle_id=0;
   $t->comments="";
}
if(!$_POST||$validate_error)
{
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