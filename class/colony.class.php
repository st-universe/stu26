<?php
class colony extends qpm
{
	function colony()
	{
		global $db,$_SESSION,$_GET,$map,$gfx;
		$this->gfx = $gfx;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->sess = $_SESSION;
		if (($_GET['p'] == "colony" || $_GET['p'] == "colony2" || $_GET['p'] == "colship") && check_int($_GET['id']))
		{
			$result = $this->db->query("SELECT a.id,a.colonies_classes_id,a.faction,a.sx,a.sy,a.systems_id,a.name,a.planet_name,a.bev_work,a.bev_free,a.bev_max,a.eps,a.max_eps,a.max_storage,a.schilde,a.max_schilde,a.schilde_status,a.rotation,a.gravitation,a.dn_change,a.dn_mode,a.lastrw,a.bevstop,a.einwanderung,a.w_type,a.w_temp,a.ground_enabled,b.name as cname,b.erz,b.deut,b.dili,b.trit,b.water,b.is_moon FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) WHERE a.user_id=".$this->uid." AND a.id=".$_GET['id']." LIMIT 1",4);
			if ($result == 0) exit;
			foreach($result as $key => $value) $this->$key = $value;
		}
		elseif ($_GET['p'] == "colony" && $_GET['s'] && !is_numeric($_GET['id'])) exit;
	}

	function getcolonylist() { return $this->db->query("SELECT a.id,a.colonies_classes_id,a.name,a.sx,a.sy,a.systems_id,a.bev_free,a.bev_work,a.eps,a.max_eps,a.max_storage,a.schilde_status,a.gravitation,a.lastrw,b.type,b.name as sname,b.cx,b.cy FROM stu_colonies as a LEFT JOIN stu_systems as b USING(systems_id) WHERE a.user_id=".$this->uid." ORDER BY a.colonies_classes_id,a.id"); }

	function getcolstorage($id) { return $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_colonies_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=".$id); }

	function getstoragesum($id) { return $this->db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=".$id,1); }

	function addcol($x,$y,$sys,$classid)
	{
		if ($x == 0 || $y == 0 || $sys == 0 || $classid == 0) return 0;
		if ($this->db->query("SELECT id FROM stu_colonies WHERE sx=".$x." AND sy=".$y." AND systems_id=".$sys,1) > 0) return 0;
		$fd = $this->db->query("SELECT type FROM stu_map_ftypes WHERE colonies_classes_id=".$classid,1);
		$this->db->query("UPDATE stu_sys_map SET type=".$fd." WHERE sx=".$x." AND sy=".$y." AND systems_id=".$sys);
		$cid = $this->db->query("INSERT INTO stu_colonies (colonies_classes_id,sx,sy,systems_id,user_id) VALUES ('".$classid."','".$x."','".$y."','".$sys."','1')",5);
		include_once("inc/gencol.inc.php");
		return generate_colony($cid,$classid);
	}

	function loadsysteminfo() { $this->sys = $this->db->query("SELECT type,name,cx,cy,sr FROM stu_systems WHERE systems_id=".$this->systems_id." LIMIT 1",4); }

	function rendercolony($cid,$ca="")
	{
		global $_GET;
		
		$class = $this->db->query("SELECT colonies_classes_id FROM stu_colonies where id=".$cid."",1);

		if ($class >= 220 && $class <= 240) {
			
			$giant = $class % 10;
			echo "<table cellpadding=1 cellspacing=1 bgcolor=#262323><tr><td><img src=".$this->gfx."/fields/fullsurface/".$giant.".png border=0></td></tr></table>";
		} else {
		
			$fd = $this->db->query("SELECT a.aktiv,a.type,a.field_id,a.buildings_id,b.terraforming_id FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies_terraforming as b ON b.colonies_id=".$cid." AND b.field_id=a.field_id WHERE a.colonies_id=".$cid." ORDER BY a.field_id");
			mysql_num_rows($fd) < 90 ? $t = 7 : $t = 10;
			$fm = 1;
			// if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE (buildings_id=408 OR  buildings_id=418 OR  buildings_id=428) AND aktiv=1 AND colonies_id=".$cid." LIMIT 1",1) > 0)
			// {
				// $tfg = 1;
				// $is_moon = $this->db->query("SELECT b.is_moon FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) WHERE id=".$cid." LIMIT 1",1);
				// $osize = ($is_moon ? 13 : 17);
			// }
			echo "<table cellpadding=1 cellspacing=1 bgcolor=#262323><tr>";
			while($data = mysql_fetch_assoc($fd))
			{
				if ($data[field_id] > 49 && $t == 7) break;
				if ($data[field_id] > 80 && $t == 10) break;
				if ($i%$t==0) echo "</tr><tr>";
				// if ($tfg == 1) {
					// if ($i > $osize) echo "<td><img src=".$this->gfx."/fields/noise.gif></td>";
					// else {
						// if ($data['buildings_id'] > 0) echo "<td><img src=".$this->gfx."/buildings/".($data['aktiv'] > 1 ? 0 : $data['buildings_id'])."/".$data['type'].".gif border=0></td>";
						// else echo "<td><img src=".$this->gfx."/fields/".$data['type'].".gif border=0></td>";
					// }
					// $i++;
				// }
				// else {
					// if ($data['buildings_id'] > 0) echo "<td style='background-image:url(".$gfx."/fields/".$data['type'].".gif); background-repeat: no-repeat; background-position:center;'>".($ca == 1 ? "<a href=?p=ship&s=csc&id=".$_GET["id"]."&t=".$cid."&a=atc&fid=".$data['field_id'].">" : "")."<img src=".$this->gfx."/".($tfg == 1 && (($is_moon == 1 && $data['field_id'] > 14) || ($is_moon == 0 && $data['field_id'] > 18)) ? "fields" : "buildings/".($data['aktiv'] > 1 ? 0 : $data['buildings_id']))."/".($tfg == 1 && $data['type'] >= 41 && $data['type'] <= 45 ? ($data['type']-10) : $data['type']).".gif border=0>".($ca == 1 ? "</a>" : "")."</td>";
					$buildpic = buildingpic($data['buildings_id'],$data['type']).($data['aktiv'] > 1 ? "b" : "");					
					if ($data['buildings_id'] > 0) echo "<td style='background-image:url(".$this->gfx."/fields/".$data['type'].".gif); background-repeat: no-repeat; background-position:center;'><img src=".$this->gfx."/buildings/".$data['buildings_id']."/".$buildpic.".png border=0></td>";
					else
					{
						echo "<td><img src=".$this->gfx."/".($data['terraforming_id'] > 0 ? "terraforming/".$data['terraforming_id'].".gif" : "fields/".$data['type'].".gif")."></td>";
					}
					if ($cid == 8484 && $data['field_id'] == 49) break;
					$i++;
				// }
			}
			echo "</tr></table>";
		}
	}

	function loadfield($fieldId,$colId) { $this->fdd = $this->db->query("SELECT a.type,a.buildings_id,a.aktiv,a.integrity,b.name,b.integrity as maxintegrity,b.eps,b.is_activateable,c.terraforming_id,c.terraformtime FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b ON b.buildings_id=a.buildings_id LEFT JOIN stu_colonies_terraforming as c ON c.colonies_id=".$this->id." AND c.field_id=a.field_id WHERE a.colonies_id=".$colId." AND a.field_id=".$fieldId." LIMIT 1",4); }

	function getimmigration($classId)
	{
		if ($this->bev_work+$this->bev_free >= $this->bev_max) return 0;
		switch ($classId)
		{
			case 1:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/3);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 2:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/4);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 3:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/4);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 4:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/5);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 5:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/5);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 6:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/6);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 7:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/6);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 8:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/6);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 9:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/6);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 10:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/6);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 20:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/5);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 21:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/6);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 22:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/6);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 23:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/7);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 24:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/7);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 25:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/8);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 26:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/8);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 27:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/8);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 28:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/8);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
			case 29:
				$im = ceil(($this->bev_max-($this->bev_work+$this->bev_free))/6);
				if ($im > $this->bev_max-($this->bev_work+$this->bev_free)) $im = $this->bev_max-($this->bev_work+$this->bev_free);
				break;
		}
		return $im;
	}


	function getkss($x,$y,$range,$sysId) { return $this->db->query("SELECT a.sx as cx,a.sy as cy,a.type, (SELECT max(cloakstrength) as cs FROM stu_ships WHERE systems_id = a.systems_id AND sx = a.sx AND sy = a.sy AND cloak=1) as cs,COUNT(b.id) + (SELECT count(t.id) as bla FROM stu_sys_map as r LEFT JOIN stu_stations as t ON r.sy = t.sy AND r.sx = t.sx AND r.systems_id = t.systems_id WHERE r.sx = a.sx AND r.sy = a.sy AND r.systems_id = a.systems_id GROUP BY r.sx,r.sy ORDER BY r.sy,r.sx ) as sc, d.type as special FROM stu_sys_map as a LEFT JOIN stu_ships as b ON a.sx=b.sx AND a.sy=b.sy AND a.systems_id=b.systems_id AND b.rumps_id != 99 AND (b.cloak!='1' OR b.cloak='' OR ISNULL(b.cloak)) AND b.systems_id=".$sysId." LEFT JOIN stu_map_special AS d ON a.sx=d.sx AND a.sy=d.sy AND a.systems_id=d.systems_id WHERE a.sx BETWEEN ".($x-$range)." AND ".($x+$range)." AND a.sy BETWEEN ".($y-$range)." AND ".($y+$range)." AND a.systems_id=".$sysId." GROUP BY a.sx,a.sy ORDER BY a.sy,a.sx LIMIT ".(($range*2+1)*($range*2+1))); }
	function getSensorDataShip() {
		$res = array();
		$res['desaturated'] = false;
		$res['fields'] = array();
		
		$kss = 1;
		
		$queryResult = $this->getkss($this->sx,$this->sy,$kss,$this->systems_id);
		
		$shipx = $this->sx;
		$shipy = $this->sy;
		$detectbonus = 10;
		
		while($data=mysql_fetch_assoc($queryResult))
		{
			$field = array();

			$field['x'] = $data['cx'];
			$field['y'] = $data['cy'];
			$field['type'] = $data['type'];

			$field['onclick'] = false;
			
			$field['class'] = "fieldnormal";
			if (($field['x'] == $shipx) && ($field['y'] == $shipy)) $field['class'] = "fieldcurrent";

			$field['display'] = ($data['sc'] > 0 ? $data['sc'] : "");
			
			if ($data['cs']) {
				$field['display'] .= "?";
			}			

			array_push($res['fields'],$field);
		}
		
		return $res;
	}
	
	
	
	function loadcolony($id) { $this->col = $this->db->query("SELECT a.id,a.colonies_classes_id,a.user_id,a.name,a.rotation,a.gravitation,a.dn_change,a.dn_mode,a.lastrw,a.ground_enabled,c.is_moon FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_colonies_classes as c ON a.colonies_classes_id=c.colonies_classes_id WHERE a.id=".$id." LIMIT 1",4); }

	function loadship($id) { $this->ship = $this->db->query("SELECT a.id,a.plans_id,a.rumps_id,a.user_id,a.name,a.cloak,a.schilde_status,a.sx,a.sy,a.systems_id,a.huelle,a.max_huelle,a.eps,a.max_eps,a.crew,a.min_crew,a.max_crew,b.trumfield,b.storage,b.slots,c.user,c.level,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=".$id." LIMIT 1",4); }

	function loadstat($id) { $this->stat = $this->db->query("SELECT * FROM stu_stations WHERE id=".$id." LIMIT 1",4); }

	function getstorage() { $this->result = $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_colonies_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=".$this->id." ORDER BY b.sort"); }
		
	function getAllFleetPoints() {
		$sum = 0;
		
		$cpoints = $this->getAllPoints("pcrew");
		$mpoints = $this->getAllPoints("pmaintain");
		$spoints = $this->getAllPoints("psupply");
		
		return 20+min($cpoints,min($mpoints,$spoints));
	}
	
	function getAllPoints($type) {
		$result = $this->db->query("SELECT id FROM stu_colonies WHERE user_id=".$this->uid.";");
		
		$sum = 0;
		while($d=mysql_fetch_assoc($result)) {
			$sum += $this->getPoints($d['id'],$type);
		}			
	
		if ($this->uid < 100) $sum += 500;	

		return $sum;
	}
	
	function getPoints($cid,$type) {
		return $this->db->query("SELECT SUM(count) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings_effects as b on a.buildings_id = b.buildings_id WHERE a.colonies_id=".$cid." AND a.aktiv = 1 AND b.type='".$type."';",1);
	}
	
	function getFleetPoints($id) {
		$buildings = $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$id." AND buildings_id > 0 AND aktiv < 2;",1);
		
		$isMoon = $this->db->query("SELECT b.is_moon FROM stu_colonies AS a LEFT JOIN stu_colonies_classes AS b ON a.colonies_classes_id = b.colonies_classes_id WHERE a.id = ".$id.";",1);
		
		if ($isMoon)
			$res['buildcount'] = floor(min($buildings*0.5, 10));
		else 
			$res['buildcount'] = floor(min($buildings*0.5, 15));
		
		$build = 42; $res[$build] = $this->getBuildFleetPoints($id,$build);
		$build = 51; $res[$build] = $this->getBuildFleetPoints($id,$build);
		$build = 56; $res[$build] = $this->getBuildFleetPoints($id,$build);
		
		
		$sum = 0;
		foreach ($res as $val)
			$sum += $val;
		
		$res['sum'] = $sum;
		return $res;
	}
	
	function getBuildFleetPoints($col,$build) {
		return $this->db->query("SELECT SUM(b.count) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings_effects as b on a.buildings_id=b.buildings_id WHERE a.colonies_id=".$col." AND a.buildings_id=".$build." AND aktiv=1 AND b.type = 'fleet';",1);		
	}
	
	function getBuildingData() {
		
		$q = $this->db->query("SELECT f.*,b.* FROM stu_colonies_fielddata as f LEFT JOIN stu_buildings as b ON f.buildings_id=b.buildings_id WHERE f.colonies_id = ".$this->id." AND f.buildings_id > 0 AND f.aktiv < 2;");
		
		$res = array();
		
		while ($row = mysql_fetch_assoc($q)) {
			$res[$row['buildings_id']]['name'] = $row['name'];
			$res[$row['buildings_id']]['eps_proc'] = $row['eps_proc'];
			$res[$row['buildings_id']]['bev_pro'] = $row['bev_pro'];
			$res[$row['buildings_id']]['bev_use'] = $row['bev_use'];
			$res[$row['buildings_id']]['activateable'] = $row['is_activateable'];
			$res[$row['buildings_id']]['goods'] = array();
			$res[$row['buildings_id']]['effects'] = array();
			
			$res[$row['buildings_id']]['count']++;
			if ($row['aktiv']) {
				$res[$row['buildings_id']]['on'] += 1;
				$res[$row['buildings_id']]['off'] += 0;
			} else {
				$res[$row['buildings_id']]['on'] += 0;
				$res[$row['buildings_id']]['off'] += 1;
			}
			
			if ($row['buildings_id'] == 54 && $row['aktiv']) $weathercontrol = 1;
		}
		
		foreach($res as $k => $v) {
			$q = $this->db->query("SELECT * FROM stu_buildings_goods WHERE buildings_id = ".$k.";");
			while ($row = mysql_fetch_assoc($q)) {
				$res[$k]['goods'][$row['goods_id']] = $row['count'];
			}
			$q = $this->db->query("SELECT * FROM stu_colonies_bonus WHERE buildings_id = ".$k." AND colonies_classes_id = ".$this->colonies_classes_id.";");
			while ($row = mysql_fetch_assoc($q)) {
				if ($row['goods_id'] == 0)
					$res[$k]['eps_proc'] += $row['count'];
				else
					$res[$k]['goods'][$row['goods_id']] += $row['count'];
			}
			$q = $this->db->query("SELECT * FROM stu_buildings_effects WHERE buildings_id = ".$k.";");
			while ($row = mysql_fetch_assoc($q)) {
				$res[$k]['effects'][$row['type']] = $row['count'];
			}
			
			if ($k == 2 && $weathercontrol) 
				$res[$k]['goods'][1] += 2;

			if ($k == 9 && $weathercontrol) 
				$res[$k]['goods'][1] += 2;
			
			ksort($res[$k]['goods']);
			ksort($res[$k]['effects']);
		}
		ksort($res);
		return $res;
	}
	
	
	
	
	
	
	
	function loadcolstorage()
	{
		$this->result = $this->db->query("SELECT SUM(a.count) as gc,a.goods_id FROM stu_buildings_goods as a LEFT JOIN stu_colonies_fielddata as b USING(buildings_id) WHERE b.colonies_id=".$this->id." AND b.aktiv=1 GROUP BY a.goods_id");
		while($d=mysql_fetch_assoc($this->result)) $this->goods[$d['goods_id']] = $d['gc'];
		
		$this->result = $this->db->query("SELECT b.goods_id, SUM(b.count) as gc FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies_bonus as b ON a.buildings_id = b.buildings_id LEFT JOIN stu_colonies as c ON c.id = a.colonies_id WHERE a.colonies_id=".$this->id." AND a.aktiv=1 AND b.colonies_classes_id = c.colonies_classes_id GROUP by b.goods_id;");
		while($d=mysql_fetch_assoc($this->result)) $this->goods[$d['goods_id']] += $d['gc'];
		
		$this->result = $this->db->query("SELECT a.goods_id,a.name,b.count FROM stu_goods as a LEFT JOIN stu_colonies_storage as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." ORDER BY a.sort");
		$this->storage = $this->db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=".$this->id,1);
	}
	

	function changename($name)
	{
		$name = strip_tags($name,"<font></font><i></i><b></b>");
		$name = addslashes($name);
		if (!check_html_tags($name)) $name = strip_tags($name);
		$this->db->query("UPDATE stu_colonies SET name='".str_replace("\"","",$name)."' WHERE id=".$this->id." LIMIT 1");
		return "Der Name der Kolonie wurde geändert";
	}

	function loadterraformingtypes() { $this->result = $this->db->query("SELECT a.name,a.z_feld,a.flimit,COUNT(DISTINCT(c.type)) as fc FROM stu_terraforming as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." LEFT JOIN stu_colonies_fielddata as c ON c.colonies_id=".$this->id." AND c.type=a.z_feld WHERE a.v_feld=".$this->fdd[type]." AND (b.research_id>0 OR a.research_id=0) GROUP BY a.terraforming_id HAVING (a.flimit=0 OR (a.flimit>0 AND fc<a.flimit))"); }

	function loadpossiblebuildings() { $this->result = $this->db->query("SELECT b.buildings_id,b.name FROM stu_field_build as a LEFT JOIN stu_buildings as b USING (buildings_id) LEFT JOIN stu_researched as c ON b.research_id=c.research_id AND c.user_id=".$this->uid." WHERE a.type=".$this->fdd[type]." AND (c.research_id>0 OR b.research_id=0) AND b.level<=".$this->sess["level"]." AND view=1 AND upgrade_from=0 ORDER BY b.name"); }

	function loadpossiblenpcbuildings() { $this->result = $this->db->query("SELECT b.buildings_id,b.name FROM stu_field_build as a LEFT JOIN stu_buildings as b USING (buildings_id) LEFT JOIN stu_researched as c ON b.research_id=c.research_id AND c.user_id=".$this->uid." LEFT JOIN stu_buildings_factions as f ON f.buildings_id = b.buildings_id WHERE a.type=".$this->fdd[type]." AND (c.research_id>0 OR b.research_id=0 OR b.research_id=999) AND b.level<=".$this->sess["level"]." AND upgrade_from=0 ORDER BY b.name"); }
	
	function build($building,$field)
	{
		if ($field < 1 || $field > 100) return;
		if ($nxb != "" && !check_int($nxb)) return;
		// if ($this->is_moon == 1 && $field > 72) return;

		// if (!$this->db->query("SELECT faction FROM stu_buildings_factions WHERE buildings_id = ".$building." AND faction = ".$this->faction." LIMIT 1;",1)) return "Dieses Gebäude ist für diese Kolonie nicht verfügbar.";
		
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			if ($this->is_moon == 1 && $field < 15) return "Bauen im Orbit ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
			if ($this->is_moon == 0 && $field < 21) return "Bauen im Orbit ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
			if (($this->schilde_status != '1') && ($amode > 1)) return "Bauen auf der Oberfläche ist aufgrund einer angreifenden Flotte nicht möglich.";
		}
		$this->loadfield($field,$this->id);
		if ($this->fdd[buildings_id] == 1 && $this->uflag != 1) return;

		if ($this->fdd[terraforming_id] > 0) return;
		if ($this->fdd[buildings_id] > 0 && $this->uflag != 1) return "Willst du das Gebäude auf diesem Feld wirklich ersetzen? <a href=?ps=".$this->sess['pagesess']."&p=colony&s=sc&id=".$this->id."&a=dmb&fid=".$field."&nxb=".$building."><font color=#FF0000>Bestätigen</font></a>";
		if ($this->db->query("SELECT COUNT(*) FROM stu_field_build WHERE type=".$this->fdd[type]." AND buildings_id=".$building,1) == 0) return;
		// NPC-Weiche
		if ($this->uid < 101) $data = $this->db->query("SELECT a.buildings_id,a.name,a.lager,a.eps_cost,a.bev_pro,a.integrity,a.schilde,a.buildtime,a.blimit,a.bclimit FROM stu_buildings as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." WHERE a.buildings_id=".$building." AND (upgrade_from=0 OR upgrade_from=".$this->fdd[buildings_id].") AND a.level<=".$this->sess["level"]." AND (b.research_id>0 OR a.research_id=0)",4);
		else $data = $this->db->query("SELECT a.buildings_id,a.name,a.lager,a.eps_cost,a.bev_pro,a.integrity,a.schilde,a.buildtime,a.blimit,a.bclimit FROM stu_buildings as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." WHERE a.buildings_id=".$building." AND ((a.view=1 AND upgrade_from=0) OR upgrade_from=".$this->fdd[buildings_id].") AND a.level<=".$this->sess["level"]." AND (b.research_id>0 OR a.research_id=0)",4);
		if ($data == 0) return;
		// if ($building == 8)
		// {
			// if ($this->db->query("SELECT COUNT(*) FROM stu_ships WHERE rumps_id=1 AND user_id=".$this->uid,1) >= 1) return "Du kannst nur 1 Kolonisationsschiff besitzen";
			// if ($this->db->query("SELECT COUNT(b.field_id) FROM stu_colonies as a LEFT JOIN stu_colonies_fielddata as b ON b.colonies_id=a.id WHERE a.user_id=".$this->uid." AND b.buildings_id=8",1) > 0) return "Du kannst nur ein Kolonisationsschiff besitzen";
		// }
		if ($data[bclimit] > 0 && $data[bclimit] <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$building,1)) return "Von diesem Gebäudetyp dürfen nur ".$data[bclimit]." pro Kolonie gebaut werden";
		if ($data[blimit] > 0 && $data[blimit] <= $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_fielddata as b ON a.id=b.colonies_id WHERE a.user_id=".$this->uid." AND b.buildings_id=".$building,1)) return "Von diesem Gebäudetyp dürfen nur ".$data[blimit]." gebaut werden";

		if ($field < 21 && !$this->is_moon && $this->check_rbf($this->id) == 0) return "Zum Bau im Orbit wird ein aktivierter Raumbahnhof benötigt";
		if ($field < 15 && $this->is_moon == 1 && $this->check_rbf($this->id) == 0) return "Zum Bau im Orbit wird ein aktivierter Raumbahnhof benötigt";
		if ($field > 80 && !$this->is_moon && $this->check_ulift($this->id) == 0) return "Zugang zum Untergrund wurde noch nicht freigelegt.";
		if ($field > 49 && $this->is_moon && $this->check_ulift($this->id) == 0) return "Zugang zum Untergrund wurde noch nicht freigelegt.";
		
		
		// if ($building == 15)
		// {
			// $bl = $this->db->query("SELECT erz FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			// if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=15",1)) return "Alle Erzvorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		// }
		// if ($building == 14 || $building == 114)
		// {
			// $bl = $this->db->query("SELECT deut FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			// if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND (buildings_id=14 OR buildings_id=114)",1)) return "Alle Deuteriumvorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		// }		
		if ($building == 20)
		{
			$bl = $this->db->query("SELECT dili FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=20",1)) return "Alle Dilithiumvorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}
		
		if ($building >= 45 && $building <= 48)
		{
			$bl = $this->db->query("SELECT water FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND (buildings_id>=45 AND buildings_id<=48)",1)) return "Alle Wasservorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}		
		
		if ($building == 85)
		{
			$bl = $this->db->query("SELECT earths FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=85",1)) return "Alle Vorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}	
		
		if ($building == 61)
		{
			$bid = 61;
			$tag = "res1";
			$bl = $this->db->query("SELECT ".$tag." FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$bid."",1)) return "Alle Vorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}
		if ($building == 62)
		{
			$bid = 62;
			$tag = "res2";
			$bl = $this->db->query("SELECT ".$tag." FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$bid."",1)) return "Alle Vorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}
		if ($building == 63)
		{
			$bid = 63;
			$tag = "res3";
			$bl = $this->db->query("SELECT ".$tag." FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$bid."",1)) return "Alle Vorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}
		if ($building == 64)
		{
			$bid = 64;
			$tag = "res4";
			$bl = $this->db->query("SELECT ".$tag." FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$bid."",1)) return "Alle Vorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}
		if ($building == 65)
		{
			$bid = 65;
			$tag = "res5";
			$bl = $this->db->query("SELECT ".$tag." FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$bid."",1)) return "Alle Vorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}
		if ($building == 66)
		{
			$bid = 66;
			$tag = "trit";
			$bl = $this->db->query("SELECT ".$tag." FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$bid."",1)) return "Alle Vorkommen auf dieser Kolonie (".$bl.") werden bereits genutzt.";
		}
		
		// if ($building >= 20 && $building <=26)
		// {
			// $bl = $this->db->query("SELECT mine".$building." FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			// if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$building,1)) return "Von diesem Gebäudetyp können auf diesem Planeten maximal ".$bl." gebaut werden";
		// }
		// if ($building == 48)
		// {
			// $bl = $this->db->query("SELECT geos FROM stu_colonies_classes WHERE colonies_classes_id=".$this->colonies_classes_id,1);
			// if ($bl <= $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=".$building,1)) return "Von diesem Gebäudetyp können auf diesem Planeten maximal ".$bl." gebaut werden";
		// }
		// if ($this->uflag != 1 && ($building == 300 || $building == 330 || ($building > 301 && $building < 307)) && $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE (buildings_id=300 OR buildings_id=330 OR (buildings_id>301 AND buildings_id<307)) AND colonies_id=".$this->id,1) >= 1) return "Es ist nur eine Werft pro Kolonie möglich";
		if ($this->uflag == 1) $dmre = $this->removebuilding($field);
		if ($this->eps < $data[eps_cost]) return $return."Zum Bau wird ".$data[eps_cost]." Energie benötigt - Vorhanden ist nur ".$this->eps;
		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_buildings_cost as a LEFT JOIN stu_colonies_storage as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.buildings_id=".$building." ORDER BY c.sort");
		while($cost=mysql_fetch_assoc($result))
		{
			if ($cost['vcount'] < $cost['count'])
			{
				return $return."Es werden ".$cost['count']." ".$cost[name]." benötigt - Vorhanden sind nur ".(!$cost[vcount] ? 0 : $cost[vcount]);
			}
		}
		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_buildings_cost as a LEFT JOIN stu_colonies_storage as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.buildings_id=".$building." ORDER BY c.sort");
		while($cost=mysql_fetch_assoc($result))
		{
			$this->lowerstorage($this->id,$cost[goods_id],$cost['count']);
		}
		$this->db->query("UPDATE stu_colonies SET eps=eps-".$data[eps_cost]." WHERE id=".$this->id." LIMIT 1");
		$res = $this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=".$data[buildings_id].",integrity=".$data[integrity].",aktiv=".(time()+$data[buildtime])." WHERE field_id=".$field." AND colonies_id=".$this->id." LIMIT 1",6);
		// if ($data[buildings_id] == 46)
		// {
			// $ground = $field-19;
			// $to = floor($ground/18);
			// $tl = floor($ground/9);
			// $rest = $ground-$tl*9;
			// $nf = 73+$to*9+$rest;
			// $this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=47,integrity=50,aktiv=".(time()+$data[buildtime])." WHERE colonies_id=".$this->id." AND field_id=".$nf);
			// if ($this->colonies_classes_id == 9) $this->db->query("UPDATE stu_colonies_fielddata SET type=59 WHERE field_id=".$nf." AND colonies_id=".$this->id);
		// }
		return $return."Gebäude (".$data[name].") wird gebaut - Fertigstellung: ".date("d.m. H:i",time()+$data[buildtime])." Uhr";
	}

	function lowerstorage($id,$good,$count)
	{
		if ($count > 0) {
			$result = $this->db->query("UPDATE stu_colonies_storage SET count=count-".$count." WHERE colonies_id=".$id." AND goods_id=".$good." AND count>".$count." LIMIT 1",6);
			if ($result == 0) $this->db->query("DELETE FROM stu_colonies_storage WHERE colonies_id=".$id." AND goods_id=".$good." LIMIT 1");
		}
	}

	function upperstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_colonies_storage SET count=count+".$count." WHERE colonies_id=".$id." AND goods_id=".$good." LIMIT 1",6);
		if ($result == 0) $this->db->query("INSERT INTO stu_colonies_storage (colonies_id,goods_id,count) VALUES ('".$id."','".$good."','".$count."')");
	}

	function shiplowerstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count-".$count." WHERE ships_id=".$id." AND goods_id=".$good." AND count>".$count." LIMIT 1",6);
		if ($result == 0)
		{
			if ($good >= 80 && $good < 100) $this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=".$id." LIMIT 1");
			$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$id." AND goods_id=".$good." LIMIT 1");
		}
	}
	function statlowerstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_stations_storage SET count=count-".$count." WHERE stations_id=".$id." AND goods_id=".$good." AND count>".$count." LIMIT 1",6);
		if ($result == 0)
		{
			$this->db->query("DELETE FROM stu_stations_storage WHERE stations_id=".$id." AND goods_id=".$good." LIMIT 1");
		}
	}
	function shipupperstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count+".$count." WHERE ships_id=".$id." AND goods_id=".$good." LIMIT 1",6);
		if ($result == 0) $this->db->query("INSERT INTO stu_ships_storage (ships_id,goods_id,count) VALUES ('".$id."','".$good."','".$count."')");
	}

	function statupperstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_stations_storage SET count=count+".$count." WHERE stations_id=".$id." AND goods_id=".$good." LIMIT 1",6);
		if ($result == 0) $this->db->query("INSERT INTO stu_stations_storage (stations_id,goods_id,count) VALUES ('".$id."','".$good."','".$count."')");
	}

	function check_rbf($id) { return $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$id." AND buildings_id=24 AND aktiv=1",1); }

	function check_werft($id) { return $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$id." AND (buildings_id=8 OR buildings_id=51) AND aktiv=1",1); }

	function check_warwerft($id) { return $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$id." AND (buildings_id=300 OR buildings_id=330 OR (buildings_id>301 AND buildings_id<307) OR buildings_id=313) AND aktiv=0",1); }

	function getwerfttype($id) { return $this->db->query("SELECT buildings_id FROM stu_colonies_fielddata WHERE colonies_id=".$id." AND (buildings_id=8 OR buildings_id=51) ORDER BY buildings_id DESC LIMIT 1",1); }

	function check_ulift($id) { return $this->db->query("SELECT ground_enabled FROM stu_colonies WHERE id=".$id."",1); }

	function switchCenterMode($type) {
		$field = $this->db->query("UPDATE stu_colonies_fielddata SET buildings_id = ".$type." WHERE colonies_id = ".$this->id." AND (buildings_id=1 OR buildings_id=101 OR buildings_id=102) LIMIT 1;",1);
		return "Koloniezentralen-Modus wurde geändert";
	}	
	
	function activateBuildingType($type) {
		$field = $this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id = ".$this->id." AND buildings_id=".$type." AND aktiv=0 LIMIT 1;",1);
		if ($field == 0) return;
		return $this->activatebuilding($field);
	}

	function deactivateBuildingType($type) {
		$field = $this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id = ".$this->id." AND buildings_id=".$type." AND aktiv=1 LIMIT 1;",1);
		if ($field == 0) return;
		return $this->deactivatebuilding($field);
	}	
	
	function deactivatebuilding($field)
	{
		if (!$this->fdd) $this->loadfield($field,$this->id);
		if ($this->fdd == 0) return;
		if ($this->fdd['aktiv'] == 0) return;
		if ($this->fdd['buildings_id'] == 0) return;
		if (!$this->fdd['is_activateable']) return;
		if ($this->fdd['aktiv'] > 1) return;
		$bd = $this->db->query("SELECT bev_pro,bev_use,schilde FROM stu_buildings WHERE buildings_id=".$this->fdd[buildings_id]." LIMIT 1",4);
		if ($this->fdd['buildings_id'] == 24) $ob = $this->db->query("SELECT SUM(b.bev_use) as bu,SUM(b.bev_pro) as bp FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$this->id." AND a.aktiv=1 AND a.buildings_id<400 AND a.field_id<".($this->is_moon == 1 ? 15 : 19),4);
		// if ($this->fdd['buildings_id'] == 46 && $this->is_moon != 1) $ug = $this->db->query("SELECT SUM(b.bev_use) as bu,SUM(b.bev_pro) as bp,SUM(b.schilde) as sh FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=".$this->id." AND a.aktiv=1 AND a.field_id>72",4);
		$this->db->query("START TRANSACTION");
		if (is_array($ob))
		{
			if (!$ob['bu']) $ob['bu'] = 0;
			if (!$ob['bp']) $ob['bp'] = 0;
			$this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE colonies_id=".$this->id." AND aktiv=1  AND buildings_id<400 AND field_id<".($this->is_moon == 1 ? 15 : 21));
			$this->db->query("UPDATE stu_colonies SET bev_work=bev_work-".$ob['bu'].",bev_free=bev_free+".$ob['bu'].",bev_max=bev_max-".$ob['bp']." WHERE id=".$this->id);
			$return = "<br>Alle Einrichtungen im Orbit (außer Plattformen) wurden deaktiviert";
		}
		// if (is_array($ug))
		// {
			// if (!$ug['bu']) $ug['bu'] = 0;
			// if (!$ug['bp']) $ug['bp'] = 0;
			// $this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE colonies_id=".$this->id." AND aktiv=1 AND field_id>80");
			// $this->db->query("UPDATE stu_colonies SET bev_work=bev_work-".$ug['bu'].",bev_free=bev_free+".$ug['bu'].",bev_max=bev_max-".$ug['bp']." WHERE id=".$this->id);
			// $return = "<br>Alle Einrichtungen im Untergrund wurden deaktiviert";
		// }
		$this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE field_id=".$field." AND colonies_id=".$this->id." LIMIT 1");
		$this->db->query("UPDATE stu_colonies SET bev_work=bev_work-".$bd['bev_use'].",bev_free=bev_free+".$bd['bev_use'].",bev_max=bev_max-".$bd['bev_pro']." WHERE id=".$this->id." LIMIT 1");
		// if ($this->fdd['buildings_id'] == 46) $this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE buildings_id=47 AND colonies_id=".$this->id);
		// if ($this->fdd['buildings_id'] == 100) $this->db->query("UPDATE stu_colonies SET schilde_status='0' WHERE id=".$this->id." LIMIT 1");
		// if (($this->fdd['buildings_id'] == 401) || ($this->fdd['buildings_id'] == 411) || ($this->fdd['buildings_id'] == 421)) $this->db->query("UPDATE stu_colonies SET beamblock='0' WHERE id=".$this->id." LIMIT 1");
		$this->db->query("COMMIT");
		if ($this->uflag == 1) return;
		return $this->fdd['name']." auf Feld ".$field." deaktiviert".$return;
	}

	function activatebuilding($field)
	{
		$this->loadfield($field,$this->id);
		if ($this->fdd == 0) return;
		if ($this->fdd['buildings_id'] == 0) return;
		if (!$this->fdd['is_activateable']) return;
		if ($this->fdd['aktiv'] == 1) return;
		if ($this->fdd['aktiv'] > 1) return "Dieses Gebäude wurde noch nicht fertiggestellt";
		
		$limit = $this->db->query("SELECT blimit FROM stu_buildings WHERE buildings_id =".$this->fdd['buildings_id']." LIMIT 1;",1);
		$activelimit = $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata AS a LEFT JOIN stu_colonies AS b ON a.colonies_id = b.id WHERE a.buildings_id =".$this->fdd['buildings_id']." AND a.aktiv=1 AND b.user_id = ".$this->uid.";",1);
		if (($limit > 0) && ($activelimit >= $limit)) return "Limit erreicht";
		
		$bd = $this->db->query("SELECT bev_pro,bev_use,level,research_id,schilde FROM stu_buildings WHERE buildings_id=".$this->fdd['buildings_id']." LIMIT 1",4);
		if ($bd == 0) return;
		if ($bd['research_id'] > 0 && $this->db->query("SELECT research_id FROM stu_researched WHERE research_id=".$bd['research_id']." AND user_id=".$this->uid,1) == 0) {
			if ($this->fdd['buildings_id'] == 402) return "Wegen Änderungen an diesem Gebäude muss es nun erforscht werden, bevor es aktiviert werden kann.";
			else return "Dieses Gebäude wurde noch nicht erforscht.";

		}
		if ($bd['bev_use'] >0 && $bd['bev_use'] > $this->bev_free) return "Zum Aktivieren des Gebäudes (".$this->fdd['name'].") werden ".$bd['bev_use']." Arbeitslose benötigt - Verfügbar sind ".$this->bev_free;
		if (!$this->is_moon && $field < 19 && $this->fdd['buildings_id'] < 400 && $this->check_rbf($this->id) == 0) return "Zum Aktivieren des Gebäudes (".$this->fdd['name'].") wird ein aktivierter Raumbahnhof benötigt";
		if ($this->is_moon == 1 && $field < 15 && $this->fdd['buildings_id'] < 400 && $this->check_rbf($this->id) == 0) return "Zum Aktivieren des Gebäudes (".$this->fdd['name'].") wird ein aktivierter Raumbahnhof benötigt";
		// if ($field > 72 && $this->check_ulift($this->id) == 0) return "Zum Aktivieren des Gebäudes (".$this->fdd['name'].") wird ein aktivierter Untergrundlift benötigt";
		if ($this->sess['level'] < $bd['level']) return "Das Gebäude kann nicht aktiviert werden";
		if ($this->fdd['buildings_id'] == 100 && $this->schilde == 0) return "Der Planetare Schildgenerator kann nicht aktiviert werden, da die Schilde nicht geladen sind";
		$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_colonies_fielddata SET aktiv=1 WHERE field_id=".$field." AND colonies_id=".$this->id." LIMIT 1");
		if ($this->fdd['buildings_id'] == 46) $this->db->query("UPDATE stu_colonies_fielddata SET aktiv=1 WHERE buildings_id=47 AND colonies_id=".$this->id." LIMIT 1");
		if ($this->fdd['buildings_id'] == 100) $this->db->query("UPDATE stu_colonies SET schilde_status='1' WHERE id=".$this->id." LIMIT 1");
		if (($this->fdd['buildings_id'] == 401) || ($this->fdd['buildings_id'] == 411) || ($this->fdd['buildings_id'] == 421)) $this->db->query("UPDATE stu_colonies SET beamblock='1' WHERE id=".$this->id." LIMIT 1");
		$this->db->query("UPDATE stu_colonies SET bev_work=bev_work+".$bd['bev_use'].",bev_free=bev_free-".$bd['bev_use'].",bev_max=bev_max+".$bd['bev_pro']." WHERE id=".$this->id." LIMIT 1");
		$this->db->query("COMMIT");
		$this->bev_work+=$bd['bev_use'];
		$this->bev_free-=$bd['bev_use'];
		return $this->fdd['name']." auf Feld ".$field." aktiviert";
	}

	function removebuilding($field,$nxb="")
	{
		$this->loadfield($field,$this->id);
		// if ($this->fdd['buildings_id'] == 47)
		// {
			// $field = $this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=46 AND colonies_id=".$this->id,1);
			// $this->loadfield($field,$this->id);
		// }
		if ($this->fdd == 0) return;
		// if ($this->fdd['buildings_id'] == 0 || (($this->fdd['buildings_id'] == 1) && $this->uflag != 1)) return;
		if ($this->fdd['buildings_id'] == 0 || $this->fdd['buildings_id'] == 1) return;
		if ($this->fdd['aktiv'] == 1) $return = $this->deactivatebuilding($field)."<br>";
		// $result = $this->db->query("SELECT ROUND(((count/2)/".$this->fdd['maxintegrity'].")*".$this->fdd['integrity'].") as rc,goods_id FROM stu_buildings_cost WHERE (goods_id<80 OR goods_id>100) AND buildings_id=".$this->fdd['buildings_id']);
		$result = $this->db->query("SELECT ROUND(((count/2)/".($this->fdd['maxintegrity']+1).")*".($this->fdd['integrity']+1).") as rc,goods_id FROM stu_buildings_cost WHERE buildings_id=".$this->fdd['buildings_id']);
		$bd = $this->db->query("SELECT lager,eps,bev_use,schilde FROM stu_buildings WHERE buildings_id=".$this->fdd['buildings_id']." LIMIT 1",4);
		// if (!$this->uflag)
		// {
			while ($data=mysql_fetch_assoc($result))
			{
				if ($data['rc'] > 0) $this->upperstorage($this->id,$data['goods_id'],$data['rc']);
			}
		// }
		$this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=0,aktiv=0,integrity=0 WHERE field_id=".$field." AND colonies_id=".$this->id." LIMIT 1");
		if ($this->fdd['aktiv'] < 2)
		{
			// if ($this->eps > $this->max_eps-$bd['eps']) $this->eps = $this->max_eps-$bd['eps'];
			if ($this->schilde > $this->max_schilde-$bd['schilde']) $this->schilde = $this->max_schilde-$bd['schilde'];
			$this->db->query("UPDATE stu_colonies SET max_storage=max_storage-".$bd['lager'].",eps=".$this->eps.",max_eps=max_eps-".$bd['eps'].",schilde=".$this->schilde.",max_schilde=max_schilde-".$bd['schilde']." WHERE id=".$this->id." LIMIT 1");
		}
		// if ($this->fdd['buildings_id'] == 100) $this->db->query("UPDATE stu_colonies SET schilde_status='0' WHERE id=".$this->id);
		// if (($this->fdd['buildings_id'] == 401) || ($this->fdd['buildings_id'] == 411) || ($this->fdd['buildings_id'] == 421)) $this->db->query("UPDATE stu_colonies SET beamblock='0' WHERE id=".$this->id);
		// if ($this->fdd['buildings_id'] == 46) $this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=0,integrity=0,aktiv=0 WHERE buildings_id=47 AND colonies_id=".$this->id);
		if ($nxb != "" && check_int($nxb)) $abm = "<br>Soll der Bau des neuen Gebäudes gestartet werden? <a href=?ps=".$this->sess['pagesess']."&p=colony&s=sc&id=".$this->id."&a=bu&fid=".$field."&bu=".$nxb."><font color=#FF0000>Bestätigung</font></a>";
		return $return.$this->fdd['name']." auf Feld ".$field." demontiert".$abm;
	}

	function removeplatform($field)
	{
		$this->loadfield($field,$this->id);

		if ($this->eps < 10) return "Zum Rückbau einer Plattform werden 10 Energie benötigt.";


		if ($this->fdd == 0) return;
		if ($this->fdd['buildings_id'] == 0 || (($this->fdd['buildings_id'] == 1 || $this->fdd['buildings_id'] == 53 || ($this->fdd['buildings_id'] >= 57 && $this->fdd['buildings_id'] <= 61)) && $this->uflag != 1)) return;
		if ($this->fdd['aktiv'] == 1) $return = $this->deactivatebuilding($field)."<br>";
		$result = $this->db->query("SELECT ROUND(((count/2)/".$this->fdd['maxintegrity'].")*".$this->fdd['integrity'].") as rc,goods_id FROM stu_buildings_cost WHERE (goods_id<80 OR goods_id>100) AND buildings_id=".$this->fdd['buildings_id']);
		$bd = $this->db->query("SELECT lager,eps,bev_use,schilde FROM stu_buildings WHERE buildings_id=".$this->fdd['buildings_id']." LIMIT 1",4);
		if (!$this->uflag)
		{
			while ($data=mysql_fetch_assoc($result))
			{
				if ($data['rc'] > 0) $this->upperstorage($this->id,$data['goods_id'],$data['rc']);
			}
		}
		$this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=400,aktiv=0,integrity=40 WHERE field_id=".$field." AND colonies_id=".$this->id." LIMIT 1");

		if (($this->fdd['buildings_id'] == 401) || ($this->fdd['buildings_id'] == 411) || ($this->fdd['buildings_id'] == 421)) $this->db->query("UPDATE stu_colonies SET beamblock='0' WHERE id=".$this->id);
		$this->eps -= 10;
		$this->db->query("UPDATE stu_colonies SET eps=".$this->eps." WHERE id=".$this->id);
		return $return.$this->fdd['name']." auf Feld ".$field." zurückgebaut";
	}

	function beamto($id,$goods="",$count="",$crew=0)
	{
		if ($this->eps == 0) return "Keine Energie vorhanden";
		$this->loadship($id);
		if ($this->ship == 0) return;
		if (checkcolsector($this->ship) == 0) return;

		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Beamen ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}

		if ($this->ship[vac_active] == 1) return "Der Siedler befindet sich im Urlaubsmodus";
		if ($this->ship[schilde_status] == 1 && $this->ship[user_id] != $this->uid) return "Die Schilde der ".stripslashes($this->ship[name])." sind aktiviert";
		if ($this->ship[cloak] == 1) return;
		if ($crew > 0 && $this->bev_free > 0) $return = $this->beamcrewup($crew);
		if ($this->uid == $this->ship[user_id]) $al = "<br>->> <a href=?p=".($this->ship['slots'] > 0 ? "stat" : "ship")."&s=ss&id=".$id.">Zu Schiff wechseln</a>";
		if ($this->eps == 0 && is_array($goods)) return $return."Keine Energie zum Beamen weiterer Waren vorhanden".$al;
		$ssc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$id,1);
		if ($ssc >= $this->ship[storage]) return $return."Kein Lagerraum auf der ".stripslashes($this->ship[name])." vorhanden".$al;
		if (is_array($goods) || is_array($count))
		{
			foreach($goods as $key => $value)
			{
				if (!check_int($count[$key]) || $count[$key] == 0) continue;
				$c = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=".$value,1);
				if ($c == 0) continue;
				if ($this->eps == 0)
				{
					$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
					break;
				}
				if ($count[$key] > $c) $count[$key] = $c;
				// if ($value >= 80 && $value<95)
				// {
					// $lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$this->ship[id]." AND goods_id>=80 AND goods_id<100",1);
					// if ($lt != $value && $lt != 0)
					// {
						// $return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
						// continue;
					// }
					// $tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON b.module_id=a.m10 WHERE a.plans_id=".$this->ship[plans_id],4);
					// if ($tmc[max_torps] == 0)
					// {
						// $return .= "Dieses Schiff kann keine Torpedos laden<br>";
						// continue;
					// }
					// $tt = $this->db->query("SELECT torp_type,type FROM stu_torpedo_types WHERE goods_id=".$value,4);
					// if ($tt[type] > $tmc[torp_type])
					// {
						// $return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
						// continue;
					// }
					// $tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$id,1);
					// if ($tc >= $tmc[max_torps])
					// {
						// $return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
						// continue;
					// }
					// if ($tmc[m10] != 9000) $this->db->query("UPDATE stu_ships SET torp_type=".$tt[torp_type]." WHERE id=".$id);
					// $tc + $count[$key] > $tmc[max_torps] ? $c = $tmc[max_torps]-$tc : $c = $count[$key]; 
				// }
				// elseif ($value >= 110 && $value < 190)
				// {
					// if ($shuttle_stop == 1) continue;
					// $shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value,4);
					// if ($this->ship[is_shuttle] == 1 || $this->ship[max_shuttles] == 0)
					// {
						// $return .= "Dieses Schiff kann keine Shuttles laden<br>";
						// $shuttle_stop = 1;
						// continue;
					// }
					// if ($shud[shuttle_type] > $this->ship[max_shuttle_type])
					// {
						// $return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
						// continue;
					// }
					// if ($this->ship[max_shuttles] <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->ship[id]." AND !ISNULL(b.shuttle_type)",1))
					// {
						// $shuttle_stop = 1;
						// $return .= "Die Shuttlerampe ist belegt<br>";
						// continue;
					// }
					// if ($this->ship[max_cshuttle_type] <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->ship[id]." AND a.goods_id!=".$value." AND converts_to!=".$value." AND !ISNULL(b.shuttle_type)",1))
					// {
						// $return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
						// continue;
					// }
					// $sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<142 AND ships_id=".$this->ship[id],1);
					// $sc + $count[$key] > $this->ship[max_shuttles] ? $c = $this->ship[max_shuttles]-$sc : $c = $count[$key];
				// }
				// else 
					$count[$key] > $c ? $c = $c : $c = $count[$key];
				if ($c > $this->ship[storage]-$ssc) $c = $this->ship[storage]-$ssc;
				if ($c <= 0) continue;
				if (ceil($c/30) > $this->eps) $c = $this->eps*30;
				$e += ceil($c/30);
				$this->eps -= ceil($c/30);
				$this->db->query("START TRANSACTION");
				$this->lowerstorage($this->id,$value,$c);
				$this->shipupperstorage($id,$value,$c);
				$this->db->query("COMMIT");
				$msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value,1)."<br>";
				$ssc += $c;
				if ($ssc >= $this->ship[storage]) break;
			}
		}
		if (!$msg) return $return."Keine Waren zum Beamen vorhanden".$al;
		$this->db->query("UPDATE stu_colonies SET eps=".$this->eps." WHERE id=".$this->id);
		if ($this->uid != $this->ship[user_id]) $this->send_pm($this->uid,$this->ship[user_id],"<b>Die Kolonie ".stripslashes($this->name)." beamt Waren zur ".stripslashes($this->ship[name])."</b><br>".$msg,2);
		return "<b>Es wurden folgende Waren zur ".$this->ship[name]." gebeamt</b><br>".$msg.$return."Energieverbrauch: <b>".$e."</b>".$al;
	}

	function beamtostat($id,$goods="",$count="",$crew=0)
	{
		if ($this->eps == 0) return "Keine Energie vorhanden";
		$this->loadstat($id);
		if ($this->stat == 0) return;
		if (checkcolsectors($this->stat) == 0) return;

		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Beamen ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}

		if ($this->stat[schilde_status] == 1 && $this->stat[user_id] != $this->uid) return "Die Schilde der ".stripslashes($this->stat[name])." sind aktiviert";

		if ($crew > 0 && $this->bev_free > 0) $return = $this->beamcrewupsta($crew);

		if ($this->uid == $this->stat[user_id]) $al = "<br>->> <a href=?p=station&s=show&id=".$id.">Zu Station wechseln</a>";
		if ($this->eps == 0 && is_array($goods)) return $return."Keine Energie zum Beamen weiterer Waren vorhanden".$al;
		$ssc = $this->db->query("SELECT SUM(count) FROM stu_stations_storage WHERE stations_id=".$id,1);
		if ($ssc >= $this->stat[max_storage]) return $return."Kein Lagerraum auf der ".stripslashes($this->stat[name])." vorhanden".$al;
		if (is_array($goods) || is_array($count))
		{
			foreach($goods as $key => $value)
			{
				if (!check_int($count[$key]) || $count[$key] == 0) continue;
				$c = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=".$value,1);
				if ($c == 0) continue;
				if ($this->eps == 0)
				{
					$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
					break;
				}
				if ($count[$key] > $c) $count[$key] = $c;

				 $count[$key] > $c ? $c = $c : $c = $count[$key];
				if ($c > $this->stat[max_storage]-$ssc) $c = $this->stat[max_storage]-$ssc;
				if ($c <= 0) continue;
				if (ceil($c/30) > $this->eps) $c = $this->eps*30;
				$e += ceil($c/30);
				$this->eps -= ceil($c/30);
				$this->db->query("START TRANSACTION");
				$this->lowerstorage($this->id,$value,$c);
				$this->statupperstorage($id,$value,$c);
				$this->db->query("COMMIT");
				$msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value,1)."<br>";
				$ssc += $c;
				if ($ssc >= $this->stat[max_storage]) break;
			}
		}
		if (!$msg) return $return."Keine Waren zum Beamen vorhanden".$al;
		$this->db->query("UPDATE stu_colonies SET eps=".$this->eps." WHERE id=".$this->id);
		if ($this->uid != $this->stat[user_id]) $this->send_pm($this->uid,$this->stat[user_id],"<b>Die Kolonie ".stripslashes($this->name)." beamt Waren zur ".stripslashes($this->stat[name])."</b><br>".$msg,5);
		return "<b>Es wurden folgende Waren zur ".$this->stat[name]." gebeamt</b><br>".$msg.$return."Energieverbrauch: <b>".$e."</b>".$al;
	}

	function beamcrewup($crew)
	{
		if ($this->uid != $this->ship[user_id]) die;
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Beamen ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if ($crew > $this->bev_free) $crew = $this->bev_free;
		if ($crew > $this->ship[max_crew]-$this->ship[crew]) $crew = $this->ship[max_crew]-$this->ship[crew];
		if ($crew <= 0) return "Es wurde keine Crew gebeamt<br>";
		if ($this->eps < ceil($crew/5)) $crew = $this->eps*10;
		$this->eps -= ceil($crew/10);
		$this->db->query("UPDATE stu_ships SET crew=crew+".$crew." WHERE id=".$this->ship['id']." LIMIT 1");
		$this->db->query("UPDATE stu_colonies SET bev_free=bev_free-".$crew.",eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return "<b>Es wurde ".$crew." Crew zur ".$this->ship[name]." gebeamt</b><br>Energieverbrauch: ".ceil($crew/5)."<br>";
	}

	function beamcrewupsta($crew)
	{
		if ($this->uid != $this->stat[user_id]) die;
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Beamen ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if ($crew > $this->bev_free) $crew = $this->bev_free;
		$crew = min($crew,$this->stat[bev_max]-($this->stat[bev_free] + $this->stat[bev_work]));
		if ($crew <= 0) return "Es wurde keine Crew gebeamt<br>";
		if ($this->eps < ceil($crew/5)) $crew = $this->eps*10;
		$this->eps -= ceil($crew/10);
		$this->db->query("UPDATE stu_stations SET bev_free=bev_free+".$crew." WHERE id=".$this->stat['id']." LIMIT 1");
		$this->db->query("UPDATE stu_colonies SET bev_free=bev_free-".$crew.",eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return "<b>Es wurde ".$crew." Crew zur ".$this->stat[name]." gebeamt</b><br>Energieverbrauch: ".ceil($crew/5)."<br>";
	}

	function beamfrom($id,$goods="",$count="",$crew=0)
	{
		if ($this->eps == 0) return "Keine Energie vorhanden";
		$this->loadship($id);
		if ($this->ship == 0) return;
		if (checkcolsector($this->ship) == 0) return;
		if ($this->ship['rumps_id'] == 9) return "Von Konstrukten kann nicht gebeamt werden";
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Beamen ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		// if ($this->ship[level] < 2) return "Von diesem Schiff kann nicht gebeamt werden";
		if ($this->ship['vac_active'] == 1) return "Der Siedler befindet sich im Urlaubsmodus";
		if ($this->ship[schilde_status] == 1 && $this->ship[user_id] != $this->uid) return "Die Schilde der ".stripslashes($this->ship[name])." sind aktiviert";
		if ($this->ship[cloak] == 1) return;
		if ($crew > 0 && $this->ship[crew] > 0) $return = $this->beamcrewdown($crew);
		if ($this->uid == $this->ship[user_id]) $al = "<br>->> <a href=?p=".($this->ship['slots'] > 0 ? "stat" : "ship")."&s=ss&id=".$id.">Zu Schiff wechseln</a>";
		if ($this->eps == 0 && is_array($goods)) return $return."Keine Energie zum Beamen weiterer Waren vorhanden".$al;
		$ssc = $this->db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=".$this->id,1);
		if ($ssc >= $this->max_storage) return $return."Kein Lagerraum auf der Kolonie vorhanden".$al;
		if (is_array($goods) || is_array($count))
		{
			foreach($goods as $key => $value)
			{
				if (!check_int($count[$key]) || $count[$key] == 0) continue;
				if ($value >= 80 && $value < 100 && $this->uid != $this->ship[user_id]) continue;
				if ($value >= 110 && $value < 160 && $this->uid != $this->ship[user_id] && $this->ship[user_id] != 1) continue;
				$c = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$id." AND goods_id=".$value,1);
				if ($c == 0) continue;
				if ($this->eps == 0)
				{
					$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
					break;
				}
				$count[$key] > $c ? $c = $c : $c = $count[$key];
				if ($c > $this->max_storage-$ssc) $c = $this->max_storage-$ssc;
				if ($c <= 0) continue;
				if (ceil($c/30) > $this->eps) $c = $this->eps*30;
				$e += ceil($c/30);
				$this->eps -= ceil($c/30);
				$this->db->query("START TRANSACTION");
				$this->shiplowerstorage($id,$value,$c);
				$this->upperstorage($this->id,$value,$c);
				$this->db->query("COMMIT");
				$msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value,1)."<br>";
				$ssc += $c;
				if ($ssc >= $this->max_storage) break;
			}
		}
		if (!$msg) return $return."Keine Waren zum Beamen vorhanden".$al;
		$this->db->query("UPDATE stu_colonies SET eps=".$this->eps." WHERE id=".$this->id);
		if ($this->uid != $this->ship[user_id]) $this->send_pm($this->uid,$this->ship[user_id],"<b>Die Kolonie ".stripslashes($this->name)." beamt Waren von der ".stripslashes($this->ship[name])."</b><br>".$msg,2);
		return $return."<b>Es wurden folgende Waren von der ".$this->ship[name]." gebeamt</b><br>".$msg."Energieverbrauch: <b>".$e."</b>".$al;
	}

	function beamfromstat($id,$goods="",$count="",$crew=0)
	{
		if ($this->eps == 0) return "Keine Energie vorhanden";
		$this->loadstat($id);
		if ($this->stat == 0) return;
		if (checkcolsectors($this->stat) == 0) return;

		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Beamen ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if ($this->stat['vac_active'] == 1) return "Der Siedler befindet sich im Urlaubsmodus";
		if ($this->stat[schilde_status] == 1 && $this->stat[user_id] != $this->uid) return "Die Schilde der ".stripslashes($this->stat[name])." sind aktiviert";

		if ($crew > 0 && $this->stat[bev_free] > 0) $return = $this->beamcrewdownsta($crew);
		if ($this->uid == $this->stat[user_id]) $al = "<br>->> <a href=?p=station&s=show&id=".$id.">Zur Station wechseln</a>";
		if ($this->eps == 0 && is_array($goods)) return $return."Keine Energie zum Beamen weiterer Waren vorhanden".$al;
		$ssc = $this->db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=".$this->id,1);
		if ($ssc >= $this->max_storage) return $return."Kein Lagerraum auf der Kolonie vorhanden".$al;
		if (is_array($goods) || is_array($count))
		{
			foreach($goods as $key => $value)
			{
				if (!check_int($count[$key]) || $count[$key] == 0) continue;
				if ($value >= 80 && $value < 100 && $this->uid != $this->stat[user_id]) continue;
				if ($value >= 110 && $value < 160 && $this->uid != $this->stat[user_id] && $this->stat[user_id] != 1) continue;
				$c = $this->db->query("SELECT count FROM stu_stations_storage WHERE stations_id=".$id." AND goods_id=".$value,1);
				if ($c == 0) continue;
				if ($this->eps == 0)
				{
					$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
					break;
				}
				$count[$key] > $c ? $c = $c : $c = $count[$key];
				if ($c > $this->max_storage-$ssc) $c = $this->max_storage-$ssc;
				if ($c <= 0) continue;
				if (ceil($c/30) > $this->eps) $c = $this->eps*30;
				$e += ceil($c/30);
				$this->eps -= ceil($c/30);
				$this->db->query("START TRANSACTION");
				$this->statlowerstorage($id,$value,$c);
				$this->upperstorage($this->id,$value,$c);
				$this->db->query("COMMIT");
				$msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value,1)."<br>";
				$ssc += $c;
				if ($ssc >= $this->max_storage) break;
			}
		}
		if (!$msg) return $return."Keine Waren zum Beamen vorhanden".$al;
		$this->db->query("UPDATE stu_colonies SET eps=".$this->eps." WHERE id=".$this->id);
		if ($this->uid != $this->stat[user_id]) $this->send_pm($this->uid,$this->stat[user_id],"<b>Die Kolonie ".stripslashes($this->name)." beamt Waren von der ".stripslashes($this->stat[name])."</b><br>".$msg,5);
		return $return."<b>Es wurden folgende Waren von der ".$this->stat[name]." gebeamt</b><br>".$msg."Energieverbrauch: <b>".$e."</b>".$al;
	}

	function beamcrewdown($crew)
	{
		if ($this->uid != $this->ship[user_id]) die;
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Beamen ist aufgrund einer angreifenden Flotte nicht möglich.";
		}
		if ($crew > $this->ship[crew]) $crew = $this->ship[crew];
		if ($crew > $this->bev_max-$this->bev_free-$this->bev_work) $crew = $this->bev_max-$this->bev_free-$this->bev_work;
		if ($crew <= 0) return "Es wurde keine Crew gebeamt<br>";
		if ($this->eps < ceil($crew/5)) $crew = $this->eps*5;
		$this->eps -= ceil($crew/5);
		$this->db->query("UPDATE stu_ships SET crew=crew-".$crew." WHERE id=".$this->ship['id']." LIMIT 1");
		$this->db->query("UPDATE stu_colonies SET bev_free=bev_free+".$crew.",eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return "<b>Es wurde ".$crew." Crew von der ".$this->ship[name]." gebeamt</b><br>Energieverbrauch: ".ceil($crew/5)."<br>";
	}

	function beamcrewdownsta($crew)
	{
		if ($this->uid != $this->stat[user_id]) die;
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Beamen ist aufgrund einer angreifenden Flotte nicht möglich.";
		}
		if ($crew > $this->stat[bev_free]) $crew = $this->stat[bev_free];
		if ($crew > $this->bev_max-$this->bev_free-$this->bev_work) $crew = $this->bev_max-$this->bev_free-$this->bev_work;
		if ($crew <= 0) return "Es wurde keine Crew gebeamt<br>";
		if ($this->eps < ceil($crew/5)) $crew = $this->eps*5;
		$this->eps -= ceil($crew/5);
		$this->db->query("UPDATE stu_stations SET bev_free=bev_free-".$crew." WHERE id=".$this->stat['id']." LIMIT 1");
		$this->db->query("UPDATE stu_colonies SET bev_free=bev_free+".$crew.",eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return "<b>Es wurde ".$crew." Crew von der ".$this->stat[name]." gebeamt</b><br>Energieverbrauch: ".ceil($crew/5)."<br>";
	}

	function etrans($id,$count)
	{
		if (substr_count($count,".") > 0) addlog(666,$this->uid,"Float-Betrugsversuch (Kolonien)",9);
		if ($this->eps == 0) return "Keine Energie vorhanden";
		$this->loadship($id);
		if ($this->ship == 0) return;
		if (checkcolsector($this->ship) == 0) return;
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Energietransfer ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if ($this->ship['trumfield'] == 1) return "Auf das Trümmerfeld kann keine Energie transferiert werden";
		if ($this->ship['vac_active'] == 1) return "Der Siedler befindet sich im Urlaubsmodus";
		if ($this->ship['schilde_status'] == 1 && $this->ship['user_id'] != $this->uid) return "Die Schilde der ".stripslashes($this->ship[name])." sind aktiviert";
		if ($this->ship['cloak'] == 1)
		{
			if ($this->ship['user_id'] == $this->uid) return "Die ".$this->ship['name']." ist getarnt. Energietransfer nicht möglich";
			else return;
		}
		if ($this->ship['eps'] >= $this->ship['max_eps']) return "Das EPS der ".stripslashes($this->ship['name'])." ist bereits voll".($this->uid == $this->ship[user_id] ? "<br>->> <a href=?p=".($this->ship['slots'] == 0 ? "ship" : "stat")."&s=ss&id=".$id.">Zu Schiff wechseln</a>" : "");
		if ($count == "max") $count = $this->eps;
		if ($count > $this->ship[max_eps]-$this->ship['eps']) $count = $this->ship['max_eps']-$this->ship['eps'];
		if ($count > $this->eps) $count = $this->eps;
		$this->db->query("UPDATE stu_colonies SET eps=eps-".$count." WHERE id=".$this->id." LIMIT 1");
		$this->db->query("UPDATE stu_ships SET eps=eps+".$count." WHERE id=".$id." LIMIT 1");
		if ($this->uid == $this->ship['user_id']) $al = "<br>->> <a href=?p=".($this->ship['slots'] == 0 ? "ship" : "stat")."&s=ss&id=".$id.">Zur ".stripslashes($this->ship[name])." wechseln</a>";
		else $this->send_pm($this->uid,$this->ship['user_id'],"<b>Die Kolonie ".stripslashes($this->name)." hat in Sektor ".$this->sx."|".$this->sy." (".$this->db->query("SELECT name FROM stu_systems WHERE systems_id=".$this->systems_id." LIMIT 1",1)." System) ".$count." Energie zur ".stripslashes($this->ship['name'])." transferiert</b><br>",3);
		return "Es wurde ".$count." Energie zur ".stripslashes($this->ship['name'])." transferiert".$al;
	}

	function loadorbititems() { $this->result = $this->db->query("SELECT a.id,a.user_id,a.fleets_id,a.name,a.rumps_id,a.eps,a.max_eps,a.batt,a.max_batt,a.huelle,a.max_huelle,b.name as rname,b.slots,b.storage,b.trumfield,c.user,SUM(d.count) as ss
			FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id LEFT JOIN stu_ships_storage as d ON a.id=d.ships_id WHERE a.sx=".$this->sx." AND
			a.sy=".$this->sy." AND a.systems_id=".$this->systems_id." AND (ISNULL(a.cloak) OR a.cloak!='1' OR cloak='' OR (a.cloak='1' AND a.user_id=".$this->uid.")) GROUP BY a.id ORDER BY a.fleets_id DESC,a.rumps_id,a.id"); }

	function swapactivatemode($field)
	{
		$this->loadfield($field,$this->id);
		if ($this->fdd == 0) return;
		if ($this->fdd[buildings_id] == 0 || $this->fdd[buildings_id] == 1 || $this->fdd[aktiv] <= 1) return;
		$result =$this->db->query("SELECT value FROM stu_colonies_actions WHERE var='db' AND colonies_id=".$this->id." AND value='".$field."'",1);
		if ($result > 0) $this->db->query("DELETE FROM stu_colonies_actions WHERE colonies_id=".$this->id." AND var='db' AND value=".$field);
		else $this->db->query("INSERT INTO stu_colonies_actions (colonies_id,var,value) VALUES ('".$this->id."','db','".$field."')");
		return "Aktivierungseinstellung für Feld ".$field." geändert";
	}

	function getpossibleupgrades($field) { $this->result = $this->db->query("SELECT a.buildings_id,a.name FROM stu_buildings as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." LEFT JOIN stu_field_build as c ON c.buildings_id=a.buildings_id WHERE a.level<=".$this->sess["level"]." AND a.upgrade_from=".$this->fdd[buildings_id]." AND c.type=".$field." AND (b.research_id>0 OR a.research_id=0)"); }

	function upgradebuilding($building,$field)
	{
		$this->loadfield($field,$this->id);
		if ($this->fdd == 0) return;
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			if ($this->is_moon == 1 && $field < 15) return "Bauen im Orbit ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
			if ($this->is_moon == 0 && $field < 21) return "Bauen im Orbit ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
			if (($this->schilde_status != '1') && ($amode > 1)) return "Bauen auf der Oberfläche ist aufgrund einer angreifenden Flotte nicht möglich.";
		}
		if ($this->fdd['buildings_id'] == 0 || $this->fdd['aktiv'] > 1) return;
		if ($this->db->query("SELECT COUNT(*) FROM stu_field_build WHERE type=".$this->fdd['type']." AND buildings_id=".$building,1) == 0) return;
		$bd = $this->db->query("SELECT buildings_id,eps_cost FROM stu_buildings WHERE upgrade_from=".$this->fdd['buildings_id']." AND buildings_id=".$building." LIMIT 1",4);
		if ($bd == 0) return;
		if ($this->eps < $bd['eps_cost']) return "Für das Upgrade wird ".$bd['eps_cost']." Energie benötigt - Vorhanden ist nur ".$this->eps;
		if ($this->fdd['buildings_id'] == 1 || $this->fdd['buildings_id'] == 4)
		{
			if ($this->max_eps-$this->fdd['eps'] < $bd['eps_cost']) return "Nach dem Abriss des Gebäudes steht nicht mehr Energie für das Upgrade zur Verfügung";
		}
		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_buildings_cost as a LEFT JOIN stu_colonies_storage as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.buildings_id=".$building." ORDER BY c.sort");
		while($cost=mysql_fetch_assoc($result))
		{
			if ($cost['vcount'] < $cost['count'])
			{
				return "Es werden ".$cost['count']." ".$cost['name']." benötigt - Vorhanden sind nur ".(!$cost['vcount'] ? 0 : $cost['vcount']);
			}
		}
		$this->uflag = 1;
		return $this->build($building,$field);
	}

	function loadships()
	{
		global $_GET;
		$this->result = $this->db->query("SELECT a.id,a.name,a.rumps_id,a.user_id,a.huelle,a.max_huelle,a.cloak,b.user,b.allys_id,b.level,b.vac_active FROM stu_ships as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.systems_id=".$this->systems_id." AND a.sx=".$_GET[sx]." AND a.sy=".$_GET[sy]." ORDER BY a.fleets_id,a.id");
	}

	function loadcto() { $this->result = $this->db->query("SELECT a.goods_id,a.name,b.mode,c.count FROM stu_goods as a LEFT JOIN stu_colonies_trade as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.view=1 ORDER BY a.sort"); }

	function changecto($good,$mod)
	{
		$this->db->query("DELETE FROM stu_colonies_trade WHERE colonies_id=".$this->id);
		foreach($good as $key => $value)
		{
			if (!check_int($value) || !check_int($mod[$value]) || ($mod[$value] != 1 && $mod[$value] != 2)) continue;
			if ($this->db->query("SELECT view FROM stu_goods WHERE goods_id=".$value,1) == 0) die(show_error(902));
			$this->db->query("INSERT INTO stu_colonies_trade (colonies_id,mode,goods_id) VALUES ('".$this->id."','".$mod[$value]."','".$value."')");
		}
		return "Angebote geändert";
	}

	function loadgschaltung()
	{
		$this->orbit_r = $this->db->query("SELECT a.field_id,a.aktiv,b.name FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.buildings_id>0 AND b.is_activateable=1 AND a.colonies_id=".$this->id." AND a.field_id<".($this->is_moon == 0 ? 19 : 15)." ORDER BY b.name");
		$this->field_r = $this->db->query("SELECT a.field_id,a.aktiv,b.name FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.buildings_id>0 AND a.buildings_id!=1 AND b.is_activateable=1 AND a.colonies_id=".$this->id." AND a.field_id>".($this->is_moon == 0 ? 18 : 14)." AND a.field_id<".($this->is_moon == 0 ? 81 : 50)." ORDER BY b.name");
		if ($this->is_moon == 0) $this->ground_r = $this->db->query("SELECT a.field_id,a.aktiv,b.name FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.buildings_id>0 AND b.is_activateable=1 AND a.colonies_id=".$this->id." AND a.field_id>80 ORDER BY b.name");
	}

	function getsystembyid($id) { return $this->db->query("SELECT systems_id,cx,cy,sr,type,name FROM stu_systems WHERE systems_id=".$id." LIMIT 1",4); }

	// function launchcolship()
	function createcolship()
	{
		
		if (($this->sess['level'] >= 5)) return "Starrampe kann ab Level 5 nicht mehr benutzt werden.";
		
		if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=8 AND aktiv<2 AND colonies_id=".$this->id,1) == 0) return;
		$sys = $this->getsystembyid($this->systems_id);
		// $data = $this->db->query("SELECT m1,m2,m3,m4,m5,m7,m8,m11  FROM stu_ships_buildplans WHERE plans_id=1",4);
		// $this->getrumpbyid(1);

		$ecost = 50;
		$bmcost = 50;
		$alucost = 25;
		$duracost = 25;
		$deutcost = 50;
		$workers = 15;
		
		$rumpid = 6500 + $_SESSION['race'];
		
		$planets = $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING (colonies_classes_id) WHERE a.user_id=".$this->uid." AND ISNULL(b.is_moon)",1);
		$moons = $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING (colonies_classes_id) WHERE a.user_id=".$this->uid." AND b.is_moon='1'",1);
		$colships = $this->db->query("SELECT COUNT(id) FROM stu_ships WHERE user_id=".$this->uid." AND (plans_id=111 OR plans_id=112 OR plans_id=113)",1);
		
		if ($colships > 0) return "Es kann kein weiteres Schiff über die Startrampe gebaut werden, solange ein anderes Kolonieschiff aktiv ist.";
		
		if (($this->sess['level'] == 3) && ($planets >= 1) && ($moons >= 1)) return "Kolonielimit für diesen Level wurde bereits erreicht!";
		if (($this->sess['level'] == 4) && ($planets >= 2) && ($moons >= 2)) return "Kolonielimit für diesen Level wurde bereits erreicht!";
		// if (($this->sess['level'] == 5) && ($planets >= 4) && ($moons >= 6)) return "Kolonielimit für diesen Level wurde bereits erreicht!";
		if (($this->sess['level'] == 1)) return "Äh, wie hast du das gemacht?!?";
		if (($this->sess['level'] == 2)) return "Äh, wie hast du das gemacht?!?";


		
		if ($this->eps < $ecost) return "Zum Start wird ".$ecost." Energie benötigt - Vorhanden ist nur ".$this->eps;	
		if ($this->bev_free < $workers) return "Zum Start werden ".$workers." Arbeiter benötigt - Frei sind nur ".$this->bev_free;	
		
		$bm = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=2",1);
		if ($bm < $bmcost) return "Es werden ".$bmcost." Baumaterial benötigt - Vorhanden sind nur ".$bm;
		$alu = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=4",1);
		if ($alu < $alucost) return "Es werden ".$alucost." Transparentes Aluminium benötigt - Vorhanden sind nur ".$alu;
		$dura = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=21",1);
		if ($dura < $duracost) return "Es werden ".$duracost." Duranium benötigt - Vorhanden sind nur ".$dura;
		$deut = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=5",1);
		if ($deut < $deutcost) return "Es werden ".$deutcost." Deuterium benötigt - Vorhanden sind nur ".$deut;		
		
		// $amode = $this->getcolattackstate($this->id);
		// if ($amode > 0) {
			// return "Starten von Schiffen ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		// }
		
		$buildplan = 110 + $_SESSION['race'];
		// Array ( [systems_id] => 32 [cx] => 68 [cy] => 20 [sr] => 25 [typ
		$shipdata = getShipValuesForBuildplan($buildplan);
		
		// return ;
		
		$query = "INSERT INTO `stu_ships` (`user_id`, `rumps_id`, `plans_id`, `fleets_id`, `systems_id`, `cx`, `cy`, `sx`, `sy`, `direction`, `name`, `alvl`, `warp`, `warpcore`, `max_warpcore`, `warpable`, `warpfields`, `max_warpfields`, `cloak`, `cloakable`, `eps`, `max_eps`, `reaktor`, `batt`, `max_batt`, `huelle`, `max_huelle`, `schilde`, `max_schilde`, `schilde_status`, `lss_range`, `kss_range`, `traktor`, `traktormode`, `dock`, `crew`, `max_crew`, `min_crew`, `nbs`, `lss`, `trumps_id`, `replikator`, `phaser`, `cfield`, `torp_type`, `wea_phaser`, `wea_torp`, `shuttle_type`, `is_hp`, `is_rkn`, `points`, `lastmaintainance`, `still`, `maintain`, `batt_wait`, `hud`, `assigned`, `slots`) VALUES
			(".$this->uid.", ".$shipdata['rumps_id'].", ".$buildplan.", 0, ".$this->systems_id.", ".$sys[cx].", ".$sys[cy].", ".$this->sx.", ".$this->sy.", '3', 'Kolonieschiff', '1', '0', ".$shipdata[warpcore].", ".$shipdata[warpcore].", '1', ".$shipdata[warpfields].", ".$shipdata[warpfields].", 0, NULL, ".$shipdata[eps].", ".$shipdata[eps].", ".$shipdata[reaktor].", 0, 0, ".$shipdata[huelle].", ".$shipdata[huelle].", ".$shipdata[schilde].", ".$shipdata[schilde].", 0, ".$shipdata[lss_range].", ".$shipdata[kss_range].", 0, '', 0, 15, 15, 15, '1', '1', 0, '', 0, 1, 0, '0', '0', 0, '0', 0, '0', 0, 0, 0, 0, '1', 0, 0);";		

			
			
		$this->lowerstorage($this->id,2,$bmcost);
		$this->lowerstorage($this->id,4,$alucost);
		$this->lowerstorage($this->id,21,$duracost);
		$this->lowerstorage($this->id,5,$deutcost);
			
		$this->db->query("START TRANSACTION");
			
		$this->db->query("UPDATE stu_colonies SET eps=eps-".$ecost.",bev_free=bev_free-".$workers." WHERE id=".$this->id);
		$this->db->query($query);
			
		$this->db->query("COMMIT");
		
		// $i = 0;
		// while($i<=11)
		// {
			// if ($data["m".$i] == 0)
			// {
				// $i++;
				// continue;
			// }
			// $mod = $this->db->query("SELECT type,huelle,schilde,buildtime,eps,lss,kss FROM stu_modules WHERe module_id=".$data["m".$i],4);
			// $huelle += $mod[huelle]*$this->rump["m".$mod[type]."c"];
			// $schilde += $mod[schilde]*$this->rump["m".$mod[type]."c"];
			// $eps += $mod[eps]*$this->rump["m".$mod[type]."c"];
			// $lss += $mod[lss];
			// $kss += $mod[kss];
			// $i++;
		// }
		
		// if ($this->bev_free < $this->rump["min_crew"]) return "Zum Start werden ".$this->rump["min_crew"]." Arbeitslose benötigt - Vorhanden sind nur ".$this->bev_free;
		// $batt = $this->rump["m8c"]*2;
		// $this->db->query("START TRANSACTION");
		// 
		// $this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=0,integrity=0 WHERE buildings_id=8 AND colonies_id=".$this->id);
		// $this->db->query("INSERT INTO stu_ships (user_id,rumps_id,plans_id,cx,cy,sx,sy,systems_id,name,warpable,warpcore,eps,max_eps,batt,max_batt,huelle,max_huelle,schilde,max_schilde,lss_range,kss_range,crew,max_crew,min_crew,lss,phaser,cfield,lastmaintainance) VALUES 
		// ('".$this->uid."','1','1','".$sys[cx]."','".$sys[cy]."','".$this->sx."','".$this->sy."','".$this->systems_id."','Kolonisationsschiff','1','500','".$eps."','".$eps."','0','".$batt."','".$huelle."','".$huelle."','0','".$schilde."','".$lss."','".$kss."','".$this->rump["min_crew"]."','10','3','0','0','".$this->db->query("SELECT type FROM stu_sys_map WHERE systems_id=".$this->systems_id." AND sx=".$this->sx." AND sy=".$this->sy,1)."','".time()."')",5);
		// $this->db->query("COMMIT");
		return "Das Kolonieschiff wurde erfolgreich gestartet";
	}

	function gschaltung($mode)
	{
		switch ($mode)
		{
			default:
				return;
			case 1:
				$qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=0 AND a.colonies_id=".$this->id." AND b.eps_proc>0 AND a.buildings_id!=1";
				$am = 1;
				break;
			case 2:
				$qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$this->id." AND b.eps_proc>0 AND a.buildings_id!=1";
				$am = 2;
				break;
			case 3:
				$qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=0 AND a.colonies_id=".$this->id." AND b.eps_proc<0 AND a.buildings_id!=1";
				$am = 1;
				break;
			case 4:
				$qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$this->id." AND b.eps_proc<0 AND a.buildings_id!=1";
				$am = 2;
				break;
			case 5:
				global $_GET;
				if (!check_int($_GET['go'])) return;
				if ($_GET['wpro'] != 1 && $_GET['wver'] != 1) return "Bitte eine Option wählen";
				if ($_GET['wpro'] == 1 && $_GET['wver'] != 1) $qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) LEFT JOIN stu_buildings_goods as c USING(buildings_id) WHERE a.colonies_id=".$this->id." AND b.is_activateable=1 AND a.buildings_id!=1 AND c.goods_id=".$_GET['go']." AND c.count>0";
				if ($_GET['wver'] == 1 && $_GET['wpro'] != 1) $qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) LEFT JOIN stu_buildings_goods as c USING(buildings_id) WHERE a.colonies_id=".$this->id." AND b.is_activateable=1 AND a.buildings_id!=1 AND c.goods_id=".$_GET['go']." AND c.count<0";
				if ($_GET['wpro'] == 1 && $_GET['wver'] == 1) $qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) LEFT JOIN stu_buildings_goods as c USING(buildings_id) WHERE a.colonies_id=".$this->id." AND b.is_activateable=1 AND a.buildings_id!=1 AND c.goods_id=".$_GET['go'];
				if ($_GET['am'] == "Aktivieren") $am = 1;
				if ($_GET['am'] == "Deaktivieren") $am = 2;
				break;
			case 6:
				$qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=0 AND a.colonies_id=".$this->id." AND b.bev_pro>0 AND a.buildings_id!=1";
				$am = 1;
				break;
			case 7:
				$qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$this->id." AND b.bev_pro>0 AND a.buildings_id!=1";
				$am = 2;
				break;
			case 8:
				$qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=0 AND a.colonies_id=".$this->id." AND b.bev_use>0 AND a.buildings_id!=1 AND a.buildings_id!=100 AND a.buildings_id!=101 AND a.buildings_id!=46 AND a.buildings_id!=47";
				$am = 1;
				break;
			case 9:
				$qry = "SELECT a.field_id,b.name,b.eps,b.eps_proc,b.bev_pro,b.bev_use FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=".$this->id." AND b.bev_use>0 AND a.buildings_id!=1 AND a.buildings_id!=100 AND a.buildings_id!=101 AND a.buildings_id!=46 AND a.buildings_id!=47";
				$am = 2;
				break;
			case 10:
				$fz = 82;
				$msg = "Neues Forschungsziel: Verarbeitung";
				break;
			case 11:
				$fz = 81;
				$msg = "Neues Forschungsziel: Technik";
				break;
			case 12:
				$fz = 80;
				$msg = "Neues Forschungsziel: Konstruktion";
				break;
		}
		if ($qry)
		{
			$result = $this->db->query($qry);
			while($data=mysql_fetch_assoc($result))
			{
				if ($am) $am == 1 ? $res = $this->activatebuilding($data['field_id']) : $res = $this->deactivatebuilding($data['field_id']);
				if ($res) $msg .= $res."<br>";
				unset($this->fdd);
			}
		}
		if ($fz) $this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=".$fz." WHERE colonies_id=".$this->id." AND (buildings_id=80 OR buildings_id=81 OR buildings_id=82)");
		return $msg;
	}

	function loadcolshiprump() { $this->result = $this->db->query("SELECT b.rumps_id,b.name,COUNT(c.id) as shid FROM stu_rumps_user as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships as c ON a.rumps_id=c.rumps_id AND c.user_id=".$this->uid." WHERE a.user_id=".$this->uid." AND  (a.rumps_id>6500 AND a.rumps_id<6600) GROUP BY a.rumps_id ORDER BY b.sort,b.rumps_id"); }
	
	function loadpossibleoldrumps() { $this->result = $this->db->query("SELECT b.rumps_id,b.name,COUNT(c.id) as shid FROM stu_rumps_user as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships as c ON a.rumps_id=c.rumps_id AND c.user_id=".$this->uid." WHERE a.user_id=".$this->uid." AND b.slots=0 AND a.rumps_id!=5 AND (a.rumps_id<1200 OR a.rumps_id>2000) GROUP BY a.rumps_id ORDER BY b.sort,b.rumps_id"); }

	function loadpossiblerumps() { $this->result = $this->db->query("SELECT b.rumps_id,b.name,COUNT(c.id) as shid FROM stu_rumps_user as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships as c ON a.rumps_id=c.rumps_id AND c.user_id=".$this->uid." WHERE a.user_id=".$this->uid." AND b.slots=0 AND b.rumps_id!=5 GROUP BY a.rumps_id ORDER BY b.sort,b.rumps_id"); }

	function loadbuildplans($m) { $this->result = $this->db->query("SELECT a.*,COUNT(b.id)+COUNT(c.rumps_id) as idc FROM stu_ships_buildplans as a LEFT JOIN stu_ships as b ON b.plans_id=a.plans_id AND b.user_id=".$this->uid." LEFT JOIN stu_ships_buildprogress as c ON c.plans_id=a.plans_id AND c.user_id=".$this->uid." LEFT JOIN stu_rumps as d ON a.rumps_id=d.rumps_id WHERE a.user_id=".$this->uid." AND d.slots=0 GROUP BY a.plans_id,c.plans_id ORDER BY a.hidden,a.rumps_id,a.plans_id LIMIT ".$m.",10"); }

	function checkrump($rumps_id)
	{
		if ($this->db->query("SELECT user_id FROM stu_rumps_user WHERE user_id=".$this->uid." AND rumps_id=".$rumps_id,1) == 0) return FALSE;
		return TRUE;
	}

	function getrumpbyid($rumps_id) { $this->rump = $this->db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$rumps_id,4); }

	function getmodbylvl($minlvl,$maxlvl,$type) { return $this->db->query("SELECT a.module_id,a.special_id1,a.special_id2,a.type,a.level,a.name,a.huelle,a.schilde,a.reaktor,a.wkkap,a.eps,a.evade_val,a.lss,a.kss,cloak_val,detect_val,a.hit_val,a.torps,a.points,a.torp_type,a.warp_capability,a.buildtime,a.maintaintime,b.count FROM stu_modules as a LEFT JOIN stu_colonies_storage as b ON a.module_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_researched as c ON a.research_id=c.research_id AND c.user_id=".$this->uid." WHERE a.type=".$type." AND a.level>=".$minlvl." AND a.level<=".$maxlvl." AND (a.viewable='1' OR !ISNULL(b.count)) AND (a.research_id=0 OR (a.research_id>0 AND (!ISNULL(c.user_id) OR !ISNULL(b.count)))) ORDER BY a.level,a.module_id"); }

	function getmods($lvl,$type) { return $this->db->query("SELECT a.* FROM stu_modules as a LEFT JOIN stu_colonies_storage as b ON a.module_id=b.goods_id AND b.colonies_id=".$this->id." WHERE a.type=".$type." AND a.level=".$lvl." AND (a.viewable='1') AND b.count > 0 ORDER BY a.level,a.module_id"); }
	function getmodsnolvl($type) { return $this->db->query("SELECT a.* FROM stu_modules as a WHERE a.type=".$type." AND (a.viewable='1') ORDER BY a.level,a.module_id"); }
	function getmodcolony() { return $this->db->query("SELECT a.* FROM stu_modules as a WHERE a.subtype='colony' AND (a.viewable='1') ORDER BY a.level,a.module_id"); }
	
	function terraform($field,$to_field)
	{
		$this->loadfield($field,$this->id);
		if ($this->fdd['buildings_id'] > 0) return;
		if ($this->fdd['terraforming_id'] > 0) return;
		$data = $this->db->query("SELECT a.terraforming_id,a.name,a.ecost,a.uglift,a.flimit,a.t_time FROM stu_terraforming as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." WHERE a.v_feld=".$this->fdd[type]." AND a.z_feld=".$to_field." AND (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) LIMIT 1",4);
		if ($data == 0) return;
		if ($data['ecost'] > $this->eps) return "Zum Terraforming wird ".$data['ecost']." Energie benötigt - Vorhanden ist nur ".$this->eps;
		if ($data['uglift'] == 1 && $this->check_ulift($this->id) == 0) return "Zum Terraforming wird ein aktivierter Untergrundlift benötigt";
		if ($data['flimit'] > 0 && $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE type=".$to_field." AND colonies_id=".$this->id,1) >= $data['flimit']) return "Dieses Terraforming ist maximal ".$data['flimit']." mal möglich";
		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_terraforming_cost as a LEFT JOIN stu_colonies_storage as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.terraforming_id=".$data[terraforming_id]." ORDER BY c.sort");
		// if ($data['terraforming_id'] == 9)
		// {
			// if ($this->colonies_classes_id == 5 && $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND type=21",1) >= 3) return "Dieses Terraforming ist hier nur 3 Mal möglich";
			// if ($this->colonies_classes_id == 24 && $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND type=21",1) >= 1) return "Dieses Terraforming ist hier nur 1 Mal möglich";
			// if ($this->colonies_classes_id != 5 && $this->colonies_classes_id != 24) return "Dieses Terraforming ist hier nicht möglich";
		// }
		// if ($data['terraforming_id'] == 10)
		// {
			// if ($this->colonies_classes_id == 7 && $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND type=22",1) >= 4) return "Dieses Terraforming ist hier nur 4 Mal möglich";
			// if ($this->colonies_classes_id == 26 && $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND type=22",1) >= 2) return "Dieses Terraforming ist hier nur 2 Mal möglich";
			// if ($this->colonies_classes_id != 7 && $this->colonies_classes_id != 26) return "Dieses Terraforming ist hier nicht möglich";
		// }
		// if ($data['terraforming_id'] == 11)
		// {
			// if ($this->colonies_classes_id == 8 && $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND type=23",1) >= 5) return "Dieses Terraforming ist hier nur 5 Mal möglich";
			// if ($this->colonies_classes_id == 27 && $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND type=23",1) >= 3) return "Dieses Terraforming ist hier nur 3 Mal möglich";
			// if ($this->colonies_classes_id != 8 && $this->colonies_classes_id != 27) return "Dieses Terraforming ist hier nicht möglich";
		// }
		// if ($to_field >=41 && $to_field <= 45)
		// {
			// if ($this->is_moon == 1) return "Dieses Terraforming ist auf einem Mond nicht möglich";
			// $ground = $field-19;
			// $to = floor($ground/18);
			// $tl = floor($ground/9);
			// $rest = $ground-$tl*9;
			// $nf = 73+$to*9+$rest;
			// $nfd = $this->db->query("SELECT type FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND field_id=".$nf,1);
			// if ($nfd != 71 && $nfd != 53) return "Dieses Terraforming ist auf diesem Feld nicht möglich";
			// $this->db->query("START TRANSACTION");
			// $this->db->query("INSERT INTO stu_colonies_terraforming (colonies_id,field_id,terraforming_id,terraformtime) VALUES ('".$this->id."','".$nf."','99','".(time()+$data[t_time])."')");
		// }
		$this->db->query("START TRANSACTION");
		while($cost=mysql_fetch_assoc($result))
		{
			if ($cost[vcount] < $cost['count'])
			{
				return "Es werden ".$cost['count']." ".$cost[name]." benötigt - Vorhanden sind nur ".(!$cost[vcount] ? 0 : $cost[vcount]);
			}
		}
		$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_terraforming_cost as a LEFT JOIN stu_colonies_storage as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.terraforming_id=".$data[terraforming_id]." ORDER BY c.sort");
		while($cost=mysql_fetch_assoc($result))
		{
			$this->lowerstorage($this->id,$cost[goods_id],$cost['count']);
		}
		$this->db->query("UPDATE stu_colonies SET eps=eps-".$data[ecost]." WHERE id=".$this->id." LIMIT 1");
		$this->db->query("INSERT INTO stu_colonies_terraforming (colonies_id,field_id,terraforming_id,terraformtime) VALUES ('".$this->id."','".$field."','".$data[terraforming_id]."','".(time()+$data[t_time])."')");
		$this->db->query("COMMIT");
		return "Terraforming (".$data[name].") hat begonnen - Fertigstellung am ".date("d.m.Y H:i",time()+$data[t_time]);
	}

	function loadresearchpoints() { $this->research = $this->db->query("SELECT SUM(a.research_t) as rt,SUM(a.research_k) as rk,SUM(a.research_v) as rv FROM stu_buildings as a LEFT JOIN stu_colonies_fielddata as b ON a.buildings_id=b.buildings_id WHERE b.aktiv=1 AND b.colonies_id=".$this->id,4); }

	function cycleresearchbuildings($field,$new_id)
	{
		$this->loadfield($field,$this->id);
		if ($this->fdd[buildings_id] != 80 && $this->fdd[buildings_id] != 81 && $this->fdd[buildings_id] != 82) return;
		if ($this->fdd[buildings_id] == 80 && $new_id != 81 && $new_id != 82) return;
		if ($this->fdd[buildings_id] == 81 && $new_id != 80 && $new_id != 82) return;
		if ($this->fdd[buildings_id] == 82 && $new_id != 80 && $new_id != 81) return;
		$this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=".$new_id." WHERE colonies_id=".$this->id." AND field_id=".$field);
		return "Forschungsziel geändert";
	}

	function seteinwanderung($ew)
	{
		$this->db->query("UPDATE stu_colonies SET einwanderung='".$ew."' WHERE id=".$this->id);
		return "Einwanderungseinstellung geändert";
	}

	function seteinwanderungslimit($scount)
	{
		if ($scount > 9999) return;
		$this->db->query("UPDATE stu_colonies SET bevstop=".$scount." WHERE id=".$this->id);
		if ($scount == 0) return "Einwanderungsgrenze aufgehoben";
		return "Einwanderungsgrenze auf ".$scount." festgelegt";
	}

	function loadrepaircost() { $this->rpc = $this->db->query("SELECT a.goods_id,CEILING((a.count/".$this->fdd[maxintegrity].")*".($this->fdd[maxintegrity]-$this->fdd[integrity]).") as count,b.count as vcount,c.name FROM stu_buildings_cost as a LEFT JOIN stu_colonies_storage as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.buildings_id=".$this->fdd[buildings_id]." ORDER BY c.sort"); }

	function repairbuilding($fid)
	{
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			if ($this->is_moon == 1 && $field < 15) return "Reparieren im Orbit ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
			if ($this->is_moon == 0 && $field < 19) return "Reparieren im Orbit ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
			if (($this->schilde_status != '1') && ($amode > 1)) return "Reparieren auf der Oberfläche ist aufgrund einer angreifenden Flotte nicht möglich.";
		}
		$ecost = $this->db->query("SELECT CEILING((eps_cost/".$this->fdd[maxintegrity].")*".($this->fdd[maxintegrity]-$this->fdd[integrity]).") FROM stu_buildings WHERE buildings_id=".$this->fdd[buildings_id],1);
		if ($this->eps < $ecost) return "Für die Reparatur wird ".$ecost." Energie benötigt - Vorhanden ist nur ".$this->eps;
		while($cost=mysql_fetch_assoc($this->rpc))
		{
			if ($cost[vcount] < $cost['count'])
			{
				return "Es werden ".$cost['count']." ".$cost[name]." benötigt - Vorhanden sind nur ".(!$cost[vcount] ? 0 : $cost[vcount]);
			}
		}
		$this->loadrepaircost();
		while($cost=mysql_fetch_assoc($this->rpc))
		{
			$this->lowerstorage($this->id,$cost[goods_id],$cost['count']);
		}
		$this->db->query("UPDATE stu_colonies SET eps=eps-".$ecost." WHERE id=".$this->id);
		$this->db->query("UPDATE stu_colonies_fielddata SET integrity=".$this->fdd[maxintegrity]." WHERE colonies_id=".$this->id." AND field_id=".$fid." LIMIT 1");
		$this->db->query("COMMIT");
		return "Gebäude repariert";
	}

	function giveupcol($id)
	{
		$data = $this->db->query("SELECT id,colonies_classes_id,fieldstring FROM stu_colonies WHERE id=".$id." AND user_id=".$this->uid,4);
		if ($data == 0) die(show_error(902));
		$this->db->query("DELETE FROM stu_colonies_storage WHERE colonies_id=".$id);
		$this->db->query("DELETE FROM stu_colonies_trade WHERE colonies_id=".$id);
		$this->db->query("DELETE FROM stu_colonies_actions WHERE colonies_id=".$id);
		$this->db->query("DELETE FROM stu_colonies_fielddata WHERE colonies_id=".$id);
		$this->db->query("DELETE FROM stu_ships_buildprogress WHERE colonies_id=".$id);
		global $global_path;
		// include_once($global_path."inc/gencol.inc.php");
		// generate_colony($id,$data[colonies_classes_id]);
		
		$this->setColonyFields($id,$data[fieldstring]);
		
		$this->db->query("UPDATE stu_colonies SET bev_max=0,bev_free=0,bev_work=0,max_eps=0,eps=0,user_id=1,name='',max_storage=0,lastrw='0',bevstop=0,einwanderung='0',schilde_status='0',schilde=0,max_schilde=0,ground_enabled = 0 WHERE id=".$id);
		if ($this->uid < 100) return "Kolonie aufgegeben";
		$cs = $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE rumps_id=1 AND user_id=".$this->uid,1);
		$cc = $this->db->query("SELECT COUNT(*) FROM stu_colonies WHERE user_id=".$this->uid,1);
		if ($cs == 0 && $cc == 0)
		{
			global $_SESSION;
			$_SESSION['level'] = 0;
			$this->db->query("UPDATE stu_user SET level='0' WHERE id=".$this->uid);
		}
		return "Kolonie aufgegeben";
	}

	function digground()
	{
		return "deaktiviert";
		if (!$this->id) return "Fehler 39";
		// if (!check_int($id)) return;
		$ge = $this->db->query("SELECT ground_enabled FROM stu_colonies WHERE id=".$this->id."",1);
		if ($ge == 1) return "Untergrund bereits freigelegt!";
		// if ($data == 0) die(show_error(902));
		
		$re = $this->db->query("SELECT research_id FROM stu_researched WHERE user_id=".$this->uid." AND research_id = 7001",1);
		if (!$re) return "Fehler 17";
		
		$ecost = 150;
		$bmcost 	=  50;
		$alucost 	=  50;
		$duracost 	= 100;
		$deutcost 	= 100;
		$amcost 	= 100;
		
		
		if ($this->eps < $ecost) return "Es wird ".$ecost." Energie benötigt - Vorhanden ist nur ".$this->eps;	
		
		$bm = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=2",1);
		if ($bm < $bmcost) return "Es werden ".$bmcost." Baumaterial benötigt - Vorhanden sind nur ".$bm;
		$alu = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=4",1);
		if ($alu < $alucost) return "Es werden ".$alucost." Transparentes Aluminium benötigt - Vorhanden sind nur ".$alu;
		$dura = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=21",1);
		if ($dura < $duracost) return "Es werden ".$duracost." Duranium benötigt - Vorhanden sind nur ".$dura;
		$deut = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=5",1);
		if ($deut < $deutcost) return "Es werden ".$deutcost." Deuterium benötigt - Vorhanden sind nur ".$deut;		
		$am = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=6",1);
		if ($am < $damcost) return "Es werden ".$amcost." Deuterium benötigt - Vorhanden sind nur ".$am;		
		
		$this->lowerstorage($this->id,2,$bmcost);
		$this->lowerstorage($this->id,4,$alucost);
		$this->lowerstorage($this->id,21,$duracost);
		$this->lowerstorage($this->id,5,$deutcost);
		$this->lowerstorage($this->id,6,$amcost);
		
		$this->db->query("UPDATE stu_colonies SET eps = eps - 150, ground_enabled = 1 WHERE id=".$this->id);

		return "Zugang zum Untergrund wurde freigelegt!";
	}
	
	function setColonyFields($id,$encodedFields) {
		global $db;
		if ($encodedFields != "XX") {
			$i = 1;
			while (strlen($encodedFields) > 0) {
			
				$nextfield = hexdec(substr($encodedFields,0,2));
				$encodedFields = substr($encodedFields,2);
			
				 $db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$id."','".$i."','".$nextfield."')");
				 $i++;
			}
		}
	}
	
	function getmodbyid($minlvl,$maxlvl,$type,$id) { return $this->db->query("SELECT a.module_id,a.special_id1,a.special_id2,a.type,a.level,a.name,a.huelle,a.schilde,a.reaktor,a.wkkap,a.eps,a.evade_val,a.cloak_val,a.detect_val,a.hit_val,a.lss,a.kss,a.torps,a.warp_capability,a.warp_cost,a.points,a.buildtime,a.maintaintime,a.stellar,b.count FROM stu_modules as a LEFT JOIN stu_colonies_storage as b ON a.module_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_researched as c ON a.research_id=c.research_id AND c.user_id=".$this->uid." WHERE a.type=".$type." AND a.level>=".$minlvl." AND a.level<=".$maxlvl." AND (a.viewable='1' OR !ISNULL(b.count)) AND (a.research_id=0 OR (a.research_id>0 AND (!ISNULL(c.user_id) OR !ISNULL(b.count)))) AND a.module_id=".$id,4); }

	
	function checkModuleForBuilding($rump,$moduleid,$slot) {
	
		if ($moduleid == 0) {
			if ($slot == "w1") return true;
			if ($slot == "w2") return true;
			if ($slot == "s1") return true;
			if ($slot == "s2") return true;
			return false;
		}


		$rump = $this->db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$rump." LIMIT 1",4);
		$module = $this->db->query("SELECT * FROM stu_modules WHERE module_id=".$moduleid." LIMIT 1",4);
		
		if (($module[subtype] == "freight") || ($module[subtype] == "lab")) {
			
		} else {
		
			if ($rump[$slot."_lvl"] && ($rump[$slot."_lvl"] != $module['level'])) return false;					
			$storage = $this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id = ".$this->id." AND goods_id=".$moduleid." LIMIT 1",1);
			if ($storage < 1) return false;
		}
		if (($slot == "m1") && ($module[type] != 1)) return false;
		if (($slot == "m2") && ($module[type] != 2)) return false;
		if (($slot == "m3") && ($module[type] != 3)) return false;
		if (($slot == "m4") && ($module[type] != 4)) return false;
		if (($slot == "m5") && ($module[type] != 5)) return false;
		
		if (($slot == "w1") && ($module[type] != 6)) return false;
		if (($slot == "w2") && ($module[type] != 6)) return false;
		
		
		if (($slot == "s1") && ($module[type] != 7)) return false;
		if (($slot == "s2") && ($module[type] != 7)) return false;
		
		return true;
	}
	
	
	function getCurrentFleetPoints() {
		return $this->db->query("SELECT SUM(b.fleetpoints) FROM stu_ships AS a LEFT JOIN stu_rumps AS b ON a.rumps_id = b.rumps_id WHERE a.user_id=".$this->uid,1);
	}
	function getCurrentCivilianCount() {
		return $this->db->query("SELECT COUNT(*) FROM stu_ships AS a LEFT JOIN stu_rumps AS b ON a.rumps_id = b.rumps_id WHERE b.fleetpoints=0 AND a.user_id=".$this->uid,1);
	}
	
	function buildship()
	{
		global $_GET;
		
		

		
		
		// if ($this->rump['rumps_id'] == 5) return;
		// if ($this->sess['wpo'] > 0) return "Schiffbau und -Wartung sind noch für ".$this->sess['wpo']." Runde(n) gesperrt";
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Schiffbau ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		

		if ($this->check_rbf($this->id) == 0) return "Für den Schiffbau wird ein aktivierter Raumbahnhof benötigt";
		if ($this->check_werft($this->id) == 0) die(show_error(902));
	
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_m1'],"m1")) return "Modulauswahl (Hüllenpanzerung) ungültig!";
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_m2'],"m2")) return "Modulauswahl (Schilde) ungültig!";
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_m3'],"m3")) return "Modulauswahl (Warpkern) ungültig!";
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_m4'],"m4")) return "Modulauswahl (Antrieb) ungültig!";
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_m5'],"m5")) return "Modulauswahl (Sensoren) ungültig!";
		
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_w1'],"w1")) return "Modulauswahl (Primärwaffe) ungültig!";
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_w2'],"w2")) return "Modulauswahl (Sekundärwaffe) ungültig!";
		
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_s1'],"s1")) return "Modulauswahl (Spezial) ungültig!";
		if (!$this->checkModuleForBuilding($this->rump['rumps_id'],$_GET['m_s2'],"s2")) return "Modulauswahl (Spezial) ungültig!";
		
		if ($_GET['m_s1'] > 0 && $_GET['m_s2'] > 0 && $_GET['m_s1']==$_GET['m_s2']) return "Spezialmodule dürfen nicht identisch sein!";
		
		if ($_GET['m_w1'] > 0) {
			if (!in_array( $this->db->query("SELECT subtype FROM stu_modules WHERE module_id=".$_GET['m_w1']."",1) ,array_map('trim', explode(',',$this->rump['w1_types'])))) return "Primärwaffentyp ungültig!";
		}
		if ($_GET['m_w2'] > 0) {
			if (!in_array( $this->db->query("SELECT subtype FROM stu_modules WHERE module_id=".$_GET['m_w2']."",1) ,array_map('trim', explode(',',$this->rump['w2_types'])))) return "Sekundärwaffentyp ungültig!";
		}
		
		if (($_GET['m_w1'] > 0) && ($_GET['m_w2'] > 0)) {
			if ($this->db->query("SELECT subtype FROM stu_modules WHERE module_id=".$_GET['m_w1']."",1) == $this->db->query("SELECT subtype FROM stu_modules WHERE module_id=".$_GET['m_w2']."",1)) return "Primär- und Sekundärwaffentyp müssen unterschiedlich sein.";
		}

		if ($this->eps < $this->rump['eps_cost']) return "Zum Schiffbau werden ".$this->rump['eps_cost']." Energie benötigt - Vorhanden sind nur ".$this->eps;
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships_buildprogress WHERE colonies_id=".$this->id." AND user_id=".$this->uid,1) != 0) return "Es können nicht mehrere Schiffe zur selben Zeit gebaut werden";
		
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships_buildprogress WHERE user_id=".$this->uid,1) >= 3) return "Es können nicht mehr als 3 Schiffe zur selben Zeit gebaut werden";
		
		
		$pc = $this->getAllPoints("pcrew");
		$pv = $this->getAllPoints("psupply");
		$pw = $this->getAllPoints("pmaintain");
	
		$cf = $this->getCurrentFleetPoints();
		$cc = $this->getCurrentCivilianCount();
	
		$stepFleetPoints = stepFleetLimit($pc,$pv,$pw);
		
		$modules = array();
		if ($_GET['m_s1'] != 0) array_push($modules,$_GET['m_s1']);
		if ($_GET['m_s2'] != 0) array_push($modules,$_GET['m_s2']);

		$shipvals = getShipValuesWithMods($this->rump['rumps_id'],$modules);

		
		if ($shipvals['fleetpoints'] == 0) {
			if ($cc >= $stepFleetPoints['civilianships']) return "Limit für Zivile Schiffe wurde erreicht.";
		} else {
			if ($cf + $shipvals['fleetpoints'] > $stepFleetPoints['battleships'])  return "Benötige Flottenpunkte übersteigen aktuelles Limit.";
		}
		
		
		$result = $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.rumps_id=".$_GET['rid']);
		while($cost=mysql_fetch_assoc($result))
		{
			if ($cost['vcount'] < $cost['count'])
			{
				return "Es werden ".$cost['count']." ".$cost['name']." benötigt - Vorhanden sind nur ".(!$cost['vcount'] ? 0 : $cost['vcount']);
			}
		}
		
		$result = $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.rumps_id=".$_GET['rid']);
		while($cost=mysql_fetch_assoc($result))
		{
			$this->lowerstorage($this->id,$cost['goods_id'],$cost['count']);
		}
		$this->db->query("UPDATE stu_colonies SET eps=eps-".$this->rump['eps_cost']." WHERE id=".$this->id." LIMIT 1");
		
		
		
		

		
		if ($_GET['m_m1']) $this->lowerstorage($this->id,$_GET['m_m1'],1);
		if ($_GET['m_m2']) $this->lowerstorage($this->id,$_GET['m_m2'],1);
		if ($_GET['m_m3']) $this->lowerstorage($this->id,$_GET['m_m3'],1);
		if ($_GET['m_m4']) $this->lowerstorage($this->id,$_GET['m_m4'],1);
		if ($_GET['m_m5']) $this->lowerstorage($this->id,$_GET['m_m5'],1);
		if ($_GET['m_w1']) $this->lowerstorage($this->id,$_GET['m_w1'],1);
		if ($_GET['m_w2']) $this->lowerstorage($this->id,$_GET['m_w2'],1);
		// if ($_GET['m_s1']) $this->lowerstorage($this->id,$_GET['m_s1'],1);
		// if ($_GET['m_s2']) $this->lowerstorage($this->id,$_GET['m_s2'],1);

		
		$plan = $this->getpossiblebuildplans();
		
		echo "<br>";
		if ($plan == 0)
		{
			!$_GET['npn'] ? $pn = $this->rump['name']." ".date("d.m.Y H:i") : $pn = $_GET[npn];
			$id = $this->db->query("INSERT INTO stu_ships_buildplans (rumps_id,user_id,name,m1,m2,m3,m4,m5,w1,w2,s1,s2) VALUES ('".$this->rump['rumps_id']."','".$this->uid."','".addslashes($pn)."','".$_GET['m_m1']."','".$_GET['m_m2']."','".$_GET['m_m3']."','".$_GET['m_m4']."','".$_GET['m_m5']."','".$_GET['m_w1']."','".$_GET['m_w2']."','".$_GET['m_s1']."','".$_GET['m_s2']."')",5);
		}
		else $id = $plan['plans_id'];
		
		// return "BREAKING POINT REACHED";
		$time = time()+$this->rump['buildtime'];
		$this->db->query("INSERT INTO stu_ships_buildprogress (colonies_id,user_id,rumps_id,plans_id,buildtime,huelle,schilde,warpable,cloakable,phaser,eps,batt,lss,kss,points) VALUES ('".$this->id."','".$this->uid."','".$this->rump['rumps_id']."','".$id."','".$time."','0','0','0','0','0','0','0','0','0','0')");
		// $this->db->query("UPDATE stu_ships_buildplans SET evade=".$evade.",treffer=".$hit.",reaktor=".$reaktor.",wkkap=".$wkkap.",warp_cost=".$warpcost.",max_torps=".$torps.",maintaintime=".$main.",buildtime=".$bz.",stellar='".$stellar."',sensor_val=".$detect.",cloak_val=".$cloak.",wpoints='".$points."' WHERE plans_id=".$id);
		// $this->db->query("COMMIT");
		return "Schiff (".$this->rump['name'].") wird gebaut. Fertigstellung: ".date("d.m.Y H:i",$time);
	}

	function getpossiblebuildplans()
	{
		global $_GET;
		return $this->db->query("SELECT a.plans_id,a.name,COUNT(b.id) as idc FROM stu_ships_buildplans as a LEFT JOIN stu_ships as b ON a.plans_id=b.plans_id
		WHERE a.rumps_id=".$_GET[rid]." AND a.m1='".$_GET[m_m1]."' AND a.m2='".$_GET[m_m2]."' AND a.m3='".$_GET[m_m3]."' AND
		a.m4='".$_GET[m_m4]."' AND a.m5='".$_GET[m_m5]."' AND a.w1='".$_GET[m_w1]."' AND a.w2='".$_GET[m_w2]."' AND a.s1='".$_GET[m_s1]."' AND a.s2='".$_GET[m_s2]."' AND a.user_id=".$this->uid." GROUP BY a.plans_id",4);
	}

	function getbuildingprogress() { return $this->db->query("SELECT a.id,a.colonies_classes_id,a.name as cname,b.field_id,b.type,b.buildings_id,b.aktiv,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_fielddata as b ON a.id=b.colonies_id LEFT JOIN stu_buildings as c ON b.buildings_id=c.buildings_id WHERE a.user_id=".$this->uid." AND b.aktiv>1 ORDER BY a.colonies_classes_id,a.id ASC,b.aktiv,b.field_id"); }

	function getmodulecost($modid) { return $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_modules_cost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.module_id=".$modid." ORDER BY b.sort"); }

	function getrepmods($t)
	{
		if ($t == 1) return $this->db->query("SELECT a.module_id,a.type,a.name,a.ecost,c.count FROM stu_modules as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." LEFT JOIN stu_colonies_storage as c ON c.goods_id=a.module_id AND c.colonies_id=".$this->id." WHERE a.ecost>0 AND (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) AND (a.type=1 OR a.type=2 OR a.type=4 OR a.type=7 OR a.type=11 OR a.module_id=657) ORDER BY type,level");
		if ($t == 2) return $this->db->query("SELECT a.module_id,a.type,a.name,a.ecost,c.count FROM stu_modules as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." LEFT JOIN stu_colonies_storage as c ON c.goods_id=a.module_id AND c.colonies_id=".$this->id." WHERE a.ecost>0 AND (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) AND (a.type=3 OR a.type=5 OR a.type=8 OR a.type=9 OR a.module_id=658) ORDER BY type,level");
		if ($t == 3) return $this->db->query("SELECT a.module_id,a.type,a.name,a.ecost,c.count FROM stu_modules as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." LEFT JOIN stu_colonies_storage as c ON c.goods_id=a.module_id AND c.colonies_id=".$this->id." WHERE a.ecost>0 AND (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) AND (a.type=6 OR (a.type=10 AND a.module_id!=658 AND a.module_id!=657)) ORDER BY type,level");
	}
	function getallmods()
	{
		return $this->db->query("SELECT a.module_id,a.type,a.name,a.ecost,c.count FROM stu_modules as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." LEFT JOIN stu_colonies_storage as c ON c.goods_id=a.module_id AND c.colonies_id=".$this->id." WHERE a.ecost>1 AND (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) ORDER BY type,level");
	}
	function gettrashgoods()
	{
		return $this->db->query("SELECT a.*,b.name FROM stu_colonies_storage as a LEFT JOIN stu_goods as b on a.goods_id = b.goods_id WHERE a.colonies_id=".$this->id." AND a.goods_id < 80 ORDER BY a.goods_id ASC");
	}
	
	function gettorpcost($torp_type) { return $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_torpedo_cost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.torp_type=".$torp_type." ORDER BY b.sort"); }

	function getreptorps() { return $this->db->query("SELECT a.torp_type,a.name,a.ecost FROM stu_torpedo_types as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." WHERE (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) ORDER BY torp_type"); }

	function modulherstellung($moarr)
	{
		foreach($moarr as $key => $value)
		{
			if (!check_int($value) || !check_int($key) || $value< 1) continue;
			if ($value > 99) $value = 99;
			$data = $this->db->query("SELECT a.module_id,a.type,a.name,a.ecost FROM stu_modules as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." WHERE a.viewable='1' AND (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) AND a.module_id=".$key,4);
			if ($data == 0) die(show_error(902));
			// switch($data[type])
			// {
				// case 1:
					// $bid = 92;
					// break;
				// case 2:
					// $bid = 92;
					// break;
				// case 3:
					// $bid = 91;
					// break;
				// case 4:
					// $bid = 92;
					// break;
				// case 5:
					// $bid = 91;
					// break;
				// case 6:
					// $bid = 90;
					// break;
				// case 7:
					// $bid = 92;
					// break;
				// case 8:
					// $bid = 91;
					// break;
				// case 9:
					// $bid = 91;
					// break;
				// case 10:
					// $bid = 90;
					// break;
				// case 11:
					// $bid = 92;
					// break;
			// }
			// if ($data[module_id] == 658) $bid = 91;
			// if ($data[module_id] == 657) $bid = 92;
			$bid = 40;
			
			if (!$bid) return;
			// if (($data[module_id] == 955) || ($data[module_id] == 959) || ($data[module_id] == 963)) $npcmulti = 10;
			// elseif ($data[module_id] == 951) $npcmulti = 4;
			// else $npcmulti = 0;
			if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=".$bid." AND aktiv=0 AND colonies_id=".$this->id,1) == 0) return;
			if ($data[ecost]*$value > $this->eps) $value = floor($this->eps/$data[ecost]);
			if ($this->eps < $data[ecost]*$value)
			{
				$msg .= "Für die Herstellung von Modulen des Typs ".$data[name]." wird ".$data[ecost]." Energie benötigt - Vorhanden ist nur ".$this->eps."<br>";
				continue;
			}
			$cr = $this->getmodulecost($key);
			while($co=mysql_fetch_assoc($cr))
			{
				if ($value*$co['count'] > $co[vcount])
				{
					$value = floor($co[vcount]/$co['count']);
					if ($value == 0)
					{
						$msg .= "Für die Herstellung von Modulen des Typs ".$data[name]." werden ".$co['count']." ".$co[name]." benötigt - Vorhanden ist nur ".(!$co[vcount] ? 0 : $co[vcount])."<br>";
						break;
					}
				}
				if ($value != 0) $cost[$co[goods_id]] = $co['count'];
			}
			if ($value == 0)
			{
				$msg .= "Es wurden keine Module des Typs ".$data[name]." hergestellt<br>";
				continue;
			}
			$this->db->query("START TRANSACTION");
			if (is_array($cost))
			{
				foreach($cost as $key2 => $value2) $this->lowerstorage($this->id,$key2,$value2*$value);
			}
			if ($npcmulti != 0) $this->upperstorage($this->id,$data[module_id],($npcmulti*$value));
			else $this->upperstorage($this->id,$data[module_id],$value);
			$this->db->query("UPDATE stu_colonies SET eps=eps-".($value*$data[ecost])." WHERE id=".$this->id);
			$this->db->query("COMMIT");
			$this->eps -= ($value*$data[ecost]);
			if ($npcmulti != 0) $msg .= "Es wurden ".($value)." Module des Typs ".$data[name]." hergestellt<br>";
			else $msg .= "Es wurden ".$value." Module des Typs ".$data[name]." hergestellt<br>";
			unset($cost);
		}
		return $msg;
	}

	function modulinstandsetzung($moarr)
	{
		foreach($moarr as $key => $value)
		{
			if (!check_int($value) || !check_int($key) || $value< 1) continue;
			if ($value > 1) $value = 1;

			if ($key == 0) die(show_error(902));
			switch($key)
			{
				case 901:
					$bid = 92;
					$mcount = 28;
					break;
				case 902:
					$bid = 92;
					$mcount = 28;
					break;
				case 903:
					$bid = 91;
					$mcount = 1;
					break;
				case 904:
					$bid = 92;
					$mcount = 3;
					break;
				case 905:
					$bid = 91;
					$mcount = 1;
					break;
				case 906:
					$bid = 90;
					$mcount = 3;
					break;
				case 907:
					$bid = 92;
					$mcount = 1;
					break;
				case 908:
					$bid = 91;
					$mcount = 10;
					break;

			}
			if (!$bid) return;
			if ($this->db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id=".$this->id." AND goods_id=".$key,1) == 0) return;		
			if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=".$bid." AND aktiv=0 AND colonies_id=".$this->id,1) == 0) return;
			if ($this->eps < 10)
			{
				$msg .= "Für die Instandsetzung von Modulen wird 10 Energie benötigt - Vorhanden ist nur ".$this->eps."<br>";
				continue;
			}
			$modid = $this->drawmodule($key);
			$name =  $this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$modid,1); 
			if ($value == 0)
			{
				$msg .= "Es wurden keine Module instandgesetzt<br>";
				continue;
			}
			$this->db->query("START TRANSACTION");
			$this->upperstorage($this->id,$modid,$mcount);
			$this->lowerstorage($this->id,$key,1);	
			$this->db->query("UPDATE stu_colonies SET eps=eps-10 WHERE id=".$this->id);
			$this->db->query("COMMIT");
			$this->eps -= (10);
			$msg .= "Module wurden instandgesetzt, es handelte sich um ".$mcount."x ".$name."!<br>";
			$this->send_pm($this->uid,23,"Der Siedler hat Module instandgesetzt, es handelte sich um ".$mcount."x ".$name."!",4);
			unset($cost);
		}
		return $msg;
	}


	function torpedoherstellung($moarr)
	{
		foreach($moarr as $key => $value)
		{
			if (!check_int($value) || !check_int($key) || $value< 1) continue;

			if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=80 AND aktiv=0 AND colonies_id=".$this->id,1) == 0) return;
			$data = $this->db->query("SELECT a.torp_type,a.name,a.ecost FROM stu_torpedo_types as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." WHERE (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) AND a.torp_type=".$key,4);
			if ($data == 0) die(show_error(902));
			if ($data[ecost]*$value > $this->eps) $value = floor($this->eps/$data[ecost]);
			if ($this->eps < $data[ecost]*$value)
			{
				$msg .= "Für die Herstellung von Torpedos des Typs ".$data[name]." wird ".$data[ecost]." Energie benötigt - Vorhanden ist nur ".$this->eps."<br>";
				continue;
			}
			$cr = $this->gettorpcost($key);
			while($co=mysql_fetch_assoc($cr))
			{
				if ($value*$co['count'] > $co[vcount])
				{
					$value = floor($co[vcount]/$co['count']);
					if ($value == 0)
					{
						$msg .= "Für die Herstellung von Torpedos des Typs ".$data[name]." werden ".$co['count']." ".$co[name]." benötigt - Vorhanden ist nur ".(!$co[vcount] ? 0 : $co[vcount])."<br>";
						break;
					}
				}
				if ($value != 0) $cost[$co[goods_id]] = $co['count'];
			}
			if ($value == 0)
			{
				$msg .= "Es wurden keine Torpedos des Typs ".$data[name]." hergestellt<br>";
				$this->db->query("ROLLBACK");
				continue;
			}
			$this->db->query("START TRANSACTION");
			if (is_array($cost))
			{
				foreach($cost as $key2 => $value2) $this->lowerstorage($this->id,$key2,$value2*$value);
			}
			$this->upperstorage($this->id,$data[torp_type],$value);
			$this->db->query("UPDATE stu_colonies SET eps=eps-".($value*$data[ecost])." WHERE id=".$this->id);
			$this->db->query("COMMIT");
			$this->eps -= ($value*$data[ecost]);
			$msg .= "Es wurden ".($value)." Torpedos des Typs ".$data[name]." hergestellt<br>";
		}
		return $msg;
	}

	
	function schrottcount($good) { return $this->db->query("SELECT * FROM stu_colonies_storage WHERE goods_id=".$good.""); }	
	
	function verschrotten($moarr)
	{
		if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=4 AND aktiv=0 AND colonies_id=".$this->id,1) == 0) return;
		foreach($moarr as $key => $value)
		{
			if (!check_int($value) || !check_int($key) || $value< 1) continue;

			
			$data = $this->db->query("SELECT a.count as hc,b.* FROM stu_colonies_storage as a LEFT JOIN stu_goods as b on a.goods_id = b.goods_id WHERE a.goods_id=".$key." AND a.colonies_id = ".$this->id,4);
			if ($data == 0) die(show_error(902));
			

			if ($value > $data[hc]) $rvalue = $data[hc];
			else $rvalue = $value;

			$this->lowerstorage($this->id,$data[goods_id],$rvalue);
			$msg .= "Es wurden ".($rvalue)." ".$data[name]." entsorgt.<br>";
		}
		return $msg;
	}
	
	
	
	function setplanname($pn)
	{
		foreach($pn as $key => $value)
		{
			if (!check_int($key) || !is_string($value) || strlen($value) < 3) continue;
			$this->db->query("UPDATE stu_ships_buildplans SET name='".addslashes($value)."' WHERE plans_id=".$key." AND user_id=".$this->uid);
		}
		return "Namen geändert";
	}

	function delplan($pid)
	{
		$result = $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE user_id=".$this->uid." AND plans_id=".$pid,1);
		if ($result != 0) return "Der Plan kann nicht gelöscht werden, da Schiffe existieren, die nach diesem Plan gebaut wurden";
		$result = $this->db->query("SELECT COUNT(*) FROM stu_ships_buildprogress WHERE plans_id=".$pid,1);
		if ($result != 0) return "Der Plan kann nicht gelöscht werden, da Schiffe existieren, die nach diesem Plan gebaut wurden";
		$this->db->query("DELETE FROM stu_ships_buildplans WHERE plans_id=".$pid." AND user_id=".$this->uid);
		return "Bauplan gelöscht";
	}

	function loadmaintainancelist() { $this->result = $this->db->query("SELECT a.lastmaintainance+b.maintaintime as um,a.id,a.rumps_id,a.name FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON b.rumps_id=c.rumps_id WHERE c.slots=0 AND (ISNULL(c.is_shuttle) OR c.is_shuttle='0') AND a.user_id=".$this->uid." AND b.plans_id!=1 ORDER BY um ASC LIMIT 10"); }

	function loadsectorflights() { return $this->db->query("SELECT a.user_id,a.rumps_id,UNIX_TIMESTAMP(a.date) as date_tsp,b.user FROM stu_sectorflights as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.systems_id=".$this->systems_id." AND a.sx=".$this->sx." AND a.sy=".$this->sy." AND UNIX_TIMESTAMP(a.date)>".(time()-86400)." AND a.user_id!=".$this->uid." AND a.cloak!='1' ORDER BY a.fleets_id,a.user_id"); }

	function getsectorflights($sx,$sy,$sys) { return $this->db->query("SELECT COUNT(*) FROM stu_sectorflights WHERE sx=".$sx." AND sy=".$sy." AND cloak!='1' AND systems_id=".$sys." AND UNIX_TIMESTAMP(date)>".(time()-86400)." AND user_id!=".$this->uid,1); }

	function getshipbuildprogress() { return $this->db->query("SELECT a.rumps_id,a.buildtime,a.colonies_id,b.colonies_classes_id,b.name,c.name as pname FROM stu_ships_buildprogress as a LEFT JOIN stu_colonies as b ON a.colonies_id=b.id LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id WHERE a.user_id=".$this->uid." ORDER BY a.colonies_id"); }

	function ebatt($arr)
	{
		if ($this->check_werft($this->id) == 0) return;
		if ($this->check_rbf($this->id) == 0) return "Für diese Aktion wird ein aktivierter Raumbahnhof benötigt";
		if ($this->eps == 0) return "Keine Energie zum Laden der Ersatzbatterien vorhanden";
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Laden der Batterien ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		foreach($arr as $key => $value)
		{
			if ($this->eps == 0) break;
			if (!check_int($key) || (!check_int($value) && $value != "m")) continue;
			$data = $this->db->query("SELECT a.id,a.name,a.sx,a.sy,a.systems_id,a.batt,a.max_batt,a.user_id,a.cloak,b.slots,b.is_shuttle,b.trumfield,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=".$key." LIMIT 1",4);
			if ($data == 0) continue;
			if (checkcolsector($data) == 0) return;
			if ($data['slots'] > 0) continue;
			if ($data['cloak'] == 1) continue;
			if ($data['trumfield'] == 1) continue;
			if ($data['batt'] >= $data['max_batt']) continue;
			if ($data['is_shuttle'] == 1) continue;
			if ($data['vac_active'] == 1) continue;
			if ($value == "m") $value = $data['max_batt'];
			if ($value > $this->eps) $value = $this->eps;
			if ($value > $data['max_batt']-$data['batt']) $value = $data['max_batt']-$data['batt'];
			$this->db->query("UPDATE stu_ships set batt=batt+".$value." WHERE id=".$key." LIMIT 1");
			if ($this->uid != $data['user_id']) $this->send_pm($this->uid,$data['user_id'],"Die Kolonie ".addslashes($this->name)." hat die Ersatzbatterie der ".addslashes($data['name'])." um ".$value." Energie auf ".($data['batt']+$value)."/".$data['max_batt']." geladen",3);
			$this->eps-=$value;
			$msg .= "Ersatzbatterie der ".$data['name']." um ".$value." Energie auf ".($data['batt']+$value)."/".$data['max_batt']." geladen<br>";
		}
		$this->db->query("UPDATE stu_colonies SET eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return $msg;
	}
	
	function massetrans($arr)
	{
		if ($this->eps == 0) return "Keine Energie für den Transfer vorhanden";
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Aktion ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}

		// GOTCHA
		$gotcha = "Registrierter abbruch beim Energietransfer: Userid ".$this->uid;
		

		foreach($arr as $key => $value)
		{
			$gotcha .= "<br>".$key." <- ".$value;

			if ($this->eps == 0) break;
			if (!check_int($key) || (!check_int($value) && $value != "m")) continue;
			$data = $this->db->query("SELECT a.id,a.name,a.sx,a.sy,a.systems_id,a.eps,a.max_eps,a.user_id,a.cloak,b.slots,b.is_shuttle,b.trumfield,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=".$key." LIMIT 1",4);
			if ($data == 0) continue;
	
			if ($data[cloak] == 1) $gotcha .= " cloaked";
			if (checkcolsector($data) == 0) {

				//$this->send_pm(1,102,$gotcha,3);


				//return;
			}

			if ($data['trumfield'] == 1) continue;
			if ($data['eps'] >= $data['max_eps'])
			{
				$msg .= "Das EPS der ".$data['name']." ist bereits gefüllt<br />";
				continue;
			}
			if ($data['vac_active'] == 1) continue;
			if ($value == "m") $value = $data['max_eps'];
			if ($value > $this->eps) $value = $this->eps;
			if ($value > $data['max_eps']-$data['eps']) $value = $data['max_eps']-$data['eps'];
			$this->db->query("UPDATE stu_ships set eps=eps+".$value." WHERE id=".$key." LIMIT 1");
			if ($this->uid != $data['user_id']) $this->send_pm($this->uid,$data['user_id'],"Die Kolonie ".addslasheS($this->name)." hat das EPS der ".addslashes($data['name'])." um ".$value." Energie auf ".($data['eps']+$value)."/".$data['max_eps']." geladen",3);
			$this->eps-=$value;
			$msg .= "EPS der ".$data['name']." um ".$value." Energie auf ".($data['eps']+$value)."/".$data['max_eps']." geladen<br>";
		}
		$this->db->query("UPDATE stu_colonies SET eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return $msg;
	}
	
	function getpossibletelesystems($range) { return $this->db->query("SELECT a.systems_id,a.name,a.cx,a.cy,a.type FROM stu_systems as a LEFT JOIN stu_systems as b ON b.systems_id=".$this->systems_id." WHERE a.cx BETWEEN b.cx-".$range." AND b.cx+".$range." AND a.cy BETWEEN b.cy-".$range." AND b.cy+".$range.""); }

	function gettelerange()
	{
		if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=314",1) > 0) return array("norm" => 50,"sys" => 20);
		else return array("norm" => 40,"sys" => 20);
	}

	function gettelecoords($range) { return $this->db->query("SELECT cx-".$range." as xmin,cx+".$range." as xmax,cy-".$range." as ymin,cy+".$range." as ymax FROM stu_systems WHERE systems_id=".$this->systems_id,4); }

	function telescan($cx,$cy)
	{
		if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND (buildings_id=403 OR buildings_id=314) AND aktiv=0",1) == 0) return "Zum Scannen wird ein fertiggestelltes Subraumteleskop benötigt";
		if ($this->check_rbf($this->id) == 0) return "Zum Scannen wird ein aktivierter Raumbahnhof benötigt";
		if ($this->eps == 0) return "Zum Scannen wird 1 Energie benötigt";
		if (!$cx) return "Es wurde keine X-Koordinate eingegeben";
		if (!$cy) return "Es wurde keine Y-Koordinate eingegeben";
		if (!check_int($cx) || !check_int($cy)) die(show_error(902));
		global $mapfields;
		$range = $this->gettelerange();
		$data = $this->gettelecoords($range['norm']);
		if ($cx < 1 || $cx < $data[xmin] || $cx > $mapfields[max_x] || $cx > $data[xmax]) return "Die X-Koordinate ist ungültig";
		if ($cy < 1 || $cy < $data[ymin] || $cy > $mapfields[max_y] || $cy > $data[ymax]) return "Die Y-Koordinate ist ungültig";
		$this->db->query("UPDATE stu_colonies SET eps=eps-1 WHERE id=".$this->id);
		$this->type = $this->db->query("SELECT b.name,b.type,b.sensoroff FROM stu_map as a LEFT JOIN stu_map_ftypes as b USING(type) WHERE a.cx=".$cx." AND a.cy=".$cy,4);
		if ($this->type['sensoroff'] == 1 || $this->type['type'] == 8) return "Dieser Sektor kann nicht gescant werden (Grund: ".$this->type['name'].")";
		$this->result = $this->db->query("SELECT a.rumps_id,a.user_id,a.fleets_id,a.name,a.fname,a.user,a.cloak FROM stu_views_cs as a LEFT JOIN stu_ally_relationship as b ON ((a.allys_id=b.allys_id1 AND b.allys_id2=".$this->sess[allys_id].") OR (a.allys_id=b.allys_id2 AND b.allys_id1=".$this->sess[allys_id].")) WHERE a.cx=".$cx." AND a.cy=".$cy." AND (a.cfield!=7 AND a.cfield!=8) AND a.rumps_id != 99 AND (a.cloak!='1' OR ISNULL(a.cloak) OR a.cloak='' OR (a.cloak='1' AND (a.user_id=".$this->uid." OR b.type='4'))) GROUP BY a.id ORDER BY a.fleets_id DESC,a.id");
	}

	function telescansystem($sys)
	{
		if ($this->check_rbf($this->id) == 0) return "Zum Scannen wird ein aktivierter Raumbahnhof benötigt";
		if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND (buildings_id=403 OR buildings_id=314) AND aktiv=0",1) == 0) return "Zum Scannen wird ein fertiggestelltes Subraumteleskop benötigt";
		if ($this->eps < 2) return "Zum Scannen des System wird 2 Energie benötigt";
		$range = $this->gettelerange();
		if ($this->db->query("SELECT a.systems_id,a.name,a.cx,a.cy,a.type FROM stu_systems as a LEFT JOIN stu_systems as b ON b.systems_id=".$this->systems_id." WHERE a.cx BETWEEN b.cx-".$range['sys']." AND b.cx+".$range['sys']." AND a.cy BETWEEN b.cy-".$range['sys']." AND b.cy+".$range['sys']." AND a.systems_id=".$sys,1) == 0) die(show_error(902));
		$this->db->query("UPDATE stu_colonies SET eps=eps-2 WHERE id=".$this->id);
		if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=314",1) > 0 && $this->db->query("SELECT user_id FROM stu_systems_user WHERE systems_id=".$sys." AND user_id=".$this->uid,1) == 0) $this->db->query("INSERT INTO stu_systems_user (systems_id,user_id) VALUES ('".$sys."','".$this->uid."')");
		$this->result = $this->db->query("SELECT a.type,a.sx,a.sy,COUNT(b.rumps_id) as cid FROM stu_sys_map as a LEFT JOIN stu_ships as b ON b.sx=a.sx AND b.sy=a.sy AND b.systems_id=a.systems_id AND (b.cloak!='1' OR ISNULL(b.cloak) OR b.cloak='') WHERE a.systems_id=".$sys." GROUP BY a.sx,a.sy ORDER BY a.sy,a.sx");
	}

	function getweaponbyid($moduleId) { return $this->db->query("SELECT wtype,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$moduleId,4); }

	function getmodulespecial($data)
	{
		global $gfx;
		// if ($data[torp_type] > 0)
		// {
			// $result = $this->db->query("SELECT goods_id,name FROM stu_torpedo_types WHERE research_id!=999 AND type=".$data[torp_type]);
			// while($dat = mysql_fetch_assoc($result)) $rt .= "<img src=".$gfx."/goods/".$dat[goods_id].".gif title=\"".ftit($dat[name])."\">&nbsp;";
			// return $rt;
		// }
		// if ($data[wtype] > 0)
		// {
			// $return = "<img src=".$gfx."/specials/wd_".$data[wtype].".gif title=\"".getWeaponTypeDescription($data[wtype])."\"> ".getWeaponTypeDescription($data[wtype])."-Waffe";
			// if ($data[special_id1] > 0)
			// {
				// $mod = $this->db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id1],4);
				// $return .= "<br><img src=".$gfx."/specials/".$data['special_id1'].".gif title=\"".$mod['description']."\"> ".$mod['name'];
			// }
			// if ($data[special_id2] > 0)
			// {
				// $mod = $this->db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id2],4);
				// $return .= "<br><img src=".$gfx."/specials/".$data['special_id2'].".gif title=\"".$mod['description']."\"> ".$mod['name'];
			// }
			// return $return;
		// }
		// if ($data[special_id1] == 0) return "<img src=".$gfx."/specials/0.gif> Keines";
		// $mod = $this->db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id1],4);
		// $return = "<img src=".$gfx."/specials/".$data[special_id1].".gif title=\"".$mod[description]."\"> ".$mod[name];
		// if ($data[special_id2] == 0) return $return;
		// $mod = $this->db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data['special_id2'],4);
		// $return .= "<br><img src=".$gfx."/specials/".$data['special_id2'].".gif title=\"".$mod['description']."\"> ".$mod['name'];
		$id = $data['module_id'];
		
		$return = "";
		
		$result = $this->db->query("SELECT * FROM stu_modules_special WHERE modules_id=".$id);
		while($spec = mysql_fetch_assoc($result)) {
			$return .= modvalmapping($spec[type],$spec[value])."<br>";
		}
		
		if ($return == "") return "Keine";
		return $return;
	}

	function getdamagedships() { return $this->db->query("SELECT a.id,a.rumps_id,a.name,a.user_id,a.huelle,a.max_huelle,b.m1,b.m2,b.m3,b.m4,b.m5,b.w1,b.w2,b.s1,b.s2,c.user,d.name as rname FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_user as c ON a.user_id=c.id LEFT JOIN stu_rumps as d ON a.rumps_id=d.rumps_id WHERE a.systems_id=".$this->systems_id." AND a.sx=".$this->sx." AND a.sy=".$this->sy." AND a.huelle<a.max_huelle AND d.trumfield!='1' AND d.slots=0 AND a.cloak!='1'  ORDER BY a.fleets_id,a.rumps_id,a.id"); }

	
	function addModuleCost($arr,$module) {
		
		if ($module > 0) {
			$result = $this->db->query("SELECT * FROM stu_modules_cost WHERE module_id = ".$module);		
			while ($res = mysql_fetch_assoc($result)) {
				$arr[$res[goods_id]] += $res[count];
			}
		}
		return $arr;
	}
	
	function getShipRepairCost($data)
	{
		$rump = $this->getrumpbyid($data[rumps_id]);
		
		$bk[0] = $this->rump[eps_cost];
		
		// $result = $this->db->query("SELECT ROUND(a.count/100*(100-((100/".$data[max_huelle].")*".$data[huelle]."))) as gcount,a.goods_id,b.name,c.count as vcount FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.rumps_id=".$data[rumps_id]." ORDER BY b.sort");
		// while($arr=mysql_fetch_assoc($result)) if ($arr[gcount] > 0) $bk[] = $arr;
		

		$hullfac = 	($data[max_huelle] - $data[huelle]) / $data[max_huelle];

		$bk = $this->addModuleCost($bk,$data['m1']);
		$bk = $this->addModuleCost($bk,$data['m2']);
		$bk = $this->addModuleCost($bk,$data['m3']);
		$bk = $this->addModuleCost($bk,$data['m4']);
		$bk = $this->addModuleCost($bk,$data['m5']);
		$bk = $this->addModuleCost($bk,$data['w1']);
		$bk = $this->addModuleCost($bk,$data['w2']);
		$bk = $this->addModuleCost($bk,$data['s1']);
		$bk = $this->addModuleCost($bk,$data['s2']);
		
		ksort($bk);
		$res = array();
		foreach($bk as $gid => $cost) {
			if ($gid == 0) $res[] = array("goods_id" => $gid, "gcount" => round($hullfac*$cost), "name" => "Energie", "vcount" => $this->eps);
			else {
				$res[] = array("goods_id" => $gid, "gcount" => round($hullfac*$cost), "name" => $this->db->query("SELECT name FROM stu_goods WHERE goods_id = ".$gid,1), "vcount" => $this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id = ".$gid." AND colonies_id = ".$this->id."",1));
			}
		}

		return $res;
	}

	function repairship($shipid)
	{
		$data = $this->db->query("SELECT a.id,a.rumps_id,a.user_id,a.name,a.cloak,a.schilde_status,a.huelle,a.max_huelle,a.systems_id,a.sx,a.sy,b.m1,b.m2,b.m3,b.m4,b.m5,b.w1,b.w2,b.s1,b.s2,c.vac_active FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.huelle<a.max_huelle AND a.id=".$shipid,4);
		if ($data == 0) return;
		if (checkcolsector($data) == 0) return;
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Schiffsreparatur ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if ($data[rumps_id] > 100 && $data[rumps_id] < 116) return;
		if ($data[vac_active] == 1) return "Der Siedler befindet sich im Urlaubsmodus";
		if ($data[schilde_status] == 1) return "Schiff kann nicht repariert werden (Grund: Schilde des Schiffs sind aktiviert)";
		if ($data[cloak] == 1) return;
		$cost = $this->getShipRepairCost($data);

		// $this->db->query("START TRANSACTION");
		foreach($cost as $key => $value)
		{
			if ($value[goods_id] == 0)
			{
				if ($value[gcount] > $value[vcount])
				{
					$err = 1;
					$msg = "Für die Reparatur wird ".$value[gcount]." Energie benötigt - Vorhanden ist nur ".$value[vcount];
					break;
				}
				else
				{
					$this->db->query("UPDATE stu_colonies SET eps=eps-".$value[gcount]." WHERE id=".$this->id);
					continue;
				}
			}
			if ($value[gcount] > $value[vcount] && $value[gcount] > 0)
			{
				$err = 1;
				$msg = "Für die Reparatur wird ".$value[gcount]." ".$value[name]." benötigt - Vorhanden ist nur ".(!$value[vcount] ? 0 : $value[vcount]);
				break;
			}
		}
		if ($err == 1)
		{
			return $msg;
		}
		$res = "";
		foreach($cost as $key => $value)
		{
			$this->lowerstorage($this->id,$value[goods_id],$value[gcount]);
		}
		$this->db->query("UPDATE stu_ships SET huelle=max_huelle WHERE id=".$shipid." LIMIT 1");
		$this->db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=".$shipid);
		// $this->db->query("COMMIT");
		if ($data[user_id] != $this->uid) $this->send_pm($this->uid,$data[user_id],"Die ".$data['name']." wurde in der Werft der Kolonie ".addslashes($this->name)." repariert",3);

		return "Die ".stripslashes($data[name])." wurde repariert";
	}

	function loadshields($count)
	{
		if ($count == "m") $count = $this->eps;
		if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=100 AND aktiv<2",1) == 0) return "Es befindet sich kein planetarer Schildgenerator auf dieser Kolonie";
		if ($this->eps == 0) return "Keine Energie vorhanden";
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Schildladung ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if ($this->schilde >= $this->max_schilde) return "Die Schilde sind bereits vollständig aufgeladen";
		if ($count > $this->eps) $count = $this->eps;
		if ($count*25 > $this->max_schilde-$this->schilde) $count = ceil(($this->max_schilde-$this->schilde)/25);
		$ecount = ceil($count);
		$scount = $ecount*25;
		if ($scount + $this->schilde > $this->max_schilde) $scount = $this->max_schilde-$this->schilde;
		$this->db->query("UPDATE stu_colonies SET eps=eps-".$ecount.",schilde=schilde+".$scount." WHERE id=".$this->id);
		return "Die planetaren Schilde wurden um ".$ecount." Energie (".$scount." Schildpunkte) aufgeladen";
	}

	function getmaintainanceships() { return $this->db->query("SELECT a.id,a.rumps_id,a.name,a.user_id,a.huelle,a.max_huelle,a.lastmaintainance,a.maintain,b.maintaintime,b.m1,b.m2,b.m3,b.m4,b.m5,b.m6,b.m7,b.m8,b.m9,b.m10,b.m11,c.user,d.name as rname,d.eps_cost FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_user as c ON a.user_id=c.id LEFT JOIN stu_rumps as d ON a.rumps_id=d.rumps_id WHERE a.systems_id=".$this->systems_id." AND a.sx=".$this->sx." AND a.sy=".$this->sy." AND a.user_id>100 AND d.trumfield!='1' AND d.slots=0 AND (ISNULL(d.is_shuttle) OR d.is_shuttle='0') AND a.cloak!='1' AND a.plans_id!=1 ORDER BY a.lastmaintainance+b.maintaintime"); }

	function getShipMaintainanceCost($data) { return $result = $this->db->query("SELECT ROUND(SUM(a.count)/4) as gcount,a.goods_id,b.name,c.count as vcount FROM stu_modules_cost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE b.view='1' AND (a.module_id=".$data['m1']." OR module_id=".$data['m2']." OR module_id=".$data['m3']." OR module_id=".$data['m4']." OR module_id=".$data['m5']." OR module_id=".$data['m6']." OR module_id=".$data['m7']." OR module_id=".$data['m8']." OR module_id=".$data['m9']." OR module_id=".$data['m10']." OR module_id=".$data['m11'].") GROUP BY a.goods_id ORDER BY b.sort"); }

	function loadcurrentmaintainances() { $this->result = $this->db->query("SELECT a.ships_id,a.maintaintime,b.rumps_id,b.name FROM stu_colonies_maintainance as a LEFT JOIN stu_ships as b ON b.id=a.ships_id WHERE a.colonies_id=".$this->id); }

	function maintainship($shipid)
	{
		if ($this->sess['wpo'] > 0) return "Schiffbau und -Wartung sind noch für ".$this->sess['wpo']." Runde(n) gesperrt";
		// Prüfen ob eine Wartungsanlage verfügbar ist
		if ($this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id=".$this->id." AND buildings_id=313",1) > 0)
		{
			$time_modif = 0.9;
			$max_maintain = 3;
		}
		else
		{
			$time_modif = 1;
			$max_maintain = 1;
		}
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Schiffswartung ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if ($this->db->query("SELECT COUNT(*) FROM stu_colonies_maintainance WHERE colonies_id=".$this->id,1) >= $max_maintain) return "Es werden bereits ".$max_maintain." Schiffe gewartet";
		$data = $this->db->query("SELECT a.id,a.rumps_id,a.user_id,a.name,a.cloak,a.schilde_status,a.huelle,a.max_huelle,a.systems_id,a.sx,a.sy,a.alvl,a.still,a.maintain,b.buildtime,b.m1,b.m2,b.m3,b.m4,b.m5,b.m6,b.m7,b.m8,b.m9,b.m10,b.m11,c.eps_cost,c.slots,c.is_shuttle,d.vac_active FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON c.rumps_id=b.rumps_id LEFT JOIN stu_user as d ON d.id=a.user_id WHERE a.id=".$shipid." LIMIT 1",4);
		if ($data == 0) return;
		if (checkcolsector($data) == 0) return;
		if ($data['vac_active'] == 1) return "Der Siedler befindet sich im Urlaubsmodus";
		if ($data['schilde_status'] == 1) return "Schiff kann nicht gewartet werden (Grund: Schilde des Schiffs sind aktiviert)";
		if ($data['cloak'] == 1) return;
		if ($data['slots'] > 0) return;
		if ($data['is_shuttle'] == 1) return;
		if ($data['maintain'] > 0) return "Dieses Schiff wird bereits gewartet";
		if ($data['user_id'] != $this->uid && $data['alvl'] > 1) return "Fremde Schiffe in Alarmbereitschaft können nicht gewartet werden";
		if ($this->eps < round($data['eps_cost']/4)) return "Für die Wartung wird ".round($data['eps_cost']/4)." Energie benötigt - Vorhanden ist nur ".$this->eps;
		$result = $this->getShipMaintainanceCost($data);
		$time = time()+round(($data['buildtime']/4)*$time_modif);
		while ($value = mysql_fetch_assoc($result))
		{
			if (!$value['gcount']) continue;
			if ($value['gcount'] > $value['vcount'])
			{
				$err = 1;
				$msg = "Für die Wartung wird ".$value['gcount']." ".$value['name']." benötigt - Vorhanden ist nur ".(!$value[vcount] ? 0 : $value[vcount]);
				break;
			}
		}
		if ($err == 1)
		{
			return $msg;
		}
		$result = $this->getShipMaintainanceCost($data);
		while ($value = mysql_fetch_assoc($result))
		{
			$this->lowerstorage($this->id,$value['goods_id'],$value['gcount']);
		}
		$this->db->query("UPDATE stu_ships SET still=0,maintain=".$time.",wea_phaser='0',wea_torp='0' WHERE id=".$data['id']." LIMIT 1");
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=NULL WHERE traktor=".$data['id']." OR id=".$data['id']." LIMIT 2");
		$this->db->query("UPDATE stu_colonies SET eps=eps-".round($data['eps_cost']/4)." WHERE id=".$this->id);
		$this->db->query("INSERT INTO stu_colonies_maintainance (colonies_id,ships_id,maintaintime) VALUES ('".$this->id."','".$data['id']."','".$time."')");
		if ($data['user_id'] != $this->uid) $this->send_pm($this->uid,$data['user_id'],"Die ".$data['name']." wird in der Werft der Kolonie ".$this->name." gewartet",3);
		return "Die ".$data['name']." wird gewartet - Abschluß der Arbeiten: ".date("d.m.Y H:i",$time);
	}

	function buildcolship()
	{
		if ($this->check_rbf($this->id) == 0) return "Zum Bau wird ein aktivierter Raumbahnhof benötigt";
		if ($this->check_werft($this->id) == 0) die(show_error(902));
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Schiffbau ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships WHERE rumps_id=1 AND user_id=".$this->uid,1) > 0) return "Du kannst nur ein Kolonisationsschiff besitzen";
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships_buildprogress WHERE rumps_id=1 AND user_id=".$this->uid,1) > 0) return "Du kannst nur ein Kolonisationsschiff besitzen";
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships_buildprogress WHERE colonies_id=".$this->id." AND user_id=".$this->uid,1) != 0) return "Es können nicht mehrere Kolonisationsschiffe zur selben Zeit gebaut werden";
		if ($this->db->query("SELECT COUNT(b.field_id) FROM stu_colonies as a LEFT JOIN stu_colonies_fielddata as b ON b.colonies_id=a.id WHERE a.user_id=".$this->uid." AND b.buildings_id=8",1) > 0) return "Du kannst nur ein Kolonisationsschiff besitzen";
		$this->getrumpbyid(1);
		if ($this->db->query("SELECT COUNT(*) FROM stu_colonies WHERE user_id=".$this->uid,1) == 6) return "Du kannst zur Zeit kein Kolonieschiff bauen, da das Planetenlimit (6) erreicht ist";
		if ($this->rump[eps_cost] > $this->eps) return "Zum Bau des Kolonisationschiffes wird ".$this->rump['eps_cost']." Energie benötigt - Vorhanden ist nur ".$this->eps;
		$result = $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.rumps_id=1 ORDER BY b.sort");
		while($data=mysql_fetch_assoc($result))
		{
			if ($data['count'] > $data['vcount'])
			{
				return "Zum Bau des Kolonisationsschiffes werden ".$data['count']." ".$data['name']." benötigt - Vorhanden sind nur ".(!$data[vcount] ? 0 : $data[vcount]);
			}
		}
		$result = $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.rumps_id=1 ORDER BY b.sort");
		while($data=mysql_fetch_assoc($result))
		{
			$this->lowerstorage($this->id,$data['goods_id'],$data['count']);
		}
		$this->db->query("UPDATE stu_colonies SET eps=eps-".$this->rump['eps_cost']." WHERE id=".$this->id);
		$data = $this->db->query("SELECT m1,m2,m3,m4,m5,m7,m8,m11  FROM stu_ships_buildplans WHERE plans_id=1",4);
		$i = 0;
		while($i<=11)
		{
			if ($data["m".$i] == 0)
			{
				$i++;
				continue;
			}
			$mod = $this->db->query("SELECT type,huelle,schilde,buildtime,eps,lss,kss FROM stu_modules WHERe module_id=".$data["m".$i],4);
			$huelle += $mod[huelle]*$this->rump["m".$mod[type]."c"];
			$bz += $mod[buildtime]*$this->rump["m".$mod[type]."c"];
			$schilde += $mod[schilde]*$this->rump["m".$mod[type]."c"];
			$eps += $mod[eps]*$this->rump["m".$mod[type]."c"];
			$lss += $mod[lss];
			$kss += $mod[kss];
			$i++;
		}
		$batt = $this->rump["m8c"]*2;
		$bz = time()+$this->rump["buildtime"];
		$this->db->query("INSERT INTO stu_ships_buildprogress (colonies_id,user_id,rumps_id,plans_id,buildtime,huelle,schilde,warpable,cloakable,phaser,eps,batt,lss,kss) VALUES ('".$this->id."','".$this->uid."','".$this->rump[rumps_id]."','1','".$bz."','".$huelle."','".$schilde."','1','0','0','".$eps."','".$batt."','".$lss."','".$kss."')");
		return "Schiff (Kolonisationsschiff) wird gebaut. Fertigstellung: ".date("d.m.Y H:i",$bz);
	}

	function getdemontableships() { return $this->db->query("SELECT a.id,a.name,a.rumps_id,a.huelle,a.max_huelle,b.name as rname FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.user_id=".$this->uid." AND a.systems_id=".$this->systems_id." AND a.sx=".$this->sx." AND a.sy=".$this->sy." AND b.slots=0 ORDER BY a.fleets_id,a.rumps_id,a.id"); }

	function demontship($shipid)
	{
		if ($this->check_rbf($this->id) == 0) return "Zum Demontieren wird ein aktivierter Raumbahnhof benötigt";
		if ($this->check_werft($this->id) == 0) die(show_error(902));
		$data = $this->db->query("SELECT a.id,a.rumps_id,a.plans_id,a.user_id,a.fleets_id,a.name,a.cloak,a.schilde_status,a.huelle,a.max_huelle,a.systems_id,a.sx,a.sy,b.buildtime,b.m1,b.m2,b.m3,b.m4,b.m5,b.w1,b.w2,b.s1,b.s2 FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON c.rumps_id=a.rumps_id WHERE a.user_id=".$this->uid." AND c.slots=0  AND a.id=".$shipid,4);
		if ($data == 0) return;
		$amode = $this->getcolattackstate($this->id);
		if ($amode > 0) {
			return "Schiffsdemontage ist aufgrund einer angreifenden oder blockierenden Flotte nicht möglich.";
		}
		if (checkcolsector($data) == 0) return;
		if ($this->db->query("SELECT fleets_id FROM stu_fleets WHERE ships_id=".$data[id],1) > 0) return "Das Schiff kann nicht demontiert werden, da es Flaggschiff einer Flotte ist";
		$dat = $this->db->query("SELECT m1,m2,m3,m4,m5,w1,w2,s1,s2 FROM stu_ships_buildplans WHERE plans_id=".$data[plans_id],4);
		// Status der Huelle berechnen
		$pro = round((100/$data[max_huelle])*$data[huelle]);
		
		$i=1;
		while($i<=5)
		{
			if ($dat["m".$i] == 0)
			{
				$i++;
				continue;
			}
			$mod[] = $this->db->query("SELECT b.name,b.module_id,b.demontchg FROM stu_rumps as a LEFT JOIN stu_modules as b ON b.module_id=".$dat["m".$i]." WHERE a.rumps_id=".$data[rumps_id],4);
			$i++;
		}
		
		$i=1;
		while($i<=2)
		{
			if ($dat["w".$i] == 0)
			{
				$i++;
				continue;
			}
			$mod[] = $this->db->query("SELECT b.name,b.module_id,b.demontchg FROM stu_rumps as a LEFT JOIN stu_modules as b ON b.module_id=".$dat["w".$i]." WHERE a.rumps_id=".$data[rumps_id],4);
			$i++;
		}
		
		$i=1;
		while($i<=2)
		{
			if ($dat["s".$i] == 0)
			{
				$i++;
				continue;
			}
			$mod[] = $this->db->query("SELECT b.name,b.module_id,b.demontchg FROM stu_rumps as a LEFT JOIN stu_modules as b ON b.module_id=".$dat["s".$i]." WHERE a.rumps_id=".$data[rumps_id],4);
			$i++;
		}
		
		$i=1;
		$result = $this->db->query("SELECT a.goods_id,ROUND((a.count/100)*".$pro.") as count,b.name FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.rumps_id=".$data[rumps_id]." ORDER BY b.sort");
		$mo = 0;
		foreach($mod as $key => $value)
		{
				if (rand(1,100) <= $value[demontchg])
				{
					$this->upperstorage($this->id,$value[module_id],1);
					$mo++;
				}
		}
		while($dat=mysql_fetch_assoc($result))
		{
			if (!$dat['count'] || $dat['count'] == 0) continue;
			$this->upperstorage($this->id,$dat[goods_id],$dat['count']);
			$msg .= "<br>".$dat['count']." ".$dat[name];
		}
		$this->db->query("UPDATE stu_ships_logdata SET destroytime=".time()." WHERE ships_id=".$data['id']." LIMIT 1");
		$this->db->query("DELETE FROM stu_ships WHERE id=".$data[id]);
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE traktor=".$data[id]);
		$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$data[id]);
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=".$data[id]);
		$this->db->query("DELETE FROM stu_ships_decloaked WHERE ships_id=".$data[id]);
		$this->db->query("DELETE FROM stu_dockingrights WHERE type='1' AND id=".$data[id]);
		return "Die ".$data[name]." wurde demontiert. ".($msg ? "Folgende Waren wurden dabei gewonnen".$msg : "").($mo > 0 ? "<br>Es wurden ".$mo." Module gewonnen" : "");
	}

	function getprodoverview() { return $this->db->query("SELECT c.goods_id,SUM(c.count) as pc,d.name FROM stu_colonies as a LEFT JOIN stu_colonies_fielddata as b ON b.colonies_id=a.id LEFT JOIN stu_buildings_goods as c ON c.buildings_id=b.buildings_id LEFT JOIN stu_goods as d ON d.goods_id=c.goods_id WHERE a.user_id=".$this->uid." AND b.aktiv=1 GROUP BY c.goods_id ORDER BY d.sort"); }
	
	function getbonusoverview() { return $this->db->query("SELECT b.goods_id, SUM(b.count) as gc FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies_bonus as b ON a.buildings_id = b.buildings_id LEFT JOIN stu_colonies as c ON c.id = a.colonies_id WHERE c.user_id=".$this->uid." AND a.aktiv=1 AND b.colonies_classes_id = c.colonies_classes_id GROUP by b.goods_id;"); }
	
	function getpopulationsum() { return $this->db->query("SELECT SUM(bev_work)+SUM(bev_free) FROM stu_colonies WHERE user_id=".$this->uid,1); }

	function loadsysoffers($system_id)
	{
		$this->co = $this->db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=".$system_id." AND b.mode='1' GROUP BY b.goods_id ORDER BY c.sort");
		$this->cw = $this->db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=".$system_id." AND b.mode='2' GROUP BY b.goods_id ORDER BY c.sort");
	}

	function getpossibleShuttles() { return $this->db->query("SELECT a.rumps_id,a.goods_id,a.name,c.eps_cost FROM stu_shuttle_types as a LEFT JOIN stu_researched as b ON b.research_id=a.research_id AND b.user_id=".$this->uid." LEFT JOIN stu_rumps as c ON c.rumps_id=a.rumps_id WHERE (a.research_id=0 OR (a.research_id>0 AND !ISNULL(b.user_id))) AND (a.faction='0' OR a.faction='".$this->sess[race]."') AND a.buildable='1' AND a.rumps_id!=5 ORDER BY a.name"); }

	function getrumpcost($rid) { return $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_colonies_storage as c ON a.goods_id=c.goods_id AND c.colonies_id=".$this->id." WHERE a.rumps_id=".$rid." ORDER BY b.sort"); }

	function shuttleherstellung($moarr)
	{
		if ($this->sess['level'] < 6) return;
		foreach($moarr as $key => $value)
		{
			if (!check_int($value) || !check_int($key) || $value< 1) continue;
			if ($value > 99) $value = 99;
			$data = $this->db->query("SELECT a.goods_id,a.rumps_id,a.name,b.eps_cost FROM stu_shuttle_types as a LEFT JOIN stu_rumps as b ON b.rumps_id=a.rumps_id LEFT JOIN stu_researched as c ON c.research_id=a.research_id AND c.user_id=".$this->uid." WHERE a.goods_id=".$key." AND (a.faction='".$this->sess[race]."' OR a.faction='0') AND a.buildable='1' AND (a.research_id=0 OR (a.research_id>0 AND !ISNULL(c.user_id)))",4);
			if ($data == 0) die(show_error(902));
			$werft = $this->getwerfttype($this->id);
			if ($data[rumps_id] > 5 && $werft == 300) return;
			if ($data[eps_cost]*$value > $this->eps) $value = floor($this->eps/$data[eps_cost]);
			if ($this->eps < $data[eps_cost]*$value)
			{
				$msg .= "Für die Herstellung von ".$data[name]." wird ".$data[eps_cost]." Energie benötigt - Vorhanden ist nur ".$this->eps."<br>";
				continue;
			}
			$cr = $this->getrumpcost($data[rumps_id]);
			while($co=mysql_fetch_assoc($cr))
			{
				if ($value*$co['count'] > $co[vcount])
				{
					$value = floor($co[vcount]/$co['count']);
					if ($value == 0)
					{
						$msg .= "Für die Herstellung von ".$data[name]." werden ".$co['count']." ".$co[name]." benötigt - Vorhanden ist nur ".(!$co[vcount] ? 0 : $co[vcount])."<br>";
						break;
					}
				}
				if ($value != 0) $cost[$co[goods_id]] = $co['count'];
			}
			if ($value == 0)
			{
				$msg .= "Es wurden keine Shuttle des Typs ".$data[name]." hergestellt<br>";
				continue;
			}
			$this->db->query("START TRANSACTION");
			if (is_array($cost))
			{
				foreach($cost as $key2 => $value2) $this->lowerstorage($this->id,$key2,$value2*$value);
			}
			$this->upperstorage($this->id,$data[goods_id],$value);
			$this->db->query("UPDATE stu_colonies SET eps=eps-".($value*$data[eps_cost])." WHERE id=".$this->id);
			$this->db->query("COMMIT");
			$this->eps -= ($value*$data[eps_cost]);
			$msg .= "Es wurden ".$value." ".$data[name]." hergestellt<br>";
			unset($cost);
		}
		return $msg;
	}
	
	function getterraformprogress() { return $this->db->query("SELECT a.terraformtime,a.terraforming_id,b.id,b.colonies_classes_id,b.name as cname,c.name ,c.v_feld FROM stu_colonies_terraforming as a LEFT JOIN stu_colonies as b ON b.id=a.colonies_id LEFT JOIN stu_terraforming as c ON a.terraforming_id=c.terraforming_id WHERE b.user_id=".$this->uid." ORDER BY b.colonies_classes_id,b.id ASC,a.field_id"); }

	function save_buildplan()
	{
		global $_GET;
		if ($this->check_werft($this->id) == 0) die(show_error(902));
		if ($this->rump['warpable'] != 1 && check_int($_GET["m11"]) && $_GET["m11"] != 0) die(show_error(902));
		if ($this->rump['m5c'] == 0 && check_int($_GET["m5"]) && $_GET["m5"] != 0) die(show_error(902));
		$i = 1;
		while($i <= 11)
		{
			if (($i == 9 || $i == 10 || $i == 11 || $i == 5 || $i == 6) && (!check_int($_GET["m".$i]) || $_GET["m".$i] == 0))
			{
				$i++;
				continue;
			}
			if ($i == 11 && !check_int($_GET["m5"])) return "Um einen Warpantrieb einzubauen muss auch ein Warpkern eingebaut werden";
			if (!check_int($_GET["m".$i])) return "Es wurde kein Modul des Typs ".getmodtypedescr($i)." ausgewählt";
			$mod = $this->getmodbyid($this->rump["m".$i."minlvl"],$this->rump["m".$i."maxlvl"],$i,$_GET["m".$i]);
			if ($mod == 0) die(show_error(902));
			$bm[] = $mod;
			$i++;
		}
		$stellar = 0;
		foreach($bm as $key => $data)
		{
			$huelle += $data[huelle]*$this->rump["m".$data[type]."c"];
			$bz += $data[buildtime];
			$points += $data[points];
			$schilde += $data[schilde]*$this->rump["m".$data[type]."c"];
			$maintain += $data[maintaintime];
			$reaktor += $data[reaktor]*$this->rump["m".$data[type]."c"];
			$wkkap += $data[wkkap]*$this->rump["m".$data[type]."c"];
			$eps += $data[eps]*$this->rump["m".$data[type]."c"];
			if ($data[type] == 1)
			{
				if ($data[special_id1] == 1)
				{
					$ev_mul = 1.1;
					$hit_mul = 1.1;
				}
				if ($data[special_id1] == 2)
				{
					$ev_mul = 0.9;
					$hit_mul = 0.9;
				}
			}
			else $evade += $data[evade_val];
			$hit += $data[hit_val];
			$torps += $data[torps]*$this->rump["m".$data[type]."c"];
			$detect += $data[detect_val];
			$cloak += $data[cloak_val];
			if ($data[type] == 4)
			{
				$lss = $data[lss]+($this->rump["m".$data[type]."c"] - 1);
				$kss = $data[kss]+($this->rump["m".$data[type]."c"] - 1);
			}
			if ($data[type] == 6)
			{
				$weapon = $this->getweaponbyid($data[module_id]);
				$phaser = round($weapon[strength] * (1 + (log($this->rump["m".$data[type]."c"]) / log(2))/3));
				$vari = $weapon[varianz];
			}
			if ($data[stellar] == 1) $stellar = 1;
		}
		if (!$weapon) $phaser = 0;
		$points = round(($this->rump[wp] * $points) / 10);
		if (!$reaktor) $reaktor = $this->rump[reaktor];
		$evade += $this->rump[evade_val];
		if (!$ev_mul) $ev_mul = 1;
		$evade = round($evade * $ev_mul);
		if (!$hit_mul) $hit_mul = 1;
		$hit = round($hit * $hit_mul);
		$batt = $this->rump["m8c"]*2;
		$bz = round($this->rump[buildtime]*$bz/1000);
		$main = round($this->rump[maintaintime]*(2- ($maintain/1100)));
		$time = time()+$bz;
		if (check_int($_GET[m9])) $cloak = 1;
		if (check_int($_GET[m5]) && check_int($_GET[m11])) $warp = 1;
		$plan = $this->getpossiblebuildplans();
		if ($plan != 0) return "Es ist bereits ein Bauplan mit diesen Module vorhanden (".stripslashes($plan['name']).")";
		// Bauplan anlegen
		!$_GET['npn'] ? $pn = $this->rump['name']." ".date("d.m.Y H:i") : $pn = $_GET['npn'];
		$this->db->query("INSERT INTO stu_ships_buildplans (rumps_id,user_id,name,m1,m2,m3,m4,m5,m6,m7,m8,m9,m10,m11,wpoints) VALUES ('".$this->rump[rumps_id]."','".$this->uid."','".addslashes($pn)."','".$_GET[m1]."','".$_GET[m2]."','".$_GET[m3]."','".$_GET[m4]."','".$_GET[m5]."','".$_GET[m6]."','".$_GET[m7]."','".$_GET[m8]."','".$_GET[m9]."','".$_GET[m10]."','".$_GET[m11]."','".$points."')",5);
		return "Der Bauplan wurde unter dem Namen ".stripslashes($pn)." abgespeichert";
	}

	function get_free_wpoints($userId) { return $this->db->query("SELECT SUM(lastrw) FROM stu_colonies WHERE user_id=".$userId,1)-$this->db->query("SELECT SUM(points) FROM stu_ships WHERE user_id=".$userId,1); }

	function get_buildplan_count($userId) { return $this->db->query("SELECT COUNT(b.plans_id) FROM stu_rumps as a LEFT JOIN stu_ships_buildplans as b USING(rumps_id) WHERE a.slots=0 AND b.user_id=".$userId,1); }

	function hide_buildplan($planId)
	{
		$this->db->query("UPDATE stu_ships_buildplans SET hidden='1' WHERE plans_id=".$planId." AND user_id=".$this->uid." LIMIT 1");
		return "Bauplan wurde verschoben";
	}

	function unhide_buildplan($planId)
	{
		$this->db->query("UPDATE stu_ships_buildplans SET hidden='0' WHERE plans_id=".$planId." AND user_id=".$this->uid." LIMIT 1");
		return "Bauplan wird wieder normal angezeigt";
	}

	function get_buildplan_list()
	{
		global $_GET;
		// OVERRIDE
		return $this->db->query("SELECT a.*,b.name as rname FROM stu_ships_buildplans as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.user_id=".$this->uid." ORDER BY a.hidden");
	}

	function getjunkmodules($type,$cid) 
	{ 
		if ($type == 90) return $this->db->query("SELECT a.*,b.name FROM stu_colonies_storage as a left join stu_goods as b on a.goods_id = b.goods_id WHERE colonies_id=".$cid." AND a.goods_id=906"); 
		if ($type == 91) return $this->db->query("SELECT a.*,b.name FROM stu_colonies_storage as a left join stu_goods as b on a.goods_id = b.goods_id WHERE colonies_id=".$cid." AND ( a.goods_id=903 OR a.goods_id=905 OR a.goods_id=908)"); 
		if ($type == 92) return $this->db->query("SELECT a.*,b.name FROM stu_colonies_storage as a left join stu_goods as b on a.goods_id = b.goods_id WHERE colonies_id=".$cid." AND ( a.goods_id=901 OR a.goods_id=902 OR a.goods_id=904 OR a.goods_id=907)"); 		
	}

	function drawrandom($modules)
	{
		$chance = 0;
		for ($i = 1; $i <= $modules['count']; $i++) 
		{
			$chance += $modules[$i]['chance'];
		}
		$rand = rand(1,$chance);
		$chance = 0;
		for ($i = 1; $i <= $modules['count']; $i++) 
		{
			if ($rand > $chance)
			{
				$result = $modules[$i]['id'];
			}
			$chance += $modules[$i]['chance'];
		}
		return $result;
	}
	
	function drawmodule($goodid)
	{
		$npc = rand (1,100);
		include_once("inc/junkmods.inc.php");
		return $this->drawrandom($modules);
	}

	function modulanalyse($goodid)
	{
		if ($goodid == 99)
		{
			$researchid = 8001;
			$needcount = 1;
		}
		
		else return "Fehler: Unbekanntes Modul.";
		$result = $this->db->query("SELECT * FROM stu_researched WHERE research_id = ".$researchid." AND user_id = ".$this->uid,3);
		if ($result != 0) return "Diese Daten sind bereits verfügbar."; 
		$havecount = $this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id = ".$goodid." AND colonies_id = ".$this->id,1);
		if ($havecount < $needcount) return "Nicht genug Module vorhanden - zur Analyse werden ".$needcount." Module dieses Typs benötigt.";
		$this->db->query("INSERT INTO stu_researched (research_id,user_id) VALUES ('".$researchid."','".$this->uid."')");
		$this->lowerstorage($this->id,$goodid,$needcount);
		return "Datenkern erfolgreich eingespeist.";
	}

	function kernkopie()
	{
		$result = $this->db->query("SELECT * FROM stu_researched WHERE research_id = 8007 AND user_id = ".$this->uid,3);
		if ($result == 0) return "Fehler: Forschung fehlt."; 
		$havecount = $this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id = 98 AND colonies_id = ".$this->id,1);
		if ($havecount < 5) return "Nicht genug Dominion-Bauteile vorhanden - es werden 5 benötigt.";

		$this->lowerstorage($this->id,98,5);
		$this->upperstorage($this->id,99,1);
		
		return "Datenkern erfolgreich kopiert.";
	}
	
	function send_buildplan($plans_id,$user_id)
	{
		$user = $this->db->query("SELECT a.id,a.user FROM stu_user as a LEFT JOIN stu_contactlist as b ON b.user_id=a.id AND b.recipient=".$this->uid." WHERE (a.id!=".$this->uid." AND (a.allys_id=".$this->sess['allys_id']." AND a.allys_id>0) OR b.mode='1') AND a.race='".$this->sess['race']."' AND a.id=".$user_id,4);
		if ($user == 0) return;
		$data = $this->db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id=".$plans_id." LIMIT 1",4);
		if ($this->db->query("SELECt plans_id FROM stu_ships_buildplans WHERE rumps_id=".$data['rumps_id']." AND user_id=".$user_id." AND m1=".$data['m1']." AND m2=".$data['m2']." AND m3=".$data['m3']." AND m4=".$data['m4']." AND m5=".$data['m5']." AND m6=".$data['m6']." AND m7=".$data['m7']." AND m8=".$data['m8']." AND m9=".$data['m9']." AND m10=".$data['m10']." AND m11=".$data['m11']." LIMIT 1",1) > 0) return "Dieser Siedler hat bereits einen derartigen Bauplan";
		$data['user_id'] = $user_id;
		$data['plans_id'] = "";
		$data['hidden'] = 0;
		$data['name'] = addslashes($data['name']);
		$this->db->query("INSERT INTO stu_ships_buildplans (plans_id,rumps_id,user_id,name,evade,treffer,reaktor,wkkap,max_torps,buildtime,maintaintime,stellar,sensor_val,cloak_val,m1,m2,m3,m4,m5,m6,m7,m8,m9,m10,m11,wpoints,hidden) VALUES ('".implode("','",$data)."')",5);
		$this->send_pm($this->uid,$user_id,"Der Siedler ".$this->sess['user']." hat Dir den Bauplan ".$data['name']." übermittelt. Dieser ist jetzt beim Schiffsbau verfügbar",1);
		return "Der Bauplan wurde verschickt";
	}

	function getcolattackstring($colid) {
		$blockade = $this->db->query("SELECT a.*,b.name,c.user FROM stu_colonies_actions as a LEFT OUTER JOIN stu_fleets as b on a.value=b.fleets_id LEFT OUTER JOIN stu_user as c ON b.user_id = c.id WHERE a.colonies_id =".$colid." AND (a.var='fdef' OR a.var='fblock' OR a.var='fattack') LIMIT 1",4);
		if ($blockade == 0) return "";
		else {
			if ($blockade['var'] == "fdef") {
				return "<img src='http://www.stuniverse.de/gfx/buttons/guard2.gif' border=0> <font color='#00FF00'>Wird von </font>".stripslashes($blockade[user])." <font color='#00FF00'>verteidigt.</font>";
			}
			elseif ($blockade['var'] == "fblock") {
				return "<img src='http://www.stuniverse.de/gfx/buttons/x2.gif' border=0> <font color='#CCCCCC'>Wird von </font>".stripslashes($blockade[user])." <font color='#CCCCCC'>blockiert.</font>";
			}
			elseif ($blockade['var'] == "fattack") {
				return "<img src='http://www.stuniverse.de/gfx/buttons/leavecol2.gif' border=0> <font color='#FF0000'>Wird von </font>".stripslashes($blockade[user])." <font color='#FF0000'>angegriffen!</font>";
			}
		}
		return "";
	}



	function getcolattackstate($colid) {
		$mode = $this->db->query("SELECT attackmode FROM stu_colonies_actions WHERE colonies_id = ".$colid." AND attackmode != 0",1);
		return $mode;

	}


	function workbeeherstellung($moarr)
	{
		if ($this->sess['level'] < 6) return;
		foreach($moarr as $key => $value)
		{
			if (!check_int($value) || !check_int($key) || $value< 1) continue;
			if ($value > 99) $value = 99;

			$werft = $this->getwerfttype($this->id);

			if (15*$value > $this->eps) $value = floor($this->eps/15);
			if (($this->eps < 15*$value) || ($value == 0))
			{
				return "Für die Herstellung der Workbee wird 15 Energie benötigt - Vorhanden ist nur ".$this->eps."<br>";

			}

			
			$result = $this->db->query("SELECT a.goods_id,a.count,b.count as vcount,c.name FROM stu_rumps_buildcost as a LEFT JOIN stu_colonies_storage as b ON a.goods_id=b.goods_id AND b.colonies_id=".$this->id." LEFT JOIN stu_goods as c ON a.goods_id=c.goods_id WHERE a.rumps_id=5 ORDER BY c.sort");
			while($cost=mysql_fetch_assoc($result))
			{
				if ($cost['vcount'] < $cost['count'])
				{
					return "Es werden ".$cost['count']." ".$cost['name']." benötigt - Vorhanden sind nur ".(!$cost['vcount'] ? 0 : $cost['vcount']);
				}
			}


			if ($value != 0) {
				$cst[2] = 20*$value;
				$cst[3] = 20*$value;
			}


			if ($value == 0)
			{
				$msg .= "Es wurden keine Shuttle des Typs ".$data[name]." hergestellt<br>";
				continue;
			}
			$this->db->query("START TRANSACTION");
			if (is_array($cst))
			{
				foreach($cst as $key2 => $value2) $this->lowerstorage($this->id,$key2,$value2);
			}
			$this->upperstorage($this->id,10,$value);
			$this->db->query("UPDATE stu_colonies SET eps=eps-".($value*15)." WHERE id=".$this->id);
			$this->db->query("COMMIT");
			$this->eps -= ($value*15);
			$msg .= "Es wurden ".$value." Workbee hergestellt<br>";
			unset($cost);
		}
		return $msg;
	}


}
?>
