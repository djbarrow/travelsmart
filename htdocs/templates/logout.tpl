<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Bye</title>
<link rel=stylesheet href=../{CSS_FILE}>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Logout</title>
</head>
<body>
{LOGOS}
<center>
<h1>Logout Page</h1>
<!-- BEGIN THANKS -->
<p><h3>Thank you {NAME} for using travelsmart.</h3>
<!-- END THANKS -->
<!-- BEGIN ERR_MSG -->
<p class=error_big>
{ERROR_MESSAGE}
</p>
<!-- END ERR_MSG -->
<p>Click <a href="index.php">here</a> to login in / go back to the homepage.</p>		
If you have any problems or queries feel free to <a href="javascript:;" onclick=popup('send_email.php?email_to={ADMINISTRATOR_EMAIL_ADDRESS}&email_subject=travelsmart%20-%20query')>contact the webmaster.</a>
</center>
</p>
{GOOGLE_ANALYTICS}
</body>
</html>
