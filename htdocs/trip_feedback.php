<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/tripplan_func.php";
require_once "include/upload_file.php";
require_once "include/latlonfunc.php";
require_once "include/validate.php";
require_once "include/logos.php";
require_once "include/time.php";
require_once "include/select.php";
require_once "include/display_plan_trip.php";

session_authenticate(0);

if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
   die("Cannot connect");
if(!mysql_select_db($databasename,$connection))
   showerror();
if(empty($_GET["tripmatch_id"]))
   die("tripmatch_id empty this should not happen\n");
$tripmatch_id=mysqlclean($_GET,"tripmatch_id",id_len,$connection);
$query="SELECT * FROM saved_tripmatch WHERE tripmatch_id={$tripmatch_id}";
if (!$saved_tripmatch_result = @ mysql_query ($query, $connection))
   showerror();
$saved_tripmatch_row=mysql_fetch_array($saved_tripmatch_result);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("trip_feedback.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$other_userinfo=get_userinfo_from_user_id($connection,$saved_tripmatch_row["other_user_id"]);
$template->setVariable("NAME",xss_encode($other_userinfo["name"]));
$my_trip_info=get_trip_info_from_trip_id($saved_tripmatch_row["trip_id"],$connection);
$other_trip_info=get_trip_info_from_trip_id($saved_tripmatch_row["other_trip_id"],$connection);
$template->setVariable("ORIGIN_LATLON_NAME",
		       xss_encode(get_start_of_address($my_trip_info->trip_origin_latlon->name)));
$template->setVariable("DESTINATION_LATLON_NAME",
		       xss_encode(get_start_of_address($my_trip_info->trip_destination_latlon->name)));
$template->setVariable("MATCH_TIME",$saved_tripmatch_row["match_time"]);
if($saved_tripmatch_row["satisfaction_done"])
{
   $satisfaction=$saved_tripmatch_row["satisfaction"];
   $comments=$saved_tripmatch_row["satisfaction_comments"];
}
else
{
   $satisfaction=0;
   $comments="";
}
$template->setVariable("COMMENTS",xss_encode($comments));
$satisfaction_str=output_integer_select("satisfaction",0,10,$satisfaction);
$template->setVariable("SATISFACTION_SELECT",$satisfaction_str);

$user_picture=get_picture_filename("user",$other_userinfo["user_id"]);
if (file_exists($user_picture))
   show_nested_variable_block($template,"DISPLAY_PIC","USER_IMAGE",$user_picture);
if($my_trip_info->regular_trip&&$other_trip_info->regular_trip)
{
   $template->setCurrentBlock("FINISHED_THIS_REGULAR_TRIP_CHECKBOX");
   set_check($template,"finished_this_regular_trip",1,$saved_tripmatch_row["expire_time"]!=null ? 1:0);
   $template->parseCurrentBlock();
   $template->setCurrentBlock("__global__");
}
$template->setVariable("TRIPMATCH_ID",$tripmatch_id);
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>