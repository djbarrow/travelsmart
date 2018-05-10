<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "Date/Calc.php";
require_once "include/latlonfunc.php";
require_once "include/time.php";
require_once "include/upload_file.php";
require_once "include/tripplan_func.php";
require_once "Pager/Pager.php";
require_once "include/logos.php";
require_once "include/validate.php";

if(!isset($refine_old_trip_searches))
   $refine_old_trip_searches=0;
session_authenticate(0);
$connection=db_connect();
if($refine_old_trip_searches)
{
  $query1 = "SELECT * FROM trip WHERE user_id=${_SESSION["user_id"]} AND searchable=1";
}
else
{
   $query1 = "SELECT * FROM trip WHERE user_id=${_SESSION["user_id"]}"
      . " AND searchable=1 AND (regular_trip=1 OR"
      . " (regular_trip=0 AND trip_date>NOW()))";
}
if(!($result=@mysql_query($query1,$connection)))
      showerror();
$num_matchable_trips=mysql_num_rows($result);
if($num_matchable_trips==0)
   url_forward("control_centre.php");
$params = array(
    'perPage' => 10,
    'delta' => 8,             // for 'Jumping'-style a lower number is better
    'append' => true,
    //'separator' => ' | ',
    'clearIfVoid' => false,
    'urlVar' => 'tripmatch',
    'totalItems' => $num_matchable_trips,
    'mode'  => 'Jumping',
);
$pager = & Pager::factory($params);
$links = $pager->getLinks();
list($page_from, $page_to) = $pager->getOffsetByPageId();
$page_from--;
$num_entries=$page_to-$page_from;
if($num_entries==0)
   die("illegal num_entries");
$query2 = $query1
   . " ORDER BY trip_id LIMIT {$page_from},{$num_entries}";
if (!$result = @ mysql_query ($query2, $connection))
   showerror();
$num_rows=mysql_num_rows($result);
if($num_rows!=$num_entries)
{
   die("numrows mismatch for matchable trips in database for userid=" .
       $_SESSION["user_id"] . " got " . $num_rows .
       " expected " .  $num_entries . "\n");
}
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template->setCurrentBlock("PAGE_PART_ONE");
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
if($refine_old_trip_searches)
   $title="Refine Old Trip Searches";
else
   $title="Make Trips Unmatchable";
$template->setVariable("TITLE",$title);
$template->parseCurrentBlock();
$template->setCurrentBlock("PAGE_PART_TWO");
$template->setVariable("TITLE",$title);
$template->parseCurrentBlock();
$template->show();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template2=new HTML_Template_IT("./templates");
$template2->loadTemplatefile("make_trip_unmatchable.tpl",true,true);
for($i=0;$i<$num_entries;$i++)
{
   $trip_row=mysql_fetch_array($result);
   $c=get_trip_info($connection,$trip_row,0);
   $template2->setCurrentBlock("USER_MATCH");
   set_bg_colour($template2,$i);
   $template2->setVariable("CREATE_TIME",$trip_row["create_time"]);
   if($c->regular_trip)
   {
      $template2->setCurrentBlock("DAYS_TRAVELLING");
      $template2->touchBlock("DAYS_TRAVELLING");
      $template2->parseCurrentBlock();
      for($day=0;$day<7;$day++)
      {
	 if($c->trip_day[$day])
	 {
	    $template2->setCurrentBlock("TRIP_DAY");
	    $template2->touchBlock("TRIP_DAY" . $day);
	    $template2->parseCurrentBlock();
	 }
      }
      $template2->setCurrentBlock("TRIP_FULL_STOP");
      $template2->touchBlock("TRIP_FULL_STOP");
      $template2->parseCurrentBlock();
   }
   $template2->setCurrentBlock("TRIP_SOURCE_DEST");
   setlatloninfo($template2,$c->trip_origin_latlon,"ORIGIN");
   setlatloninfo($template2,$c->trip_destination_latlon,"DESTINATION");
   $template2->parseCurrentBlock();
   if(!$c->regular_trip)
   {
       $template2->setCurrentBlock("TRIP_DATE");
       $template2->setVariable("TRIP_DATE",$c->trip_date);
       $template2->parseCurrentBlock();
   }
   $template2->setCurrentBlock("USER_MATCH");
   $template2->setVariable("EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME",
			  mins_to_time($c->outward->earliest_possible_trip_departure_time));
   $template2->setVariable("LATEST_POSSIBLE_TRIP_ARRIVAL_TIME",
			  mins_to_time($c->outward->latest_possible_trip_arrival_time));
   $template2->setCurrentBlock($refine_old_trip_searches ? "REFINE_THIS_TRIP_SEARCH" : "MAKE_TRIP_UNMATCHABLE");
   $template2->setVariable("TRIP_ID",$c->trip_id);
   $template2->parseCurrentBlock();
   $template2->setCurrentBlock("USER_MATCH");
   $template2->parseCurrentBlock();
}
$template2->show();
$template->setCurrentBlock("SET_PAGELINKS");   
$template->setVariable("SET_PAGELINKS",$links['all']);
$template->parseCurrentBlock();
show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
$template->setCurrentBlock("PAGE_FOOTER");
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>