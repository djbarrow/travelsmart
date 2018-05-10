<?php

function is_user_permanent($connection,$user_id=0,$return_false_if_no_user=0,$return_false_if_not_authenticated=1)
{
   $connection=db_connect_if_neccessary($connection);
   if($user_id==0)
   {
      if(isset($_SESSION["user_id"]))
	 $user_id=$_SESSION["user_id"];
   }
   if(empty($user_id))
   {
      if($return_false_if_no_user)
	 return 0;
      else
	 return 1;
   }
   $query = "SELECT * FROM user WHERE user_id=${user_id}";
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   $numrows=mysql_num_rows($result);
    if($numrows!=1)
       die("is_user_permanent num_rows!=1 it is ${numrows}\n");
   $row=mysql_fetch_array($result);
   if($return_false_if_not_authenticated&&$row["email_address_is_authenticated"]==0)
      return 0;
   return $row["is_permanent"];
}


function create_temporary_user($connection)
{
  //  print "<pre> create_temporary_user</pre>"
   $connection=db_connect_if_neccessary($connection);
   $datetime=get_datetime();
   
   $query = "INSERT INTO user VALUES (NULL,NULL,FALSE,"
      . ( EMAIL_AUTHENTICATION ? "TRUE" : "FALSE")
      . ",{$datetime},'temp',FALSE,'','','','',0,". default_email_style .")";
   if(!(@mysql_query($query,$connection)))
      showerror();
   $user_id=mysql_insert_id($connection);
   init_session();
   $_SESSION["user_id"]=$user_id;
   $_SESSION["name"]="temp";
   $_SESSION["login_email_address"]="";
   $_SESSION["login_IP"]=$_SERVER["REMOTE_ADDR"];
}

function has_trip_to_merge($connection,$merge_from_user_id)
{
   global $db_hostname,$db_username,$db_password,$databasename;

   if($connection==0)
   {
      if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
	 die("Cannot connect");
      if(!mysql_select_db($databasename,$connection))
	 showerror();
   }
   $query = "SELECT * FROM trip WHERE user_id = {$merge_from_user_id}";
    if (!$result = @ mysql_query ($query, $connection))
       showerror();
    $numrows=mysql_num_rows($result);

    if($numrows>1&&!DEBUG_MANY_DATABASE_ENTRIES)
       die("has_trip_to_merge num_rows>1 it is ${numrows}\n");
    return ($numrows>=1 ? 1:0);
}

function merge_user_database_info($connection,$merge_from_user_id,$merge_to_user_id)
{
   require_once "include/time.php";

   $query = "SELECT * FROM user_location WHERE user_id = {$merge_from_user_id}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   while($row=mysql_fetch_array($result))
   {
      $name=mysqlclean($row,"name",location_name_len,$connection);
      $query="REPLACE INTO user_location VALUES("
	 . "'${row["location_id"]}',"
	 . "{$merge_to_user_id},"
	 . "'${row["nearby_location_ufi"]}',"
	 . "'${name}',"
	 . "'${row["latitude"]}',"
	 . "'${row["longitude"]}',"
	 . "'${row["visible"]}'"
	 .")";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
   }
   $query = "SELECT * FROM vehicle WHERE user_id = {$merge_from_user_id}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   while($row=mysql_fetch_array($result))
   {
      $make=mysqlclean($row,"make",make_len,$connection);
      $model=mysqlclean($row,"model",model_len,$connection);
      $colour=mysqlclean($row,"colour",colour_len,$connection);
      $vehicle_registration_number=mysqlclean($row,"vehicle_registration_number",vehicle_registration_number_len,$connection);
      $query="REPLACE INTO vehicle VALUES("
	 . "'${row["vehicle_id"]}',"
	 . "{$merge_to_user_id},"
	 . "'${make}',"
	 . "'${model}',"
	 . "'${colour}',"
	 . "'${vehicle_registration_number}',"
	 . "${row["vehicle_type"]},"
	 . "'${row["visible"]}'"
	 .")";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
   }
   $query = "SELECT * FROM trip WHERE user_id = {$merge_from_user_id}";
    if (!$result = @ mysql_query ($query, $connection))
       showerror();
    $count=0;
    while($row=mysql_fetch_array($result))
    {
       $count++;
       $return_trip_id=$row["trip_id"];
       $comments=mysqlclean($row,"comments"
			      ,comments_len,$connection);
       $trip_date=dash_remove_date($row["trip_date"]);
       $query="REPLACE INTO trip VALUES("
	  . "'${row["trip_id"]}',"
	  . "{$merge_to_user_id},"
	  . "'${row["create_time"]}',"
	  . "'1',"
	  . "'${row["is_driver"]}',"
	  . "'${row["is_passenger"]}',"
	  . "'${row["regular_trip"]}',"
	  . "'${row["trip_day1"]}',"
	  . "'${row["trip_day2"]}',"
	  . "'${row["trip_day3"]}',"
	  . "'${row["trip_day4"]}',"
	  . "'${row["trip_day5"]}',"
	  . "'${row["trip_day6"]}',"
	  . "'${row["trip_day7"]}',"
	  . "{$trip_date},"
	  . "'${row["earliest_possible_trip_departure_time"]}',"
	  . "'${row["latest_possible_trip_arrival_time"]}',"
	  . "'${row["expected_trip_duration_mins"]}',"
	  . "'${row["is_return_trip"]}',"
	  . "'${row["return_trip_earliest_possible_trip_departure_time"]}',"
	  . "'${row["return_trip_latest_possible_trip_arrival_time"]}',"
	  . "'${row["return_trip_expected_trip_duration_mins"]}',"
	  . "'${row["trip_origin_location_id"]}',"
	  . "'${row["trip_origin_latitude"]}',"
	  . "'${row["trip_origin_longitude"]}',"
	  . "'${row["trip_destination_location_id"]}',"
	  . "'${row["trip_destination_latitude"]}',"
	  . "'${row["trip_destination_longitude"]}',"
	  . "'${row["vehicle_id"]}',"
	  . "'${comments}'"
	  .")";
       if (!$result = @ mysql_query ($query, $connection))
	  showerror();
    }
    if($count!=1)
       die("merge_user_database_info {$count} trips only supposed to be 1\n");
    $query = "DELETE FROM tripmatch1 WHERE user_id = {$merge_from_user_id}";
    if (!$result = @ mysql_query ($query, $connection))
       showerror();
    $query = "DELETE FROM tripmatch2 WHERE user_id = {$merge_from_user_id}";
    if (!$result = @ mysql_query ($query, $connection))
       showerror();
    $query = "DELETE FROM user WHERE user_id = {$merge_from_user_id}";
    if (!$result = @ mysql_query ($query, $connection))
       showerror();
   return $return_trip_id;
}

function garbage_collect_temp_users($connection)
{
   // Delete obselete temporary users
   $connection=db_connect_if_neccessary($connection);
   $query = "SELECT user_id FROM user WHERE is_permanent=FALSE AND create_time<(NOW()-INTERVAL 7 DAY)";
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   while($row=mysql_fetch_array($result))
   {
      $user_id=$row["user_id"];
      $query = "DELETE FROM user WHERE user_id = {$user_id}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $query = "DELETE FROM user_location WHERE user_id = {$user_id}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
       $query = "DELETE FROM vehicle WHERE user_id = {$user_id}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $query = "DELETE FROM trip WHERE user_id = {$user_id}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $query = "DELETE FROM tripmatch1 WHERE user_id = {$user_id}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $query = "DELETE FROM tripmatch2 WHERE user_id = {$user_id}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
   }
}
?>