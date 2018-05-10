<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/latlonfunc.php";
require_once "include/tripplan_func.php";
require_once "Pager/Pager.php";
require_once "include/logos.php";
require_once "include/time.php";
require_once "include/validate.php";
require_once "include/upload_file.php";

session_authenticate(0);

$connection=db_connect();
if(empty($_GET["match_user_id"]))
   die("match_user_id empty this should not happen\n");
$match_user_id=mysqlclean($_GET,"match_user_id",id_len,$connection);
if(empty($_GET["page"]))
   $page=0;
else
   $page=$_GET["page"];
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_user_feedback.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$satisfaction_user=get_userinfo_from_user_id($connection,$match_user_id);
$template->setVariable("SATISFACTIONNAME",xss_encode($satisfaction_user["name"]));
$user_picture=get_picture_filename("user",$satisfaction_user["user_id"]);
if (file_exists($user_picture))
{
   $template->setCurrentBlock("DISPLAY_PIC");
   $template->setVariable("USER_IMAGE",$user_picture);
   $template->parseCurrentBlock();
   $template->setCurrentBlock("__global__");
}
$query="SELECT * FROM saved_tripmatch WHERE other_user_id={$match_user_id}"
   . " AND satisfaction_done=1"; 
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$numrows=mysql_num_rows($result);
$params = array(
    'perPage' => 10,
    'delta' => 8,             // for 'Jumping'-style a lower number is better
    'append' => true,
    // 'separator' => ' | ',
    'clearIfVoid' => false,
    'urlVar' => 'match_user_id={$match_user_id}' . '&' . 'page',
    'totalItems' => $numrows,
    'mode'  => 'Jumping',
);
$pager = & Pager::factory($params);
$links = $pager->getLinks();
list($page_from, $page_to) = $pager->getOffsetByPageId();
$page_from--;
$num_entries=$page_to-$page_from;
if($num_entries>0)
{
   $ip_addr_list1=get_ip_addr_list($connection,$match_user_id);
   $query = "SELECT * from saved_tripmatch WHERE other_user_id={$match_user_id}" 
      . " AND satisfaction_done=TRUE ORDER BY match_time DESC LIMIT {$page_from},{$num_entries}";
   if (!$feedback_result = @ mysql_query ($query, $connection))
       showerror();
   $num_rows=mysql_num_rows($feedback_result);
   if($num_rows!=$num_entries)
   {
      die("numrows mismatch for feedback in database "
	  . " got " . $num_rows .
	  " expected " .  $num_entries . "\n");
   }
   for($i=0;$i<$num_entries;$i++)
   {
      $template->setCurrentBlock("FEEDBACK_FROM_ONE");
      set_bg_colour($template,$i);
      $feedback_row=mysql_fetch_array($feedback_result);
      $feedback_user=get_userinfo_from_user_id($connection,
					       $feedback_row["user_id"]);
      $ip_addr_list2=get_ip_addr_list($connection,$feedback_row["user_id"]);
      $return_ip_addr=check_for_matched_user_ip_addresses($ip_addr_list1,$ip_addr_list2);
      if($return_ip_addr!=null)
      {
	 show_nested_variable_block($template,"POSSIBLE_FORGED_SATISFACTION_ENTRY",
			   "IP_ADDR",$return_ip_addr);
      }
      if($feedback_row["maybe_forged"])
      {
	 show_nested_block($template,"POSSIBLE_FORGED_SATISFACTION_ENTR2");
      }
      $template->setVariable("MATCH_TIME",$feedback_row["match_time"]);
      $template->setVariable("FEEDBACK_NAME",xss_encode($feedback_user["name"]));
      $template->setVariable("SATISFACTION_NAME",xss_encode($satisfaction_user["name"]));
      $t=get_trip_info_from_trip_id($feedback_row["trip_id"],$connection);
      $c=get_trip_info_from_trip_id($feedback_row["other_trip_id"],$connection);
      $regular_trip=$t->regular_trip&&$c->regular_trip;
      if($regular_trip)
      {
	 $template->setCurrentBlock("REGULAR_TRIPS"); 
	 $template->touchBlock("REGULAR_TRIPS");
      }
      else
      {
	 $template->setCurrentBlock("INDIVIDUAL_TRIP");
	 if(!$t->regular_trip)
	    $trip_date=$t->trip_date;
	 else
	    $trip_date=$c->trip_date;
	 $template->setVariable("TRIP_DATE",$trip_date);
      }
      $template->parseCurrentBlock();
      $template->setCurrentBlock("FEEDBACK_FROM_ONE");
      $template->setVariable("ORIGIN_LATLON_NAME",
			     xss_encode($t->trip_origin_latlon->name));
      $template->setVariable("DESTINATION_LATLON_NAME",
			     xss_encode($t->trip_destination_latlon->name));
      $template->setVariable("SATISFACTION",$feedback_row["satisfaction"]);
      $template->setVariable("COMMENTS",
			     empty($feedback_row["satisfaction_comments"]) ?
			     "":xss_encode($feedback_row["satisfaction_comments"]));
      $template->parseCurrentBlock();
   }
   $template->setVariable("SET_PAGELINKS",$links['all']);
   $template->parseCurrentBlock();
}
$template->setCurrentBlock("__global__");
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>