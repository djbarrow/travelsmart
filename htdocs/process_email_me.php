<?php
require_once "HTML/Template/IT.php";
require_once "include/defines.php";
require_once "include/db.php";
require_once "include/authentication.php";
require_once "include/latlonfunc.php";
require_once "include/time.php";
require_once "include/upload_file.php";
require_once "include/tripplan_func.php";
require_once "include/email_func.php";
require_once "include/config.php";
require_once "include/temp_user.php";
require_once "include/logos.php";
require_once "include/display_plan_trip.php";

if($_POST["trip1_id"]&&$_POST["trip2_id"])
{
   $connection=db_connect();
   $trip1_id=mysqlclean($_POST,"trip1_id",id_len,$connection);
   $trip2_id=mysqlclean($_POST,"trip2_id",id_len,$connection);
}
else
   die("process_email_me.php couldn't get trip_id's\n");
$comments=$_POST["comments"];
email_func($connection,$trip1_id,$trip2_id,
	   $email_from,$email_to,$email_reply_to,$email_style,$email_subject,$email_body,$page,2,$comments);

if($email_style==email_style_html)
{
// To send HTML mail, the Content-type header must be set
   $headers  = 'MIME-Version: 1.0' . "\r\n";
//$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
   $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
}
$headers .= 'To: '. $email_to . "\r\n";
$headers .= 'From: ' . $email_from . "\r\n";
$headers .= 'Reply-To: ' .$email_reply_to . "\r\n"; 
// Mail it
if(!DEBUG_DONT_SEND_EMAIL)
   mail($email_to, $email_subject, $email_body, $headers);
display_email_sent_successfully_page($email_to,1);
//print "<pre>\n{$email_body}\n</pre>";
?>
