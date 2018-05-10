<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/time.php";
require_once "include/tripplan_func.php";
require_once "include/logos.php";
require_once "include/validate.php";

session_authenticate(0);

if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
   die("Cannot connect");
if(!mysql_select_db($databasename,$connection))
   showerror();
if(empty($_POST["tripmatch_id"])||
   !isset($_POST["satisfaction"]))
   die("Fields empty this should not happen\n");
$tripmatch_id=mysqlclean($_POST,"tripmatch_id",id_len,$connection);
$satisfaction=mysqlclean($_POST,"satisfaction",satisfaction_len,$connection);
$comments=(empty($_POST["comments"]) ? "" : mysqlclean($_POST,"comments",satisfaction_comments_len,$connection));
$query="SELECT * FROM saved_tripmatch WHERE tripmatch_id={$tripmatch_id}";
if (!$saved_tripmatch_result = @ mysql_query ($query, $connection))
   showerror();
$num_rows=mysql_num_rows($saved_tripmatch_result);
if($num_rows!=1)
   die("SELECT * from saved_tripmatch num_rows=0");
$row=mysql_fetch_array($saved_tripmatch_result);
$match_time=mysql_cleantime($row["match_time"]);
$finished_this_regular_trip=(empty($_POST["finished_this_regular_trip"]) ? 0:1);
if($row["expire_time"]==null)
   $expire_time=get_datetime();
else
   $expire_time=mysql_cleantime($row["expire_time"]);
$finished_this_regular_trip_str=$finished_this_regular_trip ? $expire_time:"NULL";
$maybe_forged=check_sec_cookie($row["user_id"],$row["other_user_id"]);
$query="REPLACE INTO saved_tripmatch VALUES("
   . "${row["tripmatch_id"]},"
   . "${row["match_initiator_id"]},"
   . "${row["user_id"]},"
   . "${row["other_user_id"]},"
   . "${row["trip_id"]},"
   . "${row["other_trip_id"]},"
   . "{$match_time},"
   . "{$finished_this_regular_trip_str},"
   . "TRUE,"
   . "{$satisfaction},"
   . "'{$comments}',"
   . ($maybe_forged ? "TRUE" : "FALSE")
   . ")";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$other_userinfo=get_userinfo_from_user_id($connection,$row["other_user_id"]);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template->setCurrentBlock("PAGE_PART_ONE");
$template->setVariable("TITLE","Trip Feedback Processed Successfully");
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$template->parseCurrentBlock();
$template->setCurrentBlock("SATISFACTION_PROCESSED_SUCCESSFULLY");
$template->setVariable("NAME",$other_userinfo["name"]);
$template->parseCurrentBlock();
$template->setCurrentBlock("PAGE_FOOTER");
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
//$template->touchBlock("PAGE_FOOTER");
$template->parseCurrentBlock();
$template->show();
?>