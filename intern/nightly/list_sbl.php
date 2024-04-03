<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
	
$op .= "<table>
<tr>";

$res = $db->query("SELECT a.* FROM stu_station_components as a WHERE a.view=1 ORDER BY a.name");

while($data=mysql_fetch_assoc($res))
{
	$cost = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_station_component_cost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.component_id=".$data[component_id]." ORDER BY b.sort");
	$goods = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_station_component_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.component_id=".$data[component_id]." ORDER BY b.sort");



	$showfield = 0;
	if ($data[field1] != 0) $showfield = 1;
	elseif ($data[field2] != 0) $showfield = 2;
	elseif ($data[field3] != 0) $showfield = 3;
	elseif ($data[field4] != 0) $showfield = 4;
	elseif ($data[field5] != 0) $showfield = 5;
	elseif ($data[field6] != 0) $showfield = 6;
	elseif ($data[field9] != 0) {
		$showfield = 9;
		$ignore = 1;
		if ($data[field6] != 0) $ignore = 0;
		if ($data[field5] != 0) $ignore = 0;
		if ($data[field4] != 0) $ignore = 0;
		if ($data[field3] != 0) $ignore = 0;
		if ($data[field2] != 0) $ignore = 0;
		if ($data[field1] != 0) $ignore = 0;
	}


	$op .= "<td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
	<tr>
		<th>".stripslashes($data[name])."</th>
	</tr>
	<tr>
		<td><div align=center><img src=gfx/components/".$data[component_id]."_".($showfield).".gif></div><br>";
		$j = 0;
		if ($data[field1] != 0) $op .= "<img src=gfx/fieldss/1.gif width=20 height=20>&nbsp;";
		if ($data[field2] != 0) $op .= "<img src=gfx/fieldss/2.gif width=20 height=20>&nbsp;";
		if ($data[field3] != 0) $op .= "<img src=gfx/fieldss/3.gif width=20 height=20>&nbsp;";
		if ($data[field4] != 0) $op .= "<img src=gfx/fieldss/4.gif width=20 height=20>&nbsp;";
		if ($data[field5] != 0) $op .= "<img src=gfx/fieldss/5.gif width=20 height=20>&nbsp;";
		if ($data[field6] != 0) $op .= "<img src=gfx/fieldss/6.gif width=20 height=20>&nbsp;";

		$op .= "</td>
	</tr>
	<tr>
	<td>";
	if ($data[bev_pro] > 0) $op .= "<img src=gfx/bev/bev_free_1_1.gif title='Wohnraum'> +".$data[bev_pro]."<br>";
	if ($data[bev_use] > 0) $op .= "<img src=gfx/bev/bev_used_1_1.gif title='Benötige Arbeiter'> ".$data[bev_use]."<br>";
	if ($data[lager] > 0) $op .= "<img src=gfx/buttons/lager.gif title='Lagerraum'> +".$data[lager]."<br>";
	if ($data[eps] > 0) $op .= "<img src=gfx/buttons/e_trans1.gif title='Energiespeicher'> +".$data[eps]."<br>";
	if ($data[eps_proc] != 0) $op .= "<img src=gfx/buttons/e_trans2.gif title='Energie'> ".($data[eps_proc] > 0 ? "+".$data[eps_proc] : $data[eps_proc])."<br>";
	if ($data[wk_proc] != 0) $op .= "<img src=gfx/buttons/e_trans1.gif title='Warpkernladung'> ".$data[wk_proc]."<br>";


	if ($data[schilde] != 0) $op .= "<img src=gfx/buttons/shldac2.gif title='Schilde'> ".$data[schilde]."<br>";
	if ($data[schildredu] != 0) {
		if ($data[schildtyp] == 1) $op .= "<img src=gfx/buttons/shldp2.gif title='Schadensreduktion'> ".$data[schildredu]." / Phasisch<br>";
		if ($data[schildtyp] == 2) $op .= "<img src=gfx/buttons/shldp2.gif title='Schadensreduktion'> ".$data[schildredu]." / Plasmatisch<br>";
		if ($data[schildtyp] == 3) $op .= "<img src=gfx/buttons/shldp2.gif title='Schadensreduktion'> ".$data[schildredu]." / Polarisch<br>";
	}

	if ($data[component_id] == 102) $op .= "<img src=gfx/buttons/repli2.gif title='Replikatorration'> +3<br>";

	if ($data[sensor] != 0) $op .= "<img src=gfx/buttons/lss2.gif title='Sensorreichweite'> ".$data[sensor]."<br>";



	$op .= "<img src=gfx/buttons/time.gif title='Bauzeit'> ".gen_time($data[buildtime]);
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
unlink($global_path."/inc/lists/sbl.html");
$fp = fopen($global_path."/inc/lists/sbl.html","a+");
fwrite($fp,$op);
fclose($fp);
?>
