<?php
require_once "HTML/Template/IT.php";
require_once "include/logos.php";
require_once "include/defines.php";

$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("legal.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$template->setVariable("ENQUIRIES_EMAIL_ADDRESS",$enquiries_email_address);
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>