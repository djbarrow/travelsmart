<!-- BEGIN USER_MATCH -->
<table align=center class={LIST_STYLE} width=100%>
<tr>
<td>
Name: {MATCH_NAME}
</td>
<td>
<!-- BEGIN HREFFED_EMAIL_ADDRESS -->
<!--
<a href="email_me.php?trip1_id={TRIP1_ID}&trip2_id={TRIP2_ID}">email {MATCH_EMAIL_ADDRESS}.</a>
-->
<a href="javascript:;" onclick=popup('email_me.php?trip1_id={TRIP1_ID}&trip2_id={TRIP2_ID}')>email {MATCH_EMAIL_ADDRESS}.</a>
<!-- END HREFFED_EMAIL_ADDRESS -->
<!-- BEGIN PLAIN_EMAIL_ADDRESS -->
email: {MATCH_EMAIL_ADDRESS}
<!-- END PLAIN_EMAIL_ADDRESS -->
</td>
<!-- BEGIN DISPLAY_PIC -->
<td ALIGN=CENTER>
<img SRC="{USER_IMAGE}" NOSAVE class=image_border>
</td>
<!-- END DISPLAY_PIC --> 
</tr>
<!-- BEGIN ADD_TO_SAVED_MATCHES -->
<tr>
<td colspan=2>
<!--
<a href="add_to_saved_matches.php?trip1_id={TRIP1_ID}&trip2_id={TRIP2_ID}">Get contact details & add to saved matches.</a>
-->
<a href="javascript:;" onclick=popup('add_to_saved_matches.php?trip1_id={TRIP1_ID}&trip2_id={TRIP2_ID}')>Get contact details & add to saved matches.</a>
</td>
</tr>
<!-- END ADD_TO_SAVED_MATCHES -->
<!-- BEGIN DISPLAY_USER_FEEDBACK -->
<tr>
<td colspan=2>
<!--
<a href="display_user_feedback.php?match_user_id={MATCH_USER_ID}">Display what other carpoolers thought about travelling with {MATCHNAME}.</a>
-->
<a href="javascript:;" onclick=popup('display_user_feedback.php?match_user_id={MATCH_USER_ID}')>Display what other carpoolers thought about travelling with {MATCHNAME}.</a>
</td>
</tr>
<!-- END DISPLAY_USER_FEEDBACK -->
<!-- BEGIN DISPLAY_NO_USER_FEEDBACK -->
<tr>
<td colspan=2>
No people who have travelled with {MATCHNAME} has yet given feedback on the trip.
</td>
</tr>
<!-- END DISPLAY_NO_USER_FEEDBACK -->
<tr>
<td>Distance per trip saved by carpooling {MILEAGE_SAVED}KM.</td>
<!-- BEGIN REGULAR_TRIPS_MATCHED -->
<td>Number of trips per week matched {NUM_REGULAR_TRIPS_MATCHED}.</td>
<!-- END REGULAR_TRIPS_MATCHED -->
<!-- BEGIN ONE_TIME_TRIPS_MATCHED -->
<td>Number of trips matched {NUM_ONE_TIME_TRIPS_MATCHED}.</td>
<!-- END ONE_TIME_TRIPS_MATCHED -->
</tr>
<!-- BEGIN PHONE_DETAILS -->
<tr>
<td>
Primary phone number: {MATCH_PRIMARY_PHONE_NUMBER}
</td>
<td>
<!-- BEGIN TRIP_SECONDARY_PHONE_NUMBER -->
Secondary phone number: {MATCH_SECONDARY_PHONE_NUMBER}
<!-- END TRIP_SECONDARY_PHONE_NUMBER -->
</td>
</tr>
<tr>
<!-- END PHONE_DETAILS -->
<td colspan=3>
<!-- BEGIN TRIP_DRIVER_AND_PASSENGER -->
I am a driver or passenger.
<!-- END TRIP_DRIVER_AND_PASSENGER -->
<!-- BEGIN TRIP_PASSENGER -->
I am a passenger.
<!-- END TRIP_PASSENGER -->
<!-- BEGIN TRIP_DRIVER -->
I am driving.
<!-- END TRIP_DRIVER -->
</td>
</tr>
<tr>
<td colspan=3>
<!-- BEGIN DAYS_TRAVELLING -->
I am travelling on
<!-- END DAYS_TRAVELLING -->
<!-- BEGIN TRIP_DAY0 -->
Sun
<!-- END TRIP_DAY0 -->
<!-- BEGIN TRIP_DAY1 -->
Mon
<!-- END TRIP_DAY1 -->
<!-- BEGIN TRIP_DAY2 -->
Tue
<!-- END TRIP_DAY2 -->
<!-- BEGIN TRIP_DAY3 -->
Wed
<!-- END TRIP_DAY3 -->
<!-- BEGIN TRIP_DAY4 -->
Thu
<!-- END TRIP_DAY4 -->
<!-- BEGIN TRIP_DAY5 -->
Fri
<!-- END TRIP_DAY5 -->
<!-- BEGIN TRIP_DAY6 -->
Sat
<!-- END TRIP_DAY6 -->
<!-- BEGIN TRIP_FULL_STOP -->
.
<!-- END TRIP_FULL_STOP -->
</td>
</tr>
<tr>
<td colspan=3>
I am travelling from {ORIGIN_LATLON_INFO} to {DESTINATION_LATLON_INFO}.
<a href="javascript:;" onclick=popup('{HTTP_HEADER}process_trip_map.php?trip1_id={TRIP1_ID}&trip2_id={TRIP2_ID}')>View trip map.</a>
<!--
<a href="{HTTP_HEADER}process_trip_map.php?trip1_id={TRIP1_ID}&trip2_id={TRIP2_ID}">View trip map.</a>
-->
<a href="javascript:;" onclick=popup('{HTTP_HEADER}get_directions.php?trip1_id={TRIP1_ID}&trip2_id={TRIP2_ID}')>Get Directions.</a>
<!--
<a href="{HTTP_HEADER}get_directions.php?trip1_id={TRIP1_ID}&trip2_id={TRIP2_ID}">Get Directions.</a>
-->
</td></tr>
<tr>
<td colspan=3>The earliest it is possible for me to start my trip
<!-- BEGIN TRIP_DATE -->
on {TRIP_DATE}
<!-- END TRIP_DATE -->
is {EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME} & the latest it is possible for me to
arrive at my destination is {LATEST_POSSIBLE_TRIP_ARRIVAL_TIME}.
I estimate this trip will take {EXPECTED_TRIP_DURATION}.
<!-- BEGIN RETURN_TRIP -->
I plan to return at eariest at {RETURN_EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME}
& arrive back at the trip origin at {RETURN_LATEST_POSSIBLE_TRIP_ARRIVAL_TIME}.
I estimate the return trip will take {RETURN_EXPECTED_TRIP_DURATION}.
<!-- END RETURN_TRIP -->
</td></tr>
<tr><td>
<!-- BEGIN VEHICLE_WILL_DRIVE -->
The vehicle I will be driving on this trip is a 
<!-- END VEHICLE_WILL_DRIVE -->
<!-- BEGIN VEHICLE_MAYBE_DRIVE -->
The vehicle I may be driving on this trip is a 
<!-- END VEHICLE_MAYBE_DRIVE -->
<!-- BEGIN VEHICLE_DESCRIPTION -->
{VEH_DES}.
<!-- END VEHICLE_DESCRIPTION -->
<!-- BEGIN DISPLAY_VEHICLE_PIC -->
<td ALIGN=CENTER>
<img SRC="{VEHICLE_IMAGE}" NOSAVE class=image_border>
</td>
<!-- END DISPLAY_VEHICLE_PIC -->
<!-- BEGIN ADDITIONAL_COMMENTS -->
<tr><td colspan=3>
Additional Comments: {ADDITIONAL_COMMENTS}
</td><tr>
<!-- END ADDITIONAL_COMMENTS -->
<!-- BEGIN ADDITIONAL_COMMENTS_FORM -->
<tr>
<td colspan=3 align=center>
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
</tr>
<tr>
<td colspan=3 align=center>
<textarea name="comments" rows=5 cols=80>
{ADDITIONAL_COMMENTS}
</textarea>
</td>
</tr>
<!-- END ADDITIONAL_COMMENTS_FORM -->
</td><tr>
</table>
<!-- END USER_MATCH -->
