<?php
if (!is_object($db)) exit;
include_once("class/map.class.php");
$map = new map;
include_once("class/ship.class.php");
$ship = new ship;
include_once("class/fleet.class.php");
$fleet = new fleet;
include_once("inc/lists/goods.php");
include_once("class/ferg.class.php");
$ferg = new ferg;

if ($ship->destroyed == 1 || $ship->dsships[$_GET['id']] == 1 || $fleet->dsships[$_GET['id']] == 1)
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <b>Schiffsdetails</b>");
	meldung("Du bist nicht Besitzer dieses Schiffes");
	exit;
}
$res = $db->query("UPDATE stu_ferg_dabo SET date=NOW() WHERE user_id=".$_SESSION['uid']." LIMIT 1",6);
if ($res == 0) $db->query("INSERT INTO stu_ferg_dabo (user_id,date) VALUES ('".$_SESSION['uid']."',NOW())");

switch($_GET['s'])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
}

echo "<script language=\"Javascript\">
function registerbid(cn)
{
	elt = 'dabo';
	sendRequest('backend/dabo.php?PHPSESSID=".session_id()."&id=' + ".$_GET['id']." + '&cn=' + cn);
}
function refresh()
{
	window.location.href = 'main.php?p=fergp&id=".$_GET['id']."';
}
</script>
<style>
td.pages {
	text-align: center;
	width: 20px;
	border: 1px groove #8897cf;
}
td.pages:hover
{
	background: #262323;
}
</style>";

function get_pre($i)
{
	$di = floor($i/4);
	$re = $i-($di*4);
	if ($re == 1) return "Single";
	if ($re == 2) return "Double";
	if ($re == 3) return "Triple";
	if ($re == 0) return "Quadruple";
}

function get_post($i)
{
	$di = ceil($i/4);
	switch ($di)
	{
		case 1:
			return "Top";
		case 2:
			return "Higher";
		case 3:
			return "Over";
		case 4:
			return "Middle";
		case 5:
			return "Under";
		case 6:
			return "Lower";
		case 7:
			return "Bottom";
	}
}

if ($v == "main")
{
	pageheader("/ <a href=?p=ship>Schiffe</a> / <a href=?p=ship&s=ss&id=".$ship->id.">".strip_tags(stripslashes($ship->name))."</a> / <b>Ferengi-Posten</b>");
	if ($_GET['a'] == "tw") meldung($ferg->changegood());
	$lr = $db->query("SELECT value FROM stu_game_vars WHERE var='dabo_lastresult'",1);
	echo "<table width=\"100%\">
	<tr>
	<form action=main.php method=get><input type=hidden name=p value=fergp><input type=hidden name=id value=".$_GET['id'].">
	<input type=hidden name=a value=tw>
	<td style=\"width: 40%;\" valign=\"top\">
	
	<table class=\"tcal\">
	<th>Dabo!</th>
	<tr><td>Letztes Ergebnis: ".($lr == 0 ? "-" : get_pre($lr)." ".get_post($lr))."</td></tr>
	<tr><td>Letzte Runde: ";
	$date = $db->query("SELECT value FROM stu_game_vars WHERE var='dabo_lastround'",1);
	echo date("d.m.",$date).setyear(date("Y",$date)).date(" H:i",$date);
	echo "</td></tr>
	<tr><td>Nächste Runde: ".date("d.m.",$date+600).setyear(date("Y",$date+600)).date(" H:i",$date+600)."</td></tr>
	<tr><td>Letzte Gewinner: ".$db->query("SELECT value FROM stu_game_vars WHERE var='dabo_lastuser'",1)."</td></tr>
	<tr><td>Aktueller Jackpot: <img src=".$gfx."/goods/8.gif title=\"Dilithium\"> ".$db->query("SELECT value FROM stu_game_vars WHERE var='dabo_jackpot'",1)."</td></tr>
	<tr>
	<td>
	<div id=\"dabo\">";
	$dt = $db->query("SELECT bid FROM stu_ferg_dabo WHERE user_id=".$_SESSION['uid'],1);
	if ($dt != 0) echo "Du tippst auf: ".get_pre($dt)." ".get_post($dt);
	else
	{
		echo "<table><th colspan=\"4\">Tipp abgeben</th><tr>";
		$i=1;
		while($i<=28)
		{
			echo "<td class=\"pages\"><a href=\"javascript:void(0);\" onClick=\"registerbid(".$i.");\"><img src=gfx/dabo/".$i.".gif title=\"".get_pre($i)." ".get_post($i)."\" border=\"0\"></a></td>";
			if ($i%4 == 0) echo "</tr><tr>";
			$i++;
		}
		echo "</tr></table>";
	}
	echo "</div>
	</td>
	</tr>
	<tr>
	<td><input type=button class=button value=\"Aktualisieren\" onClick=\"refresh();\"></td>
	</tr>
	</table><br>
	
	<table class=\"tcal\">
	<th>Gutschein eintauschen</th>
	<tr>
	<td>Beim einlösen bitte beachten, dass genügend Laderaum auf dem Schiff vorhanden ist<br><br>
	<input type=\"submit\" value=\"Eintauschen\" class=\"button\"></td>
	</tr>
	</form>
	</table>
	</td>
	<td style=\"width: 5%;\"></td>
	<td style=\"width: 55%;\" valign=\"top\">
	<table class=\"tcal\">
	<th colspan=\"2\">Siedler in der Bar</th><tr><td>Siedler</td><td>Spielt Dabo?</td></tr>";
	$result = $db->query("SELECT a.user_id,a.bid,b.user FROM stu_ferg_dabo as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE UNIX_TIMESTAMP(a.date)>120");
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=\"3\">Keine Siedler in der Bar</td></tr>";
	else while($data=mysql_fetch_assoc($result)) echo "<tr><td><a href=?p=comm&s=nn&recipient=".$data['user_id']." ".getonm('pm'.$data['user_id'],'buttons/msg')."><img src=".$gfx."/buttons/msg1.gif border=0 title=\"PM an ".ftit($data['user'])." schicken\" name=pm".$data['user_id']."></a> ".stripslashes($data['user'])." (".$data['user_id'].")</td><td>".($data['bid'] > 0 ? "<font color=Green>Ja</font>" : "<font color=#FF0000>Nein</font>")."</td></tr>";

	echo "</table>
	</td>
	</tr>
	</table>";
}
?>