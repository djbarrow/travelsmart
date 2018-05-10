<?php
require_once "HTML/Template/IT.php";
require_once "include/defines.php";
require_once "include/logos.php";
require_once "include/db.php";
require_once "include/validate.php";
require_once "include/temp_user.php";

$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("developer_info.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
 $template->setVariable("DJ_BARROW_EMAIL_ADDRESS",$dj_barrow_email_address);
if(is_user_permanent(0,0,1))
   show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
 else
    show_nested_block($template,"BACK_TO_LOGIN_PAGE");
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>
