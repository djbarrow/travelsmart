<?php
require_once "include/tripplan_func.php";
require_once "include/latlonfunc.php";
require_once "Date/Calc.php";
require_once "include/time.php";
require_once "include/progress.php";
require_once "include/defines.php";

function mpg_to_kmpl($mpg)
{
   return (($mpg*1.609)/4.4546);
}


function get_vehicle_type_from_vehicle_id($connection,$vehicle_id)
{
   $query="SELECT * FROM vehicle WHERE vehicle_id = {$vehicle_id}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $num_rows=mysql_num_rows($result);
   switch($num_rows)
   {     
      case 1:
	 $row=mysql_fetch_array($result);
	 return $row["vehicle_type"];
      case 0:
	 if($vehicle_id==0)
	    return default_vehicle_type; 
      default:
	  die("num_rows!=1 it is ${numrows} for vehicles matching vehicle_id={$vehicle_id}\n");
   }
}

function get_vehicle_carbon_footprint_per_km_from_vehicle_id($connection,$vehicle_id,
							     &$vehicle_type,
							     &$carbon_footprint_per_km,&$kmpl)
{
   $vehicle_type=get_vehicle_type_from_vehicle_id($connection,$vehicle_id);
   $query="SELECT * FROM vehicle_type WHERE type_id={$vehicle_type}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $num_rows=mysql_num_rows($result);
   if($num_rows!=1)
      die("num_rows!=1 it is ${numrows} for vehicle_type matching type_id\n");
   $row2=mysql_fetch_array($result);
   $query="SELECT * FROM fuel WHERE fuel_id=${row2["primary_fuel"]}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $num_rows=mysql_num_rows($result);
   if($num_rows!=1)
      die("num_rows!=1 it is ${numrows} for fuel matching fuel_id\n");
   $row3=mysql_fetch_array($result);
   $kmpl=mpg_to_kmpl($row2["mpg"]);
   $carbon_footprint_per_km=$row3["carbon_footprint_per_litre"]/$kmpl;
}

class vehicle_breakdown_stats
{
   var $num_one_time_trips;
   var $num_regular_trips;
   var $total_mileage_saved;
   var $litres_of_fuel_saved;
   var $carbon_footprint_saved;
   function __construct()
   {
      $this->num_one_time_trips=
	 $this->num_regular_trips=
	 $this->total_mileage_saved=
	 $this->litres_of_fuel_saved=
	 $this->carbon_footprint_saved=0;
   }
}

class server_stats
{
   var $calc_time;
   var $number_of_users_registered;
   var $number_of_matched_trips;
   var $total_mileage_saved;
   var $litres_of_fuel_saved;
   var $carbon_footprint_saved;
   var $number_of_trips;
   var $number_of_regular_trips;
   var $number_of_one_time_trips;
   var $number_of_matched_regular_trips;
   var $number_of_one_time_trips_matched;
   var $number_of_one_time_trips_completed;
   var $number_of_searchable_regular_trips;
   var $number_of_unexpired_one_time_trips;
   var $number_of_unexpired_searchable_one_time_trips;
   var $vehicle_stats=array();
}

function get_server_stats($connection,$all,$search_period)
{
   $connection=db_connect_if_neccessary($connection);
   $prb=progress_init("Calculating Server Stats");
   $stats=new server_stats();
   if($search_period==0)
   {
      $trip_query_search_period1=
	 $match_query_search_period1="";
      $trip_query_search_period2=
	 $match_query_search_period2="";
   }
   else
   {
      if($search_period>=2007)
      {
	 $start_time=$search_period . "0101000000";
	 $end_time=$search_period+1 . "0101000000";
	 $trip_query_search_period="create_time>=" 
	    . $start_time . " and create_time<"
	    . $end_time;
	 $match_query_search_period="match_time>=" 
	    . $start_time . " and match_time<"
	    . $end_time;
      }
      else
      {
	 $trip_query_search_period="create_time>=(NOW()-INTERVAL ${search_period} DAY)";
	 $match_query_search_period="match_time>=(NOW()-INTERVAL ${search_period} DAY)";
      }
      $trip_query_search_period1=" WHERE " . $trip_query_search_period;
      $match_query_search_period1=" WHERE " . $match_query_search_period;
      $trip_query_search_period2=" AND " . $trip_query_search_period;
      $match_query_search_period2=" AND " . $match_query_search_period;
   }
   $query="SELECT user_id FROM user WHERE parent_user_id is NULL AND is_permanent=TRUE"; 
   if($search_period==0)
   {      
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $stats->number_of_users_registered=mysql_num_rows($result);
   }
   else
   {
      if($search_period>=2007)
	 $query.=" AND create_time>=" 
	    . $start_time . " and create_time<"
	    . $end_time;
      else
	 $query.=" AND create_time>=(NOW()-INTERVAL {$search_period} DAY)"; 
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $stats->number_of_users_registered=0;
      while($row=mysql_fetch_array($result))
      {
	 $query="SELECT user_id FROM user WHERE parent_user_id=${row["user_id"]} AND is_permanent=TRUE";
	 if($search_period>=2007)
	    " AND create_time<" . $start_time;
	 else
	    " AND create_time<(NOW()-INTERVAL {$search_period} DAY)";
	 if (!$result2 = @ mysql_query ($query, $connection))
	    showerror();
	 if(mysql_num_rows($result2)==0)
	    $stats->number_of_users_registered++;
      }
   }
   $query="SELECT user_id FROM saved_tripmatch WHERE trip_id>other_trip_id";
   if($search_period)
      $query.=$match_query_search_period2;
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $stats->number_of_matched_trips=mysql_num_rows($result);

  
   $query = "SELECT * FROM vehicle_type";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   while($row=mysql_fetch_array($result))
   {
      $stats->vehicle_stats[$row["type_id"]]=new vehicle_breakdown_stats();
   }
   $stats->number_of_matched_regular_trips=
      $stats->number_of_one_time_trips_matched=
      $stats->number_of_one_time_trips_completed=
      $stats->total_mileage_saved=
      $stats->litres_of_fuel_saved=
      $stats->carbon_footprint_saved=0;
   $query="SELECT * FROM saved_tripmatch WHERE trip_id>other_trip_id";
   if($search_period)
      $query.=$match_query_search_period2;
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $curr_percent=$last_percent=0;
   if($stats->number_of_matched_trips==0)
     $inc_percent=100;
   else
     $inc_percent=100/$stats->number_of_matched_trips;
   while($row=mysql_fetch_array($result))
   {
      $t=get_trip_info_from_trip_id($row["trip_id"],$connection,1);
      $c=get_trip_info_from_trip_id($row["other_trip_id"],$connection,1);
      if($t->is_driver)
      {
	 if($c->is_passenger)
	    check_trip_compat($t,$c,$c);
	 get_vehicle_carbon_footprint_per_km_from_vehicle_id($connection,
							     $t->vehicle_id,
							     $t_vehicle_type,
							     $t_carbon_footprint_per_km,
							     $t_kmpl);
	 $t_vehicle_stats=$stats->vehicle_stats[$t_vehicle_type];
      }
      if($c->is_driver)
      {
	 if($t->is_passenger)
	    check_trip_compat($c,$t,$c);
	 get_vehicle_carbon_footprint_per_km_from_vehicle_id($connection,
							     $c->vehicle_id,
							     $c_vehicle_type,
							     $c_carbon_footprint_per_km,
							     $c_kmpl);
	 $c_vehicle_stats=$stats->vehicle_stats[$c_vehicle_type];
      }
      $mileage_saved=$c->mileage_saved;
      if($c->regular_trip&&$t->regular_trip)
      {
	 $query="SELECT * FROM saved_tripmatch WHERE trip_id=${row["other_trip_id"]} AND other_trip_id=${row["trip_id"]}";
	 if (!$result2 = @ mysql_query ($query, $connection))
	    showerror();
	 $row2=mysql_fetch_array($result2);
	 if(empty($row2))
	    die("row2 empty\n");
	 $expire_time1=$row["expire_time"];
	 if($expire_time1==null)
	    $expire_time1=get_datetime();
	 else
	    $expire_time1=mysql_cleantime($expire_time1);
	 $expire_time2=$row2["expire_time"];
	 if($expire_time2==null)
	    $expire_time2=get_datetime();
	 else
	    $expire_time2=mysql_cleantime($expire_time2);
	 $expire_time=($expire_time1<$expire_time2 ? $expire_time1:$expire_time2);
	 if($search_period>=2007)
	 {
	    if($end_time<$expire_time)
	       $expire_time=$end_time;
	 }
	 $match_time=mysql_cleantime($row["match_time"]);
	 $num_match_days=time_to_days($expire_time)-time_to_days($match_time);
	 $num_trips_matched=(int)(($c->num_trips_matched*$num_match_days)/7);
	 $stats->number_of_matched_regular_trips+=$num_trips_matched;
	 $total_mileage_saved=$num_trips_matched*$mileage_saved;
	 if($t->is_driver)
	 {
	    if($c->is_driver)
	    {
	       $t_vehicle_stats->num_regular_trips+=(.5*$num_trips_matched);
	       $c_vehicle_stats->num_regular_trips+=(.5*$num_trips_matched);
	    }
	    else
	       $t_vehicle_stats->num_regular_trips+=$num_trips_matched;  
	 }
	 else
	    $c_vehicle_stats->num_regular_trips+=$num_trips_matched;
      }
      else
      {
	 $stats->number_of_one_time_trips_matched+=$c->num_trips_matched;
	 if(!$t->regular_trip)
	    $trip_date=$t->trip_date;
	 else
	    $trip_date=$c->trip_date;
	 $date_time=strtotime($trip_date);
	 if($date_time<=time())
	 {
	    $stats->number_of_one_time_trips_completed+=$c->num_trips_matched;
	    $total_mileage_saved=$c->num_trips_matched*$mileage_saved;
	    if($t->is_driver)
	    {
	       if($c->is_driver)
	       {
		  $t_vehicle_stats->num_one_time_trips+=(.5*$c->num_trips_matched);
		  $c_vehicle_stats->num_one_time_trips+=(.5*$c->num_trips_matched);
	       }
	       else
		  $t_vehicle_stats->$num_one_time_trips+=$c->num_trips_matched;  
	    }
	    else
	       $c_vehicle_stats->$num_one_time_trips+=$c->num_trips_matched;
	 }
      }
      if($t->is_driver)
      {
	 if($c->is_driver)
	 {
	    $carbon_footprint_per_km=($t_carbon_footprint_per_km+
				      $c_carbon_footprint_per_km)/2;
	    $kmpl=($t_kmpl+$c_kmpl)/2;
	 }
	 else
	 {
	    $carbon_footprint_per_km=$t_carbon_footprint_per_km;
	    $kmpl=$t_kmpl;
	 }
      }
      else
      {
	 $carbon_footprint_per_km=$c_carbon_footprint_per_km;
	 $kmpl=$c_kmpl;
      }
      $stats->total_mileage_saved+=$total_mileage_saved;
      $stats->litres_of_fuel_saved+=($total_mileage_saved/$kmpl);
      $stats->carbon_footprint_saved+=($total_mileage_saved*$carbon_footprint_per_km);
      if($t->is_driver)
      {
	 if($c->is_driver)
	 {
	    $t_vehicle_stats->total_mileage_saved+=(.5*$total_mileage_saved);
	    $c_vehicle_stats->total_mileage_saved+=(.5*$total_mileage_saved);
	    $t_vehicle_stats->litres_of_fuel_saved+=(.5*($total_mileage_saved/$t_kmpl));
	    $c_vehicle_stats->litres_of_fuel_saved+=(.5*($total_mileage_saved/$c_kmpl));
	    $t_vehicle_stats->carbon_footprint_saved+=
	       (.5*($total_mileage_saved*$t_carbon_footprint_per_km));
	    $c_vehicle_stats->carbon_footprint_saved+=
	       (.5*($total_mileage_saved*$c_carbon_footprint_per_km));
	 }
	 else
	 {
	    $t_vehicle_stats->total_mileage_saved+=$total_mileage_saved;
	    $t_vehicle_stats->litres_of_fuel_saved+=($total_mileage_saved/$t_kmpl);
	    $t_vehicle_stats->carbon_footprint_saved+=
	       ($total_mileage_saved*$t_carbon_footprint_per_km);
	 }
      }
      else
      {
	 $c_vehicle_stats->total_mileage_saved+=$total_mileage_saved;
	 $c_vehicle_stats->litres_of_fuel_saved+=($total_mileage_saved/$t_kmpl);
	 $c_vehicle_stats->carbon_footprint_saved+=
	    ($total_mileage_saved*$c_carbon_footprint_per_km);
      }
      $curr_percent+=$inc_percent;
      if((int)$curr_percent>$last_percent)
      {
	 $last_percent=(int)$curr_percent;
	 progress_move_step($prb,$curr_percent);
      }
   }
   $stats->total_mileage_saved=sprintf("%.1f",$stats->total_mileage_saved);
   $stats->litres_of_fuel_saved=sprintf("%.1f",$stats->litres_of_fuel_saved);
   $stats->carbon_footprint_saved=sprintf("%.1f",$stats->carbon_footprint_saved);
   if($all==0)
   {
      progress_hide($prb);
      return $stats;
   }
   $query="SELECT user_id FROM trip";
   if($search_period)
      $query.=$trip_query_search_period1;
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $stats->number_of_trips=mysql_num_rows($result);
   $query="SELECT user_id FROM trip WHERE regular_trip=TRUE";
   if($search_period)
      $query.=$trip_query_search_period2;
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $stats->number_of_regular_trips=mysql_num_rows($result);
   $query="SELECT user_id FROM trip WHERE regular_trip=FALSE";
   if($search_period)
      $query.=$trip_query_search_period2;
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $stats->number_of_one_time_trips=mysql_num_rows($result);
   $query="SELECT user_id FROM trip WHERE regular_trip=TRUE AND searchable=TRUE";
   if($search_period)
      $query.=$trip_query_search_period2;
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $stats->number_of_searchable_regular_trips=mysql_num_rows($result);
   $query="SELECT user_id FROM trip WHERE regular_trip=FALSE AND trip_date>=NOW()";
   if($search_period)
      $query.=$trip_query_search_period2;
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $stats->number_of_unexpired_one_time_trips=mysql_num_rows($result);
   $query="SELECT user_id FROM trip WHERE regular_trip=FALSE AND trip_date>=NOW() AND searchable=TRUE";
   if($search_period)
      $query.=$trip_query_search_period2;
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $stats->number_of_unexpired_searchable_one_time_trips=mysql_num_rows($result);
   progress_hide($prb);
   return $stats;
}


function get_stats($connection)
{
   $connection=db_connect_if_neccessary($connection);
   $query = "SELECT * FROM stats WHERE create_time>(NOW()-INTERVAL 6 HOUR)";
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   $num_rows=mysql_num_rows($result);
   switch($num_rows)
   {
      case 0:
	 $row=mysql_fetch_array($result);
	 $stats=get_server_stats($connection,0,0);
	 $time=time();
	 $datetime=mysql_format_time($time);
	 $stats->calc_time=mysql_format_time2($time);
	    
	 $query="REPLACE INTO stats VALUES("
	    . "1,"
	    . "'{$datetime}',"
	    . "{$stats->number_of_users_registered},"
	    . "{$stats->number_of_matched_trips},"
	    . "{$stats->total_mileage_saved},"
	    . "{$stats->litres_of_fuel_saved},"
	    . "{$stats->carbon_footprint_saved}"
	    .")";
	 if (!$result = @ mysql_query ($query, $connection))
	    showerror();
	 break;
      case 1:
	 $row=mysql_fetch_array($result);
	 $stats=new server_stats();
	 $stats->calc_time=$row["create_time"];
	 $stats->number_of_users_registered=$row["number_of_users_registered"];
	 $stats->number_of_matched_trips=$row["number_of_matched_trips"];
	 $stats->total_mileage_saved=$row["total_mileage_saved"];
	 $stats->litres_of_fuel_saved=$row["litres_of_fuel_saved"];
	 $stats->carbon_footprint_saved=$row["carbon_footprint_saved"];
	 break;
      default:
	 die("get_stats num_rows>1 ${num_rows}\n");
   }
   return $stats;

}

?>