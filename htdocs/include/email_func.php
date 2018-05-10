<?php

function add_saved_match($connection,$trip1,$trip2)
{
   $query="select * from saved_tripmatch where trip_id={$trip1->trip_id} AND other_trip_id={$trip2->trip_id}";
    if (!$result = @ mysql_query ($query, $connection))
      showerror();
    $numrows=mysql_num_rows($result);
    if($numrows==0)
    {
       $datetime=get_datetime();
       $query="INSERT into saved_tripmatch values(NULL,"
	  . "${_SESSION["user_id"]},{$trip1->user_id},"
	  . "{$trip2->user_id},{$trip1->trip_id},{$trip2->trip_id},'{$datetime}',"
	  . "NULL,false,null,null,false)";
       if (!$result = @ mysql_query ($query, $connection))
	  showerror();
    }
 }

function email_func($connection,$trip1_id,$trip2_id,
		    &$email_from,&$email_to,&$email_reply_to,&$email_style,&$email_subject,&$email_body,&$page,$sending_email_state,$comments="")
{
   global $tripmatch_email_address;

   $trip1=get_trip_info_from_trip_id($trip1_id,$connection);
   $trip2=get_trip_info_from_trip_id($trip2_id,$connection);
   $row_from=get_userinfo_from_user_id($connection,$trip1->user_id);
   $email_reply_to="\"{$row_from["name"]}\" <{$row_from["email_address"]}>";
   $email_from=$tripmatch_email_address;
   $name_from=$row_from["name"];
   $row_to=get_userinfo_from_user_id($connection,$trip2->user_id);
   $email_to="\"{$row_to["name"]}\" <{$row_to["email_address"]}>";
   $email_style=$row_to["email_style"];
   $name_to=$row_to["name"];
   $email_subject="Hi " . $name_to . ", your trip from " . $trip2->trip_origin_latlon->name . " to " .
      $trip2->trip_destination_latlon->name . " has just been matched with " . $name_from . " by Travelsmart.";
   $email_is_html=($sending_email_state!=2||$email_style==email_style_html);
   if($email_is_html)
   {
      $template=new HTML_Template_IT("./templates");
      $template->loadTemplatefile("display_trip_matches.tpl",true,true);
      $template->setCurrentBlock("PAGE_PART_ONE");
      $template->setVariable("TITLE","");
      $template->setVariable("CSS_FILE",get_css_name());
      $template->setVariable("LOGOS",get_logos_str($sending_email_state==2 ? 0:1));
      $template->parseCurrentBlock();
      $part_one=$template->get();
      $template=new HTML_Template_IT("./templates");
      $template->loadTemplatefile("display_trip_matches.tpl",true,true);
      $template->setCurrentBlock("EMAIL_SUBJECT");
      $template->setVariable("EMAIL_TO",$email_to);
      $template->setVariable("EMAIL_SUBJECT",$email_subject);
      $template->parseCurrentBlock();
      $template->setCurrentBlock("SEND_CANCEL_EMAIL_PART1");
      $template->setVariable("TRIP1_ID",$trip1_id);
      $template->setVariable("TRIP2_ID",$trip2_id);
      $template->parseCurrentBlock();
      $subject_and_hidden_fields=$template->get();
      $template2=new HTML_Template_IT("./templates");
      $template2->loadTemplatefile("display_one_match.tpl",true,true);
      display_one_match($connection,$template2,$trip2,$trip1,0,$sending_email_state,1,0,$comments);
      $part_two=$template2->get();
      $template=new HTML_Template_IT("./templates");
      $template->loadTemplatefile("display_trip_matches.tpl",true,true);
      $template->setCurrentBlock("SEND_CANCEL_EMAIL_PART2");
      $template->touchBlock("SEND_CANCEL_EMAIL_PART2");
      $template->parseCurrentBlock();
      $send_cancel_email_part2=$template->get();
      $template=new HTML_Template_IT("./templates");
      $template->loadTemplatefile("display_trip_matches.tpl",true,true);
      $template->setCurrentBlock("PAGE_FOOTER");
      $template->touchBlock("PAGE_FOOTER");
      $template->parseCurrentBlock();
      $part_three=$template->get();
      $email_body=$part_one . $part_two . $part_three;
      $page=$part_one .  $subject_and_hidden_fields . $part_two . $send_cancel_email_part2 . $part_three;
   }
   else
   {
      $template2=null;
      $email_body=display_one_match($connection,$template2,$trip2,$trip1,0,$sending_email_state,1,0,$comments);
      $page="";
   }
   
}


function display_email_sent_successfully_page_header()
{
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("display_trip_matches.tpl",true,true);
   $template->setCurrentBlock("PAGE_PART_ONE");
   $template->setVariable("TITLE","Email Sent Successfully");
   $template->setVariable("CSS_FILE",get_css_name());
   $template->setVariable("LOGOS",get_logos_str());
   $template->parseCurrentBlock();
   $template->show();
}

function display_email_sent_successfully_page_footer($email_to,$template_idx)
{
   $template=new HTML_Template_IT("./templates");
   $template->loadTemplatefile("display_trip_matches.tpl",true,true);
   $template->setCurrentBlock("EMAIL_SENT_SUCCESSFULLY{$template_idx}");
   $template->setVariable("EMAIL_TO",$email_to);
   $template->parseCurrentBlock();
   $template->setCurrentBlock("PAGE_FOOTER");
   $template->touchBlock("PAGE_FOOTER");
   $template->parseCurrentBlock();
   $template->show();
}

function display_email_sent_successfully_page($email_to,$close_window)
{
   display_email_sent_successfully_page_header();
   display_email_sent_successfully_page_footer($email_to,$close_window);
}

?>