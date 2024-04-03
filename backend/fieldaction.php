<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;
if (!check_int($_GET['id']) || !check_int($_GET['fid'])) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../../gfx/";

$sta = $db->query("SELECT * FROM stu_stations WHERE id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($sta == 0) exit;

$field = $db->query("SELECT a.*,b.* FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING(component_id) WHERE a.field_id=".$_GET['fid']." AND a.stations_id=".$_GET['id']." LIMIT 1",4);
if ($field == 0) exit;

echo "<table class=\"tcal\" style=\"border: 1px solid #8897cf;\">
<th onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">";

if ($_GET['fid'] < 100) {
echo "Feld ".$_GET['fid']." - ";

echo "".($field['component_id'] > 0 ? stripslashes($field['name']) : getsnamebyfield($field['type']))."</th><th width=16 align=right><a href=\"javascript:void(0);\" onClick=\"cClick();\" ".getonm('clx','buttons/x')."><img src=".$gfx."/buttons/x1.gif title=\"Schließen\" name=\"clx\" border=\"0\"></a></th>
<tr>
	<td colspan=\"2\">
	<table>
	<tr>
	<td style=\"width: 40px;\" valign=\"top\">";
	if ($field['component_id'] > 0)
	{
		echo "<img src=".$gfx."/components/".$field['component_id']."_".$field['type'].".gif title=\"".ftit($field['name'])."\"><br />";
		$building = $db->query("SELECT * FROM  stu_station_components WHERE component_id=".$field['component_id']." LIMIT 1",4);
		if ($building['is_activateable'] == 1)
		{
			echo "<br /><b>Produktion</b>";
			if ($building['bev_use'] > 0) echo "<br><img src=".$gfx."/bev/bev_used_1_".$_SESSION['race'].".gif title='Benötige Arbeiter'> ".$building['bev_use'];
			if ($building['bev_pro'] > 0) echo "<br><img src=".$gfx."/bev/bev_free_1_".$_SESSION['race'].".gif title='Erzeugt Wohnraum'> ".$building['bev_pro'];
			if ($building['lager'] > 0) echo "<br><img src=".$gfx."/buttons/lager.gif title=\"Lager\"> ".$building['lager'];
			if ($building['eps'] > 0) echo "<br><img src=".$gfx."/buttons/e_trans1.gif title=\"EPS\"> ".$building['eps'];
			if ($building['points'] > 0) echo "<br><img src=".$gfx."/buttons/points.gif title='Wirtschaft'> +".$building['points'];
			echo "<br /><br /><img src=".$gfx."/buttons/e_trans2.gif title=\"Energie\"> ".($building['eps_proc'] > 0 ? "+".$building['eps_proc'] : $building['eps_proc'])."<br />";
			$result = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_station_component_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.component_id=".$field['component_id']." ORDER BY b.sort");
			while($data=mysql_fetch_assoc($result))
			{
				echo "<img src=".$gfx."/goods/".$data['goods_id'].".gif title=\"".ftit($data['name'])."\"> ".($data['count'] > 0 ? "+".$data['count'] : $data['count'])."<br />";
			}
		}
		echo "</td><td valign=\"top\"><br/>
		<table><tr>";



		if ($field['is_activateable'] == 1) echo "<td style=\"width: 130px; border: 1px solid #262323; margin-left: 3px;\">".($field['aktiv'] == 1 ? "<a href=?ps=".$_SESSION['pagesess']."&p=station&s=show&id=".$_GET['id']."&a=db&fid=".$_GET['fid']." ".getonm("av","buttons/gebdact")."><img src=".$gfx."/buttons/gebdact1.gif name=av title='Modul deaktivieren' border=0> Deaktivieren</a>" : "<a href=?ps=".$_SESSION['pagesess']."&p=station&s=show&id=".$_GET['id']."&a=ab&fid=".$_GET['fid']." ".getonm("av","buttons/gebact")."><img src=".$gfx."/buttons/gebact1.gif name=av title='Modul aktivieren' border=0> Aktivieren</a>")."</span>";
		echo "<td style=\"width: 130px; border : 1px solid #9C1417; margin-left: 3px;\"><a href=\"javascript:void(0);\" onClick=\"showConfirm(".$_GET['fid'].");\"><img src=".$gfx."/buttons/demont.gif border=0><font color=#FF0000> Deinstallieren</font></a></span>";

		echo "</tr></table><table><tr><td style=\"width: 130px; border : 1px solid #262323;\"><a href=?p=station&s=bm&id=".$_GET['id']."&fid=".$_GET['fid']." ".getonm('bmt','buttons/notiz')."><img src=".$gfx."/buttons/notiz1.gif border=0 name=bmt title=\"Ausrüstung\"> Ausrüstung</a></td>";








		echo "</tr></table>";
	}
	else
	{
		echo "<img src=".$gfx."/fieldss/".$field['type'].".gif title=\"".getsnamebyfield($field['type'])."\">
		</td><td><table><tr><td style=\"border : 1px solid #262323; width: 180px;\"><a href=?p=station&s=bm&id=".$_GET['id']."&fid=".$_GET['fid']." ".getonm('bmt','buttons/notiz')."><img src=".$gfx."/buttons/notiz1.gif border=0 name=bmt title=\"Ausrüstungsmenü\"> Ausrüstung</a></td></tr></table></td>";
	}
	echo "<div id=dmc></div>
	</td>
	</tr>
	</table>";

	echo "</td>
</tr>
</table>";

} else {

echo "Frachtschiff der ";

echo "".($field['component_id'] > 0 ? getsnamebyfield($field['type'])." ".stripslashes($field['name']) : getsnamebyfield($field['type']))."</th><th width=16 align=right><a href=\"javascript:void(0);\" onClick=\"cClick();\" ".getonm('clx','buttons/x')."><img src=".$gfx."/buttons/x1.gif title=\"Schließen\" name=\"clx\" border=\"0\"></a></th>
<tr>
	<td colspan=\"2\">
	<table>
	<tr>
	<td style=\"width: 40px;\" valign=\"top\">";
	if ($field['component_id'] > 0)
	{
		echo "<img src=".$gfx."/components/".$field['component_id']."_".$field['type'].".gif title=\"".ftit($field['name'])."\"><br />";
		$building = $db->query("SELECT * FROM  stu_station_components WHERE component_id=".$field['component_id']." LIMIT 1",4);
		if ($building['is_activateable'] == 1)
		{
			echo "<br /><b>Produktion</b>";
			if ($building['bev_use'] > 0) echo "<br><img src=".$gfx."/bev/bev_used_1_".$_SESSION['race'].".gif title='Benötige Arbeiter'> ".$building['bev_use'];

			echo "<br /><br /><img src=".$gfx."/buttons/e_trans2.gif title=\"Energie\"> ".($building['eps_proc'] > 0 ? "+".$building['eps_proc'] : $building['eps_proc'])."<br />";
			$result = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_station_component_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.component_id=".$field['component_id']." ORDER BY b.sort");
			while($data=mysql_fetch_assoc($result))
			{
				echo "<img src=".$gfx."/goods/".$data['goods_id'].".gif title=\"".ftit($data['name'])."\"> ".($data['count'] > 0 ? "+".$data['count'] : $data['count'])."<br />";
			}
		}
		echo "</td><td valign=\"top\"><br/>";
		echo "<table><tr>";



		echo "<td style=\"width: 130px; border : 1px solid #262323;\"><a href=?p=station&s=show&id=".$_GET['id']."&a=ret&fid=".$_GET['fid']." ".getonm('bmt','buttons/b_down')."><img src=".$gfx."/buttons/b_down1.gif border=0 name=bmt title=\"Rückkehr\"> Zurückkehren</a></td>";


		//echo "</tr></table><table><tr><td style=\"width: 130px; border : 1px solid #262323;\"><a href=?p=station&s=show&id=".$_GET['id']."&a=ret&fid=".$_GET['fid']." ".getonm('bmt','buttons/b_down')."><img src=".$gfx."/buttons/b_down1.gif border=0 name=bmt title=\"Rückkehr\"> Zurückkehren</a></td>";








		echo "</tr></table>";
	}
	else
	{
		echo "<img src=".$gfx."/fieldss/".$field['type'].".gif title=\"".getsnamebyfield($field['type'])."\">
		</td><td><table><tr><td style=\"border : 1px solid #262323; width: 180px;\"><a href=?p=station&s=gat&id=".$_GET['id']."&fid=".$_GET['fid']." ".getonm('bmt','buttons/notiz')."><img src=".$gfx."/buttons/notiz1.gif border=0 name=bmt title=\"Auftrag\"> Auftrag erteilen</a></td></tr>
<tr><td style=\"border : 1px solid #262323; width: 180px;\"><a href=?p=station&s=gat&id=".$_GET['id']."&fid=".$_GET['fid']." ".getonm('bmt','buttons/notiz')."><img src=".$gfx."/buttons/notiz1.gif border=0 name=bmt title=\"Auftrag\"> Auftrag erteilen</a></td></tr></table></td>";
	}
	echo "<div id=dmc></div>
	</td>
	</tr>
	</table>";

	echo "</td>
</tr>
</table>";




}

?>
