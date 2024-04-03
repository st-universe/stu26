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

include_once("../../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;


include_once($global_path."/class/log.class.php");
$log = new log;




$result = $db->query("SELECT a.id,a.rumps_id,a.user_id,a.name,a.crew,a.min_crew,a.cx,a.cy,c.reaktor as dreaktor,c.storage FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON a.rumps_id=c.rumps_id LEFT JOIN stu_user as d ON a.user_id=d.id WHERE a.user_id=102 AND a.assigned = 1 AND ISNULL(d.vac_active)");
while($data=mysql_fetch_assoc($result))
{
	$collector = getCollector($data['id']);
	if ($collector == "none") continue;
	if ($data['crew'] >= $data['min_crew']) {
		
		if ($collector == "deut") upperstorage($data['id'],5,4);
		if ($collector == "ore") upperstorage($data['id'],11,4);
	}
}










?>
