<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;

@session_start();

if (!check_int($_GET['id']) || !check_int($_GET['cx']) || !check_int($_GET['cy']) || $_SESSION['login'] != 1) exit;

$data = $db->query("SELECT id,name,systems_id,cx,cy,lss_range FROM stu_ships WHERE id=".$_GET['id']." AND user_id=".$_SESSION['uid'],4);
if ($data == 0) exit;

if ($_GET['cx'] < $data['cx']-$data['lss_range'] || $_GET['cx'] > $data['cx']+$data['lss_range']) exit;
if ($_GET['cy'] < $data['cy']-$data['lss_range'] || $_GET['cy'] > $data['cy']+$data['lss_range']) exit;

if ($_GET['cx'] < 1 || $_GET['cx'] > $mapfields['max_x']) exit;
if ($_GET['cy'] < 1 || $_GET['cy'] > $mapfields['max_y']) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$result = $db->query("SELECT a.user_id,a.ships_id,a.rumps_id,UNIX_TIMESTAMP(a.date) as date_tsp,b.user,c.mode,d.type FROM stu_sectorflights as a LEFT JOIN stu_user as b ON b.id=a.user_id LEFT JOIN stu_contactlist as c ON c.recipient=a.user_id AND c.user_id=".$_SESSION['uid']." LEFT JOIN stu_ally_relationship as d ON (d.allys_id1=a.allys_id AND d.allys_id2=".$_SESSION['allys_id'].") OR (d.allys_id2=a.allys_id AND d.allys_id1=".$_SESSION['allys_id'].") WHERE a.systems_id=0 AND a.cx=".$_GET['cx']." AND a.cy=".$_GET['cy']." AND a.cloak='0' AND a.user_id!=".$_SESSION['uid']." GROUP BY a.ships_id");

echo "<table class=tcal style=\"border: 1px groove #8897cf;\"><th colspan=\"4\">Sektorinformation ".$_GET['cx']."|".$_GET['cy']."</th><tr></tr><td></td><td>Siedler</td><td>Letzter Kontakt</td><td>Status</td></td>";
while($data=mysql_fetch_assoc($result))
{
	echo "<tr><td><img src=".$gfx."/ships/".$data['rumps_id'].".gif></td>
	<td>".stripslashes($data['user'])." (".$data['user_id'].")</td>
	<td>".date("d.m H:i",$data['date_tsp'])."</td>
	<td>".getUserRelationship($data)."</td>
	</tr>";
}
echo "<tr><td colspan=\"4\"><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table>";
?>