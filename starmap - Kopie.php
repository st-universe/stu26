<?php
if (!is_object($db)) exit;
include_once("class/map.class.php");
$map = new map;

switch($_GET['s'])
{
	default:
		$v = "main";
	case "ma":
		$v = "main";
		break;
	case "ss":
		$v = "showsystem";
		if (!check_int($_GET['id'])) die(show_error(902));
		if ($db->query("SELECT systems_id FROM stu_systems_user WHERE systems_id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",1) == 0) die(show_error(902));
		break;
}

if ($v == "main")
{
	pageheader("/ <b>Sternenkarte</b>");
	echo "<script language=\"Javascript\">
	var lay_stick = 0;
	function proceed_change()
	{
		obj = document.tform.wort.value;
		ftest = obj.length;
		if (obj.length < 2) return;
		elt = \"muh\";
		sendRequest('backend/ssmap.php?sstring='+obj);
		elem = document.getElementById(elt);
		elem.style.left = \"454px\";
		elem.style.top = \"80px\";
		elem.style.width = \"270px\";
		elem.style.position = \"absolute\";
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
	#bla a {
		font-weight: bold;
		width: 300px;
		display: block;
		padding-top: 3px;
		padding-bottom: 3px;
	}
	</style>";
	// Anzahl bekannter Systeme holen
	$cnt = $map->get_known_systems_count();
	
	if (!$_GET['pa'] || !check_int($_GET['pa'])) $_GET['pa'] = 1;
	// Seiten erzeugen
	$in = $_GET['pa'];
	$i = $in-2;
	$j = $in+2;
	$ceiled_knc = ceil($cnt/25);
	$ps0 = "<td>Seite: <a href=?p=map&sor=".$_GET["sor"]."&way=".$_GET["way"]."&pa=1>|<</a> <a href=?p=map&sor=".$_GET["sor"]."&way=".$_GET["way"]."&pa=".($pa == 1 ? 1 : $pa-1)."><</a></td>";
	if ($i > 1) $ps = "<td class=\"pages\"><a href=?p=map&sor=".$_GET["sor"]."&way=".$_GET["way"]."&pa=1>1</a></td>";
	if ($j < $ceiled_knc) $pe = "<td class=\"pages\"><a href=?p=map&sor=".$_GET["sor"]."&way=".$_GET["way"]."&pa=".$ceiled_knc.">".$ceiled_knc."</a></td>";
	if ($j > $ceiled_knc) $j = $ceiled_knc;
	if ($i < 1) $i = 1;
	while($i<=$j)
	{
		$pages .= "<td class=\"pages\"><a href=?p=map&sor=".$_GET["sor"]."&way=".$_GET["way"]."&pa=".$i.">".($i == $in ? "<div style=\"font-weight : bold; color: Yellow;\">".$i."</div>" : $i)."</a></td>";
		$i++;
	}
	$i = $in-2;
	$j = $in+2;
	$pages = $ps.($i > 2 ? "<td style=\"width: 20px; text-align: center;\">...</td>" : "").$pages.($ceiled_knc > $j+1 ? "<td style=\"width: 20px; text-align: center;\">... </td>" : "").$pe;
	$pe0 = "<td><a href=?p=map&sor=".$_GET["sor"]."&way=".$_GET["way"]."&pa=".($pa == $ceiled_knc ? 1 : $pa+1).">></a>&nbsp;<a href=?p=map&sor=".$_GET["sor"]."&way=".$_GET["way"]."&pa=".$ceiled_knc.">>|</a> (".$cnt." Systeme)</td>";

	$m = ($_GET['pa']-1)*25;
	$result = $map->get_known_systems($m,$_GET['sor'],$_GET['way']);
	echo "<table>
	<tr>
	<td>
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<th colspan=3>Bekannte Systeme</th>
	<tr>
	<td colspan=3><table><tr>".$ps0.$pages.$pe0."</tr></table></td>
	</tr>
	<tr>
		<td><a href=?p=map&sor=ty&way=up><img src=".$gfx."/buttons/pup.gif title=\"Aufwärts nach Typ sortieren\" border=0></a>
		<a href=?p=map&sor=ty&way=dn><img src=".$gfx."/buttons/pdown.gif title=\"Abwärts nach Typ sortieren\" border=0></a></td>
		<td><a href=?p=map&sor=x&way=up><img src=".$gfx."/buttons/pup.gif title=\"Aufwärts nach X-koordinate sortieren\" border=0></a>
		<a href=?p=map&sor=x&way=dn><img src=".$gfx."/buttons/pdown.gif title=\"Abwärts nach X-Koordinate sortieren\" border=0></a>
		<b>x|y</b>
		<a href=?p=map&sor=y&way=up><img src=".$gfx."/buttons/pup.gif title=\"Aufwärts nach Y-koordinate sortieren\" border=0></a>
		<a href=?p=map&sor=y&way=dn><img src=".$gfx."/buttons/pdown.gif title=\"Abwärts nach Y-Koordinate sortieren\" border=0></a>
		<td><a href=?p=map&sor=na&way=up><img src=".$gfx."/buttons/pup.gif title=\"Aufwärts nach Name sortieren\" border=0></a>
		<b>Name</b>
		<a href=?p=map&sor=na&way=dn><img src=".$gfx."/buttons/pdown.gif title=\"Abwärts nach Name sortieren\" border=0></a></td>
		</td>
	</tr>";
	if (mysql_num_rows($result) == 0) echo "<tr><td colspan=3>Keine Systeme bekannt</td></tr>";
	else
	{
		while($data=mysql_fetch_assoc($result)) echo "<tr><td style=\"width: 30px;\"><img src=".$gfx."/map/".$data['type'].".gif></td><td>".$data['cx']."|".$data['cy']."</td><td><a href=?p=map&s=ss&id=".$data[systems_id].">".$data[name]."-System</a></td></tr>";
	}
	echo "<tr>
	<td colspan=3><table><tr>".$ps0.$pages.$pe0."</tr></table></td>
	</tr></table>
	</td>
	<td valign=\"top\">
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=400>
	<th>System suchen</th>
	<form name=\"tform\">
	<tr><td>Name: <input type=\"text\" size=\"15\" class=\"text\" name=\"wort\" onKeyUp=\"proceed_change();\"></td></tr>
	</form>
	</table>
	</td>
	</tr>
	</table>
	<div id=\"muh\"></div>";
}
if ($v == "showsystem")
{
	$system = $map->getsystembyid($_GET["id"]);
	$result = $map->getknownsystembyid($_GET["id"]);
	pageheader("/ <a href=?p=map>Sternenkarte</a> / <b>".$system[name]."-System</b>");
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1><tr><td width=30 height=30></td>";
	for($i=1;$i<=$system[sr];$i++) echo "<th width=30>".$i."</th>";
	while($data=mysql_fetch_assoc($result))
	{
		if ($ly != $data[sy])
		{
			echo "</tr><tr><th>".$data[sy]."</th>";
			$ly = $data[sy];
		}
		if ($data[type] == 99) echo "<td><img src=".$gfx."/map/12.gif".($data[id] > 0 ? " onmouseover=\"return overlib('".$system[name]." ".str_replace("'","",$data[planet_name])." (".$data[id].")', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER)\" onmouseout=\"nd();\"" : "")."></td>";
		else echo "<td><img src=".$gfx."/map/".$data[type].".gif".($data[id] > 0 ? " onmouseover=\"return overlib('".$system[name]." ".str_replace("'","",$data[planet_name])." (".$data[id].")', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER)\" onmouseout=\"nd();\"" : "")."></td>";
	}
	echo "</tr></table>";
}
?>