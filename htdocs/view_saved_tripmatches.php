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
require_once "include/temp_user.php";

session_authenticate(0);

$connection=db_connect();
$query="SELECT * FROM saved_tripmatch WHERE user_id=${_SESSION["user_id"]}"; 
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$num_tripmatches=mysql_num_rows($result);
if($num_tripmatches==0)
   die("view saved tripmatches 0 trip_matches\n");
$params = array(
    'perPage' => 10,
    'delta' => 8,             // for 'Jumping'-style a lower number is better
    'append' => true,
    //'separator' => ' | ',
    'clearIfVoid' => false,
    'urlVar' => 'saved_tripmatches',
    'totalItems' => $num_tripmatches,
    'mode'  => 'Jumping',

);
$pager = & Pager::factory($params);
$links = $pager->getLinks();
list($page_from, $page_to) = $pager->getOffsetByPageId();
$page_from--;
$num_entries=$page_to-$page_from;
if($num_entries==0)
   die("illegal num_entries");
$query="SELECT * FROM saved_tripmatch WHERE user_id=${_SESSION["user_id"]}"
      . " ORDER BY match_time DESC LIMIT {$page_from},{$num_entries}";
if (!$saved_tripmatch_result = @ mysql_query ($query, $connection))
   showerror();
$num_rows=mysql_num_rows($saved_tripmatch_result);
if($num_rows!=$num_entries)
{
   die("numrows mismatch for saved_tripmatches in database for userid=" .
       $_SESSION["user_id"] . " got " . $num_rows .
       " expected " .  $num_entries . "\n");
}
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template->setCurrentBlock("PAGE_PART_ONE");
$title="Saved Trip Matches";
$template->setVariable("TITLE",$title);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$template->parseCurrentBlock();
$template->setCurrentBlock("PAGE_PART_TWO");
$template->setVariable("TITLE",$title);
$template->parseCurrentBlock();
$template->show();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
for($i=0;$i<$num_entries;$i++)
{
   $template2=new HTML_Template_IT("./templates");
   $template2->loadTemplatefile("display_one_saved_match.tpl",true,true);
   $template2->setCurrentBlock("ONE_SAVED_MATCH_HEAD");
   set_bg_colour($template2,$i);
   $saved_tripmatch_row=mysql_fetch_array($saved_tripmatch_result);
   $template2->setVariable("MATCH_TIME",$saved_tripmatch_row["match_time"]);
   $match_initiator_userinfo=get_userinfo_from_user_id($connection,$saved_tripmatch_row["match_initiator_id"]);
   $template2->setVariable("MATCH_INITIATOR",xss_encode($match_initiator_userinfo["name"]));
   $my_trip_info=get_trip_info_from_trip_id($saved_tripmatch_row["trip_id"],$connection);
   $other_trip_info=get_trip_info_from_trip_id($saved_tripmatch_row["other_trip_id"],$connection);
   $other_userinfo=get_userinfo_from_user_id($connection,$saved_tripmatch_row["other_user_id"]);
   $template2->setVariable("ORIGIN_LATLON_NAME",
			   xss_encode(get_start_of_address($my_trip_info->trip_origin_latlon->name)));
   $template2->setVariable("DESTINATION_LATLON_NAME",
			   xss_encode(get_start_of_address($my_trip_info->trip_destination_latlon->name)));
   $template2->parseCurrentBlock();
   $template2->show();
   $template3=new HTML_Template_IT("./templates");
   $template3->loadTemplatefile("display_one_match.tpl",true,true);
   display_one_match($connection,$template3,$my_trip_info,$other_trip_info,$i,0,1,0);
   $template3->show();
   $template2=new HTML_Template_IT("./templates");
   $template2->loadTemplatefile("display_one_saved_match.tpl",true,true);
   $template2->setCurrentBlock("ONE_SAVED_MATCH_HEAD_TAIL");
   $template2->setVariable("MATCH_NAME",xss_encode($other_userinfo["name"]));
   $template2->setVariable("TRIPMATCH_ID",$saved_tripmatch_row["tripmatch_id"]);
   $template2->parseCurrentBlock();
   $template2->show();
}

show_nested_variable_block($template,"SET_PAGELINKS","SET_PAGELINKS",$links['all']);
show_nested_block($template,"BACK_TO_CONTROL_CENTRE");

show_nested_variable_block($template,"PAGE_FOOTER","GOOGLE_ANALYTICS",get_google_analytics_str());
$template->show();
?>