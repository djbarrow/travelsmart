<?php
require_once "include/defines.php";
require_once "include/config.php";

function get_http_header()
{
   global $local_ip_addr;

   return "http://" . $_SERVER["SERVER_NAME"] . "/";
}

function get_local_http_header($local)
{
   return (($local) ? "": get_http_header());
}


function get_sponsor_name()
{
   $sponsor_array=explode(".",$_SERVER["SERVER_NAME"]);
   return $sponsor_array[0];
}

function get_css_name($local=1)
{
   $css_name=get_sponsor_name() . ".css";
   if(!file_exists($css_name))
      $css_name="travelsmart.css";
   return get_local_http_header($local) . $css_name;
}

function get_logos_str($local=1,$test_cookie=0)
{
   
   $sponsor_filename="images/" . get_sponsor_name();
   if(file_exists($sponsor_filename . ".jpg"))
      $sponsor_filename.=".jpg";
   else if(file_exists($sponsor_filename . ".gif"))
      $sponsor_filename.=".gif";
   else
      unset($sponsor_filename); 
   switch(rand(0,2))
   {
      case 0:
	 $logostr="TS_butterfly_RGB.jpg";
         break;
      case 1:
	 $logostr="TS_dots-carpooling_RGB.jpg";
	 break;
      default:
	 $logostr="TS_dots-moving_RGB.jpg";
   }
   $sponsor_logo_filename= "images/" . get_sponsor_name() . "-" . $logostr;
   $str="<TABLE align=center><TR>";
   if(file_exists($sponsor_logo_filename))
   {
     $str.="<TD><img SRC=" .
     get_local_http_header($local) . $sponsor_logo_filename . " NOSAVE >";
   }
   else
   {
      $str .= (isset($sponsor_filename) ? 
	  ("<TD WIDTH=30%><img SRC=" . get_local_http_header($local) . $sponsor_filename . " NOSAVE >" . "<TD WIDTH=40%></TD>" )
	  : ("<TD WIDTH=70%>")) .
	 "</TD>" .
	 
	 "<TD WIDTH=30%>" .
	 "<img SRC=" . get_local_http_header($local) . "images/" . $logostr . " NOSAVE >";
	
   }
   $str.= "</TD></TR></TABLE>";
   if($test_cookie&&(!isset($_COOKIE["test"])||strcmp($_COOKIE["test"],"1234")))
   {
      $str.="<center><p class=error_big>"
	    . "Cookies are disabled, this website cannot work without "
	    . "cookies, please enable them in your browsers preferences "
	    . "or if you have anti spyware software installed which "
	    . "disables cookies please enable them in the anti spyware "
	    . "software."
	    . "</p></center>";
   }
   return $str;
}

function get_google_maps_key()
{
   //return "";
   $server_name=$_SERVER["SERVER_NAME"];
   $google_maps_key="http://maps.google.com/maps?file=api&amp;v=2&amp;key=";
   
   if(strstr($server_name,"travelsmart.ie"))
      $google_maps_key.="ABQIAAAAEtCdkUSk-YhLUVXxe6VOiBQikexyDJI62025KMqB6GENm6XaHhTVz5ARBFpF8tIEt8gAEPnQApw-xQ";
   else if(strcmp($server_name,"10.0.0.2")==0)
      $google_maps_key.="ABQIAAAAEtCdkUSk-YhLUVXxe6VOiBQorY6mP03n72EKZcQG5HcZqK1IwRS1w-rulFgldPEfCI6PBJDMsK3Ycg";
   else if(strcmp($server_name,"localhost")==0)
      $google_maps_key.="ABQIAAAAEtCdkUSk-YhLUVXxe6VOiBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQXIa6HpPnZ5XKmYStG2dQC5lTfrA";

   else if(strcmp($server_name,"206.196.111.221")==0)
      $google_maps_key.="ABQIAAAAEtCdkUSk-YhLUVXxe6VOiBRfhqjeGoLRnGoXI-L2sWoX6NRAnxTXAcmUNwiI9vZdc6OxJuL9mMstJA";
   else
      die("No appropriate google maps key found.");
   return $google_maps_key;
}

function get_google_maps_width_height_string()
{
   if(isset($_SESSION["screen_width"])&&isset($_SESSION["screen_height"]))
   {
      $width=(int)($_SESSION["screen_width"]*7.5/10);
      $height=(int)($_SESSION["screen_height"]*5.5/10);
   }
   else
   {
      $width=700;
      $height=380;
   }
   return "width: {$width}px; height: {$height}px";
}

function get_google_maps_mapsize_control_string()
{
   $height=480;
   if(isset($_SESSION["screen_height"]))
      $height=$_SESSION["screen_height"];
   return $height<=480 ? "GSmallMapControl" :"GLargeMapControl";
}

function get_google_analytics_str()
{
  $server_name=$_SERVER["SERVER_NAME"];
  switch(GOOGLE_ANALYTICS)
  {
  case 0:
    $retstr="";
  case 1:
    if(strstr($server_name,"travelsmart.ie"))
    {
      $retstr= "<script src=\"http://www.google-analytics.com/urchin.js\" type=\"text/javascript\">\n"
	. "</script>\n"
	. "<script type=\"text/javascript\">\n"
	. "_uacct = \"UA-2997034-1\";\n"
	. "urchinTracker();\n"
	. "</script>\n";
      
      break;
    }
  case 2:
    //$retstr="<pre>test analytics</pre>";
    $retstr="";
  }
  return $retstr;
}
?>