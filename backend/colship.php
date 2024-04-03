<?php
header("Content-Type: text/html; charset=iso-8859-1");

include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;
session_start();
include_once("../class/sess.class.php");
$sess = new sess;
include_once("../class/user.class.php");
$u = new user;
include_once("../class/comm.class.php");
$comm = new comm;
if ($_SESSION["login"] != 1) die(show_error(106));

// Ausführende Funktionen - Können an sich auch in nen separates Objekt gepackt werden

function settorpcount($colid,$shipid,$type,$count)
{
	if (!check_int($colid) || !check_int($shipid) || !check_int($type) || !check_int($count)) return "\n1";
	global $db,$_SESSION;
	$col = $db->query("SELECT id,name,user_id,systems_id,sx,sy,eps FROM stu_colonies WHERE user_id=".$_SESSION["uid"]." AND id=".$colid,4);
	if ($col == 0) return "\n2";
	$ship = $db->query("SELECT a.id,a.name,a.user_id,a.systems_id,a.sx,a.sy,a.schilde_status,a.cloak,a.torp_type,b.max_torps,b.m10,c.storage,SUM(d.count) as sc FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON c.rumps_id=a.rumps_id LEFT JOIN stu_ships_storage as d ON d.ships_id=a.id WHERE a.user_id=".$_SESSION["uid"]." AND a.systems_id>0 AND a.systems_id=".$col[systems_id]." AND a.sx=".$col[sx]." AND a.sy=".$col[sy]." AND a.id=".$shipid." GROUP BY a.id",4);
	if ($ship == 0 || $ship[m10] == 0) return "\n3";
	if ($ship[sc] >= $ship[storage]) return "Kein freier Laderaum auf dem Schiff vorhanden";
	if ($ship[cloak] == 1) return "\n4";
	if ($col[eps] == 0) return "Keine Energie vorhanden";
	$to = $db->query("SELECT a.torp_type,a.type,a.name,a.goods_id,b.count FROM stu_torpedo_types as a LEFT JOIN stu_colonies_storage as b ON b.goods_id=a.goods_id AND b.colonies_id=".$colid." AND b.count>0 WHERE a.goods_id=".$type,4);
	if ($to == 0) return "\n5";
	$la_type = $db->query("SELECT torp_type FROM stu_modules WHERE module_id=".$ship[m10],1);
	if ($la_type < $to[type]) return "Dieser Torpedotyp kann nicht geladen werden";
	$ts = $db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$ship[id]." AND goods_id=".$to[goods_id],1);
	if ($ts >= $ship[max_torps]) return "Das Schiff ist bereit vollständig mit Torpedos bestückt";
	if ($to['count'] < $count) $count = $to['count'];
	if ($count+$ts > $ship[max_torps]) $count = $ship[max_torps]-$ts;
	if ($ship[sc]+$count > $ship[storage]) $count = $ship[storage]-$ship[sc];
	if (ceil($count/30) > $col[eps]) $count = $col[eps]*30;
	if ($count <= 0) return "\n6";
	$result = $db->query("UPDATE stu_ships_storage SET count=count+".$count." WHERE ships_id=".$ship[id]." AND goods_id=".$to[goods_id],6);
	if ($result == 0) $db->query("INSERT INTO stu_ships_storage (ships_id,goods_id,count) VALUES ('".$ship[id]."','".$to[goods_id]."','".$count."')");
	$result = $db->query("UPDATE stu_colonies_storage SET count=count-".$count." WHERE colonies_id=".$col[id]." AND goods_id=".$to[goods_id]." AND count>".$count,6);
	if ($result == 0) $db->query("DELETE FROM stu_colonies_storage WHERE colonies_id=".$col[id]." AND goods_id=".$to[goods_id]);
	$db->query("UPDATE stu_colonies SET eps=eps-".ceil($count/30)." WHERE id=".$col[id]);
	$db->query("UPDATE stu_ships SET torp_type=".$to[torp_type]." WHERE id=".$ship[id]);
	return $to[goods_id]."|".($count+$ts)."|".ceil($count/30)."\n".$count." Torpedos (Typ: ".$to[name].") geladen - ".ceil($count/30)." Energie verbraucht";
}

function settorpmin($colid,$shipid,$type,$count)
{
	if (!check_int($colid) || !check_int($shipid) || !check_int($type) || !check_int($count)) return "\n1";
	global $db,$_SESSION;
	$col = $db->query("SELECT a.id,a.name,a.user_id,a.systems_id,a.sx,a.sy,a.eps,a.max_storage,SUM(b.count) as sc FROM stu_colonies as a LEFT JOIN stu_colonies_storage as b ON b.colonies_id=a.id WHERE a.user_id=".$_SESSION["uid"]." AND a.id=".$colid." GROUP BY a.id",4);
	if ($col == 0) return "\n2";
	$ship = $db->query("SELECT a.id,a.name,a.user_id,a.systems_id,a.sx,a.sy,a.schilde_status,a.cloak,a.torp_type,b.m10 FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON c.rumps_id=a.rumps_id WHERE a.user_id=".$_SESSION["uid"]." AND a.systems_id>0 AND a.systems_id=".$col[systems_id]." AND a.sx=".$col[sx]." AND a.sy=".$col[sy]." AND a.id=".$shipid,4);
	if ($ship == 0 || $ship[m10] == 0) return "\n3";
	if ($col[sc] >= $col[max_storage]) return "Kein freier Laderaum auf der Kolonie vorhanden";
	if ($ship[cloak] == 1) return "\n4";
	if ($col[eps] == 0) return "Keine Energie vorhanden";
	$to = $db->query("SELECT a.torp_type,a.type,a.name,a.goods_id,b.count FROM stu_torpedo_types as a LEFT JOIN stu_colonies_storage as b ON b.goods_id=a.goods_id AND b.colonies_id=".$col[id]." AND b.count>0 WHERE a.goods_id=".$type,4);
	if ($to == 0) return "\n5";
	$la_type = $db->query("SELECT torp_type FROM stu_modules WHERE module_id=".$ship[m10],1);
	if ($la_type < $to[type]) return "Dieser Torpedotyp kann nicht geladen werden";
	$ts = $db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$ship[id]." AND goods_id=".$to[goods_id],1);
	if ($ts == 0) return "\n7";
	if ($count > $ts) $count = $ts;
	if ($col[sc]+$count > $col[max_storage]) $count = $col[max_storage]-$col[sc];
	if (ceil($count/30) > $col[eps]) $count = $col[eps]*30;
	if ($count<0) $count = 0;
	$result = $db->query("UPDATE stu_ships_storage SET count=count-".$count." WHERE ships_id=".$ship[id]." AND goods_id=".$to[goods_id]." AND count>".$count,6);
	$ris = $db->query("UPDATE stu_colonies_storage SET count=count+".$count." WHERE colonies_id=".$col[id]." AND goods_id=".$to[goods_id],6);
	if ($ris == 0) $db->query("INSERT INTO stu_colonies_storage (colonies_id,goods_id,count) VALUES ('".$col[id]."','".$to[goods_id]."','".$count."')");
	if ($result == 0)
	{
		$db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=".$ship[id]);
		$db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$ship[id]." AND goods_id=".$to[goods_id]);
		$t_poss = $db->query("SELECT a.name,b.goods_id,b.count FROM stu_torpedo_types as a LEFT JOIN stu_colonies_storage as b ON b.goods_id=a.goods_id AND b.colonies_id=".$col[id]." WHERE a.type<=".$db->query("SELECT torp_type FROM stu_modules WHERE module_id=".$ship[m10],1)." AND b.count>0");
		$tp = "Typ wählen: ";
		while($dat=mysql_fetch_assoc($t_poss)) $tp .= "<a href=\"javascript:void(0);\" onClick=\"choosetorp(".$dat[goods_id].",'".$dat[name]."');\"><img src=".$_SESSION[gfx_path]."/goods/".$dat[goods_id].".gif border=\"0\" title=\"".ftit($dat[name])."\"></a>&nbsp;";
	}
	$db->query("UPDATE stu_colonies SET eps=eps-".ceil($count/30)." WHERE id=".$col[id]);
	return $to[goods_id]."|".($ts-$count)."|".ceil($count/30)."|".$tp."\n".$count." Torpedos (Typ: ".$to[name].") entladen - ".ceil($count/30)." Energie verbraucht";
}

function returntorptype($shipid)
{
	return 1;
	if (!check_int($shipid)) return;
	global $db,$_SESSION;
	return $db->query("SELECT torp_type FROM stu_ships WHERE user_id=".$_SESSION[uid]." AND id=".$shipid,1);
}
function setcrewcount($colid,$shipid,$count)
{
	if (!check_int($colid) || !check_int($shipid) || !check_int($count) || $count < 0) return "\n1";
	global $db,$_SESSION;
	$col = $db->query("SELECT id,name,user_id,systems_id,sx,sy,eps,bev_free,bev_work,bev_max FROM stu_colonies WHERE user_id=".$_SESSION["uid"]." AND id=".$colid,4);
	if ($col == 0) return "\n2";
	$ship = $db->query("SELECT id,name,user_id,systems_id,sx,sy,schilde_status,cloak,crew,max_crew FROM stu_ships WHERE user_id=".$_SESSION["uid"]." AND systems_id>0 AND systems_id=".$col[systems_id]." AND sx=".$col[sx]." AND sy=".$col[sy]." AND id=".$shipid,4);
	if ($ship == 0) return "\n3";
	if ($ship[cloak] == 1) return "\n4";
	if ($col[eps] == 0) return "Keine Energie vorhanden";
	if ($count == $ship[crew] || $count < 0 || $count > $ship[max_crew]) return "\n4";
	if ($count < $ship[crew])
	{
		$uc = $ship[crew];
		if ($uc > $col[bev_max]-$col[bev_free]-$col[bev_work]) $uc = $col[bev_max]-$col[bev_free]-$col[bev_work];
		if (ceil($uc/5) > $col[eps])
		{
			$uc = $col[eps]*5;
			$ec = $col[eps];
		}
		else $ec = ceil($uc/5);
		$db->query("UPDATE stu_ships SET crew=crew-".$uc." WHERE id=".$shipid." LIMIT 1");
		$db->query("UPDATE stu_colonies SET bev_free=bev_free+".$uc.",eps=eps-".$ec." WHERE id=".$colid." LIMIT 1");
		return ($col[bev_free]+$uc)."|".($col[eps]-$ec)."|".($ship[crew]-$uc)."\n".$uc." Crewmitglieder von der ".stripslashes($ship[name])." gebeamt - ".$ec." Energie verbraucht";
	}
	$uc = $count-$ship[crew];
	if ($uc < 1) return "\n5";
	if ($uc > $col[bev_free]) $uc = $col[bev_free];
	if ($uc + $ship[crew] > $ship[max_crew]) return "\n6";
	if ($uc == 0) return "\n7";
	if (ceil($uc/5) > $col[eps])
	{
		$uc = $col[eps]*5;
		$ec = $col[eps];
	}
	else $ec = ceil($uc/5);
	$db->query("UPDATE stu_ships SET crew=crew+".$uc." WHERE id=".$shipid." LIMIT 1");
	$db->query("UPDATE stu_colonies SET bev_free=bev_free-".$uc.",eps=eps-".$ec." WHERE id=".$colid." LIMIT 1");
	return ($col[bev_free]-$uc)."|".($col[eps]-$ec)."|".($ship[crew]+$uc)."\n".$uc." Crewmitglieder zu der ".stripslashes($ship[name])." gebeamt - ".$ec." Energie verbraucht";
}
function setepscount($colid,$shipid,$count)
{
	if (!check_int($colid) || !check_int($shipid) || !check_int($count)) return "\n1";
	global $db,$_SESSION,$gfx;
	$col = $db->query("SELECT id,name,user_id,systems_id,sx,sy,eps,bev_free,bev_work,bev_max FROM stu_colonies WHERE user_id=".$_SESSION["uid"]." AND id=".$colid,4);
	if ($col == 0) return "\n2";
	$ship = $db->query("SELECT id,name,user_id,systems_id,sx,sy,schilde_status,cloak,eps,max_eps FROM stu_ships WHERE user_id=".$_SESSION["uid"]." AND systems_id>0 AND systems_id=".$col[systems_id]." AND sx=".$col[sx]." AND sy=".$col[sy]." AND id=".$shipid,4);
	if ($ship == 0) return "\n3";
	if ($ship[cloak] == 1) return "\n4";
	if ($col[eps] == 0) return "Keine Energie vorhanden";
	$count = $count-$ship[eps];
	if ($count > $col[eps]) $count = $col[eps];
	if ($count > $ship[max_eps]-$ship[eps]) $count = $ship[max_eps]-$ship[eps];
	if ($count < 0 || $count > $ship[max_eps]) return "\n5";
	$db->query("UPDATE stu_ships SET eps=eps+".$count." WHERE id=".$shipid);
	$db->query("UPDATE stu_colonies SET eps=eps-".$count." WHERE id=".$colid);
	$gfx = $_SESSION[gfx_path];
	if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
	return ($ship[eps]+$count)."|".($col[eps]-$count)."\n".$count." Energie zu der ".stripslashes($ship[name])." transferiert\n".renderepsstatusbar(($ship[eps]+$count),$ship[max_eps])." ".($ship[eps]+$count)."/".$ship[max_eps];
}
function setbattcount($colid,$shipid,$count)
{
	if (!check_int($colid) || !check_int($shipid) || !check_int($count)) return "\n1";
	global $db,$_SESSION,$gfx;
	$col = $db->query("SELECT id,name,user_id,systems_id,sx,sy,eps,bev_free,bev_work,bev_max FROM stu_colonies WHERE user_id=".$_SESSION["uid"]." AND id=".$colid,4);
	if ($col == 0) return "\n2";
	$ship = $db->query("SELECT a.id,a.name,a.user_id,a.systems_id,a.sx,a.sy,a.schilde_status,a.cloak,a.batt,a.max_batt,b.slots,b.is_shuttle FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.user_id=".$_SESSION["uid"]." AND a.systems_id>0 AND a.systems_id=".$col[systems_id]." AND a.sx=".$col[sx]." AND a.sy=".$col[sy]." AND a.id=".$shipid,4);
	if ($ship == 0) return "\n3";
	if ($ship[cloak] == 1) return "\n4";
	if ($ship[slots] > 0) return "Die Batterie von Stationen kann nicht aufgeladen werden";
	if ($col[eps] == 0) return "Keine Energie vorhanden";
	if ($ship[is_shuttle] == 1) return "Die Batterie von Shuttles kann nicht aufgeladen werden";
	if ($count <= $ship[batt] || $count < 0 || $count > $ship[max_batt]) return "\n5";
	$count = $count-$ship[batt];
	if ($count > $col[eps]) $count = $col[eps];
	if ($count > $ship[max_batt]-$ship[batt]) $count = $ship[max_batt]-$ship[batt];

	$db->query("UPDATE stu_ships SET batt=batt+".$count.",batt_wait=0 WHERE id=".$shipid);
	$db->query("UPDATE stu_colonies SET eps=eps-".$count." WHERE id=".$colid);
	$gfx = $_SESSION[gfx_path];
	if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
	return ($ship[batt]+$count)."|".($col[eps]-$count)."\nDie Ersatzbatterie der ".stripslashes($ship[name])." wurde um ".$count." Energie aufgeladen\n".renderepsstatusbar(($ship[eps]+$count),$ship[max_eps])." ".($ship[eps]+$count)."/".$ship[max_eps];
}
// Zuweisung
if ($_GET["a"] == "stc") $result = settorpcount($_GET[id],$_GET[t],$_GET[to],$_GET[c]);
if ($_GET["a"] == "stm") $result = settorpmin($_GET[id],$_GET[t],$_GET[to],$_GET[c]);
if ($_GET["a"] == "gtt") $result = returntorptype($_GET[t]);
if ($_GET["a"] == "scc") $result = setcrewcount($_GET[id],$_GET[t],$_GET[c]);
if ($_GET["a"] == "sec") $result = setepscount($_GET[id],$_GET[t],$_GET[c]);
if ($_GET["a"] == "sbc") $result = setbattcount($_GET[id],$_GET[t],$_GET[c]);
echo $_GET["a"]."\n".$result;
?>