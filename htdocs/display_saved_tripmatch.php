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
require_once "include/logos.php";
require_once "include/validate.php";
require_once "include/temp_user.php";

session_authenticate(0);
$connection=db_connect();
if(isset($_GET["saved_tripmatch_id"]))
   $saved_tripmatch_id=mysqlclean($_GET,"saved_tripmatch_id",id_len,$connection);
else
   die("saved_tripmatch_id is not set\n");

$query="SELECT * FROM saved_tripmatch WHERE user_id=${_SESSION["user_id"]}"
   ." AND tripmatch_id={$saved_tripmatch_id}";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$num_tripmatches=mysql_num_rows($result);
if($num_tripmatches!=1)
   die("num_tripmatches={$num_tripmatches} expected 1\n");
$saved_tripmatch_row=mysql_fetch_array($result);
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
$template->setCurrentBlock("PAGE_PART_ONE");
$title="Trip Match";
$template->setVariable("TITLE",$title);
$template->setVariable("CSS_FILE",get_css_name());
$template->setVariable("LOGOS",get_logos_str());
$template->parseCurrentBlock();
$template->setCurrentBlock("PAGE_PART_TWO");
$template->setVariable("TITLE",$title);
$template->parseCurrentBlock();
$template->show();
$template2=new HTML_Template_IT("./templates");
$template2->loadTemplatefile("display_one_match.tpl",true,true);
$my_trip_info=get_trip_info_from_trip_id($saved_tripmatch_row["trip_id"],$connection);
$other_trip_info=get_trip_info_from_trip_id($saved_tripmatch_row["other_trip_id"],$connection);
display_one_match($connection,$template2,$my_trip_info,$other_trip_info,0,0,1,0);
$template2->show();
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("display_trip_matches.tpl",true,true);
show_nested_block($template,"GO_TO_CONTROL_CENTRE");
show_nested_variable_block($template,"PAGE_FOOTER","GOOGLE_ANALYTICS",get_google_analytics_str());
$template->show();
?>