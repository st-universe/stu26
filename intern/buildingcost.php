<?php
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
echo "<html>
<head>
	<title>STU Gebäudebaukosten</title>
</head><body>
<< <a href=index.html>Interner Bereich</a><br><br>";
if (!$_GET[id])
{
	echo "<b>Gebäude wählen</b><br><br>";
	$result = $db->query("SELECT buildings_id,name FROM stu_buildings ORDER BY buildings_id");
	while($data=mysql_fetch_assoc($result))
	{
		$i++;
		echo "<img src=../gfx/buildings/".$data[buildings_id]."/0.png> ID ".$data[buildings_id]." - <a href=?id=".$data[buildings_id].">".$data[name]."</a> - [<a href=?id=".$data[buildings_id]."&m=w>werte</a>]<br>";
	}
}
else
{
	if (!$_GET[m])
	{
		if ($_GET["a"] == "ck")
		{
			$db->query("BEGIN");
			$db->query("DELETE FROM stu_buildings_goods WHERE buildings_id=".$_GET[id]);
			$db->query("DELETE FROM stu_buildings_cost WHERE buildings_id=".$_GET[id]);
			foreach($_GET[good] as $key => $value)
			{
				if ($_GET[value][$key] == 0 || !$_GET[value][$key] || !is_numeric($_GET[value][$key])) continue;
				$db->query("INSERT INTO stu_buildings_cost () VALUES ('".$_GET[id]."','".$value."','".$_GET[value][$key]."')");
			}
			foreach($_GET[ggood] as $key => $value)
			{
				if ($_GET[gvalue][$key] == 0 || !$_GET[gvalue][$key] || !is_numeric($_GET[gvalue][$key])) continue;
				$db->query("INSERT INTO stu_buildings_goods () VALUES ('".$_GET[id]."','".$value."','".$_GET[gvalue][$key]."')");
			}
			$db->query("UPDATE stu_buildings SET eps_proc=".$_GET[et]." WHERE buildings_id=".$_GET[id]);
			if (is_array($_GET[bf]))
			{
				$db->query("DELETE FROM stu_field_build WHERE buildings_id=".$_GET[id]);
				foreach($_GET[bf] as $key => $value) $db->query("INSERT INTO stu_field_build (buildings_id,type) VALUES ('".$_GET[id]."','".$value."')");
			}
			$db->query("COMMIT");
			echo "Datensatz aktualisiert<br><br>";
		}
		$bd = $db->query("SELECT name,eps_proc FROM stu_buildings WHERE buildings_id=".$_GET[id],4);
		echo "<b>".$bd[name]."</b><br><a href=buildingcost.php><<- Zurück</a><br><table><tr><td colspan=3></td></tr><tr><td width=110 valign=top><form action=buildingcost.php method=get><input type=hidden name=id value=".$_GET[id]."><input type=hidden name=a value=ck><input type=submit value=ändern><b>Baukosten: ID ".$_GET[id]."</b><br><br>";
		$result = $db->query("SELECT a.goods_id,b.count FROM stu_goods as a LEFT JOIN stu_buildings_cost as b ON a.goods_id=b.goods_id AND b.buildings_id=".$_GET[id]." WHERE a.goods_id<200");
		while ($data=mysql_fetch_assoc($result)) echo "<input type=hidden name=good[] value=".$data[goods_id]."><img src=../gfx/goods/".$data[goods_id].".gif> <input type=text size=4 name=value[] value=".$data['count']."><br>";
		echo "</td><td valign=top width=110><b>Waren: ID ".$_GET[id]."</b><br><br>
		<img src=../gfx/buttons/e_trans2.gif> <input type=text size=4 value=".$bd[eps_proc]." name=et><br>";
		$result = $db->query("SELECT a.goods_id,b.count FROM stu_goods as a LEFT JOIN stu_buildings_goods as b ON a.goods_id=b.goods_id AND b.buildings_id=".$_GET[id]." WHERE a.goods_id<200");
		while ($data=mysql_fetch_assoc($result)) echo "<input type=hidden name=ggood[] value=".$data[goods_id]."><img src=../gfx/goods/".$data[goods_id].".gif> <input type=text size=4 name=gvalue[] value=".$data['count']."><br>";
		echo "</td>
		<td valign=top><b>Baubar auf</b><br><table>";
		$result = $db->query("SELECT type FROM stu_field_build WHERE buildings_id=".$_GET[id]);
		while($data=mysql_fetch_assoc($result)) $fd[$data[type]] = $data[type];
		
		
		function fieldLine($arr) {
			global $fd;
			
			echo "<tr>";
			foreach($arr as $f) {
				echo "<td><img src=../gfx/fields/".$f.".gif></td>";
			}
			echo "</tr><tr>";
			foreach($arr as $f) {
				echo "<td><input type=checkbox name=bf[] value=".$f."".($fd[$f] ? " checked" : "")."></td>";
			}			
			echo "</tr>";
		}
		
		fieldLine(array(1,45));
		
		fieldLine(array(5,40,41,42,44,6,20,16));
			
		fieldLine(array(7,19,47,8,9,18));
		
		fieldLine(array(31,32,33,34,35,36,201));
		
		fieldLine(array(10,12));
		
		fieldLine(array(200,205,203,204));
		
		fieldLine(array(223,225,226,229,230,232,241,242,243,244));
		
		fieldLine(array("100"));
		
		fieldLine(array(81,82,83,84));
		
		fieldLine(array(301,302,303,304,305,306,307));
		
		echo "</table><br>
		<b>Vorhandene Gebäudebilder</b><br>";
		$dir = dir("../gfx/buildings/".$_GET[id]);
		while($datei = $dir->read())
		{
			if (!is_dir("../gfx/buildings/".$_GET[id]."/".$datei))
			{
				if (is_numeric(str_replace(".gif","",$datei))) echo "<img src=../gfx/buildings/".$_GET[id]."/$datei>&nbsp;";
			}
		}
		$dir->close();
		echo "<br><br><input type=submit value=ändern></td></tr><tr><td colspan=3><input type=submit value=ändern></td></tr></table></form>";
	}
	else
	{
		$bd = $db->query("SELECT name,eps_proc FROM stu_buildings WHERE buildings_id=".$_GET[id],4);
		if ($_GET[a] == "ck" && is_array($_GET[da]))
		{
			$da = $_GET[da];
			$db->query("UPDATE stu_buildings SET name='".$da[name]."',lager=".$da[lager].",eps_cost=".$da[eps_cost].",
			eps=".$da[eps].",eps_proc=".$da[eps_proc].",bev_pro=".$da[bev_pro].",bev_use=".$da[bev_use].",
			level=".$da[level].",integrity=".$da[integrity].",research_id=".$da[research_id].",points='".$da[points]."',
			view='".($da[view] == 1 ? 1 : "")."',schilde=".$da[schilde].",buildtime=".$da[buildtime].",blimit=".$da[blimit].",
			bclimit=".$da[bclimit].",is_activateable='".($da[is_activateable] ? 1 : "")."',upgrade_from=".$da[upgrade_from].",
			research_t=".$da[research_t].",research_k=".$da[research_k].",research_v=".$da[research_v]." WHERE buildings_id=".$_GET[id]);
			echo "Datensatz aktualisiert<br><br>";
		}
		$data = $db->query("SELECT * FROM stu_buildings WHERE buildings_id=".$_GET[id],4);
		echo "<form action=buildingcost.php method=get><input type=hidden name=m value=w><input type=hidden name=id value=".$_GET[id]."><input type=hidden name=a value=ck>
		<b>".$bd[name]."</b><br><a href=buildingcost.php><<- Zurück</a><br><br><table><tr><td colspan=2><input type=submit value=ändern></td></tr>
		<tr>
			<td>Name</td>
			<td><input type=text size=15 name=da[name] value=\"".$data[name]."\"></td>
		</tr>
		<tr>
			<td>Lagerplatz</td>
			<td><input type=text size=4 name=da[lager] value=\"".$data[lager]."\"></td>
		</tr>
		<tr>
			<td>Energiekosten</td>
			<td><input type=text size=4 name=da[eps_cost] value=\"".$data[eps_cost]."\"></td>
		</tr>
		<tr>
			<td>EPS-Speicher</td>
			<td><input type=text size=4 name=da[eps] value=\"".$data[eps]."\"></td>
		</tr>
		<tr>
			<td>Energieproduktion/Verbrauch</td>
			<td><input type=text size=4 name=da[eps_proc] value=\"".$data[eps_proc]."\"></td>
		</tr>
		<tr>
			<td>Wohnraum</td>
			<td><input type=text size=3 name=da[bev_pro] value=".$data[bev_pro]."></td>
		</tr>
		<tr>
			<td>Arbeiter</td>
			<td><input type=text size=3 name=da[bev_use] value=".$data[bev_use]."></td>
		</tr>
		<tr>
			<td>Level</td>
			<td><input type=text size=2 name=da[level] value=".$data[level]."></td>
		</tr>
		<tr>
			<td>Integrität</td>
			<td><input type=text size=3 name=da[integrity] value=".$data[integrity]."></td>
		</tr>
		<tr>
			<td>Forschungs-ID</td>
			<td><input type=text size=4 name=da[research_id] value=".$data[research_id]."></td>
		</tr>
		<tr>
			<td>Wirtschaftspunkte</td>
			<td><input type=text size=4 name=da[points] value=".$data[points]."></td>
		</tr>
		<tr>
			<td>Anzeigen?</td>
			<td><input type=checkbox name=da[view] value=1 ".($data[view] == 1 ? "CHECKED" : "")."></td>
		</tr>
		<tr>
			<td>Schilde</td>
			<td><input type=text size=4 name=da[schilde] value=".$data[schilde]."></td>
		</tr>
		<tr>
			<td>Bauzeit</td>
			<td><input type=tesz size=7 name=da[buildtime] value=".$data[buildtime]."></td>
		</tr>
		<tr>
			<td>Globales Baulimit</td>
			<td><input type=text size=2 name=da[blimit] value=".$data[blimit]."></td>
		</tr>
		<tr>
			<td>Baulimit pro Kolonie</td>
			<td><input type=text size=2 name=da[bclimit] value=".$data[bclimit]."></td>
		</tr>
		<tr>
			<td>Aktivierbar?</td>
			<td><input type=checkbox name=da[is_activateable] value=1 ".($data[is_activateable] == 1 ? "CHECKED" : "")."></td>
		</tr>
		<tr>
			<td>Upgrade von buildings_id</td>
			<td><input type=text size=3 name=da[upgrade_from] value=".$data[upgrade_from]."></td>
		</tr>
		<tr>
			<td>Forschungspunkte (T/K/V)</td>
			<td><input type=text size=2 name=da[research_t] value=".$data[research_t].">
			<input type=text size=2 name=da[research_k] value=".$data[research_k].">
			<input type=text size=2 name=da[research_v] value=".$data[research_v]."></td>
		</tr>
		</table>";
	}
}
?>
</body></html>