<?php
if (!is_object($db)) exit;
include_once("class/map.class.php");
$map = new map;

switch($_GET['s'])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	case "ss":
		$v = "showsystem";
		if (!check_int($_GET['id'])) die(show_error(902));
		if ($db->query("SELECT systems_id FROM stu_systems_user WHERE systems_id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",1) == 0) die(show_error(902));
		break;
}

if ($v == "main")
{
	pageheader("/ <b>Sternenkarte</b>");
	echo "<script language=\"Javascript\">
	var lay_stick = 0;
	function proceed_change()
	{
		obj = document.tform.wort.value;
		ftest = obj.length;
		if (obj.length < 2) return;
		elt = \"muh\";
		sendRequest('backend/ssmap.php?sstring='+obj);
		elem = document.getElementById(elt);
		elem.style.left = \"454px\";
		elem.style.top = \"80px\";
		elem.style.width = \"270px\";
		elem.style.position = \"absolute\";
	}
	
	function onpageload() {
		loadActualMap()
		// alert('loaded');
	}
	
	if (window.addEventListener)
	  window.addEventListener('load', onpageload, false);
	else if (window.attachEvent)
	  window.attachEvent('onload', onpageload);
	else window.onload = onpageload;
  
	function loadActualMap() {
		elt = 'tehmap';

		sendRequest('starmap/actual.php');
	}
  	function loadActualMapZoom(i) {
		elt = 'tehmap';
		hideInfo();
		sendRequest('starmap/actualZoom.php?s='+i);
	}  
	function loadPoliticalMap() {
		elt = 'tehmap';
		hideInfo();
		sendRequest('starmap/political.php');
	}
  	function loadPoliticalMapZoom(i) {
		elt = 'tehmap';

		sendRequest('starmap/politicalZoom.php?s='+i);
	}  	
	function loadEventMap() {
		elt = 'tehmap';
		hideInfo();
		sendRequest('starmap/event.php');		
	}
  	function loadEventMapZoom(i) {
		elt = 'tehmap';

		sendRequest('starmap/eventZoom.php?s='+i);		
	}  
	
	
  	function showSystem(i,f) {
		elt = 'tehmap';
		hideInfo();
		sendRequest('starmap/showSystem.php?s='+i+'&f='+f);
	}	
	


function showInfoField(el,rx,ry) {
	var bodyRect = document.body.getBoundingClientRect();
    elemRect = el.getBoundingClientRect();
	
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],wx=w.innerWidth||e.clientWidth||g.clientWidth,wy=w.innerHeight||e.clientHeight||g.clientHeight;

    y   = Math.min(elemRect.top - bodyRect.top, wy - 250);
	x   = elemRect.left - bodyRect.left;
	
	elt = 'infodiv';
	sendRequest('starmap/fieldInfoWarp.php?x='+rx+'&y='+ry);
	
	document.getElementById('infodiv').style.left = (x + 30) + 'px';
	document.getElementById('infodiv').style.top = y + 'px';
	document.getElementById('infodiv').style.visibility = 'visible';	
}	
function showInfoFaction(el,rx,ry) {
	var bodyRect = document.body.getBoundingClientRect();
    elemRect = el.getBoundingClientRect();
	
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],wx=w.innerWidth||e.clientWidth||g.clientWidth,wy=w.innerHeight||e.clientHeight||g.clientHeight;

    y   = Math.min(elemRect.top - bodyRect.top, wy - 250);
	x   = elemRect.left - bodyRect.left;
	
	elt = 'infodiv';
	sendRequest('starmap/fieldInfoFaction.php?x='+rx+'&y='+ry);
	
	document.getElementById('infodiv').style.left = (x + 30) + 'px';
	document.getElementById('infodiv').style.top = y + 'px';
	document.getElementById('infodiv').style.visibility = 'visible';	
}	
function showInfoFieldSystem(el,rx,ry,sys) {
	var bodyRect = document.body.getBoundingClientRect();
    elemRect = el.getBoundingClientRect();
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],wx=w.innerWidth||e.clientWidth||g.clientWidth,wy=w.innerHeight||e.clientHeight||g.clientHeight;

    y   = Math.min(elemRect.top - bodyRect.top, wy - 250);
	x   = elemRect.left - bodyRect.left;
	
	elt = 'infodiv';
	sendRequest('starmap/fieldInfoSystem.php?x='+rx+'&y='+ry+'&s='+sys);
	
	document.getElementById('infodiv').style.left = (x + 30) + 'px';
	document.getElementById('infodiv').style.top = y + 'px';
	document.getElementById('infodiv').style.visibility = 'visible';	
}


function showInfoEvents(el,rx,ry) {
	var bodyRect = document.body.getBoundingClientRect();
    elemRect = el.getBoundingClientRect();
	
	var w=window,d=document,e=d.documentElement,g=d.getElementsByTagName('body')[0],wx=w.innerWidth||e.clientWidth||g.clientWidth,wy=w.innerHeight||e.clientHeight||g.clientHeight;

    y   = Math.min(elemRect.top - bodyRect.top, wy - 250);
	x   = elemRect.left - bodyRect.left;
	
	elt = 'infodiv';
	sendRequest('starmap/fieldInfoEvents.php?x='+rx+'&y='+ry);
	
	document.getElementById('infodiv').style.left = (x + 30) + 'px';
	document.getElementById('infodiv').style.top = y + 'px';
	document.getElementById('infodiv').style.visibility = 'visible';	
}
function highlightRegion(region,rgbacolor) {
	var clusterborders =  document.getElementsByName('clb'+region);
	var i;
	for (i = 0; i < clusterborders.length; i++) {
		clusterborders[i].style.border = '1px solid '+rgbacolor;
	} 		
}	
function noHighlight(region) {
	var clusterborders =  document.getElementsByName('clb'+region);
	var i;
	for (i = 0; i < clusterborders.length; i++) {
		clusterborders[i].style.border = 'none';
	} 	
}
	</script>
	
	
	<style>
	td.pages {
		text-align: center;
		width: 20px;
		border: 1px groove #8897cf;
	}
	td.pages:hover
	{
		background: #262323;
	}
	#bla a {
		font-weight: bold;
		width: 300px;
		display: block;
		padding-top: 3px;
		padding-bottom: 3px;
	}
.rotate {
  /* FF3.5+ */
  -moz-transform: rotate(-90.0deg);
  /* Opera 10.5 */
  -o-transform: rotate(-90.0deg);
  /* Saf3.1+, Chrome */
  -webkit-transform: rotate(-90.0deg);
  /* IE6,IE7 */
  filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=0.083);
  /* IE8 */
  -ms-filter: \"progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)\";
  /* Standard */
  transform: rotate(-90.0deg);
}	
.helper {
  width:14px;
  height:14px;
  border: none;
}	
.helpersys {
  width:20px;
  height:20px;
  border: none;
}	
	</style>";


	
	
	echo "<center><div id=\"tehmap\" style=\"width:727px; height:727px;\"></div></center>
	";
	
}
if ($v == "showsystem")
{
	 $system = $map->getsystembyid($_GET["id"]);
	$result = $map->getknownsystembyid($_GET["id"]);
	pageheader("/ <a href=?p=map>Sternenkarte</a> / <b>".$system[name]."-System</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td width=30 height=30></td>";
	for($i=1;$i<=$system[sr];$i++) echo "<th width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($ly != $data[sy])
		{
			echo "</tr><tr><th>".$data[sy]."</th>";
			$ly = $data[sy];
		}
		if ($data[type] == 99) echo "<td><img src=".$gfx."/map/12.gif".($data[id] > 0 ? " onmouseover=\"return overlib('".$system[name]." ".str_replace("'","",$data[planet_name])." (".$data[id].")', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER)\" onmouseout=\"nd();\"" : "")."></td>";
		else echo "<td><img src=".$gfx."/map/".$data[type].".gif".($data[id] > 0 ? " onmouseover=\"return overlib('".$system[name]." ".str_replace("'","",$data[planet_name])." (".$data[id].")', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER)\" onmouseout=\"nd();\"" : "")."></td>";
	}
	echo "</tr></table>";
}
?>