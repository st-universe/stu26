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

$ship = $db->query("SELECT id,systems_id,cx,cy,sx,sy FROM stu_ships WHERE id=".$_GET['id'],4);
if ($ship['systems_id'] == 0)
{
	$sys = $db->query("SELECT systems_id FROM stu_systems WHERE cx=".$ship['cx']." AND cy=".$ship['cy'],1);
	if ($sys == 0) exit;
	$ship['systems_id'] = $sys;
}
$result = $db->query("SELECT a.systems_id,a.colonies_classes_id,a.sx,a.sy,b.name FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b ON b.colonies_classes_id=a.colonies_classes_id WHERE a.systems_id=".$ship['systems_id']." AND a.user_id!=20 AND a.colonies_classes_id<30");
if (mysql_num_rows($result) == 0)
{
	echo "<table cellspacing=1 cellpadding=1 class=tcal><th>Keine Planeten vorhanden</th></table>";
	exit;
}
echo "<table cellspacing=1 cellpadding=1 class=tcal><tr><th colspan=4>Planeten in diesem System</th></tr><tr>";
while($data=mysql_fetch_assoc($result))
{
	if ($i == 2)
	{
		echo "</tr><tr>";
		$i = 0;
	}
	echo "<td align=center width=30><img src=".$gfx."/planets/".$data[colonies_classes_id].".gif title=\"".$data[name]."\"></td><td>".$data[sx]."|".$data[sy]."</td>";
	$i++;
	$sys = $data[systems_id];
}
echo "<td colspan=2></td></tr>";
echo "<table class=tcal>";
	
$co = $db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=".$sys." AND b.mode='1' GROUP BY b.goods_id ORDER BY c.sort");
$cw = $db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=".$sys." AND b.mode='2' GROUP BY b.goods_id ORDER BY c.sort");

if (mysql_num_rows($co) > 0 || mysql_num_rows($cw) > 0)
{
	if (mysql_num_rows($co) > 0)
	{
		echo "<th>Warenangebot</th><tr><td>";
		while($data=mysql_fetch_assoc($co))
		{
			$res = $db->query("SELECT b.name,b.sx,b.sy FROM stu_colonies_trade as a LEFT JOIN stu_colonies as b ON b.id=a.colonies_id WHERE a.goods_id=".$data['goods_id']." AND a.mode='1' AND b.systems_id=".$sys);
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
			$res = $db->query("SELECT b.name,b.sx,b.sy FROM stu_colonies_trade as a LEFT JOIN stu_colonies as b ON b.id=a.colonies_id WHERE a.goods_id=".$data['goods_id']." AND a.mode='2' AND b.systems_id=".$sys);
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