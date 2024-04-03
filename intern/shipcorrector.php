<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	
	function sendShipPm($user,$arr) {
		global $db;
		$text = "Das Schiffs-Korrektur-Skript hat folgende Schiffe angepasst:\r\n";		
		foreach($arr as $e) {
			$text .= "\r\n".$e[0]." (".$e[1].")";
		}
		$text .= "\r\n\r\nDies ist wahrscheinlich das Resultat einer Balancing-Aenderung und kein Grund zur Besorgnis.";
		$db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('2','".$user."','".addslashes($text)."','3',NOW())");
	}
	

	
	$gfx = "http://www.stuniverse.de/gfx/";
	

		$valuechecks = array();

		array_push($valuechecks, array("warpcore","max_warpcore",array("warpcore","max_warpcore")) );
		array_push($valuechecks, array("huelle","max_huelle",array("huelle","max_huelle")) );
		array_push($valuechecks, array("schilde","max_schilde",array("schilde","max_schilde")) );
		array_push($valuechecks, array("warpfields","max_warpfields",array("warpfields","max_warpfields")) );
		
		array_push($valuechecks, array("min_crew","min_crew",array("min_crew")) );
		array_push($valuechecks, array("max_crew","max_crew",array("max_crew","crew")) );

		array_push($valuechecks, array("lss_range","lss_range",array("lss_range")) );
		array_push($valuechecks, array("kss_range","kss_range",array("kss_range")) );

		$pms = array();
		$lastid = -1;
		$shiplist = array();
		$result = $db->query("SELECT * FROM stu_ships WHERE user_id > 1 AND rumps_id < 9000 ORDER by user_id ASC");
		while($data=mysql_fetch_assoc($result))
		{
			$res = $data;
			$plan = getShipValuesForBuildplan($data['plans_id']);
			// $plan[huelle] = 180;
				

			$changed = false;

			foreach($valuechecks as $check) {
				$pkey = $check[0];
				$dkey = $check[1];
				$vals = $check[2];
				
				if ($plan[$pkey] != $data[$dkey]) {
					$changed = true;
					// echo "<br>Key: ".$pkey;
					foreach ($vals as $vkey) {
						$res[$vkey] = $plan[$pkey];
					}
				}
			}
			
			if ($changed) {
			
				// save values
			
				if ($data[user_id] != $lastid) {
					

					if ($lastid > 0) {
						// sendShipPm($lastid,$shiplist);
						echo "<br>";print_r($shiplist);echo "<br>";
						$shiplist = array();
					}
					$lastid = $data[user_id];
				}
				
				array_push($shiplist,array($data[name],$data[id]));

			}
			// echo "<br><br>";print_r($plan);
			// echo "<br><br>";print_r($data);
			// echo "<br><br>";print_r($res);

			// break;
		}
		if ($lastid > 0) {
			// sendShipPm($lastid,$shiplist);
			echo "<br>";print_r($shiplist);echo "<br>";
			$shiplist = array();
		}



?>
