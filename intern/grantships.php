<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	
	function sendShipPm($user,$type) {
		global $db;
		$text = "Hallo,\r\n\r\nich bin das Script das Schiffe bereitstellt, solange dies noch nicht automatisch in den Levelaufstieg integriert ist. Du erfuellst die Kriterien fuer das folgende Schiff, das dir soeben erstellt wurde:\r\n\r\n<b>".$type."</b>\r\n\r\nViel Spass damit.";			
		$db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('2','".$user."','".addslashes($text)."','1',NOW())");
	}
	
	
	
	$gfx = "http://www.stuniverse.de/gfx/";
	



		$result = $db->query("SELECT * FROM stu_user WHERE 1");
		// $result = $db->query("SELECT * FROM stu_user WHERE id = 102");
		while($data=mysql_fetch_assoc($result))
		{
			// if ($data[id] != 102) continue;
		
			$ships = $db->query("SELECT count(id) FROM stu_ships WHERE user_id=".$data[id],1);
			
			$scouts = $db->query("SELECT count(id) FROM stu_ships WHERE plans_id = ".(100 + $data[race])." AND user_id=".$data[id],1);
			$freighters = $db->query("SELECT count(id) FROM stu_ships WHERE plans_id = ".(120 + $data[race])." AND user_id=".$data[id],1);
			
			
			$col = $db->query("SELECT a.*,b.cx,b.cy FROM stu_colonies as a LEFT JOIN stu_systems as b on a.systems_id = b.systems_id WHERE a.user_id=".$data[id]."  ORDER by a.bev_work desc LIMIT 1",4);
			
			$rumpsknown = $db->query("SELECT count(*) FROM stu_rumps_user WHERE user_id=".$data[id],1);
			
			if ($data[level] == 3 && $scouts == 0 && $col[id]) {
				echo "<br>".$data[search_user]." (".$data[id].") - ".$scouts;			
				echo "<br> -> grant scout";		
				
				$buildplan = 100 + $data[race];
				$shipdata = getShipValuesForBuildplan($buildplan);
				$shipname = "Erkundungsschiff";
				
				$query = "INSERT INTO `stu_ships` (`user_id`, `rumps_id`, `plans_id`, `fleets_id`, `systems_id`, `cx`, `cy`, `sx`, `sy`, `direction`, `name`, `alvl`, `warp`, `warpcore`, `max_warpcore`, `warpable`, `warpfields`, `max_warpfields`, `cloak`, `cloakable`, `eps`, `max_eps`, `reaktor`, `batt`, `max_batt`, `huelle`, `max_huelle`, `schilde`, `max_schilde`, `schilde_status`, `lss_range`, `kss_range`, `traktor`, `traktormode`, `dock`, `crew`, `max_crew`, `min_crew`, `nbs`, `lss`, `trumps_id`, `replikator`, `phaser`, `cfield`, `torp_type`, `wea_phaser`, `wea_torp`, `shuttle_type`, `is_hp`, `is_rkn`, `points`, `lastmaintainance`, `still`, `maintain`, `batt_wait`, `hud`, `assigned`, `slots`) VALUES
					(".$data[id].", ".$shipdata['rumps_id'].", ".$buildplan.", 0, ".$col[systems_id].", ".$col[cx].", ".$col[cy].", ".$col[sx].", ".$col[sy].", '3', '".$shipname."', '1', '0', ".$shipdata[warpcore].", ".$shipdata[warpcore].", '1', ".$shipdata[warpfields].", ".$shipdata[warpfields].", 0, NULL, ".$shipdata[eps].", ".$shipdata[eps].", ".$shipdata[reaktor].", 0, 0, ".$shipdata[huelle].", ".$shipdata[huelle].", ".$shipdata[schilde].", ".$shipdata[schilde].", 0, ".$shipdata[lss_range].", ".$shipdata[kss_range].", 0, '', 0, 15, 15, 15, '1', '1', 0, '', 0, 1, 0, '0', '0', 0, '0', 0, '0', 0, 0, 0, 0, '1', 0, 0);";						

				$db->query($query);
				sendShipPm($data[id],$shipname);
			}

			if ($data[level] == 4 && $scouts == 0 && $col[id]) {
				echo "<br>".$data[search_user]." (".$data[id].") - ".$scouts;			
				echo "<br> -> grant scout";				

				$buildplan = 100 + $data[race];
				$shipdata = getShipValuesForBuildplan($buildplan);
				$shipname = "Erkundungsschiff";
				
				$query = "INSERT INTO `stu_ships` (`user_id`, `rumps_id`, `plans_id`, `fleets_id`, `systems_id`, `cx`, `cy`, `sx`, `sy`, `direction`, `name`, `alvl`, `warp`, `warpcore`, `max_warpcore`, `warpable`, `warpfields`, `max_warpfields`, `cloak`, `cloakable`, `eps`, `max_eps`, `reaktor`, `batt`, `max_batt`, `huelle`, `max_huelle`, `schilde`, `max_schilde`, `schilde_status`, `lss_range`, `kss_range`, `traktor`, `traktormode`, `dock`, `crew`, `max_crew`, `min_crew`, `nbs`, `lss`, `trumps_id`, `replikator`, `phaser`, `cfield`, `torp_type`, `wea_phaser`, `wea_torp`, `shuttle_type`, `is_hp`, `is_rkn`, `points`, `lastmaintainance`, `still`, `maintain`, `batt_wait`, `hud`, `assigned`, `slots`) VALUES
					(".$data[id].", ".$shipdata['rumps_id'].", ".$buildplan.", 0, ".$col[systems_id].", ".$col[cx].", ".$col[cy].", ".$col[sx].", ".$col[sy].", '3', '".$shipname."', '1', '0', ".$shipdata[warpcore].", ".$shipdata[warpcore].", '1', ".$shipdata[warpfields].", ".$shipdata[warpfields].", 0, NULL, ".$shipdata[eps].", ".$shipdata[eps].", ".$shipdata[reaktor].", 0, 0, ".$shipdata[huelle].", ".$shipdata[huelle].", ".$shipdata[schilde].", ".$shipdata[schilde].", 0, ".$shipdata[lss_range].", ".$shipdata[kss_range].", 0, '', 0, 15, 15, 15, '1', '1', 0, '', 0, 1, 0, '0', '0', 0, '0', 0, '0', 0, 0, 0, 0, '1', 0, 0);";						

				$db->query($query);
				sendShipPm($data[id],$shipname);
			}			
			
			if ($data[level] == 4 && $freighters == 0 && $col[id]) {
				echo "<br>".$data[search_user]." (".$data[id].") - ".$freighters;			
				echo "<br> -> grant freighter";				

				$buildplan = 120 + $data[race];
				$shipdata = getShipValuesForBuildplan($buildplan);
				$shipname = "Frachter";
				
				$query = "INSERT INTO `stu_ships` (`user_id`, `rumps_id`, `plans_id`, `fleets_id`, `systems_id`, `cx`, `cy`, `sx`, `sy`, `direction`, `name`, `alvl`, `warp`, `warpcore`, `max_warpcore`, `warpable`, `warpfields`, `max_warpfields`, `cloak`, `cloakable`, `eps`, `max_eps`, `reaktor`, `batt`, `max_batt`, `huelle`, `max_huelle`, `schilde`, `max_schilde`, `schilde_status`, `lss_range`, `kss_range`, `traktor`, `traktormode`, `dock`, `crew`, `max_crew`, `min_crew`, `nbs`, `lss`, `trumps_id`, `replikator`, `phaser`, `cfield`, `torp_type`, `wea_phaser`, `wea_torp`, `shuttle_type`, `is_hp`, `is_rkn`, `points`, `lastmaintainance`, `still`, `maintain`, `batt_wait`, `hud`, `assigned`, `slots`) VALUES
					(".$data[id].", ".$shipdata['rumps_id'].", ".$buildplan.", 0, ".$col[systems_id].", ".$col[cx].", ".$col[cy].", ".$col[sx].", ".$col[sy].", '3', '".$shipname."', '1', '0', ".$shipdata[warpcore].", ".$shipdata[warpcore].", '1', ".$shipdata[warpfields].", ".$shipdata[warpfields].", 0, NULL, ".$shipdata[eps].", ".$shipdata[eps].", ".$shipdata[reaktor].", 0, 0, ".$shipdata[huelle].", ".$shipdata[huelle].", ".$shipdata[schilde].", ".$shipdata[schilde].", 0, ".$shipdata[lss_range].", ".$shipdata[kss_range].", 0, '', 0, 15, 15, 15, '1', '1', 0, '', 0, 1, 0, '0', '0', 0, '0', 0, '0', 0, 0, 0, 0, '1', 0, 0);";						

				$db->query($query);
				sendShipPm($data[id],$shipname);
			}				

			if ($data[level] == 4 && $rumpsknown < 3) {
				echo "<br>".$data[search_user]." (".$data[id].") - ".$rumpsknown;			
				echo "<br> -> grant rumps";				

				$rump = 3400 + $data[race];
				if ($db->query("SELECT count(*) FROM stu_rumps_user WHERE rumps_id = ".$rump." AND user_id=".$data[id],1) < 1)
					$db->query("INSERT INTO `stu_rumps_user` (`rumps_id` ,`user_id`) VALUES ('".$rump."', '".$data[id]."');");
				$rump = 6500 + $data[race];
				if ($db->query("SELECT count(*) FROM stu_rumps_user WHERE rumps_id = ".$rump." AND user_id=".$data[id],1) < 1)
					$db->query("INSERT INTO `stu_rumps_user` (`rumps_id` ,`user_id`) VALUES ('".$rump."', '".$data[id]."');");
				$rump = 6700 + $data[race];
				if ($db->query("SELECT count(*) FROM stu_rumps_user WHERE rumps_id = ".$rump." AND user_id=".$data[id],1) < 1)
					$db->query("INSERT INTO `stu_rumps_user` (`rumps_id` ,`user_id`) VALUES ('".$rump."', '".$data[id]."');");					
			}							
		}







?>
