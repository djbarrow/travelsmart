<?php
require_once "HTML/Template/IT.php";
require_once "include/authentication.php";
require_once "include/defines.php";
require_once "include/db.php";
require_once "include/upload_file.php";
require_once "include/logos.php";
require_once "include/validate.php";
require_once "include/config.php";

session_authenticate(0);
unset($_SESSION["saved_add_edit_user"]);
unset($_SESSION["saved_plan_trip"]);
unset($_SESSION["log_query_suffix"]);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("control_centre.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str(1,1));
$user_picture=get_picture_filename("user",$_SESSION["user_id"]);
$template->setCurrentBlock("WELCOME");
$template->setVariable("NAME",$_SESSION["name"]);
$template->parseCurrentBlock();
if (file_exists($user_picture))
{
   $template->setCurrentBlock("DISPLAY_PIC");
   $template->setVariable("USER_IMAGE",$user_picture);
   $template->parseCurrentBlock();
   $template->setCurrentBlock("__global__");
}
if($_SESSION["is_administrator"])
   show_nested_block($template,"USER_OPTIONS");
$connection=db_connect();
$query = "SELECT user_id FROM saved_tripmatch WHERE user_id= ${_SESSION["user_id"]}";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$num_saved_tripmatches=mysql_num_rows($result);
if($num_saved_tripmatches>0)
   show_nested_block($template,"VIEW_SAVED_TRIPMATCHES");
$query = "SELECT address_location_id FROM user WHERE user_id= ${_SESSION["user_id"]}";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$row=mysql_fetch_array($result);
$address_location_id=$row["address_location_id"];
$query = "SELECT * FROM user_location WHERE user_id= ${_SESSION["user_id"]} AND visible=TRUE AND location_id!={$address_location_id}";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$num_locations=mysql_num_rows($result);
if ($num_locations>0)
   show_nested_block($template,"DELETE_LOCATION");
$query = "SELECT vehicle_id FROM vehicle WHERE user_id= ${_SESSION["user_id"]} AND visible=TRUE";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
if (mysql_num_rows($result)>0)
{
   show_nested_block($template,"DELETE_VEHICLE");
}

$query = "SELECT user_id FROM tripmatch1 WHERE user_id= ${_SESSION["user_id"]}";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$num_last_tripsearches=mysql_num_rows($result);
if($num_last_tripsearches==1)
   show_nested_block($template,"VIEW_LAST_TRIPSEARCH");
else if ($num_last_tripsearches!=0)
   die("num_last_tripsearches=" .$num_last_tripsearches . "\n");
$query = "SELECT * FROM trip WHERE user_id=${_SESSION["user_id"]}"
      . " AND searchable=1 AND (regular_trip=1 OR"
      . " (regular_trip=0 AND trip_date>NOW()))";
if(!$result=@mysql_query($query,$connection))
      showerror();
$num_matchable_trips=mysql_num_rows($result);
if($num_matchable_trips>0)
   show_nested_block($template,"REMOVE_MATCHABLE_TRIPS");
$query = "SELECT * FROM trip WHERE user_id=${_SESSION["user_id"]} AND searchable=1";
if(!$result=@mysql_query($query,$connection))
      showerror();
$num_refinable_trips=mysql_num_rows($result);
if($num_refinable_trips>0)
   show_nested_block($template,"REFINE_OLD_TRIP_SEARCHES");
if(INTERNATIONAL_EDITION)
{
   show_nested_block($template,"INTERNAT_EDITION");
}
else
{
   show_nested_block($template,"NATIONAL_EDITION");
}
$template->setCurrentBlock("__global__");
$template->setVariable("DEVELOPER_EMAIL_ADDRESS",$developer_email_address);
$template->setVariable("ADMINISTRATOR_EMAIL_ADDRESS",$administrator_email_address);
if($_SESSION["is_administrator"])
{
   show_nested_block($template,"ADMINISTRATOR_OPTIONS");
   $query = "SELECT user_id FROM user WHERE email_address='{$administrator_email_address}' AND parent_user_id is NULL AND is_permanent=TRUE";
   if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $num_rows=mysql_num_rows($result);
	 // exactly one row? then we have found the user
      if($num_rows==0)
	 show_error_block($template,"UNKNOWN_USER_EMAIL_ADDRESS");
      else if($num_rows!=1)
	 die("database corruption {$num_rows} users matching {$administrator_email_address}\n");
      $row=mysql_fetch_array($result);
      if($row["user_id"]!=$_SESSION["user_id"])
	 show_nested_block($template,"REVERT_TO_USUAL_ADMINISTRATOR");
}
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->show();
?>
