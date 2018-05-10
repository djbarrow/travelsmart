<?php
require_once "include/logos.php";

function get_picture_filename($type,$id)
{
   global $uploaddir;
   
   return $uploaddir . "/" . $type . "_" . $id . ".jpg"; 
}

function get_file_extension($str)
{
   $i =strpos($str,".");
   if(!$i)
   {
      return "";
   }
   $l = strlen($str) -$i;
   $ext = substr($str,$i+1,$l);
   return $ext;

}

function check_jpeg($str,$uploaded_tmp_name)
{
   $pext = get_file_extension($str);
   $pext = strtolower($pext);
   if(($pext!="jpg") && ($pext !="jpeg"))
      return false;
   list($width, $height, $type) = getimagesize($uploaded_tmp_name);
   if($type!=IMAGETYPE_JPEG)
      return false;
   return true;
}

/*
function upload_jpeg($type,$id)
{
   global $uploaddir;
   $newfile= get_picture_filename($type,$id);
   if(empty($_FILES['imgfile']['name']))
      return;
   if(is_uploaded_file($_FILES['imgfile']['tmp_name']))
   {
      if(!check_jpeg($_FILES['imgfile']['name']))
	 die("uploaded image must be a jpeg");
      if(!move_uploaded_file($_FILES['imgfile']['tmp_name'],$newfile))
      {
	 die("Error uploading image");
      }
   }
   else
      die("is_uploaded_file failure, passible hack attempt");
}
*/

function resize_upload_jpeg($type,$id,$maxwidth,$maxheight)
{
   global $uploaddir;
   $newfile= get_picture_filename($type,$id);
   if(empty($_FILES['imgfile']['name']))
      return false;
   if(is_uploaded_file($_FILES['imgfile']['tmp_name']))
   {
      if(!check_jpeg($_FILES['imgfile']['name'],$_FILES['imgfile']['tmp_name']))
      {
	 unlink($_FILES['imgfile']['tmp_name']);
	 return true;
      }
      $systemcmd="djpeg {$_FILES['imgfile']['tmp_name']} | "
	 . " pamscale -xyfit {$maxwidth} {$maxheight} | cjpeg > "
	 . "{$newfile}";
      system($systemcmd);
      unlink($_FILES['imgfile']['tmp_name']);
   }
   else
      die("is_uploaded_file failure, passible hack attempt");
   return false;
}

/*
function makeimage($type,$filename)
{
	$image=null;

	switch($type)
	{
	case IMAGETYPE_GIF:
		$image = imagecreatefromgif($filename);
		break;
	case IMAGETYPE_JPEG:
		$image = imagecreatefromjpeg($filename);
		break;
	case IMAGETYPE_PNG:
		$image = imagecreatefrompng($filename);
		break;
	case IMAGETYPE_SWF:
		break;
	case IMAGETYPE_PSD:
		break;
	case IMAGETYPE_BMP:
		break;
	case IMAGETYPE_TIFF_II:
		break;
	case IMAGETYPE_TIFF_MM:
		break;
	case IMAGETYPE_JPC:
		break;
	case IMAGETYPE_JP2:
		break;
	case IMAGETYPE_JPX:
		break;
	case IMAGETYPE_JB2:
		break;
	case IMAGETYPE_SWC:
		break;
	case IMAGETYPE_IFF:
		break;
	case IMAGETYPE_WBMP:
		$image = imagecreatefromwbmp($filename);
		break;
	case IMAGETYPE_XBM:
		$image = imagecreatefromxbm($filename);
		break;
	}
}

function resizeimage($maxwidth,$maxheight,$infilename,$outfilename)
{
	list($width, $height, $type) = getimagesize($infilename);
	$image=makeimage($type,$infilename);
	if($image==null)
		return false;
	$widthfactor=$width/$maxwidth;
	$heightfactor=$height/$maxheight;
	if($widthfactor>$heightfactor)
	{
		$newwidth=$maxwidth;
		$newheight=$height/$widthfactor;
	}
	else
	{
		$newwidth=$width/$heightfactor;
		$newheight=$maxheight;
	}
	$new_image = imagecreatetruecolor($newwidth,$newheight);
	imagecopyresampled($new_image, $image, 0, 0, 0, 0,
			   $newwidth, $newheight, $width, $height);
	
	imagejpeg($new_image,$outfilename,100);
	return true;
}

*/

//resizeimage(100,100,"Chrissie.jpg","Chrissie2.jpg");

?>