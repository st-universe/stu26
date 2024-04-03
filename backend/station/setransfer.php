<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;
if (!check_int($_GET['id']) || !check_int($_GET['t']) || !check_int($_GET['e'])) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../../gfx/";

$tar = $db->query("SELECT id,name,cloak,cx,cy,sx,sy,systems_id FROM stu_ships WHERE id=".$_GET['t']." LIMIT 1",4);
if ($tar == 0) exit;
$ship = $db->query("SELECT a.cx,a.cy,a.sx,a.sy,systems_id,b.slots FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.user_id=".$_SESSION['uid']." AND a.id=".$_GET['id']." LIMIT 1",4);
if ($ship == 0) exit;
	
if ($tar['cloak'] == 1) exit;
if ($ship['systems_id'] > 0 || $tar['systems_id'] > 0)
{
	if ($ship['systems_id'] != $tar['systems_id']) exit;
	if ($ship['sx'] != $tar['sx'] || $ship['sy'] != $tar['sy']) exit;
}
else
{
	if ($ship['cx'] != $tar['cx'] || $ship['cy'] != $tar['cy']) exit;
}

echo "<form action=main.php method=get name=\"pform\"><input type=hidden name=p value=stat>
<table class=\"tcal\" style=\"border: 1px solid #8897cf;\">
<input type=hidden name=s value=ss><input type=hidden name=a value=et>
<input type=hidden name=id value=".$_GET["id"]."><input type=hidden name=t value=".$_GET['t'].">
<tr><th onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">Ziel: ".stripslashes($tar['name'])."</th><th width=\"16\"><a href=\"javascript:void(0);\" onClick=\"cClick();\" ".getonm('clx','buttons/x')."><img src=".$gfx."/buttons/x1.gif name=\"clx\" border=0 title=\"Schließen\"></a></th></tr>
<tr><td colspan=\"2\"><input type=text size=3 name=count class=text> / ".$_GET['e']." <input type=submit value=Transfer class=button> <input type=submit name=count value=max class=button></td></tr>
</form>
</table>";
?>