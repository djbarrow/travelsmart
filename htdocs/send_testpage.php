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
require_once "include/progress.php";

$validate_error=0;
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("send_testpage.tpl",true,true);
if ($_POST)
{

   $email_address=xss_decode($_POST["email_address"]);
   if(empty($email_address))
      show_error_block($template,"EMPTY_EMAIL_ADDRESS");
   else if(!check_email($email_address))
      show_error_block($template,"INVALID_EMAIL_ADDRESS");
   if(!$validate_error)
   {
         $template=new HTML_Template_IT("./templates");
	 $template->loadTemplatefile("send_authenticate.tpl",true,true);
	 $template->setCurrentBlock("PAGE_HEADER");
	 $template->setVariable("CSS_FILE",get_css_name());
	 $template->setVariable("LOGOS",get_logos_str());
	 $template->parseCurrentBlock();
	 $template->show();
	 $prb=progress_init("Sending emails please wait...");
	 $email_subject="travelsmart - test email";
	 $headers = 'To: '. $email_address . "\r\n";
	 $headers .= 'From: ' . $tripmatch_email_address . "\r\n";
	 $headers .= 'Reply-To: ' . $tripmatch_email_address . "\r\n";
	 $headers_html  = $headers . 'MIME-Version: 1.0' . "\r\n";
	 $headers_html .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	 $template2=new HTML_Template_IT("./templates");
	 $template2->loadTemplatefile("test_email.html",true,true);
	 $template2->touchBlock("__global__");
	 $template2->parseCurrentBlock();
	 $email_body=$template2->get();
	 mail($email_address,$email_subject . " - html",$email_body,$headers_html);
	 progress_move_step($prb,50);
	 $template2=new HTML_Template_IT("./templates");
	 $template2->loadTemplatefile("test_email.txt",true,true);
	 $template2->touchBlock("__global__");
	 $template2->parseCurrentBlock();
	 $email_body=$template2->get();
	 mail($email_address,$email_subject . " - text",$email_body,$headers);
	 progress_move_step($prb,100);
	 progress_hide($prb);
	 $template=new HTML_Template_IT("./templates");
	 $template->loadTemplatefile("send_authenticate.tpl",true,true);
	 $template->setCurrentBlock("PAGE_FOOTER");
	 show_nested_variable_block($template,"TEST_TRIPMATCH_EMAILS_SENT",
	 			    "EMAIL_ADDRESS",$email_address);
	 $template->setVariable("TRIPMATCH_EMAIL_ADDRESS",$tripmatch_email_address);
	 $template->setVariable("ADMINISTRATOR_EMAIL_ADDRESS",$administrator_email_address);
	 $template->setVariable("DEVELOPER_EMAIL_ADDRESS",$tripmatch_email_address);
	 $template->setVariable("ENQUIRIES_EMAIL_ADDRESS",$administrator_email_address);
	 if(SHOW_SPAM_PRECAUTIONS)
	 {
	    show_nested_block($template,"SPAM_PRECAUTIONS");
	 }
	 $template->parseCurrentBlock();
	 $template->show();
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
   $template->setVariable("EMAIL_ADDRESS",xss_encode($email_address));
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>