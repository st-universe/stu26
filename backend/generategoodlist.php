<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION["login"] != 1) exit;

$db = new db;

switch ($_GET['m'])
{
	case 1:
		$m = 1;
		break;
	case 2:
		$m = 2;
		break;
	default:
		$m = 1;
}

if ($m == 1)
{
	$result1 = $db->query("SELECT SUM(b.count) as gc,b.goods_id,c.name,c.sort as sorti FROM stu_ships as a LEFT JOIN stu_ships_storage as b ON b.ships_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id LEFT JOIN stu_modules as d ON d.module_id=c.goods_id WHERE ISNULL(d.module_id) AND a.user_id=".$_SESSION['uid']." GROUP BY b.goods_id");
	$result2 = $db->query("SELECT SUM(b.count) as gc,b.goods_id,c.name,c.sort as sorti FROM stu_colonies as a LEFT JOIN stu_colonies_storage as b ON b.colonies_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id LEFT JOIN stu_modules as d ON d.module_id=c.goods_id WHERE ISNULL(d.module_id) AND  a.user_id=".$_SESSION['uid']." GROUP BY b.goods_id");
	$result3 = $db->query("SELECT SUM(a.count) as gc,a.goods_id,b.name,b.sort as sorti FROM stu_trade_goods as a LEFT JOIN stu_goods as b ON b.goods_id=a.goods_id LEFT JOIN stu_modules as c ON c.module_id=b.goods_id WHERE ISNULL(c.module_id) AND a.user_id=".$_SESSION['uid']." AND (ISNULL(mode) OR mode='') GROUP BY goods_id");
	$result4 = $db->query("SELECT SUM(a.count*c.count) as gc,a.goods_id,b.name,b.sort as sorti,c.offer_id,c.count FROM stu_trade_goods as a LEFT JOIN stu_goods as b ON b.goods_id=a.goods_id LEFT JOIN stu_trade as c ON c.offer_id=a.offer_id LEFT JOIN stu_modules as d ON d.module_id=b.goods_id WHERE ISNULL(d.module_id) AND  a.user_id=".$_SESSION['uid']." AND (mode='1') GROUP BY goods_id");
	$result5 = $db->query("SELECT SUM(a.gcount*a.count) as gc,b.goods_id,b.name,b.sort as sorti FROM stu_trade_offers as a LEFT JOIN stu_goods as b ON b.goods_id=a.ggoods_id LEFT JOIN stu_modules as d ON d.module_id=b.goods_id WHERE ISNULL(d.module_id) AND a.user_id=".$_SESSION['uid']." GROUP BY b.goods_id");
	$result6 = $db->query("SELECT SUM(b.count) as gc,b.goods_id,c.name,c.sort as sorti FROM stu_stations as a LEFT JOIN stu_stations_storage as b ON b.stations_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id LEFT JOIN stu_modules as d ON d.module_id=c.goods_id WHERE ISNULL(d.module_id) AND  a.user_id=".$_SESSION['uid']." GROUP BY b.goods_id");
}
if ($m == 2)
{
	$result1 = $db->query("SELECT SUM(b.count) as gc,b.goods_id,c.name,c.sort as sorti FROM stu_ships as a LEFT JOIN stu_ships_storage as b ON b.ships_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id LEFT JOIN stu_modules as d ON d.module_id=c.goods_id WHERE !ISNULL(d.module_id) AND a.user_id=".$_SESSION['uid']." GROUP BY b.goods_id");
	$result2 = $db->query("SELECT SUM(b.count) as gc,b.goods_id,c.name,c.sort as sorti FROM stu_colonies as a LEFT JOIN stu_colonies_storage as b ON b.colonies_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id LEFT JOIN stu_modules as d ON d.module_id=c.goods_id WHERE !ISNULL(d.module_id) AND  a.user_id=".$_SESSION['uid']." GROUP BY b.goods_id");
	$result3 = $db->query("SELECT SUM(a.count) as gc,a.goods_id,b.name,b.sort as sorti FROM stu_trade_goods as a LEFT JOIN stu_goods as b ON b.goods_id=a.goods_id LEFT JOIN stu_modules as c ON c.module_id=b.goods_id WHERE !ISNULL(c.module_id) AND a.user_id=".$_SESSION['uid']." AND (ISNULL(mode) OR mode='') GROUP BY goods_id");
	$result4 = $db->query("SELECT SUM(a.count*c.count) as gc,a.goods_id,b.name,b.sort as sorti,c.offer_id,c.count FROM stu_trade_goods as a LEFT JOIN stu_goods as b ON b.goods_id=a.goods_id LEFT JOIN stu_trade as c ON c.offer_id=a.offer_id LEFT JOIN stu_modules as d ON d.module_id=b.goods_id WHERE !ISNULL(d.module_id) AND  a.user_id=".$_SESSION['uid']." AND (mode='1') GROUP BY goods_id");
	$result5 = $db->query("SELECT SUM(a.gcount*a.count) as gc,b.goods_id,b.name,b.sort as sorti FROM stu_trade_offers as a LEFT JOIN stu_goods as b ON b.goods_id=a.ggoods_id LEFT JOIN stu_modules as d ON d.module_id=b.goods_id WHERE !ISNULL(d.module_id) AND a.user_id=".$_SESSION['uid']." GROUP BY b.goods_id");
	$result6 = $db->query("SELECT SUM(b.count) as gc,b.goods_id,c.name,c.sort as sorti FROM stu_stations as a LEFT JOIN stu_stations_storage as b ON b.stations_id=a.id LEFT JOIN stu_goods as c ON c.goods_id=b.goods_id LEFT JOIN stu_modules as d ON d.module_id=c.goods_id WHERE !ISNULL(d.module_id) AND  a.user_id=".$_SESSION['uid']." GROUP BY b.goods_id");
}

// Verarbeitung
while($data=mysql_fetch_assoc($result1))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
while($data=mysql_fetch_assoc($result2))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
while($data=mysql_fetch_assoc($result3))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
while($data=mysql_fetch_assoc($result4))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
while($data=mysql_fetch_assoc($result5))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
while($data=mysql_fetch_assoc($result6))
{
	$goods[$data['goods_id']]['gi'] = $data['goods_id'];
	$goods[$data['goods_id']]['id'] = $data['sorti'];
	$goods[$data['goods_id']]['c'] += $data['gc'];
	$goods[$data['goods_id']]['n'] = $data['name'];
}
// Sortierung
function cmp ($a, $b)
{
	return strnatcmp($a["id"],$b["id"]);
}
@usort($goods, "cmp");
// Ausgabe
echo "<table style=\"width: 100%;\"><tr>";
$i = 0;
while (@list ($key, $value) = @each ($goods))
{
	if (!$value['c']) continue;
	if ($i>0 && $i%3 == 0) echo "</tr><tr>";
	echo "<td id=\"txf\"><a href=\"javascript:void(0);\" onClick=\"retrieve_good_distribution(".$value['gi'].");\"><img src=".$_SESSION['gfx_path']."/goods/".$value['gi'].".gif title=\"".stripslashes($value['n'])."\" border=\"0\"> ".stripslashes($value['n']).": ".$value['c']."</a></td>";
	$i++;
}
echo "</tr></table>";
?>