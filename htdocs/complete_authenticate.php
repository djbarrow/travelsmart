<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/authentication.php";
require_once "include/defines.php";
require_once "include/tripplan_func.php";
require_once "include/temp_user.php";
require_once "include/tripmatch_logic.php";

if($_GET)
{
   $connection=db_connect();
   $email_address=mysqlclean($_GET,"email_address",email_address_len,$connection);
   if(empty($email_address))
      logout_die("empty email address\n");
   $password_digest=mysqlclean($_GET,"auth",password_digest_len,$connection);
   if(empty($password_digest))
      logout_die("invalid authentication\n");
   $forgot_password=$_GET["forgot_password"];
   if(!$forgot_password)
	   $trip_id=mysqlclean($_GET,"trip_id",id_len,$connection);
   $query = "SELECT * FROM user WHERE email_address='${email_address}' AND password_digest='${password_digest}' AND parent_user_id is NULL";
   if(!($result=@mysql_query($query,$connection)))
      showerror();
   $num_rows=mysql_num_rows($result);
   if($num_rows!=1)
       logout_die("couldn't find unique user, number of matching users=${num_rows}, are you sure you copied & pasted the authentication url properly into the browser?\n");
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   $row=mysql_fetch_array($result);
   $user_id=$row["user_id"];
   $name=mysqlclean($row,"name",name_len,$connection);
   $email_address=mysqlclean($row,"email_address",email_address_len,$connection);
   $primary_phone_number=mysqlclean($row,"primary_phone_number",phone_number_len,$connection);
   $secondary_phone_number=mysqlclean($row,"secondary_phone_number",phone_number_len,$connection);
   if(isset($_GET["email_style"]))
      $email_style=mysqlclean($_GET,"email_style",email_style_len,$connection);
   else
      $email_style=$row["email_style"];
   $query = "REPLACE INTO user VALUES (" 
      . "${user_id},NULL,TRUE,TRUE,'${row["create_time"]}',"
      . "'{$name}',${row["is_male"]},'${row["password_digest"]}',"
      . "'{$email_address}','{$primary_phone_number}',"
      . "'{$secondary_phone_number}','${row["address_location_id"]}',"
      . "'{$email_style}')";
   if (!$result = @ mysql_query ($query, $connection))
      showerror();
   do_login_stuff($connection,
		  $user_id,
		  $row["name"],
		  $email_address,
		  "login from complete_authenticate.php" . ($forgot_password ? " forgot password" : ""));
   if($forgot_password)
   {
      $_SESSION["merge_user_id"]=$_GET["merge_user_id"];
      url_forward("add_edit_user.php?edit=1\n");
   } 
   if(isset($_GET["saved_tripmatch_id"]))
   {
      url_forward("display_saved_tripmatch.php?saved_tripmatch_id=${_GET["saved_tripmatch_id"]}");
   }
   if(!empty($trip_id))
   {
      $query = "SELECT * FROM trip WHERE user_id = {$user_id} AND trip_id = {$trip_id}";
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $numrows=mysql_num_rows($result);
      if($numrows!=1)
	 die("illegal number trips num_rows!=1 it is ${numrows}\n");
      require_once "include/config.php";
      require_once "include/latlonfunc.php";
      require_once "include/tripplan_func.php";

      change_trip_matchable_state($connection,$trip_id,1);
      $t=get_trip_info_from_trip_id($trip_id,$connection);
      tripmatch_logic($connection,$t);
   }
   else
      url_forward("plan_trip.php");
}
else
   logout_die("No $_GET arguments\n");
?>