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
if (!check_int($_GET['ccid'])) $_GET['ccid'] = 0;

$col = $db->query("SELECT id,colonies_classes_id,eps,max_eps,max_storage,bev_free,bev_work,bev_max,gravitation FROM stu_colonies WHERE id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($col == 0 && $_GET['fid'] != 0) exit;

// NPC-Weiche
if ($_SESSION[uid] < 101) $data = $db->query("SELECT buildings_id,name,lager,eps_cost,eps,eps_proc,bev_pro,bev_use,buildtime,level,integrity,points,schilde,is_activateable,bclimit,blimit,upgrade_from FROM stu_buildings WHERE buildings_id=".$_GET['bid'],4);
else $data = $db->query("SELECT buildings_id,name,lager,eps_cost,eps,eps_proc,bev_pro,bev_use,buildtime,level,integrity,points,schilde,is_activateable,bclimit,blimit,upgrade_from FROM stu_buildings WHERE (view=1 OR buildings_id=1 OR buildings_id=222) AND buildings_id=".$_GET['bid'],4);
if ($data == 0) exit;
$field = $db->query("SELECT buildings_id,type,aktiv FROM stu_colonies_fielddata WHERE colonies_id=".$_GET['id']." AND field_id=".$_GET['fid'],4);
if ($field == 0)
{
	// Override für reine Anzeige
	if (iscolcent($_GET['bid'])) $field = array("buildings_id" => $_GET['bid'],"type" => 1);
	else $field = array("buildings_id" => $_GET['bid'],"type" => $db->query("SELECT type FROM stu_field_build WHERE buildings_id=".($_GET['bid'])." AND type<200 ORDER BY TYPE ASC LIMIT 1",1));
}
$cost = $db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_buildings_cost as a LEFT JOIN stu_goods as b ON b.goods_id=a.goods_id LEFT JOIN stu_colonies_storage as c ON c.colonies_id=".$_GET[id]." AND c.goods_id=a.goods_id WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
$goods = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
$result = $db->query("SELECT SUM(a.count) as gc,a.goods_id,c.name FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) LEFT JOIN stu_goods as c USING(goods_id) WHERE b.colonies_id=".$_GET[id]." AND b.aktiv=1 GROUP BY a.goods_id");
while($dat=mysql_fetch_assoc($result)) $wd[$dat['goods_id']] = $dat['gc'];
$result = $db->query("SELECT goods_id,name FROM stu_goods ORDER BY sort LIMIT 50");

$thisbonus = $db->query("SELECT * FROM stu_colonies_bonus WHERE buildings_id = ".$_GET['bid']." AND colonies_classes_id = ".$col['colonies_classes_id']." LIMIT 1;",4);
$thatbonus = $db->query("SELECT * FROM stu_colonies_bonus WHERE buildings_id = ".$field['buildings_id']." AND colonies_classes_id = ".$col['colonies_classes_id']." LIMIT 1;",4);

if ($thisbonus['count'] > 0 && $thisbonus['goods_id'] == 0) $data['eps_proc'] += $thisbonus['count'];

$bonusres = $db->query("SELECT b.goods_id, SUM(b.count) as gc FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies_bonus as b ON a.buildings_id = b.buildings_id LEFT JOIN stu_colonies as c ON c.id = a.colonies_id WHERE a.colonies_id=".$_GET['id']." AND a.aktiv=1 AND b.colonies_classes_id = c.colonies_classes_id GROUP by b.goods_id;");
while($d=mysql_fetch_assoc($bonusres)) $wd[$d['goods_id']] += $d['gc'];
		
$fe = $db->query("SELECT SUM(b.eps_proc) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$_GET[id],1);
$fe += $wd[0];


$wkz = $db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=54 AND aktiv=1 AND colonies_id=".$_GET[id],1);
if ($wkz != 0)
{
	$fc = $db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE (buildings_id=2 OR buildings_id=9 OR buildings_id=104) AND aktiv=1 AND colonies_id=".$_GET[id],1);
	if ($wkz != 0) $wd[1] += 2*$fc;
}
// $wd[1] += $db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE buildings_id=103 AND aktiv=1 AND colonies_id=".$_GET['id'],1);

$wd[1] -= ceil(($col[bev_work]+$col[bev_free])/10);

echo "<html><body>
<form action=main.php method=get>
<input type=hidden name=p value=colony>
<input type=hidden name=s value=sc>".($_GET[u] == 1 ? "<input type=hidden name=a value=upg><input type=hidden name=ubu value=".$_GET[bid].">" : "<input type=hidden name=a value=bu><input type=hidden name=bu value=".$_GET[bid].">")."
<input type=hidden name=id value=".$_GET[id].">
<input type=hidden name=fid value=".$_GET[fid].">
<table class=tcal style=\"border: 1px solid #8897cf;\"><th colspan=2>Gebäudeinformationen ".stripslashes($data[name])."</th>
<tr>
<td colspan=2 width=200 valign=top><br>
<div align=\"center\"><img src=".$gfx."/buildings/".$data[buildings_id]."/".buildingpic($data['buildings_id'],$field['type']).".png  style=\"background-image:url(".$gfx."/fields/".$field['type'].".gif); background-repeat: no-repeat; background-position:center; width:30px;\" >";
$res = $db->query("SELECT type FROM stu_field_build WHERE buildings_id=".$_GET[bid]." ORDER BY type");
if (mysql_num_rows($res) > 0)
{
	echo "<br><br>";
	$bla = 0;
	while($da=mysql_fetch_assoc($res)) {
		echo "<img src=".$gfx."/fields/".$da[type].".gif width=15 height=15 title='".getnamebyfield($da[type])."'> ";
		if ($bla % 16 == 15) echo "<br>";
		$bla++;
	}
}
// echo "</div>Ab Level: ".$data[level]."<br><img src=".$gfx."/buttons/integ.gif title=\"Integrität\"> ".$data[integrity]."<br>
echo "</div><br></td></tr><tr><td width=50%><b>Werte</b><br><img src=".$gfx."/icons/armor.gif title=\"Integrität\"> ".$data[integrity]."<br>
<img src=".$gfx."/icons/clock.gif title=\"Bauzeit\"> ".gen_time($data[buildtime]);
if ($data['lager'] > 0) echo "<br><img src=".$gfx."/icons/storage.gif title=\"Lager\"> ".$data['lager'];
if ($data['eps'] > 0) echo "<br><img src=".$gfx."/icons/eps.gif title=\"EPS\"> ".$data['eps'];
if ($data['bev_use'] > 0) echo "<br><img src=".$gfx."/icons/crew".$_SESSION["race"].".gif title='Benötige Arbeiter'> ".$data[bev_use];
if ($data['bev_pro'] > 0) echo "<br><img src=".$gfx."/icons/crewspace".$_SESSION["race"].".gif title='Wohnraum'> ".$data[bev_pro];




echo "<br><br><b>Baukosten</b><br><img src=".$gfx."/icons/energy.gif title='Energie'> ".$data[eps_cost].($_GET['id'] > 0 ? " / ".($col[eps] < $data[eps_cost] ? "<font color=#FF0000>".$col[eps]."</font>" : $col[eps]) : "")."<br>";
while($c=mysql_fetch_assoc($cost)) echo "<img src=".$gfx."/goods/".$c[goods_id].".gif title='".$c[name]."'> ".$c['count'].($_GET['id'] > 0 ? " / ".(!$c[vcount] ? "<font color=#FF0000>0</font>" : ($c[vcount] < $c['count'] ? "<font color=#FF0000>".$c[vcount]."</font>" : $c[vcount])) : "")."<br>";





if ($field[buildings_id] > 0 && $field['aktiv'] < 2 && $field['buildings_id'] != $_GET['bid'])
{
	$bd2 = $db->query("SELECT buildings_id,name,lager,eps_cost,eps,eps_proc,bev_pro,bev_use,buildtime,level,integrity,points,schilde,bclimit,blimit,upgrade_from FROM stu_buildings WHERE (view=1 OR buildings_id=1) AND buildings_id=".$field[buildings_id],4);
	
	$data['lager'] -= $bd2['lager'];
	$data['eps'] -= $bd2['eps'];
	if ($field['aktiv'] == 1)
	{
		if ($thatbonus['count'] > 0 && $thatbonus['goods_id'] == 0) $bd2['eps_proc'] += $thatbonus['count'];
		// $data['eps_proc'] -= $bd2['eps_proc'];
		// $data['bev_pro'] -= $bd2['bev_pro'];
		$res = $db->query("SELECT goods_id,SUM(count) as count FROM stu_buildings_goods WHERE buildings_id=".$field['buildings_id']." GROUP BY goods_id");
		while ($dat=mysql_fetch_assoc($res)) {
			$vg[$dat['goods_id']] = $dat['count'];
			if ($dat['goods_id'] == $thatbonus['goods_id']) $vg[$dat['goods_id']] += $thatbonus['count'];
		}
		if ($field['buildings_id'] == 54) $vg[1] += 2*$fc;
		if ($wkz && ($field['buildings_id'] == 2 || $field['buildings_id'] == 9)) $vg[1] += 2;

	}
}

if ($fe < 0) $fes = "<font color=#FF0000>".$fe."</font>";
if ($fe > 0) $fes = "<font color=Green>+".$fe."</font>";
if ($data[eps_proc] < 0) $fes2 = "<font color=#FF0000>".($data[eps_proc])."</font>";
elseif ($data[eps_proc] > 0) $fes2 = "<font color=Green>+".($data[eps_proc])."</font>";
else $fes2 = $data[eps_proc];
$fe += $data[eps_proc] - $bd2['eps_proc'];
if ($fe < 0) $fes3 = "<font color=#FF0000>".$fe."</font>";
if ($fe > 0) $fes3 = "<font color=Green>+".$fe."</font>";
if ($data['blimit'] > 0 || $data['bclimit'] > 0)
{
	echo "<br><b>Baulimits</b><br>";
	if ($data['bclimit'] > 0)
	{
		$lc = $db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$_GET['id']." AND buildings_id=".$_GET['bid'],1);
		echo "Pro Kolonie: ".($lc >= $data['bclimit'] ? "<font color=#FF0000>".$lc."</font>" : $lc)." / ".$data['bclimit'];
		if ($data['blimit'] > 0) echo "<br>";
	}
	if ($data['blimit'] > 0)
	{
		$lc = $db->query("SELECT COUNT(*) FROM stu_colonies as a LEFT JOIN stu_colonies_fielddata as b ON b.colonies_id=a.id WHERE a.user_id=".$_SESSION['uid']." AND b.buildings_id=".$_GET['bid'],1);
		echo "Pro Siedler: ".($lc >= $data['blimit'] ? "<font color=#FF0000>".$lc."</font>" : $lc)." / ".$data['blimit'];
	}
	
}
echo "</td>";

if ($_GET['bid'] != $field['buildings_id'])
{
echo "<td valign=top>";


if ($data['is_activateable'] == 1)
{
	echo "<b>Produktion/Verbrauch</b><br>";
	if ($data[eps_proc] != 0) echo "<img src=".$gfx."/icons/energy.gif title='Energie'> ".($data[eps_proc] > 0 ? "+".$data[eps_proc] : $data[eps_proc])."<br>";
	while($g=mysql_fetch_assoc($goods))
	{
		if ($wkz && ($g[goods_id] == 1) && ($data[buildings_id] == 2 || $data[buildings_id] == 9)) $g['count'] += 2;
		echo "<img src=".$gfx."/goods/".$g[goods_id].".gif title='".$g[name]."'> ".($g['count'] > 0 ? "+".$g['count'] : $g['count'])."<br>";
		$wd2[$g[goods_id]] = $g['count'];
	}
	echo "<br>";
}


echo "<b>Auswirkungen</b><br><table cellpadding=0>";

if ($fes != $fes3) {
	// echo "<tr><td><img src=".$gfx."/icons/energy.gif title=\"Energie\"></td>
// <td>".$fes."</td><td>(".$fes2.")</td><td>= ".(!$fes3 ? 0 : $fes3)."</td></tr>";

	echo "<tr><td width=35><img src=".$gfx."/icons/energy.gif title=\"Energie\"></td>
	<td width=25 style=\"text-align:center;\">".$fes."</td>
	<td width=25 style=\"text-align:center;\">-></td>
	<td width=25 style=\"text-align:center;\">".$fes3."</tr>";

}


while($dat=mysql_fetch_assoc($result))
{
	if ($wd2[$dat[goods_id]] == 0 && $vg[$dat[goods_id]] == 0) continue;
	
	$wd2[$dat['goods_id']] -= $vg[$dat['goods_id']];
	if (!$wd[$dat[goods_id]] && !$wd2[$dat[goods_id]]) continue;
	if (!$wd[$dat[goods_id]]) $wd[$dat[goods_id]] = 0;
	if (!$wd2[$dat[goods_id]]) $wd2[$dat[goods_id]] = 0;
	
	$com = $wd[$dat[goods_id]] + $wd2[$dat[goods_id]];
	if ($com < 0) $com = "<font color=#FF0000>".$com."</font>";
	if ($com > 0) $com = "<font color=Green>+".$com."</font>";
	
	if ($wd[$dat[goods_id]] > 0) $swd = "<font color=Green>+".$wd[$dat[goods_id]]."</font>";
	elseif ($wd[$dat[goods_id]] < 0) $swd = "<font color=#FF0000>".$wd[$dat[goods_id]]."</font>";
	else $swd = 0;

	// if ($wd2[$dat[goods_id]] > 0) $swd2 = "<font color=Green>+".$wd2[$dat[goods_id]]."</font>";
	// elseif ($wd2[$dat[goods_id]] < 0) $swd2 = "<font color=#FF0000>".$wd2[$dat[goods_id]]."</font>";
	// else $swd2 = 0;

	echo "<tr><td width=35><img src=".$gfx."/goods/".$dat[goods_id].".gif title=\"".ftit($data[name])."\"></td>
	<td width=25 style=\"text-align:center;\">".$swd."</td>
	<td width=25 style=\"text-align:center;\">-></td>
	<td width=25 style=\"text-align:center;\">".$com."</tr>";
}
//<tr><td><img src=".$gfx."/icon/crewspace".$_SESSION["race"].".gif title=\"Wohnraum\"></td>


if ($data[bev_pro] != 0 || $bd2[bev_pro] > 0) {
	echo "<tr><td width=35><img src=".$gfx."/icons/crewspace".$_SESSION["race"].".gif title=\"Wohnraum\"></td>
	<td width=25 style=\"text-align:center;\">".$col[bev_max]."</td>
	<td width=25 style=\"text-align:center;\">-></td>
	<td width=25 style=\"text-align:center;\">".($col[bev_max]+$data[bev_pro]-$bd2[bev_pro])."</tr>";
}
if ($data[eps] != 0 || $bd2[eps] > 0) {
	echo "<tr><td width=35><img src=".$gfx."/icons/eps.gif title=\"EPS\"></td>
	<td width=25 style=\"text-align:center;\">".$col[max_eps]."</td>
	<td width=25 style=\"text-align:center;\">-></td>
	<td width=25 style=\"text-align:center;\">".($col[max_eps]+$data[eps]-$bd2[eps])."</tr>";
}
if ($data[lager] != 0 || $bd2[lager] > 0) {
	echo "<tr><td width=35><img src=".$gfx."/icons/storage.gif title=\"Lager\"></td>
	<td width=25 style=\"text-align:center;\">".$col[max_storage]."</td>
	<td width=25 style=\"text-align:center;\">-></td>
	<td width=25 style=\"text-align:center;\">".($col[max_storage]+$data[lager]-$bd2[lager])."</tr>";
}

echo "</table></td>";
}
echo "</tr>
<tr><td colspan=2 align=center>".($_GET['fid'] > 0 ? ($_GET[u] == 1 ? "<input type=submit class=button value=Upgrade>" : ($field[buildings_id] != $_GET[bid] ? "<input type=submit class=button value=Bauen>" : "")) : "")."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr>
</table>
<input type=\"hidden\" name=\"ps\" value=\"".$_SESSION['pagesess']."\">
</form></body></html>";

?>
