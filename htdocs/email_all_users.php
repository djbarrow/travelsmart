<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/validate.php";
require_once "include/display_plan_trip.php";
require_once "include/logos.php";
require_once "include/email_func.php";
require_once "include/config.php";
require_once "include/progress.php";

session_authenticate(1);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("email_all_users.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$validate_error=0;
$from=$_SESSION["login_email_address"]; 
if($_POST)
{
   $subject=xss_decode($_POST["subject"]);
   if(empty($subject))
      show_error_block($template,"NO_SUBJECT");
   $email_body=xss_decode($_POST["email_body"]);
   if(empty($email_body))
      show_error_block($template,"NO_BODY");
   if(!$validate_error)
   {
      display_email_sent_successfully_page_header();
      $headers="From: " . $from . "\r\n";
      $connection=db_connect();
      $query = "SELECT * FROM user WHERE parent_user_id is NULL AND is_permanent=TRUE";
      if (!$result = @ mysql_query ($query, $connection))
	       showerror();
      $num_users=mysql_num_rows($result);
      $prb=progress_init("Sending email(s) please wait...");
      $curr_percent=$last_percent=0;
      $inc_percent=100/$num_users;
      while($row=mysql_fetch_array($result))
      {
	 $curr_body=$email_body;
	 $curr_body=str_replace("USER_NAME",$row["name"],$curr_body);
	 $to_str=make_to_str($row["name"],$row["email_address"]);
	 mail($to_str, $subject, $curr_body, $headers);
	 $curr_percent+=$inc_percent;
	 if((int)$curr_percent>$last_percent)
	 {
	    $last_percent=(int)$curr_percent;
	    progress_move_step($prb,$curr_percent);
	 }
      }
      progress_hide($prb);
      display_email_sent_successfully_page_footer("all users",0);
   }
}
else
{
   $to="";
   $subject="travelsmart - notification from administrator";
   $email_body="Dear USER_NAME,\n" .
      "Sincerely,\n" .
       $_SESSION["name"];
}

if(!$_POST||$validate_error)
{
   $template->setVariable("FROM",xss_encode($from));
   $template->setVariable("SUBJECT",xss_encode($subject));
   $template->setVariable("EMAIL_BODY",xss_encode($email_body));
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>