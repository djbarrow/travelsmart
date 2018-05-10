<?php
require_once "HTML/Template/IT.php";
require_once "include/logos.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/display_plan_trip.php";
require_once "include/upload_file.php";
require_once "include/db.php";
require_once "include/validate.php";

session_authenticate(1);
$validate_error=0;
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("change_to_user.tpl",true,true);
$email_address="";
$user_id=0;
$drop_administrator_privileges=
   $change_user_id_only=0;
if ($_POST)
{
   require_once "include/defines.php";
   require_once "include/upload_file.php";
   
   if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
      die("Cannot connect");
   if(!mysql_select_db($databasename,$connection))
      showerror();
   $email_address=mysqlclean($_POST,"email_address",email_address_len,$connection);
   if(empty($email_address))
   {  
      show_error_block($template,"EMPTY_EMAIL_ADDRESS");
   }
   else if(!check_email($email_address))
   {
      show_error_block($template,"INVALID_EMAIL_ADDRESS");
   }
   if(!$validate_error)
   {
      $query = "SELECT * FROM user WHERE email_address='{$email_address}' AND parent_user_id is NULL AND is_permanent=TRUE";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      if(!$validate_error)
      {
	 $num_rows=mysql_num_rows($result);
	 // exactly one row? then we have found the user
	 if($num_rows==0)
	    show_error_block($template,"UNKNOWN_USER_EMAIL_ADDRESS");
	 else if($num_rows!=1)
	    die("database corruption {$num_rows} users matching {$email_address}\n");
	 $row=mysql_fetch_array($result);
	 $user_id=$row["user_id"];
	 $drop_administrator_privileges=(empty($_POST["drop_administrator_privileges"]) ? 0:1);
	 $change_user_id_only=(empty($_POST["change_user_id_only"]) ? 0:1);
	 if($change_user_id_only)
	 {
	     $_SESSION["user_id"]=$user_id;
	      add_log_entry($connection,"administrator changing user_id to {$user_id}");
	 }
	 else
	 {
	    print_r($row);
	    do_base_login_stuff($connection,
			$row["user_id"],
			$row["name"],
			$row["email_address"],
			"administrator changing to {$user_id} ${row["email_address"]}");
	 }
	 $_SESSION["is_administrator"]=($drop_administrator_privileges ? 0:1);
	 url_forward("control_centre.php");
      }
   }
}
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$template->setVariable("EMAIL_ADDRESS",$email_address);
set_check($template,"drop_administrator_privileges",1,$drop_administrator_privileges);
set_check($template,"change_user_id_only",1,$change_user_id_only);
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>