<?php
include_once("/var/www/st-universe.eu/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

include_once($global_path."/inc/func.inc.php");



		
	function isRelevant($region,$faction) {
		global $regions;
		
		$nbs = $region['neighbours'];

		if ($region['status'] == "fixed") return false;
		if ($region['status'] == "objective") return false;
		
		if ($region['faction'] == $faction) return true;
		if ($region['attacker'] == $faction) return true;
		
		foreach($nbs as $n) {
			if ($regions[$n]['faction'] == $faction) return true;
		}
		
		return false;
	}


	function sgetSectors() {
		
		global $db, $regions;
		
		$systems = array();
		$qry = $db->query("SELECT * FROM stu_systems WHERE 1;");
		while ($row = mysql_fetch_assoc($qry)) {
			$systems[$row['systems_id']] = $row;
		}
		
		$qry = $db->query("SELECT * FROM stu_map_regions WHERE 1;");
		
		$regions = array();
		while ($row = mysql_fetch_assoc($qry)) {
			
			if ($row['status'] == "fixed" || $row['status'] == "objective") continue;

			$neighbours = array();
			if ($row['neighbours'] != "") {	
				$neighbours = explode(",",$row['neighbours']);
			}
			$sysids = array();
			if ($row['systems'] != "") {	
				$sysids = explode(",",$row['systems']);
			}
			
			$syslist = array();
			foreach($sysids as $id) array_push($syslist,$systems[$id]);
			

			
			$sector = array();
			$sector['name'] 		= $row['name'];
			$sector['id'] 			= $row['id'];
			$sector['faction'] 		= $row['faction'];
			$sector['attacker']		= $row['attacker'];
			$sector['status'] 		= $row['status'];
			$sector['neighbours'] 	= $neighbours;
			$sector['systems'] 		= $syslist;
			$sector['systemstring'] = $row['systems'];
			$sector['counter'] 		= $row['counter'];
			$sector['relevant'][1] 	= 0;
			$sector['relevant'][2] 	= 0;
			$sector['relevant'][3] 	= 0;
			
			// $sector['priority'] = $this->sectorPriority($sector);
			
			$regions[$sector['id']] = $sector;
		}

		foreach($regions as $id => $region) {
			
			$regions[$id]['relevant'][1] = isRelevant($region,1);
			$regions[$id]['relevant'][2] = isRelevant($region,2);
			$regions[$id]['relevant'][3] = isRelevant($region,3);
			
		}
		
		
		$race = $_SESSION['race'];
		foreach($regions as $id => $region) {
			
			if ($region['systemstring'] != "") {	
			
				$region['ships'][1] = 0;
				$region['ships'][2] = 0;
				$region['ships'][3] = 0;
			
				$s[1] = $db->query("SELECT SUM(r.fleetpoints) FROM stu_ships AS s LEFT JOIN stu_user as u ON s.user_id = u.id LEFT JOIN stu_rumps as r on s.rumps_id = r.rumps_id LEFT JOIN stu_fleets as f on s.fleets_id = f.fleets_id WHERE s.systems_id IN (".$region['systemstring'].") AND s.fleets_id > 0 AND f.faction=1;",1);	
				$s[2] = $db->query("SELECT SUM(r.fleetpoints) FROM stu_ships AS s LEFT JOIN stu_user as u ON s.user_id = u.id LEFT JOIN stu_rumps as r on s.rumps_id = r.rumps_id LEFT JOIN stu_fleets as f on s.fleets_id = f.fleets_id WHERE s.systems_id IN (".$region['systemstring'].") AND s.fleets_id > 0 AND f.faction=2;",1);
				$s[3] = $db->query("SELECT SUM(r.fleetpoints) FROM stu_ships AS s LEFT JOIN stu_user as u ON s.user_id = u.id LEFT JOIN stu_rumps as r on s.rumps_id = r.rumps_id LEFT JOIN stu_fleets as f on s.fleets_id = f.fleets_id WHERE s.systems_id IN (".$region['systemstring'].") AND s.fleets_id > 0 AND f.faction=3;",1);							

				if ($region['relevant'][1]) $region['ships'][1] = $s[1];
				if ($region['relevant'][2]) $region['ships'][2] = $s[2];
				if ($region['relevant'][3]) $region['ships'][3] = $s[3];
			}
			
			$dominant = 0;
			if (($region['ships'][1] > 90) && ($region['ships'][1] > (1.5 * ($region['ships'][2]+$region['ships'][3])))) $dominant = 1;
			if (($region['ships'][2] > 90) && ($region['ships'][2] > (1.5 * ($region['ships'][1]+$region['ships'][3])))) $dominant = 2;
			if (($region['ships'][3] > 90) && ($region['ships'][3] > (1.5 * ($region['ships'][1]+$region['ships'][2])))) $dominant = 3;			
			
			$region['dominant'] = $dominant;
			
			$relevantSectors[$region['id']] = $region;				
			
		}
		
		return $relevantSectors;
	}
	
	
	function writeHistory($name, $faction) {
		global $db;

		if ($faction > 0) {
			$message = getofficialfactionname($faction)." hat die Kontrolle über die Region ".$name." erlangt.";
		} else {
			$message = "Die Region ".$name." ist nicht länger unter Kontrolle einer Großmacht.";
		}
		$db->query("INSERT INTO stu_history (message,date,type,ft_msg,coords_x,coords_y,user_id) VALUES ('".addslashes($message)."',NOW(),'5','".strip_tags(str_replace("'","",stripslashes($message)))."','0','0','0')");
	}
	

$time = time();


	function updateSector($sector,$change) {
		global $db;
		$sector['faction'] = $change['faction'];
		$sector['counter'] = $change['counter'];
		$sector['status'] = $change['status'];
		$sector['attacker'] = $change['attacker'];
		$db->query("UPDATE stu_map_regions SET faction='".$sector['faction']."', counter='".$sector['counter']."', status='".$sector['status']."', attacker='".$sector['attacker']."' WHERE id='".$sector['id']."' LIMIT 1;");
	}


	$sectors = sgetSectors();


	// print_r($sectors);


	foreach($sectors as $sector) {
		
		
		
		$change = sectorStateChange($sector['status'], $sector['counter'], $sector['faction'], $sector['attacker'], $sector['dominant']);
		if ($sector['faction'] != $change['faction']) writeHistory($sector['name'],$change['faction']);
		
		$update = false;
		if ($sector['faction'] != $change['faction'])			$update = true;
		if ($sector['counter'] != $change['counter'])			$update = true;
		if ($sector['status'] != $change['status'])				$update = true;
		if ($sector['attacker'] != $change['attacker'])			$update = true;
		
		
		
		if ($update) {
			updateSector($sector,$change);
			echo $sector['name']." ".print_r($change,true)."<br>";
		}
			
	}
	

// $ticktime = $time - 600;
// $starttime = $time - 1800;

// $result = $db->query("SELECT * FROM stu_ships WHERE (max_schilde > schilde) AND (crew >= min_crew) AND (lasthit <= ".$starttime.") AND (lastshieldreg <= ".$ticktime.") AND (schilde_status = 0) AND (cloak != 1);");
// while($data=mysql_fetch_assoc($result))
// {
	// $plan =  $db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id = ".$data[plans_id]."",4);
	// if (!$plan) continue;
	// $regsum = $db->query("SELECT SUM(value) FROM stu_modules_special WHERE type = 'shieldreg' AND modules_id IN (".$plan[m1].", ".$plan[m2].", ".$plan[m3].", ".$plan[m4].", ".$plan[m5].", ".$plan[w1].", ".$plan[w2].", ".$plan[s1].", ".$plan[s2].")",1);

	// if ($data['cloak'] == 1) $regsum = floor($regsum*0.3);
	
	// if ($regsum <= 0) continue;
	
	// $db->query("UPDATE stu_ships SET schilde = ".min($data[max_schilde],$data[schilde]+$regsum).", lastshieldreg = ".$time." WHERE id = ".$data[id]." LIMIT 1");
// }



















?>
