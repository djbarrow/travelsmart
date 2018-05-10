<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/latlonfunc.php";
require_once "include/time.php";
require_once "include/tripplan_func.php";
require_once "include/email_func.php";
require_once "include/temp_user.php";
require_once "include/upload_file.php";
require_once "include/logos.php";
require_once "include/display_plan_trip.php";

session_authenticate(0);

if($_GET["trip1_id"]&&$_GET["trip2_id"])
{
   $connection=db_connect();
   $trip1_id=mysqlclean($_GET,"trip1_id",id_len,$connection);
   $trip2_id=mysqlclean($_GET,"trip2_id",id_len,$connection);
}
else
   die("add_to_saved_matches.php couldn't get trip_id's\n");
$trip1=get_trip_info_from_trip_id($trip1_id,$connection);
$trip2=get_trip_info_from_trip_id($trip2_id,$connection);
add_saved_match($connection,$trip1,$trip2);
add_saved_match($connection,$trip2,$trip1);
$other_userinfo=get_userinfo_from_user_id($connection,$trip2->user_id);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template2=new HTML_Template_IT("./templates");
$template2->loadTemplatefile("display_one_match.tpl",true,true);
$template->setCurrentBlock("PAGE_PART_ONE");
$template->setVariable("TITLE","Match Added To Saved Matches Successfully");
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$template->parseCurrentBlock();
$template->setCurrentBlock("ADDED_TO_SAVED_MATCHES_SUCCESSFULLY");
$template->setVariable("NAME",$other_userinfo["name"]);
$template->parseCurrentBlock();
$template->show();
$t=get_trip_info_from_trip_id($trip1_id,$connection);
$c=get_trip_info_from_trip_id($trip2_id,$connection);
display_one_match($connection,$template2,$t,$c,0,0,1,0);
$template2->parseCurrentBlock();
$template2->show();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template->setCurrentBlock("CLOSE_WINDOW_LINK");
$template->touchBlock("CLOSE_WINDOW_LINK");
$template->parseCurrentBlock();
$template->setCurrentBlock("PAGE_FOOTER");
//$template->touchBlock("PAGE_FOOTER");
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>