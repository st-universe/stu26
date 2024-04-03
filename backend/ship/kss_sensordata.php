<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;

@session_start();

if (!check_int($_GET['id']) || !check_int($_GET['sx']) || !check_int($_GET['sy']) || $_SESSION['login'] != 1) exit;

$data = $db->query("SELECT id,name,systems_id,sx,sy,lss_range FROM stu_ships WHERE id=".$_GET['id']." AND user_id=".$_SESSION['uid'],4);
if ($data == 0) exit;

if ($_GET['sx'] < $data['sx']-$data['lss_range'] || $_GET['sx'] > $data['sx']+$data['lss_range']) exit;
if ($_GET['sy'] < $data['sy']-$data['lss_range'] || $_GET['sy'] > $data['sy']+$data['lss_range']) exit;

$sr = $db->query("SELECT sr FROM stu_systems WHERE systems_id=".$data['systems_id'],1);
if ($_GET['sx'] < 1 || $_GET['sx'] > $sr) exit;
if ($_GET['sy'] < 1 || $_GET['sy'] > $sr) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$result = $db->query("SELECT a.user_id,a.ships_id,a.rumps_id,UNIX_TIMESTAMP(a.date) as date_tsp,b.user,c.mode,d.type FROM stu_sectorflights as a LEFT JOIN stu_user as b ON b.id=a.user_id LEFT JOIN stu_contactlist as c ON c.recipient=a.user_id AND c.user_id=".$_SESSION['uid']." LEFT JOIN stu_ally_relationship as d ON (d.allys_id1=a.allys_id AND d.allys_id2=".$_SESSION['allys_id'].") OR (d.allys_id2=a.allys_id AND d.allys_id1=".$_SESSION['allys_id'].") WHERE a.systems_id=".$data['systems_id']." AND a.sx=".$_GET['sx']." AND a.sy=".$_GET['sy']." AND a.cloak='0' AND a.user_id!=".$_SESSION['uid']." GROUP BY a.ships_id");

echo "<table class=tcal style=\"border: 1px groove #8897cf;\"><th colspan=\"4\">Sektorinformation ".$_GET['sx']."|".$_GET['sy']."</th><tr></tr><td></td><td>Siedler</td><td>Letzter Kontakt</td><td>Status</td></td>";
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