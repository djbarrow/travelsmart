<?php
require_once "HTML/Template/IT.php";
require_once "include/config.php";
require_once "include/logos.php";
require_once "include/validate.php";
require_once "include/defines.php";
require_once "include/db.php";
require_once "include/authentication.php";
require_once "include/temp_user.php";
require_once "include/tripplan_func.php";
require_once "include/tripmatch_logic.php";

if(isset($_SESSION["quick_trip"]))
   unset($_SESSION["quick_trip"]);
$validate_error=0;
$template=new HTML_Template_IT("./templates");
$template->loadTemplatefile("login.tpl",true,true);
$template->setCurrentBlock("PAGE_HEADER");
$connection=db_connect();
if(!$_POST)
{
   $t=time();
   setcookie("test","",$t-3600);
   setcookie("test","1234",$t+(60*60*24*365*10));
}
if ($_POST)
{
   $email_address=xss_decode($_POST["email_address"]);   
   if(empty($email_address))
   {  
      show_error_block($template,"EMPTY_EMAIL_ADDRESS");
   }
   else if(!check_email($email_address))
   {
      show_error_block($template,"INVALID_EMAIL_ADDRESS");
   }
   $password=xss_decode($_POST["password"]);
   if(empty($password))
   {  
      show_error_block($template,"EMPTY_PASSWORD");
   }
   else if(!check_alphanumeric($password))
   {
      show_error_block($template,"INVALID_PASSWORD"); 
   }
   if(!$validate_error)
   {
      $mysql_email_address=mysqlclean_str($email_address,email_address_len,$connection);
      $mysql_password=mysqlclean_str($password,password_len,$connection);
      if(authenticate_user($connection, $mysql_email_address, $mysql_password ,$result))
      {
	 $row=mysql_fetch_array($result);
	 $user_id=$row["user_id"];
	 if(!$row["email_address_is_authenticated"])
	    show_error_block($template,"USER_NOT_AUTHENTICATED",
			     "USER_ID",$user_id);
	 else
	 {
	    $user_permanent=is_user_permanent($connection);
	    $trip_to_merge=($user_permanent ? false:has_trip_to_merge($connection,$_SESSION["user_id"]));
	    if($trip_to_merge)
	       $trip_id=merge_user_database_info($connection,$_SESSION["user_id"],$row["user_id"]);
	    $email_address_is_authenticated=$row["email_address_is_authenticated"];
	    if($email_address_is_authenticated)
	    {
	       do_login_stuff($connection,
			      $user_id,
			      $row["name"],
			      $email_address,
			      "login from index.php");
	       if($trip_to_merge)
	       {
		  require_once "include/config.php";
		  require_once "include/latlonfunc.php";
		  require_once "include/tripplan_func.php";
		  
		  $t=get_trip_info_from_trip_id($trip_id,$connection);
		  tripmatch_logic($connection,$t);
	       }
	       else
		  url_forward("control_centre.php");
	    }
	    else
	    {
	       if($trip_to_merge)
		  url_forward("send_authenticate.php?email_address=${email_address}&trip_id=${trip_id}");
	       else
		  url_forward("send_authenticate.php?email_address=${email_address}");
	    }
	 }
      }
      else
      {
	 show_error_block($template,"AUTHENTICATION_FAILED");
      }
      
   }
}
else
{
   $email_address=$password="";
}
require_once "include/Sajax.php";
require_once "include/internet_speed_cookie.php";

function get_login_email_address_cookie()
{
   
   if(isset($_COOKIE["login_email_address_cookie"]))
   {
     
       $retval1=$_COOKIE["login_email_address_cookie"];
       $retval2=1;
   }
   else
      $retval2=0;
   $retstr= "document.login.remember_login_email_address.checked=" . $retval2 . ";";
   if($retval2)
      $retstr .= "document.login.email_address.setAttribute('value','{$retval1}');";
   return $retstr;
}

function set_login_email_address_cookie($checked,$cookie)
{
   setcookie("login_email_address_cookie",$cookie,$checked ? time()+(60*60*24*365):time());
   return "";
}

function set_screen_width_and_height($width,$height)
{
   $_SESSION["screen_width"]=$width;
   $_SESSION["screen_height"]=$height;
   return "";
}

$sajax_request_type = "GET";
sajax_init();
sajax_export("set_internet_speed_cookie","get_internet_speed_cookie",
	     "get_login_email_address_cookie","set_login_email_address_cookie",
   "set_screen_width_and_height");
sajax_handle_client_request();
?>
 <script>
<?php
  sajax_show_javascript();
?>

function generic_cb(evalstr)
{
   eval(evalstr);
}


function set_checkboxes()
{
   x_get_internet_speed_cookie("login",generic_cb);
   x_get_login_email_address_cookie(generic_cb);
   x_set_screen_width_and_height(screen.availWidth,screen.availHeight,
				 generic_cb);
}

function set_internet_speed()
{  
   speed=document.login.fast_internet_connection.checked;
   if(speed)
      speed=1;
   else
      speed=0;
   x_set_internet_speed_cookie(speed,generic_cb);
}

function set_login_email_address_cookie()
{
   checked=document.login.remember_login_email_address.checked;
   if(checked)
      checked=1;
   else
      checked=0;
   cookie=document.login.email_address.value;
   x_set_login_email_address_cookie(checked,cookie,generic_cb);
}
</script>
<?php
if(!$_POST||$validate_error)
{
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str(1,$_POST));
   
   if(!isset($_SESSION["animation_done"])&&fast_internet_connection())
   {
       $_SESSION["animation_done"]=1;
       show_nested_variable_block($template,"DISPLAY_MAP",
				  "GOOGLE_MAPS_KEY",get_google_maps_key());
   }
   else
      show_nested_block($template,"NO_MAP");
   $template->setVariable("EMAIL_ADDRESS",xss_encode($email_address));
   $template->setVariable("PASSWORD",xss_encode($password));
   $template->setVariable("ADD_NEW_LOCATION",$add_new_location);
   $user_permanent=is_user_permanent(0);
   if(!$user_permanent)
      $user_permanent=!has_trip_to_merge(0,$_SESSION["user_id"]);
   $randfact=rand(1,num_carpooling_facts);
   $factstr="FACT_{$randfact}";
   show_nested_block($template,$factstr);
   $template->show();
   if(DISPLAY_STATS)
   {
      require_once "include/server_stats.php";
      
      $template=new HTML_Template_IT("./templates");
      $template->loadTemplatefile("login.tpl",true,true);
      
      
      $stats=get_stats(0);
      $template->setCurrentBlock("DISPLAY_STATS");
      $template->setVariable("NUMBER_OF_USERS_REGISTERED",$stats->number_of_users_registered);
      $template->setVariable("NUMBER_OF_MATCHED_TRIPS",$stats->number_of_matched_trips);
      $template->setVariable("TOTAL_MILEAGE_SAVED",$stats->total_mileage_saved);
      $template->setVariable("LITRES_OF_FUEL_SAVED",$stats->litres_of_fuel_saved);
      $template->setVariable("CARBON_FOOTPRINT_SAVED",$stats->carbon_footprint_saved);
      $template->setVariable("CALC_TIME",$stats->calc_time);
      $template->parseCurrentBlock();
      $template->show();
   }
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("login.tpl",true,true);
   $template->setCurrentBlock("PAGE_FOOTER");
   $template->setVariable("ADMINISTRATOR_EMAIL_ADDRESS",$administrator_email_address);
   $template->setVariable("GOOGLE_ANALYTICS",get_google_analytics_str());
   $template->parseCurrentBlock();
   $template->show();
}
if(!$_POST)
   garbage_collect_temp_users(0);
?>