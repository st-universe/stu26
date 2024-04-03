<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
	
$op .= "<table>
<tr>";

$res = $db->query("SELECT a.buildings_id,a.name,a.lager,a.eps_cost,a.eps,a.eps_proc,a.bev_pro,a.bev_use,a.level,a.integrity,a.points,a.schilde,a.bclimit,a.blimit,a.upgrade_from,a.buildtime,b.name as upname FROM stu_buildings as a LEFT JOIN stu_buildings as b ON a.upgrade_from=b.buildings_id WHERE ((a.view=1 OR a.buildings_id=1) AND (a.buildings_id < 200 OR a.buildings_id > 299) AND a.buildings_id != 99 AND a.buildings_id != 98) ORDER BY a.level,a.name");

while($data=mysql_fetch_assoc($res))
{
	$field = $db->query("SELECT type FROM stu_field_build WHERE buildings_id=".$data[buildings_id]." AND type<200 ORDER BY type ASC LIMIT 1",1);
	$cost = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_cost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
	$goods = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
	$op .= "<td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>".stripslashes($data[name])."</th>
	</tr>
	<tr>
		<td><div align=center><img src=gfx/buildings/".$data[buildings_id]."/".($field == 0 ? 1 : $field).".gif></div><br>";
		$j = 0;
		$result = $db->query("SELECT type FROM stu_field_build WHERE type<200 AND buildings_id=".$data[buildings_id]);
		while($dat=mysql_fetch_assoc($result))
		{
			$j++;
			$op .= "<img src=gfx/fields/".$dat[type].".gif width=16 height=16>&nbsp;";
			if ($j%7 == 0) $op .= "<br>";
		}
			$op .= "</td>
	</tr>
	<tr>
	<td>Ab Level: ".$data[level]."<br>";
	if ($data[bev_pro] > 0) $op .= "<img src=gfx/bev/bev_free_1_1.gif title='Wohnraum'> +".$data[bev_pro]."<br>";
	if ($data[bev_use] > 0) $op .= "<img src=gfx/bev/bev_used_1_1.gif title='Benötige Arbeiter'> ".$data[bev_use]."<br>";
	if ($data[lager] > 0) $op .= "<img src=gfx/buttons/lager.gif title='Lagerraum'> +".$data[lager]."<br>";
	if ($data[eps] > 0) $op .= "<img src=gfx/buttons/e_trans1.gif title='Energiespeicher'> +".$data[eps]."<br>";
	if ($data[eps_proc] != 0) $op .= "<img src=gfx/buttons/e_trans2.gif title='Energie'> ".($data[eps_proc] > 0 ? "+".$data[eps_proc] : $data[eps_proc])."<br>";
	if ($data[points] > 0) $op .= "<img src=gfx/buttons/points.gif title='Wirtschaft'> +".$data[points]."<br>";
	$op .= "Integrität: ".$data[integrity]."<br>
	<img src=gfx/buttons/time.gif title='Bauzeit'> ".gen_time($data[buildtime]);
	if ($data[blimit] > 0) $op .= "<br>Limit (global): ".$data[blimit];
	if ($data[bclimit] > 0) $op .= "<br>Limit (pro Kolonie): ".$data[bclimit];
	if ($data[upgrade_from] > 0) $op .= "<br>Upgrade von: ".$data[upname];
	$op .= "</td>
	</tr>";
	if (mysql_num_rows($goods) > 0)
	{
		$op .= "<tr>
		<td><u>Waren</u><br>";
		while($g=mysql_fetch_assoc($goods)) $op .= "<img src=gfx/goods/".$g[goods_id].".gif title='".$g[name]."'> ".($g['count'] > 0 ? "+".$g['count'] : $g['count'])."<br>";
		$op .= "</td>
		</tr>";
	}
	$op .= "<tr>
		<td><u>Baukosten</u><br>
		<img src=gfx/buttons/e_trans2.gif title='Energie'> ".$data[eps_cost]."<br>";
		while($c=mysql_fetch_assoc($cost)) $op .= "<img src=gfx/goods/".$c[goods_id].".gif title='".$c[name]."'> ".$c['count']."<br>";
		$op .= "</td>
	</tr></table></td><td width=40></td>";
	$i++;
	if ($i%4==0) $op .= "</tr><tr>";
}
$op .= "</tr></table>";
unlink($global_path."/inc/lists/gbl.html");
$fp = fopen($global_path."/inc/lists/gbl.html","a+");
fwrite($fp,$op);
fclose($fp);
?>
