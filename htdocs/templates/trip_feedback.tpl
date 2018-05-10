<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Satisfaction feedback for {NAME}</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<center>
<h3>Satisfaction feedback for {NAME} for trip from {ORIGIN_LATLON_NAME} to {DESTINATION_LATLON_NAME} matched on {MATCH_TIME}.</h3>
<!-- BEGIN DISPLAY_PIC -->
<img SRC="{USER_IMAGE}" NOSAVE class=image_border>
<!-- END DISPLAY_PIC --> 
</center>
<table align=center>
<form name="process_trip_feedback" action="process_trip_feedback.php" method=POST>
<tr>
<input type=hidden name=tripmatch_id value={TRIPMATCH_ID}>
<td colspan=2>Satisfaction
{SATISFACTION_SELECT}
out of 10.
</td></tr>
<!-- BEGIN FINISHED_THIS_REGULAR_TRIP_CHECKBOX -->
<tr><td>
I have finished travelling on this regular trip <input type=checkbox {CHECKED_finished_this_regular_trip_1} value=1 name=finished_this_regular_trip>
</td></tr>
<!-- END FINISHED_THIS_REGULAR_TRIP_CHECKBOX -->
<tr><td>
Comments max 256 characters
</td></tr>
<tr>
<td colspan=2>
<textarea name="comments" rows=3 cols=80>
{COMMENTS}
</textarea>
</td>
</tr>
<tr>
<td><input type=submit value="Submit Feedback"></td>
<td><input type=reset value="Cancel" onclick="window.close()"></td>
</tr>
</form>
</table>
{GOOGLE_ANALYTICS}
</body>
</html>
