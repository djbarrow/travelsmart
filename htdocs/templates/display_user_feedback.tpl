<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>Feedback on {SATISFACTIONNAME} from other carpoolers</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
<body>
{LOGOS}
<table>
<tr>
<td>
<h2>Feedback on {SATISFACTIONNAME} from other carpoolers.</h2>
</td>
<!-- BEGIN DISPLAY_PIC -->
<td ALIGN=CENTER>
<img SRC="{USER_IMAGE}" NOSAVE class=image_border>
</td>
<!-- END DISPLAY_PIC -->
</tr>
<!-- BEGIN FEEDBACK_FROM_ONE -->
<tr><td>
<table align=center class={LIST_STYLE} width=100%>
<!-- BEGIN POSSIBLE_FORGED_SATISFACTION_ENTRY -->
<tr class=error_big colspan=2 align=center>
<td>
This satisfaction entry might be forged!
</td>
</tr>
<tr class=error colspan=2 align=center>
<td>
The same machine might have been used (ip_address={IP_ADDR})
for person giving satisfaction feedback & the person who
is receiving satisfaction feedback.
</td>
</tr>
<!-- END POSSIBLE_FORGED_SATISFACTION_ENTRY -->
<!-- BEGIN POSSIBLE_FORGED_SATISFACTION_ENTR2 -->
<tr class=error_big colspan=2 align=center>
<td>
This satisfaction entry is almost definitely forged!
</td>
</tr>
<tr class=error colspan=2 align=center>
<td>
The same machine was definitely used for person giving satisfaction 
feedback & the person who is receiving satisfaction feedback.
</td>
</tr>
<!-- END POSSIBLE_FORGED_SATISFACTION_ENTR2 -->
<tr>
<td>
Match Time: {MATCH_TIME}<br>
Satisfaction of {FEEDBACK_NAME} after carpooling with {SATISFACTION_NAME} 
<!-- BEGIN INDIVIDUAL_TRIP -->
on a single trip on {TRIP_DATE}
<!-- END INDIVIDUAL_TRIP -->
<!-- BEGIN REGULAR_TRIPS -->
on regular trips
<!-- END REGULAR_TRIPS -->
between {ORIGIN_LATLON_NAME} & {DESTINATION_LATLON_NAME},
{SATISFACTION} out of 10.
</td>
</tr>
<tr><td>
Comments:
</td></tr>
<tr>
<td>
{COMMENTS}
</td>
</tr>
</table>
</td></tr>
<!-- END FEEDBACK_FROM_ONE -->
</table>
<center>
{SET_PAGELINKS}
</center>
<center>
<a href="javascript:;" onclick=window.close()>Close Window</a>
</center>
{GOOGLE_ANALYTICS}
</body>
</html>
