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
require_once "include/temp_user.php";
require_once "Pager/Pager.php";
require_once "include/logos.php";
require_once "include/validate.php";
require_once "include/display_plan_trip.php";

session_authenticate(-1);
$connection=db_connect();
$user_permanent=is_user_permanent($connection);
$query="SELECT * FROM tripmatch1 WHERE user_id=${_SESSION["user_id"]}"; 
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$numrows=mysql_num_rows($result);
if($numrows!=1)
{
      die($numrows . "tripmatch1 in database for userid=" .
	  $_SESSION["user_id"] . "\n");
}

$tripmatch1=mysql_fetch_array($result);
$params = array(
    'perPage' => 10,
    'delta' => 8,             // for 'Jumping'-style a lower number is better
    'append' => true,
    // 'separator' => ' | ',
    'clearIfVoid' => false,
    'urlVar' => 'tripmatch',
    'totalItems' => $tripmatch1["num_trip_matches"],
    'mode'  => 'Jumping',
    'fileName'=>'display_trip_matches.php',
    'fixFileName'=> false,
);
$query = "SELECT * FROM trip WHERE trip_id=${tripmatch1["trip_id"]}"
   . " AND user_id=${_SESSION["user_id"]}";
if(!($result=@mysql_query($query,$connection)))
      showerror();
$numrows=mysql_num_rows($result);
if($numrows!=1)
   die("expected unique trip from SELECT got " . $numrows);
$row=mysql_fetch_array($result);
$t=get_trip_info($connection,$row,1);

$pager = & Pager::factory($params);
$links = $pager->getLinks();
list($page_from, $page_to) = $pager->getOffsetByPageId();
$page_from--;
$num_entries=$page_to-$page_from;
if($num_entries>0)
{
   $query = "SELECT * from tripmatch2 WHERE user_id=${_SESSION["user_id"]}" 
      ." ORDER BY rank DESC LIMIT {$page_from},{$num_entries}";
   if (!$tripmatch2_result = @ mysql_query ($query, $connection))
      showerror();
   $num_rows=mysql_num_rows($tripmatch2_result);
   if($num_rows!=$num_entries)
   {
      die("numrows mismatch for tripmatch2 in database for userid=" .
	  $_SESSION["user_id"] . " got " . $num_rows .
	  " expected " .  $num_entries . "\n");
   }
}
if(!isset($tripmatches_header_displayed))
   $template=display_tripmatches_header($connection);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template2=new HTML_Template_IT("./templates");
$template2->loadTemplatefile("display_one_match.tpl",true,true);
for($i=0;$i<$num_entries;$i++)
{
   $tripmatch2_row=mysql_fetch_array($tripmatch2_result);
   $c=get_trip_info_from_trip_id($tripmatch2_row["other_trip_id"],$connection);
   display_one_match($connection,$template2,$t,$c,$i,0,0,1);
   $template2->parseCurrentBlock();
}
$template2->show();
if($num_entries==0)
{
   $template->setCurrentBlock("TRIP_NO_MATCHES_FOUND");
   if(!$user_permanent)
      show_nested_block($template,"TRIP_NO_MATCHES_USER_TEMPORARY");
   else
      $template->touchBlock("TRIP_NO_MATCHES_FOUND");
   $template->parseCurrentBlock();
}
else
{
   $template->setCurrentBlock("SET_PAGELINKS");
   
   $template->setVariable("SET_PAGELINKS",$links['all']);
   $template->parseCurrentBlock(); 
}
if($user_permanent)
   show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
$template->setCurrentBlock("PAGE_FOOTER");
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
//$template->touchBlock("PAGE_FOOTER");
$template->parseCurrentBlock();
$template->show();
exit;
?>