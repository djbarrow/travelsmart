<?php
   // This file is the same as example 6-7, but includes mysqlclean() and shellclean() 

$db_hostname = "localhost";
$db_username = "root";
$db_password = "password";
$databasename = "atob";

#$db_hostname = "mysql.ariasoft.ie";
#$db_username = "atob";
#$db_password = "password";
#$databasename = "atob";

#/usr/local/mysql/bin/mysql -h mysql.ariasoft.ie -uatob -ppassword 

function showerror()
{
   die("Error " . mysql_errno() . " : " . mysql_error());
}

function mysqlclean_str($str,$maxlength,$connection)
{
  if (isset($str))
   {
      $input = substr($str, 0, $maxlength);
      $input = mysql_real_escape_string($input, $connection);
      return ($input);
   }
   return NULL;
}

function mysqlclean($array, $index, $maxlength, $connection)
{
   return mysqlclean_str($array["{$index}"], $maxlength, $connection);
}

function xss_decode($str)
{
   if(stristr($str,"script"))
   {
      $logstr=substr("possible xxs hack attempt ". $str,0,log_len);
      add_log_entry($connection,$log_str);
   }
   $str=str_replace("&#60;","<",$str);
   $str=str_replace("&#62;",">",$str);
   $str=str_replace("&#38;","&",$str);
   $str=str_replace("&#34;","\"",$str);
   $str=str_replace("&#39;","'",$str);
   $str=str_replace("&#40;","(",$str);
   $str=str_replace("&#41;",")",$str);
   $str=str_replace("&#37;","%",$str);
   $str=str_replace("&#59;",";",$str);
   $str=str_replace("&#43;","+",$str);
   $str=str_replace("&#45;","-",$str);
   $str=str_replace("&#35;","#",$str);
   return $str;
}

function semicolon_replace($str)
{
   for($i=0;$i<strlen($str);$i++)
   {
      if($str[$i]==';')
      {
	 if(($i-4)>=0&&($str[$i-4]=='&')&&
	    ($str[$i-3]=='#')
	    &&($str[$i-2]>='0'&&$str[$i-2]<='9')		
	    &&($str[$i-1]>='0'&&$str[$i-1]<='9'))
	    continue;
/*
	 if(($i-3)>=0&&($str[$i-3]=='&')&&
	    (($str[$i-2]=='l'||$str[$i-2]='g')&&$str[$i-1]=='t'))
	    continue;
*/
	 $str=substr($str,0,$i) . "&#59;" .
	    substr($str,$i+1,strlen($str)-$i+1);
      }
      
   }
   return $str;
}
function xss_encode($str)
{
   $str=str_replace("#","&#35;",$str);
   $str=str_replace("<","&#60;",$str);
   $str=str_replace(">","&#62;",$str);
   $str=str_replace("&","&#38;",$str);
   $str=str_replace("\"","&#34;",$str);
   $str=str_replace("'","&#39;",$str);
   $str=str_replace("(","&#40;",$str);
   $str=str_replace(")","&#41;",$str);
   $str=str_replace("%","&#37;",$str);
   $str=str_replace("+","&#43;",$str);
   $str=str_replace("-","&#45;",$str);
   $str=semicolon_replace($str);
   return $str;
}


function xss_decode_mysqlclean($array, $index, $maxlength, $connection)
{
   $array["{$index}"]=xss_decode($array["{$index}"]);
   return mysqlclean($array, $index, $maxlength, $connection);
}

function shellclean($array, $index, $maxlength)
{
   if (isset($array["{$index}"]))
   {
      $input = substr($array["{$index}"], 0, $maxlength);
      $input = EscapeShellArg($input);
      return ($input);
   }
   return NULL;
}

function db_connect()
{
   global $db_hostname,$db_username,$db_password,$databasename;

   if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
      die("Cannot connect");
   if(!mysql_select_db($databasename,$connection))
      showerror();
   return $connection;
}

function db_connect_if_neccessary($connection)
{
   if($connection==0)
      $connection=db_connect();
   return $connection;
}
?>
