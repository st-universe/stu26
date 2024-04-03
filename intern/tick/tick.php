<?php
include_once("/var/www/st-universe.eu/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$state = $db->query("SELECT value FROM stu_game_vars WHERE var='state'",1);
if ($state == 3) {
	die();
}
if ($state == 1) {
	$db->query("UPDATE stu_game_vars SET value='2' WHERE var='state'");
}
$ro = $db->query("SELECT MAX(runde) FROM stu_game_rounds LIMIT 1",1);
$db->query("UPDATE stu_game_rounds SET ende=NOW() WHERE runde=".$ro);
while (TRUE) {
	for ($i=1;$i<=8;$i++) {
		if (file_exists('/var/www/virtual/stuniverse.de/htdocs/intern/tick/lock/lock_coltick_'.$i)) {
			sleep(1);
			continue;
		}
	}
	$db->query("UPDATE stu_game_vars SET value='1' WHERE var='state'");
	startTurn();
	break;
}


function startTurn() {
	global $db;

	// Neue Runde starten
	$ro = $db->query("SELECT MAX(runde) FROM stu_game_rounds LIMIT 1",1);
	$db->query("INSERT INTO stu_game_rounds (runde,start,spieler,schiffe,wirtschaft) VALUES ('".($ro+1)."',NOW(),'".$db->query("SELECT COUNT(id) FROM stu_user WHERE id>100 AND aktiv='1'",1)."','".$db->query("SELECT COUNT(a.id) as id FROM stu_ships as a LEFT JOIN stu_rumps as b USING (rumps_id) WHERE b.trumfield!='1'",1)."','".round($db->query("SELECT SUM(lastrw) FROM stu_colonies WHERE user_id!=1",1))."')");

	// WP-Masahme
	// $result = $db->query("SELECT id,wp_overrounds FROM stu_user WHERE id>100 AND ISNULL(vac_active)");
	// while($data=mysql_fetch_assoc($result))
	// {
		// $wsum = $db->query("SELECT SUM(lastrw) FROM stu_colonies WHERE user_id=".$data['id'],1);
		// $psum = $db->query("SELECT SUM(points) FROM stu_ships WHERE user_id=".$data['id'],1);
		// if ($data['wp_overrounds'] == -2 && $wsum >= $psum) continue;
		// if ($data['wp_overrounds'] > -2 && $wsum >= $psum)
		// {
			// $db->query("UPDATE stu_user SET wp_overrounds=wp_overrounds-1 WHERE id=".$data['id']." LIMIT 1");
			// continue;
		// }
		// $db->query("UPDATE stu_user SET wp_overrounds=wp_overrounds+1 WHERE id=".$data['id']." LIMIT 1");
	// }
	// Statistiken erstellen
	$db->query("TRUNCATE TABLE stu_stats");

	$result = $db->query("SELECT COUNT(id) as id,user_id FROM stu_ships WHERE user_id>100 GROUP BY user_id");
	while($data=mysql_fetch_assoc($result)) $arr[$data['user_id']]['ship'] = $data['id'];

	$result = $db->query("SELECT COUNT(id) as id,user_id FROM stu_ships WHERE user_id>100 AND crew>=min_crew GROUP BY user_id");
	while($data=mysql_fetch_assoc($result)) $arr[$data['user_id']]['shipb'] = $data['id'];

	$result = $db->query("SELECT SUM(lastrw) as id,user_id FROM stu_colonies WHERE user_id>100 GROUP BY user_id");
	while($data=mysql_fetch_assoc($result)) $arr[$data['user_id']]['wirt'] = $data['id'];

	$result = $db->query("SELECT SUM(bev_free)+SUM(bev_work) as id,user_id FROM stu_colonies WHERE user_id>100 GROUP BY user_id");
	while($data=mysql_fetch_assoc($result)) $arr[$data['user_id']]['popu'] = $data['id'];

	$result = $db->query("SELECT user_id,COUNT(research_id)*5 as id FROM stu_researched WHERE user_id>100 AND research_id!=500 GROUP BY user_id");
	while($data=mysql_fetch_assoc($result))
	{
		$arr[$data['user_id']]['resa'] = $data['id'];
		$arr[$data['user_id']]['resa'] += $db->query("SELECT COUNT(systems_id) FROM stu_systems_user WHERE user_id=".$data['user_id'],1);
	}

	$result = $db->query("SELECT id FROM stu_user WHERE id>100");
	while($data=mysql_fetch_assoc($result))
	{
		$arr[$data['id']]['lat'] += $db->query("SELECT SUM(b.count) FROM stu_ships as a LEFT JOIN stu_ships_storage as b ON b.ships_id=a.id WHERE a.user_id=".$data['id']." AND b.goods_id=12",1);
		$arr[$data['id']]['lat'] += $db->query("SELECT SUM(b.count) FROM stu_colonies as a LEFT JOIN stu_colonies_storage as b ON b.colonies_id=a.id WHERE a.user_id=".$data['id']." AND b.goods_id=12",1);

		$tr = $db->query("SELECT a.offer_id,a.count,b.count as acount FROM stu_trade_goods as a LEFT JOIN stu_trade as b USING(offer_id) WHERE a.goods_id=12 AND (a.mode='0' OR a.mode='1') AND a.user_id=".$data['id']);
		while($lat = mysql_fetch_assoc($tr))
		{
			$arr[$data['id']]['lat'] += ($lat['offer_id'] > 0 ? ($lat['count']*$lat['acount']) : $lat['count']);
		}
		$tr = $db->query("SELECT give_count FROM stu_trade_ferg WHERE give_good=12 AND user_id=".$data['id']);
		while($lat = mysql_fetch_assoc($tr))
		{
			$arr[$data['id']]['lat'] += $lat['give_count'];
		}
	}

	foreach($arr as $key => $value)	$db->query("INSERT INTO stu_stats (user_id,schiffe,schiffeb,wirtschaft,population,researched,latinum) VALUES ('".$key."','".$value['ship']."','".$value['shipb']."','".$value['wirt']."','".$value['popu']."','".$value['resa']."','".$value['lat']."')");

	
	if (date("d-H") == "01-06") {
		$db->query("UPDATE `stu_user` SET vac_possible = '4' WHERE vac_possible = '3';");
		$db->query("UPDATE `stu_user` SET vac_possible = '4' WHERE vac_possible = '2';");
		$db->query("UPDATE `stu_user` SET vac_possible = '3' WHERE vac_possible = '1';");
		$db->query("UPDATE `stu_user` SET vac_possible = '2' WHERE vac_possible IS NULL;");
	}
	
}
?>
