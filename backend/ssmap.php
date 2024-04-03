<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION['login'] != 1) exit;

if (!$_GET['sstring'] || strlen($_GET['sstring']) < 2) exit;

$db = new db;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$result = $db->query("SELECT a.systems_id,a.cx,a.cy,a.name,a.type FROM stu_systems as a LEFT JOIN stu_systems_user as b USING(systems_id) WHERE b.user_id=".$_SESSION['uid']." AND a.name LIKE '%".addslashes($_GET['sstring'])."%'");
echo "<table style=\"border: 1px solid #2d3243; width: 300px;\" cellpadding=\"0\" cellspacing=\"0\">";
if (mysql_num_rows($result) == 0) echo "<tr><td id=\"bla\">Kein System gefunden</td></tr>";
else
{
	while ($data=mysql_fetch_assoc($result))
	{
		echo "<tr>
			<td id=\"bla\" width=\"30\" rowspan=\"2\"><img src=".$gfx."/map/".$data['type'].".gif></td>
			<td id=\"bla\"><a href=../main.php?p=map&s=ss&id=".$data['systems_id'].">".stripslashes($data['name'])."-System</a></td>
		</tr>
		<tr>
			<td>Koordinaten: ".$data['cx']."|".$data['cy']."</td>
		</tr>";
	}
}
echo "</table>";
?>