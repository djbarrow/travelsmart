<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/validate.php";
require_once "include/logos.php";
require_once "include/config.php";

session_authenticate(1);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("init_view_server_logs.tpl",true,true);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$validate_error=0;
if($_POST)
{
   $connection=db_connect();
   $query_suffix=$_POST["query_suffix"];
   $query="SELECT * FROM log " . $query_suffix . " ORDER BY log_time DESC LIMIT 0,1 ";
   if (!$result = @ mysql_query ($query, $connection))
      show_error_block($template,"INVALID_SQL_QUERY","QUERY_SUFFIX",$query_suffix);
   if(!$validate_error)
   {
      $_SESSION["log_query_suffix"]=$query_suffix;
      url_forward("view_server_logs.php");
   }
}
else
{
   $query_suffix=(isset($_SESSION["log_query_suffix"]) ? 
		  $_SESSION["log_query_suffix"] : "");
}
if(!$_POST||$validate_error)
{
   $template->setVariable("QUERY_SUFFIX",$query_suffix);
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>