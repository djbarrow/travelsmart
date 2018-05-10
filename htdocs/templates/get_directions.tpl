<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Get Directions</title>
<link rel=stylesheet href={CSS_FILE}>
<body>
{LOGOS}
<center>
<h2>Which trip directions do you want?</h2>
</center>
<form name="get_directions" action="get_directions.php" method=post>
<table align=center border=1>
<!-- BEGIN ERROR_OPTION_NOT_SELECTED -->
<tr class=error_big align=center>
<td colspan=2>
Option not selected.
</td>
</tr>
<!-- END ERROR_OPTION_NOT_SELECTED -->
<!-- BEGIN OPTION_1 --> 
<tr>
<td>{NAME1} driving on outward journey from {ORIGIN1} to {DEST1},<br> {NAME2} is a passenger on outward journey from {ORIGIN2} to {DEST2}.<input type=radio {CHECKED_option_1} value=1 name=option><br></td>
</tr>
<!-- END OPTION_1 -->
<!-- BEGIN OPTION_2 --> 
<tr>
<td>{NAME1} driving on return journey from {DEST1} to {ORIGIN1},<br> {NAME2} is a passenger on outward journey from {ORIGIN2} to {DEST2}.<input type=radio {CHECKED_option_2} value=2 name=option><br></td>
</tr>
<!-- END OPTION_2 -->
<!-- BEGIN OPTION_3 --> 
<tr>
<td>{NAME1} driving on return journey from {DEST1} to {ORIGIN1},<br> {NAME2} is a passenger on return journey from {DEST2} to {ORIGIN2}.<input type=radio {CHECKED_option_3} value=3 name=option><br></td>
</tr>
<!-- END OPTION_3 -->
<!-- BEGIN OPTION_4 --> 
<tr>
<td>{NAME1} driving on outward journey from {ORIGIN1} to {DEST1},<br> {NAME2} is a passenger on return journey from {DEST2} to {ORIGIN2}.<input type=radio {CHECKED_option_4} value=4 name=option><br></td>
</tr>
<!-- END OPTION_4 -->
<!-- BEGIN OPTION_5 --> 
<tr>
<td>{NAME2} driving on outward journey from {ORIGIN2} to {DEST2},<br> {NAME1} is a passenger on outward journey from {ORIGIN1} to {DEST1}.<input type=radio {CHECKED_option_5} value=5 name=option><br></td>
</tr>
<!-- END OPTION_5 -->
<!-- BEGIN OPTION_6 --> 
<tr>
<td>{NAME2} driving on return journey from {DEST2} to {ORIGIN2},<br> {NAME1} is a passenger on outward journey from {ORIGIN1} to {DEST1}.<input type=radio {CHECKED_option_6} value=6 name=option><br></td>
</tr>
<!-- END OPTION_6 -->
<!-- BEGIN OPTION_7 --> 
<tr>
<td>{NAME2} driving on return journey from {DEST2} to {ORIGIN2},<br> {NAME1} is a passenger on return journey from {DEST1} to {ORIGIN1}.<input type=radio {CHECKED_option_7} value=7 name=option><br></td>
</tr>
<!-- END OPTION_7 -->
<!-- BEGIN OPTION_8 --> 
<tr>
<td>{NAME2} driving on outward journey from {ORIGIN2} to {DEST2},<br> {NAME1} is a passenger on return journey from {DEST1} to {ORIGIN1}.<input type=radio {CHECKED_option_8} value=8 name=option><br></td>
</tr>
<!-- END OPTION_8 -->
</table>
<input type=hidden name=trip1_id value={TRIP1_ID}>
<input type=hidden name=trip2_id value={TRIP2_ID}>
<center>
<input type=submit value="Get Directions">
</center>
</form>
<center>
<a href="javascript:;" onclick=window.close()>Close Window</a>
</center>
{GOOGLE_ANALYTICS}
</body>
</html>
