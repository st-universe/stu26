<?php
include_once("/var/www/st-universe.eu/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;



include_once($global_path."/class/log.class.php");
$log = new log;

$time = time();

$log->deleteLogType("shieldtick");

$log->enterLog("shieldtick","start");

$ticktime = $time - 600;
$starttime = $time - 1800;

$result = $db->query("SELECT * FROM stu_ships WHERE (max_schilde > schilde) AND (crew >= min_crew) AND (lasthit <= ".$starttime.") AND (lastshieldreg <= ".$ticktime.") AND (schilde_status = 0) AND (cloak != 1);");
while($data=mysql_fetch_assoc($result))
{
	$plan =  $db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id = ".$data[plans_id]."",4);
	if (!$plan) continue;
	$regsum = $db->query("SELECT SUM(value) FROM stu_modules_special WHERE type = 'shieldreg' AND modules_id IN (".$plan[m1].", ".$plan[m2].", ".$plan[m3].", ".$plan[m4].", ".$plan[m5].", ".$plan[w1].", ".$plan[w2].", ".$plan[s1].", ".$plan[s2].")",1);

	if ($data['cloak'] == 1) $regsum = floor($regsum*0.3);
	
	if ($regsum <= 0) continue;
	
	$db->query("UPDATE stu_ships SET schilde = ".min($data[max_schilde],$data[schilde]+$regsum).", lastshieldreg = ".$time." WHERE id = ".$data[id]." LIMIT 1");
}

$log->enterLog("shieldtick","all done");

















?>
