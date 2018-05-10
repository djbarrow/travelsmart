<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Tell a friend</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<form action="tell_a_friend.php" method=POST>
<table align=center>
<tr align=center>
<td colspan=2>
<h2>Tell a friend</h2>
</td>
</tr>
<tr>
<td>From:</td><td>{FROM}</td>
</tr>
</table>
<table align=center>
<!-- BEGIN ERROR_NO_EMAIL_ADDRESSES -->
<tr class=error>
<td></td>
<td>
You need to send the email to somebody!
</td>
</tr>
<!-- END ERROR_NO_EMAIL_ADDRESSES -->
<!-- BEGIN ERROR_INVALID_ADDRESS -->
<tr class=error>
<td></td>
<td>
Invalid email address {EMAIL_ADDRESS}
</td>
</tr>
<!-- END ERROR_INVALID_ADDRESS -->
<!-- BEGIN ERROR_INVALID_EMAIL_STRING -->
<tr class=error>
<td></td>
<td>
Invalid email string {EMAIL_STR}
</td>
</tr>
<!-- END ERROR_INVALID_EMAIL_STRING -->
<tr>
<td colspan=2>
You can enter multiple email addresses just seperate them by commas.
</td>
</tr>
<tr>
<td>To :</td>
<td><input name=to value="{TO}" type=text size=50 maxlength=1000></td>
</tr>
</table>
<table align=center>
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
<td><input name=subject value="{SUBJECT}" type=text size=50 maxlength=200></td>
</tr>
</table>
<table align=center>
<!-- BEGIN ERROR_NO_BODY -->
<tr class=error>
<td>
No email text.
</td>
</tr>
<!-- END ERROR_NO_BODY -->
<tr>
<td colspan=2>
<textarea name="email_body" rows=5 cols=80>
{EMAIL_BODY}
</textarea>
</td>
</tr>
<tr align=center>
<td><input type=submit value="Send email"></td>
</tr>
<tr align=center>
<td>Please be patient, sending the email might take a minute.</td>
</tr>
</table>
</form>
<center>
<a href="control_centre.php">Back To Control Centre</a>
</center>
{GOOGLE_ANALYTICS}
</body>
</html>
