<!-- BEGIN ONE_SAVED_MATCH_HEAD -->
<table align=center class={LIST_STYLE} width=100%>
<tr>
<td>
Match Time: {MATCH_TIME}
</td>
</tr>
<tr>
<td>
Match Initiator: {MATCH_INITIATOR}
</td>
</tr>
<tr>
<td>
I was matched on a trip between {ORIGIN_LATLON_NAME} and {DESTINATION_LATLON_NAME} with the following trip match 
</td>
</tr>
<tr><td>
<!-- END ONE_SAVED_MATCH_HEAD -->
<!-- BEGIN ONE_SAVED_MATCH_TAIL -->
</td></tr>
<tr><td>
<center>
<!--
<a href="trip_feedback.php?tripmatch_id={TRIPMATCH_ID}">Give satisfaction feedback on this trip match with {MATCH_NAME} if completed.</a>
-->
<a href="javascript:;" onclick=popup('trip_feedback.php?tripmatch_id={TRIPMATCH_ID}')>Give satisfaction feedback on this trip match with {MATCH_NAME} if completed.</a>
</center>
</td></tr>
</table>
<!-- END ONE_SAVED_MATCH_TAIL -->
