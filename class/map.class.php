<?php
class map
{
	function map()
	{
		global $db,$_SESSION;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->sess = $_SESSION;
	}

	function getfieldspecial($x,$y,$sysid) { 
		if ($sysid == 0) return $this->db->query("SELECT type FROM stu_map_special WHERE cx = ".$x." AND cy = ".$y."",1); 
		else return $this->db->query("SELECT type FROM stu_map_special WHERE sx = ".$x." AND sy = ".$y." AND systems_id = ".$sysid."",1);
	}

	function getspecial($x,$y,$sys) {
		if ($sys > 0) {
			return $this->db->query("SELECT * FROM stu_map_special WHERE sx=".$x." AND sy=".$y." AND systems_id = ".$sys." LIMIT 1;",4);
		} else {
			return $this->db->query("SELECT * FROM stu_map_special WHERE cx=".$x." AND cy=".$y." AND systems_id = 0 LIMIT 1;",4);
		}
	}
	
	function getlss($x,$y,$range) { return $this->db->query("SELECT a.cx,a.cy,a.type,a.faction_id,a.is_border,c.name as fname,c.color, (SELECT max(cloakstrength) as cs FROM stu_ships WHERE systems_id = 0 AND cx = a.cx AND cy = a.cy AND cloak=1) as cs,(COUNT(b.id) + (SELECT count(t.id) as bla FROM stu_map as r LEFT JOIN stu_systems as s on r.cx=s.cx AND r.cy=s.cy LEFT JOIN stu_stations as t ON s.systems_id = t.systems_id WHERE r.cx = a.cx AND r.cy = a.cy GROUP BY r.cx,r.cy ORDER BY r.cy,r.cx )) as sc, d.type as special FROM stu_map as a LEFT JOIN stu_ships as b ON a.cx=b.cx AND a.cy=b.cy AND b.rumps_id != 99 AND (b.cloak!='1' OR b.cloak='' OR ISNULL(b.cloak)) AND ((b.systems_id>0 AND b.cfield!=7) OR b.systems_id=0) LEFT JOIN stu_factions as c USING(faction_id) LEFT JOIN stu_map_special AS d ON a.cx=d.cx AND a.cy=d.cy WHERE a.cx BETWEEN ".($x-$range)." AND ".($x+$range)." AND a.cy BETWEEN ".($y-$range)." AND ".($y+$range)." GROUP BY a.cx,a.cy ORDER BY a.cy,a.cx LIMIT ".(($range*2+1)*($range*2+1))); }

	function getkss($x,$y,$range,$sysId) { return $this->db->query("SELECT a.sx as cx,a.sy as cy,a.type, (SELECT max(cloakstrength) as cs FROM stu_ships WHERE systems_id = a.systems_id AND sx = a.sx AND sy = a.sy AND cloak=1) as cs,COUNT(b.id) + (SELECT count(t.id) as bla FROM stu_sys_map as r LEFT JOIN stu_stations as t ON r.sy = t.sy AND r.sx = t.sx AND r.systems_id = t.systems_id WHERE r.sx = a.sx AND r.sy = a.sy AND r.systems_id = a.systems_id GROUP BY r.sx,r.sy ORDER BY r.sy,r.sx ) as sc, d.type as special FROM stu_sys_map as a LEFT JOIN stu_ships as b ON a.sx=b.sx AND a.sy=b.sy AND a.systems_id=b.systems_id AND b.rumps_id != 99 AND (b.cloak!='1' OR b.cloak='' OR ISNULL(b.cloak)) AND b.systems_id=".$sysId." LEFT JOIN stu_map_special AS d ON a.sx=d.sx AND a.sy=d.sy AND a.systems_id=d.systems_id WHERE a.sx BETWEEN ".($x-$range)." AND ".($x+$range)." AND a.sy BETWEEN ".($y-$range)." AND ".($y+$range)." AND a.systems_id=".$sysId." GROUP BY a.sx,a.sy ORDER BY a.sy,a.sx LIMIT ".(($range*2+1)*($range*2+1))); }
	
	function getlssfieldsonly($x,$y,$range) { return $this->db->query("SELECT a.cx,a.cy,a.type,a.faction_id,a.is_border FROM stu_map as a WHERE a.cx BETWEEN ".($x-$range)." AND ".($x+$range)." AND a.cy BETWEEN ".($y-$range)." AND ".($y+$range)." GROUP BY a.cx,a.cy ORDER BY a.cy,a.cx LIMIT ".(($range*2+1)*($range*2+1))); }

	function getkssfieldsonly($x,$y,$range,$sysId) { return $this->db->query("SELECT a.sx as cx,a.sy as cy,a.type FROM stu_sys_map as a WHERE a.sx BETWEEN ".($x-$range)." AND ".($x+$range)." AND a.sy BETWEEN ".($y-$range)." AND ".($y+$range)." AND a.systems_id=".$sysId." GROUP BY a.sx,a.sy ORDER BY a.sy,a.sx LIMIT ".(($range*2+1)*($range*2+1))); }
	
	
	function getfieldbyid_lss($x,$y) { return $this->db->query("SELECT a.type,a.faction_id,a.is_border,b.name,b.colonies_classes_id as cid,b.is_system,b.ecost,b.deut,b.erz,b.sensoroff,b.shieldoff,b.cloakoff,c.name as fname,c.color,c.flight_infix FROM stu_map as a LEFT JOIN stu_map_ftypes as b USING(type) LEFT JOIN stu_factions as c USING(faction_id) WHERE a.cx=".$x." AND a.cy=".$y." LIMIT 1",4); }

	function getfieldbyid_kss($x,$y,$sysId) { return $this->db->query("SELECT a.type,b.name,b.colonies_classes_id as cid,b.is_system,b.ecost,b.deut,b.erz,b.sensoroff,b.shieldoff,b.cloakoff FROM stu_sys_map as a LEFT JOIN stu_map_ftypes as b USING(type) WHERE a.systems_id=".$sysId." AND a.sx=".$x." AND a.sy=".$y." LIMIT 1",4); }

	function getsystembyxy($x,$y) { return $this->db->query("SELECT systems_id,sr,sr,type,name FROM stu_systems WHERE cx=".$x." AND cy=".$y." LIMIT 1",4); }

	function getsystembyid(&$id) { return $this->db->query("SELECT systems_id,cx,cy,sr,type,name FROM stu_systems WHERE systems_id=".$id." LIMIT 1",4); }

	function getGlobalMap() {
		return $this->db->query("SELECT a.* FROM stu_map as a ORDER BY a.cy,a.cx");
	}
	
	function nbs()
	{
		global $ship;
		// return $this->db->query("SELECT a.id,a.fleets_id,a.rumps_id,a.name,a.huelle,a.max_huelle,a.cloak,a.schilde_status,a.schilde,a.max_schilde,a.user_id,a.traktor,a.traktormode,a.trumps_id,a.is_hp,a.user,a.allys_id,a.warp,a.cname,a.trumfield,a.fname,a.fship_id,a.is_rkn,b.type,c.ships_id as dcship_id,d.mode FROM stu_views_nbs as a LEFT JOIN stu_ally_relationship as b ON (a.allys_id=b.allys_id1 AND b.allys_id2=".$_SESSION['allys_id'].") OR (a.allys_id=b.allys_id2 AND b.allys_id1=".$this->sess['allys_id'].") LEFT JOIN stu_ships_decloaked as c ON a.id=c.ships_id AND c.user_id=".$this->sess['uid']." LEFT JOIN stu_contactlist as d ON d.user_id=a.user_id AND d.recipient=".$this->sess['uid']." WHERE a.id!=".$ship->id." AND ".($ship->systems_id > 0 ? "a.systems_id=".$ship->systems_id : "a.systems_id=0")." AND ".($ship->systems_id > 0 ? "a.sx=".$ship->sx : "a.cx=".$ship->cx)." AND ".($ship->systems_id > 0 ? "a.sy=".$ship->sy : "a.cy=".$ship->cy)." GROUP BY a.id");
		return $this->db->query("SELECT a.id,a.fleets_id,a.rumps_id,a.name,a.huelle,a.max_huelle,a.cloak,a.schilde_status,a.schilde,a.max_schilde,a.user_id,a.traktor,a.traktormode,a.trumps_id,a.is_hp,a.user,a.allys_id,a.warp,a.cname,a.trumfield,a.fname,a.fship_id,a.is_rkn,b.type,c.ships_id as dcship_id,d.mode FROM stu_views_nbs as a LEFT JOIN stu_ally_relationship as b ON (a.allys_id=b.allys_id1 AND b.allys_id2=".$_SESSION['allys_id'].") OR (a.allys_id=b.allys_id2 AND b.allys_id1=".$this->sess['allys_id'].") LEFT JOIN stu_ships_decloaked as c ON a.id=c.ships_id AND (c.user_id=".$this->sess['uid']." || c.user_id=0) LEFT JOIN stu_contactlist as d ON d.user_id=a.user_id AND d.recipient=".$this->sess['uid']." WHERE a.id!=".$ship->id." AND ".($ship->systems_id > 0 ? "a.systems_id=".$ship->systems_id : "a.systems_id=0")." AND ".($ship->systems_id > 0 ? "a.sx=".$ship->sx : "a.cx=".$ship->cx)." AND ".($ship->systems_id > 0 ? "a.sy=".$ship->sy : "a.cy=".$ship->cy)." GROUP BY a.id");
	}

	function getsysnamebyid($id) { return $this->db->query("SELECT name FROM stu_systems WHERE systems_id=".$id." LIMIT 1",1); }

	function loadsysdata($id) { $this->sys = $this->db->query("SELECT cx,cy,sr,name FROM stu_systems WHERE systems_id=".$id." LIMIT 1",4); }

	function get_known_systems($m,$sor="",$wa="")
	{
		switch ($sor)
		{
			case "ty":
				$order = "b.type";
				break;
			case "x":
				$order = "b.cx";
				break;
			case "y":
				$order = "b.cy";
				break;
			case "nm":
				$order = "b.name";
				break;
			default:
				$order = "b.name";
		}
		switch ($wa)
		{
			case "up":
				$way = "DESC";
				break;
			case "dn":
				$way = "ASC";
				break;
			default:
				$way = "ASC";
		}
		return $this->db->query("SELECT b.systems_id,b.name,b.cx,b.cy,b.type FROM stu_systems_user as a LEFT JOIN stu_systems as b USING(systems_id) WHERE a.user_id=".$this->uid." ORDER BY ".$order." ".$way." LIMIT ".$m.",25");
	}

	function get_known_systems_count() { return $this->db->query("SELECT COUNT(systems_id) FROM stu_systems_user WHERE user_id=".$this->uid,1); }

	function getknownsystembyid($sys_id) { return $this->db->query("SELECT a.sx,a.sy,a.type,b.id,b.planet_name,c.id as uid,c.user FROM stu_sys_map as a LEFT JOIN stu_colonies as b ON a.sx=b.sx AND a.sy=b.sy AND a.systems_id=b.systems_id LEFT JOIN stu_user as c ON b.user_id=c.id WHERE a.systems_id=".$sys_id." ORDER BY a.sy,a.sx"); }

	function getFieldByType(&$type) { return $this->db->query("SELECT ecost,name,colonies_classes_id,deut,damage,x_damage,sensoroff,shieldoff,cloakoff,shieldoff FROM stu_map_ftypes WHERE type=".$type." LIMIT 1",4); }

	function getsystemsinrange($x,$y,$r) { return $this->db->query("SELECT * FROM stu_systems WHERE cx BETWEEN ".($x-$r)." AND ".($x+$r)." AND cy BETWEEN ".($y-$r)." AND ".($y+$r)." ORDER BY cy,cx LIMIT ".(($r*2+1)*($r*2+1))); }

	function getosystemsinrange($x,$y,$r) { return $this->db->query("SELECT * FROM stu_systems WHERE cx BETWEEN ".($x-$r)." AND ".($x+$r)." AND cy BETWEEN ".($y-$r)." AND ".($y+$r)." ORDER BY cy,cx LIMIT ".(($r*2+1)*($r*2+1))); }

	function getokss($x,$y,$range,$sysId) { return $this->db->query("SELECT a.sx as cx,a.sy as cy,a.type, d.type as special FROM stu_sys_map as a LEFT JOIN stu_ships as b ON a.sx=b.sx AND a.sy=b.sy AND a.systems_id=b.systems_id AND (b.cloak!='1' OR b.cloak='' OR ISNULL(b.cloak)) AND b.systems_id=".$sysId." LEFT JOIN stu_map_special AS d ON a.sx=d.sx AND a.sy=d.sy AND a.systems_id=d.systems_id WHERE a.sx BETWEEN ".($x-$range)." AND ".($x+$range)." AND a.sy BETWEEN ".($y-$range)." AND ".($y+$range)." AND a.systems_id=".$sysId." GROUP BY a.sx,a.sy ORDER BY a.sy,a.sx LIMIT ".(($range*2+1)*($range*2+1))); }

}
?>