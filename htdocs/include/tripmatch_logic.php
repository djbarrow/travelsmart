<?php

function tripmatch_logic($connection,$t)
{
   require_once "include/progress.php";
   global $template,$tripmatches_header_displayed;
 
   $template=display_tripmatches_header($connection);
   get_user_location_latlon($connection,$t->trip_origin_location_id,
			    $t->trip_origin_latlon,1);
   get_user_location_latlon($connection,$t->trip_destination_location_id,
			    $t->trip_destination_latlon,1);
   $query = "SELECT * FROM trip WHERE user_id!=${_SESSION["user_id"]}"
      . " AND searchable=1 AND (regular_trip=1 OR"
      . " (regular_trip=0 AND trip_date>NOW()))";
   
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   $num_entries=mysql_num_rows($result);
   $triplist=array();
   
   $last_percent=$curr_percent=$num_trip_matches=0;
   $inc_percent=(100/$num_entries);
   $prb=progress_init("Calculating Tripmatches, Please Be Patient","no trip matches found");
   while($row=mysql_fetch_array($result))
   {
      for($i=0;$i<(DEBUG_FAKE_MANY_MATCHES ? 2000:1);$i++)
      {
	 $c=get_trip_info($connection,$row,1);
	 if($t->is_driver&&$c->is_passenger)
	    check_trip_compat($t,$c,$c);
	 if($c->is_driver&&$t->is_passenger)
	    check_trip_compat($c,$t,$c);  
	 if($c->total_mileage_saved>0)
	 {
	    $triplist[]=$c;
	    $num_trip_matches++;
	 }
      }
      $curr_percent+=$inc_percent;
      if((int)$curr_percent>$last_percent)
      {
	       $last_percent=(int)$curr_percent;
	       progress_move_step($prb,$curr_percent,"{$num_trip_matches} trip matches found");
      }
   }
   usort($triplist,"trip_sort_function");
   $query="DELETE FROM tripmatch1 WHERE user_id=${_SESSION["user_id"]}";
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   $query="DELETE FROM tripmatch2 WHERE user_id=${_SESSION["user_id"]}";
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   $query="INSERT INTO tripmatch1 VALUES (${_SESSION["user_id"]},"
      . "{$num_trip_matches},{$t->trip_id})";
   if(!(@mysql_query($query,$connection)))
      showerror();
   for($i=0;$i<$num_trip_matches;$i++)
   {
      $t=$triplist[$i];
      $query="INSERT INTO tripmatch2 VALUES (${_SESSION["user_id"]},"
	 . "{$t->trip_id},{$t->total_mileage_saved})";
      if(!(@mysql_query($query,$connection)))
	 showerror();
   }
   progress_hide($prb,1);
   $tripmatches_header_displayed=1;
   require_once "display_trip_matches.php";
}

?>