<?php

define("EARTH_RADIUS",((6378.135+6356.750)/2));
define("PI",3.14159265);

function RADIANS_TO_DEGREES($angle)
{
   return ((($angle)*180)/PI);
}

function DEGREES_TO_RADIANS($angle)
{
   return ((($angle)*PI)/180);
}


function getxyangle($x,$y)
{
   if($y==0)
      return ($x>0 ? 90:270);
   $angle=atan($x/$y);
   $angle=RADIANS_TO_DEGREES($angle);
   if($y>=0&&$x<0)
      $angle+=360;
   else if($y<0)
      $angle+=180;
   return $angle;
}


function getxydist($x,$y)
{
   return(sqrt((($x*$x)+($y*$y))));
}


function getxyzdist($x,$y,$z)
{
   return(sqrt((($x*$x)+($y*$y)+($z*$z))));
}


function old_getlatlondist($lat1,$lon1,$lat2,$lon2,$radius)
{
   $lat1=DEGREES_TO_RADIANS($lat1);
   $lon1=DEGREES_TO_RADIANS($lon1);
   $lat2=DEGREES_TO_RADIANS($lat2);
   $lon2=DEGREES_TO_RADIANS($lon2);

   $x1=($radius*sin($lon1)*cos($lat1));
   $x2=($radius*sin($lon2)*cos($lat2));
   $y1=($radius*sin($lon1)*sin($lat1));
   $y2=($radius*sin($lon2)*sin($lat2));
   $z1=($radius*cos($lon1));
   $z2=($radius*cos($lon2));
   
   return(getxyzdist($x2-$x1,$y2-$y1,$z2-$z1));
}




/* Formula from www.geog.ubc.ca/courses/klink/gis.notes/
   ncgia/u26.html#SEC2.6.5.3 */
function getlatlondist($lat1,$lon1,$lat2,$lon2,$radius)
{
   $lat1=DEGREES_TO_RADIANS($lat1);
   $lon1=DEGREES_TO_RADIANS($lon1);
   $lat2=DEGREES_TO_RADIANS($lat2);
   $lon2=DEGREES_TO_RADIANS($lon2);

   return $radius*acos((sin($lat1)*sin($lat2))+
			 (cos($lat1)*cos($lat2)*cos($lon1-$lon2)));
}


function getlatlonangle($lat1,$lon1,$lat2,$lon2)
{
   $y=$lat2-$lat1;
   $x=$lon2-$lon1;
   if($y==0)
      return ($x>0 ? 90:270);
   $distlat1=getlatlondist($lat1,$lon1,$lat1+1,$lon1,EARTH_RADIUS);
   $distlon1=getlatlondist($lat1,$lon1,$lat1,$lon1+1,EARTH_RADIUS);
   $y=$y*$distlat1;
   $x=$x*$distlon1;
   $angle=atan($x/$y);
   $angle=RADIANS_TO_DEGREES($angle);
   if($y>=0&&$x<0)
      $angle+=360;
   else if($y<0)
      $angle+=180;
   return $angle;
}


function getdiffangle($lat1,$lon1,$lat2,$lon2)
{
   
   $angle1=getangle($lat1,$lon1);
   $angle2=getangle($lat2,$lon2);
   $diffangle=$angle1-$angle2;
   if($diffangle>180)
      $diffangle-=360;
   else if($diffangle<-180)
      $diffangle+=360;
   return $diffangle;
}

function issamedirection($lat1,$lon1,$lat2,$lon2)
{
   $diffangle=getdiffangle($lat1,$lon1,$lat2,$lon2);
   
   return (($diffangle>=0&&$diffangle<45)||($diffangle<=0&&$diffangle>45));
}




/* angle North =0 East 90 */
function getnewlatlong($lat,$lon,$distance,$angle,
		       &$outlat,&$outlon)
{

   $distlat1=getlatlondist($lat,$lon,$lat+1,$lon,EARTH_RADIUS);
   $distlon1=getlatlondist($lat,$lon,$lat,$lon+1,EARTH_RADIUS);
   $distlat2=(cos(DEGREES_TO_RADIANS($angle))/$distlat1);
   $distlon2=(sin(DEGREES_TO_RADIANS($angle))/$distlon1);
   $outlat=$lat+($distance*$distlat2);
   $outlon=$lon+($distance*$distlon2);
}

?>
