<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	
	function sendPm($user) {
		global $db;
		$text = "Hallo,\r\n\r\nich bin das Script das Tundra-Kolonien korrigiert. Terraforming zu Wiese war nicht vorgesehen, deshalb wurden alle Wiesenfelder auf Tundra-Kolonien zu Tundra ersetzt. Dir sollte kein Schaden dadurch entstehen.";
		$db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('2','".$user."','".addslashes($text)."','1',NOW())");
	}
	
	
	
	$gfx = "http://www.stuniverse.de/gfx/";
	

		$colos = array();

		$result = $db->query("SELECT a.*,b.* FROM `stu_colonies_fielddata` as a left join stu_colonies as b on a.colonies_id = b.id WHERE a.type = 1 AND (b.colonies_classes_id = 205 OR b.colonies_classes_id = 305)");
		// $result = $db->query("SELECT * FROM stu_user WHERE id = 102");
		while($data=mysql_fetch_assoc($result))
		{
			// print_r($data);
			
			$ele = array();
			
			$ele[id] = $data['colonies_id'];
			$ele[user] = $data['user_id'];
			$add = true;
			foreach ($colos as $a) {
				if ($a[id] == $ele[id]) {
					$add = false;
					break;
				}
			}
			if ($add) array_push($colos,$ele);
			
		}

		print_r($colos);
		
		foreach ($colos as $a) {
			
			$db->query("UPDATE stu_colonies_fielddata SET type = 18 WHERE type = 1 AND colonies_id = ".$a[id]."");
						
			sendPm($a[user_id]);
		}
		
		$db->query("UPDATE stu_colonies_fielddata SET buildings_id = 102 WHERE type = 18 AND buildings_id = 2");





?>
