<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION["login"] != 1 || !check_int($_GET['good'])) exit;
$db = new db;
if ($db->query("SELECT goods_id FROM stu_goods WHERE goods_id=".$_GET['good'],1) == 0) exit;
$gfx = $_SESSION['gfx_path'];


$col = $db->query("SELECT a.count,b.id,b.colonies_classes_id,b.sx,b.sy,b.systems_id,b.name,c.name as sysname FROM stu_colonies_storage as a LEFT JOIN stu_colonies as b ON b.id=a.colonies_id LEFT JOIN stu_systems as c ON c.systems_id=b.systems_id WHERE a.goods_id=".$_GET['good']." AND b.user_id=".$_SESSION['uid']);
$sta = $db->query("SELECT a.count,b.*,c.name as sysname FROM stu_stations_storage as a LEFT JOIN stu_stations as b ON b.id=a.stations_id LEFT JOIN stu_systems as c ON c.systems_id=b.systems_id WHERE a.goods_id=".$_GET['good']." AND b.user_id=".$_SESSION['uid']);
$ship = $db->query("SELECT a.count,b.id,b.rumps_id,b.sx,b.sy,b.systems_id,b.cx,b.cy,b.name,c.name as sysname,d.slots FROM stu_ships_storage as a LEFT JOIN stu_ships as b ON b.id=a.ships_id LEFT JOIN stu_systems as c ON c.systems_id=b.systems_id LEFT JOIN stu_rumps as d ON d.rumps_id=b.rumps_id WHERE a.goods_id=".$_GET['good']." AND b.user_id=".$_SESSION['uid']);
$trade = $db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND goods_id=".$_GET['good']." AND user_id=".$_SESSION['uid'],1);
$offers = $db->query("SELECT SUM(gcount*count) FROM stu_trade_offers WHERE ggoods_id=".$_GET['good']." AND user_id=".$_SESSION['uid'],1);


echo "<table class=\"tcal\" style=\"border: 1px groove #8897cf;\"><th colspan=\"4\">Suche nach <img src=".$gfx."/goods/".$_GET['good'].".gif></th>";
if (mysql_num_rows($col) > 0)
{
	echo "<tr><td colspan=\"4\"><b>Auf Kolonien</b></td></tr>";
	while($data=mysql_fetch_assoc($col))
	{
		echo "<tr>
		<td><a href=?p=colony&s=sc&id=".$data['id']."><img src=".$gfx."/planets/".$data['colonies_classes_id'].".gif border=0></<></td>
		<td>".stripslashes($data['name'])."</td>
		<td>".$data['sx']."|".$data['sy']." (".stripslashes($data['sysname'])."-System)</td>
		<td><img src=".$gfx."/goods/".$_GET['good'].".gif> ".$data['count']."</td>
		</tr>";
	}
}
if (mysql_num_rows($sta) > 0)
{
	echo "<tr><td colspan=\"4\"><b>Auf Stationen</b></td></tr>";
	while($data=mysql_fetch_assoc($sta))
	{
		echo "<tr>
		<td><a href=?p=station&s=show&id=".$data['id']."><img src=".$gfx."/stations/".$data['stations_classes_id'].".gif border=0></<></td>
		<td>".stripslashes($data['name'])."</td>
		<td>".$data['sx']."|".$data['sy']." (".stripslashes($data['sysname'])."-System)</td>
		<td><img src=".$gfx."/goods/".$_GET['good'].".gif> ".$data['count']."</td>
		</tr>";
	}
}
if (mysql_num_rows($ship) > 0)
{
	echo "<tr><td colspan=\"4\"><b>Auf Schiffen</b></td></tr>";
	while($data=mysql_fetch_assoc($ship))
	{
		echo "<tr>
		<td><a href=?p=".($data['slots'] > 0 ? "stat" : "ship")."&s=ss&id=".$data['id']."><img src=".$gfx."/ships/".$data['rumps_id'].".gif border=0></a></td>
		<td>".stripslashes($data['name'])."</td>
		<td>".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".stripslashes($data['sysname'])."-System)" : $data['cx']."|".$data['cy'])."</td>
		<td><img src=".$gfx."/goods/".$_GET['good'].".gif> ".$data['count']."</td>
		</tr>";
	}
}
if ($trade > 0) echo "<tr><td colspan=\"3\"><a href=?p=trade&s=no>Im Lager der Warenbörse</a></td><td><img src=".$gfx."/goods/".$_GET['good'].".gif> ".$trade."</td></tr>";
if ($offers > 0) echo "<tr><td colspan=\"3\"><a href=?p=trade&s=no>In Angeboten in der Warenbörse</a></td><td><img src=".$gfx."/goods/".$_GET['good'].".gif> ".$offers."</td></tr>";
echo "<tr><td colspan=\"4\" style=\"text-align: center;\"><input type=button class=button value=Schließen onClick=\"cClick();\"></td></tr></table>";
?>