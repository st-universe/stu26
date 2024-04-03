<?php
class game
{
	function game()
	{
		global $db,$_SESSION;
		$this->db = $db;
		$this->uid = $_SESSION["uid"];
		$this->sess = $_SESSION;
	}

	function gethistorycountbytype($t) { return $this->db->query("SELECT COUNT(message) FROM stu_history WHERE type=".$t,1); }

	function gethistory($c,$t)
	{
		if ($t < 1 || $t > 5) $t = 1;
		if (!is_numeric($c) || $c < 50) $c = 50;
		return $this->db->query("SELECT message,UNIX_TIMESTAMP(date) as date_tsp,type FROM stu_history WHERE type=".$t." ORDER BY date DESC LIMIT ".$c);
	}

	function loadtradeposts($qry) { $this->result = $this->db->query("SELECT a.id,a.rumps_id,a.cx,a.cy,a.name,COUNT(c.id) as pc FROM stu_ships as a LEFT JOIN stu_systems as b ON b.cx>=a.cx-20 AND b.cx<a.cx+20 AND b.cy>=a.cy-20 AND b.cy<=a.cy+20 LEFT JOIN stu_colonies as c ON b.systems_id=c.systems_id AND c.user_id=1 AND (c.colonies_classes_id=1 OR c.colonies_classes_id=2 OR c.colonies_classes_id=3) WHERE (".$qry.") AND is_hp='1' GROUP BY a.id"); }
	
	function loadClosestColonies($x,$y) { $this->result = $this->db->query("SELECT a.id,a.colonies_classes_id,a.planet_name,b.name as sysname,b.type as system_type,b.cx,b.cy,c.name as classname, (ABS(b.cx-".$x.") + ABS(b.cy-".$y.")) as dist FROM stu_colonies as a LEFT JOIN stu_systems as b on a.systems_id = b.systems_id LEFT JOIN stu_colonies_classes as c on a.colonies_classes_id = c.colonies_classes_id WHERE a.user_id = 1 AND (a.colonies_classes_id = 201 OR a.colonies_classes_id = 202 OR a.colonies_classes_id = 203) ORDER BY `dist` ASC LIMIT 15"); }

	function getship()
	{
		global $_SESSION;
		if ($_SESSION['level'] != 0)
		{
			meldung("Diese Leistungen steht nur Siedlern mit Kolonisationsstufe 0 zur Verfügung");
			die;
		}
		$data = $this->db->query("SELECT cx,cy FROM stu_ships WHERE is_hp='1' AND id=".$_GET["hp"],4);
		if ($data == 0) die();
		$ret = $this->createship($data['cx'],$data['cy'],$this->uid);
		$_SESSION['level'] = 1;
		$this->db->query("UPDATE stu_user SET level='1' WHERE id=".$this->uid);
		return "Kolonisationsschiff beantragt und bereit";
	}

	function createship($cx,$cy,$user)
	{
		$id = $this->db->query("INSERT INTO stu_ships (user_id,rumps_id,plans_id,cx,cy,name,warpable,warpcore,eps,max_eps,batt,max_batt,huelle,max_huelle,schilde,max_schilde,lss_range,kss_range,crew,max_crew,min_crew,lss,phaser,cfield,cloak,warp) VALUES 
		('".$user."','1','1','".$cx."','".$cy."','Kolonisationsschiff','1','500','50','50','100','30','18','18','12','12','3','2','5','10','3','1','0','1','0','1')",5);
		return $id;
	}

	
	
	function colonize($cid)
	{
		global $_SESSION;	
		$data = $this->db->query("SELECT a.id,a.colonies_classes_id,a.user_id,a.sx,a.sy,a.systems_id,b.is_moon,b.level,b.research_id FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) WHERE a.id=".$cid." LIMIT 1",4);
		if ($data == 0) return;
		if ($data[colonies_classes_id] > 203) return;
		

		

		if ($_SESSION['level'] != 0)
		{
			meldung("Diese Leistung steht nur Siedlern mit Kolonisationsstufe 0 zur Verfügung");
			die;
		}
		
		if ($data['user_id'] != 1) return;

		$field = $this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE type=1 AND colonies_id=".$cid." ORDER BY RAND() LIMIT 1",1);
		
		if (!$field || $field == 0) return $cid;
		
		if ($this->sess['level'] == 0)
		{
			$this->db->query("UPDATE stu_user SET level='1' WHERE id=".$this->uid." LIMIT 1");
			global $_SESSION;
			$_SESSION['level'] = 1;
		}

		$this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=1,integrity=120,aktiv=1 WHERE colonies_id=".$cid." AND field_id=".$field." LIMIT 1");
		$this->db->query("UPDATE stu_colonies SET user_id=".$this->uid.",faction=".$_SESSION['race'].",name='Kolonie',bev_free=50,max_eps=150,max_storage=5000,bev_max=50,eps=150,einwanderung='0' WHERE id=".$cid." LIMIT 1");
		$this->colupperstorage($data['id'],1,200);
		$this->colupperstorage($data['id'],2,800);
		return "Der Planet wurde kolonisiert";
	}
	
	
	function colupperstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_colonies_storage SET count=count+".$count." WHERE colonies_id=".$id." AND goods_id=".$good." LIMIT 1",6);
		if ($result == 0) $this->db->query("INSERT INTO stu_colonies_storage (colonies_id,goods_id,count) VALUES ('".$id."','".$good."','".$count."')");
	}
	
	
	
	function getModSpecialTypes() {
		$d = $this->db->query("SELECT DISTINCT(subtype) FROM stu_modules WHERE type=7 AND viewable='1'",0);
		
		$arr = array();
			
		while($m=mysql_fetch_assoc($d)) array_push($arr,$m[subtype]);
		return $arr;
	}
	
	function getModSpecialValues($modtype = 7) {

		$res = array();

		foreach ($this->getModSpecialTypes() as $subtype) {
			$res[$subtype] = array();
			
			$mdata = $this->db->query("SELECT * FROM stu_modules WHERE type=".$modtype." AND subtype = '".$subtype."' AND viewable='1'");
	
			while($m=mysql_fetch_assoc($mdata)) {
			
			
				$ele = array();
				$ele[name] = $m[name];
				$ele[id] = $m[module_id];
				$ele[specials] = array();
				
				$sdata = $this->db->query("SELECT * FROM stu_modules_special WHERE modules_id=".$m[module_id]."");
				while($s=mysql_fetch_assoc($sdata)) {
					array_push($ele[specials],$s);
				}
				array_push($res[$subtype],$ele);
			}
		}
		return $res;
	}
	
	function getModValues($modtype) {
	
		$res = array();
		$res[1] = array();
		$res[2] = array();
		$res[3] = array();
		$res[4] = array();
		if ($modtype != 0) {
			$mdata = $this->db->query("SELECT * FROM stu_modules WHERE type=".$modtype." AND viewable='1'");
	
			while($m=mysql_fetch_assoc($mdata)) {
			
			
				$ele = array();
				$ele[name] = $m[name];
				$ele[id] = $m[module_id];
				$ele[specials] = array();
				$ele[cost] = array();
				$ele[ecost] = $m['ecost'];
				$sdata = $this->db->query("SELECT * FROM stu_modules_special WHERE modules_id=".$m[module_id]."");
				while($s=mysql_fetch_assoc($sdata)) {
					array_push($ele[specials],$s);
				}

				$cdata = $this->db->query("SELECT goods_id, count FROM stu_modules_cost WHERE module_id=".$m[module_id]." ORDER BY goods_id ASC;");
				while($c=mysql_fetch_assoc($cdata)) {
					// array_push($ele[cost],$goods_id);
					$ele[cost][$c['goods_id']] = $c['count'];
				}
				
				if ($m[type] == 6) {
					$wdata = $this->db->query("SELECT * FROM stu_weapons WHERE module_id=".$m[module_id]." LIMIT 1;",4);
					$ele[weapon] = $wdata;
				}
				array_push($res[$m[level]],$ele);
			}
	
		}
		return $res;
	}
	
	function getTorpedoValues() {
	
		$res = array();


			$mdata = $this->db->query("SELECT * FROM stu_torpedo_types WHERE 1;");
	
			while($m=mysql_fetch_assoc($mdata)) {
			

				array_push($res,$m);
			}
	
		
		return $res;
	}	
	
	
	function loadfreem()
	{
		$data = $this->db->query("SELECT cx,cy FROM stu_ships WHERE plans_id=1 AND user_id=".$this->uid,4);
		$this->result = $this->db->query("SELECT a.sx,a.sy,a.colonies_classes_id,b.cx,b.cy,b.name FROM stu_colonies as a LEFT JOIN stu_systems as b USING(systems_id) LEFT JOIN stu_map as c ON c.cx=b.cx AND c.cy=b.cy WHERE c.faction_id=0 AND a.user_id=1 AND (a.colonies_classes_id=1 OR a.colonies_classes_id=2 OR a.colonies_classes_id=3) AND b.cx BETWEEN ".($data[cx]-50)." AND ".($data[cx]+50)." AND b.cy BETWEEN ".($data[cy]-50)." AND ".($data[cy]+50)." ORDER BY b.cx,b.cy,a.sx,a.sy");
	}

	function loadslist($type,$way,$page,$ses="")
	{
		switch ($type)
		{
			default:
				$sort = "a.id";
			case 1:
				$sort = "a.id";
				break;
			case 2:
				$sort = "a.search_user";
				break;
			case 3:
				$sort = "a.allys_id";
				break;
			case 5:
				$sort = "a.level";
				break;
			case 6:
				$sort = "a.race";
				break;
		}
		switch ($way)
		{
			default:
				$order = "asc";
			case 1:
				$order = "asc";
				break;
			case 2:
				$order = "desc";
				break;
		}
		if (!check_int($page)) $page = 1;
		if (check_int($ses)) $seadd = " AND id=".$ses;
		elseif (strlen($ses) > 2) $seadd = " AND search_user LIKE '%".$ses."%'";
		$this->result = $this->db->query("SELECT a.id,a.user,a.allys_id,a.level,a.race,a.subrace,b.name FROM stu_user as a LEFT JOIN stu_allylist as b USING(allys_id) WHERE a.aktiv='1' AND ISNULL(a.npc_type) AND a.id>100".$seadd." ORDER BY ".$sort." ".$order." LIMIT ".(($page-1)*50).",50");
		$this->sc = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE aktiv='1' AND ISNULL(npc_type) AND id>100".$seadd,1);
	}

	function getcurrentround() { return $this->db->query("SELECT runde,UNIX_TIMESTAMP(start) as start FROM stu_game_rounds ORDER BY runde DESC LIMIT 1",4); }

	function getnextlevel()
	{
		global $_SESSION;

		if ($_SESSION[level] >= 5) return "Der momentan höchste Level wurde bereits erreicht"; 
		
		$critarr = array();
		switch($_SESSION[level]) {
		
			case 1:		
				array_push($critarr,checkLevelCriterion($_SESSION['level'],1,$_SESSION['uid']));
				array_push($critarr,checkLevelCriterion($_SESSION['level'],2,$_SESSION['uid']));
				array_push($critarr,checkLevelCriterion($_SESSION['level'],3,$_SESSION['uid']));
				array_push($critarr,checkLevelCriterion($_SESSION['level'],4,$_SESSION['uid']));
				break;
	
			case 2:		
				array_push($critarr,checkLevelCriterion($_SESSION['level'],1,$_SESSION['uid']));
				array_push($critarr,checkLevelCriterion($_SESSION['level'],2,$_SESSION['uid']));
				break;
			case 3:		
				array_push($critarr,checkLevelCriterion($_SESSION['level'],1,$_SESSION['uid']));
				array_push($critarr,checkLevelCriterion($_SESSION['level'],2,$_SESSION['uid']));
				array_push($critarr,checkLevelCriterion($_SESSION['level'],3,$_SESSION['uid']));
				break;		
			case 4:		
				array_push($critarr,checkLevelCriterion($_SESSION['level'],1,$_SESSION['uid']));
				array_push($critarr,checkLevelCriterion($_SESSION['level'],2,$_SESSION['uid']));
				array_push($critarr,checkLevelCriterion($_SESSION['level'],3,$_SESSION['uid']));
				break;	
			default: return "Kein Levelaufstieg möglich.";
		}
		
		$alldone = true;
		foreach($critarr as $v) if (!$v['done']) $alldone = false;

		if (!$alldone) return "Neues Level wurde nicht erreicht!";
		
		$_SESSION[level] = $_SESSION[level]+1;
		$this->db->query("UPDATE stu_user SET level='".$_SESSION[level]."' WHERE id=".$this->uid);

		$ships = $this->db->query("SELECT count(id) FROM stu_ships WHERE user_id=".$this->uid,1);
		
		$scouts = $this->db->query("SELECT count(id) FROM stu_ships WHERE plans_id = ".(100 + $_SESSION[race])." AND user_id=".$this->uid,1);
		$freighters = $this->db->query("SELECT count(id) FROM stu_ships WHERE plans_id = ".(120 + $data[race])." AND user_id=".$this->uid,1);
		
		
		$col = $this->db->query("SELECT a.*,b.cx,b.cy FROM stu_colonies as a LEFT JOIN stu_systems as b on a.systems_id = b.systems_id WHERE a.user_id=".$this->uid." ORDER by a.bev_work desc LIMIT 1",4);
		
			
			
		if (($_SESSION[level] >= 3) && ($this->db->query("SELECT count(id) FROM stu_ships WHERE plans_id = ".(100 + $_SESSION[race])." AND user_id=".$this->uid,1) == 0) && $col[id]) {

			$buildplan = 100 + $_SESSION[race];
			$shipdata = getShipValuesForBuildplan($buildplan);
			$shipname = "Erkundungsschiff";
			
			$query = "INSERT INTO `stu_ships` (`user_id`, `rumps_id`, `plans_id`, `fleets_id`, `systems_id`, `cx`, `cy`, `sx`, `sy`, `direction`, `name`, `alvl`, `warp`, `warpcore`, `max_warpcore`, `warpable`, `warpfields`, `max_warpfields`, `cloak`, `cloakable`, `eps`, `max_eps`, `reaktor`, `batt`, `max_batt`, `huelle`, `max_huelle`, `schilde`, `max_schilde`, `schilde_status`, `lss_range`, `kss_range`, `traktor`, `traktormode`, `dock`, `crew`, `max_crew`, `min_crew`, `nbs`, `lss`, `trumps_id`, `replikator`, `phaser`, `cfield`, `torp_type`, `wea_phaser`, `wea_torp`, `shuttle_type`, `is_hp`, `is_rkn`, `points`, `lastmaintainance`, `still`, `maintain`, `batt_wait`, `hud`, `assigned`, `slots`, `storage`) VALUES
				(".$this->uid.", ".$shipdata['rumps_id'].", ".$buildplan.", 0, ".$col[systems_id].", ".$col[cx].", ".$col[cy].", ".$col[sx].", ".$col[sy].", '3', '".$shipname."', '1', '0', ".$shipdata[warpcore].", ".$shipdata[warpcore].", '1', ".$shipdata[warpfields].", ".$shipdata[warpfields].", 0, NULL, ".$shipdata[eps].", ".$shipdata[eps].", ".$shipdata[reaktor].", 0, 0, ".$shipdata[huelle].", ".$shipdata[huelle].", ".$shipdata[schilde].", ".$shipdata[schilde].", 0, ".$shipdata[lss_range].", ".$shipdata[kss_range].", 0, '', 0, '".$shipdata[max_crew]."', '".$shipdata[max_crew]."', '".$shipdata[min_crew]."', '1', '1', 0, '', 0, 1, 0, '0', '0', 0, '0', 0, '0', 0, 0, 0, 0, '1', 0, 0, ".$shipdata[storage].");";						
			$this->db->query($query);
			
			$text = "Mit dem Levelaufstieg hat Deine Großmacht dir das folgende Schiff im Orbit über der Kolonie ".$col[name]." überlassen:\r\n\r\n<b>".$shipname."</b>\r\n\r\n";			
			$this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('2','".$this->uid."','".addslashes($text)."','3',NOW())");		
		}

		if (($_SESSION[level] >= 4) && ($this->db->query("SELECT count(id) FROM stu_ships WHERE plans_id = ".(120 + $_SESSION[race])." AND user_id=".$this->uid,1) == 0) && $col[id]) {

			$buildplan = 120 + $_SESSION[race];
			$shipdata = getShipValuesForBuildplan($buildplan);
			$shipname = "Frachter";
			
			$query = "INSERT INTO `stu_ships` (`user_id`, `rumps_id`, `plans_id`, `fleets_id`, `systems_id`, `cx`, `cy`, `sx`, `sy`, `direction`, `name`, `alvl`, `warp`, `warpcore`, `max_warpcore`, `warpable`, `warpfields`, `max_warpfields`, `cloak`, `cloakable`, `eps`, `max_eps`, `reaktor`, `batt`, `max_batt`, `huelle`, `max_huelle`, `schilde`, `max_schilde`, `schilde_status`, `lss_range`, `kss_range`, `traktor`, `traktormode`, `dock`, `crew`, `max_crew`, `min_crew`, `nbs`, `lss`, `trumps_id`, `replikator`, `phaser`, `cfield`, `torp_type`, `wea_phaser`, `wea_torp`, `shuttle_type`, `is_hp`, `is_rkn`, `points`, `lastmaintainance`, `still`, `maintain`, `batt_wait`, `hud`, `assigned`, `slots`, `storage`) VALUES
				(".$this->uid.", ".$shipdata['rumps_id'].", ".$buildplan.", 0, ".$col[systems_id].", ".$col[cx].", ".$col[cy].", ".$col[sx].", ".$col[sy].", '3', '".$shipname."', '1', '0', ".$shipdata[warpcore].", ".$shipdata[warpcore].", '1', ".$shipdata[warpfields].", ".$shipdata[warpfields].", 0, NULL, ".$shipdata[eps].", ".$shipdata[eps].", ".$shipdata[reaktor].", 0, 0, ".$shipdata[huelle].", ".$shipdata[huelle].", ".$shipdata[schilde].", ".$shipdata[schilde].", 0, ".$shipdata[lss_range].", ".$shipdata[kss_range].", 0, '', 0, '".$shipdata[max_crew]."', '".$shipdata[max_crew]."', '".$shipdata[min_crew]."', '1', '1', 0, '', 0, 1, 0, '0', '0', 0, '0', 0, '0', 0, 0, 0, 0, '1', 0, 0, ".$shipdata[storage].");";						
			$this->db->query($query);
			
			$text = "Mit dem Levelaufstieg hat Deine Großmacht dir das folgende Schiff im Orbit über der Kolonie ".$col[name]." überlassen:\r\n\r\n<b>".$shipname."</b>\r\n\r\n";			
			$this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('2','".$this->uid."','".addslashes($text)."','3',NOW())");				
		}

		if ($_SESSION[level] == 4) {
			$this->grantShipClass($this->uid, 6501, 1);
			$this->grantShipClass($this->uid, 6601, 1);
			$this->grantShipClass($this->uid, 6701, 1);

			$this->grantShipClass($this->uid, 6502, 2);
			$this->grantShipClass($this->uid, 6602, 2);
			$this->grantShipClass($this->uid, 6702, 2);

			$this->grantShipClass($this->uid, 6503, 3);
			$this->grantShipClass($this->uid, 6603, 3);
			$this->grantShipClass($this->uid, 6703, 3);

		}	
		
		if ($_SESSION[level] == 5) {
			$this->grantShipClass($this->uid, 2101, 1);
			$this->grantShipClass($this->uid, 2201, 1);
			$this->grantShipClass($this->uid, 3401, 1);
			$this->grantShipClass($this->uid, 4101, 1);
			$this->grantShipClass($this->uid, 4901, 1);
			$this->grantShipClass($this->uid, 5101, 1);
			$this->grantShipClass($this->uid, 5201, 1);
			$this->grantShipClass($this->uid, 5401, 1);
			
			$this->grantShipClass($this->uid, 2102, 2);
			$this->grantShipClass($this->uid, 2202, 2);
			$this->grantShipClass($this->uid, 3402, 2);
			$this->grantShipClass($this->uid, 4102, 2);
			$this->grantShipClass($this->uid, 4902, 2);
			$this->grantShipClass($this->uid, 5102, 2);
			$this->grantShipClass($this->uid, 5202, 2);
			$this->grantShipClass($this->uid, 5402, 2);
			
			$this->grantShipClass($this->uid, 2103, 3);
			$this->grantShipClass($this->uid, 2203, 3);
			$this->grantShipClass($this->uid, 3403, 3);
			$this->grantShipClass($this->uid, 4103, 3);
			$this->grantShipClass($this->uid, 4903, 3);
			$this->grantShipClass($this->uid, 5103, 3);
			$this->grantShipClass($this->uid, 5203, 3);
			$this->grantShipClass($this->uid, 5403, 3);
			
			$this->grantShipClass($this->uid, 5104, 1);
			$this->grantShipClass($this->uid, 2104, 1);
			
			$this->grantShipClass($this->uid, 5105, 2);
		}	
		return "Kolonisationslevel ".$_SESSION[level]." wurde erreicht";
	}

	
	
	function grantShipClass($user, $rump, $race) {
		global $_SESSION;
		
		if ($_SESSION[race] == $race) {
			if ($this->db->query("SELECT count(*) FROM stu_rumps_user WHERE rumps_id = ".$rump." AND user_id=".$user,1) < 1)
				$this->db->query("INSERT INTO `stu_rumps_user` (`rumps_id` ,`user_id`) VALUES ('".$rump."', '".$user."');");
		}
	}
	
	
	function getwirtstats() { return $this->db->query("SELECT SUM(b.lastrw) as lwp,a.user,a.race FROM stu_user as a LEFT JOIN stu_colonies as b ON b.user_id=a.id WHERE b.user_id>100 GROUP BY a.id"); }

	function getbevstats() { return $this->db->query("SELECT a.bev_free+a.bev_work as c,a.name,b.user,b.race FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.user_id>100 ORDER BY c DESC LIMIT 50"); }

	function getshipstats() { return $this->db->query("SELECT a.user,a.race,COUNT(b.id) AS c FROM stu_user AS a LEFT JOIN stu_ships AS b ON a.id = b.user_id WHERE a.id >100 AND b.crew>=b.min_crew GROUP BY a.id"); }

	function gethps() { return $this->db->query("SELECT a.rumps_id,a.name,a.cx,a.cy,b.name as cname FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.is_hp='1' AND a.user_id != 20 ORDER BY a.user_id"); }

	function getusercols() { return $this->db->query("SELECT a.id,a.colonies_classes_id,a.name,a.sx,a.sy,b.name as sname,b.cx,b.cy FROM stu_colonies as a LEFT JOIN stu_systems as b USING(systems_id) WHERE a.user_id=".$this->uid." ORDER BY a.colonies_classes_id"); }

	function getknstats() { return $this->db->query("SELECT SUM(a.rating)/COUNT(DISTINCT(a.id)) as c,SUM(DISTINCT(a.votes)) as vc,COUNT(a.id) as kc,b.user FROM stu_kn as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.rating>0 AND b.id>100 AND a.official='1' AND UNIX_TIMESTAMP(a.date)>".(time()-10368000)." GROUP BY a.user_id ORDER BY c DESC,kc DESC LIMIT 50"); }

	function getmapfieldtypes() { return $this->db->query("SELECT type,name,ecost,damage,x_damage,sensoroff,cloakoff,shieldoff FROM stu_map_ftypes WHERE view='1' ORDER BY type"); }

	function getgamestats()
	{
		$return['player'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE id>100 AND aktiv='1'",1);
		$return['online'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE id>100 AND aktiv='1' AND UNIX_TIMESTAMP(lastaction) > ".(time()-300),1);
		$return['urlaub'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE id>100 AND aktiv='1' AND vac_active='1'",1);
		$return['sperre'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE id>100 AND aktiv='2'",1);
		$return['schalt'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE id>100 AND ISNULL(aktiv)",1);
		$return['fed'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE race='1' AND aktiv='1' AND id>100",1);
		$return['rom'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE race='2' AND aktiv='1'AND id>100",1);
		$return['kli'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE race='3' AND aktiv='1'AND id>100",1);
		$return['car'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE race='4' AND aktiv='1'AND id>100",1);
		$return['fer'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE race='5' AND aktiv='1'AND id>100",1);
		$return['gor'] = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE race='6' AND aktiv='1'AND id>100",1);
		$return['kolos'] = $this->db->query("SELECT COUNT(id) FROM stu_colonies WHERE user_id!=1",1);
		$return['kolos_max'] = $this->db->query("SELECT COUNT(id) FROM stu_colonies",1);
		$return['popu'] = $this->db->query("SELECT SUM(bev_free)+SUM(bev_work) FROM stu_colonies",1);
		$return['wirt'] = $this->db->query("SELECT SUM(lastrw) FROM stu_colonies WHERE user_id!=1",1);
		$return['wavg'] = @round($return[wirt]/$return[kolos],2);
		$result = $this->db->query("SELECT wirtschaft FROM stu_game_rounds ORDER BY runde DESC LIMIT 2");
		$i = 0;
		while($data=mysql_fetch_assoc($result))
		{
			$wirt[$i] = $data['wirtschaft'];
			$i++;
		}
		if ($wirt[0] > $wirt[1]) $return['wchg'] = "<font color=Green>+".@round((((100/$wirt[1])*$wirt[0])-100),2)."</font>";
		elseif ($wirt[0] < $wirt[1]) $return['wchg'] = "<font color=#FF0000>-".@abs(round((((100/$wirt[1])*$wirt[0])-100),2))."</font>";
		else $return['wchg'] = 0;
		$return['ships'] = $this->db->query("SELECT COUNT(id) FROM stu_ships WHERE rumps_id!=3 AND user_id!=1",1);
		$return['aships'] = $this->db->query("SELECT COUNT(id) FROM stu_ships WHERE rumps_id!=3 AND user_id!=1 AND crew>=min_crew",1);
		$return['trums'] = $this->db->query("SELECT COUNT(id) FROM stu_ships WHERE rumps_id=8",1);
		$return['flight'] = $this->db->query("SELECT COUNT(ships_id) FROM stu_sectorflights",1);
		$return['ion'] = $this->db->query("SELECT COUNT(type) FROM stu_map_special WHERE type=5",1);
		$return['od'] = $this->db->query("SELECT schiffe,schiffeb,wirtschaft,population FROM stu_stats WHERE user_id=".$this->uid,4);
		$return['al']['ac'] = $this->db->query("SELECT COUNT(allys_id) FROM stu_allylist",1);
		$return['al']['wa'] = $this->db->query("SELECT COUNT(allys_id1) FROM stu_ally_relationship WHERE type='1'",1);
		@$return['od']['sp'] = $this->db->query("SELECT COUNT(user_id) FROM stu_stats WHERE schiffe>".(!$return['od']['schiffe'] ? 0 : $return['od']['schiffe']),1);
		@$return['od']['spb'] = $this->db->query("SELECT COUNT(user_id) FROM stu_stats WHERE schiffeb>".(!$return['od']['schiffeb'] ? 0 : $return['od']['schiffeb']),1);
		@$return['od']['wi'] = $this->db->query("SELECT COUNT(user_id) FROM stu_stats WHERE wirtschaft>".(!$return['od']['wirtschaft'] ? 0 : $return['od']['wirtschaft']),1);
		@$return['od']['po'] = $this->db->query("SELECT COUNT(user_id) FROM stu_stats WHERE population>".(!$return['od']['population'] ? 0 : $return['od']['population']),1);
		$return['tr']['tc'] = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id=1013",1)+$this->db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE goods_id=1013",1);
		$return['nagus'] = $this->db->query("SELECT value FROM stu_game_vars WHERE var='nagusdili' LIMIT 1",1);
		return $return;
	}

	function getrichestsettlers() { return $this->db->query("SELECT a.user_id,a.latinum,b.user,b.race FROM stu_stats as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.latinum>0 ORDER BY a.latinum DESC LIMIT 10"); }

	function getusertraffic() { return $this->db->query("SELECT COUNT(a.ships_id) as shid,b.user,b.race FROM stu_sectorflights as a LEFT JOIN stu_user as b ON a.user_id=b.id GROUP BY a.user_id"); }

	function getpopucolonys() { return $this->db->query("SELECT a.user,a.race, b.name, b.bev_work + b.bev_free AS bev, b.bev_work, b.bev_free FROM stu_user AS a LEFT JOIN stu_colonies AS b ON b.user_id = a.id WHERE a.id >100"); }

	function getpopuuser() { return $this->db->query("SELECT a.user,a.race, SUM( b.bev_work ) + SUM( b.bev_free ) AS bev, SUM( b.bev_work ) AS bev_work, SUM( b.bev_free ) AS bev_free FROM stu_user AS a LEFT JOIN stu_colonies AS b ON a.id = b.user_id WHERE a.id >100 GROUP BY a.id"); }

	function getresearchuser() { return $this->db->query("SELECT a.researched,b.user,b.race FROM stu_stats as a LEFT JOIN stu_user as b ON b.id=a.user_id ORDER BY a.researched DESC LIMIT 50"); }

	function getknownsystemsuser() { return $this->db->query("SELECT user_id,COUNT(systems_id) as sc FROM stu_systems_user WHERE user_id>100 GROUP BY user_id"); }

	function getallymembers() { return $this->db->query("SELECT a.name,COUNT(b.id) as c FROM stu_allylist as a LEFT JOIN stu_user as b USING(allys_id) GROUP BY a.allys_id"); }

	function getallyships() { return $this->db->query("SELECT a.name,COUNT(c.id) as co FROM stu_allylist as a LEFT JOIN stu_user as b USING(allys_id) LEFT JOIN stu_ships as c ON b.id=c.user_id GROUP BY a.allys_id"); }

	function getallywirt() { return $this->db->query("SELECT a.name,SUM(c.lastrw) as wi FROM stu_allylist as a LEFT JOIN stu_user as b USING(allys_id) LEFT JOIN stu_colonies as c ON b.id=c.user_id GROUP BY a.allys_id"); }

	function getallypopu() { return $this->db->query("SELECT a.name,SUM(c.bev_work)+SUM(c.bev_free) as bev,SUM(c.bev_work) as bev_work,SUM(c.bev_free) as bev_free FROM stu_allylist as a LEFT JOIN stu_user as b USING(allys_id) LEFT JOIN stu_colonies as c ON b.id=c.user_id GROUP BY a.allys_id"); }

	function getrumplist() { return $this->db->query("SELECT * FROM stu_rumps WHERE npc!='1' ORDER BY sort,rumps_id"); }

	
	function getrumpsbycategory($cat,$limited=1) {
		$rumps = array();
		
		if ($limited)	$res = $this->db->query("SELECT rumps_id,name,race FROM stu_rumps WHERE type = '".$cat."' AND ((rumps_id IN (SELECT rumps_id FROM stu_rumps_user WHERE user_id = ".$this->uid.")) OR (rumps_id IN (SELECT rumps_id FROM stu_rumps_scans WHERE user_id = ".$this->uid."))) ORDER BY name");
		else			
			$res = $this->db->query("SELECT rumps_id,name,race FROM stu_rumps WHERE type = '".$cat."' ORDER BY name");
		
		while($data=mysql_fetch_assoc($res)) {
			array_push($rumps,$data);
		}
		return $rumps;
	}
	
	function getrumplistfordb() { 
		$rumps = array();	
		for ($i = 0; $i <= 5; $i++) $rumps[$i] = $this->getrumpsbycategory($i);
		
		return $rumps;
	}

	
	
	function getallywars() { return $this->db->query("SELECT UNIX_TIMESTAMP(a.date) as date_tsp,b.name,c.name as name2 FROM stu_ally_relationship as a LEFT JOIN stu_allylist as b ON b.allys_id=a.allys_id1 LEFT JOIN stu_allylist as c ON c.allys_id=a.allys_id2 WHERE a.type='1' ORDER BY date ASC LIMIT 20"); }

	function getresearchpoints() { return $this->db->query("SELECT SUM(c.research_t) as rt,SUM(c.research_v) as rv,SUM(c.research_k) as rk,SUM(c.research_d) as rd FROM stu_colonies as a LEFT JOIN stu_colonies_fielddata as b ON b.colonies_id=a.id LEFT JOIN stu_buildings as c ON b.buildings_id=c.buildings_id WHERE a.user_id=".$this->uid." AND b.aktiv=1 AND (c.research_t>0 OR c.research_v>0 OR c.research_k>0 OR c.research_d>0)",4); }
	
	function showdominion() { return $this->db->query("SELECT research_id FROM stu_researched WHERE user_id = ".$this->uid." AND research_id = 8001",1); }

	function getuserawards() { return $this->db->query("SELECT COUNT(a.award_id) as ac,a.user_id,b.user FROM stu_user_awards as a LEFT JOIN stu_user as b ON b.id=a.user_id GROUP BY a.user_id ORDER BY ac DESC LIMIT 10"); }

	function getawardsperuser(&$user_id) { return $this->db->query("SELECT award_id FROM stu_user_awards WHERE user_id=".$user_id); }
	
	
	
	
	
	
	
	
	function sectorPriority($sector) {
		
		$score = 10000;
		$score += $sector['id'];
		
		if ($sector['status'] == "occupied") $score += 9000;
		if ($sector['status'] == "contested") $score += 8000;
		if ($sector['status'] == "core") $score += 5000;
		if ($sector['status'] == "held") $score += 6000;
		
		return $score;
	}
	

	
	function getSectors() {
		
		
		function sectorSorting($a, $b)
		{
			if ($a['priority'] == $b['priority']) {
				return 0;
			}
			return ($a['priority'] > $b['priority']) ? -1 : 1;
		}
		
		
		$relevantSectors = array();
		
		$systems = array();
		$qry = $this->db->query("SELECT * FROM stu_systems WHERE 1;");
		while ($row = mysql_fetch_assoc($qry)) {
			$systems[$row['systems_id']] = $row;
		}
		
		$qry = $this->db->query("SELECT * FROM stu_map_regions WHERE 1;");
		
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
			$sector['ships'][1] 	= $s[1];
			$sector['ships'][2] 	= $s[2];
			$sector['ships'][3] 	= $s[3];
			
			// $sector['priority'] = $this->sectorPriority($sector);
			
			$regions[$sector['id']] = $sector;
		}

		$race = $_SESSION['race'];
		foreach($regions as $id => $region) {
			
			$relevant = array();
			$relevant[1] = false;
			$relevant[2] = false;
			$relevant[3] = false;
			
			foreach($region['neighbours'] as $neighbour) {
				if ($regions[$neighbour]['faction'] == 1) $relevant[1] = true;
				if ($regions[$neighbour]['faction'] == 2) $relevant[2] = true;
				if ($regions[$neighbour]['faction'] == 3) $relevant[3] = true;
			}
			if ($region['faction'] == 1) $relevant[1] = true;
			if ($region['faction'] == 2) $relevant[2] = true;
			if ($region['faction'] == 3) $relevant[3] = true;
			if ($region['attacker'] == 1) $relevant[1] = true;
			if ($region['attacker'] == 2) $relevant[2] = true;
			if ($region['attacker'] == 3) $relevant[3] = true;
			
			if ($relevant[$race]) {

				if ($region['systemstring'] != "") {	
				
					$s[1] = 0;
					$s[2] = 0;
					$s[3] = 0;
				
					$s[1] = $this->db->query("SELECT SUM(r.fleetpoints) FROM stu_ships AS s LEFT JOIN stu_user as u ON s.user_id = u.id LEFT JOIN stu_rumps as r on s.rumps_id = r.rumps_id LEFT JOIN stu_fleets as f on s.fleets_id = f.fleets_id WHERE s.systems_id IN (".$region['systemstring'].") AND s.fleets_id > 0 AND f.faction=1;",1);	
					$s[2] = $this->db->query("SELECT SUM(r.fleetpoints) FROM stu_ships AS s LEFT JOIN stu_user as u ON s.user_id = u.id LEFT JOIN stu_rumps as r on s.rumps_id = r.rumps_id LEFT JOIN stu_fleets as f on s.fleets_id = f.fleets_id WHERE s.systems_id IN (".$region['systemstring'].") AND s.fleets_id > 0 AND f.faction=2;",1);
					$s[3] = $this->db->query("SELECT SUM(r.fleetpoints) FROM stu_ships AS s LEFT JOIN stu_user as u ON s.user_id = u.id LEFT JOIN stu_rumps as r on s.rumps_id = r.rumps_id LEFT JOIN stu_fleets as f on s.fleets_id = f.fleets_id WHERE s.systems_id IN (".$region['systemstring'].") AND s.fleets_id > 0 AND f.faction=3;",1);							

					if ($relevant[1]) $region['ships'][1] = $s[1];
					if ($relevant[2]) $region['ships'][2] = $s[2];
					if ($relevant[3]) $region['ships'][3] = $s[3];
				}
				
				$relevantSectors[$region['id']] = $region;				
			}
		}
		
		

			
			
		foreach($relevantSectors as $id => $sector) {
			$relevantSectors[$id]['priority'] = $this->sectorPriority($sector);
		}
		
		usort($relevantSectors,"sectorSorting");
		
		return $relevantSectors;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>
