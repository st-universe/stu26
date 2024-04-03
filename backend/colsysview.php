<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if (!check_int($_GET[id]) || $_SESSION["login"] != 1) exit;

$db = new db;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$result = $db->query("SELECT a.id,a.colonies_classes_id,a.user_id,a.sx,a.sy,a.planet_name,b.name,c.user FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b ON b.colonies_classes_id=a.colonies_classes_id LEFT JOIN stu_user as c ON c.id=a.user_id WHERE a.systems_id=".$_GET['id']." AND a.user_id!=20 ORDER BY a.planet_name");

if (mysql_num_rows($result) == 0) exit;

$cols = array();
while($data=mysql_fetch_assoc($result))
{
	array_push($cols,$data);
	if ($data[user_id] == $_SESSION[uid]) $gui = 1;
}

$highest = 0;
$current = 0;
$i = 0;
$lastplanet = -1;
foreach ($cols as $data) {

	if ($data[colonies_classes_id] < 300) {
		if ($lastplanet >= 0) {
			$cols[$lastplanet][moons] = $current;
		}
		$current = 0;
		$lastplanet = $i;
	}
	else $current++;
	
	$i++;
	if ($current > $highest) $highest = $current;
}
$cols[$lastplanet][moons] = $current;

$i = 0;
$ret .= "<table cellspacing=1 cellpadding=1 class=tcal><tr><th colspan=".($highest*2).">Planeten/Monde in diesem System</th></tr>";


function entry($data) {
	global $gfx;
	switch($data[colonies_classes_id]) {
	
		case 231:
		case 232:
		case 233:
			return "<td align=center width=50><img style='background-repeat: no-repeat;background-position: center;background-image: url(\"".$gfx."/planets/".$data[colonies_classes_id].".gif"."\");' src=".$gfx."/planets/".$data[colonies_classes_id]."r.png title=\"".$data[planet_name].": ".$data[name]."\"></td><td style='text-align:center;'>".$data[sx]."|".$data[sy]."<br>&nbsp;".($data[user_id] != 1 ? "<a href=?p=comm&s=nn&recipient=".$data[user_id]." target=main ".getonm("pm".$data[id],"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$data[id]." title=\"PM an ".ftit($data[user])." schreiben\" border=0></a>" : "")."</td>";
		default:
			return "<td align=center width=50><img src=".$gfx."/planets/".$data[colonies_classes_id].".gif title=\"".$data[planet_name].": ".$data[name]."\"></td><td style='text-align:center;'>".$data[sx]."|".$data[sy]."<br>&nbsp;".($data[user_id] != 1 ? "<a href=?p=comm&s=nn&recipient=".$data[user_id]." target=main ".getonm("pm".$data[id],"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$data[id]." title=\"PM an ".ftit($data[user])." schreiben\" border=0></a>" : "")."</td>";		
	}
	
}


for ($i = 0; $i < count($cols); $i++) {
	
	$ret .= "<tr>";
	
	$data = $cols[$i];
	$ret .= entry($data);
	
	for ($j = 0; $j < $data[moons]; $j++) {
		$i++;
		$mdata = $cols[$i];
		$ret .= entry($mdata);
	}
	for ($j = 0; $j < ($highest-$data[moons]); $j++) {
		$ret .= "<td colspan=2></td>";		
	}	
	$ret .= "</tr>";
}

// foreach ($cols as $data)
// {
	// if ($i == ($highest))
	// {
		// $ret .= "</tr><tr>";
		// $i = 0;
	// }
	// $ret .= "<td align=center width=30><img src=".$gfx."/planets/".$data[colonies_classes_id].".gif title=\"".$data[name]."\"></td><td>".$data[sx]."|".$data[sy]."</td><td>".($data[user_id] != 1 ? "<a href=?p=comm&s=nn&recipient=".$data[user_id]." target=main ".getonm("pm".$data[id],"buttons/msg")."><img src=".$gfx."/buttons/msg1.gif name=pm".$data[id]." title=\"PM an ".ftit($data[user])." schreiben\" border=0></a>" : "")."</td>";
	// if ($data[user_id] == $_SESSION[uid]) $gui = 1;
	// $i++;
// }

if ($gui != 1) exit;
//if ($i == 1) $ret .= "<td colspan=3></td></tr>";
$ret .= "<table class=tcal>";
echo $ret;
	
$co = $db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=".$_GET[id]." AND b.mode='1' GROUP BY b.goods_id ORDER BY c.sort");
$cw = $db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=".$_GET[id]." AND b.mode='2' GROUP BY b.goods_id ORDER BY c.sort");

if (mysql_num_rows($co) > 0 || mysql_num_rows($cw) > 0)
{
	if (mysql_num_rows($co) > 0)
	{
		echo "<th>Warenangebot</th><tr><td>";
		while($data=mysql_fetch_assoc($co))
		{
			$res = $db->query("SELECT b.name,b.sx,b.sy FROM stu_colonies_trade as a LEFT JOIN stu_colonies as b ON b.id=a.colonies_id WHERE a.goods_id=".$data['goods_id']." AND a.mode='1' AND b.systems_id=".$_GET['id']);
			while($dat=mysql_fetch_assoc($res)) $tt .= "<br>".ftit($dat['name'])." (".$dat['sx']."|".$dat['sy'].")";;
			echo "<img src=".$gfx."/goods/".$data[goods_id].".gif onMouseover=\"return overlib2('<b>".ftit($data[name])."</b>".$tt."', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', ABOVE, CELLPAD, 0, 0, 0, 0, CENTER);\" onMouseOut=\"nd2();\"> ";
			$i++;
			if($i%8==0) echo "<br>";
			unset($tt);
		}
		echo "</td></tr>";
	}
	$i = 0;
	if (mysql_num_rows($cw) > 0)
	{
		echo "<th>Warennachfrage</th><tr><td>";
		while($data=mysql_fetch_assoc($cw))
		{
			$res = $db->query("SELECT b.name,b.sx,b.sy FROM stu_colonies_trade as a LEFT JOIN stu_colonies as b ON b.id=a.colonies_id WHERE a.goods_id=".$data['goods_id']." AND a.mode='2' AND b.systems_id=".$_GET['id']);
			while($dat=mysql_fetch_assoc($res)) $tt .= "<br>".ftit($dat['name'])." (".$dat['sx']."|".$dat['sy'].")";;
			echo "<img src=".$gfx."/goods/".$data[goods_id].".gif onMouseover=\"return overlib2('<b>".ftit($data[name])."</b>".$tt."', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', ABOVE, CELLPAD, 0, 0, 0, 0, CENTER);\" onMouseOut=\"nd2();\"> ";
			$i++;
			if($i%8==0) echo "<br>";
			unset($tt);
		}
		$ret .= "</td></tr>";
	}
	echo "</table>";
}
echo "<input type=button class=button onClick=\"cClick();\" value=Schließen>";
?>