<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Delete Vehicle</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<center>
<table>
<tr><td colspan=3></td>
<h2>Select the location you wish to remove from the database.</h2>
</td></tr>
<tr><td width=10%></td><td width=80% align=center>
Please note you cannot delete your home address location,
if you need to alter this please insert a new home location in
the Edit User Info option from the control centre, &
then come back here to delete the old location.
</td><td width=10%></td></tr>
</table>
<table>
<form action="delete_location.php" method=GET>
<!-- BEGIN ERROR_LOCATION_NOT_SELECTED -->
<tr class=error>
<td>
Location not selected
</td>
</tr>
<!-- END ERROR_LOCATION_NOT_SELECTED -->
<tr>
<td>
<select name="location_id">
<!-- BEGIN LOCATION -->
<option value={LOCATION_ID}>{LOCATION_NAME}  
<!-- END LOCATION -->
</select>
</td>
</tr>
<tr>
<td align=center><input type=submit value="Delete Location"></td>
</form>
<tr><td align=center>
<a href="control_centre.php">Back To Control Centre</a>
</td></tr>
</table>
</center>
{GOOGLE_ANALYTICS}
</body>
<html>
