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

if ($_GET['st'] == 2)
{
	if (!check_int($_GET['gc']) || !check_int($_GET['go']) || strlen($_GET['gc']) >= 10) exit;
	$res = $db->query("SELECT a.count FROM stu_trade_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE ".$_GET['gc'].">=b.ferg_minvalue AND a.goods_id=".$_GET['go']." AND a.count>=".$_GET['gc']." AND a.offer_id=0",1);
	if ($res == 0)
	{
		unset($_GET['st']);
		$meldung = "Es wurde zu wenig von dieser Waren angeboten";
	}
}


if ($db->query("SELECT COUNT(trade_id) FROM stu_trade_ferg WHERE user_id=".$_SESSION['uid'],1) == 15)
{
	echo "<table class=\"tcal\" style=\"border: 1px groove #8897cf;\"><th>Fehler</th><tr><td>Es sind nur 15 Auktionen pro Siedler möglich</td></tr>
	<tr><td><input type=\"button\" class=\"button\" value=\"Schließen\" onClick=\"cClick();\"</td></tr></table>";
	exit;
}
// if (!$_GET['st'])
// {
	// echo "<div id=\"auwi2\"><form name=\"ferga\"><table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
	// <th colspan=\"2\">Schritt 1 - Ware auswählen</th>
	// <tr><td colspan=\"2\">Wähle hier eine Ware aus, die Du anbieten möchtest.<br>Beachte dabei, dass jede Ware eine Mindestmenge hat, die minimal angeboten werden muss und Du nur Waren anbieten kannst, die sich auf deinem Konto befinden.</td></tr>";
	// if ($meldung) echo "<tr><td colspan=\"2\">".$meldung."</td></tr>";
	// echo "<tr>
	// <td>Ware</td><td>Menge</td>
	// </tr>
	// <tr>
	// <td><span id=picgo><img src=".$gfx."/buttons/info1.gif></span> <select name=\"go\" onChange=\"chggopic();\"><option value=\"0\">----------";
	// $result = $db->query("SELECT a.goods_id,a.count,b.name,b.ferg_minvalue FROM stu_trade_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.user_id=".$_SESSION['uid']." AND a.offer_id=0 ORDER BY b.sort");
	// while($data=mysql_fetch_assoc($result)) echo "<option value=\"".$data['goods_id']."\"".($data['count'] < $data['ferg_minvalue'] ? " disabled" : "").($_GET['go'] == $data['goods_id'] ? " SELECTED" : "")."> ".$data['count']." ".$data['name']." (".$data['ferg_minvalue'].")";
	// echo "</select>
	// </td><td><input type=\"text\" size=\"4\" class=\"text\" name=\"gc\"".(check_int($_GET['gc']) ? " value=\"".$_GET['gc']."\"" : "")."></td>
	// </tr>
	// </form>";
	// echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"button\" class=\"button\" value=\"Schritt 2 >>\" onClick=\"auctionsteptwo();\">&nbsp;&nbsp;<input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table></div>";
// }


if (!$_GET['st'])
{
	echo "<div id=\"auwi2\"><form name=\"ferga\"><table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
	<th colspan=\"2\">Funktion deaktiviert</th>
	<tr><td colspan=\"2\">Aufgrund von Änderungen an der Warenbörse und seiner nur vereinzelten Benutzung wird das Ferengi-Auktionshaus in Kürze geschlossen. Es ist daher nicht mehr möglich, neue Auktionen zu starten.</td></tr>";
	echo "<tr><td align=\"center\" colspan=\"2\"><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table></div>";
}


if ($_GET['st'] == 2)
{
	if (!check_int($_GET['gc']) || !check_int($_GET['go'])) exit;
	$good_name = $db->query("SELECT name FROM stu_goods WHERE goods_id=".$_GET['go']." LIMIT 1",1);
	if ($good_name === 0) exit;
	$result = $db->query("SELECT goods_id,name FROM stu_goods WHERE goods_id!=".$_GET['go']." AND view='1' ORDER BY sort");
	while($data=mysql_fetch_assoc($result)) $ol .= "<option value=\"".$data['goods_id']."\">".$data['name'];

	echo "<form name=\"ferga\" action=\"main.php\" method=\"get\">
	<input type=hidden name=p value=fergb><input type=hidden name=a value=sa>
	<input type=hidden name=go value=".$_GET['go']."><input type=hidden name=gc value=".$_GET['gc'].">
	<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
	<th colspan=\"2\">Schritt 2 - Tauschwaren auswählen</th>
	<tr><td colspan=\"2\">Wähle hier die beiden Warentypen aus, die Du als Bezahlung akzeptieren würdest. Sobald das Erste Gebot abgegeben wurde, wird die Ware bindend die für das Erste Gebot ausgewählt wurde.</td></tr>
	<tr><td colspan=\"2\">Angeboten: <img src=".$gfx."/goods/".$_GET['go'].".gif title=\"".$good_name."\"> ".$_GET['gc']."</td></tr>
	<tr>
	<td>Ware 1</td><td>Ware 2 (Optional)</td>
	</tr>
	<tr>
	<td><select name=\"go1\">".$ol."</select></td>
	<td><select name=\"go2\"><option value=x>".$ol."</select></td>
	</tr>
	<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" class=\"button\" value=\"Auktion starten\">&nbsp;&nbsp;<input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr></table></form>";
}
?>