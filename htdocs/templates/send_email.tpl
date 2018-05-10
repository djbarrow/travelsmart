<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Send Developer Email</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<form action="send_email.php" method=POST>
<table align=center>
<tr>
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
<td>From:</td>
<!-- BEGIN ENTER_EMAIL_ADDRESS -->
<td><input name=email_address value="{EMAIL_FROM}" type=text size=30 maxlength=120></td>
<!-- END ENTER_EMAIL_ADDRESS -->
<!-- BEGIN USER_EMAIL_ADDRESS -->
<td>{EMAIL_FROM}</td>
<!-- END USER_EMAIL_ADDRESS -->
</tr>
<tr>
<td>To:</td>
<td>{EMAIL_TO}</td>
</tr>
<input type=hidden name=email_to value="{EMAIL_TO}">
<!-- BEGIN ERROR_NO_SUBJECT -->
<tr class=error>
<td></td>
<td>
No subject.
</td>
</tr>
<!-- END ERROR_NO_SUBJECT -->
<tr>
<td>Subject :</td>
<td><input name=email_subject value="{EMAIL_SUBJECT}" type=text size=50 maxlength=200></td>
</tr>
<!-- BEGIN ERROR_NO_BODY -->
<tr class=error>
<td>
No email text.
</td>
</tr>
<!-- END ERROR_NO_BODY -->
<tr>
<td colspan=2>
<textarea name="email_body" rows=5 cols=80>{EMAIL_BODY}</textarea>
</td>
</tr>
<tr><td colspan=2>
<center>
<input type=submit value="Send Email"><br>
Please be patient after submission,<br> 
sending the email might take a minute.
</center>
</td></tr>
</table>
</form>
<!-- BEGIN BACK_TO_CONTROL_CENTRE -->
<center>
<a href="control_centre.php">Back To Control Centre</a>
</center>
<!-- END BACK_TO_CONTROL_CENTRE -->
<!-- BEGIN CLOSE_WINDOW_LINK -->
<center>
<a href="javascript:;" onclick=window.close()>Close Window</a>
</center>
<!-- END CLOSE_WINDOW_LINK -->
{GOOGLE_ANALYTICS}
</body>
</html>
