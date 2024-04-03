<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if (!check_int($_GET[id]) || $_SESSION["login"] != 1) exit;

$db = new db;
include_once("../class/map.class.php");
$map = new map;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

$data = $db->query("SELECT a.id,a.rumps_id,a.sx,a.sy,a.systems_id,a.cx,a.cy,a.huelle,a.max_huelle,a.schilde,a.max_schilde,a.schilde_status,a.eps,a.max_eps,a.warp,a.warpable,a.cloakable,a.torp_type,a.kss_range,a.lss_range,a.points,a.lastmaintainance+b.maintaintime as mt,b.name as pname,b.evade,b.treffer,b.reaktor,b.wkkap,b.max_torps,b.sensor_val,b.cloak_val,b.m1,b.m2,b.m3,b.m4,b.m5,b.m6,b.m7,b.m8,b.m9,b.m10,b.m11,c.eps_cost,c.slots,c.bussard,c.erz,c.is_shuttle,c.storage,c.m1c,c.m2c,c.m3c,c.m4c,c.m5c,c.m6c,c.m7c,c.m8c,c.m9c,c.m10c,c.m11c FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON c.rumps_id=a.rumps_id WHERE a.id=".$_GET['id']." AND a.user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($data == 0) exit;
if ($data[m6] > 0) $data[weapon] = $db->query("SELECT wtype,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$data[m6],4);
if ($data[m10] > 0 && $data[torp_type] > 0) $data[torpedo] = $db->query("SELECT torp_type,name,damage,varianz,goods_id FROM stu_torpedo_types WHERE torp_type=".$data[torp_type],4);

echo "<table class=\"tcal\" style=\"border: 1px groove #8897cf;\">
<th colspan=3 onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">Schiffsdetails ".stripslashes($data[name])."</th>
<tr><td width=200 valign=top><b>Informationen</b><br><br><div align=\"center\"><img src=".$gfx."/ships/".vdam($data).$data[rumps_id].".gif title=\"".ftit($data['pname'])."\"></div><br>";
if ($data[m6] > 0)
{
	
	if ($data['weapon']['pulse'] == 0) $damage = round($data['weapon']['strength'] * get_weapon_damage($data[m6c]),1);
	else $damage = round($data['weapon']['strength'],1);
	echo "<br><img src=".$gfx."/goods/".$data[m6].".gif title=\"Schaden (Varianz)\"> Dmg.: ".$damage." (".$data[weapon][varianz]." %)
	<br><img src=".$gfx."/buttons/gefecht.gif title=\"Treffer-Wahrscheinlichkeit\"> Treffer: ".$data[treffer]." %";
}
if ($data[m10] > 0 && $data[torp_type] > 0)
{
	echo "<br><img src=".$gfx."/goods/".$data[m10].".gif title=\"Schaden (Varianz)\"> Dmg.: ".$data[torpedo][damage]." (".$data[torpedo][varianz]." %)";
}
if ($data[max_torps] > 0) echo "<br><img src=".$gfx."/goods/".$data[m10].".gif title=\"Torpedokapazität\"> Torpedos: ".$data[max_torps];
echo "<br><img src=".$gfx."/buttons/torp.gif title=\"Torpedo Ausweichwahrscheinlickeit\"> T-Ausweich: ".$data[evade]."%
<br><img src=".$gfx."/buttons/battp2.gif title=\"Reaktorwert\"> Reaktor: ".$data[reaktor]."
<br><img src=".$gfx."/goods/".$data[m4].".gif title=\"Sensorenreichweite\"> KSS/LSS: ".$data[kss_range]."/".$data[lss_range];
if ($data[mt] > 0 && $data[rumps_id] != 1) echo "<br><img src=".$gfx."/buttons/maint1.gif title=\"Nächste Wartung\"> ".date("d.m.Y H:i",$data[mt]);
echo "<br><img src=".$gfx."/buttons/points.gif title=\"Wirtschaftspunkte\"> Punkte: ".$data[points]."<br><br>";
$result = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_ships_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.ships_id=".$_GET['id']." ORDER BY b.sort");
if (mysql_num_rows($result) > 0)
{
	echo "<img src=".$gfx."/buttons/lager.gif> ".$db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$_GET['id'],1)."/".$data['storage']."<br>";
	while($dat=mysql_fetch_assoc($result)) echo "<img src=".$gfx."/goods/".$dat['goods_id'].".gif title=\"".$dat['name']."\"> ".$dat['count']."<br>";
}
echo "</td>
<td valign=top width=200><b>Status</b><br><br>
<img src=".$gfx."/goods/".$data[m1].".gif title=\"Hülle\"> ".renderhuellstatusbar($data[huelle],$data[max_huelle])." ".$data[huelle]."/".$data[max_huelle]."
<br><img src=".$gfx."/goods/".$data[m2].".gif title=\"Schilde\"> ".rendershieldstatusbar($data[schilde_status],$data[schilde],$data[max_schilde])." ".($data[schilde_aktiv] == 1 ? "<font color=cyan>".$data[schilde]."/".$data[max_schilde]."</font>" : $data[schilde]."/".$data[max_schilde])."
<br><img src=".$gfx."/goods/".$data[m8].".gif title=\"Energie\"> ".renderepsstatusbar($data[eps],$data[max_eps])." ".$data[eps]."/".$data[max_eps];
if ($data[huelle] < $data[max_huelle] && $data[is_shuttle] != 1)
{
	echo "<br><br><b>Reparaturkosten</b><br><br><img src=".$gfx."/buttons/e_trans2.gif title=\"Energie \"> ".round(($data[eps_cost]/100)*(100-((100/$data[max_huelle])*$data[huelle])))."<br>";
	
	$result = $db->query("SELECT ROUND(a.count/100*(100-((100/".$data[max_huelle].")*".$data[huelle]."))) as gcount,a.goods_id,b.name FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.rumps_id=".$data[rumps_id]." ORDER BY b.sort");
	while($arr=mysql_fetch_assoc($result))
	{
		if ($arr['gcount'] <= 0) continue;
		echo "<img src=".$gfx."/goods/".$arr['goods_id'].".gif title=\"".stripslashes($arr['name'])."\"> ".$arr['gcount']."<br>";
	}
	if ($data[rumps_id] != 1)
	{
		$i = 1;
		while($i<=11)
		{
			if ($data["m".$i] > 0)
			{
				$cn =round($data["m".$i."c"]/100*(100-(100/$data[max_huelle])*$data[huelle]));
				if ($cn < 1)
				{
					$i++;
					continue;
				}
				$arr = $db->query("SELECT goods_id,name FROM stu_goods as a WHERE goods_id=".$data["m".$i],4);
				echo "<img src=".$gfx."/goods/".$arr[goods_id].".gif title=\"".$arr[name]."\"> ".$cn."<br>";
			}
			$i++;
		}
	}
}
if ($data['slots'] == 0)
{
	$result = $db->query("SELECT ROUND(SUM(a.count)/4) as gcount,a.goods_id,b.name FROM stu_modules_cost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE b.view='1' AND (a.module_id=".$data['m1']." OR module_id=".$data['m2']." OR module_id=".$data['m3']." OR module_id=".$data['m4']." OR module_id=".$data['m5']." OR module_id=".$data['m6']." OR module_id=".$data['m7']." OR module_id=".$data['m8']." OR module_id=".$data['m9']." OR module_id=".$data['m10']." OR module_id=".$data['m11'].") GROUP BY a.goods_id ORDER BY b.sort");
	echo "<br><br><b>Wartungskosten</b><br><br><img src=".$gfx."/buttons/e_trans2.gif title=\"Energie \"> ".round($data['eps_cost']/4)."<br>";
	while($arr=mysql_fetch_assoc($result))
	{	
		if ($arr['gcount'] <= 0) continue;
			echo "<img src=".$gfx."/goods/".$arr['goods_id'].".gif title=\"".stripslashes($arr['name'])."\"> ".$arr['gcount']."<br>";
	}
}
echo "</td>
<td valign=top width=200>
<b>Aktionen</b><br><br>";
if ($data['systems_id'] > 0) $field = $map->getfieldbyid_kss($data['sx'],$data['sy'],$data['systems_id']);
else $field = $map->getfieldbyid_lss($data['cx'],$data['cy']);
if (($field['type'] == 11 || $field['type'] == 12) && $data['erz'] > 0 && $data['warp'] != 1)
{
	echo "<form action=main.php><input type=hidden name=p value=ship><input type=hidden name=a value=ce><input type=hidden name=id value=".$_GET['id'].">
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200>
	<tr><th colspan=2><img src=".$gfx."/map/".$field['type'].".gif title='".$field['name']."' width=15 height=15> Erze sammeln</th></tr>
	<tr><td><select name=c".($data['eps'] < 3 ? " disabled" : "").">";
	for($i=1;$i<=floor($data['eps']/3);$i++) echo "<option value=".($i*3).">".($i*3);
	echo "</select> <input type=submit value=sammeln class=button".($data['eps'] == 0 ? " disabled" : "")."> <input type=submit name=c value=max class=button".($data['eps'] == 0 ? " disabled" : "")."></td></tr></form></table>";
}
if ($field['deut'] > 0 && $data['bussard'] > 0 && $data['warp'] != 1)
{
	echo "<form action=main.php><input type=hidden name=p value=ship><input type=hidden name=a value=cd><input type=hidden name=m value=5><input type=hidden name=id value=".$_GET['id'].">
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200>
	<tr><th colspan=2><img src=".$gfx."/goods/5.gif title='Deuterium'> Deuterium (".$data['bussard']."/".$field['deut'].")</th></tr>
	<tr><td><input type=text size=2 name=c class=text".($data['eps'] == 0 ? " disabled" : "")."> <input type=submit value=sammeln class=button".($data['eps'] == 0 ? " disabled" : "")."> <input type=submit name=c value=max class=button".($data['eps'] == 0 ? " disabled" : "")."></td></tr></form></table>";
}
if ($field['type'] == 6 && $data['bussard'] > 0 && $data['warp'] != 1)
{
	echo "<form action=main.php><input type=hidden name=p value=ship><input type=hidden name=a value=cd><input type=hidden name=m value=7><input type=hidden name=id value=".$_GET['id'].">
	<table bgcolor=#262323 cellspacing=1 cellpadding=1 width=200>
	<tr><th colspan=2><img src=".$gfx."/goods/7.gif title='Plasma'> Plasma (".$data['bussard']."/".round($data['bussard']/3).")</th></tr>
	<tr><td><input type=text size=2 name=c class=text".($data['eps'] == 0 ? " disabled" : "")."> <input type=submit value=sammeln class=button".($data['eps'] == 0 ? " disabled" : "")."> <input type=submit name=c value=max class=button".($data['eps'] == 0 ? " disabled" : "")."></td></tr></form></table>";
}
echo "</td>
</tr><tr><td align=\"center\" colspan=3><input type=button class=button onClick=\"cClick();\" value=Schließen></td></tr>
</table>";
?>