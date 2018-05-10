<?php

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

resizeimage(100,100,"Chrissie.jpg","Chrissie2.jpg")
?>
