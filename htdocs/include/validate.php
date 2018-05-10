<?php

function check_email($email) {
   // Used as callback or validate="email" shortcut
   return preg_match(
      '#^([a-zA-Z0-9_\\-\\.]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.)|(([a-zA-Z0-9\\-]+\\.)+))([a-zA-Z]{2,20}|[0-9]{1,3})(\\]?)$#',
      $email
      );
   // Regexp from http://www.regexplib.com/REDetails.aspx?regexp_id=26
}


function get_name_and_email_addr($str,&$name,&$email_addr)
{
   $left_bracket_pos=
      $right_bracket_pos=
      $left_inverted_comma_pos=
      $right_inverted_comma_pos=
      $at_pos=-1;
   $has_space=0;
   $left_bracket_cnt=
      $right_bracket_cnt=
      $inverted_comma_cnt=0;
   $str=trim($str);
   $len=strlen($str);
   $name="";
   $email_addr="";
   for($i=0;$i<$len;$i++)
   {
      switch($str[$i])
      {
	 case '<':
	 case '[':
	    $left_bracket_pos=$i;
	    $left_bracket_cnt++;
	    if($left_bracket_cnt>1)
	       return 0;
	    if($right_bracket_pos!=-1&&
	       $right_bracket_pos<$left_bracket_pos)
	       return 0;
	    if($inverted_comma_cnt!=0&&$inverted_comma_cnt!=2)
	       return 0;
	    break;
	 case '>':
	 case ']':
	    $right_bracket_pos=$i;
	    $right_bracket_cnt++;
	    if($right_bracket_cnt>1)
	       return 0;
	    break;
	 case '"':
	    if($left_inverted_comma_pos==-1)
	       $left_inverted_comma_pos=$i;
	    else
	       $right_inverted_comma_pos=$i;
	    $inverted_comma_cnt++;			
	    break;
	 case '@':
	    $at_pos=$i;
	    break;
	 case ' ':
	    $has_space=1;
	    break;
      }
   }
   if($at_pos==-1)
      return 0;
   if($left_bracket_cnt>0&&$at_pos<$left_bracket_pos)
      return 0;
   if($left_bracket_cnt>1||$right_bracket_cnt>1)
      return 0;
   if($inverted_comma_cnt!=0&&$inverted_comma_cnt!=2)
      return 0;
   if($left_bracket_cnt)
   {
      $email_left=$left_bracket_pos+1;
      $email_right=$right_bracket_pos-1;
   }
   else
   {
      $loop_break=0;
      for($email_left=$at_pos-1;$email_left>0;$email_left--)
      {
	 switch($str[$email_left])
	 {
	    case '"':
	    case ' ':
	       $email_left++;
	       $loop_break=1;
	       break;
	 }
	 if($loop_break)
	    break;
      }
      $loop_break=0;
      for($email_right=$at_pos+1;$email_right<$len;$email_right++)
      {
	 switch($str[$email_right])
	 {
	    case '"':
	    case ' ':
	       $email_right--;
	       $loop_break=1;
	       break;
	 }
	 if($loop_break)
	    $break;
      }
   }
   if($email_right-$email_left<3)
      return 0;
   $email_addr=substr($str,$email_left,($email_right+1)-$email_left);
   if($has_space==0)
   {
      return 1;
   }
   else
   {
      if($inverted_comma_cnt==2)
      {
	 $name=substr($str,$left_inverted_comma_pos+1,
		      $right_inverted_comma_pos-
		      ($left_inverted_comma_pos+1));
      }
      else
      {
	 $name=trim(substr($str,0,$email_left-$left_bracket_cnt));
      }
   }
   return 1;
}

function make_greeting($name)
{
   return (empty($name)) ? "Hello there" : "Dear {$name}";
}

function make_to_str($name,$email_addr)
{
   if(empty($name))
      return "<{$email_addr}>";
   else
      return "\"{$name}\" <{$email_addr}>";
}

function email_fix_textarea($str)
{
   $len=strlen($str);
   $len1=$len-1;
   for($i=0;$i<$len;$i++)
   {
      $ord_str=ord($str[$i]);
      if($ord_str==0xa)
	 $str[$i]=',';
      if($ord_str==0xd||$ord_str==0x9)
	 $str[$i]=' ';
   }
   for($i=0;$i<$len1;$i++)
   {
      if($str[$i]==',')
      {
	 for($j=$i+1;$j<$len1;$j++)
	 {
		     
	    if($str[$j]==',')
	    {
	       $str[$i]=' ';
	       break;
	    }
	    else if($str[$j]!=' ')
	       break;
	 }
      }
   } 
   for($i=$len1;$i>=0;$i--)
   {
      if($str[$i]==',')
	 $str[$i]=' ';
      else if($str[$i]!=' ')
	 break;
   }
   for($i=0;$i<$len;$i++)
   {
      if($str[$i]==',')
	 $str[$i]=' ';
      else if($str[$i]!=' ')
	 break;
   }
   return $str;
}

function check_alpha($var)
{
   return preg_match('|^[a-zA-Z]*$|',$var);
}

function check_alphanumeric($var)
{
   return preg_match('|^[a-zA-Z0-9]*$|',$var);
}

function check_numeric($var)
{
   return preg_match('|^[0-9]*$|',$var);
}

function show_error_block($template,$blockname,$variable="",$value="")
{
   global $validate_error;
 
   $saveblock=$template->currentBlock;
   $template->setCurrentBlock("ERROR_" . $blockname);
   if(!empty($variable))
      $template->setVariable($variable,$value);
   else
      $template->touchBlock("ERROR_" . $blockname);
   $template->parseCurrentBlock();
   $template->setCurrentBlock($saveblock);
   $validate_error=1;
}

function show_nested_block($template,$blockname)
{
   $saveblock=$template->currentBlock;
   $template->setCurrentBlock($blockname);
   $template->touchBlock($blockname);
   $template->parseCurrentBlock();
   $template->setCurrentBlock($saveblock);
}

function show_nested_variable_block($template,$blockname,$variable,$value)
{
   $saveblock=$template->currentBlock;
   $template->setCurrentBlock($blockname);
   $template->setVariable($variable,$value);
   $template->parseCurrentBlock();
   $template->setCurrentBlock($saveblock);
}

?>
