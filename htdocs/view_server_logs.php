<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "Date/Calc.php";
require_once "include/latlonfunc.php";
require_once "include/time.php";
require_once "include/upload_file.php";
require_once "include/tripplan_func.php";
require_once "include/validate.php";
require_once "Pager/Pager.php";
require_once "include/logos.php";


session_authenticate(1);
$connection=db_connect();
$query1="SELECT * FROM log " . $_SESSION["log_query_suffix"]; 
if (!$result = @ mysql_query ($query1, $connection))
   showerror();
$num_logs=mysql_num_rows($result);
$params = array(
    'perPage' => 25,
    'delta' => 8,             // for 'Jumping'-style a lower number is better
    'append' => true,
    //'separator' => ' | ',
    'clearIfVoid' => false,
    'urlVar' => 'logs',
    'totalItems' => $num_logs,
    'mode'  => 'Jumping',

);
$pager = & Pager::factory($params);
$links = $pager->getLinks();
list($page_from, $page_to) = $pager->getOffsetByPageId();
$page_from--;
$num_entries=$page_to-$page_from;
if($num_entries==0)
   die("illegal num_entries");
$query=$query1 . " ORDER BY log_time DESC LIMIT {$page_from},{$num_entries}";
if (!$log_result = @ mysql_query ($query, $connection))
   showerror();
$num_rows=mysql_num_rows($log_result);
if($num_rows!=$num_entries)
{
   die("numrows mismatch for logs in database got " 
       . $num_rows . " expected " .  $num_entries . "\n");
}
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template->setCurrentBlock("PAGE_PART_ONE");
$title="Log Entries";
$template->setVariable("TITLE",$title);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$template->parseCurrentBlock();
$template->setCurrentBlock("PAGE_PART_TWO");
$template->setVariable("TITLE",$title);
$template->parseCurrentBlock();
$template->show();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template2=new HTML_Template_IT("./templates");
$template2->loadTemplatefile("display_one_log_entry.tpl",true,true);
for($i=0;$i<$num_entries;$i++)
{
   $template2->setCurrentBlock("ONE_LOG_ENTRY");
   set_bg_colour($template2,$i);
   $log_row=mysql_fetch_array($log_result);
   if($log_row["user_id"]!=-1)
   {
      $query="SELECT * FROM user WHERE user_id={$log_row["user_id"]}";
      if (!$user_result = @ mysql_query ($query, $connection))
	 showerror();
      $num_user_rows=mysql_num_rows($user_result);
      if($num_user_rows!=1)
	 die("view_server_logs expected num_user_rows=1 got {$num_user_rows}");
      $user_row=mysql_fetch_array($user_result);
   }
   else
   {
      $user_row=array("name"=>"no user logged in","email_address"=>"not_applicable");
   }
   $template2->setVariable("USER_NAME",$user_row["name"]);
   $template2->setVariable("EMAIL_ADDRESS",$user_row["email_address"]);
   $template2->setVariable("USER_ID",$log_row["user_id"]);
   $template2->setVariable("LOG_TIME",$log_row["log_time"]);
   $template2->setVariable("IP_ADDR",$log_row["ip_addr"]);
   $template2->setVariable("LOG_ENTRY",$log_row["log_entry"]);
   $template2->parseCurrentBlock();
}
$template2->show();
if($num_entries==0)
   show_nested_block($template,"NO_LOG_ENTRIES");
else
   show_nested_variable_block($template,"SET_PAGELINKS",
			      "SET_PAGELINKS",$links['all']);
show_nested_block($template,"BACK_TO_SQL_QUERY");
show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
$template->setCurrentBlock("PAGE_FOOTER");
//$template->touchBlock("PAGE_FOOTER");
$template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
$template->parseCurrentBlock();
$template->show();
?>