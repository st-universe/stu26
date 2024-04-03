<?php
if (!is_object($db)) exit;
include_once("class/game.class.php");
$game = new game;
pageheader("/ <b>History</b>");
if (!is_numeric($_GET["t"]) || !$_GET["t"] || ($_GET["t"] < 1 || $_GET["t"] > 5)) $t = 1;
else $t = $_GET["t"];
if (!is_numeric($_GET["c"]) || !$_GET["c"] || $_GET["c"] < 50) $c = 50;
else $c = $_GET["c"];
echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=100%>
<form action=main.php method=get><input type=hidden name=p value=history><input type=hidden name=t value=".$t.">
<tr>
	<td class=m colspan=5>Ereignistyp | <input type=text size=3 class=text name=c value=".$c."> Ereignisse <input type=submit value=anzeigen class=button></td>
</tr>
</form>
<tr>
	<th width=13%>".($t == 1 ? "<b>Schiffe</b>" : "<a href=?p=history&t=1>Schiffe</a>")." (".$game->gethistorycountbytype(1).")</th>
	<th width=13%>".($t == 2 ? "<b>Kolonien</b>" : "<a href=?p=history&t=2>Kolonien</a>")." (".$game->gethistorycountbytype(2).")</th>
	<th width=13%>".($t == 3 ? "<b>Diplomatie</b>" : "<a href=?p=history&t=3>Diplomatie</a>")." (".$game->gethistorycountbytype(3).")</th>
	<th width=13%>".($t == 5 ? "<b>Rassenkonflikt</b>" : "<a href=?p=history&t=5>Rassenkonflikt</a>")." (".$game->gethistorycountbytype(5).")</th>
	<td width=48%>&nbsp;</td>
</tr></table>
<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=100%>";
$tc = $game->gethistory($c,$t);
if (mysql_num_rows($tc) == 0) meldung("Keine Einträge vorhanden");
else
{
	while($data=mysql_fetch_assoc($tc)) echo "<tr><td>".stripslashes($data[message])."</td><td width=115>".date("d.m.",$data[date_tsp]).setyear(date("Y",$data[date_tsp])).date(" H:i",$data[date_tsp])."</td></tr>";
}
echo "</table>";
?>