<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/validate.php";
require_once "include/select.php";

require_once "include/logos.php";
require_once "include/server_stats.php";

session_authenticate(1);
$connection=db_connect();
if($_GET)
{
   if(isset($_GET["search_period"]))
      $search_period=$_GET["search_period"];
   else
      die("search_period not set");
}
else
   $search_period=0;
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("view_server_stats.tpl",true,true);
$template->setCurrentBlock("PAGE_HEADER");
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$search_period_array=array(array());
$search_period_array=array(
      array(0,"Since Travelsmart First Went On-line"),
      array(1,"In Last Day"),
      array(3,"In Last 3 Days"),
      array(7,"In Last 7 Days"),
      array(14,"In Last 14 Days"),
      array(30,"In Last 30 Days"),
      array(90,"In Last 90 Days"),
      array(365,"In Last 365 Days"),
   );
$end_year=Date_Calc::dateNow('%Y');
$query = "select create_time from user order by create_time";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
if(mysql_num_rows($result)>1)
{
   $row=mysql_fetch_array($result);
   $start_year=substr($row["create_time"],0,4);
}
else
   $start_year=$end_year;
for($curr_year=$start_year,$idx=8;$curr_year<=$end_year;$curr_year++,$idx++)
   $search_period_array[$idx]=array($curr_year,"In {$curr_year} calendar year");
$search_period_select=output_select("search_period",$search_period_array,$search_period);
$template->setVariable("SEARCH_PERIOD_SELECT",$search_period_select);
if($_GET)
{
   $template->parseCurrentBlock();
   $template->show();
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("view_server_stats.tpl",true,true);
   $stats=get_server_stats($connection,1,$search_period);
   $template->setCurrentBlock("DISPLAY_STATS");
   $template->setVariable("NUMBER_OF_USERS_REGISTERED",$stats->number_of_users_registered);
   $template->setVariable("NUMBER_OF_MATCHED_TRIPS",$stats->number_of_matched_trips);
   $template->setVariable("TOTAL_MILEAGE_SAVED",$stats->total_mileage_saved);
   $template->setVariable("LITRES_OF_FUEL_SAVED",$stats->litres_of_fuel_saved);
   $template->setVariable("CARBON_FOOTPRINT_SAVED",$stats->carbon_footprint_saved);
   $template->setVariable("NUMBER_OF_TRIPS",$stats->number_of_trips);
   $template->setVariable("NUMBER_OF_REGULAR_TRIPS",$stats->number_of_regular_trips);
   $template->setVariable("NUMBER_OF_ONE_TIME_TRIPS",$stats->number_of_one_time_trips);
   $template->setVariable("NUMBER_OF_MATCHED_REGULAR_TRIPS",$stats->number_of_matched_regular_trips);
   $template->setVariable("NUMBER_OF_ONE_TIME_TRIPS_MATCHED",$stats->number_of_one_time_trips_matched);
   $template->setVariable("NUMBER_OF_ONE_TIME_TRIPS_COMPLETED",$stats->number_of_one_time_trips_completed);
   $template->setVariable("NUMBER_OF_SEARCHABLE_REGULAR_TRIPS",$stats->number_of_searchable_regular_trips);
  
   $template->setVariable("NUMBER_OF_UNEXPIRED_ONE_TIME_TRIPS",
			  $stats->number_of_unexpired_one_time_trips);

   $template->setVariable("NUMBER_OF_UNEXPIRED_SEARCHABLE_ONE_TIME_TRIPS",
			  $stats->number_of_unexpired_searchable_one_time_trips);
   $query = "SELECT * FROM vehicle_type";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   while($row=mysql_fetch_array($result))
   {
      $template->setCurrentBlock("VEHICLE_STATS");
      $template->setVariable("VEHICLE_TYPE",$row["type_name"]);
      $vehicle_stats=$stats->vehicle_stats[$row["type_id"]];
      $template->setVariable("VEH_NUM_ONE_TIME_TRIPS",
			     $vehicle_stats->num_one_time_trips);
      $template->setVariable("VEH_NUM_REGULAR_TRIPS",
			     $vehicle_stats->num_regular_trips);
      $template->setVariable("VEH_TOTAL_MILEAGE_SAVED",
			     sprintf("%.1f",$vehicle_stats->total_mileage_saved));
      $template->setVariable("VEH_LITRES_OF_FUEL_SAVED",
			     sprintf("%.1f",$vehicle_stats->litres_of_fuel_saved));
      $template->setVariable("VEH_CARBON_FOOTPRINT_SAVED",
			     sprintf("%.1f",$vehicle_stats->carbon_footprint_saved));	
      $template->parseCurrentBlock();
   }
   $template->setCurrentBlock("DISPLAY_STATS");
   $template->parseCurrentBlock();
   
}
show_nested_variable_block($template,"PAGE_FOOTER","GOOGLE_ANALYTICS",get_google_analytics_str());
$template->setCurrentBlock("__global__");
$template->parseCurrentBlock();
$template->show();
?>