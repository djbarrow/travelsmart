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
$template->loadTemplatefile("ban_unban_user.tpl",true,true);
$banned_by_login_authentication=
   $banned_by_cookie=
   $banned_by_ip_address=0;
$email_address="";
$user_id=0;
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
      $query = "SELECT user_id FROM user WHERE email_address='{$email_address}' AND parent_user_id is NULL AND is_permanent=TRUE";
      // Execute the query
      //print "<pre>{$query}</pre>";
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
	 if(!empty($_POST["get_ban_user_options"]))
	 {
	   
	    $query="SELECT * FROM ban_user WHERE user_id={$user_id}";
	    //print "<pre>{$query}</pre>";
	    if (!$result = @ mysql_query ($query, $connection))
	       showerror();
	    $num_rows=mysql_num_rows($result);
	    switch($num_rows)
	    {
	       case 0:
		  break;
	       case 1:
		  $row=mysql_fetch_array($result);
		  $banned_by_login_authentication=$row["banned_by_login_authentication"];
		  $banned_by_cookie=$row["banned_by_cookie"];
		  $banned_by_ip_address=$row["banned_by_ip_address"];
		  break;
	       default:
		  die("query {$query} failed num_rows={num_rows}\n");
	    }
	 }
	 else if(!empty($_POST["set_ban_user_options"]))
	 {
	    $banned_by_login_authentication=(empty($_POST["banned_by_login_authentication"]) ? 0:1);
	    $banned_by_cookie=(empty($_POST["banned_by_cookie"]) ? 0:1);
	    $banned_by_ip_address=(empty($_POST["banned_by_ip_address"]) ? 0:1);
	    if($banned_by_login_authentication||$banned_by_cookie||$banned_by_ip_address)
	    {
	       $query="REPLACE INTO ban_user VALUES("
		  . "{$user_id},"
		  . "{$banned_by_login_authentication}," 
		  . "{$banned_by_cookie},"
		  . "{$banned_by_ip_address})";
	       //print "<pre>{$query}</pre>";
	       
	    }
	    else
	       $query="DELETE FROM ban_user WHERE user_id={$user_id}";
	    if (!$result = @ mysql_query ($query, $connection))
		  showerror();
	 }
	 else
	 {
	    die("Neither submit button was pressed");
	 }
      }
   }
}
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$user_picture=get_picture_filename("user",$user_id);
if (file_exists($user_picture))
{
   $template->setCurrentBlock("DISPLAY_PIC");
   $template->setVariable("USER_IMAGE",$user_picture);
   $template->parseCurrentBlock();
   $template->setCurrentBlock("__global__");
}
$template->setVariable("EMAIL_ADDRESS",$email_address);
set_check($template,"banned_by_login_authentication",1,$banned_by_login_authentication);
set_check($template,"banned_by_cookie",1,$banned_by_cookie);
set_check($template,"banned_by_ip_address",1,$banned_by_ip_address);
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>