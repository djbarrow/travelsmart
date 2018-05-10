<?php

function output_select($select_name,$select_array,$select_val)
{
	$retval="<select name={$select_name}>\n";
	$count=count($select_array);
	for($i=0;$i<$count;$i++)
	{
		$retval.="<option value={$select_array[$i][0]} " . 
			($select_array[$i][0]==$select_val ? "selected" : "") .
			">{$select_array[$i][1]}</option>\n";
	}
	$retval.="</select>\n";
	return $retval;
}

function output_integer_select($select_name,$low_val,$hi_val,$select_val)
{
	$retval="<select name={$select_name}>\n";
	for($i=$low_val;$i<=$hi_val;$i++)
	{
		$retval.="<option value={$i} " . 
			($i==$select_val ? "selected" : "") .
			" >{$i}</option>\n";
	}
	$retval.="</select>\n";
	return $retval;
}

?>