<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/select.php";
require_once "HTML/Template/IT.php";
require_once "Date/Calc.php";
require_once "include/latlonfunc.php";
require_once "include/time.php";
require_once "include/tripplan_func.php";
require_once "include/config.php";
require_once "include/display_plan_trip.php";
require_once "include/validate.php";
require_once "include/temp_user.php";

session_authenticate(-1); 
$connection=db_connect();
$query="SELECT trip_id FROM tripmatch1 WHERE user_id=${_SESSION["user_id"]}";
if(!($result=@mysql_query($query,$connection)))
   showerror();
$numrows=mysql_num_rows($result);
if($numrows!=1)
   die("numrows!=1 i.e. non unique it is {$numrows} saved trip in refine_plan_trip\n");
$row=mysql_fetch_array($result);
$t=get_trip_info_from_trip_id($row["trip_id"],$connection);
if(is_user_permanent($connection))
   $_SESSION["make_unsearchable_trip_id"]=$t->trip_id;
else
   $_SESSION["delete_trip_id"]=$t->trip_id;
display_trip_class_plan_trip($connection,$t);
?>