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

function sdeactivate()
{
	global $db,$data,$smsg,$eminus,$gd,$bev,$gl,$cachedbg;
	$rd = $db->query("SELECT a.field_id,a.ship,b.* FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING(component_id) LEFT JOIN stu_station_component_goods as c USING(component_id) WHERE a.stations_id=".$data[id]." AND a.aktiv=1 AND b.eps_proc<0 ORDER BY b.eprio,RAND() DESC LIMIT 1",4);
	if ($rd == 0)
	{
		return false;
	}

	$result = $db->query("SELECT goods_id,count FROM stu_station_component_goods WHERE component_id=".$rd['component_id']);
	while($gr=mysql_fetch_assoc($result))
	{
		if ($gr['count'] < 0) $gd[$gr['goods_id']] += abs($gr['count']);
		else $gd[$gr['goods_id']] -= $gr['count'];
	}

	if ($rd[ship] == 0) {


		$smsg .= $rd['name']." auf Feld ".$rd['field_id']." deaktiviert (Energiemangel)<br>";

		$bev['u'] -= $rd['bev_use'];
		$bev['f'] += $rd['bev_use'];
		$bev['w'] -= $rd['bev_pro'];

		$db->query("UPDATE stu_stations_fielddata SET aktiv=0 WHERE field_id=".$rd['field_id']." AND stations_id=".$data['id']." LIMIT 1");
		$db->query("UPDATE stu_stations SET max_schilde=max_schilde-".$rd['schilde']."".($data['schilde'] > $data['max_schilde']-$rd['schilde'] ? ",schilde=".($data['max_schilde']-$rd['schilde']) : "")."  WHERE id=".$data[id]." LIMIT 1");
	}
	else {
		$smsg .= "Ein Frachtschiff kehrte wegen Energiemangel zur Basis zurück<br>";

		$targetsys = $db->query("SELECT * FROM stu_systems WHERE systems_id = ".$data[systems_id]."",4);

		$bev['u'] -= 3;
		$bev['f'] += 3;

		$db->query("UPDATE stu_ships SET crew=0,cx=".$targetsys[cx].",cy=".$targetsys[cy].",systems_id = ".$data[systems_id].",sx=".$data[sx].",sy=".$data[sy]." WHERE id = ".$rd[ship]." LIMIT 1");
		$db->query("UPDATE stu_stations_fielddata SET aktiv=0,component_id=0 WHERE field_id=".$rd['field_id']." AND stations_id=".$data['id']." LIMIT 1");
		$db->query("UPDATE stu_stations SET bev_work=bev_work-3,bev_free=bev_free+3 WHERE id=".$data[id]." LIMIT 1");




	}
	return true;
}
function sdeactivateb()
{
	global $db,$data,$smsg,$eminus,$eplus,$gd,$bev,$gl,$cachedbg;
	$rd = $db->query("SELECT a.field_id,a.ship,b.* FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING(component_id) LEFT JOIN stu_station_component_goods as c USING(component_id) WHERE a.stations_id=".$data[id]." AND a.aktiv=1 ORDER BY b.eprio,RAND() DESC LIMIT 1",4);
	if ($rd == 0)
	{
		return false;
	}

	$result = $db->query("SELECT goods_id,count FROM stu_station_component_goods WHERE component_id=".$rd['component_id']);
	while($gr=mysql_fetch_assoc($result))
	{
		if ($gr['count'] < 0) $gd[$gr['goods_id']] += abs($gr['count']);
		else $gd[$gr['goods_id']] -= $gr['count'];
	}


	if ($rd[ship] == 0) {

		$smsg .= $rd['name']." auf Feld ".$rd['field_id']." deaktiviert (Nahrungsmangel)<br>";

		$bev['u'] -= $rd['bev_use'];
		$bev['f'] += $rd['bev_use'];
		$bev['w'] -= $rd['bev_pro'];
		if ($rd[eps_proc] > 0) $eplus += $rd[eps_proc];
		else $eminus -= $rd[eps_proc];
		$db->query("UPDATE stu_stations_fielddata SET aktiv=0 WHERE field_id=".$rd['field_id']." AND stations_id=".$data['id']." LIMIT 1");
		$db->query("UPDATE stu_stations SET max_schilde=max_schilde-".$rd['schilde']."".($data['schilde'] > $data['max_schilde']-$rd['schilde'] ? ",schilde=".($data['max_schilde']-$rd['schilde']) : "")."  WHERE id=".$data[id]." LIMIT 1");
	}
	else {
		$smsg .= "Ein Frachtschiff kehrte wegen Nahrungsmangel zur Basis zurück<br>";

		$targetsys = $db->query("SELECT * FROM stu_systems WHERE systems_id = ".$data[systems_id]."",4);

		$bev['u'] -= 3;
		$bev['f'] += 3;

		$db->query("UPDATE stu_ships SET crew=0,cx=".$targetsys[cx].",cy=".$targetsys[cy].",systems_id = ".$data[systems_id].",sx=".$data[sx].",sy=".$data[sy]." WHERE id = ".$rd[ship]." LIMIT 1");
		$db->query("UPDATE stu_stations_fielddata SET aktiv=0,component_id=0 WHERE field_id=".$rd['field_id']." AND stations_id=".$data['id']." LIMIT 1");
		$db->query("UPDATE stu_stations SET bev_work=bev_work-3,bev_free=bev_free+3 WHERE id=".$data[id]." LIMIT 1");
	}
	return true;
}
function sdeactivatew()
{
	global $db,$data,$smsg,$eminus,$eplus,$gd,$bev,$gl,$cachedbg;
	$rd = $db->query("SELECT a.field_id,a.ship,b.* FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING(component_id) LEFT JOIN stu_station_component_goods as c USING(component_id) WHERE a.stations_id=".$data[id]." AND a.aktiv=1 AND b.bev_use > 0 ORDER BY b.eprio,RAND() DESC LIMIT 1",4);
	if ($rd == 0)
	{
		return false;
	}

	$result = $db->query("SELECT goods_id,count FROM stu_station_component_goods WHERE component_id=".$rd['component_id']);
	while($gr=mysql_fetch_assoc($result))
	{
		if ($gr['count'] < 0) $gd[$gr['goods_id']] += abs($gr['count']);
		else $gd[$gr['goods_id']] -= $gr['count'];
	}


	if ($rd[ship] == 0) {

		$smsg .= $rd['name']." auf Feld ".$rd['field_id']." deaktiviert (Wohnraummangel)<br>";

		$bev['u'] -= $rd['bev_use'];
		$bev['f'] += $rd['bev_use'];
		$bev['w'] -= $rd['bev_pro'];
		if ($rd[eps_proc] > 0) $eplus += $rd[eps_proc];
		else $eminus -= $rd[eps_proc];
		$db->query("UPDATE stu_stations_fielddata SET aktiv=0 WHERE field_id=".$rd['field_id']." AND stations_id=".$data['id']." LIMIT 1");
		$db->query("UPDATE stu_stations SET max_schilde=max_schilde-".$rd['schilde']."".($data['schilde'] > $data['max_schilde']-$rd['schilde'] ? ",schilde=".($data['max_schilde']-$rd['schilde']) : "")."  WHERE id=".$data[id]." LIMIT 1");
	}
	else {
		$smsg .= "Ein Frachtschiff kehrte wegen Wohnraummangel zur Basis zurück<br>";

		$targetsys = $db->query("SELECT * FROM stu_systems WHERE systems_id = ".$data[systems_id]."",4);

		$bev['u'] -= 3;
		$bev['f'] += 3;

		$db->query("UPDATE stu_ships SET crew=0,cx=".$targetsys[cx].",cy=".$targetsys[cy].",systems_id = ".$data[systems_id].",sx=".$data[sx].",sy=".$data[sy]." WHERE id = ".$rd[ship]." LIMIT 1");
		$db->query("UPDATE stu_stations_fielddata SET aktiv=0,component_id=0 WHERE field_id=".$rd['field_id']." AND stations_id=".$data['id']." LIMIT 1");
		$db->query("UPDATE stu_stations SET bev_work=bev_work-3,bev_free=bev_free+3 WHERE id=".$data[id]." LIMIT 1");
	}
	return true;
}
function sdeactivateo()
{
	global $db,$data,$smsg,$eminus,$eplus,$gd,$bev,$gl,$cachedbg;
	$rd = $db->query("SELECT a.field_id,b.* FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING(component_id) LEFT JOIN stu_station_component_goods as c USING(component_id) WHERE a.stations_id=".$data[id]." AND a.aktiv=1 AND c.goods_id = 20 AND c.count < 0 ORDER BY b.eprio,RAND() DESC LIMIT 1",4);
	if ($rd == 0)
	{
		return false;
	}

	$result = $db->query("SELECT goods_id,count FROM stu_station_component_goods WHERE component_id=".$rd['component_id']);
	while($gr=mysql_fetch_assoc($result))
	{
		if ($gr['count'] < 0) $gd[$gr['goods_id']] += abs($gr['count']);
		else $gd[$gr['goods_id']] -= $gr['count'];
	}

	$smsg .= $rd['name']." auf Feld ".$rd['field_id']." deaktiviert (Erzmangel)<br>";

	$bev['u'] -= $rd['bev_use'];
	$bev['f'] += $rd['bev_use'];
	$bev['w'] -= $rd['bev_pro'];
	$eminus -= $rd[eps_proc];
	$db->query("UPDATE stu_stations_fielddata SET aktiv=0 WHERE field_id=".$rd['field_id']." AND stations_id=".$data['id']." LIMIT 1");
	$db->query("UPDATE stu_stations SET max_schilde=max_schilde-".$rd['schilde']."".($data['schilde'] > $data['max_schilde']-$rd['schilde'] ? ",schilde=".($data['max_schilde']-$rd['schilde']) : "")."  WHERE id=".$data[id]." LIMIT 1");
	return true;
}
function sdeactivateam()
{
	global $db,$data,$smsg,$eminus,$eplus,$gd,$bev,$gl,$cachedbg;
	$rd = $db->query("SELECT a.field_id,b.* FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b USING(component_id) LEFT JOIN stu_station_component_goods as c USING(component_id) WHERE a.stations_id=".$data[id]." AND a.aktiv=1 AND a.component_id = 113 ORDER BY b.eprio,RAND() DESC LIMIT 1",4);
	if ($rd == 0)
	{
		return false;
	}

	$result = $db->query("SELECT goods_id,count FROM stu_station_component_goods WHERE component_id=".$rd['component_id']);
	while($gr=mysql_fetch_assoc($result))
	{
		if ($gr['count'] < 0) $gd[$gr['goods_id']] += abs($gr['count']);
		else $gd[$gr['goods_id']] -= $gr['count'];
	}

	$smsg .= $rd['name']." auf Feld ".$rd['field_id']." deaktiviert (Zusammenbruch des Feedback-Systems)<br>";

	$bev['u'] -= $rd['bev_use'];
	$bev['f'] += $rd['bev_use'];
	$bev['w'] -= $rd['bev_pro'];
	$eminus -= $rd[eps_proc];
	$db->query("UPDATE stu_stations_fielddata SET aktiv=0 WHERE field_id=".$rd['field_id']." AND stations_id=".$data['id']." LIMIT 1");
	return true;
}
function lowerstorage(&$good,&$count)
{
	global $gs,$db,$data;
	$res = $db->query("UPDATE stu_stations_storage SET count=count-".$count." WHERE stations_id=".$data['id']." AND goods_id=".$good." AND count>".$count." LIMIT 1",6);
	if ($res == 0) $db->query("DELETE FROM stu_stations_storage WHERE stations_id=".$data['id']." AND goods_id=".$good." LIMIT 1");
	$gs -= $count;
}
function upperstorage(&$good,&$count)
{
	global $gs,$db,$data;
	$res = $db->query("UPDATE stu_stations_storage SET count=count+".$count." WHERE stations_id=".$data['id']." AND goods_id=".$good." LIMIT 1",6);
	if ($res == 0) $db->query("INSERT INTO stu_stations_storage (stations_id,goods_id,count) VALUES ('".$data['id']."','".$good."','".$count."')");
	$gs += $count;
}



$time1 = time();

// Check des Spielstatus
//@touch("/var/tmp/lock_coltick_".$_SERVER['argv'][1]);
//@unlink("/srv/www/stu_sys/webroot/intern/ticklog/log".$_SERVER['argv'][1]);
//$fp = fopen("/srv/www/stu_sys/webroot/intern/ticklog/log".$_SERVER['argv'][1],"a+");
//$ot = time();

// Na dann starten wir mal...her mit den Kolonien
$result = $db->query("SELECT a.*,c.lav_not FROM stu_stations as a LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.user_id!=1 AND ISNULL(c.vac_active) ORDER BY a.user_id,a.id");
while($data=mysql_fetch_assoc($result))
{
	// Setzen wir mal die Vars neu
	if (!$luser || $msg == "") $luser = $data['user_id'];

	unset($gd);
	unset($sd);

	$smsg = "";
	$wkmsg = "";

	$bev = array("u" => $data['bev_work'],"f" => $data['bev_free'],"w" => $data['bev_max']);

	$gs = $db->query("SELECT SUM(count) FROM stu_stations_storage WHERE stations_id=".$data['id'],1);
	$fd = $db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_station_component_goods as a LEFT JOIN stu_stations_fielddata as b USING(component_id) WHERE b.stations_id=".$data['id']." AND b.aktiv=1 GROUP BY a.goods_id");
	while($gdt=mysql_fetch_assoc($fd)) $gd[$gdt['goods_id']] = $gdt['gc'];
	$sr = $db->query("SELECT a.goods_id,a.count,b.name FROM stu_stations_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.stations_id=".$data['id']);
	while($sdt=mysql_fetch_assoc($sr)) $sd[$sdt['goods_id']] = array("ct" => $sdt['count'],"nm" => $sdt['name']);

	$warpcores = $db->query("SELECT count(aktiv) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc > 0 AND a.stations_id = ".$data['id']."",1);
	$antimatter = $db->query("SELECT count(aktiv) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND a.component_id = 113 AND a.stations_id = ".$data['id']."",1);


	while ($antimatter > $warpcores) {

		sdeactivateam();
		$antimatter--;
	}

	// Jetzt gehts los! Verarbeiten wir mal die Waren...
	while(TRUE)
	{
		$shutdown = false;
		if ($sd[20]['ct'] - abs($gd[20]) < 0) { 
			$shutdown = sdeactivateo();
		}
		if ($shutdown == false) break;

	}

	$i = 1;
	while ($i < 120)
	{
		$i++;
		$recalc = false;

		// Energiedaten sammeln
		$eplus = $db->query("SELECT SUM(b.eps_proc) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc > 0 AND a.stations_id = ".$data['id']."",1);
		$eminus = $db->query("SELECT SUM(b.eps_proc) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc < 0 AND a.stations_id = ".$data['id']."",1);
		$crew = $db->query("SELECT (bev_work+bev_free+bev_crew) FROM stu_stations WHERE id = ".$data['id']."",1);
		$mode = $db->query("SELECT wkfull FROM stu_stations WHERE id = ".$data['id']."",1);
		$replis = $db->query("SELECT COUNT(aktiv) FROM stu_stations_fielddata WHERE stations_id = ".$data['id']." AND aktiv=1 AND component_id = 102",1);
		$warpcores = $db->query("SELECT count(aktiv) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.eps_proc > 0 AND a.stations_id = ".$data['id']."",1);

		$wkloadsize = 40;
				
		$rs = 5+$replis*3;
		$erepli = ceil($crew/$rs);

		$nrofloads = $warpcores;

		if ($mode == 1) {

		if (($nrofloads * $wkloadsize)-$eplus > ($data['wkload_max'] - $data['wkload'])) {
			$use = ($data['wkload_max'] - $data['wkload']) + $eplus;
			$nrofloads= ceil($use/$wkloadsize) - 1;
		}
		} else {

			if ($eplus < $data['wkload']) $nrofloads = 0;
			else $nrofloads = ceil($eplus/$wkloadsize);
		}
		$egesamt = $eplus+$eminus-$erepli;

		$am = $gd[6] + $sd[6]['ct'];
		$de = $gd[5] + $sd[5]['ct'];
		if ($de > 0 && $am > 0) {
			$availloads = min(floor($am/2),floor($de/2));
		}
		else $availloads = 0;

		$nrofloads = min($availloads,$nrofloads);


		$wkblubb = $data['wkload'] + $wkloadsize*$nrofloads;

		if ($wkblubb < $eplus) {
			$eplus = $wkblubb;
			$wkmsg = "Unzureichende Warpkernladung, mögliches Systemversagen steht bevor!<br>";
		}

		if ($eplus+$eminus-$erepli+$data[eps] < 0)
		{
			$shutdown = sdeactivate();
			$recalc = true;


		}



		if (($recalc == false) || ($recalc = true && $shutdown == false)) {
			break;
		}


	}
	
	$gd[5] -= $nrofloads * 2;
	$gd[6] -= $nrofloads * 2;


	if ($eplus+$eminus-$erepli+$data[eps] < 0) {

		// Hunger! Nix wie weg hier!

		$feed = $rs*($eplus+$eminus+$data[eps]);

		if ($bev[f]+$bev[u] > $feed) {
			$flee = $bev[f]+$bev[u] - $feed;
		} else $flee = 0;
		

		if ($flee > $bev[f]) {

			$i = 1;
			while ($i < 120)
			{
				$i++;
				if ($bev[f] < $flee) {
					$s = sdeactivateb();
					if ($s == false) break;
				}
				else break;
			}
		}
		$bev[f] -= $flee;

		$smsg .= $flee." Besatzungsmitglieder wegen fehlender Replikatorrationen geflohen<br>";

	}


	$flee = 0;

	// Wohnraummangel? Oh weia
	if ($bev['w'] < $bev['f']+$bev['u'])
	{

		$flee = $bev['f']+$bev['u'] - $bev['w'];

		
		if ($flee > $bev[f]) {

			$i = 1;
			while ($i < 120)
			{
				$i++;
				if ($bev[f] < $flee) {
					$s = sdeactivatew();
					if ($s == false) break;
				}
				else break;
			}
		}
		$bev[f] -= $flee;

		$smsg .= $flee." Besatzungsmitglieder wegen fehlendem Wohnraum geflohen<br>";
	}


	@asort($gd);
	foreach($gd as $key => $value)
	{
		if ($value > 0)
		{
			if ($gs >= $data['max_storage'])
			{
				if ($data['lav_not'] == 1) $smsg .= "Der Lagerraum der Station ist voll - Keine weiteren Waren gutgeschrieben<br>";
				break;
			}
			if ($value > $data['max_storage']-$gs) $value = $data['max_storage']-$gs;
			upperstorage($key,$value);
		}
		if ($value < 0) lowerstorage($key,abs($value));
	}

	$crew = $db->query("SELECT (bev_work+bev_free+bev_crew) FROM stu_stations WHERE id = ".$data['id']."",1);
	$replis = $db->query("SELECT COUNT(aktiv) FROM stu_stations_fielddata WHERE stations_id = ".$data['id']." AND aktiv=1 AND component_id = 102",1);

	$rs = 5+$replis*3;
	$erepli = ceil($crew/$rs);

	$ges = $eplus+$eminus-$erepli;



	$newwk = $data[wkload]-$eplus+$nrofloads*$wkloadsize;

	$newwk = min($newwk,$data[wkload_max]);
	$newwk = max($newwk,0);



	$sensor = $db->query("SELECT SUM(b.sensor) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND b.sensor > 0 AND a.stations_id = ".$data['id']."",1);

	$sensor = floor($sensor);

	$subraum = $db->query("SELECT COUNT(a.field_id) FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.aktiv = 1 AND (a.component_id  = 118 OR a.component_id  = 119) AND a.stations_id = ".$data['id']."",1);

	if ($subraum != 0) $subraum = 1;

	// Kolonie aktualisieren *fump*
	if ($ges > $data['max_eps']-$data['eps']) $ges = $data['max_eps']-$data['eps'];
	if ($ges < 0) $ges = $data['eps']-abs($ges);
	else $ges = $data['eps']+$ges;
	$db->query("UPDATE stu_stations SET bev_work=".$bev['u'].",bev_free=".$bev['f'].",bev_max=".$bev['w'].",eps=".$ges.",wkload=".$newwk.",sensor=".$sensor.",subspace=".$subraum." WHERE id=".$data['id']." LIMIT 1");
	
	// Nun kriegt der Eigentümer vielleicht noch eine PM
	if ($msg != "" || $smsg != "")
	{
		if ($luser != $data['user_id'] && $msg != "")
		{
			$db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$luser."','".$msg."',NOW(),'5')");
			$luser = $data['user_id'];
			$msg = "";
		}
		if ($smsg != "") $msg .= "<b>Tickreport der Station ".addslashes($data['name'])."</b><br>".$smsg;
	}








	// 292, 296, 108, 226, 28











}
// Und fertig...Halt - Moment. Da könnte noch ne PM sein...
if ($msg != "") $db->query("INSERT INTO stu_pms (send_user,recip_user,text,date,type) VALUES ('1','".$luser."','".$msg."',NOW(),'4')");

@unlink("/var/tmp/lock_coltick_".$_SERVER['argv'][1]);
?>
