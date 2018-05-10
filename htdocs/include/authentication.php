<?php
require_once "include/config.php";
require_once "include/defines.php";

function url_forward($url)
{
   header("Location: " . $url);
   exit;
}

/*
function clean_name($name)
{
   $name=str_replace("\'","'",$name);
   return $name;
}
*/

function get_ip_addr_list($connection,$user_id)
{
   $connection=db_connect_if_neccessary($connection);
   $query="select ip_addr from user_ip_addr_list where user_id=${user_id}";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $list=array();
   $count=0;
   while($row=mysql_fetch_array($result))
   {
      $list[$count]=$row["ip_addr"];
      $count++;
   }
   return $list;
}

function check_for_matched_user_ip_addresses($list1,$list2)
{
   
   for($cnt1=0;$cnt1<count($list1);$cnt1++)
      for($cnt2=0;$cnt2<count($list2);$cnt2++)
	 if(strcmp($list1[$cnt1],$list2[$cnt2])==0)
	    return $list1[$cnt1];
   return null;
}


function add_user_ip_address($connection)
{
   $connection=db_connect_if_neccessary($connection);
   $ip_addr=$_SERVER["REMOTE_ADDR"];
   $user_id=$_SESSION["user_id"];
   $query="select * from user_ip_addr_list where user_id=${user_id} and ip_addr='${ip_addr}'";
   if (!$result = @ mysql_query ($query, $connection))
       showerror();
   $numrows=mysql_num_rows($result);
   if($numrows==0)
   {
      $query="INSERT INTO user_ip_addr_list VALUES("
	 . "${user_id},'${ip_addr}')";
      if (!$result = @ mysql_query ($query, $connection))
       showerror();
   }
}


function add_sec_cookie()
{
   $user_id=$_SESSION["user_id"];
   if(isset($_COOKIE["sec"]))
   {
      $cookie_array=explode(",",$_COOKIE["sec"]);
      for($i=0;$i<count($cookie_array);$i++)
      {
	 if($cookie_array[$i]==$user_id)
	    break;
      }
      if($i==count($cookie_array))
	 $cookie_array[$i]=$user_id;
      $new_cookie=implode(",",$cookie_array);
   }
   else
      $new_cookie=$user_id;
   setcookie("sec",$new_cookie,time()+(60*60*24*365*10));
}

function check_sec_cookie($user_id1,$user_id2)
{
   $found_user_id1=$found_user_id2=false;
   if(isset($_COOKIE["sec"]))
   {
      $cookie_array=explode(",",$_COOKIE["sec"]);
      for($i=0;$i<count($cookie_array);$i++)
      {
	 if($cookie_array[$i]==$user_id1)
	    $found_user_id1=true;
	 if($cookie_array[$i]==$user_id2)
	    $found_user_id2=true;
	 if($found_user_id1&&$found_user_id2)
	    return true;
      }
   }
   return false;
}


function add_log_entry($connection,$log_entry)
{
   require_once "include/time.php";

   $connection=db_connect_if_neccessary($connection);
   $ip_addr=$_SERVER["REMOTE_ADDR"];
   if(empty($_SESSION["user_id"]))
      $user_id=-1;
   else
      $user_id=$_SESSION["user_id"];
   $log_time=get_datetime();
   $query="INSERT INTO log VALUES("
      . "${user_id},'${ip_addr}','${log_time}','${log_entry}')";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
}


function authenticate_user($connection, $email_address, $password,&$result)
{
   // Test the username and password parameters
   if (!isset($email_address) || !isset($password))
      return false;
   
   // Create a digest of the password collected from
   // the challenge
   $password_digest = md5(trim($password));
   
   // Formulate the SQL find the user
   $query = "SELECT * FROM user WHERE email_address = '{$email_address}'
            AND password_digest = '{$password_digest}' AND parent_user_id is NULL";
   // Execute the query
   if (!$result = @ mysql_query ($query, $connection))
      showerror();

   // exactly one row? then we have found the user
   $num_rows=mysql_num_rows($result);
   switch($num_rows)
   {
      case 1:
	 return true;
      case 0:
	 add_log_entry($connection,"Failed login ${email_address}");
	 return false;
      default:
	 die("authenticate_user ${num_rows} entries matching ${email_address}\n");
   }
}


// Connects to a session and checks that the user has
// authenticated and that the remote IP address matches
// the address used to create the session.
function session_authenticate($user_level)
{
  if(isset($_SESSION["error_message"])) 
      unset($_SESSION["error_message"]);
  if(isset($_SESSION["message"])) 
      unset($_SESSION["message"]);
   // Check if the user hasn't logged in
   if ($user_level!=-1&&empty($_SESSION["login_email_address"]))
   {
      // The request does not identify a session
      $_SESSION["error_message"] = "You are not authorized to access the URL 
                            {$_SERVER["REQUEST_URI"]}";

   }

  // Check if the request is from a different IP address to previously
   else if (!isset($_SESSION["login_IP"]) || 
	    ($_SESSION["login_IP"] != $_SERVER["REMOTE_ADDR"]))
   {
      // The request did not originate from the machine
      // that was used to create the session.
      // THIS IS POSSIBLY A SESSION HIJACK ATTEMPT
      
      $_SESSION["error_message"] = "You are not authorized to access the URL 
                            {$_SERVER["REQUEST_URI"]} from the address 
                            {$_SERVER["REMOTE_ADDR"]}";
      
   }
   else if($user_level==1)
   {
      if(empty($_SESSION["is_administrator"])||!$_SESSION["is_administrator"])
      {
	 $_SESSION["error_message"] = "You need to be a system administrator to use this function";
      }
   }
   if(!empty($_SESSION["error_message"]))
   {
     print "<pre>";
     print $_SESSION["error_message"]; 
     if(isset($_SESSION["quick_trip"]))
	print " 1";
     print "</pre>";
     exit(1);
     add_log_entry($connection,"session_authenticate failure " . $_SESSION["error_message"]);
     url_forward("logout.php");
   }
}

function get_user_name($connection,$user_id)
{
    $query = "SELECT name FROM user WHERE user_id = '{$user_id}' AND parent_user_id is NULL";
   // Execute the query
   if (!$result = @ mysql_query ($query, $connection))
      showerror();

   // exactly one row? then we have found the user
   $num_rows=mysql_num_rows($result);
   switch($num_rows)
   {
      case 1:
	 $row=mysql_fetch_array($result);
	 return $row["name"];
      default:
	 die("get_user_name ${num_rows} entries matching ${user_id}\n");
   }
}

function check_if_user_is_banned($connection,$check_user_id)
{
   unset($_SESSION["error_message"]);
   unset($_SESSION["message"]);
   $connection=db_connect_if_neccessary($connection);
   $this_ip_addr=$_SERVER["REMOTE_ADDR"];

    $query="SELECT * FROM ban_user";
    //print "<pre>{$query}</pre>";
    if (!$result = @ mysql_query ($query, $connection))
       showerror();
    $banned_by_login_authentication=
       $banned_by_cookie=
       $banned_by_ip_address=0;
    if(isset($_COOKIE["sec"]))
       $cookie_array=explode(",",$_COOKIE["sec"]);
    while($row=mysql_fetch_array($result))
    {
       $banned_user_id=$row["user_id"];
       $banned_by_login_authentication=$row["banned_by_login_authentication"];
       $banned_by_cookie=$row["banned_by_cookie"];
       $banned_by_ip_address=$row["banned_by_ip_address"];
       if($banned_by_login_authentication&&$banned_user_id==$check_user_id)
       {
	  $reason="authentication";
	  $_SESSION["error_message"]=get_user_name($connection,$check_user_id) 
	     . " is banned from the system.";
	  break;
       }
       if($banned_by_cookie)
       {
	  if(isset($cookie_array))
	  {
	     for($i=0;$i<count($cookie_array);$i++)
	     {
		if($cookie_array[$i]==$banned_user_id)
		{
		   $reason="cookie";
		   $_SESSION["error_message"]=get_user_name($connection,$banned_user_id) 
		      . " is banned from the system & we have reason to believe you are that person.";
		   break;
		}
	     }
	  }
       }
       if($banned_by_ip_address)
       {
	  $ip_addr_list=get_ip_addr_list($connection,$banned_user_id);
	  for($i=0;$i<count($ip_addr_list);$i++)
	  {
	     if($ip_addr_list[$i]==$this_ip_addr)
	     {
		$reason="ip_addr";
		$_SESSION["error_message"]=get_user_name($connection,$banned_user_id) 
		   . " is banned from the system & we have reason to believe you are that person.";
		break;
	     }
		
	  }
       }
       
    }
    if(!empty($_SESSION["error_message"]))
    {
       add_log_entry($connection,"check_if_user_is_banned reason={$reason} " . $_SESSION["message"]);
       url_forward("logout.php");
    }
}

function logout_die($str)
{
   $_SESSION["error_message"]="Fatal Error in {$_SERVER["REQUEST_URI"]} {$str}";
   url_forward("logout.php");
}


function init_session()
{
  //print "<pre>init_session</pre>";
  //exit (1);
    if(isset($_SESSION["screen_width"]))
       $screen_width=$_SESSION["screen_width"];
    if(isset($_SESSION["screen_height"]))
       $screen_height=$_SESSION["screen_height"];
     if(isset($_SESSION["animation_done"]))
       $animation_done=$_SESSION["animation_done"];
    $_SESSION=array();
    if(isset($screen_width))
       $_SESSION["screen_width"]=$screen_width;
    if(isset($screen_height))
       $_SESSION["screen_height"]=$screen_height;
    if(isset($animation_done))
       $_SESSION["animation_done"]=$animation_done;
}


function do_base_login_stuff($connection,
			     $user_id,
			     $name,
			     $email_address,
			     $log_entry)
{
   init_session();
   $_SESSION["user_id"]=$user_id;
   //$_SESSION["name"]=clean_name($name);
   $_SESSION["name"]=$name;
   $_SESSION["login_email_address"]=$email_address;
   $_SESSION["login_IP"]=$_SERVER["REMOTE_ADDR"];
   add_log_entry($connection,$log_entry);
}

function do_login_stuff($connection,
			$user_id,
			$name,
			$email_address,
			$log_entry)
{
   global $administrator_email_address;

   do_base_login_stuff($connection,
		       $user_id,
		       $name,
		       $email_address,
		       $log_entry);
   $_SESSION["is_administrator"]=(strcmp($email_address,
					 $administrator_email_address) ? 0:1);
   add_user_ip_address($connection);
   add_sec_cookie();
   if(!$_SESSION["is_administrator"])
      check_if_user_is_banned($connection,$user_id);
}

function add_to_invitation_list($connection,$email_address)
{
   $connection=db_connect_if_neccessary($connection);
   $query = "SELECT * FROM user WHERE email_address='{$email_address}' AND parent_user_id is NULL AND is_permanent=TRUE";
   // Execute the query
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
     // exactly one row? then we have found the user
   $num_rows=mysql_num_rows($result);
   if($num_rows==1)
   {
      $row=mysql_fetch_array($result);
      if($row["email_address_is_authenticated"]==0)
      {
	    $name=mysqlclean($row,"name",name_len,$connection);
	    $email_address=mysqlclean($row,"email_address",email_address_len,$connection);
	    $primary_phone_number=mysqlclean($row,"primary_phone_number",phone_number_len,$connection);
	    $secondary_phone_number=mysqlclean($row,"secondary_phone_number",phone_number_len,$connection);
	    $query = "REPLACE INTO user VALUES (" 
	       . "${row["user_id"]},NULL,TRUE,TRUE,'${row["create_time"]}',"
	       . "'{$name}',${row["is_male"]},'${row["password_digest"]}',"
	       . "'{$email_address}','{$primary_phone_number}',"
	       . "'{$secondary_phone_number}','${row["address_location_id"]}')";
	    if (!$result = @ mysql_query ($query, $connection))
	       showerror();
      }
   } 
   else if($num_rows==0)
   {
      $query = "REPLACE INTO invitation_list VALUES ('{$email_address}')";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
   }
   else
      die("add_to_invation_list num_rows={$num_rows}\n");
}

function in_invitation_list($connection,$email_address)
{
   $connection=db_connect_if_neccessary($connection);
   $query = "SELECT * FROM invitation_list WHERE email_address='{$email_address}'";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $num_rows=mysql_num_rows($result);
   if($num_rows)
   {
      $query = "DELETE FROM invitation_list WHERE email_address='{$email_address}'";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      return 1;
   }
   return 0;
}