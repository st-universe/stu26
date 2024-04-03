<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;
if (!check_int($_GET['pid'])) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

echo "<div id=\"bpcw\">
<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
<th onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">Bauplan verschicken an</th>";
$result = $db->query("SELECT a.id,a.user FROM stu_user as a LEFT JOIN stu_contactlist as b ON b.user_id=a.id AND b.recipient=".$_SESSION['uid']." WHERE a.id!=".$_SESSION['uid']." AND a.race='".$_SESSION['race']."' AND  ((a.allys_id=".$_SESSION['allys_id']." AND a.allys_id>0) OR b.mode='1')");
while($data=mysql_fetch_assoc($result))
{
	echo "<tr><td><a href=\"main.php?p=colony&s=sb&a=sbp&id=".$_GET['id']."&pid=".$_GET['pid']."&rec=".$data['id']."\">".stripslashes($data['user'])." (".$data['id'].")</td></tr>";
}
echo "<tr><td>&nbsp;</td></tr>
<input type=hidden name=ps value=".$_SESSION['pagesess'].">
<tr><td><input type=\"button\" value=\"Schließen\" class=\"button\" onClick=\"cClick();\"></td></tr>
</table>
</div>";
?>
