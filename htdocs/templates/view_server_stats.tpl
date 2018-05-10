<!-- BEGIN PAGE_HEADER -->
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Server Stats</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<center>
<h1>Server Stats</h1><br>
</center>
<form action="view_server_stats.php" method=GET>
<table align=center>
<tr>
<td>Search Period:</td>
<td>{SEARCH_PERIOD_SELECT}</td>
</tr>
</table>
<!-- END PAGE_HEADER -->
<!-- BEGIN DISPLAY_STATS -->
<table align=center border=1>

<tr><td colspan=2>All Stats Displayed Are For The Selected Period</td></tr>
<tr><td>Number Of Users Registered:</td><td>{NUMBER_OF_USERS_REGISTERED}</td></tr>
<tr><td>Number Of Matched Trips:</td><td>{NUMBER_OF_MATCHED_TRIPS}</td></tr>
<tr><td>Mileage Saved By Carpooling:</td><td>{TOTAL_MILEAGE_SAVED} KM</td></tr>
<tr><td>Fuel Saved By Carpooling:</td><td>{LITRES_OF_FUEL_SAVED} L</td></tr>
<tr><td>Carbon Footprint Saved By Carpooling:</td><td>{CARBON_FOOTPRINT_SAVED} KG of CO2</td></tr>
<tr><td>Total Number Of Trip Entries In Database :</td><td>{NUMBER_OF_TRIPS}</td></tr>
<tr><td>Number Of Regular Trip Entries In Database:</td><td>{NUMBER_OF_REGULAR_TRIPS}</td></tr>
<tr><td>Number Of One Time Trips Entries In Database:</td><td>{NUMBER_OF_ONE_TIME_TRIPS}</td></tr>
<tr><td>Number Of Regular Trip Journeys Completed:</td><td>{NUMBER_OF_MATCHED_REGULAR_TRIPS}</td></tr>
<tr><td>Number Of One Time Trip Journeys Matched:</td><td>{NUMBER_OF_ONE_TIME_TRIPS_MATCHED}</td></tr>
<tr><td>Number Of One Time Trip Journeys Completed:</td><td>{NUMBER_OF_ONE_TIME_TRIPS_COMPLETED}</td></tr>
<tr><td>Number Of Searchable Regular Trip Entries In Database:</td><td>{NUMBER_OF_SEARCHABLE_REGULAR_TRIPS}</td></tr>
<tr><td>Number Of Unexpired One Time Trips Entries In Database:</td><td>{NUMBER_OF_UNEXPIRED_ONE_TIME_TRIPS}</td></tr>
<tr><td>Number Of Unexpired Searchable Active One Time Entries In Database:</td><td>{NUMBER_OF_UNEXPIRED_SEARCHABLE_ONE_TIME_TRIPS}</td></tr>
</table>
<table align=center border=1>
<tr>
<td>Vehicle Type</td>
<td>Num. One Time Trips Done</td>
<td>Num. Return Trips Done</td>
<td>Total Mileage Saved KM</td>
<td>Fuel Saved L</td>
<td>Carbon Footprint Saved KG of CO2</td>
<tr>
<!-- BEGIN VEHICLE_STATS -->
<tr>
<td>{VEHICLE_TYPE}</td>
<td>{VEH_NUM_ONE_TIME_TRIPS}</td>
<td>{VEH_NUM_REGULAR_TRIPS}</td>
<td>{VEH_TOTAL_MILEAGE_SAVED}</td>
<td>{VEH_LITRES_OF_FUEL_SAVED}</td>
<td>{VEH_CARBON_FOOTPRINT_SAVED}</td>
<tr>
<!-- END VEHICLE_STATS -->
</table>
<!-- END DISPLAY_STATS -->
<!-- BEGIN PAGE_FOOTER -->
<center>
<input type=submit value="Get Stats">
</center>
</form>
<center>
<a href="control_centre.php">Back To Control Centre</a>
</center>
{GOOGLE_ANALYTICS}
</body>
<html>
<!-- END PAGE_FOOTER -->
