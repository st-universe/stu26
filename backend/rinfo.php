<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION["login"] != 1) exit;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";

if (!check_int($_GET[rid]) || $_GET[rid] == 0) exit;

$res = $db->query("SELECT rumps_id,name FROM stu_rumps WHERE npc!='1' AND is_shuttle='0' ORDER BY sort,rumps_id");
$i = 0;
$max = mysql_num_rows($res);
while($da=mysql_fetch_assoc($res))
{
	$i++;
	if ($da[rumps_id] == $_GET[rid]) break;
}
$i-=1;
$res = floor($i/4)*4;
if ($res < 0) $res = 0;

$result = $db->query("SELECT rumps_id,name FROM stu_rumps WHERE npc!='1' AND is_shuttle='0' ORDER BY sort,rumps_id LIMIT ".$res.",4");

$data = $db->query("SELECT rumps_id,name,beamgood,beamcrew,evade_val,storage,bussard,erz,slots,reaktor,warp_cost,replikator,warpable,cloakable,min_crew,max_crew,buildtime,maintaintime,wp,eps_cost,max_shuttles,max_shuttle_type,max_cshuttle_type,m1c,m1minlvl,m1maxlvl,m2c,m2minlvl,m2maxlvl,m3c,m3minlvl,m3maxlvl,m4c,m4minlvl,m4maxlvl,m5c,m5minlvl,m5maxlvl,m6c,m6minlvl,m6maxlvl,m7c,m7minlvl,m7maxlvl,m8c,m8minlvl,m8maxlvl,m9c,m9minlvl,m9maxlvl,m10c,m10minlvl,m10maxlvl,m11c,m11minlvl,m11maxlvl FROM stu_rumps WHERE npc!='1' AND is_shuttle='0' AND rumps_id=".$_GET[rid],4);
if ($data == 0) exit;

echo "<style>td.kd:hover { background: #262323; }</style>
<table class=tcal style=\"border: 1px groove #8897cf;\"><th onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">Rumpfdetails</th><th width=16><a href=\"javascript:void(0);\" onClick=\"cClick();\" ".getonm('clx','buttons/x')."><img src=".$gfx."/buttons/x1.gif title=\"Schließen\" name=\"clx\" border=\"0\"></a></th><tr><td colspan=\"2\">
<div id=\"rl\">
<table class=tcal><tr>
<td width=\"10\" height=\"95\">".($res-4 < 0 ? "&nbsp;" : "<a href=\"javascript:void(0);\" onClick=\"setpos(".($res-4).");\" ".getonm('srb','buttons/b_from')."><img src=".$gfx."/buttons/b_from1.gif name=srb border=0 title=\"Zurück\"></a>")."</td>";
while($da=mysql_fetch_assoc($result))
{
	echo "<td align=center width=100 class=\"kd\"><a href=\"javascript:void(0);\" onClick=\"loadinfo(".$da[rumps_id].",'ri');\"><img src=".$gfx."/ships/".$da[rumps_id].".gif border=0><br>".stripslashes($da[name])."</td>";
}
echo "<td width=\"10\">".($res+4 > $max ? "&nbsp;" : "<a href=\"javascript:void(0);\" onclick=\"setpos(".($res+4).");\" ".getonm('srf','buttons/b_to')."><img src=".$gfx."/buttons/b_to1.gif name=srf border=0 title=\"Vorwärts\"></a>")."</td></tr></table>

</div></td></tr>
<tr><td colspan=\"2\"><div id=\"ri\">


<table class=tcal>
<tr><td width=\"50%\" valign=\"top\"><b>Informationen</b><br><br><div align=center>".stripslashes($data[name])."<br><img src=".$gfx."/ships/".$data[rumps_id].".gif title=\"".$data[name]."\"></div><br><br>";
if ($data[bussard] > 0) echo "<img src=".$gfx."/buttons/buss1.gif title=\"Bussard-Kollektoren Kapazität\"> Bussard: ".$data[bussard]."<br>";
if ($data[erz] > 0) echo "<img src=".$gfx."/buttons/erz1.gif title=\"Erz-Kollektoren Kapazität\"> ".$data[erz]."<br>";
echo "<img src=".$gfx."/buttons/ftp_1_2.gif title=\"Ausweichen\"> ".$data[evade_val]."<br>";
if ($data[reaktor] > 0) echo "<img src=".$gfx."/buttons/battp2.gif title=\"Fusionsreaktor\"> ".$data[reaktor]."<br>";
if ($data[replikator] == 1) echo "<img src=".$gfx."/buttons/repli.gif title=\"Replikator\"> Replikator<br>";
if ($data[warpable] == 1) echo "<img src=".$gfx."/buttons/warp2.gif title=\"Warpfähig\"> Warpfähig<br>";
if ($data[warpable] == 1) echo "<img src=".$gfx."/buttons/warp2.gif title=\"Warpkosten\"> ".$data[warp_cost]."<br>";
if ($data[cloakable] == 1) echo "<img src=".$gfx."/buttons/tarnv.gif title=\"Tarnfähig\"> Tarnfähig<br>";
if ($data[slots] > 0) echo "<img src=".$gfx."/buttons/dock1.gif title=\"Dockplätze\"> ".$data[slots]."<br>";
if ($data[beamgood] > 0) echo "<img src=".$gfx."/buttons/bgo.gif title=\"Frachttransporter\"> ".$data[beamgood]."<br>";
if ($data[beamcrew] > 0) echo "<img src=".$gfx."/buttons/bcr.gif title=\"Crewtransporter\"> ".$data[beamcrew]."<br>";
if ($data[slots] > 0) echo "<img src=".$gfx."/buttons/dock1.gif title=\"Dockplätze\"> ".$data[slots]."<br>";
echo "<img src=".$gfx."/buttons/crew.gif title=\"Crew min/max\"> ".$data['min_crew']."/".$data['max_crew']."<br>";
echo "<img src=".$gfx."/buttons/lager.gif title=\"Laderaum\"> ".$data[storage]."<br>";
if ($data[max_shuttles] > 0)
{
	echo "<br>Max. Shuttles: ".$data[max_shuttles]."<br>
	Max. Shuttletyp: ".$data[max_shuttle_type]."<br>
	Shuttletypen: ".$data[max_cshuttle_type]."<br><br>";
}
echo "<br><br>
<b>Baukosten</b><br><img src=".$gfx."/buttons/e_trans2.gif title=\"Energie\"> ".$data[eps_cost]."<br>";
$result = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.rumps_id=".$data[rumps_id]." ORDER BY b.sort");
while($da=mysql_fetch_assoc($result)) echo "<img src=".$gfx."/goods/".$da[goods_id].".gif title=\"".$da[name]."\"> ".$da["count"]."<br>";
echo "</td><td valign=\"top\"><b>Module</b><br><br><table width=100%>
<tr><td></td><td>Anz</td><td>Minlvl</td><td>Maxlvl</td></tr>";
$i = 1;
while($i<=11)
{
	if ($data["m".$i."c"] == 0)
	{
		$i++;
		continue;
	}
	echo "<tr><td><img src=".$gfx."/buttons/modul_".$i.".gif title=\"".getmodtypedescr($i)."\"></td><td>".$data["m".$i."c"]."</td><td style=\"color: Green;\">".$data["m".$i."minlvl"]."</td><td style=\"color: Lime;\">".$data["m".$i."maxlvl"]."</td></tr>";
	$i++;
}
echo "</table></td></tr>
</table>

</div>
</td></tr></table>";
?>