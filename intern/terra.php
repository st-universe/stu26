<?php
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
if (!$_GET[p] || $_GET[p] == "ma")
{
	$result = $db->query("SELECT * FROM stu_terraforming ORDER BY terraforming_id");
	echo "<html>
	<head>
	<title>Terraforming</title>
	</head>
	</html>
	<table width=800>
	<th width=50%><< <a href=index.html>Interner Bereich</a>  -  Terraforming wählen</th>
	<tr>
	<td valign=top>";
	while($data=mysql_fetch_assoc($result))
	{
		echo "<a href=terra.php?p=sc&id=".$data[terraforming_id]."><b>".$data[name]."</b><br><img src=../gfx/fields/".$data[v_feld].".gif border=0> => <img src=../gfx/fields/".$data[z_feld].".gif border=0></a><br>";
	}
	echo "</td>
	</tr>
	</table>";
}
if ($_GET[p] == "sc")
{
	if (is_array($_GET[da]))
	{
		$db->query("DELETE FROM stu_terraforming_cost WHERE terraforming_id=".$_GET["id"]);
		$db->query("UPDATE stu_terraforming SET ecost=".$_GET[ec]." WHERE terraforming_id=".$_GET["id"]);
		foreach($_GET[da] as $key => $value)
		{
			if (!$_GET[go][$key] || $_GET[go][$key] == 0) continue;
			$db->query("INSERT INTO stu_terraforming_cost (terraforming_id,goods_id,count) VALUES ('".$_GET["id"]."','".$value."','".$_GET[go][$key]."')");
		}
		echo "<b>Terraforming geändert</b><br>";
	}
	$data = $db->query("SELECT * FROM stu_terraforming WHERE terraforming_id=".$_GET["id"],4);
	echo "<table>
	<th><< <a href=terra.php>Terraforming</a>  -  ".$data[name]."</th>
	<form action=terra.php method=get><input type=hidden name=p value=sc><input type=hidden name=id value=".$_GET[id].">
	<tr>
	<td><input type=submit value=ändern><br>
	<img src=../gfx/buttons/e_trans2.gif title=\"Energie\"> <input type=text size=3 name=ec value=\"".$data[ecost]."\"><br>";
	$result = $db->query("SELECT a.goods_id,a.name,b.count FROM stu_goods as a LEFT JOIN stu_terraforming_cost as b ON a.goods_id=b.goods_id AND b.terraforming_id=".$data[terraforming_id]." WHERE a.goods_id<=100 ORDER BY a.sort");
	while($dat=mysql_fetch_assoc($result))
	{
		echo "<input type=hidden name=da[] value=".$dat[goods_id]."><img src=../gfx/goods/".$dat[goods_id].".gif title=\"".$dat[name]."\"> <input type=text size=3 name=go[] value=".$dat['count']."><br>";
	}
	echo "<input type=submit value=ändern></td>
	</tr></form>
	</table>";
}
?>