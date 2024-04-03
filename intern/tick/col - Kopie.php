<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$state = $db->query("SELECT value FROM stu_game_vars WHERE var='state'",1);
if ($state == 3) {
	die();
}

// Eine Liste herstellbarer Güter erstellen
$result = $db->query("SELECT goods_id,name FROM stu_goods WHERE goods_id<100 LIMIT 100");
while($data=mysql_fetch_assoc($result)) $gl[$data['goods_id']] = $data['name'];

// Platz für tolle Funktionen!
function getgravenergy(&$grav)
{
	return round ( 9.2 * (log(1.2 + $grav) / log(2.4)) - ($grav*$grav)/12);
}

function deactivate(&$good)
{
	global $db,$data,$smsg,$fe,$gd,$bev,$gl,$red,$grav,$cachedbg,$ofc,$fac;
	if ($good == -1) $rd = $db->query("SELECT a.field_id,b.buildings_id,b.name,b.bev_use,b.bev_pro,b.eps_proc,b.research_t,b.research_k,b.research_v, b.research_d FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$data[id]." AND a.aktiv=1 AND b.eps_proc<0 ORDER BY RAND(),b.bev_pro ASC LIMIT 1",4);
	else $rd = $db->query("SELECT a.field_id,b.buildings_id,b.name,b.bev_use,b.bev_pro,b.eps_proc,b.research_t,b.research_k,b.research_v, b.research_d FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) LEFT JOIN stu_buildings_goods as c USING(buildings_id) WHERE a.colonies_id=".$data[id]." AND a.aktiv=1 AND c.goods_id=".$good." AND c.count<1 ORDER BY RAND(),b.bev_pro ASC LIMIT 1",4);
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
	if ($rd['buildings_id'] == 19)
	{
		$et = $db->query("SELECT SUM(bev_use) as bu, SUM(bev_pro) as bp, SUM(b.eps_proc) as ep FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.field_id<".($data['is_moon'] == 1 ? 15 : 19)." AND a.colonies_id=".$data['id'],4);
		$fd = $db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) WHERE b.colonies_id=".$data['id']." AND b.field_id<".($data['is_moon'] == 1 ? 15 : 19)." AND b.aktiv=1 GROUP BY a.goods_id");
		while($gdt=mysql_fetch_assoc($fd)) $gd[$gdt['goods_id']] -= $gdt['gc'];
		$redt = $db->query("SELECT SUM(a.research_t) as rt,SUM(a.research_k) as rk,SUM(a.research_v) as rv,SUM(a.research_d) as rd FROM stu_buildings as a LEFT JOIN stu_colonies_fielddata as b ON a.buildings_id=b.buildings_id WHERE b.aktiv=1 AND b.field_id<".($data['is_moon'] == 1 ? 15 : 19)." AND b.colonies_id=".$data['id'],4);
		$bev['u'] -= $et['bu'];
		$bev['f'] += $et['bu'];
		$bev['w'] -= $et['bp'];
		$et['ep'] < 0 ? $fe+=abs($et['ep']) : $fe-=$et['ep'];
		$red['rt'] -= $redt['rt'];
		$red['rk'] -= $redt['rk'];
		$red['rv'] -= $redt['rv'];
		$red['rd'] -= $redt['rd'];
		$db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE colonies_id=".$data['id']." AND buildings_id< 400 AND field_id<".($data['is_moon'] == 1 ? 15 : 19)." LIMIT 20");
		$smsg .= "Aufgrund des Ausfalls des Raumbahnhofs wurden alle Gebäude (außer Plattformen) im Orbit deaktiviert<br>";
	}
	if ($rd[buildings_id] == 46)
	{
		$et = $db->query("SELECT SUM(bev_use) as bu, SUM(bev_pro) as bp, SUM(b.eps_proc) as ep,SUM(b.schilde) as sh FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.field_id>72 AND a.colonies_id=".$data[id],4);
		$fd = $db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) WHERE b.colonies_id=".$data[id]." AND b.field_id>72 AND b.aktiv=1 GROUP BY a.goods_id");
		while($gdt=mysql_fetch_assoc($fd)) $gd[$gdt['goods_id']] -= $gdt['gc'];
		$redt = $db->query("SELECT SUM(a.research_t) as rt,SUM(a.research_k) as rk,SUM(a.research_v) as rv,SUM(a.research_d) as rd  FROM stu_buildings as a LEFT JOIN stu_colonies_fielddata as b ON a.buildings_id=b.buildings_id WHERE b.aktiv=1 AND b.field_id>72 AND b.colonies_id=".$data[id],4);
		$bev['u'] -= $et['bu'];
		$bev['f'] += $et['bu'];
		$bev['w'] -= $et['bp'];
		$et['ep'] < 0 ? $fe+=abs($et['ep']) : $fe-=$et['ep'];
		$red['rt'] -= $redt['rt'];
		$red['rk'] -= $redt['rk'];
		$red['rv'] -= $redt['rv'];
		$red['rd'] -= $redt['rd'];
		$db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE colonies_id=".$data['id']." AND field_id>72 LIMIT 30");
		if ($et['sh']) $db->query("UPDATE stu_colonies SET max_schilde=max_schilde-".$et['sh']."".($data['schilde'] > $data['max_schilde']-$et['sh'] ? ",schilde=".($data['max_schilde']-$et['sh']) : "")." WHERE id=".$data['id']);
		$smsg .= "Aufgrund des Ausfalls des Untergrundlifts wurden alle Gebäude im Untergrund deaktiviert<br>";
	}
	if ($rd['buildings_id'] == 310) $gd[1] -= $db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE (buildings_id=2 OR buildings_id=104 OR buildings_id=9) AND aktiv=1 AND colonies_id=".$data['id'],1);
	if ($rd['buildings_id'] == 100) $db->query("UPDATE stu_colonies SET schilde_status='0' WHERE id=".$data['id']." LIMIT 1");
	if ($rd['buildings_id'] == 101) $db->query("UPDATE stu_colonies SET max_schilde=max_schilde-700".($data['max_schilde']-700 < $data['schilde'] ? ",schilde=".($data['max_schilde']-700) : "")." WHERE id=".$data['id']." LIMIT 1");
	if ($rd['buildings_id'] == 102 && $ofc > 0) deactivate_organik($ofc);
	if ($rd['buildings_id'] == 70 && $fac > 0) deactivate_fergam($fac);
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
	switch ($data['colonies_classes_id'])
	{
		case 1:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/3);
			break;
		case 2:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/4);
			break;
		case 3:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/4);
			break;
		case 4:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/5);
			break;
		case 5:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/5);
			break;
		case 6:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/6);
			break;
		case 7:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/6);
			break;
		case 8:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/6);
			break;
		case 9:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/6);
			break;
		case 10:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/6);
			break;
		case 20:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/5);
			break;
		case 21:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/6);
			break;
		case 22:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/6);
			break;
		case 23:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/7);
			break;
		case 24:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/7);
			break;
		case 25:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/8);
			break;
		case 26:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/8);
			break;
		case 27:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/8);
			break;
		case 28:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/8);
			break;
		case 29:
			$im = ceil(($bev['w']-($bev['u']+$bev['f']))/6);
			break;
	}
	return $im;
}

function get_organik_fabs()
{
	global $db,$data;
	return $db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE buildings_id=103 AND aktiv=1 AND colonies_id=".$data['id'],1);
}

function deactivate_organik(&$res)
{
	global $db,$data,$smsg,$fe,$gd,$bev,$gl,$red,$grav,$ofc;
	for($k=1;$k<=$res;$k++)
	{
		$rd = $db->query("SELECT a.field_id,b.buildings_id,b.name,b.bev_use,b.bev_pro,b.eps_proc,b.research_t,b.research_k,b.research_v, b.research_d FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$data['id']." AND a.buildings_id=103 AND a.aktiv=1 LIMIT 1",4);
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
		$result = $db->query("SELECT goods_id,count FROM stu_buildings_goods WHERE buildings_id=".$rd['buildings_id']);
		while($gr=mysql_fetch_assoc($result))
		{
			if ($gr['count'] < 0) $gd[$gr['goods_id']] += abs($gr['count']);
			else $gd[$gr['goods_id']] -= $gr['count'];
		}
		$smsg .= $rd['name']." auf Feld ".$rd['field_id']." deaktiviert (Organischer Regenerator nicht aktiviert)<br>";
		$rd['eps_proc'] < 0 ? $fe+=abs($rd['eps_proc']) : $fe-=$rd['eps_proc'];
		$bev['u'] -= $rd['bev_use'];
		$bev['f'] += $rd['bev_use'];
		$bev['w'] -= $rd['bev_pro'];
		$red['rt'] -= $rd['research_t'];
		$red['rk'] -= $rd['research_k'];
		$red['rv'] -= $rd['research_v'];
		$red['rd'] -= $rd['research_d'];
		$db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE field_id=".$rd['field_id']." AND colonies_id=".$data['id']." LIMIT 1");
	}
}

function get_fergam_fabs()
{
	global $db,$data;
	return $db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE buildings_id=71 AND aktiv=1 AND colonies_id=".$data['id']."",1);
}

function deactivate_fergam(&$res)
{
	global $db,$data,$smsg,$fe,$gd,$bev,$gl,$red,$grav,$ofc;
	for($k=1;$k<=$res;$k++)
	{
		$rd = $db->query("SELECT a.field_id,b.buildings_id,b.name,b.bev_use,b.bev_pro,b.eps_proc,b.research_t,b.research_k,b.research_v, b.research_d FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$data['id']." AND a.buildings_id=71 AND a.aktiv=1 LIMIT 1",4);
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
		$result = $db->query("SELECT goods_id,count FROM stu_buildings_goods WHERE buildings_id=".$rd['buildings_id']);
		while($gr=mysql_fetch_assoc($result))
		{
			if ($gr['count'] < 0) $gd[$gr['goods_id']] += abs($gr['count']);
			else $gd[$gr['goods_id']] -= $gr['count'];
		}
		$smsg .= $rd['name']." auf Feld ".$rd['field_id']." deaktiviert (Antimaterie Forschungskomplex nicht aktiviert)<br>";
		$rd['eps_proc'] < 0 ? $fe+=abs($rd['eps_proc']) : $fe-=$rd['eps_proc'];
		$bev['u'] -= $rd['bev_use'];
		$bev['f'] += $rd['bev_use'];
		$bev['w'] -= $rd['bev_pro'];
		$red['rt'] -= $rd['research_t'];
		$red['rk'] -= $rd['research_k'];
		$red['rv'] -= $rd['research_v'];
		$red['rd'] -= $rd['research_d'];
		$db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE field_id=".$rd['field_id']." AND colonies_id=".$data['id']." LIMIT 1");
	}
}

$time1 = time();

// Check des Spielstatus
@touch("/var/tmp/lock_coltick_".$_SERVER['argv'][1]);
@unlink("/srv/www/stu_sys/webroot/intern/ticklog/log".$_SERVER['argv'][1]);
$fp = fopen("/srv/www/stu_sys/webroot/intern/ticklog/log".$_SERVER['argv'][1],"a+");
$ot = time();

// Na dann starten wir mal...her mit den Kolonien
$result = $db->query("SELECT a.id,a.name,a.colonies_classes_id,a.user_id,a.bev_free,a.bev_work,a.bev_max,a.eps,a.max_eps,a.schilde,a.max_schilde,a.max_storage,a.bevstop,a.einwanderung,a.gravitation,b.is_moon,c.lav_not FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.user_id!=1 AND ISNULL(c.vac_active) AND c.tick=".$_SERVER['argv'][1]." ORDER BY a.user_id,a.id");
while($data=mysql_fetch_assoc($result))
{
	fwrite($fp,time()-$ot." :: ".$data['id']."\n");
	// Setzen wir mal die Vars neu
	if (!$luser || $msg == "") $luser = $data['user_id'];
	$gochk = 0;
	$nvb = 0;
	$smsg = "";
	$grav = getgravenergy($data['gravitation']);
	unset($gd);
	unset($sd);
	$bev = array("u" => $data['bev_work'],"f" => $data['bev_free'],"w" => $data['bev_max']);
	// Und nun ab...sammeln wir mal die relevanten Daten
	$gs = $db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=".$data['id'],1);
	$fe = $db->query("SELECT SUM(b.eps_proc) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$data['id'],1);
	$fd = $db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) WHERE b.colonies_id=".$data['id']." AND b.aktiv=1 GROUP BY a.goods_id");
	while($gdt=mysql_fetch_assoc($fd)) $gd[$gdt['goods_id']] = $gdt['gc'];
	$sr = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_colonies_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=".$data['id']);
	while($sdt=mysql_fetch_assoc($sr)) $sd[$sdt['goods_id']] = array("ct" => $sdt['count'],"nm" => $sdt['name']);
	$red = $db->query("SELECT SUM(a.research_t) as rt,SUM(a.research_k) as rk,SUM(a.research_v) as rv,SUM(a.research_d) as rd  FROM stu_buildings as a LEFT JOIN stu_colonies_fielddata as b ON a.buildings_id=b.buildings_id WHERE b.aktiv=1 AND b.colonies_id=".$data['id'],4);
	if ($db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=310 AND aktiv=1 AND colonies_id=".$data['id'],1) != 0) $gd[1] += $db->query("SELECT COUNT(field_id) FROM stu_colonies_fielddata WHERE (buildings_id=2 OR buildings_id=104 OR buildings_id=9) AND aktiv=1 AND colonies_id=".$data['id'],1);
	if ($db->query("SELECT field_id FROM stu_colonies_fielddata WHERE (buildings_id=401 OR buildings_id=411 OR buildings_id=421) AND aktiv=1 AND colonies_id=".$data['id'],1) != 0) $beamblock = 1;
	else $beamblock = 0;
	// Tribbles?!
	$tr = $db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$data['id']." AND goods_id=1013 LIMIT 1",1);
	if ($tr > 0) tribbles($data,$tr);
	// Gibt es Organikfabriken?
	$ofc = get_organik_fabs();
	if ($ofc > 0 && $db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=102 AND aktiv=1 AND colonies_id=".$data['id'],1) == 0) deactivate_organik($ofc);
	// Gibt es Ferg-AM-Dinger?
	$fac = get_fergam_fabs();
	if ($fac > 0 && $db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=70 AND aktiv=1 AND colonies_id=".$data['id'],1) == 0) deactivate_fergam($fac);
	// Nahrung wird ja auch verbraucht...
	$gd[1] -= ceil(($data['bev_work']+$data['bev_free'])/5);
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
		$smsg .= $nr."% der Wirtschaftsleistung sind aufgrund von Streiks nicht verfügbar<br>";
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
		$smsg .= $nr."% der Wirtschaftsleistung sind aufgrund von Streiks nicht verfügbar<br>";
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
	$db->query("UPDATE stu_colonies SET bev_work=".$bev['u'].",bev_free=".$bev['f'].",bev_max=".$bev['w'].",eps=".$fe.",lastrw=".$lrw.",beamblock=".$beamblock." WHERE id=".$data['id']." LIMIT 1");
	// Forschungspunkte, wenn vorhanden, gutschreiben
	if ($red['rk'] > 0 || $red['rt'] > 0 || $red['rv'] > 0)
	{
		$fdata = $db->query("SELECT research_konstruktion,research_technik,research_verarbeitung,research_dominion FROM stu_user WHERE id=".$data['user_id'],4);
		if ($fdata['research_konstruktion'] > 500) $fdata['research_konstruktion'] = 500;
		if ($fdata['research_technik'] > 500) $fdata['research_technik'] = 500;
		if ($fdata['research_verarbeitung'] > 500) $fdata['research_verarbeitung'] = 500;
		if ($fdata['research_dominion'] > 500) $fdata['research_dominion'] = 500;
		
		if ($fdata['research_konstruktion'] + $red['rk'] > 500) $red['rk'] = 500;
		else $red['rk'] += $fdata['research_konstruktion'];
		
		if ($fdata['research_technik'] + $red['rt'] > 500) $red['rt'] = 500;
		else $red['rt'] += $fdata['research_technik'];
		
		if ($fdata['research_verarbeitung'] + $red['rv'] > 500) $red['rv'] = 500;
		else $red['rv'] += $fdata['research_verarbeitung'];
		
		if ($fdata['research_dominion'] + $red['rd'] > 500) $red['rd'] = 500;
		else $red['rd'] += $fdata['research_dominion'];
		
		$db->query("UPDATE stu_user SET research_konstruktion=".$red['rk'].",research_technik=".$red['rt'].",research_verarbeitung=".$red['rv'].",research_dominion=".$red['rd']." WHERE id=".$data['user_id']." LIMIT 1");
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

@unlink("/var/tmp/lock_coltick_".$_SERVER['argv'][1]);
?>
