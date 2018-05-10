<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/validate.php";
require_once "include/tripplan_func.php";
require_once "include/logos.php";

session_authenticate(0);
$connection=db_connect();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("delete_location.tpl",true,true);
$validate_error=0;
if($_GET)
{
   $location_id=mysqlclean($_GET,"location_id",id_len,$connection);
   if(empty($location_id))
      show_error_block($template,"LOCATION_NOT_SELECTED");
   $query = "SELECT * FROM user_location WHERE user_id= ${_SESSION["user_id"]} AND visible=TRUE AND location_id = {$location_id}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   if(!$validate_error)
   {
      $num_rows=mysql_num_rows($result);
      if($num_rows!=1)
	 die("num_rows!=1 it is ${numrows} for locations matching location_id\n");
      $row=mysql_fetch_array($result);
      $name=mysqlclean($row,"name",location_name_len,$connection);
      $query="REPLACE INTO user_location VALUES("
	 . "'${row["location_id"]}',"
	 . "'${row["user_id"]}',"
	 . "'${row["nearby_location_ufi"]}',"
	 . "'${name}',"
	 . "'${row["latitude"]}',"
	 . "'${row["longitude"]}',"
	 . "FALSE"
	 .")";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      url_forward("control_centre.php");
   }
}
if(!$_GET||$validate_error)
{
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str());
   $query = "SELECT address_location_id FROM user WHERE user_id= ${_SESSION["user_id"]}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $row=mysql_fetch_array($result);
   $address_location_id=$row["address_location_id"];
   $query = "SELECT * FROM user_location WHERE user_id= ${_SESSION["user_id"]} AND visible=TRUE AND location_id!={$address_location_id}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $template->setCurrentBlock("LOCATION");
   $template->setVariable("LOCATION_ID",0);
   $template->setVariable("LOCATION_NAME","---------------------------------------");
   $template->parseCurrentBlock();
   while($row=mysql_fetch_array($result))
   {
      $template->setCurrentBlock("LOCATION");
      $template->setVariable("LOCATION_ID",$row["location_id"]);
      $template->setVariable("LOCATION_NAME",xss_encode(get_start_of_address($row["name"])));
      $template->parseCurrentBlock();
   }
   $template->setCurrentBlock("__global__");
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>