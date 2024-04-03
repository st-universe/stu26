<?php
function leistung()
{
	global $db,$data,$deut,$wk;
	if ($data['m5'] > 0 && $data['warpcore'] > 0 && $db->query("SELECT ships_id FROM stu_ships_subsystems WHERE ships_id=".$data['id']." AND system_id=5 LIMIT 1",1) == 0)
	{
		$data['reaktor'] > $data['warpcore'] ? $en = $data['warpcore'] : $en = $data['reaktor'];
		$wk = 1;
		return $en;
	}
	$deut = $db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$data['id']." AND goods_id=5 LIMIT 1",1);
	$deut < $data['dreaktor'] ? $en = $deut : $en = $data['dreaktor'];
	return $en;
}

function tarnkosten()
{
	global $data;
	switch ($data['rumps_id']) {
		case 1101: return 2;
		case 1201: return 2;
		case 1301: return 2;
		case 1401: return 3;
		case 1501: return 3;
		case 1601: return 4;
		case 1701: return 4;
		case 1801: return 5;
		case 1102: return 2;
		case 1202: return 2;
		case 1302: return 2;
		case 1402: return 3;
		case 1502: return 3;
		case 1602: return 4;
		case 1702: return 4;
		case 1802: return 5;
		case 1103: return 2;
		case 1203: return 2;
		case 1303: return 2;
		case 1403: return 3;
		case 1503: return 3;
		case 1603: return 4;
		case 1703: return 4;
		case 1803: return 5;
		case 1104: return 2;
		case 1204: return 2;
		case 1304: return 2;
		case 1404: return 3;
		case 1504: return 3;
		case 1604: return 4;
		case 1704: return 4;
		case 1804: return 5;
		case 1105: return 2;
		case 1205: return 2;
		case 1305: return 2;
		case 1405: return 3;
		case 1505: return 3;
		case 1605: return 4;
		case 1705: return 4;
		case 1805: return 5;
		case 1106: return 2;
		case 1206: return 2;
		case 1306: return 2;
		case 1406: return 3;
		case 1506: return 3;
		case 1606: return 4;
		case 1706: return 4;
		case 1806: return 5;
		default: return 0;
	}
	return 0;
}

function verbrauch()
{
	global $db,$data,$reaktor,$nahr,$type,$sd;
	$vb = 0;
	if ($data['wea_phaser'] == 1)
	{
		$type['wea_phaser'] = 1;
		$vb++;
	}
	if ($data['wea_torp'] == 1)
	{
		$type['wea_torp'] = 1;
		$vb++;
	}
	if ($data['nbs'] == 1)
	{
		$type['nbs'] = 1;
		$vb++;
	}
	if ($data['lss'] == 1)
	{
		$type['lss'] = 1;
		$vb++;
	}
	if ($data['schilde_status'] == 1)
	{
		$type['schilde_status'] = 1;
		$vb++;
	}
	if ($data['rumps_id'] != 10)
	{
		// Nahrungsverbrauch berechnen
		$bn = ceil($data['crew']/5);
		$nahr = $db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$data['id']." AND goods_id=1 LIMIT 1",1);
		
		// Replikator nötig?
		if ($data['replikator'] == 1 || ($data['creplikator'] == 1 && $nahr < $bn))
		{
			$ar = 1;
			$vb += $bn;
			$data['replikator'] = 1;
		}
		else
		{
			if ($bn > $nahr)
			{
				$bn = $nahr;
				if ($bn*5 < $data['crew']) $db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$data[user_id]."','Aufgrund von Nahrungsmangel sind ".($data[crew]-$bn*5)." Besatzungsmitglieder von der ".addslashes($data[name])." geflohen',NOW(),'3')");
				$data['crew'] = $bn*5;
			}
			lowerstorage($data['id'],1,$bn);
		}
	}
	if ($ar == 1) $type['replikator'] = $bn;
	return $vb;
}
function tribbles(&$data,&$count)
{
	global $db;
	$nahr = $db->query("SELECT count FROM stu_ships_storage WHERE goods_id=1 AND ships_id=".$data['id'],1);
	lowerstorage($data['id'],1,$count);
	if ($nahr < $count) lowerstorage($data['id'],1013,$count-$nahr);
	else
	{
		$gs = $db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$data['id'],1);
		$nt = ceil($count*1.5)-$count;
		if ($gs+$nt > $data['storage']) $nt = $data['storage']-$gs;
		if ($nt > 0) $db->query("UPDATE stu_ships_storage SET count=count+".$nt." WHERE ships_id=".$data['id']." AND goods_id=1013 LIMIT 1");
	}
}
function abschalten($key)
{
	global $type,$data,$verbrauch,$reaktor,$sd,$db;
	$data["$key"] = 0;
	if ($key == "replikator")
	{
		$cv = ($data['eps']+$reaktor)*5;
		$verbrauch += $data['eps']+$reaktor;
		if ($cv < $data['crew'])
		{
			$nahr = $db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$data['id']." AND goods_id=1 LIMIT 1",1);
			$zv = $data['crew']-$cv;
			if ($nahr < ceil($zv/5))
			{
				// Fluuuuuuuucht!
				$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$data['user_id']."','Aufgrund von Nahrungsmangel sind ".($zv-$nahr*5)." Besatzungsmitglieder von der ".addslashes($data['name'])." geflohen',NOW(),'3')");
				$dn = $nahr;
				$cv += $nahr*5;
			}
			else
			{
				$dn = ceil($zv/5);
				$cv = $data['crew'];
			}
			lowerstorage($data['id'],1,$dn);
			$data['crew'] = $cv;
		}
		if ($data['crew'] < $data['min_crew'])
		{
			$sd = 1;
			deactivate_ship();
		}
	}
	$verbrauch -= $type[$key];
	unset($type[$key]);
}

function deactivate_ship()
{
	global $data,$db;
	if ($data['schilde_status'] > 1) $sd = $data['schilde_status'];
	else $sd = 0;
	$db->query("UPDATE stu_ships SET crew=".$data['crew'].",warp='0',cloak='0',schilde_status=".$sd.",lss=NULL,nbs=NULL,still=0,wea_phaser='0',wea_torp='0' WHERE id=".$data['id']." LIMIT 1");
	if ($data['traktormode'] == 1)
	{
		$db->query("UPDATE stu_ships SET traktor=0,traktormode=NULL WHERE traktor=".$data['id']." OR id=".$data['id']." LIMIT 2");
		$data['traktormode'] = 0;
	}
	return 1;
}

// Zieh die müllenden Schweinehunde und ihre Bojen ins System
function dropintosystem()
{
	global $data,$db;
	if ($data['traktormode'] == 2) return;
	if ($data['fleets_id'] > 0) return;
	$sys = $db->query("SELECT systems_id,sr,sr,type,name FROM stu_systems WHERE cx=".$data['cx']." AND cy=".$data['cy']." LIMIT 1",4);
	if ($sys == 0) return 0;
	if (!$data['direction']) $data['direction'] = rand(1,4);
	if ($data['direction'] == 1) { $sx = $sys['sr']; $sy = rand(round($sys['sr']/2)-round($sys['sr']/5),round($sys['sr']/2)+round($sys['sr']/5)); }
	if ($data['direction'] == 2) { $sx = 1; $sy = rand(round($sys['sr']/2)-round($sys['sr']/5),round($sys['sr']/2)+round($sys['sr']/5)); }
	if ($data['direction'] == 3) { $sx = rand(round($sys['sr']/2)-round($sys['sr']/5),round($sys['sr']/2)+round($sys['sr']/5)); $sy = $sys['sr']; }
	if ($data['direction'] == 4) { $sx = rand(round($sys['sr']/2)-round($sys['sr']/5),round($sys['sr']/2)+round($sys['sr']/5)); $sy = 1; }
	$ftype = $db->query("SELECT type FROM stu_sys_map WHERE systems_id=".$sys['systems_id']." AND sx=".$sx." AND sy=".$sy." LIMIT 1",1);
	$db->query("UPDATE stu_ships SET sx=".$sx.",sy=".$sy.",systems_id=".$sys['systems_id'].",warp='0',cfield=".$ftype." WHERE id=".$data['id']." LIMIT 1");
	$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$data['user_id']."','Die ".addslashes($data['name'])." kann die Position nicht halten und wird durch Gravitation in das nahe ".addslashes($sys['name'])."-System gezogen.',NOW(),'3')");
	return;
}

function lowerstorage($id,$good,$count)
{
	global $db;
	$result = $db->query("UPDATE stu_ships_storage SET count=count-".$count." WHERE ships_id=".$id." AND goods_id=".$good." AND count>".$count,6);
	if ($result == 0) $db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$id." AND goods_id=".$good);
}

function upperstorage($id,$good,$count)
{
	global $db;	
	$current = $db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id = ".$id."",1);
	$max = $db->query("SELECT storage FROM stu_ships WHERE id = ".$id."",1);
	
	$amount = min($count,$max-$current);
	
	if ($amount > 0) {
		$result = $db->query("UPDATE stu_ships_storage SET count=count+".$amount." WHERE ships_id=".$id." AND goods_id=".$good." LIMIT 1",6);
		if ($result == 0) $db->query("INSERT INTO stu_ships_storage (ships_id,goods_id,count) VALUES ('".$id."','".$good."','".$amount."')");
	}
}
function getCollector($id) {
	global $db;
	$s1 = $db->query("SELECT x.type FROM stu_ships as s LEFT JOIN stu_ships_buildplans as p ON s.plans_id = p.plans_id LEFT JOIN stu_modules_special as x ON x.modules_id = p.s1 WHERE s.id=".$id." LIMIT 1",1);
	$s2 = $db->query("SELECT x.type FROM stu_ships as s LEFT JOIN stu_ships_buildplans as p ON s.plans_id = p.plans_id LEFT JOIN stu_modules_special as x ON x.modules_id = p.s2 WHERE s.id=".$id." LIMIT 1",1);
	if ($s1 === "col_deut" || $s2 === "col_deut") return "deut";
	if ($s1 === "col_ore" || $s2 === "col_ore") return "ore";
	return "none";
}
	
	
		

	function applyModifiersForModule($data,$moduleid) {
		global $db;	

		// case "hull": return "Hüllenstärke";
		// case "shield": return "Schildstärke";
		// case "eps": return "EPS";
		// case "core": return "Warpkernladung";
		// case "reactor": return "Reaktorleistung";
		// case "hitchance": return "Trefferchance";
		// case "sensor": return "Sensorreichweite";
		// case "warpdrive": return "Max. Warpantrieb";
		// case "warpregen": return "Warpantrieb/Tick";
		// case "colonize": return "Kolonisierung";
		// case "col_ore": return "Erzabbau/Tick";
		// case "col_deut": return "Deuteriumabbau/Tick";	
		
		$res = $db->query("SELECT * FROM stu_modules_special WHERE modules_id=".$moduleid."",0);
		while($special=mysql_fetch_assoc($res)) {
			if ($special[type] == "hull") 		$data[huelle] 			+= $special[value];
			if ($special[type] == "shield") 	$data[schilde] 			+= $special[value];
			if ($special[type] == "eps") 		$data[eps] 				+= $special[value];
			if ($special[type] == "core") 		$data[warpcore] 		+= $special[value];
			if ($special[type] == "reactor") 	$data[reaktor] 			+= $special[value];
			// if ($special[type] == "hitchance") 	$data[eps] 				+= $special[value];
			if ($special[type] == "sensor") 	$data[lss_range]		+= $special[value];
			if ($special[type] == "sensor") 	$data[kss_range]	 	+= $special[value];
			if ($special[type] == "warpdrive") 	$data[warpfields] 		+= $special[value];
			if ($special[type] == "warpregen") 	$data[warpfield_regen] 	+= $special[value];
			if ($special[type] == "max_crew") 	$data[max_crew] 		+= $special[value];
			if ($special[type] == "min_crew") 	$data[min_crew] 		+= $special[value];
		}
			
		return $data;
	}

	function getShipValuesWithMods($rumpid,$modules) {
		global $db;	

		$rump = $db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$rumpid." LIMIT 1",4);

		$ship = $rump;
		foreach($modules as $mod) {
			if ($mod == 0) continue;
			$ship = applyModifiersForModule($ship,$mod);
		}
		return $ship;
	}

	function getShipValuesForBuildplan($plan) {
		global $db;

		$plan = $db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id=".$plan." LIMIT 1",4);
		if (!$plan['plans_id']) return array();

		$modules = array();
		if ($plan[m1] != 0) array_push($modules,$plan[m1]);
		if ($plan[m2] != 0) array_push($modules,$plan[m2]);
		if ($plan[m3] != 0) array_push($modules,$plan[m3]);
		if ($plan[m4] != 0) array_push($modules,$plan[m4]);
		if ($plan[m5] != 0) array_push($modules,$plan[m5]);
		if ($plan[w1] != 0) array_push($modules,$plan[w1]);
		if ($plan[w2] != 0) array_push($modules,$plan[w2]);
		if ($plan[s1] != 0) array_push($modules,$plan[s1]);
		if ($plan[s2] != 0) array_push($modules,$plan[s2]);

		return getShipValuesWithMods($plan[rumps_id],$modules);
	}

	
	
	
	
	
include_once("/var/www/st-universe.eu/inc/config.inc.php");
// include_once("../../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;


include_once($global_path."/class/log.class.php");
$log = new log;

// Check des Spielstatus
if ($db->query("SELECT value FROM stu_game_vars WHERE var='state'",1) == 3) exit;



$log->deleteLogType("tick_ship");

$log->enterLog("tick_ship","start");

	
// Alte PMs löschen
$db->query("DELETE FROM stu_pms WHERE UNIX_TIMESTAMP(date)<".(time()-5184000));

// Sektordurchflüge, die älter als 24h sind, löschen
$db->query("DELETE FROM stu_sectorflights WHERE UNIX_TIMESTAMP(date)<".(time()-86400));

// > 2 Wochen von der IPtable löschen
$db->query("DELETE FROM stu_user_iptable WHERE UNIX_TIMESTAMP(start)<".(time()-1209600));

// KN-Rating festlegen
$result = $db->query("SELECT a.id,ROUND(SUM(b.rating)/COUNT(b.kn_id)) as rat,COUNT(b.kn_id) as votes FROM stu_kn as a LEFT JOIN stu_kn_rating as b ON a.id=b.kn_id WHERE UNIX_TIMESTAMP(a.date)<".(time()-86400)." AND ISNULL(a.rating) AND a.official='1' GROUP BY a.id");
while($data=mysql_fetch_assoc($result))
{
	$db->query("UPDATE stu_kn SET rating='".$data['rat']."',votes=".$data['votes']." WHERE id=".$data['id']." LIMIT 1");
}
$db->query("DELETE FROM stu_kn_rating WHERE UNIX_TIMESTAMP(date)<".(time()-172800));

// Umods setzen
if (date("d") == "01" && date("H") == "00") $db->query("UPDATE stu_user SET vac_possible='4'");



$fleetpoints = array();

// flottenpunkte laden
$result = $db->query("SELECT id, conflict_points FROM stu_user;");
while($data=mysql_fetch_assoc($result))
{
	$fleetpoints[$data['id']] = $data['conflict_points'];
}







$collectcount = 10;

$result = $db->query("SELECT a.id,a.rumps_id,a.user_id,a.name,a.crew,a.min_crew,a.cx,a.cy,c.reaktor as dreaktor,c.storage FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON a.rumps_id=c.rumps_id LEFT JOIN stu_user as d ON a.user_id=d.id WHERE a.user_id>100 AND a.assigned = 1 AND ISNULL(d.vac_active)");
while($data=mysql_fetch_assoc($result))
{
	$collector = getCollector($data['id']);
	if ($collector == "none") continue;
	if ($data['crew'] >= $data['min_crew']) {
		
		if ($collector == "deut") upperstorage($data['id'],5,$collectcount);
		if ($collector == "ore") upperstorage($data['id'],11,$collectcount);
	}
}




	function crewDesert($reason) {
		global $data, $shipvals;
		// echo " !CREW LEAVE! ".$reason;
		
		if ($data['crew'] > 0) {
			$msg = "<br>Ein Teil der Crew hat das Schiff verlassen. Grund: ".$reason;
			$data['crew'] = max($data['crew'] - ceil($shipvals['min_crew'] * 0.25),0);
			return $msg;
		}
		$data['crew'] = 0;
		return;			
	}

	function damageHull($reason) {
		global $data, $shipvals;
		// echo " !HULL DAMAGE! ".$reason;

		if ($data['huelle'] > floor($shipvals['huelle'] * 0.25)) {
			$msg = "<br>Hülle wurde beschädigt. Grund: ".$reason;
			$data['huelle'] = max($data['huelle'] - ceil($shipvals['huelle'] * 0.1),$shipvals['huelle'] * 0.25);
			return $msg;
		}
		$data['huelle'] = floor($shipvals['huelle'] * 0.25);
		return;			
	}
	
	function crewFlee($reason) {
		global $data, $shipvals;
		// echo " !CREW FLEE! ".$reason;
		
		if ($data['crew'] > 0) {
			$msg = "<br>Versagen der Lebenserhaltung, Crew verlässt das Schiff. Grund: ".$reason;
			$data['crew'] = 0;
			return $msg;
		}
		$data['crew'] = 0;
		return;			
	}
	function failCloak($reason) {
		global $data, $shipvals;
		// echo " !SHIELDS FAIL! ".$reason;
		
		if ($data['cloak'] == 1) {
			$msg = "<br>Versagen der Tarnvorrichtung. Grund: ".$reason;
			$data['cloak'] =  time() + 900;
			return $msg;
		}
		return;		
	}	
	function failShields($reason) {
		global $data, $shipvals;
		// echo " !SHIELDS FAIL! ".$reason;
		
		if ($data['schilde'] > 0) {
			$msg = "<br>Versagen der Schilde. Grund: ".$reason;
			$data['schilde'] = 0;
			$data['schilde_status'] = 0;
			return $msg;
		}
		$data['schilde'] = 0;
		$data['schilde_status'] = 0;
		return;		
	}
	function failWarpdrive($reason) {
		global $data, $shipvals;
		// echo " !WARPDRIVE FAIL! ".$reason;		
		
		if ($data['warpfields'] > 0 || $data['warp'] > 0) {
			$msg = "<br>Versagen des Warpantriebs. Grund: ".$reason;
			$data['warp'] = 0;
			$data['warpfields'] = 0;
			return $msg;
		}
		$data['warp'] = 0;
		$data['warpfields'] = 0;
		return;
	}
	function failWarpcore($reason) {
		global $data, $shipvals;
		// echo " !WARPDRIVE FAIL! ".$reason;		
		
		if ($data['warpcore'] > 0) {
			$msg = "<br>Versagen des Warpkerns. Grund: ".$reason;
			$data['warpcore'] = 0;
			return $msg;
		}
		$data['warpcore'] = 0;
		return;
	}		
	function failLSS($reason) {
		global $data, $shipvals;
		// echo " !LSS FAIL! ".$reason;
		
		if ($data['lss'] > 0) {
			$msg = "<br>Versagen der Langstreckensensoren. Grund: ".$reason;
			$data['lss'] = 0;
			return $msg;
		}
		return;		
	}
	function failNBS($reason) {
		global $data, $shipvals;
		// echo " !NBS FAIL! ".$reason;
		
		if ($data['nbs'] > 0) {
			$msg = "<br>Versagen der Nahbereichssensoren. Grund: ".$reason;
			$data['nbs'] = 0;
			return $msg;
		}
		return;		
	}
	function failPrimary($reason) {
		global $data, $shipvals;
		// echo " !PWE FAIL! ".$reason;
		
		if ($data['wea_phaser'] > 0) {
			$msg = "<br>Versagen der Primärwaffe. Grund: ".$reason;
			$data['wea_phaser'] = 0;
			return $msg;
		}
		return;		
	}
	function failSecondary($reason) {
		global $data, $shipvals;
		// echo " !SWE FAIL! ".$reason;
		
		if ($data['wea_torp'] > 0) {
			$msg = "<br>Versagen der Sekundärwaffe. Grund: ".$reason;
			$data['wea_torp'] = 0;
			return $msg;
		}
		return;		
	}
	

	
	function deadShip() {
		global $data, $shipvals;
		failShields("");
		failWarpdrive("");
		failLSS("");
		failNBS("");
		failPrimary("");
		failSecondary("");
	}
	
	
	function processEnergyCost($type,$cost,$badThings) {
		global $data, $shipvals;
		
		// echo $type." CST:".$cost;
		
		// if ($data['warpcore'] > 0 || $data['eps'] > 0) {

		if ($cost > 0) {
		
			$availableCore = min($data['unused_reactor'],$data['warpcore']);
			$getFromCore = min($availableCore,$cost);
			$getFromEPS = ($cost - $getFromCore);
			
			// echo " AVC:".$availableCore;		
			// echo " GFC:".$getFromCore;		
			// echo " GFE:".$getFromEPS;		

			$data['unused_reactor'] 	-= $getFromCore;
			$data['warpcore']			-= $getFromCore;
			$data['eps'] 				-= $getFromEPS;
			
			if ($data['eps'] < 0) {
				$data['eps'] = 0;
				return $badThings("Energiemangel");
			}		
		} 
		// $badThings("Energiemangel");
		return "";
	}


	function processWarpdrive($type,$fields,$badThings) {
		global $data, $shipvals;

		// echo $type." FDS:".$fields;
		
		if ($data['warpcore'] > 0) {

			$availableCore = $data['warpcore']-1;
			$getFromCore = min($availableCore,min($fields,$data['max_warpfields']-$data['warpfields']));
			
			// echo " AVC:".$availableCore;		
			// echo " GFC:".$getFromCore;		

			$data['warpfields'] 		+= $getFromCore;
			$data['warpcore']			-= $getFromCore+$data['warp'];
			$data['unused_reactor'] 	-= $data['warp'];
		} else {
			return $badThings("Ausfall des Warpkerns");
		}
	}



	function sendPm($user,$text,$type) {
		global $db;
		$db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('2','".$user."','".addslashes($text)."','".$type."',NOW())");
	}
	
// Liste der Schiffe holen *fump* <- Ich tu ma noch Coords und so dazu, wegen der Bojen. BOJEN! AAARGH! <- Traktormode abfragen wäre auch praktisch gewesen
$result = $db->query("SELECT a.id,a.rumps_id,a.user_id,a.name,a.warp,a.warpfields,a.max_warpfields,a.warpcore,a.max_warpcore,a.warpable,a.cloak,a.eps,a.max_eps,a.schilde,a.schilde_status,a.dock,a.crew,a.min_crew,a.lss,a.nbs,a.replikator,a.cx,a.cy,a.direction,a.fleets_id,a.systems_id,a.traktormode,a.wea_phaser,a.wea_torp,a.plans_id,a.huelle,c.reaktor as dreaktor,c.storage,d.level,c.fleetpoints FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON a.rumps_id=c.rumps_id LEFT JOIN stu_user as d ON a.user_id=d.id WHERE a.user_id>10 AND a.assigned = 0 AND ISNULL(d.vac_active) ORDER BY RAND();");
// $result = $db->query("SELECT a.id,a.rumps_id,a.user_id,a.name,a.warp,a.warpfields,a.max_warpfields,a.warpcore,a.warpable,a.cloak,a.eps,a.max_eps,a.schilde,a.schilde_status,a.dock,a.crew,a.min_crew,a.lss,a.nbs,a.replikator,a.cx,a.cy,a.direction,a.fleets_id,a.systems_id,a.traktormode,a.wea_phaser,a.wea_torp,a.plans_id,c.reaktor as dreaktor,c.storage FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON a.rumps_id=c.rumps_id LEFT JOIN stu_user as d ON a.user_id=d.id WHERE a.user_id>100 AND a.assigned = 0 AND ISNULL(d.vac_active) AND a.user_id = 102");
while($data=mysql_fetch_assoc($result))
{

	$log->enterLog("tick_ship",$data[id]);
	$fleetpoints[$data['user_id']] -= $data['fleetpoints'];
	$shipvals = getShipValuesForBuildplan($data['plans_id']);
	
	// echo "<br>".$data['id']." ".$data['name']."<br>";
		
	$msg = "";		
		
	// Prüfen ob das Schiff zu deaktivieren ist
	if ($data['crew'] == 0 && $data['rumps_id'] != 10)
	{
		deadShip();
		// Ich hasse Bojen. Ich HASSE sie. HASS! TOD ALLEN BOJEN! AAAARGH!
		if ($data['systems_id'] == 0) dropintosystem();
		// continue;
	} else {
		if ($data['cloak'] == 1) {
			$shipvals['reaktor'] = floor($shipvals['reaktor']*0.7);
		}
		
		if ($data['user_id'] < 100) {
			$data['warpcore'] = $data['max_warpcore'];
			$data['eps'] = $data['max_eps'];
		}		
		
		$data['unused_reactor'] = $shipvals['reaktor'];

		if ($data['level'] < 5) $data['warpcore'] = $data['max_warpcore'];
		// processLifesupport();
		
		
		
		// print_r($shipdata);
		// echo "<br>";
		$msg .= processEnergyCost("Lebenserhaltung",		$shipvals['eps_drain'],										"crewFlee");
		$msg .= processWarpdrive("Warpantrieb",				$shipvals['warpfield_regen'],								"failWarpdrive");
		$msg .= processEnergyCost("Tarnvorrichtung",		$data['cloak'] == 1 ? 3 : 0,								"failCloak");
		$msg .= processEnergyCost("Schilde",				$data['schilde_status'] == 1 ? 1 : 0,						"failShields");
		$msg .= processEnergyCost("LSS",					$data['lss'] == 1 ? 1 : 0,									"failLSS");
		$msg .= processEnergyCost("NBS",					$data['nbs'] == 1 ? 1 : 0,									"failNBS");
		$msg .= processEnergyCost("Primärwaffe",			$data['wea_phaser'] == 1 ? $shipvals['w1_lvl'] : 0,			"failPrimary");
		$msg .= processEnergyCost("Sekundärwaffe",			$data['wea_torp'] == 1 ? $shipvals['w2_lvl'] : 0,			"failSecondary");
		
		$availableCore = min($data['unused_reactor'],$data['warpcore']);
		$getFromCore = min($availableCore,$data['max_eps'] - $data['eps']);
		
		$data['unused_reactor'] 	-= $getFromCore;
		$data['warpcore']			-= $getFromCore;
		$data['eps'] 				+= $getFromCore;
		
		if ($data['user_id'] < 100) {
			$data['warpcore'] = $data['max_warpcore'];
		}
		
		// echo "<br>".$data['unused_reactor']." ".$availableCore." ".$getFromCore." ".$data['eps']." ".$data['max_eps'];
		// print_r($data);
		// echo "<br>".$msg."<br>";
		
		
	}
	
	if (($data['user_id'] > 100) && ($fleetpoints[$data['user_id']] < 0)) {
		$bad = rand(1,4);
		if ($bad == 1) $msg .= crewDesert("Flottenlimit überschritten");
		if ($bad == 2) $msg .= failWarpdrive("Flottenlimit überschritten");
		if ($bad == 3) $msg .= failWarpcore("Flottenlimit überschritten");
		if ($bad == 4) $msg .= damageHull("Flottenlimit überschritten");
	}
	
	 
	if ($msg != "") sendPm($data['user_id'],"<b>Tickreport der ".$data['name']."</b> (".$data['id']."):<br>".$msg,3);
	 
	if ($data['level'] < 5) $data['warpcore'] = $data['max_warpcore'];
	
	// Variablen zurücksetzen
	$wk = 0;
	$sd = 0;
	$deut = 0;
	$j = 0;
	unset($type);
	
	$db->query("UPDATE stu_ships SET eps=".$data['eps'].",crew=".$data['crew'].",schilde=".$data['schilde'].",schilde_status=".$data['schilde_status'].",huelle=".$data['huelle'].",nbs='".$data['nbs']."',lss='".$data['lss']."',cloak='".$data['cloak']."',warpcore=".$data['warpcore'].",replikator='".$data['replikator']."',warp='".$data['warp']."',warpfields='".$data['warpfields']."',wea_phaser='".$data['wea_phaser']."',wea_torp='".$data['wea_torp']."' WHERE id=".$data['id']." LIMIT 1");
}






// NPC Schiffe
// $result = $db->query("SELECT a.id,a.eps,a.max_eps,b.reaktor FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) WHERE a.user_id<100 AND a.user_id>=10 AND a.eps<a.max_eps");
// while($data=mysql_fetch_assoc($result))
// {
	// if (!$data['reaktor']) continue;
	// if ($data['eps']+$data['reaktor'] > $data['max_eps']) $rea = $data['max_eps']-$data['eps'];
	// else $rea = $data['reaktor'];
	// $db->query("UPDATE stu_ships SET eps=eps+".$rea." WHERE id=".$data['id']." LIMIT 1");
// }


// Wracks zerfallen lassen
$db->query("UPDATE stu_ships SET huelle = huelle-2 WHERE rumps_id=8");
$db->query("DELETE FROM stu_ships WHERE rumps_id=8 AND huelle <= 0");

// Mal schaun, welche Spezialisten da inner Sonne hocken
$result = $db->query("SELECT a.type,a.name as sname,a.x_damage,b.name,b.id,b.user_id,b.rumps_id,b.fleets_id,b.systems_id,b.sx,b.sy,b.cx,b.cy,b.huelle,b.schilde,b.schilde_status FROM stu_map_ftypes as a LEFT JOIN stu_ships as b ON b.cfield=a.type WHERE a.x_damage>0 AND (b.systems_id>0 OR a.type=4) AND b.rumps_id!=8");
while($data=mysql_fetch_assoc($result))
{
	if ($data['type'] == 4 && ($data['rumps_id'] == 9510 || $data['rumps_id'] == 9610)) continue;
	$msg = $data['name']."<br>".$data['x_damage']." Schaden ausgelöst durch ".$data['sname']." in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$db->query("SELECT name FROM stu_systems WHERE systems_id=".$data['systems_id']." LIMIT 1",1)."-System)" : $data['cx']."|".$data['cy']);
	if ($data['schilde_status'] == 1)
	{
		$data['schilde'] -= $data['x_damage'];
		if ($data['schilde'] > 0)
		{
			$data['x_damage'] = 0;
			$msg .= "<br>Schilde halten - Status: ".$data['schilde'];
		}
		else
		{
			$data['schilde_status'] = 0;
			$data['x_damage'] = abs($data['schilde']);
			$data['schilde'] = 0;
			$msg .= "<br>Schilde brechen zusammen!";
		}
		
	}
	if ($data['x_damage'] > 0)
	{
		if ($data['huelle'] > $data['x_damage'])
		{
			$data['huelle'] -= $data['x_damage'];
			$msg .= "<br>Hülle bei ".$data['huelle'];
		}
		else
		{
			$msg .= "<br>Hüllenbruch! Das Schiff wurde zerstört";
			trumfield($data,"Anomalie (".$data['sname'].")");
			$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$data['user_id']."','".$msg."',NOW(),'3')");
			continue;
		}
	}
	$db->query("UPDATE stu_ships SET huelle=".$data['huelle'].",schilde=".$data['schilde'].",schilde_status=".$data['schilde_status']." WHERE id=".$data['id']);
	$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$data['user_id']."','".$msg."',NOW(),'3')");
}

function trumfield($data,$name="")
{
	global $db;
	if ($data[fleets_id] > 0 && $db->query("SELECT fleets_id FROM stu_fleets WHERE ships_id=".$data[id],1) > 0)
	{
		$sc = $db->query("SELECT id FROM stu_ships WHERE id!=".$data[id]." AND fleets_id=".$data[fleets_id]." ORDER BY RAND() LIMIT 1",1);
		if ($sc > 0) $db->query("UPDATE stu_fleets SET ships_id=".$sc." WHERE fleets_id=".$data[fleets_id]);
		else $db->query("DELETE FROM stu_fleets WHERE fleets_id=".$data[fleets_id]);
	}
	$db->query("START TRANSACTION");
	$tx = "Die ".addslashes($data[name])." wurde in Sektor ".($data[systems_id] > 0 ? $data[sx]."|".$data[sy]." (".$db->query("SELECT name FROM stu_systems WHERE systems_id=".$data['systems_id']." LIMIT 1",1)."-System)" : $data[cx]."|".$data[cy])." ".($name != "" ? "von der ".addslashes($name)." zerstört" : "beim Sektoreinflug zerstört");
	$db->query("INSERT INTO stu_history (message,date,type,ft_msg) VALUES ('".$tx."',NOW(),'1','".stripslashes(strip_tags(str_replace("'","",$tx)))."')");
	$db->query("DELETE FROM stu_ships WHERE id=".$data[id]);
	$db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$data[id]);
	$db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE traktor=".$data[id]);
	$db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=".$data[id]);
	$db->query("DELETE FROM stu_ships_decloaked WHERE ships_id=".$data[id]);
	$db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=".$data[id]);
	$db->query("UPDATE stu_ships SET dock=0 WHERE dock=".$data[id]);
	$db->query("COMMIT");
}
// Stationen reparieren
$result = $db->query("SELECT a.id FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE b.slots>0 AND a.huelle<a.max_huelle AND a.eps>0 AND a.crew>=a.min_crew");
while($data=mysql_fetch_assoc($result))
{
	$bm = $db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$data['id']." AND goods_id=2 LIMIT 1",1);
	if ($bm < 1) continue;
	$dur = $db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$data['id']." AND goods_id=3 LIMIT 1",1);
	if ($dur < 1) continue;
	if ($bm > 1) $db->query("UPDATE stu_ships_storage SET count=count-1 WHERE ships_id=".$data['id']." AND goods_id=2 LIMIT 1");
	else $db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$data['id']." AND goods_id=2 LIMIT 1");
	if ($dur > 1) $db->query("UPDATE stu_ships_storage SET count=count-1 WHERE ships_id=".$data['id']." AND goods_id=3 LIMIT 1");
	else $db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$data['id']." AND goods_id=3 LIMIT 1");
	$db->query("UPDATE stu_ships SET huelle=huelle+1,eps=eps-1 WHERE id=".$data['id']." LIMIT 1");
}

	$log->enterLog("tick_ship","done");
?>
