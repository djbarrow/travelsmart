<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Travelsmart Control Centre</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<script language="JavaScript">
function popup(url) {
window.open(url, '', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1');
}
</script>
<body>
{LOGOS}
<table align=center>
<tr>
<!-- BEGIN WELCOME -->
<td>
<h2>Travelsmart Control Centre<br> Welcome {NAME}</h2>
</td>
<!-- END WELCOME -->
<!-- BEGIN DISPLAY_PIC -->
<td ALIGN=CENTER>
<img SRC="{USER_IMAGE}" NOSAVE class=image_border>
</td>
<!-- END DISPLAY_PIC --> 
</tr>
</table>
<center>
<a href="javascript:;" onclick=popup('carpooling_tips.php')>Carpooling Tips</a></center>
<table align=center>
<!-- BEGIN USER_OPTIONS -->
<tr><td>
<h2>User Options</h2>
</td><tr>
<!-- END USER_OPTIONS --> 
<tr><td>
<a href="plan_trip.php">Plan Trip</a>
</td></tr>
<!-- BEGIN VIEW_SAVED_TRIPMATCHES -->
<tr><td>
<a href="view_saved_tripmatches.php">View Saved Trip Matches & Give Satisfaction Feedback On Your Partner For Completed Trips.</a>
</td></tr>
<!-- END VIEW_SAVED_TRIPMATCHES -->
<!-- BEGIN VIEW_LAST_TRIPSEARCH -->
<tr><td>
<a href="display_trip_matches.php">View Last Trip Search Results</a>
</td></tr>
<!-- END VIEW_LAST_TRIPSEARCH -->
<tr><td>
<a href="add_edit_user.php?edit=1">Edit User Info</a>
</td></tr>
<!-- BEGIN REMOVE_MATCHABLE_TRIPS -->
<tr><td>
<a href="make_trips_unmatchable.php">Make Some Of Your Trips No Longer Matchable</a>
</td></tr>
<!-- END REMOVE_MATCHABLE_TRIPS -->
<!-- BEGIN REFINE_OLD_TRIP_SEARCHES -->
<tr><td>
<a href="refine_old_trip_searches.php">Refine Old Trip Searches</a>
</td></tr>
<!-- END REFINE_OLD_TRIP_SEARCHES -->
<!-- BEGIN INTERNAT_EDITION -->
<tr><td>
<a href="add_new_location_int.php">Add New Location</a>
</td></tr>
<!-- END INTERNAT_EDITION -->
 <!-- BEGIN NATIONAL_EDITION -->
<tr><td>
<a href="add_new_location.php">Add New National Location</a>
</td></tr>
<tr><td>
<a href="add_new_location_int.php">Add New International Location</a>
</td></tr>
<!-- END NATIONAL_EDITION -->
<!--  BEGIN DELETE_LOCATION -->
<tr><td>
<a href="delete_location.php">Delete Location</a>
</td></tr>
<!-- END DELETE_LOCATION -->
<tr><td>
<a href="add_new_vehicle.php">Add New Vehicle</a>
</td></tr>
<!-- BEGIN DELETE_VEHICLE -->
<tr><td>
<a href="delete_vehicle.php">Delete Vehicle</a>
</td></tr>
<!-- END DELETE_VEHICLE -->
<tr><td>
<h2></h2>
</td></tr>
<tr><td>
<a href="tell_a_friend.php">Tell A Friend About This Really Useful Website</a>
</td></tr>
<tr><td>
<a href="flyers.php">Promote TravelSmart, flyers here in word format to print out & put on bulletin boards</a>
</td><tr>
<tr><td>
<a href="logout.php">Logout</a>
</td></tr>
<tr><td>
<a href="send_email.php?email_to={ADMINISTRATOR_EMAIL_ADDRESS}&email_subject=travelsmart%20-%20complaint">Send A Complaint To The System Administrator</a>
</td></tr>
<tr><td>
<a href="send_email.php?email_to={ADMINISTRATOR_EMAIL_ADDRESS}&email_subject=travelsmart%20-%20general%20query">Send A General Query To The System Administrator</a>
</td></tr>
<tr><td>
<a href="send_email.php?email_to={DEVELOPER_EMAIL_ADDRESS}&email_subject=travelsmart%20-%20enhancement%20request">Send An Enhancement Request To The Developer</a>
</td></tr>
<tr><td>
<a href="send_email.php?email_to={DEVELOPER_EMAIL_ADDRESS}&email_subject=travelsmart%20-%20bug%20report">Report A Bug To The Developer</a>
</td></tr>
<!-- BEGIN ADMINISTRATOR_OPTIONS -->
<tr><td>
<h2>Administrator Options</h2>
</td><tr>
<tr><td>
<a href="ban_unban_user.php">Ban-Unban User</a>
</td></tr>
<tr><td>
<a href="email_group.php">Email A Group</a>
</td></tr>
<tr><td>
<a href="email_all_users.php">Email All Users</a>
</td></tr>
<tr><td>
<a href="change_to_user.php">Change To User</a>
</td></tr>
<tr><td>
<a href="view_server_stats.php">View Server Stats</a>
</td></tr>
<tr><td>
<a href="init_view_server_logs.php">View Server Logs</a>
</td></tr>	
<!-- END ADMINISTRATOR_OPTIONS -->
<!-- BEGIN REVERT_TO_USUAL_ADMINISTRATOR -->
<tr><td>
<a href="revert_to_usual_administrator.php">Revert To Usual Administrator</a>
</td></tr>
<!-- END REVERT_TO_USUAL_ADMINISTRATOR -->
</table>
{GOOGLE_ANALYTICS}
</body>
</html>
