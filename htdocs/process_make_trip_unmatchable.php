<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/tripplan_func.php";
require_once "include/upload_file.php";
require_once "include/time.php";

session_authenticate(0);
$connection=db_connect();
if(empty($_GET["trip_id"]))
   die("trip_id empty this should not happen\n");
$trip_id=mysqlclean($_GET,"trip_id",id_len,$connection);
change_trip_matchable_state($connection,$trip_id,0);
url_forward("make_trips_unmatchable.php");
?>