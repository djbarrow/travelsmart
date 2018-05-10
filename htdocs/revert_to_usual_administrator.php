<?php
require_once "include/defines.php";
require_once "include/authentication.php";
require_once "include/db.php";

session_authenticate(1);
$connection=db_connect();
$query = "SELECT * FROM user WHERE email_address = '{$administrator_email_address}' AND parent_user_id is NULL";
if (!$result = @ mysql_query ($query, $connection))
   showerror();
$num_rows=mysql_num_rows($result);
switch($num_rows)
{
   case 1:
      $row=mysql_fetch_array($result);
      do_base_login_stuff($connection,
		     $row["user_id"],
		     $row["name"],
		     $administrator_email_address,
		     "revert_to_usual_administrator from ${_SESSION["user_id"]} ${_SESSION["login_email_address"]}");
      $_SESSION["is_administrator"]=1;
   url_forward("control_centre.php");   
   case 0:
      add_log_entry($connection,"Failed revert_to_usual_administrator ${administrator_email_address} from ${_SESSION["user_id"]} ${_SESSION["login_email_address"]}");
      die("revert to_usual_administrator.php ${num_rows} entries matching ${administrator_email_address}\n");
}
?>