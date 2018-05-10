<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Email all users</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<form action="email_all_users.php" method=POST>
<table align=center>
<tr align=center>
<td colspan=2>
<h2>Email all users</h2>
</td>
</tr>
<tr>
<td>From:</td><td>{FROM}</td>
</tr>
</table>
<table align=center>
<tr>
<td>To :</td>
<td>
all users
</td>
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
<td>Please be patient, sending the email might take a minute per user, i.e hours.</td>
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
