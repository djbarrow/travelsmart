<!-- BEGIN PAGE_HEADER -->
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>TravelSmart, Irelands Favourite Carpool Website, try us & find out why...</title>
<META name="description" content="Travelsmart,Carpool,Carpool Ireland,TravelSmart, Irelands Favourite Carpool Website, Homepage">
<META name="keywords" content="homepage,website,travelsmart,Carpool,Carpool Ireland,Carpooling,Carpooling & more,we care about fresh air,bringing people together,aria,aria enterprises,aria software,aria software ireland,aria software ireland ltd,aria software ireland ltd.,barrow,d.j. barrow,denis barrow,dj barrow,denis joseph barrow,scarra,glantane,mallow,co. cork,cork,county cork,ireland">
<link rel=stylesheet href={CSS_FILE}>
</head>
<script language="JavaScript">
function popup(url) {
window.open(url, '', 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1');
}
</script>
<!-- BEGIN DISPLAY_MAP -->
    <script src="{GOOGLE_MAPS_KEY}"
      type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[
    function load() {
      set_checkboxes(); 	
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
	 map.setCenter(new GLatLng(48.80686,2.28516),5);
	point=new GLatLng(53.34748,-6.25976)
        window.setTimeout(function() {
          map.panTo(point);
        }, 5000);
	window.setTimeout(function() {
          	map.setZoom(7);
		map.panTo(point);
        	},7000);
	window.setTimeout(function() {
          	map.setZoom(9);
		map.panTo(point);
        	},9000);
	window.setTimeout(function() {
          	map.setZoom(11);
		map.panTo(point);
        	},11000);
	window.setTimeout(function() {
          	map.setZoom(13);
		map.panTo(point);
        	},13000);
	window.setTimeout(function() {
          	map.setZoom(15);
		map.panTo(point);
        	},15000);
     }	
  }
    //]]>
    </script>
<body onload="load()">
{LOGOS}
<table>
<tr>
<td width=50%></td><td>
<div id="map" style="width: 500px; height: 180px"></div>
</td><td width=50%></td>
</tr>
</table>
<!-- END DISPLAY_MAP -->
<!-- BEGIN NO_MAP -->
<body onload="set_checkboxes()">
{LOGOS}
<!-- END NO_MAP -->
<center>
<table>
<tr>
<td>
<table>
<form name="login" action="index.php" method=post>
<tr><td colspan=2>
<h2>New User?, Register <a href="add_edit_user.php?edit=0">here.</a></h2>
</td></tr>
<tr><td colspan=2>
<h2>Existing Member?, Login below.</h2>
</td>
</tr>
<!-- BEGIN ERROR_AUTHENTICATION_FAILED -->
<tr class=error_big colspan=2 align=center>
<td>
Authentication Failed
</td>
</tr>
<!-- END ERROR_AUTHENTICATION_FAILED -->
<!-- BEGIN ERROR_USER_NOT_AUTHENTICATED -->
<tr class=error_big align=center>
<td colspan=2>
This email address has not been authenticated,<td>
</tr>
<tr class=error_medium align=center>
<td colspan=2>
please double check whether you got the authentication email,<br>
if you didn't click <a href="send_authenticate.php?user_id={USER_ID}">here</a>
 to resend it,sending the email may take a minute please be patient,<br>
check that the email isn't being spam filtered<br>
or in the bulk email folder.
</td>
</tr>
<!-- END ERROR_USER_NOT_AUTHENTICATED -->
<!-- BEGIN ERROR_INVALID_EMAIL_ADDRESS -->
<tr class=error>
<td></td>
<td>
invalid email address
</td>
</tr>
<!-- END ERROR_INVALID_EMAIL_ADDRESS -->
<!-- BEGIN ERROR_EMPTY_EMAIL_ADDRESS -->
<tr class=error>
<td></td>
<td>
empty email address
</td>
</tr>
<!-- END ERROR_EMPTY_EMAIL_ADDRESS -->
<tr>
<td>email address:</td>
<td><input name=email_address value="{EMAIL_ADDRESS}" type=text size=30 maxlength=120></td>
</tr>
<!-- BEGIN ERROR_INVALID_PASSWORD -->
<tr class=error>
<td></td>
<td>
invalid password
</td>
</tr>
<!-- END ERROR_INVALID_PASSWORD -->
<!-- BEGIN ERROR_EMPTY_PASSWORD -->
<tr class=error>
<td></td>
<td>
empty password
</td>
</tr>
<!-- END ERROR_EMPTY_PASSWORD -->
<tr>
<td>password:</td>
<td><input name=password type=password value="{PASSWORD}" size=30 maxlength=12></td>
</tr>
<tr>
<td>I've got a <b>fast internet connection</b>, enable google maps<input type=checkbox value=1 name=fast_internet_connection onclick="set_internet_speed()"></td>
<td>Remember my login email address on this computer<input type=checkbox value=1 name=remember_login_email_address onclick="set_login_email_address_cookie()">
</tr>
<tr><td></td><td colspan=3 align=center>
<input type=submit value="Sign In">
</td><tr>
</form>
</table>
<td>
<table>
<tr><td>
<a href="{ADD_NEW_LOCATION}?quick_search=1">Quick Search</a>
Just two map clicks & you've got instant relevant Carpool search results,
no registration required.
</td></tr>
<tr><td>
<a href="{ADD_NEW_LOCATION}?fisher_price=1">Google Maps Gadget</a><br>
If you like Fisher Price toys you'll love this.
</td></tr>
<tr><td>
<a href="flyers.php">Flyers</a><br>
to put on notice boards.
</td></tr>
<tr><td>
<a href="forgot_password.php">Forgot Password</a><br>
Click here to get out of trouble.
</td></tr>
<tr><td>
<a href="send_testpage.php">Send Test Tripmatch Pages</a><br>
Click here to test if you tripmatches might be getting spam filtered.
</td></tr>
</table>
</td>
</tr>
</table>
<table vspace=10>
<tr><td colspan=3 align=center><h2>Carpooling fact:</h2></td><tr>
<tr><td width=10%></td><td width=80% align=center>
<!-- BEGIN FACT_1 -->
Commuters driving 20 miles each way to work contribute 1.69 pounds 
of pollutants to the atmosphere each day. That is 405.6 pounds of 
hydrocarbons, carbon monoxide and nitrogen oxides each year.
<!-- END FACT_1 -->
<!-- BEGIN FACT_2 -->
If 100 people pair up into daily carpools they would keep 1,848 
pounds of hydrocarbons, 1,320 pounds of carbon monoxide, 792 pounds of 
nitrogen oxides, and 2,376,000 pounds of carbon dioxide from entering 
the atmosphere each year. They would also save 12,000 gallons of 
petrol. 
<!-- END FACT_2 -->
<!-- BEGIN FACT_3 -->
The Los Angeles area has the highest carpooling rate in the U.S. 
<!-- END FACT_3 -->
<!-- BEGIN FACT_4 -->
Every day carpoolers in southern California keep 134 tons of 
pollutants from entering the air.
<!-- END FACT_4 -->
<!-- BEGIN FACT_5 -->
Passenger vehicles are a major source of hydrocarbons, nitrogen
oxides and carbon monoxide, and traffic on the road accounts for about
40 percent of the pollution that contributes to groundlevel ozone - the
main ingredient in smog.
<!-- END FACT_5 -->
<!-- BEGIN FACT_6 -->
Only about 15% of the energy in the fuel you put in your gas tank gets used to move your car down the road or run accessories like air conditioning or power steering. The rest of the energy is lost: 62% to waste heat, the friction of moving engine parts or to pumping air into and out of the engine. In urban driving, another 17% is lost to idling at stoplights or in traffic.
<!-- END FACT_6 -->
<!-- BEGIN FACT_7 -->
The most effective way to reduce emissions from your vehicle is to use it less. Vehicle travel in this country is doubling every 20 years. Traffic trends that see more and more cars driving more and more miles will soon begin to outpace technological progress in vehicle emission control.
<!-- END FACT_7 -->
<!-- BEGIN FACT_8 -->
Every day, Americans use more energy and generate
more pollution in vehicular travel than they do in the production of all goods, the operation of all commercial enterprises, or the running of their homes.
<!-- END FACT_8 -->
<!-- BEGIN FACT_9 -->
In addition to being a lot more economical than driving alone, carpooling can help you... Balance work and family commitments. Develop a flexible commuting schedule with your fellow carpool members.
<!-- END FACT_9 -->
<!-- BEGIN FACT_10 -->
In addition to being a lot more economical than driving alone, carpooling can help you... Spend less time in traffic. Carpool vehicles get to destinations faster because they can travel in high occupancy vehicle (HOV) lanes on major highways.
<!-- END FACT_10 -->
<!-- BEGIN FACT_11 -->
In addition to being a lot more economical than driving alone, carpooling can help you... Relax more. Carpool members generally rotate driving duties. When you re a passenger, you can sit back and read, sip a cup of coffee, or even take a nap.
<!-- END FACT_11 -->
<!-- BEGIN FACT_12 -->
Why is carpooling better than driving alone?<br>
First of all, it saves you money. Commuting is expensive when you drive alone. Gas, tolls and parking can add up to thousands of dollars every year. By carpooling with just one other person, you can cut those costs in half. Add another person or two and your costs will be even lower   a small fraction of what they would have been had you continued driving alone.
<!-- END FACT_12 -->
<!-- BEGIN FACT_13 -->
Travelsmart can be used for much more than carpooling,
it can be used to put haulage firms & individuals wanting
goods hauled in contact, van pooling, even buying
& selling of transferable travel tickets, just add the
relevant details in the addiditional comments box in
the plan trip page.
<!-- END FACT_13 -->
<!-- BEGIN FACT_14 -->
A carpool between 2 people travelling to work for 1 year
which saves 30 miles travelling, assuming a 48 week work year,
a 5 day week saves assuming 40 cents a mile for petrol & vehicle
wear & tear, this is less than what can be claimed for milage
expenses from the government & much less than taxi fares,
saves apporoximately .40 x 48 x 5 x 30 = 2880 Euro and around
48 x 5 = 240 gallons of petrol, this vanpool
among 10 passengers would save 28,000 Euro.
<!-- END FACT_14 -->
</td><td width=10%></td></tr>
</table>
<!-- END PAGE_HEADER -->
<!-- BEGIN DISPLAY_STATS -->
<table vspace=10 align=center border=1>
<tr><td colspan=4 align=center><h2>Travelsmart Site Statistics</h2></td><tr>
<tr><td>Number Of Users Registered:</td><td>{NUMBER_OF_USERS_REGISTERED}</td>
<td>Number Of Matched Trips:</td><td>{NUMBER_OF_MATCHED_TRIPS}</td></tr>
<tr><td>Mileage Saved By Carpooling:</td><td>{TOTAL_MILEAGE_SAVED} KM</td>
<td>Fuel Saved By Carpooling:</td><td>{LITRES_OF_FUEL_SAVED} L</td></tr>
<tr><td>Carbon Footprint Saved By Carpooling:</td><td>{CARBON_FOOTPRINT_SAVED} KG of CO2</td>
<td>Stats Last Calculated On:</td><td>{CALC_TIME}</td></tr>
</table>
<!-- END DISPLAY_STATS -->
<!-- BEGIN PAGE_FOOTER -->
<p>
<center>
If you have any problems or queries feel free to <a href="javascript:;" onclick=popup('send_email.php?email_to={ADMINISTRATOR_EMAIL_ADDRESS}&email_subject=travelsmart%20-%20query')>contact the webmaster.</a>
</center>
</p>
<p>
<center><i>
Developed by Aria Enterprises <a href="javascript:;" onclick=popup('http://www.ariasoft.ie')>http://www.ariasoft.ie</a></i>
</center>
</p>
</center>
{GOOGLE_ANALYTICS}
</body>
</html>
<!-- END PAGE_FOOTER -->
