<?php
require_once "HTML/Template/IT.php";
require_once "include/config.php";
require_once "include/logos.php";
require_once "include/validate.php";
require_once "include/defines.php";
require_once "include/db.php";
require_once "include/authentication.php";
require_once "include/temp_user.php";
require_once "include/tripplan_func.php";

$validate_error=0;
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("forgot_password.tpl",true,true);
if ($_POST)
{

   $connection=db_connect();
   $email_address=mysqlclean($_POST,"email_address",email_address_len,$connection);
   if(empty($email_address))
      show_error_block($template,"EMPTY_EMAIL_ADDRESS");
   else if(!check_email($email_address))
      show_error_block($template,"INVuiALID_EMAIL_ADDRESS");
   $query = "SELECT user_id FROM user WHERE email_address = '{$email_address}' AND parent_user_id is NULL";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $num_rows=mysql_num_rows($result);
   switch($num_rows)
   {
      case 0:
	 show_error_block($template,"EMAIL_ADDRESS_NOT_KNOWN");
	 break;
      case 1:
	 break;
      default:
	 die("corrupt database ${num_rows} users with ${email_address} email address\n");
   }
   
   if(!$validate_error)
   {
      $row=mysql_fetch_array($result);
      $user_permanent=is_user_permanent($connection);
      $trip_to_merge=($user_permanent ? false : has_trip_to_merge($connection,$_SESSION["user_id"]));
      $trip_to_merge_str=($trip_to_merge ? "&merge_user_id=${_SESSION["user_id"]}":"");
      url_forward("send_authenticate.php?email_address=${email_address}&forgot_password=1" 
      . $trip_to_merge_str);
   }
}
else
{
   $email_address="";
}
if(!$_POST||$validate_error)
{
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str(1,1));
   $template->setVariable("EMAIL_ADDRESS",$email_address);
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>