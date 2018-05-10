<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Plan Trip</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<center>
<table>
<tr>
<td colspan=3 align=center>
<h2>Plan Trip</h2>
</td>
<tr>
<form action="plan_trip.php" method=POST>
<!-- BEGIN ERROR_DRIVER_PASSENGER_UNSELECTED -->
<tr class=error>
<td colspan=3>
Please select driver only, passenger only, driver or passenger.
</td>
</tr>
<!-- END ERROR_DRIVER_PASSENGER_UNSELECTED -->
<tr>
<td align=left>I am the driver/I will transport goods:<input type=radio {CHECKED_driver_passenger_d} value=d name=driver_passenger></td>
<td align=center>I am the passenger/I want goods transported:<input type=radio {CHECKED_driver_passenger_p} value=p name=driver_passenger></td>
<td align=right>I can drive or be a passenger/have goods transported for me:<input type=radio {CHECKED_driver_passenger_b} value=b name=driver_passenger></td>
</tr>
</table>
<table>
<!-- BEGIN ERROR_TRIP_TYPE_UNSELECTED -->
<tr class=error>
<td colspan=2>
Please select whether this is a one time trip or a regular trip.
</td>
</tr>
<!-- END ERROR_TRIP_TYPE_UNSELECTED -->
<tr>
<td>One time trip:<input type=radio {CHECKED_regular_trip_n} value=n name=regular_trip></td>
<td>Regular trip:<input type=radio {CHECKED_regular_trip_y} value=y name=regular_trip></td>
</tr>
</table>
<table>
<tr>
<td>
<table>
<!-- BEGIN ERROR_DATE_INVALID -->
<tr class=error>
<td colspan=3>
Date invalid
</td>
</tr>
<!-- END ERROR_DATE_INVALID -->
<!-- BEGIN ERROR_DATE_IS_IN_PAST -->
<tr class=error>
<td colspan=3>
Date is in the past.
</td>
</tr>
<!-- END ERROR_DATE_IS_IN_PAST -->
<tr>
<td>Trip Date:
<td>{TRIP_DATE_DAY_SELECT}</td>
<td>{TRIP_DATE_MONTH_SELECT}</td>
<td>{TRIP_DATE_YEAR_SELECT}</td>
</td>
</tr>
</table>
<td>
<table>
<tr>
<td>Trip Days:</td>
<td>Sunday<br><input type=checkbox {CHECKED_day1_1} value=1 name=day1></td>
<td>Monday<br><input type=checkbox {CHECKED_day2_1} value=1 name=day2></td>
<td>Tuesday<br><input type=checkbox {CHECKED_day3_1} value=1 name=day3></td>
<td>Wednesday<br><input type=checkbox {CHECKED_day4_1} value=1 name=day4></td>
<td>Thursday<br><input type=checkbox  {CHECKED_day5_1} value=1 name=day5></td>
<td>Friday<br><input type=checkbox {CHECKED_day6_1} value=1 name=day6></td>
<td>Saturday<br><input type=checkbox {CHECKED_day7_1} value=1 name=day7></td>
</tr>
</table>
</td>
</tr>
</table>
<table>
<!-- BEGIN ERROR_TRIP_ORIGIN_UNSELECTED -->
<tr class=error>
<td colspan=2>
Trip origin not selected
</td>
</tr>
<!-- END ERROR_TRIP_ORIGIN_UNSELECTED -->
<tr>
<td>Trip Origin:</td>
<td><select name="trip_origin_location_id">
<!-- BEGIN TRIP_ORIGIN -->
<option value={TRIP_ORIGIN_LOCATION_ID} {TRIP_ORIGIN_LOCATION_ID_SELECTED}>{TRIP_ORIGIN_LOCATION_NAME}   
<!-- END TRIP_ORIGIN -->
</select>
</td>
<td>
<input type=submit name="add_new_trip_origin" value="Add New Trip Origin"></td>
</td>
</tr>
<!-- BEGIN ERROR_TRIP_DESTINATION_UNSELECTED -->
<tr class=error>
<td colspan=2>
Trip destination unselected.
</td>
</tr>
<!-- END ERROR_TRIP_DESTINATION_UNSELECTED -->
<tr>
<td>Trip Destination:</td>
<td><select name="trip_destination_location_id">
<!-- BEGIN TRIP_DESTINATION -->
<option value={TRIP_DESTINATION_LOCATION_ID} {TRIP_DESTINATION_LOCATION_ID_SELECTED}>{TRIP_DESTINATION_LOCATION_NAME}   
<!-- END TRIP_DESTINATION -->
</select>
</td>
<td>
<input type=submit name="add_new_trip_destination" value="Add New Trip Destination"></td>
</td>
</tr>
<!-- BEGIN ERROR_VEHICLE_UNSELECTED -->
<tr class=error>
<td colspan=2>
Vehicle unselected
</td>
</tr>
<!-- END ERROR_VEHICLE_UNSELECTED -->
<tr>
<td>Trip Vehicle:</td>
<td><select name="vehicle_id">
<!-- BEGIN VEHICLE -->
<option value={VEHICLE_ID} {VEHICLE_ID_SELECTED}>{VEH_DES}
<!-- END VEHICLE -->
</select>
</td>
<td>
<input type=submit name="add_new_vehicle" value="Add New Vehicle"></td>
</td>
</tr>
<!-- BEGIN ERROR_INCONSISTENT_TRIP_TIMES -->
<tr class=error>
<td colspan=2>
Inconsistent trip times (earliest possible trip departure time + estimated trip duration) > latest possible trip arrival time.
</td>
</tr>
<!-- END ERROR_INCONSISTENT_TRIP_TIMES -->
<tr>
<td>Earliest Possible Trip Departure Time:</td>
<td>{EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME_SELECT}</td>
</tr>
<tr>
<td>Latest Possible Trip Arrival Time:</td>
<td>{LATEST_POSSIBLE_TRIP_ARRIVAL_TIME_SELECT}</td>
</tr>
<tr>
<td>Expected Trip Duration:</td>
<td>{EXPECTED_TRIP_DURATION_MINS_SELECT}</td> 
</tr>
</table>
<table>
<!-- BEGIN ERROR_IS_RETURN_UNSELECTED -->
<tr class=error>
<td colspan=2>
Please select whether this is a one way trip or a return trip.
</td>
</tr>
<!-- END ERROR_IS_RETURN_UNSELECTED -->
<tr>
<td>One Way Trip:<input type=radio {CHECKED_is_return_trip_n} value=n name=is_return_trip></td>
<td>Return Trip:<input type=radio {CHECKED_is_return_trip_y} value=y name=is_return_trip></td>
</tr>
</table>
<table>
<!-- BEGIN ERROR_RETURN_TRIP_INCONSISTENT_TRIP_TIMES -->
<tr class=error>
<td colspan=2>
Inconsistent trip times (return trip earliest possible trip departure time + return trip estimated trip duration) > return trip latest possible trip arrival time.
</td>
</tr>
<!-- END ERROR_RETURN_TRIP_INCONSISTENT_TRIP_TIMES -->
<tr>
<td>Return Trip Earliest Possible Trip Departure Time:</td>
<td>{RETURN_TRIP_EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME_SELECT}</td>
</tr>
<tr>
<td>Return Trip Latest Possible Trip Arrival Time:</td>
<td>{RETURN_TRIP_LATEST_POSSIBLE_TRIP_ARRIVAL_TIME_SELECT}</td>
</tr>
<tr>
<td>Return Trip Expected Trip Duration:</td>
<td>{RETURN_TRIP_EXPECTED_TRIP_DURATION_MINS_SELECT}</td> 
</tr>
</table>
</center>
<table align=center>
<tr>
<td width=10%></td><td width=80% align=center>
<br>
Additional comments to go in emails sent to matches, max 1024 characters:<br>
Tip: Comments here can be used to indicate 
that this is not a car pool e.g. if you are a truck driver
with a half empty refrigerated container after a delivery.
For freight company reg. no's & VAT details may go into the comments.
The number of free seats you have available in your vehicle,
your smoking preferences, the fee you will charge for the carpool
or additional details wrt to the pick up.
</td>
<td width=10%></td>
</tr>
</table>
<table align=center>
<tr>
<td>
<textarea name="comments" rows=5 cols=80>
{COMMENTS}
</textarea>
</td>
</tr>
<tr>
<table align=center>
<tr>
<td><input type=submit name="find_trip_matches" value="Find Trip Matches"></td>
</tr>
<!-- BEGIN BACK_TO_CONTROL_CENTRE -->
<tr>
<td><a href="control_centre.php">Back To Control Centre</a></td>
</tr>
<!-- END BACK_TO_CONTROL_CENTRE -->
</table>
</tr>
</form>
</table>
{GOOGLE_ANALYTICS}
</body>
</html>
