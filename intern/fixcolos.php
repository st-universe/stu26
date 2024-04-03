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
	$users = $db->query("SELECT * FROM stu_user WHERE id > 10 ORDER by id ASC");
	while($u=mysql_fetch_assoc($users)) {
	
		// echo "<br>-".$u[id]."- ";
	
		$message = "Das Kolonie-Korrekturscript hat die Werte der folgenden Kolonien angepasst:<br>";
		$afix = 0;
		$result = $db->query("SELECT bev_work, bev_max, eps, max_eps, max_storage, id, user_id, name FROM stu_colonies WHERE user_id = ".$u[id]."");
		while($data=mysql_fetch_assoc($result))
		{
			// echo "<br>".$data[id]."<br>";
			// print_r($data);
			// echo "<br><br>";
			$comp = array();
			
			$comp[bev_work] = $db->query("SELECT SUM(b.bev_use) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON a.buildings_id = b.buildings_id WHERE a.colonies_id = ".$data[id]." AND a.aktiv = 1",1);
			$comp[bev_max] = $db->query("SELECT SUM(b.bev_pro) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON a.buildings_id = b.buildings_id WHERE a.colonies_id = ".$data[id]." AND a.aktiv = 1",1);
					
			// $comp[bev_free] = min(max($comp[bev_max]-$comp[bev_work],0),$data[bev_free]);
			
			$comp[eps] = 0;
			$comp[max_eps] = $db->query("SELECT SUM(b.eps) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON a.buildings_id = b.buildings_id WHERE a.colonies_id = ".$data[id]." AND a.aktiv < 2",1);
			$comp[max_storage] = $db->query("SELECT SUM(b.lager) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON a.buildings_id = b.buildings_id WHERE a.colonies_id = ".$data[id]." AND a.aktiv < 2",1);
			$comp[eps] = min($data[eps],$comp[max_eps]);
			
			// print_r($comp);
			// echo "<br><br>";
			
			$vals = "";
			$fix = 0;
			foreach($comp as $k => $v) {
				if ($data[$k] != $v) {
					$fix = 1;
					$afix = 1;
					$vals .= $k." ";
				}
			}
			
			if ($fix) {
				$db->query("UPDATE stu_colonies SET bev_work=".$comp[bev_work].", bev_max=".$comp[bev_max].", eps=".$comp[eps].", max_eps=".$comp[max_eps].", max_storage=".$comp[max_storage]." WHERE id = ".$data[id]."");
				$message .= "<br>".$data[name]." (".$data[id].")";
				// echo "<br>--------------------FIX";
			}
			

			// $db->query("DELETE FROM stu_colonies_fielddata WHERE colonies_id=".$data[id]);
			// setColonyFields($data[id],$data[fieldstring]);
		}
		
		if ($afix) {
			echo $message;		
			sendPm($u[id],$message,4);
		}
	}










echo "</body>";


?>
