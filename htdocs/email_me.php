<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/latlonfunc.php";
require_once "include/time.php";
require_once "include/upload_file.php";
require_once "include/tripplan_func.php";
require_once "include/email_func.php";
require_once "include/temp_user.php";
require_once "include/logos.php";
require_once "include/display_plan_trip.php";

session_authenticate(0);

if($_GET["trip1_id"]&&$_GET["trip2_id"])
{
   $connection=db_connect();
   $trip1_id=mysqlclean($_GET,"trip1_id",id_len,$connection);
   $trip2_id=mysqlclean($_GET,"trip2_id",id_len,$connection);
}
else
   die("email_me.php couldn't get trip_id's\n");
email_func($connection,$trip1_id,$trip2_id,
	   $email_from,$email_to,$email_reply_to,$email_style,$email_subject,$email_body,$page,1);
print $page;
?>