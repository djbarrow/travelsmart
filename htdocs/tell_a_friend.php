<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/validate.php";
require_once "include/display_plan_trip.php";
require_once "include/logos.php";
require_once "include/email_func.php";
require_once "include/progress.php";

session_authenticate(0);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("tell_a_friend.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$validate_error=0;
$from=$_SESSION["login_email_address"]; 
if($_POST)
{
   $to=xss_decode($_POST["to"]);
   if(empty($to))
      show_error_block($template,"NO_EMAIL_ADDRESSES");
   else
   {
      $to_array=explode(",",$to);
      $to_names=array();
      $to_email_addrs=array();
      $cnt=count($to_array);
      for($i=0;$i<$cnt;$i++)
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
	    show_error_block($template,"INVALID_ADDRESS","EMAIL_ADDRESS",$email_address);
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
      $headers="From: " . $from . "\r\n";
      $to_str="";
      $prb=progress_init("Sending email(s) please wait...");
      $curr_percent=$last_percent=0;
      $inc_percent=100/$cnt;
      for($i=0;$i<$cnt;$i++)
      {
	 
	 $to_str.=make_to_str($to_names[$i],$to_email_addrs[$i]) .
	    ($i==$cnt-1 ? "":",");
	 add_to_invitation_list(0,$to_email_addrs[$i]);
	 mail($to_str, $subject, $email_body, $headers);
	 $curr_percent+=$inc_percent;
	 if((int)$curr_percent>$last_percent)
	 {
	    $last_percent=(int)$curr_percent;
	    progress_move_step($prb,$curr_percent);
	 }
      }
      progress_hide($prb);
      display_email_sent_successfully_page_footer($to,0);
   }
}
else
{
   $to="";
   $subject="I've found this really useful carpool website";
   $email_body="Hello there,\n" .
      "It's http://" . $_SERVER["SERVER_NAME"] . "\n" .
      "It can also be used for carpooling, vanpooling, truckpooling\n" .
      "finding designated drivers willing to travel to the pub or disco.\n" .
      "organising travel events like concerts or GAA matches & much more\n\n" .
      "Have a look, talk to you soon\n" .
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