<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Forgot Password</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<table align=center>
<form name="forgot_password" action="forgot_password.php" method=post>
<tr>
<td colspan=2 align=center>
<h1>Forgot Password</h1>
</td>
</tr>
<!-- BEGIN ERROR_EMAIL_ADDRESS_NOT_KNOWN -->
<tr class=error>
<td></td>
<td>
email address not known to system
</td>
</tr>
<!-- END ERROR_EMAIL_ADDRESS_NOT_KNOWN -->
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
<td>email address:</td>
<td><input name=email_address value="{EMAIL_ADDRESS}" type=text size=30 maxlength=120></td>
</tr>
<tr>
<td align=center colspan=2><input type=submit value="Send Forgot Password Email"></td>
</tr>
<tr><td colspan=2>
You might need to wait a minute after submission for an email to be sent to you, please be patient.
</td></tr>
<form>
</table>
<center>
<a href="index.php">Back To Login Page</a>
</center>
{GOOGLE_ANALYTICS}
</body>
</html>
