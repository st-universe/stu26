<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION["login"] != 1) exit;

$db = new db;
include_once("../class/comm.class.php");
$comm = new comm;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

if (!$_GET['m'] || $_GET['m'] == "ac")
{
	echo "<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
	<th colspan=\"4\">Auktionsstatus</th>
	<tr>
	<td>Angebot</td><td>Aktuelles Gebot</td><td>Höchstbietender</td><td>Restzeit</td>
	</tr>";
	$result = $db->query("SELECT b.give_count,b.give_good,b.want_good,UNIX_TIMESTAMP(b.date) as date_tsp,b.want_count,b.want_user_id,c.user FROM stu_trade_ferg_history as a LEFT JOIN stu_trade_ferg as b ON b.trade_id=a.trade_id LEFT JOIN stu_user as c ON c.id=b.want_user_id WHERE a.user_id=".$_SESSION['uid']." AND UNIX_TIMESTAMP(b.date)+259200>".time()." GROUP BY a.trade_id ORDER BY b.date");
	while($data=mysql_fetch_assoc($result))
	{
		echo "<tr>
		<td><img src=".$gfx."/goods/".$data['give_good'].".gif> ".$data['give_count']."</td>
		<td>".($data['want_user_id'] > 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif> ".$data['want_count'] : " - ")."</td>
		<td>".stripslashes($data['user'])."</td>
		<td>".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		</tr>";
	}
	echo "<tr><td align=\"center\" colspan=\"4\"><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table>";
}
if ($_GET['m'] == "oac")
{
	echo "<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
	<th colspan=\"4\">Meine Auktionen</th>
	<tr>
	<td>Angebot</td><td>Aktuelles Gebot</td><td>Höchstbietender</td><td>Restzeit</td>
	</tr>";
	$result = $db->query("SELECT a.give_count,a.give_good,a.want_good,UNIX_TIMESTAMP(a.date) as date_tsp,a.want_count,a.want_user_id,b.user FROM stu_trade_ferg as a LEFT JOIN stu_user as b ON b.id=a.want_user_id WHERE a.user_id=".$_SESSION['uid']." AND UNIX_TIMESTAMP(a.date)+259200>".time()." ORDER BY a.date");
	while($data=mysql_fetch_assoc($result))
	{
		echo "<tr>
		<td><img src=".$gfx."/goods/".$data['give_good'].".gif> ".$data['give_count']."</td>
		<td>".($data['want_user_id'] > 0 ? "<img src=".$gfx."/goods/".$data['want_good'].".gif> ".$data['want_count'] : " - ")."</td>
		<td>".stripslashes($data['user'])."</td>
		<td>".($data['date_tsp']+259200 < time()+3600 ? "<font color=#FF0000>".gen_time(($data['date_tsp']+259200)-time())."</font>" : gen_time(($data['date_tsp']+259200)-time()))."</td>
		</tr>";
	}
	echo "<tr><td align=\"center\" colspan=\"4\"><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table>";
}
?>