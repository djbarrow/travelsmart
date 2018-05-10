<?php
require_once "include/class.progressbar.php";

function progress_flush()
{
   ob_flush();
   flush();
}

function progress_init($title,$subtitle="")
{
   $width=300;
   $height=30;
   $prb = new ProgressBar($width,$height);
   $prb->left=$prb->top=0;
   $prb->color='#CC6600';
   print "<table><tr align=center><td colspan=3 id=\"progress_title\"><b>{$title}</b></td></tr>";
   print "<tr><td width=50%></td><td>";
   $prb->status = 'show';
   echo $prb->getHtml();
   print "</td><td width=50%></td></tr>";
   if($subtitle)
      print "<tr align=center><td colspan=3 id=\"progress_subtitle\">{$subtitle}</td></tr>";
   print "</table>";
   progress_flush();
   return $prb;
}

function progress_append_subtitle($new_subtitle="")
{
   print "var progress_subtitle=document.getElementById('progress_subtitle');" .
      "while (progress_subtitle.hasChildNodes())" .
      "{" .
      " progress_subtitle.removeChild(progress_subtitle.lastChild);" .
      "}";
   if($new_subtitle)
   {
      print "var new_text=document.createTextNode('{$new_subtitle}');" .
      "progress_subtitle.appendChild(new_text);";
   }
}

function hide_progress_title($has_subtitle=0)
{
   print "<script type=\"text/JavaScript\">" .
      "var progress_title=document.getElementById('progress_title');" .
      "while (progress_title.hasChildNodes())" .
      "{" .
      " progress_title.removeChild(progress_title.lastChild);" .
      "}";
   if($has_subtitle)
      progress_append_subtitle();
   print "</script>";
   progress_flush();
}

function progress_hide($prb,$has_subtitle=0)
{
   hide_progress_title($has_subtitle);
   $prb->hide();
   progress_flush();
}

function progress_move_step($prb,$curr_val,$subtitle="")
{
   $prb->moveStep($curr_val);
   if($subtitle)
   {
      print "<script type=\"text/JavaScript\">";
      progress_append_subtitle($subtitle);
      print "</script>";
   }
   progress_flush();
}

?>