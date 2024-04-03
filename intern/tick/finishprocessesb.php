<?php
include_once("../../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;



include_once($global_path."/class/log.class.php");
$log = new log;


include_once($global_path."/class/comm.class.php");
$comm = new comm;

include_once($global_path."/class/fleet.class.php");
$fleet = new fleet;

include_once($global_path."/class/qpm.class.php");
$qpm = new qpm;

include_once($global_path."/class/ship.class.php");
$ship = new ship;

include_once($global_path."/class/stations.class.php");
$stations = new stations;

include_once($global_path."/inc/func.inc.php");

// Check des Spielstatus
if ($db->query("SELECT value FROM stu_game_vars WHERE var='state'",1) != 1) exit;

// Damit wir nicht andauernd $time aufrufen müssen...
$time = time();


$log->deleteLogType("finishp");

$log->enterLog("finishp","start");

// Gebäude fertigstellen
$result = $db->query("SELECT a.colonies_id,a.field_id,a.buildings_id,b.value FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies_actions as b ON a.colonies_id=b.colonies_id AND a.field_id=b.value AND b.var='db' WHERE a.aktiv>1 AND a.aktiv<".$time." AND a.buildings_id!=47 ORDER BY a.colonies_id");
while($data=mysql_fetch_assoc($result))
{
	if ($data['colonies_id'] != $lc)
	{
		if ($lc) $comm->sendpm(1,$cd['user_id'],"<b>Auf der Kolonie ".$cd['name']." wurden Gebäude fertiggestellt</b><br>".$txt,4);
		unset($txt);
		$lc = $data['colonies_id'];
		$cd = $db->query("SELECT user_id,name,bev_free FROM stu_colonies WHERE id=".$lc." LIMIT 1",4);
		$bf = $cd['bev_free'];
		$luser = $cd['user_id'];
	}
	if ($data['buildings_id'] == 0)
	{
		$db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE colonies_id=".$data['colonies_id']." AND field_id=".$data['field_id']." LIMIT 1");
		continue;
	}
	$bd = $db->query("SELECT name,eps,lager,bev_use,bev_pro,schilde,is_activateable,needs_rbf FROM stu_buildings WHERE buildings_id=".$data['buildings_id']." LIMIT 1",4);
	$aktiv = 0;
	$stop = 0;
	if ($bd['needs_rbf'] == 1 && $db->query("SELECT field_id FROM stu_colonies_fielddata WHERE aktiv=1 AND colonies_id=".$data['colonies_id']." AND buildings_id=24 LIMIT 1",1) == 0) $stop = 1;
	// if ($data['field_id'] > 72 && $db->query("SELECT field_id FROM stu_colonies_fielddata WHERE aktiv=1 AND colonies_id=".$data['colonies_id']." AND buildings_id=46 LIMIT 1",1) == 0) $stop = 1;
	if (!$data['value'] && $bd['is_activateable'] == 1 && $bf >= $bd['bev_use'] && $stop==0)
	{
		$db->query("UPDATE stu_colonies SET bev_free=bev_free-".$bd['bev_use'].",bev_work=bev_work+".$bd['bev_use'].",bev_max=bev_max+".$bd['bev_pro']." WHERE id=".$data['colonies_id']." LIMIT 1");
		// if ($data['buildings_id'] == 46) $db->query("UPDATE stu_colonies_fielddata SET aktiv=1 WHERE colonies_id=".$data['colonies_id']." AND buildings_id=47 LIMIT 1");
		// if (($data['buildings_id'] == 401) || ($data['buildings_id'] == 411) || ($data['buildings_id'] == 421)) $db->query("UPDATE stu_colonies SET beamblock=1 WHERE id=".$data['colonies_id']." LIMIT 1");
		$aktiv = 1;
		$bf -= $bd['bev_use'];
	}
	// elseif ($data['buildings_id'] == 46) $db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE colonies_id=".$data['colonies_id']." AND buildings_id=47 LIMIT 1");
	$txt .= $bd['name']." auf Feld ".$data['field_id']."<br>";
	$db->query("UPDATE stu_colonies SET max_storage=max_storage+".$bd['lager'].",max_eps=max_eps+".$bd['eps'].",max_schilde=max_schilde+".$bd['schilde']." WHERE id=".$data['colonies_id']." LIMIT 1");
	$db->query("UPDATE stu_colonies_fielddata SET aktiv=".$aktiv." WHERE field_id=".$data['field_id']." AND colonies_id=".$data['colonies_id']." LIMIT 1");
	$db->query("DELETE FROM stu_colonies_actions WHERE colonies_id=".$data['colonies_id']." AND var='db' AND value=".$data['field_id']." LIMIT 1");
}
if (is_string($txt)) $comm->sendpm(1,$luser,"<b>Auf der Kolonie ".$cd['name']." wurden Gebäude fertiggestellt</b><br>".$txt,4);
unset($txt);
unset($lc);

$log->enterLog("finishp","builds done");

// Stationsmodule fertigstellen
$result = $db->query("SELECT a.stations_id,a.field_id,a.component_id FROM stu_stations_fielddata as a WHERE a.aktiv>1 AND a.aktiv<".$time." ORDER BY a.stations_id");
while($data=mysql_fetch_assoc($result))
{
	if ($data['stations_id'] != $lc)
	{
		if ($ls) $comm->sendpm(1,$cd['user_id'],"<b>Auf der Station ".$cd['name']." wurden Module installiert</b><br>".$txt,5);
		unset($txt);
		$lc = $data['stations_id'];
		$cd = $db->query("SELECT user_id,name,bev_free FROM stu_stations WHERE id=".$lc." LIMIT 1",4);
		$bf = $cd['bev_free'];
		$luser = $cd['user_id'];
	}
	if ($data['component_id'] == 0)
	{
		$db->query("UPDATE stu_stations_fielddata SET aktiv=0 WHERE stations_id=".$data['stations_id']." AND field_id=".$data['field_id']." LIMIT 1");
		continue;
	}
	$bd = $db->query("SELECT * FROM stu_station_components WHERE component_id=".$data['component_id']." LIMIT 1",4);
	$aktiv = 0;
	$stop = 0;
	if ($bd['is_activateable'] == 1 && $bf >= $bd['bev_use'] && $stop == 0)
	{
		$db->query("UPDATE stu_stations SET bev_free=bev_free-".$bd['bev_use'].",bev_work=bev_work+".$bd['bev_use'].",bev_max=bev_max+".$bd['bev_pro'].",max_schilde=max_schilde+".$bd['schilde']." WHERE id=".$data['stations_id']." LIMIT 1");
		$aktiv = 1;
		$bf -= $bd['bev_use'];
	}
	$txt .= $bd['name']." auf Feld ".$data['field_id']."<br>";
	$db->query("UPDATE stu_stations SET max_storage=max_storage+".$bd['lager'].",max_eps=max_eps+".$bd['eps'].",wkload_max=wkload_max+".$bd[wk_proc]." WHERE id=".$data['stations_id']." LIMIT 1");
	$db->query("UPDATE stu_stations_fielddata SET aktiv=".$aktiv." WHERE field_id=".$data['field_id']." AND stations_id=".$data['stations_id']." LIMIT 1");
}
if (is_string($txt)) $comm->sendpm(1,$cd['user_id'],"<b>Auf der Station ".$cd['name']." wurden Module installiert</b><br>".$txt,5);
unset($txt);

// Schiffe fertigstellen
$result = $db->query("SELECT a.colonies_id,a.ships_id,a.user_id,a.rumps_id,a.plans_id,a.buildtime,a.huelle,a.schilde,a.warpable,a.cloakable,a.eps,a.batt,a.lss,a.kss,a.phaser,a.points,b.name,b.systems_id,b.sx,b.sy,c.cx,c.cy FROM stu_ships_buildprogress as a LEFT JOIN stu_colonies as b ON a.colonies_id=b.id LEFT JOIN stu_systems as c ON b.systems_id=c.systems_id WHERE a.buildtime<".$time);
while($data=mysql_fetch_assoc($result))
{
	echo "<br>";
	print_r($data);
	
	echo "<br><br>";
	$shipdata = getShipValuesForBuildplan($data['plans_id']);
	print_r($shipdata);
	$comm->sendpm(1,$data['user_id'],"In der Werft im Orbit der Kolonie ".$data['name']." wurde ein Schiff fertiggestellt",4);
	$rump = $db->query("SELECT min_crew,max_crew FROM stu_rumps WHERE rumps_id=".$data['rumps_id']." LIMIT 1",4);
	
	$query = "INSERT INTO `stu_ships` (`user_id`, `rumps_id`, `plans_id`, `fleets_id`, `systems_id`, `cx`, `cy`, `sx`, `sy`, `direction`, `name`, `alvl`, `warp`, `warpcore`, `max_warpcore`, `warpable`, `warpfields`, `max_warpfields`, `cloak`, `cloakable`, `cloakstrength`, `eps`, `max_eps`, `reaktor`, `batt`, `max_batt`, `huelle`, `max_huelle`, `schilde`, `max_schilde`, `schilde_status`, `lss_range`, `kss_range`, `traktor`, `traktormode`, `dock`, `crew`, `max_crew`, `min_crew`, `nbs`, `lss`, `trumps_id`, `replikator`, `phaser`, `cfield`, `torp_type`, `wea_phaser`, `wea_torp`, `shuttle_type`, `is_hp`, `is_rkn`, `points`, `lastmaintainance`, `still`, `maintain`, `batt_wait`, `hud`, `assigned`, `slots`, `storage`) VALUES
		(".$data[user_id].", ".$shipdata['rumps_id'].", ".$data[plans_id].", 0, ".$data[systems_id].", ".$data[cx].", ".$data[cy].", ".$data[sx].", ".$data[sy].", '3', 'Schiff', '1', '0', 0, ".$shipdata[warpcore].", '1', 0, ".$shipdata[warpfields].", 0, ".($shipdata['cloak'] > 0 ? "'1'" : "NULL").", ".$shipdata['cloak'].", 0, ".$shipdata[eps].", ".$shipdata[reaktor].", 0, 0, ".$shipdata[huelle].", ".$shipdata[huelle].", ".$shipdata[schilde].", ".$shipdata[schilde].", 0, ".$shipdata[lss_range].", ".$shipdata[kss_range].", 0, '', 0, 0, '".$rump['max_crew']."','".$rump['min_crew']."', '1', '1', 0, '', 0, '".$db->query("SELECT type FROM stu_sys_map WHERE systems_id=".$data['systems_id']." AND sx=".$data['sx']." AND sy=".$data['sy']." LIMIT 1",1)."', 0, '0', '0', 0, '0', 0, '0', 0, 0, 0, 0, '1', 0, 0, ".$shipdata['storage'].");";						

		
	print_r($query);
	$res = $res = $db->query($query,6);
	$db->query("INSERT INTO stu_ships_logdata (ships_id,user_id,name,buildtime) VALUES ('".$res."','".$data['user_id']."','Schiff','".$time."')");
	$db->query("DELETE FROM stu_ships_buildprogress WHERE buildtime=".$data['buildtime']." AND plans_id=".$data['plans_id']." AND rumps_id=".$data['rumps_id']." AND user_id=".$data['user_id']." LIMIT 1");
}
// Perimeteralarm SYS

$result = $db->query("SELECT a.ships_id,a.user_id,a.rumps_id,UNIX_TIMESTAMP(a.date) as date_tsp,a.sx,a.sy,b.user_id as ruser_id,b.name,e.user FROM stu_sectorflights as a LEFT JOIN stu_views_spx as b ON
b.sxmin<=a.sx AND b.sxmax>=a.sx AND b.symin<=a.sy AND b.symax>=a.sy AND b.systems_id=a.systems_id LEFT JOIN
stu_contactlist as c ON c.user_id=b.user_id AND c.recipient=a.user_id LEFT JOIN stu_ally_relationship as d ON
(d.allys_id1=a.allys_id AND d.allys_id2=b.allys_id) OR (d.allys_id2=a.allys_id AND d.allys_id1=b.allys_id) LEFT JOIN stu_user as e ON e.id=a.user_id WHERE a.notified='0' AND a.systems_id>0 AND a.user_id!=b.user_id AND (c.mode='3' OR d.type='1') AND a.cloak='0' ORDER BY b.user_id,a.date DESC");
while($data=mysql_fetch_assoc($result))
{
	if ($data['ruser_id'] != $luid)
	{
		if ($luid != 0)
		{
			$txt .= "</table>";
			$comm->sendpm(1,$luid,$txt,3);
		}
		$txt = "<table class=tcal><th colspan=4>".$data['name']." löst Perimeteralarm aus</th><tr><td colspan=4>Die Sensorenphalanx hat innerhalb des Systems feindliche Schiffe in Reichweite geortet</td></tr>
		<tr><td></td><td>Siedler</td><td>Letzte Koordinaten</td><td>Letzter Kontakt</td></tr>";
		$luid = $data['ruser_id'];
	}
	if ($rsh[$data['ruser_id']][$data['ships_id']] == 1) continue;
	$txt .= "<tr><td><img src=gfx/ships/".$data['rumps_id'].".gif></td><td>".$data['user']."</td><td>".$data['sx']."|".$data['sy']."</td><td>".date("d.m H:i",$data['date_tsp'])."</td></tr>";
	$rsh[$data['ruser_id']][$data['ships_id']] = 1;
}
if (is_string($txt))
{
	$txt .= "</table>";
	$comm->sendpm(1,$luid,$txt,3);
}
$luid = 0;
unset($txt);
// Perimeteralarm NON-SYS
$result = $db->query("SELECT a.ships_id,a.user_id,a.rumps_id,UNIX_TIMESTAMP(a.date) as date_tsp,a.cx,a.cy,b.user_id as ruser_id,b.name,e.user FROM stu_sectorflights as a LEFT JOIN stu_views_spx as b ON
b.cxmin<=a.cx AND b.cxmax>=a.cx AND b.cymin<=a.cy AND b.cymax>=a.cy LEFT JOIN
stu_contactlist as c ON c.user_id=b.user_id AND c.recipient=a.user_id LEFT JOIN stu_ally_relationship as d ON
(d.allys_id1=a.allys_id AND d.allys_id2=b.allys_id) OR (d.allys_id2=a.allys_id AND d.allys_id1=b.allys_id) LEFT JOIN stu_user as e ON e.id=a.user_id WHERE a.notified='0' AND a.systems_id=0 AND a.user_id!=b.user_id AND (c.mode='3' OR d.type='1') AND a.cloak='0' ORDER BY b.user_id,a.date DESC");
while($data=mysql_fetch_assoc($result))
{
	if ($data['ruser_id'] != $luid)
	{
		if ($luid != 0)
		{
			$txt .= "</table>";
			$comm->sendpm(1,$luid,$txt,3);
		}
		$txt = "<table class=tcal><th colspan=4>".$data['name']." löst Perimeteralarm aus</th><tr><td colspan=4>Die Sensorenphalanx hat außerhalb des Systems feindliche Schiffe in Reichweite geortet</td></tr>
		<tr><td></td><td>Siedler</td><td>Letzte Koordinaten</td><td>Letzter Kontakt</td></tr>";
		$luid = $data['ruser_id'];
	}
	if ($rsh[$data['ruser_id']][$data['ships_id']] == 1) continue;
	$txt .= "<tr><td><img src=gfx/ships/".$data['rumps_id'].".gif></td><td>".$data['user']."</td><td>".$data['cx']."|".$data['cy']."</td><td>".date("d.m H:i",$data['date_tsp'])."</td></tr>";
	$rsh[$data['ruser_id']][$data['ships_id']] = 1;
}
if (is_string($txt))
{
	$txt .= "</table>";
	$comm->sendpm(1,$luid,$txt,3);
}
$db->query("UPDATE stu_sectorflights SET notified='1'");

// Kartographierung beenden
$result = $db->query("SELECT id,name,user_id,systems_id FROM stu_ships WHERE still>0 AND still<".$time);
while($data=mysql_fetch_assoc($result))
{
	// if ($db->query("SELECT systems_id FROM stu_systems_user WHERE systems_id=".$data['systems_id']." AND user_id=".$data['user_id']." AND infotype = 'map' LIMIT 1",1) > 0) continue;
	$comm->sendpm(1,$data['user_id'],"Die ".$data['name']." hat die Kartographierung des Systems abgeschlossen",3);
	$db->query("REPLACE INTO stu_systems_user (systems_id,user_id,infotype) VALUES ('".$data['systems_id']."','".$data['user_id']."','map')");
}
$db->query("UPDATE stu_ships SET still=0 WHERE still>0 AND still<".$time);

// Wartung beenden
$result = $db->query("SELECT id,name,user_id,systems_id FROM stu_ships WHERE maintain>0 AND maintain<".$time);
while($data=mysql_fetch_assoc($result))
{
	$comm->sendpm(1,$data['user_id'],"Die Wartung der ".stripslashes($data['name'])." wurde abgeschlossen",3);
}
$db->query("DELETE FROM stu_colonies_maintainance WHERE maintaintime<".$time);
$db->query("UPDATE stu_ships SET maintain=0,lastmaintainance=".$time." WHERE maintain>0 AND maintain<".$time);
$db->query("DELETE FROM stu_ships_subsystems WHERE date<".$time);
$db->query("UPDATE stu_ships SET batt_wait=0 WHERE batt_wait<".$time);

unset($txt);
unset($lc);
// Terraforming beenden
$result = $db->query("SELECT a.colonies_id,a.field_id,a.terraforming_id,b.user_id,b.name,b.colonies_classes_id,c.name as tfname,c.z_feld FROM stu_colonies_terraforming as a LEFT JOIN stu_colonies as b ON b.id=a.colonies_id LEFT JOIN stu_terraforming as c ON c.terraforming_id=a.terraforming_id WHERE a.terraformtime<".$time." AND a.terraforming_id!=99");
while($data=mysql_fetch_assoc($result))
{
	if ($data['colonies_id'] != $lc)
	{
		if ($lc) $comm->sendpm(1,$luserid,"<b>Auf der Kolonie ".$lcol." wurden Terraforming-Maßnahmen abgeschlossen</b><br>".$txt,4);
		unset($txt);
		$lc = $data['colonies_id'];
		$luserid = $data['user_id'];
	}
	$lcol = $data['name'];
	$txt .= $data['tfname']." auf Feld ".$data['field_id']."<br>";
	if ($data['terraforming_id'] == 26 && $data['colonies_classes_id'] == 10) $data['z_feld'] = 201;
	$db->query("UPDATE stu_colonies_fielddata SET type=".$data['z_feld']." WHERe colonies_id=".$data['colonies_id']." AND field_id=".$data['field_id']." LIMIT 1");
	$db->query("DELETE FROM stu_colonies_terraforming WHERE colonies_id=".$data['colonies_id']." AND field_id=".$data['field_id']." LIMIT 1");
	if ($data['terraforming_id'] >= 21 && $data['terraforming_id'] <= 25)
	{
		$ug = $db->query("SELECT field_id FROM stu_colonies_terraforming WHERE colonies_id=".$data['colonies_id']." AND terraforming_id=99 LIMIT 1",1);
		$db->query("UPDATE stu_colonies_fielddata SET type=".($data['colonies_classes_id'] == 9 ? 53 : 72)." WHERE colonies_id=".$data['colonies_id']." AND field_id=".$ug." LIMIT 1");
		$db->query("DELETE FROM stu_colonies_terraforming WHERE colonies_id=".$data['colonies_id']." AND terraforming_id=99 LIMIT 1");
	}
}
if (is_string($txt)) $comm->sendpm(1,$luserid,"<b>Auf der Kolonie ".$lcol." wurden Terraforming-Maßnahmen abgeschlossen</b><br>".$txt,4);

$result = $db->query("SELECT a.colonies_id,a.field_id,a.terraforming_id,b.user_id,b.name,b.colonies_classes_id,c.name as tfname,c.z_feld FROM stu_colonies_terraforming as a LEFT JOIN stu_colonies as b ON b.id=a.colonies_id LEFT JOIN stu_terraforming as c ON c.terraforming_id=a.terraforming_id WHERE a.terraformtime<".$time." AND a.terraforming_id=99");
while($data=mysql_fetch_assoc($result))
{
		$db->query("UPDATE stu_colonies_fielddata SET type=".($data['colonies_classes_id'] == 9 ? 53 : 72)." WHERE colonies_id=".$data['colonies_id']." AND field_id=".$data[field_id]." LIMIT 1");
		$db->query("DELETE FROM stu_colonies_terraforming WHERE colonies_id=".$data['colonies_id']." AND terraforming_id=99 LIMIT 1");
}

// Auktionen beenden
$result = $db->query("SELECT a.trade_id,a.user_id,a.date,a.give_good,a.give_count,a.want_good,a.want_count,a.want_user_id,a.bids,b.user FROM stu_trade_ferg as a LEFT JOIN stu_user as b ON b.id=a.want_user_id WHERE UNIX_TIMESTAMP(a.date)<".($time-259200));
while($data=mysql_fetch_assoc($result))
{
	$mc = $db->query("SELECT MAX(maxcount) FROM stu_trade_ferg_history WHERE trade_id=".$data['trade_id']." LIMIT 1",1);
	$db->query("START TRANSACTION");
	$db->query("DELETE FROM stu_trade_ferg WHERE trade_id=".$data['trade_id']." LIMIT 1");
	// Auktionen ohne Gebote
	if ($data['want_user_id'] == 0)
	{
		$res = $db->query("UPDATE stu_trade_goods SET count=count+".$data['give_count']." WHERE goods_id=".$data['give_good']." AND offer_id=0 AND user_id=".$data['user_id'],6);
		if ($res == 0) $db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('".$data['user_id']."','".$data['give_good']."','".$data['give_count']."',NOW())");
		$db->query("COMMIT");
		$comm->sendpm(1,$data['user_id'],"Die Auktion ".$data['trade_id']." ist soeben ausgelaufen. Leider gab es keine Interessenten weshalb die Waren ins Warenkonto zurückgebucht wurden.",2);
		continue;
	}
	$res = $db->query("UPDATE stu_trade_goods SET count=count+".($data['want_count']-round(($data['want_count']/100)*10))." WHERE goods_id=".$data['want_good']." AND offer_id=0 AND user_id=".$data['user_id'],6);
	if ($res == 0) $db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('".$data['user_id']."','".$data['want_good']."','".($data['want_count']-round(($data['want_count']/100)*10))."',NOW())");
	if (round(($data['want_count']/100)*10) > 0)
	{
		$res = $db->query("UPDATE stu_trade_goods SET count=count+".(round(($data['want_count']/100)*10))." WHERE goods_id=".$data['want_good']." AND offer_id=0 AND user_id=14",6);
		if ($res == 0) $db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('14','".$data['want_good']."','".(round(($data['want_count']/100)*10))."',NOW())");
	}
	if ($mc > $data['want_count'])
	{
		$res = $db->query("UPDATE stu_trade_goods SET count=count+".($mc-$data['want_count'])." WHERE goods_id=".$data['want_good']." AND offer_id=0 AND user_id=".$data['want_user_id'],6);
		if ($res == 0) $db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('".$data['want_user_id']."','".$data['want_good']."','".($mc-$data['want_count'])."',NOW())");
	}
	$res = $db->query("UPDATE stu_trade_goods SET count=count+".$data['give_count']." WHERE goods_id=".$data['give_good']." AND offer_id=0 AND user_id=".$data['want_user_id'],6);
	if ($res == 0) $db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('".$data['want_user_id']."','".$data['give_good']."','".$data['give_count']."',NOW())");
	$db->query("DELETE FROM stu_trade_ferg_history WHERE trade_id=".$data['trade_id']);
	$comm->sendpm(1,$data['user_id'],"Die Auktion ".$data['trade_id']." ist soeben ausgelaufen. ".$data['user']." hat den Zuschlag bekommen und zahlte dafür ".$data['want_count']." (abzüglich einer Gebühr von ".(round(($data['want_count']/100)*10)).") ".$db->query("SELECT name FROM stu_goods WHERE goods_id=".$data['want_good']." LIMIT 1",1).".",2);
	$comm->sendpm(1,$data['want_user_id'],"Die Auktion ".$data['trade_id']." ist soeben ausgelaufen. Herzlichen Glückwunsch! Du hast den Zuschlag für ".$data['give_count']." ".$db->query("SELECT name FROM stu_goods WHERE goods_id=".$data['give_good']." LIMIT 1",1)." bekommen und zahlst ".$data['want_count']." ".$db->query("SELECT name FROM stu_goods WHERE goods_id=".$data['want_good']." LIMIT 1",1).". Die Waren befinden sich nun in Deinem Warenkonto.",2);
	$db->query("INSERT INTO stu_trade_ferg_last (trade_id,user_id,give_good,give_count,want_user_id,want_good,want_count,date,bids) VALUES ('".$data['trade_id']."','".$data['user_id']."','".$data['give_good']."','".$data['give_count']."','".$data['want_user_id']."','".$data['want_good']."','".$data['want_count']."','".$data['date']."','".$data['bids']."')");
	$db->query("COMMIT");
}
$db->query("DELETE FROM stu_trade_ferg_last WHERE UNIX_TIMESTAMP(date)<".($time-262800));
//Tag-Nacht Wechsel
$db->query("UPDATE stu_colonies SET dn_mode='2',dn_change=".$time."+rotation WHERE user_id!=1 AND dn_mode='1' AND dn_change<".$time);
$db->query("UPDATE stu_colonies SET dn_mode='1',dn_change=".$time."+rotation WHERE user_id!=1 AND dn_mode='2' AND dn_change<".$time);
// Ferengi-Bar leeren
$db->query("DELETE FROM stu_ferg_dabo WHERE bid=0 AND UNIX_TIMESTAMP(date)<".($time-180));
// Dabo!

if ($time > $db->query("SELECT value FROM stu_game_vars WHERE var='dabo_lastround'",1)+550)
{
	// Runde beenden
	$rn = rand(1,28);
	$result = $db->query("SELECT user_id FROM stu_ferg_dabo WHERE bid=".$rn);
	$kc = mysql_num_rows($result);
	$oj = $db->query("SELECT value FROM stu_game_vars WHERE var='dabo_jackpot' LIMIT 1",1);
	if ($kc > 0)
	{
		$jp = floor($oj/$kc);
		if ($jp < 4) $jp = 4;
		while($data=mysql_fetch_assoc($result))
		{
			$comm->sendpm(1,$data['user_id'],"<b>Dabo!</b><br>Du hast soeben den Dabo-Jackpot geknackt. Dein Gewinn beträgt ".$jp." Dilithium (".$kc." Gewinner)",1);
			$res = $db->query("UPDATE stu_trade_goods SET count=count+".$jp." WHERE offer_id=0 AND goods_id=8 AND user_id=".$data['user_id']." LIMIT 1",6);
			if ($res == 0) $db->query("INSERT INTO stu_trade_goods (goods_id,user_id,count) VALUES ('8','".$data['user_id']."','".$jp."')");
		}
		$nj = 10;
	}
	else $nj = $oj;
	$db->query("UPDATE stu_game_vars SET value='".$nj."' WHERE var='dabo_jackpot'");
	$db->query("UPDATE stu_game_vars SET value='".$kc."' WHERE var='dabo_lastuser'");
	$db->query("UPDATE stu_game_vars SET value='".$rn."' WHERE var='dabo_lastresult'");
	$db->query("UPDATE stu_game_vars SET value='".$time."' WHERE var='dabo_lastround'");
	$db->query("UPDATE stu_ferg_dabo SET bid=0");
}
// Schilde
$db->query("UPDATE stu_ships SET schilde_status=0 WHERE schilde_status>1 AND schilde_status<".$time);
// Markierte Schiffe
$db->query("DELETE FROM stu_ships_decloaked WHERE UNIX_TIMESTAMP(date)>0 AND UNIX_TIMESTAMP(date)<".($time-300));
// Umode aktivieren
$db->query("UPDATE stu_user SET vac_active='1',vac_blocktime=0 WHERE vac_blocktime<=".$time." AND vac_blocktime>0");






// Kolonieangriff

$result = $db->query("SELECT a.*,b.user_id,c.user_id as owner_id FROM stu_colonies_actions as a left outer join stu_colonies as b on a.colonies_id = b.id LEFT OUTER JOIN stu_fleets as c on a.value = c.fleets_id WHERE a.var != 'db' AND a.value2<".$time."");
while($data=mysql_fetch_assoc($result))
{
	if ($data['attackmode'] >= 1) $bla = $fleet->attackcolony($data['value'],$data['colonies_id'],$data['attackmode']);
	if ($bla[event]) {
		$comm->sendpm(1,$data['owner_id'],$bla[msg],3);
		$comm->sendpm($data['owner_id'],$data['user_id'],$bla[msg],4);
	}
	$db->query("UPDATE stu_colonies_actions SET value2='".($time+900)."' WHERE value='".$data['value']."' AND var != 'db'");

}




// Stationsbau
$result = $db->query("SELECT * FROM stu_stations_buildprogress WHERE buildtime<".$time);
while($data=mysql_fetch_assoc($result))
{
	$stations->finishbuilding($data[stations_id]);
}









$log->enterLog("finishp","all done");

















?>
