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
$template->loadTemplatefile("delete_vehicle.tpl",true,true);
$validate_error=0;
if($_GET)
{
   $vehicle_id=mysqlclean($_GET,"vehicle_id",id_len,$connection);
   if(empty($vehicle_id))
      show_error_block($template,"VEHICLE_NOT_SELECTED");
   $query = "SELECT * FROM vehicle WHERE user_id= ${_SESSION["user_id"]} AND visible=TRUE AND vehicle_id = {$vehicle_id}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   if(!$validate_error)
   {
      $num_rows=mysql_num_rows($result);
      if($num_rows!=1)
	 die("num_rows!=1 it is ${numrows} for vehicles matching vehicle_id\n");
      $row=mysql_fetch_array($result);
      $make=mysqlclean($row,"make",make_len,$connection);
      $model=mysqlclean($row,"model",model_len,$connection);
      $colour=mysqlclean($row,"colour",colour_len,$connection);
      $vehicle_registration_number=mysqlclean($row,"vehicle_registration_number",vehicle_registration_number_len,$connection);
      $query="REPLACE INTO vehicle VALUES("
	 . "'${row["vehicle_id"]}',"
	 . "'${row["user_id"]}',"
	 . "'${make}',"
	 . "'${model}',"
	 . "'${colour}',"
	 . "'${vehicle_registration_number}',"
	 . "${row["vehicle_type"]},"
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
   $query = "SELECT * FROM vehicle WHERE user_id= ${_SESSION["user_id"]} AND visible=TRUE";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $template->setCurrentBlock("VEHICLE");
   $template->setVariable("VEHICLE_ID",0);
   $template->setVariable("VEH_DES","-------------------------------------------------");
   $template->parseCurrentBlock();
   while($row=mysql_fetch_array($result))
   {
      $template->setCurrentBlock("VEHICLE");
      $template->setVariable("VEHICLE_ID",$row["vehicle_id"]);
      $template->setVariable("VEH_DES",xss_encode(get_vehicle_description_str($row)));
      $template->parseCurrentBlock();
   }
   $template->setCurrentBlock("__global__");
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>