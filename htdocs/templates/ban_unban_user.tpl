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
<h1 align=center>Ban Unban User</h1>
<form action="ban_unban_user.php" method=post>
<table align=center>
<!-- BEGIN DISPLAY_PIC -->
<tr>
<td>
<img SRC="{USER_IMAGE}" NOSAVE class=image_border>
</td>
</tr>
<!-- END DISPLAY_PIC --> 
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
<td>Banned By Login Authentication</td>
<td><input type=checkbox {CHECKED_banned_by_login_authentication_1} value=1 name=banned_by_login_authentication></td>
</tr>
<tr>
<td>Banned By Cookie</td>
<td><input type=checkbox {CHECKED_banned_by_cookie_1} value=1 name=banned_by_cookie></td>
</tr>
<tr>
<td>Banned By IP Address</td>
<td><input type=checkbox {CHECKED_banned_by_ip_address_1} value=1 name=banned_by_ip_address></td>
</tr>
</tr>
<td>
<input type=submit name="get_ban_user_options" value="Get Ban User Options">
</td>
<td>
<input type=submit name="set_ban_user_options" value="Set Ban User Options">
</td>
</tr>
</table>
<center>
<p>
<a href="control_centre.php">Back To Control Centre</a>
</p>
</center>
</form>
{GOOGLE_ANALYTICS}
</body>
</html>
