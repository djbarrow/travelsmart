<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Send Test Tripmatch Pages</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<center>
<table align=center>
<form name="send_testpage" action="send_testpage.php" method=post>
<tr>
<td colspan=2 align=center>
<h1>Send Test Tripmatch Pages</h1>
</td>
</tr>
<tr>
<td colspan=2 align=center>
This page is to be used to send test tripmatch pages
to your email address to confirm that you can receive
emails from travelsmart.
</td>
</tr>
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
<tr align=center>
<td>email address:</td>
<td><input name=email_address value="{EMAIL_ADDRESS}" type=text size=30 maxlength=120></td>
</tr>
<tr>
<td align=center colspan=2><input type=submit value="Send Test Tripmatch Emails"></td>
</tr>
<tr><td align=center colspan=2>
You might need to wait a minute after submission for the emails to be sent to you, please be patient.
</td></tr>
<form>
</table>
<a href="index.php">Back To Login Page</a>
</center>
{GOOGLE_ANALYTICS}
</body>
</html>
