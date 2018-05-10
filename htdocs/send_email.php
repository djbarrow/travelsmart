<?php
require_once "HTML/Template/IT.php";
require_once "include/defines.php";
require_once "include/validate.php";
require_once "include/display_plan_trip.php";
require_once "include/db.php";
require_once "include/temp_user.php";
require_once "include/email_func.php";
require_once "include/logos.php";
require_once "include/progress.php";

$validate_error=0;
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("send_email.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
if($_GET)
{
   if(empty($_GET["email_subject"]))
      die("email subject empty this should not happen\n");
   $email_subject=xss_decode($_GET["email_subject"]);
   if(empty($_GET["email_to"]))
      die("email to empty this should not happen\n");
   $email_to=xss_decode($_GET["email_to"]);
   if(!empty($_SESSION["login_email_address"]))
      $email_address=$_SESSION["login_email_address"];
   else
      $email_address=""; 
   $email_body="";
} 
else if($_POST)
{
   $email_subject=xss_decode($_POST["email_subject"]);
   if(empty($email_subject))
      show_error_block($template,"NO_SUBJECT");
   if(empty($_POST["email_to"]))
      die("email_to empty\n");
   $email_to=xss_decode($_POST["email_to"]);
   if(!empty($_SESSION["login_email_address"]))
      $email_address=$_SESSION["login_email_address"];
   else
   {
      $email_address=xss_decode($_POST["email_address"]);
      if(empty($email_address))
      {  
	 show_error_block($template,"EMPTY_EMAIL_ADDRESS");
      }
      else if(!check_email($email_address))
      {
	 show_error_block($template,"INVALID_EMAIL_ADDRESS");
      }
   }
   $email_body=xss_decode($_POST["email_body"]);
   if(empty($email_body))
      show_error_block($template,"NO_BODY");
   if(!$validate_error)
   {
      display_email_sent_successfully_page_header();
      $prb=progress_init("Sending email please wait...");
      mail($email_to,
	   $email_subject,
	   $email_body,
	   'From: ' . $email_address . "\r\n");
      progress_move_step($prb,100);
      progress_hide($prb);
      display_email_sent_successfully_page_footer($email_to,empty($_SESSION["login_email_address"]) ? 1:0);
   }
}
if(!$_POST||$validate_error)
{
  
   $template->setVariable("TITLE",xss_encode($email_subject));
   if(empty($_SESSION["login_email_address"]))
      $template->setCurrentBlock("ENTER_EMAIL_ADDRESS");
   else
      $template->setCurrentBlock("USER_EMAIL_ADDRESS");
   $template->setVariable("EMAIL_FROM",xss_encode($email_address));
   $template->parseCurrentBlock();
   $template->setCurrentBlock("__global__");
   $template->setVariable("EMAIL_TO",xss_encode($email_to));
   $template->setVariable("EMAIL_SUBJECT",xss_encode($email_subject));
   $template->setVariable("EMAIL_BODY",xss_encode($email_body));
   if(is_user_permanent(0,0,1))
      show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
   else
      show_nested_block($template,"CLOSE_WINDOW_LINK");
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>
