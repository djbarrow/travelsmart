<!-- BEGIN PAGE_PART_ONE -->
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>{TITLE}</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<script language="JavaScript">
function popup(url) {
window.open(url, '', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1');
}
</script>
<body>
{LOGOS}
<!-- END PAGE_PART_ONE -->
<!-- BEGIN PAGE_PART_TWO -->
<center>
<h1>{TITLE}</h1>
<!-- BEGIN REFINE_TRIP_SEARCH_CRITERIA -->
<a href="refine_plan_trip.php">Refine Trip Search Criteria</a><br>
<!-- END REFINE_TRIP_SEARCH_CRITERIA -->
<!-- BEGIN NOT_LOGGED_IN -->
You will need to login or register as a new user to get contact details of trip matches.<br>
<a href="index.php">Login Now</a><br>
<a href="add_edit_user.php">Register Now</a><br>
<!-- END NOT_LOGGED_IN -->
</center>
<!-- END PAGE_PART_TWO -->
<!-- BEGIN EMAIL_SUBJECT -->
<center>
<h3>You are about to send the following email to {EMAIL_TO}.<h3><br>
<h3>{EMAIL_SUBJECT}<h3>
</center>
<!-- END EMAIL_SUBJECT -->
<!-- BEGIN SEND_CANCEL_EMAIL_PART1 -->
<form name="process_email_me" action="process_email_me.php" method=POST>
<input type=hidden name=trip1_id value={TRIP1_ID}>
<input type=hidden name=trip2_id value={TRIP2_ID}>
<!-- END SEND_CANCEL_EMAIL_PART1 -->
<!-- BEGIN TRIP_NO_MATCHES_FOUND -->
<center>
<h2>Sorry, no trip matches found.</h2>
<table>
<tr><td width=10%></td><td width=80% align=center>
<b>Don't worry,</b> as soon as another carpooler registers a compatible trip
they can contact you via this website
<!-- BEGIN TRIP_NO_MATCHES_USER_TEMPORARY -->
,however, you need to login or register via the links above
to make this trip searchable.
<!-- END TRIP_NO_MATCHES_USER_TEMPORARY -->
<td width=10%></td></tr>
</table>
</center>
<!-- END TRIP_NO_MATCHES_FOUND -->
<!-- BEGIN NO_LOG_ENTRIES -->
<center>
<h2>Sorry, no log entries.</h2>
</center>
<!-- END NO_LOG_ENTRIES -->
<!-- BEGIN SET_PAGELINKS -->
<center>
{SET_PAGELINKS}
</center>
<!-- END SET_PAGELINKS -->
<!-- BEGIN BACK_TO_SQL_QUERY -->
<center>
<a href="init_view_server_logs.php">Back To SQL Query</a>
</center>
<!-- END BACK_TO_SQL_QUERY -->
<!-- BEGIN BACK_TO_CONTROL_CENTRE -->
<center>
<a href="control_centre.php">Back To Control Centre</a>
</center>
<!-- END BACK_TO_CONTROL_CENTRE -->
<!-- BEGIN GO_TO_CONTROL_CENTRE -->
<center>
<a href="control_centre.php">Go To Control Centre</a>
</center>
<!-- END GO_TO_CONTROL_CENTRE -->
<!-- BEGIN SEND_CANCEL_EMAIL_PART2 -->
<table align=center>
<tr>
<td><input type=submit value="Send email" onclick="send_email()"></td>
<td><input type=reset value="Cancel" onclick="window.close()"></td>
</tr>
<tr>
<td>Please be patient, sending the email might take a minute.</td>
</tr>
</table>
</form>
<!-- END SEND_CANCEL_EMAIL_PART2 -->
<!-- BEGIN EMAIL_SENT_SUCCESSFULLY0 -->
<center>
<p>
The email to {EMAIL_TO} has been sent successfully.
</p>
<a href="control_centre.php">Back To Control Centre</a>
</center>
<!-- END EMAIL_SENT_SUCCESSFULLY0 -->
<!-- BEGIN EMAIL_SENT_SUCCESSFULLY1 -->
<center>
<p>
The email to {EMAIL_TO} has been sent successfully.
</p>
<a href="javascript:;" onclick=window.close()>Close Window</a>
</center>
<!-- END EMAIL_SENT_SUCCESSFULLY1 -->
<!-- BEGIN SATISFACTION_PROCESSED_SUCCESSFULLY -->
<center>
The satisfaction feedback for {NAME} has been processed successfully.
<p>
<a href="javascript:;" onclick=window.close()>Close Window</a>
</p>
</center>
<!-- END SATISFACTION_PROCESSED_SUCCESSFULLY -->
<!-- BEGIN ADDED_TO_SAVED_MATCHES_SUCCESSFULLY -->
<center>
<h3>The trip with {NAME} has been added to your saved matches successfully.</h3></center>
<!-- END ADDED_TO_SAVED_MATCHES_SUCCESSFULLY -->
<!-- BEGIN CLOSE_WINDOW_LINK -->
<center>
<a href="javascript:;" onclick=window.close()>Close Window</a>
</center>
<!-- END CLOSE_WINDOW_LINK -->
<!-- BEGIN PAGE_FOOTER -->
{GOOGLE_ANALYTICS}
</body>
</html>
<!-- END PAGE_FOOTER -->
