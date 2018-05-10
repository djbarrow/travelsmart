<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Delete Vehicle</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<center>
<h2>Select the vehicle you wish to remove from the database.</h2>
<table align=center>
<form action="delete_vehicle.php" method=GET>
<!-- BEGIN ERROR_VEHICLE_NOT_SELECTED -->
<tr class=error>
<td>
Vehicle not selected
</td>
</tr>
<!-- END ERROR_VEHICLE_NOT_SELECTED -->
<tr>
<td>
<select name="vehicle_id">
<!-- BEGIN VEHICLE -->
<option value={VEHICLE_ID}>{VEH_DES}  
<!-- END VEHICLE -->
</select>
</td>
</tr>
<tr>
<td align=center><input type=submit value="Delete Vehicle"></td>
</form>
<tr><td align=center>
<a href="control_centre.php">Back To Control Centre</a>
</td></tr>
</table>
</center>
{GOOGLE_ANALYTICS}
</body>
<html>
