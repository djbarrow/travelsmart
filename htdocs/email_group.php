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
$template->loadTemplatefile("email_group.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$validate_error=0;
$from=$_SESSION["login_email_address"]; 
if($_POST)
{
   
   $to=xss_decode($_POST["to"]);
   $email_to=xss_decode(email_fix_textarea($to));
   if(empty($email_to))
      show_error_block($template,"NO_EMAIL_ADDRESSES");
   else
   {
      $to_array=explode(",",$email_to);
      $to_names=array();
      $to_email_addrs=array();
      for($i=0;$i<count($to_array);$i++)
      {
	 
	 $email_str=$to_array[$i];
	 if(get_name_and_email_addr($email_str,$to_names[$i],
				    $to_email_addrs[$i])==0)
	 {
	    show_error_block($template,"INVALID_EMAIL_STRING","EMAIL_STR",$email_str);
	    break;
	 }
	 $to_email_address=$to_email_addrs[$i];
	 if(!check_email($to_email_address))
	 {
	    show_error_block($template,"INVALID_ADDRESS","EMAIL_ADDRESS",$to_email_address);
	    break;
	 }
      }
   }
   $subject=xss_decode($_POST["subject"]);
   if(empty($subject))
      show_error_block($template,"NO_SUBJECT");
   $email_body=xss_decode($_POST["email_body"]);
   if(empty($email_body))
      show_error_block($template,"NO_BODY");
   if(!$validate_error)
   {
      display_email_sent_successfully_page_header();
      $cnt=count($to_array);
      $prb=progress_init("Sending email(s) please wait...");
      $curr_percent=$last_percent=0;
      $inc_percent=100/$cnt;
      $headers="From: " . $from . "\r\n";
      for($i=0;$i<$cnt;$i++)
      {
	 $curr_body=$email_body;
	 $curr_body=str_replace("GREETING",make_greeting($to_names[$i]),
				$curr_body);	 
	 $to_str=make_to_str($to_names[$i],$to_email_addrs[$i]);
	 add_to_invitation_list(0,$to_email_addrs[$i]);
	 mail($to_str, $subject, $curr_body, $headers);
	 $curr_percent+=$inc_percent;
	 if((int)$curr_percent>$last_percent)
	 {
	    $last_percent=(int)$curr_percent;
	    progress_move_step($prb,$curr_percent);
	 }
      }
      progress_hide($prb);
      display_email_sent_successfully_page_footer("all group",0);
   }
}
else
{
   $to="";
   $subject="travelsmart -";
   $email_body="GREETING,\n" .
      "Sincerely,\n" .
       $_SESSION["name"];
}

if(!$_POST||$validate_error)
{
   $template->setVariable("FROM",xss_encode($from));
   $template->setVariable("TO",xss_encode($to));
   $template->setVariable("SUBJECT",xss_encode($subject));
   $template->setVariable("EMAIL_BODY",xss_encode($email_body));
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>