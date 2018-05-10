<?php
function url_forward($url)
{
   header("Location: " . $url);
   exit;
}

session_start();
$_SESSION["temp"]="blah";
url_forward("check_session2.php");
?>