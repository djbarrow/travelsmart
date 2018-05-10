<?php
require_once "HTML/Template/IT.php";
require_once "include/db.php";
require_once "include/validate.php";
require_once "include/time.php";
require_once "include/authentication.php";
require_once "include/tripplan_func.php";
require_once "include/display_plan_trip.php";
require_once "include/config.php";
require_once "include/latlonfunc.php";
require_once "include/temp_user.php";
require_once "include/logos.php";
require_once "include/tripmatch_logic.php";

$validate_error=0;
$connection=0;
if($_GET&&$_GET["edit"]==0)
   $_SESSION=array();
$user_permanent=is_user_permanent(0,0,1);
if($user_permanent)
   $editing=(empty($_SESSION["user_id"]) ? 0:1);
else
   $editing=0;
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("add_edit_user.tpl",true,true);
$real_post=true;
if(isset($_SESSION["saved_add_edit_user"]))
{
   $_POST=unserialize($_SESSION["saved_add_edit_user"]);
   unset($_SESSION["saved_add_edit_user"]);
   $real_post=false;
}
$have_email_style_select=($editing||!EMAIL_AUTHENTICATION);
if ($_POST)
{
   require_once "include/defines.php";
   require_once "include/upload_file.php";
   
   if($real_post)
   {
      if(!empty($_POST["add_edit_user"]))
	 unset($_SESSION["saved_add_edit_user"]);
      else if(!empty($_POST["add_address"]))
      {
	 if(empty($_SESSION["user_id"]))
	    create_temporary_user(0); 
	 $_SESSION["saved_add_edit_user"]=serialize($_POST);
	 url_forward($add_new_location);
      }
   }
   if(!($connection=@mysql_connect($db_hostname,$db_username,$db_password)))
      die("Cannot connect");
   $name=xss_decode($_POST["name"]);
   $mysql_name=mysqlclean_str($name,name_len,$connection);
   if($real_post&&empty($name))
      show_error_block($template,"EMPTY_NAME"); 
   $sex=mysqlclean($_POST,"sex",sex_len,$connection);
   if($real_post&&empty($sex))
      show_error_block($template,"SEX_UNSELECTED"); 
   $is_male=($sex=='m' ? 1:0);
   $password=xss_decode($_POST["password"]);
   if($real_post&&empty($password))
      show_error_block($template,"EMPTY_PASSWORD"); 
   $confirm_password=xss_decode($_POST["confirm_password"]);
   if($real_post&&empty($confirm_password))
      show_error_block($template,"EMPTY_CONFIRM_PASSWORD"); 
   if($real_post&&strcmp($password,$confirm_password))
      show_error_block($template,"UNMATCHED_PASSWORDS"); 
   $password_digest=md5(trim($password));
   $email_address=xss_decode($_POST["email_address"]);
   $mysql_email_address=mysqlclean_str($email_address,email_address_len,$connection);
   if($real_post&&empty($email_address))
   {  
      show_error_block($template,"EMPTY_EMAIL_ADDRESS");
   }
   else if($real_post&&!check_email($email_address))
   {
      show_error_block($template,"INVALID_EMAIL_ADDRESS");
   }
   $primary_phone_number=xss_decode($_POST["primary_phone_number"]);
   $mysql_primary_phone_number=mysqlclean_str($primary_phone_number,phone_number_len,$connection);
   if($real_post&&empty($primary_phone_number))
   {  
      show_error_block($template,"EMPTY_PHONE_NUMBER");
   }
   $secondary_phone_number=xss_decode($_POST["secondary_phone_number"]);
   $mysql_secondary_phone_number=mysqlclean($secondary_phone_number,phone_number_len,$connection);
   $address_location_id=mysqlclean($_POST,"address_location_id",id_len,$connection);
   if($real_post&&empty($address_location_id))
   {  
      show_error_block($template,"ADDRESS_UNSELECTED");
   }
   
   if($have_email_style_select)
   {
      $email_style=mysqlclean($_POST,"email_style",email_style_len,$connection);
      if(!isset($email_style))
	 die("email style not set");
   }
   else
      $email_style=default_email_style;
   if(HAVE_TERMS_OF_USE)
      $terms_of_use=(empty($_POST["terms_of_use"]) ? 0:1);
   if($real_post&&!$editing)
   {

      $query = "SELECT * FROM user WHERE email_address='{$email_address}' AND parent_user_id is NULL AND is_permanent=TRUE";
      // Execute the query
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      // exactly one row? then we have found the user
      if(mysql_num_rows($result)>0)
      {
	 $user_row=mysql_fetch_array($result);
	 if($user_row["email_address_is_authenticated"])
	    show_error_block($template,"USER_ALREADY_EXISTS");
	 else
	    show_error_block($template,"USER_NOT_AUTHENTICATED",
			       "USER_ID",$user_row["user_id"]);
      }
      if(HAVE_TERMS_OF_USE&&!$terms_of_use)
	 show_error_block($template,"TERMS_OF_USE_CHECKBOX_NOT_CLICKED");
    }
   if($real_post&&!$validate_error)
   {
      if(!mysql_select_db($databasename,$connection))
	 showerror();
      if($editing)
      {
	 session_authenticate(0);
	 $query="SELECT * FROM user WHERE user_id=${_SESSION["user_id"]} AND parent_user_id is NULL"; 
	 if (!$result = @ mysql_query ($query, $connection))
	    showerror();
	 $num_users=mysql_num_rows($result);
	 if($num_users!=1)
	    die("num_users should be 1 it is " . $num_users ."\n");
	 $row=mysql_fetch_array($result);
	 if($row["is_permanent"]==0)
	    die("editing a temporary user\n");
	 $old_name=mysqlclean($row,"name",name_len,$connection);
	 $old_email_address=mysqlclean($row,"email_address",email_address_len,$connection);
	 $old_primary_phone_number=mysqlclean($row,"primary_phone_number",phone_number_len,$connection);
	 $old_secondary_phone_number=mysqlclean($row,"secondary_phone_number",phone_number_len,$connection);
	 $query = "INSERT INTO user VALUES (NULL,${_SESSION["user_id"]},"
	    . "TRUE,${row["email_address_is_authenticated"]},'${row["create_time"]}',"
	    . "'{$old_name}',${row["is_male"]},'${row["password_digest"]}',"
	    . "'{$old_email_address}','{$old_primary_phone_number}',"
	    . "'${old_secondary_phone_number}','${row["address_location_id"]}',"
	    . "'${row["email_style"]}')";
	 if (!$result = @ mysql_query ($query, $connection))
	    showerror();
	 $user_picture=get_picture_filename("user",$row["user_id"]);
	 if (file_exists($user_picture))
	 {
	    $new_user_id=mysql_insert_id($connection);
	    $new_user_picture=get_picture_filename("user",$new_user_id);
	    copy($user_picture,$new_user_picture);
	 }
	 
      }
      $datetime=get_datetime();
      $email_address_changed=((($editing&&strcmp($email_address,$row["email_address"]))||!$editing) ? true : false );
      $email_authentication_required=(EMAIL_AUTHENTICATION&&
				      ($email_address_changed&&
				       !in_invitation_list($connection,$email_address)));
      $query = "REPLACE INTO user VALUES (" 
	 . (($editing||!$user_permanent) ? "${_SESSION["user_id"]}" : "NULL" )
	 . ",NULL,TRUE,"
	 . ( $email_authentication_required ? "FALSE" : "TRUE")
	 . ",{$datetime},'${mysql_name}',${is_male},'${password_digest}','${mysql_email_address}','${mysql_primary_phone_number}','${mysql_secondary_phone_number}','${address_location_id}',{$email_style})";
      if(!(@mysql_query($query,$connection)))
	 showerror();
      $user_id=mysql_insert_id($connection);
      resize_upload_jpeg("user",$user_id,150,150);
      if(!$email_authentication_required)
      {
	 $merge_user_id=$_SESSION["merge_user_id"];
	 do_login_stuff($connection,
			$user_id,
			$name,
			$email_address,
			"login from add_edit_user.php");
	 if(!empty($merge_user_id)&&$editing)
	 {
	    if(!is_user_permanent($connection,$merge_user_id))
	    {
	       if(has_trip_to_merge($connection,$merge_user_id))
	       {
		  $trip_id=merge_user_database_info($connection,
						    $merge_user_id,
						    $_SESSION["user_id"]);
		  $t=get_trip_info_from_trip_id($trip_id,$connection);
		  tripmatch_logic($connection,$t);
	       }
	    }
	 }
      }	 
      if($user_permanent)
      {
	 if($email_authentication_required)
	    url_forward("send_authenticate.php?email_address=${email_address}");
	 else
	 {
	    if($editing)
	       url_forward("control_centre.php");
	    else
	       url_forward("plan_trip.php");
	 }
      }
      else
      {
	 $query = "SELECT trip_id FROM trip WHERE user_id=${_SESSION["user_id"]}";
	 if (!$result = @ mysql_query ($query, $connection))
	    showerror();
	 $numrows=mysql_num_rows($result);
	 if($numrows==1)
	 {
	    $row=mysql_fetch_array($result);
	    
	    $trip_id=$row["trip_id"];
	    if($email_authentication_required)
	       url_forward("send_authenticate.php?email_address=${email_address}&trip_id=${trip_id}");
	    else
	    {
	       $t=get_trip_info_from_trip_id($row["trip_id"],$connection);
	       tripmatch_logic($connection,$t);
	    }
	 }
	 else
	 {
	    if($email_authentication_required)
	       url_forward("send_authenticate.php?email_address=${email_address}");
	    else
	    {
	       if($editing)
		  url_forward("control_centre.php");
	       else
		  url_forward("plan_trip.php");
	    }
	 }
      }
   }
} 
else
{
   if($editing)
   {
      session_authenticate(0);
      $connection=db_connect();
      $query="SELECT * FROM user WHERE user_id=${_SESSION["user_id"]} AND parent_user_id is NULL"; 
      if (!$result = @ mysql_query ($query, $connection))
	 showerror();
      $num_users=mysql_num_rows($result);
      if($num_users!=1)
	 die("num_users should be 1 it is" . $num_users ."\n");
      $user_row=mysql_fetch_array($result);
      $name=$user_row["name"];
      $is_male=$user_row["is_male"];
      $email_address=$user_row["email_address"];
      $primary_phone_number=$user_row["primary_phone_number"];
      $secondary_phone_number=$user_row["secondary_phone_number"];
      $address_location_id=$user_row["address_location_id"];
      $password=$confirm_password="";
      $email_style=$user_row["email_style"];
   }
   else
   {
      $name=
	 $email_address=
	 $primary_phone_number=
	 $secondary_phone_number=
	 $password=
	 $confirm_password="";
      $address_location_id=0;
      $terms_of_use=0;
      $email_style=default_email_style;
   }
}

if(!$_POST||$validate_error||!$real_post)
{
   $template->setVariable("TITLE",$editing ? "Edit User" : "New User");
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str(1,1));
   $template->setVariable("NAME",xss_encode($name));
   if(($_POST&&(empty($sex)||$sex=='0'))||(!$_POST&&!$editing))
      $sel=0;
   else if($is_male)
      $sel=1;
   else
      $sel=2;
   $template->setVariable("SELECTED_0",$sel==0 ? "selected" : "");
   $template->setVariable("SELECTED_m",$sel==1 ? "selected" : "");
   $template->setVariable("SELECTED_f",$sel==2 ? "selected" : "");
   $template->setVariable("PASSWORD",xss_encode($password));
   $template->setVariable("CONFIRM_PASSWORD",xss_encode($confirm_password));
   $template->setVariable("EMAIL_ADDRESS",xss_encode($email_address));
   $template->setVariable("PRIMARY_PHONE_NUMBER",xss_encode($primary_phone_number));
   $template->setVariable("SECONDARY_PHONE_NUMBER",xss_encode($secondary_phone_number));
   if($have_email_style_select)
   {
      $email_style_array=array(
	 array(email_style_html,"HTML - pretty but more likely to be blocked by aggressive spam filters"),
	 array(email_style_text,"Plain Text - not pretty but more likely to get past spam filters")
	 ); 
      $email_style_select=
      output_select("email_style",$email_style_array,$email_style);
      $template->setCurrentBlock("EMAIL_TYPE_SELECT");
      $template->setVariable("EMAIL_STYLE_SELECT",$email_style_select);
      $template->parseCurrentBlock();
      $template->setCurrentBlock("__global__");
   }
   fill_user_location_select($template,$connection,"ADDRESS_SELECT",$address_location_id);
   if(SHOW_SPAM_PRECAUTIONS)
   {
      show_nested_block($template,"SPAM_PRECAUTIONS");
   }
   if(HAVE_TERMS_OF_USE&&!$editing)
   {
       $template->setCurrentBlock("TERMS_OF_USE");
       set_check($template,"terms_of_use",1,$terms_of_use);
       $template->parseCurrentBlock();
       $template->setCurrentBlock("__global__");
   }
   if($editing)
      show_nested_block($template,"BACK_TO_CONTROL_CENTRE");
   else
      show_nested_block($template,"BACK_TO_LOGIN_PAGE");
   $template->setCurrentBlock("__global__");
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
?>