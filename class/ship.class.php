<?php
class ship extends qpm
{

	function weaponDebugEnabled()
	{
		return false;
	}

	function ship()
	{
		global $db, $_SESSION, $_GET, $map, $fleet, $gfx;
		$this->db = $db;
		$this->debug = $this->db->query("SELECT value FROM stu_game_vars WHERE var = 'debug' LIMIT 1;", 1);
		$this->uid = $_SESSION['uid'];
		$this->sess = $_SESSION;
		$this->m = $map;
		$this->f = $fleet;
		$this->destroyed = 0;
		$this->gfx = $gfx;
		if ($fleet->nfs > 0) $_GET['id'] = $fleet->nfs;
		if (($_GET['p'] == "ship" || $_GET['p'] == "fergp") && check_int($_GET['id'])) {
			$result = $this->db->query("SELECT a.*,b.name as cname,b.type as rtype,b.beamgood,b.beamcrew,b.storage,b.reaktor as creaktor,b.eps_drain,b.race,d.ships_id as fsf,d.name as fname, c.m1 as mod_m1,c.m2 as mod_m2,c.m3 as mod_m3,c.m4 as mod_m4,c.m5 as mod_m5,c.w1 as mod_w1,c.w2 as mod_w2, c.s1 as mod_s1, c.s2 as mod_s2 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id WHERE a.user_id=" . $this->uid . " AND a.id=" . $_GET['id'] . " LIMIT 1", 4);
			if ($result == 0) {
				$this->destroyed = 1;
				$this->dsships[$_GET['id']] = 1;
				return;
			}
			foreach ($result as $key => $value) $this->$key = $value;
			$this->systems_id > 0 ? $this->map = $map->getfieldbyid_kss($this->sx, $this->sy, $this->systems_id) : $this->map = $map->getfieldbyid_lss($this->cx, $this->cy);
		} elseif ((($_GET['p'] == "ship" && $_GET['s']) || $_GET['p'] == "fergp") && !check_int($_GET['id'])) die(show_error(902));
	}


	function getAllFleetPoints()
	{
		$sum = 0;

		$cpoints = $this->getAllPoints("pcrew");
		$mpoints = $this->getAllPoints("pmaintain");
		$spoints = $this->getAllPoints("psupply");

		return 20 + min($cpoints, min($mpoints, $spoints));
	}

	function getAllPoints($type)
	{
		$result = $this->db->query("SELECT id FROM stu_colonies WHERE user_id=" . $this->uid . ";");

		$sum = 0;
		while ($d = mysql_fetch_assoc($result)) {
			$sum += $this->getPoints($d['id'], $type);
		}

		if ($this->uid < 100) $sum += 500;

		return $sum;
	}





	function getPoints($cid, $type)
	{
		return $this->db->query("SELECT SUM(count) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings_effects as b on a.buildings_id = b.buildings_id WHERE a.colonies_id=" . $cid . " AND a.aktiv = 1 AND b.type='" . $type . "';", 1);
	}

	function getFleetPoints($id)
	{
		$buildings = $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id=" . $id . " AND buildings_id > 0 AND aktiv < 2;", 1);

		$isMoon = $this->db->query("SELECT b.is_moon FROM stu_colonies AS a LEFT JOIN stu_colonies_classes AS b ON a.colonies_classes_id = b.colonies_classes_id WHERE a.id = " . $id . ";", 1);

		if ($isMoon)
			$res['buildcount'] = floor(min($buildings * 0.5, 10));
		else
			$res['buildcount'] = floor(min($buildings * 0.5, 15));

		$build = 42;
		$res[$build] = $this->getBuildFleetPoints($id, $build);
		$build = 51;
		$res[$build] = $this->getBuildFleetPoints($id, $build);
		$build = 56;
		$res[$build] = $this->getBuildFleetPoints($id, $build);


		$sum = 0;
		foreach ($res as $val)
			$sum += $val;

		$res['sum'] = $sum;
		return $res;
	}

	function getBuildFleetPoints($col, $build)
	{
		return $this->db->query("SELECT SUM(b.count) FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings_effects as b on a.buildings_id=b.buildings_id WHERE a.colonies_id=" . $col . " AND a.buildings_id=" . $build . " AND aktiv=1 AND b.type = 'fleet';", 1);
	}


	function getCurrentFleetPoints()
	{
		return $this->db->query("SELECT SUM(b.fleetpoints) FROM stu_ships AS a LEFT JOIN stu_rumps AS b ON a.rumps_id = b.rumps_id WHERE a.user_id=" . $_SESSION['uid'], 1);
	}
	function getCurrentCivilianCount()
	{
		return $this->db->query("SELECT COUNT(*) FROM stu_ships AS a LEFT JOIN stu_rumps AS b ON a.rumps_id = b.rumps_id WHERE b.fleetpoints=0 AND a.user_id=" . $_SESSION['uid'], 1);
	}

	function getAllowedLSSModes()
	{
		$modes = array();
		array_push($modes, 'ship');
		array_push($modes, 'borders');

		$plan = $this->db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id = " . $this->plans_id, 4);

		$sensorspecials = $this->db->query("SELECT * FROM stu_modules_special WHERE modules_id=" . $plan['s1'] . " OR modules_id=" . $plan['s2'] . ";");

		while ($special = mysql_fetch_assoc($sensorspecials)) {
			if ($special['type'] == "scansub")
				array_push($modes, 'subspace');
			if ($special['type'] == "scanchr")
				array_push($modes, 'chroniton');
		}


		return $modes;
	}

	function switchLSSMode($mode)
	{
		$allowed = $this->getAllowedLSSModes();
		if (in_array($mode, $allowed)) {
			$this->db->query("UPDATE stu_ships SET lssmode='" . $mode . "' WHERE id = " . $this->id . " LIMIT 1;");
			return "Scanmodus ge�ndert";
		} else return "Scanmodus nicht erlaubt";
	}

	function enterWormhole()
	{

		if ($this->systems_id > 0)
			$special = $this->m->getspecial($this->dx, $this->dy, $ship->systems_id);
		else
			$special = $this->m->getspecial($this->cx, $this->cy, $ship->systems_id);

		if (!($special['type'] == "wormhole")) return;

		if ($this->fleets_id > 0) {
			$unable = $this->db->query("SELECT * FROM stu_ships WHERE fleets_id = " . $this->fleets_id . " AND (eps < 5 OR cloak=1 OR nbs=0 OR crew < min_crew);", 3);
			if ($unable > 0) return "Ein oder mehrere Schiffe k�nnen nicht in das Wurmloch einfliegen";

			$this->db->query("UPDATE stu_ships SET cx='" . $special['tx'] . "',cy='" . $special['ty'] . "',eps=eps-5 WHERE fleets_id = " . $this->fleets_id . ";");
			return "Wurmloch wurde durchflogen";
		} else {
			$return = shipexception(array("nbs" => 1, "eps" => 5, "cloak" => 0, "crew" => $this->min_crew), $this);
			if ($return['code'] == 1) return "Einflug in das Wurmloch nicht m�glich (Grund: " . $return['msg'] . ")";

			$this->db->query("UPDATE stu_ships SET cx='" . $special['tx'] . "',cy='" . $special['ty'] . "',eps=eps-5 WHERE id = " . $this->id . " LIMIT 1;");
			return "Wurmloch wurde durchflogen";
		}
	}

	function getSensorData($mode)
	{
		if ($mode == 'ship') return $this->getSensorDataShip();
		if ($mode == 'subspace') return $this->getSensorDataSubspace();
		if ($mode == 'chroniton') return $this->getSensorDataChroniton();
		if ($mode == 'borders') return $this->getSensorDataBorders();

		return false;
	}

	function getSensorDataShip()
	{
		$res = array();
		$res['desaturated'] = false;
		$res['fields'] = array();

		if ($this->systems_id > 0) {
			$queryResult = $this->m->getkss($this->sx, $this->sy, $this->kss_range, $this->systems_id);

			$shipx = $this->sx;
			$shipy = $this->sy;
			$detectbonus = 1;
		} else {
			$queryResult = $this->m->getlss($this->cx, $this->cy, $this->lss_range);

			$shipx = $this->cx;
			$shipy = $this->cy;
			$detectbonus = 0;
		}

		while ($data = mysql_fetch_assoc($queryResult)) {
			$field = array();

			$field['x'] = $data['cx'];
			$field['y'] = $data['cy'];
			$field['type'] = $data['type'];

			$field['onclick'] = false;

			$field['class'] = "fieldnormal";
			if (($field['x'] == $shipx) && ($field['y'] == $shipy)) $field['class'] = "fieldcurrent";
			if (($field['x'] == $shipx) && ($field['y'] != $shipy)) {
				$field['class'] = "fieldflyable";
				if ($field['y'] > $shipy) {
					$field['onclick'] = "doFly('d'," . ($field['y'] - $shipy) . ");";
				} else {
					$field['onclick'] = "doFly('u'," . ($shipy - $field['y']) . ");";
				}
			}
			if (($field['x'] != $shipx) && ($field['y'] == $shipy)) {
				$field['class'] = "fieldflyable";
				if ($field['x'] > $shipx) {
					$field['onclick'] = "doFly('r'," . ($field['x'] - $shipx) . ");";
				} else {
					$field['onclick'] = "doFly('l'," . ($shipx - $field['x']) . ");";
				}
			}

			$field['display'] = ($data['sc'] > 0 ? $data['sc'] : "");

			if ($data['cs'] && (abs($field['x'] - $shipx) + abs($field['y'] - $shipy)) < $data['cs'] + $ship->detectstrength + $detectbonus) {
				$field['display'] .= "?";
			}

			array_push($res['fields'], $field);
		}

		return $res;
	}

	function getSensorDataSubspace()
	{
		$res = array();
		$res['desaturated'] = true;
		$res['fields'] = array();

		if ($this->systems_id > 0) {
			$queryResult = $this->m->getkssfieldsonly($this->sx, $this->sy, $this->kss_range, $this->systems_id);
			$shipx = $this->sx;
			$shipy = $this->sy;
			$secResult = $this->db->query("SELECT rumps_id, sx as x, sy as y FROM stu_sectorflights  WHERE (sx BETWEEN " . ($shipx - ($this->kss_range + 1)) . " AND " . ($shipx + ($this->kss_range + 1)) . ") AND (sy BETWEEN " . ($shipy - ($this->kss_range + 1)) . " AND " . ($shipy + ($this->kss_range + 1)) . ") AND systems_id=" . $this->systems_id . " AND user_id!=" . $this->uid . " AND cloak='0';");
			$tertResult = $this->db->query("SELECT type,sx as x, sy as y FROM stu_map_special WHERE (sx BETWEEN " . ($shipx - ($this->kss_range + 1)) . " AND " . ($shipx + ($this->kss_range + 1)) . ") AND (sy BETWEEN " . ($shipy - ($this->kss_range + 1)) . " AND " . ($shipy + ($this->kss_range + 1)) . ") AND systems_id=" . $this->systems_id . ";");
		} else {
			$queryResult = $this->m->getlssfieldsonly($this->cx, $this->cy, $this->lss_range);
			$shipx = $this->cx;
			$shipy = $this->cy;
			$secResult = $this->db->query("SELECT rumps_id, cx as x, cy as y FROM stu_sectorflights  WHERE (cx BETWEEN " . ($shipx - ($this->lss_range + 1)) . " AND " . ($shipx + ($this->lss_range + 1)) . ") AND (cy BETWEEN " . ($shipy - ($this->lss_range + 1)) . " AND " . ($shipy + ($this->lss_range + 1)) . ") AND systems_id=0 AND user_id!=" . $this->uid . " AND cloak='0';");
			$tertResult = $this->db->query("SELECT type,cx as x, cy as y FROM stu_map_special WHERE (cx BETWEEN " . ($shipx - ($this->lss_range + 1)) . " AND " . ($shipx + ($this->lss_range + 1)) . ") AND (cy BETWEEN " . ($shipy - ($this->lss_range + 1)) . " AND " . ($shipy + ($this->lss_range + 1)) . ") AND systems_id=0;");
		}

		$subspacestrength = array();
		while ($secres = mysql_fetch_assoc($secResult)) {

			$subspacestrength[$secres['x']][$secres['y']] += 1;
		}

		$superspecial = array();
		while ($terres = mysql_fetch_assoc($tertResult)) {

			$superspecial[$terres['x'] - 1][$terres['y'] - 1] = "whirl-7";
			$superspecial[$terres['x']][$terres['y'] - 1] = "whirl-8";
			$superspecial[$terres['x'] + 1][$terres['y'] - 1] = "whirl-9";
			$superspecial[$terres['x'] - 1][$terres['y']] = "whirl-4";
			$superspecial[$terres['x']][$terres['y']] = "whirl-5";
			$superspecial[$terres['x'] + 1][$terres['y']] = "whirl-6";
			$superspecial[$terres['x'] - 1][$terres['y'] + 1] = "whirl-1";
			$superspecial[$terres['x']][$terres['y'] + 1] = "whirl-2";
			$superspecial[$terres['x'] + 1][$terres['y'] + 1] = "whirl-3";
		}



		while ($data = mysql_fetch_assoc($queryResult)) {
			$field = array();

			$field['x'] = $data['cx'];
			$field['y'] = $data['cy'];
			$field['type'] = $data['type'];

			$field['onclick'] = false;

			$field['class'] = "fieldnormal";
			if (($field['x'] == $shipx) && ($field['y'] == $shipy)) $field['fieldclass'] = "current";
			if (($field['x'] == $shipx) && ($field['y'] != $shipy)) {
				$field['fieldclass'] = "flyable";
				if ($field['y'] > $shipy) {
					$field['onclick'] = "doFly('d'," . ($field['y'] - $shipy) . ");";
				} else {
					$field['onclick'] = "doFly('u'," . ($shipy - $field['y']) . ");";
				}
			}
			if (($field['x'] != $shipx) && ($field['y'] == $shipy)) {
				$field['fieldclass'] = "flyable";
				if ($field['x'] > $shipx) {
					$field['onclick'] = "doFly('r'," . ($field['x'] - $shipx) . ");";
				} else {
					$field['onclick'] = "doFly('l'," . ($shipx - $field['x']) . ");";
				}
			}

			if ($superspecial[$field['x']][$field['y']]) {

				$field['display'] = "<img src=\"" . $this->gfx . "/map/subspace/" . $superspecial[$field['x']][$field['y']] . ".png\" border=0/>";
			} else {

				if ($subspacestrength[$field['x']][$field['y']] > 0) {

					$strength = 1;
					if ($subspacestrength[$field['x']][$field['y']] > 5) $strength++;
					if ($subspacestrength[$field['x']][$field['y']] > 10) $strength++;

					$direction = "";
					$direction .= ($subspacestrength[$field['x'] - 1][$field['y']] > 0 ? "1" : "0");
					$direction .= ($subspacestrength[$field['x']][$field['y'] + 1] > 0 ? "1" : "0");
					$direction .= ($subspacestrength[$field['x'] + 1][$field['y']] > 0 ? "1" : "0");
					$direction .= ($subspacestrength[$field['x']][$field['y'] - 1] > 0 ? "1" : "0");

					$field['display'] = "<img src=\"" . $this->gfx . "/map/subspace/s" . $strength . "-" . $direction . ".png\" border=0/>";
				} else {
					$field['display'] = "";
				}
			}

			array_push($res['fields'], $field);
		}
		return $res;
	}

	function getSensorDataChroniton()
	{
		$res = array();
		$res['desaturated'] = true;
		$res['fields'] = array();

		if ($this->systems_id > 0) {
			$queryResult = $this->m->getkssfieldsonly($this->sx, $this->sy, $this->kss_range, $this->systems_id);
			$shipx = $this->sx;
			$shipy = $this->sy;
			$secResult = $this->db->query("SELECT rumps_id, sx as x, sy as y FROM stu_sectorflights  WHERE (sx BETWEEN " . ($shipx - ($this->kss_range + 1)) . " AND " . ($shipx + ($this->kss_range + 1)) . ") AND (sy BETWEEN " . ($shipy - ($this->kss_range + 1)) . " AND " . ($shipy + ($this->kss_range + 1)) . ") AND systems_id=" . $this->systems_id . " AND cloak='1';");
			$tertResult = $this->db->query("SELECT type,sx as x, sy as y FROM stu_map_special WHERE (sx BETWEEN " . ($shipx - ($this->kss_range + 1)) . " AND " . ($shipx + ($this->kss_range + 1)) . ") AND (sy BETWEEN " . ($shipy - ($this->kss_range + 1)) . " AND " . ($shipy + ($this->kss_range + 1)) . ") AND systems_id=" . $this->systems_id . ";");
		} else {
			$queryResult = $this->m->getlssfieldsonly($this->cx, $this->cy, $this->lss_range);
			$shipx = $this->cx;
			$shipy = $this->cy;
			$secResult = $this->db->query("SELECT rumps_id, cx as x, cy as y FROM stu_sectorflights  WHERE (cx BETWEEN " . ($shipx - ($this->lss_range + 1)) . " AND " . ($shipx + ($this->lss_range + 1)) . ") AND (cy BETWEEN " . ($shipy - ($this->lss_range + 1)) . " AND " . ($shipy + ($this->lss_range + 1)) . ") AND systems_id=0 AND cloak='1';");
			$tertResult = $this->db->query("SELECT type,cx as x, cy as y FROM stu_map_special WHERE (cx BETWEEN " . ($shipx - ($this->lss_range + 1)) . " AND " . ($shipx + ($this->lss_range + 1)) . ") AND (cy BETWEEN " . ($shipy - ($this->lss_range + 1)) . " AND " . ($shipy + ($this->lss_range + 1)) . ") AND systems_id=0;");
		}

		$strength = array();
		while ($secres = mysql_fetch_assoc($secResult)) {
			$strength[$secres['x']][$secres['y']] = 1;
		}

		while ($data = mysql_fetch_assoc($queryResult)) {
			$field = array();

			$field['x'] = $data['cx'];
			$field['y'] = $data['cy'];
			$field['type'] = $data['type'];

			$field['onclick'] = false;

			$field['class'] = "fieldnormal";
			if (($field['x'] == $shipx) && ($field['y'] == $shipy)) $field['class'] = "fieldcurrent";
			if (($field['x'] == $shipx) && ($field['y'] != $shipy)) {
				$field['class'] = "fieldflyable";
				if ($field['y'] > $shipy) {
					$field['onclick'] = "doFly('d'," . ($field['y'] - $shipy) . ");";
				} else {
					$field['onclick'] = "doFly('u'," . ($shipy - $field['y']) . ");";
				}
			}
			if (($field['x'] != $shipx) && ($field['y'] == $shipy)) {
				$field['class'] = "fieldflyable";
				if ($field['x'] > $shipx) {
					$field['onclick'] = "doFly('r'," . ($field['x'] - $shipx) . ");";
				} else {
					$field['onclick'] = "doFly('l'," . ($shipx - $field['x']) . ");";
				}
			}


			if ($strength[$field['x']][$field['y']] > 0) {

				$direction = "";
				$direction .= ($strength[$field['x'] - 1][$field['y']] > 0 ? "1" : "0");
				$direction .= ($strength[$field['x']][$field['y'] + 1] > 0 ? "1" : "0");
				$direction .= ($strength[$field['x'] + 1][$field['y']] > 0 ? "1" : "0");
				$direction .= ($strength[$field['x']][$field['y'] - 1] > 0 ? "1" : "0");

				$field['display'] = "<img src=\"" . $this->gfx . "/map/subspace/c-" . $direction . ".png\" border=0/>";
			} else {
				$field['display'] = "";
			}


			array_push($res['fields'], $field);
		}
		return $res;
	}

	function getSensorDataBorders()
	{
		$res = array();
		$res['desaturated'] = true;
		$res['fields'] = array();

		$facs = array();


		if ($this->systems_id > 0) {
			$queryResult = $this->m->getkssfieldsonly($this->sx, $this->sy, $this->kss_range, $this->systems_id);
			$shipx = $this->sx;
			$shipy = $this->sy;
		} else {
			$queryResult = $this->m->getlssfieldsonly($this->cx, $this->cy, $this->lss_range);
			$shipx = $this->cx;
			$shipy = $this->cy;
			$secResult = $this->db->query("SELECT b.faction, a.cx as x, a.cy as y FROM stu_map as a LEFT JOIN stu_map_regions as b on a.region = b.id WHERE (a.cx BETWEEN " . ($shipx - ($this->lss_range + 1)) . " AND " . ($shipx + ($this->lss_range + 1)) . ") AND (a.cy BETWEEN " . ($shipy - ($this->lss_range + 1)) . " AND " . ($shipy + ($this->lss_range + 1)) . ");");
			while ($secres = mysql_fetch_assoc($secResult)) {
				$facs[$secres['x']][$secres['y']] = $secres['faction'];
			}
		}



		while ($data = mysql_fetch_assoc($queryResult)) {
			$field = array();

			$field['x'] = $data['cx'];
			$field['y'] = $data['cy'];
			$field['type'] = $data['type'];

			$field['onclick'] = false;

			$field['class'] = "normal";
			if (($field['x'] == $shipx) && ($field['y'] == $shipy)) $field['class'] = "current";
			if (($field['x'] == $shipx) && ($field['y'] != $shipy)) {
				$field['class'] = "flyable";
				if ($field['y'] > $shipy) {
					$field['onclick'] = "doFly('d'," . ($field['y'] - $shipy) . ");";
				} else {
					$field['onclick'] = "doFly('u'," . ($shipy - $field['y']) . ");";
				}
			}
			if (($field['x'] != $shipx) && ($field['y'] == $shipy)) {
				$field['class'] = "flyable";
				if ($field['x'] > $shipx) {
					$field['onclick'] = "doFly('r'," . ($field['x'] - $shipx) . ");";
				} else {
					$field['onclick'] = "doFly('l'," . ($shipx - $field['x']) . ");";
				}
			}


			if ($facs[$field['x']][$field['y']] > 0) {

				$field['display'] = "<img src=\"" . $this->gfx . "/starmap/faction" . $facs[$field['x']][$field['y']] . ".png\" border=0 width=30 height=30/>";
			} else {
				$field['display'] = "";
			}


			array_push($res['fields'], $field);
		}
		return $res;
	}

	function getshiplist()
	{
		if (!$this->sess['sort_mode']) $this->sess['sort_mode'] = "b.sort";
		if (!$this->sess['sort_way']) $this->sess['sort_way'] = "ASC";
		return $this->db->query("SELECT a.*,b.name as cname,b.reaktor as creaktor,b.trumfield,b.w1_lvl,b.w2_lvl,c.name as sysname,c.type FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_systems as c ON a.systems_id=c.systems_id LEFT JOIN stu_ships_buildplans as d ON a.plans_id=d.plans_id WHERE a.user_id=" . $this->uid . " ORDER BY a.fleets_id DESC," . $this->sess['sort_mode'] . " " . $this->sess['sort_way'] . ",a.id LIMIT 200");
	}


	function getashiplist()
	{
		// if (!$this->sess['sort_mode']) $this->sess['sort_mode'] = "b.sort";
		// if (!$this->sess['sort_way']) $this->sess['sort_way'] = "ASC";
		// return $this->db->query("SELECT a.*,b.name as cname,b.reaktor as creaktor,b.trumfield,c.name as sysname,c.type,d.reaktor as rreaktor,d.maintaintime,s.name as sname,s.systems_id as ssid FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_systems as c ON a.systems_id=c.systems_id LEFT JOIN stu_ships_buildplans as d ON a.plans_id=d.plans_id LEFT JOIN stu_stations as s on a.assigned = s.id WHERE a.user_id=".$this->uid." AND a.assigned != 0 ORDER BY a.fleets_id DESC,b.slots DESC,".$this->sess['sort_mode']." ".$this->sess['sort_way'].",a.id LIMIT 200");
		return;
	}

	function changesorting($mode, $way)
	{
		if ($mode == "ru") $mo = "b.sort ASC,a.rumps_id";
		if ($mode == "hu") $mo = "a.huelle";
		if ($mode == "sh") $mo = "a.schilde";
		if ($mode == "ep") $mo = "a.eps";
		if ($mode == "cr") $mo = "a.crew";
		if (!$mo) return;
		if ($way == "up") $wa = "DESC";
		if ($way == "do") $wa = "ASC";
		if (!$wa) return;
		$this->db->query("UPDATE stu_user SET sort_mode='" . $mo . "',sort_way='" . $wa . "' WHERE id=" . $this->uid . " LIMIT 1");
		global $_SESSION;
		$_SESSION['sort_mode'] = $mo;
		$_SESSION['sort_way'] = $wa;
		return "Sortierung ge�ndert";
	}

	function canMap()
	{
		$s1 = $this->db->query("SELECT x.modules_id FROM stu_ships as s LEFT JOIN stu_ships_buildplans as p ON s.plans_id = p.plans_id LEFT JOIN stu_modules_special as x ON x.modules_id = p.s1 WHERE s.id=" . $this->id . " AND x.type = 'map' LIMIT 1;", 1);
		$s2 = $this->db->query("SELECT x.modules_id FROM stu_ships as s LEFT JOIN stu_ships_buildplans as p ON s.plans_id = p.plans_id LEFT JOIN stu_modules_special as x ON x.modules_id = p.s2 WHERE s.id=" . $this->id . " AND x.type = 'map' LIMIT 1;", 1);
		if ($s1 > 0 || $s2 > 0) return true;
		return false;
	}

	function getCollector()
	{
		$s1 = $this->db->query("SELECT x.type FROM stu_ships as s LEFT JOIN stu_ships_buildplans as p ON s.plans_id = p.plans_id LEFT JOIN stu_modules_special as x ON x.modules_id = p.s1 WHERE s.id=" . $this->id . " LIMIT 1", 1);
		$s2 = $this->db->query("SELECT x.type FROM stu_ships as s LEFT JOIN stu_ships_buildplans as p ON s.plans_id = p.plans_id LEFT JOIN stu_modules_special as x ON x.modules_id = p.s2 WHERE s.id=" . $this->id . " LIMIT 1", 1);
		if ($s1 === "col_deut" || $s2 === "col_deut") return "deut";
		if ($s1 === "col_ore" || $s2 === "col_ore") return "ore";
		return "none";
	}

	function getCollectionInfo()
	{

		$res["curr"] = 0;
		$res["max"] = max($this->map[deut], $this->map[erz]);
		$res['deut'] = $this->map[deut];
		$res['ore'] = $this->map[erz];

		if ($this->systems_id > 0) {
			$res["curr"] = $this->db->query("SELECT COUNT(id) FROM stu_ships WHERE assigned > 0 AND systems_id = " . $this->systems_id . " AND sx = " . $this->sx . " AND sy = " . $this->sy, 1);
		} else {
			$res["curr"] = $this->db->query("SELECT COUNT(id) FROM stu_ships WHERE assigned > 0 AND systems_id = 0 AND cx = " . $this->cx . " AND cy = " . $this->cy, 1);
		}

		return $res;
	}

	function startCollect()
	{
		$ci = $this->getCollectionInfo();
		$cl = $this->getCollector();
		// print_r($ci);
		if ($ci['curr'] >= $ci['max']) return "Alle Slots zum Sammeln in diesem Feld sind bereits belegt.";

		if ($cl == 'deut' && $ci['deut'] > 0) {
			$this->db->query("UPDATE stu_ships set assigned = 1 WHERE id = " . $this->id . " LIMIT 1");
			return "Schiff beginnt, Deuterium zu sammeln.";
		}
		if ($cl == 'ore' && $ci['ore'] > 0) {
			$this->db->query("UPDATE stu_ships set assigned = 1 WHERE id = " . $this->id . " LIMIT 1");
			return "Schiff beginnt, Erz zu sammeln.";
		}
		return "Sammeln konnte nicht begonnen werden.";
	}

	function stopCollect()
	{
		$this->db->query("UPDATE stu_ships set assigned = 0 WHERE id = " . $this->id . " LIMIT 1");
		return "Sammeln gestoppt.";
	}










	function checkmove($ship, $targetfield, $isactive = true)
	{
		global $mapfields, $fleet;

		$shipdata = $this->db->query("SELECT eps,warp,warpfields,traktorlimit,traktormode,crew,min_crew FROM stu_ships WHERE id = " . $ship . " LIMIT 1;", 4);

		if (!$targetfield['is_passable'])
			return array('result' => false, 'reason' => "inpassable");

		if (!$isactive && $targetfield['iswarp'] && $shipdata['traktorlimit'] == 0) {
			return array('result' => false, 'reason' => "tractorlimit");
		}

		if ($isactive) {
			if ($shipdata['traktormode'] == 2)
				return array('result' => false, 'reason' => "tractor");
			if ($shipdata['crew'] <  $shipdata['min_crew'] - 2)
				return array('result' => false, 'reason' => "crew");


			if (!$targetfield['iswarp']) {
				$sr = $this->db->query("SELECT sr FROM stu_systems WHERE systems_id = " . $targetfield['sys'] . " LIMIT 1;", 1);

				if ($targetfield['sx'] < 1 || $targetfield['sy'] < 1 || $targetfield['sx'] > $sr || $targetfield['sy'] > $sr)
					return array('result' => false, 'reason' => "border");

				if ($shipdata['eps'] < 1)
					return array('result' => false, 'reason' => "energy");

				if ($shipdata['eps'] < 2 && $shipdata['traktormode'] == 1)
					return array('result' => false, 'reason' => "tractorenergy");
			} else {
				if ($targetfield['cx'] < 1 || $targetfield['cy'] < 1 || $targetfield['cx'] > $mapfields['max_x'] || $targetfield['cy'] > $mapfields['max_y'])
					return array('result' => false, 'reason' => "border");

				if ($shipdata['warp'] == 0)
					return array('result' => false, 'reason' => "warp");

				if ($shipdata['warpfields'] < 1)
					return array('result' => false, 'reason' => "warpfields");

				if ($shipdata['warpfields'] < 2 && $shipdata['traktormode'] == 1)
					return array('result' => false, 'reason' => "tractorwarp");
			}
		}

		return array('result' => true);
	}


	function paymove($ship, $targetfield, $isactive)
	{
		global $mapfields, $fleet;

		$msg = "";
		$interrupt = false;
		$shipdata = $this->db->query("SELECT eps,warpfields,huelle,max_huelle,name,fleets_id,traktormode,assigned FROM stu_ships WHERE id = " . $ship . " LIMIT 1;", 4);


		// sammeln abbrechen
		if ($shipdata['assigned'] != 0) $msg .= "<b>Sammeln abgebrochen.</b><br>";
		$shipdata['assigned'] = 0;

		// Werte updaten
		// $this->db->query("UPDATE stu_ships SET ");
		if ($isactive) {
			if ($targetfield['iswarp']) {
				$shipdata['warpfields']--;
				if ($shipdata['traktormode'] == 1) $shipdata['warpfields']--;
			} else {
				$shipdata['eps']--;
				if ($shipdata['traktormode'] == 1) $shipdata['eps']--;
			}
		}

		if ($targetfield['ecost'] > 0) $shipdata['eps'] -= $targetfield['ecost'];

		// Deflektorversagen
		if ($shipdata['eps'] < $targetfield['ecost']) {
			$damage = ceil($shipdata['max_huelle'] * 0.05);

			$shipdata['huelle'] = $shipdata['huelle'] - $damage;
			$shipdata['eps'] = 0;

			$msg .= "<b>Deflektorausfall!</b> Die " . $shipdata['name'] . " (" . $ship . ") nimmt " . formatDmg("environment", $damage, 0, 0, 1) . "";

			if ($shipdata['huelle'] <= 0) {
				$shiparr['id'] = $ship;
				$reasonarr['reason'] = "environment";
				$this->destroyShip($shiparr, $reasonarr);
				$msg .= " - H�llenbruch! Das Schiff wurde zerst�rt!<br>";
				return array("interrupt" => true, "msg" => $msg);
			} else {
				$msg .= " - H�lle bei " . $shipdata['huelle'] . ".<br>";
			}
		}

		// Einflugschaden
		if ($targetfield['damage'] > 0) {
			$interrupt = true;

			$damage = ceil($shipdata['max_huelle'] * ($targetfield['damage'] / 100));

			$shipdata['huelle'] = $shipdata['huelle'] - $damage;

			$msg .= "<b>Einflugschaden!</b> Die " . $shipdata['name'] . " (" . $ship . ") nimmt " . formatDmg("environment", $damage, 0, 0, 1) . ".";
			if ($shipdata['huelle'] <= 0) {
				$shiparr['id'] = $ship;
				$reasonarr['reason'] = "environment";
				$this->destroyShip($shiparr, $reasonarr);
				$msg .= " - H�llenbruch! Das Schiff wurde zerst�rt!<br>";
				return array("interrupt" => true, "msg" => $msg);
			} else {
				$msg .= " - H�lle bei " . $shipdata['huelle'] . ".<br>";
			}
		}

		$this->db->query("UPDATE stu_ships SET warpfields = " . $shipdata['warpfields'] . ",eps = " . $shipdata['eps'] . ",huelle=" . $shipdata['huelle'] . ",assigned = " . $shipdata['assigned'] . " WHERE id = " . $ship . " LIMIT 1;");


		return array("interrupt" => $interrupt, "msg" => $msg);
	}


	function phrasereason($reason, $isfleet)
	{
		if ($isfleet) {
			switch ($reason) {
				case "energy":
					return "Ein oder mehrere Schiffe haben nicht genug Energie f�r den Flug.";
				case "energy":
					return "Ein oder mehrere Schiffe haben nicht genug Energie f�r den Flug.";
				case "crew":
					return "Ein oder mehrere Schiffe haben nicht genug Crew f�r den Flug.";
				case "warp":
					return "Ein oder mehrere Schiffe haben einen deaktivierten Warpantrieb.";
				case "warpfields":
					return "Ein oder mehrere Schiffe haben nicht genug Warpantrieb f�r den Flug.";
				case "tractor":
					return "Schiff wird von einem Traktorstrahl gehalten.";
				case "tractorenergy":
					return "Nicht genug Energie, um mit Traktorstrahl ein Schiff zu ziehen.";
				case "tractorwarp":
					return "Nicht genug Warpantrieb, um mit Traktorstrahl ein Schiff zu ziehen.";
				case "inpassable":
					return "Ziel-Feld kann nicht durchflogen werden.";
				case "border":
					return "Feld liegt au�erhalb des g�ltigen Kartenbereichs.";
				case "tractorlimit":
					return "Weiteres Schleppen im Warp w�rde Zielschiff besch�digen.";
				default:
					return "Unbekannter Grund";
			}
		} else {
			switch ($reason) {
				case "energy":
					return "Nicht genug Energie f�r den Flug vorhanden.";
				case "crew":
					return "Nicht genug Crew f�r den Flug vorhanden.";
				case "warp":
					return "Warpantrieb ist deaktiviert.";
				case "warpfields":
					return "Nicht genug Warpantrieb f�r den Flug vorhanden.";
				case "tractor":
					return "Schiff wird von einem Traktorstrahl gehalten.";
				case "tractorenergy":
					return "Nicht genug Energie, um mit Traktorstrahl ein Schiff zu ziehen.";
				case "tractorwarp":
					return "Nicht genug Warpantrieb, um mit Traktorstrahl ein Schiff zu ziehen.";
				case "inpassable":
					return "Ziel-Feld kann nicht durchflogen werden.";
				case "border":
					return "Feld liegt au�erhalb des g�ltigen Kartenbereichs.";
				case "tractorlimit":
					return "Weiteres Schleppen im Warp w�rde Zielschiff besch�digen.";
				default:
					return "Unbekannter Grund";
			}
		}
	}


	function newmove($count, $direction, $isactive = 0, $istractor = 0)
	{
		global $mapfields, $fleet;
		if (!check_int($count)) return;

		$msg = "";
		$interrupt = false;

		$ships = array();
		$ships[] = $this->id;
		if ($this->fleets_id > 0) {
			$res = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id = " . $this->fleets_id . " AND id != " . $this->id . " ORDER BY id ASC;");
			while ($s = mysql_fetch_assoc($res)) {
				$ships[] = $s['id'];
			}
		}
		if ($this->debug) $msg .= "<br>Ships: " . print_r($ships, true);

		if ($this->fleets_id > 0) {
			$pc = $this->getAllPoints("pcrew");
			$pv = $this->getAllPoints("psupply");
			$pw = $this->getAllPoints("pmaintain");

			$cf = $this->getCurrentFleetPoints();
			$cc = $this->getCurrentCivilianCount();

			$stepFleetPoints = stepFleetLimit($pc, $pv, $pw);

			if ($stepFleetPoints['battleships'] < $cf) return "�berlastung des Flottenkommandos verhindert Koordination der Flottenbewegungen!";
		}

		$targetfield['iswarp']  = ($this->systems_id == 0);
		$targetfield['cx'] 		= $this->cx;
		$targetfield['cy'] 		= $this->cy;
		$targetfield['sx'] 		= $this->sx;
		$targetfield['sy'] 		= $this->sy;
		$targetfield['sr'] 		= $this->sys['sr'];
		$targetfield['sys'] 	= $this->systems_id;
		if ($direction == 'l') $targetfield[$targetfield['iswarp'] ? 'cx' : 'sx']--;
		if ($direction == 'r') $targetfield[$targetfield['iswarp'] ? 'cx' : 'sx']++;
		if ($direction == 'u') $targetfield[$targetfield['iswarp'] ? 'cy' : 'sy']--;
		if ($direction == 'd') $targetfield[$targetfield['iswarp'] ? 'cy' : 'sy']++;

		if ($direction == 'l') $targetfield['dirnum'] = 1;
		if ($direction == 'r') $targetfield['dirnum'] = 2;
		if ($direction == 'u') $targetfield['dirnum'] = 3;
		if ($direction == 'd') $targetfield['dirnum'] = 4;
		if ($targetfield['iswarp']) $targetfield['type'] = $this->db->query("SELECT type FROM stu_map WHERE cx = " . $targetfield['cx'] . " AND cy = " . $targetfield['cy'] . " LIMIT 1;", 1);
		else						$targetfield['type'] = $this->db->query("SELECT type FROM stu_sys_map WHERE systems_id = " . $targetfield['sys'] . " AND sx = " . $targetfield['sx'] . " AND sy = " . $targetfield['sy'] . " LIMIT 1;", 1);


		$data = $this->db->query("SELECT ecost,damage,x_damage,is_passable FROM stu_map_ftypes WHERE type = " . $targetfield['type'] . " LIMIT 1;", 4);
		$targetfield['ecost'] = $data['ecost'];
		$targetfield['damage'] = $data['damage'];
		$targetfield['x_damage'] = $data['x_damage'];
		$targetfield['is_passable'] = $data['is_passable'];

		if ($this->debug) $msg .= "<br>Targetfield: " . print_r($targetfield, true);

		// CHECKMOVE
		foreach ($ships as $ship) {
			$check = $this->checkmove($ship, $targetfield, true);
			if ($this->debug) $msg .= "<br>Checkmove (" . $ship . "): " . print_r($check, true);

			if (!$check['result']) return $msg .= "" . $this->phrasereason($check['reason'], count($ships) > 1);
		}
		if ($this->traktormode == "1") {
			$check = $this->checkmove($this->traktor, $targetfield, false);
			if ($this->debug) $msg .= "<br>Checkmove (" . $this->traktor . "): " . print_r($check, true);

			if (!$check['result']) return $msg .= "" . $this->phrasereason($check['reason'], count($ships) > 1);
		}

		// UPDATE COORDS
		foreach ($ships as $ship) {
			$this->db->query("UPDATE stu_ships SET cx=" . $targetfield['cx'] . ",cy=" . $targetfield['cy'] . ",sx=" . $targetfield['sx'] . ",sy=" . $targetfield['sy'] . ",cfield=" . $targetfield['type'] . ",direction=" . $targetfield['dirnum'] . " WHERE id=" . $ship . " LIMIT 1;");
			$this->cx = $targetfield['cx'];
			$this->cy = $targetfield['cy'];
			$this->sx = $targetfield['sx'];
			$this->sy = $targetfield['sy'];

			$rumpsid = $this->db->query("SELECT rumps_id FROM stu_ships WHERE id = " . $ship . " LIMIT 1;", 1);
			$cloaked = $this->db->query("SELECT cloak FROM stu_ships WHERE id = " . $ship . " LIMIT 1;", 1) == 1 ? "1" : "0";

			$this->db->query("REPLACE INTO `stu_sectorflights` (`user_id`, `ships_id`, `rumps_id`, `date`, `cx`, `cy`, `sx`, `sy`, `systems_id`, `notified`, `cloak`) VALUES ('" . $this->uid . "', '" . $ship . "', '" . $rumpsid . "', NOW(), '" . $targetfield['cx'] . "', '" . $targetfield['cy'] . "', '" . $targetfield['sx'] . "', '" . $targetfield['sy'] . "', '" . $targetfield['sys'] . "', '0', '" . $cloaked . "');");
			$this->db->query("DELETE FROM stu_ships_decloaked WHERE ships_id = " . $ship . ";", 1);
		}
		if ($this->traktormode == "1") {
			if ($targetfield['iswarp']) $this->db->query("UPDATE stu_ships SET traktorlimit=traktorlimit-1,cx=" . $targetfield['cx'] . ",cy=" . $targetfield['cy'] . ",sx=" . $targetfield['sx'] . ",sy=" . $targetfield['sy'] . ",cfield=" . $targetfield['type'] . ",direction=" . $targetfield['dirnum'] . " WHERE id=" . $this->traktor . " LIMIT 1;");
			else						$this->db->query("UPDATE stu_ships SET cx=" . $targetfield['cx'] . ",cy=" . $targetfield['cy'] . ",sx=" . $targetfield['sx'] . ",sy=" . $targetfield['sy'] . ",cfield=" . $targetfield['type'] . ",direction=" . $targetfield['dirnum'] . " WHERE id=" . $this->traktor . " LIMIT 1;");
		}
		// RESET FACTIONTIMER
		$this->db->query("UPDATE stu_fleets SET faction_change_time = 0 WHERE fleets_id = " . $this->fleets_id . " LIMIT 1;");

		// SECTORFLIGHTS
		// TODO
		// if ($targetfield['iswarp'])
		// {

		// if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,cx,cy,cloak,fieldcount) VALUES ('".$this->uid."','".$this->id."','".$this->rumps_id."','".$this->sess['allys_id']."',NOW(),'".$nx."','".$ny."','".$this->cloak."','".$i."')");
		// } else {
		// $res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=".$this->sess['allys_id'].",cloak='0',notified='0',fieldcount=".$i." WHERE sx=".$nx." AND sy=".$ny." AND ships_id=".$this->id." AND systems_id=".$this->systems_id." LIMIT 1",6);
		// if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,sx,sy,systems_id,cloak,fieldcount) VALUES ('".$this->uid."','".$this->id."','".$this->rumps_id."','".$this->sess['allys_id']."',NOW(),'".$nx."','".$ny."','".$this->systems_id."','".$this->cloak."','".$i."')");			
		// }		

		// PAYMOVE
		foreach ($ships as $ship) {
			$pay = $this->paymove($ship, $targetfield, true);
			if ($pay['interrupt']) $interrupt = true;
			$msg .= $pay['msg'];
		}
		if ($this->traktormode == "1") {
			$pay = $this->paymove($this->traktor, $targetfield, false);
			$msg .= $pay['msg'];
		}


		// if (!$check['result']) return $msg."<br>".$check['reason'];

		if ($this->debug) $msg .= "<br>Interrupt: " . $interrupt;
		if ($this->debug) $msg .= "<br>Count: " . $count;

		if (!$interrupt && $count > 1) {
			$msg .= $this->newmove($count - 1, $direction, $isactive, $istractor);
		} else {
			if ($this->debug) $msg .= "<br>";
			if ($targetfield['iswarp']) $msg .= "Die " . (count($ships) > 1 ? "Flotte" : $this->name . " (" . $this->id . ")") . " fliegt in Sektor " . $targetfield['cx'] . "|" . $targetfield['cy'] . " ein.";
			else						$msg .= "Die " . (count($ships) > 1 ? "Flotte" : $this->name . " (" . $this->id . ")") . " fliegt in Sektor " . $targetfield['sx'] . "|" . $targetfield['sy'] . " ein.";

			if ($this->traktormode == "1") {
				$msg .= " Ein Schiff wird im Traktorstrahl mitgezogen.";
			}
		}


		return $msg;
	}






	function move($id, $fields, $l = 0, $r = 0, $u = 0, $d = 0)
	{
		global $mapfields, $fleet;
		if ($this->slots > 0) return;
		if ($fleet->fm == 1) {

			$fleet->nfs = $id;
			if (!$fleet->shipdata[$id]) $this->ship();
			else {
				unset($fleet->shipdata[$id]['fmmsg']);
				unset($fleet->shipdata[$id]['facme']);
				unset($fleet->shipdata[$id]['facml']);
				foreach ($fleet->shipdata[$id] as $key => $value) $this->$key = $value;
			}
		}
		//if ($this->uid == 102) {
		$result = $this->db->query("SELECT a.id FROM stu_ships as a LEFT JOIN stu_user as q ON a.user_id = q.id LEFT JOIN stu_contactlist as b ON a.user_id=b.user_id AND b.recipient=" . $this->uid . " LEFT JOIN stu_ally_relationship as c ON ((q.allys_id=c.allys_id1 AND c.allys_id2=" . $this->sess['allys_id'] . ") OR (q.allys_id=c.allys_id2 AND c.allys_id1=" . $this->sess['allys_id'] . ")) WHERE a.user_id!=" . $this->uid . " AND (a.alvl='3' OR (a.alvl='2' AND (b.mode='3' OR c.type='1'))) AND (ISNULL(c.type) OR c.type='1' OR c.type='2') AND ((q.allys_id>0 AND q.allys_id!=" . $this->sess['allys_id'] . ") OR q.allys_id=0) AND (ISNULL(b.mode) OR b.mode='2' OR b.mode='3') AND " . ($this->systems_id > 0 ? "a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " AND a.systems_id=" . $this->systems_id : "a.systems_id=0 AND a.cx=" . $this->cx . " AND a.cy=" . $this->cy . "") . "");
		if (mysql_num_rows($result) != 0) {
			$warpblocked = 0;
			if (($l != 0) && ($this->direction != 2) && $this->direction != 0) $warpblocked = 1;
			if (($r != 0) && ($this->direction != 1) && $this->direction != 0) $warpblocked = 1;
			if (($u != 0) && ($this->direction != 4) && $this->direction != 0) $warpblocked = 1;
			if (($d != 0) && ($this->direction != 3) && $this->direction != 0) $warpblocked = 1;
			if ($warpblocked != 0) return "Feindliche Schiffe verhindern ein Vorankommen";
		}
		//}
		if ($this->systems_id == 0 && $this->warp == 0) {
			$this->warpon();
			if ($this->warp == 0) {
				if ($this->fleets_id > 0 && $fleet->fm == 1 && $this->id != $fleet->ff) {
					$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $this->id . " LIMIT 1");
					return "In Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy : $this->cx . "|" . $this->cy) . " zur�ckgeblieben";
				}
				$this->fleet_stop = 1;
				return "Zum fliegen muss sich das Schiff im Warp befinden";
			}
		}
		$flcost = 1;

		if ($this->dock > 0) {
			if ($this->fleets_id > 0 && $fleet->fm == 1 && $this->id != $fleet->ff) {
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $this->id . " LIMIT 1");
				return "In Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy : $this->cx . "|" . $this->cy) . " zur�ckgeblieben";
			}
			return "Das Schiff ist angedockt";
		}
		if ($this->crew < $this->min_crew) {
			if ($this->fleets_id > 0 && $fleet->fm == 1 && $this->id != $fleet->ff) {
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $this->id . " LIMIT 1");
				return "In Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy : $this->cx . "|" . $this->cy) . " zur�ckgeblieben";
			}
			$this->fleet_stop = 1;
			return "F�r den Flug wird die Minimalbesatzung (" . $this->min_crew . ") ben�tigt - Vorhanden sind nur " . $this->crew;
		}
		if (($this->checksubsystem(7, $id) == 1 && $this->systems_id > 0) || ($this->systems_id == 0 && $this->checksubsystem(11, $id) == 1)) {
			if ($this->fleets_id > 0 && $fleet->fm == 1 && $this->id != $fleet->ff) {
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $this->id . " LIMIT 1");
				return "In Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy : $this->cx . "|" . $this->cy) . " zur�ckgeblieben";
			}
			$this->fleet_stop = 1;
			return "Die Reparatur am Antrieb wurde noch nicht abgeschlossen";
		}
		// if ($this->plans_id != 1 && $this->uid > 100 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance,"maintaintime" => $this->maintaintime)) > rand(1,150))
		// {
		// $df = 1;
		// if ($this->warp == 1) $ret = $this->damage_subsystem("foo",$this->id,11);
		// else $ret = $this->damage_subsystem("foo",$this->id,7);
		// if ($this->fleets_id > 0 && $fleet->fm == 1 && $this->id != $fleet->ff)
		// {
		// $this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=".$this->id." LIMIT 1");
		// return "In Sektor ".($this->systems_id > 0 ? $this->sx."|".$this->sy : $this->cx."|".$this->cy)." zur�ckgeblieben";
		// }
		// $this->fleet_stop = 1;
		// return $ret;
		// }
		if ($this->traktormode == 2) {
			if ($this->fleets_id > 0 && $fleet->fm == 1 && $this->id != $fleet->ff) {
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $this->id . " LIMIT 1");
				return "In Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy : $this->cx . "|" . $this->cy) . " zur�ckgeblieben";
			}
			$this->fleet_stop = 1;
			return "Das Schiff wird von einem Traktorstrahl gehalten";
		}
		if ((($this->warp == 0) && ($this->eps < $flcost)) || $this->crew < $this->min_crew || $df == 1 || $this->maintain > 0 || (($this->warp == 1) && ($this->warpfields == 0))) {
			if ($this->fleets_id > 0 && $fleet->fm == 1 && $this->id != $fleet->ff) {
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $this->id . " LIMIT 1");
				return "In Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy : $this->cx . "|" . $this->cy) . " zur�ckgeblieben";
			}
			if ($df == 1) return $ret;
			if ($this->eps < $flcost) return "Nicht genug Energie f�r den Flug vorhanden";
			if (($this->warp == 1) && ($this->warpfields == 0)) return "Nicht genug Warpantrieb f�r den Flug vorhanden";
			if ($this->maintain > 0) return "Das Schiff wird zur Zeit gewartet";
		}
		$i = 1;
		$this->systems_id > 0 ? $nx = $this->sx : $nx = $this->cx;
		$this->systems_id > 0 ? $ny = $this->sy : $ny = $this->cy;
		if ($this->fleets_id > 0 && !$fleet->ff) {
			$fdat = $this->db->query("SELECT ships_id,name FROM stu_fleets WHERE fleets_id=" . $this->fleets_id . " LIMIT 1", 4);
			if ($this->id == $fdat['ships_id']) $fleet->move($this->fleets_id, $this->id);
		}
		if ($this->systems_id > 0 && !$this->sys) $this->sys = $this->m->getsystembyid($this->systems_id);
		if ($fleet->fm != 1 || $fleet->fleet_start == 1) {
			if ($l != 0) $dr = 1;
			if ($r != 0) $dr = 2;
			if ($u != 0) $dr = 3;
			if ($d != 0) $dr = 4;
			while ($i <= $fields) {
				if ($l != 0) {
					$cx = $nx - 1;
					$cy = $ny;
				}
				if ($r != 0) {
					$cx = $nx + 1;
					$cy = $ny;
				}
				if ($u != 0) {
					$cx = $nx;
					$cy = $ny - 1;
				}
				if ($d != 0) {
					$cx = $nx;
					$cy = $ny + 1;
				}
				if ($cx < 1 || $cy < 1 || $cx > ($this->systems_id > 0 ? $this->sys['sr'] : $mapfields['max_x']) || $cy > ($this->systems_id > 0 ? $this->sys['sr'] : $mapfields['max_y'])) {
					if ($i == 1) return "Einflug in diesen Sektor nicht m�glich";
					else break;
				}
				if (!$fleet->moved_ships[$this->id]) {
					$this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=" . $this->id . " LIMIT 1");
					$fleet->moved_ships[$this->id] = 1;
				}
				if ($this->fleets_id > 0 && $fleet->fm == 0) {
					if ($this->id != $fdat['ships_id']) {
						$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $this->id . " LIMIT 1");
						$flm = "Die " . stripslashes($this->name) . " hat die Flotte " . stripslashes($fdat['name']) . " verlassen<br>";
						$this->fleet_id = 0;
					}
				}
				$nx = $cx;
				$ny = $cy;
				if (!is_array($fleet->field[$nx][$ny])) {
					if ($this->systems_id == 0) $fleet->field[$nx][$ny] = $this->db->query("SELECT a.faction_id,a.is_border,b.type,b.name,b.ecost,b.damage,b.sensoroff,b.cloakoff,b.shieldoff,c.name as fname,c.flight_infix FROM stu_map as a LEFT JOIN stu_map_ftypes as b USING(type) LEFT JOIN stu_factions as c USING(faction_id) WHERE a.cx=" . $nx . " AND a.cy=" . $ny . " LIMIT 1", 4);
					else {
						$fleet->field[$nx][$ny] = $this->db->query("SELECT b.type,b.name,b.ecost,b.damage,b.x_damage,b.sensoroff,b.cloakoff,b.shieldoff FROM stu_sys_map as a LEFT JOIN stu_map_ftypes as b USING(type) WHERE a.systems_id=" . $this->systems_id . " AND a.sx=" . $nx . " AND a.sy=" . $ny . " LIMIT 1", 4);
						if (!$this->m->sysname) $this->m->sysname = stripslashes($this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $this->systems_id . " LIMIT 1", 1));
					}
					$fleet->field[$nx][$ny][ecost] += $flcost;
				}
				if ($this->traktormode == 1) {
					if ($this->eps - 1 < 2) {
						$trm = $this->deactivatetraktor() . "<br />";
						$this->traktormode = 0;
					} else {
						$this->eps -= 2;
						$this->systems_id > 0 ? $this->db->query("UPDATE stu_ships SET sx=" . $nx . ",sy=" . $ny . ",cfield=" . $fleet->field[$nx][$ny]['type'] . ",fleets_id=0,still=0 WHERE id=" . $this->traktor . " LIMIT 1") : $this->db->query("UPDATE stu_ships SET cx=" . $nx . ",cy=" . $ny . ",cfield=" . $fleet->field[$nx][$ny]['type'] . ",fleets_id=0 WHERE id=" . $this->traktor . " LIMIT 1");
					}
				}
				if ($i > 1 && $fleet->fm != 1) $this->systems_id > 0 ? $this->map = $this->m->getfieldbyid_kss($this->sx, $this->sy, $this->systems_id) : $this->map = $this->m->getfieldbyid_lss($this->cx, $this->cy);
				if ($fleet->field[$nx][$ny]['sensoroff'] != 1 && $fleet->field[$nx][$ny]['type'] != 8) {
					if ($this->systems_id > 0) {
						$res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=" . $this->sess['allys_id'] . ",cloak='" . $this->cloak . "',notified='0',fieldcount=" . $i . " WHERE sx=" . $nx . " AND sy=" . $ny . " AND ships_id=" . $this->id . " AND systems_id=" . $this->systems_id . " LIMIT 1", 6);
						if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,sx,sy,systems_id,cloak,fieldcount) VALUES ('" . $this->uid . "','" . $this->id . "','" . $this->rumps_id . "','" . $this->sess['allys_id'] . "',NOW(),'" . $nx . "','" . $ny . "','" . $this->systems_id . "','" . $this->cloak . "','" . $i . "')");
					} else {
						$res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=" . $this->sess['allys_id'] . ",cloak='" . $this->cloak . "',fieldcount=" . $i . " WHERE cx=" . $nx . " AND cy=" . $ny . " AND ships_id=" . $this->id . " LIMIT 1", 6);
						if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,cx,cy,cloak,fieldcount) VALUES ('" . $this->uid . "','" . $this->id . "','" . $this->rumps_id . "','" . $this->sess['allys_id'] . "',NOW(),'" . $nx . "','" . $ny . "','" . $this->cloak . "','" . $i . "')");
					}
				}
				if (!$facme && !$this->facme && $fleet->field[$nx][$ny]['faction_id'] > 0 && $fleet->field[$nx][$ny]['is_border'] == 1 && $this->map['faction_id'] != $fleet->field[$nx][$ny]['faction_id'] && ($fleet->fm != 1 || ($fleet->fm == 1 && $ffacm != 1))) {
					$facme = "Sie haben soeben die Grenze " . $fleet->field[$nx][$ny]['flight_infix'] . " passiert<br>";
					if ($fleet->fm == 1) $this->facme = $facme;
				}
				if ($fleet->field[$nx][$ny]['sensoroff'] == 1 && ($this->lss == 1)) {
					$as[] = "Langstreckensensoren";
					$this->lss = 0;
					if ($this->traktormode == 1) $tma = $this->move_traktor_events(0, 0, $nx, $ny, 3);
				}
				if ($fleet->field[$nx][$ny]['cloakoff'] && $this->cloak == 1) {
					$as[] = "Tarnung";
					$this->cloak = 0;
					$fm .= "<br>- <font color=#FF0000>Tarnung in Sektor " . $nx . "|" . $ny . " ausgefallen</font>";
				}
				if ($fleet->field[$nx][$ny]['shieldoff'] == 1 && $this->schilde_status == 1) {
					$as[] = "Schilde";
					$this->schilde_status = 0;
				}
				if (!check_int($fleet->map_special[$nx][$ny])) $fleet->map_special[$nx][$ny] = $this->db->query("SELECT type FROM stu_map_special WHERE " . ($this->systems_id > 0 ? "sx=" . $nx . " AND sy=" . $ny . " AND systems_id=" . $this->systems_id : "cx=" . $nx . " AND cy=" . $ny) . " LIMIT 1", 1);

				if ($fleet->map_special[$nx][$ny] == 5) {
					// Captain, Captain...ein Ionensturm! - Jojo, is scho Recht Chekov...du Depp
					if ($this->m4 != 4901) {
						$dmg = rand(5, 15);
						$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $nx . "|" . $ny . ($this->systems_id > 0 ? " (" . $this->m->sysname . "-System)" : "") . " durch einen Ionensturm besch�digt. Schaden: " . $dmg, "type" => 2);
						$fm .= "<br>- <font color=#FF0000>" . $dmg . " Schaden ausgel�st durch einen Ionensturm in Sektor " . $nx . "|" . $ny . "</font>";
						$arr = damageship($dmg, $this->huelle, $this->schilde, $this->schilde_status);
						$fm .= $arr['msg'];
						$this->huelle = $arr['huelle'];
						$this->schilde = $arr['schilde'];
						$this->schilde_status = $arr['schilde_status'];
						if ($this->huelle <= 0) {
							$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $nx . "|" . $ny . ($this->systems_id > 0 ? " (" . $this->m->sysname . "-System)" : "") . " beim Sektoreinflug durch einen Ionensturm zerst�rt", "type" => 2);
							$fm .= "<br>-- H�llenbruch! Die " . stripslashes($this->name) . " wurde zerst�rt";
							$this->trumfield(array("user_id" => $this->uid, "name" => $this->name, "max_huelle" => $this->max_huelle, "id" => $this->id, "cx" => $this->cx, "cy" => $this->cy, "systems_id" => $this->systems_id, "sx" => $this->sx, "sy" => $this->sy, "rumps_id" => $this->rumps_id, "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $this->cname), "Anomalie (Ionensturm)");
							$this->destroyed = 1;
							$this->dsships[$this->id] = 1;
							$fleet->dsships[$this->id] = 1;
							$this->huelle = ceil(($this->max_huelle / 100) * 15);
							$this->stopmove = 1;
						}
						if ($this->traktormode == 1) {
							$as[] = "Traktorstrahl";
							$tma = $this->move_traktor_events("Ionensturm", rand(5, 15), $nx, $ny);
						}
					} else $fm .= "<br>- <font color=\"#FF0000\">Warnung! Es wurde ein Ionensturm in Sektor " . $nx . "|" . $ny . " festgestellt</font>";
				}
				if ($this->traktormode == 1) $tm = "<br>1 Schiff im Traktorstrahl mitgezogen";

				// deativate collection
				$this->db->query("UPDATE stu_ships set assigned=0 WHERE id = " . $this->id . " LIMIT 1;");

				if ($this->eps - $fleet->field[$nx][$ny]['ecost'] < 0 && $fleet->field[$nx][$ny]['damage'] > 0) {
					$fm .= "<br>- <font color=#FF0000>Deflektoren wegen Energiemangel ausgefallen</font>";
					$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $nx . "|" . $ny . ($this->systems_id > 0 ? " (" . $this->m->sysname . "-System)" : "") . " beim Sektoreinflug wegen Energiemangels besch�digt. Schaden: " . $fleet->field[$nx][$ny]['damage'], "type" => 2);
					if ($this->cloak == 1) {
						$as[] = "Tarnung";
						$this->cloak = 0;
						$fm .= "<br>- <font color=#FF0000>Tarnung in Sektor " . $nx . "|" . $ny . " ausgefallen</font>";
					}
					// if ($this->schilde_status == 1)
					// {
					// $this->schilde-=$fleet->field[$nx][$ny]['damage'];
					// if ($this->schilde <= 0)
					// {
					// $fm .= "<br>-- Schilde brechen zusammen";
					// $this->huelle-=abs($this->schilde);
					// $this->schilde_status = 0;
					// $this->schilde = 0;
					// }
					// else $fm .= "<br>-- Schilde halten - Status: ".$this->schilde;
					// }
					// else 
					$this->huelle -= ceil($fleet->field[$nx][$ny]['damage'] / 100 * $this->max_huelle);
					if ($this->huelle > 0) $fm .= "<br>H�lle bei " . $this->huelle;
					else {
						$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $nx . "|" . $ny . ($this->systems_id > 0 ? " (" . $this->field[$nx][$ny]['sysname'] . "-System)" : "") . " beim Sektoreinflug wegen Energiemangels zerst�rt", "type" => 2);
						$fm .= "<br>--- H�llenbruch! Die " . stripslashes($this->name) . " wurde zerst�rt";
						$this->trumfield(array("user_id" => $this->uid, "name" => $this->name, "max_huelle" => $this->max_huelle, "id" => $this->id, "cx" => $this->cx, "cy" => $this->cy, "systems_id" => $this->systems_id, "sx" => $this->sx, "sy" => $this->sy, "rumps_id" => $this->rumps_id, "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $this->cname), "durch Sch�den wegen Deflektorversagen", 2);
						$this->destroyed = 1;
						$this->huelle = ceil(($this->max_huelle / 100) * 15);
						$this->stopmove = 1;
					}
					$this->eps = 0;
					if ($this->traktormode == 1) {
						$as[] = "Traktorstrahl";
						$tma = $this->move_traktor_events(0, 0, $nx, $ny);
					}
				}

				if ($this->systems_id == 0) {
					$sysonfield = $this->db->query("SELECT systems_id FROM stu_systems WHERE cx = " . $cx . " AND cy = " . $cy . " LIMIT 1;", 1);
					if ($sysonfield && $sysonfield > 0) {
						$this->db->query("INSERT INTO `stu_systems_user` (`systems_id`, `user_id`, `infotype`) VALUES ('" . $sysonfield . "', '" . $this->uid . "', 'name');");
					}
				}

				if ($this->systems_id > 0 && $fleet->field[$nx][$ny]['x_damage'] > 0 && $this->destroyed != 1) {
					$fm .= "<br>- <font color=#FF0000>" . $fleet->field[$nx][$ny]['x_damage'] . " Schaden ausgel�st durch " . $fleet->field[$nx][$ny]['name'] . " in Sektor " . $nx . "|" . $ny . "</font>";
					if ($this->cloak == 1) {
						$as[] = "Tarnung";
						$this->cloak = 0;
						$fm .= "<br>- <font color=#FF0000>Tarnung in Sektor " . $nx . "|" . $ny . " ausgefallen</font>";
					}
					$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $nx . "|" . $ny . ($this->systems_id > 0 ? " (" . $this->m->sysname . "-System)" : "") . " durch eine Anomalie (" . $fleet->field[$nx][$ny]['name'] . ") besch�digt. Schaden: " . $fleet->field[$nx][$ny]['x_damage'], "type" => 2);
					// if ($this->schilde_status == 1)
					// {
					// $this->schilde-=$fleet->field[$nx][$ny]['x_damage'];
					// if ($this->schilde <= 0)
					// {
					// $fm .= "<br>-- Schilde brechen zusammen";
					// $this->huelle-=abs($this->schilde);
					// $this->schilde_status = 0;
					// $this->schilde = 0;
					// }
					// else $fm .= "<br>-- Schilde halten - Status: ".$this->schilde;
					// }
					// else 
					$this->huelle -= ceil($fleet->field[$nx][$ny]['x_damage'] / 100 * $this->max_huelle);
					if ($this->huelle > 0) $fm .= "<br>H�lle bei " . $this->huelle;
					else {
						$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $nx . "|" . $ny . ($this->systems_id > 0 ? " (" . $this->m->sysname . "-System)" : "") . " durch eine Anomalie (" . $fleet->field[$nx][$ny]['name'] . ") zerst�rt", "type" => 2);
						$fm .= "<br>--- H�llenbruch! Die " . stripslashes($this->name) . " wurde zerst�rt";
						$this->trumfield(array("user_id" => $this->uid, "name" => $this->name, "max_huelle" => $this->max_huelle, "id" => $this->id, "cx" => $this->cx, "cy" => $this->cy, "systems_id" => $this->systems_id, "sx" => $this->sx, "sy" => $this->sy, "rumps_id" => $this->rumps_id, "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $this->cname), "durch die Anomalie (" . $fleet->field[$nx][$ny][name] . ")", 2);
						$this->destroyed = 1;
						$this->dsships[$this->id] = 1;
						$fleet->dsships[$this->id] = 1;
						$this->huelle = ceil(($this->max_huelle / 100) * 15);
						$this->stopmove = 1;
					}
					if ($this->traktormode == 1) {
						$as[] = "Traktorstrahl";
						$tma = $this->move_traktor_events($fleet->field[$nx][$ny]['name'], $fleet->field[$nx][$ny]['x_damage'], $nx, $ny);
					}
				}
				if ($fleet->fm == 1 && $fm) $this->fmmsg .= "<br><b>" . stripslashes($this->name) . " in Sektor " . $nx . "|" . $ny . "</b>" . $fm;
				if ($this->systems_id > 0) {
					$this->sx = $nx;
					$this->sy = $ny;
				} else {
					$this->cx = $nx;
					$this->cy = $ny;
				}
				if ($this->destroyed != 1 && $this->systems_id == 0 && $this->warpfields > 0) $this->warpfields -= 1;
				if ($this->destroyed != 1 && $this->systems_id > 0 && $this->eps > 0) $this->eps -= $fleet->field[$nx][$ny]['ecost'];

				// Flugfelder runtersetzen bei zu wenig Energie
				if ($this->eps < $fields - $i) $fields = $this->eps + $i;

				$i++;

				// Alarm rot ausl�sen
				if ($fleet->fm != 1 && $this->warp != 1 && $this->dsships[$id] != 1 && $this->sess['level'] > 1) {
					$ram = $this->redalert();
					if ($ram) {
						$ram = "<br><b>Das Schiff wurde abgefangen</b>" . $ram;
						if ($this->dsships[$id] == 1) $this->destroyed = 1;
						else {
							$res = $this->db->query("SELECT huelle,schilde,schilde_status,eps FROM stu_ships WHERE id=" . $this->id . " LIMIT 1", 4);
							foreach ($res as $key => $value) $this->$key = $value;
						}
						break;
					}
				}
				if ($warp_break == 1 || $this->stopmove == 1 || ($this->eps == 0 && $i < $fields)) break;
			}

			if ($this->systems_id == 0 && $i > 1) {
				$this->cx = $nx;
				$this->cy = $ny;
				// return "a";
				$this->db->query("UPDATE stu_ships SET cx=" . $nx . ",cy=" . $ny . ",direction='" . $dr . "',warpfields=" . $this->warpfields . ",eps=" . $this->eps . ",huelle=" . $this->huelle . ",schilde=" . $this->schilde . ",schilde_status=" . $this->schilde_status . ",cloak='" . $this->cloak . "',lss='" . $this->lss . "',nbs='" . $this->nbs . "',traktor=" . $this->traktor . ",traktormode='" . $this->traktormode . "',crew=" . $this->crew . ",still=0,cfield=" . $fleet->field[$nx][$ny][type] . " WHERE id=" . $id . " LIMIT 1");
			} elseif ($this->systems_id > 0 && $i > 1) {
				$this->sx = $nx;
				$this->sy = $ny;
				// return "b";
				$this->db->query("UPDATE stu_ships SET sx=" . $nx . ",sy=" . $ny . ",direction='" . $dr . "',eps=" . $this->eps . ",huelle=" . $this->huelle . ",schilde=" . $this->schilde . ",schilde_status=" . $this->schilde_status . ",cloak='" . $this->cloak . "',lss='" . $this->lss . "',nbs='" . $this->nbs . "',traktor='" . $this->traktor . "',traktormode='" . $this->traktormode . "',crew=" . $this->crew . ",still=0,cfield=" . $fleet->field[$nx][$ny][type] . " WHERE id=" . $id . " LIMIT 1");
			}
			if ($this->still > 0) $km = "<br>Kartographierung des Systems abgebrochen";
			if (is_array($as)) {
				$am = "<br>Ausgefallene Systeme:<br>";
				foreach ($as as $key) $am .= $key . " ";
			}
			if ($fleet->fm != 1) return $trm . $flm . $facme . $facml . "Die " . stripslashes($this->name) . " fliegt in Sektor " . $nx . "|" . $ny . " (" . $fleet->field[$nx][$ny]['name'] . ") ein" . $fm . $am . $km . $tm . $ram . $adding;
			foreach ($this as $key => $value) $fleet->shipdata[$id][$key] = $value;
		} else {
			$fleet->fleet_start = 1;
			if ($fleet->fm == 1 && $fleet->ff == $id) {
				$this->systems_id > 0 ? $fleet->nx = $this->sx : $fleet->nx = $this->cx;
				$this->systems_id > 0 ? $fleet->ny = $this->sy : $fleet->ny = $this->cy;
				$fleet->systems_id = $this->systems_id;
			}
			// Flottenbewegung
			if ($fleet->ff == $this->id && $this->fleet_stop != 1) {
				if ($fields > $this->eps) $fields = $this->eps;
				$res = $this->db->query("SELECT id,name FROM stu_ships WHERE fleets_id=" . $this->fleets_id . " LIMIT 25");
				if (mysql_num_rows($res) > 0) {
					$i = 1;
					while ($i <= $fields) {
						if ($l != 0) {
							$cx = $fleet->nx - 1;
							$cy = $fleet->ny;
						}
						if ($r != 0) {
							$cx = $fleet->nx + 1;
							$cy = $fleet->ny;
						}
						if ($u != 0) {
							$cx = $fleet->nx;
							$cy = $fleet->ny - 1;
						}
						if ($d != 0) {
							$cx = $fleet->nx;
							$cy = $fleet->ny + 1;
						}
						if ($cx < 1 || $cy < 1 || $cx > ($this->systems_id > 0 ? $this->sys['sr'] : $mapfields['max_x']) || $cy > ($this->systems_id > 0 ? $this->sys['sr'] : $mapfields['max_y'])) break;
						$fs = $this->db->query("SELECT id,name,eps,systems_id FROM stu_ships WHERE id=" . $fleet->ff . " LIMIT 1", 4);
						if ($fs == 0) break;
						if ($fs['eps'] <= 0) break;
						$fmm = $this->move($fs['id'], 1, $l, $r, $u, $d);
						if ($fmm != "") $msg .= "<br>" . $fs['name'] . ": " . $fmm;
						if ($this->fleet_stop == 1) break;
						$res = $this->db->query("SELECT id,name,eps,systems_id FROM stu_ships WHERE id!=" . $fleet->ff . " AND fleets_id=" . $this->fleets_id . " LIMIT 25");
						while ($fs = mysql_fetch_assoc($res)) {
							if ($fs['eps'] <= 0 && $fs['id'] == $fleet->ff) {
								$this->stopmovereal = 1;
								$fields = $i;
								break;
							}
							$fmm = $this->move($fs['id'], 1, $l, $r, $u, $d);
							if ($fmm != "") $msg .= "<br>" . stripslashes($fs['name']) . ": " . $fmm;
						}
						if ($this->stopmovereal == 1) break;
						$fleet->nx = $cx;
						$fleet->ny = $cy;
						if ($this->warp != 1 && $this->sess['level'] > 1) $ram = $this->fleet_redalert();
						if ($this->facme && $this->fme != 1) {
							$fmsg .= $this->facme;
							$this->fme = 1;
						}
						if ($this->facml && $this->fml != 1) {
							$fmsg .= $this->facml;
							$this->fml = 1;
						}
						if ($this->fmmsg) {
							$msg .= $this->fmmsg;
							unset($this->fmmsg);
						}
						if ($ram) {
							$msg .= "<br><b>Die Flotte wurde beim Einflug in den Sektor abgefangen</b>" . $ram;
							break;
						}
						if ($this->dsships[$fleet->ff] == 1) break;
						if ($this->stopmove == 1) break;
						$i++;
					}
				}
				$msg = $fmsg . "Die Flotte fliegt in Sektor " . $fleet->nx . "|" . $fleet->ny . " (" . $fleet->field[$fleet->nx][$fleet->ny]['name'] . ") ein" . $msg;
				global $_GET;
				if ($fleet->dsships[$fleet->ff] == 1) {
					$newfs = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=" . $this->fleets_id . " AND id!=" . $fleet->ff . " LIMIT 1", 1);
					if ($newfs == 0) $this->destroyed = 1;
					else $_GET['id'] = $newfs;
				} else {
					$_GET['id'] = $fleet->ff;
					$this->destroyed = 0;
				}
				unset($fleet->nfs);
				return $msg;
			}
		}
	}

	function set_destroy_coords($x, $y)
	{
		if ($this->systems_id > 0) {
			$this->sx = $x;
			$this->sy = $y;
			return;
		}
		$this->cx = $x;
		$this->cy = $y;
	}

	function write_logbook()
	{
		foreach ($this->logbook as $key => $value) {
			foreach ($value as $data) {
				$this->db->query("INSERT INTO stu_ships_logs (ships_id,user_id,text,date,type) VALUES ('" . $key . "','" . $data['user_id'] . "','" . addslashes($data['text']) . "',NOW(),'" . $data['type'] . "')");
			}
		}
	}

	function write_private_logbook(&$text)
	{
		$this->db->query("INSERT INTO stu_ships_logs (ships_id,user_id,text,date,type) VALUES ('" . $this->id . "','" . $this->uid . "','" . addslashes($text) . "',NOW(),'4')");
		return "Eintrag wurde im Logbuch gespeichert";
	}

	function move_traktor_events($event, $damage = 0, $x, $y, $special = 0)
	{
		global $fleet;
		$data = $this->db->query("SELECT a.user_id,a.name,a.rumps_id,a.huelle,a.max_huelle,a.crew,a.nbs,a.lss,a.max_crew,a.min_crew,b.name as cname FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id=" . $this->traktor . " LIMIT 1", 4);
		if ($damage > 0) {
			if ($data['huelle'] - $damage <= 0) {
				$this->trumfield(array("user_id" => $data['user_id'], "name" => $data['name'], "max_huelle" => $data['max_huelle'], "id" => $this->traktor, "cx" => $x, "cy" => $y, "systems_id" => $this->systems_id, "sx" => $x, "sy" => $y, "rumps_id" => $data['rumps_id'], "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $data['cname']), "durch die Anomalie (" . $event . ")");
				$tma = "<br>Das Schiff wurde beim Sektoreinflug durch eine Anomalie (" . $event . ") zerst�rt";
			} else {
				$data['huelle'] -= $damage;
				$this->db->query("UPDATE stu_ships SET huelle=" . $data['huelle'] . " WHERe id=" . $this->traktor . " LIMIT 1");
				$tma = "<br>Das Schiff wurde beim Sektoreinflug durch eine Anomalie (" . $event . ") besch�digt - H�lle jetzt bei " . $data['huelle'];
			}
		}
		if ($special == 1) {
			$dc = round($data['max_crew'] / 5);
			if ($dc > $data['crew']) $dc = $data['crew'];
			$tma = "<br>-- Das Schiff von Strahlung �berflutet - " . $dc . " Crewmitglieder werden get�tet";
			$data['crew'] -= $dc;
			if ($data['crew'] < $data['min_crew']) {
				$data['nbs'] = 0;
				$data['lss'] = 0;
			}
			$this->db->query("UPDATE stu_ships SET crew=" . $data['crew'] . ",nbs='" . $data['nbs'] . "',lss='" . $data['lss'] . "' WHERE id=" . $this->traktor . " LIMIT 1");
		}
		if ($special == 2 && $data['nbs'] == 1) $this->db->query("UPDATE stu_ships SET nbs='0' WHERE id=" . $this->traktor . " LIMIT 1");
		if ($special == 3 && ($data['nbs'] == 1 || $data['lss'] == 1)) $this->db->query("UPDATE stu_ships SET nbs='0',lss='0' WHERE id=" . $this->traktor . " LIMIT 1");
		if ($data['user_id'] != $this->uid) $this->send_pm($this->uid, $data['user_id'], "Die " . $this->name . " hat den auf die " . $data['name'] . " gerichteten Traktorstrahl in Sektor " . ($this->systems_id > 0 ? $x . "|" . $y . " (" . $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $this->systems_id . " LIMIT 1", 1) . "-System)" : $x . "|" . $y) . " deaktiviert" . $tma, 3);
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=" . $this->traktor);
		$this->traktor = 0;
		$this->traktormode = 0;
		return $tma;
	}

	function entersystem($id)
	{
		$return = shipexception(array("slots" => 0, "eps" => 1, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->checksubsystem(7, $this->id) == 1) return "Das System kann nicht betreten werden (Grund: Reparatur am Impulsantrieb wurde noch nicht abgeschlossen)";
		if ($this->checksubsystem(11, $this->id) == 1) return "Das System kann nicht betreten werden (Grund: Reparatur am Warpantrieb wurde noch nicht abgeschlossen)";
		if ($this->traktormode == 2) return "Das Schiff wird von einem Traktorstrahl gehalten";
		$sys = $this->m->getsystembyxy($this->cx, $this->cy);
		if ($sys == 0) return;
		if (!$this->direction) $this->direction = rand(1, 4);
		if ($this->direction == 1) {
			$sx = $sys[sr];
			$sy = rand(round($sys['sr'] / 2) - round($sys['sr'] / 5), round($sys['sr'] / 2) + round($sys['sr'] / 5));
		}
		if ($this->direction == 2) {
			$sx = 1;
			$sy = rand(round($sys['sr'] / 2) - round($sys['sr'] / 5), round($sys['sr'] / 2) + round($sys['sr'] / 5));
		}
		if ($this->direction == 3) {
			$sx = rand(round($sys['sr'] / 2) - round($sys['sr'] / 5), round($sys['sr'] / 2) + round($sys['sr'] / 5));
			$sy = $sys['sr'];
		}
		if ($this->direction == 4) {
			$sx = rand(round($sys['sr'] / 2) - round($sys['sr'] / 5), round($sys['sr'] / 2) + round($sys['sr'] / 5));
			$sy = 1;
		}
		$this->sx = $sx;
		$this->sy = $sy;
		$this->systems_id = $sys['systems_id'];
		$this->eps -= 1;
		$ftype = $this->db->query("SELECT type FROM stu_sys_map WHERE systems_id=" . $sys['systems_id'] . " AND sx=" . $sx . " AND sy=" . $sy, 1);
		$res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=" . $this->sess['allys_id'] . ",cloak='" . $this->cloak . "',notified='0' WHERE sx=" . $sx . " AND sy=" . $sy . " AND ships_id=" . $this->id . " AND systems_id=" . $sys['systems_id'] . " LIMIT 1", 6);
		if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,sx,sy,systems_id,cloak) VALUES ('" . $this->uid . "','" . $this->id . "','" . $this->rumps_id . "','" . $this->sess['allys_id'] . "',NOW(),'" . $sx . "','" . $sy . "','" . $sys['systems_id'] . "','" . $this->cloak . "')");
		if ($this->fleets_id > 0) {
			global $fleet;
			if ($this->fsf != $this->id) {
				$lfm = "Die " . $this->name . " hat die Flotte verlassen<br>";
				$fla = ",fleets_id=0";
				if ($this->traktormode == 1) $this->db->query("UPDATE stu_ships SET sx=" . $sx . ",sy=" . $sy . ",systems_id=" . $sys['systems_id'] . ",warp='0',cfield=" . $ftype . " WHERE id=" . $this->traktor . " LIMIT 1");
				return $fleet->entersystem($this->fleets_id, $this->id, array("systems_id" => $sys['systems_id'], "sx" => $sx, "sy" => $sy, "ftype" => $ftype, "sysname" => $sys['name']));
			} else {
				if ($this->traktormode == 1) $this->db->query("UPDATE stu_ships SET sx=" . $sx . ",sy=" . $sy . ",systems_id=" . $sys['systems_id'] . ",warp='0',cfield=" . $ftype . " WHERE id=" . $this->traktor . " LIMIT 1");
				return $fleet->entersystem($this->fleets_id, $this->id, array("systems_id" => $sys['systems_id'], "sx" => $sx, "sy" => $sy, "ftype" => $ftype, "sysname" => $sys['name']));
			}
		}
		$this->db->query("UPDATE stu_ships SET sx=" . $sx . ",sy=" . $sy . ",systems_id=" . $sys['systems_id'] . ",warp='0',eps=eps-1,cfield=" . $ftype . $fla . " WHERE id=" . $this->id);
		if ($this->traktormode == 1) $this->db->query("UPDATE stu_ships SET sx=" . $sx . ",sy=" . $sy . ",systems_id=" . $sys['systems_id'] . ",warp='0',cfield=" . $ftype . " WHERE id=" . $this->traktor . " LIMIT 1");
		$see = $this->entersystem_event($sys['name']);
		return $lfm . ($this->warp == 1 ? "Das Schiff kommt aus dem Warp<br>" : "") . "Die " . $this->name . " fliegt in das " . $sys['name'] . "-System ein" . ($see ? $see . "<br />" : "");
	}

	function entersystem_event(&$sysname)
	{
		$map_special = $this->db->query("SELECT type FROM stu_map_special WHERE systems_id=" . $this->systems_id . " AND sx=" . $this->sx . " AND sy=" . $this->sy . " LIMIT 1", 1);
		$field = $this->db->query("SELECT b.type,b.name,b.ecost,b.damage,b.x_damage,b.sensoroff,b.cloakoff,b.shieldoff FROM stu_sys_map as a LEFT JOIN stu_map_ftypes as b USING(type) WHERE a.systems_id=" . $this->systems_id . " AND a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " LIMIT 1", 4);
		if ($field['sensoroff'] == 1 && ($this->nbs == 1 || $this->lss == 1)) {
			$as[] = "Sensoren";
			$this->nbs = 0;
			$this->lss = 0;
			if ($this->traktormode == 1) $tma = $this->move_traktor_events(0, 0, $this->sx, $this->sy, 3);
		}
		if ($field['cloakoff'] && $this->cloak == 1) {
			$as[] = "Tarnung";
			$this->cloak = 0;
		}
		if ($field['shieldoff'] == 1 && $this->schilde_status == 1) {
			$as[] = "Schilde";
			$this->schilde_status = 0;
		}
		if ($field['type'] == 6) {
			// Plasmanebel
			if ($this->schilde_status == 1 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=" . $this->m2 . " LIMIT 1", 1) == 5) {
				$as[] = "Schilde";
				$this->schilde_status = 0;
			}
			if ($map_special == 1) {
				// Plasmasturm
				if (($this->m4 != 4901) && ($this->m4 != 357) && ($this->m4 != 364) && ($this->m4 != 954)) {
					$dmg = rand(10, 20);
					$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde durch einen Plasmasturm besch�digt. Schaden: " . $dmg, "type" => 2);
					$fm .= "<br>- <font color=#FF0000>" . $dmg . " Schaden ausgel�st durch einen Plasmasturm</font>";
					if ($this->cloak == 1) $this->cloak = 0;
					$arr = damageship($dmg, $this->huelle, $this->schilde, $this->schilde_status);
					$fm .= $arr['msg'];
					$this->huelle = $arr['huelle'];
					$this->schilde = $arr['schilde'];
					$this->schilde_status = $arr['schilde_status'];
					if ($this->huelle <= 0) {
						$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde durch einen Plasmasturm besch�digt. Schaden: " . $dmg . "", "type" => 2);
						$fm .= "<br>-- H�llenbruch! Die " . stripslashes($this->name) . " wurde zerst�rt";
						$this->trumfield(array("user_id" => $this->uid, "name" => $this->name, "max_huelle" => $this->max_huelle, "id" => $this->id, "cx" => $this->cx, "cy" => $this->cy, "systems_id" => $this->systems_id, "sx" => $this->sx, "sy" => $this->sy, "rumps_id" => $this->rumps_id, "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $this->cname), "durch die Anomalie (Plasmasturm)");
						$this->destroyed = 1;
						$this->dsships[$this->id] = 1;
						$fleet->dsships[$this->id] = 1;
						$this->huelle = ceil(($this->max_huelle / 100) * 15);
					}
					if ($this->traktormode == 1) {
						$as[] = "Traktorstrahl";
						$tma = $this->move_traktor_events("Plasmasturm", rand(10, 20), $this->sx, $this->sy);
					}
				} else $fm .= "<br>- <font color=\"#FF0000\">Warnung! Ein Plasmasturm wurde in Sektor " . $this->sx . "|" . $this->sy . " festgestellt</font>";
			}
		}
		if ($field['type'] == 8) {
			// Dunkelnebel
			if ($this->nbs == 1) {
				$as[] = "Nahbereichssensoren";
				$this->nbs = 0;
			}
			if ($this->traktormode == 1) {
				$as[] = "Traktorstrahl";
				$tma = $this->move_traktor_events("Dunkelnebel", 0, $this->sx, $this->sy, 2);
			}
		}
		if ($field['type'] == 15) {
			// Metaphasennebel
			if ($this->schilde_status == 1 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=" . $this->m2 . " LIMIT 1", 1) == 4) {
				$as[] = "Schilde";
				$this->schilde_status = 0;
			}
		}
		if ($field['type'] == 9) {
			// Protostellarer Nebel
			if ($this->schilde_status == 1 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=" . $this->m2 . " LIMIT 1", 1) == 7) {
				$as[] = "Schilde";
				$this->schilde_status = 0;
			}
		}
		if ($field['type'] == 10 && $map_special == 3) {
			// Radioaktiver Nebel
			$dmg = rand(5, 15);
			$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $this->sx . "|" . $this->sy . " (" . $sysname . "-System) durch einen radioaktiven Nebel besch�digt. Schaden: " . $dmg, "type" => 2);
			$fm .= "<br>- <font color=#FF0000>- Ein radioaktiver Strahlungsausbruch wurde in diesem Sektor registriert</font>";
			if ($this->schilde_status == 1 && (($this->m4 != 4901) && ($this->m4 != 357) && ($this->m4 != 364) && ($this->m4 != 954))) {
				if ($this->schilde <= $dmg) {
					$fm .= "<br>-- Schilde brechen zusammen";
					$this->schilde_status = 0;
					$this->schilde = 0;
				} else {
					$this->schilde -= $dmg;
					$fm .= "<br>-- Schilde halten - Status: " . $this->schilde;
				}
			}
			if ($this->schilde_status == 0) {
				$dc = round($this->max_crew / 5);
				if ($dc > $this->crew) $dc = $this->crew;
				$fm .= "<br>--- Das Schiff von Strahlung �berflutet - " . $dc . " Crewmitglieder werden get�tet";
				$this->crew -= $dc;
				if ($this->crew < $this->min_crew) {
					$this->schilde_status = 0;
					$this->cloak = 0;
					$this->nbs = 0;
					$this->lss = 0;
				}
			}
			if ($this->traktormode == 1) {
				$as[] = "Traktorstrahl";
				$tma = $this->move_traktor_events("Radioaktiver Nebel", 0, $this->sx, $this->sy, 1);
			}
		}
		if ($map_special == 5) {
			// Captain, Captain...ein Ionensturm! - Jojo, is scho Recht Chekov...du Depp
			if (($this->m4 != 4901) && ($this->m4 != 357) && ($this->m4 != 364) && ($this->m4 != 954)) {
				$dmg = rand(5, 15);
				$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $this->sx . "|" . $this->sy . " (" . $sysname . "-System) durch einen Ionensturm besch�digt. Schaden: " . $dmg, "type" => 2);
				$fm .= "<br>- <font color=#FF0000>" . $dmg . " Schaden ausgel�st durch einen Ionensturm in diesem Sektor</font>";
				$arr = damageship($dmg, $this->huelle, $this->schilde, $this->schilde_status);
				$fm .= $arr['msg'];
				$this->huelle = $arr['huelle'];
				$this->schilde = $arr['schilde'];
				$this->schilde_status = $arr['schilde_status'];
				if ($this->huelle <= 0) {
					$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $this->sx . "|" . $this->sy . " (" . $sysname . "-System) beim Sektoreinflug durch einen Ionensturm zerst�rt", "type" => 2);
					$fm .= "<br>-- H�llenbruch! Die " . $this->name . " wurde zerst�rt";
					$this->trumfield(array("user_id" => $this->uid, "name" => $this->name, "max_huelle" => $this->max_huelle, "id" => $this->id, "cx" => $this->cx, "cy" => $this->cy, "systems_id" => $this->systems_id, "sx" => $this->sx, "sy" => $this->sy, "rumps_id" => $this->rumps_id, "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $this->cname), "durch Anomalie (Ionensturm)");
					$this->destroyed = 1;
					$this->dsships[$this->id] = 1;
					$fleet->dsships[$this->id] = 1;
					$this->huelle = ceil(($this->max_huelle / 100) * 15);
				}
				if ($this->traktormode == 1) {
					$as[] = "Traktorstrahl";
					$tma = $this->move_traktor_events("Ionensturm", rand(5, 15), $this->sx, $this->sy);
				}
			} else $fm .= "<br>- <font color=\"#FF0000\">Warnung! Es wurde ein Ionensturm in diesem Sektor festgestellt</font>";
		}
		if ($this->eps - $field['ecost'] < 0 && $field['damage'] > 0) {
			$fm .= "<br>- <font color=#FF0000>Deflektoren wegen Energiemangel ausgefallen</font>";
			$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $this->sx . "|" . $this->sy . " (" . $sysname . "-System) beim Sektoreinflug wegen Energiemangels besch�digt. Schaden: " . $field['damage'], "type" => 2);
			if ($this->cloak == 1) $this->cloak = 0;
			if ($this->schilde_status == 1) {
				$this->schilde -= $field['damage'];
				if ($this->schilde <= 0) {
					$fm .= "<br>-- Schilde brechen zusammen";
					$this->huelle -= abs($this->schilde);
					$this->schilde_status = 0;
					$this->schilde = 0;
				} else $fm .= "<br>-- Schilde halten - Status: " . $this->schilde;
			} else $this->huelle -= $field['damage'];
			if ($this->huelle > 0) $fm .= "<br>H�lle bei " . $this->huelle;
			else {
				$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $this->sx . "|" . $this->sy . " (" . $sysname . "-System) beim Sektoreinflug wegen Energiemangels zerst�rt", "type" => 2);
				$fm .= "<br>--- H�llenbruch! Die " . stripslashes($this->name) . " wurde zerst�rt";
				$this->trumfield(array("user_id" => $this->uid, "name" => $this->name, "max_huelle" => $this->max_huelle, "id" => $this->id, "cx" => $this->cx, "cy" => $this->cy, "systems_id" => $this->systems_id, "sx" => $this->sx, "sy" => $this->sy, "rumps_id" => $this->rumps_id, "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $this->cname));
				$this->destroyed = 1;
				$this->huelle = ceil(($this->max_huelle / 100) * 15);
			}
			$this->eps = 0;
			if ($this->traktormode == 1) {
				$as[] = "Traktorstrahl";
				$tma = $this->move_traktor_events(0, 0, $this->sx, $this->sy);
			}
		}
		if ($this->systems_id > 0 && $field['x_damage'] > 0 && $this->destroyed != 1) {
			$fm .= "<br>- <font color=#FF0000>" . $field['x_damage'] . " Schaden ausgel�st durch " . $field['name'] . " in diesem Sektor</font>";
			if ($this->cloak == 1) $this->cloak = 0;
			$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $this->sx . "|" . $this->sy . " (" . $sysname . "-System) durch eine Anomalie (" . $field['name'] . ") besch�digt. Schaden: " . $field['x_damage'], "type" => 2);
			if ($this->schilde_status == 1) {
				$this->schilde -= $field['x_damage'];
				if ($this->schilde <= 0) {
					$fm .= "<br>-- Schilde brechen zusammen";
					$this->huelle -= abs($this->schilde);
					$this->schilde_status = 0;
					$this->schilde = 0;
				} else $fm .= "<br>-- Schilde halten - Status: " . $this->schilde;
			} else $this->huelle -= $field['x_damage'];
			if ($this->huelle > 0) $fm .= "<br>H�lle bei " . $this->huelle;
			else {
				$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff wurde in Sektor " . $this->sx . "|" . $this->sy . " (" . $sysname . "-System) durch eine Anomalie (" . $field['name'] . ") zerst�rt", "type" => 2);
				$fm .= "<br>--- H�llenbruch! Die " . $this->name . " wurde zerst�rt";
				$this->trumfield(array("user_id" => $this->uid, "name" => $this->name, "max_huelle" => $this->max_huelle, "id" => $this->id, "cx" => $this->cx, "cy" => $this->cy, "systems_id" => $this->systems_id, "sx" => $this->sx, "sy" => $this->sy, "rumps_id" => $this->rumps_id, "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $this->cname), "Anomalie (" . $field['name'] . ")");
				$this->destroyed = 1;
				$this->dsships[$this->id] = 1;
				$fleet->dsships[$this->id] = 1;
				$this->huelle = ceil(($this->max_huelle / 100) * 15);
			}
			if ($this->traktormode == 1) {
				$as[] = "Traktorstrahl";
				$tma = $this->move_traktor_events($field['name'], $field['x_damage'], $this->sx, $this->sy);
			}
		}
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . ",huelle=" . $this->huelle . ",schilde=" . $this->schilde . ",schilde_status=" . $this->schilde_status . ",cloak='" . $this->cloak . "',lss='" . $this->lss . "',nbs='" . $this->nbs . "',traktor='" . $this->traktor . "',traktormode='" . $this->traktormode . "',crew=" . $this->crew . ",still=0 WHERE id=" . $this->id . " LIMIT 1");
		if (is_array($as)) {
			$am = "<br>Ausgefallene Systeme:<br>";
			foreach ($as as $key) $am .= $key . " ";
		}
		return $fm . $am;
	}

	function leavesystem($id)
	{
		$return = shipexception(array("system" => -1, "warp" => 1, "slots" => 0, "traktor" => 0, "eps" => 2, "dock" => 0, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->checksubsystem(7, $this->id) == 1) return "Das System kann nicht verlassen werden (Grund: Reparatur am Impulsantrieb wurde noch nicht abgeschlossen)";
		if ($this->checksubsystem(11, $this->id) == 1) return "Das System kann nicht verlassen werden (Grund: Reparatur am Warpantrieb wurde noch nicht abgeschlossen)";
		$this->sys = $this->m->getsystembyid($this->systems_id);
		if ($this->fleets_id > 0) {
			global $fleet;
			if ($this->fsf != $this->id) {
				// $lfm = "Die ".$this->name." hat die Flotte verlassen<br>";
				// $fla = ",fleets_id=0";
				return $fleet->leavesystem($this->fleets_id, $this->id);
			} else {
				return $fleet->leavesystem($this->fleets_id, $this->id);
			}
		}
		$this->direction = null;
		$this->db->query("UPDATE stu_ships SET direction=NULL,sx=0,sy=0,systems_id=0,eps=eps-2,warp='1',still=0,cfield=" . $this->sys['type'] . "22" . $fla . " WHERE id=" . $id . " LIMIT 1");
		return $lfm . "Die " . $this->name . " hat das System verlassen";
	}

	function loadwarpcore($c)
	{
		$return = shipexception(array("crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->is_shuttle == 1) return "Warpkern aufladen ist auf Shuttles nicht m�glich";
		// if ($this->checksubsystem(11,$this->id) == 1) return "Der Warpkern kann nicht geladen werden (Grund: Reparatur am Warpkern wurde noch nicht abgeschlossen)";
		if ($this->max_warpcore == $this->warpcore) return "Der Warpkern ist bereits vollst�ndig geladen";
		$dil = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $this->id . " AND goods_id=8 LIMIT 1", 1);
		if ($dil == 0) return "Zum Laden wird mindestens 1 Dilithium ben�tigt";
		$c == "max" ? $c = $dil : $c = 1;
		$deut = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $this->id . " AND goods_id=5 LIMIT 1", 1);
		if ($deut < 4) return "Zum Laden werden mindestens 4 Deuterium ben�tigt";
		if (floor($deut / 4) < $c) $c = floor($deut / 4);
		$am = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $this->id . " AND goods_id=6 LIMIT 1", 1);
		if ($am < 4) return "Zum Laden werden mindestens 4 Antimaterie ben�tigt";
		if (floor($am / 4) < $c) $c = floor($am / 4);
		$load = $c * 100;
		if ($load > $this->max_warpcore - $this->warpcore) {
			$load = $this->max_warpcore - $this->warpcore;
			$c = ceil($load / 100);
		}
		#$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_ships SET warpcore=warpcore+" . $load . " WHERE id=" . $this->id . " LIMIT 1");
		$this->lowerstorage($this->id, 8, $c);
		$this->lowerstorage($this->id, 5, $c * 4);
		$this->lowerstorage($this->id, 6, $c * 4);
		#$this->db->query("COMMIT");
		return "Der Warpkern wurde um " . $load . " Einheiten geladen - Status: " . ($this->warpcore + $load);
	}

	function av($dev)
	{
		if ($this->cloakable == 0 && $dev == "cl") return;
		if ($this->porep != 1 && $dev == "re") return;
		if ($this->rumps_id == 9) return "Konstrukte k�nnen keine Systeme aktivieren";
		if ($dev == "cl") {
			$e = tarnkosten($this->rumps_id);
			$nm = "Tarnung";
			$cr = $this->min_crew;
			$fc = "cloak";
			if ($this->checksubsystem(9, $this->id) == 1) return "Die Tarnung kann nicht aktiviert werden (Grund: Reparatur an der Tarnung wurde noch nicht abgeschlossen)";
			if ($this->map['cloakoff'] == 1) return "Die Tarnung kann nicht aktiviert werden (Grund: " . $this->map['name'] . ")";
			if ($this->traktormode == 1) return "Die Tarnung kann nicht aktiviert werden (Grund: Schiff hat den Traktorstrahl aktiviert)";
			if ($this->traktormode == 2) return "Die Tarnung kann nicht aktiviert werden (Grund: Schiff wird von einem Traktorstrahl gehalten)";
			if ($this->dock > 0) return "Die Tarnung kann nicht aktiviert werden (Grund: Schiff ist angedockt)";
			// if ($this->plans_id != 1 && $this->slots == 0 && $this->uid > 100 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance,"maintaintime" => $this->maintaintime)) > rand(1,100)) return $this->damage_subsystem("foo",$this->id,9);
			if ($this->still > 0) $smsg = "<br>" . $this->stopkartographie();
		}
		if ($dev == "sh") {
			$nm = "Schilde";
			$e = 1;
			$cr = $this->min_crew;
			$fc = "schilde_status";
			if ($this->checksubsystem(2, $this->id) == 1) return "Schilde k�nnen nicht aktiviert werden (Grund: Reparatur an den Schilden wurde noch nicht abgeschlossen)";
			if ($this->cloak == 1) return "Schilde k�nnen nicht aktiviert werden (Grund: Tarnung aktiviert)";
			if ($this->schilde == 0) return "Schilde k�nnen nicht aktiviert werden (Grund: Nicht aufgeladen)";
			if ($this->map['shieldoff'] == 1) return "Die Schilde k�nnen nicht aktiviert werden (Grund: " . $this->map[name] . ")";
			if ($this->traktormode == 1) return "Die Schilde k�nnen nicht aktiviert werden (Grund: Schiff hat den Traktorstrahl aktiviert)";
			if ($this->traktormode == 2) return "Die Schilde k�nnen nicht aktiviert werden (Grund: Schiff wird von einem Traktorstrahl gehalten)";
			if ($this->dock > 0) return "Die Schilde k�nnen nicht aktiviert werden (Grund: Schiff ist angedockt)";
			// if ($this->plans_id != 1 && $this->uid > 100 && $this->slots == 0 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance,"maintaintime" => $this->maintaintime)) > rand(1,100)) return $this->damage_subsystem("foo",$this->id,2);
			if ($this->still > 0) $smsg = "<br>" . $this->stopkartographie();
		}


		if ($dev == "wrp") {
			$nm = "Warpantrieb";
			$e = 1;
			$cr = $this->min_crew;
			$fc = "warp";
			// if ($this->checksubsystem(2,$this->id) == 1) return "Schilde k�nnen nicht aktiviert werden (Grund: Reparatur an den Schilden wurde noch nicht abgeschlossen)";
			if ($this->systems_id > 0) return "Warpantrieb kann innerhalb eines Systems nicht benutzt werden";
			// if ($this->schilde == 0) return "Schilde k�nnen nicht aktiviert werden (Grund: Nicht aufgeladen)";
			// if ($this->map['shieldoff'] == 1) return "Die Schilde k�nnen nicht aktiviert werden (Grund: ".$this->map[name].")";
			// if ($this->traktormode == 1) return "Die Schilde k�nnen nicht aktiviert werden (Grund: Schiff hat den Traktorstrahl aktiviert)";
			// if ($this->traktormode == 2) return "Die Schilde k�nnen nicht aktiviert werden (Grund: Schiff wird von einem Traktorstrahl gehalten)";
			// if ($this->dock > 0) return "Der Warpantrieb kann nicht aktiviert werden (Grund: Schiff ist angedockt)";
			// if ($this->plans_id != 1 && $this->uid > 100 && $this->slots == 0 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance,"maintaintime" => $this->maintaintime)) > rand(1,100)) return $this->damage_subsystem("foo",$this->id,2);
			// if ($this->still > 0) $smsg = "<br>".$this->stopkartographie();
		}


		if ($dev == "wp") {
			$nm = "Waffensystem (Prim�rwaffe)";
			$e = 1;
			$cr = $this->min_crew;
			$fc = "wea_phaser";
			if ($this->mod_w1 == 0) return "Es ist keine Prim�rwaffe auf diesem Schiff installiert";
			// if ($this->checksubsystem(6,$this->id) == 1) return "Waffensystem (Strahlenwaffe) kann nicht aktiviert werden (Grund: Reparatur an dem Waffensystem wurde noch nicht abgeschlossen)";
			// if ($this->cloak == 1) return "Waffensystem (Strahlenwaffe) kann nicht aktiviert werden (Grund: Tarnung aktiviert)";
		}
		if ($dev == "wt") {
			$nm = "Waffensystem (Sekund�rwaffe)";
			$e = 1;
			$cr = $this->min_crew;
			$fc = "wea_torp";
			if ($this->mod_w2 == 0) return "Es ist keine Sekund�rwaffe auf diesem Schiff installiert";
			// if ($this->checksubsystem(10,$this->id) == 1) return "Waffensystem (Torpedob�nke) kann nicht aktiviert werden (Grund: Reparatur an dem Waffensystem wurde noch nicht abgeschlossen)";
			// if ($this->cloak == 1) return "Waffensystem (Torpedob�nke) kann nicht aktiviert werden (Grund: Tarnung aktiviert)";
		}
		if ($dev == "lss") {
			$this->systems_id > 0 ? $nm = "Kurzstreckensensoren" : $nm = "Langstreckensensoren";
			$e = 1;
			$cr = ($this->rumps_id == 10 ? 0 : $this->min_crew - 1);
			$fc = "lss";
			if ($this->checksubsystem(4, $this->id) == 1) return "Die Sensoren k�nnen nicht aktiviert werden (Grund: Reparatur an den Sensoren wurden noch nicht abgeschlossen)";
			if ($this->map['sensoroff'] == 1) return "Die Sensoren k�nnen nicht aktiviert werden (Grund: " . $this->map[name] . ")";
			if ($this->plans_id != 1 && $this->uid > 100 && $this->slots == 0 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance, "maintaintime" => $this->maintaintime)) > rand(1, 100)) return $this->damage_subsystem("foo", $this->id, 4);
		}
		if ($dev == "nbs") {
			$nm = "Nahbereichssensoren";
			$e = 1;
			$cr = ($this->rumps_id == 10 ? 0 : $this->min_crew - 1);
			$fc = "nbs";
			if ($this->checksubsystem(4, $this->id) == 1) return "Die Sensoren k�nnen nicht aktiviert werden (Grund: Reparatur an den Sensoren wurden noch nicht abgeschlossen)";
			if ($this->sess['level'] < 2) return "Die Sensoren k�nnen nicht aktiviert werden (Grund: Erst ab Level 2 m�glich)";
			if ($this->plans_id != 1 && $this->uid > 100 && $this->slots == 0 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance, "maintaintime" => $this->maintaintime)) > rand(1, 100)) return $this->damage_subsystem("foo", $this->id, 4);
		}
		if ($dev == "re") {
			$nm = "Replikator";
			$cr = 1;
			$e = 0;
			$fc = "replikator";
		}
		if ($this->$fc == 1) return;
		if ($this->eps < $e) return "System (" . $nm . ") kann nicht aktiviert werden - " . $e . " Energie ben�tigt";
		if ($this->crew < $cr) return "System (" . $nm . ") kann nicht aktiviert werden - " . $cr . " Crew ben�tigt";
		if ($dev == "sh" && $this->schilde_status > 1 && $this->schilde_status > time()) return "Die Schilde sind polarisiert (" . date("d.m.Y H:i", $this->schilde_status) . ")";
		if ($dev == "cl" && $this->cloak > 1 && $this->cloak > time()) return "Die Tarnvorrichtung baut noch Chronitonen ab (" . date("d.m.Y H:i", $this->cloak) . ")";
		if ($dev == "cl") {
			// $this->db->query("UPDATE stu_ships SET wea_phaser='0',wea_torp='0' WHERE id=".$this->id." LIMIT 1");
			if ($this->schilde_status == 1) $this->db->query("UPDATE stu_ships SET schilde_status=0 WHERE id=" . $this->id . " LIMIT 1");
			$blmsg = "<br>" . $this->stopblockade($this->fleets_id);
		}
		if (!$fc) return;
		$this->db->query("UPDATE stu_ships SET " . $fc . "='1',eps=eps-" . $e . " WHERE id=" . $this->id . " LIMIT 1");
		$this->eps -= $e;
		return "System (" . $nm . ") wurde aktiviert" . $smsg . $blmsg;
	}

	function dv($dev)
	{
		if ($dev == "cl" && $this->cloak == 1) {
			$nm = "Tarnung";
			$fc = "cloak";
			if ($this->systems_id > 0) {
				$res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=" . $this->sess['allys_id'] . ",cloak='0',notified='0' WHERE sx=" . $this->sx . " AND sy=" . $this->sy . " AND ships_id=" . $this->id . " AND systems_id=" . $this->systems_id . " LIMIT 1", 6);
				if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,sx,sy,systems_id,cloak) VALUES ('" . $this->uid . "','" . $this->id . "','" . $this->rumps_id . "','" . $this->sess['allys_id'] . "',NOW(),'" . $this->sx . "','" . $this->sy . "','" . $this->systems_id . "','0')");
			} else {
				$res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=" . $this->sess['allys_id'] . ",cloak='0' WHERE cx=" . $this->cx . " AND cy=" . $this->cy . " AND ships_id=" . $this->id . " LIMIT 1", 6);
				if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,cx,cy,cloak) VALUES ('" . $this->uid . "','" . $this->id . "','" . $this->rumps_id . "','" . $this->sess['allys_id'] . "',NOW(),'" . $this->cx . "','" . $this->cy . "','0')");
			}
		}
		if ($dev == "sh" && $this->schilde_status == 1) {
			$nm = "Schilde";
			$fc = "schilde_status";
		}
		if ($dev == "wrp" && $this->warp == 1) {
			$nm = "Warpantrieb";
			$fc = "warp";
		}
		if ($dev == "lss" && $this->lss == 1) {
			$nm = ($this->systems_id == 0 ? "Langstreckensensoren" : "Kurzstreckensensoren");
			$fc = "lss";
		}
		if ($dev == "wp" && $this->wea_phaser == 1) {
			$nm = "Waffensystem (Prim�rwaffe)";
			$fc = "wea_phaser";
		}
		if ($dev == "wt" && $this->wea_torp == 1) {
			$nm = "Waffensystem (Sekund�rwaffe)";
			$fc = "wea_torp";
		}
		if ($dev == "nbs" && $this->nbs == 1) {
			$nm = "Nahbereichssensoren";
			$fc = "nbs";
			if ($this->still > 0) $smsg = $this->stopkartographie() . "<br>";
		}
		if ($dev == "re" && $this->replikator == 1) {
			$nm = "Replikator";
			$fc = "replikator";
		}
		if (!$fc) return;
		$this->db->query("UPDATE stu_ships SET " . $fc . "='0' WHERE id=" . $this->id . " LIMIT 1");
		if ($dev == "cl") {
			$this->db->query("DELETE FROM stu_ships_decloaked WHERE ships_id=" . $this->id . " AND UNIX_TIMESTAMP(date)=0 LIMIT 1");


			$this->cloak = time() + 900;



			$this->db->query("UPDATE stu_ships SET cloak='" . $this->cloak . "' WHERE id=" . $this->id . " LIMIT 1");





			//if ($this->warp == 0)
			//{
			//if ($this->fleets_id == 0) $ramsg = $this->redalert();
			//else
			//{
			//	global $fleet;
			//	if ($this->systems_id > 0)
			//	{
			//		$fleet->nx = $this->sx;
			//		$fleet->ny = $this->sy;
			//		$fleet->systems_id = $this->systems_id;
			//	}
			//	else
			//	{
			//		$fleet->nx = $this->cx;
			//		$fleet->ny = $this->cy;
			//	}
			//	$ramsg = $this->fleet_redalert();
			//}
			//}
		} else $this->db->query("UPDATE stu_ships SET " . $fc . "='0' WHERE id=" . $this->id . " LIMIT 1");
		return "System (" . $nm . ") wurde deaktiviert" . $smsg . $ramsg;
	}

	function bussard($id, $good, $c)
	{
		$data = $this->db->query("SELECT a.eps,a.schilde_status,a.warp,a.cloak,a.crew,a.min_crew,a.dock,b.bussard,b.storage FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.user_id=" . $this->uid . " AND a.id=" . $id . " LIMIT 1", 4);
		if ($data == 0 || $data['bussard'] == 0 || ($c != "max" && $c < 1) || ($good != 5 && $good != 7)) return;
		if ($good == 5 && $this->map['deut'] == 0) return;
		if ($good == 7 && $this->map['type'] != 6) return;
		$return = shipexception(array("schilde_status" => 0, "cloak" => 0, "eps" => -1, "warpstate" => 0, "dock" => 0, "crew" => $data['min_crew']), $data);
		if ($return['code'] == 1) return $return['msg'];
		if ($good == 5) $buss = $this->map['deut'];
		if ($good == 7) $buss = round($data['bussard'] / 3);
		if ($buss < $data['bussard']) $data['bussard'] = $buss;
		if ($c == "max") $c = $data['eps'];
		else {
			if ($c > $data['eps']) $c = $data['eps'];
		}
		$stor = $data['storage'] - $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=" . $id, 1);
		if ($stor == 0) return "Auf dem Schiff ist kein freier Lagerraum vorhanden";
		if ($good == 5) {
			if ($data['bussard'] * $c > $stor) {
				$c = ceil($stor / $data['bussard']);
				$str = $stor;
			} else $str = $c * $data['bussard'];
		}
		if ($good == 7) {
			if ($data['bussard'] * $c > $stor) {
				$c = ceil($stor / $data['bussard']);
				$str = $stor;
			} else $str = $c * $data['bussard'];
		}
		$this->db->query("UPDATE stu_ships SET eps=eps-" . $c . " WHERE id=" . $id . " LIMIT 1");
		$this->upperstorage($id, $good, $str);
		return $str . " " . $this->db->query("SELECT name FROM stu_goods WHERE goods_id=" . $good . " LIMIT 1", 1) . " gesammelt - " . $c . " Energie verbraucht";
	}

	function upperstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count+" . $count . " WHERE ships_id=" . $id . " AND goods_id=" . $good . " LIMIT 1", 6);
		if ($result == 0) $this->db->query("INSERT INTO stu_ships_storage (ships_id,goods_id,count) VALUES ('" . $id . "','" . $good . "','" . $count . "')");
	}

	function lowerstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count-" . $count . " WHERE ships_id=" . $id . " AND goods_id=" . $good . " AND count>" . $count . " LIMIT 1", 6);
		if ($result == 0) {
			// if ($good >=80 && $good < 100) $this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=".$id." LIMIT 1");
			$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=" . $id . " AND goods_id=" . $good . " LIMIT 1");
		}
	}

	function tradesumall($userid)
	{

		$result = $this->db->query("SELECT * FROM stu_trade_offers WHERE user_id=" . $userid);
		$sum = 0;
		while ($data = mysql_fetch_assoc($result)) {
			if ($data['wcount'] > $data['gcount'])	$sum += $data['count'] * $data['wcount'];
			else									$sum += $data['count'] * $data['gcount'];
		}

		$result = $this->db->query("SELECT SUM(count) FROM stu_trade_goods WHERE user_id=" . $userid, 1);

		$sum += $result;

		return $sum;
	}

	function tradesummod($userid)
	{

		$result = $this->db->query("SELECT * FROM stu_trade_offers WHERE user_id=" . $userid);
		$sum = 0;
		while ($data = mysql_fetch_assoc($result)) {

			if (($data['wgoods_id'] > 1000) && ($data['ggoods_id'] < 1000)) {
				$sum += $data['count'] * $data['wcount'];
			} elseif (($data['wgoods_id'] < 1000) && ($data['ggoods_id'] > 1000)) {
				$sum += $data['count'] * $data['gcount'];
			} elseif (($data['wgoods_id'] > 1000) && ($data['ggoods_id'] > 1000)) {
				if ($data['wcount'] > $data['gcount'])	$sum += $data['count'] * $data['wcount'];
				else									$sum += $data['count'] * $data['gcount'];
			}
		}

		$result = $this->db->query("SELECT SUM(count) FROM stu_trade_goods WHERE goods_id > 1000 AND user_id=" . $userid, 1);

		$sum += $result;

		return $sum;
	}






	function inputtradegoods($id, $good, $count)
	{
		$wball = $this->db->query("SELECT value FROM stu_game_vars WHERE var = 'wblimitall'", 1);
		$wbmod = $this->db->query("SELECT value FROM stu_game_vars WHERE var = 'wblimitmod'", 1);

		$myall = $this->tradesumall($this->uid);
		$mymod = $this->tradesummod($this->uid);

		$msg = "";

		$goodname = $this->db->query("SELECT name FROM stu_goods WHERE goods_id = " . $good, 1);

		$c = $this->checkgood($id, $good);

		if ($c < $count) $count = $c;

		$rg = 0;
		if ($good > 1000) {
			if ($mymod + $count > $wbmod) {
				$msg .= "Modulmenge �bersteigt maximale Lagerkapazit�t von " . $wbmod . " - ";
				$rg = 1;
			}
		}
		if ($myall + $count > $wball) {
			if ($rg == 0) $msg .= "Warenmenge �bersteigt maximale Lagerkapazit�t von " . $wball . " - ";
		}

		if ($good > 1000) {
			$volume = max($wbmod - $mymod, 0);
			$volume2 = max($wball - $myall, 0);

			$volume = min($volume, $volume2);
		} else $volume = max($wball - $myall, 0);

		$volume = min($count, $volume);



		if ($volume > 0) {
			$res = $this->db->query("UPDATE stu_trade_goods SET count=count+" . $volume . " WHERE goods_id=" . $good . " AND offer_id=0 AND user_id=" . $this->uid . " LIMIT 1", 6);
			if ($res == 0) $this->db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('" . $this->uid . "','" . $good . "','" . $volume . "',NOW())");

			$msg .= $volume . " " . $goodname . " ins Konto �berwiesen";

			$this->lowerstorage($id, $good, $volume);
		} else $msg .= "keine Waren ins Konto �berwiesen.";

		return $msg;
	}


	function colupperstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_colonies_storage SET count=count+" . $count . " WHERE colonies_id=" . $id . " AND goods_id=" . $good . " LIMIT 1", 6);
		if ($result == 0) $this->db->query("INSERT INTO stu_colonies_storage (colonies_id,goods_id,count) VALUES ('" . $id . "','" . $good . "','" . $count . "')");
	}

	function collowerstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_colonies_storage SET count=count-" . $count . " WHERE colonies_id=" . $id . " AND goods_id=" . $good . " AND count>" . $count . " LIMIT 1", 6);
		if ($result == 0) $this->db->query("DELETE FROM stu_colonies_storage WHERE colonies_id=" . $id . " AND goods_id=" . $good . " LIMIT 1");
	}

	function beamto($target, $good, $count)
	{
		if ($this->id == $target) return;
		$tar = $this->db->query("SELECT a.id,a.rumps_id,a.plans_id,a.name,a.user_id,a.warp,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.schilde_status,a.dock,a.huelle,a.max_huelle,a.is_hp,b.storage,b.slots,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=" . $target . " LIMIT 1", 4);
		if ($tar == 0 || $tar[cloak] == 1) return;
		if ($this->rumps_id == 9) return "Konstrukte k�nnen nicht beamen";
		if ($tar[schilde_status] == 1) return "Das Zielschiff hat die Schilde aktiviert";
		if ($tar['vac_active'] == 1 && $tar['is_hp'] != 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($tar['warp'] == 1) return "Das Zielschiff befindet sich im Warp";
		if ($this->warp == 1) {
			$wmsg = $this->warpoff();
			if ($this->dsships[$this->id] == 1) return $wmsg;
		}
		$result = shipexception(array("schilde_status" => 0, "cloak" => 0, "crew" => $this->min_crew), $this);
		if ($result[code] == 1) {
			$this->stop_trans = 1;
			return $result['msg'];
		}
		if ($this->dock != $target && $tar[dock] != $this->id && ($tar['dock'] != $this->dock || $this->dock == 0)) {
			$result = shipexception(array("nbs" => 1, "eps" => -1), $this);
			if ($result[code] == 1) {
				$this->stop_trans = 1;
				return $result['msg'];
			}
		}
		if (checksector($tar) == 0) return;
		$tast = $this->getshipstoragesum($target);
		if ($tast >= $tar[storage] && $tar[is_hp] != 1) return "Kein Lagerraum auf dem Zielschiff vorhanden";
		if ($tar['is_hp'] == 1  && $tar['user_id'] >= 10 && $tar['user_id'] <= 14 && $this->db->query("SELECT deny_hp FROM stu_npc_contactlist WHERE user_id=" . $tar['user_id'] . " AND recipient=" . $this->uid . " LIMIT 1", 1) == 1) return "Du besitzt keine Handelserlaubnis f�r diesen Posten";
		$mb = $this->beamgood;
		if (($this->dock == 0 || ($this->dock != $tar[dock] && $this->dock != $tar[id])) && ($tar[dock] == 0 || $tar[dock] != $this->id)) $docked = 0;
		else $docked = 1;
		foreach ($good as $key => $value) {
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->checkgood($this->id, $value);
			if ($c == 0) continue;

			if ($tar['is_hp'] == 1) {
				$wmsg .= $this->inputtradegoods($this->id, $value, $count[$key]) . "<br>";

				// $res = $this->db->query("UPDATE stu_trade_goods SET count=count+".$c." WHERE goods_id=".$value." AND offer_id=0 AND user_id=".$this->uid." LIMIT 1",6);
				// if ($res == 0) $this->db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('".$this->uid."','".$value."','".$c."',NOW())");
				// $tar['user_id'] = 3;
			} else {


				if ($this->eps == 0 && $docked == 0) {
					$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
					break;
				}
				// if (($value == 1012 || $value == 1013) && $tar['is_hp'] == 1)
				// {
				// $msg .= "Diese Ware kann nicht zum Handelsposten gebeamt werden<br />";
				// continue;
				// }
				if ($c < $count[$key]) $count[$key] = $c;
				// if ($value >= 80 && $value<95 && $tar[is_hp] != 1)
				// {
				// $lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$tar[id]." AND goods_id>=80 AND goods_id<100",1);
				// if ($lt != $value && $lt != 0)
				// {
				// $return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
				// continue;
				// }
				// $tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON a.m10=b.module_id WHERE a.plans_id=".$tar[plans_id],4);
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
				// $tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$tar[id],1);
				// if ($tc >= $tmc[max_torps])
				// {
				// $return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
				// continue;
				// }
				// if ($tmc[m10] != 9000) $this->db->query("UPDATE stu_ships SET torp_type=".$tt[torp_type]." WHERE id=".$tar[id]);
				// $tc + $count[$key] > $tmc[max_torps] ? $c = $tmc[max_torps]-$tc : $c = $count[$key]; 
				// }
				// elseif ($value >= 110 && $value < 190 && $tar[is_hp] != 1)
				// {
				// if ($shuttle_stop == 1) continue;
				// if ($tar[max_shuttles] == 0 || $tar[is_shuttle] == 1)
				// {
				// $shuttle_stop = 1;
				// $return .= "Dieses Schiff kann keine Shuttles laden<br>";
				// continue;
				// }
				// $shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value." LIMIT 1",4);
				// if ($shud[shuttle_type] > $tar[max_shuttle_type])
				// {
				// $return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
				// continue;
				// }
				// if ($tar[max_shuttles] <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$tar[id]." AND !ISNULL(b.shuttle_type)",1))
				// {
				// $shuttle_stop = 1;
				// $return .= "Die Shuttlerampe ist belegt<br>";
				// continue;
				// }
				// if ($tar[max_cshuttle_type] <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$tar[id]." AND a.goods_id!=".$value." AND !ISNULL(b.shuttle_type)",1))
				// {
				// $return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
				// continue;
				// }
				// $sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<190 AND ships_id=".$tar['id'],1);
				// $sc + $count[$key] > $tar['max_shuttles'] ? $c = $tar['max_shuttles']-$sc : $c = $count[$key];
				// }
				// else 
				$count[$key] > $c ? $c = $c : $c = $count[$key];
				if ($tar['is_hp'] != 1) {
					if ($c > $tar['storage'] - $tast) $c = $tar['storage'] - $tast;
					$tast += $c;
				}
				if ($c <= 0) continue;
				if ($docked == 0) {
					if (ceil($c / $mb) > $this->eps) {
						$c = $this->eps * $mb;
						$this->eps = 0;
					} else $this->eps -= ceil($c / $mb);
				} else $mb = $c;
				$this->lowerstorage($this->id, $value, $c);
				$this->upperstorage($target, $value, $c);
				$msg .= $c . " " . $this->db->query("SELECT name FROM stu_goods WHERE goods_id=" . $value . " LIMIT 1", 1) . "<br>";
				$e += ceil($c / $mb);
				if ($tast >= $tar['storage']) break;
				if ($this->eps == 0 && $docked == 0) break;
			}
		}

		if ($tar['is_hp'] == 1) return $wmsg;

		if (!$e) return ($wmsg ? $wmsg . "<br>" : "") . $return . "Keine Waren zum Beamen vorhanden";
		if ($this->uid != $tar['user_id']) $this->send_pm($this->uid, $tar['user_id'], "<b>Die " . stripslashes($this->name) . " beamt in Sektor " . ($tar['systems_id'] > 0 ? $tar['sx'] . "|" . $tar['sy'] . " (" . $this->m->getsysnamebyid($tar['systems_id']) . "-System)" : $tar['cx'] . "|" . $tar['cy']) . " Waren zur " . stripslashes($tar['name']) . "</b><br>" . $msg, 2);
		else $rad = "->> <a href=?p=" . ($tar['slots'] == 0 ? "ship" : "stat") . "&s=ss&id=" . $tar['id'] . ">Zur " . $tar['name'] . " wechseln</a>";
		if (($this->dock > 0 && ($this->dock == $tar['dock'] || $this->dock == $tar['id'])) ||  $tar['dock'] == $this->id) return "<b>Folgende Waren wurden zur " . stripslashes($tar['name']) . " transportiert</b><br>" . $msg . $rad;
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . " WHERE id=" . $this->id . " LIMIT 1");
		return ($wmsg ? $wmsg . "<br>" : "") . "<b>Es wurden folgende Waren zu der " . $tar['name'] . " gebeamt</b><br>" . $msg . $return . "Energieverbrauch: <b>" . $e . "</b><br>" . $rad;
	}

	function beamfrom($target, $good, $count)
	{
		if ($this->id == $target) return;
		$tar = $this->db->query("SELECT a.id,a.rumps_id,a.plans_id,a.name,a.user_id,a.warp,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.schilde_status,a.dock,a.is_hp,a.huelle,a.max_huelle,b.storage,b.trumfield,b.slots,c.vac_active,c.level FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id LEFT JOIN stu_ships_buildplans as d ON d.plans_id=a.plans_id WHERE a.id=" . $target . " LIMIT 1", 4);
		if (($tar['level'] != "6") && ($tar['user_id'] != $this->uid)) return "Von Spielern unter Level 6 kann nicht gebeamt werden.";
		if ($tar == 0 || $tar['cloak'] == 1) return;
		if (checksector($tar) == 0) return;
		if ($tar['rumps_id'] == 9) return "Von Konstrukten kann nicht gebeamt werden";
		if ($tar['schilde_status'] == 1) return "Das Zielschiff hat die Schilde aktiviert";
		if ($tar['vac_active'] == 1 && $tar['is_hp'] != 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($tar['warp'] == 1) return "Das Zielschiff befindet sich im Warp";
		if ($tar['m10'] == 990) return "Es besteht ein Blockadefeld um den Frachtraum dieses Schiffes";
		if ($this->warp == 1) {
			$wmsg = $this->warpoff();
			if ($this->dsships[$this->id] == 1) return $wmsg;
		}
		$result = shipexception(array("schilde_status" => 0, "cloak" => 0, "crew" => $this->min_crew), $this);
		if ($result['code'] == 1) {
			$this->stop_trans = 1;
			return $result['msg'];
		}
		if ($this->dock != $target && $tar['dock'] != $this->id && ($tar['dock'] != $this->dock || $this->dock == 0)) {
			$result = shipexception(array("nbs" => 1, "eps" => -1), $this);
			if ($result['code'] == 1) {
				$this->stop_trans = 1;
				return $result['msg'];
			}
		}
		$tast = $this->getshipstoragesum($this->id);
		if ($tast >= $this->storage) return "Kein Lagerraum auf dem Schiff vorhanden";
		$mb = $this->beamgood;
		$docked = 0;
		if ($this->dock == $tar['id']) $docked = 1;
		elseif ($this->dock == $tar['dock'] && $this->dock > 0) $docked = 1;
		elseif ($this->id == $tar['dock']) $docked = 1;
		foreach ($good as $key => $value) {
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->checkgood($target, $value);
			if ($c == 0) continue;
			if ($this->eps == 0 && $docked == 0) {
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			if ($count[$key] > $c) $count[$key] = $c;
			// if ($value >= 80 && $value<95)
			// {
			// if ($this->uid != $tar['user_id'] && $tar['is_hp'] != 1) continue;
			// $lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id>=80 AND goods_id<100",1);
			// if ($lt != $value && $lt != 0)
			// {
			// $return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
			// continue;
			// }
			// $tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON a.m10=b.module_id WHERE a.plans_id=".$this->plans_id." LIMIT 1",4);
			// if ($tmc['max_torps'] == 0)
			// {
			// $return .= "Dieses Schiff kann keine Torpedos laden<br>";
			// continue;
			// }
			// $tt = $this->db->query("SELECT torp_type,type FROM stu_torpedo_types WHERE goods_id=".$value,4);
			// if ($tt['type'] > $tmc['torp_type'])
			// {
			// $return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
			// continue;
			// }
			// $tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$this->id,1);
			// if ($tc >= $tmc['max_torps'])
			// {
			// $return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
			// continue;
			// }
			// if ($tmc['m10'] != 9000) $this->db->query("UPDATE stu_ships SET torp_type=".$tt['torp_type']." WHERE id=".$this->id." LIMIT 1");
			// $tc + $count[$key] > $tmc['max_torps'] ? $c = $tmc['max_torps']-$tc : $c = $count[$key]; 
			// }
			// elseif ($value >= 110 && $value < 190)
			// {
			// if ($this->uid != $tar['user_id'] && $tar['is_hp'] != 1 && $tar['user_id'] != 1) continue;
			// if ($shuttle_stop == 1) continue;
			// if ($this->max_shuttles == 0 || $this->is_shuttle == 1)
			// {
			// $shuttle_stop = 1;
			// $return .= "Dieses Schiff kann keine Shuttles laden<br>";
			// continue;
			// }
			// $shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value." LIMIT 1",4);
			// if ($shud['shuttle_type'] > $this->max_shuttle_type)
			// {
			// $return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
			// continue;
			// }
			// if ($this->max_shuttles <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND !ISNULL(b.shuttle_type)",1))
			// {
			// $shuttle_stop = 1;
			// $return .= "Die Shuttlerampe ist belegt<br>";
			// continue;
			// }
			// if ($this->max_cshuttle_type <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND a.goods_id!=".$value." AND !ISNULL(b.shuttle_type)",1))
			// {
			// $return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
			// continue;
			// }
			// $sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<142 AND ships_id=".$this->id,1);
			// $sc + $count[$key] > $this->max_shuttles ? $c = $this->max_shuttles-$sc : $c = $count[$key];
			// }
			// else 
			$count[$key] > $c ? $c = $c : $c = $count[$key];
			if ($c > $this->storage - $tast) $c = $this->storage - $tast;
			if ($c <= 0) continue;
			$tast += $c;
			if ($docked == 0) {
				if (ceil($c / $mb) > $this->eps) {
					$c = $this->eps * $mb;
					$this->eps = 0;
				} else $this->eps -= ceil($c / $mb);
			} else $mb = $c;
			$this->lowerstorage($target, $value, $c);
			$this->upperstorage($this->id, $value, $c);
			$msg .= $c . " " . $this->db->query("SELECT name FROM stu_goods WHERE goods_id=" . $value . " LIMIT 1", 1) . "<br>";
			$e += ceil($c / $mb);
			if ($tast >= $this->storage) break;
			if ($this->eps == 0 && $docked == 0) break;
		}
		if ($tar['is_hp']) $tar['user_id'] = 3;
		if (!$msg) return ($wmsg ? $wmsg . "<br>" : "") . $return . "Keine Waren zum Beamen vorhanden";
		if ($this->uid != $tar['user_id']) $this->send_pm($this->uid, $tar['user_id'], "<b>Die " . stripslashes($this->name) . " beamt in Sektor " . ($tar['systems_id'] > 0 ? $tar['sx'] . "|" . $tar['sy'] . " (" . $this->m->getsysnamebyid($tar['systems_id']) . "-System)" : $tar['cx'] . "|" . $tar['cy']) . " Waren von der " . stripslashes($tar['name']) . "</b><br>" . $msg, 2);
		else $rad = "->> <a href=?p=" . ($tar['slots'] == 0 ? "ship" : "stat") . "&s=ss&id=" . $tar['id'] . ">Zur " . $tar['name'] . " wechseln</a>";
		if ($docked == 1) return "<b>Folgende Waren wurden von der " . $tar['name'] . " transportiert</b><br>" . $msg . $rad;
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . " WHERE id=" . $this->id . " LIMIT 1");
		return ($wmsg ? $wmsg . "<br>" : "") . "<b>Es wurden folgende Waren von der " . $tar['name'] . " gebeamt</b><br>" . $msg . $return . "Energieverbrauch: <b>" . $e . "</b><br>" . $rad;
	}

	function etransfer($target, $count)
	{
		if ($this->id == $target) return;
		$data = $this->db->query("SELECT a.id,a.name,a.user_id,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.schilde_status,a.warp,a.eps,a.max_eps,a.crew,a.min_crew,a.dock,b.slots,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=" . $target . " LIMIT 1", 4);
		if ($data == 0 || checksector($data) == 0 || $data['cloak'] == 1) return;
		// if ($this->is_shuttle == 1) return "Ein Shuttle kann keinen Energietransfer durchf�hren";
		if ($data['schilde_status'] == 1) return "Das Zielschiff hat die Schilde aktiviert";
		if ($this->rumps_id == 9) return "Vom Konstrukten kann keine Energie transferiert werden";
		if ($data['warp'] == 1) return "Das Zielschiff ist im Warp";
		if ($data['eps'] >= $data['max_eps']) return "Das EPS der " . stripslashes($data['name']) . " ist voll" . ($data['user_id'] == $this->uid ? "<br>->> <a href=?p=" . ($data['slots'] == 0 ? "ship" : "stat") . "&s=ss&id=" . $data['id'] . ">Zur " . $data['name'] . " wechseln</a>" : "");
		if ($data['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($this->warp == 1) {
			$wmsg = $this->warpoff();
			if ($this->dsships[$this->id] == 1) return $wmsg;
		}
		$result = shipexception(array("schilde_status" => 0, "eps" => -1, "cloak" => 0, "crew" => $this->min_crew), $this);
		if ($result['code'] == 1) return $result['msg'];
		if ($this->dock != $target && $data['dock'] != $this->id) {
			$result = shipexception(array("nbs" => 1), $this);
			if ($result['code'] == 1) return $result['msg'];
		}
		if ($count == "max") $count = $this->eps;
		else if ($count > $this->eps) $count = $this->eps;
		if ($data['max_eps'] < $count + $data['eps']) $count = $data['max_eps'] - $data['eps'];
		#$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_ships SET eps=eps+" . $count . " WHERE id=" . $target . " LIMIT 1");
		$this->eps -= $count;
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . " WHERE id=" . $this->id . " LIMIT 1");
		#$this->db->query("COMMIT");
		$msg = $count . " Energie zur " . $data['name'] . " transferiert";
		if ($this->uid != $data['user_id']) $this->send_pm($this->uid, $data['user_id'], "<b>Die " . $this->name . " hat in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->getsysnamebyid($data['systems_id']) . " System)" : $data['cx'] . "|" . $data['cy']) . " " . $count . " Energie zur " . $data['name'] . " transferiert</b><br>" . $msg, 3);
		else $rad = "<br>->> <a href=?p=" . ($data['slots'] == 0 ? "ship" : "stat") . "&s=ss&id=" . $data['id'] . ">Zur " . stripslashes($data['name']) . " wechseln</a>";
		return ($wmsg ? $wmsg . "<br>" : "") . $msg . $rad;
	}

	function transfercrew($target, $count, $way)
	{
		if ($this->id == $target) return;
		$data = $this->db->query("SELECT a.id,a.name,a.assigned,a.user_id,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.schilde_status,a.crew,a.max_crew,a.eps,a.max_eps,a.dock FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id=" . $target, 4);
		if ($data == 0 || checksector($data) == 0 || $tar[cloak] == 1 || $this->uid != $data[user_id]) return;
		if ($data[schilde_status] == 1) return "Das Zielschiff hat die Schilde aktiviert";
		if ($way == "fr" && $data[crew] == 0) return "Keine Crew auf der " . $data[name] . " vorhanden";
		if ($way == "to" && $this->crew == 0) return "Keine Crew auf der " . $this->name . " vorhanden";
		if ($data[assigned] != 0) return "Crewtransfer ist mit diesem Schiff zur Zeit nicht m�glich.";
		$result = shipexception(array("schilde_status" => 0, "cloak" => 0, "warpstate" => 0, "crew" => $this->min_crew), $this);
		if ($result[code] == 1) return $result['msg'];
		if ($this->dock != $target && $data[dock] != $this->id) {
			$result = shipexception(array("nbs" => 1, "eps" => -1), $this);
			if ($result[code] == 1) return $result['msg'];
		}
		if ($count == "max") $count = $this->eps;
		$mb = $this->beamcrew;
		if ($way == "fr") {
			if ($this->max_crew <= $this->crew) return "Alle Crewquartiere auf der " . $this->name . " sind belegt";
			if ($count > $data[crew]) $count = $data[crew];
			if ($count > $this->max_crew - $this->crew) $count = $this->max_crew - $this->crew;
			if (($this->dock > 0 && ($this->dock == $data[dock] || $this->dock == $data[id])) || ($data[dock] > 0 && $data[dock] == $this->id)) $e = 0;
			else $e = ceil($count / $mb);
			if ($e > $this->eps) {
				$count = $this->eps * $mb;
				$e = $this->eps;
			}
			$this->eps -= $e;
			$this->db->query("UPDATE stu_ships SET crew=crew-" . $count . " WHERE id=" . $target . " LIMIT 1");
			$this->db->query("UPDATE stu_ships SET crew=crew+" . $count . ",eps=" . $this->eps . " WHERE id=" . $this->id . " LIMIT 1");
			return $count . " Crew von der " . stripslashes($data[name]) . " gebeamt" . ($e > 0 ? " (" . $e . " Energie verbraucht)" : "");
		} elseif ($way == "to") {
			if ($data[max_crew] <= $data[crew]) return "Alle Crewquartiere auf der " . $data[name] . " sind belegt";
			if ($count > $this->crew) $count = $this->crew;
			if ($count > $data[max_crew] - $data[crew]) $count = $data[max_crew] - $data[crew];
			if (($this->dock > 0 && ($this->dock == $data[dock] || $this->dock == $data[id])) || ($data[dock] > 0 && $data[dock] == $this->id)) $e = 0;
			else $e = ceil($count / $mb);
			if ($e > $this->eps) {
				$count = $this->eps * $mb;
				$e = $this->eps;
			}
			$this->eps -= $e;
			$this->db->query("UPDATE stu_ships SET crew=crew+" . $count . " WHERE id=" . $target . " LIMIT 1");
			$this->db->query("UPDATE stu_ships SET crew=crew-" . $count . ",eps=" . $this->eps . " WHERE id=" . $this->id . " LIMIT 1");
			return $count . " Crew zu der " . stripslashes($data[name]) . " gebeamt" . ($e > 0 ? " (" . $e . " Energie verbraucht)" : "");
		}
		return;
	}

	function checkgood($id, $good)
	{
		return $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $id . " AND goods_id=" . $good, 1);
	}

	function getshipstorage($id)
	{
		return $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_ships_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.ships_id=" . $id . " ORDER BY b.sort");
	}

	function getshipstoragesum($id)
	{
		return $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=" . $id, 1);
	}

	function geterzfeld()
	{
		return $this->db->query("SELECT chance_20,chance_21,chance_22,chance_23,chance_24,chance_25 FROM stu_map_values WHERE systems_id=" . $this->systems_id . " AND sx=" . $this->sx . " AND sy=" . $this->sy, 4);
	}

	function collect($id, $count)
	{
		if ($this->map['type'] != 11 && $this->map['type'] != 12) return;
		if ($count < 3 && check_int($count)) return;
		$data = $this->db->query("SELECT a.name,a.schilde_status,a.cloak,a.eps,a.crew,a.min_crew,a.dock,b.erz,b.storage FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.user_id=" . $this->uid . " AND a.id=" . $id . " LIMIT 1", 4);
		if ($data == 0 || $data['erz'] == 0) return;
		$return = shipexception(array("schilde_status" => 0, "cloak" => 0, "eps" => 3, "warpstate" => 0, "dock" => 0, "crew" => $data[min_crew]), $data);
		if ($return['code'] == 1) return $return['msg'];
		if ($count == "max") $count = $data['eps'];
		$count == "max" || $count > $data['eps'] ? $count = $data['eps'] : $count = $count;
		$count = floor($count / 3) * 3;
		$tast = $this->getshipstoragesum($id);
		$taste = $tast;
		if ($tast >= $data['storage']) return "Kein Laderaum auf der " . $data['name'] . " vorhanden";
		$fv = $this->geterzfeld();
		$en = array(20 => "Iridium-Erz", 21 => "Kelbonit-Erz", 22 => "Nitrium-Erz", 23 => "Magnesit-Erz", 24 => "Talgonit-Erz", 25 => "Galazit-Erz");
		$mul = $data[erz];

		$i = 1;
		while ($i <= $count) {
			if ($tast + $mul * $fv['chance_20'] > $data['storage']) {
				$erz[20] += $data['storage'] - $tast;
				$tast = $data['storage'];
				$count = $i;
				break;
			} else {
				$erz[20] += $mul * $fv['chance_20'];
				$tast += $mul * $fv['chance_20'];
			}
			if (($this->sess['race'] == 3 || $this->sess['race'] == 4 || $this->sess['race'] == 5) && $fv['chance_21'] != 0 && $tast < $data['storage']) {
				if ($tast + $mul * $fv['chance_21'] > $data['storage']) {
					$erz[21] += $data['storage'] - $tast;
					$tast = $data['storage'];
					$count = $i;
					break;
				} else {
					$erz[21] += $mul * $fv['chance_21'];
					$tast += $mul * $fv['chance_21'];
				}
			}
			if (($this->sess['race'] == 1 || $this->sess['race'] == 2 || $this->sess['race'] == 4) && $fv['chance_22'] != 0 && $tast < $data['storage']) {
				if ($tast + $mul * $fv['chance_22'] > $data['storage']) {
					$erz[22] = +$data['storage'] - $tast;
					$tast = $data['storage'];
					$count = $i;
					break;
				} else {
					$erz[22] += $mul * $fv['chance_22'];
					$tast += $mul * $fv['chance_22'];
				}
			}
			if (($this->sess['race'] == 1 || $this->sess['race'] == 3 || $this->sess['race'] == 4) && $fv['chance_23'] != 0 && $tast < $data['storage']) {
				if ($tast + $mul * $fv['chance_23'] > $data['storage']) {
					$erz[23] += $data['storage'] - $tast;
					$tast = $data['storage'];
					$count = $i;
					break;
				} else {
					$erz[23] += $mul * $fv['chance_23'];
					$tast += $mul * $fv['chance_23'];
				}
			}
			if (($this->sess['race'] == 1 || $this->sess['race'] == 2 || $this->sess['race'] == 5) && $fv['chance_24'] != 0 && $tast < $data['storage']) {
				if ($tast + $fv['chance_24'] > $data['storage']) {
					$erz[24] += $data['storage'] - $tast;
					$tast = $data['storage'];
					$count = $i;
					break;
				} else {
					$erz[24] += $mul * $fv['chance_24'];
					$tast += $mul * $fv['chance_24'];
				}
			}
			if (($this->sess['race'] == 2 || $this->sess['race'] == 3 || $this->sess['race'] == 5) && $fv['chance_25'] != 0 && $tast < $data['storage']) {
				if ($tast + $mul * $fv['chance_25'] > $data['storage']) {
					$erz[25] += $data['storage'] - $tast;
					$tast = $data['storage'];
					$count = $i;
					break;
				} else {
					$erz[25] += $mul * $fv['chance_25'];
					$tast += $mul * $fv['chance_25'];
				}
			}
			$i++;
		}
		$msg = "Es wurden folgende Erze gesammelt:";
		foreach ($erz as $key => $value) {
			$vl = round($value);
			if ($vl == 0) continue;
			if ($taste + $vl > $data['storage']) $vl = $data['storage'] - $taste;
			$this->upperstorage($id, $key, $vl);
			$taste += $vl;
			$msg .= "<br>" . round($value) . " " . $en[$key];
			if ($taste >= $data['storage']) break;
		}
		$this->db->query("UPDATE stu_ships SET eps=eps-" . $count . " WHERE id=" . $id . " LIMIT 1");
		return $msg . "<br><b>Energieverbrauch: " . $count . "</b>";
	}

	function getcolstorage($id)
	{
		return $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_colonies_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=" . $id . " ORDER BY b.goods_id");
	}

	function getcolstoragesum($id)
	{
		return $this->db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=" . $id, 1);
	}

	function beamtocol($target, $good, $count)
	{
		$tar = $this->db->query("SELECT a.id,a.name,a.user_id,a.sx,a.sy,a.systems_id,a.schilde_status,a.max_storage,b.vac_active FROM stu_colonies as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.id=" . $target . " LIMIT 1", 4);
		if ($tar == 0) return;
		if (checksector($tar) == 0) return;
		if ($this->rumps_id == 9) return "Konstrukte k�nnen nicht beamen";
		if ($tar['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($tar[schilde_status] == 1 && $tar[user_id] != $this->uid) return "Die Kolonie hat die Schilde aktiviert";
		if ($tar[user_id] == 1) return "Zu dieser Kolonie kann nicht gebeamt werden";
		$blockade = $this->db->query("SELECT * FROM stu_colonies_actions WHERE colonies_id =" . $target . " AND (var='fdef' OR var='fblock' OR var='fattack')", 4);
		if ($blockade != 0) {
			$blocked = $this->db->query("SELECT * FROM stu_colonies WHERE colonies_id =" . $target . " LIMIT 1", 4);
			if ($blocked[user_id] != $this->uid) {
				if ($blockade['var'] == "fblock") return "Die blockierende Flotte " . $blocker['name'] . " verhindert s�mtliche Transportversuche.";
				if (($blockade['var'] == "fdef") && ($this->uid != $tar[user_id])) {
					$ally1 = $this->db->query("SELECT allys_id FROm stu_user WHERE id = " . $this->uid . "", 1);
					$ally2 = $this->db->query("SELECT allys_id FROm stu_user WHERE id = " . $tar[user_id] . "", 1);
					if ((($ally1 > 0) || ($ally2 > 0)) && ($ally1 != $ally2)) return "Die verteidigende Flotte " . $blocker['name'] . " verhindert s�mtliche Transportversuche.";
				}
				if ($blockade['var'] == "fattack") return "Die angreifende Flotte " . $blocker['name'] . " verhindert s�mtliche Transportversuche.";
			}
		}

		$return = shipexception(array("nbs" => 1, "schilde_status" => 0, "cloak" => 0, "eps" => -1, "warpstate" => 0, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) {
			$this->stop_trans = 1;
			return $return['msg'];
		}
		$tast = $this->getcolstoragesum($target);
		if ($tast >= $tar[max_storage]) return "Kein Lagerraum auf der Kolonie vorhanden";
		$mb = $this->beamgood;
		foreach ($good as $key => $value) {
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->checkgood($this->id, $value);
			if ($c == 0) continue;
			if ($this->eps == 0) {
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			$c < $count[$key] ? $c = $c : $c = $count[$key];
			if ($c > $tar[max_storage] - $tast) $c = $tar[max_storage] - $tast;
			if ($c <= 0) continue;
			if (ceil($c / $mb) > $this->eps) {
				$c = $this->eps * $mb;
				$this->eps = 0;
			} else $this->eps -= ceil($c / $mb);
			#$this->db->query("START TRANSACTION");
			$this->lowerstorage($this->id, $value, $c);
			$this->colupperstorage($target, $value, $c);
			#$this->db->query("COMMIT");
			$msg .= $c . " " . $this->db->query("SELECT name FROM stu_goods WHERE goods_id=" . $value, 1) . "<br>";
			$e += ceil($c / $mb);
			$tast += $c;
			if ($tast >= $tar[max_storage]) break;
			if ($this->eps == 0) break;
		}
		if (!$msg) return "Es wurden keine Waren gebeamt";
		if ($this->uid != $tar[user_id]) $this->send_pm($this->uid, $tar[user_id], "<b>Die " . stripslashes($this->name) . " beamt Waren zur Kolonie " . stripslashes($tar[name]) . "</b><br>" . $msg, 2);
		else $al = "<br>->> <a href=?p=colony&s=sc&id=" . $tar['id'] . "&shd=" . $this->id . ">Zur Kolonie wechseln</a>";
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . " WHERE id=" . $this->id);
		return "<b>Es wurden folgende Waren zu der Kolonie " . $tar[name] . " gebeamt</b><br>" . $msg . "Energieverbrauch: <b>" . $e . "</b>" . $al;
	}

	function beamfromcol($target, $good, $count)
	{
		$tar = $this->db->query("SELECT a.id,a.name,a.user_id,a.sx,a.sy,a.systems_id,a.schilde_status,b.vac_active,b.level,a.beamblock FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=" . $target, 4);
		if ($tar == 0) return;
		if (checksector($tar) == 0) return;
		$blockade = $this->db->query("SELECT * FROM stu_colonies_actions WHERE colonies_id =" . $target . " AND (var='fdef' OR var='fblock' OR var='fattack')", 4);
		if ($blockade != 0) {
			$blocked = $this->db->query("SELECT * FROM stu_colonies WHERE colonies_id =" . $target . " LIMIT 1", 4);
			if ($blocked[user_id] != $this->uid) {
				if ($blockade['var'] == "fblock") return "Die blockierende Flotte " . $blocker['name'] . " verhindert s�mtliche Transportversuche.";
				if (($blockade['var'] == "fdef") && ($this->uid != $tar[user_id])) {
					$ally1 = $this->db->query("SELECT allys_id FROm stu_user WHERE id = " . $this->uid . "", 1);
					$ally2 = $this->db->query("SELECT allys_id FROm stu_user WHERE id = " . $tar[user_id] . "", 1);
					if ((($ally1 > 0) || ($ally2 > 0)) && ($ally1 != $ally2)) return "Die verteidigende Flotte " . $blocker['name'] . " verhindert s�mtliche Transportversuche.";
				}
				if ($blockade['var'] == "fattack") return "Die angreifende Flotte " . $blocker['name'] . " verhindert s�mtliche Transportversuche.";
			}
		}


		if ($tar['user_id'] != $this->uid) return "Ich blocke das Klauen mal. Kenn euch doch.";



		if (($tar['level'] != "6") && ($tar['user_id'] != $this->uid)) return "Von Spielern unter Level 6 kann nicht gebeamt werden.";
		if ($tar[schilde_status] == 1 && $tar[user_id] != $this->uid) return "Die Kolonie hat die Schilde aktiviert";
		if ($tar[beamblock] == 1 && $tar[user_id] != $this->uid) return "Ein Blockadefeld verhindert das Beamen";
		if ($tar[vac_active] == 1) {
			if (!(($blockade != 0) && (($blockade['var'] == "fblock") || ($blockade['var'] == "fattack")))) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		}
		if ($this->sess[level] < 2) return "Du kannst erst ab Level 2 von Kolonien beamen";
		$result = shipexception(array("nbs" => 1, "schilde_status" => 0, "cloak" => 0, "eps" => -1, "warpstate" => 0, "crew" => $this->min_crew), $this);
		if ($result[code] == 1) {
			$this->stop_trans = 1;
			return $result['msg'];
		}
		if ($tar['user_id'] != $this->uid && $this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=107 AND aktiv=1 AND colonies_id=" . $target, 1) > 0) return "Die Oberfl�che der Kolonie ist nicht scanbar";
		$tast = $this->getshipstoragesum($this->id);
		if ($tast >= $this->storage) return "Kein Lagerraum auf dem Schiff vorhanden";
		$mb = $this->beamgood;
		foreach ($good as $key => $value) {
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=" . $value . " AND colonies_id=" . $target, 1);
			if ($c == 0) continue;
			if ($this->eps == 0) {
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			if ($count[$key] > $c) $count[$key] = $c;
			// if ($value >= 80 && $value<90)
			// {
			// if ($this->uid != $tar[user_id]) continue;
			// $lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id>=80 AND goods_id<100",1);
			// if ($lt != $value && $lt != 0)
			// {
			// $return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
			// continue;
			// }
			// $tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON a.m10=b.module_id WHERE a.plans_id=".$this->plans_id,4);
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
			// $tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$this->id,1);
			// if ($tc >= $tmc[max_torps])
			// {
			// $return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
			// continue;
			// }
			// if ($tmc[m10] != 9000) $this->db->query("UPDATE stu_ships SET torp_type=".$tt[torp_type]." WHERE id=".$this->id);
			// $tc + $count[$key] > $tmc[max_torps] ? $c = $tmc[max_torps]-$tc : $c = $count[$key]; 
			// }
			// elseif ($value >= 110 && $value < 190)
			// {
			// if ($this->uid != $tar[user_id]) continue;
			// if ($shuttle_stop == 1) continue;
			// if ($this->max_shuttles == 0 || $this->is_shuttle == 1)
			// {
			// $shuttle_stop = 1;
			// $return .= "Dieses Schiff kann keine Shuttles laden<br>";
			// continue;
			// }
			// $shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value,4);
			// if ($shud[shuttle_type] > $this->max_shuttle_type)
			// {
			// $return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
			// continue;
			// }
			// if ($this->max_shuttles <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND !ISNULL(b.shuttle_type)",1))
			// {
			// $shuttle_stop = 1;
			// $return .= "Die Shuttlerampe ist belegt<br>";
			// continue;
			// }
			// if ($this->max_cshuttle_type <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND b.goods_id!=".$value." AND !ISNULL(b.shuttle_type)",1))
			// {
			// $return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
			// continue;
			// }
			// $sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<142 AND ships_id=".$this->id,1);
			// $sc + $count[$key] > $this->max_shuttles ? $c = $this->max_shuttles-$sc : $c = $count[$key];
			// }
			// else 
			$count[$key] > $c ? $c = $c : $c = $count[$key];
			if ($c > $this->storage - $tast) $c = $this->storage - $tast;
			if ($c <= 0) continue;
			if (ceil($c / $mb) > $this->eps) {
				$c = $this->eps * $mb;
				$this->eps = 0;
			} else $this->eps -= ceil($c / $mb);
			#$this->db->query("START TRANSACTION");
			$this->collowerstorage($target, $value, $c);
			$this->upperstorage($this->id, $value, $c);
			#$this->db->query("COMMIT");
			$msg .= $c . " " . $this->db->query("SELECT name FROM stu_goods WHERE goods_id=" . $value, 1) . "<br>";
			$e += ceil($c / $mb);
			$tast += $c;
			if ($tast >= $this->storage) break;
			if ($this->eps == 0) break;
		}
		if (!$msg) return $return . "Es wurden keine Waren gebeamt";
		if ($this->uid != $tar[user_id]) $this->send_pm($this->uid, $tar[user_id], "<b>Die " . stripslashes($this->name) . " beamt Waren von der Kolonie " . stripslashes($tar[name]) . "</b><br>" . $msg, 2);
		else $al = "<br>->> <a href=?p=colony&s=sc&id=" . $tar['id'] . "&shd=" . $this->id . ">Zur Kolonie wechseln</a>";
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . " WHERE id=" . $this->id);
		return "<b>Es wurden folgende Waren von der Kolonie " . $tar[name] . " gebeamt</b><br>" . $msg . $return . "Energieverbrauch: <b>" . $e . "</b>" . $al;
	}

	function transfercrewcol($target, $count, $way)
	{
		$data = $this->db->query("SELECT id,name,user_id,sx,sy,systems_id,bev_free,bev_work,bev_max,schilde_status FROM stu_colonies WHERE id=" . $target, 4);
		if ($data == 0 || checksector($data) == 0  || $this->uid != $data[user_id]) return;
		if ($data[schilde_status] == 1 && $data[user_id] != $this->uid) return "Die Kolonie hat die Schilde aktiviert";
		if ($way == "fr" && $data[bev_free] == 0) return "Keine Crew auf der Kolonie " . $data[name] . " vorhanden";
		if ($way == "to" && $this->crew == 0) return "Keine Crew auf der " . $this->name . " vorhanden";
		$return = shipexception(array("schilde_status" => 0, "cloak" => 0, "nbs" => 1, "eps" => -1, "warpstate" => 0, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($count == "max") $count = $this->eps;
		$al = "<br>->> <a href=?p=colony&s=sc&id=" . $data['id'] . "&shd=" . $this->id . ">Zur Kolonie wechseln</a>";
		if ($way == "fr") {
			if ($this->max_crew <= $this->crew) return "Alle Crewquartiere auf der " . $this->name . " sind belegt";
			if ($count > $data[bev_free]) $count = $data[bev_free];
			if ($count > $this->max_crew - $this->crew) $count = $this->max_crew - $this->crew;
			$e = ceil($count / 5);
			if ($e > $this->eps) {
				$count = $this->eps * 5;
				$e = $this->eps;
			}
			$this->eps -= $e;
			#$this->db->query("START TRANSACTION");
			$this->db->query("UPDATE stu_colonies SET bev_free=bev_free-" . $count . " WHERE id=" . $target);
			$this->db->query("UPDATE stu_ships SET crew=crew+" . $count . ",eps=" . $this->eps . " WHERE id=" . $this->id);
			#$this->db->query("COMMIT");
			return $count . " Crew von der Kolonie " . stripslashes($data[name]) . " gebeamt (" . $e . " Energie verbraucht)" . $al;
		} elseif ($way == "to") {
			if ($data[bev_work] + $data[bev_free] >= $data[bev_max]) return "Kein Wohnraum auf der Kolonie " . $data[name] . " vorhanden";
			if ($count > $this->crew) $count = $this->crew;
			if ($count > $data[bev_max] - $data[bev_free] - $data[bev_work]) $count = $data[bev_max] - $data[bev_free] - $data[bev_work];
			$e = ceil($count / 5);
			if ($e > $this->eps) {
				$count = $this->eps * 5;
				$e = $this->eps;
			}
			$this->eps -= $e;
			#$this->db->query("START TRANSACTION");
			$this->db->query("UPDATE stu_colonies SET bev_free=bev_free+" . $count . " WHERE id=" . $target);
			$this->db->query("UPDATE stu_ships SET crew=crew-" . $count . ",eps=" . $this->eps . " WHERE id=" . $this->id);
			#$this->db->query("COMMIT");
			return $count . " Crew zu der Kolonie " . stripslashes($data[name]) . " gebeamt (" . $e . " Energie verbraucht)" . $al;
		}
		return;
	}

	function loadshields($count)
	{
		if ($this->checksubsystem(4, $this->id) == 1) return "Die Reparatur an den Schilden ist noch nicht abgeschlossen";
		$return = shipexception(array("schilde_load" => $this->max_schilde, "schilde_status" => 0, "cloak" => 0, "eps" => -1, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($count == "max" || $count > $this->eps) $count = $this->eps;
		if ($count > $this->max_schilde - $this->schilde) $count = $this->max_schilde - $this->schilde;
		$ecount = ceil($count);
		$timedata = $this->db->query("SELECT schilde_status as ss FROM stu_ships WHERE id=" . $this->id, 4);
		if ($timedata['ss'] > 1) $newtime = $timedata['ss'];
		else $newtime = time();
		$newtime += 30 * $ecount;
		$this->db->query("UPDATE stu_ships SET schilde=schilde+" . $count . ",eps=eps-" . $ecount . ",schilde_status=" . $newtime . " WHERE id=" . $this->id);
		$this->eps -= $ecount;
		$this->schilde += $count;
		return "Die Schilde wurden um " . $count . " Einheiten aufgeladen";
	}

	function etransferc($target, $count)
	{
		$data = $this->db->query("SELECT a.id,a.sx,a.sy,a.systems_id,a.user_id,a.name,a.eps,a.max_eps,a.schilde_status,b.vac_active FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=" . $target, 4);
		if ($data == 0 || checksector($data) == 0) return;
		if ($data['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($data['schilde_status'] == 1 && $data['user_id'] != $this->uid) return "Die Kolonie " . $data['name'] . " hat die Schilde aktiviert";
		if ($data['user_id'] == 1) return "Zu dieser Kolonie kann keine Energie transferiert werden";
		if ($this->is_shuttle == 1) return "Ein Shuttle kann keinen Energietransfer durchf�hren";
		if ($data['eps'] >= $data['max_eps']) return "Das EPS der Kolonie " . $data[name] . " ist voll";
		$return = shipexception(array("nbs" => 1, "schilde_status" => 0, "cloak" => 0, "eps" => -1, "warpstate" => 0, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($count == "max") $count = $this->eps;
		else if ($count > $this->eps) $count = $this->eps;
		if ($data['max_eps'] < $count + $data['eps']) $count = $data['max_eps'] - $data['eps'];
		$this->db->query("UPDATE stu_colonies SET eps=eps+" . $count . " WHERE id=" . $target . " LIMIT 1");
		$this->eps -= $count;
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . " WHERE id=" . $this->id . " LIMIT 1");
		$msg = $count . " Energie zur Kolonie " . $data['name'] . " transferiert";
		if ($this->uid != $data['user_id']) $this->send_pm($this->uid, $data['user_id'], "<b>Die " . stripslashes($this->name) . " hat " . $count . " Energie zur Kolonie " . stripslashes($data['name']) . " transferiert</b><br>", 4);
		else $al = "<br>->> <a href=?p=colony&s=sc&id=" . $data['id'] . "&shd=" . $this->id . ">Zur Kolonie wechseln</a>";
		return $msg . $al;
	}

	function changename($nn)
	{
		$nn = addslashes(format_string($nn));
		if (strlen($nn) > 255) $nn = strip_tags($nn);
		if (!check_html_tags($nn)) $nn = strip_tags($nn);
		$filter = new InputFilter(array("font", "b", "i"), array("color"), 0, 0);
		$nn = $filter->process($nn);
		$nn = str_replace("\"", "", $nn);
		if (strlen(trim($nn)) < 1) return "Fehler";
		$this->db->query("UPDATE stu_ships SET name='" . $nn . "' WHERE id=" . $this->id . " LIMIT 1");
		$this->db->query("UPDATE stu_ships_logdata SET name='" . $nn . "' WHERE ships_id=" . $this->id . " LIMIT 1");
		$this->name = $nn;
		return "Schiffsname ge�ndert in " . stripslashes($nn);
	}

	function activatetraktor($target)
	{
		if ($this->id == $target) return;
		// $return = shipexception(array("nbs" => 1,"traktor" => 0,"schilde_status" => 0,"dock" => 0,"cloak" => 0,"eps" => 2,"warpstate" => 0,"crew" => $this->min_crew),$this);
		$return = shipexception(array("nbs" => 1, "traktor" => 0, "schilde_status" => 0, "dock" => 0, "cloak" => 0, "eps" => 2, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->checksubsystem(8, $this->id) == 1) return "Das EPS-System ist besch�digt - Traktorstrahl nicht aktivierbar";
		$data = $this->db->query("SELECT a.id,a.name,a.user_id,a.rumps_id,a.fleets_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.eps,a.schilde_status,a.schilde,a.warp,a.alvl,a.dock,a.traktor,a.traktormode,a.crew,a.min_crew,a.maintain,b.trumfield,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=" . $target . " LIMIT 1", 4);
		if ($data == 0 || $data['cloak'] == 1 || checksector($data) == 0) return;
		if ($data['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($data['trumfield'] == 1 || $data[probe] == 1) return "Ziel kann nicht erfasst werden";
		if ($data['fleets_id'] > 0) return "Schiffe in einer Flotte k�nnen nicht erfasst werden";
		if ($this->fleets_id > 0) return "Schiffe in einer Flotte k�nnen keinen Traktorstrahl aktivieren";
		if ($data['maintain'] > 0) return "Ziel kann nicht erfasst werden";
		// if ($data['rumps_id'] == 1912) return "Ziel kann nicht erfasst werden";
		// if ($data['rumps_id'] == 4000) return "Ziel ist zu massiv um erfasst zu werden";
		if ($data['slots'] > 0) return "Station kann nicht erfasst werden";
		// if ($data['warp'] == 1 && $data['user_id'] != $this->uid) return "Fremde Schiffe im Warp k�nnen nicht abgeschleppt werden";
		if ($data['rumps_id'] == 999) return "Der Weihnachtsmann verbietet das erfassen von T�rchen";
		if ($data['dock'] > 0) return "Das Zielschiff ist angedockt und kann nicht erfasst werden";
		if ($data['schilde_status'] == 1) return "Die " . stripslashes($data[name]) . " hat die Schilde aktiviert";
		if ($data['traktormode'] == 2) return "Die " . stripslashes($data[name]) . " wird bereits von einem Traktorstrahl gehalten";
		if ($data['traktor'] > 0) return "Der Traktorstrahl der " . stripslashes($data[name]) . " ist aktiviert";

		$targetsize =  $this->db->query("SELECT b.type FROM stu_ships as a LEFT JOIN stu_rumps as b on a.rumps_id = b.rumps_id WHERE a.id = " . $target . " LIMIT 1;", 1);

		$thissize = ($this->rtype == 0 ? 2 : $this->rtype);
		$targetsize = ($targetsize == 0 ? 2 : $targetsize);

		if ($targetsize > $thissize) return "Gr��ere Schiffe k�nnen nicht geschleppt werden.";

		if ($this->systems_id > 0) $sysname = $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $data['systems_id'] . " LIMIT 1", 1);
		if ($this->uid != $data['user_id'] && $data['maintain'] == 0 && ($data['alvl'] > 1 || $data['fleets_id'] > 0) && $data['crew'] > $data['min_crew'] && $data['eps'] > 1 && $data['schilde'] > 0 && ($data['schilde_status'] < 1 || $data['schilde_status'] < time()) && $this->checksubsystem(4, $data['id']) != 1) {
			$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff hat in Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy . " (" . $sysname . "-System)" : $this->cx . "|" . $this->cy) . " versucht, die " . $data['name'] . " mit einem Traktorstrahl zu erfassen", "type" => 3);
			$this->logbook[$data['id']][] = array("user_id" => $data['user_id'], "text" => "Die " . $data['name'] . " hat in Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy . " (" . $sysname . "-System)" : $this->cx . "|" . $this->cy) . " versucht, das Schiff mit einem Traktorstrahl zu erfassen", "type" => 3);
			$this->db->query("UPDATE stu_ships SET schilde_status=1,eps=eps-1,still=0 WHERE id=" . $target);
			$this->db->query("UPDATE stu_ships SET eps=eps-2 WHERE id=" . $this->id);
			$this->send_pm($this->uid, $data['user_id'], "Die " . $this->name . " hat versucht, die " . $data['name'] . " mit dem Traktorstrahl zu erfassen", 3);
			return "Die " . $data['name'] . " aktiviert die Schilde - Kann Ziel nicht erfassen";
		}
		$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff hat in Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy . " (" . $sysname . "-System)" : $this->cx . "|" . $this->cy) . " die " . $data['name'] . " mit einem Traktorstrahl erfasst", "type" => 3);
		$this->logbook[$data['id']][] = array("user_id" => $data['user_id'], "text" => "Die " . $data['name'] . " hat in Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy . " (" . $sysname . "-System)" : $this->cx . "|" . $this->cy) . " das Schiff mit einem Traktorstrahl erfasst", "type" => 3);
		$this->db->query("UPDATE stu_ships SET traktor=" . $this->id . ",traktormode=2 WHERE id=" . $target);
		$this->db->query("UPDATE stu_ships SET traktor=" . $target . ",traktormode=1,eps=eps-2 WHERE id=" . $this->id);
		$this->eps -= 2;
		if ($this->uid != $data['user_id']) $this->send_pm($this->uid, $data['user_id'], "Die " . $data['name'] . " wurde in Sektor " . ($data['systems_id'] == 0 ? $data['cx'] . "|" . $data['cy'] : $data['sx'] . "|" . $data['sy'] . " (" . $sysname . "-System)") . " vom Traktorstrahl der " . $this->name . " erfasst", 3);
		return "Traktorstrahl auf die " . $data['name'] . " gerichtet";
	}

	function deactivatetraktor()
	{
		if ($this->traktormode != 1) return;
		$data = $this->db->query("SELECT id,user_id,name,systems_id FROM stu_ships WHERE id=" . $this->traktor . " LIMIT 1", 4);
		if ($data['user_id'] != $this->uid) $this->send_pm($this->uid, $data['user_id'], "Die " . $this->name . " hat den auf die " . $data['name'] . " gerichteten Traktorstrahl in Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy . " (" . $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $this->systems_id . " LIMIT 1", 1) . "-System)" : $this->cx . "|" . $this->cy) . " deaktiviert", 3);
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=" . $this->id . " OR id=" . $this->traktor . " LIMIT 2");
		if ($this->systems_id > 0) $sysname = $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $data['systems_id'] . " LIMIT 1", 1);
		$this->logbook[$this->id][] = array("user_id" => $this->uid, "text" => "Das Schiff hat in Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy . " (" . $sysname . "-System)" : $this->cx . "|" . $this->cy) . " den auf die " . $data['name'] . " gerichteten Traktorstrahl deaktiviert", "type" => 3);
		$this->logbook[$data['id']][] = array("user_id" => $data['user_id'], "text" => "Die " . $data['name'] . " hat in Sektor " . ($this->systems_id > 0 ? $this->sx . "|" . $this->sy . " (" . $sysname . "-System)" : $this->cx . "|" . $this->cy) . " den auf das Schiff gerichteten Traktorstrahl deaktiviert", "type" => 3);
		$this->traktor = 0;
		$this->traktormode = 0;
		return "Der Traktorstrahl wurde deaktiviert";
	}

	function changealvl($lvl)
	{
		if ($lvl < 1 || $lvl > 3) return;
		if ($this->alvl == $lvl) return;
		if ($lvl == 3) return "Alarm Rot zur Zeit nicht m�glich. Weil ich nicht weiss, welche alten Codefetzen noch aktiv sind.";
		if ($this->crew < $this->min_crew) return "Es werden " . $this->min_crew . " Crewmitglieder ben�tigt";
		$this->db->query("UPDATE stu_ships SET alvl=" . $lvl . " WHERE id=" . $this->id . " LIMIT 1");
		if ($lvl == 2) $sa = "<br>" . $this->av("nbs");
		if ($lvl == 3) {
			$msg = "<br>" . $this->av("sh");
			$msg .= "<br>" . $this->av("nbs");
			$msg .= "<br>" . $this->av("wp");
			$msg .= "<br>" . $this->av("wt");
		}
		return "Alarmstufe wurde ge�ndert" . $msg;
	}

	function getweapon(&$wmod)
	{
		return $this->db->query("SELECT name,wtype,eps_cost,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=" . $wmod . " LIMIT 1", 4);
	}



	function getShipData($id)
	{
		// return $this->db->query("SELECT a.*,b.name as cname,b.beamgood,b.beamcrew,b.storage,b.reaktor as creaktor,d.ships_id as fsf,d.name as fname, c.m1 as mod_m1,c.m2 as mod_m2,c.m3 as mod_m3,c.m4 as mod_m4,c.m5 as mod_m5,c.w1 as mod_w1,c.w2 as mod_w2, c.s1 as mod_s1, c.s2 as mod_s2 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id, LEFT JOIN stu_ships_decloaked as c ON a.id=c.ships_id AND (c.user_id=".$this->sess['uid']." || c.user_id=0) WHERE a.id=".$id." LIMIT 1",4);
		// return $this->db->query("SELECT a.*,b.name as cname,b.beamgood,b.beamcrew,b.storage,b.reaktor as creaktor,d.ships_id as fsf,d.name as fname, c.m1 as mod_m1,c.m2 as mod_m2,c.m3 as mod_m3,c.m4 as mod_m4,c.m5 as mod_m5,c.w1 as mod_w1,c.w2 as mod_w2, c.s1 as mod_s1, c.s2 as mod_s2, NOT(ISNULL(f.ships_id)) as decloaked FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id LEFT JOIN stu_ships_decloaked as f ON a.id=f.ships_id AND (f.user_id=".$this->sess['uid']." || f.user_id=0) WHERE a.id=".$id." LIMIT 1",4);

		$shipDb = $this->db->query("SELECT a.*,b.name as cname,b.beamgood,b.beamcrew,b.storage,b.reaktor as creaktor,d.ships_id as fsf,d.name as fname, c.m1 as mod_m1,c.m2 as mod_m2,c.m3 as mod_m3,c.m4 as mod_m4,c.m5 as mod_m5,c.w1 as mod_w1,c.w2 as mod_w2, c.s1 as mod_s1, c.s2 as mod_s2, NOT(ISNULL(f.ships_id)) as decloaked FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id LEFT JOIN stu_ships_decloaked as f ON a.id=f.ships_id AND (f.user_id=" . $this->sess['uid'] . " || f.user_id=0) WHERE a.id=" . $id . " LIMIT 1", 4);

		$planDb = getShipValuesForBuildplan($shipDb['plans_id']);

		$res = array();
		foreach ($shipDb as $k => $v) {
			$res[$k] = $v;
		}
		$res['planvals'] = $planDb;
		return $res;
	}
	function getAttackerList($shipId, $cloaked)
	{
		$fleetId = $this->db->query("SELECT fleets_id FROM stu_ships WHERE id = " . $shipId . " LIMIT 1;", 1);

		$fleet = array();
		if ($fleetId > 0) {
			$res = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id = " . $fleetId . " AND id != " . $shipId . " ");
			while ($id = mysql_fetch_assoc($res)) {
				$ship = $this->getShipData($id['id']);
				if ($ship['cloak'] == 1) {
					if ($cloaked == 1) {
						array_push($fleet, $ship);
					}
				} else {
					if ($cloaked != 1) {
						array_push($fleet, $ship);
					}
				}
			}
		}
		return $fleet;
	}
	function getDefenderList($shipId)
	{
		$fleetId = $this->db->query("SELECT fleets_id FROM stu_ships WHERE id = " . $shipId . " LIMIT 1;", 1);

		$fleet = array();
		if ($fleetId > 0) {
			$res = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id = " . $fleetId . "");
			while ($id = mysql_fetch_assoc($res)) {
				$ship = $this->getShipData($id['id']);
				array_push($fleet, $ship);
			}
		} else {
			array_push($fleet, $this->getShipData($shipId));
		}

		return $fleet;
	}
	function getTargetList($shipId, $userId)
	{
		$fleetId = $this->db->query("SELECT fleets_id FROM stu_ships WHERE id = " . $shipId . " LIMIT 1;", 1);

		$fleet = array();
		if ($fleetId > 0) {
			$res = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id = " . $fleetId . "");
			while ($id = mysql_fetch_assoc($res)) {
				$ship = $this->getShipData($id['id']);
				if ($ship['cloak'] == 1) {
					$decloaked = $this->db->query("SELECT ships_id FROM stu_ships_decloaked WHERE ships_id = " . $id['id'] . " AND (user_id = " . $userId . " OR user_id = 0) LIMIT 1;", 1);
					if (!$decloaked) {
						continue;
					}
				}

				array_push($fleet, $ship);
			}
		} else {
			$ship = $this->getShipData($shipId);
			if ($ship['cloak'] == 1) {

				$decloaked = $this->db->query("SELECT ships_id FROM stu_ships_decloaked WHERE ships_id = " . $shipId . " AND (user_id = " . $userId . " OR user_id = 0) LIMIT 1;", 1);
				if ($decloaked) {
					array_push($fleet, $ship);
				}
			} else array_push($fleet, $ship);
		}

		return $fleet;
	}
	function getWeaponData($id, $ship = 0)
	{
		$data = $this->db->query("SELECT * FROM stu_weapons WHERE module_id = " . $id . "", 4);
		if (!$data) return 0;
		$data['ammo'] = array();
		if ($data['wtype'] == "torpedo") {

			$ammotype = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id = " . $ship . " AND (goods_id = 81 OR goods_id = 82) LIMIT 1;", 1);

			$data['ammo'] = $this->db->query("SELECT * FROM stu_torpedo_types WHERE torp_type = " . $ammotype . " LIMIT 1;", 4);
		}
		return $data;
	}


	// NEW ATTACK FUNCTIONS

	function removeFromShipList($list, $shipid)
	{
		$res = array();
		foreach ($list as $l) {
			if ($l['id'] != $shipid) array_push($res, $l);
		}
		return $res;
	}
	function debugIds($list)
	{
		$s = "";
		foreach ($list as $o) {
			$s .= $o['id'] . " ";
		}
		return $s;
	}

	function fleetBattle($targetShip)
	{

		if ($this->fleets_id > 0) {
			if ($this->db->query("SELECT fleets_id FROM stu_ships WHERE id = " . $targetShip, 1) == $this->fleets_id) return "Kann kein Schiff derselben Flotte angreifen.";
		}

		$res = "";
		$initiator = $this->getShipData($this->id);
		$attackers = $this->getAttackerList($this->id, 0);
		$prioAttackers = $this->getAttackerList($this->id, 1);
		$defenders = $this->getDefenderList($targetShip);
		$destroyed = array();


		$attackTargets = $this->getTargetList($targetShip, $this->uid);
		$defendTargets = $this->getTargetList($this->id, 0);


		$attackUser = $initiator['user_id'];
		$defendUser = $defenders[0]['user_id'];


		$initiator['brole'] = "attacker";
		foreach ($attackers as $k => $v) $attackers[$k]['brole'] = "attacker";
		foreach ($defenders as $k => $v) $defenders[$k]['brole'] = "defender";

		$numOfAttackers = count($attackers) + count($prioAttackers) + 1;
		$numOfDefenders = count($defenders);



		$actors = array();
		foreach ($attackers as $ship) array_push($actors, $ship);
		foreach ($defenders as $ship) array_push($actors, $ship);
		shuffle($actors);

		array_unshift($actors, $initiator);
		array_unshift($attackers, $initiator);

		foreach ($prioAttackers as $prio) {
			array_unshift($attackers, $prio);
			array_unshift($actors, $prio);
		}


		// angreifer enttarnen sich
		// return "BREAK";
		foreach ($attackers as $ship) $res .= $this->decloakAction($ship, true);

		// debugHelper($actors);
		// return print_r($actors,true);

		$battleOngoing = true;
		while ($battleOngoing) {
			if (count($actors) == 0) break;
			if ($numOfAttackers == 0 || $numOfDefenders == 0) break;
			$current = array_shift($actors);
			if ($destroyed[$current['id']]) continue;

			$w1 = $this->getWeaponData($current['mod_w1'], $current['id']);
			$w2 = $this->getWeaponData($current['mod_w2'], $current['id']);
			$weapRes = array();

			if ($current['cloak'] == 1) continue;

			if ($current['brole'] == "attacker") {
				if ($w1['wtype'] && $current['wea_phaser']) {
					$weapRes = $this->fireWeapon($current, $w1, $attackTargets);
					$res .= "" . $weapRes['msg'];
				}
				if ($w2['wtype'] && $current['wea_torp']) {
					$weapRes = $this->fireWeapon($current, $w2, $attackTargets);
					$res .= "" . $weapRes['msg'];
				}
				if (count($weapRes['destroyed']) > 0)
					foreach ($weapRes['destroyed'] as $d) {
						$attackTargets = $this->removeFromShipList($attackTargets, $d);
						$attackers = $this->removeFromShipList($attackers, $d);
						$defendTargets = $this->removeFromShipList($defendTargets, $d);
						$defenders = $this->removeFromShipList($defenders, $d);
					}
			}
			if ($current['brole'] == "defender") {
				if ($current['user_id'] == $initiator['user_id']) continue;
				if ($w1['wtype'] && $current['wea_phaser']) {
					$weapRes = $this->fireWeapon($current, $w1, $defendTargets);
					$res .= "" . $weapRes['msg'];
				}
				if ($w2['wtype'] && $current['wea_torp']) {
					$weapRes = $this->fireWeapon($current, $w2, $defendTargets);
					$res .= "" . $weapRes['msg'];
				}
				if (count($weapRes['destroyed']) > 0)
					foreach ($weapRes['destroyed'] as $d) {
						$attackTargets = $this->removeFromShipList($attackTargets, $d);
						$attackers = $this->removeFromShipList($attackers, $d);
						$defendTargets = $this->removeFromShipList($defendTargets, $d);
						$defenders = $this->removeFromShipList($defenders, $d);
					}
			}

			if (count($weapRes['destroyed']) > 0)
				foreach ($weapRes['destroyed'] as $d)
					$destroyed[$d] = true;

			// return print_r($weapRes,true);
			// return print_r($attackers,true);
		}

		foreach ($defenders as $ship) $res .= $this->attackReaction($ship, false);
		foreach ($attackers as $ship) $res .= $this->attackReaction($ship, true);

		if (str_word_count($res) > 0) {
			// send PMs
			if ($initiator['systems_id'] > 0) {
				$sysname = $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $initiator['systems_id'] . " LIMIT 1", 1);
				$pmtext = "<b>Kampf in Sektor " . $initiator['sx'] . "|" . $initiator['sy'] . ". (" . $sysname . "-System):</b><br><br>";
			} else
				$pmtext = "<b>Kampf in Sektor " . $initiator['cx'] . "|" . $initiator['cy'] . ":</b><br><br>";

			$pmtext .= $res;

			$this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('" . $attackUser . "','" . $defendUser . "','" . addslashes($pmtext) . "','3',NOW())");
			// $this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date,new) VALUES ('".$attackUser."','".$attackUser."','".addslashes($pmtext)."','3',NOW(),NULL)");
		}

		return $pmtext;
		return $res;
	}


	function attackReaction($ship, $isAttacker)
	{
		$res = "";
		// debugHelper($ship);



		if ($isAttacker) {
			$nbs = $this->db->query("UPDATE stu_ships SET nbs = '1', eps = eps -1 WHERE id = " . $ship[id] . " AND eps > 0 AND NOT (nbs = '1') LIMIT 1", 6);
			if ($nbs == 1) $res .= "<br>" . $ship[name] . " aktiviert die Nahbereichssensoren.";

			$shd = $this->db->query("UPDATE stu_ships SET schilde_status = 1, eps = eps -1 WHERE id = " . $ship[id] . " AND eps > 0 AND schilde_status = 0 AND schilde > 0 AND cloak != 1 LIMIT 1", 6);
			if ($shd == 1) $res .= "<br>" . $ship[name] . " aktiviert die Schilde.";
		} else {
			$nbs = $this->db->query("UPDATE stu_ships SET nbs = '1', eps = eps -1 WHERE id = " . $ship[id] . " AND eps > 0 AND NOT (nbs = '1') LIMIT 1", 6);
			if ($nbs == 1) $res .= "<br>" . $ship[name] . " aktiviert die Nahbereichssensoren.";

			$shd = $this->db->query("UPDATE stu_ships SET schilde_status = 1, eps = eps -1 WHERE id = " . $ship[id] . " AND eps > 0 AND schilde_status = 0 AND schilde > 0 AND cloak != 1 LIMIT 1", 6);
			if ($shd == 1) $res .= "<br>" . $ship[name] . " aktiviert die Schilde.";

			if ($ship['alvl'] > 1) {

				$cloak = $this->db->query("UPDATE stu_ships SET cloak = '" . (time() + 900) . "' WHERE id = " . $ship[id] . " AND cloak = 1 LIMIT 1", 6);
				if ($cloak == 1) $res .= "<br>" . $ship[name] . " enttarnt sich.";

				$wp1 = 0;
				if ($ship['mod_w1'] > 0) {
					$wp1 = $this->db->query("UPDATE stu_ships SET wea_phaser = '1', eps = eps -1 WHERE id = " . $ship[id] . " AND eps > 0 AND wea_phaser = '0' LIMIT 1", 6);
				}
				if ($ship['mod_w2'] > 0) {
					$wp2 = $this->db->query("UPDATE stu_ships SET wea_torp = '1', eps = eps -1 WHERE id = " . $ship[id] . " AND eps > 0 AND wea_torp = '0' LIMIT 1", 6);
				}
				if ($wp1 == 1 || $wp2 == 1) $res .= "<br>" . $ship[name] . " aktiviert die Waffen.";
			}
		}
		return $res;
	}

	function decloakAction($ship, $isAttacker)
	{
		$res = "";

		$cloaktimer = time() + 900;
		// $cloaktimer = 0;

		if ($isAttacker) {
			$cloak = $this->db->query("UPDATE stu_ships SET cloak = '" . $cloaktimer . "' WHERE id = " . $ship[id] . " AND cloak = '1' LIMIT 1;", 6);
			if ($cloak == 1) $res .= "" . $ship[name] . " enttarnt sich.<br>";
		} else {
			$cloak = $this->db->query("UPDATE stu_ships SET cloak = '" . $cloaktimer . "' WHERE id = " . $ship[id] . " AND cloak = '1' AND alvl != '1' LIMIT 1;", 6);
			if ($cloak == 1) $res .= "" . $ship[name] . " enttarnt sich.<br>";
		}
		return $res;
	}


	function fireWeapon($ship, $weapon, $targets)
	{
		$res['msg'] = "";
		$res['destroyed'] = array();
		// print_r($weapon);			
		$remaining = $targets;
		shuffle($remaining);
		if (count($remaining) === 0) return $res;
		if ($this->checkWeapon($ship, $weapon, $targets)) {
			if ($weapon['ammo']) {
				$res['msg'] .= $ship['name'] . " feuert " . $weapon['ammo']['name'] . " aus " . $weapon['name'] . ":";
			} else {
				$res['msg'] .= $ship['name'] . " feuert " . $weapon['name'] . ":";
			}


			for ($i = 0; $i < $weapon['salvos']; $i++) {
				if (count($remaining) == 0) break;

				$shot = $this->fireShot($ship, $weapon, $remaining[0]);
				$res['msg'] .= "<br>&nbsp;&nbsp;&nbsp;" . $shot['msg'];

				// debugHelper($res);

				if ($shot['isDestroyed']) {
					array_push($res['destroyed'], $remaining[0]['id']);
					array_shift($remaining);
				}

				if ($weapon['wtype'] == 'beam' || $weapon['wtype'] == 'torpedo' || $weapon['wtype'] == 'cannon') {
					shuffle($remaining);
				} elseif ($weapon['wtype'] == 'pulse' && $shot['destroyed']) break;
			}
			$res['msg'] .= "<br>";
		}

		return $res;
	}

	function checkWeapon($ship, $weapon, $targets)
	{
		if (!$this->db->query("SELECT nbs FROM stu_ships WHERE id = " . $ship['id'] . " LIMIT 1", 1)) return false;
		if ($weapon['wtype'] == 'torpedo') {
			$res = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id = " . $ship['id'] . " AND goods_id = " . $weapon['ammo']['torp_type'] . "", 1);
			if (!$res) return false;
		}
		if (!$this->db->query("UPDATE stu_ships SET eps = eps-" . $weapon['ecost'] . " WHERE id = " . $ship['id'] . " AND eps >= " . $weapon['ecost'] . " LIMIT 1", 6)) return false;
		return true;
	}
	function payShot($ship, $weapon, $targets)
	{
		if ($weapon['wtype'] == 'torpedo')
			$this->lowerstorage($ship['id'], $weapon['ammo']['torp_type'], 1);
		return true;
	}

	function fireShot($ship, $weapon, $target)
	{
		$res['msg'] = "";
		$res['isDestroyed'] = false;

		if (!$this->payShot($ship, $weapon, $target)) return $res;

		$hitChanceEvade = 100 - $target['planvals']['evade'];
		$hitChanceTargeting = $weapon['hitchance'] + $weapon['ammo']['hitchance'] + $ship['planvals']['hitchance'];

		if ($this->weaponDebugEnabled()) {
			$res['msg'] .= "HCE: " . print_r($hitChanceEvade, true) . "<br>";
			$res['msg'] .= "HCT: " . print_r($hitChanceTargeting, true) . "<br>";
		}

		if (rand(1, 100) > $hitChanceEvade) {
			$res['msg'] .= $target['name'] . " weicht aus!";
			return $res;
		}
		if (rand(1, 100) > $hitChanceTargeting) {
			$res['msg'] .= $target['name'] . " wurde verfehlt!";
			return $res;
		}

		if (rand(1, 100) <= $weapon['critical'] + $weapon['ammo']['critical']) {
			$res['msg'] .= "kritischer Treffer! ";
			$damage = rand(2 * ($weapon['mindmg'] + $weapon['ammo']['mindmg']), 2 * ($weapon['maxdmg'] + $weapon['ammo']['maxdmg']));
		} else {
			$res['msg'] .= "Treffer. ";
			$damage = rand($weapon['mindmg'] + $weapon['ammo']['mindmg'], $weapon['maxdmg'] + $weapon['ammo']['maxdmg']);
		}

		// if ($this->weaponDebugEnabled()) {
		// $res['msg'] .= "<br>".print_r($weapon,true);
		// $res['msg'] .= "<br>".print_r($ship,true);
		// $res['msg'] .= "<br>".print_r($target,true);
		// }

		$reason = array();
		$reason['type'] = "ship";
		$reason['id'] = $ship['id'];

		$dres = $this->damageShip($weapon['dtype'], $damage, $target, $reason);

		$res['msg'] = $res['msg'] . "" . $dres['msg'];
		$res['isDestroyed'] = $dres['isDestroyed'];

		return $res;
	}

	function damageShip($type, $amount, $target, $reason = 0)
	{
		$res['msg'] = "";
		$res['isDestroyed'] = false;

		$data = $this->db->query("SELECT a.*,b.name as cname,b.beamgood,b.beamcrew,b.storage,b.reaktor as creaktor,d.ships_id as fsf,d.name as fname, c.m1 as mod_m1,c.m2 as mod_m2,c.m3 as mod_m3,c.m4 as mod_m4,c.m5 as mod_m5,c.w1 as mod_w1,c.w2 as mod_w2, c.s1 as mod_s1, c.s2 as mod_s2 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id WHERE a.id=" . $target['id'] . " LIMIT 1", 4);

		if ($data['rumps_id'] > 9000) $amount = 0;
		if ($data['rumps_id'] == 8) $amount = 0;

		$res['msg'] = $data['name'] . " nimmt " . formatDmg($type, $amount, 0, 0, 1);

		$shamount = 0;

		$res['schilde_status'] = $data['schilde_status'];
		$res['schilde'] = $data['schilde'];
		$res['huelle'] = $data['huelle'];

		if ($data['schilde_status'] == 1) {
			if ($amount >= $data['schilde']) {
				$shamount = min($data['schilde'], $amount);
				$amount = $amount - $shamount;
				$res['schilde_status'] = 0;
				$res['schilde'] = 0;
				$res['msg'] .= " - Schilde brechen zusammen!";
			} else {
				$res['schilde'] = $data['schilde'] - $amount;
				$amount = 0;
				$res['msg'] .= " - Schilde bei " . $res['schilde'];
			}
		}

		$armor = 0;
		if ($type != 'kinetic') $armor = $target['planvals']['armor'];
		$armorreduced = max(0, $amount - $armor);

		// if ($this->weaponDebugEnabled()) {
		// $res['msg'] .= "<br>ARM: ".print_r($armor,true)."<br>";
		// $res['msg'] .= "<br>AMT: ".print_r($amount,true)."<br>";
		// $res['msg'] .= "<br>ARD: ".print_r($armorreduced,true)."<br>";
		// $res['msg'] .= "<br>TYP: ".print_r($type,true)."<br>";
		// }		



		if ($amount != $armorreduced) {
			$res['msg'] .= " - Panzerung verhindert " . ($amount - $armorreduced) . " Schaden";
		}
		$amount = $armorreduced;
		if ($amount >= $data['huelle']) {
			$res['msg'] .= " - H�llenbruch! Schiff wurde zerst�rt!";
			$res['isDestroyed'] = 1;
			$this->destroyShip($target, $reason);
			return $res;
		}

		if ($amount > 0) {
			$res['huelle'] = $data['huelle'] - $amount;
			$amount = 0;
			$res['msg'] .= " - H�lle bei " . $res['huelle'];
		}

		if ($this->weaponDebugEnabled()) {
			return $res;
		}

		$time = time();
		$this->db->query("UPDATE stu_ships SET schilde=" . $res[schilde] . ", schilde_status='" . $res['schilde_status'] . "',huelle=" . $res['huelle'] . ",lasthit=" . $time . " WHERE id = " . $target['id'] . " LIMIT 1");

		$res['msg'] .= ".";
		return $res;
	}


	// function goodDecay($good,$amount) {

	// if ($good > 1000) return round(0.75 * rand(1,100) * $amount / 100);
	// switch ($good) {
	// case 1: return round(rand(25,100) * $amount / 100);

	// else return $amount;
	// }
	// }

	function destroyShip($ship, $reason)
	{
		$modulechance = 20;

		$shipdata = $this->db->query("SELECT a.*,b.name as cname,b.beamgood,b.beamcrew,b.storage,b.reaktor as creaktor,d.ships_id as fsf,d.name as fname, c.m1 as mod_m1,c.m2 as mod_m2,c.m3 as mod_m3,c.m4 as mod_m4,c.m5 as mod_m5,c.w1 as mod_w1,c.w2 as mod_w2, c.s1 as mod_s1, c.s2 as mod_s2 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id WHERE a.id=" . $ship['id'] . " LIMIT 1", 4);

		if ($shipdata['rumps_id'] == 8) return;
		// return;
		if ($shipdata['fleets_id'] > 0 && $this->db->query("SELECT fleets_id FROM stu_fleets WHERE ships_id=" . $shipdata['id'] . " LIMIT 1", 1) > 0) {
			$sc = $this->db->query("SELECT id FROM stu_ships WHERE id!=" . $shipdata['id'] . " AND fleets_id=" . $shipdata['fleets_id'] . " ORDER BY RAND() LIMIT 1", 1);
			if ($sc > 0) $this->db->query("UPDATE stu_fleets SET ships_id=" . $sc . " WHERE fleets_id=" . $shipdata['fleets_id'] . " LIMIT 1");
			else {
				$this->db->query("DELETE FROM stu_fleets WHERE fleets_id=" . $shipdata['fleets_id'] . " LIMIT 1");
				$this->db->query("DELETE FROM stu_colonies_actions WHERE value=" . $shipdata['fleets_id'] . " LIMIT 1");
			}
		}
		if (strlen($shipdata['name']) == 0) return;

		$tx = "Die " . $shipdata['name'] . " (" . $shipdata['cname'] . ") wurde in Sektor " . ($shipdata['systems_id'] > 0 ? $shipdata['sx'] . "|" . $shipdata['sy'] . " (" . $this->m->sysname . "-System)" : $shipdata['cx'] . "|" . $shipdata['cy']) . " ";

		if ($reason['type'] == 'ship') {
			$name = $this->db->query("SELECT name FROM stu_ships WHERE id = " . $reason['id'] . " LIMIT 1;", 1);
			$tx .= "von der " . $name . " zerst�rt";
			$historytype = 1;
		}

		$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg,coords_x,coords_y,user_id) VALUES ('" . addslashes($tx) . "',NOW(),'" . $historytype . "','" . strip_tags(str_replace("'", "", stripslashes($tx))) . "','" . $shipdata['cx'] . "','" . $shipdata['cy'] . "','" . $this->uid . "')");


		$this->db->query("UPDATE stu_ships SET user_id=1,huelle=" . ceil($shipdata['max_huelle'] / 2) . ",schilde=0,schilde_status=0,alvl=1,warpable=0,warpcore=0,traktor=0,traktormode=0,dock=0,crew=0,name='Tr�mmerfeld',eps=0,batt=0,nbs=0,lss=0,torp_type=0,rumps_id=8,trumps_id=8,cloak='0',fleets_id=0,warp='0',still=0,wea_phaser='0',wea_torp='0',is_rkn=0 WHERE id=" . $shipdata['id'] . " LIMIT 1");


		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE traktor=" . $shipdata['id'] . " LIMIT 2");
		$this->db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=" . $shipdata['id']);
		$this->db->query("DELETE FROM stu_ships_decloaked WHERE ships_id=" . $shipdata['id']);
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=" . $shipdata['id'] . " LIMIT 1");
		$this->db->query("DELETE FROM stu_ships_storage WHERE goods_id>=80 AND goods_id<100 AND ships_id=" . $shipdata['id']);
		$this->db->query("UPDATE stu_ships SET dock=0 WHERE dock=" . $shipdata['id']);
		$this->db->query("DELETE FROM stu_ships_buildprogress WHERE ships_id=" . $shipdata['id'] . " LIMIT 1");
		$this->db->query("UPDATE stu_ships_logdata SET destroytime=" . time() . " WHERE ships_id=" . $shipdata['id'] . " LIMIT 1");
		$this->db->query("DELETE FROM stu_dockingrights WHERE ships_id=" . $shipdata['id'] . " OR (type='1' AND id=" . $shipdata['id'] . ")");

		// drop modules

		$dat = $this->db->query("SELECT b.* FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b ON b.plans_id=a.plans_id LEFT JOIN stu_rumps as c ON c.rumps_id=b.rumps_id WHERE a.id=" . $shipdata['id'] . " LIMIT 1", 4);
		$i = 1;

		for ($i = 1; $i <= 5; $i++) {
			if ($dat['m' . $i] == 0) continue;
			$dchg = $this->db->query("SELECT demontchg FROM stu_modules WHERE module_id=" . $dat['m' . $i] . " LIMIT 1", 1);
			if ($dchg == 0 || rand(1, 100) > $dchg / 2) continue;
			$this->upperstorage($shipdata['id'], $dat['m' . $i], 1);
		}
		for ($i = 1; $i <= 2; $i++) {
			if ($dat['w' . $i] == 0) continue;
			$dchg = $this->db->query("SELECT demontchg FROM stu_modules WHERE module_id=" . $dat['w' . $i] . " LIMIT 1", 1);
			if ($dchg == 0 || rand(1, 100) > $dchg / 2) continue;
			$this->upperstorage($shipdata['id'], $dat['w' . $i], 1);
		}
		for ($i = 1; $i <= 2; $i++) {
			if ($dat['s' . $i] == 0) continue;
			$dchg = $this->db->query("SELECT demontchg FROM stu_modules WHERE module_id=" . $dat['s' . $i] . " LIMIT 1", 1);
			if ($dchg == 0 || rand(1, 100) > $dchg / 2) continue;
			$this->upperstorage($shipdata['id'], $dat['s' . $i], 1);
		}


		if (!check_int($shipdata['user_id'])) return;
	}






















	function attack(&$tarId, &$shipId, $fleet = 0, $strb = 0, $redalert = 0)
	{
		if ($tarId == $shipId) return;
		if (!check_int($tarId)) return;
		$data = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.plans_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.eps,a.phaser,a.nbs,a.wea_phaser,a.wea_torp,a.torp_type,a.crew,a.min_crew,a.warp,a.cfield,a.lastmaintainance,b.treffer,b.maintaintime,b.m6,b.m10,c.name as rname,c.m1c,c.m2c,c.m6c,c.slots FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON c.rumps_id=a.rumps_id WHERE a.id=" . $shipId . " LIMIT 1", 4);
		if ($data == 0) return;
		if ($data['wea_phaser'] == 0 && $data['wea_torp'] == 0 && $strb == 0) return "Auf der " . $data['name'] . " ist kein Waffensystem aktiviert<br />";
		$target = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.plans_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,a.lastmaintainance,a.cfield,b.name as rname,b.slots,b.trumfield,b.is_shuttle,b.m1c,b.m2c,c.maintaintime,c.evade,c.m1,c.m2,c.m4,c.m6,d.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_user as d ON d.id=a.user_id WHERE a.id=" . $tarId . " LIMIT 1", 4);
		if ($target == 0) return;
		if ($target['m10'] == 990) return "Dieses Schiff hat einen Sensorblocker aktiviert und kann nicht erfasst werden";
		if ($redalert == 1) {
			// Koordinaten-Override bei Alarm Rot
			$target['cx'] = $data['cx'];
			$target['cy'] = $data['cy'];
			$target['sx'] = $data['sx'];
			$target['sy'] = $data['sy'];
		}

		if (($this->map['sensoroff'] == 1 || $this->map['type'] == 8) && $this->db->query("SELECT ships_id FROM stu_ships_decloaked WHERE UNIX_TIMESTAMP(date)>0 AND ships_id=" . $target['id'] . " AND user_id=" . $data['user_id'] . " LIMIT 1", 1) != 0) {
			$excep_nbs = 0;
			$target['warp'] = 0;
			$this->db->query("UPDATE stu_ships SET warp='0' WHERE id=" . $target['id'] . " LIMIT 1");
		} else $excep_nbs = 1;

		if (($this->map['type'] == 8) || ($this->map['type'] == 7)) {
			$missfac = 0.7;
		} else {
			$missfac = 1;
		}
		if ($redalert == 1) {
			global $fleet;
			if ($data['systems_id'] > 0 && $fleet->field[$data['sx']][$data['sy']]['shieldoff'] == 1) $target['schilde_status'] = 0;
			elseif ($data['systems_id'] == 0 && $fleet->field[$data['cx']][$data['cy']]['shieldoff'] == 1) $target['schilde_status'] = 0;
		}
		if ($target['warp'] == 1) return "Schiffe im Warp k�nnen nicht erfasst werden";
		if ($target['maintain'] > 0) return "Die " . $target['name'] . " wird gewartet und kann nicht erfasst werden";
		if ($target['vac_active'] == 1) {
			global $fleet;
			$fleet->umode = 1;
			return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		}
		if ($data['systems_id'] > 0 || $target['systems_id'] > 0) {
			if ($data['systems_id'] != $target['systems_id']) return;
			if ($data['sx'] != $target['sx'] || $data['sy'] != $target['sy']) return;
		} elseif ($data['cx'] != $target['cx'] || $data['cy'] != $target['cy']) return;
		$return = shipexception(array("nbs" => $excep_nbs, "eps" => 1, "cloak" => 0, "warpstate" => 0, "crew" => $data['min_crew']), $data);
		if ($return['code'] == 1) {
			if ($strb == 1) return;
			return $return['msg'] . "<br />";
		}
		// Systemname laden
		if (!$this->m->sysname && $data['systems_id'] > 0) $this->m->sysname = $this->m->getsysnamebyid($data['systems_id']);

		if (($data['wea_phaser'] == 1 || $data['wea_torp'] == 1) && $data['user_id'] != $target['user_id'] && $target['user_id'] != 1 && $target['trumfield'] != 1) {
			$pm = "Kampf in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->sysname . "-System " . $data['cx'] . "|" . $data['cy'] . ")" : $data['cx'] . "|" . $data['cy']) . "<br />";
			if ($redalert == 1) {
				$this->send_pm(1, $data['user_id'], "Die " . stripslashes($data['name']) . " hat in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->sysname . "-System " . $data['cx'] . "|" . $data['cy'] . ")" : $data['cx'] . "|" . $data['cy']) . " das Feuer auf die " . stripslashes($target['name']) . " er�ffnet (Alarm Rot)", 1);
			}
		}
		// Attacke!
		if ($data['wea_phaser'] == 1 && $this->checksubsystem(6, $data['id']) != 1) {
			$wd = $this->getweapon($data['m6']);
			if ($wd['pulse'] > 0) {
				$ro = 1;
				$wd['pulse'] += floor($data['m6c'] / 1.5);
			} else {
				$wd['strength'] *= get_weapon_damage($data['m6c']);
				$ro = ($data['m6c'] < 4 ? 1 : $data['m6c'] - 2);
			}
			for ($i = 1; $i <= $ro; $i++) {
				if ($this->checksubsystem(6, $data['id']) == 1) break;
				if ($data['plans_id'] != 1 && $data['user_id'] > 100 && $data['slots'] == 0 && getSystemDamageChance(array("lastmaintainance" => $data['lastmaintainance'], "maintaintime" => $data['maintaintime'])) > rand(1, 100)) {
					$result .= $this->damage_subsystem("foo", $data['id'], 6);
					break;
				}
				if ($target['fleets_id'] == 0) $tar = $target;
				else {
					$tar = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,a.cfield,b.name as rname,b.slots,b.trumfield,b.is_shuttle,b.m1c,b.m2c,b.name as rname,c.evade,c.m1,c.m2,c.m6 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_ships_decloaked as d ON d.ships_id=a.id AND d.user_id=" . $data['user_id'] . " AND UNIX_TIMESTAMP(d.date)=0 WHERE a.fleets_id=" . $target['fleets_id'] . " AND a.id!=" . $data['id'] . " AND (a.cloak!='1' OR !ISNULL(d.user_id)) AND warp='0' ORDER BY RAND() LIMIT 1", 4);
					if ($tar == 0) break;
				}
				if ($this->dsships[$tar['id']]) break;
				$res = $this->phaser($data, $tar, $wd, $fleet, $strb, $missfac);
				$result .= $res['msg'] . "<br />";
				$pm .= $res['pm'] . "<br />";
				if (!$this->dsships[$tar['id']]) {
					$this->write_damage($tar);
					if ($tar['id'] == $target['id']) {
						$target['huelle'] = $tar['huelle'];
						$target['schilde'] = $tar['schilde'];
						$target['schilde_status'] = $tar['schilde_status'];
					}
				}
				if ($tar['id'] == $this->id) {
					global $fleet;
					if ($fleet->fm != 1) {
						if ($destroy == 1) $this->huelle = ceil(($tar['max_huelle'] / 100) * 15);
						else $this->huelle = $tar['huelle'];
						$this->schilde = $tar['schilde'];
						$this->schilde_status = $tar['schilde_status'];
					}
				}
			}
		}
		if ($this->dsships[$target['id']] == 1 && $target['fleets_id'] == 0) {
			if (strlen(strip_tags($pm)) > 0) $this->send_pm(($excep_nbs == 0 ? 1 : $data['user_id']), $target['user_id'], $pm, 3);
			$this->db->query("UPDATE stu_ships SET eps=" . $data['eps'] . " WHERE id=" . $data['id'] . " LIMIT 1");
			return $result;
		}
		if ($this->dsships[$target['id']] == 1) {
			if ($target['fleets_id'] == 0) {
				if (strlen(strip_tags($pm)) > 0) $this->send_pm(($excep_nbs == 0 ? 1 : $data['user_id']), $target['user_id'], $pm, 3);
				return $result;
			} else {
				$tar = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,a.cfield,b.slots,b.trumfield,b.is_shuttle,b.m1c,b.m2c,b.name as rname,c.evade,c.m1,c.m2,c.m6 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_ships_decloaked as d ON d.ships_id=a.id AND d.user_id=" . $data['user_id'] . " AND UNIX_TIMESTAMP(d.date)=0 WHERE a.fleets_id=" . $target['fleets_id'] . " AND (a.cloak!='1' OR !ISNULL(d.user_id)) AND warp='0' ORDER BY RAND() LIMIT 1", 4);
				if ($tar == 0) {
					if (strlen(strip_tags($pm)) > 0) $this->send_pm(($excep_nbs == 0 ? 1 : $data['user_id']), $target['user_id'], $pm, 3);
					return $result;
				} else $target = $tar;
			}
		}
		if (!$this->dsships[$target['id']]) $this->write_damage($target);
		if ($data['wea_torp'] == 1 && $this->checksubsystem(10, $data['id']) != 1) {
			if ($data['plans_id'] != 1 && $data['user_id'] > 100 && $data['slots'] == 0 && getSystemDamageChance(array("lastmaintainance" => $data['lastmaintainance'], "maintaintime" => $data['maintaintime'])) > rand(1, 100)) $result .= $this->damage_subsystem("foo", $data['id'], 10);
			else {
				$res = $this->torpedo($data, $target, $wd, $fleet, $strb, $missfac);
				$result .= $res['msg'] . "<br />";
				$pm .= $res['pm'] . "<br />";
			}
		}
		if (strlen(strip_tags($pm)) > 0) {
			$this->send_pm(($excep_nbs == 0 ? 1 : $data['user_id']), $target['user_id'], $pm, 3);
		}
		$this->db->query("UPDATE stu_ships SET eps=" . $data['eps'] . " WHERE id=" . $data['id'] . " LIMIT 1");
		if ($strb == 1 || $fleet == 1 || $redalert == 1 || $this->fleet_hit == 0 || $excep_nbs == 0) return $result;
		$field = $this->m->getFieldByType($target['cfield']);
		if ($target['fleets_id'] > 0) {
			$result .= "<br /><b>Gegenangriff</b><br />";
			$res = $this->db->query("SELECT id,name,torp_type,phaser,nbs,cloak,eps,schilde,schilde_status,huelle,max_huelle,traktor,traktormode,alvl,phaser,torp_type,wea_phaser,wea_torp FROM stu_ships WHERE fleets_id=" . $target['fleets_id'] . " LIMIT 25");
			while ($fd = mysql_fetch_assoc($res)) {
				$qry = 0;
				$con = 0;
				if ($fd['cloak'] == 1) {

					$hpfac = 1 - ($fd['huelle'] / $fd['max_huelle']);
					$hpfac = round($hpfac * 1800);
					$fd['cloak'] = time() + 900 + $hpfac;
					$result .= "- Die " . $fd['name'] . " deaktiviert die Tarnung<br>";
					$qry = 1;
				}
				if ($fd['nbs'] != 1 && $fd['eps'] > 1 && $this->checksubsystem(4, $fd['id']) != 1) {
					$fd['nbs'] = 1;
					$result .= "- Die " . $fd['name'] . " aktiviert die Sensoren<br>";
					$fd['eps'] -= 1;
					$qry = 1;
				}
				if ((($fd['schilde_status'] == 0 || ($fd['schilde_status'] > 1 && $fd['schilde_status'] < time())) && $fd['traktormode'] != 2 && $fd['schilde'] > 0 && $fd['eps'] > 1 && $this->checksubsystem(2, $fd['id']) != 1) && $field['shieldoff'] != 1) {
					if ($fd['traktor'] > 0) $this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=" . $target['id'] . " OR id=" . $fd['traktor'] . " LIMIT 2");
					$fd['schilde_status'] = 1;
					$result .= "- Die " . $fd['name'] . " aktiviert die Schilde<br>";
					$fd['eps'] -= 1;
					$qry = 1;
				}
				if ($fd['alvl'] > 1 && $fd['eps'] > 0 && (($fd['phaser'] > 0 && $fd['wea_phaser'] == 0 && $this->checksubsystem(6, $fd['id']) != 1) || ($fd['torp_type'] > 0 && $fd['wea_torp'] == 0 && $this->checksubsystem(10, $fd['id']) != 1))) {
					if ($fd['wea_phaser'] == 0 && $fd['wea_torp'] == 0) $con = 1;
					if ($fd['phaser'] > 0 && $fd['wea_phaser'] == 0) {
						$fd['eps'] -= 1;
						$fd['wea_phaser'] = 1;
					}
					if ($fd['torp_type'] > 0 && $fd['eps'] > 0 && $fd['wea_torp'] == 0) {
						$fd['eps'] -= 1;
						$fd['wea_torp'] = 1;
					}
					$result .= "- Die " . $fd['name'] . " aktiviert die Waffensysteme<br />";
					$qry = 1;
				}
				if ($qry == 1) $this->db->query("UPDATE stu_ships SET eps=" . $fd['eps'] . ",nbs='" . $fd['nbs'] . "',schilde_status=" . $fd['schilde_status'] . ",cloak='" . $fd['cloak'] . "',wea_phaser='" . $fd['wea_phaser'] . "',wea_torp='" . $fd['wea_torp'] . "',still=0 WHERE id=" . $fd['id'] . " AND rumps_id!=8 LIMIT 1");
				if ($con == 1) continue;
				if ($data['fleets_id'] > 0) $tar = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=" . $data['fleets_id'] . " ORDER BY RAND() LIMIT 1", 1);
				else $tar = $shipId;
				$result .= $this->attack($tar, $fd['id'], 0, 1);
			}
		} else {
			if ($target['alvl'] == 1) return $result;
			$result .= "<br /><b>Gegenangriff</b><br />";
			if ($target['cloak'] == 1) {
				$hpfac = 1 - ($target['huelle'] / $target['max_huelle']);
				$hpfac = round($hpfac * 1800);
				$target['cloak'] = time() + 900 + $hpfac;
				$result .= "- Die " . $target['name'] . " deaktiviert die Tarnung<br>";
				$qry = 1;
			}
			if ($target['nbs'] != 1 && $target['eps'] > 1 && $this->checksubsystem(4, $target['id']) != 1) {
				$target['nbs'] = 1;
				$result .= "- Die " . $target['name'] . " aktiviert die Sensoren<br>";
				$target['eps'] -= 1;
				$qry = 1;
			}
			if (($target['schilde_status'] == 0 || ($target['schilde_status'] > 1 && $target['schilde_status'] < time())) && $target['traktormode'] != 2 && $target['schilde'] > 0 && $target['eps'] > 1 && $this->checksubsystem(2, $target['id']) != 1 && $field['shieldoff'] != 1) {
				if ($target['traktor'] > 0) $this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=" . $target['id'] . " OR id=" . $target['traktor'] . " LIMIT 2");
				if ($target['slots'] > 0) {
					$this->db->query("UPDATE stu_ships SET dock=0,maintain=0 WHERE dock=" . $target['id']);
					$this->db->query("DELETE FROM stu_colonies_maintainance WHERE station_id=" . $target['id']);
				}
				$target['schilde_status'] = 1;
				$result .= "- Die " . $target['name'] . " aktiviert die Schilde<br>";
				$target['eps'] -= 1;
				$qry = 1;
			}
			if ($target['eps'] > 0 && (($target['phaser'] > 0 && $target['wea_phaser'] == 0 && $this->checksubsystem(6, $target['id']) != 1) || ($target['torp_type'] > 0 && $target['wea_torp'] == 0 && $this->checksubsystem(10, $target['id']) != 1))) {
				if ($target['wea_phaser'] == 0 && $target['wea_torp'] == 0) $con = 1;
				if ($target['phaser'] > 0 && $target['wea_phaser'] == 0) {
					$target['eps'] -= 1;
					$target['wea_phaser'] = 1;
				}
				if ($target['torp_type'] > 0 && $target['eps'] > 0 && $target['wea_torp'] == 0) {
					$target['eps'] -= 1;
					$target['wea_torp'] = 1;
				}
				$result .= "- Die " . $target['name'] . " aktiviert die Waffensysteme<br />";
				$qry = 1;
			}
			if ($qry == 1) $this->db->query("UPDATE stu_ships SET eps=" . $target['eps'] . ",nbs='" . $target['nbs'] . "',schilde_status=" . $target['schilde_status'] . ",cloak='" . $target['cloak'] . "',wea_phaser='" . $target['wea_phaser'] . "',wea_torp='" . $target['wea_torp'] . "',still=0 WHERE id=" . $target['id'] . " AND rumps_id!=8 LIMIT 1");
			if ($con == 1) return $result;
			$result .= $this->attack($shipId, $target['id'], 0, 1);
		}
		return $result;
	}

	function write_damage(&$arr)
	{
		$this->db->query("UPDATE stu_ships SET huelle=" . $arr['huelle'] . ",schilde=" . $arr['schilde'] . ",schilde_status=" . $arr['schilde_status'] . " WHERE id=" . $arr['id'] . " LIMIT 1");
	}


	function getSignatures()
	{
	}

	function phaser(&$data, &$target, &$wd, $fleet = 0, $strb = 0, $missfac = 1)
	{
		if (($this->map['sensoroff'] == 1 || $this->map['type'] == 8) && $this->db->query("SELECT ships_id FROM stu_ships_decloaked WHERE UNIX_TIMESTAMP(date)>0 AND ships_id=" . $target['id'] . " AND user_id=" . $data['user_id'] . " LIMIT 1", 1) != 0) {
			$excep_nbs = 0;
			$target['warp'] = 0;
			$this->db->query("UPDATE stu_ships SET warp='0' WHERE id=" . $target['id'] . " LIMIT 1");
		} else $excep_nbs = 1;

		$this->fleet_hit = 1;
		$data['eps'] -= 1;
		if ($target['trumfield'] == 1) $msg .= "<b>Die " . $data['name'] . " feuert mit einer Strahlenwaffe (" . $wd['name'] . ") auf das Tr�mmerfeld</b>";
		else $msg .= "<b>Die " . $data['name'] . " feuert mit einer Strahlenwaffe (" . $wd['name'] . ") auf die " . $target['name'] . "</b>";
		// Schaden anhand des Basiswerts und der Varianz errechnen

		$minvari = round(($wd['strength'] / 10) * (100 - $wd['varianz']));
		if ($minvari < 1) $minvari = 1;
		$maxvari = round(($wd['strength'] / 10) * (100 + $wd['varianz']));
		if ($maxvari > 1000) $maxvari = 1000;

		$wd['pulse'] == 0 ? $ro = 1 : $ro = $wd['pulse'];

		$i = 1;
		while ($i <= $ro) {
			$dmg = round(rand($minvari, $maxvari) / 10, 1);
			// Metreongas?
			if ($data['cfield'] == 4) {
				$mul = 1;
				// Tasche etwa auch noch?
				if ($this->db->query("SELECT type FROM stu_map_special WHERE type=4 AND " . ($data['systems_id'] > 0 ? "systems_id=" . $data['systems_id'] . " AND sx=" . $data['sx'] . " AND sy=" . $data['sy'] : "cx=" . $data['cx'] . " AND cy=" . $data['cy'] . " LIMIT 1"), 1) > 0) $mul = rand(3, 10);
				$result = $this->db->query("SELECT a.id,a.rumps_id,a.user_id,a.fleets_id,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.name,a.huelle,a.max_huelle,a.schilde,a.max_schilde,a.schilde_status,b.is_shuttle,b.trumfield,b.name as rname FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE b.trumfield='0' AND a.systems_id=" . $data['systems_id'] . " AND " . ($data['systems_id'] > 0 ? "a.sx=" . $data['sx'] . " AND a.sy=" . $data['sy'] : "a.cx=" . $data['cx'] . " AND a.cy=" . $data['cy']) . "");
				while ($dat = mysql_fetch_assoc($result)) {
					$dmg = rand(10, 25) * $mul;
					$pmm = "Eine Metreongasexplosion in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->sysname . "-System " . $data['cx'] . "|" . $data['cy'] . ")" : $data['cx'] . "|" . $data['cy']) . " verursacht " . $dmg . " Schaden an der " . $dat['name'];
					$arr = damageship($dmg, $dat['huelle'], $dat['schilde'], $dat['schilde_status']);
					if ($arr['huelle'] <= 0) {
						$this->trumfield($dat, "durch die Anomalie (Metreongas-Explosion)");
						$pmm .= $arr['msg'] . "<br>-- Das Schiff wurde zerst�rt";
						if ($dat['id'] == $data['id']) $this->dsships[$dat['id']] = 1;
					} else {
						$this->db->query("UPDATE stu_ships SET huelle=" . $arr['huelle'] . ",schilde=" . $arr['schilde'] . ",schilde_status=" . $arr['schilde_status'] . ",cloak!='1' WHERE id=" . $dat['id'] . " LIMIT 1");
						$pmm .= $arr['msg'];
					}
					$this->send_pm(1, $dat['user_id'], $pmm, 3);
				}
				return "Eine Metreongas-Explosion richtet schwere Sch�den an allen Schiffen in diesem Sektor an";
			}
			if ($ro > 1) $msg .= "<br>- Schuss " . $i . ":";
			$hitchance = $data['treffer'] * $missfac;
			if (rand(1, 100) > ($excep_nbs == 0 ? $hitchance - 25 : $hitchance)) {
				$msg .= "<br>- Der Schuss verfehlt sein Ziel!";
				$i++;
				continue;
			}
			// Liegt ein kritischer Treffer vor?
			if (rand(1, 100) <= $wd['critical'] && $target['trumfield'] != 1 && $target['user_id'] > 100) {
				$dmg *= 2;
				if ($target['schilde_status'] != 1 && rand(1, 100) < (5 - 2 * $target['huelle'] / ($target['m1c'] + $target['m2c'])) * 20) {
					$crit_msg = $this->damage_subsystem($target, $target['id']);
					if ($crit_msg) $crit = "<br>- " . $crit_msg;
				}
				if ($this->shoff == 1) {
					$target['schilde_status'] = 0;
					if ($target['id'] == $this->id) $this->schilde_status = 0;
					$this->shoff = 0;
				}
				if ($this->senoff == 1) {
					$target['nbs'] = 0;
					$target['lss'] = 0;
					if ($target['id'] == $this->id) {
						$this->lss = 0;
						$this->nbs = 0;
					}
				}
			}
			$pdmg += $dmg;
			$hit = 1;
			$rand = rand(1, 100);
			$rand > 100 - $wd['shields_through'] ? $ds = 1 : $ds = 0;

			if ($target['schilde_status'] == 1 && $ds == 0) {
				$dmgr = 0;
				$dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 AND (b.dmg_redu_wtype=" . $wd['wtype'] . " OR b.dmg_redu_wtype=99) WHERE a.module_id=" . $target['m2'], 1);
				if ($dmgr == 0) $dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 AND (b.dmg_redu_wtype=" . $wd['wtype'] . " OR b.dmg_redu_wtype=99) WHERE a.module_id=" . $target['m2'], 1);
				$dmge = 0;
				$dmge = $this->db->query("SELECT b.dmg_enh_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 WHERE a.module_id=" . $data['m6'], 1);
				if ($dmge == 0) $dmge = $this->db->query("SELECT b.dmg_enh_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 WHERE a.module_id=" . $data['m6'], 1);
				$dada = $dmg;
				if ($dmgr != 0 || $dmge != 0) {
					$dmg = round((($dmg / 100) * (100 - $dmgr + $dmge)), 1);
					$rb = 1;
				}
				$dada .= " - " . $dmg;
				$msg .= "<br>- Schildschaden: " . ($target['schilde'] <= $dmg ? $target['schilde'] : $dmg);
				if ($target['schilde'] <= $dmg) {
					$dmg -= $target['schilde'];
					$target['schilde'] = 0;
					$target['schilde_status'] = 0;
					$msg .= "<br>-- Schilde brechen zusammen!";
				} else {
					$target['schilde'] -= $dmg;
					$dmg = 0;
					$msg .= " - Schilde bei " . $target['schilde'];
				}
				if ($dmg != 0 && $rb == 1) $dmg = round((($dmg / (100 - $dmgr + $dmge)) * 100), 1);
			}
			if (strlen(strip_tags($crit)) > 0) {
				$msg .= $crit;
				unset($crit);
			}
			if ($ro == $i && $dmg == 0) break;
			if ($dmg > 0 && $target['trumfield'] != 1) {
				if ($target['rumps_id'] == 9) $target['m1'] = 200;
				$dmgr = 0;
				$dmgr = $this->db->query("SELECT b.dmg_redu_huell FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 AND (b.dmg_redu_wtype=" . $wd['wtype'] . " OR b.dmg_redu_wtype=99) WHERE a.module_id=" . $target['m1'], 1);
				if ($dmgr == 0) $dmgr = $this->db->query("SELECT b.dmg_redu_huell FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 AND (b.dmg_redu_wtype=" . $wd['wtype'] . " OR b.dmg_redu_wtype=99) WHERE a.module_id=" . $target['m1'], 1);
				if ($dmgr > 0) {
					$dmg = round((($dmg / 100) * (100 - $dmgr)), 1);
					$rb = 1;
				}
				$msg .= "<br>- H�llenschaden: " . ($target['huelle'] <= $dmg ? $target['huelle'] : $dmg);
				if ($target['huelle'] <= $dmg) {
					$msg .= "<br>-- H�llenbruch! Das Schiff wurde zerst�rt";
					$target['huelle'] = 0;
					$this->trumfield($target, "con der " . $data['name']);
					$destroy = 1;
					$this->dsships[$target['id']] = 1;
					break;
				} else {
					$target['huelle'] -= $dmg;
					$msg .= " - H�lle bei " . $target['huelle'];
				}
			}
			if ($dmg > 0 && $target['trumfield'] == 1) {
				$msg .= "<br>- Schaden am Tr�mmerfeld: " . ($target['huelle'] <= $dmg ? $target['huelle'] : $dmg);
				if ($target['huelle'] <= $dmg) {
					$msg .= "<br>-- Das Tr�mmerfeld wurde beseitigt";
					$target['huelle'] = 0;
					$this->deletetrumfield($target);
					$destroy = 1;
					$this->dsships[$target['id']] = 1;
					$hit = 0;
					break;
				} else {
					$target['huelle'] -= $dmg;
					$msg .= " - Status: " . $target['huelle'];
				}
			}
			$i++;
		}
		if ($target['trumfield'] != 1 && $hit == 1) {
			if ($destroy == 1) $this->logbook[$data['id']][] = array("user_id" => $this->uid, "text" => "Das Schiff hat die " . $target['name'] . " zerst�rt", "type" => 1);
			$this->logbook[$data['id']][] = array("user_id" => $this->uid, "text" => "Das Schiff feuert in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->sysname . "-System)" : $data['cx'] . "|" . $data['cy']) . " mit einem " . $wd['name'] . " auf die " . $target['name'] . " und richtet " . $pdmg . " Schaden an", "type" => 1);
			$this->logbook[$target['id']][] = array("user_id" => $target['user_id'], "text" => "Das Schiff wurde in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->sysname . "-System)" : $data['cx'] . "|" . $data['cy']) . " von der " . $data['name'] . " beschossen. Schaden: " . $pdmg . ($destroy == 1 ? "<br>Das Schiff wurde zerst�rt" : ""), "type" => 1);
		}
		if ($data['user_id'] != $target['user_id'] && $target['user_id'] != 1 && $target['trumfield'] != 1) $pm = $msg;
		return array("msg" => $msg, "pm" => $pm);
	}

	function torpedo(&$data, &$target, &$wd, $fleet = 0, $strb = 0, $missfac = 1)
	{
		if ($data['id'] == $target['id']) return;
		$data['torp_fire_amount'] = $this->db->query("SELECT torp_fire_amount FROM stu_modules WHERE module_id=" . $data['m10'] . " LIMIT 1", 1);
		$wd = $this->db->query("SELECT name,goods_id,damage,varianz,shields_through,critical,hitchance FROM stu_torpedo_types WHERE torp_type=" . $data['torp_type'] . " LIMIT 1", 4);

		// Schaden anhand des Basiswerts und der Varianz errechnen
		$minvari = round(($wd['damage'] / 10) * (100 - $wd['varianz']));
		if ($minvari < 1) $minvari = 1;
		$maxvari = round(($wd['damage'] / 10) * (100 + $wd['varianz']));
		$dmg = round(rand($minvari, $maxvari) / 10, 1);
		$this->fleet_hit = 1;
		// Durchgang starten
		$i = 1;
		if ($data['torp_fire_amount'] > $data['eps']) $data['torp_fire_amount'] = $data['eps'];
		$tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $data['id'] . " AND goods_id=" . $wd['goods_id'] . " LIMIT 1", 1);
		if ($data['torp_fire_amount'] > $tc) $data['torp_fire_amount'] = $tc;
		while ($i <= $data['torp_fire_amount']) {
			if ($target['fleets_id'] > 0) {
				$tar = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,a.cfield,b.trumfield,b.is_shuttle,b.name as rname,b.m1c,b.m2c,c.evade,c.m1,c.m2,c.m6 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_ships_decloaked as d ON d.ships_id=a.id AND d.user_id=" . $data['user_id'] . " AND UNIX_TIMESTAMP(d.date)=0 WHERE a.fleets_id=" . $target['fleets_id'] . " AND a.id!=" . $data['id'] . " AND (a.cloak!='1' OR !ISNULL(d.user_id)) ORDER BY RAND() LIMIT 1", 4);
				if ($tar != 0) $target = $tar;
			}
			$dmg = round(rand($minvari, $maxvari) / 10, 1);

			if ($i != 1) $msg .= "<br>";
			$data['eps'] -= 1;
			if ($target['trumfield'] == 1) $msg .= "<b>Die " . $data['name'] . " feuert einen Torpedo (" . $wd['name'] . ") auf das Tr�mmerfeld</b>";
			else $msg .= "<b>Die " . $data['name'] . " feuert einen Torpedo (" . $wd['name'] . ") auf die " . $target['name'] . "</b>";
			$this->lowertorpedo($data['id'], $data['torp_type']);

			// Metreongas?
			if ($data['cfield'] == 4) {
				$mul = 1;
				// Tasche etwa auch noch?
				if ($this->db->query("SELECT type FROM stu_map_special WHERE type=4 AND " . ($data['systems_id'] > 0 ? "sx=" . $data['sx'] . " AND sy=" . $data['sy'] : "cx=" . $data['cx'] . " AND cy=" . $data['cy']) . " LIMIT 1", 1) > 0) $mul = rand(3, 10);
				$result = $this->db->query("SELECT a.id,a.rumps_id,a.user_id,a.fleets_id,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.name,a.huelle,a.max_huelle,a.schilde,a.max_schilde,a.schilde_status,b.is_shuttle,b.name as rname FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE b.trumfield='0' AND a.systems_id=" . $data['systems_id'] . " AND " . ($data['systems_id'] > 0 ? "a.sx=" . $data['sx'] . " AND a.sy=" . $data['sy'] : "a.cx=" . $data['cx'] . " AND a.cy=" . $data['cy']) . "");
				while ($dat = mysql_fetch_assoc($result)) {
					$dmg = rand(10, 25) * $mul;
					$pmm = "Eine Metreongasexplosion in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->sysname . " System " . $data['cx'] . "|" . $data['cy'] . ")" : $data['cx'] . "|" . $data['cy']) . " verursacht " . $dmg . " Schaden an der " . $dat['name'];
					$arr = damageship($dmg, $dat['huelle'], $dat['schilde'], $dat['schilde_status']);
					if ($arr['huelle'] <= 0) {
						$this->trumfield($dat, "durch die Anomalie (Metreongas-Explosion)");
						$pmm .= $arr['msg'] . "<br>-- Das Schiff wurde zerst�rt";
						if ($dat['id'] == $data['id']) $this->dsships[$dat['id']] = 1;
					} else {
						$this->db->query("UPDATE stu_ships SET huelle=" . $arr['huelle'] . ",schilde=" . $arr['schilde'] . ",schilde_status=" . $arr['schilde_status'] . ",cloak='0' WHERE id=" . $dat['id'] . " LIMIT 1");
						if ($dat['id'] == $target['id']) {
							$target['huelle'] = $arr['huelle'];
							$target['schilde'] = $arr['schilde'];
							$target['schilde_status'] = $arr['schilde_status'];
						}
						$pmm .= $arr['msg'];
					}
					$this->send_pm(1, $dat['user_id'], $pmm, 3);
				}
				return array("msg" => "Eine Metreongas-Explosion richtet schwere Sch�den an allen Schiffen in diesem Sektor an");
			}
			$hitchance = round(($wd['hitchance'] - $target['evade']) * $missfac);
			if ($hitchance < 0) $hitchance = 0;
			if (rand(1, 100) > $hitchance && $target['trumfield'] != 1) {
				$msg .= "<br>- Der Torpedo verfehlt sein Ziel!";
				$i++;
				continue;
			}
			if (rand(1, 100) <= $wd['critical'] && $target['trumfield'] != 1 && $target['user_id'] > 100) {
				$dmg *= 2;
				if ($target['schilde_status'] != 1 && rand(1, 100) > (5 - 2 * $target['huelle'] / ($target['m1c'] + $target['m2c'])) * 20) {
					$crit_msg = $this->damage_subsystem($target, $target['id']);
					if ($crit_msg) $crit = "<br>- " . $crit_msg;
				}
				if ($this->shoff == 1) {
					$target['schilde_status'] = 0;
					if ($target['id'] == $this->id) $this->schilde_status = 0;
				}
				if ($this->senoff == 1) {
					$target['nbs'] = 0;
					$target['lss'] = 0;
					if ($target['id'] == $this->id) {
						$this->lss = 0;
						$this->nbs = 0;
					}
				}
			}
			$pdmg += $dmg;
			$hit = 1;
			$rand = rand(1, 100);
			$rand > 100 - $wd['shields_through'] ? $ds = 1 : $ds = 0;
			if ($target['schilde_status'] == 1 && $ds == 0 && $target['trumfield'] != 1) {
				$dmgr = 0;
				$dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 AND b.dmg_redu_wtype=99 WHERE a.module_id=" . $target['m2'], 1);
				if ($dmgr == 0) $dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 AND b.dmg_redu_wtype=99 WHERE a.module_id=" . $target['m2'], 1);
				if ($dmgr > 0) {
					$dmg = round(($dmg / 100) * (100 - $dmgr));
					$rb = 1;
				}
				$msg .= "<br>- Schildschaden: " . ($target['schilde'] <= $dmg ? $target['schilde'] : $dmg);
				if ($target['schilde'] <= $dmg) {
					$dmg -= $target['schilde'];
					$target['schilde'] = 0;
					$target['schilde_status'] = 0;
					$msg .= "<br>-- Schilde brechen zusammen!";
				} else {
					$target['schilde'] -= $dmg;
					$dmg = 0;
					$msg .= " - Schilde bei " . $target['schilde'];
				}
				if ($dmg > 0 && $rb == 1) $dmg = round(($dmg / 3) * 4);
			}
			if (strlen(strip_tags($crit)) > 0) {
				$msg .= $crit;
				unset($crit);
			}
			if ($dmg > 0  && $target['trumfield'] != 1) {
				if ($target['rumps_id'] == 9) $target['m1'] = 200;
				$dmgr = 0;
				$dmgr = $this->db->query("SELECT b.dmg_redu_huell FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 AND b.dmg_redu_wtype=99 WHERE a.module_id=" . $target['m1'], 1);
				if ($dmgr == 0) $dmgr = $this->db->query("SELECT b.dmg_redu_huell FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 AND b.dmg_redu_wtype=99 WHERE a.module_id=" . $target['m1'], 1);
				if ($dmgr > 0) $dmg = round(($dmg / 100) * (100 - $dmgr), 1);
				$msg .= "<br>- H�llenschaden: " . ($target['huelle'] <= $dmg ? $target['huelle'] : $dmg);
				if ($target['huelle'] <= $dmg) {
					$msg .= "<br>-- H�llenbruch! Das Schiff wurde zerst�rt";
					$target['huelle'] = 0;
					$this->trumfield($target, "von der" . $data['name']);
					$destroy = 1;
					$this->dsships[$target['id']] = 1;
					$i++;
					break;
				} else {
					$target['huelle'] -= $dmg;
					$msg .= " - H�lle bei " . $target['huelle'];
				}
			}
			if ($target['trumfield'] == 1 && $dmg > 0) {
				if ($target['huelle'] <= $dmg) {
					$this->deletetrumfield($target);
					$msg .= "<br>Das Tr�mmerfeld wurde beseitigt";
					$hit = 0;
					$i++;
					break;
				}
				$target['huelle'] -= $dmg;
				$msg .= "<br>Schaden " . $dmg . " - Status des Tr�mmerfeldes " . $target['huelle'];
			}
			if (!$this->dsships[$target['id']]) $this->write_damage($target);
			$i++;
		}
		if ($hit == 1) {
			if ($destroy == 1) $this->logbook[$data['id']][] = array("user_id" => $this->uid, "text" => "Das Schiff hat die " . $target['name'] . " zerst�rt", "type" => 1);
			$this->logbook[$data['id']][] = array("user_id" => $this->uid, "text" => "Das Schiff feuert in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->sysname . "-System)" : $data['cx'] . "|" . $data['cy']) . " mit Torpedos (" . $wd['name'] . ") auf die " . $target['name'] . " und richtet " . $pdmg . " Schaden an", "type" => 1);
			$this->logbook[$target['id']][] = array("user_id" => $target['user_id'], "text" => "Das Schiff wurde in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $this->m->sysname . "-System)" : $data['cx'] . "|" . $data['cy']) . " von der " . $data['name'] . " beschossen. Schaden: " . $pdmg . ($destroy == 1 ? "<br>Das Schiff wurde zerst�rt" : ""), "type" => 1);
		}
		if ($data['user_id'] != $target['user_id'] && $target['user_id'] != 1) $pm = $msg;
		return array("msg" => $msg, "pm" => $pm);
	}

	function trumfield($data, $name = "", $color = 1)
	{
		$this->dsships[$data['id']] = 1;
		if ($data['fleets_id'] > 0 && $this->db->query("SELECT fleets_id FROM stu_fleets WHERE ships_id=" . $data['id'] . " LIMIT 1", 1) > 0) {
			$sc = $this->db->query("SELECT id FROM stu_ships WHERE id!=" . $data['id'] . " AND fleets_id=" . $data['fleets_id'] . " ORDER BY RAND() LIMIT 1", 1);
			if ($sc > 0) $this->db->query("UPDATE stu_fleets SET ships_id=" . $sc . " WHERE fleets_id=" . $data['fleets_id'] . " LIMIT 1");
			else {
				$this->db->query("DELETE FROM stu_fleets WHERE fleets_id=" . $data['fleets_id'] . " LIMIT 1");
				$this->db->query("DELETE FROM stu_colonies_actions WHERE value=" . $data['fleets_id'] . " LIMIT 1");
			}
		}
		if (strlen($data['name']) == 0) return;
		if ($data['systems_id'] > 0) $tx = "Die " . $data['name'] . " (" . $data['rname'] . ") wurde im " . $this->m->sysname . "-System " . ($name != "" ? "" . $name . " zerst�rt" : "beim Sektoreinflug zerst�rt");
		else $tx = "Die " . $data['name'] . " (" . $data['rname'] . ") wurde in Sektor " . $data['cx'] . "|" . $data['cy'] . " " . ($name != "" ? "" . $name . " zerst�rt" : "beim Sektoreinflug zerst�rt");
		if ($data['rumps_id'] != 1) $this->db->query("INSERT INTO stu_history (message,date,type,ft_msg,coords_x,coords_y,user_id,color) VALUES ('" . addslashes($tx) . "',NOW(),'" . ($this->col_destroy == 1 ? 2 : 1) . "','" . strip_tags(str_replace("'", "", stripslashes($tx))) . "','" . $data['cx'] . "','" . $data['cy'] . "','" . $this->uid . "','" . $color . "')");
		if ($data['is_shuttle'] != 1) $this->db->query("UPDATE stu_ships SET user_id=1,huelle=" . ceil(($data['max_huelle'] / 100) * 15) . ",schilde=0,schilde_status=0,alvl=1,warpable=0,warpcore=0,traktor=0,traktormode=0,dock=0,crew=0,name='Tr�mmerfeld',eps=0,batt=0,nbs=0,lss=0,torp_type=0,rumps_id=8,trumps_id=8,cloak='0',fleets_id=0,warp='0',still=0,wea_phaser='0',wea_torp='0',is_rkn=0 WHERE id=" . $data['id'] . " LIMIT 1");
		else {
			$this->db->query("DELETE FROM stu_ships WHERE id=" . $data['id'] . " LIMIT 1");
			$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=" . $data['id']);
		}
		// $assigned = $this->db->query("SELECT * FROM stu_stations_fielddata WHERE ship = ".$data[id]."",1);
		// if ($assigned != 0)
		// {
		// if ($assigned[aktiv] == 1)
		// {
		// $this->db->query("UPDATE stu_stations SET bev_work = bev_work-3 WHERE id=".$assigned['stations_id']." LIMIT 1");

		// }
		// $this->db->query("DELETE FROM stu_stations_fielddata WHERE ship=".$data['id']." LIMIT 1");

		// }
		if ($data['rumps_id'] == 999) $this->upperstorage($data['id'], 998, 1);
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE traktor=" . $data['id'] . " LIMIT 2");
		$this->db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=" . $data['id']);
		$this->db->query("DELETE FROM stu_ships_decloaked WHERE ships_id=" . $data['id']);
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=" . $data['id'] . " LIMIT 1");
		$this->db->query("DELETE FROM stu_ships_storage WHERE goods_id>=80 AND goods_id<100 AND ships_id=" . $data['id']);
		$this->db->query("UPDATE stu_ships SET dock=0 WHERE dock=" . $data['id']);
		$this->db->query("DELETE FROM stu_ships_buildprogress WHERE ships_id=" . $data['id'] . " LIMIT 1");
		$this->db->query("UPDATE stu_ships_logdata SET destroytime=" . time() . " WHERE ships_id=" . $data['id'] . " LIMIT 1");
		$this->db->query("DELETE FROM stu_ships_shuttles WHERE ships_id=" . $data['id']);
		$this->db->query("DELETE FROM stu_dockingrights WHERE ships_id=" . $data['id'] . " OR (type='1' AND id=" . $data['id'] . ")");
		$result = $this->db->query("SELECT ships_id FROM stu_colonies_maintainance WHERE station_id=" . $data['id']);
		while ($dat = mysql_fetch_assoc($result)) $this->db->query("UPDATE stu_ships SET maintain=0 WHERE id=" . $dat['ships_id'] . " LIMIT 1");
		$this->db->query("DELETE FROM stu_colonies_maintainance WHERE station_id=" . $data['id']);
		$dat = $this->db->query("SELECT b.* FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b ON b.plans_id=a.plans_id LEFT JOIN stu_rumps as c ON c.rumps_id=b.rumps_id WHERE a.id=" . $data['id'] . " LIMIT 1", 4);
		$i = 1;

		for ($i = 1; $i <= 5; $i++) {
			if ($dat['m' . $i] == 0) continue;
			$dchg = $this->db->query("SELECT demontchg FROM stu_modules WHERE module_id=" . $dat['m' . $i] . " LIMIT 1", 1);
			if ($dchg == 0 || rand(1, 100) > $dchg / 2) continue;
			$this->upperstorage($data['id'], $dat['m' . $i], 1);
		}
		for ($i = 1; $i <= 2; $i++) {
			if ($dat['w' . $i] == 0) continue;
			$dchg = $this->db->query("SELECT demontchg FROM stu_modules WHERE module_id=" . $dat['w' . $i] . " LIMIT 1", 1);
			if ($dchg == 0 || rand(1, 100) > $dchg / 2) continue;
			$this->upperstorage($data['id'], $dat['w' . $i], 1);
		}
		for ($i = 1; $i <= 2; $i++) {
			if ($dat['s' . $i] == 0) continue;
			$dchg = $this->db->query("SELECT demontchg FROM stu_modules WHERE module_id=" . $dat['s' . $i] . " LIMIT 1", 1);
			if ($dchg == 0 || rand(1, 100) > $dchg / 2) continue;
			$this->upperstorage($data['id'], $dat['s' . $i], 1);
		}

		if (!check_int($data['user_id'])) return;
		if ($data['user_id'] < 100) return;
		$cc = $this->db->query("SELECT COUNT(*) FROM stu_colonies WHERE user_id=" . $data['user_id'], 1);
		if ($cc == 0) {
			if ($cs = $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE rumps_id=1 AND user_id=" . $data['user_id'], 1) == 0) $this->db->query("UPDATE stu_user SET level='0' WHERE id=" . $data['user_id'] . " LIMIT 1");
		}
	}

	function deletetrumfield($data)
	{
		$this->db->query("DELETE FROM stu_ships WHERE id=" . $data[id]);
		$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=" . $data[id]);
	}

	function getscbygood($shipId, $goodId)
	{
		return $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $shipId . " AND goods_id=" . $goodId, 1);
	}


	function cloakscan()
	{
		$return = shipexception(array("cloak" => 0, "eps" => 1, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];

		$cship = $this->db->query("SELECT a.id FROM stu_views_nbs as a LEFT JOIN stu_ally_relationship as b ON (a.allys_id=b.allys_id1 AND b.allys_id2=" . $this->sess['allys_id'] . ") OR (a.allys_id=b.allys_id2 AND b.allys_id1=" . $this->sess['allys_id'] . ") LEFT JOIN stu_ships_decloaked as c ON a.id=c.ships_id AND (c.user_id=" . $this->sess['uid'] . " || c.user_id=0) LEFT JOIN stu_contactlist as d ON d.user_id=a.user_id AND d.recipient=" . $this->sess['uid'] . " WHERE a.user_id!=" . $this->uid . " AND a.cloak=1 AND ISNULL(c.ships_id) AND " . ($this->systems_id > 0 ? "a.systems_id=" . $this->systems_id : "a.systems_id=0") . " AND " . ($this->systems_id > 0 ? "a.sx=" . $this->sx : "a.cx=" . $this->cx) . " AND " . ($this->systems_id > 0 ? "a.sy=" . $this->sy : "a.cy=" . $this->cy) . " GROUP BY a.id LIMIT 1", 1);

		$this->db->query("UPDATE stu_ships SET eps=eps-1 WHERE id=" . $this->id);

		if (!$cship) 			return "Keine Schiffe aufgesp�rt.";
		if (rand(1, 5) != 4) 	return "Keine Schiffe aufgesp�rt.";

		$this->db->query("REPLACE INTO `stu_ships_decloaked` (`ships_id`, `user_id`, `date`) VALUES ('" . $cship . "', '" . $this->uid . "', NOW());");

		return "Ein getarntes Schiff konnte entdeckt werden!";
	}



	function colonize($cid, $cf)
	{
		$data = $this->db->query("SELECT a.id,a.colonies_classes_id,a.user_id,a.sx,a.sy,a.systems_id,b.is_moon,b.level,b.research_id FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) WHERE a.id=" . $cid . " LIMIT 1", 4);
		if ($this->mod_s1 != 7001) return;
		if ($data == 0) return;
		if (checksector($data) == 0) return;
		if ($data['user_id'] != 1) return;
		$nf = getcolonizefield($data['colonies_classes_id']);
		if ($this->db->query("SELECT type FROM stu_colonies_fielddata WHERE field_id=" . $cf . " AND colonies_id=" . $cid . " LIMIT 1", 1) != $nf) return;
		if ($data['level'] > $this->sess['level']) return "Um diesen Planeten zu besiedeln wird Kolonisationslevel " . $data['level'] . " oder h�her ben�tigt";
		if ($data['research_id'] > 0 && $this->db->query("SELECT research_id FROM stu_researched WHERE research_id=" . $data['research_id'] . " AND user_id=" . $this->uid, 1) == 0) return "Um diesen Planeten zu kolonisieren wird eine Forschung ben�tigt";
		$return = shipexception(array("traktor" => -1, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->eps + $this->batt < 5) return "Zum Kolonisieren werden 5 Energie ben�tigt";
		if ($this->sess['level'] == 2) return;
		if ($this->sess['level'] == 3) {
			if ($data['is_moon'] != 1 && $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING (colonies_classes_id) WHERE a.user_id=" . $this->uid . " AND ISNULL(b.is_moon)", 1) >= 1) return "Es darf in diesem Level maximal 1 Planet besiedelt werden";
			if ($data['is_moon'] == 1 && $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING (colonies_classes_id) WHERE a.user_id=" . $this->uid . " AND b.is_moon='1'", 1) >= 1) return "Es d�rfen in diesem Level maximal 1 Mond besiedelt werden";
		}
		if ($this->sess['level'] == 4) {
			if ($data['is_moon'] != 1 && $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING (colonies_classes_id) WHERE a.user_id=" . $this->uid . " AND ISNULL(b.is_moon)", 1) >= 2) return "Es d�rfen in diesem Level maximal 2 Planeten besiedelt werden";
			if ($data['is_moon'] == 1 && $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING (colonies_classes_id) WHERE a.user_id=" . $this->uid . " AND b.is_moon='1'", 1) >= 2) return "Es d�rfen in diesem Level maximal 2 Monde besiedelt werden";
		}
		if ($this->sess['level'] >= 5) {
			if ($data['is_moon'] != 1 && $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING (colonies_classes_id) WHERE a.user_id=" . $this->uid . " AND ISNULL(b.is_moon)", 1) >= 3) return "Es d�rfen in diesem Level maximal 3 Planeten besiedelt werden";
			if ($data['is_moon'] == 1 && $this->db->query("SELECT COUNT(a.id) FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING (colonies_classes_id) WHERE a.user_id=" . $this->uid . " AND b.is_moon='1'", 1) >= 3) return "Es d�rfen in diesem Level maximal 3 Monde besiedelt werden";
		}
		if ($this->db->query("SELECT COUNT(id) FROM stu_colonies WHERE user_id=" . $this->uid . ";", 1) >= 10) return "Es k�nnen maximal 10 Kolonien gegr�ndet werden.";
		// if ($this->sess['level'] == 1)
		// {
		// $this->db->query("UPDATE stu_user SET level='2' WHERE id=".$this->uid." LIMIT 1");
		// global $_SESSION;
		// $_SESSION['level'] = 2;
		// }
		if ($this->fleets_id > 0 && $this->db->query("SELECT fleets_id FROM stu_fleets WHERE fleets_id=" . $this->fleets_id . " AND ships_id=" . $this->id, 1) > 0) {
			$this->db->query("DELETE FROM stu_fleets WHERE fleets_id=" . $this->fleets_id . " LIMIT 1");
			$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE fleets_id=" . $this->fleets_id);
		}
		$this->db->query("UPDATE stu_ships_logdata SET destroytime=" . time() . " WHERE ships_id=" . $this->id . " LIMIT 1");
		$this->db->query("UPDATE stu_colonies_fielddata SET type=" . $nf . ",buildings_id=1,integrity=120,aktiv=1 WHERE colonies_id=" . $cid . " AND field_id=" . $cf . " LIMIT 1");
		$this->db->query("UPDATE stu_colonies SET user_id=" . $this->uid . ",name='Kolonie',faction=" . $this->race . ",bev_free=50,max_eps=150,max_storage=5000,bev_max=50,eps=150,einwanderung='0' WHERE id=" . $cid . " LIMIT 1");
		$this->db->query("DELETE FROM stu_ships WHERE id=" . $this->id . " LIMIT 1");
		$this->colupperstorage($data['id'], 2, 500);
		$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=" . $this->id);
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=" . $this->id);
		$this->colonized = 1;
		if ($data['is_moon'] == 1) return "Das Kolonieschiff landet....<br>Der Mond wurde kolonisiert";
		else return "Das Kolonieschiff landet....<br>Der Planet wurde kolonisiert";
	}

	function ebatt($count)
	{
		if ($this->batt == 0) return "Die Ersatzbatterie ist leer";
		if ($this->eps >= $this->max_eps) return "Das EPS des Schiffes ist voll";
		if ($this->batt_wait > time()) return "Die Ersatzbatterie kann fr�hestens am " . date("d.m.Y H:i", $this->batt_wait) . " wieder geleert werden";
		$return = shipexception(array("crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->checksubsystem(8, $this->id) == 1) return "Das EPS-System ist besch�digt";
		if ($this->plans_id != 1 && $this->uid > 100 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance, "maintaintime" => $this->maintaintime)) > rand(1, 100)) return $this->damage_subsystem("foo", $this->id, 8);
		if ($count == "max" || $count > $this->batt) $count = $this->batt;
		if ($this->eps + $count > $this->max_eps) $count = $this->max_eps - $this->eps;
		if ($count > round($this->max_batt / 2)) $count = round($this->max_batt / 2);
		$this->db->query("UPDATE stu_ships SET eps=eps+" . $count . ",batt=batt-" . $count . ",batt_wait=" . ($this->batt - $count == 0 ? 0 : (time() + $count * 600)) . " WHERE id=" . $this->id);
		return "Die Ersatzbatterie wurde um " . $count . " Energie entladen";
	}

	function notruf()
	{
		global $_GET;
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships_ecalls WHERE user_id=" . $this->uid . " AND ships_id=" . $_GET[id], 1) != 0) return "F�r dieses Schiff existiert bereits ein Notruf";
		global $ship;
		if ($ship->systems_id > 0) $text = "Das Schiff befindet sich in Sektor " . $ship->sx . "|" . $ship->sy . " im " . $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $ship->systems_id, 1) . "-System (" . $ship->cx . "|" . $ship->cy . ")";
		else $text = "Das Schiff befindet sich au�erhalb eines Systems bei " . $ship->cx . "|" . $ship->cy;
		$this->db->query("INSERT INTO stu_ships_ecalls (user_id,ships_id,text,date) VALUES ('" . $this->uid . "','" . $this->id . "','" . $text . "',NOW())");
		return "Notruf erstellt";
	}

	function loadcto($colId)
	{
		$this->result = $this->db->query("SELECT a.mode,a.goods_id,b.name FROM stu_colonies_trade as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=" . $colId . " ORDER BY a.mode,b.sort");
	}

	function checksectorbase()
	{


		$bla = $this->db->query("SELECT a.*,b.name as cname,b.trumfield,c.user FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE b.slots>0 AND b.rumps_id!=1912 AND " . ($this->systems_id > 0 ? "a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " AND a.systems_id=" . $this->systems_id : "a.cx=" . $this->cx . " AND a.cy=" . $this->cy . " AND a.systems_id=0"), 4);
		if ($bla != 0) return $bla;
		if ($this->systems_id == 0) return 0;
		return $this->db->query("SELECT a.*,b.name as cname,c.user FROM stu_stations as a LEFT JOIN stu_stations_classes as b USING(stations_classes_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " AND a.systems_id=" . $this->systems_id . "", 4);
	}

	function warpon()
	{
		$return = shipexception(array("warp" => 1, "eps" => 2, "dock" => 0, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->checksubsystem(11, $this->id) == 1) return "Der Warpantrieb ist besch�digt";
		if ($this->plans_id != 1 && $this->uid > 100 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance, "maintaintime" => $this->maintaintime)) > rand(1, 100)) return $this->damage_subsystem("foo", $this->id, 11);
		if ($this->systems_id > 0) return "Das Schiff befindet sich in einem System";
		$this->db->query("UPDATE stu_ships SET warp='1',eps=eps-2 WHERE id=" . $this->id);
		$this->eps -= 2;
		$this->warp = 1;
		return "Das Schiff befindet sich jetzt im Warp";
	}

	function warpoff()
	{
		$return = shipexception(array("warp" => 1), $this);
		if ($return['code'] == 1) return $return['msg'];
		$this->db->query("UPDATE stu_ships SET warp='0' WHERE id=" . $this->id . " LIMIT 1");
		$this->warp = '';
		$ramsg = $this->redalert();
		return "Das Schiff befindet sich jetzt nicht mehr im Warp" . $ramsg;
	}

	function loadsysoffers($system_id)
	{
		$this->co = $this->db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=" . $system_id . " AND b.mode='1' GROUP BY b.goods_id ORDER BY c.sort");
		$this->cw = $this->db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=" . $system_id . " AND b.mode='2' GROUP BY b.goods_id ORDER BY c.sort");
	}

	function generateszcode()
	{
		global $_SESSION;
		$sc = substr(md5(ceil($_SESSION['logintime'] / $this->id)), 0, 6);
		$_SESSION['szcode'] = $sc;
	}

	function selfdestruct($id, $sc)
	{
		$data = $this->db->query("SELECT id,rumps_id,user_id,name,warpcore,systems_id,sx,sy,cx,cy,huelle,max_huelle,fleets_id FROM stu_ships WHERE id=" . $id . " AND user_id=" . $this->uid, 4);
		if ($data == 0) die(show_error(902));
		global $_SESSION;
		if ($_SESSION['szcode'] != $sc) {
			$_SESSION['szcode'] = "";
			return "Der Code ist ung�ltig";
		}
		$_SESSION['szcode'] = "";
		if ($data['rumps_id'] != 1) {
			if ($data['systems_id'] > 0) {
				$md = $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $data['systems_id'], 1);
				$tx = "Die " . addslashes($data['name']) . " hat sich im " . $md . "-System zerst�rt";
				$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg,user_id) VALUES ('" . $tx . "',NOW(),'1','" . strip_tags($tx) . "','" . $this->uid . "')");
			} else {
				$md = $this->db->query("SELECT cx,cy FROM stu_map WHERE cx BETWEEN " . ($data['cx'] - 5) . " AND " . ($data['cx'] + 5) . " AND cy BETWEEN " . ($data['cy'] - 5) . " AND " . ($data['cy'] + 5) . " ORDER BY RAND() LIMIT 1", 4);
				$tx = "Die " . addslashes($data['name']) . " hat sich in der N�he von " . $md['cx'] . "|" . $md['cy'] . " zerst�rt";
				$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg,user_id) VALUES ('" . $tx . "',NOW(),'1','" . strip_tags($tx) . "','" . $this->uid . "')");
			}
		}
		if ($data['fleets_id'] > 0) {
			$fl = $this->db->query("SELECT fleets_id FROM stu_fleets WHERE ships_id=" . $data['id'] . " LIMIT 1", 1);
			if ($fl > 0) {
				$this->db->query("DELETE FROM stu_fleets WHERE fleets_id=" . $fl . " LIMIT 1");
				$this->db->query("DELETE FROM stu_colonies_actions WHERE var=" . $fl . " LIMIT 1");
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE fleets_id=" . $fl . " LIMIT 25");
			}
		}
		$this->db->query("UPDATE stu_ships_logdata SET destroytime=" . time() . " WHERE ships_id=" . $id . " LIMIT 1");
		$this->db->query("UPDATE stu_ships SET user_id=1,huelle=" . (floor(($data['max_huelle'] / 100) * 15)) . ",schilde=0,schilde_status=0,alvl=1,warpable=0,warpcore=0,traktor=0,traktormode=0,dock=0,crew=0,name='Tr�mmerfeld',eps=0,batt=0,nbs=0,lss=0,torp_type=0,rumps_id=8,trumps_id=8,cloak='0',fleets_id=0,warp='0',wea_phaser='0',wea_torp='0' WHERE id=" . $data['id'] . " LIMIT 1");
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0,dock=0 WHERE traktor=" . $data['id'] . " LIMIT 2");
		$this->db->query("UPDATE stu_ships SET dock=0 WHERE dock=" . $data['id']);
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=" . $id);
		$this->db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=" . $id);
		$this->db->query("DELETE FROM stu_ships_shuttles WHERE ships_id=" . $id);
		$this->db->query("DELETE FROM stu_ships_buildprogress WHERE ships_id=" . $id);
		if ($data['user_id'] < 100) return "Selbstzerst�rung erfolgreich";
		$cs = $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE rumps_id=1 AND user_id=" . $this->uid, 1);
		$cc = $this->db->query("SELECT COUNT(*) FROM stu_colonies WHERE user_id=" . $this->uid, 1);
		if ($cc == 0 && $cs == 0) {
			$_SESSION['level'] = 0;
			$this->db->query("UPDATE stu_user SET level='0' WHERE id=" . $this->uid);
		}
		return "Selbstzerst�rung erfolgreich";
	}

	function kartographie()
	{
		if ($this->db->query("SELECT systems_id FROM stu_systems_user WHERE systems_id=" . $this->systems_id . " AND user_id=" . $this->uid . " AND infotype = 'map' LIMIT 1", 1) > 0) return "Dieses System wurde bereits kartographiert";
		if ($this->systems_id > 0 && $this->still > 0) return "Das System wird bereits kartographiert";
		$return = shipexception(array("schilde_status" => 0, "cloak" => 0, "nbs" => 1, "eps" => 10, "warpstate" => 0, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		$sys = $this->m->getsystembyid($this->systems_id);
		$time = $sys[sr] * 900;
		$this->db->query("UPDATE stu_ships SET still=" . (time() + $time) . ",eps=eps-10 WHERE id=" . $this->id . " LIMIT 1");
		return "Die Kartographierung hat begonnen - Voraussichtliche Fertigstellung: " . date("d.m.Y H:i", (time() + $time));
	}

	function stopkartographie()
	{
		if ($this->still == 0) return;
		$this->db->query("UPDATE stu_ships SET still=0 WHERE id=" . $this->id . " LIMIT 1");
		return "Die Kartografierung wurde abgebrochen";
	}

	function timeSort($a, $b)
	{
	}

	function sectorscan()
	{
		$return = shipexception(array("cloak" => 0, "nbs" => 1, "eps" => 2, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];

		$this->db->query("UPDATE stu_ships SET eps=eps-2 WHERE id=" . $this->id);

		$locstring = " AND " . ($this->systems_id > 0 ? "a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " AND a.systems_id=" . $this->systems_id : "a.cx=" . $this->cx . " AND a.cy=" . $this->cy . " AND a.systems_id = 0");

		$res = $this->db->query("SELECT a.*,UNIX_TIMESTAMP(a.date) as date_tsp, b.type as rtype,b.name as rname FROM stu_sectorflights as a LEFT JOIN stu_rumps as b ON a.rumps_id = b.rumps_id WHERE a.ships_id!=" . $this->id . " AND UNIX_TIMESTAMP(a.date)>" . (time() - 36000) . " AND a.cloak!='1' " . $locstring . " ORDER BY date_tsp DESC;");
		$a = array();
		while ($b = mysql_fetch_assoc($res)) {
			$b['type'] = "warp";
			array_push($a, $b);
		}

		$res = $this->db->query("SELECT a.*,UNIX_TIMESTAMP(a.date) as date_tsp, b.type as rtype,b.name as rname FROM stu_sectorflights as a LEFT JOIN stu_rumps as b ON a.rumps_id = b.rumps_id WHERE a.ships_id!=" . $this->id . " AND UNIX_TIMESTAMP(a.date)>" . (time() - 36000) . " AND a.cloak='1' " . $locstring . " ORDER BY date_tsp DESC LIMIT 1;");
		while ($b = mysql_fetch_assoc($res)) {
			$b['type'] = "cloak";
			array_push($a, $b);
		}

		usort($a, function ($a, $b) {
			if ($a['date_tsp'] == $b['date_tsp']) return 0;
			else if ($a['date_tsp'] > $b['date_tsp']) return -1;
			else return 1;
		});




		$data['ss'] = $a;
		return $data;
	}

	function setdecloaked($shipId, $userid, $name)
	{
		$this->db->query("INSERT INTO stu_ships_decloaked (ships_id,user_id) VALUES ('" . $shipId . "','" . $this->uid . "')");
		$this->send_pm($this->uid, $userid, "Der Siedler " . addslashes($this->sess['user']) . " hat die " . addslashes($name) . " enttarnt", 3);
	}

	function lowertorpedo(&$shipId, &$torp)
	{
		$data = $this->db->query("SELECT a.goods_id,b.count FROM stu_torpedo_types as a LEFT JOIN stu_ships_storage as b ON a.goods_id=b.goods_id AND b.ships_id=" . $shipId . " WHERE a.torp_type=" . $torp . " LIMIT 1", 4);
		if ($data == 0) {
			$this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=" . $shipId . " LIMIT 1");
			return;
		}
		$this->lowerstorage($shipId, $data['goods_id'], 1);
		if ($data['count'] == 1) $this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=" . $shipId . " LIMIT 1");
		return;
	}

	function redalert()
	{
		if ($this->cloak == 1)  return;
		$result = $this->db->query("SELECT a.id FROM stu_views_fight as a LEFT JOIN stu_contactlist as b ON a.user_id=b.user_id AND b.recipient=" . $this->uid . " LEFT JOIN stu_ally_relationship as c ON ((a.allys_id=c.allys_id1 AND c.allys_id2=" . $this->sess['allys_id'] . ") OR (a.allys_id=c.allys_id2 AND c.allys_id1=" . $this->sess['allys_id'] . ")) WHERE a.user_id!=" . $this->uid . " AND (a.alvl='3' OR (a.alvl='2' AND (b.mode='3' OR c.type='1'))) AND (ISNULL(c.type) OR c.type='1' OR c.type='2') AND ((a.allys_id>0 AND a.allys_id!=" . $this->sess['allys_id'] . ") OR a.allys_id=0) AND (ISNULL(b.mode) OR b.mode='2' OR b.mode='3') AND " . ($this->systems_id > 0 ? "a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " AND a.systems_id=" . $this->systems_id : "a.systems_id=0 AND a.cx=" . $this->cx . " AND a.cy=" . $this->cy . "") . " AND (ISNULL(a.warp) OR a.warp='' OR a.warp='0')");
		if (mysql_num_rows($result) == 0) return;
		$msg = "Anzahl feuerbereiter Schiffe: " . mysql_num_rows($result) . "<br />";
		$chance = getrashipchance(mysql_num_rows($result));
		$i = 1;
		while ($data = mysql_fetch_assoc($result)) {
			if ($this->dsships[$this->id]) continue;
			$return = $this->attack($this->id, $data['id'], 0, 0, 1);
			if (!$return) continue;
			$msg .= $return;
			if ($i >= $chance) break;;
			$i++;
		}
		return "<br>" . $msg;
	}

	function fleet_redalert()
	{
		global $fleet;
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships WHERE fleets_id=" . $this->fleets_id . " AND (ISNULL(cloak) OR cloak='' OR cloak!='1')", 1) == 0) return;
		$result = $this->db->query("SELECT a.id FROM stu_views_fight as a LEFT JOIN stu_contactlist as b ON a.user_id=b.user_id AND b.recipient=" . $this->uid . " LEFT JOIN stu_ally_relationship as c ON ((a.allys_id=c.allys_id1 AND c.allys_id2=" . $this->sess['allys_id'] . ") OR (a.allys_id=c.allys_id2 AND c.allys_id1=" . $this->sess['allys_id'] . ")) WHERE a.user_id!=" . $this->uid . " AND (a.alvl='3' OR (a.alvl='2' AND (b.mode='3' OR c.type='1'))) AND (ISNULL(c.type) OR c.type='1' OR c.type='2') AND ((a.allys_id>0 AND a.allys_id!=" . $this->sess['allys_id'] . ") OR a.allys_id=0) AND (ISNULL(b.mode) OR b.mode='2' OR b.mode='3') AND " . ($fleet->systems_id > 0 ? "a.sx=" . $fleet->nx . " AND a.sy=" . $fleet->ny . " AND a.systems_id=" . $fleet->systems_id : "a.systems_id=0 AND a.cx=" . $fleet->nx . " AND a.cy=" . $fleet->ny . "") . " AND (ISNULL(a.warp) OR a.warp='' OR a.warp='0')");
		if (mysql_num_rows($result) == 0) return;
		$msg = "Anzahl feuerbereiter Schiffe: " . mysql_num_rows($result) . "<br />";
		$chance = getrashipchance(mysql_num_rows($result));
		$i = 1;
		while ($data = mysql_fetch_assoc($result)) {
			$target = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=" . $this->fleets_id . " ORDER BY RAND() LIMIT 1", 1);
			if ($this->dsships[$target['id']]) continue;
			$return = $this->attack($target, $data['id'], 0, 0, 1);
			if (!$return) continue;
			$msg .= $return;
			if ($i >= $chance) break;
			$i++;
		}
		return "<br>" . $msg;
	}

	function checksubsystem($system, &$shipId)
	{
		return $this->db->query("SELECT COUNT(*) FROM stu_ships_subsystems WHERE ships_id=" . $shipId . " AND system_id=" . $system . " LIMIT 1", 1);
	}



	function damage_subsystem($ship, $shipId, $sys = "")
	{
		// keine wartung mehr!
		return;

		if ($shipId == 1) return;
		if (!check_int($sys)) {
			$systems = generatesubsystemlistbyarr($ship, $shipId);
			$index = $systems[array_rand($systems)];
			$sys = $index;
		} else $index = $sys;
		switch ($index) {
			case 2:
				$dt = rand(3600, 7200);
				$this->db->query("UPDATE stu_ships SET schilde_status=0 WHERE id=" . $shipId . " LIMIT 1");
				$this->shoff = 1;
				break;
			case 4:
				$dt = rand(1800, 5400);
				$this->db->query("UPDATE stu_ships SET nbs=NULL,lss=NULL WHERE id=" . $shipId . " LIMIT 1");
				$this->senoff = 1;
				break;
			case 5:
				$dt = rand(3600, 18000);
				break;
			case 6:
				$dt = rand(1800, 7200);
				$this->db->query("UPDATE stu_ships SET wea_phaser='0' WHERE id=" . $shipId . " LIMIT 1");
				break;
			case 7:
				if (rand(1, 30) != 1 && check_int($sys)) return;
				$dt = rand(1800, 5400);
				break;
			case 8:
				$dt = rand(600, 1800);
				break;
			case 9:
				$dt = rand(3600, 36000);
				$this->db->query("UPDATE stu_ships SET cloak='0' WHERE id=" . $shipId . " LIMIT 1");
				break;
			case 10:
				$dt = rand(1800, 7200);
				$this->db->query("UPDATE stu_ships SET wea_torp='1' WHERE id=" . $shipId . " LIMIT 1");
				break;
			case 11:
				if (rand(1, 30) != 1 && check_int($sys)) return;
				$dt = rand(3600, 86400);
				$this->db->query("UPDATE stu_ships SET warp='0' WHERE id=" . $shipId . " LIMIT 1");
				break;
		}
		$this->dmsubs[$shipId][$index] = 1;
		if ($this->db->query("SELECT b.race FROM stu_ships as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=" . $shipId . " LIMIT 1", 1) == 1) $dt = round($dt / 2);
		$this->db->query("INSERT INTO stu_ships_subsystems (ships_id,system_id,date) VALUES ('" . $shipId . "','" . $index . "','" . (time() + $dt) . "')");
		return "System (" . getmodtypedescr($index) . ")" . (!check_int($sys) ? " ist aufgrund der �berf�lligen Wartung ausgefallen" : " wurde besch�digt") . " - Voraussichtliche Reparaturdauer: " . gen_time($dt);
	}

	function changeshiphud()
	{
		$this->db->query("UPDATE stu_ships SET hud='" . $this->hud . "' WHERE id=" . $this->id);
		return "HUD umgeschaltet";
	}
	function intercept($tar)
	{
		if ($this->id == $tar) return;
		if ($this->systems_id > 0) return;
		$return = shipexception(array("nbs" => 1, "eps" => 1, "warpstate" => 1, "cloak" => 0, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return "Abfangen nicht m�glich (Grund: " . $return['msg'] . ")";
		$target = $this->db->query("SELECT a.id,a.fleets_id,a.user_id,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.name,a.eps,a.cloak,a.torp_type,a.phaser,a.warp,a.crew,a.min_crew,a.eps,c.warp_capability,d.vac_active FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_modules as c ON b.m11=c.module_id LEFT JOIN stu_user as d ON d.id=a.user_id WHERE a.warp='1' AND a.id=" . $tar . " LIMIT 1", 4);
		if ($target == 0) return;
		if ($target['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($target['user_id'] == $this->sess['uid']) return;
		if ($target['cloak'] == 1 && $this->db->query("SELECT ships_id FROM stu_ships_decloaked WHERE UNIX_TIMESTAMP(date)=0 AND ships_id=" . $target['id'] . " AND user_id=" . $this->uid . " LIMIT 1", 1) == 0) return;
		if ($target['fleets_id'] > 0 && $target['warp_capability'] < 9) $target = $this->db->query("SELECT a.id,a.fleets_id,a.user_id,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.name,a.warp,a.crew,a.min_crew,a.eps,c.warp_capability,d.name as fname FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_modules as c ON b.m11=c.module_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id WHERE a.warp='1' AND a.fleets_id=" . $target['fleets_id'] . " ORDER BY c.warp_capability LIMIT 1", 4);
		if ($this->cx != $target['cx'] || $this->cy != $target['cy']) return;
		$this->warp_capability = $this->db->query("SELECT warp_capability FROM stu_modules WHERE module_id=" . $this->m11 . " LIMIT 1", 1);
		if ($target['warp_capability'] > $this->warp_capability) $chance = 10 - ($target['warp_capability'] - $this->warp_capability);
		else $chance = 10 + (($this->warp_capability - $target['warp_capability']) * 20);
		if (rand(1, 100) <= $chance) {
			global $fleet;
			$msg .= "Die " . ($target['fleets_id'] > 0 ? "Flotte " . $target['fname'] : $target['name']) . " wurde erfolgreich abgefangen";
			$this->db->query("UPDATE stu_ships SET eps=eps-1,warp='0' WHERE id=" . $this->id . " LIMIT 1");
			$ramsg = $this->redalert();
			if ($target['fleets_id'] > 0) {
				$this->db->query("UPDATE stu_ships SET warp='0' WHERE fleets_id=" . $target['fleets_id'] . " LIMIT 25");
				$rm = $fleet->strikeback($this->id, $target['fleets_id'], 0);
				if (strlen(strip_tags($rm)) > 0)  $msg .= "<br><b>Die abgefangene Flotte formiert sich zum Angriff</b><br>" . $rm;
			} else {
				$this->db->query("UPDATE stu_ships SET warp='0' WHERE id=" . $target['id'] . " LIMIT 1");
				if ($target['eps'] > 0 && $target['crew'] >= $target['crew_min']) {
					$rm = $this->attack($this->id, $target['id'], 0, 1);
					if (strlen(strip_tags($rm)) > 0)  $msg .= "<br><b>Das abgefangene Schiff startet ein Angriffsman�ver</b><br>" . $rm;
				}
			}
			$this->send_pm($this->uid, $target['user_id'], "Die " . ($target['fleets_id'] > 0 ? "Flotte " . $target['fname'] : $target['name']) . " wurde von der " . $this->name . " in Sektor " . $this->cx . "|" . $this->cy . " abgefangen", 3);
		} else {
			$this->db->query("UPDATE stu_ships SET eps=eps-1 WHERE id=" . $this->id . " LIMIT 1");
			$msg .= "Die " . ($target['fleets_id'] > 0 ? "Flotte " . $target['fname'] : $target['name']) . " konnte nicht abgefangen werden";
			$this->send_pm($this->uid, $target['user_id'], "Die " . $this->name . " hat versucht die " . ($target['fleets_id'] > 0 ? "Flotte " . $target['fname'] : $target['name']) . " in Sektor " . $this->cx . "|" . $this->cy . " abzufangen", 3);
		}
		return $msg . $ramsg;
	}

	function buildkonstrukt()
	{
		return "Derzeit nicht m�glich.";
		$return = shipexception(array("nbs" => 1, "schilde_status" => 0, "cloak" => 0, "eps" => 30, "warpstate" => 0, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->sess['wpo'] > 0) return "Stationsbau ist noch f�r " . $this->sess['wpo'] . " Runde(n) gesperrt";
		if ($this->map[sensoroff] == 1) return "Hier kann keine Basis errichtet werden";
		$result = $this->db->query("SELECT a.id FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE " . ($this->systems_id > 0 ? "systems_id=0 AND cx=" . $this->cx . " AND cy=" . $this->cy : "systems_id=" . $this->systems_id . " AND sx=" . $this->sx . " AND sy=" . $this->sy) . " AND b.slots>0", 1);
		if ($result > 0) return "In diesem Sektor befindet sich bereits eine Station";
		if ($this->db->query("SELECT COUNT(a.id) FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE b.slots>0 AND a.user_id=" . $this->uid, 1) >= 18 && $this->uid > 100) return "Es k�nnen nicht mehr als 5 Stationen gebaut werden";
		$count = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=2 AND ships_id=" . $this->traktor, 1);
		if ($count < 150) return "F�r den Bau eines Konstrukts werden 150 Baumaterial ben�tigt - Vorhanden sind nur " . (!$count ? 0 : $count) . "<br>Diese Ware muss sich auf einem Schiff befinden, welches die Workbee im Traktorstrahl h�lt";
		$this->db->query("INSERT INTO stu_ships (rumps_id,user_id,systems_id,sx,sy,cx,cy,direction,name,max_eps,huelle,max_huelle,cfield)
		 VALUES ('9','" . $this->uid . "','" . $this->systems_id . "','" . ($this->systems_id > 0 ? $this->sx : 0) . "','" . ($this->systems_id > 0 ? $this->sy : 0) . "'
		 ,'" . $this->cx . "','" . $this->cy . "','1','Konstrukt','400','50','50','" . $this->cfield . "')");
		$this->lowerstorage($this->traktor, 2, 150);
		$this->db->query("UPDATE stu_ships SET eps=eps-30 WHERE id=" . $this->id);
		return "Konstrukt errichtet";
	}

	function dock($target)
	{
		if ($this->id == $target) return;
		$return = shipexception(array("traktor" => 0, "schilde_status" => 0, "cloak" => 0, "eps" => 1, "warpstate" => 0, "fleet" => 0, "nbs" => 1, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		$target = $this->db->query("SELECT a.id,a.user_id,a.name,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.schilde_status,b.slots FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id=" . $target, 4);
		if ($target == 0) return;
		if (checksector($target) == 0) return;
		if ($target[slots] == 0) return;
		if ($target[schilde_status] == 1) return "Die Station hat die Schilde aktiviert";
		if ($this->fleets_id > 0) return "Flottenschiffe k�nnen nicht andocken";
		if ($target[slots] <= $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE dock=" . $target['id'], 1)) return "Alle Dockpl�tze sind bereits belegt";
		if ($target[user_id] != $this->uid && !$this->checkDockingRights($target)) return "Andockerlaubnis verweigert";
		$this->db->query("UPDATE stu_ships SET dock=" . $target[id] . ",eps=eps-1 WHERE id=" . $this->id . " LIMIT 1");
		return "Die " . $this->name . " hat an der " . $target[name] . " angedockt";
	}

	function undock()
	{
		$return = shipexception(array("eps" => 1, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		$this->db->query("UPDATE stu_ships SET dock=0,eps=eps-1 WHERE id=" . $this->id . " LIMIT 1");
		return "Die " . $this->name . " hat abgedockt";
	}

	function checkIfIscartogryphed($systemid)
	{
		return $this->db->query("SELECT systems_id FROM stu_systems_user WHERE systems_id=" . $systemid . " AND user_id=" . $this->uid . " LIMIT 1", 1);
	}

	function systemIsMapped($systemid)
	{
		return $this->db->query("SELECT systems_id FROM stu_systems_user WHERE systems_id=" . $systemid . " AND user_id=" . $this->uid . " AND infotype = 'map' LIMIT 1", 1);
	}

	function attackcolfield($colid, $fieldid)
	{
		return "Der Kolonieangriff  ist vor�bergehend deaktiviert.";
		$return = shipexception(array("cloak" => 0, "eps" => 1, "crew" => $this->min_crew, "nbs" => 1), $this);
		if ($return['code'] == 1) return $return['msg'];

		$blockade = $this->db->query("SELECT * FROM stu_colonies_actions WHERE colonies_id =" . $colid . " AND (var='fdef' OR var='fblock' OR var='fattack')", 4);
		if ($blockade != 0) {
			if ($blockade['var'] == "fdef") $blocktext = "verteidigende";
			elseif ($blockade['var'] == "fblock") $blocktext = "blockierende";
			else $blocktext = "angreifende";
			$blocktext = "Eine " . $blocktext . " Flotte verhindert ein Bombardement dieser Kolonie.<br>";
			if ($blockade['var'] != $this->fleets_id) return $blocktext;
		}
		if ($this->wea_phaser == 0 && $this->wea_torp == 0) return "Es ist kein Waffensystem aktiviert";
		$col = $this->db->query("SELECT a.id,a.name,a.colonies_classes_id,a.user_id,a.eps,a.max_schilde,a.schilde,a.schilde_status,a.max_eps,a.bev_free,b.is_moon,b.atmosphere,c.vac_active,c.race FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=" . $colid, 4);
		if ($col[vac_active] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if (($col[is_moon] != 1 && $fieldid > 72) || ($col[is_moon] == 1 && $fieldid > 49)) return;
		$field = $this->db->query("SELECT a.field_id,a.buildings_id,a.aktiv,a.integrity,b.name,b.bev_use,bev_pro,eps,lager,schilde FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=" . $colid . " AND a.field_id=" . $fieldid, 4);
		if ($field[buildings_id] == 0) return;
		if (iscolcent($field[buildings_id])) return "Dieses Geb�ude kann nicht erfasst werden";
		if ($this->wea_phaser == 1 && $this->wea_torp == 0) {
			$wp = $this->db->query("SELECT name,wtype,eps_cost,pulse,varianz,strength,mgoods_id,mcount FROM stu_weapons WHERE module_id=" . $this->m6, 4);
			if ($wp[eps_cost] > $this->eps) return "Es wird " . $wp[eps_cost] . " Energie ben�tigt - Vorhanden ist nur " . $ship->eps;
			$minvari = round(($this->phaser / 10) * (100 - $wp[varianz]));
			if ($minvari < 0.1) $minvari = 0.1;
			$maxvari = round(($this->phaser / 10) * (100 + $wp[varianz]));
			$wd[pulse] == 0 ? $ro = 1 : $ro = $wd[pulse];
			$msg = "<b>Die " . $this->name . " feuert mit einer Strahlenwaffe (" . $wp[name] . ") auf das Geb�ude (" . $field[name] . ") auf Feld " . $fieldid . " auf der Kolonie " . $col[name] . "</b>";
		} else {
			if ($this->torp_type == 0) return "Es sind keine Torpedos geladen";
			$wp = $this->db->query("SELECT name,goods_id,damage,varianz FROM stu_torpedo_types WHERE torp_type=" . $this->torp_type, 4);
			$tofimo = $this->db->query("SELECT torp_fire_amount FROM stu_modules WHERE module_id=" . $this->m10, 1);
			$minvari = round(($wp[damage] / 10) * (100 - $wp[varianz]));
			if ($minvari < 1) $minvari = 1;
			$maxvari = round(($wp[damage] / 10) * (100 + $wp[varianz]));
			$tofimo > $this->eps ? $ro = $this->eps : $ro = $tofimo;
			$ro = 1;
			$msg = "<b>Die " . $this->name . " feuert " . ($ro == 1 ? "mit einem Torpedo" : $ro . " Torpedos") . " (" . $wp[name] . ") auf das Geb�ude (" . $field[name] . ") auf Feld " . $fieldid . " auf der Kolonie " . $col[name] . "</b>";
			$pvp = $this->db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE buildings_id=325 AND colonies_id=" . $col[id], 1);
		}
		$i = 1;
		while ($i <= $ro) {
			$field = $this->db->query("SELECT a.field_id,a.buildings_id,a.aktiv,a.integrity,b.name,b.bev_use,bev_pro,eps,lager,schilde FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=" . $colid . " AND a.field_id=" . $fieldid . " LIMIT 1", 4);
			if ($field['buildings_id'] == 0) {
				$msg .= "<br />Das Geb�ude auf diesem Feld ist zerst�rt";
				break;
			}
			$dmg = round(rand($minvari, $maxvari) / 10, 1);
			if ($this->wea_phaser == 1 && $this->wea_torp == 0) {
				if ($wp[mgoods_id] > 0) {
					if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $this->id . " AND goods_id=" . $wp[mgoods_id], 1) == 0) {
						$msg .= "<br>Keine Munition mehr vorhanden";
						break;
					} else $this->lowerstorage($this->id, $wp[mgoods_id], $wp[mcount]);
				}
				if (($fieldid > 18 && $col[is_moon] != 1) || ($fieldid > 14 && $col[is_moon] == 1)) {
					if ($dmg <= $col[atmosphere]) {
						$msg .= "<br>Der Schu� wurde von der Atmosphere des Planeten absorbiert";
						$i++;
						continue;
					} else $dmg -= $col[atmosphere];
				}
			} else {
				if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $this->id . " AND goods_id=" . $wp[goods_id], 1) == 0) {
					$msg .= "<br>Keine Munition mehr vorhanden";
					break;
				}
				$this->lowerstorage($this->id, $wp[goods_id], 1);
				if ($pvp > 0 && $col[eps] > 0) {
					if (rand(1, 100) <= round(sqrt($pvp) * 20)) {
						$col[eps] -= 1;
						$msg .= "<br>Der Torpedo wurde von einer Punkt-Verteidigungs-Phalanx abgefangen";
						$i++;
						continue;
					}
				}
			}
			if ((($fieldid > 18 && $col[is_moon] != 1) || ($fieldid > 14 && $col[is_moon] == 1)) && $col[schilde_status] == 1) {
				if ($col[schilde] <= $dmg) {
					$this->db->query("UPDATE stu_colonies SET schilde=0,schilde_status=0 WHERE id=" . $col['id'] . " LIMIT 1");
					$this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE buildings_id=100 AND colonies_id=" . $col['id'] . " LIMIT 1");
					$msg .= "<br>- Die planetaren Schilde absorbieren " . ($dmg - $col['schilde']) . " Schaden und fallen aus";
					$dmg -= $col[schilde];
					if ($dmg == 0) {
						$i++;
						continue;
					}
				} else {
					$msg .= "<br>- Die planetaren Schilde absorbieren " . $dmg . " Schaden";
					$this->db->query("UPDATE stu_colonies SET schilde=schilde-" . $dmg . " WHERE id=" . $col[id] . " LIMIT 1");
					$col[schilde] -= $dmg;
					$i++;
					continue;
				}
			}
			if ($dmg < $field[integrity]) {
				$msg .= "<br>- Der Beschuss richtet " . $dmg . " Schaden am Geb�ude an - Zustand: " . ($field[integrity] - $dmg);
				$this->db->query("UPDATE stu_colonies_fielddata SET integrity=integrity-" . $dmg . " WHERE field_id=" . $fieldid . " AND colonies_id=" . $col[id] . " LIMIT 1");
				$field[integrity] -= $dmg;
				$i++;
				continue;
			}
			if ($dmg >= $field[integrity]) {
				$msg .= "<br>- Das Geb�ude wurde zerst�rt";
				if ($field[aktiv] == 1) {
					$msg .= "<br>- Es gab " . $field[bev_use] . " Tote";
					if ($field['bev_pro'] > 0 && $col['bev_free'] > 0 && $col['user_id'] != $this->uid) {
						$rnd = rand(1, $col['bev_free']);
						$bf = ",bev_free=bev_free-" . $rnd;
						$col['bev_free'] -= $rnd;
						$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg,user_id) VALUES ('Bei der Bombardierung einer Wohnanlage auf der Kolonie " . $col['name'] . " kamen " . $rnd . " " . getfactionname($col['race']) . " ums Leben',NOW(),'2','Bei der Bombardierung einer Wohnanlage auf der Kolonie " . strip_tags(str_replace("'", "", stripslashes($col['name']))) . " kamen " . $rnd . " " . getfactionname($col['race']) . " ums Leben','" . $this->uid . "')");
					}
					$this->db->query("UPDATE stu_colonies SET bev_work=bev_work-" . $field[bev_use] . $bf . " WHERE id=" . $col[id]);
					$bf = "";
				}
				if ($field['eps'] > 0 && $col['max_eps'] - $field['eps'] < $col['eps']) $col['eps'] = $col['max_eps'] - $field['eps'];
				$this->db->query("UPDATE stu_colonies SET bev_max=bev_max-" . $field[bev_pro] . ",max_eps=max_eps-" . $field[eps] . ",max_storage=max_storage-" . $field[lager] . ",max_schilde=max_schilde-" . $field['schilde'] . ($col['schilde'] > $col['max_schilde'] - $field['schilde'] ? ",schilde=" . ($col['max_schilde'] - $field['schilde']) : "") . " WHERE id=" . $col['id'] . " LIMIT 1");
				$this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=0,aktiv=0,integrity=0 WHERE field_id=" . $fieldid . " AND colonies_id=" . $col[id] . " LIMIT 1");
				if ($field[buildings_id] == 19) {
					$orbit = $this->db->query("SELECT SUM(b.bev_pro) as bp, SUM(b.bev_use) as bu FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=" . $col[id] . " AND a.field_id<=" . ($col[is_moon] == 1 ? 14 : 18), 4);
					$this->db->query("UPDATE stu_colonies SET bev_free=bev_free+" . (!$orbit[bu] ? 0 : $orbit[bu]) . ",bev_work=bev_work-" . (!$orbit[bu] ? 0 : $orbit[bu]) . ",bev_max=bev_max-" . (!$orbit[bp] ? 0 : $orbit[bp]) . " WHERE id=" . $col[id]);
					$this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE aktiv=1 AND buildings_id < 400 AND colonies_id=" . $col[id] . " AND field_id<=" . ($col[is_moon] == 1 ? 14 : 18));
				}
				if ($field[buildings_id] == 401) {
					$this->db->query("UPDATE stu_colonies SET beamblock='0' WHERE id=" . $col[id]);
				}
				if ($field[buildings_id] == 46) {
					$ground = $this->db->query("SELECT SUM(b.bev_pro) as bp, SUM(b.bev_use) as bu FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=" . $col[id] . " AND a.field_id>72", 4);
					$this->db->query("UPDATE stu_colonies SET bev_free=bev_free+" . (!$ground[bu] ? 0 : $ground[bu]) . ",bev_work=bev_work-" . (!$ground[bu] ? 0 : $ground[bu]) . ",bev_max=bev_max-" . (!$ground[bp] ? 0 : $ground[bp]) . " WHERE id=" . $col[id]);
					$this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE aktiv=1 AND colonies_id=" . $col[id] . " AND field_id>72");
					$this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=0,aktiv=0,integrity=0 WHERE buildings_id=47 AND colonies_id=" . $col[id]);
				}
				if ($field['buildings_id'] == 300 || ($field['buildings_id'] > 301 && $field['buildings_id'] < 307) || $field['buildings_id'] == 313) {
					$result = $this->db->query("SELECT ships_id FROM stu_colonies_maintainance WHERE colonies_id=" . $col['id']);
					while ($dat = mysql_fetch_assoc($result)) $this->db->query("UPDATE stu_ships SET maintain=0 WHERE id=" . $dat['ships_id'] . " LIMIT 1");
					$this->db->query("DELETE FROM stu_colonies_maintainance WHERE colonies_id=" . $col['id']);
				}
				break;
			}
			$i++;
		}
		if ($this->uid != $col[user_id]) $this->send_pm($this->uid, $col[user_id], addslashes($msg), 4);

		// Hehe - das hat er nun davon :>
		$result = $this->db->query("SELECT a.buildings_id,a.field_id,b.name FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.colonies_id=" . $colid . " AND a.buildings_id=402 AND aktiv=1 ORDER BY a.field_id");
		// Keine Verteidigung oder Energie? Dann halt nicht :(
		if (mysql_num_rows($result) == 0 || $col[eps] == 0) return $msg;
		$this->m->sysname = stripslashes($this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $this->systems_id . " LIMIT 1", 1));
		while ($data = mysql_fetch_assoc($result)) {
			if ($col[eps] == 0) break;
			$wp = "";
			$wp = getWeaponDeviceData($data[buildings_id]);
			if (!is_array($wp)) continue;
			if ($wp[goods_id] > 0 && $this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=" . $wp[goods_id] . " AND colonies_id=" . $colid, 1) == 0) continue;
			$msg .= "<br><b>Die Verteidigungsplattform (" . $data[name] . ") auf Feld " . $data[field_id] . " erwidert das Feuer</b>";
			// Schaden anhand der Varianz errechnen
			$minvari = round(($wp[dmg] / 10) * (100 - $wp[varianz]));
			if ($minvari < 1) $minvari = 1;
			$maxvari = round(($wp[dmg] / 10) * (100 + $wp[varianz]));
			$ro = $wp[rounds];
			$i = 1;
			while ($i <= $ro) {
				if ($col[eps] == 0) break;
				$col[eps] -= 1;
				if ($wp[goods_id] > 0) {
					if ($this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=" . $wp[goods_id] . " AND colonies_id=" . $colid, 1) == 0) continue;
					$this->collowerstorage($colid, $wp[goods_id], 1);
				}
				$dmg = round(rand($minvari, $maxvari) / 10, 1);
				if ($ro > 1) $msg .= "<br>- Schuss " . $i . ":";
				// Liegt ein kritischer Treffer vor?
				if (rand(1, 100) <= $wp[critical]) {
					$dmg *= 2;
					if ($this->schilde_status == 0) $crit = "<br>- " . $this->damage_subsystem("foo", $this->id);
					if ($this->shoff == 1) $this->schilde_status = 0;
				}

				$hit = 1;
				$rand = rand(1, 100);
				$rand > 100 - $wp[shields_through] ? $ds = 1 : $ds = 0;
				if ($this->schilde_status == 1 && $ds == 0) {
					if ($wd['type']) $sd = $this->db->query("SELECT b.dmg_redu_shields as dm1,c.dmg_redu_shields as dm2 FROM stu_modules as a LEFT JOIN stu_modules_special as b ON a.special_id1=b.special_id AND (b.dmg_redu_wtype=" . $wp[type] . " OR b.dmg_redu_wtype=99) LEFT JOIN stu_modules_special as c ON a.special_id2=c.special_id AND (b.dmg_redu_wtype=" . $wp[type] . " OR b.dmg_redu_wtype=99) WHERE a.module_id=" . $this->m2, 4);
					$dmgr = $sd['dm1'] + $sd['dm2'];
					if ($dmgr > 0) {
						$dmg = round(($dmg / 100) * (100 - $dmgr));
						$rb = 1;
					}
					$msg .= "<br>- Schildschaden: " . ($this->schilde <= $dmg ? $this->schilde : $dmg);
					if ($this->schilde <= $dmg) {
						$dmg -= $this->schilde;
						$this->schilde = 0;
						$this->schilde_status = 0;
						$msg .= "<br>-- Schilde brechen zusammen!";
					} else {
						$this->schilde -= $dmg;
						$dmg = 0;
						$msg .= " - Schilde bei " . $this->schilde;
					}
					if ($dmg > 0 && $rb == 1) $dmg = round(($dmg / (100 - $dmgr)) * 100);
				}
				$msg .= $crit;
				if ($ro == $i && $dmg == 0) {
					if ($destroy != 1 && $hit != 0) $this->db->query("UPDATE stu_ships SET huelle=" . $this->huelle . ",schilde_status=" . $this->schilde_status . ",schilde=" . $this->schilde . " WHERE id=" . $this->id);
					break;
				}
				if ($dmg > 0) {
					if ($wd['type']) $sd = $this->db->query("SELECT b.dmg_redu_huell as dm1,c.dmg_redu_huell as dm2 FROM stu_modules as a LEFT JOIN stu_modules_special as b ON a.special_id1=b.special_id AND (b.dmg_redu_wtype=" . $wp[type] . " OR b.dmg_redu_wtype=99) LEFT JOIN stu_modules_special as c ON a.special_id2=c.special_id AND (b.dmg_redu_wtype=" . $wp[type] . " OR b.dmg_redu_wtype=99) WHERE a.module_id=" . $this->m1 . " LIMIT 1", 4);
					$dmgr = $sd['dm1'] + $sd['dm2'];
					if ($dmgr > 0) {
						$dmg = round(($dmg / 100) * (100 - $dmgr));
						$rb = 1;
					}
					$msg .= "<br>- H�llenschaden: " . ($this->huelle <= $dmg ? $this->huelle : $dmg);
					if ($this->huelle <= $dmg) {
						$msg .= "<br>-- H�llenbruch! Das Schiff wurde zerst�rt";
						$this->huelle = 0;
						$this->col_destroy = 1;
						$this->trumfield(array("id" => $this->id, "fleets_id" => $this->fleets_id, "systems_id" => $this->systems_id, "sx" => $this->sx, "sy" => $this->sy, "cx" => $this->cx, "cy" => $this->cy, "rumps_id" => $this->rumps_id, "name" => $this->name, "max_huelle" => $this->max_huelle, "fleets_id" => $this->fleets_id, "is_shuttle" => $this->is_shuttle, "rname" => $this->cname), "von der Verteidigungsplattform (" . $data['name'] . ")");
						$destroy = 1;
						$this->dsships[$this->id] = 1;
						break;
					} else {
						$this->huelle -= $dmg;
						$msg .= " - H�lle bei " . $this->huelle;
					}
				}
				if ($destroy != 1 && $hit != 0) $this->db->query("UPDATE stu_ships SET huelle=" . $this->huelle . ",schilde_status=" . $this->schilde_status . ",schilde=" . $this->schilde . " WHERE id=" . $this->id);
				$i++;
			}
			if ($destroy == 1) break;
		}
		$this->db->query("UPDATE stu_colonies SET eps=" . $col['eps'] . " WHERE id=" . $col['id']);
		return $msg;
	}

	// function getshuttles() { return $this->db->query("SELECT a.goods_id,a.count,b.rumps_id,b.name FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE b.rumps_id>0 AND a.ships_id=".$this->id." ORDER BY b.rumps_id"); }

	// function getoldshuttles() { return $this->db->query("SELECT a.goods_id,a.count,b.rumps_id,b.name FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE b.rumps_id=0 AND a.ships_id=".$this->id." ORDER BY b.rumps_id"); }

	// function launchshuttle($rump)
	// {
	// $shu = $this->db->query("SELECT rumps_id,plans_id,shuttle_type,goods_id,name FROM stu_shuttle_types WHERE rumps_id>0 AND rumps_id=".$rump,4);
	// if ($shu == 0) return;
	// if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id=".$shu[goods_id],1) == 0) return;
	// $data = $this->db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id=".$shu[plans_id],4);
	// if ($data == 0) return;
	// if ($this->uid > 100 && $this->warpcore < $data[wkkap]) return "Zum Start des Shuttles wird eine Warpkernladung von mindestens ".$data[wkkap]." ben�tigt";
	// $return = shipexception(array("schilde_status" => 0,"cloak" => 0,"warpstate" => 0,"eps" => ($data[reaktor]+1),"crew" => $this->min_crew),$this);
	// if ($return['code'] == 1) return $return['msg'];
	// $rump = $this->db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$shu[rumps_id],4);
	// if ($rump[min_crew] > $this->crew-$this->min_crew) return "Zum Start des Shuttles werden mindestens ".$rump[min_crew]." freie Crewmitglieder ben�tigt";
	// if ($rump[warpable] != 1 && $this->systems_id == 0) return "Dieser Shuttletyp kann nur innerhalb von Systemen gestartet werden";
	// $i=1;
	// while($i<=11)
	// {
	// if ($data['m'.$i] == 0)
	// {
	// $i++;
	// continue;
	// }
	// $dat = $this->db->query("SELECT * FROM stu_modules WHERE module_id=".$data['m'.$i],4);
	// $huelle += $dat[huelle]*$rump['m'.$i.'c'];
	// $schilde += $dat[schilde]*$rump['m'.$i.'c'];
	// $eps += $dat[eps]*$rump['m'.$i.'c'];
	// if ($i == 4)
	// {
	// $lss = $dat[lss]+($rump["m".$dat[type]."c"] - 1);
	// $kss = $dat[kss]+($rump["m".$dat[type]."c"] - 1);
	// }
	// if ($i == 6)
	// {
	// $weapon = $this->db->query("SELECT * FROM stu_weapons WHERE module_id=".$data["m".$i],4);
	// $phaser = round($weapon[strength] * (1 + (log($rump["m".$i."c"]) / log(2))/3));
	// $vari = $weapon[varianz];
	// }
	// if ($dat[stellar] == 1) $stellar = 1;
	// $i++;
	// }
	// if ($data[m5] > 0 && $data[m11] > 0) $warp = 1;
	// if ($data[m9] > 0) $cloak = 1; 
	// $batt = $rump["m8c"]*2;
	// $this->lowerstorage($this->id,$shu[goods_id],1);
	// $sn = $this->db->query("SELECT ships_id,shuttle_id,name FROM stu_ships_shuttles WHERE ships_id=".$this->id." LIMIT 1",4);
	// if ($sn != 0)
	// {
	// $id = $sn['shuttle_id'];
	// $this->db->query("INSERT INTO stu_ships (id,user_id,rumps_id,plans_id,systems_id,cx,cy,sx,sy,direction,name,warpcore,warpable,cloakable,max_eps,max_batt,huelle,max_huelle,max_schilde,lss_range,kss_range,max_crew,min_crew,phaser,cfield,points,lastmaintainance) VALUES ('".$sn['shuttle_id']."','".$this->uid."','".$data[rumps_id]."','".$data[plans_id]."',".($this->systems_id > 0 ? "'".$this->systems_id."','".$this->cx."','".$this->cy."','".$this->sx."','".$this->sy."'" : "'0','".$this->cx."','".$this->cy."','0','0'").",'1','".addslashes($sn['name'])."','".($data[wkkap])."','".($warp == 1 ? 1 : '')."','".($cloak == 1 ? 1 : '')."','".$eps."','".$batt."','".$huelle."','".$huelle."','".$schilde."','".$lss."','".$kss."','".$rump[max_crew]."','".$rump[min_crew]."','".$phaser."','".$this->cfield."','0','".time()."')",5);
	// $this->db->query("DELETE FROM stu_ships_shuttles WHERE shuttle_id=".$sn['shuttle_id']." AND ships_id=".$sn['ships_id']." LIMIT 1");
	// }
	// else $id = $this->db->query("INSERT INTO stu_ships (user_id,rumps_id,plans_id,systems_id,cx,cy,sx,sy,direction,name,warpcore,warpable,cloakable,max_eps,max_batt,huelle,max_huelle,max_schilde,lss_range,kss_range,max_crew,min_crew,phaser,cfield,points,lastmaintainance) VALUES ('".$this->uid."','".$data[rumps_id]."','".$data[plans_id]."',".($this->systems_id > 0 ? "'".$this->systems_id."','".$this->cx."','".$this->cy."','".$this->sx."','".$this->sy."'" : "'0','".$this->cx."','".$this->cy."','0','0'").",'1','".addslashes("Shuttle der ".stripslashes($this->name))."','".($data[wkkap])."','".($warp == 1 ? 1 : '')."','".($cloak == 1 ? 1 : '')."','".$eps."','".$batt."','".$huelle."','".$huelle."','".$schilde."','".$lss."','".$kss."','".$rump[max_crew]."','".$rump[min_crew]."','".$phaser."','".$this->cfield."','0','".time()."')",5);
	// $this->db->query("UPDATE stu_ships SET eps=".$data[reaktor].",crew=".$rump[min_crew]." WHERE id=".$id." LIMIT 1");
	// if ($this->uid < 101) $data['wkkap'] = 0;
	// $this->db->query("UPDATE stu_ships SET eps=eps-".($data[reaktor]+1).",crew=crew-".$rump[min_crew].",warpcore=warpcore-".$data[wkkap]." WHERE id=".$this->id);
	// return $rump[name]." gestartet";
	// }

	// function landshuttle ($target)
	// {
	// global $_GET;
	// if ($this->is_shuttle != 1) return;
	// $return = shipexception(array("eps" => 2,"traktor" => 0,"nbs" => 1,"crew" => $this->min_crew),$this);
	// if ($return['code'] == 1) return $return['msg'];
	// $target = $this->db->query("SELECT a.id,a.name,a.user_id,a.cx,a.cy,a.systems_id,a.sx,a.sy,a.schilde_status,a.cloak,a.warp,a.eps,a.max_eps,a.warpcore,a.crew,a.max_crew,b.storage,b.is_shuttle,b.max_shuttles,b.max_shuttle_type,b.max_cshuttle_type,c.wkkap FROM stu_ships as a LEFT JOIN stu_rumps as b ON b.rumps_id=a.rumps_id LEFT JOIN stu_ships_buildplans as c ON c.plans_id=a.plans_id WHERE a.id=".$target,4);
	// if ($target[user_id] != $this->uid) return;
	// if ($target == 0) return;
	// if (checksector($target) == 0) return;
	// $return = shipexception(array("warpstate" => 0,"schilde_status" => 0,"cloak" => 0,"crew" => $target[min_crew]),$target);
	// if ($return['code'] == 1) return $return['msg'];
	// if ($this->shuttle_type > $target[max_shuttle_type]) return "Dieser Shuttle-Typ kann auf diesem Schiff nicht landen";
	// if ($target[crew] + $this->crew > $target[max_crew]) return "Es sind nicht gen�gend Crewquartiere auf dem Schiff vorhanden";
	// $stor_sum = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$target[id],1);
	// $shu = $this->db->query("SELECT goods_id,converts_to FROM stu_shuttle_types WHERE rumps_id=".$this->rumps_id,4);
	// if ($target[storage] <= $stor_sum) return "Kein freier Laderaum vorhanden";
	// if ($target[is_shuttle] == 1) return "Auf einem Shuttle kann kein Shuttle gelandet werden";
	// if ($target[max_shuttles] == 0) return "Dieses Schiff kann keine Shuttles aufnehmen";
	// if ($target[max_shuttles] <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$target[id]." AND !ISNULL(b.shuttle_type)",1)) return "Die Shuttlerampe ist belegt";
	// if ($target[max_cshuttle_type] <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$target[id]." AND !ISNULL(b.shuttle_type) AND a.goods_id!=".$shu[goods_id],1)) return "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht";
	// $stor_sum++;
	// $result = $this->db->query("SELECT goods_id,count FROM stu_ships_storage WHERE ships_id=".$this->id);
	// while($data=mysql_fetch_assoc($result))
	// {
	// if ($stor_sum + $data['count'] > $target[storage])
	// {
	// $data['count'] = $target[storage]-$stor_sum;
	// $this->upperstorage($target[id],$data[goods_id],$data['count']);
	// break;
	// }
	// $stor_sum += $data['count'];
	// $this->upperstorage($target[id],$data[goods_id],$data['count']);
	// }
	// if ($this->warpcore + $target[warpcore] > $target[wkkap]) $this->warpcore = $target[wkkap]-$target[warpcore];
	// if ($this->eps-2 + $target[eps] > $target[max_eps]) $this->eps = $target[max_eps]-$target[eps];
	// if ($this->eps > $target['max_eps'] - $target['eps']) $this->eps = $target['max_eps'] - $target['eps'];
	// $this->db->query("UPDATE stu_ships SET eps=eps+".$this->eps.",warpcore=warpcore+".$this->warpcore.",crew=crew+".$this->crew." WHERE id=".$target[id]); 
	// $this->upperstorage($target[id],$shu[converts_to],1);
	// $this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$this->id);
	// $this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=".$this->id);
	// $this->db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=".$this->id);
	// $this->db->query("DELETE FROM stu_ships WHERE id=".$this->id);
	// $this->db->query("INSERT INTO stu_ships_shuttles (ships_id,shuttle_id,name) VALUES ('".$target['id']."','".$this->id."','".addslashes($this->name)."')");
	// $_GET[id] = $target[id];
	// return "Die ".$this->name." ist auf der ".$target['name']." gelandet";
	// }

	// function maintainshuttle($good)
	// {
	// $shu = $this->db->query("SELECT rumps_id,plans_id,shuttle_type,goods_id,name,converts_to,maintain_epscost FROM stu_shuttle_types WHERE rumps_id=0 AND goods_id=".$good,4);
	// if ($shu == 0) return;
	// if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id=".$shu[goods_id],1) == 0) return;
	// $return = shipexception(array("eps" => $shu[maintain_epscost],"crew" => $this->min_crew),$this);
	// if ($return['code'] == 1) return $return['msg'];
	// $this->lowerstorage($this->id,$shu[goods_id],1);
	// $this->upperstorage($this->id,$shu[converts_to],1);
	// $this->db->query("UPDATE stu_ships SET eps=eps-".$shu[maintain_epscost]." WHERE id=".$this->id);
	// return "Ein Shuttle wurde gewartet";
	// }

	function feedbackimpulse()
	{
		$return = shipexception(array("eps" => 4, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->checksubsystem(8, $this->id) == 1) return "Das EPS-System ist besch�digt - Feedbackimpuls nicht aktivierbar";
		$this->db->query("UPDATE stu_ships SET eps=eps-4 WHERE id=" . $this->id);
		if (rand(1, 4) != 1) {
			if (rand(1, 5) == 1) {
				$ret = $this->damage_subsystem("foo", $this->id, 8);
				$ret = "<br>Der Impuls erzeugt eine �berlastung der Energiesysteme<br>" . $ret;
			}
			return "Der Feedbackimpuls erzielte keine Wirkung auf dem Zielschiff" . $ret;
		}
		$target = $this->db->query("SELECT name,user_id FROM stu_ships WHERE id=" . $this->traktor, 4);
		$ret = $this->damage_subsystem("foo", $this->traktor, 8);
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=" . $this->id . " OR id=" . $this->traktor);
		$this->send_pm($this->uid, $target['user_id'], "Die " . $this->name . " erzeugte einen Feedbackimpuls �ber den Traktorstrahl.\n" . $ret, 3);
		return "Der Feedbackimpuls erzeugte eine �berladung in den Energiesystemen der " . stripslashes($target['name']) . " - Traktorstrahl deaktiviert";
	}

	function checkDockingRights($target)
	{
		$result = $this->db->query("SELECT mode FROM stu_dockingrights WHERE ships_id=" . $target['id'] . " AND type='4' LIMIT 1", 1);
		if ($result == 1) {
			if ($this->db->query("SELECT user_id FROM stu_contactlist WHERE mode='1' AND recipient=" . $this->uid . " AND user_id=" . $target['user_id'] . " LIMIT 1", 1) != 0) return TRUE;
			if ($this->sess['allys_id'] > 0 && $this->db->query("SELECT allys_id FROM stu_user WHERE id=" . $target['user_id'] . " LIMIT 1", 1) == $this->sess['allys_id']) return TRUE;
		}
		$result = $this->db->query("SELECT id,type,mode FROM stu_dockingrights WHERE ships_id=" . $target['id'] . " AND type!='4' ORDER BY type");
		$sm = FALSE;
		$um = FALSE;
		$am = FALSE;
		while ($data = mysql_fetch_assoc($result)) {
			if ($data['type'] == 1 && $data['id'] == $this->id) {
				if ($data['mode'] == 2) return FALSE;
				if ($data['mode'] == 1) $sm = TRUE;
			}
			if ($data['type'] == 2 && $data['id'] == $this->uid) {
				if ($data['mode'] == 2) return FALSE;
				if ($data['mode'] == 1) $um = TRUE;
			}
			if ($data['type'] == 3 && $data['id'] == $this->sess['allys_id']) {
				if ($data['mode'] == 2) return FALSE;
				if ($data['mode'] == 1) $am = TRUE;
			}
		}
		if (!$sm && !$um && !$am) return FALSE;
		return TRUE;
	}

	function nebula_scan()
	{
		$return = shipexception(array("cloak" => 0, "eps" => 1, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) return $return['msg'];
		$rm = rand(1, 5);
		$this->db->query("UPDATE stu_ships SET eps=eps-1 WHERe id=" . $this->id . " LIMIT 1");
		if (rand(1, 3) != 1) return "Es wurden 0 Schiffe entdeckt";
		$result = $this->db->query("SELECT a.id FROM stu_ships as a LEFT JOIN stu_ships_decloaked as b ON b.ships_id=a.id AND b.user_id=" . $this->uid . " WHERE " . ($this->systems_id > 0 ? "a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " AND a.systems_id=" . $this->systems_id : "a.cx=" . $this->cx . " AND a.cy=" . $this->cy . " AND a.systems_id=0") . " AND a.id!=" . $this->id . " AND a.user_id!=" . $this->uid . " AND a.user_id!=1 AND ISNULL(b.ships_id) ORDER BY RAND() LIMIT " . $rm);
		while ($data = mysql_fetch_assoc($result)) $this->db->query("INSERT INTO stu_ships_decloaked (ships_id,user_id,date) VALUES ('" . $data['id'] . "','" . $this->uid . "',NOW())");
		return "Es wurden " . mysql_num_rows($result) . " Schiffe entdeckt";
	}

	function get_marked_ships()
	{
		return $this->db->query("SELECT a.id,a.rumps_id FROM stu_ships as a LEFT JOIN stu_ships_decloaked as b ON b.ships_id=a.id WHERE " . ($this->systems_id > 0 ? "a.sx=" . $this->sx . " AND a.sy=" . $this->sy . " AND a.systems_id=" . $this->systems_id : "a.cx=" . $this->cx . " AND a.cy=" . $this->cy . " AND a.systems_id=0") . " AND UNIX_TIMESTAMP(b.date)>0 AND b.user_id=" . $this->uid);
	}

	function switchtoship(&$shipId)
	{
		$data = $this->db->query("SELECT rumps_id FROM stu_ships WHERE id=" . $shipId . " LIMIT 1", 1);
		if ($data != 1912) return;
		$this->db->query("UPDATE stu_ships SET rumps_id=1712 WHERE id=" . $shipId . " LIMIT 1");
		$this->db->query("UPDATE stu_ships SET dock=0 WHERE dock=" . $shipId);
		return "Zu Schiff umgeschalten";
	}

	function switchtostation(&$shipId)
	{
		$data = $this->db->query("SELECT rumps_id FROM stu_ships WHERE id=" . $shipId . " LIMIT 1", 1);
		if ($data != 1712) return;
		$this->db->query("UPDATE stu_ships SET rumps_id=1912,warp='0' WHERE id=" . $shipId . " LIMIT 1");
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=NULL WHERE traktor=" . $shipId);
		return "Zu Station umgeschalten";
	}

	function get_private_logbook(&$id)
	{
		return $this->db->query("SELECT UNIX_TIMESTAMP(date) as date_tsp,text FROM stu_ships_logs WHERE ships_id=" . $id . " AND type='4' ORDER BY date DESC LIMIT 5");
	}

	function addtorkn()
	{
		$this->db->query("UPDATE stu_ships SET is_rkn=" . $this->sess['race'] . " WHERE id=" . $this->id . " LIMIT 1");
		return "Das Schiff wurde dem RPG hinzugef�gt";
	}

	function getfleetactionstring($fleetid)
	{
		$blockade = $this->db->query("SELECT * FROM stu_colonies_actions WHERE value =" . $fleetid . " AND (var='fdef' OR var='fblock' OR var='fattack')", 4);
		if ($blockade == 0) return 0;
		else {
			$col = $this->db->query("SELECT a.name, a.colonies_classes_id, b.name as sysname FROM stu_colonies as a LEFT JOIN stu_systems as b on b.systems_id = a.systems_id WHERE a.id =" . $blockade['colonies_id'] . "", 4);
			if ($blockade['var'] == "fdef") {
				$res[text] = "Verteidigt die Kolonie " . $col['name'] . " im " . $col['sysname'] . "-System.";
				$res[code] = 1;
			} elseif ($blockade['var'] == "fblock") {
				$res[text] = "Blockiert die Kolonie " . $col['name'] . " im " . $col['sysname'] . "-System.";
				$res[code] = 2;
			} elseif ($blockade['var'] == "fattack") {
				$res[text] = "Attackiert die Kolonie " . $col['name'] . " im " . $col['sysname'] . "-System.";
				$res[code] = 3;
			}
			$res[coltype] = $col['colonies_classes_id'];
		}
		return $res;
	}

	function stopblockade($fleetId)
	{
		$blockade = $this->db->query("SELECT * FROM stu_colonies_actions WHERE value =" . $fleetId . " AND (var='fdef' OR var='fblock' OR var='fattack')", 4);
		if ($blockade != 0) {
			if ($blockade['var'] == "fdef") $blocktext = "Verteidigung";
			elseif ($blockade['var'] == "fblock") $blocktext = "Blockade";
			else $blocktext = "Angriff";
			$blocktext .= " wurde abgebrochen.<br>";
			$this->db->query("DELETE FROM stu_colonies_actions WHERE value =" . $fleetId . " AND var != 'db' LIMIT 1");
			return $blocktext . $lfm;
		}
		return;
	}




	function getwbstorage($id)
	{
		return $this->db->query("SELECT a.*,b.name FROM stu_trade_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.network_id=" . $id . " AND a.offer_id = 0 AND user_id=" . $this->uid . " ORDER BY b.goods_id");
	}
	function getwbstoragesum($id)
	{
		return $this->db->query("SELECT SUM(count) FROM stu_trade_goods WHERE network_id = " . $id . " AND offer_id = 0 AND user_id=" . $this->uid, 1);
	}

	function beamtowb($target, $good, $count)
	{
		$tar = $this->db->query("SELECT * FROM stu_trade_networks WHERE network_id=" . $target . " LIMIT 1", 4);
		if ($tar == 0) return;
		if ($this->cx != $tar['cx'] || $this->cy != $tar['cy']) return;

		$return = shipexception(array("nbs" => 1, "schilde_status" => 0, "cloak" => 0, "eps" => -1, "crew" => $this->min_crew), $this);
		if ($return['code'] == 1) {
			$this->stop_trans = 1;
			return $return['msg'];
		}
		$tast = $this->getwbstoragesum($target);
		if ($tast >= $tar[max_storage]) return "Kein Lagerraum im Konto vorhanden";
		$mb = $this->beamgood;
		foreach ($good as $key => $value) {
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->checkgood($this->id, $value);
			if ($c == 0) continue;
			if ($this->eps == 0) {
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			$c < $count[$key] ? $c = $c : $c = $count[$key];
			if ($c > $tar[max_storage] - $tast) $c = $tar[max_storage] - $tast;
			if ($c <= 0) continue;
			if (ceil($c / $mb) > $this->eps) {
				$c = $this->eps * $mb;
				$this->eps = 0;
			} else $this->eps -= ceil($c / $mb);

			$this->lowerstorage($this->id, $value, $c);
			$this->wbupperstorage($target, $value, $c);

			$msg .= $c . " " . $this->db->query("SELECT name FROM stu_goods WHERE goods_id=" . $value, 1) . "<br>";
			$e += ceil($c / $mb);
			$tast += $c;
			if ($tast >= $tar[max_storage]) break;
			if ($this->eps == 0) break;
		}
		if (!$msg) return "Es wurden keine Waren gebeamt";
		// if ($this->uid != $tar[user_id]) $this->send_pm($this->uid,$tar[user_id],"<b>Die ".stripslashes($this->name)." beamt Waren zur Station ".stripslashes($tar[name])."</b><br>".$msg,5);
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . " WHERE id=" . $this->id);
		return "<b>Es wurden folgende Waren ins Konto gebeamt</b><br>" . $msg . "Energieverbrauch: <b>" . $e . "</b>" . $al;
	}


	function beamfromwb($target, $good, $count)
	{
		$tar = $this->db->query("SELECT * FROM stu_trade_networks WHERE network_id=" . $target . " LIMIT 1", 4);
		if ($tar == 0) return;
		if ($this->cx != $tar['cx'] || $this->cy != $tar['cy']) return;

		$result = shipexception(array("nbs" => 1, "schilde_status" => 0, "cloak" => 0, "eps" => -1, "crew" => $this->min_crew), $this);
		if ($result[code] == 1) {
			$this->stop_trans = 1;
			return $result['msg'];
		}

		$tast = $this->getshipstoragesum($this->id);
		if ($tast >= $this->storage) return "Kein Lagerraum auf dem Schiff vorhanden";
		$mb = $this->beamgood;
		foreach ($good as $key => $value) {
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->db->query("SELECT count FROM stu_trade_goods WHERE goods_id=" . $value . " AND offer_id = 0 AND user_id = " . $this->uid . " AND network_id=" . $target, 1);
			if ($c == 0) continue;
			if ($this->eps == 0) {
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			if ($count[$key] > $c) $count[$key] = $c;
			// if ($value >= 80 && $value<90)
			// {
			// if ($this->uid != $tar[user_id]) continue;
			// $lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id>=80 AND goods_id<100",1);
			// if ($lt != $value && $lt != 0)
			// {
			// $return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
			// continue;
			// }
			// $tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON a.m10=b.module_id WHERE a.plans_id=".$this->plans_id,4);
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
			// $tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$this->id,1);
			// if ($tc >= $tmc[max_torps])
			// {
			// $return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
			// continue;
			// }
			// if ($tmc[m10] != 9000) $this->db->query("UPDATE stu_ships SET torp_type=".$tt[torp_type]." WHERE id=".$this->id);
			// $tc + $count[$key] > $tmc[max_torps] ? $c = $tmc[max_torps]-$tc : $c = $count[$key]; 
			// }
			// elseif ($value >= 110 && $value < 190)
			// {
			// if ($this->uid != $tar[user_id]) continue;
			// if ($shuttle_stop == 1) continue;
			// if ($this->max_shuttles == 0 || $this->is_shuttle == 1)
			// {
			// $shuttle_stop = 1;
			// $return .= "Dieses Schiff kann keine Shuttles laden<br>";
			// continue;
			// }
			// $shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value,4);
			// if ($shud[shuttle_type] > $this->max_shuttle_type)
			// {
			// $return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
			// continue;
			// }
			// if ($this->max_shuttles <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND !ISNULL(b.shuttle_type)",1))
			// {
			// $shuttle_stop = 1;
			// $return .= "Die Shuttlerampe ist belegt<br>";
			// continue;
			// }
			// if ($this->max_cshuttle_type <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND b.goods_id!=".$value." AND !ISNULL(b.shuttle_type)",1))
			// {
			// $return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
			// continue;
			// }
			// $sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<142 AND ships_id=".$this->id,1);
			// $sc + $count[$key] > $this->max_shuttles ? $c = $this->max_shuttles-$sc : $c = $count[$key];
			// }
			// else 
			$count[$key] > $c ? $c = $c : $c = $count[$key];
			if ($c > $this->storage - $tast) $c = $this->storage - $tast;
			if ($c <= 0) continue;
			if (ceil($c / $mb) > $this->eps) {
				$c = $this->eps * $mb;
				$this->eps = 0;
			} else $this->eps -= ceil($c / $mb);
			$this->wblowerstorage($target, $value, $c);
			$this->upperstorage($this->id, $value, $c);
			$msg .= $c . " " . $this->db->query("SELECT name FROM stu_goods WHERE goods_id=" . $value, 1) . "<br>";
			$e += ceil($c / $mb);
			$tast += $c;
			if ($tast >= $this->storage) break;
			if ($this->eps == 0) break;
		}
		if (!$msg) return $return . "Es wurden keine Waren gebeamt";
		if ($this->uid != $tar[user_id]) $this->send_pm($this->uid, $tar[user_id], "<b>Die " . stripslashes($this->name) . " beamt Waren von der Station " . stripslashes($tar[name]) . "</b><br>" . $msg, 2);
		$this->db->query("UPDATE stu_ships SET eps=" . $this->eps . " WHERE id=" . $this->id);
		return "<b>Es wurden folgende Waren aus dem Konto gebeamt</b><br>" . $msg . $return . "Energieverbrauch: <b>" . $e . "</b>" . $al;
	}





	function wbupperstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_trade_goods SET count=count+" . $count . " WHERE network_id=" . $id . " AND goods_id=" . $good . " AND offer_id = 0 AND user_id = " . $this->uid . " LIMIT 1", 6);
		if ($result == 0) $this->db->query("INSERT INTO stu_trade_goods (network_id,goods_id,count,offer_id,user_id) VALUES ('" . $id . "','" . $good . "','" . $count . "','0','" . $this->uid . "')");
	}
	function wblowerstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_trade_goods SET count=count-" . $count . " WHERE network_id=" . $id . " AND goods_id=" . $good . " AND offer_id = 0 AND user_id = " . $this->uid . " AND count > " . $count . " LIMIT 1", 6);
		if ($result == 0) $this->db->query("DELETE FROM stu_trade_goods WHERE network_id=" . $id . " AND goods_id=" . $good . " AND offer_id = 0 AND user_id = " . $this->uid . " LIMIT 1");
	}



	// function getstastorage($id) { return $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_stations_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.stations_id=".$id." ORDER BY b.goods_id"); }	

	// function getstastoragesum($id) { return $this->db->query("SELECT SUM(count) FROM stu_stations_storage WHERE stations_id=".$id,1); }

	// function staupperstorage($id,$good,$count)
	// {
	// $result = $this->db->query("UPDATE stu_stations_storage SET count=count+".$count." WHERE stations_id=".$id." AND goods_id=".$good." LIMIT 1",6);
	// if ($result == 0) $this->db->query("INSERT INTO stu_stations_storage (stations_id,goods_id,count) VALUES ('".$id."','".$good."','".$count."')");
	// }

	// function stalowerstorage($id,$good,$count)
	// {
	// $result = $this->db->query("UPDATE stu_stations_storage SET count=count-".$count." WHERE stations_id=".$id." AND goods_id=".$good." AND count>".$count." LIMIT 1",6);
	// if ($result == 0) $this->db->query("DELETE FROM stu_stations_storage WHERE stations_id=".$id." AND goods_id=".$good." LIMIT 1");
	// }

	// function beamtosta($target,$good,$count)
	// {
	// $tar = $this->db->query("SELECT a.id,a.name,a.user_id,a.sx,a.sy,a.systems_id,a.schilde_status,a.max_storage,b.vac_active FROM stu_stations as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.id=".$target." LIMIT 1",4);
	// if ($tar == 0) return;
	// if (checksector($tar) == 0) return;

	// if ($tar['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
	// if ($tar[schilde_status] == 1 && $tar[user_id] != $this->uid) return "Die Station hat die Schilde aktiviert";

	// $return = shipexception(array("nbs" => 1,"schilde_status" => 0,"cloak" => 0,"eps" => -1,"warpstate" => 0,"crew" => $this->min_crew),$this);
	// if ($return['code'] == 1)
	// {
	// $this->stop_trans = 1;
	// return $return['msg'];
	// }
	// $tast = $this->getstastoragesum($target);
	// if ($tast >= $tar[max_storage]) return "Kein Lagerraum auf der Station vorhanden";
	// $mb = $this->beamgood;
	// foreach ($good as $key => $value)
	// {
	// if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
	// $c = $this->checkgood($this->id,$value);
	// if ($c == 0) continue;
	// if ($this->eps == 0)
	// {
	// $msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
	// break;
	// }
	// $c < $count[$key] ? $c = $c : $c = $count[$key];
	// if ($c > $tar[max_storage] - $tast) $c = $tar[max_storage] - $tast;
	// if ($c <= 0) continue;
	// if (ceil($c/$mb) > $this->eps)
	// {
	// $c = $this->eps*$mb;
	// $this->eps = 0;
	// }
	// else $this->eps -= ceil($c/$mb);

	// $this->lowerstorage($this->id,$value,$c);
	// $this->staupperstorage($target,$value,$c);

	// $msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value,1)."<br>";
	// $e += ceil($c/$mb);
	// $tast += $c;
	// if ($tast >= $tar[max_storage]) break;
	// if ($this->eps == 0) break;
	// }
	// if (!$msg) return "Es wurden keine Waren gebeamt";
	// if ($this->uid != $tar[user_id]) $this->send_pm($this->uid,$tar[user_id],"<b>Die ".stripslashes($this->name)." beamt Waren zur Station ".stripslashes($tar[name])."</b><br>".$msg,5);
	// $this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id);
	// return "<b>Es wurden folgende Waren zu der Station ".$tar[name]." gebeamt</b><br>".$msg."Energieverbrauch: <b>".$e."</b>".$al;
	// }

	// function beamfromsta($target,$good,$count)
	// {
	// $tar = $this->db->query("SELECT a.id,a.name,a.user_id,a.sx,a.sy,a.systems_id,a.schilde_status,b.vac_active,b.level FROM stu_stations as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=".$target,4);
	// if ($tar == 0) return;
	// if (checksector($tar) == 0) return;

	// if ($tar[schilde_status] == 1 && $tar[user_id] != $this->uid) return "Die Station hat die Schilde aktiviert";

	// if ($tar[vac_active] == 1) {
	// return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
	// }
	// if ($this->sess[level] < 4) return "Du kannst erst ab Level 4 von Stationen beamen";
	// $result = shipexception(array("nbs" => 1,"schilde_status" => 0,"cloak" => 0,"eps" => -1,"warpstate" => 0,"crew" => $this->min_crew),$this);
	// if ($result[code] == 1)
	// {
	// $this->stop_trans = 1;
	// return $result['msg'];
	// }

	// $tast = $this->getshipstoragesum($this->id);
	// if ($tast >= $this->storage) return "Kein Lagerraum auf dem Schiff vorhanden";
	// $mb = $this->beamgood;
	// foreach ($good as $key => $value)
	// {
	// if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
	// $c = $this->db->query("SELECT count FROM stu_stations_storage WHERE goods_id=".$value." AND stations_id=".$target,1);
	// if ($c == 0) continue;
	// if ($this->eps == 0)
	// {
	// $msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
	// break;
	// }
	// if ($count[$key] > $c) $count[$key] = $c;
	// if ($value >= 80 && $value<90)
	// {
	// if ($this->uid != $tar[user_id]) continue;
	// $lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id>=80 AND goods_id<100",1);
	// if ($lt != $value && $lt != 0)
	// {
	// $return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
	// continue;
	// }
	// $tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON a.m10=b.module_id WHERE a.plans_id=".$this->plans_id,4);
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
	// $tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$this->id,1);
	// if ($tc >= $tmc[max_torps])
	// {
	// $return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
	// continue;
	// }
	// if ($tmc[m10] != 9000) $this->db->query("UPDATE stu_ships SET torp_type=".$tt[torp_type]." WHERE id=".$this->id);
	// $tc + $count[$key] > $tmc[max_torps] ? $c = $tmc[max_torps]-$tc : $c = $count[$key]; 
	// }
	// elseif ($value >= 110 && $value < 190)
	// {
	// if ($this->uid != $tar[user_id]) continue;
	// if ($shuttle_stop == 1) continue;
	// if ($this->max_shuttles == 0 || $this->is_shuttle == 1)
	// {
	// $shuttle_stop = 1;
	// $return .= "Dieses Schiff kann keine Shuttles laden<br>";
	// continue;
	// }
	// $shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value,4);
	// if ($shud[shuttle_type] > $this->max_shuttle_type)
	// {
	// $return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
	// continue;
	// }
	// if ($this->max_shuttles <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND !ISNULL(b.shuttle_type)",1))
	// {
	// $shuttle_stop = 1;
	// $return .= "Die Shuttlerampe ist belegt<br>";
	// continue;
	// }
	// if ($this->max_cshuttle_type <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND b.goods_id!=".$value." AND !ISNULL(b.shuttle_type)",1))
	// {
	// $return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
	// continue;
	// }
	// $sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<142 AND ships_id=".$this->id,1);
	// $sc + $count[$key] > $this->max_shuttles ? $c = $this->max_shuttles-$sc : $c = $count[$key];
	// }
	// else $count[$key] > $c ? $c = $c : $c = $count[$key];
	// if ($c > $this->storage - $tast) $c = $this->storage - $tast;
	// if ($c <= 0) continue;
	// if (ceil($c/$mb) > $this->eps)
	// {
	// $c = $this->eps*$mb;
	// $this->eps = 0;
	// }
	// else $this->eps -= ceil($c/$mb);
	// $this->stalowerstorage($target,$value,$c);
	// $this->upperstorage($this->id,$value,$c);
	// $msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value,1)."<br>";
	// $e += ceil($c/$mb);
	// $tast += $c;
	// if ($tast >= $this->storage) break;
	// if ($this->eps == 0) break;
	// }
	// if (!$msg) return $return."Es wurden keine Waren gebeamt";
	// if ($this->uid != $tar[user_id]) $this->send_pm($this->uid,$tar[user_id],"<b>Die ".stripslashes($this->name)." beamt Waren von der Station ".stripslashes($tar[name])."</b><br>".$msg,2);
	// $this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id);
	// return "<b>Es wurden folgende Waren von der Station ".$tar[name]." gebeamt</b><br>".$msg.$return."Energieverbrauch: <b>".$e."</b>".$al;
	// }

	// function transfercrewsta($target,$count,$way)
	// {
	// $data = $this->db->query("SELECT id,name,user_id,sx,sy,systems_id,bev_free,bev_work,bev_max,schilde_status FROM stu_stations WHERE id=".$target,4);
	// if ($data == 0 || checksector($data) == 0  || $this->uid != $data[user_id]) return;
	// if ($data[schilde_status] == 1 && $data[user_id] != $this->uid) return "Die Station hat die Schilde aktiviert";
	// if ($way == "fr" && $data[bev_free] == 0) return "Keine Crew auf der Station ".$data[name]." vorhanden";
	// if ($way == "to" && $this->crew == 0) return "Keine Crew auf der ".$this->name." vorhanden";
	// $return = shipexception(array("schilde_status" => 0,"cloak" => 0,"nbs" => 1,"eps" => -1,"warpstate" => 0,"crew" => $this->min_crew),$this);
	// if ($return['code'] == 1) return $return['msg'];
	// if ($count == "max") $count = $this->eps;

	// if ($way == "fr")
	// {
	// if ($this->max_crew <= $this->crew) return "Alle Crewquartiere auf der ".$this->name." sind belegt";
	// if ($count > $data[bev_free]) $count = $data[bev_free];
	// if ($count > $this->max_crew-$this->crew) $count = $this->max_crew-$this->crew;
	// $e = ceil($count/5);
	// if ($e > $this->eps)
	// {
	// $count = $this->eps*5;
	// $e = $this->eps;
	// }
	// $this->eps -= $e;
	// $this->db->query("UPDATE stu_stations SET bev_free=bev_free-".$count." WHERE id=".$target);
	// $this->db->query("UPDATE stu_ships SET crew=crew+".$count.",eps=".$this->eps." WHERE id=".$this->id);
	// return $count." Crew von der Station ".stripslashes($data[name])." gebeamt (".$e." Energie verbraucht)".$al;
	// }
	// elseif ($way == "to")
	// {
	// if ($data[bev_work]+$data[bev_free] >= $data[bev_max]) return "Kein Wohnraum auf der Station ".$data[name]." vorhanden";
	// if ($count > $this->crew) $count = $this->crew;
	// if ($count > $data[bev_max]-$data[bev_free]-$data[bev_work]) $count = $data[bev_max]-$data[bev_free]-$data[bev_work];
	// $e = ceil($count/5);
	// if ($e > $this->eps)
	// {
	// $count = $this->eps*5;
	// $e = $this->eps;
	// }
	// $this->eps -= $e;
	// $this->db->query("UPDATE stu_stations SET bev_free=bev_free+".$count." WHERE id=".$target);
	// $this->db->query("UPDATE stu_ships SET crew=crew-".$count.",eps=".$this->eps." WHERE id=".$this->id);
	// return $count." Crew zu der Station ".stripslashes($data[name])." gebeamt (".$e." Energie verbraucht)".$al;
	// }
	// return;
	// }

	// function setkonstrukt() {
	// $return = shipexception(array("nbs" => 1,"schilde_status" => 0,"cloak" => 0,"eps" => 30,"warpstate" => 0,"crew" => $this->min_crew),$this);
	// if ($return['code'] == 1) return $return['msg'];
	// if ($this->map[sensoroff] == 1) return "Hier kann keine Basis errichtet werden";

	// if (($this->map[type] != 1) && (($this->map[type] < 51) || ($this->map[type] > 71))) return "Hier kann keine Station errichtet werden.";

	// if ($this->systems_id == 0) return "Stationen k�nnen nur innerhalb von Systemen errichtet werden.";
	// $result = $this->db->query("SELECT * FROM stu_stations WHERE systems_id=".$this->systems_id." AND sx=".$this->sx." AND sy=".$this->sy."",1);
	// if ($result > 0) return "In diesem Sektor befindet sich bereits eine Station";

	// if (($this->db->query("SELECT SUM(slimit) FROM stu_stations WHERE user_id=".$this->uid,1)+4 >= 16) && ($this->db->query("SELECT COUNT(id) FROM stu_stations WHERE user_id=".$this->uid." AND stations_classes_id = 2",1) >= 3) && ($this->db->query("SELECT COUNT(id) FROM stu_stations WHERE user_id=".$this->uid." AND stations_classes_id = 1",1) >= 3)) return "Das Stationslimit wurde erreicht.";

	// $count = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=10 AND ships_id=".$this->id,1) ;
	// if ($count < 3) return "F�r den Bau eines Konstrukts werden 3 Workbees ben�tigt - Vorhanden sind nur ".(!$count ? 0 : $count)."";
	// $this->db->query("INSERT INTO stu_stations (stations_classes_id,user_id,systems_id,sx,sy,name,max_eps,armor,max_armor,max_storage)
	// VALUES ('99','".$this->uid."','".$this->systems_id."','".$this->sx."','".$this->sy."'
	// ,'Konstrukt','500','500','500','1500')");
	// $this->lowerstorage($this->id,10,3);
	// $this->db->query("UPDATE stu_ships SET eps=eps-30 WHERE id=".$this->id);
	// return "Konstrukt errichtet";
	// }


	// function transferfreighter($id,$target)
	// {
	// global $_GET;
	// $shp = $this->db->query("SELECT * FROM stu_ships WHERE id=".$id,4);
	// $target = $this->db->query("SELECT * FROM stu_stations WHERE id=".$target,4);
	// if ($target[user_id] != $shp[user_id]) return;

	// if ($target == 0) return;
	// if ($target[systems_id] != $shp[systems_id]) return;

	// if ( $shp[id] <= 100) return "Fehler: Mit diesem Schiff ist diese Aktion wegen ID-Kollision nicht m�glich. Bei Administrator melden.";
	// if ($shp[fleets_id] != 0) return "Das Schiff darf nicht teil einer Flotte sein";
	// if ($shp[warpable] != 1) return "Das Schiff ben�tigt einen Warpantrieb";
	// switch( $shp[rumps_id]) {
	// case 2001: $f = 11; break;
	// case 2002: $f = 12; break;
	// case 2003: $f = 13; break;
	// case 2004: $f = 14; break;
	// case 2005: $f = 15; break;
	// case 2006: $f = 16; break;
	// case 2301: $f = 21; break;
	// case 2302: $f = 22; break;
	// case 2303: $f = 23; break;
	// case 2304: $f = 24; break;
	// case 2305: $f = 25; break;
	// case 2306: $f = 26; break;
	// default: $f = 0; break;
	// }

	// if ($f == 0) return "Nur Frachter k�nnen an Stationen �berstellt werden.";

	// if ($target[bev_free] + $shp[crew] + $target[bev_work] > $target[bev_max]) return "Es sind nicht gen�gend Crewquartiere auf der Station vorhanden";

	// $stor_fre = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$shp[id],1);
	// if ($stor_fre > 0) return "Der Frachter muss zuerst vollst�dnig entladen werden. (Bis auf Crew)";

	// $this->db->query("UPDATE stu_ships SET schilde_status = 0, crew=0,assigned=".$target[id]." WHERE id=".$shp[id]); 
	// $this->db->query("UPDATE stu_stations SET bev_free = bev_free+".$shp[crew]." WHERE id=".$target[id]); 

	// $this->db->query("INSERT INTO stu_stations_fielddata (stations_id,field_id,type,ship) VALUES ('".$target['id']."','".$shp[id]."','".$f."','".$shp[id]."')");
	// $this->assigned = $target;
	// return "Die ".$shp[name]." wurde der Station ".$target['name']." �berstellt.";
	// }

	// function etransfers($target,$count)
	// {
	// $data = $this->db->query("SELECT a.id,a.sx,a.sy,a.systems_id,a.user_id,a.name,a.eps,a.max_eps,a.schilde_status,b.vac_active FROM stu_stations as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=".$target,4);
	// if ($data == 0 || checksector($data) == 0) return;
	// if ($data['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";

	// if ($data['eps'] >= $data['max_eps']) return "Das EPS der Station ".$data[name]." ist voll";
	// $return = shipexception(array("nbs" => 1,"schilde_status" => 0,"cloak" => 0,"eps" => -1,"warpstate" => 0,"crew" => $this->min_crew),$this);
	// if ($return['code'] == 1) return $return['msg'];
	// if ($count == "max") $count = $this->eps;
	// else if ($count > $this->eps) $count = $this->eps;
	// if ($data['max_eps'] < $count+$data['eps']) $count = $data['max_eps']-$data['eps'];
	// $this->db->query("UPDATE stu_stations SET eps=eps+".$count." WHERE id=".$target." LIMIT 1");
	// $this->eps -= $count;
	// $this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
	// $msg = $count." Energie zur Station ".$data['name']." transferiert";
	// if ($this->uid != $data['user_id']) $this->send_pm($this->uid,$data['user_id'],"<b>Die ".stripslashes($this->name)." hat ".$count." Energie zur Station ".stripslashes($data['name'])." transferiert</b><br>",5);
	// return $msg.$al;
	// }








	// function domwreckage($id,$target)
	// {
	// global $_GET;
	// $shp = $this->db->query("SELECT * FROM stu_ships WHERE id=".$id,4);
	// $target = $this->db->query("SELECT * FROM stu_ships WHERE id=".$target,4);


	// if ($target == 0) return;
	// if ($target[systems_id] != $shp[systems_id]) return;
	// if ($target[sx] != $shp[sx]) return;
	// if ($target[sy] != $shp[sy]) return;

	// if ($target['rumps_id'] != 99) return "Ich weiss nicht, was du da versuchst, aber: LASS ES.";
	// if ($target['huelle'] <= 10) return "Der Zustand des Wracks ist zu schlecht f�r einen Transport.";
	// $alreadydone = $this->db->query("SELECT boarding_done FROM stu_user WHERE id=".$this->uid,1);
	// if ($alreadydone) return "Eine weitere Untersuchung w�re nutzlos.";
	// $this->db->query("UPDATE stu_user SET boarding_done = 1 WHERE id=".$this->uid); 



	// $this->db->query("UPDATE stu_ships SET huelle = huelle - 1 WHERE id=".$target['id']);
	// $teammsg = "Unsere Sichtung der begehbaren Teile des Wracks best�tigen den ersten Eindruck. Das Schiff vom Typ eines vom Dominion oft verwendeten Jem'Hadar-Angriffsschiffs scheint bereits vor vielen Jahren mit dem Asteroiden kollidiert zu sein - die Ursache hierf�r ist aber weiter unklar. Die meisten Schiffssysteme sind zerst�rt und nicht zu bergen. <br><br>Das Schiff ist teilweise ohne Atmosph�re, es gibt keine �berlebenden. Die Leichen der Jem'Hadar sowie des kommandierenden Vorta sind im Vakuum mumifiziert. Es gibt keine Anzeichen daf�r, dass auch ein Gr�nder an Bord war.<br><br>Wie erhofft war eine Kommandokonsole auf der Br�cke noch aktiv, und wir konnten einige Daten auf einen Datenkern �berspielen. Wir haben den Datenkern im Frachtraum hinterlegt und sollten ihn zur Auswertung in ein Forschungszentrum bringen.";
	// $this->send_pm(20,$this->uid,$teammsg,1);
	// $this->upperstorage($this->id,99,1);		
	// return "Das Au�enteam war erfolgreich - ein Bericht kommt gerade herein.";
	// }





	function getTradeNetwork()
	{
		if (!$this->id) return;
		if ($this->systems_id > 0) return;

		$network = $this->db->query("SELECT * FROM stu_trade_networks WHERE cx=" . $this->cx . " AND cy=" . $this->cy . " LIMIT 1", 4);

		if ($network[ships_id] > 0) {
			$network[shipdata] = $this->db->query("SELECT user_id,rumps_id,name FROM stu_ships WHERE id = " . $network[ships_id] . " LIMIT 1", 4);
			$network[username] = $this->db->query("SELECT user FROM stu_user WHERE id = " . $network['shipdata']['user_id'] . " LIMIT 1", 1);
		}

		return $network;
	}
}