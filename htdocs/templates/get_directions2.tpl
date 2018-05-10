<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Get Directions</title>
<link rel=stylesheet href={CSS_FILE}>
</head>
    <script src="{GOOGLE_MAPS_KEY}"
      type="text/javascript"></script>
  <script type="text/javascript">
   var map=new Array();
   var directionsPanel=new Array();
   var directions=new Array();
function initialize() {
	var i;
	for(i=0;i<3;i++)
	{
      		map[i] = new GMap2(document.getElementById("map" + i));
      		directionsPanel[i] = document.getElementById("route" + i);
      		directions[i] = new GDirections(map[i], directionsPanel[i]);
	}
	directions[0].load("{JLEG0_LAT},{JLEG0_LON} to {JLEG1_LAT},{JLEG1_LON}");
	directions[1].load("{JLEG1_LAT},{JLEG1_LON} to {JLEG2_LAT},{JLEG2_LON}");
	directions[2].load("{JLEG2_LAT},{JLEG2_LON} to {JLEG3_LAT},{JLEG3_LON}");
    }
    </script>
<body onload="initialize()">
{LOGOS}
<br>
<center>
<h3>From {JLEG0_NAME} to {JLEG1_NAME}</h3><br>
</center><br>
<div id="route0" style="width=90%  border: 1px solid black;"></div>
<div id="map0" style="width: 90%; height: 480px; border: 1px solid black;"></div>
<br>
<center>
<h3>From {JLEG1_NAME} to {JLEG2_NAME}</h3>
</center>
<br>
<div id="route1" style="width: 90%; border: 1px solid black;"></div>
<div id="map1" style="width: 90%; height: 480px; border: 1px solid black;"></div
<br>
<center>
<h3>From {JLEG2_NAME} to {JLEG3_NAME}</h3>
</center>
<br>
<div id="route2" style="width: 90%; border: 1px solid black;"></div>
<div id="map2" style="width: 90%; height: 480px; border: 1px solid black;"></div>
<br>
<center>
<a href="javascript:;" onclick=window.close()>Close Window</a>
</center>
{GOOGLE_ANALYTICS}
</body>
</html>
