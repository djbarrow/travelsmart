<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/validate.php";
require_once "include/authentication.php";
require_once "include/logos.php";
require_once "include/temp_user.php";
require_once "include/display_plan_trip.php";

session_authenticate(0); 
$validate_error=0;
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("add_new_vehicle.tpl",true,true);
$connection=db_connect();
if ($_POST)
{
   require_once "include/defines.php";
   require_once "include/upload_file.php";
   
   $make=xss_decode($_POST["make"]);
   $mysql_make=mysqlclean_str($make,make_len,$connection);
   if(empty($make))
      show_error_block($template,"EMPTY_VEHICLE_MAKE");
   $model=xss_decode($_POST["model"]);
   $mysql_model=mysqlclean_str($model,model_len,$connection);
   if(empty($model))
      show_error_block($template,"EMPTY_VEHICLE_MODEL");
   $colour=xss_decode($_POST["colour"]);
   $mysql_colour=mysqlclean_str($colour,colour_len,$connection);
   if(empty($colour))
      show_error_block($template,"EMPTY_VEHICLE_COLOUR");
   $vehicle_registration_number=xss_decode($_POST["vehicle_registration_number"]);
   $mysql_vehicle_registration_number=mysqlclean_str($vehicle_registration_number,vehicle_registration_number_len,$connection);
   if(empty($vehicle_registration_number))
      show_error_block($template,"EMPTY_VEHICLE_REGISTRATION_NUMBER");
   $query = "SELECT * FROM vehicle WHERE user_id = ${_SESSION["user_id"]} AND vehicle_registration_number='${mysql_vehicle_registration_number}' AND visible=TRUE";
   if (!$result=@mysql_query($query, $connection))
	    showerror();
   if (mysql_num_rows($result)>= 1)
      show_error_block($template,"VEHICLE_ALREADY_EXISTS");
   $vehicle_type_id=mysqlclean($_POST,"vehicle_type_id",id_len,$connection);
   $query = "SELECT type_id FROM vehicle_type WHERE type_id = {$vehicle_type_id}";
   if (!$result=@mysql_query($query, $connection))
	    showerror();
   $num_rows=mysql_num_rows($result);
   if($num_rows!=1)
      die("illegal vehicle_type_id={$vehicle_type_id} num_rows={$num_rows}");
   if(!$validate_error)
   {
      $query = "INSERT INTO vehicle VALUES (NULL,'${_SESSION["user_id"]}','${mysql_make}','${mysql_model}','${mysql_colour}','${mysql_vehicle_registration_number}',${vehicle_type_id},TRUE)";
      if(!(@mysql_query($query,$connection)))
	 showerror();
      $vehicle_id=mysql_insert_id($connection);
      resize_upload_jpeg("vehicle",$vehicle_id,150,150);
      if(empty($_SESSION["saved_plan_trip"]))
	 url_forward("control_centre.php");
      else
      {
	 
	 require_once "include/display_plan_trip.php";
      
	 $saved_plan_trip=unserialize($_SESSION["saved_plan_trip"]);
	 $saved_plan_trip["vehicle_id"]=$vehicle_id;
	 display_saved_plan_trip($connection,$saved_plan_trip);
      }
   }
}
else
{
   $make=$model=$colour=$vehicle_registration_number="";
   $vehicle_type_id=default_vehicle_type;
}
if(!$_POST||$validate_error)
{
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str());
   $query = "SELECT * FROM vehicle_type";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   while($row=mysql_fetch_array($result))
   {
      $template->setCurrentBlock("VEHICLE_TYPE");
      $template->setVariable("VEHICLE_TYPE_ID",$row["type_id"]);
      set_selected($template,"VEHICLE_TYPE_ID_SELECTED",
		   $row["type_id"],$vehicle_type_id);
      $template->setVariable("VEHICLE_TYPE_NAME",xss_encode($row["type_name"]));
      $template->parseCurrentBlock();
   }
   $template->setVariable("MAKE",xss_encode($make));
   $template->setVariable("MODEL",xss_encode($model));
   $template->setVariable("COLOUR",xss_encode($colour));
   $template->setVariable("VEHICLE_REGISTRATION_NUMBER",xss_encode($vehicle_registration_number));
   if(is_user_permanent($connection))
      show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}