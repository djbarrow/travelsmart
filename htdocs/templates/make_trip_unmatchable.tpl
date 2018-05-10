<!-- BEGIN USER_MATCH -->
<table align=center class={LIST_STYLE} width=100%>
<tr>
<td>
Trip Entry Creation Time: {CREATE_TIME}
</td>
<tr>
<td>
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
<!-- BEGIN TRIP_SOURCE_DEST -->
<tr><td>
I am travelling from {ORIGIN_LATLON_INFO} to {DESTINATION_LATLON_INFO}.
</td></tr>
<!-- END TRIP_SOURCE_DEST -->
<tr>
<td>The earliest it is possible for me to start my trip
<!-- BEGIN TRIP_DATE -->
on {TRIP_DATE}
<!-- END TRIP_DATE -->
is {EARLIEST_POSSIBLE_TRIP_DEPARTURE_TIME} & the latest it is possible for me to
arrive at my destination is {LATEST_POSSIBLE_TRIP_ARRIVAL_TIME}.
</td></tr>
<tr><td>
<center>
<!-- BEGIN MAKE_TRIP_UNMATCHABLE -->
<a href="process_make_trip_unmatchable.php?trip_id={TRIP_ID}">Make This Trip Unmatchable</a>
<!-- END MAKE_TRIP_UNMATCHABLE -->
<!-- BEGIN REFINE_THIS_TRIP_SEARCH -->
<a href="process_refine_trip_search.php?trip_id={TRIP_ID}">Refine This Trip Search</a>
<!-- END REFINE_THIS_TRIP_SEARCH -->
</center>
</td></tr>
</table>
<!-- END USER_MATCH -->
