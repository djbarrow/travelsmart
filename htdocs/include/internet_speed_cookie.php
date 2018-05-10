<?php

function set_internet_speed_cookie($set)
{
   setcookie("fast_internet_connection",$set,time()+(60*60*24*365));
   return "";
}

function get_internet_speed_cookie($form)
{
   if(isset($_COOKIE["fast_internet_connection"]))
       $retval=$_COOKIE["fast_internet_connection"];
   else
   {
      $retval=1;
      set_internet_speed_cookie(1);
   }
   $retstr="document.{$form}.fast_internet_connection.checked={$retval};";
   return $retstr;
}

function fast_internet_connection()
{
   if(isset($_COOKIE["fast_internet_connection"]))
      return  $_COOKIE["fast_internet_connection"];
   return 1;
}

?>
