<?php
include_once("/var/www/st-universe.eu/inc/config.inc.php");
// include_once("../../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
include_once($global_path."/inc/func.inc.php");
$db = new db;


include_once($global_path."/class/log.class.php");
$log = new log;

$time = time();

$log->deleteLogType("savepoints");

$log->enterLog("savepoints","start");

$ticktime = $time - 600;
$starttime = $time - 1800;


	function getPoints($cid,$type) {
		global $db;
		return $db->query("SELECT SUM(count) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings_effects as b on a.buildings_id = b.buildings_id WHERE a.colonies_id=".$cid." AND a.aktiv = 1 AND b.type='".$type."';",1);
	}
	
	function getAllPoints($type, $uid) {
		global $db;
		$result = $db->query("SELECT id FROM stu_colonies WHERE user_id=".$uid.";");
		
		$sum = 0;
		while($d=mysql_fetch_assoc($result)) {
			$sum += getPoints($d['id'],$type);
		}			
		
		return $sum;
	}

	function limit($uid) {
		$pc = getAllPoints("pcrew", $uid);
		$pv = getAllPoints("psupply", $uid);
		$pw = getAllPoints("pmaintain", $uid);

		$stepFleetPoints = stepFleetLimit($pc,$pv,$pw);			
			
		return $stepFleetPoints['battleships'];	
	}




$result = $db->query("SELECT * FROM stu_user WHERE 1;");
while($data=mysql_fetch_assoc($result))
{

	$limt = limit($data['id']);
	echo "<br>".$data['id']." ".$limt;
	
	$db->query("UPDATE stu_user SET conflict_points = ".$limt." WHERE id = ".$data['id'].";");
}

$log->enterLog("savepoints","all done");

?>
