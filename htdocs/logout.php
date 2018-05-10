<?php
require_once "HTML/Template/IT.php";
require_once "include/config.php";
require_once "include/defines.php";
require_once "include/logos.php";
require_once "include/db.php";
require_once "include/temp_user.php";

session_start();
$template = new HTML_Template_IT("./templates");
$template->loadTemplatefile("logout.tpl", true, true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
if (isset($_SESSION["name"])&&is_user_permanent(0,0,1))
{
   $template->setCurrentBlock("THANKS");
   $template->setVariable("NAME",$_SESSION["name"]);
   $template->parseCurrentBlock();
   $template->setCurrentBlock("__global__");
}
if (isset($_SESSION["error_message"]))
{
   $template->setCurrentBlock("ERR_MSG");
   $template->setVariable("ERROR_MESSAGE",$_SESSION["error_message"]);
   $template->parseCurrentBlock();
   $template->setCurrentBlock("__global__");   
}
// Destroy the session.
session_destroy();
$template->setVariable("ADMINISTRATOR_EMAIL_ADDRESS",$administrator_email_address);
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>
