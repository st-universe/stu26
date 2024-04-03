<?php
class fleet
{
	function fleet()
	{
		global $db, $ship, $map, $_SESSION, $gfx;
		$this->db = $db;
		$this->s = $ship;
		$this->m = $map;
		$this->gfx = $gfx;
		$this->sess = $_SESSION;
		$this->uid = $_SESSION['uid'];
	}

	function checkflaggship($id)
	{
		return $this->db->query("SELECT COUNT(fleets_id) FROM stu_fleets WHERE ships_id=" . $id, 1);
	}



	function joinconflict($fleetId)
	{
		return "deaktiviert";
		$data = $this->db->query("SELECT * FROM stu_fleets WHERE fleets_id = " . $fleetId . " LIMIT 1;", 4);
		if ($data == 0) return;
		if ($data['user_id'] != $this->uid) return "Du bist nicht Besitzer dieser Flotte";
		if ($data['faction'] > 0) return "Flotte bereits bereitgestellt";

		// $count = $this->db->query("SELECT COUNT(*) FROM stu_fleets WHERE user_id = ".$this->uid." AND faction > 0;",1);
		// if ($count >= 4) return "Es k�nnen maximal 4 Flotten bereitgestellt werden";


		$freigthers = $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE user_id = " . $this->uid . " AND fleets_id = " . $fleetId . " AND rumps_id > 6000;", 1);
		if ($freighters > 0) return "Flotten mit zivilen Schiffen k�nnen nicht bereitgestellt werden.";

		$userfaction = $this->db->query("SELECT race FROM stu_user WHERE id = " . $this->uid . " LIMIT 1;", 1);
		$aship = $this->db->query("SELECT * FROM stu_ships WHERE fleets_id = " . $fleetId . " LIMIT 1;", 4);
		$fac = 0;
		if ($aship['systems_id'] > 0) {
			$fac = getFieldFaction(0, 0, $aship['systems_id']);
		} else {
			$fac = getFieldFaction($aship['cx'], $aship['cy'], 0);
		}
		if ($fac != $userfaction) return "Bereitstellung kann nur in einem Cluster ge�ndert werden, den die eigene Rasse zur Zeit h�lt.";

		$this->db->query("UPDATE stu_fleets SET faction=" . $_SESSION['race'] . " WHERE fleets_id=" . $fleetId);
		return "Flotte wurde dem globalen Konflikt bereitgestellt";
	}

	function leaveconflict($fleetId)
	{
		// return "Bereitstellung kann nicht aufgehoben werden.";
		$data = $this->db->query("SELECT * FROM stu_fleets WHERE fleets_id = " . $fleetId . " LIMIT 1;", 4);
		if ($data == 0) return;
		if ($data['user_id'] != $this->uid) return "Du bist nicht Besitzer dieser Flotte";
		if ($data['faction'] == 0) return "Flotte nicht bereitgestellt";


		$userfaction = $this->db->query("SELECT race FROM stu_user WHERE id = " . $this->uid . " LIMIT 1;", 1);
		$aship = $this->db->query("SELECT * FROM stu_ships WHERE fleets_id = " . $fleetId . " LIMIT 1;", 4);
		$fac = 0;
		if ($aship['systems_id'] > 0) {
			$fac = getFieldFaction(0, 0, $aship['systems_id']);
		} else {
			$fac = getFieldFaction($aship['cx'], $aship['cy'], 0);
		}
		if ($fac != $userfaction) return "Bereitstellung kann nur in einem Cluster ge�ndert werden, den die eigene Rasse zur Zeit h�lt.";


		$time = time() + 18000;

		$this->db->query("UPDATE stu_fleets SET faction_change_time=" . $time . " WHERE fleets_id=" . $fleetId);
		return "Flottenbereitstellung wird in 5 Stunden aufgehoben";
	}

	function cancelconflict($fleetId)
	{
		// return "Bereitstellung kann nicht aufgehoben werden.";
		$data = $this->db->query("SELECT * FROM stu_fleets WHERE fleets_id = " . $fleetId . " LIMIT 1;", 4);
		if ($data == 0) return;
		if ($data['user_id'] != $this->uid) return "Du bist nicht Besitzer dieser Flotte";
		if ($data['faction'] == 0) return "Flotte nicht bereitgestellt";

		$this->db->query("UPDATE stu_fleets SET faction_change_time = 0 WHERE fleets_id = " . $fleetId . " LIMIT 1;");

		return "Flottenbereitstellung wird nicht weiter aufgehoben";
	}





	function newfleet($shipId)
	{
		// return "Flottenerstellung zur Zeit aus technischen Gr�nden blockiert";
		$data = $this->db->query("SELECT a.id,a.fleets_id,a.traktormode,a.dock FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE id=" . $shipId . " AND user_id=" . $this->uid, 4);
		if ($data == 0) return;
		if ($data['fleets_id'] > 0) return;
		if ($data['slots'] > 0) return;
		if ($data['is_shuttle'] == 1) return;
		if ($data['dock'] > 0) return;
		// if ($data['traktormode'] == 2) return "Das Schiff wird von einem Traktorstrahl gehalten und kann keine Flotte bilden";
		if ($data['traktormode'] > 0) return "Das Schiff hat den Traktorstrahl aktiviert oder wird von einem Traktorstrahl gehalten";
		if ($data['assigned'] != 0) return "Zugewiesene Schiffe k�nnen keine Flotte bilden";
		if ($this->db->query("SELECT id FROM stu_ships WHERE dock=" . $shipId, 1) > 0) return "Angedockte Schiffe k�nnen keine Flotte erstellen";
		$fid = $this->db->query("INSERT INTO stu_fleets (user_id,ships_id) VALUES ('" . $this->uid . "','" . $shipId . "')", 5);
		$this->db->query("UPDATE stu_ships SET fleets_id=" . $fid . " WHERE id=" . $shipId);
		return "Flotte erstellt";
	}

	function getfleetwp($fleetId)
	{
		return $this->db->query("SELECT SUM(points) FROM stu_ships WHERE fleets_id=" . $fleetId, 1);
	}
	function getTotalPoints($fleetId)
	{
		return $this->db->query("SELECT SUM(b.fleetpoints) FROM stu_ships AS a LEFT JOIN stu_rumps AS b ON a.rumps_id = b.rumps_id WHERE a.user_id=" . $_SESSION['uid'] . " AND a.fleets_id=" . $fleetId . ";", 1);
	}

	function getjoinlist($data, $shipId)
	{
		$result = $this->db->query("SELECT a.id,a.name FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.cx=" . $data['cx'] . " AND a.cy=" . $data['cy'] . " AND a.systems_id=" . $data['systems_id'] . " AND a.sx=" . $data['sx'] . " AND a.sy=" . $data['sy'] . " AND a.id!=" . $shipId . " AND a.fleets_id=0 AND a.user_id=" . $this->uid . " AND a.dock=0");
		if (mysql_num_rows($result) == 0) return "<option>----------";
		while ($d = mysql_fetch_assoc($result)) $ret .= "<option value=" . $d['id'] . ">" . strip_tags(stripslashes($d['name']));
		return $ret;
	}

	function addtofleet($shipId, $fleetId)
	{
		$data = $this->db->query("SELECT a.id,a.name,a.fleets_id,a.traktormode,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.points,a.min_crew,b.fleetpoints FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id=" . $shipId . " AND a.dock=0 AND a.user_id=" . $this->uid, 4);
		if ($data == 0) return 0;
		if ($data['is_shuttle'] == 1) return;
		if ($data['traktormode'] > 0) return "Das Schiff hat den Traktorstrahl aktiviert oder wird von einem Traktorstrahl gehalten";
		if ($data['assigned'] != 0) return "Zugewiesene Schiffe k�nnen keine Flotte bilden";
		if ($data['fleets_id'] > 0) return 0;
		$fdat = $this->db->query("SELECT a.name,b.cx,b.cy,b.sx,b.sy,b.systems_id FROM stu_fleets as a LEFT JOIN stu_ships as b ON a.ships_id=b.id WHERE a.fleets_id=" . $fleetId . " AND a.user_id=" . $this->uid, 4);
		if ($fdat == 0) return 0;
		if ($data['systems_id'] > 0) {
			if ($fdat['systems_id'] != $data['systems_id']) return 0;
			if ($fdat['sx'] != $data['sx']) return 0;
			if ($fdat['sy'] != $data['sy']) return 0;
		}
		if ($fdat['cx'] != $data['cx']) return 0;
		if ($fdat['cy'] != $data['cy']) return 0;
		if ($data['slots'] > 0) return;
		if (($this->uid > 100) && ($this->getTotalPoints($fleetId) + $data['fleetpoints'] > 60)) return "Flotte �bersteigt maximale Gr��e von 60 Flottenpunkten.";
		// if ($this->db->query("SELECT COUNT(id) FROM stu_ships WHERE fleets_id=".$fleetId,1) >= 25) return "Es d�rfen maximal 25 Schiffe in einer Flotte sein";
		$this->db->query("UPDATE stu_ships SET fleets_id=" . $fleetId . " WHERE id=" . $shipId . " LIMIT 1");
		return "Die " . $data['name'] . " hat sich der Flotte " . $fdat['name'] . " angeschlossen";
	}

	function leavefleet($shipId)
	{
		$data = $this->db->query("SELECT a.id,a.name,a.fleets_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,b.name as fname,b.ships_id FROM stu_ships as a LEFT JOIN stu_fleets as b USING(fleets_id) WHERE a.id=" . $shipId . " AND a.user_id=" . $this->uid, 4);
		if ($data == 0) return;
		if ($data['fleets_id'] == 0) return;
		if ($data['id'] == $data['ships_id']) return;
		$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $shipId . " LIMIT 1");
		return "Die " . $data['name'] . " hat die Flotte " . $data['fname'] . " verlassen";
	}

	function delfleet($shipId)
	{
		$data = $this->db->query("SELECT a.id,a.name,a.fleets_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,b.name as fname,b.ships_id FROM stu_ships as a LEFT JOIN stu_fleets as b USING(fleets_id) WHERE a.id=" . $shipId . " AND a.user_id=" . $this->uid, 4);
		if ($data == 0) return;
		if ($data['fleets_id'] == 0) return;
		if ($data['id'] != $data['ships_id']) return;
		$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE fleets_id=" . $data['fleets_id'] . " LIMIT 25");
		$this->db->query("DELETE FROM stu_fleets WHERE fleets_id=" . $data['fleets_id'] . " AND user_id=" . $this->uid . " LIMIT 1");
		$msga = "<br>" . $this->stopblockade($data['fleets_id']);
		return "Die Flotte " . $data['fname'] . " wurde aufgel�st" . $msga;
	}

	function renamefleet($fleetId, $name)
	{
		$data = $this->db->query("SELECT fleets_id FROM stu_fleets WHERE fleets_id=" . $fleetId . " AND user_id=" . $this->uid . " LIMIT 1", 1);
		if ($data == 0) return;
		$name = str_replace("\"", "", str_replace("size=-", "", $name));
		$name = strip_tags($name, "<font></font><b></b><i></i>");
		if (strlen(strip_tags($name)) > 100) return "Der Name darf aus nur 100 Zeichen bestehen";
		if (strlen($name) > 255) return "Der Name darf mit HTML nur aus 255 Zeichen bestehen";
		$this->db->query("UPDATE stu_fleets SET name='" . addslashes($name) . "' WHERE fleets_id=" . $fleetId);
		return "Der Flottenname wurde in " . $name . " ge�ndert";
	}

	function move($fleetId, $shipId)
	{
		$this->fm = 1;
		$this->ff = $shipId;
		$msga = "<br>" . $this->stopblockade($fleetId);
	}

	function leavesystem($fleetId, $shipId)
	{
		$result = $this->db->query("SELECT a.id,a.name,a.systems_id,a.traktormode,a.crew,a.warpable,a.eps,a.min_crew,a.maintain,b.slots FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id!=" . $shipId . " AND a.fleets_id=" . $fleetId . " LIMIT 25");
		while ($data = mysql_fetch_assoc($result)) {
			$lf = 0;
			if ($data['systems_id'] == 0) $lf = 1;
			if ($data['slots'] > 0) $lf = 1;
			if ($data['traktormode'] == 1) $lf = 1;
			if ($data['traktormode'] == 2) $lf = 1;
			if ($data['crew'] < $data['min_crew']) $lf = 1;
			if ($data['warpable'] == 0) $lf = 1;
			if ($data['eps'] < 2) $lf = 1;
			if ($data['maintain'] > 0) $lf = 1;
			if ($this->s->checksubsystem(7, $data['id']) == 1) $lf = 1;
			if ($this->s->checksubsystem(11, $data['id']) == 1) $lf = 1;
			if ($lf == 1) {
				$lfm .= "Die " . stripslashes($data['name']) . " hat die Flotte verlassen<br>";
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $data['id'] . " LIMIT 1");
			}
		}
		$this->db->query("UPDATE stu_ships SET direction=NULL,sx=0,sy=0,systems_id=0,eps=eps-2,still=0,warp='1',cfield=" . $this->s->sys['type'] . "22 WHERE fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		$msga = "<br>" . $this->stopblockade($fleetId);
		return $lfm . "Die Flotte hat das System verlassen" . $msga;
	}

	function entersystem($fleetId, $shipId, $fdata)
	{
		$result = $this->db->query("SELECT a.id,a.rumps_id,a.name,a.systems_id,a.traktormode,a.traktor,a.cloak,a.crew,a.warpable,a.eps,a.min_crew,b.slots FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id!=" . $shipId . " AND a.fleets_id=" . $fleetId . " LIMIT 25");
		while ($data = mysql_fetch_assoc($result)) {
			$lf = 0;
			if ($data['slots'] > 0) $lf = 1;
			if ($data['traktormode'] == 1) $lf = 1;
			if ($data['crew'] < $data['min_crew']) $lf = 1;
			if ($data['eps'] == 0) $lf = 1;
			if ($this->s->checksubsystem(7, $data['id']) == 1) $lf = 1;
			if ($this->s->checksubsystem(11, $data['id']) == 1) $lf = 1;
			if ($lf == 1) {
				$lfm .= "Die " . $data['name'] . " hat die Flotte verlassen<br>";
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $data['id'] . " LIMIT 1");
				continue;
			}
			$res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=" . $this->sess['allys_id'] . ",cloak='" . $data['cloak'] . "',notified='0' WHERE sx=" . $fdata['sx'] . " AND sy=" . $fdata['sy'] . " AND ships_id=" . $data['id'] . " AND systems_id=" . $fdata['systems_id'] . " LIMIT 1", 6);
			if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,sx,sy,systems_id,cloak) VALUES ('" . $this->uid . "','" . $data['id'] . "','" . $data['rumps_id'] . "','" . $this->sess['allys_id'] . "',NOW(),'" . $fdata['sx'] . "','" . $fdata['sy'] . "','" . $fdata['systems_id'] . "','" . $data['cloak'] . "')");
			if ($data['traktormode'] == 1) $this->db->query("UPDATE stu_ships SET sx=" . $fdata['sx'] . ",sy=" . $fdata['sy'] . ",systems_id=" . $fdata['systems_id'] . ",warp='0',cfield=" . $fdata['ftype'] . " WHERE id=" . $data['traktor'] . " LIMIT 1");
		}
		$this->db->query("UPDATE stu_ships SET sx=" . $fdata['sx'] . ",sy=" . $fdata['sy'] . ",systems_id=" . $fdata['systems_id'] . ",eps=eps-1,warp='0',cfield=" . $fdata['ftype'] . " WHERE fleets_id=" . $fleetId . " LIMIT 25");
		return $lfm . "Die Flotte fliegt in das " . $fdata['sysname'] . "-System ein";
	}

	function changealvl($lvl)
	{
		if ($lvl < 1 || $lvl > 2) return;
		$result = $this->db->query("SELECT id,name,alvl,crew,min_crew FROM stu_ships WHERE fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			if ($lvl == $data['alvl']) continue;
			if ($data['crew'] < $data['min_crew']) {
				$fmsg .= "<br>" . stripslashes($data['name']) . ": Es werden " . $data['min_crew'] . " Crewmitglieder ben�tigt";
				continue;
			}
			$this->db->query("UPDATE stu_ships SET alvl=" . $lvl . " WHERE id=" . $data['id'] . " LIMIT 1");
		}
		if ($lvl == 3) {
			$fmsg .= "<br />" . $this->activate_kss();
			$fmsg .= "<br />" . $this->activate_shields();
			$fmsg .= "<br />" . $this->activate_phaser();
			$fmsg .= "<br />" . $this->activate_torps();
		}
		return "Flottenbefehl \"Alarmstufe �ndern\" wurde ausgef�hrt" . $fmsg;
	}








	function av($d)
	{
		$system = "";
		switch ($d) {
			case "sh":
				$system = "schilde_status";
				$conditions = "AND s.schilde > 0 AND s.cloak != 1";
				$desc = "die Schilde";
				break;
			case "wa":
				$system = "warp";
				$conditions = "AND s.systems_id = 0";
				$desc = "den Warpantrieb";
				break;
			case "pw":
				$system = "wea_phaser";
				$conditions = "AND (SELECT b.w1 FROM `stu_ships_buildplans` as b WHERE b.plans_id = s.plans_id) > 0";
				$desc = "die Prim�rwaffen";
				break;
			case "sw":
				$system = "wea_torp";
				$conditions = "AND (SELECT b.w2 FROM `stu_ships_buildplans` as b WHERE b.plans_id = s.plans_id) > 0";
				$desc = "die Sekund�rwaffen";
				break;
			case "ck":
				$system = "cloak";
				$desc = "die Tarnvorrichtung";
				$res = "";

				$notEps = $this->db->query("SELECT * FROM stu_ships as s WHERE s.fleets_id = " . $this->s->fleets_id . " AND s.eps = 0 AND s.crew >= s.min_crew AND s.cloak != 1 AND s.cloak < " . time() . " AND s.cloakable='1' AND s.schilde_status != 1", 3);
				if ($notEps > 0) $res .= "<br>" . $notEps . " Schiffe haben den Befehl nicht ausgef�hrt wegen: EPS ist leer.";
				$notCrew = $this->db->query("SELECT * FROM stu_ships as s WHERE s.fleets_id = " . $this->s->fleets_id . " AND s.eps > 0 AND s.crew < s.min_crew AND s.cloak != 1 AND s.cloak < " . time() . " AND s.cloakable='1' AND s.schilde_status != 1", 3);
				if ($notCrew > 0) $res .= "<br>" . $notCrew . " Schiffe haben den Befehl nicht ausgef�hrt wegen: Zuwenig Crew.";
				$notCloakTime = $this->db->query("SELECT * FROM stu_ships as s WHERE s.fleets_id = " . $this->s->fleets_id . " AND s.eps > 0 AND s.crew >= s.min_crew AND s.cloak != 1 AND s.cloak >= " . time() . " AND s.cloakable='1' AND s.schilde_status != 1", 3);
				if ($notCloakTime > 0) $res .= "<br>" . $notCloakTime . " Schiffe haben den Befehl nicht ausgef�hrt wegen: Tarnvorrichtung noch nicht bereit.";
				$notShields = $this->db->query("SELECT * FROM stu_ships as s WHERE s.fleets_id = " . $this->s->fleets_id . " AND s.eps > 0 AND s.crew >= s.min_crew AND s.cloak != 1 AND s.cloak < " . time() . " AND s.cloakable='1' AND s.schilde_status = 1", 3);
				if ($notShields > 0) $res .= "<br>" . $notShields . " Schiffe haben den Befehl nicht ausgef�hrt wegen: Schilde aktiv.";



				$num = $this->db->query("UPDATE stu_ships as s SET s.cloak = '1', s.eps = s.eps-1 WHERE s.fleets_id = " . $this->s->fleets_id . " AND s.eps > 0 AND s.crew >= s.min_crew AND s.cloak != 1 AND s.cloak < " . time() . " AND s.cloakable='1' AND s.schilde_status != 1", 6);


				$res = $num . " Schiffe der Flotte haben " . $desc . " aktiviert." . $res;
				return $res;

				break;
			default:
				return "Unbekanntes System";
		}

		$num = $this->db->query("UPDATE stu_ships as s SET s." . $system . " = '1', s.eps = s.eps-1 WHERE s." . $system . " = '0' AND s.fleets_id = " . $this->s->fleets_id . " AND s.eps > 0 AND s.crew >= s.min_crew " . $conditions, 6);

		return $num . " Schiffe der Flotte haben " . $desc . " aktiviert.";
	}


	function dv($d)
	{
		$system = "";
		$setto = 0;
		switch ($d) {
			case "sh":
				$system = "schilde_status";
				$conditions = "AND s.schilde > 0";
				$desc = "die Schilde";
				break;
			case "wa":
				$system = "warp";
				$conditions = "";
				$desc = "den Warpantrieb";
				break;
			case "pw":
				$system = "wea_phaser";
				$conditions = "AND (SELECT b.w1 FROM `stu_ships_buildplans` as b WHERE b.plans_id = s.plans_id) > 0";
				$desc = "die Prim�rwaffen";
				break;
			case "sw":
				$system = "wea_torp";
				$conditions = "AND (SELECT b.w2 FROM `stu_ships_buildplans` as b WHERE b.plans_id = s.plans_id) > 0";
				$desc = "die Sekund�rwaffen";
				break;
			case "ck":
				$system = "cloak";
				$conditions = "AND s.cloak = 1";
				$desc = "die Tarnvorrichtung";
				$setto = time() + 900;
				break;
			default:
				return "Unbekanntes System";
		}

		$num = $this->db->query("UPDATE stu_ships as s SET s." . $system . " = '" . $setto . "' WHERE s." . $system . " = '1' AND s.fleets_id = " . $this->s->fleets_id . " AND s.eps > 0 AND s.crew >= s.min_crew " . $conditions, 6);

		return $num . " Schiffe der Flotte haben " . $desc . " deaktiviert.";
	}











	function activate_shields()
	{
		if ($this->s->map['shieldoff'] == 1) return "Die Schilde k�nnen nicht aktiviert werden (Grund: " . $this->s->map['name'] . ")";
		$this->db->query("UPDATE stu_ships SET schilde_status=0 WHERE schilde_status>1 AND schilde_status<" . time());
		$result = $this->db->query("SELECT id,name,crew,min_crew,eps,schilde_status,cloak,schilde,traktormode,dock FROM stu_ships WHERE schilde_status!=1 AND fleets_id=" . $this->s->fleets_id . " AND still=0 LIMIT 25");
		if (mysql_num_rows($result) == 0) return "Flottenbefehl \"Schilde aktivieren\" wurde ausgef�hrt";
		while ($data = mysql_fetch_assoc($result)) {
			$return = shipexception(array("cloak" => 0, "eps" => 2, "schilde" => 0, "traktor" => 0, "dock" => 0, "crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Schilde k�nnen nicht aktiviert werden (Grund: " . $return['msg'] . ")";
				continue;
			}
			if ($this->s->checksubsystem(2, $data['id']) == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Schilde k�nnen nicht aktiviert werden (Grund: Reparatur noch nicht abgeschlossen)";
				continue;
			}
			if ($data['schilde_status'] > time()) {
				$fmsg .= "<br>" . $data['name'] . ": Schilde k�nnen nicht aktiviert werden (Grund: Polarisiert bis " . date("d.m H:i", $data['schilde_status']) . ")";
				continue;
			}
			if ($this->s->cfield == 6 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=" . $data['id'] . " LIMIT 1", 1) == 5) {
				$fmsg .= "<br>" . $data['name'] . ": Schilde k�nnen nicht aktiviert werden (Grund: " . $this->s->map['name'] . ")";
				continue;
			}
			if ($this->s->cfield == 9 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=" . $data['id'] . " LIMIT 1", 1) == 7) {
				$fmsg .= "<br>" . $data['name'] . ": Schilde k�nnen nicht aktiviert werden (Grund: " . $this->s->map['name'] . ")";
				continue;
			}
			if ($this->s->cfield == 15 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=" . $data['id'] . " LIMIT 1", 1) == 4) {
				$fmsg .= "<br>" . $data['name'] . ": Schilde k�nnen nicht aktiviert werden (Grund: " . $this->s->map['name'] . ")";
				continue;
			}
			$this->db->query("UPDATE stu_ships SET schilde_status=1,eps=eps-2 WHERE id=" . $data['id'] . " LIMIT 1");
		}
		return "Flottenbefehl \"Schilde aktivieren\" wurde ausgef�hrt" . $fmsg;
	}

	function activate_phaser()
	{
		$result = $this->db->query("SELECT id,name,crew,min_crew,eps FROM stu_ships WHERE wea_phaser='0' AND cloak!='1' AND phaser>0 AND fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		if (mysql_num_rows($result) == 0) return "Flottenbefehl \"Waffensystem (Strahlenwaffe) aktivieren\" wurde ausgef�hrt";
		while ($data = mysql_fetch_assoc($result)) {
			$return = shipexception(array("eps" => 1, "crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Waffensystem (Strahlenwaffe) kann nicht aktiviert werden (Grund: " . $return['msg'] . ")";
				continue;
			}
			if ($this->s->checksubsystem(6, $data['id']) == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Waffensystem (Strahlenwaffe) kann nicht aktiviert werden (Grund: Reparatur noch nicht abgeschlossen)";
				continue;
			}
			$this->db->query("UPDATE stu_ships SET eps=eps-1,wea_phaser='1' WHERE id=" . $data['id'] . " LIMIT 1");
		}
		return "Flottenbefehl \"Waffensystem (Strahlenwaffe) aktivieren\" wurde ausgef�hrt" . $fmsg;
	}

	function deactivate_phaser()
	{
		$this->db->query("UPDATE stu_ships SET wea_phaser='0' WHERE fleets_id=" . $this->s->fleets_id . " AND wea_phaser='1' LIMIT 25");
		return "Flottenbefehl \"Waffensystem (Strahlenwaffe) deaktivieren\" wurde ausgef�hrt" . $fmsg;
	}

	function activate_torps()
	{
		$result = $this->db->query("SELECT id,name,crew,min_crew,eps,torp_type FROM stu_ships WHERE wea_torp='0' AND cloak!='1' AND torp_type>0 AND fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		if (mysql_num_rows($result) == 0) return "Flottenbefehl \"Waffensystem (Torpedob�nke) aktivieren\" wurde ausgef�hrt";
		while ($data = mysql_fetch_assoc($result)) {
			$return = shipexception(array("eps" => 1, "crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Waffensystem (Torpedob�nke) kann nicht aktiviert werden (Grund: " . $return['msg'] . ")";
				continue;
			}
			if ($this->s->checksubsystem(10, $data['id']) == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Waffensystem (Torpedob�nke) kann nicht aktiviert werden (Grund: Reparatur noch nicht abgeschlossen)";
				continue;
			}
			if ($data['torp_type'] == 0) {
				$fmsg .= "<br>" . $data['name'] . ": Waffensystem (Torpedob�nke) kann nicht aktiviert werden (Grund: Keine Torpedos geladen)";
				continue;
			}
			$this->db->query("UPDATE stu_ships SET eps=eps-1,wea_torp='1' WHERE id=" . $data['id'] . " LIMIT 1");
		}
		return "Flottenbefehl \"Waffensystem (Torpedob�nke) aktivieren\" wurde ausgef�hrt" . $fmsg;
	}

	function deactivate_torps()
	{
		$this->db->query("UPDATE stu_ships SET wea_torp='0' WHERE fleets_id=" . $this->s->fleets_id . " AND wea_torp='1' LIMIT 25");
		return "Flottenbefehl \"Waffensystem (Torpedob�nke) deaktivieren\" wurde ausgef�hrt" . $fmsg;
	}

	function activate_cloak()
	{
		if ($this->s->map['cloakoff'] == 1) return "Die Tarnung kann nicht aktiviert werden (Grund: " . $this->s->map[name] . ")";
		$result = $this->db->query("SELECT id,name,crew,min_crew,eps,schilde_status,cloak,schilde,traktormode,fleets_id FROM stu_ships WHERE cloak!='1' AND cloakable='1' AND still=0 AND maintain=0 AND fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			$fl = $data[fleets_id];
			$return = shipexception(array("eps" => 3, "traktor" => 0, "crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Tarnung kann nicht aktiviert werden (Grund: " . $return['msg'] . ")";
				continue;
			}
			if ($this->s->checksubsystem(9, $data['id']) == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Tarnung kann nicht aktiviert werden (Grund: Reparatur noch nicht abgeschlossen)";
				continue;
			}
			if ($data['cloak'] > time()) {
				$fmsg .= "<br>" . $data['name'] . ": Tarnung kann nicht aktiviert werden (Grund: Chronitonenabbau bis " . date("d.m H:i", $data['cloak']) . ")";
				continue;
			}
			if ($data['schilde_status'] > 1) $sd = $data['schilde_status'];
			else $sd = 0;
			$this->db->query("UPDATE stu_ships SET schilde_status=" . $sd . ",eps=eps-3,cloak='1',wea_phaser='0',wea_torp='0' WHERE id=" . $data['id'] . " LIMIT 1");
		}
		$msga = "<br>" . $this->stopblockade($fl);
		return "Flottenbefehl \"Tarnung aktivieren\" wurde ausgef�hrt" . $fmsg . $msga;
	}

	function deactivate_shields()
	{
		$this->db->query("UPDATE stu_ships SET schilde_status=0 WHERE schilde_status=1 AND fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		return "Flottenbefehl \"Schilde deaktivieren\" wurde ausgef�hrt";
	}

	function deactivate_cloak()
	{
		$result = $this->db->query("SELECT id,rumps_id,cx,cy,sx,sy FROM stu_ships WHERE cloak='1' AND fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		while ($data = mysql_fetch_assoc($result)) {
			if ($this->s->systems_id > 0) {
				$res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=" . $this->sess['allys_id'] . ",cloak='0',notified='0' WHERE sx=" . $data['sx'] . " AND sy=" . $data['sy'] . " AND ships_id=" . $data['id'] . " AND systems_id=" . $this->s->systems_id . " LIMIT 1", 6);
				if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,sx,sy,systems_id,cloak) VALUES ('" . $this->uid . "','" . $data['id'] . "','" . $data['rumps_id'] . "','" . $this->sess['allys_id'] . "',NOW(),'" . $this->sy . "','" . $this->sy . "','" . $this->s->systems_id . "','0')");
			} else {
				$res = $this->db->query("UPDATE stu_sectorflights SET date=NOW(),allys_id=" . $this->sess['allys_id'] . ",cloak='0' WHERE cx=" . $data['cx'] . " AND cy=" . $data['cy'] . " AND ships_id=" . $data['id'] . " LIMIT 1", 6);
				if ($res == 0) $this->db->query("INSERT INTO stu_sectorflights (user_id,ships_id,rumps_id,allys_id,date,cx,cy,cloak) VALUES ('" . $this->uid . "','" . $data['id'] . "','" . $data['rumps_id'] . "','" . $this->sess['allys_id'] . "',NOW(),'" . $data['cx'] . "','" . $data['cy'] . "','0')");
			}


			$hpfac = 1 - ($this->s->huelle / $this->s->max_huelle);

			$hpfac = round($hpfac * 1800);

			$cloak = time() + 900 + $hpfac;

			$result2 = $this->db->query("UPDATE stu_ships SET cloak='" . $cloak . "' WHERE cloak='1' AND id=" . $data[id] . " LIMIT 1", 6);

			if ($result2 != 0) $keineahnungwasdassoll = 1;
		}
		if ($keineahnungwasdassoll == 1) {
			$res = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=" . $this->s->fleets_id . " LIMIT 25");
			while ($data = mysql_fetch_assoc($res)) $this->db->query("DELETE FROM stu_ships_decloaked WHERE ships_id=" . $data['id'] . " AND UNIX_TIMESTAMP(date)=0 LIMIT 1");
			$this->systems_id = $this->s->systems_id;
			if ($this->s->systems_id > 0) {
				$this->nx = $this->s->sx;
				$this->ny = $this->s->sy;
				$this->systems_id = $this->s->systems_id;
			} else {
				$this->nx = $this->s->cx;
				$this->ny = $this->s->cy;
			}
			//if ($this->db->query("SELECT COUNT(id) FROM stu_ships WHERE fleets_id=".$this->s->fleets_id." AND warp='0'",1) != 0) $ramsg = $this->s->fleet_redalert();
		}
		return "Flottenbefehl \"Tarnung deaktivieren\" wurde ausgef�hrt" . $ramsg;
	}

	function activate_warp()
	{
		if ($this->s->systems_id > 0) return "Der Warpantrieb kann innerhalb eines Systems nicht genutzt werden";
		$result = $this->db->query("SELECT id,name,crew,min_crew,eps FROM stu_ships WHERE warp='0' AND warpable='1' AND fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			$return = shipexception(array("eps" => 2, "crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Warpantrieb kann nicht aktiviert werden (Grund: " . $return['msg'] . ")";
				continue;
			}
			if ($this->s->checksubsystem(11, $data['id']) == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Warpantrieb kann nicht aktiviert werden (Grund: Reparatur am Warpantrieb noch nicht abgeschlossen)";
				continue;
			}
			$this->db->query("UPDATE stu_ships SET eps=eps-2,warp='1' WHERE id=" . $data['id'] . " LIMIT 1");
		}
		return "Flottenbefehl \"Warp aktivieren\" wurde ausgef�hrt" . $fmsg;
	}

	function deactivate_warp()
	{
		if ($this->s->systems_id > 0) return;
		$result = $this->db->query("UPDATE stu_ships SET warp='0' WHERE warp='1' AND fleets_id=" . $this->s->fleets_id . " LIMIT 25", 6);
		if ($result == 0) return "Keine Schiffe der Flotte befinden sich im Warp";
		$this->systems_id = $this->s->systems_id;
		$this->nx = $this->s->cx;
		$this->ny = $this->s->cy;
		$ramsg = $this->s->fleet_redalert();
		return "Flottenbefehl \"Warp deaktivieren\" wurde ausgef�hrt" . $ramsg;
	}

	function attack(&$tarId, &$fleetId, $redalert = 0)
	{
		if (!check_int($tarId)) return;
		$target = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,a.cfield,b.slots,b.trumfield,b.is_shuttle,c.evade,c.m1,c.m2,c.m6,d.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_user as d ON d.id=a.user_id WHERE a.id=" . $tarId . " LIMIT 1", 4);
		if ($target == 0) return;
		$res = $this->db->query("SELECT id,name,torp_type,phaser,nbs,cloak,eps,schilde,schilde_status,traktor,traktormode FROM stu_ships WHERE fleets_id=" . $fleetId . " AND warp='0' LIMIT 25");
		while ($fd = mysql_fetch_assoc($res)) {
			if ($target['fleets_id'] > 0) $tar = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=" . $target['fleets_id'] . " AND warp='0' ORDER BY RAND() LIMIT 1", 1);
			else {
				if ($this->dsships[$data['id']] == 1) break;
				$tar = $tarId;
			}
			$result .= $this->s->attack($tar, $fd['id'], 1);
			if ($this->umode == 1) return $result;
		}
		if ($redalert == 1 || ($data['fleets_id'] == 0 && $this->s->dsships[$target['id']] == 1) || $this->s->fleet_hit != 1) return $result;
		$field = $this->m->getFieldByType($target['cfield']);
		if ($target['fleets_id'] > 0) {
			$result .= "<br /><b>Gegenangriff</b><br />";
			$res = $this->db->query("SELECT id,name,torp_type,phaser,nbs,cloak,eps,alvl,schilde,schilde_status,traktor,traktormode,wea_phaser,wea_torp,phaser,torp_type,huelle,max_huelle FROM stu_ships WHERE fleets_id=" . $target['fleets_id'] . " LIMIT 25");
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
				if ($fd['nbs'] != 1 && $fd['eps'] > 1 && $this->s->checksubsystem(4, $fd['id']) != 1) {
					$fd['nbs'] = 1;
					$result .= "- Die " . $fd['name'] . " aktiviert die Sensoren<br>";
					$fd['eps'] -= 1;
					$qry = 1;
				}
				if (($fd['schilde_status'] == 0 || ($fd['schilde_status'] > 1 && $fd['schilde_status'] < time())) && $fd['traktormode'] != 2 && $fd['schilde'] > 0 && $fd['eps'] > 1 && $this->s->checksubsystem(2, $fd['id']) != 1 && $field['shieldoff'] != 1) {
					if ($fd['traktor'] > 0) $this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=" . $target['id'] . " OR id=" . $fd['traktor'] . " LIMIT 2");
					$fd['schilde_status'] = 1;
					$result .= "- Die " . $fd['name'] . " aktiviert die Schilde<br>";
					$fd['eps'] -= 1;
					$qry = 1;
				}
				if ($fd['alvl'] > 1 && $fd['eps'] > 0 && (($fd['phaser'] > 0 && $fd['wea_phaser'] == 0 && $this->s->checksubsystem(6, $fd['id']) != 1) || ($fd['torp_type'] > 0 && $fd['wea_torp'] == 0 && $this->s->checksubsystem(10, $fd['id']) != 1))) {
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
				$tar = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=" . $fleetId . " AND warp='0' AND cloak!='1' ORDER BY RAND() LIMIT 1", 1);
				$result .= $this->s->attack($tar, $fd['id'], 1, 1);
			}
		} else {
			if ($target['alvl'] == 1) return $result;
			$result .= "<b>Gegenangriff</b><br />";
			if ($target['cloak'] == 1) {
				$hpfac = 1 - ($target['huelle'] / $target['max_huelle']);
				$hpfac = round($hpfac * 1800);
				$target['cloak'] = time() + 900 + $hpfac;
				$result .= "- Die " . $target['name'] . " deaktiviert die Tarnung<br>";
				$qry = 1;
			}
			if ($target['nbs'] != 1 && $target['eps'] > 1 && $this->s->checksubsystem(4, $target['id']) != 1) {
				$target['nbs'] = 1;
				$result .= "- Die " . $target['name'] . " aktiviert die Sensoren<br>";
				$target['eps'] -= 1;
				$qry = 1;
			}
			if (($target['schilde_status'] == 0 || ($target['schilde_status'] > 1 && $target['schilde_status'] < time())) && $target['traktormode'] != 2 && $target['schilde'] > 0 && $target['eps'] > 1 && $this->s->checksubsystem(2, $target['id']) != 1) {
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
			if ($target['eps'] > 0 && (($target['phaser'] > 0 && $target['wea_phaser'] == 0 && $this->s->checksubsystem(6, $target['id']) != 1) || ($target['torp_type'] > 0 && $target['wea_torp'] == 0 && $this->s->checksubsystem(10, $target['id']) != 1))) {
				if ($fd['wea_phaser'] == 0 && $fd['wea_torp'] == 0) $con = 1;
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
			$tar = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=" . $fleetId . " AND warp='0' AND cloak!='1' ORDER BY RAND() LIMIT 1", 1);
			$result .= $this->s->attack($tar, $target['id'], 1, 1);
		}
		$blockade = $this->db->query("SELECT * FROM stu_colonies_actions WHERE value =" . $tarId . " AND (var='fdef' OR var='fblock' OR var='fattack')", 4);
		if ($blockade != 0) {
			if ($blockade['var'] == "fdef") $blocktext = "Verteidigung";
			elseif ($blockade['var'] == "fblock") $blocktext = "Blockade";
			else $blocktext = "Angriff";
			$blocktext .= " wurde abgebrochen.<br>";
			$this->db->query("DELETE FROM stu_colonies_actions WHERE value =" . $data['fleets_id'] . " AND var != 'db' LIMIT 1");
		} else $blocktext = "";
		return $result;
	}

	function strikeback($data, $tarfleet, $ship = 0)
	{
		$res = $this->db->query("SELECT id,name,torp_type,phaser,cloak,nbs,eps,schilde,schilde_status,traktor,traktormode,alvl,phaser,torp_type,wea_phaser,wea_torp,maintain,cfield,huelle,max_huelle FROM stu_ships WHERE fleets_id=" . $tarfleet . " AND maintain=0 LIMIT 25");
		if (mysql_num_rows($res) == 0) return;
		while ($fd = mysql_fetch_assoc($res)) {
			$qry = 0;
			$con = 0;
			if ($ship == 0) $tar = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=" . $data . " AND warp='0' AND cloak!='1' ORDER BY RAND() LIMIT 1", 1);
			if ($tar == 0 || $ship == 1) $tar = $this->db->query("SELECT id FROM stu_ships WHERE id=" . $data . " LIMIT 1", 1);
			if (!$tar || $tar == 0) break;
			if (!$field) $field = $this->m->getFieldByType($fd['cfield']);
			if ($fd['cloak'] == 1) {

				$hpfac = 1 - ($fd['huelle'] / $fd['max_huelle']);
				$hpfac = round($hpfac * 1800);
				$fd['cloak'] = time() + 900 + $hpfac;
				$msg .= "- Die " . $fd['name'] . " deaktiviert die Tarnung<br>";
				$qry = 1;
			}
			if ($fd['nbs'] != 1 && $fd['eps'] > 1 && $this->s->checksubsystem(4, $fd['id']) != 1) {
				$fd['nbs'] = 1;
				$msg .= "- Die " . $fd['name'] . " aktiviert die Sensoren<br>";
				$fd['eps'] -= 1;
				$qry = 1;
			}
			if (($fd['schilde_status'] == 0 || ($fd['schilde_status'] > 1 && $fd['schilde_status'] < time())) && $fd['traktormode'] != 2 && $fd['schilde'] > 0 && $fd['eps'] > 1 && $this->s->checksubsystem(2, $fd['id']) != 1 && $field['shieldoff'] != 1) {
				if ($fd['traktor'] > 0) $this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=" . $tar . " OR id=" . $fd['traktor'] . " LIMIT 2");
				$fd['schilde_status'] = 1;
				$msg .= "- Die " . $fd['name'] . " aktiviert die Schilde<br>";
				$fd['eps'] -= 1;
				$qry = 1;
			}
			if ($fd['alvl'] > 1 && $fd['eps'] > 0 && (($fd['phaser'] > 0 && $fd['wea_phaser'] == 0 && $this->s->checksubsystem(6, $fd['id']) != 1) || ($fd['torp_type'] > 0 && $fd['wea_torp'] == 0 && $this->s->checksubsystem(10, $fd['id']) != 1))) {
				if ($fd['wea_phaser'] == 0 && $fd['wea_torp'] == 0) $con = 1;
				if ($fd['phaser'] > 0 && $fd['wea_phaser'] == 0) {
					$fd['eps'] -= 1;
					$fd['wea_phaser'] = 1;
				}
				if ($fd['torp_type'] > 0 && $fd['eps'] > 0 && $fd['wea_torp'] == 0) {
					$fd['eps'] -= 1;
					$fd['wea_torp'] = 1;
				}
				$msg .= "- Die " . $fd['name'] . " aktiviert die Waffensysteme<br />";
				$qry = 1;
			}
			if ($qry == 1) $this->db->query("UPDATE stu_ships SET eps=" . $fd['eps'] . ",nbs='" . $fd['nbs'] . "',schilde_status=" . $fd['schilde_status'] . ",cloak='" . $fd['cloak'] . "',wea_phaser='" . $fd['wea_phaser'] . "',wea_torp='" . $fd['wea_torp'] . "',still=0 WHERE id=" . $fd['id'] . " LIMIT 1");
			if ($con == 1) continue;
			$msg .= $this->s->attack($tar, $fd['id'], 0, 1);
		}
		if (strlen(strip_tags($msg)) > 0) $msg = "<b>Gegenangriff</b><br>" . $msg;
		return $msg;
	}

	function changeflaggship($nf)
	{
		$data = $this->db->query("SELECT id,name FROM stu_ships WHERE id=" . $nf . " AND fleets_id=" . $this->s->fleets_id . " LIMIT 1", 4);
		if ($data == 0) return;
		$this->db->query("UPDATE stu_fleets SET ships_id=" . $nf . " WHERE fleets_id=" . $this->s->fleets_id . " LIMIT 1");
		return "Die " . $data['name'] . " wurde zum neuen Flaggschiff der Flotte ernannt";
	}

	function activate_kss()
	{
		//if ($this->s->map['sensoroff'] == 1) return "Die Sensoren k�nnen nicht aktiviert werden (Grund: ".$this->s->map['name'].")";
		//if ($this->s->map['type'] == 8) return "Die Sensoren k�nnen nicht aktiviert werden (Grund: ".$this->s->map['name'].")";
		if ($this->sess["level"] < 2) return "Die Sensoren k�nnen nicht aktiviert werden (Grund: Erst ab Level 2 m�glich)";
		$result = $this->db->query("SELECT id,name,crew,min_crew,eps FROM stu_ships WHERE (nbs='0' OR ISNULL(nbs) OR nbs='') ANd still=0 AND fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		if (mysql_num_rows($result) == 0) return "Flottenbefehl \"Nahbereichsensoren aktivieren\" wurde ausgef�hrt";
		while ($data = mysql_fetch_assoc($result)) {
			$return = shipexception(array("eps" => 1, "crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Nahbereichssensoren k�nnen nicht aktiviert werden (Grund: " . $return['msg'] . ")";
				continue;
			}
			if ($this->s->checksubsystem(4, $data['id']) == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Nahbereichssensoren k�nnen nicht aktiviert werden (Grund: Reparatur noch nicht abgeschlossen)";
				continue;
			}
			$this->db->query("UPDATE stu_ships SET nbs='1',eps=eps-1 WHERE id=" . $data['id'] . " LIMIT 1");
		}
		return "Flottenbefehl \"Nahbereichsensoren aktivieren\" wurde ausgef�hrt" . $fmsg;
	}

	function deactivate_kss()
	{
		$this->db->query("UPDATE stu_ships SET nbs='0' WHERE nbs='1' AND fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		return "Flottenbefehl \"Nahbereichsensoren deaktivieren\" wurde ausgef�hrt";
	}

	function emergencybatt($bcount)
	{
		$result = $this->db->query("SELECT id,name,eps,max_eps,batt,max_batt,crew,min_crew,batt_wait FROM stu_ships WHERE still=0 AND fleets_id=" . $this->s->fleets_id . " AND eps<max_eps AND batt>0 LIMIT 25");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			$return = shipexception(array("crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Die Ersatzbatterie kann nicht entladen werden (Grund: " . $return['msg'] . ")";
				continue;
			}
			if ($this->s->checksubsystem(4, $data['id']) == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Die Ersatzbatterie kann nicht entladen werden (Grund: Reparatur noch nicht abgeschlossen)";
				continue;
			}
			if ($data['batt_wait'] > time()) {
				$fmsg .= "<br>" . $data['name'] . ": Die Ersatzbatterie kann nicht entladen werden (Grund: Die Batterie kann erst am " . date("d.m.Y H:i", $data[batt_wait]) . " wieder entladen werden)";
				continue;
			}
			if ($bcount == "m") $count = $data['batt'];
			else {
				$count = $bcount;
				if ($count > $data['batt']) $count = $data['batt'];
			}
			if ($count > round($data['max_batt'] / 2)) $count = round($data['max_batt'] / 2);
			if ($count > $data['max_eps'] - $data['eps']) $count = $data['max_eps'] - $data['eps'];
			$this->db->query("UPDATE stu_ships SET batt=batt-" . $count . ",eps=eps+" . $count . ",batt_wait=" . ($data['batt'] - $count <= 0 ? 0 : time() + $count * 600) . " WHERE id=" . $data['id'] . " LIMIT 1");
			$fmsg .= "<br>" . $data['name'] . ": Ersatzbatterie um " . $count . " Energie entladen";
		}
		return "Flottenbefehl \"Ersatzbatterie entladen\" wurde ausgef�hrt" . $fmsg;
	}

	function loadshields($bcount)
	{
		$result = $this->db->query("SELECT id,name,eps,schilde,max_schilde,crew,min_crew,cloak,schilde_status FROM stu_ships WHERE still=0 AND fleets_id=" . $this->s->fleets_id . " AND schilde<max_schilde AND eps>0 LIMIT 25");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			$return = shipexception(array("schilde_status" => 0, "cloak" => 0, "crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Schilde k�nnen nicht geladen werden (Grund: " . $return['msg'] . ")";
				continue;
			}
			if ($this->s->checksubsystem(4, $data['id']) == 1) {
				$fmsg .= "<br>" . $data['name'] . ": Schilde k�nnen nicht geladen werden (Grund: Reparatur noch nicht abgeschlossen)";
				continue;
			}
			if ($bcount == "m") $count = $data['eps'];
			else {
				$count = $bcount;
				if ($count > $data['eps']) $count = $data['eps'];
			}

			if ($count > $data['max_schilde'] - $data['schilde']) $count = $data['max_schilde'] - $data['schilde'];


			$timedata = $this->db->query("SELECT schilde_status as ss FROM stu_ships WHERE id=" . $data['id'], 4);
			if ($timedata['ss'] > 1) $newtime = $timedata['ss'];
			else $newtime = time();
			$newtime += 30 * $count;

			$this->db->query("UPDATE stu_ships SET eps=eps-" . $count . ",schilde=schilde+" . $count . ",schilde_status=" . (time() + 21600) . " WHERE id=" . $data['id'] . " LIMIT 1");
			$fmsg .= "<br>" . $data[name] . ": Schilde um " . $count . " Energie geladen";
		}
		return "Flottenbefehl \"Schilde laden\" wurde ausgef�hrt" . $fmsg;
	}

	function intercept($target)
	{
		if ($this->s->systems_id > 0) return;
		$return = shipexception(array("warpstate" => 1, "nbs" => 1, "eps" => 1, "crew" => $this->s->min_crew), $this->s);
		if ($return['code'] == 1) return "Flaggschiff: " . $return['msg'];
		if ($this->db->query("SELECT COUNT(id) FROM stu_ships WHERE cloak='1' AND fleets_id=" . $this->s->fleets_id, 1) > 0) return "Es m�ssen alle Schiffe der Flotte enttarnt sein";
		$target = $this->db->query("SELECT a.id,a.fleets_id,a.user_id,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.name,a.warp,a.cloak,a.crew,a.min_crew,a.eps,c.warp_capability,d.vac_active FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b ON b.plans_id=a.plans_id LEFT JOIN stu_modules as c ON b.m11=c.module_id LEFT JOIN stu_user as d ON d.id=a.user_id WHERE a.warp='1' AND a.id=" . $target . " LIMIT 1", 4);
		if ($target == 0) return;
		if ($target['user_id'] == $this->sess['uid']) return;
		if ($target['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($target['fleets_id'] > 0 && $target['warp_capability'] < 9) $target = $this->db->query("SELECT a.id,a.fleets_id,a.user_id,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.name,a.warp,a.crew,a.min_crew,a.eps,c.warp_capability,d.name as fname FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_modules as c ON b.m11=c.module_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id WHERE a.warp='1' AND a.fleets_id=" . $target[fleets_id] . " ORDER BY c.warp_capability LIMIT 1", 4);
		if ($this->s->cx != $target['cx'] || $this->s->cy != $target['cy']) return;
		$result = $this->db->query("SELECT id,fleets_id,name,eps,warp,warpable,cloak,crew,min_crew,nbs FROM stu_ships WHERE id!=" . $this->s->id . " AND fleets_id=" . $this->s->fleets_id . " LIMIT 14");
		while ($data = mysql_fetch_assoc($result)) {
			if ($data['crew'] < $data['min_crew']) {
				$wmsg .= "<br>- " . $data['name'] . ": Es werden mindestens " . $data['min_crew'] . " Besatzungsmitglieder ben�tigt - Flotte verlassen";
				$this->db->query("UPDATE stu_ships SET fleets_id=0 WHERE id=" . $data['id'] . " LIMIT 1");
				continue;
			}
			if ($data['nbs'] != 1 && $data['eps'] > 0) {
				$data['eps'] -= 1;
				$data['nbs'] = 1;
				$wmsg .= "<br>- " . $data['name'] . ": Die Sensoren wurden aktiviert";
			}
			if ($data['eps'] < 2) {
				$wmsg .= "<br>- " . $data['name'] . ": Es werden mindestens 2 Energie ben�tigt - Flotte verlassen";
				$this->db->query("UPDATE stu_ships SET fleets_id=0,nbs='" . $data['nbs'] . "' WHERE id=" . $data['id'] . " LIMIT 1");
				continue;
			}
			if ($data['warp'] != 1 && $data['eps'] > 1) {
				$data['eps'] -= 2;
				$data['warp'] = 1;
				$wmsg .= "<br>- " . $data['name'] . ": Der Warpantrieb wurde aktiviert";
			}
			if ($data['eps'] == 0) {
				$wmsg .= "<br>- " . $data['name'] . ": Es wird mindestens 1 Energie ben�tigt - Flotte verlassen";
				$this->db->query("UPDATE stu_ships SET fleets_id=0,nbs='" . $data['nbs'] . "',warp='" . $data['warp'] . "' WHERE id=" . $data['id'] . " LIMIT 1");
				continue;
			}
			$this->db->query("UPDATE stu_ships SET fleets_id=" . $data['fleets_id'] . ",nbs='" . $data['nbs'] . "',eps=" . $data['eps'] . ",warp='" . $data['warp'] . "' WHERE id=" . $data['id'] . " LIMIT 1");
		}
		$data = $this->db->query("SELECT a.id,a.user_id,a.cx,a.cy,a.name,a.warp,a.crew,a.min_crew,a.eps,c.warp_capability,d.name as fname FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_modules as c ON b.m11=c.module_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id WHERE a.fleets_id=" . $this->s->fleets_id . " ORDER BY c.warp_capability LIMIT 1", 4);
		if ($data == 0) return "<b>Flottenbefehl \"Abfangkurs setzen\" wurde ausgef�hrt</b>" . $wmsg . "<br>Es wurde keine weitere Aktion durchgef�hrt";
		if ($target['cloak'] == 1 && $this->db->query("SELECT ships_id FROM stu_ships_decloaked WHERE UNIX_TIMESTAMP(date)=0 AND ships_id=" . $target['id'] . " AND user_id=" . $this->uid . " LIMIT 1", 1) == 0) return;
		if ($target['warp_capability'] > $data['warp_capability']) $chance = 10 - ($target['warp_capability'] - $data['warp_capability']);
		else $chance = 10 + (($data['warp_capability'] - $target['warp_capability']) * 20);
		if (rand(1, 100) <= $chance) {
			$msg .= "<br>Die " . ($target['fleets_id'] > 0 ? "Flotte " . $target['fname'] : $target['name']) . " wurde erfolgreich abgefangen";
			$this->db->query("UPDATE stu_ships SET eps=eps-1,warp='0' WHERE fleets_id=" . $this->s->fleets_id . " LIMIT 25");
			$this->systems_id = $this->s->systems_id;
			$this->nx = $this->s->cx;
			$this->ny = $this->s->cy;
			$ramsg = $this->s->fleet_redalert();
			if ($target['fleets_id'] > 0) {
				$this->db->query("UPDATE stu_ships SET warp='0' WHERE fleets_id=" . $target['fleets_id'] . " LIMIT 25");
				$rm = $this->strikeback($this->s->fleets_id, $target['fleets_id'], 0);
				if (strlen(strip_tags($rm)) > 0)  $msg .= "<br><b>Die abgefangene Flotte formiert sich zum Angriff</b><br>" . $rm;
			} else {
				$this->db->query("UPDATE stu_ships SET warp='0' WHERE id=" . $target['id'] . " LIMIT 1");
				if ($target['eps'] > 0 && $target['crew'] >= $target['crew_min']) {
					$rm = $this->s->attack($this->s->id, $target['id'], 0, 1);
					if (strlen(strip_tags($rm)) > 0)  $msg .= "<br><b>Das abgefangene Schiff startet ein Angriffsman�ver</b><br>" . $rm;
				}
			}
			$this->s->send_pm($data['user_id'], $target['user_id'], "Die " . ($target['fleets_id'] > 0 ? "Flotte " . $target['fname'] : $target['name']) . " wurde von der Fotte " . $data['fname'] . " in Sektor " . $data['cx'] . "|" . $data['cy'] . " abgefangen", 3);
		} else {
			$this->db->query("UPDATE stu_ships SET eps=eps-1 WHERE fleets_id=" . $this->s->fleets_id . " LIMIT 25");
			$msg .= "<br>Die " . ($target['fleets_id'] > 0 ? "Flotte " . $target['fname'] : $target['name']) . " konnte nicht abgefangen werden";
			$this->s->send_pm($data['user_id'], $target['user_id'], "Die Flotte " . $data['fname'] . " hat versucht die " . ($target['fleets_id'] > 0 ? "Flotte " . $target['fname'] : $target['name']) . " in Sektor " . $data['cx'] . "|" . $data['cy'] . " abzufangen", 3);
		}
		return "<b>Flottenbefehl \"Abfangkurs setzen\" wurde ausgef�hrt</b>" . $wmsg . $msg . $ramsg;
	}

	function bussard($good, $count)
	{
		$result = $this->db->query("SELECT id,name FROM stu_ships WHERE fleets_id=" . $this->s->fleets_id . " AND still=0 AND eps>0 LIMIT 25");
		while ($data = mysql_fetch_assoc($result)) {
			$ret = $this->s->bussard($data['id'], $good, $count);
			if ($ret != "") {
				if ($msg) $msg .= "<br>";
				$msg .= $data['name'] . ": " . $ret;
			}
		}
		return $msg;
	}

	function collect($count)
	{
		$result = $this->db->query("SELECT id,name FROM stu_ships WHERE fleets_id=" . $this->s->fleets_id . " AND eps>0 LIMIT 25");
		while ($data = mysql_fetch_assoc($result)) {
			$ret = $this->s->collect($data['id'], $count);
			if ($ret != "") {
				if ($msg) $msg .= "<br>";
				$msg .= $data['name'] . ": " . $ret;
			}
		}
		return $msg;
	}

	function loadwarpcore()
	{
		$result = $this->db->query("SELECT a.id,a.name,a.warpcore,a.crew,a.min_crew,b.is_shuttle,c.wkkap FROM stu_ships as a LEFT JOIN stu_rumps as b ON b.rumps_id=a.rumps_id LEFT JOIN stu_ships_buildplans as c ON c.plans_id=a.plans_id WHERE a.still=0 AND a.fleets_id=" . $this->s->fleets_id . " AND b.is_shuttle='0' AND c.wkkap>0 LIMIT 25");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			$return = shipexception(array("crew" => $data['min_crew']), $data);
			if ($return['code'] == 1) {
				$lm .= $data['name'] . ": " . $return['msg'] . "<br />";
				continue;
			}
			if ($data['is_shuttle'] == 1) {
				$lm .= $data['name'] . ": Warpkern aufladen ist auf Shuttles nicht m�glich<br />";
				continue;
			}
			if ($this->s->checksubsystem(11, $data['id']) == 1) {
				$lm .= $data['name'] . ": Der Warpkern kann nicht geladen werden (Grund: Reparatur am Warpkern wurde noch nicht abgeschlossen)<br />";
				continue;
			}
			if ($data['wkkap'] == $data['warpcore']) {
				$lm .= $data['name'] . ": Der Warpkern ist bereits vollst�ndig geladen<br />";
				continue;
			}
			$dil = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $data['id'] . " AND goods_id=8 LIMIT 1", 1);
			if ($dil == 0) {
				$lm .= $data['name'] . ": Zum Laden wird mindestens 1 Dilithium ben�tigt<br />";
				continue;
			}
			$c = $dil;
			$deut = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $data['id'] . " AND goods_id=5 LIMIT 1", 1);
			if ($deut < 2) {
				$lm .= $data['name'] . ": Zum Laden werden mindestens 2 Deuterium ben�tigt<br />";
				continue;
			}
			if (floor($deut / 2) < $c) $c = floor($deut / 2);
			$am = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $data['id'] . " AND goods_id=6 LIMIT 1", 1);
			if ($am < 2) {
				$lm .= $data['name'] . ": Zum Laden werden mindestens 2 Antimaterie ben�tigt<br />";
				continue;
			}
			if (floor($am / 2) < $c) $c = floor($am / 2);
			$load = $c * 60;
			if ($load > $data['wkkap'] - $data['warpcore']) {
				$load = $data['wkkap'] - $data['warpcore'];
				$c = ceil($load / 40);
			}
			$this->db->query("UPDATE stu_ships SET warpcore=warpcore+" . $load . " WHERE id=" . $data['id'] . " LIMIT 1");
			$this->s->lowerstorage($data['id'], 8, $c);
			$this->s->lowerstorage($data['id'], 5, $c * 2);
			$this->s->lowerstorage($data['id'], 6, $c * 2);
			$lm .= $data['name'] . ": Der Warpkern wurde um " . $load . " Einheiten geladen - Status: " . ($data['warpcore'] + $load) . "<br />";
		}
		return $lm;
	}

	function addtorkn()
	{
		$this->db->query("UPDATE stu_ships SET is_rkn=" . $this->sess['race'] . " WHERE fleets_id=" . $this->s->fleets_id . " LIMIT 25");
		return "Flotte wurde dem RPG hinzugef�gt";
	}

	function endblockade()
	{
		$blockade = $this->db->query("SELECT * FROM stu_colonies_actions WHERE value =" . $this->s->fleets_id . " AND (var='fdef' OR var='fblock' OR var='fattack')", 4);
		if ($blockade != 0) {
			if ($blockade['var'] == "fdef") $blocktext = "Verteidigung";
			elseif ($blockade['var'] == "fblock") $blocktext = "Blockade";
			else $blocktext = "Angriff";
			$blocktext .= " wurde abgebrochen.<br>";
			$this->db->query("DELETE FROM stu_colonies_actions WHERE value =" . $data['fleets_id'] . " AND var != 'db' LIMIT 1");
		} else return;
		return $blocktext;
	}

	function startblockade($mode = 0, $fleetid, $colid)
	{
		return "deaktiviert";
		//if (($this->uid != 11)) return "N�, das geht noch nicht.";
		$cloak = $this->db->query("SELECT * FROM stu_ships WHERE cloak = '1' AND fleets_id = " . $fleetid . "", 3);
		if ($cloak != 0) return "Aktion kann nur gestartet werden, wenn die gesamte Flotte ungetarnt ist.";
		$tar = $this->db->query("SELECT a.id,a.name,a.user_id,a.sx,a.sy,a.systems_id,a.schilde_status,b.vac_active,b.level,a.beamblock FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=" . $colid, 4);
		if ($tar[vac_active] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";



		$blockade = $this->db->query("SELECT * FROM stu_colonies_actions WHERE colonies_id=" . $colid . " AND value !=" . $fleetid . " AND (var='fdef' OR var='fblock' OR var='fattack')", 4);
		if ($blockade == 0) {
			$u = $this->db->query("SELECT user_id,name FROM stu_colonies WHERE id=" . $colid . "", 4);
			if ($mode == 0) {
				$blocktext = "Verteidigung";
				$fm = "fdef";
				$bpm = "Eine Verteidigung der Kolonie " . stripslashes($u[name]) . " wurde begonnen!";
			} elseif ($mode == 1) {
				$blocktext = "Blockade";
				$fm = "fblock";
				$bpm = "Eine Blockade der Kolonie " . stripslashes($u[name]) . " wurde begonnen!";
			} elseif ($mode == 2) {
				$blocktext = "Angriff";
				$fm = "fattack";
				$bpm = "Ein Angriff auf die Kolonie " . stripslashes($u[name]) . " wurde begonnen!";
			} elseif ($mode == 3) {
				$blocktext = "Angriff";
				$fm = "fattack";
				$bpm = "Ein Angriff auf die Kolonie " . stripslashes($u[name]) . " wurde begonnen!";
			} elseif ($mode == 4) {
				$blocktext = "Angriff";
				$fm = "fattack";
				$bpm = "Ein Angriff auf die Kolonie " . stripslashes($u[name]) . " wurde begonnen!";
			} else {
				$blocktext = "Angriff";
				$fm = "fattack";
				$bpm = "Ein Angriff auf die Kolonie " . stripslashes($u[name]) . " wurde begonnen!";
			}
			$blocktext .= " begonnen.<br>";
			$this->db->query("DELETE FROM stu_colonies_actions WHERE value =" . $fleetid . "");
			$this->db->query("INSERT INTO stu_colonies_actions (colonies_id,var,value,value2,attackmode) VALUES ('" . $colid . "','" . $fm . "','" . $fleetid . "','" . (time() + 1800) . "','" . $mode . "')", 5);
			$this->sendpm(1, $u[user_id], $bpm, 4, 0);
			return $blocktext;
		} else return "Aktion nicht m�glich. Diese Kolonie ist bereits belegt.<br>";
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

	function getfleetstatusbars($fleetid = 0)
	{
		if ($fleetid == 0) return;
		$result = $this->db->query("SELECT a.*,b.max_torps as mt FROM stu_ships as a LEFT OUTER JOIN stu_ships_buildplans as b on a.plans_id = b.plans_id WHERE a.fleets_id=" . $fleetid . " LIMIT 25");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			if ($data[torp_type] != 0) {
				$a = $this->db->query("SELECT a.count FROM stu_ships_storage as a LEFT outer JOIN stu_torpedo_types as b ON a.goods_id=b.goods_id WHERE b.torp_type = " . $data[torp_type] . " AND a.ships_id = " . $data[id] . "", 4);
				$mint = $a;
				$maxt = $data[mt];
			} else {
				$mint = 0;
				$maxt = 1;
			}
			$ships .= "<tr><td width=240><img src=" . $this->gfx . "/ships/" . $data[rumps_id] . ".gif border=0> " . stripslashes($data[name]) . "</td>
			<td width=80>E: " . renderepsstatusbar($data[eps], $data[max_eps]) . "</td>
			<td width=80>H: " . renderhuellstatusbar($data[huelle], $data[max_huelle]) . "</td>
			<td width=80>S: " . rendershieldstatusbar($data[schilde_aktiv], $data[schilde], $data[max_schilde]) . "</td>
			<td width=80>T: " . renderstatusbar($mint, $maxt, "red") . "</td></tr>";
		}

		$res = "<table cellpadding=1 cellspacing=1><th colspan=5>Flottenstatus</th>" . $ships . "</table>";
		return $res;
	}

	function dmgbuilding($colid, $field, $dmg, $crit, $user)
	{
		$tar = $this->db->query("SELECT a.*,b.category,b.name as bname,b.bev_use,b.bev_pro,b.eps as beps,b.lager,b.schilde as bschilde,c.*,d.is_moon FROM stu_colonies_fielddata as a left outer join stu_buildings as b on a.buildings_id = b.buildings_id LEFT OUTER join stu_colonies as c on a.colonies_id =  c.id LEFT OUTER JOIN stu_colonies_classes as d on d.colonies_classes_id = c.colonies_classes_id WHERE a.colonies_id = " . $colid . " AND a.field_id = " . $field . " ORDER BY RAND() LIMIT 1", 4);
		if ($tar == 0) return;
		if ($tar[is_moon]) $osize = 14;
		else $osize = 18;

		$destroyed = 0;

		if (($field > $osize) && ($tar[schilde_status] == 1)) {
			if ($dmg > $tar[schilde]) {
				$gebdmg = $tar[schilde] - $dmg;
				$res = "<br>- Schildschaden: " . ($tar[schilde] <= 0 ? "keiner" : $tar[schilde]) . " " . ($crit == 1 ? "(kritisch)" : "") . " - Schilde brechen zusammen!";
				$res .= "<br>- Schaden: " . ($gebdmg <= 0 ? "keiner" : $gebdmg) . " ";
				$tar[schilde_status] = 0;
				$tar[schilde] = 0;
				$tar[integrity] -= $gebdmg;
				if ($tar[integrity] < 0) {
					$res .= "- Das Geb�ude wurde zerst�rt!";
					$destroyed = 1;
				}
			} else {
				$res = "<br>- Schildschaden: " . ($dmg <= 0 ? "keiner" : $dmg) . " " . ($crit == 1 ? "(kritisch)" : "") . " ";
				$tar[schilde] -= $dmg;
			}
			$this->db->query("UPDATE stu_colonies set schilde_status = '" . $tar[schilde_status] . "', schilde = " . $tar[schilde] . " WHERE id = " . $colid . " LIMIT 1");
		} else {
			$res = "<br>- Schaden: " . ($dmg <= 0 ? "keiner" : $dmg) . " " . ($crit == 1 ? "(kritisch)" : "") . "";
			$tar[integrity] -= $dmg;
			if ($tar[integrity] < 0) {
				$res .= "- Das Geb�ude wurde zerst�rt!";
				$destroyed = 1;
			}
		}
		if ($destroyed) {
			if ($tar[bev_use] > 0 && $tar[aktiv] == 1) {
				$killed = $tar[bev_use];
				$tar[bev_work] -= $tar[bev_use];
			}
			if ($tar[bev_pro] > 0 && $tar[aktiv] == 1) {
				$killed += floor($tar[bev_max] / $tar[bev_pro] * $tar[bev_free]);
				$tar[bev_free] -= floor($tar[bev_max] / $tar[bev_pro] * $tar[bev_free]);
			}
			if ($killed > 0) {
				$res .= " (" . $killed . " Tote)";
				$this->db->query("UPDATE stu_user SET killed=killed+" . $killed . " WHERE id = " . $user);
			}

			if ($tar['beps'] > 0 && $tar['max_eps'] - $tar['beps'] < $tar['eps']) $tar['eps'] = $tar['max_eps'] - $tar['beps'];
			$this->db->query("UPDATE stu_colonies SET bev_work=" . $tar[bev_work] . ",bev_free=" . $tar[bev_free] . ", bev_max=bev_max-" . $tar[bev_pro] . ",max_eps=max_eps-" . $tar[beps] . ",max_storage=max_storage-" . $tar[lager] . ",max_schilde=max_schilde-" . $tar['bschilde'] . ($tar['schilde'] > $tar['max_schilde'] - $tar['bschilde'] ? ",schilde=" . ($tar['max_schilde'] - $tar['bschilde']) : "") . " WHERE id=" . $colid . " LIMIT 1");

			if ($tar[buildings_id] == 19) {
				$orbit = $this->db->query("SELECT SUM(b.bev_pro) as bp, SUM(b.bev_use) as bu FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=" . $colid . " AND a.field_id<=" . $osize, 4);
				$this->db->query("UPDATE stu_colonies SET bev_free=bev_free+" . (!$orbit[bu] ? 0 : $orbit[bu]) . ",bev_work=bev_work-" . (!$orbit[bu] ? 0 : $orbit[bu]) . ",bev_max=bev_max-" . (!$orbit[bp] ? 0 : $orbit[bp]) . " WHERE id=" . $colid);
				$this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE aktiv=1 AND buildings_id < 400 AND colonies_id=" . $colid . " AND field_id<=" . $osize);
			}
			if (($tar[buildings_id] == 401) || ($tar[buildings_id] == 411) || ($tar[buildings_id] == 421)) {
				$this->db->query("UPDATE stu_colonies SET beamblock='0' WHERE id=" . $colid);
			}
			if ($tar[buildings_id] == 46) {
				$ground = $this->db->query("SELECT SUM(b.bev_pro) as bp, SUM(b.bev_use) as bu FROM stu_colonies_fielddata as a LEFT JOIN stu_buildings as b USING(buildings_id) WHERE a.aktiv=1 AND a.colonies_id=" . $colid . " AND a.field_id>72", 4);
				$this->db->query("UPDATE stu_colonies SET bev_free=bev_free+" . (!$ground[bu] ? 0 : $ground[bu]) . ",bev_work=bev_work-" . (!$ground[bu] ? 0 : $ground[bu]) . ",bev_max=bev_max-" . (!$ground[bp] ? 0 : $ground[bp]) . " WHERE id=" . $colid);
				$this->db->query("UPDATE stu_colonies_fielddata SET aktiv=0 WHERE aktiv=1 AND colonies_id=" . $colid . " AND field_id>72");
				$this->db->query("UPDATE stu_colonies_fielddata SET buildings_id=0,aktiv=0,integrity=0 WHERE buildings_id=47 AND colonies_id=" . $colid);
			}
			if ($tar['buildings_id'] == 300 || ($tar['buildings_id'] > 301 && $tar['buildings_id'] < 307) || $tar['buildings_id'] == 313) {
				$result = $this->db->query("SELECT ships_id FROM stu_colonies_maintainance WHERE colonies_id=" . $colid);
				while ($dat = mysql_fetch_assoc($result)) $this->db->query("UPDATE stu_ships SET maintain=0 WHERE id=" . $dat['ships_id'] . " LIMIT 1");
				$this->db->query("DELETE FROM stu_colonies_maintainance WHERE colonies_id=" . $colid);
			}


			$this->db->query("UPDATE stu_colonies_fielddata set integrity = '0',buildings_id = '0', aktiv = '0' WHERE colonies_id = " . $colid . " AND field_id = " . $field . " LIMIT 1");
		} else {

			$this->db->query("UPDATE stu_colonies_fielddata set integrity = '" . $tar[integrity] . "' WHERE colonies_id = " . $colid . " AND field_id = " . $field . " LIMIT 1");
		}

		return $res;
	}

	function attackcolony($fleetId, $coloId, $category)
	{

		$col = $this->db->query("SELECT a.id,a.name,a.colonies_classes_id,a.user_id,a.eps,a.max_schilde,a.schilde,a.schilde_status,a.max_eps,a.bev_free,b.is_moon,b.atmosphere,c.vac_active,c.race FROM stu_colonies as a LEFT JOIN stu_colonies_classes as b USING(colonies_classes_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=" . $coloId, 4);
		// if ($col[vac_active] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($col[is_moon] != 1) {
			$maxfield = 72;
			$osize = 18;
		} else {
			$maxfield = 49;
			$osize = 14;
		}

		$hasdefense = true;
		$allbreak = false;

		$msg = "<b>Angriff auf Kolonie " . $col[name] . "</b><br>";

		$pdef = 3 * $this->db->query("SELECT * FROM stu_colonies_fielddata WHERE (buildings_id = 407 OR buildings_id = 417 OR buildings_id = 427) AND colonies_id=" . $coloId, 3);

		$result = $this->db->query("SELECT a.*,b.treffer,b.m6,c.m6c,d.name as wname, d.varianz, d.pulse, d.critical, e.torp_fire_amount as tfa FROM stu_ships as a left outer join stu_ships_buildplans as b on a.plans_id = b.plans_id LEFT OUTER JOIN stu_rumps as c on a.rumps_id = c.rumps_id LEFT OUTER JOIN stu_weapons as d on b.m6=d.module_id LEFT OUTER JOIN stu_modules as e on e.module_id = b.m10 WHERE a.fleets_id=" . $fleetId . " ORDER BY RAND()");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			if ($allbreak) break;
			if ($data[wea_phaser]) {
				$ro = ($data['m6c'] < 4 ? 1 : $data['m6c'] - 2);
				$shipowner = $data['user_id'];
				for ($i = 0; $i < $ro; $i++) {
					$tar = 0;
					if ($data[eps] < 1) continue;
					$varfac = rand(100 - $data[varianz], 100 + $data[varianz]) / 100;
					$dmg = round($varfac * $data[phaser] * $data[treffer] / 100, 1);
					$c = rand(1, 100);
					$crit = 0;
					if ($c <= $data[critical]) {
						$dmg = $dmg * 2;
						$crit = 1;
					}

					if ($hasdefense) {
						$tar = $this->db->query("SELECT a.*,b.category,b.name FROM stu_colonies_fielddata as a left outer join stu_buildings as b on a.buildings_id = b.buildings_id WHERE b.category=1 AND a.colonies_id = " . $coloId . " AND a.field_id < " . $maxfield . " ORDER BY RAND() LIMIT 1", 4);
						if ($tar == 0) {
							$hasdefense = false;
							$tar = $this->db->query("SELECT a.*,b.category,b.name FROM stu_colonies_fielddata as a left outer join stu_buildings as b on a.buildings_id = b.buildings_id WHERE b.category<=" . $category . " AND a.colonies_id = " . $coloId . " AND a.field_id < " . $maxfield . " ORDER BY RAND() LIMIT 1", 4);
							if ($tar == 0) {
								$allbreak = true;
								break;
							} else {
								if ($tar[field_id] > $osize) {
									$dmg -= $col[atmosphere];
									if ($dmg < 0) $dmg = 0;
								}
								$data[eps]--;
								$bla[event] = 1;
								$msg .= "<br>" . $data[name] . " feuert auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")" . $this->dmgbuilding($coloId, $tar[field_id], $dmg, $crit, $data[user_id]) . "";
							}
						} else {
							if ($tar[field_id] > $osize) {
								$dmg -= $col[atmosphere];
								if ($dmg < 0) $dmg = 0;
							}
							$data[eps]--;
							$bla[event] = 1;
							$msg .= "<br>" . $data[name] . " feuert auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")" . $this->dmgbuilding($coloId, $tar[field_id], $dmg, $crit, $data[user_id]) . "";
						}
					} else {
						$tar = $this->db->query("SELECT a.*,b.category,b.name FROM stu_colonies_fielddata as a left outer join stu_buildings as b on a.buildings_id = b.buildings_id WHERE b.category<=" . $category . " AND a.colonies_id = " . $coloId . " AND a.field_id < " . $maxfield . " ORDER BY RAND() LIMIT 1", 4);
						if ($tar == 0) {
							$allbreak = true;
							break;
						} else {
							if ($tar[field_id] > $osize) {
								$dmg -= $col[atmosphere];
								if ($dmg < 0) $dmg = 0;
							}
							$data[eps]--;
							$bla[event] = 1;
							$msg .= "<br>" . $data[name] . " feuert auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")" . $this->dmgbuilding($coloId, $tar[field_id], $dmg, $crit, $data[user_id]) . "";
						}
					}
				}
				$this->db->query("UPDATE stu_ships set eps = " . $data[eps] . " WHERE id = " . $data[id] . " LIMIT 1");
			}
			if ($allbreak) break;
			if (($data[wea_torp]) && ($data[torp_type] != 0)) {

				if ($data[eps] < 1) continue;
				$tp = $this->db->query("SELECT * FROM stu_torpedo_types WHERE torp_type = " . $data[torp_type] . " LIMIT 1", 4);
				if ($tp == 0) {
					$this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=" . $data[id] . " LIMIT 1");
				}
				if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $data[id] . " AND goods_id=" . $tp[goods_id], 1) < 1) continue;


				for ($i = 0; $i < $data[tfa]; $i++) {

					$tar = 0;
					if ($data[eps] < 1) break;
					if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $data[id] . " AND goods_id=" . $tp[goods_id], 1) < 1) break;
					$varfac = rand(100 - $tp[varianz], 100 + $tp[varianz]) / 100;
					$dmg = round($varfac * $tp[damage], 1);
					$c = rand(1, 100);
					$crit = 0;
					if ($c <= $tp[critical]) {
						$dmg = $dmg * 2;
						$crit = 1;
					}
					$this->lowertorpedo($data[id], $data[torp_type]);

					if ($hasdefense) {
						$tar = $this->db->query("SELECT a.*,b.category,b.name FROM stu_colonies_fielddata as a left outer join stu_buildings as b on a.buildings_id = b.buildings_id WHERE b.category=1 AND a.colonies_id = " . $coloId . " AND a.field_id < " . $maxfield . " ORDER BY RAND() LIMIT 1", 4);
						if ($tar == 0) {
							$hasdefense = false;
							$tar = $this->db->query("SELECT a.*,b.category,b.name FROM stu_colonies_fielddata as a left outer join stu_buildings as b on a.buildings_id = b.buildings_id WHERE b.category<=" . $category . " AND a.colonies_id = " . $coloId . " AND a.field_id < " . $maxfield . " ORDER BY RAND() LIMIT 1", 4);
							if ($tar == 0) {
								$allbreak = true;
								break;
							} else {
								$data[eps]--;
								$bla[event] = 1;
								if ($pdef > 0) {
									$msg .= "<br>" . $data[name] . " feuert " . $tp[name] . " auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")<br>- Der Torpedo wurde von der Punktverteidigung abgefangen.";
									$pdef--;
								} else $msg .= "<br>" . $data[name] . " feuert " . $tp[name] . " auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")" . $this->dmgbuilding($coloId, $tar[field_id], $dmg, $crit, $data[user_id]) . "";
							}
						} else {
							$data[eps]--;
							$bla[event] = 1;
							if ($pdef > 0) {
								$msg .= "<br>" . $data[name] . " feuert " . $tp[name] . " auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")<br>- Der Torpedo wurde von der Punktverteidigung abgefangen.";
								$pdef--;
							} else $msg .= "<br>" . $data[name] . " feuert " . $tp[name] . " auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")" . $this->dmgbuilding($coloId, $tar[field_id], $dmg, $crit, $data[user_id]) . "";
						}
					} else {
						$tar = $this->db->query("SELECT a.*,b.category,b.name FROM stu_colonies_fielddata as a left outer join stu_buildings as b on a.buildings_id = b.buildings_id WHERE b.category<=" . $category . " AND a.colonies_id = " . $coloId . " AND a.field_id < " . $maxfield . " ORDER BY RAND() LIMIT 1", 4);
						if ($tar == 0) {
							$allbreak = true;
							break;
						} else {
							$data[eps]--;
							$bla[event] = 1;
							if ($pdef > 0) {
								$msg .= "<br>" . $data[name] . " feuert " . $tp[name] . " auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")<br>- Der Torpedo wurde von der Punktverteidigung abgefangen.";
								$pdef--;
							} else $msg .= "<br>" . $data[name] . " feuert " . $tp[name] . " auf: " . $tar[name] . " (Feld " . $tar[field_id] . ")" . $this->dmgbuilding($coloId, $tar[field_id], $dmg, $crit, $data[user_id]) . "";
						}
					}
				}
			}
		}

		$allbreak = false;


		$bstring = "(a.buildings_id = 402 OR a.buildings_id = 412 OR a.buildings_id = 422 OR a.buildings_id = 404 OR a.buildings_id = 414 OR a.buildings_id = 424 OR a.buildings_id = 405 OR a.buildings_id = 415 OR a.buildings_id = 425  OR a.buildings_id = 406 OR a.buildings_id = 416 OR a.buildings_id = 426)";
		$result = $this->db->query("SELECT a.*,b.name FROM stu_colonies_fielddata as a LEFT OUTER JOIN stu_buildings as b on a.buildings_id = b.buildings_id WHERE a.colonies_id = " . $coloId . " AND a.aktiv = 1 AND " . $bstring . " ORDER BY RAND()");
		if (mysql_num_rows($result) == 0) $msg .= "<br><br><b>Keine Gegenwehr</b><br>";
		else $msg .= "<br><br><b>Gegenwehr: </b><br>";
		while ($data = mysql_fetch_assoc($result)) {
			if ($col[eps] < 1) break;
			if ($allbreak) break;
			$wp = "";
			$wp = $this->getWeaponDeviceData($data[buildings_id]);
			if (!is_array($wp)) continue;

			if ($wp[goods_id] > 0 && $this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=" . $wp[goods_id] . " AND colonies_id=" . $coloId, 1) == 0) continue;

			for ($i = 0; $i < $wp[rounds]; $i++) {
				if ($col[eps] < 1) break;
				if ($wp[goods_id] > 0 && $this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=" . $wp[goods_id] . " AND colonies_id=" . $coloId, 1) == 0) break;
				$destroyed = 0;
				$tar = $this->db->query("SELECT a.*,b.evade,b.m1 FROM stu_ships as a LEFT OUTER JOIN stu_ships_buildplans as b on a.plans_id = b.plans_id WHERE a.fleets_id = " . $fleetId . " ORDER BY RAND() LIMIT 1", 4);
				if ($tar != 0) {
					if ($wp[goods_id] > 0) $hit = $wp[hitchance] - $tar[evade];
					else $hit = $wp[hitchance];
					if ($hit < 5) $hit = 5;

					$col[eps]--;
					if ($wp[goods_id] != 0) {
						$result2 = $this->db->query("UPDATE stu_colonies_storage SET count=count-1 WHERE colonies_id=" . $coloId . " AND goods_id=" . $wp[goods_id] . " AND count>1 LIMIT 1", 6);
						if ($result2 == 0) $this->db->query("DELETE FROM stu_colonies_storage WHERE colonies_id=" . $coloId . " AND goods_id=" . $wp[goods_id] . " LIMIT 1");
					}
					if (rand(1, 100) <= $hit) {
						$crit = 0;
						if (rand(1, 100) <= $wp[critical]) {
							$crit = 1;
							$dmg = $wp[dmg] * 2;
						} else $dmg = $wp[dmg];
						$varfac = rand(100 - $wp[varianz], 100 + $wp[varianz]) / 100;
						$dmg = round($varfac * $dmg, 1);
						$msg .= "<br> " . $data[name] . " (Feld " . $data[field_id] . ") feuert auf: " . $tar[name] . "";
						$bla[event] = 1;
						if (($tar[schilde_status] == 1) && ($tar[schilde] > 0)) {
							if ($dmg > $tar[schilde]) {
								$hdmg = $dmg - $tar[schilde];
								$msg .= "<br>- Schildschaden: " . $tar[schilde] . " " . ($crit == 1 ? "(kritisch)" : "") . " - Schilde brechen zusammen!";
								$msg .= "<br>- H�llenschaden: " . $hdmg . "";

								if ($hdmg > $tar[huelle]) {
									$destroyed = 1;
									$msg .= " - Das Schiff wurde zerst�rt!";
								}
								$tar[schilde] = 0;
								$tar[schilde_status] = 0;
								$tar[huelle] -= $hdmg;
							} else {
								$tar[schilde] -= $dmg;
								$msg .= "<br>- Schildschaden: " . $dmg . " " . ($crit == 1 ? "(kritisch)" : "") . "";
							}
						} else {
							$msg .= "<br>- H�llenschaden: " . $dmg . " " . ($crit == 1 ? "(kritisch)" : "") . "";
							if ($dmg > $tar[huelle]) {
								$destroyed = 1;
								$msg .= " - Das Schiff wurde zerst�rt!";
							} else $tar[huelle] -= $dmg;
						}
					} else {
						$msg .= "<br> " . $data[name] . " (Feld " . $data[field_id] . ") hat ihr Ziel verfehlt.";
					}
					if ($destroyed) $this->ftrumfield(array("id" => $tar[id], "fleets_id" => $tar[fleets_id], "systems_id" => $tar[systems_id], "sx" => $tar[sx], "sy" => $tar[sy], "cx" => $tar[cx], "cy" => $tar[cy], "rumps_id" => $tar[rumps_id], "name" => $tar[name], "max_huelle" => $tar[max_huelle], "fleets_id" => $tar[fleets_id], "is_shuttle" => $tar[is_shuttle], "rname" => $tar[cname]), "Verteidigungsplattform (" . $data['name'] . ")", 2);
					else {
						$this->db->query("UPDATE stu_ships set huelle = " . $tar[huelle] . ",schilde=" . $tar[schilde] . ",schilde_status=" . $tar[schilde_status] . " WHERE id = " . $tar[id] . "  LIMIT 1");
					}
				} else {
					$msg .= "<br><br>Angreifende Flotte wurde vernichtet!";
					$allbreak = true;
					break;
				}
			}
		}
		$this->db->query("UPDATE stu_colonies SET eps=" . $col[eps] . " where id = " . $col[id] . " LIMIT 1");
		$bla[msg] = $msg;
		return $bla;
	}



	function getWeaponDeviceData($id)
	{
		global $db;
		switch ($id) {
			case 404:
			case 414:
			case 424:
				$data = $db->query("SELECT name,goods_id,damage as dmg,varianz,critical,hitchance FROM stu_torpedo_types WHERE torp_type=2", 4);
				$data['rounds'] = 2;
				return $data;
			case 405:
			case 415:
			case 425:
				$data = $db->query("SELECT name,goods_id,damage as dmg,varianz,critical,hitchance FROM stu_torpedo_types WHERE torp_type=6", 4);
				$data['rounds'] = 2;
				return $data;
			case 406:
			case 416:
			case 426:
				$data = $db->query("SELECT name,goods_id,damage as dmg,varianz,critical,hitchance FROM stu_torpedo_types WHERE torp_type=4", 4);
				$data['rounds'] = 2;
				return $data;
			case 402:
			case 412:
			case 422:
				return array("dmg" => 12, "rounds" => 2, "varianz" => 20, "hitchance" => 95, "critical" => 5, "type" => 9);
		}
	}

	function lowerstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count-" . $count . " WHERE ships_id=" . $id . " AND goods_id=" . $good . " AND count>" . $count . " LIMIT 1", 6);
		if ($result == 0) {
			if ($good >= 80 && $good < 100) $this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=" . $id . " LIMIT 1");
			$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=" . $id . " AND goods_id=" . $good . " LIMIT 1");
		}
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


	function sendpm($sender, $recipient, $text, $type, $rpl = 0)
	{
		global $global_path;
		include_once($global_path . "/inc/inputfilter.inc.php");
		$filter = new InputFilter(array("font", "b", "i", "br"), array("color"), 0, 0);
		$text = $filter->process($text);
		$this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('" . $sender . "','" . $recipient . "','" . addslashes($text) . "','" . $type . "',NOW())");
		if ($rpl > 0 && check_int($rpl)) $this->db->query("UPDATE stu_pms SET replied='1' WHERE recip_user=" . $this->uid . " AND id=" . $rpl . " LIMIT 1");
		if ($type == 1) {
			$dat = $this->db->query("SELECT user,email_not,email FROM stu_user WHERE id=" . $recipient . " LIMIT 1", 4);
			if ($dat['email_not'] != 1) return;
			mail($dat['email'], "Neue Private Nachricht", "Hallo " . stripslashes($dat['user']) . ". Es ist eine neue private Nachricht von " . stripslashes($this->db->query("SELECT user FROM stu_user WHERE id=" . $sender, 1)) . " f�r Dich eingetroffen<br><br>" . $text . "", "From: Star Trek Universe <automail@changeme.de>
Content-Type: text/html");
		}
	}





	function attackstation($fleetId, $statId)
	{

		//if (($this->uid != 102)) return "Woah. Geht nich.";

		$sta = $this->db->query("SELECT a.*,c.vac_active,c.race FROM stu_stations as a LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=" . $statId, 4);

		$allbreak = false;

		$msg = "<b>Angriff auf Station " . $sta[name] . "</b><br>";

		$result = $this->db->query("SELECT a.*,b.treffer,b.m6,c.m6c,d.name as wname, d.varianz, d.pulse, d.critical, d.wtype, e.torp_fire_amount as tfa FROM stu_ships as a left outer join stu_ships_buildplans as b on a.plans_id = b.plans_id LEFT OUTER JOIN stu_rumps as c on a.rumps_id = c.rumps_id LEFT OUTER JOIN stu_weapons as d on b.m6=d.module_id LEFT OUTER JOIN stu_modules as e on e.module_id = b.m10 WHERE a.fleets_id=" . $fleetId . " ORDER BY RAND()");
		if (mysql_num_rows($result) == 0) return;
		while ($data = mysql_fetch_assoc($result)) {
			if ($destroyed == 1) break;
			if ($data[wea_phaser]) {
				$ro = ($data['m6c'] < 4 ? 1 : $data['m6c'] - 2);
				$shipowner = $data['user_id'];
				for ($i = 0; $i < $ro; $i++) {
					if ($data[eps] < 1) continue;
					$varfac = rand(100 - $data[varianz], 100 + $data[varianz]) / 100;
					$dmg = round($varfac * $data[phaser], 1);
					$h = rand(1, 100);
					$c = rand(1, 100);

					if ($sta[schilde_status] == 1) {
						if ($data[wtype] == 1) $redu = $this->db->query("SELECT SUM(b.schildredu) FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.stations_id = " . $sta[id] . " AND b.schildtyp = 1 AND a.aktiv=1", 1);
						if ($data[wtype] == 2) $redu = $this->db->query("SELECT SUM(b.schildredu) FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.stations_id = " . $sta[id] . " AND b.schildtyp = 3 AND a.aktiv=1", 1);
						if ($data[wtype] == 4) $redu = $this->db->query("SELECT SUM(b.schildredu) FROM stu_stations_fielddata as a LEFT JOIN stu_station_components as b on a.component_id = b.component_id WHERE a.stations_id = " . $sta[id] . " AND b.schildtyp = 2 AND a.aktiv=1", 1);
						$dmg = max($dmg - $redu, 0);
					}

					$crit = 0;
					if ($c <= $data[critical]) {
						$dmg = $dmg * 2;
						$crit = 1;
					}

					$schilddmg = 0;
					$hulldmg = 0;
					$collapse = "";
					$msg .= "<br>" . $data[name] . " feuert auf die " . $sta[name] . " ";
					if ($h <= $data[treffer]) {
						if ($sta[schilde_status] == 1) {
							$schilddmg = min($dmg, $sta[schilde]);
							if ($schilddmg != $dmg) {
								$collapse = "<br>Die Schilde brechen zusammen! ";
								$hulldmg = $dmg - $schilddmg;
								$sta[schilde_status] = 0;
							}
						} else $hulldmg = min($dmg, $sta[armor]);

						if ($hulldmg >= $sta[armor]) $destroyed = 1;
						if ($crit == 1) $crittext = "- Kritischer Treffer! ";
						else $crittext = "";
						$sta[schilde] -= $schilddmg;
						$sta[armor] -= $hulldmg;
						if ($schilddmg != 0) $msg .= $crittext . "- Schilde bei " . $sta[schilde] . "" . $collapse;
						if ($hulldmg != 0) $msg .= $crittext . "- H�lle bei " . $sta[armor] . "";
						if ($schilddmg == 0 && $hulldmg == 0) $msg .= "- kein Schaden";
					} else {
						$msg .= "- der Schuss verfehlt sein Ziel.";
					}



					$data[eps]--;
					if ($destroyed == 1) break;
				}
				if ($destroyed == 1) $msg .= "<br>" . $this->destructstation($sta[id], $data[name]);
				$this->db->query("UPDATE stu_ships set eps = " . $data[eps] . " WHERE id = " . $data[id] . " LIMIT 1");
			}
			if ($destroyed == 1) break;


			if (($data[wea_torp]) && ($data[torp_type] != 0)) {

				if ($destroyed == 1) break;
				if ($data[eps] < 1) continue;
				$tp = $this->db->query("SELECT * FROM stu_torpedo_types WHERE torp_type = " . $data[torp_type] . " LIMIT 1", 4);
				if ($tp == 0) {
					$this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=" . $data[id] . " LIMIT 1");
				}
				if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $data[id] . " AND goods_id=" . $tp[goods_id], 1) < 1) continue;


				for ($i = 0; $i < $data[tfa]; $i++) {

					if ($data[eps] < 1) break;
					if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=" . $data[id] . " AND goods_id=" . $tp[goods_id], 1) < 1) break;
					$varfac = rand(100 - $tp[varianz], 100 + $tp[varianz]) / 100;
					$dmg = round($varfac * $tp[damage], 1);
					$c = rand(1, 100);
					$crit = 0;
					if ($c <= $tp[critical]) {
						$dmg = $dmg * 2;
						$crit = 1;
					}
					$this->lowertorpedo($data[id], $data[torp_type]);




					$schilddmg = 0;
					$hulldmg = 0;
					$collapse = "";
					$msg .= "<br>" . $data[name] . " feuert einen " . $tp[name] . " auf die " . $sta[name] . " ";

					if ($sta[schilde_status] == 1) {
						$schilddmg = min($dmg, $sta[schilde]);
						if ($schilddmg != $dmg) {
							$collapse = "<br>Die Schilde brechen zusammen! ";
							$hulldmg = $dmg - $schilddmg;
							$sta[schilde_status] = 0;
						}
					} else $hulldmg = min($dmg, $sta[armor]);

					if ($hulldmg >= $sta[armor]) $destroyed = 1;
					if ($crit == 1) $crittext = "- Kritischer Treffer! ";
					else $crittext = "";
					$sta[schilde] -= $schilddmg;
					$sta[armor] -= $hulldmg;
					if ($schilddmg != 0) $msg .= $crittext . "- Schilde bei " . $sta[schilde] . "" . $collapse;
					if ($hulldmg != 0) $msg .= $crittext . "- H�lle bei " . $sta[armor] . "";
					if ($schilddmg == 0 && $hulldmg == 0) $msg .= "- kein Schaden";

					$data[eps]--;
					if ($destroyed == 1) break;
				}

				if ($destroyed == 1) $msg .= "<br>" . $this->destructstation($sta[id], $data[name]);
				$this->db->query("UPDATE stu_ships set eps = " . $data[eps] . " WHERE id = " . $data[id] . " LIMIT 1");
			}
		}
		$shipsgone = 0;
		$nodef = 2;
		if ($destroyed == 0) {
			$msg .= "<br><br><b>Gegenwehr: </b><br>";
			$this->db->query("UPDATE stu_stations set schilde=" . $sta[schilde] . ",armor=" . $sta[armor] . ",schilde_status = '" . $sta[schilde_status] . "' WHERE id = " . $sta[id] . " LIMIT 1");

			$result = $this->db->query("SELECT a.*,b.*,c.* FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id LEFT OUTER JOIN stu_weapons as c on b.weapon = c.module_id WHERE a.stations_id = " . $statId . " AND a.aktiv = 1 AND b.weapon != 0 ORDER BY RAND()");
			if (mysql_num_rows($result) == 0) $nodef--;

			while ($data = mysql_fetch_assoc($result)) {
				if ($sta[eps] < 1) break;
				if ($shipsgone) break;

				for ($i = 0; $i < $data[attacks]; $i++) {
					if ($sta[eps] < 1) break;
					if ($shipsgone) break;
					$tar = $this->db->query("SELECT a.*,b.evade,b.m1,b.m2 FROM stu_ships as a LEFT OUTER JOIN stu_ships_buildplans as b on a.plans_id = b.plans_id WHERE a.fleets_id = " . $fleetId . " ORDER BY RAND() LIMIT 1", 4);
					if ($tar != 0) {
						$destroyed = 0;
						if ($data[varianz] == 20) $hit = 68;
						else $hit = 95;



						$redhull = $this->db->query("SELECT b.* FROM stu_modules as a left outer join stu_modules_special as b on a.special_id1 = b.special_id WHERE a.module_id = " . $tar[m1] . " ORDER BY RAND() LIMIT 1", 4);
						$redshield = $this->db->query("SELECT b.* FROM stu_modules as a left outer join stu_modules_special as b on a.special_id1 = b.special_id WHERE a.module_id = " . $tar[m2] . " ORDER BY RAND() LIMIT 1", 4);

						$sta[eps]--;

						$dmg = $data[strength] * 1.25;

						if (rand(1, 100) <= $hit) {
							$crit = 0;
							if (rand(1, 100) <= $data[critical]) {
								$crit = 1;
								$dmg = $dmg * 2;
							}

							$varfac = rand(100 - $data[varianz], 100 + $data[varianz]) / 100;
							$dmg = round($varfac * $dmg, 1);
							$msg .= "<br> " . $data[name] . " feuert auf die " . $tar[name] . " ";

							$schilddmg = 0;
							$hulldmg = 0;

							if ($tar[schilde_status] == 1) {
								$schilddmg = $dmg;
								if (($redhull[dmg_redu_shields] != 0) && ($data[wtype] == $redshield[dmg_redu_wtype])) {
									$schilddmg = $schilddmg * (100 - $redshield[dmg_redu_shields]) / 100;
									$schilddmg = round($schilddmg, 1);
								}
								$schilddmg = min($schilddmg, $tar[schilde]);
								if ($schilddmg != $dmg) {
									$collapse = "<br>Die Schilde brechen zusammen! ";
									$hulldmg = $dmg - $tar[schilde];
									if ($redhull[dmg_redu_huell] != 0) {
										$hulldmg = $hulldmg * (100 - $redhull[dmg_redu_huell]) / 100;
										$hulldmg = round($hulldmg, 1);
									}
									$tar[schilde_status] = 0;
								}
							} else {
								$hulldmg = $dmg;
								if ($redhull[dmg_redu_huell] != 0) {
									$hulldmg = $hulldmg * (100 - $redhull[dmg_redu_huell]) / 100;
									$hulldmg = round($hulldmg, 1);
								}
							}

							if ($hulldmg >= $tar[huelle]) $destroyed = 1;
							if ($crit == 1) $crittext = "- Kritischer Treffer! ";
							else $crittext = "";
							$tar[schilde] -= $schilddmg;
							$tar[huelle] -= $hulldmg;
							if ($schilddmg != 0) $msg .= $crittext . "- Schilde bei " . $tar[schilde] . "" . $collapse;
							if ($hulldmg != 0) $msg .= $crittext . "- H�lle bei " . $tar[huelle] . "";
							if ($schilddmg == 0 && $hulldmg == 0) $msg .= "- kein Schaden";
						} else {
							$msg .= "<br> " . $data[name] . " hat verfehlt";
						}
						if ($destroyed) $this->s->trumfield(array("id" => $tar[id], "fleets_id" => $tar[fleets_id], "systems_id" => $tar[systems_id], "sx" => $tar[sx], "sy" => $tar[sy], "cx" => $tar[cx], "cy" => $tar[cy], "rumps_id" => $tar[rumps_id], "name" => $tar[name], "max_huelle" => $tar[max_huelle], "fleets_id" => $tar[fleets_id], "is_shuttle" => $tar[is_shuttle], "rname" => $tar[cname]), "" . $sta['name'] . "");
						else {
							$this->db->query("UPDATE stu_ships set huelle = " . $tar[huelle] . ",schilde=" . $tar[schilde] . ",schilde_status=" . $tar[schilde_status] . " WHERE id = " . $tar[id] . "  LIMIT 1");
						}
					} else {
						$msg .= "<br><br>Angreifende Flotte wurde vernichtet!";
						$shipsgone = true;
						break;
					}
				}
			}

			$result = $this->db->query("SELECT a.*,b.*,c.* FROM stu_stations_fielddata as a LEFT OUTER JOIN stu_station_components as b on a.component_id = b.component_id LEFT OUTER JOIN stu_torpedo_types as c on a.torptype = c.torp_type WHERE a.stations_id = " . $statId . " AND a.aktiv = 1 AND a.torptype != 0 ORDER BY RAND()");
			if (mysql_num_rows($result) == 0) $nodef--;

			while ($data = mysql_fetch_assoc($result)) {
				if ($sta[eps] < 1) break;
				if ($shipsgone) break;

				for ($i = 0; $i < $data[attacks]; $i++) {
					if ($sta[eps] < 1) break;
					if ($this->db->query("SELECT count FROM stu_stations_storage WHERE stations_id=" . $sta[id] . " AND goods_id=" . $data[goods_id], 1) < 1) continue;

					if ($shipsgone) break;
					$tar = $this->db->query("SELECT a.*,b.evade,b.m1,b.m2 FROM stu_ships as a LEFT OUTER JOIN stu_ships_buildplans as b on a.plans_id = b.plans_id WHERE a.fleets_id = " . $fleetId . " ORDER BY RAND() LIMIT 1", 4);
					if ($tar != 0) {
						$destroyed = 0;
						$hit = $data[hitchance] - $tar[evade];



						$redhull = $this->db->query("SELECT b.* FROM stu_modules as a left outer join stu_modules_special as b on a.special_id1 = b.special_id WHERE a.module_id = " . $tar[m1] . " ORDER BY RAND() LIMIT 1", 4);

						$sta[eps]--;
						$this->statlowerstorage($sta[id], $data[goods_id], 1);
						$dmg = $data[damage];

						if (rand(1, 100) <= $hit) {
							$crit = 0;
							if (rand(1, 100) <= $data[critical]) {
								$crit = 1;
								$dmg = $dmg * 2;
							}

							$varfac = rand(100 - $data[varianz], 100 + $data[varianz]) / 100;
							$dmg = round($varfac * $dmg, 1);
							$msg .= "<br>" . $data[name] . " wird auf die " . $tar[name] . " abgefeuert ";

							$schilddmg = 0;
							$hulldmg = 0;

							if ($tar[schilde_status] == 1) {
								$schilddmg = $dmg;

								$schilddmg = min($schilddmg, $tar[schilde]);
								if ($schilddmg != $dmg) {
									$collapse = "<br>Die Schilde brechen zusammen! ";
									$hulldmg = $dmg - $tar[schilde];
									if ($redhull[dmg_redu_huell] != 0) {
										$hulldmg = $hulldmg * (100 - $redhull[dmg_redu_huell]) / 100;
										$hulldmg = round($hulldmg, 1);
									}
									$tar[schilde_status] = 0;
								}
							} else {
								$hulldmg = $dmg;
								if ($redhull[dmg_redu_huell] != 0) {
									$hulldmg = $hulldmg * (100 - $redhull[dmg_redu_huell]) / 100;
									$hulldmg = round($hulldmg, 1);
								}
							}

							if ($hulldmg >= $tar[huelle]) $destroyed = 1;
							if ($crit == 1) $crittext = "- Kritischer Treffer! ";
							else $crittext = "";
							$tar[schilde] -= $schilddmg;
							$tar[huelle] -= $hulldmg;
							if ($schilddmg != 0) $msg .= $crittext . "- Schilde bei " . $tar[schilde] . "" . $collapse;
							if ($hulldmg != 0) $msg .= $crittext . "- H�lle bei " . $tar[huelle] . "";
							if ($schilddmg == 0 && $hulldmg == 0) $msg .= "- kein Schaden";
						} else {
							$msg .= "<br>Ein " . $data[name] . " hat verfehlt";
						}
						if ($destroyed) $this->ftrumfield(array("id" => $tar[id], "fleets_id" => $tar[fleets_id], "systems_id" => $tar[systems_id], "sx" => $tar[sx], "sy" => $tar[sy], "cx" => $tar[cx], "cy" => $tar[cy], "rumps_id" => $tar[rumps_id], "name" => $tar[name], "max_huelle" => $tar[max_huelle], "fleets_id" => $tar[fleets_id], "is_shuttle" => $tar[is_shuttle], "rname" => $tar[cname]), "" . $sta['name'] . "", 4);
						else {
							$this->db->query("UPDATE stu_ships set huelle = " . $tar[huelle] . ",schilde=" . $tar[schilde] . ",schilde_status=" . $tar[schilde_status] . " WHERE id = " . $tar[id] . "  LIMIT 1");
						}
					} else {
						$msg .= "<br><br>Angreifende Flotte wurde vernichtet!";
						$shipsgone = true;
						break;
					}
				}
			}
			$this->db->query("UPDATE stu_stations SET eps=" . $sta[eps] . " where id = " . $sta[id] . " LIMIT 1");

			if ($nodef == 0) $msg .= "<br>Keine";
		}
		$this->sendpm($this->uid, $sta[user_id], $msg, 5, 0);
		return $msg;
	}

	function destructstation($id, $attacker)
	{
		$data = $this->db->query("SELECT a.*,b.cx,b.cy,b.name as sysname,c.name as statname FROM stu_stations as a left join stu_systems as b on a.systems_id = b.systems_id left join stu_stations_classes as c on a.stations_classes_id = c.stations_classes_id WHERE a.id=" . $id . "", 4);
		if ($data == 0) return "";

		$this->db->query("START TRANSACTION");
		$this->db->query("DELETE FROM stu_stations WHERE id=" . $id . "");
		$this->db->query("DELETE FROM stu_stations_fielddata WHERE stations_id = " . $id . "");
		$this->db->query("DELETE FROM stu_stations_storage WHERE stations_id = " . $id . "");
		$this->db->query("DELETE FROM stu_stations_buildprogress WHERE stations_id = " . $id . "");
		$this->db->query("UPDATE stu_ships SET assigned=0 WHERE assigned=" . $id . "");
		$tx = "Die " . $data[name] . " (" . $data[statname] . ") wurde in Sektor " . $data[sx] . "|" . $data[sy] . " (" . $data[sysname] . "-System) von der " . $attacker . " zerst�rt";
		$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg,coords_x,coords_y,user_id) VALUES ('" . addslashes($tx) . "',NOW(),'4','" . strip_tags(str_replace("'", "", stripslashes($tx))) . "','" . $data['cx'] . "','" . $data['cy'] . "','" . $this->uid . "')");
		$this->db->query("COMMIT");
		return "Station wurde zerst�rt.";
	}

	function statlowerstorage($id, $good, $count)
	{
		$result = $this->db->query("UPDATE stu_stations_storage SET count=count-" . $count . " WHERE stations_id=" . $id . " AND goods_id=" . $good . " AND count>" . $count . " LIMIT 1", 6);
		if ($result == 0) $this->db->query("DELETE FROM stu_stations_storage WHERE stations_id=" . $id . " AND goods_id=" . $good . " LIMIT 1");
	}

	function ftrumfield($data, $name = "", $mtype)
	{

		if ($data['fleets_id'] > 0 && $this->db->query("SELECT fleets_id FROM stu_fleets WHERE ships_id=" . $data['id'] . " LIMIT 1", 1) > 0) {
			$sc = $this->db->query("SELECT id FROM stu_ships WHERE id!=" . $data['id'] . " AND fleets_id=" . $data['fleets_id'] . " ORDER BY RAND() LIMIT 1", 1);
			if ($sc > 0) $this->db->query("UPDATE stu_fleets SET ships_id=" . $sc . " WHERE fleets_id=" . $data['fleets_id'] . " LIMIT 1");
			else {
				$this->db->query("DELETE FROM stu_fleets WHERE fleets_id=" . $data['fleets_id'] . " LIMIT 1");
				$this->db->query("DELETE FROM stu_colonies_actions WHERE value=" . $data['fleets_id'] . " LIMIT 1");
			}
		}
		$sysname = $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $data['systems_id'] . " LIMIT 1", 1);
		if (strlen($data['name']) == 0) return;
		$tx = "Die " . $data['name'] . " (" . $data['rname'] . ") wurde in Sektor " . ($data['systems_id'] > 0 ? $data['sx'] . "|" . $data['sy'] . " (" . $sysname . "-System)" : $data['cx'] . "|" . $data['cy']) . " " . ($name != "" ? "von der " . $name . " zerst�rt" : "beim Sektoreinflug zerst�rt");
		$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg,coords_x,coords_y,user_id) VALUES ('" . addslashes($tx) . "',NOW(),'" . $mtype . "','" . strip_tags(str_replace("'", "", stripslashes($tx))) . "','" . $data['cx'] . "','" . $data['cy'] . "','" . $this->uid . "')");
		if ($data['is_shuttle'] != 1) $this->db->query("UPDATE stu_ships SET user_id=1,huelle=" . ceil(($data['max_huelle'] / 100) * 15) . ",schilde=0,schilde_status=0,alvl=1,warpable=0,warpcore=0,traktor=0,traktormode=0,dock=0,crew=0,name='Tr�mmerfeld',eps=0,batt=0,nbs=0,lss=0,torp_type=0,rumps_id=8,trumps_id=8,cloak='0',fleets_id=0,warp='0',still=0,wea_phaser='0',wea_torp='0',is_rkn=0 WHERE id=" . $data['id'] . " LIMIT 1");
		else {
			$this->db->query("DELETE FROM stu_ships WHERE id=" . $data['id'] . " LIMIT 1");
			$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=" . $data['id']);
		}
		$assigned = $this->db->query("SELECT * FROM stu_stations_fielddata WHERE ship = " . $data[id] . "", 1);
		if ($assigned != 0) {
			if ($assigned[aktiv] == 1) {
				$this->db->query("UPDATE stu_stations SET bev_work = bev_work-3 WHERE id=" . $assigned['stations_id'] . " LIMIT 1");
			}
			$this->db->query("DELETE FROM stu_stations_fielddata WHERE ship=" . $data['id'] . " LIMIT 1");
		}
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
		$dat = $this->db->query("SELECT b.m1,b.m2,b.m3,b.m4,b.m5,b.m6,b.m7,b.m8,b.m9,b.m10,b.m11,c.m1c,c.m2c,c.m3c,c.m4c,c.m5c,c.m6c,c.m7c,c.m8c,c.m9c,c.m10c,c.m11c FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b ON b.plans_id=a.plans_id LEFT JOIN stu_rumps as c ON c.rumps_id=b.rumps_id WHERE a.id=" . $data['id'] . " LIMIT 1", 4);
		$i = 1;
		while ($i <= 11) {
			if ($dat['m' . $i] == 0) {
				$i++;
				continue;
			}
			$dchg = $this->db->query("SELECT demontchg FROM stu_modules WHERE module_id=" . $dat['m' . $i] . " LIMIT 1", 1);
			if ($dchg == 0 || rand(1, 100) > $dchg) {
				$i++;
				continue;
			}
			//$this->upperstorage($data['id'],$dat['m'.$i],rand(1,$dat['m'.$i.'c']));
			$i++;
		}
		if (!check_int($data['user_id'])) return;
		if ($data['user_id'] < 100) return;
		$cc = $this->db->query("SELECT COUNT(*) FROM stu_colonies WHERE user_id=" . $data['user_id'], 1);
		if ($cc == 0) {
			if ($cs = $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE rumps_id=1 AND user_id=" . $data['user_id'], 1) == 0) $this->db->query("UPDATE stu_user SET level='0' WHERE id=" . $data['user_id'] . " LIMIT 1");
		}
	}
}