<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>
Ban Unban User
</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<h1 align=center>Change To User</h1>
<form action="change_to_user.php" method=post>
<table align=center>
<!-- BEGIN ERROR_UNKNOWN_USER_EMAIL_ADDRESS -->
<tr class=error>
<td></td>
<td>
unknown user email address
</td>
</tr>
<!-- END ERROR_UNKNOWN_USER_EMAIL_ADDRESS -->
<!-- BEGIN ERROR_INVALID_EMAIL_ADDRESS -->
<tr class=error>
<td></td>
<td>
invalid email address
</td>
</tr>
<!-- END ERROR_INVALID_EMAIL_ADDRESS -->
<!-- BEGIN ERROR_EMPTY_EMAIL_ADDRESS -->
<tr class=error>
<td></td>
<td>
empty email address
</td>
</tr>
<!-- END ERROR_EMPTY_EMAIL_ADDRESS -->
<tr>
<td>email address *:</td>
<td><input name=email_address value="{EMAIL_ADDRESS}" type=text size=30 maxlength=120></td>
<tr>
<td>Drop Administrator Privileges</td>
<td><input type=checkbox {CHECKED_drop_administrator_privileges_1} value=1 name=drop_administrator_privileges></td>
</tr>
<tr>
<td>Change User ID Only</td>
<td><input type=checkbox {CHECKED_change_user_id_only_1} value=1 name=change_user_id_only></td>
</tr>
</table>
<center>
<input type=submit value="Change To User">
<p>
<a href="control_centre.php">Back To Control Centre</a>
</p>
</center>
</form>
{GOOGLE_ANALYTICS}
</body>
</html>
