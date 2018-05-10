<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/authentication.php";
require_once "include/defines.php";
require_once "include/upload_file.php";
require_once "include/validate.php";
require_once "include/tripplan_func.php";
require_once "HTML/Template/IT.php";
require_once "include/logos.php";
require_once "include/validate.php";
require_once "include/progress.php";

if($_GET)
{
   $email_address=$_GET["email_address"];
   if(empty($email_address))
      die("no email address\n");
   if(isset($_GET["trip_id"]))
      $trip_id=$_GET["trip_id"];
   if(isset($_GET["forgot_password"]))
      $forgot_password=$_GET["forgot_password"];
   if(isset($_GET["merge_user_id"]))
      $merge_user_id=$_GET["merge_user_id"];
   $connection=db_connect();
   $query = "SELECT * FROM user WHERE email_address = '{$email_address}' " .
      "AND parent_user_id is NULL";
   // Execute the query
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $numrows=mysql_num_rows($result);
   if($numrows!=1)
      die("send_authenticate illegal number of user rows " . $numrows .
	  "for email_address" . $email_address . "\n");
   $row=mysql_fetch_array($result);
   $user_id=$row["user_id"];
   $password_digest=$row["password_digest"];
   if(!empty($trip_id))
   {
      $query = "SELECT * FROM trip WHERE user_id = {$user_id} AND trip_id = {$trip_id}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $numrows=mysql_num_rows($result);
      if($numrows!=1)
	 die("illegal number trips num_rows!=1 it is ${numrows}\n");
   }
   $email_subject="travelsmart - authentication email";
   $forgot_password_str=(empty($forgot_password) ? "" : "&forgot_password=1");
   $authentication_url=get_http_header() 
      . "complete_authenticate.php?email_address=${email_address}"
      . "&auth=${password_digest}" 
      . (empty($trip_id) ? "": "&trip_id=${trip_id}" )
      . $forgot_password_str
      . (empty($merge_user_id) ? "" : "&merge_user_id=${merge_user_id}");

   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("send_authenticate1.tpl",true,true);
   $template->setVariable("CSS_FILE",get_css_name(0));
   $template->setVariable("LOGOS",get_logos_str(0));
   $template->setVariable("LINK",$authentication_url ."&email_style=" . email_style_html);
   show_nested_block($template,(empty($forgot_password) 
				? "COMPLETE_REGISTRATION":
				"FORGOT_PASSWORD"));
   $email_body=$template->get();
   // To send HTML mail, the Content-type header must be set
// Additional headers
   $headers .= 'To: '. $email_address . "\r\n";
   $headers .= 'From: ' . $administrator_email_address . "\r\n";
//   $headers .= 'Return-Path: ' . $administrator_email_address . "\r\n";
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("send_authenticate.tpl",true,true);
   $template->setCurrentBlock("PAGE_HEADER");
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str());
   $template->parseCurrentBlock();
   $template->show();
   $prb=progress_init("Sending emails please wait...");
   $headers_html  = $headers . 'MIME-Version: 1.0' . "\r\n";
   $headers_html .= 'Content-type: text/html; charset=utf-8' . "\r\n";
   mail($email_address,$email_subject . " - html",$email_body,$headers_html);
   progress_move_step($prb,50);
   $email_body="Please click the following link\r\n" .
      $authentication_url ."&email_style=" . email_style_text . "\r\n"
      . (empty($forgot_password) 
	 ? "to reset your users password.\r\n":
	 "to complete registration with travelsmart.\r\n");
   mail($email_address,$email_subject . " - text",$email_body,$headers);
   progress_move_step($prb,100);
   progress_hide($prb);
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("send_authenticate.tpl",true,true);
   $template->setCurrentBlock("PAGE_FOOTER");
   show_nested_variable_block($template,"AUTHENTICATION_SENT",
			      "EMAIL_ADDRESS",$email_address);
   $template->setVariable("TRIPMATCH_EMAIL_ADDRESS",$tripmatch_email_address);
   $template->setVariable("ADMINISTRATOR_EMAIL_ADDRESS",$administrator_email_address);
   $template->setVariable("DEVELOPER_EMAIL_ADDRESS",$tripmatch_email_address);
   $template->setVariable("ENQUIRIES_EMAIL_ADDRESS",$administrator_email_address);
   show_nested_block($template,
		     empty($forgot_password) ? "COMPLETE_REGISTRATION" :
		     "FORGOT_PASSWORD");
   if(SHOW_SPAM_PRECAUTIONS)
   {
      show_nested_block($template,"SPAM_PRECAUTIONS");
   }
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
else
   die("No $_GET arguments\n");
?>
