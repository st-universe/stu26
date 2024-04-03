<?php
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
if (!$_GET[p] || $_GET[p] == "ma")
{
	$sresult = $db->query("SELECT * FROM stu_rumps ORDER BY rumps_id");
	$mresult = $db->query("SELECT * FROM stu_modules ORDER BY type,level");
	echo "<html>
	<head>
	<title>Baukosten (Schiffe/Module)</title>
	</head>
	</html>
	<table width=800>
	<th width=50%>Schiff wählen</th>
	<th width=50%>Modul wählen</th>
	<tr>
	<td valign=top>";
	while($data=mysql_fetch_assoc($sresult)) echo "<img src=../gfx/ships/".$data[rumps_id].".gif> <a href=cost.php?p=sc&id=".$data[rumps_id].">".$data[name]."</a> (".$data[rumps_id].")<br>";
	echo "</td>
	<td valign=top>";
	while($data=mysql_fetch_assoc($mresult)) echo "<a href=cost.php?p=mc&id=".$data[module_id].">".$data[name]."</a> (".$data[module_id].")<br>";
	echo "</td>
	</tr>
	</table>";
}
if ($_GET[p] == "sc")
{
	if (is_array($_GET[gc]))
	{
		$db->query("DELETE FROM stu_rumps_buildcost WHERE rumps_id=".$_GET[id]);
		foreach($_GET[gc] as $key => $value)
		{
			if ($key <= 0 || !is_numeric($key) || $value <= 0 || !is_numeric($value)) continue;
			$db->query("INSERT INTO stu_rumps_buildcost (rumps_id,goods_id,count) VALUES ('".$_GET[id]."','".$key."','".$value."')");
		}
		$db->query("UPDATE stu_rumps SET eps_cost=".$_GET[ec]." WHERE rumps_id=".$_GET[id]);
		$return = "Baukosten geändert!<br><br>";
	}
	$ship = $db->query("SELECT name,eps_cost FROM stu_rumps WHERE rumps_id=".$_GET[id],4);
	$result = $db->query("SELECT a.goods_id,a.name,b.count FROM stu_goods as a LEFT JOIN stu_rumps_buildcost as b ON a.goods_id=b.goods_id AND b.rumps_id=".$_GET[id]);
	echo "<html>
	<head>
	<title>Baukosten (Schiffe: ".$ship[name].")</title>
	</head>
	</html><< <a href=cost.php>Zurück zur Übersicht</a><br><br>";
	if ($return) echo $return;
	echo "<b>Baukosten für Schiff ".$ship[name]."</b><br><br>
	<form action=cost.php method=get><input type=hidden name=p value=sc><input type=hidden name=id value=".$_GET[id].">
	<table>
	<tr><td><img src=../gfx/buttons/e_trans2.gif title=\"Energie\"> Energie</td><td><input type=text name=ec size=4 value=".$ship[eps_cost]."></td></tr>";
	while($data=mysql_fetch_assoc($result))
	{
		$i++;
		if ($i%10 == 5) echo "<tr><td colspan=2><input type=submit value=speichern></td></tr>";
		echo "<tr><td><img src=../gfx/goods/".$data[goods_id].".gif> ".$data[name]."</td><td><input type=text name=gc[".$data[goods_id]."] size=4 value=\"".$data['count']."\"></td></tr>";
	}
	echo "</form></table>";
}
if ($_GET[p] == "mc")
{
	$ship = $db->query("SELECT name FROM stu_modules WHERE module_id=".$_GET[id],1);
	if (is_array($_GET[gc]))
	{
		$db->query("DELETE FROM stu_modules_cost WHERE module_id=".$_GET[id]);
		foreach($_GET[gc] as $key => $value)
		{
			if ($key <= 0 || !is_numeric($key) || $value <= 0 || !is_numeric($value)) continue;
			$db->query("INSERT INTO stu_modules_cost (module_id,goods_id,count) VALUES ('".$_GET[id]."','".$key."','".$value."')");
		}
		$return = "Baukosten geändert!<br><br>";
	}
	$result = $db->query("SELECT a.goods_id,a.name,b.count FROM stu_goods as a LEFT JOIN stu_modules_cost as b ON a.goods_id=b.goods_id AND b.module_id=".$_GET[id]);
	echo "<html>
	<head>
	<title>Baukosten (Modul: ".$ship.")</title>
	</head>
	</html><< <a href=cost.php>Zurück zur Übersicht</a><br><br>";
	if ($return) echo $return;
	echo "<b>Baukosten für Modul ".$ship."</b><br><br>
	<form action=cost.php method=get><input type=hidden name=p value=mc><input type=hidden name=id value=".$_GET[id].">
	<table>";
	while($data=mysql_fetch_assoc($result))
	{
		$i++;
		if ($i%10 == 5) echo "<tr><td colspan=2><input type=submit value=speichern></td></tr>";
		echo "<tr><td><img src=../gfx/goods/".$data[goods_id].".gif> ".$data[name]."</td><td><input type=text name=gc[".$data[goods_id]."] size=4 value=\"".$data['count']."\"></td></tr>";
	}
	echo "</form></table>";
}
?>