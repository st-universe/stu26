<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION["login"] != 1 || !check_int($_GET['x']) || !check_int($_GET['y']) || $_GET['x'] <= 0 || $_GET['y'] <= 0) exit;

$db = new db;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$cx = floor(($_GET['x']-2)/3);
$cy = floor(($_GET['y']-2)/3);

if ($cx > $mapfields['max_x'] || $cy > $mapfields['max_y']) exit;

echo "<table class=Tcal style=\"border: 1px groove #8897cf;\">
<th colspan=\"2\">Ereignisse in Sektor ".$cx."|".$cy." innerhalb der letzten 24 Stunden</th>";
$result = $db->query("SELECT message,UNIX_TIMESTAMP(date) as date_tsp FROM stu_history WHERE coords_x=".$cx." AND coords_y=".$cy." AND UNIX_TIMESTAMP(date)>".(time()-86400)." ORDER BY date DESC");
while($data=mysql_fetch_assoc($result))
{
	echo "<tr>
	<td>".date("H:i",$data['date_tsp'])."</td>
	<td>".stripslashes($data['message'])."</td>
	</tr>";
}
echo "<tr><td colspan=\"2\"><input type=\"button\" class=\"button\" value=\"Schließen\" onClick=\"cClick();\"></tr></table>";
?>