<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>View Server Logs</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<h1 align=center>View Server Logs</h1>
<form action="init_view_server_logs.php" method=post>
<table align=center>
<tr>
<!-- BEGIN ERROR_INVALID_SQL_QUERY -->
<tr class=error>
<td>
Invalid SQL query suffix {QUERY_SUFFIX}
</td>
</tr>
<!-- END ERROR_INVALID_SQL_QUERY -->
<tr><td>Query :SELECT * FROM log </td></tr>
<td><input name=query_suffix value="{QUERY_SUFFIX}" type=text size=60 maxlength=300></td></tr>
<tr><td>ORDER BY log_time DESC LIMIT page_from,num_entries</td></tr>
<tr>
<td>Query Variables: user_id, ip_addr, log_time, log_entry</td></tr>
</table>
<center>
<input type=submit value="Submit Query">
</center>
<center>
<a href="control_centre.php">Back To Control Centre</a>
</center>
</form>
{GOOGLE_ANALYTICS}
</body>
</html>
