<?php
include_once("/var/www/virtual/stuniverse.de/htdocs/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;


include_once($global_path."/class/log.class.php");
$log = new log;

$state = $db->query("SELECT value FROM stu_game_vars WHERE var='state'",1);
if ($state == 3) {
	die();
}



$log->deleteLogType("tick_col".$_SERVER['argv'][1]);

$log->enterLog("tick_col".$_SERVER['argv'][1],"start");


// Eine Liste herstellbarer Güter erstellen
$result = $db->query("SELECT goods_id,name FROM stu_goods WHERE goods_id<100 LIMIT 100");
while($data=mysql_fetch_assoc($result)) $gl[$data['goods_id']] = $data['name'];

// Platz für tolle Funktionen!
function getgravenergy(&$grav)
{
	return round ( 9.2 * (log(1.2 + $grav) / log(2.4)) - ($grav*$grav)/12);
}
function grantResearchPoints($user,$type,$count) {
	global $db;
	$activity = $db->query("SELECT a.*,b.effecttype FROM stu_research_active as a LEFT JOIn stu_research as b ON a.research_id = b.research_id WHERE a.user_id = ".$user." AND b.effecttype = '".$type."' LIMIT 1;",4);
	
	
	if (!$activity) return;

	// LATER: CHECK TYPE HERE
	$activity[progress] += $count;
	if ($activity[progress] >= $activity[total]) {

		$resname = $db->query("SELECT name FROM stu_research WHERE research_id = ".$activity[research_id]."",1);	
		$resrump = $db->query("SELECT rumps_id FROM stu_research WHERE research_id = ".$activity[research_id]."",1);	
		if ($activity[removing] > 0) {
			$msg = "Forschung wurde entfernt: ".$resname;
			$db->query("DELETE FROM stu_researched WHERE research_id = ".$activity[research_id]." AND user_id = '".$user."' LIMIT 1;");
		} else {
			$msg = "Forschung wurde fertiggestellt: ".$resname;
			$db->query("INSERT INTO stu_researched (`research_id` ,`user_id`) VALUES ('".$activity[research_id]."', '".$user."');");
			if ($resrump > 0) $db->query("INSERT IGNORE INTO stu_rumps_user (`rumps_id` ,`user_id`) VALUES ('".$resrump."', '".$user."');");
		}
		$db->query("DELETE FROM stu_research_active WHERE user_id = ".$user.";");
		$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$user."','".addslashes($msg)."',NOW(),'4')");				
	} else {
		$db->query("UPDATE stu_research_active SET progress = ".$activity[progress]." WHERE user_id = ".$user.";");
	}
}
function deactivate(&$good)
{
	global $db,$data,$smsg,$fe,$gd,$bev,$gl,$red,$grav,$cachedbg,$ofc,$fac;
	if ($good == -1) $rd = $db->query("SELECT a.field_id,b.buildings_id,b.name,b.bev_use,b.bev_pro,b.eps_proc,b.research_t,b.research_k,b.research_v,b.research_d FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$data[id]." AND a.aktiv=1 AND b.eps_proc<0 ORDER BY RAND(),b.bev_pro ASC LIMIT 1",4);
	else $rd = $db->query("SELECT a.field_id,b.buildings_id,b.name,b.bev_use,b.bev_pro,b.eps_proc,b.research_t,b.research_k,b.research_v,b.research_d FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) LEFT JOIN stu_buildings_goods as c USING(buildings_id) WHERE a.colonies_id=".$data[id]." AND a.aktiv=1 AND c.goods_id=".$good." AND c.count<1 ORDER BY RAND(),b.bev_pro ASC LIMIT 1",4);
	if ($rd == 0)
	{
		if ($good == 1)
		{
			global $nvb;
			$nvb = abs($gd[1]);
			$gd[1] = 0;
		}
		return;
	}
	
	$bonus = $db->query("SELECT * FROM stu_colonies_bonus WHERE buildings_id=".$rd['buildings_id']." AND colonies_classes_id = ".$data['colonies_classes_id'],4);
	
	if ($bonus['goods_id'] == 0 && $bonus['count'] > 0) $rd['eps_proc'] += $bonus['count'];
	
	if ($cachedbg[$rd['buildings_id']])
	{
		foreach ($cachedbg[$rd['buildings_id']] as $key => $value)
		{
			if ($value < 0) $gd[$key] += abs($value);
			else $gd[$key] -= $value;
		}
	}
	else
	{
		$result = $db->query("SELECT goods_id,count FROM stu_buildings_goods WHERE buildings_id=".$rd['buildings_id']);
		while($gr=mysql_fetch_assoc($result))
		{
			if ($bonus['goods_id'] == $gr['goods_id'] && $bonus['count'] > 0) $gr['count'] += $bonus['count'];
			
			if ($gr['count'] < 0) $gd[$gr['goods_id']] += abs($gr['count']);
			else $gd[$gr['goods_id']] -= $gr['count'];
			$cachedbg[$rd['buildings_id']][$gr['goods_id']] = $gr['count'];
		}
	}
	if ($good == -1) $smsg .= $rd['name']." auf Feld ".$rd['field_id']." deaktiviert (Energiemangel)<br>";
	else $smsg .= $rd['name']." auf Feld ".$rd[field_id]." deaktiviert (Mangel an ".$gl[$good].")<br>";
	$rd['eps_proc'] < 0 ? $fe+=abs($rd['eps_proc']) : $fe-=$rd['eps_proc'];
	$bev['u'] -= $rd['bev_use'];
	$bev['f'] += $rd['bev_use'];
	$bev['w'] -= $rd['bev_pro'];
	$red['rt'] -= $rd['research_t'];
	$red['rk'] -= $rd['research_k'];
	$red['rv'] -= $rd['research_v'];
	$red['rd'] -= $rd['research_d'];
	$db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE field_id=".$rd['field_id']." AND colonies_id=".$data['id']." LIMIT 1");
	if ($rd['buildings_id'] == 24)
	{
		
		// Berücksichtigt Boni nicht! -> Zur Zeit keine Boni für Orbitalgebäude vergeben!
		
		$et = $db->query("SELECT SUM(bev_use) as bu, SUM(bev_pro) as bp, SUM(b.eps_proc) as ep FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.field_id<".($data['is_moon'] == 1 ? 15 : 21)." AND a.colonies_id=".$data['id'],4);
		$fd = $db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) WHERE b.colonies_id=".$data['id']." AND b.field_id<".($data['is_moon'] == 1 ? 15 : 21)." AND b.aktiv=1 GROUP BY a.goods_id");
		while($gdt=mysql_fetch_assoc($fd)) $gd[$gdt['goods_id']] -= $gdt['gc'];
		$redt = $db->query("SELECT SUM(a.research_t) as rt,SUM(a.research_k) as rk,SUM(a.research_v) as rv,SUM(a.research_d) as rd  FROM stu_buildings as a LEFT JOIN stu_colonies_fielddata as b ON a.buildings_id=b.buildings_id WHERE b.aktiv=1 AND b.field_id<".($data['is_moon'] == 1 ? 15 : 21)." AND b.colonies_id=".$data['id'],4);
		$bev['u'] -= $et['bu'];
		$bev['f'] += $et['bu'];
		$bev['w'] -= $et['bp'];
		$et['ep'] < 0 ? $fe+=abs($et['ep']) : $fe-=$et['ep'];
		$red['rt'] -= $redt['rt'];
		$red['rk'] -= $redt['rk'];
		$red['rv'] -= $redt['rv'];
		$red['rd'] -= $redt['rd'];
		$db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE colonies_id=".$data['id']." AND buildings_id< 400 AND field_id<".($data['is_moon'] == 1 ? 15 : 21)." LIMIT 20");
		$smsg .= "Aufgrund des Ausfalls des Raumbahnhofs wurden alle Gebäude im Orbit deaktiviert<br>";
	}
	// if ($rd[buildings_id] == 46)
	// {
		// $et = $db->query("SELECT SUM(bev_use) as bu, SUM(bev_pro) as bp, SUM(b.eps_proc) as ep,SUM(b.schilde) as sh FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.field_id>72 AND a.colonies_id=".$data[id],4);
		// $fd = $db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) WHERE b.colonies_id=".$data[id]." AND b.field_id>72 AND b.aktiv=1 GROUP BY a.goods_id");
		// while($gdt=mysql_fetch_assoc($fd)) $gd[$gdt['goods_id']] -= $gdt['gc'];
		// $redt = $db->query("SELECT SUM(a.research_t) as rt,SUM(a.research_k) as rk,SUM(a.research_v) as rv,SUM(a.research_d) as rd FROM stu_buildings as a LEFT JOIN stu_colonies_fielddata as b ON a.buildings_id=b.buildings_id WHERE b.aktiv=1 AND b.field_id>72 AND b.colonies_id=".$data[id],4);
		// $bev['u'] -= $et['bu'];
		// $bev['f'] += $et['bu'];
		// $bev['w'] -= $et['bp'];
		// $et['ep'] < 0 ? $fe+=abs($et['ep']) : $fe-=$et['ep'];
		// $red['rt'] -= $redt['rt'];
		// $red['rk'] -= $redt['rk'];
		// $red['rv'] -= $redt['rv'];
		// $red['rd'] -= $redt['rd'];
		// $db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE colonies_id=".$data['id']." AND field_id>72 LIMIT 30");
		// if ($et['sh']) $db->query("UPDATE stu_colonies SET max_schilde=max_schilde-".$et['sh']."".($data['schilde'] > $data['max_schilde']-$et['sh'] ? ",schilde=".($data['max_schilde']-$et['sh']) : "")." WHERE id=".$data['id']);
		// $smsg .= "Aufgrund des Ausfalls des Untergrundlifts wurden alle Gebäude im Untergrund deaktiviert<br>";
	// }
	if ($rd['buildings_id'] == 54) $gd[1] -= 2*$db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE (buildings_id=2 OR buildings_id=102) AND aktiv=1 AND colonies_id=".$data['id'],1);
	// if ($rd['buildings_id'] == 100) $db->query("UPDATE stu_colonies SET schilde_status='0' WHERE id=".$data['id']." LIMIT 1");
	// if ($rd['buildings_id'] == 101) $db->query("UPDATE stu_colonies SET max_schilde=max_schilde-700".($data['max_schilde']-700 < $data['schilde'] ? ",schilde=".($data['max_schilde']-700) : "")." WHERE id=".$data['id']." LIMIT 1");
	// if ($rd['buildings_id'] == 70 && $fac > 0) deactivate_fergam($fac);
}
function lowerstorage(&$good,&$count)
{
	global $gs,$db,$data;
	$res = $db->query("UPDATE stu_colonies_storage SET count=count-".$count." WHERE colonies_id=".$data['id']." AND goods_id=".$good." AND count>".$count." LIMIT 1",6);
	if ($res == 0) $db->query("DELETE FROM stu_colonies_storage WHERE colonies_id=".$data['id']." AND goods_id=".$good." LIMIT 1");
	$gs -= $count;
}
function upperstorage(&$good,&$count)
{
	global $gs,$db,$data;
	$res = $db->query("UPDATE stu_colonies_storage SET count=count+".$count." WHERE colonies_id=".$data['id']." AND goods_id=".$good." LIMIT 1",6);
	if ($res == 0) $db->query("INSERT INTO stu_colonies_storage (colonies_id,goods_id,count) VALUES ('".$data['id']."','".$good."','".$count."')");
	$gs += $count;
}
function tribbles(&$data,&$count)
{
	global $db,$gs;
	$good = 1013;
	$nahr = $db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=1 AND colonies_id=".$data['id']." LIMIT 1",1);
	$ng = 1;
	lowerstorage($ng,$count);
	if ($nahr < $count)
	{
		$cn = $count-$nahr;
		lowerstorage($good,$cn);
	}
	else
	{
		$nt = ceil($count*1.5)-$count;
		if ($gs+$nt > $data['max_storage']) $nt = $data['max_storage']-$gs;
		if ($nt > 0) upperstorage($good,$nt);
	}
}
function immigration(&$data)
{
	global $bev;
	$free = $bev['w']-($bev['u']+$bev['f']);
	
	switch ($data['colonies_classes_id'])
	{
		case 201:
		case 202:
		case 203:
		case 301:
		case 302:
		case 303:
			$im = (ceil($free/25)+1)*4;
			break;			
		case 204:
		case 205:
		case 304:
		case 305:
			$im = (ceil($free/25)+1)*3;
			break;		
		case 206:
		case 209:
		case 306:
		case 309:
			$im = (ceil($free/25)+1)*2;
			break;	
		case 207:
		case 210:
		case 307:
		case 310:
			$im = (ceil($free/25)+1)*1;
			break;				
		default:
			$im = 0;
			break;
	}
	return $im;
}

function deactivate_nonresearched() {
	global $data,$db,$smsg;

	$result = $db->query("SELECT a.*,f.user_id,b.research_id FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as f ON a.colonies_id = f.id left join stu_buildings as b on a.buildings_id = b.buildings_id LEFT JOIN stu_researched as c ON c.user_id = f.user_id AND c.research_id = b.research_id WHERE a.buildings_id > 0 AND a.aktiv = 1 AND a.colonies_id = ".$data[id]." AND b.research_id > 0 AND ISNULL(c.research_id);");
	while($d=mysql_fetch_assoc($result))
	{
		$bdata = $db->query("SELECT * FROM stu_buildings WHERE buildings_id = ".$d[buildings_id]." LIMIT 1;",4);
		$db->query("UPDATE stu_colonies_fielddata SET aktiv = '0' WHERE colonies_id = ".$d[colonies_id]." AND field_id = ".$d[field_id]." LIMIT 1;",4);		
		$smsg .= $bdata[name]." (Feld ".$d[field_id].") wegen fehlender Forschung deaktiviert<br>";
		$data[bev_work] -= $bdata[bev_use];
	}
}

$time1 = time();

// Check des Spielstatus
@touch("/var/www/virtual/stuniverse.de/htdocs/intern/tick/lock/lock_coltick_".$_SERVER['argv'][1]);
@unlink("/var/www/virtual/stuniverse.de/htdocs/intern/ticklog/log".$_SERVER['argv'][1]);
$fp = fopen("/var/www/virtual/stuniverse.de/htdocs/intern/ticklog/log".$_SERVER['argv'][1],"a+");
$ot = time();

// Na dann starten wir mal...her mit den Kolonien
$result = $db->query("SELECT a.id,a.name,a.colonies_classes_id,a.user_id,a.bev_free,a.bev_work,a.bev_max,a.eps,a.max_eps,a.schilde,a.max_schilde,a.max_storage,a.bevstop,a.einwanderung,a.gravitation,b.is_moon,c.lav_not FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.user_id!=1 AND ISNULL(c.vac_active) AND c.tick=".$_SERVER['argv'][1]." ORDER BY a.user_id,a.id");
while($data=mysql_fetch_assoc($result))
{
	$log->enterLog("tick_col".$_SERVER['argv'][1],$data['id']);
	
	fwrite($fp,time()-$ot." :: ".$data['id']."\n");
	// Setzen wir mal die Vars neu
	if (!$luser || $msg == "") $luser = $data['user_id'];
	$gochk = 0;
	$nvb = 0;
	$smsg = "";

	unset($gd);
	unset($sd);
	
	deactivate_nonresearched();
	
	$bev = array("u" => $data['bev_work'],"f" => $data['bev_free'],"w" => $data['bev_max']);
	// Und nun ab...sammeln wir mal die relevanten Daten
	$gs = $db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=".$data['id'],1);
	$fe = $db->query("SELECT SUM(b.eps_proc) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$data['id'],1);
	
	
	// Production
	$fd = $db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) WHERE b.colonies_id=".$data['id']." AND b.aktiv=1 GROUP BY a.goods_id");
	while($gdt=mysql_fetch_assoc($fd)) $gd[$gdt['goods_id']] = $gdt['gc'];

	
	// BonusProduction
	$fdb = $db->query("SELECT b.goods_id, SUM(b.count) as gc FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies_bonus as b ON a.buildings_id = b.buildings_id LEFT JOIN stu_colonies as c ON c.id = a.colonies_id WHERE a.colonies_id=".$data['id']." AND a.aktiv=1 AND b.colonies_classes_id = c.colonies_classes_id GROUP by b.goods_id;");
	while($gdt=mysql_fetch_assoc($fdb)) {
		if ($gdt['goods_id'] == 0) $fe += $gdt['gc'];
		else $gd[$gdt['goods_id']] += $gdt['gc'];
	}

	// Storage
	$sr = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_colonies_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=".$data['id']);
	while($sdt=mysql_fetch_assoc($sr)) $sd[$sdt['goods_id']] = array("ct" => $sdt['count'],"nm" => $sdt['name']);
	
	
	
	// Forschung
	$red = $db->query("SELECT SUM(a.research_t) as rt,SUM(a.research_k) as rk,SUM(a.research_v) as rv,SUM(a.research_d) as rd FROM stu_buildings as a LEFT JOIN stu_colonies_fielddata as b ON a.buildings_id=b.buildings_id WHERE b.aktiv=1 AND b.colonies_id=".$data['id'],4);
	
	// Wetterkontrolle
	if ($db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=54 AND aktiv=1 AND colonies_id=".$data['id'],1) != 0) $gd[1] += 2 * $db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE (buildings_id=2 OR buildings_id=102 OR buildings_id=9) AND aktiv=1 AND colonies_id=".$data['id'],1);
	
	// if ($db->query("SELECT field_id FROM stu_colonies_fielddata WHERE (buildings_id=401 OR buildings_id=411 OR buildings_id=421) AND aktiv=1 AND colonies_id=".$data['id'],1) != 0) $beamblock = 1;
	// else $beamblock = 0;
	$beamblock = 0;
	
	// Tribbles?!
	// $tr = $db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$data['id']." AND goods_id=1013 LIMIT 1",1);
	// if ($tr > 0) tribbles($data,$tr);

	// Nahrung wird ja auch verbraucht...
	$gd[1] -= ceil(($data['bev_work']+$data['bev_free'])/10);
	// Jetzt gehts los! Verarbeiten wir mal die Waren...
	while(TRUE)
	{
		$i = 1;
		$deaid = 0;
		while($i<=50)
		{
			if (!$gd[$i] || $gd[$i] > -1)
			{
				$i++;
				continue;
			}
			if ($sd[$i]['ct'] - abs($gd[$i]) < 0) { $deaid = $i; break; }
			$i++;
		}
		if ($deaid == 0 && $fe < 0 && abs($fe) > $data['eps']) $deaid = -1;
		if ($deaid == 0) break;
		deactivate($deaid);
	}
	// Wie viel erwirtschaften wir denn?
	$lrw = $db->query("SELECT SUM(b.points) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$data['id']." AND (a.aktiv=1 OR (a.aktiv=0 AND (ISNULL(b.is_activateable) OR b.is_activateable='0' OR b.is_activateable='')))",1);
	// Zu wenig Nahrung? Dann gibts Ärger
	if ($nvb > 0)
	{
		$smsg .= "Der Nahrungsmangel auf der Kolonie hat folgende Folgen:<br>";
		if ($bev['f'] > 0)
		{
			$smsg .= $bev['f']." Einwohner ausgewandert<br>";
			$bev['f'] = 0;
		}
		$nr = rand(15,100);
		// $smsg .= $nr."% der Wirtschaftsleistung sind aufgrund von Streiks nicht verfügbar<br>";
		$lrw = round(($lrw/100)*(100-$nr));
	}
	// Waren verrechnen...ab ins Lager damit

	@asort($gd);
	foreach($gd as $key => $value)
	{
		if ($value > 0)
		{
			if ($gs >= $data['max_storage'])
			{
				if ($data['lav_not'] == 1) $smsg .= "Der Lagerraum der Kolonie ist voll - Keine weiteren Waren gutgeschrieben<br>";
				break;
			}
			if ($value > $data['max_storage']-$gs) $value = $data['max_storage']-$gs;
			upperstorage($key,$value);
		}
		if ($value < 0) lowerstorage($key,abs($value));
	}
	// Wohnraummangel? Oh weia
	if ($bev['w'] < $bev['f']+$bev['u'])
	{
		$smsg .= "Der Mangel an Wohnraum auf der Kolonie hat folgende Folgen:<br>";
		if ($bev['f'] > 0)
		{
			$rand = rand(1,$bev['f']);
			$smsg .= $rand." Einwohner ausgewandert<br>";
			$bev['f'] -= $rand;
		}
		$nr = rand(15,100);
		// $smsg .= $nr."% der Wirtschaftsleistung sind aufgrund von Streiks nicht verfügbar<br>";
		$lrw = round(($lrw/100)*(100-$nr));
	}
	
	// Hat vielleicht jemand Lust auf die Kolonie zu kommen?
	if ($bev['w'] > 0 && $bev['f']+$bev['u'] < $bev['w'] && $data['einwanderung'] == 0 && ($bev['f']+$bev['u'] < $data['bevstop'] || $data['bevstop'] == 0))
	{
		$im = immigration($data);
		if ($im < 0) $im = 0;
		if ($bev['f'] + $bev['u'] + $im > $data['bevstop'] && $data['bevstop'] != 0) $im = $data['bevstop'] - ($bev['f'] + $bev['u']);
		if ($im > $bev['w']-$bev['f']-$bev['u']) $bev['f'] += $bev['w']-$bev['f']-$bev['u'];
		else $bev['f'] += $im;
	}
	// Kolonie aktualisieren *fump*
	if ($fe > $data['max_eps']-$data['eps']) $fe = $data['max_eps']-$data['eps'];
	if ($fe < 0) $fe = $data['eps']-abs($fe);
	else $fe = $data['eps']+$fe;
	
	$fe = min($fe,$data['max_eps']);
	
	$db->query("UPDATE stu_colonies SET bev_work=".$bev['u'].",bev_free=".$bev['f'].",bev_max=".$bev['w'].",eps=".$fe.",lastrw=".$lrw.",beamblock=".$beamblock." WHERE id=".$data['id']." LIMIT 1");

	$effects = $db->query("SELECT SUM(e.count) as count, e.type as type FROM stu_colonies_fielddata as a left join stu_buildings_effects as e on a.buildings_id = e.buildings_id WHERE e.count > 0 AND a.aktiv=1 AND a.colonies_id = ".$data['id']." GROUP BY e.type ORDER BY count DESC;");

	while ($effect = mysql_fetch_assoc($effects)) {
		grantResearchPoints($data['user_id'],$effect['type'],$effect['count']);
		// array_push($emenu,"<tr><td width=150><img src='".$gfx."/icons/".$effect[type].".gif' title='".geteffectname($effect[type])."'> ".geteffectname($effect[type])."</td><td width=760>".bigdarkuniversalstatusbar(0,1,"yel",0,760)."</td><td style='text-align:right'><font color=#00ff00>+".$effect[count]."</font>&nbsp;</td></tr>");
	}	
	
	// Nun kriegt der Eigentümer vielleicht noch eine PM
	if ($msg != "" || $smsg != "")
	{
		if ($luser != $data['user_id'] && $msg != "")
		{
			$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$luser."','".$msg."',NOW(),'4')");
			$luser = $data['user_id'];
			$msg = "";
		}
		if ($smsg != "") $msg .= "<b>Tickreport der Kolonie ".addslashes($data['name'])."</b><br>".$smsg;
	}
}
// Und fertig...Halt - Moment. Da könnte noch ne PM sein...
if ($msg != "") $db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$luser."','".$msg."',NOW(),'4')");

$log->enterLog("tick_col".$_SERVER['argv'][1],"done");

@unlink("/var/www/virtual/stuniverse.de/htdocs/intern/tick/lock/lock_coltick_".$_SERVER['argv'][1]);
?>
