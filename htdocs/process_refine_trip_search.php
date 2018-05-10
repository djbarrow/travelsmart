<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/tripplan_func.php";
require_once "include/upload_file.php";
require_once "include/latlonfunc.php";
require_once "include/time.php";
require_once "include/display_plan_trip.php";
require_once "include/temp_user.php";
require_once "include/validate.php";

session_authenticate(0);
$connection=db_connect();
if(empty($_GET["trip_id"]))
   die("trip_id empty this should not happen\n");
$trip_id=mysqlclean($_GET,"trip_id",id_len,$connection);
$t=get_trip_info_from_trip_id($trip_id,$connection);
if($t->user_id!=$_SESSION["user_id"])
   die("(\$t->user_id({$t->user_id})!=\$_SESSION[\"user_id\"](${_SESSION["user_id"]})");
$_SESSION["make_unsearchable_trip_id"]=$t->trip_id;
display_trip_class_plan_trip($connection,$t);
?>