<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
if (!$_GET[p] || $_GET[p] == "ma")
{
	$result = $db->query("SELECT * FROM stu_rumps ORDER BY sort,rumps_id");
	echo "<html>
	<head>
	<title>Schiffswerte</title>
	</head>
	</html>
	<< <a href=index.html>Zurück zum internen Bereich</a><br><br>
	<table border=1>
	<th></th><th>Name</th><tr>";
	while ($data=mysql_fetch_assoc($result))
	{
		echo "<td><img src=../gfx/ships/".$data[rumps_id].".gif></td><td><a href=?p=es&id=".$data[rumps_id].">".$data[name]."</a> (".$data[rumps_id].")</td>";
		$i++;
		if ($i%2==0) echo "</tr><tr>";
	}
	echo "</tr></table>";
}
if ($_GET[p] == "es")
{
	if (is_array($_GET[da]))
	{
		$da = $_GET[da];
		$db->query("UPDATE stu_rumps SET name='".$da[name]."',npc=".($da[npc] == 1 ? "'1'" : "'0'").",eps_cost=".$da[en].",storage=".$da[storage].",min_crew=".$da[crew].",max_crew=".$da[mcrew].",bussard=".$da[bus].",erz=".$da[erz].",slots=".$da[slots].",reaktor=".$da[reaktor].",is_shuttle='".($da[ishut] == 1 ? 1 : 0)."',max_shuttles=".$da[max_shuttles].",max_shuttle_type=".$da[msht].",max_cshuttle_type=".$da[mcsht].",replikator=".($da[rep] == 1 ? "'1'" : "'0'").",warpable=".($da[warp] == 1 ? "'1'" : "'0'").",
		cloakable=".($da[cloak] == 1 ? "'1'" : "'0'").",buildtime=".$da[build].",maintaintime=".$da[main].",evade_val=".$da[evade].",wp='".$da[wp]."',m1c=".$da[m1c].",m1minlvl=".$da[m1minlvl].",m1maxlvl=".$da[m1maxlvl]."
		,m2c=".$da[m2c].",m2minlvl=".$da[m2minlvl].",m2maxlvl=".$da[m2maxlvl].",m3c=".$da[m3c].",m3minlvl=".$da[m3minlvl].",m3maxlvl=".$da[m3maxlvl]."
		,m4c=".$da[m4c].",m4minlvl=".$da[m4minlvl].",m4maxlvl=".$da[m4maxlvl].",m5c=".$da[m5c].",m5minlvl=".$da[m5minlvl].",m5maxlvl=".$da[m5maxlvl]."
		,m6c=".$da[m6c].",m6minlvl=".$da[m6minlvl].",m6maxlvl=".$da[m6maxlvl].",m7c=".$da[m7c].",m7minlvl=".$da[m7minlvl].",m7maxlvl=".$da[m7maxlvl]."
		,m8c=".$da[m8c].",m8minlvl=".$da[m8minlvl].",m8maxlvl=".$da[m8maxlvl].",m9c=".$da[m9c].",m9minlvl=".$da[m9minlvl].",m9maxlvl=".$da[m9maxlvl]."
		,m10c=".$da[m10c].",m10minlvl=".$da[m10minlvl].",m10maxlvl=".$da[m10maxlvl].",m11c=".$da[m11c].",m11minlvl=".$da[m11minlvl].",m11maxlvl=".$da[m11maxlvl]." WHERE rumps_id=".$_GET[id]);
		$return = "Schiffswerte geändert<br><br>";
	}
	$data = $db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$_GET[id],4);
	echo "<html>
	<head>
	<title>Schiff editieren (".$data[name].")</title>
	</head>
	</html>
	<< <a href=ship.php>Zurück zur Übersicht</a><br><br>";
	if ($return) echo $return;
	echo "<b>Schiff ".$data[name]." editieren</b><br><br>
	<form action=ship.php method=get><input type=hidden name=p value=es><input type=hidden name=id value=".$_GET[id].">
	<table>
	<tr>
	<td valign=top>
	<table border=1>
	<tr>
		<td>Name</td>
		<td><input type=text size=10 name=da[name] value=\"".stripslashes($data[name])."\"></td>
	</tr>
	<tr>
		<td>NPC-Schiff?</td>
		<td><input type=\"checkbox\" name=\"da[npc]\" value=\"1\" ".($data[npc] == 1 ? "checked" : "")."></td>
	</tr>
	<tr>
		<td>Baukosten (Energie)</td>
		<td><input type=txt size=3 name=da[en] value=".$data[eps_cost]."></td>
	</tr>
	<tr>
		<td>Ladung</td>
		<td><input type=text size=5 name=da[storage] value=".$data[storage]."></td>
	</tr>
	<tr>
		<td>Minimale Crew</td>
		<td><input type=text size=3 name=da[crew] value=".$data[min_crew]."></td>
	</tr>
	<tr>
		<td>Maximale Crew</td>
		<td><input type=text size=3 name=da[mcrew] value=".$data[max_crew]."></td>
	</tr>
	<tr>
		<td>Bussard-Kapazität</td>
		<td><input type=text size=3 name=da[bus] value=".$data[bussard]."></td>
	</tr>
	<tr>
		<td>Erzkollektor-Kapazität</td>
		<td><input type=text size=3 name=da[erz] value=".$data[erz]."></td>
	</tr>
	<tr>
		<td>Docking-Slots</td>
		<td><input type=text size=3 name=da[slots] value=".$data[slots]."></td>
	</tr>
	<tr>
		<td>Fusionsreaktorleistung</td>
		<td><input type=text size=2 name=da[reaktor] value=".$data[reaktor]."></td>
	</tr>
	<tr>
		<td>Ist Shuttle?</td>
		<td><input type=\"checkbox\" name=\"da[ishut]\" value=\"1\" ".($data[is_shuttle] == 1 ? "checked" : "")."></td>
	</tr>
	<tr>
		<td>max shuttles</td>
		<td><input type=text size=2 name=da[max_shuttles] value=".$data[max_shuttles]."></td>
	</tr>
	<tr>
		<td>max shuttle typ</td>
		<td><input type=text size=2 name=da[msht] value=".$data[max_shuttle_type]."></td>
	</tr>
	<tr>
		<td>gleichzeitige shuttle typen</td>
		<td><input type=text size=2 name=da[mcsht] value=".$data[max_cshuttle_type]."></td>
	</tr>
	<tr>
		<td>Besitzt Replikator?</td>
		<td><input type=\"checkbox\" name=\"da[rep]\" value=\"1\" ".($data[replikator] == 1 ? "checked" : "")."></td>
	</tr>
	<tr>
		<td>Ist warpfähig?</td>
		<td><input type=\"checkbox\" name=\"da[warp]\" value=\"1\" ".($data[warpable] == 1 ? "checked" : "")."></td>
	</tr>
	<tr>
		<td>Ist tarnfähig?</td>
		<td><input type=\"checkbox\" name=\"da[cloak]\" value=\"1\" ".($data[cloakable] == 1 ? "checked" : "")."></td>
	</tr>
	<tr>
		<td>Basis-Torpedoausweich-%</td>
		<td><input type=\"text\" name=\"da[evade]\" value=\"".$data[evade_val]."\"></td>
	</tr>
	<tr>
		<td>Bauzeit</td>
		<td><input type=text size=8 name=da[build] value=".$data[buildtime]."></td>
	</tr>
	<tr>
		<td>Wartungszeitraum</td>
		<td><input type=text size=8 name=da[main] value=".$data[maintaintime]."></td>
	</tr>
	<tr>
		<td>Wirtschaftspunkte (x.x)</td>
		<td><input type=text size=6 name=da[wp] value=".$data[wp]."></td>
	</tr>
	<tr><td colspan=2><input type=submit value=Speichern></td></tr>
	</table>
	</td>
	<td valign=top><table border=1>";
	for($i=1;$i<=5;$i++)
	{
		echo "<tr>
		<td colspan=2><b>Modul ".getmodtypedescr($i)."</b></td></tr>
		<tr>
			<td>Anzahl</td>
			<td><input type=text size=2 name=da[m".$i."c] value=".$data["m".$i."c"]."></td>
		</tr>
		<tr>
			<td>Minimales Level</td>
			<td><input type=text size=2 name=da[m".$i."minlvl] value=".$data["m".$i."minlvl"]."></td>
		</tr>
		<tr>
			<td>Maximales Level</td>
			<td><input type=text size=2 name=da[m".$i."maxlvl] value=".$data["m".$i."maxlvl"]."></td>
		</tr>";
	}
	echo "<tr><td colspan=2><input type=submit value=Speichern></td></tr>
	</table></td>
	<td><table border=1>";
	for($i=6;$i<=11;$i++)
	{
		echo "<tr>
		<td colspan=2><b>Modul ".getmodtypedescr($i)."</b></td></tr>
		<tr>
			<td>Anzahl</td>
			<td><input type=text size=2 name=da[m".$i."c] value=".$data["m".$i."c"]."></td>
		</tr>
		<tr>
			<td>Minimales Level</td>
			<td><input type=text size=2 name=da[m".$i."minlvl] value=".$data["m".$i."minlvl"]."></td>
		</tr>
		<tr>
			<td>Maximales Level</td>
			<td><input type=text size=2 name=da[m".$i."maxlvl] value=".$data["m".$i."maxlvl"]."></td>
		</tr>";
	}
	echo "<tr><td colspan=2><input type=submit value=Speichern></td></tr>
	</table></td>
	</tr></table>
	</form>";
}
?>