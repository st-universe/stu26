<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

if (!check_int($_GET['bid'])) exit;
if (!check_int($_GET['id'])) $_GET['id'] = 0;
if (!check_int($_GET['fid'])) $_GET['fid'] = 0;

$col = $db->query("SELECT * FROM stu_stations WHERE id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($col == 0 && $_GET['fid'] != 0) exit;


$data = $db->query("SELECT * FROM stu_station_components WHERE component_id=".$_GET['bid'],4);
if ($data == 0) exit;
$field = $db->query("SELECT * FROM stu_stations_fielddata WHERE stations_id=".$_GET['id']." AND field_id=".$_GET['fid'],4);
if ($field == 0)
{
	// Override für reine Anzeige

	// TODO wenn nötig

}
$cost = $db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_station_component_cost as a LEFT JOIN stu_goods as b ON b.goods_id=a.goods_id LEFT JOIN stu_stations_storage as c ON c.stations_id=".$_GET[id]." AND c.goods_id=a.goods_id WHERE a.component_id=".$data[component_id]." ORDER BY b.sort");
$goods = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_station_component_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.component_id=".$data[component_id]." ORDER BY b.sort");
$result = $db->query("SELECT SUM(a.count) as gc,a.goods_id,c.name FROM stu_station_component_goods as a LEFT JOIN stu_stations_fielddata as b USING(component_id) LEFT JOIN stu_goods as c USING(goods_id) WHERE b.stations_id=".$_GET[id]." AND b.aktiv=1 GROUP BY a.goods_id");
while($dat=mysql_fetch_assoc($result)) $wd[$dat['goods_id']] = $dat['gc'];
$result = $db->query("SELECT goods_id,name FROM stu_goods ORDER BY sort LIMIT 50");
$fe = $db->query("SELECT SUM(b.eps_proc) FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING(component_id) WHERE a.aktiv=1 AND a.stations_id=".$_GET[id],1);

$wd[1] -= ceil(($col[bev_work]+$col[bev_free])/5);

echo "<html><body>
<form action=main.php method=get>
<input type=hidden name=p value=station>
<input type=hidden name=s value=show>".($_GET[u] == 1 ? "<input type=hidden name=a value=upg><input type=hidden name=ubu value=".$_GET[bid].">" : "<input type=hidden name=a value=bu><input type=hidden name=bu value=".$_GET[bid].">")."
<input type=hidden name=id value=".$_GET[id].">
<input type=hidden name=fid value=".$_GET[fid].">
<table class=tcal style=\"border: 1px solid #8897cf;\"><th colspan=2>Modulinformationen ".stripslashes($data[name])."</th>
<tr>
<td width=200 valign=top><b>Informationen</b><br><br>
<div align=\"center\"><img src=".$gfx."/components/".$data[component_id]."_".$field[type].".gif>";

echo "<br><br>";
if ($data[field1] == 1) echo "<img src=".$gfx."/fieldss/1.gif width=20 height=20>";
if ($data[field1] == 2) echo "<img src=".$gfx."/fieldss/2.gif width=20 height=20>";
if ($data[field1] == 3) echo "<img src=".$gfx."/fieldss/3.gif width=20 height=20>";
if ($data[field1] == 4) echo "<img src=".$gfx."/fieldss/4.gif width=20 height=20>";
if ($data[field1] == 5) echo "<img src=".$gfx."/fieldss/5.gif width=20 height=20>";
if ($data[field1] == 6) echo "<img src=".$gfx."/fieldss/6.gif width=20 height=20>";
if ($data[field1] == 9) echo "<img src=".$gfx."/fieldss/9.gif width=20 height=20>";

echo "</div><br>
<img src=".$gfx."/buttons/time.gif title=\"Bauzeit\"> ".gen_time($data[buildtime]);
if ($data['lager'] > 0) echo "<br><img src=".$gfx."/buttons/lager.gif title=\"Lager\"> ".$data['lager'];
if ($data['eps'] > 0) echo "<br><img src=".$gfx."/buttons/e_trans1.gif title=\"EPS\"> ".$data['eps'];
if ($data['bev_use'] > 0) echo "<br><img src=".$gfx."/bev/bev_used_1_".$_SESSION["race"].".gif title='Benötige Arbeiter'> ".$data[bev_use];
echo "<br><br><b>Baukosten</b><br><img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".$data[eps_cost].($_GET['id'] > 0 ? " / ".($col[eps] < $data[eps_cost] ? "<font color=#FF0000>".$col[eps]."</font>" : $col[eps]) : "")."<br>";
while($c=mysql_fetch_assoc($cost)) echo "<img src=".$gfx."/goods/".$c[goods_id].".gif title='".$c[name]."'> ".$c['count'].($_GET['id'] > 0 ? " / ".(!$c[vcount] ? "<font color=#FF0000>0</font>" : ($c[vcount] < $c['count'] ? "<font color=#FF0000>".$c[vcount]."</font>" : $c[vcount])) : "")."<br>";
if ($data['is_activateable'] == 1)
{
	echo "<br><b>Produktion/Verbrauch</b><br>";
	if ($data[eps_proc] != 0) echo "<img src=".$gfx."/buttons/e_trans2.gif title='Energie'> ".($data[eps_proc] > 0 ? "+".$data[eps_proc] : $data[eps_proc])."<br>";
	while($g=mysql_fetch_assoc($goods))
	{
		echo "<img src=".$gfx."/goods/".$g[goods_id].".gif title='".$g[name]."'> ".($g['count'] > 0 ? "+".$g['count'] : $g['count'])."<br>";
		$wd2[$g[goods_id]] = $g['count'];
	}
}

if ($field[component_id] > 0 && $field['aktiv'] < 2 && $field['component_id'] != $_GET['bid'])
{
	$bd2 = $db->query("SELECT * FROM stu_station_components WHERE component_id=".$field[component_id],4);
	$data['lager'] -= $bd2['lager'];
	$data['eps'] -= $bd2['eps'];
	if ($field['aktiv'] == 1)
	{
		$data['eps_proc'] -= $bd2['eps_proc'];
		$data['bev_pro'] -= $bd2['bev_pro'];
		$res = $db->query("SELECT goods_id,SUM(count) as count FROM stu_station_component_goods WHERE component_id=".$field['component_id']." GROUP BY goods_id");
		while ($dat=mysql_fetch_assoc($res)) $vg[$dat['goods_id']] = $dat['count'];
	}
}

if ($fe < 0) $fes = "<font color=#FF0000>".$fe."</font>";
if ($fe > 0) $fes = "<font color=Green>+".$fe."</font>";
if ($data[eps_proc] < 0) $fes2 = "<font color=#FF0000>".($data[eps_proc])."</font>";
elseif ($data[eps_proc] > 0) $fes2 = "<font color=Green>+".($data[eps_proc])."</font>";
else $fes2 = $data[eps_proc];
$fe += $data[eps_proc];
if ($fe < 0) $fes3 = "<font color=#FF0000>".$fe."</font>";
if ($fe > 0) $fes3 = "<font color=Green>+".$fe."</font>";
echo "</td>";

echo "</tr>
<tr><td colspan=2 align=center>".($_GET['fid'] > 0 ? ($_GET[u] == 1 ? "<input type=submit class=button value=Upgrade>" : ($field[component_id] != $_GET[bid] ? "<input type=submit class=button value=Bauen>" : "")) : "")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr>
</table>
<input type=\"hidden\" name=\"ps\" value=\"".$_SESSION['pagesess']."\">
</form></body></html>";

?>
