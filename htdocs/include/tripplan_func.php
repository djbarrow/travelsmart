<?php
require_once "include/config.php";
require_once "include/display_plan_trip.php";

class latlon
{
   var $country_name;
   var $adm1_region_name;
   var $nearby_location_name;
   var $nearby_location_direction;
   var $nearby_location_distance;
   var $name;
   var $lat;
   var $lon;
}

class trip_times
{
   var $earliest_possible_trip_departure_time;
   var $latest_possible_trip_arrival_time;
   var $expected_trip_duration_mins;
}

class trip
{
   var $trip_id;
   var $user_id;
   var $is_driver;
   var $is_passenger;
   var $regular_trip;
   var $trip_day=array();
   var $trip_date;
   var $undashed_trip_date;
   var $is_return_trip;
   var $outward;
   var $return;
   var $trip_origin_location_id;
   var $trip_destination_location_id;
   var $vehicle_id;
   var $comments;
   var $trip_origin_latlon;
   var $trip_destination_latlon;
   var $total_mileage_saved;
   var $mileage_saved;
   var $num_trips_matched;
   var $driver_outward_passenger_outward;
   var $driver_return_passenger_outward;
   var $driver_return_passenger_return;
   var $driver_outward_passenger_return;
   function __construct()
   {
      $this->trip_origin_latlon= new latlon();
      $this->trip_destination_latlon = new latlon();
      $this->outward = new trip_times();
      $this->return = new trip_times();
      $this->total_mileage_saved=0;
      $this->mileage_saved=
	 $this->num_trips_matched=0;
      $this->driver_outward_passenger_outward=
	 $this->driver_return_passenger_outward=
	 $this->driver_return_passenger_return=
	 $this->driver_outward_passenger_return=0;
   }
}

$compass_direction=array("North","NNE","NE","ENE",
			 "East","ESE","SE","SSE",
			 "South","SSW","SW","WSW",
			 "West","WNW","NW","NNW");

function get_user_location_latlon($connection,$user_location_id,$latlon,$quick=0)
{

   global $compass_direction;

   $query = "SELECT * FROM user_location WHERE "
      . "location_id=${user_location_id}";
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   $numrows=mysql_num_rows($result);
   if($numrows!=1)
      die("get_user_location_latlon, illegal num rows ". $numrows ." for user location\n");
   $row=mysql_fetch_array($result);
   $latlon->lat=$row["latitude"];
   $latlon->lon=$row["longitude"];
   if($quick)
      return;
   $latlon->name=$row["name"];
   $nearby_location_ufi=$row["nearby_location_ufi"];
   if($nearby_location_ufi==unknown_nearby_location_ufi)
   {
      $latlon->country_name="Unknown Country";
      $latlon->adm1_region_name=$latlon->nearby_location_name="";
      $latlon->nearby_location_direction=$compass_direction[0];
      $latlon->nearby_location_distance=0;
   }
   else
   {
      $query = "SELECT * FROM location WHERE "
	 . "ufi=${nearby_location_ufi}";
      if(!($result=@mysql_query($query,$connection)))
	 showerror();
      $numrows=mysql_num_rows($result);
      if($numrows!=1)
	 die("get_user_location_latlon, illegal num rows ". $numrows ." for nearby location\n");
      $row=mysql_fetch_array($result);
      $latlon->nearby_location_name=$row["name"];
      $nearby_location_lat=$row["latitude"];
      $nearby_location_lon=$row["longitude"];
      $latlon->nearby_location_distance=round(getlatlondist
					      ($latlon->lat,$latlon->lon,
					       $nearby_location_lat,$nearby_location_lon,
					       EARTH_RADIUS));
      $angle=getlatlonangle($nearby_location_lat,$nearby_location_lon,$latlon->lat,$latlon->lon);
      $angle=(round($angle/22.5))%16;
   
      $latlon->nearby_location_direction=$compass_direction[$angle];
      $adm1_region_ufi=$row["adm1_region_ufi"];
      $query = "SELECT name,cc1 FROM adm1_region WHERE "
	 . "ufi=${adm1_region_ufi}";
      if(!($result=@mysql_query($query,$connection)))
	 showerror();
      $numrows=mysql_num_rows($result);
      if($numrows!=1)
	 die("get_user_location_latlon, illegal num rows ". $numrows ." for adm1_region\n");
      $row=mysql_fetch_array($result);
      $latlon->adm1_region_name=$row["name"];
      if(MULTIPLE_COUNTRIES)
      {
	 $query = "SELECT * FROM country where cc='${row["cc1"]}'";
	 if (!$result = @ mysql_query ($query, $connection))
	    showerror();
	 $numrows=mysql_num_rows($result);
	 if($numrows!=1)
	    die("get_user_location_latlon, illegal num rows ". $numrows ." for country\n");
	 $row=mysql_fetch_array($result);
	 $latlon->country_name=$row["name"];
      }
      else
	 $latlon->country_name="";
   }
}

function sprintf_latlon($val)
{
   $str=sprintf("%.4f",$val);
   for($i=strlen($str)-1;($i>0&&$str[$i]=='0');$i--)
   {
      $str=substr($str,0,$i);
      if($str[$i-1]=='.')
      {
	 $str=substr($str,0,$i-1);
	 break;
      }
   }
   return $str;
}


function make_latlon_string_from_latlon($latlon)
{
   return "(" . sprintf_latlon(abs($latlon->lat)) 
      . ($latlon->lat>0 ? "N" : "S") . "," .
      sprintf_latlon(abs($latlon->lon)) 
      . ($latlon->lon>0 ? "E" : "W" ) . ")";  
}

function make_address_string_from_latlon($latlon)
{
   $retstr="";
   if(MULTIPLE_COUNTRIES&&strcmp($latlon->country_name,"Unknown Country")==0)
   {
      $retstr=$latlon->name . " " . make_latlon_string_from_latlon($latlon);
      return $retstr;
   }
   $names_equal=(strcmp($latlon->name,$latlon->nearby_location_name)==0);

   if(!empty($latlon->name)&&!$names_equal)
   {
      $retstr=$latlon->name . ",";
   }
   if($latlon->nearby_location_distance!=0)
   {
      $retstr .= $latlon->nearby_location_distance 
	 . "km " . $latlon->nearby_location_direction . " of ";
   }
   $retstr .= $latlon->nearby_location_name;
   if(!empty($latlon->adm1_region_name))
   {
      if(!empty($latlon->nearby_location_name)||!empty($latlon->name))
	 $retstr .= ",";
      $retstr .= $latlon->adm1_region_name;
   }
   if(MULTIPLE_COUNTRIES)
   {
      if(!empty($latlon->country_name))
      {
	 if(!empty($latlon->adm1_region_name)||
	    !empty($latlon->nearby_location_name)||
	    !empty($latlon->name))
	    $retstr .= ",";
	 $retstr .= $latlon->country_name;
      }
   }
   $retstr.= " " . make_latlon_string_from_latlon($latlon);
   return $retstr;
}



function check_trip_compat($t1,$t2,&$c)
{
   $num_trips_matched=0;

   $dist1=getlatlondist($t1->trip_origin_latlon->lat,
			$t1->trip_origin_latlon->lon,
			$t1->trip_destination_latlon->lat,
			$t1->trip_destination_latlon->lon,
			EARTH_RADIUS);
   $dist2=getlatlondist($t2->trip_origin_latlon->lat,
			$t2->trip_origin_latlon->lon,
			$t2->trip_destination_latlon->lat,
			$t2->trip_destination_latlon->lon,
			EARTH_RADIUS);

   $total_dist=$dist1+$dist2;
   $dist_origin_t1_origin_t2=getlatlondist($t1->trip_origin_latlon->lat,
					   $t1->trip_origin_latlon->lon,
					   $t2->trip_origin_latlon->lat,
					   $t2->trip_origin_latlon->lon,
					   EARTH_RADIUS);
   $dist_dest_t2_dest_t1=getlatlondist($t2->trip_destination_latlon->lat,
				       $t2->trip_destination_latlon->lon,
				       $t1->trip_destination_latlon->lat,
				       $t1->trip_destination_latlon->lon,
				       EARTH_RADIUS);
   $accumulated_dist=$dist_origin_t1_origin_t2
      +$dist2+$dist_dest_t2_dest_t1;
   $mileage_saved1=$total_dist-$accumulated_dist;
   // kludges for crazy data
   if($t1->outward->expected_trip_duration_mins==0)
     return; 
   if($dist1==0)
     return;
   else
     $speed1=$dist1/$t1->outward->expected_trip_duration_mins;
   $time_origin_t1_origin_t2_speed1=($dist_origin_t1_origin_t2/$speed1);
   $time_dest_t2_dest_t1_speed1=($dist_dest_t2_dest_t1/$speed1);
   $has_return=($t1->is_return_trip||$t2->is_return_trip);
   if($has_return)
   {
      $dist_origin_t1_dest_t2=getlatlondist($t1->trip_origin_latlon->lat,
					    $t1->trip_origin_latlon->lon,
					    $t2->trip_destination_latlon->lat,
					    $t2->trip_destination_latlon->lon,
					    EARTH_RADIUS);
      $dist_origin_t2_dest_t1=getlatlondist($t2->trip_origin_latlon->lat,
					    $t2->trip_origin_latlon->lon,
					    $t1->trip_destination_latlon->lat,
					    $t1->trip_destination_latlon->lon,
					    EARTH_RADIUS);
      $accumulated_dist2=$dist_origin_t1_dest_t2
	 +$dist2+$dist_origin_t2_dest_t1;
      $mileage_saved2=$total_dist-$accumulated_dist2;
      $time_origin_t1_dest_t2_speed1=($dist_origin_t1_dest_t2/$speed1);
      $time_origin_t2_dest_t1_speed1=($dist_origin_t2_dest_t1/$speed1);
      if($t1->return->expected_trip_duration_mins==0)
	return;
      if($dist1==0)
	return;
      else
	$speed2=$dist1/$t1->return->expected_trip_duration_mins;
      $time_origin_t1_origin_t2_speed2=($dist_origin_t1_origin_t2/$speed2);
      $time_dest_t2_dest_t1_speed2=($dist_dest_t2_dest_t1/$speed2);
      $time_origin_t1_dest_t2_speed2=($dist_origin_t1_dest_t2/$speed2);
      $time_origin_t2_dest_t1_speed2=($dist_origin_t2_dest_t1/$speed2);
   }
   else
      $mileage_saved2=0;
   if($mileage_saved1<=0&&$mileage_saved2<=0)
   {
      return;
   }
   $total_mileage_saved=0;
   for($day_t1=0;$day_t1<7;$day_t1++)
   {
      if($t1->regular_trip)
      {
	 if(!$t1->trip_day[$day_t1])
	    continue;
      }
      else
      {
	 if($t2->regular_trip)
	 {
	    if(date_to_day($t1->undashed_trip_date)!=$day_t1)
	       continue;
	 }
      }
      $mins_t1=($day_t1*24*60);
      for($day_t2=0;$day_t2<15;$day_t2++)
      {
	 if($t2->regular_trip)
	 {
	    if(!$t2->trip_day[$day_t2%7])
	       continue;
	 }
	 else
	 {
	    if($t1->regular_trip)
	    {
	       
	       if(date_to_day($t2->undashed_trip_date)!=($day_t2%7))
		  continue;
	    }
	    else
	    {
	       $day_t1=days_since_start_2006($t1->undashed_trip_date);
	       $mins_t1=($day_t1*24*60);

	    }
	 }
	 $mins_t2=($day_t2*24*60);
	 if($mileage_saved1>0)
	 {
	    
	    $earliest_arrival_at_t2_origin=$t1->outward->earliest_possible_trip_departure_time+
	       $mins_t1+$time_origin_t1_origin_t2_speed1;
	    $earliest_departure_from_t2_origin=
	       max($t2->outward->earliest_possible_trip_departure_time+$mins_t2,
		   $earliest_arrival_at_t2_origin);
	    $earliest_arrival_at_t2_destination=
	       $earliest_departure_from_t2_origin+
	       $t2->outward->expected_trip_duration_mins;
	    $time_leeway1=$t2->outward->latest_possible_trip_arrival_time+$mins_t2-
	       $earliest_arrival_at_t2_destination;
	    $earliest_arrival_at_t1_destination=$earliest_arrival_at_t2_destination
	       +$time_dest_t2_dest_t1_speed1;
	    $time_leeway2=$t1->outward->latest_possible_trip_arrival_time+$mins_t1-
	       $earliest_arrival_at_t1_destination;
	    $time_leeway=min($time_leeway1,$time_leeway2);
	    if($time_leeway>=0)
	    {
	       $num_trips_matched++;
	       $total_mileage_saved+=$mileage_saved1;
	       $t1->driver_outward_passenger_outward=1;
	    }
	 }
	 if($t1->is_return_trip&&$mileage_saved2>0)
	 {
	    $earliest_arrival_at_t2_origin=$t1->return->earliest_possible_trip_departure_time+
	       $mins_t1+$time_origin_t2_dest_t1_speed2;
	    $earliest_departure_from_t2_origin=
	       max($t2->outward->earliest_possible_trip_departure_time+$mins_t2,
		   $earliest_arrival_at_t2_origin);
	    $earliest_arrival_at_t2_destination=
	       $earliest_departure_from_t2_origin+
	       $t2->outward->expected_trip_duration_mins;
	    $time_leeway1=$t2->outward->latest_possible_trip_arrival_time+$mins_t2-
	       $earliest_arrival_at_t2_destination;
	    $earliest_arrival_at_t1_origin=$earliest_arrival_at_t2_destination
	       +$time_origin_t1_dest_t2_speed2;
	    $time_leeway2=$t1->return->latest_possible_trip_arrival_time+$mins_t1-
	       $earliest_arrival_at_t1_origin;
	    $time_leeway=min($time_leeway1,$time_leeway2);
	    if($time_leeway>=0)
	    {
	       $num_trips_matched++;
	       $total_mileage_saved+=$mileage_saved2;
	       $t1->driver_return_passenger_outward=1;
	    }
	 }
	 if($t1->is_return_trip&&$t2->is_return_trip&&$mileage_saved1>0)
	 {
	    $earliest_arrival_at_t2_destination=$t1->return->earliest_possible_trip_departure_time+
	       $mins_t1+$time_dest_t2_dest_t1_speed2;
	    $earliest_departure_from_t2_destination=
	       max($t2->return->earliest_possible_trip_departure_time+$mins_t2,
		   $earliest_arrival_at_t2_destination);
	    $earliest_arrival_at_t2_origin=
	       $earliest_departure_from_t2_destination+
	       $t2->return->expected_trip_duration_mins;
	    $time_leeway1=$t2->return->latest_possible_trip_arrival_time+$mins_t2-
	       $earliest_arrival_at_t2_origin;
	    $earliest_arrival_at_t1_origin=$earliest_arrival_at_t2_origin
	       +$time_origin_t1_origin_t2_speed2;
	    $time_leeway2=$t1->return->latest_possible_trip_arrival_time+$mins_t1-
	       $earliest_arrival_at_t1_origin;
	    $time_leeway=min($time_leeway1,$time_leeway2);
	    if($time_leeway>=0)
	    {
	       $num_trips_matched++;
	       $total_mileage_saved+=$mileage_saved1;
	       $t1->driver_return_passenger_return=1;
	    }
	 }
	 if($t2->is_return_trip&&$mileage_saved2>0)
	 {
	    $earliest_arrival_at_t2_destination=$t1->outward->earliest_possible_trip_departure_time+
	       $mins_t1+$time_origin_t1_dest_t2_speed1;
	    $earliest_departure_from_t2_destination=
	       max($t2->return->earliest_possible_trip_departure_time+$mins_t2,
		   $earliest_arrival_at_t2_destination);
	    
	    $earliest_arrival_at_t2_origin=
	       $earliest_departure_from_t2_destination+
	       $t2->return->expected_trip_duration_mins;
	    $time_leeway1=$t2->return->latest_possible_trip_arrival_time+$mins_t2-
	       $earliest_arrival_at_t2_origin;
	    $earliest_arrival_at_t1_destination=$earliest_arrival_at_t2_origin
	       +$time_origin_t2_dest_t1_speed1;
	    $time_leeway2=$t1->outward->latest_possible_trip_arrival_time+$mins_t1-
	       $earliest_arrival_at_t1_destination;
	    $time_leeway=min($time_leeway1,$time_leeway2);
	    if($time_leeway>=0)
	    {
	       $num_trips_matched++;
	       $total_mileage_saved+=$mileage_saved2;
	       $t1->driver_outward_passenger_return=1;
	    }	    
	 }
      }
   }
   if($total_mileage_saved>$c->total_mileage_saved)
   {
      $c->total_mileage_saved=$total_mileage_saved;
      $c->mileage_saved=$total_mileage_saved/$num_trips_matched;
      $c->num_trips_matched=$num_trips_matched;
   }
}


function get_trip_info($connection,$row,$quick)
{
   $c=new trip();
   $c->trip_id=$row["trip_id"];
   $c->user_id=$row["user_id"];
   $c->is_driver=$row["is_driver"];
   $c->is_passenger=$row["is_passenger"];
   $c->regular_trip=$row["regular_trip"];
   $c->trip_day[0]=$row["trip_day1"];
   $c->trip_day[1]=$row["trip_day2"];
   $c->trip_day[2]=$row["trip_day3"];
   $c->trip_day[3]=$row["trip_day4"];
   $c->trip_day[4]=$row["trip_day5"];
   $c->trip_day[5]=$row["trip_day6"];
   $c->trip_day[6]=$row["trip_day7"];
   $c->trip_date=$row["trip_date"];
   $c->undashed_trip_date=dash_remove_date($row["trip_date"]);
   $c->outward->earliest_possible_trip_departure_time=$row["earliest_possible_trip_departure_time"];
   $c->outward->latest_possible_trip_arrival_time=$row["latest_possible_trip_arrival_time"];
   $c->outward->expected_trip_duration_mins=$row["expected_trip_duration_mins"];
   $c->is_return_trip=$row["is_return_trip"];
   $c->return->earliest_possible_trip_departure_time=$row["return_trip_earliest_possible_trip_departure_time"];
   $c->return->latest_possible_trip_arrival_time=$row["return_trip_latest_possible_trip_arrival_time"];
   $c->return->expected_trip_duration_mins=$row["return_trip_expected_trip_duration_mins"];
   $c->trip_origin_location_id=$row["trip_origin_location_id"];
   $c->trip_destination_location_id=$row["trip_destination_location_id"];
   $c->vehicle_id=$row["vehicle_id"];
   if($quick)
   {
      $c->trip_origin_latlon->lat=$row["trip_origin_latitude"];
      $c->trip_origin_latlon->lon=$row["trip_origin_longitude"];
      $c->trip_destination_latlon->lat=$row["trip_destination_latitude"];
      $c->trip_destination_latlon->lon=$row["trip_destination_longitude"];
   }
   else
   {
      $c->comments=$row["comments"];
      get_user_location_latlon($connection,$c->trip_origin_location_id,
			    $c->trip_origin_latlon);
      get_user_location_latlon($connection,$c->trip_destination_location_id,
			    $c->trip_destination_latlon);
   }
   return $c;
}

function get_trip_info_from_trip_id($trip_id,$connection,$quick=0)
{
   $query = "SELECT * FROM trip WHERE trip_id={$trip_id}";
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   $numrows=mysql_num_rows($result);
   if($numrows!=1)
      die("numrows!=1 it is {$numrows} i.e. non unique get_trip_info_from_trip_id for trip_id={$trip_id}\n");
   $row=mysql_fetch_array($result);
   return get_trip_info($connection,$row,$quick);
}


function get_userinfo_from_user_id($connection,$user_id)
{
   $query = "SELECT * FROM user WHERE user_id=${user_id}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $numrows=mysql_num_rows($result);
   if($numrows!=1)
      die("get_userinfo_from_user_id illegal number of user rows ". $numrows ."\n");
   return mysql_fetch_array($result);
}

function setlatloninfo($template,$latlon,$origindest)
{
   $template->setVariable($origindest . "_LATLON_INFO",
			  xss_encode(make_address_string_from_latlon($latlon)));
}

function get_vehicle_description_str($row)
{
   return $row["colour"] . " " . $row["make"] . " " . $row["model"] . " reg. " .$row["vehicle_registration_number"]; 
}

function set_bg_colour($template,$rowcnt)
{
   $template->setVariable("LIST_STYLE",( $rowcnt&1 ? "list_style_1" : "list_style_2" ));
}

function display_one_match($connection,$template,$t,$c,$rowcnt,$sending_email_state,$show_contact_details,$show_add_to_saved_matches,$comments="")
{
   $email_body="";
   $user_permanent=is_user_permanent($connection);
   if($t->is_driver)
   {
      if($c->is_passenger)
	 check_trip_compat($t,$c,$c);
   }
   if($c->is_driver)
   {
      if($t->is_passenger)
	 check_trip_compat($c,$t,$c);
   }
   $row=get_userinfo_from_user_id($connection,$c->user_id);
   if($template)
   {
      $template->setCurrentBlock("USER_MATCH");
      set_bg_colour($template,$rowcnt);
      $template->setVariable("MATCH_NAME",xss_encode($row["name"]));
   }
   else
   {
      $email_body.="Name: " . $row["name"] . " email: " .
	 $row["email_address"] . "\n";
   }
   if($user_permanent)
   {
      if($show_contact_details)
      {
	 if($template)
	 {
	    if($sending_email_state==0)
	    {
	       $template->setCurrentBlock("HREFFED_EMAIL_ADDRESS");
	       $template->setVariable("TRIP1_ID",$t->trip_id);
	       $template->setVariable("TRIP2_ID",$c->trip_id);
	    }
	    else
	       $template->setCurrentBlock("PLAIN_EMAIL_ADDRESS");
	    $template->setVariable("MATCH_EMAIL_ADDRESS",xss_encode($row["email_address"]));
	    $template->parseCurrentBlock();
	    $user_picture=get_picture_filename("user",$row["user_id"]);
	    if (file_exists($user_picture))
	    {
	       $template->setCurrentBlock("DISPLAY_PIC");
	       $template->setVariable("USER_IMAGE",$sending_email_state==2 ?
				      get_http_header() . $user_picture 
				      : $user_picture);
	       $template->parseCurrentBlock();
	       $template->setCurrentBlock("USER_MATCH");
	    }
	 }
      }
   }
   if($sending_email_state==0&&$template)
   {
      if($user_permanent&&$show_add_to_saved_matches)
      {
	 $template->setCurrentBlock("ADD_TO_SAVED_MATCHES");
	 $template->setVariable("TRIP1_ID",$t->trip_id);
	 $template->setVariable("TRIP2_ID",$c->trip_id);
	 $template->parseCurrentBlock();
      }
      $query="SELECT * FROM saved_tripmatch WHERE other_user_id={$c->user_id}"
	 . " AND satisfaction_done=TRUE"; 
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $has_matches=(mysql_num_rows($result)>0);
      if($has_matches)
      {
	 $template->setCurrentBlock("DISPLAY_USER_FEEDBACK");
	 $template->setVariable("MATCH_USER_ID",$c->user_id);
      }
      else
	 $template->setCurrentBlock("DISPLAY_NO_USER_FEEDBACK"); 
      $template->setVariable("MATCHNAME",xss_encode($row["name"]));
      $template->parseCurrentBlock();
      $template->setCurrentBlock("USER_MATCH");
   }
   $mileage_saved=sprintf("%.1f",$c->mileage_saved);
   if($template)
   {
      $template->setVariable("MILEAGE_SAVED",$mileage_saved);
      $template->setVariable("TRIP1_ID",$t->trip_id);
      $template->setVariable("TRIP2_ID",$c->trip_id);
   }
   else
   {
      $email_body.="Distance per trip saved by carpooling {$mileage_saved}KM.\n";
   }
   if($c->regular_trip&&$t->regular_trip)
   {
      if($template)
      {
      $template->setCurrentBlock("REGULAR_TRIPS_MATCHED");
      $template->setVariable("NUM_REGULAR_TRIPS_MATCHED",$c->num_trips_matched);
      }
      else
	 $email_body.="Number of trips per week matched {$c->num_trips_matched}.\n";
   }
   else
   {
      if($template)
      {
	 $template->setCurrentBlock("ONE_TIME_TRIPS_MATCHED");
	 $template->setVariable("NUM_ONE_TIME_TRIPS_MATCHED",$c->num_trips_matched);
      }
      else
      {
	 $email_body.="Number of trips matched {$c->num_trips_matched}.\n";
      }
   }
   if($template)
   {
      $template->parseCurrentBlock();
      $template->setCurrentBlock("USER_MATCH");
   }
   if($user_permanent&&$show_contact_details)
   {
      if($template)
      {
	 $template->setCurrentBlock("PHONE_DETAILS");
	 $template->setVariable("MATCH_PRIMARY_PHONE_NUMBER",xss_encode($row["primary_phone_number"]));
      }
      else
	 $email_body.="Primary phone number: ${row["primary_phone_number"]}\n"; 
      if(!empty($row["secondary_phone_number"]))
      {
	 if($template)
	 {
	    $template->setCurrentBlock("TRIP_SECONDARY_PHONE_NUMBER");  
	    $template->setVariable("MATCH_SECONDARY_PHONE_NUMBER",xss_encode($row["secondary_phone_number"]));
	    $template->parseCurrentBlock();
	 }
	 else
	    $email_body.="Secondary phone number: ${row["secondary_phone_number"]}\n";
      }
      if($template)
      {
	 $template->parseCurrentBlock();
	 $template->setCurrentBlock("USER_MATCH");
      }
   }
   if($c->is_passenger)
   {
      if($c->is_driver)
      {
	 if($template)
	 {
	    $template->setCurrentBlock("TRIP_DRIVER_AND_PASSENGER");
	    $template->touchBlock("TRIP_DRIVER_AND_PASSENGER");
	 }
	 else
	    $email_body.="I am a driver or passenger.\n";
      }
      else
      {
	 if($template)
	 {
	    $template->setCurrentBlock("TRIP_PASSENGER");
	    $template->touchBlock("TRIP_PASSENGER");
	 }
	 else
	    $email_body.="I am a passenger.\n";

      } 
   }
   else
   {
      if($template)
      {
	 $template->setCurrentBlock("TRIP_DRIVER");
	 $template->touchBlock("TRIP_DRIVER");
      }
      else
      {
	 $email_body.="I am driving.\n";
      }
   }
   if($template)
      $template->parseCurrentBlock();
   if($c->regular_trip)
   {
      if($template)
      {
	 $template->setCurrentBlock("DAYS_TRAVELLING");
	 $template->touchBlock("DAYS_TRAVELLING");
	 $template->parseCurrentBlock();
      }
      else
	 $email_body.="I am travelling on";
      $day_array=array("SUN","MON","TUE","WED","THU","FRI","SAT");
      for($day=0;$day<7;$day++)
      {
	 if($c->trip_day[$day])
	 {
	    if($template)
	    {
	       $template->setCurrentBlock("TRIP_DAY");
	       $template->touchBlock("TRIP_DAY" . $day);
	       $template->parseCurrentBlock();
	    }
	    else
	    {
	       $email_body.=" " .$day_array[$day];
	    }
	 }
      }
      if($template)
      {
	 $template->setCurrentBlock("TRIP_FULL_STOP");
	 $template->touchBlock("TRIP_FULL_STOP");
	 $template->parseCurrentBlock();
      }
      else
      {
	 $email_body.=".\n";
      }
   }
   if($template)
   {
      setlatloninfo($template,$c->trip_origin_latlon,"ORIGIN");
      setlatloninfo($template,$c->trip_destination_latlon,"DESTINATION");

      $template->setVariable("HTTP_HEADER",$sending_email_state==2 ?
			     get_http_header() :"");
   }
   else
   {
      $email_body.="I am travelling from " .
	 make_address_string_from_latlon($c->trip_origin_latlon)
	 . " to " . 
	 make_address_string_from_latlon($c->trip_destination_latlon) .". ";
   }
   if(!$template)
   {
      $email_body.="The earliest it is possible for me to start my trip ";
   }
   if(!$c->regular_trip)
   {
      if($template)
      {
	 $template->setCurrentBlock("TRIP_DATE");
	 $template->setVariable("TRIP_DATE",$c->trip_date);
	 $template->parseCurrentBlock();
      }
      else
      {
	 $email_body.=" on {$c->trip_date}\n";
      }
   }
   if($template)
   {
      $template->setVariable("EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME",
			  mins_to_time($c->outward->earliest_possible_trip_departure_time));
      $template->setVariable("LATEST_POSSIBLE_TRIP_ARRIVAL_TIME",
			  mins_to_time($c->outward->latest_possible_trip_arrival_time));
      $template->setVariable("EXPECTED_TRIP_DURATION",
			  mins_to_duration($c->outward->expected_trip_duration_mins));
   }
   else
   {
      $email_body.="is " 
	 . mins_to_time($c->outward->earliest_possible_trip_departure_time)
	 ." & the latest it is possible for me to arrive at my destination is "
	 . mins_to_time($c->outward->latest_possible_trip_arrival_time) . ". "
	 . " I estimate this trip will take "
	 . mins_to_duration($c->outward->expected_trip_duration_mins). ".\n";
   }
   if($c->is_return_trip)
   {
      if($template)
      {
	 $template->setCurrentBlock("RETURN_TRIP");
	 $template->setVariable("RETURN_EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME",
			     mins_to_time($c->return->earliest_possible_trip_departure_time));
	 $template->setVariable("RETURN_LATEST_POSSIBLE_TRIP_ARRIVAL_TIME",
			     mins_to_time($c->return->latest_possible_trip_arrival_time));
	 $template->setVariable("RETURN_EXPECTED_TRIP_DURATION",
			     mins_to_duration($c->return->expected_trip_duration_mins));
	 $template->parseCurrentBlock();
      }
      else
      {
	 $email_body.="I plan to return at eariest at "
	    . mins_to_time($c->return->earliest_possible_trip_departure_time)
	    . " & arrive back at the trip origin at "
	    . mins_to_time($c->return->latest_possible_trip_arrival_time) . ". "
	    . "I estimate the return trip will take "
	    . mins_to_duration($c->return->expected_trip_duration_mins) . ". \n";
      }
   }
   if($c->is_driver&&$user_permanent&&$show_contact_details)
   {
      $query = "SELECT * FROM vehicle WHERE vehicle_id = {$c->vehicle_id}";
      // Execute the query
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $numrows=mysql_num_rows($result);
      switch($numrows)
      {
	 case 0:
	    break;
	 case 1:
	    if($template)
	    {
	       $whos_driving=($c->is_passenger ? "VEHICLE_MAYBE_DRIVE" :
			   "VEHICLE_WILL_DRIVE");
	       $template->setCurrentBlock($whos_driving);
	       $template->touchBlock($whos_driving);
	       $template->parseCurrentBlock();
	    }
	    else
	    {
	       if($c->is_passenger)
	       {
		  $email_body.="The vehicle I may be driving on this trip is a ";
	       }
	       else
	       {
		  $email_body.="The vehicle I will be driving on this trip is a ";
	       }
	    }
	    $row=mysql_fetch_array($result);
	    if($template)
	    {
	       $template->setCurrentBlock("VEHICLE_DESCRIPTION");
	       $template->setVariable("VEH_DES",xss_encode(get_vehicle_description_str($row)));
	       $template->parseCurrentBlock();
	       $vehicle_picture=get_picture_filename("vehicle",$row["vehicle_id"]);
	       if (file_exists($vehicle_picture))
	       {
		  $template->setCurrentBlock("DISPLAY_VEHICLE_PIC");
		  $template->setVariable("VEHICLE_IMAGE",$sending_email_state==2 ?
					 get_http_header() . $vehicle_picture 
					 : $vehicle_picture);
		  $template->parseCurrentBlock();
	       }
	    }
	    else
	    {
	       $email_body.=get_vehicle_description_str($row) . ".\n";
	    }
	    break;
	 default:
	    die("process_plan_trip, illegal num rows ". $numrows ." for matched vehicles\n");
      }
   }
   if($sending_email_state==2)
   {
      if(!empty($comments))
	 $c->comments=$comments;
   }
   switch($sending_email_state)
   {
      case 0:
      case 2:
	 if(!empty($c->comments))
	 {
	    if($template)
	    {
	       $template->setCurrentBlock("ADDITIONAL_COMMENTS");
	       $template->setVariable("ADDITIONAL_COMMENTS",xss_encode($c->comments));
	       $template->parseCurrentBlock();
	    }
	    else
	    {
	       $email_body.="Additional Comments: \n{$c->comments}\n";
	    }
	 }
	 break;
      case 1:
	 $template->setCurrentBlock("ADDITIONAL_COMMENTS_FORM");
	 $template->setVariable("ADDITIONAL_COMMENTS",xss_encode($c->comments));
	 $template->parseCurrentBlock();
	 break;
   }
   if($template)
   {
      $template->setCurrentBlock("USER_MATCH");
      $template->parseCurrentBlock();
   }
   else
   {
      $row=get_userinfo_from_user_id($connection,$t->user_id);
      $email_body = wordwrap($email_body, 70);
      $connection=db_connect();
      $query="SELECT * FROM saved_tripmatch WHERE user_id={$t->user_id}"
	 . " AND trip_id={$t->trip_id} AND other_trip_id={$c->trip_id}"; 
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $num_tripmatches=mysql_num_rows($result);
      if($num_tripmatches!=1)
	 die($query . " num_tripmatches={$num_tripmatches} expected 1\n");
      $saved_tripmatch_row=mysql_fetch_array($result);
      $email_address=$row["email_address"];
      $password_digest=$row["password_digest"];
      $email_body .= "To see this tripmatch in html follow the link below\n";
      $email_body .=get_http_header() 
	 . "complete_authenticate.php?email_address=${email_address}"
	 . "&auth=${password_digest}&"
	 ."saved_tripmatch_id=${saved_tripmatch_row["tripmatch_id"]}\n";
   }
   return $email_body;
}

function trip_sort_function($a,$b)
{
   return $b->total_mileage_saved-$a->total_mileage_saved;
}

function display_tripmatches_header($connection)
{
   require_once "include/logos.php";
   require_once "HTML/Template/IT.php";

   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("display_trip_matches.tpl",true,true);
   $template->setCurrentBlock("PAGE_PART_ONE");
   $title="Trip Matches";
   $template->setVariable("TITLE",$title);
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str());
   $template->parseCurrentBlock();
   $template->setCurrentBlock("PAGE_PART_TWO");
   $template->setCurrentBlock("REFINE_TRIP_SEARCH_CRITERIA");
   $template->touchBlock("REFINE_TRIP_SEARCH_CRITERIA");
   $template->parseCurrentBlock();
   if(!is_user_permanent($connection))
   {
      $template->setCurrentBlock("NOT_LOGGED_IN");
      $template->touchBlock("NOT_LOGGED_IN");
      $template->parseCurrentBlock();
   }
   $template->setCurrentBlock("PAGE_PART_TWO");
   
   $template->setVariable("TITLE",$title);
   $template->parseCurrentBlock();
   $template->show();
   return $template;
}


function insert_trip($connection,$t,$searchable)
{
   $datetime=get_datetime();
   get_user_location_latlon($connection,
			    $t->trip_origin_location_id,
			    $t->trip_origin_latlon,1);
   get_user_location_latlon($connection,
			    $t->trip_destination_location_id,
			    $t->trip_destination_latlon,1);
   for($i=0;$i<(DEBUG_MANY_DATABASE_ENTRIES ? 2000:1);$i++)
   {
      $comments=mysqlclean_str($t->comments,comments_len,$connection);
      $query = "INSERT INTO trip VALUES (NULL,${_SESSION["user_id"]},"
	 ."'{$datetime}',"
	 ."{$searchable},"
	 . "{$t->is_driver},{$t->is_passenger},{$t->regular_trip},"
	 . "{$t->trip_day[0]},{$t->trip_day[1]},{$t->trip_day[2]},{$t->trip_day[3]},{$t->trip_day[4]},{$t->trip_day[5]},{$t->trip_day[6]},"
	 . "{$t->undashed_trip_date},{$t->outward->earliest_possible_trip_departure_time},"
	 . "{$t->outward->latest_possible_trip_arrival_time},"
	 . "{$t->outward->expected_trip_duration_mins},"

	 . "{$t->is_return_trip},"
	 . "{$t->return->earliest_possible_trip_departure_time},"
	 . "{$t->return->latest_possible_trip_arrival_time},"
	 . "{$t->return->expected_trip_duration_mins},"
	 . "{$t->trip_origin_location_id},"
	 . "{$t->trip_origin_latlon->lat},"
	 . "{$t->trip_origin_latlon->lon},"
	 . "{$t->trip_destination_location_id},"
	 . "{$t->trip_destination_latlon->lat},"
	 . "{$t->trip_destination_latlon->lon},"
	 . "{$t->vehicle_id},'{$comments}')";
      if(!(@mysql_query($query,$connection)))
	 showerror();
   }
   $t->trip_id=mysql_insert_id($connection);
   //print $query . " " . $t->trip_id . "\n";
   //exit;
}

function get_start_of_address($str)
{
   
   $str=trim($str);
   $len=strlen($str);
   $cut_len=min(10,$len);
   $comma=strpos($str,",",$cut_len);
   if($comma==0)
      $comma=$len;
   return substr($str,0,$comma);
}

function change_trip_matchable_state($connection,$trip_id,$matchable)
{
   $query="SELECT * FROM trip WHERE trip_id={$trip_id} "
      . "AND user_id=${_SESSION["user_id"]}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $num_rows=mysql_num_rows($result);
   if($num_rows!=1)
      die("number of matches to make unmatchable!=1 it is " . $num_rows); 
   $row=mysql_fetch_array($result);
   $comments=mysqlclean($row,"comments"
			,comments_len,$connection);
   $trip_date=dash_remove_date($row["trip_date"]);
   $query="REPLACE INTO trip VALUES("
      . "${row["trip_id"]},"
      . "${row["user_id"]},"
      . "'${row["create_time"]}',"
      . "{$matchable},"
      . "${row["is_driver"]},"
      . "${row["is_passenger"]},"
      . "${row["regular_trip"]},"
      . "${row["trip_day1"]},"
      . "${row["trip_day2"]},"
      . "${row["trip_day3"]},"
      . "${row["trip_day4"]},"
      . "${row["trip_day5"]},"
      . "${row["trip_day6"]},"
      . "${row["trip_day7"]},"
      . "{$trip_date},"
      . "${row["earliest_possible_trip_departure_time"]},"
      . "${row["latest_possible_trip_arrival_time"]},"
      . "${row["expected_trip_duration_mins"]},"
      . "${row["is_return_trip"]},"
      . "${row["return_trip_earliest_possible_trip_departure_time"]},"
      . "${row["return_trip_latest_possible_trip_arrival_time"]},"
      . "${row["return_trip_expected_trip_duration_mins"]},"
      . "${row["trip_origin_location_id"]},"
      . "${row["trip_origin_latitude"]},"
      . "${row["trip_origin_longitude"]},"
      . "${row["trip_destination_location_id"]},"
      . "${row["trip_destination_latitude"]},"
      . "${row["trip_destination_longitude"]},"
      . "${row["vehicle_id"]},"
      . "'${comments}')";
   if(!$result = @ mysql_query ($query, $connection))
      showerror();
   return $row;
}

?>