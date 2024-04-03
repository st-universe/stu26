<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");

@session_start();

if ($_SESSION['login'] != 1 || !check_int($_GET['good'])) exit;
$db = new db;

include_once("../../class/comm.class.php");
$comm = new comm;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";


echo "<form action=main.php method=post name=tr><input type=hidden name=p value=trade>
<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
<input type=hidden name=s value=po><input type=hidden name=a value=tr>
<input type=hidden name=good value=".$_GET['good'].">
<th onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">Warenüberweisung</th>
<tr>
	<td>Bei jeder Überweisung werden 5% des Werts als Gebühr abgezogen.</td>
</tr>
<tr><td>Empfänger-ID <input type=text size=5 name=recipient class=text> <select name=rl onChange=ausgabe();><option value=>-------------";
$result = $comm->getcontacts();
if (mysql_num_rows($result) != 0) while($data=mysql_fetch_assoc($result)) echo "<option value=".$data['recipient'].">".stripslashes(strip_tags($data['user']))." (".$data['recipient'].")";
echo "</select></td></tr>
<tr>
	<td>Anzahl <input type=text name=count class=text size=4> <img src=".$gfx."/goods/".$_GET['good'].".gif></td>
</tr>
<tr><td align=\"center\"><input type=submit class=button value=Überweisen> <input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr>
</table>
</form>";
?>