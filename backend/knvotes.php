<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if (!check_int($_GET[id]) || $_SESSION["login"] != 1) exit;
$res = $_GET[off];

$db = new db;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

echo "<table class=\"tcal\" style=\"border: 1px groove #8897cf;\"><th colspan=\"2\">Votings für Beitrag ID ".$_GET[id]."</th>";
// $result = $db->query("SELECT a.user_id,a.rating,b.user FROM stu_kn_rating as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.kn_id=".$_GET[id]);
// if (mysql_num_rows($result) == 0) echo "<tr><td colspan=\"2\">Für diesen Beitrag sind keine Votings vorhanden</td></tr>";
// else
// {
	// while($data=mysql_fetch_assoc($result))
	// {
		// echo "<tr><td>".stripslashes($data[user])."</td><td>";
		// for($i=1;$i<=$data[rating];$i++) echo "<img src=".$gfx."/buttons/stern1.gif>&nbsp;";
		// echo "</td></tr>";
	// }

// }
$result = $db->query("SELECT COUNT(kn_id) as cnt, rating FROM `stu_kn_rating` WHERE kn_id=".$_GET[id]." GROUP BY rating ORDER BY rating DESC");
if (mysql_num_rows($result) == 0) echo "<tr><td colspan=\"2\">Für diesen Beitrag sind keine Votings vorhanden</td></tr>";
else {
	while($data=mysql_fetch_assoc($result))
	{
		echo "<tr><td><center>";
		for($i=1;$i<=$data[rating];$i++) echo "<img src=".$gfx."/buttons/stern1.gif>&nbsp;";
		for($i=1;$i<=(5-$data[rating]);$i++) echo "<img src=".$gfx."/buttons/stern3.gif>&nbsp;";
		echo "</td></center>";
		if ($data[cnt] < 10) echo "<td> x&nbsp;&nbsp;".$data[cnt]."</td>";
		else echo "<td> x ".$data[cnt]."</td>";
		echo "</tr>";
	}
}
echo "<tr><td align=\"center\" colspan=2><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table>";
?>