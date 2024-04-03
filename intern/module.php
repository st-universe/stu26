<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
if (!$_GET[p] || $_GET[p] == "ma")
{
	$result = $db->query("SELECT * FROM stu_modules ORDER BY module_id");
	echo "<html>
	<head>
	<title>Modulwerte</title>
	</head>
	</html>
	<< <a href=index.html>Zurück zum internen Bereich</a><br><br>
	<table border=1>";
	while ($data=mysql_fetch_assoc($result))
	{
		echo "<td><img src=../gfx/goods/".$data[module_id].".gif></td><td><a href=?p=em&id=".$data[module_id].">".$data[name]."</a> (".$data[module_id].")</td>";
		$i++;
		if ($i%3==0) echo "</tr><tr>";
	}
	echo "</tr></table>";
}
if ($_GET[p] == "em")
{
	if (is_array($_GET[da]))
	{
		$da = $_GET[da];
		$db->query("UPDATE stu_modules SET name='".addslashes($da[name])."',level=".$da[level].",ecost=".$da[en].",huelle=".$da[huelle].",
		schilde=".$da[schilde].",reaktor=".$da[reaktor].",wkkap=".$da[wkkap].",eps=".$da[eps].",evade=".$da[evade].",
		abfang=".$da[abfang].",torps=".$da[torps].",phaser=".$da[phaser].",vari=".$da[vari].",lss=".$da[lss].",kss=".$da[kss].",
		stellar='".($da[stellar] == 1 ? 1 : 0)."',cloak_val=".$da[cloak].",detect_val=".$da[decloak].",points='".$da[points]."',
		buildtime=".$da[buildtime].",maintaintime=".$da[maintain].",viewable='".($da[view] == 1 ? 1 : 0)."',research_id=".$da[res]." WHERE module_id=".$_GET[id]);
		$return = "Modul editiert<br><br>";
	}
	$data = $db->query("SELECT * FROM stu_modules WHERE module_id=".$_GET[id],4);
	echo "<html>
	<head>
	<title>Modul editieren (".$data[name].")</title>
	</head>
	</html>
	<< <a href=module.php>Zurück zur Übersicht</a><br><br>";
	if ($return) echo $return;
	echo "<b>Modul ".$data[name]." editieren</b><br><br>
	<form action=module.php method=get><input type=hidden name=p value=em><input type=hidden name=id value=".$_GET[id].">
	<table border=1>
	<tr>
		<td>Modultyp</td>
		<td><b>".getmodtypedescr($data[type])."</b></td>
	</tr>
	<tr>
		<td>Level</td>
		<td><input type=text size=2 name=da[level] value=".$data[level]."></td>
	</tr>
	<tr>
		<td>Name</td>
		<td><input type=text size=20 name=da[name] value=\"".$data[name]."\"</td>
	</tr>
	<tr>
		<td>Baukosten (Energie)</td>
		<td><input type=text size=2 name=da[en] value=".$data[ecost]."></td>
	</tr>
	<tr>
		<td>Hüllenwert</td>
		<td><input type=text size=3 name=da[huelle] value=".$data[huelle]."></td>
	</tr>
	<tr>
		<td>Schildwert</td>
		<td><input type=text size=3 name=da[schilde] value=".$data[schilde]."></td>
	</tr>
	<tr>
		<td>Reaktorwert</td>
		<td><input type=text size=2 name=da[reaktor] value=".$data[reaktor]."></td>
	</tr>
	<tr>
		<td>Warpkernkapazität</td>
		<td><input type=text size=2 name=da[wkkap] value=".$data[wkkap]."></td>
	</tr>
	<tr>
		<td>EPS-Wert</td>
		<td><input type=text size=2 name=da[eps] value=".$data[eps]."></td>
	</tr>
	<tr>
		<td>Ausweich-Wahrscheinlichkeit</td>
		<td><input type=text size=2 name=da[evade] value=".$data[evade]."></td>
	</tr>
	<tr>
		<td>Abfang-Wahrscheinlichkeit</td>
		<td><input type=text size=2 name=da[abfang] value=".$data[abfang]."></td>
	</tr>
	<tr>
		<td colspan=2><input type=submit value=Speichern></td>
	</tr>
	<tr>
		<td>Torpedo-Kapazität</td>
		<td><input type=text size=3 name=da[torps] value=".$data[torps]."></td>
	</tr>
	<tr>
		<td>Schadenswert (Strahlenwaffen)</td>
		<td><input type=text size=3 name=da[phaser] value=".$data[phaser]."></td>
	</tr>
	<tr>
		<td>Varianzwert</td>
		<td><input type=text size=2 name=da[vari] value=".$data[vari]."></td>
	</tr>
	<tr>
		<td>Langstreckensensorenwert</td>
		<td><input type=text size=2 name=da[lss] value=".$data[lss]."></td>
	</tr>
	<tr>
		<td>Kurzstreckensensorenwert</td>
		<td><input type=text size=2 name=da[kss] value=".$data[kss]."></td>
	</tr>
	<tr>
		<td>Ermöglicht Kartografie?</td>
		<td><input type=\"checkbox\" name=\"da[stellar]\" value=\"1\" ".($data[stellar] == 1 ? "CHECKED" : "")."></td>
	</tr>
	<tr>
		<td>Tarnwert</td>
		<td><input type=text size=2 name=da[cloak] value=".$data[cloak_val]."></td>
	</tr>
	<tr>
		<td>Enttarnwert</td>
		<td><input type=text size=2 name=da[decloak] value=".$data[detect_val]."></td>
	</tr>
	<tr>
		<td>Wirtschaftspunkte</td>
		<td><input type=text size=5 name=da[points] value=".$data[points]."></td>
	</tr>
	<tr>
		<td>Bauzeit</td>
		<td><input type=text size=7 name=da[buildtime] value=".$data[buildtime]."></td>
	</tr>
	<tr>
		<td>Wartungszeitraum</td>
		<td><input type=text size=7 name=da[maintain] value=".$data[maintaintime]."></td>
	</tr>
	<tr>
		<td>In der Modulliste anzeigen?</td>
		<td><input type=\"checkbox\" name=\"da[view]\" value=\"1\" ".($data[viewable] == 1 ? "CHECKED" : "")."></td>
	</tr>
	<tr>
		<td>Forschungs-ID</td>
		<td><input type=text size=6 name=da[res] value=".$data[research_id]."></td>
	</tr>
	<tr>
		<td colspan=2><input type=submit value=Speichern></td>
	</tr>
	</table></form>";
}
?>