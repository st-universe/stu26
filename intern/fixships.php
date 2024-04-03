<?php

	$gfx = "http://www.stuniverse.de/gfx/";
	

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;


	
	

echo "<html>
<head>
	<title>Star Trek Universe</title>
<link rel=\"STYLESHEET\" type=\"text/css\" href=../gfx/css/6.css>
</head>
<body>";



	function sendPm($user,$text,$type) {
		global $db;
		$db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('2','".$user."','".addslashes($text)."','".$type."',NOW())");
	}


	// $result = $db->query("SELECT * FROM stu_colonies WHERE 1");
	$users = $db->query("SELECT * FROM stu_user WHERE id > 100 ORDER by id ASC");
	// $users = $db->query("SELECT * FROM stu_user WHERE id = 102 ORDER by id ASC");
	while($u=mysql_fetch_assoc($users)) {
	
		// echo "<br>-".$u[id]."- ";
	
		$message = "Das Schiffs-Korrekturscript hat die Werte der folgenden Schiffe angepasst:<br>";
		$afix = 0;
		$result = $db->query("SELECT s.*,p.m1, p.m2, p.m3, p.m4, p.m5, p.w1, p.w2, p.s1, p.s2 FROM stu_ships as s LEFT JOIN stu_ships_buildplans as p ON s.plans_id = p.plans_id WHERE s.user_id = ".$u[id]."");
		while($data=mysql_fetch_assoc($result))
		{
			$comp = array();
			
			// $comp[bev_work] = $db->query("SELECT SUM(b.bev_use) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON a.buildings_id = b.buildings_id WHERE a.colonies_id = ".$data[id]." AND a.aktiv = 1",1);
			// $comp[bev_max] = $db->query("SELECT SUM(b.bev_pro) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON a.buildings_id = b.buildings_id WHERE a.colonies_id = ".$data[id]." AND a.aktiv = 1",1);
			
			// $comp[eps] = 0;
			// $comp[max_eps] = $db->query("SELECT SUM(b.eps) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON a.buildings_id = b.buildings_id WHERE a.colonies_id = ".$data[id]." AND a.aktiv < 2",1);
			// $comp[max_storage] = $db->query("SELECT SUM(b.lager) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON a.buildings_id = b.buildings_id WHERE a.colonies_id = ".$data[id]." AND a.aktiv < 2",1);
			// $comp[eps] = min($data[eps],$comp[max_eps]);
			
			$shipdata = getShipValuesForBuildplan($data['plans_id']);


			$comp['max_warpcore'] = $shipdata['warpcore'];
			$cons['max_warpcore'] = array('warpcore' => max($shipdata['warpcore'] - ($data['max_warpcore'] - $data['warpcore']),0) );
			
			$comp['max_eps'] = $shipdata['eps'];
			$cons['max_eps'] = array('eps' => max($shipdata['eps'] - ($data['max_eps'] - $data['eps']),0) );			

			$comp['max_batt'] = $shipdata['batt'];
			$cons['max_batt'] = array('batt' => max($shipdata['batt'] - ($data['max_batt'] - $data['batt']),0) );	

			$comp['max_huelle'] = $shipdata['huelle'];
			$cons['max_huelle'] = array('huelle' => max($shipdata['huelle'] - ($data['max_huelle'] - $data['huelle']),0) );

			$comp['max_schilde'] = $shipdata['schilde'];
			$cons['max_schilde'] = array('schilde' => max($shipdata['schilde'] - ($data['max_schilde'] - $data['schilde']),0) );
			
			$comp['max_warpfields'] = $shipdata['warpfields'];
			$cons['max_warpfields'] = array('warpfields' => max($shipdata['warpfields'] - ($data['max_warpfields'] - $data['warpfields']),0) );
			
			$comp['lss_range'] = $shipdata['lss_range'];
			$cons['lss_range'] = array();
			$comp['kss_range'] = $shipdata['kss_range'];
			$cons['kss_range'] = array();
			
			$comp['storage'] = $shipdata['storage'];
			$cons['storage'] = array();			

			$comp['crew'] = min($shipdata['max_crew'],$data['crew']);
			$cons['crew'] = array();				

			$comp['min_crew'] = $shipdata['min_crew'];
			$cons['min_crew'] = array();	
			
			$comp['max_crew'] = $shipdata['max_crew'];
			$cons['max_crew'] = array('crew' => $shipdata['max_crew']);	
						
			$comp['reaktor'] = $shipdata['reaktor'];
			$cons['reaktor'] = array();
			
			$t = $data;
			$up = array();
			
			// print_r($data);
			// echo "<br><br>SData: ";
			// print_r($shipdata);
			// echo "<br><br>Comp: ";			
			// print_r($comp);
			// echo "<br><br>Cons: ";
			// print_r($cons);
			// echo "<br><br>";			
			// echo "<br><br>";
			
			$vals = "";
			$fix = 0;
			
			$updmsg = "";
			foreach($comp as $k => $v) {
				if ($data[$k] != $v) {
					$fix = 1;
					$afix = 1;
					$vals .= $k." ";
					
					// echo "<br>".$k.": ".$data[$k]." => ".$v;
					$updmsg .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$k.": ".$data[$k]." => ".$v;
					$t[$k] = $v;
					$up[$k] = $v;
					foreach($cons[$k] as $ck => $cv) {
						
						// echo "<br>".$ck.": ".$data[$ck]." => ".$cv;
						$updmsg .= "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$ck.": ".$data[$ck]." => ".$cv;
						$t[$ck] = $cv;						
						$up[$ck] = $cv;
					}
				}
			}
			
			// echo "<br><br>";				
			// print_r($data);
			// echo "<br><br>";				
			// print_r($t);
			// echo "<br><br>";			
			// print_r($u);
			// echo "<br><br>";			
			
			if ($fix) {
				// $db->query("UPDATE stu_colonies SET bev_work=".$comp[bev_work].", bev_max=".$comp[bev_max].", eps=".$comp[eps].", max_eps=".$comp[max_eps].", max_storage=".$comp[max_storage]." WHERE id = ".$data[id]."");
				$message .= "<br>".$data[name]." (".$data[id].")".$updmsg;
				
				$updstr = "UPDATE stu_ships SET ";
				
				$i = 1;
				foreach($up as $k => $v) {
					$updstr .= " ".$k."=".$v."";
					if ($i < count($up)) $updstr .= ",";
					$i++;
				}
				$updstr .= " WHERE id=".$data[id]." LIMIT 1;";
				// echo $updstr;
				$db->query($updstr);
			}
			
			// break;
		}
		// sendPm($u[id],"ddssdsd",1);
		if ($afix) {
			// echo $message;		
			sendPm($u[id],$message,3);
		}
	}










echo "</body>";


?>
