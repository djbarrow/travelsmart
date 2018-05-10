<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Add New Vehicle</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<h1 align=center>Add New Vehicle</h1>
<form enctype="multipart/form-data" action="add_new_vehicle.php" method=post>
<table align=center>
<tr>
<td>Vehicle Type:</td>
<td><select name="vehicle_type_id">
<!-- BEGIN VEHICLE_TYPE -->
<option value={VEHICLE_TYPE_ID} {VEHICLE_TYPE_ID_SELECTED}>{VEHICLE_TYPE_NAME}   
<!-- END VEHICLE_TYPE -->
</select>
</tr>
<!-- BEGIN ERROR_EMPTY_VEHICLE_MAKE -->
<tr class=error>
<td></td>
<td>
empty vehicle make
</td>
</tr>
<!-- END ERROR_EMPTY_VEHICLE_MAKE -->
<tr>
<td>Make :</td>
<td><input name=make value="{MAKE}" type=text size=30 maxlength=40></td>
</tr>
<!-- BEGIN ERROR_EMPTY_VEHICLE_MODEL -->
<tr class=error>
<td></td>
<td>
empty vehicle model
</td>
</tr>
<!-- END ERROR_EMPTY_VEHICLE_MODEL -->
<tr>
<td>Model :</td>
<td><input name=model value="{MODEL}" type=text size=30 maxlength=40></td>
</tr>
<!-- BEGIN ERROR_EMPTY_VEHICLE_COLOUR -->
<tr class=error>
<td></td>
<td>
empty vehicle colour
</td>
</tr>
<!-- END ERROR_EMPTY_VEHICLE_COLOUR -->
<tr>
<td>Colour :</td>
<td><input name=colour value="{COLOUR}" type=text size=30 maxlength=30></td>
</tr>
<tr>
<!-- BEGIN ERROR_EMPTY_VEHICLE_REGISTRATION_NUMBER -->
<tr class=error>
<td></td>
<td>
empty vehicle registration number
</td>
</tr>
<!-- END ERROR_EMPTY_VEHICLE_REGISTRATION_NUMBER -->
<!-- BEGIN ERROR_VEHICLE_ALREADY_EXISTS -->
<tr class=error>
<td></td>
<td>
A vehicle with this registration number already exists
</td>
</tr>
<!-- END ERROR_VEHICLE_ALREADY_EXISTS -->
<td>Vehicle Registration Number:</td>
<td><input name=vehicle_registration_number value="{VEHICLE_REGISTRATION_NUMBER}" type=text size=30 maxlength=20></td
</tr>
</table>
<table>
<tr align=center>
<td>All above fields are required.</td>
</tr>
<tr align=center><td>
Adding a picture of your vehicle is optional, it is used so that you can vehicle be recognised by people travelling with you & make pick ups easier, it is also used for security. 
</td><tr>
<tr align=center><td>
<input type="hidden" name="MAX_FILE_SIZE" value="16777216" > Upload Image: <input type="file" name="imgfile">
Click browse to upload a local jpeg image of your vehicle.
</td></tr>
</table>
<center>
<input type=submit value="Add New Vehicle">
</center>
<!-- BEGIN BACK_TO_CONTROL_CENTRE -->
<center>
<a href="control_centre.php">Back To Control Centre</a>
</center>
<!-- END BACK_TO_CONTROL_CENTRE -->
</form>
{GOOGLE_ANALYTICS}
</body>
</html>
