<?php
class station extends qpm
{
	function __construct()
	{
		global $db,$_SESSION,$_GET,$map;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->sess = $_SESSION;
		$this->m = $map;
		$this->destroyed = 0;
		if (($_GET['p'] == "stat") && check_int($_GET['id']))
		{
			$result = $this->db->query("SELECT a.id,a.plans_id,a.fleets_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.direction,a.systems_id,a.name,a.alvl,a.warp,a.cloak,a.cloakable,a.warpcore,a.warpable,a.eps,a.max_eps,a.batt,a.max_batt,a.phaser,a.huelle,a.max_huelle,a.schilde,a.max_schilde,a.schilde_status,a.crew,a.max_crew,a.min_crew,a.lss_range,a.kss_range,a.traktor,a.traktormode,a.dock,a.nbs,a.lss,a.wea_phaser,a.wea_torp,a.torp_type,a.replikator,a.still,a.maintain,a.batt_wait,a.hud,a.is_rkn,a.cfield,a.lastmaintainance,b.name as cname,b.bussard,b.erz,b.replikator as porep,b.storage,b.probe,b.is_shuttle,b.max_shuttles,b.max_shuttle_type,b.max_cshuttle_type,b.slots,b.reaktor as creaktor,c.evade,c.wkkap,c.stellar,c.sensor_val,c.reaktor as rreaktor,c.maintaintime,c.m1,c.m2,c.m3,c.m4,c.m5,c.m6,c.m7,c.m8,c.m9,c.m10,c.m11,d.ships_id as fsf,d.name as fname FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_fleets as d ON a.fleets_id=d.fleets_id WHERE a.user_id=".$this->uid." AND a.id=".$_GET['id']." AND ".($_GET['a'] != "lansh" ? "b.slots>0" : "b.is_shuttle='1'")." LIMIT 1",4);
			if ($result == 0) 
			{
				$this->destroyed = 1;
				$this->dsships[$_GET['id']] = 1;
				return;
			}
			foreach($result as $key => $value) $this->$key = $value;
			$this->systems_id > 0 ? $this->map = $map->getfieldbyid_kss($this->sx,$this->sy,$this->systems_id) : $this->map = $map->getfieldbyid_lss($this->cx,$this->cy);
		}
		elseif (($_GET['p'] == "stat" && $_GET['s']) && !check_int($_GET['id'])) die(show_error(902));
	}

	function write_logbook()
	{
		foreach($this->logbook as $key => $value)
		{
			foreach($value as $data)
			{
				$this->db->query("INSERT INTO stu_ships_logs (ships_id,user_id,text,date,type) VALUES ('".$key."','".$data['user_id']."','".addslashes($data['text'])."',NOW(),'".$data['type']."')");
			}
		}
	}
	
	function write_private_logbook(&$text)
	{
		$this->db->query("INSERT INTO stu_ships_logs (ships_id,user_id,text,date,type) VALUES ('".$this->id."','".$this->uid."','".addslashes($text)."',NOW(),'4')");
		return "Eintrag wurde im Logbuch gespeichert";
	}

	function loadwarpcore($c)
	{
		$return = shipexception(array("crew" => $this->min_crew),$this);
		if ($return[code] == 1) return $return['msg'];
		if ($this->is_shuttle == 1) return "Warpkern aufladen ist auf Shuttles nicht möglich";
		if ($this->checksubsystem(11,$this->id) == 1) return "Der Warpkern kann nicht geladen werden (Grund: Reparatur am Warpkern wurde noch nicht abgeschlossen)";
		if ($this->wkkap == $this->warpcore) return "Der Warpkern ist bereits vollständig geladen";
		$dil = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id=8 LIMIT 1",1);
		if ($dil == 0) return "Zum Laden wird mindestens 1 Dilithium benötigt";
		$c == "max" ? $c = $dil : $c = 1;
		$deut = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id=5 LIMIT 1",1);
		if ($deut < 2) return "Zum Laden werden mindestens 2 Deuterium benötigt";
		if (floor($deut/2) < $c) $c = floor($deut/2);
		$am = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id=6 LIMIT 1",1);
		if ($am < 2) return "Zum Laden werden mindestens 2 Antimaterie benötigt";
		if (floor($am/2) < $c) $c = floor($am/2);
		$load = $c*40;
		if ($load > $this->wkkap-$this->warpcore)
		{
			$load = $this->wkkap-$this->warpcore;
			$c = ceil($load/40);
		}
		$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_ships SET warpcore=warpcore+".$load." WHERE id=".$this->id." LIMIT 1");
		$this->lowerstorage($this->id,8,$c);
		$this->lowerstorage($this->id,5,$c*2);
		$this->lowerstorage($this->id,6,$c*2);
		$this->db->query("COMMIT");
		return "Der Warpkern wurde um ".$load." Einheiten geladen - Status: ".($this->warpcore+$load);
	}

	function av($dev)
	{
		if ($this->cloakable == 0 && $dev == "cl") return;
		if ($this->porep != 1 && $dev == "re") return;
		if ($this->rumps_id == 9) return "Konstrukte können keine Systeme aktivieren";
		if ($dev == "cl")
		{
			$nm = "Tarnung";
			$e = 3;
			$cr = $this->min_crew;
			$fc = "cloak";
			if ($this->checksubsystem(9,$this->id) == 1) return "Die Tarnung kann nicht aktiviert werden (Grund: Reparatur an der Tarnung wurde noch nicht abgeschlossen)";
			if ($this->map['cloakoff'] == 1) return "Die Tarnung kann nicht aktiviert werden (Grund: ".$this->map[name].")";
			if ($this->traktormode == 1) return "Die Tarnung kann nicht aktiviert werden (Grund: Schiff hat den Traktorstrahl aktiviert)";
			if ($this->traktormode == 2) return "Die Tarnung kann nicht aktiviert werden (Grund: Schiff wird von einem Traktorstrahl gehalten)";
			if ($this->dock > 0) return "Die Tarnung kann nicht aktiviert werden (Grund: Schiff ist angedockt)";
			if ($this->plans_id != 1 && $this->uid > 100 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance,"maintaintime" => $this->maintaintime)) > rand(1,100)) return $this->damage_subsystem("foo",$this->id,9);
		}
		if ($dev == "sh")
		{
			$nm = "Schilde";
			$e = 2;
			$cr = $this->min_crew;
			$fc = "schilde_status";
			if ($this->checksubsystem(2,$this->id) == 1) return "Schilde können nicht aktiviert werden (Grund: Reparatur an den Schilden wurde noch nicht abgeschlossen)";
			if ($this->cloak == 1) return "Schilde können nicht aktiviert werden (Grund: Tarnung aktiviert)";
			if ($this->schilde == 0) return "Schilde können nicht aktiviert werden (Grund: Nicht aufgeladen)";
			if ($this->map['shieldoff'] == 1) return "Die Schilde können nicht aktiviert werden (Grund: ".$this->map[name].")";
			if ($this->map['type'] == 6 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=".$this->m2,1) == 5) return "Die Schilde können nicht aktiviert werden (Grund: ".$this->map[name].")";
			if ($this->map['type'] == 9 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=".$this->m2,1) == 7) return "Die Schilde können nicht aktiviert werden (Grund: ".$this->map[name].")";
			if ($this->map['type'] == 15 && $this->db->query("SELECT special_id1 FROM stu_modules WHERE module_id=".$this->m2,1) == 4) return "Die Schilde können nicht aktiviert werden (Grund: ".$this->map[name].")";
			if ($this->traktormode == 1) return "Die Schilde können nicht aktiviert werden (Grund: Schiff hat den Traktorstrahl aktiviert)";
			if ($this->traktormode == 2) return "Die Schilde können nicht aktiviert werden (Grund: Schiff wird von einem Traktorstrahl gehalten)";
			if ($this->dock > 0) return "Die Schilde können nicht aktiviert werden (Grund: Schiff ist angedockt)";
			if ($this->plans_id != 1 && $this->uid > 100 && $this->slots == 0 && getSystemDamageChance(array("lastmaintainance" => $this->lastmaintainance,"maintaintime" => $this->maintaintime)) > rand(1,100)) return $this->damage_subsystem("foo",$this->id,2);
		}
		if ($dev == "wp")
		{
			$nm = "Waffensystem (Strahlenwaffe)"; $e = 1; $cr = $this->min_crew; $fc = "wea_phaser";
			if ($this->phaser == 0) return "Es ist keine Strahlenwaffe auf diesem Schiff installiert";
			if ($this->checksubsystem(6,$this->id) == 1) return "Waffensystem (Strahlenwaffe) kann nicht aktiviert werden (Grund: Reparatur an dem Waffensystem wurde noch nicht abgeschlossen)";
			if ($this->cloak == 1) return "Waffensystem (Strahlenwaffe) kann nicht aktiviert werden (Grund: Tarnung aktiviert)";
		}
		if ($dev == "wt")
		{
			$nm = "Waffensystem (Torpedobänke)"; $e = 1; $cr = $this->min_crew; $fc = "wea_torp";
			if ($this->m10 == 0) return "Es sind keine Torpedobänke auf diesem Schiff installiert";
			if ($this->checksubsystem(10,$this->id) == 1) return "Waffensystem (Torpedobänke) kann nicht aktiviert werden (Grund: Reparatur an dem Waffensystem wurde noch nicht abgeschlossen)";
			if ($this->cloak == 1) return "Waffensystem (Torpedobänke) kann nicht aktiviert werden (Grund: Tarnung aktiviert)";
			if ($this->torp_type == 0) return "Waffensystem (Torpedobänke) kann nicht aktiviert werden (Grund: Keine Torpedos geladen)";
		}
		if ($dev == "lss")
		{
			$this->systems_id > 0 ? $nm = "Kurzstreckensensoren" : $nm = "Langstreckensensoren";
			$e = 1;
			$cr = ($this->rumps_id == 10 ? 0 : $this->min_crew-1); $fc = "lss";
			if ($this->checksubsystem(4,$this->id) == 1) return "Die Sensoren können nicht aktiviert werden (Grund: Reparatur an den Sensoren wurden noch nicht abgeschlossen)";
			if ($this->map['sensoroff'] == 1) return "Die Sensoren können nicht aktiviert werden (Grund: ".$this->map[name].")";
		}
		if ($dev == "nbs")
		{
			$nm = "Nahbereichssensoren";
			$e = 1;
			$cr = ($this->rumps_id == 10 ? 0 : $this->min_crew-1); $fc = "nbs";
			if ($this->checksubsystem(4,$this->id) == 1) return "Die Sensoren können nicht aktiviert werden (Grund: Reparatur an den Sensoren wurden noch nicht abgeschlossen)";
			if ($this->map['sensoroff'] == 1) return "Die Sensoren können nicht aktiviert werden (Grund: ".$this->map[name].")";
			if ($this->map['type'] == 8) return "Die Sensoren können nicht aktiviert werden (Grund: ".$this->map[name].")";
			if ($this->sess["level"] < 2) return "Die Sensoren können nicht aktiviert werden (Grund: Erst ab Level 2 möglich)";
		}
		if ($dev == "re") { $nm = "Replikator"; $cr = 1; $e = 0; $fc = "replikator"; }
		if ($this->$fc == 1) return;
		if ($this->eps < $e) return "System (".$nm.") kann nicht aktiviert werden - ".$e." Energie benötigt";
		if ($this->crew < $cr) return "System (".$nm.") kann nicht aktiviert werden - ".$cr." Crew benötigt";
		if ($dev == "sh" && $this->schilde_status > 1 && $this->schilde_status > time()) return "Die Schilde sind polarisiert (".date("d.m.Y H:i",$this->schilde_status).")";
		if ($dev == "cl") $this->db->query("UPDATE stu_ships SET schilde_status=0 WHERE schilde_status=1 AND id=".$this->id." LIMIT 1");
		if (!$fc) return;
		if ($dev == "sh" && $this->slots > 0)
		{
			$this->db->query("UPDATE stu_ships SET dock=0,maintain=0 WHERE dock=".$this->id);
			$this->db->query("DELETE FROM stu_colonies_maintainance WHERE station_id=".$this->id);
		}
		$this->db->query("UPDATE stu_ships SET ".$fc."='1',eps=eps-".$e." WHERE id=".$this->id." LIMIT 1");
		$this->eps -= $e;
		return "System (".$nm.") wurde aktiviert".$smsg;
	}

	function dv($dev)
	{
		if ($dev == "cl" && $this->cloak == 1)
		{
			$nm = "Tarnung";
			$fc = "cloak";
		}
		if ($dev == "sh" && $this->schilde_status == 1)
		{
			$nm = "Schilde";
			$fc = "schilde_status";
		}
		if ($dev == "lss" && $this->lss == 1)
		{
			$nm = ($this->systems_id == 0 ? "Langstreckensensoren" : "Kurzstreckensensoren");
			$fc = "lss";
		}
		if ($dev == "nbs" && $this->nbs == 1)
		{
			$nm = "Nahbereichssensoren";
			$fc = "nbs";
			if ($this->still > 0) $smsg = $this->stopkartographie()."<br>";
		}
		if ($dev == "wp" && $this->wea_phaser == 1) { $nm = "Waffensystem (Strahlenwaffe)"; $fc = "wea_phaser"; }
		if ($dev == "wt" && $this->wea_torp == 1) { $nm = "Waffensystem (Torpedobänke)"; $fc = "wea_torp"; }
		if ($dev == "re" && $this->replikator == 1) { $nm = "Replikator"; $fc = "replikator"; }
		if (!$fc) return;
		$this->db->query("UPDATE stu_ships SET ".$fc."='0' WHERE id=".$this->id." LIMIT 1");
		return "System (".$nm.") wurde deaktiviert".$smsg.$ramsg;
	}

	function upperstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count+".$count." WHERE ships_id=".$id." AND goods_id=".$good." LIMIT 1",6);
		if ($result == 0) $this->db->query("INSERT INTO stu_ships_storage (ships_id,goods_id,count) VALUES ('".$id."','".$good."','".$count."')");
	}

	function lowerstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count-".$count." WHERE ships_id=".$id." AND goods_id=".$good." AND count>".$count." LIMIT 1",6);
		if ($result == 0)
		{
			if ($good >=80 && $good < 100) $this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=".$id." LIMIT 1");
			$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$id." AND goods_id=".$good." LIMIT 1");
		}
	}

	function colupperstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_colonies_storage SET count=count+".$count." WHERE colonies_id=".$id." AND goods_id=".$good." LIMIT 1",6);
		if ($result == 0) $this->db->query("INSERT INTO stu_colonies_storage (colonies_id,goods_id,count) VALUES ('".$id."','".$good."','".$count."')");
	}

	function collowerstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_colonies_storage SET count=count-".$count." WHERE colonies_id=".$id." AND goods_id=".$good." AND count>".$count." LIMIT 1",6);
		if ($result == 0) $this->db->query("DELETE FROM stu_colonies_storage WHERE colonies_id=".$id." AND goods_id=".$good." LIMIT 1");
	}

	function beamto($target,$good,$count)
	{
		if ($this->id == $target) return;
		$tar = $this->db->query("SELECT a.id,a.rumps_id,a.plans_id,a.name,a.user_id,a.warp,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.schilde_status,a.dock,a.huelle,a.max_huelle,a.is_hp,b.storage,b.is_shuttle,b.max_shuttles,b.max_shuttle_type,b.max_cshuttle_type,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=".$target." LIMIT 1",4);
		if ($tar == 0 || $tar['cloak'] == 1) return;
		if ($this->rumps_id == 9) return "Konstrukte können nicht beamen";
		if ($tar['schilde_status'] == 1) return "Das Zielschiff hat die Schilde aktiviert";
		if ($tar['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($tar['warp'] == 1) return "Das Zielschiff befindet sich im Warp";
		$result = shipexception(array("schilde_status" => 0,"cloak" => 0,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($result[code] == 1)
		{
			$this->stop_trans = 1;
			return $result['msg'];
		}
		if (($this->dock == 0 || ($this->dock != $tar['dock'] && $this->dock != $tar['id'])) && ($tar['dock'] == 0 || $tar['dock'] != $this->id)) $docked = 0;
		else $docked = 1;
		if ($docked == 0)
		{
			$result = shipexception(array("nbs" => 1,"eps" => -1),$this);
			if ($result['code'] == 1)
			{
				$this->stop_trans = 1;
				return $result['msg'];
			}
		}
		if (checksector($tar) == 0) return;
		$tast = $this->getshipstoragesum($target);
		if ($tast >= $tar['storage']) return "Kein Lagerraum auf dem Zielschiff vorhanden";
		$this->crew - $this->min_crew > 3 ? $mb = 40 : $mb = 10+(($this->crew-$this->min_crew)*10);
		foreach ($good as $key => $value)
		{
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->checkgood($this->id,$value);
			if ($c == 0) continue;
			if ($this->eps == 0 && $docked == 0)
			{
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			if ($c < $count[$key]) $count[$key] = $c;
			if ($value >= 80 && $value<100)
			{
				$lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$tar['id']." AND goods_id>=80 AND goods_id<100",1);
				if ($lt != $value && $lt != 0)
				{
					$return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
					continue;
				}
				$tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON a.m10=b.module_id WHERE a.plans_id=".$tar['plans_id']." LIMIT 1",4);
				if ($tmc['max_torps'] == 0)
				{
					$return .= "Dieses Schiff kann keine Torpedos laden<br>";
					continue;
				}
				$tt = $this->db->query("SELECT torp_type,type FROM stu_torpedo_types WHERE goods_id=".$value." LIMIT 1",4);
				if ($tt['type'] > $tmc['torp_type'])
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				if (($tt['type'] == 1 || $tt[type] == 2) && $tmc[m10] != 656 && $this->db->query("SELECT user_id FROM stu_researched WHERE research_id=163 AND user_id=".$tar['user_id']." LIMIT 1",1) == 0)
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				if (($tt['type'] == 3 || $tt['type'] == 4) && $tmc['m10'] != 656 && $this->db->query("SELECT user_id FROM stu_researched WHERE research_id=164 AND user_id=".$tar['user_id']." LIMIT 1",1) == 0)
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				$tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$tar[id],1);
				if ($tc >= $tmc['max_torps'])
				{
					$return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
					continue;
				}
				if ($tmc['m10'] != 656) $this->db->query("UPDATE stu_ships SET torp_type=".$tt['torp_type']." WHERE id=".$tar['id']." LIMIT 1");
				$tc + $count[$key] > $tmc['max_torps'] ? $c = $tmc['max_torps']-$tc : $c = $count[$key]; 
			}
			elseif ($value >= 110 && $value < 190)
			{
				if ($shuttle_stop == 1) continue;
				if ($tar['max_shuttles'] == 0 || $tar['is_shuttle'] == 1)
				{
					$shuttle_stop = 1;
					$return .= "Dieses Schiff kann keine Shuttles laden<br>";
					continue;
				}
				$shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value." LIMIT 1",4);
				if ($shud['shuttle_type'] > $tar['max_shuttle_type'])
				{
					$return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
					continue;
				}
				if ($tar['max_shuttles'] <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$tar[id]." AND !ISNULL(b.shuttle_type)",1))
				{
					$shuttle_stop = 1;
					$return .= "Die Shuttlerampe ist belegt<br>";
					continue;
				}
				if ($tar['max_cshuttle_type'] <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$tar[id]." AND a.goods_id!=".$value." AND !ISNULL(b.shuttle_type)",1))
				{
					$return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
					continue;
				}
				$sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<190 AND ships_id=".$tar['id'],1);
				$sc + $count[$key] > $tar['max_shuttles'] ? $c = $tar['max_shuttles']-$sc : $c = $count[$key];
			}
			else $count[$key] > $c ? $c = $c : $c = $count[$key];
			if ($c > $tar[storage] - $tast) $c = $tar['storage'] - $tast;
			$tast += $c;
			if ($c <= 0) continue;
			if ($docked == 0)
			{
				if (ceil($c/$mb) > $this->eps)
				{
					$c = $this->eps*$mb;
					$this->eps = 0;
				}
				else $this->eps -= ceil($c/$mb);
			}
			else $mb = $c;
			$this->db->query("START TRANSACTION");
			$this->lowerstorage($this->id,$value,$c);
			$this->upperstorage($target,$value,$c);
			$this->db->query("COMMIT");
			$msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value." LIMIT 1",1)."<br>";
			$e += ceil($c/$mb);
			if ($tast >= $tar['storage']) break;
			if ($this->eps == 0 && $docked == 0) break;
		}
		if (!$e) return $return."Keine Waren zum Beamen vorhanden";
		if ($this->uid != $tar['user_id']) $this->send_pm($this->uid,$tar['user_id'],"<b>Die ".stripslashes($this->name)." beamt in Sektor ".($tar['systems_id'] > 0 ? $tar['sx']."|".$tar['sy']." (".$this->m->getsysnamebyid($tar['systems_id'])." System)" : $tar['cx']."|".$tar['cy'])." Waren zur ".stripslashes($tar['name'])."</b><br>".$msg,2);
		else $rad = "->> <a href=?p=ship&s=ss&id=".$tar[id].">Zur ".$tar['name']." wechseln</a>";
		if ($docked == 1) return "<b>Folgende Waren wurden zur ".stripslashes($tar['name'])." transportiert</b><br>".$msg.$rad;
		$this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return "<b>Es wurden folgende Waren zu der ".stripslashes($tar['name'])." gebeamt</b><br>".$msg.$return."Energieverbrauch: <b>".$e."</b><br>".$rad;
	}

	function beamfrom($target,$good,$count)
	{
		if ($this->id == $target) return;
		$tar = $this->db->query("SELECT a.id,a.rumps_id,a.plans_id,a.name,a.user_id,a.warp,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.schilde_status,a.dock,a.is_hp,a.huelle,a.max_huelle,b.storage,b.trumfield,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=".$target." LIMIT 1",4);
		if ($tar == 0 || $tar[cloak] == 1) return;
		if (checksector($tar) == 0) return;
		if ($tar['rumps_id'] == 9) return "Von Konstrukten kann nicht gebeamt werden";
		if ($tar['schilde_status'] == 1) return "Das Zielschiff hat die Schilde aktiviert";
		if ($tar['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($tar['warp'] == 1) return "Das Zielschiff befindet sich im Warp";
		$result = shipexception(array("schilde_status" => 0,"cloak" => 0,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($result['code'] == 1)
		{
			$this->stop_trans = 1;
			return $result['msg'];
		}
		if ($this->dock != $target && $tar['dock'] != $this->id && ($tar['dock'] != $this->dock || $this->dock == 0))
		{
			$result = shipexception(array("nbs" => 1,"eps" => -1),$this);
			if ($result['code'] == 1)
			{
				$this->stop_trans = 1;
				return $result['msg'];
			}
		}
		$tast = $this->getshipstoragesum($this->id);
		if ($tast >= $this->storage) return "Kein Lagerraum auf dem Schiff vorhanden";
		$this->crew - $this->min_crew > 3 ? $mb = 40 : $mb = 10+(($this->crew-$this->min_crew)*10);
		$docked = 0;
		if ($this->dock == $tar['id']) $docked = 1;
		elseif ($this->dock == $tar['dock'] && $this->dock > 0) $docked = 1;
		elseif ($this->id == $tar['dock']) $docked = 1;
		foreach ($good as $key => $value)
		{
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->checkgood($target,$value);
			if ($c == 0) continue;
			if ($this->eps == 0 && $docked == 0)
			{
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			if ($count[$key] > $c) $count[$key] = $c;
			if ($value >= 80 && $value<100)
			{
				if ($this->uid != $tar['user_id'] && $tar[is_hp] != 1) continue;
				$lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id>=80 AND goods_id<100",1);
				if ($lt != $value && $lt != 0)
				{
					$return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
					continue;
				}
				$tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON a.m10=b.module_id WHERE a.plans_id=".$this->plans_id." LIMIT 1",4);
				if ($tmc['max_torps'] == 0)
				{
					$return .= "Dieses Schiff kann keine Torpedos laden<br>";
					continue;
				}
				$tt = $this->db->query("SELECT torp_type,type FROM stu_torpedo_types WHERE goods_id=".$value." LIMIT 1",4);
				if ($tt['type'] > $tmc['torp_type'])
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				if (($tt['type'] == 1 || $tt['type'] == 2) && $tmc['m10'] != 656 && $this->db->query("SELECT user_id FROM stu_researched WHERE research_id=163 AND user_id=".$this->uid." LIMIT 1",1) == 0)
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				if (($tt['type'] == 3 || $tt['type'] == 4) && $tmc['m10'] != 656 && $this->db->query("SELECT user_id FROM stu_researched WHERE research_id=164 AND user_id=".$this->uid." LIMIT 1",1) == 0)
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				$tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$this->id." LIMIT 1",1);
				if ($tc >= $tmc['max_torps'])
				{
					$return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
					continue;
				}
				if ($tmc['m10'] != 656) $this->db->query("UPDATE stu_ships SET torp_type=".$tt['torp_type']." WHERE id=".$this->id." LIMIT 1");
				$tc + $count[$key] > $tmc['max_torps'] ? $c = $tmc['max_torps']-$tc : $c = $count[$key]; 
			}
			elseif ($value >= 110 && $value < 190)
			{
				if ($this->uid != $tar['user_id'] && $tar['is_hp'] != 1 && $tar['user_id'] != 1) continue;
				if ($shuttle_stop == 1) continue;
				if ($this->max_shuttles == 0 || $this->is_shuttle == 1)
				{
					$shuttle_stop = 1;
					$return .= "Dieses Schiff kann keine Shuttles laden<br>";
					continue;
				}
				$shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value." LIMIT 1",4);
				if ($shud[shuttle_type] > $this->max_shuttle_type)
				{
					$return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
					continue;
				}
				if ($this->max_shuttles <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND !ISNULL(b.shuttle_type)",1))
				{
					$shuttle_stop = 1;
					$return .= "Die Shuttlerampe ist belegt<br>";
					continue;
				}
				if ($this->max_cshuttle_type <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND a.goods_id!=".$value." AND !ISNULL(b.shuttle_type)",1))
				{
					$return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
					continue;
				}
				$sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<142 AND ships_id=".$this->id,1);
				$sc + $count[$key] > $this->max_shuttles ? $c = $this->max_shuttles-$sc : $c = $count[$key];
			}
			else $count[$key] > $c ? $c = $c : $c = $count[$key];
			if ($c > $this->storage - $tast) $c = $this->storage - $tast;
			if ($c <= 0) continue;
			$tast += $c;
			if ($docked == 0)
			{
				if (ceil($c/$mb) > $this->eps)
				{
					$c = $this->eps*$mb;
					$this->eps = 0;
				}
				else $this->eps -= ceil($c/$mb);
			}
			else $mb = $c;
			$this->db->query("START TRANSACTION");
			$this->lowerstorage($target,$value,$c);
			$this->upperstorage($this->id,$value,$c);
			$this->db->query("COMMIT");
			$msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value." LIMIT 1",1)."<br>";
			$e += ceil($c/$mb);
			if ($tast >= $this->storage) break;
			if ($this->eps == 0 && $docked == 0) break;
		}
		if ($tar['is_hp']) $tar['user_id'] = 3;
		if (!$msg) return $return."Keine Waren zum Beamen vorhanden";
		if ($this->uid != $tar['user_id']) $this->send_pm($this->uid,$tar['user_id'],"<b>Die ".stripslashes($this->name)." beamt in Sektor ".($tar[systems_id] > 0 ? $tar[sx]."|".$tar[sy]." (".$this->m->getsysnamebyid($tar['systems_id'])." System)" : $tar['cx']."|".$tar['cy'])." Waren von der ".stripslashes($tar['name'])."</b><br>".$msg,2);
		else $rad = "->> <a href=?p=ship&s=ss&id=".$tar[id].">Zur ".$tar['name']." wechseln</a>";
		if ($docked == 1) return "<b>Folgende Waren wurden von der ".stripslashes($tar['name'])." transportiert</b><br>".$msg.$rad;
		$this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return "<b>Es wurden folgende Waren von der ".stripslashes($tar['name'])." gebeamt</b><br>".$msg.$return."Energieverbrauch: <b>".$e."</b><br>".$rad;
	}

	function etransfer($target,$count)
	{
		if ($this->id == $target) return;
		$data = $this->db->query("SELECT a.id,a.name,a.user_id,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.schilde_status,a.warp,a.eps,a.max_eps,a.crew,a.min_crew,a.dock,b.probe,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=".$target." LIMIT 1",4);
		if ($data == 0 || checksector($data) == 0 || $data['cloak'] == 1) return;
		if ($this->is_shuttle == 1) return "Ein Shuttle kann keinen Energietransfer durchführen";
		if ($data['schilde_status'] == 1) return "Das Zielschiff hat die Schilde aktiviert";
		if ($this->rumps_id == 9) return "Vom Konstrukten kann keine Energie transferiert werden";
		if ($data['warp'] == 1) return "Das Zielschiff ist im Warp";
		if ($data['eps'] >= $data['max_eps']) return "Das EPS der ".$data['name']." ist voll".($data['user_id'] == $this->uid ? "<br>->> <a href=?p=ship&s=ss&id=".$data['id'].">Zur ".$data['name']." wechseln</a>" : "");
		if ($data['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		$result = shipexception(array("schilde_status" => 0,"eps" => -1,"cloak" => 0,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($result['code'] == 1) return $result['msg'];
		if ($this->dock != $target && $data['dock'] != $this->id)
		{
			$result = shipexception(array("nbs" => 1),$this);
			if ($result['code'] == 1) return $result['msg'];
		}
		if ($count == "max") $count = $this->eps;
		else if ($count > $this->eps) $count = $this->eps;
		if ($data['max_eps'] < $count+$data['eps']) $count = $data['max_eps']-$data['eps'];
		$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_ships SET eps=eps+".$count." WHERE id=".$target." LIMIT 1");
		$this->eps -= $count;
		$this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		$this->db->query("COMMIT");
		$msg = $count." Energie zur ".$data['name']." transferiert";
		if ($this->uid != $data['user_id']) $this->send_pm($this->uid,$data['user_id'],"<b>Die ".$this->name." hat in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->getsysnamebyid($data['systems_id'])." System)" : $data['cx']."|".$data['cy'])." ".$count." Energie zur ".$data['name']." transferiert</b><br>".$msg,3);
		else $rad = "<br>->> <a href=?p=ship&s=ss&id=".$data['id'].">Zur ".stripslashes($data['name'])." wechseln</a>";
		return $msg.$rad;
	}

	function transfercrew($target,$count,$way)
	{
		if ($this->id == $target) return;
		$data = $this->db->query("SELECT a.id,a.name,a.user_id,a.cloak,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.schilde_status,a.crew,a.max_crew,a.eps,a.max_eps,a.dock,b.probe FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE a.id=".$target." LIMIT 1",4);
		if ($data == 0 || checksector($data) == 0 || $tar['cloak'] == 1 || $this->uid != $data['user_id']) return;
		if ($data['schilde_status'] == 1) return "Das Zielschiff hat die Schilde aktiviert";
		if ($way == "fr" && $data[crew] == 0) return "Keine Crew auf der ".$data['name']." vorhanden";
		if ($way == "to" && $this->crew == 0) return "Keine Crew auf der ".$this->name." vorhanden";
		$result = shipexception(array("schilde_status" => 0,"cloak" => 0,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($result['code'] == 1) return $result['msg'];
		if ($this->dock != $target && $data['dock'] != $this->id)
		{
			$result = shipexception(array("nbs" => 1,"eps" => -1),$this);
			if ($result['code'] == 1) return $result['msg'];
		}
		if ($count == "max") $count = $this->eps;
		if ($way == "fr")
		{
			if ($this->max_crew <= $this->crew) return "Alle Crewquartiere auf der ".$this->name." sind belegt";
			if ($count > $data['crew']) $count = $data['crew'];
			if ($count > $this->max_crew-$this->crew) $count = $this->max_crew-$this->crew;
			if (($this->dock > 0 && ($this->dock == $data['dock'] || $this->dock == $data['id'])) || ($data['dock'] > 0 && $data['dock'] == $this->id)) $e = 0;
			else $e = ceil($count/5);
			if ($e > $this->eps)
			{
				$count = $this->eps*5;
				$e = $this->eps;
			}
			$this->eps -= $e;
			$this->db->query("UPDATE stu_ships SET crew=crew-".$count." WHERE id=".$target." LIMIT 1");
			$this->db->query("UPDATE stu_ships SET crew=crew+".$count.",eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
				return $count." Crew von der ".stripslashes($data['name'])." gebeamt".($e > 0 ? " (".$e." Energie verbraucht)" : "");
		}
		elseif ($way == "to")
		{
			if ($data['max_crew'] <= $data['crew']) return "Alle Crewquartiere auf der ".$data['name']." sind belegt";
			if ($count > $this->crew) $count = $this->crew;
			if ($count > $data['max_crew']-$data['crew']) $count = $data['max_crew']-$data['crew'];
			$e = ceil($count/4);
			if (($this->dock > 0 && ($this->dock == $data['dock'] || $this->dock == $data['id'])) || ($data['dock'] > 0 && $data['dock'] == $this->id)) $e = 0;
			else $e = ceil($count/5);
			if ($e > $this->eps)
			{
				$count = $this->eps*5;
				$e = $this->eps;
			}
			$this->eps -= $e;
			$this->db->query("UPDATE stu_ships SET crew=crew+".$count." WHERE id=".$target." LIMIT 1");
			$this->db->query("UPDATE stu_ships SET crew=crew-".$count.",eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
			return $count." Crew zu der ".stripslashes($data['name'])." gebeamt".($e > 0 ? " (".$e." Energie verbraucht)" : "");
		}
		return;
	}

	function checkgood($id,$good) { return $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$id." AND goods_id=".$good." LIMIT 1",1); }

	function getshipstorage($id) { return $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_ships_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.ships_id=".$id." ORDER BY b.sort"); }

	function getshipstoragesum($id) { return $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$id,1); }

	function geterzfeld() { return $this->db->query("SELECT chance_20,chance_21,chance_22,chance_23,chance_24,chance_25 FROM stu_map_values WHERE systems_id=".$this->systems_id." AND sx=".$this->sx." AND sy=".$this->sy,4); }

	function getcolstorage($id) { return $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_colonies_storage as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=".$id." ORDER BY b.goods_id"); }

	function getcolstoragesum($id) { return $this->db->query("SELECT SUM(count) FROM stu_colonies_storage WHERE colonies_id=".$id,1); }

	function beamtocol($target,$good,$count)
	{
		$tar = $this->db->query("SELECT id,name,user_id,sx,sy,systems_id,schilde_status,max_storage FROM stu_colonies WHERE id=".$target." LIMIT 1",4);
		if ($tar == 0) return;
		if (checksector($tar) == 0) return;
		if ($this->rumps_id == 9) return "Konstrukte können nicht beamen";
		if ($tar['schilde_status'] == 1 && $tar['user_id'] != $this->uid) return "Die Kolonie hat die Schilde aktiviert";
		if ($tar['user_id'] == 1) return "Zu dieser Kolonie kann nicht gebeamt werden";
		$return = shipexception(array("nbs" => 1,"schilde_status" => 0,"cloak" => 0,"eps" => -1,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($return['code'] == 1)
		{
			$this->stop_trans = 1;
			return $return['msg'];
		}
		$tast = $this->getcolstoragesum($target);
		if ($tast >= $tar['max_storage']) return "Kein Lagerraum auf der Kolonie vorhanden";
		$this->crew - $this->min_crew > 3 ? $mb = 40 : $mb = 10+(($this->crew-$this->min_crew)*10);
		foreach ($good as $key => $value)
		{
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->checkgood($this->id,$value);
			if ($c == 0) continue;
			if ($this->eps == 0)
			{
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			$c < $count[$key] ? $c = $c : $c = $count[$key];
			if ($c > $tar['max_storage'] - $tast) $c = $tar['max_storage'] - $tast;
			if ($c <= 0) continue;
			if (ceil($c/$mb) > $this->eps)
			{
				$c = $this->eps*$mb;
				$this->eps = 0;
			}
			else $this->eps -= ceil($c/$mb);
			$this->db->query("START TRANSACTION");
			$this->lowerstorage($this->id,$value,$c);
			$this->colupperstorage($target,$value,$c);
			$this->db->query("COMMIT");
			$msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value." LIMIT 1",1)."<br>";
			$e += ceil($c/$mb);
			$tast += $c;
			if ($tast >= $tar['max_storage']) break;
			if ($this->eps == 0) break;
		}
		if (!$msg) return "Es wurden keine Waren gebeamt";
		if ($this->uid != $tar['user_id']) $this->send_pm($this->uid,$tar['user_id'],"<b>Die ".stripslashes($this->name)." beamt Waren zur Kolonie ".stripslashes($tar['name'])."</b><br>".$msg,2);
		else $al = "<br>->> <a href=?p=colony&s=sc&id=".$tar['id']."&shd=".$this->id.">Zur Kolonie wechseln</a>";
		$this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		return "<b>Es wurden folgende Waren zu der Kolonie ".$tar['name']." gebeamt</b><br>".$msg."Energieverbrauch: <b>".$e."</b>".$al;
	}

	function beamfromcol($target,$good,$count)
	{
		$tar = $this->db->query("SELECT a.id,a.name,a.user_id,a.sx,a.sy,a.systems_id,a.schilde_status,b.vac_active FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=".$target." LIMIT 1",4);
		if ($tar == 0) return;
		if (checksector($tar) == 0) return;
		if ($tar['schilde_status'] == 1 && $tar['user_id'] != $this->uid) return "Die Kolonie hat die Schilde aktiviert";
		if ($tar['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($this->sess['level'] < 2) return "Du kannst erst ab Level 2 von Kolonien beamen";
		$result = shipexception(array("nbs" => 1,"schilde_status" => 0,"cloak" => 0,"eps" => -1,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($result['code'] == 1)
		{
			$this->stop_trans = 1;
			return $result['msg'];
		}
		if ($tar['user_id'] != $this->uid && $this->db->query("SELECT field_id FROM stu_colonies_fielddata WHERE buildings_id=107 AND aktiv=1 AND colonies_id=".$target." LIMIT 1",1) > 0) return "Die Oberfläche der Kolonie ist nicht scanbar";
		$tast = $this->getshipstoragesum($this->id);
		if ($tast >= $this->storage) return "Kein Lagerraum auf dem Schiff vorhanden";
		$this->crew - $this->min_crew > 3 ? $mb = 40 : $mb = 10+(($this->crew-$this->min_crew)*10);
		foreach ($good as $key => $value)
		{
			if (!$count[$key] || $count[$key] == 0 || !check_int($count[$key])) continue;
			$c = $this->db->query("SELECT count FROM stu_colonies_storage WHERE goods_id=".$value." AND colonies_id=".$target." LIMIT 1",1);
			if ($c == 0) continue;
			if ($this->eps == 0)
			{
				$msg .= "Keine Energie zum beamen weiterer Waren vorhanden<br>";
				break;
			}
			if ($count[$key] > $c) $count[$key] = $c;
			if ($value >= 80 && $value<100)
			{
				if ($this->uid != $tar['user_id']) continue;
				$lt = $this->db->query("SELECT goods_id FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id>=80 AND goods_id<100",1);
				if ($lt != $value && $lt != 0)
				{
					$return .= "Dieses Schiff hat bereits einen anderen Torpedotyp geladen<br>";
					continue;
				}
				$tmc = $this->db->query("SELECT a.max_torps,a.m10,b.torp_type FROM stu_ships_buildplans as a LEFT JOIN stu_modules as b ON a.m10=b.module_id WHERE a.plans_id=".$this->plans_id." LIMIT 1",4);
				if ($tmc['max_torps'] == 0)
				{
					$return .= "Dieses Schiff kann keine Torpedos laden<br>";
					continue;
				}
				$tt = $this->db->query("SELECT torp_type,type FROM stu_torpedo_types WHERE goods_id=".$value." LIMIT 1",4);
				if ($tt['type'] > $tmc['torp_type'])
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				if (($tt['type'] == 1 || $tt['type'] == 2) && $tmc['m10'] != 656 && $this->db->query("SELECT user_id FROM stu_researched WHERE research_id=163 AND user_id=".$this->uid." LIMIT 1",1) == 0)
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				if (($tt['type'] == 3 || $tt['type'] == 4) && $tmc['m10'] != 656 && $this->db->query("SELECT user_id FROM stu_researched WHERE research_id=164 AND user_id=".$this->uid." LIMIT 1",1) == 0)
				{
					$return .= "Dieser Torpedotyp kann nicht geladen werden<br>";
					continue;
				}
				$tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE goods_id=".$value." AND ships_id=".$this->id." LIMIT 1",1);
				if ($tc >= $tmc['max_torps'])
				{
					$return .= "Das Schiff ist bereits mit der Maximalzahl an Torpedos ausgestattet<br>";
					continue;
				}
				if ($tmc['m10'] != 656) $this->db->query("UPDATE stu_ships SET torp_type=".$tt['torp_type']." WHERE id=".$this->id." LIMIT 1");
				$tc + $count[$key] > $tmc['max_torps'] ? $c = $tmc[max_torps]-$tc : $c = $count[$key]; 
			}
			elseif ($value >= 110 && $value < 190)
			{
				if ($this->uid != $tar['user_id']) continue;
				if ($shuttle_stop == 1) continue;
				if ($this->max_shuttles == 0 || $this->is_shuttle == 1)
				{
					$shuttle_stop = 1;
					$return .= "Dieses Schiff kann keine Shuttles laden<br>";
					continue;
				}
				$shud = $this->db->query("SELECT shuttle_type,goods_id FROM stu_shuttle_types WHERE goods_id=".$value." LIMIT 1",4);
				if ($shud[shuttle_type] > $this->max_shuttle_type)
				{
					$return .= "Dieser Shuttle-Typ kann nicht geladen werden<br>";
					continue;
				}
				if ($this->max_shuttles <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND !ISNULL(b.shuttle_type)",1))
				{
					$shuttle_stop = 1;
					$return .= "Die Shuttlerampe ist belegt<br>";
					continue;
				}
				if ($this->max_cshuttle_type <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$this->id." AND b.goods_id!=".$value." AND !ISNULL(b.shuttle_type)",1))
				{
					$return .= "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht<br>";
					continue;
				}
				$sc = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE goods_id>=110 AND goods_id<142 AND ships_id=".$this->id,1);
				$sc + $count[$key] > $this->max_shuttles ? $c = $this->max_shuttles-$sc : $c = $count[$key];
			}
			else $count[$key] > $c ? $c = $c : $c = $count[$key];
			if ($c > $this->storage - $tast) $c = $this->storage - $tast;
			if ($c <= 0) continue;
			if (ceil($c/$mb) > $this->eps)
			{
				$c = $this->eps*$mb;
				$this->eps = 0;
			}
			else $this->eps -= ceil($c/$mb);
			$this->db->query("START TRANSACTION");
			$this->collowerstorage($target,$value,$c);
			$this->upperstorage($this->id,$value,$c);
			$this->db->query("COMMIT");
			$msg .= $c." ".$this->db->query("SELECT name FROM stu_goods WHERE goods_id=".$value." LIMIT 1",1)."<br>";
			$e += ceil($c/$mb);
			$tast += $c;
			if ($tast >= $this->storage) break;
			if ($this->eps == 0) break;
		}
		if (!$msg) return $return."Es wurden keine Waren gebeamt";
		if ($this->uid != $tar['user_id']) $this->send_pm($this->uid,$tar['user_id'],"<b>Die ".stripslashes($this->name)." beamt Waren von der Kolonie ".stripslashes($tar[name])."</b><br>".$msg,2);
		else $al = "<br>->> <a href=?p=colony&s=sc&id=".$tar['id']."&shd=".$this->id.">Zur Kolonie wechseln</a>";
		$this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id);
		return "<b>Es wurden folgende Waren von der Kolonie ".$tar['name']." gebeamt</b><br>".$msg.$return."Energieverbrauch: <b>".$e."</b>".$al;
	}

	function transfercrewcol($target,$count,$way)
	{
		$data = $this->db->query("SELECT id,name,user_id,sx,sy,systems_id,bev_free,bev_work,bev_max,schilde_status FROM stu_colonies WHERE id=".$target." LIMIT 1",4);
		if ($data == 0 || checksector($data) == 0  || $this->uid != $data['user_id']) return;
		if ($data['schilde_status'] == 1 && $data['user_id'] != $this->uid) return "Die Kolonie hat die Schilde aktiviert";
		if ($way == "fr" && $data['bev_free'] == 0) return "Keine Crew auf der Kolonie ".$data[name]." vorhanden";
		if ($way == "to" && $this->crew == 0) return "Keine Crew auf der ".$this->name." vorhanden";
		$return = shipexception(array("schilde_status" => 0,"cloak" => 0,"nbs" => 1,"eps" => -1,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($return['code'] == 1) return $return['msg'];
		if ($count == "max") $count = $this->eps;
		$al = "<br>->> <a href=?p=colony&s=sc&id=".$data['id']."&shd=".$this->id.">Zur Kolonie wechseln</a>";
		if ($way == "fr")
		{
			if ($this->max_crew <= $this->crew) return "Alle Crewquartiere auf der ".$this->name." sind belegt";
			if ($count > $data['bev_free']) $count = $data['bev_free'];
			if ($count > $this->max_crew-$this->crew) $count = $this->max_crew-$this->crew;
			$e = ceil($count/5);
			if ($e > $this->eps)
			{
				$count = $this->eps*5;
				$e = $this->eps;
			}
			$this->eps -= $e;
			$this->db->query("UPDATE stu_colonies SET bev_free=bev_free-".$count." WHERE id=".$target." LIMIT 1");
			$this->db->query("UPDATE stu_ships SET crew=crew+".$count.",eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
			return $count." Crew von der Kolonie ".stripslashes($data['name'])." gebeamt (".$e." Energie verbraucht)".$al;
		}
		elseif ($way == "to")
		{
			if ($data['bev_work']+$data['bev_free'] >= $data['bev_max']) return "Kein Wohnraum auf der Kolonie ".$data['name']." vorhanden";
			if ($count > $this->crew) $count = $this->crew;
			if ($count > $data['bev_max']-$data['bev_free']-$data['bev_work']) $count = $data['bev_max']-$data['bev_free']-$data['bev_work'];
			$e = ceil($count/5);
			if ($e > $this->eps)
			{
				$count = $this->eps*5;
				$e = $this->eps;
			}
			$this->eps -= $e;
			$this->db->query("UPDATE stu_colonies SET bev_free=bev_free+".$count." WHERE id=".$target." LIMIT 1");
			$this->db->query("UPDATE stu_ships SET crew=crew-".$count.",eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
			return $count." Crew zu der Kolonie ".stripslashes($data['name'])." gebeamt (".$e." Energie verbraucht)".$al;
		}
		return;
	}
	function loadshields($count)
	{
		if ($this->checksubsystem(4,$this->id) == 1) return "Die Reparatur an den Schilden ist noch nicht abgeschlossen";
		$return = shipexception(array("schilde_load" => $this->max_schilde,"schilde_status" => 0,"cloak" => 0,"eps" => -1,"crew" => $this->min_crew),$this);
		if ($return[code] == 1) return $return['msg'];
		if ($count == "max" || $count > $this->eps) $count = $this->eps;
		if ($count > $this->max_schilde-$this->schilde) $count = $this->max_schilde-$this->schilde;
		$this->db->query("UPDATE stu_ships SET schilde=schilde+".$count.",eps=eps-".$count.",schilde_status=".(time()+21600)." WHERE id=".$this->id." LIMIT 1");
		$this->eps -= $count;
		$this->schilde += $count;
		return "Die Schilde wurden um ".$count." Einheiten aufgeladen";
	}

	function etransferc($target,$count)
	{
		$data = $this->db->query("SELECT a.id,a.sx,a.sy,a.systems_id,a.user_id,a.name,a.eps,a.max_eps,a.schilde_status,b.vac_active FROM stu_colonies as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=".$target." LIMIT 1",4);
		if ($data == 0 || checksector($data) == 0) return;
		if ($data['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($data['schilde_status'] == 1 && $data['user_id'] != $this->uid) return "Die Kolonie ".$data['name']." hat die Schilde aktiviert";
		if ($data['user_id'] == 1) return "Zu dieser Kolonie kann keine Energie transferiert werden";
		if ($this->is_shuttle == 1) return "Ein Shuttle kann keinen Energietransfer durchführen";
		if ($data['eps'] >= $data['max_eps']) return "Das EPS der Kolonie ".$data['name']." ist voll";
		$return = shipexception(array("nbs" => 1,"schilde_status" => 0,"cloak" => 0,"eps" => -1,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($return['code'] == 1) return $return['msg'];
		if ($count == "max") $count = $this->eps;
		else if ($count > $this->eps) $count = $this->eps;
		if ($data['max_eps'] < $count+$data['eps']) $count = $data['max_eps']-$data['eps'];
		$this->db->query("UPDATE stu_colonies SET eps=eps+".$count." WHERE id=".$target." LIMIT 1");
		$this->eps -= $count;
		$this->db->query("UPDATE stu_ships SET eps=".$this->eps." WHERE id=".$this->id." LIMIT 1");
		$msg = $count." Energie zur Kolonie ".$data['name']." transferiert";
		if ($this->uid != $data['user_id']) $this->send_pm($this->uid,$data['user_id'],"<b>Die ".stripslashes($this->name)." hat ".$count." Energie zur Kolonie ".stripslashes($data['name'])." transferiert</b><br>",3);
		else $al = "<br>->> <a href=?p=colony&s=sc&id=".$data['id']."&shd=".$this->id.">Zur Kolonie wechseln</a>";
		return $msg.$al;
	}

	function changename($nn)
	{
		$nn = str_replace("\"","",$nn);
		$nn = addslashes(format_string($nn));
		if (strlen($nn) > 255) $nn = strip_tags($nn);
		if (!check_html_tags($nn)) $nn = strip_tags($nn);
		$this->db->query("UPDATE stu_ships SET name='".$nn."' WHERE id=".$this->id." LIMIT 1");
		$this->db->query("UPDATE stu_ships_logdata SET name='".$nn."' WHERE ships_id=".$this->id." LIMIT 1");
		$this->name = $nn;
		return "Schiffsname geändert in ".stripslashes($nn);
	}

	function activatetraktor($target)
	{
		if ($this->id == $target) return;
		$return = shipexception(array("nbs" => 1,"traktor" => 0,"schilde_status" => 0,"dock" => 0,"cloak" => 0,"eps" => 2,"warpstate" => 0,"crew" => $this->min_crew),$this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->checksubsystem(8,$this->id) == 1) return "Das EPS-System ist beschädigt - Traktorstrahl nicht aktivierbar";
		$data = $this->db->query("SELECT a.id,a.name,a.user_id,a.rumps_id,a.fleets_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.eps,a.schilde_status,a.schilde,a.alvl,a.dock,a.traktor,a.traktormode,a.crew,a.min_crew,a.maintain,b.trumfield,b.probe,b.slots,c.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_user as c ON a.user_id=c.id WHERE a.id=".$target." LIMIT 1",4);
		if ($data == 0 || $data['cloak'] == 1 || checksector($data) == 0) return;
		if ($data['vac_active'] == 1) return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		if ($data['trumfield'] == 1) return "Ziel kann nicht erfasst werden";
		if ($data['fleets_id'] > 0) return "Schiffe in einer Flotte können nicht erfasst werden";
		if ($data['maintain'] > 0) return "Ziel kann nicht erfasst werden";
		if ($data['slots'] > 0) return "Station kann nicht erfasst werden";
		if ($data['rumps_id'] == 999) return "Der Weihnachtsmann verbietet das erfassen von Türchen";
		if ($data['dock'] > 0) return "Das Zielschiff ist angedockt und kann nicht erfasst werden";
		if ($data['schilde_status'] == 1) return "Die ".stripslashes($data['name'])." hat die Schilde aktiviert";
		if ($data['traktormode'] == 2) return "Die ".stripslashes($data['name'])." wird bereits von einem Traktorstrahl gehalten";
		if ($data['traktor'] > 0) return "Der Traktorstrahl der ".stripslashes($data['name'])." ist aktiviert";
		if ($this->systems_id > 0) $sysname = $this->db->query("SELECT name FROM stu_systems WHERE systems_id=".$data['systems_id']." LIMIT 1",1);
		if ($this->uid != $data['user_id'] && $data['maintain'] == 0 && ($data['alvl'] > 1 || $data['fleets_id'] > 0) && $data['crew'] > $data['min_crew'] && $data['eps'] > 1 && $data['schilde'] > 0 && ($data['schilde_status'] < 1 || $data['schilde_status'] < time()) && $this->checksubsystem(4,$data['id']) != 1)
		{
			$this->logbook[$this->id][] = array("user_id" => $this->uid,"text" => "Das Schiff hat in Sektor ".($this->systems_id > 0 ? $this->sx."|".$this->sy." (".$sysname."-System)" : $this->cx."|".$this->cy)." versucht, die ".$data['name'] ." mit einem Traktorstrahl zu erfassen","type" => 3);
			$this->logbook[$data['id']][] = array("user_id" => $data['user_id'],"text" => "Die ".$data['name']." hat in Sektor ".($this->systems_id > 0 ? $this->sx."|".$this->sy." (".$sysname."-System)" : $this->cx."|".$this->cy)." versucht, das Schiff mit einem Traktorstrahl zu erfassen","type" => 3);
			$this->db->query("UPDATE stu_ships SET schilde_status=1,eps=eps-1,still=0 WHERE id=".$target." LIMIT 1");
			$this->db->query("UPDATE stu_ships SET eps=eps-2 WHERE id=".$this->id." LIMIT 1");
			$this->send_pm($this->uid,$data['user_id'],"Die ".$this->name." hat versucht, die ".$data['name']." mit dem Traktorstrahl zu erfassen",3);
			return "Die ".stripslashes($data['name'])." aktiviert die Schilde - Kann Ziel nicht erfassen";
		}
		$this->logbook[$this->id][] = array("user_id" => $this->uid,"text" => "Das Schiff hat in Sektor ".($this->systems_id > 0 ? $this->sx."|".$this->sy." (".$sysname."-System)" : $this->cx."|".$this->cy)." die ".$data['name'] ." mit einem Traktorstrahl erfasst","type" => 3);
		$this->logbook[$data['id']][] = array("user_id" => $data['user_id'],"text" => "Die ".$data['name']." hat in Sektor ".($this->systems_id > 0 ? $this->sx."|".$this->sy." (".$sysname."-System)" : $this->cx."|".$this->cy)." das Schiff mit einem Traktorstrahl erfasst","type" => 3);
		$this->db->query("UPDATE stu_ships SET traktor=".$this->id.",traktormode=2 WHERE id=".$target." LIMIT 1");
		$this->db->query("UPDATE stu_ships SET traktor=".$target.",traktormode=1,eps=eps-2 WHERE id=".$this->id." LIMIT 1");
		$this->eps -= 2;
		if ($this->uid != $data['user_id']) $this->send_pm($this->uid,$data['user_id'],"Die ".$data['name']." wurde in Sektor ".($data['systems_id'] == 0 ? $data['cx']."|".$data['cy'] : $data['sx']."|".$data['sy']." (".$sysname."-System)")." vom Traktorstrahl der ".$this->name." erfasst",3);
		return "Traktorstrahl auf die ".stripslashes($data['name'])." gerichtet";
	}

	function deactivatetraktor()
	{
		if ($this->traktormode != 1) return;
		$data = $this->db->query("SELECT id,user_id,name,systems_id FROM stu_ships WHERE id=".$this->traktor." LIMIT 1",4);
		if ($this->systems_id > 0) $sysname = $this->m->getsysnamebyid($this->systems_id);
		if ($data['user_id'] != $this->uid) $this->send_pm($this->uid,$data['user_id'],"Die ".$this->name." hat den auf die ".$data['name']." gerichteten Traktorstrahl in Sektor ".($this->systems_id > 0 ? $this->sx."|".$this->sy." (".$sysname."-System)" : $this->cx."|".$this->cy)." deaktiviert",3);
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=".$this->id." OR id=".$this->traktor." LIMIT 2");
		$this->logbook[$this->id][] = array("user_id" => $this->uid,"text" => "Das Schiff hat in Sektor ".($this->systems_id > 0 ? $this->sx."|".$this->sy." (".$sysname."-System)" : $this->cx."|".$this->cy)." den auf die ".$data['name'] ." gerichteten Traktorstrahl deaktiviert","type" => 3);
		$this->logbook[$data['id']][] = array("user_id" => $data['user_id'],"text" => "Die ".$data['name']." hat in Sektor ".($this->systems_id > 0 ? $this->sx."|".$this->sy." (".$sysname."-System)" : $this->cx."|".$this->cy)." den auf das Schiff gerichteten Traktorstrahl deaktiviert","type" => 3);
		return "Der Traktorstrahl wurde deaktiviert";
	}

	function changealvl($lvl)
	{
		if ($lvl < 1 || $lvl > 3) return;
		if ($this->alvl == $lvl) return;
		if ($this->crew < $this->min_crew) return "Es werden ".$this->min_crew." Crewmitglieder benötigt";
		$this->db->query("UPDATE stu_ships SET alvl='".$lvl."' WHERE id=".$this->id." LIMIT 1");
		if ($lvl == 2) $sa = "<br>".$this->av("nbs");
		if ($lvl == 3)
		{
			$msg = "<br>".$this->av("sh");
			$msg .= "<br>".$this->av("nbs");
			$msg .= "<br>".$this->av("wp");
			$msg .= "<br>".$this->av("wt");
		}
		return "Alarmstufe wurde geändert".$msg;
	}

	function getweaponbyid($moduleId) { return $this->db->query("SELECT name,wtype,eps_cost,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$moduleId." LIMIT 1",4); }

	function trumfield($data,$name="")
	{
		$this->dsships[$data['id']] = 1;
		if ($data['fleets_id'] > 0 && $this->db->query("SELECT fleets_id FROM stu_fleets WHERE ships_id=".$data['id']." LIMIT 1",1) > 0)
		{
			$sc = $this->db->query("SELECT id FROM stu_ships WHERE id!=".$data['id']." AND fleets_id=".$data['fleets_id']." ORDER BY RAND() LIMIT 1",1);
			if ($sc > 0) $this->db->query("UPDATE stu_fleets SET ships_id=".$sc." WHERE fleets_id=".$data['fleets_id']." LIMIT 1");
			else $this->db->query("DELETE FROM stu_fleets WHERE fleets_id=".$data['fleets_id']." LIMIT 1");
		}
		if (strlen($data['name']) == 0) return;
		$tx = "Die ".$data['name']." (".$data['rname'].") wurde in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname."-System)" : $data['cx']."|".$data['cy'])." ".($name != "" ? "von der ".$name." zerstört" : "beim Sektoreinflug zerstört");
		$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg,coords_x,coords_y) VALUES ('".$tx."',NOW(),'".($this->col_destroy == 1 ? 2 : 1)."','".strip_tags(str_replace("'","",stripslashes($tx)))."','".$data['cx']."','".$data['cy']."')");
		if ($data['is_shuttle'] != 1) $this->db->query("UPDATE stu_ships SET user_id=1,huelle=".ceil(($data['max_huelle']/100)*15).",schilde=0,schilde_status=0,alvl=1,warpable=0,warpcore=0,traktor=0,traktormode=0,dock=0,crew=0,name='Trümmerfeld',eps=0,batt=0,nbs=0,lss=0,torp_type=0,rumps_id=8,trumps_id=8,cloak='0',fleets_id=0,warp='0',still=0,wea_phaser='0',wea_torp='0',is_rkn=0 WHERE id=".$data['id']." LIMIT 1");
		else
		{
			$this->db->query("DELETE FROM stu_ships WHERE id=".$data['id']." LIMIT 1");
			$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$data['id']);
		}
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE traktor=".$data['id']." LIMIT 2");
		$this->db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=".$data['id']);
		$this->db->query("DELETE FROM stu_ships_decloaked WHERE ships_id=".$data['id']." LIMIT 1");
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=".$data['id']." LIMIT 1");
		$this->db->query("DELETE FROM stu_ships_storage WHERE goods_id>=80 AND goods_id<100 AND ships_id=".$data['id']);
		$this->db->query("UPDATE stu_ships SET dock=0 WHERE dock=".$data['id']);
		$this->db->query("DELETE FROM stu_dockingrights WHERE type='1' AND id=".$data['id']);
		$this->db->query("DELETE FROM stu_ships_shuttles WHERE ships_id=".$data['id']);
		$dat = $this->db->query("SELECT b.m1,b.m2,b.m3,b.m4,b.m5,b.m6,b.m7,b.m8,b.m9,b.m10,b.m11,c.m1c,c.m2c,c.m3c,c.m4c,c.m5c,c.m6c,c.m7c,c.m8c,c.m9c,c.m10c,c.m11c FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b ON b.plans_id=a.plans_id LEFT JOIN stu_rumps as c ON c.rumps_id=b.rumps_id WHERE a.id=".$data['id']." LIMIT 1",4);
		$i = 1;
		while($i<=11)
		{
			if ($dat["m".$i] == 0)
			{
				$i++;
				continue;
			}
			$dchg = $this->db->query("SELECT demontchg FROM stu_modules WHERE module_id=".$dat["m".$i]." LIMIT 1",1);
			if ($dchg == 0 || rand(1,100) > $dchg)
			{
				$i++;
				continue;
			}
			$this->upperstorage($data['id'],$dat["m".$i],rand(1,$dat["m".$i."c"]));
			$i++;
		}
		if (!check_int($data['user_id'])) return;
		if ($data['user_id'] < 100) return;
		$cc = $this->db->query("SELECT COUNT(*) FROM stu_colonies WHERE user_id=".$data['user_id'],1);
		if ($cc == 0)
		{
			if ($cs = $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE rumps_id=1 AND user_id=".$data['user_id'],1) == 0) $this->db->query("UPDATE stu_user SET level='0' WHERE id=".$data['user_id']." LIMIT 1");
		}
	}

	function deletetrumfield($data)
	{
		$this->db->query("DELETE FROM stu_ships WHERE id=".$data['id']." LIMIT 1");
		$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$data['id']." LIMIT 1");
	}

	function getscbygood($shipId,$goodId) { return $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$shipId." AND goods_id=".$goodId,1); }

	function notruf()
	{
		global $_GET;
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships_ecalls WHERE user_id=".$this->uid." AND ships_id=".$_GET[id],1) != 0) return "Für dieses Schiff existiert bereits ein Notruf";
		global $ship;
		if ($ship->systems_id > 0) $text = "Das Schiff befindet sich in Sektor ".$ship->sx."|".$ship->sy." im ".$this->db->query("SELECT name FROM stu_systems WHERE systems_id=".$ship->systems_id,1)."-System (".$ship->cx."|".$ship->cy.")";
		else $text = "Das Schiff befindet sich außerhalb eines Systems bei ".$ship->cx."|".$ship->cy;
		$this->db->query("INSERT INTO stu_ships_ecalls (user_id,ships_id,text,date) VALUES ('".$this->uid."','".$this->id."','".$text."',NOW())");
		return "Notruf erstellt";
	}

	function loadcto($colId) { $this->result = $this->db->query("SELECT a.mode,a.goods_id,b.name FROM stu_colonies_trade as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.colonies_id=".$colId." ORDER BY a.mode,b.sort"); }

	function loadsysoffers($system_id)
	{
		$this->co = $this->db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=".$system_id." AND b.mode='1' GROUP BY b.goods_id ORDER BY c.sort");
		$this->cw = $this->db->query("SELECT b.goods_id,b.mode,c.name FROM stu_colonies as a LEFT JOIN stu_colonies_trade as b ON a.id=b.colonies_id LEFT JOIN stu_goods as c ON b.goods_id=c.goods_id WHERE a.systems_id=".$system_id." AND b.mode='2' GROUP BY b.goods_id ORDER BY c.sort");
	}

	function generateszcode()
	{
		global $_SESSION;
		$sc = substr(md5(ceil($_SESSION['logintime']/$this->id)),0,6);
		$_SESSION['szcode'] = $sc;
	}

	function sectorscan()
	{
		$return = shipexception(array("cloak" => 0,"nbs" => 1,"eps" => 3,"crew" => $this->min_crew),$this);
		if ($return[code] == 1) return $return['msg'];
		$data['ss'] = $this->db->query("SELECT rumps_id,UNIX_TIMESTAMP(date) as date_tsp FROM stu_sectorflights WHERE ships_id!=".$this->id." AND UNIX_TIMESTAMP(date)>".(time()-28800)." AND cloak='0' AND ".($this->systems_id > 0 ? "sx=".$this->sx." AND sy=".$this->sy." AND systems_id=".$this->systems_id : "cx=".$this->cx." AND cy=".$this->cy."")." ORDER BY fleets_id DESC,ships_id ASC");
		if ($this->cloak != 1) $data['sc'] = $this->db->query("SELECT a.id,a.user_id,a.fleets_id,a.rumps_id,a.name,a.user,a.allys_id,a.fname,a.cloak_val,b.type,c.mode,d.ships_id as dcship_id FROM stu_views_cs as a LEFT JOIN stu_ally_relationship as b ON ((b.allys_id1=a.allys_id AND b.allys_id2=".$_SESSION["allys_id"].") OR (b.allys_id2=a.allys_id AND b.allys_id1=".$_SESSION["allys_id"].")) LEFT JOIN stu_contactlist as c ON c.user_id=a.user_id AND c.recipient=".$this->uid." LEFT JOIN stu_ships_decloaked as d ON a.id=d.ships_id AND d.user_id=".$this->uid." WHERE a.cloak='1' AND ".($this->systems_id > 0 ? "a.sx=".$this->sx." AND a.sy=".$this->sy." AND a.systems_id=".$this->systems_id : "a.cx=".$this->cx." AND a.cy=".$this->cy." AND a.systems_id=0")." AND a.user_id!=".$this->uid." ORDER BY a.fleets_id ASC,a.id ASC");
		$this->db->query("UPDATE stu_ships SET eps=eps-3 WHERE id=".$this->id." LIMIT 1");
		return $data;
	}

	function setdecloaked($shipId,$userid,$name)
	{
		$this->db->query("INSERT INTO stu_ships_decloaked (ships_id,user_id) VALUES ('".$shipId."','".$this->uid."')");
		$this->send_pm($this->uid,$userid,"Der Siedler ".addslashes($this->sess["user"])." hat die ".addslashes($name)." enttarnt",3);
	}

	function getweapon(&$wmod) { return $this->db->query("SELECT name,wtype,eps_cost,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$wmod." LIMIT 1",4); }

	function attack(&$tarId,&$shipId,$fleet=0,$strb=0,$redalert=0)
	{
		if ($tarId == $shipId) return;
		if (!check_int($tarId)) return;
		$data = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.eps,a.phaser,a.nbs,a.wea_phaser,a.wea_torp,a.torp_type,a.crew,a.min_crew,a.warp,a.cfield,b.treffer,b.m6,b.m10,c.name as rname,c.m1c,c.m2c,c.m6c FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON c.rumps_id=a.rumps_id WHERE a.id=".$shipId." LIMIT 1",4);
		if ($data == 0) return;
		if ($data['wea_phaser'] == 0 && $data['wea_torp'] == 0) return "Auf der ".$data['name']." ist kein Waffensystem aktiviert";
		$target = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,b.name as rname,b.slots,b.trumfield,b.is_shuttle,c.evade,c.m1,c.m2,c.m6,d.vac_active FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_user as d ON d.id=a.user_id WHERE a.id=".$tarId." LIMIT 1",4);
		if ($target == 0) return;

		if (($this->map['sensoroff'] == 1 || $this->map['type'] == 8) && $this->db->query("SELECT ships_id FROM stu_ships_decloaked WHERE UNIX_TIMESTAMP(date)>0 AND ships_id=".$target['id']." AND user_id=".$data['user_id']." LIMIT 1",1) != 0)
		{
			$excep_nbs = 0;
			$target['warp'] = 0;
			$this->db->query("UPDATE stu_ships SET warp='0' WHERE id=".$target['id']." LIMIT 1");
		}
		else $excep_nbs = 1;
		if ($target['warp'] == 1) return "Schiffe im Warp können nicht erfasst werden";
		if ($target['maintain'] > 0) return "Die ".$target['name']." wird gewartet und kann nicht erfasst werden";
		if ($target['vac_active'] == 1)
		{
			global $fleet;
			$fleet->umode = 1;
			return "Der Siedler befindet sich zur Zeit im Urlaubsmodus";
		}
		if ($data['systems_id'] > 0 || $target['systems_id'] > 0)
		{
			if ($data['systems_id'] != $target['systems_id']) return;
			if ($data['sx'] != $target['sx'] || $data['sy'] != $target['sy']) return;
		}
		elseif ($data['cx'] != $target['cx'] || $data['cy'] != $target['cy']) return;
		$return = shipexception(array("nbs" => $excep_nbs,"eps" => 1,"cloak" => 0,"warpstate" => 0,"crew" => $data['min_crew']),$data);
		if ($return['code'] == 1)
		{
			if ($strb == 1) return;
			return $return['msg'];
		}
		// Systemname laden
		if (!$this->m->sysname && $data['systems_id'] > 0) $this->m->sysname = $this->m->getsysnamebyid($data['systems_id']);
		
		if (($data['wea_phaser'] == 1 || $data['wea_torp'] == 1) && $data['user_id'] != $target['user_id'] && $target['user_id'] != 1 && $target['trumfield'] != 1)
		{
			$pm = "Kampf in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname."-System ".$data['cx']."|".$data['cy'].")" : $data['cx']."|".$data['cy'])."<br />";
			if ($redalert == 1) $this->send_pm(1,$data['user_id'],"Die ".stripslashes($data['name'])." hat in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname."-System ".$data['cx']."|".$data['cy'].")" : $data['cx']."|".$data['cy'])." das Feuer auf die ".stripslashes($target['name'])." eröffnet (Alarm Rot)",1);
		}
		// Attacke!
		if ($data['wea_phaser'] == 1 && $this->checksubsystem(6,$data['id']) != 1)
		{
			$wd = $this->getweapon($data['m6']);
			if ($wd['pulse'] > 0)
			{
				$ro = 1;
				$wd['pulse'] += floor($data['m6c']/1.5);
			}
			else
			{
				$wd['strength'] *= $this->get_weapon_damage($data['m6c']);
				$ro = ($data['m6c'] < 4 ? 1 : $data['m6c']-2);
			}
			for($i=1;$i<=$ro;$i++)
			{
				if ($this->checksubsystem(6,$data['id']) == 1) break;
				if ($data['plans_id'] != 1 && $data['user_id'] > 100 && $data['slots'] == 0 && getSystemDamageChance(array("lastmaintainance" => $data['lastmaintainance'],"maintaintime" => $data['maintaintime'])) > rand(1,100))
				{
					$result .= $this->damage_subsystem("foo",$data['id'],6);
					break;
				}
				if ($target['fleets_id'] == 0) $tar = $target;
				else
				{
					$tar = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,b.slots,b.trumfield,b.is_shuttle,b.m1c,b.m2c,b.name as rname,c.evade,c.m1,c.m2,c.m6 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_ships_decloaked as d ON d.ships_id=a.id AND d.user_id=".$data['user_id']." AND UNIX_TIMESTAMP(d.date)=0 WHERE a.fleets_id=".$target['fleets_id']." AND (a.cloak='0' OR !ISNULL(d.user_id)) AND warp='0' ORDER BY RAND() LIMIT 1",4);
					if ($tar == 0) break;
				}
				if ($this->dsships[$tar['id']]) break;
				$res = $this->phaser($data,$tar,$wd,$fleet,$strb);
				$result .= $res['msg']."<br />";
				$pm .= $res['pm']."<br />";
				if (!$this->dsships[$tar['id']])
				{
					$this->write_damage($tar);
					if ($tar['id'] == $target['id'])
					{
						$target['huelle'] = $tar['huelle'];
						$target['schilde'] = $tar['schilde'];
						$target['schilde_status'] = $tar['schilde_status'];
					}
				}
				if ($tar['id'] == $this->id)
				{
					global $fleet;
					if ($fleet->fm != 1)
					{
						if ($destroy == 1) $this->huelle = ceil(($tar['max_huelle']/100)*15);
						else $this->huelle = $tar['huelle'];
						$this->schilde = $tar['schilde'];
						$this->schilde_status = $tar['schilde_status'];
					}
				}
			}
		}
		if ($this->dsships[$target['id']] == 1 && $target['fleets_id'] == 0)
		{
			$this->db->query("UPDATE stu_ships SET eps=".$data['eps']." WHERE id=".$data['id']." LIMIT 1");
			if (strlen(strip_tags($pm)) > 0) $this->send_pm(($excep_nbs == 0 ? 1 : $data['user_id']),$target['user_id'],$pm,3);
			return $result;
		}
		if ($this->dsships[$target['id']] == 1)
		{
			if ($target['fleets_id'] == 0)
			{
				if (strlen(strip_tags($pm)) > 0) $this->send_pm(($excep_nbs == 0 ? 1 : $data['user_id']),$target['user_id'],$pm,3);
				return $result;
			}
			else
			{
				$tar = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,b.slots,b.trumfield,b.name as rname,b.is_shuttle,b.m1c,b.m2c,c.evade,c.m1,c.m2,c.m6 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_ships_decloaked as d ON d.ships_id=a.id AND d.user_id=".$data['user_id']." AND UNIX_TIMESTAMP(d.date)=0 WHERE a.fleets_id=".$target['fleets_id']." AND (a.cloak='0' OR !ISNULL(d.user_id)) AND warp='0' ORDER BY RAND() LIMIT 1",4);
				if ($tar == 0)
				{
					if (strlen(strip_tags($pm)) > 0) $this->send_pm(($excep_nbs == 0 ? 1 : $data['user_id']),$target['user_id'],$pm,3);
					return $result;
				}
				else $target = $tar;
			}
		}
		if (!$this->dsships[$target['id']]) $this->write_damage($target);
		if ($data['wea_torp'] == 1 && $data['torp_type'] > 0 && $this->checksubsystem(10,$data['id']) != 1)
		{
			if ($data['plans_id'] != 1 && $data['user_id'] > 100 && $data['slots'] == 0 && getSystemDamageChance(array("lastmaintainance" => $data['lastmaintainance'],"maintaintime" => $data['maintaintime'])) > rand(1,100)) $result .= $this->damage_subsystem("foo",$data['id'],10);
			else
			{
				$res = $this->torpedo($data,$target,$wd,$fleet,$strb);
				$result .= $res['msg']."<br />";
				$pm .= $res['pm']."<br />";
			}
		}
		if (strlen(strip_tags($pm)) > 0) $this->send_pm(($excep_nbs == 0 ? 1 : $data['user_id']),$target['user_id'],$pm,3);
		$this->db->query("UPDATE stu_ships SET eps=".$data['eps']." WHERE id=".$data['id']." LIMIT 1");
		if ($strb == 1 || $fleet == 1 || $redalert == 1) return $result;
		if ($target['fleets_id'] > 0)
		{
			$result .= "<br /><b>Gegenangriff</b><br />";
			$res = $this->db->query("SELECT id,name,torp_type,phaser,nbs,cloak,eps,schilde,schilde_status,traktor,traktormode,alvl,wea_phaser,wea_torp FROM stu_ships WHERE fleets_id=".$target['fleets_id']." LIMIT 25");
			while($fd=mysql_fetch_assoc($res))
			{
				$qry = 0;
				$con = 0;
				if ($fd['cloak'] == 1)
				{
					$fd['cloak'] = 0;
					$result .= "- Die ".$fd['name']." deaktiviert die Tarnung<br>";
					$qry = 1;
				}
				if ($fd['nbs'] != 1 && $fd['eps'] > 1 && $this->checksubsystem(4,$fd['id']) != 1)
				{
					$fd['nbs'] = 1;
					$result .= "- Die ".$fd['name']." aktiviert die Sensoren<br>";
					$fd['eps'] -= 1;
					$qry = 1;
				}
				if (($fd['schilde_status'] == 0 || ($fd['schilde_status'] > 1 && $fd['schilde_status'] < time())) && $fd['traktormode'] != 2 && $fd['schilde'] > 0 && $fd['eps'] > 1 && $this->checksubsystem(2,$fd['id']) != 1 && $data['cfield'] != 5)
				{
					if ($fd['traktor'] > 0) $this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=".$target['id']." OR id=".$fd['traktor']." LIMIT 2");
					$fd['schilde_status'] = 1;
					$result .= "- Die ".$fd['name']." aktiviert die Schilde<br>";
					$fd['eps'] -= 1;
					$qry = 1;
				}
				if ($fd['alvl'] > 1 && $fd['eps'] > 0 && (($fd['phaser'] > 0 && $fd['wea_phaser'] == 0 && $this->checksubsystem(6,$fd['id']) != 1) || ($fd['torp_type'] > 0 && $fd['wea_torp'] == 0 && $this->checksubsystem(10,$fd['id']) != 1)))
				{
					if ($fd['wea_phaser'] == 0 && $fd['wea_torp'] == 0) $con = 1;
					if ($fd['phaser'] > 0 && $fd['wea_phaser'] == 0)
					{
						$fd['eps'] -= 1;
						$fd['wea_phaser'] = 1;
					}
					if ($fd['torp_type'] > 0 && $fd['eps'] > 0 && $fd['wea_torp'] == 0)
					{
						$fd['eps'] -= 1;
						$fd['wea_torp'] = 1;
					}
					$result .= "- Die ".$fd['name']." aktiviert die Waffensysteme<br />";
					$qry = 1;
				}
				if ($qry == 1) $this->db->query("UPDATE stu_ships SET eps=".$fd['eps'].",nbs=".$fd['nbs'].",schilde_status=".$fd['schilde_status'].",cloak='".$fd['cloak']."',wea_phaser='".$fd['wea_phaser']."',wea_torp='".$fd['wea_torp']."' WHERE id=".$fd['id']." AND rumps_id!=8 LIMIT 1");
				if ($con == 1) continue;
				if ($data['fleets_id'] > 0) $tar = $this->db->query("SELECT id FROM stu_ships WHERE fleets_id=".$data['fleets_id']." ORDER BY RAND() LIMIT 1",1);
				else $tar = $shipId;
				$result .= $this->attack($tar,$fd['id'],0,1);
			}
		}
		else
		{
			if ($target['alvl'] == 1) return $result;
			$result .= "<br /><b>Gegenangriff</b><br />";
			if ($target['cloak'] == 1)
			{
				$target['cloak'] = 0;
				$msg .= "- Die ".$target['name']." deaktiviert die Tarnung<br>";
				$qry = 1;
			}
			if ($target['nbs'] != 1 && $target['eps'] > 1 && $this->checksubsystem(4,$target['id']) != 1)
			{
				$target['nbs'] = 1;
				$msg .= "- Die ".$target['name']." aktiviert die Sensoren<br>";
				$target['eps'] -= 1;
				$qry = 1;
			}
			if (($target['schilde_status'] == 0 || ($target['schilde_status'] > 1 && $target['schilde_status'] < time())) && $target['traktormode'] != 2 && $target['schilde'] > 0 && $target['eps'] > 1 && $this->checksubsystem(2,$target['id']) != 1)
			{
				if ($target['traktor'] > 0) $this->db->query("UPDATE stu_ships SET traktor=0,traktormode=0 WHERE id=".$target['id']." OR id=".$target['traktor']." LIMIT 2");
				if ($target['slots'] > 0) $this->db->query("UPDATE stu_ships SET dock=0 WHERE dock=".$target['id']." LIMIT 1");
				$target['schilde_status'] = 1;
				$msg .= "- Die ".$target['name']." aktiviert die Schilde<br>";
				$target['eps'] -= 1;
				$qry = 1;
			}
			if ($target['eps'] > 0 && (($target['phaser'] > 0 && $target['wea_phaser'] == 0 && $this->checksubsystem(6,$target['id']) != 1) || ($target['torp_type'] > 0 && $target['wea_torp'] == 0 && $this->checksubsystem(10,$target['id']) != 1)))
			{
				if ($target['wea_phaser'] == 0 && $target['wea_torp'] == 0) $con = 1;
				if ($target['phaser'] > 0 && $target['wea_phaser'] == 0)
				{
					$target['eps'] -= 1;
					$target['wea_phaser'] = 1;
				}
				if ($target['torp_type'] > 0 && $target['eps'] > 0 && $target['wea_torp'] == 0)
				{
					$target['eps'] -= 1;
					$target['wea_torp'] = 1;
				}
				$result .= "- Die ".$target['name']." aktiviert die Waffensysteme<br />";
				$qry = 1;
				$con = 1;
			}
			if ($qry == 1) $this->db->query("UPDATE stu_ships SET eps=".$target['eps'].",nbs='".$target['nbs']."',schilde_status=".$target['schilde_status'].",cloak='".$target['cloak']."',wea_phaser='".$target['wea_phaser']."',wea_torp='".$target['wea_torp']."' WHERE id=".$target['id']." AND rumps_id!=8 LIMIT 1");
			if ($con == 1) return $result;
			$result .= $this->attack($shipId,$target['id'],0,1);
		}
		return $result;
	}
	
	function write_damage(&$arr) { $this->db->query("UPDATE stu_ships SET huelle=".$arr['huelle'].",schilde=".$arr['schilde'].",schilde_status=".$arr['schilde_status']." WHERE id=".$arr['id']." LIMIT 1"); }
	
	function get_weapon_damage(&$mods)
	{
		switch($anzahl)
		{
			case 1: return 1.0;
			case 2: return 1.1;
			case 3: return 1.2;
			case 4: return 1.25;
			case 5: return 1.25;
			default: return 1.5;
		}
	}
	
	function phaser(&$data,&$target,&$wd,$fleet=0,$strb=0)
	{
		$data['eps'] -= 1;
		if ($target['trumfield'] == 1) $msg .= "<b>Die ".$data['name']." feuert mit einer Strahlenwaffe (".$wd['name'].") auf das Trümmerfeld</b>";
		else $msg .= "<b>Die ".$data['name']." feuert mit einer Strahlenwaffe (".$wd['name'].") auf die ".$target['name']."</b>";

		// Schaden anhand des Basiswerts und der Varianz errechnen
		
		$minvari = round(($wd['strength']/100)*(100-$wd['varianz']));
		if ($minvari < 1) $minvari = 1;
		$maxvari = round(($wd['strength']/100)*(100+$wd['varianz']));
		if ($maxvari > 100) $maxvari = 100;
		
		$wd['pulse'] == 0 ? $ro = 1 : $ro = $wd['pulse'];
		$i=1;
		while($i<=$ro)
		{
			$dmg = rand($minvari,$maxvari);
			// Metreongas?
			if ($data['cfield'] == 4)
			{
				$mul = 1;
				// Tasche etwa auch noch?
				if ($this->db->query("SELECT type FROM stu_map_special WHERE type=4 AND ".($data['systems_id'] > 0 ? "systems_id=".$data['systems_id']." AND sx=".$data['sx']." AND sy=".$data['sy'] : "cx=".$data['cx']." AND cy=".$data['cy']." LIMIT 1"),1) > 0) $mul = rand(3,10);
				$result = $this->db->query("SELECT a.id,a.rumps_id,a.user_id,a.fleets_id,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.name,a.huelle,a.max_huelle,a.schilde,a.max_schilde,a.schilde_status,b.name as rname,b.is_shuttle,b.trumfield FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE b.trumfield='0' AND a.systems_id=".$data['systems_id']." AND ".($data['systems_id'] > 0 ? "a.sx=".$data['sx']." AND a.sy=".$data['sy'] : "a.cx=".$data['cx']." AND a.cy=".$data['cy'])."");
				while($dat=mysql_fetch_assoc($result))
				{
					$dmg = rand(10,25)*$mul;
					$pmm = "Eine Metreongasexplosion in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname."-System ".$data['cx']."|".$data['cy'].")" : $data['cx']."|".$data['cy'])." verursacht ".$dmg." Schaden an der ".$dat['name'];
					$arr = damageship($dmg,$dat['huelle'],$dat['schilde'],$dat['schilde_status']);
					if ($arr['huelle'] <= 0)
					{
						$this->trumfield($dat,"Anomalie (Metreongas-Explosion)");
						$pmm .= $arr['msg']."<br>-- Das Schiff wurde zerstört";
						if ($dat['id'] == $data['id']) $this->dsships[$dat['id']] = 1;
					}
					else
					{
						$this->db->query("UPDATE stu_ships SET huelle=".$arr['huelle'].",schilde=".$arr['schilde'].",schilde_status=".$arr['schilde_status'].",cloak='0' WHERE id=".$dat['id']." LIMIT 1");
						$pmm .= $arr['msg'];
					}
					$this->send_pm(1,$dat['user_id'],$pmm,3);
				}
				return "Eine Metreongas-Explosion richtet schwere Schäden an allen Schiffen in diesem Sektor an";
			}
			if ($ro > 1) $msg .= "<br>- Schuss ".$i.":";
			if (rand(1,100) > ($excep_nbs == 0 ? $data['treffer']-25 : $data['treffer']))
			{
				$msg .= "<br>- Der Schuss verfehlt sein Ziel!";
				$i++;
				continue;
			}			
			// Liegt ein kritischer Treffer vor?
			if (rand(1,100) <= $wd['critical'] && $target['trumfield'] != 1 && $target['user_id'] > 100)
			{
				$dmg *= 2;
				if ($target['schilde_status'] != 1 && rand(1,100) > (5 - 2*$target['huelle'] / ($target['m1c'] + $target['m2c'])) * 20)
				{
					$crit_msg = $this->damage_subsystem($target,$target['id']);
					if ($crit_msg) $crit = "<br>- ".$crit_msg;
				}
				if ($this->shoff == 1)
				{
					$target['schilde_status'] = 0;
					if ($target['id'] == $this->id) $this->schilde_status = 0;
					$this->shoff = 0;
				}
				if ($this->senoff == 1)
				{
					$target['nbs'] = 0;
					$target['lss'] = 0;
					if ($target['id'] == $this->id)
					{
						$this->lss = 0;
						$this->nbs = 0;
					}
				}
			}
			$pdmg += $dmg;
			$hit = 1;
			$rand = rand(1,100);
			$rand > 100-$wd['shields_through'] ? $ds = 1 : $ds = 0;
			if ($data['m6'] == 804)
			{
				$ck = 1;
				$dmg = 1;
			}
			if ($target['schilde_status'] == 1 && $ds == 0)
			{
				$dmgr = 0;
				$dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 AND (b.dmg_redu_wtype=".$wd['wtype']." OR b.dmg_redu_wtype=99) WHERE a.module_id=".$target['m2'],1);
				if ($dmgr == 0) $dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 AND (b.dmg_redu_wtype=".$wd['wtype']." OR b.dmg_redu_wtype=99) WHERE a.module_id=".$target['m2'],1);
				$dmge = 0;
				$dmge = $this->db->query("SELECT b.dmg_enh_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 WHERE a.module_id=".$data['m6'],1);
				if ($dmge == 0) $dmge = $this->db->query("SELECT b.dmg_enh_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 WHERE a.module_id=".$data['m6'],1);
				$dada = $dmg;
				if ($dmgr !=0 || $dmge != 0)
				{
					$dmg = round(($dmg/100)*(100-$dmgr+$dmge));
					$rb = 1;
				}
				$dada .= " - ".$dmg;
				$msg .= "<br>- Schildschaden: ".($target['schilde'] <= $dmg ? $target['schilde'] : $dmg);
				if ($target['schilde'] <= $dmg)
				{
					$dmg -= $target['schilde'];
					$target['schilde'] = 0;
					$target['schilde_status'] = 0;
					$msg .= "<br>-- Schilde brechen zusammen!";
				}
				else
				{
					$target['schilde'] -= $dmg;
					$dmg = 0;
					$msg .= " - Schilde bei ".$target['schilde'];
				}
				if ($dmg != 0 && $rb == 1) $dmg = round(($dmg/(100-$dmgr+$dmge))*100);
			}
			if (strlen(strip_tags($crit)) > 0)
			{
				$msg .= $crit;
				unset($crit);
			}
			if ($ro == $i && $dmg == 0) break;
			if ($dmg > 0 && $target['trumfield'] != 1)
			{
				if ($target['rumps_id'] == 9) $target['m1'] = 200; 
				$dmgr = 0;
				$dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 AND (b.dmg_redu_wtype=".$wd['wtype']." OR b.dmg_redu_wtype=99) WHERE a.module_id=".$target['m1'],1);
				if ($dmgr == 0) $dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 AND (b.dmg_redu_wtype=".$wd['wtype']." OR b.dmg_redu_wtype=99) WHERE a.module_id=".$target['m1'],1);
				if ($dmgr > 0)
				{
					$dmg = round(($dmg/100)*(100-$dmgr));
					$rb = 1;
				}
				$msg .= "<br>- Hüllenschaden: ".($target['huelle'] <= $dmg ? $target['huelle'] : $dmg);
				if ($target['huelle'] <= $dmg)
				{
					$msg .= "<br>-- Hüllenbruch! Das Schiff wurde zerstört";
					$target['huelle'] = 0;
					$this->trumfield($target,$data['name']);
					$destroy = 1;
					$this->dsships[$target['id']] = 1;
					break;
				}
				else
				{
					$target['huelle'] -= $dmg;
					$msg .= " - Hülle bei ".$target['huelle'];
				}
			}
			if ($dmg > 0 && $target['trumfield'] == 1)
			{
				$msg .= "<br>- Schaden am Trümmerfeld: ".($target['huelle'] <= $dmg ? $target['huelle'] : $dmg);
				if ($target['huelle'] <= $dmg)
				{
					$msg .= "<br>-- Das Trümmerfeld wurde beseitigt";
					$target['huelle'] = 0;
					$this->deletetrumfield($target);
					$destroy = 1;
					$this->dsships[$target['id']] = 1;
					$hit = 0;
					break;
				}
				else
				{
					$target['huelle'] -= $dmg;
					$msg .= " - Status: ".$target['huelle'];
				}
			}
			$i++;
		}
		if ($target['trumfield'] != 1 && $hit == 1)
		{
			if ($destroy == 1) $this->logbook[$data['id']][] = array("user_id" => $this->uid,"text" => "Das Schiff hat die ".$target['name']." zerstört","type" => 1);
			$this->logbook[$data['id']][] = array("user_id" => $this->uid,"text" => "Das Schiff feuert in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname."-System)" : $data['cx']."|".$data['cy'])." mit einem ".$wd['name']." auf die ".$target['name']." und richtet ".$pdmg." Schaden an","type" => 1);
			$this->logbook[$target['id']][] = array("user_id" => $target['user_id'],"text" => "Das Schiff wurde in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname."-System)" : $data['cx']."|".$data['cy'])." von der ".$data['name']." beschossen. Schaden: ".$pdmg.($destroy == 1 ? "<br>Das Schiff wurde zerstört" : ""),"type" => 1);
		}
		if ($data['user_id'] != $target['user_id'] && $target['user_id'] != 1 && $target['trumfield'] != 1) $pm = $msg;
		return array("msg" => $msg,"pm" => $pm);
	}
	
	function torpedo(&$data,&$target,&$wd,$fleet=0,$strb=0)
	{
		$data['torp_fire_amount'] = $this->db->query("SELECT torp_fire_amount FROM stu_modules WHERE module_id=".$data['m10']." LIMIT 1",1);
		$wd = $this->db->query("SELECT name,goods_id,damage,varianz,shields_through,critical FROM stu_torpedo_types WHERE torp_type=".$data['torp_type']." LIMIT 1",4);

		// Schaden anhand des Basiswerts und der Varianz errechnen
		$minvari = round(($wd['damage']/100)*(100-$wd['varianz']));
		if ($minvari < 1) $minvari = 1;
		$maxvari = round(($wd['damage']/100)*(100+$wd['varianz']));
		$dmg =rand($minvari,$maxvari);
		
		// Durchgang starten
		$i = 1;
		if ($data['torp_fire_amount'] > $data['eps']) $data['torp_fire_amount'] = $data['eps'];
		$tc = $this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$data['id']." AND goods_id=".$wd['goods_id']." LIMIT 1",1);
		if ($data['torp_fire_amount'] > $tc) $data['torp_fire_amount'] = $tc;
		while($i<=$data['torp_fire_amount'])
		{
			if ($target['fleets_id'] > 0)
			{
				$tar = $this->db->query("SELECT a.id,a.fleets_id,a.name,a.user_id,a.rumps_id,a.cx,a.cy,a.sx,a.sy,a.systems_id,a.cloak,a.alvl,a.eps,a.huelle,a.max_huelle,a.schilde,a.schilde_status,a.nbs,a.wea_phaser,a.wea_torp,a.crew,a.min_crew,a.fleets_id,a.phaser,a.torp_type,a.traktor,a.warp,a.traktormode,a.maintain,a.cfield,b.slots,b.trumfield,b.name as rname,b.is_shuttle,c.evade,c.m1,c.m2,c.m6 FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) LEFT JOIN stu_ships_buildplans as c ON a.plans_id=c.plans_id LEFT JOIN stu_ships_decloaked as d ON d.ships_id=a.id AND d.user_id=".$data['user_id']." AND UNIX_TIMESTAMP(d.date)=0 WHERE a.fleets_id=".$target['fleets_id']." AND (a.cloak='0' OR !ISNULL(d.user_id)) ORDER BY RAND() LIMIT 1",4);
				if ($tar != 0) $target = $tar;
			}
			$dmg = rand($minvari,$maxvari);
			
			if ($i != 1) $msg .= "<br>";
			$data['eps'] -= 1;
			if ($target['trumfield'] == 1) $msg .= "<b>Die ".$data['name']." feuert einen Torpedo (".$wd['name'].") auf das Trümmerfeld</b>";
			else $msg .= "<b>Die ".$data['name']." feuert einen Torpedo (".$wd['name'].") auf die ".$target['name']."</b>";
			$this->lowertorpedo($data['id'],$data['torp_type']);
			
			// Metreongas?
			if ($data['cfield'] == 4)
			{
				$mul = 1;
				// Tasche etwa auch noch?
				if ($this->db->query("SELECT type FROM stu_map_special WHERE type=4 AND ".($data['systems_id'] > 0 ? "sx=".$data['sx']." AND sy=".$data['sy'] : "cx=".$data['cx']." AND cy=".$data['cy'])." LIMIT 1",1) > 0) $mul = rand(3,10);
				$result = $this->db->query("SELECT a.id,a.rumps_id,a.user_id,a.fleets_id,a.systems_id,a.sx,a.sy,a.cx,a.cy,a.name,a.huelle,a.max_huelle,a.schilde,a.max_schilde,a.schilde_status,b.is_shuttle,b.name as rname FROM stu_ships as a LEFT JOIN stu_rumps as b USING(rumps_id) WHERE b.trumfield='0' AND a.systems_id=".$data['systems_id']." AND ".($data['systems_id'] > 0 ? "a.sx=".$data['sx']." AND a.sy=".$data['sy'] : "a.cx=".$data['cx']." AND a.cy=".$data['cy'])."");
				while($dat=mysql_fetch_assoc($result))
				{
					$dmg = rand(10,25)*$mul;
					$pmm = "Eine Metreongasexplosion in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname." System ".$data['cx']."|".$data['cy'].")" : $data['cx']."|".$data['cy'])." verursacht ".$dmg." Schaden an der ".$dat['name'];
					$arr = damageship($dmg,$dat['huelle'],$dat['schilde'],$dat['schilde_status']);
					if ($arr['huelle'] <= 0)
					{
						$this->trumfield($dat,"Anomalie (Metreongas-Explosion)");
						$pmm .= $arr['msg']."<br>-- Das Schiff wurde zerstört";
						if ($dat['id'] == $data['id']) $this->dsships[$dat['id']] = 1;
					}
					else
					{
						$this->db->query("UPDATE stu_ships SET huelle=".$arr['huelle'].",schilde=".$arr['schilde'].",schilde_status=".$arr['schilde_status'].",cloak='0' WHERE id=".$dat['id']." LIMIT 1");
						if ($dat['id'] == $target['id'])
						{
							$target['huelle'] = $arr['huelle'];
							$target['schilde'] = $arr['schilde'];
							$target['schilde_status'] = $arr['schilde_status'];
						}
						$pmm .= $arr['msg'];
					}
					$this->send_pm(1,$dat['user_id'],$pmm,3);
				}
				return "Eine Metreongas-Explosion richtet schwere Schäden an allen Schiffen in diesem Sektor an";
			}
			
			if (rand(1,100) < $target['evade'] && $target['trumfield'] != 1)
			{
				$msg .= "<br>- Der Torpedo verfehlt sein Ziel!";
				$i++;
				continue;
			}
			if (rand(1,100) <= $wd['critical'] && $target['trumfield'] != 1 && $target['user_id'] > 100)
			{
				$dmg *= 2;
				if ($target['schilde_status'] != 1 && rand(1,100) > (5 - 2*$target['huelle'] / ($target['m1c'] + $target['m2c'])) * 20)
				{
					$crit_msg = $this->damage_subsystem($target,$target['id']);
					if ($crit_msg) $crit = "<br>- ".$crit_msg;
				}
				if ($this->shoff == 1)
				{
					$target['schilde_status'] = 0;
					if ($target['id'] == $this->id) $this->schilde_status = 0;
				}
				if ($this->senoff == 1)
				{
					$target['nbs'] = 0;
					$target['lss'] = 0;
					if ($target['id'] == $this->id)
					{
						$this->lss = 0;
						$this->nbs = 0;
					}
				}
			}
			$pdmg += $dmg;
			$hit = 1;
			$rand = rand(1,100);
			$rand > 100-$wd['shields_through'] ? $ds = 1 : $ds = 0; 
			if ($target['schilde_status'] == 1 && $ds == 0 && $target['trumfield'] != 1)
			{
				$dmgr = 0;
				$dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 AND b.dmg_redu_wtype=99 WHERE a.module_id=".$target['m2'],1);
				if ($dmgr == 0) $dmgr = $this->db->query("SELECT b.dmg_redu_shields FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 AND b.dmg_redu_wtype=99 WHERE a.module_id=".$target['m2'],1);
				if ($dmgr > 0)
				{
					$dmg = round(($dmg/100)*(100-$dmgr));
					$rb = 1;
				}
				$msg .= "<br>- Schildschaden: ".($target['schilde'] <= $dmg ? $target['schilde'] : $dmg);
				if ($target['schilde'] <= $dmg)
				{
					$dmg -= $target['schilde'];
					$target['schilde'] = 0;
					$target['schilde_status'] = 0;
					$msg .= "<br>-- Schilde brechen zusammen!";
				}
				else
				{
					$target['schilde'] -= $dmg;
					$dmg = 0;
					$msg .= " - Schilde bei ".$target['schilde'];
				}
				if ($dmg > 0 && $rb == 1) $dmg = round(($dmg/3)*4);
			}
			if (strlen(strip_tags($crit)) > 0)
			{
				$msg .= $crit;
				unset($crit);
			}
			if ($dmg > 0  && $target['trumfield'] != 1)
			{
				if ($target['rumps_id'] == 9) $target['m1'] = 200; 
				$dmgr = 0;
				$dmgr = $this->db->query("SELECT b.dmg_redu_huell FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id1 AND b.dmg_redu_wtype=99 WHERE a.module_id=".$target['m1'],1);
				if ($dmgr == 0) $dmgr = $this->db->query("SELECT b.dmg_redu_huell FROM stu_modules as a LEFT JOIN stu_modules_special as b ON b.special_id=a.special_id2 AND b.dmg_redu_wtype=99 WHERE a.module_id=".$target['m1'],1);
				if ($dmgr > 0) $dmg = round(($dmg/100)*(100-$dmgr));
				$msg .= "<br>- Hüllenschaden: ".($target['huelle'] <= $dmg ? $target['huelle'] : $dmg);
				if ($target['huelle'] <= $dmg)
				{
					$msg .= "<br>-- Hüllenbruch! Das Schiff wurde zerstört";
					$target['huelle'] = 0;
					$this->trumfield($target,$data['name']);
					$destroy = 1;
					$this->dsships[$target['id']] = 1;
					$i++;
					break;
				}
				else
				{
					$target['huelle'] -= $dmg;
					$msg .= " - Hülle bei ".$target['huelle'];
				}
			}
			if ($target['trumfield'] == 1 && $dmg > 0)
			{
				if ($target['huelle'] <= $dmg)
				{
					$this->deletetrumfield($target);
					$msg .= "<br>Das Trümmerfeld wurde beseitigt";
					$hit = 0;
					$i++;
					break;
				}
				$target['huelle'] -= $dmg;
				$msg .= "<br>Schaden ".$dmg." - Status des Trümmerfeldes ".$target['huelle'];
			}
			if (!$this->dsships[$target['id']]) $this->write_damage($target);
			$i++;
		}
		if ($hit == 1)
		{
			if ($destroy == 1) $this->logbook[$data['id']][] = array("user_id" => $this->uid,"text" => "Das Schiff hat die ".$target['name']." zerstört","type" => 1);
			$this->logbook[$data['id']][] = array("user_id" => $this->uid,"text" => "Das Schiff feuert in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname."-System)" : $data['cx']."|".$data['cy'])." mit Torpedos (".$wd['name'].") auf die ".$target['name']." und richtet ".$pdmg." Schaden an","type" => 1);
			$this->logbook[$target['id']][] = array("user_id" => $target['user_id'],"text" => "Das Schiff wurde in Sektor ".($data['systems_id'] > 0 ? $data['sx']."|".$data['sy']." (".$this->m->sysname."-System)" : $data['cx']."|".$data['cy'])." von der ".$data['name']." beschossen. Schaden: ".$pdmg.($destroy == 1 ? "<br>Das Schiff wurde zerstört" : ""),"type" => 1);
		}
		if ($data['user_id'] != $target['user_id'] && $target['user_id'] != 1) $pm = $msg;
		return array("msg" => $msg,"pm" => $pm);
	}


	function lowertorpedo($shipId,$torp)
	{
		$data = $this->db->query("SELECT a.goods_id,b.count FROM stu_torpedo_types as a LEFT JOIN stu_ships_storage as b ON a.goods_id=b.goods_id AND b.ships_id=".$shipId." WHERE a.torp_type=".$torp." LIMIT 1",4);
		if ($data == 0)
		{
			$this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=".$shipId." LIMIT 1");
			return;
		}
		$this->lowerstorage($shipId,$data[goods_id],1);
		if ($data['count'] == 1) $this->db->query("UPDATE stu_ships SET torp_type=0,wea_torp='0' WHERE id=".$shipId." LIMIT 1");
		return;
	}

	function checksubsystem($system,$shipId) { return $this->db->query("SELECT COUNT(*) FROM stu_ships_subsystems WHERE ships_id=".$shipId." AND system_id=".$system,1); }

	function damage_subsystem($ship,$shipId,$sys="")
	{
		if ($this->uid < 100) return;
		if ($shipId == 1) return;
		if (!check_int($sys))
		{
			$systems = generatesubsystemlistbyarr($ship,$shipId);
			$index = $systems[array_rand($systems)];
			$sys = $index;
		}
		else $index = $sys;
		switch($index)
		{
			case 2:
				$dt = rand(3600,7200);
				$this->db->query("UPDATE stu_ships SET schilde_status=0 WHERE id=".$shipId." LIMIT 1");
				$this->shoff = 1;
				break;
			case 4:
				$dt = rand(1800,5400);
				$this->db->query("UPDATE stu_ships SET nbs=NULL,lss=NULL WHERE id=".$shipId." LIMIT 1");
				$this->senoff = 1;
				break;
			case 5:
				$dt = rand(3600,18000);
				break;
			case 6:
				$dt = rand(1800,7200);
				$this->db->query("UPDATE stu_ships SET wea_phaser='0' WHERE id=".$shipId." LIMIT 1");
				break;
			case 7:
				$dt = rand(1800,5400);
				break;
			case 8:
				$dt = rand(600,1800);
				break;
			case 9:
				$dt = rand(3600,36000);
				$this->db->query("UPDATE stu_ships SET cloak='0' WHERE id=".$shipId." LIMIT 1");
				break;
			case 10:
				$dt = rand(1800,7200);
				$this->db->query("UPDATE stu_ships SET wea_torp='0' WHERE id=".$shipId." LIMIT 1");
				break;
			case 11:
				$dt = rand(3600,86400);
				$this->db->query("UPDATE stu_ships SET warp='0' WHERE id=".$shipId." LIMIT 1");
				break;
		}
		$this->dmsubs[$shipId][$index] = 1;
		if ($this->db->query("SELECT b.race FROM stu_ships as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=".$shipId,1) == 1) $dt = round($dt/2);
		$this->db->query("INSERT INTO stu_ships_subsystems (ships_id,system_id,date) VALUES ('".$shipId."','".$index."','".(time()+$dt)."')");
		return "System (".getmodtypedescr($index).")".(!check_int($sys) ? " ist aufgrund der überfälligen Wartung ausgefallen" : " wurde beschädigt")." - Voraussichtliche Reparaturdauer: ".gen_time($dt);
	}

	function dockRightFriends($fdr)
	{
		$this->db->query("DELETE FROM stu_dockingrights WHERE ships_id=".$this->id." AND type='4' LIMIT 1");
		if ($fdr == 1) $this->db->query("INSERT INTO stu_dockingrights (ships_id,type) VALUES ('".$this->id."','4')");
		return "Andockregeln für Freunde ".($fdr == 1 ? "erstellt" : "gelöscht");
	}

	function newDockRight($target,$type,$mode)
	{
		if ($type < 1 || $type > 3) return;
		if ($mode != 1 && $mode != 2) return;
		if ($this->db->query("SELECT id FROM stu_dockingrights WHERE ships_id=".$this->id." AND id=".$target." AND type='".$type."' LIMIT 1",1) > 0) return "Es gibt bereits einen Eintrag mit dieser ID";
		if ($type ==1)
		{
			if ($this->db->query("SELECT id FROM stu_ships WHERE id=".$target." AND user_id!=".$this->uid." LIMIT 1",1) == 0) return "Kein Schiff mit dieser ID vorhanden";
			$this->db->query("INSERT INTO stu_dockingrights (ships_id,id,type,mode) VALUES ('".$this->id."','".$target."','".$type."','".$mode."')");
			return "Andockregel für das Schiff erstellt";
		}
		if ($type ==2)
		{
			if ($this->db->query("SELECT id FROM stu_user WHERE id=".$target." AND id!=".$this->uid." LIMIT 1",1) == 0) return "Kein Siedler mit dieser ID vorhanden";
			$this->db->query("INSERT INTO stu_dockingrights (ships_id,id,type,mode) VALUES ('".$this->id."','".$target."','".$type."','".$mode."')");
			return "Andockregel für den Siedler erstellt";
		}
		if ($type ==3)
		{
			if ($this->db->query("SELECT allys_id FROM stu_allylist WHERE allys_id=".$target." LIMIT 1",1) == 0) return "Keine Allianz mit dieser ID vorhanden";
			$this->db->query("INSERT INTO stu_dockingrights (ships_id,id,type,mode) VALUES ('".$this->id."','".$target."','".$type."','".$mode."')");
			return "Andockregel für die Allianz erstellt";
		}
	}

	function delDockRight($target,$type)
	{
		$result = $this->db->query("DELETE FROM stu_dockingrights WHERE ships_id=".$this->id." AND id=".$target." AND type=".$type." LIMIT 1",6);
		if ($result == 0) return;
		return "Andockregel gelöscht";
	}

 	function get_kss_sensordata() { return $this->db->query("SELECT a.ships_id,a.user_id,a.rumps_id,b.user,b.allys_id,c.mode,d.type FROM stu_sectorflights as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_contactlist as c ON c.user_id=".$this->uid." AND c.recipient=a.user_id LEFT JOIN stu_ally_relationship as d ON ((d.allys_id1=a.allys_id AND d.allys_id2=".$this->sess['allys_id'].") OR (d.allys_id2=a.allys_id AND d.allys_id1=".$this->sess['allys_id'].")) WHERE a.systems_id=".$this->systems_id." AND a.cloak='0' AND a.sx BETWEEN ".($this->sx-$this->lss_range)." AND ".($this->sx+$this->lss_range)." AND a.sy BETWEEN ".($this->sy-$this->lss_range)." AND ".($this->sy+$this->lss_range)." AND a.user_id!=".$this->uid." GROUP BY a.ships_id ORDER BY a.ships_id"); }

	function get_kss_sensor_detail($shipId) { return $this->db->query("SELECT cx,cy,sx,sy,UNIX_TIMESTAMP(date) as date_tsp FROM stu_sectorflights WHERE ships_id=".$shipId." AND systems_id=".$this->systems_id." AND cloak='0' AND sx BETWEEN ".($this->sx-$this->lss_range)." AND ".($this->sx+$this->lss_range)." AND sy BETWEEN ".($this->sy-$this->lss_range)." AND ".($this->sy+$this->lss_range)." ORDER BY date DESC,fieldcount DESC LIMIT 1",4); }

	function get_kss_enemy_sensordata() { return $this->db->query("SELECT a.sx,a.sy,a.type,COUNT(b.ships_id) as sc FROM stu_sys_map as a LEFT JOIN stu_sectorflights as b ON b.systems_id=a.systems_id AND b.sx=a.sx AND b.sy=a.sy AND b.cloak='0' AND b.user_id!=".$this->uid." WHERE a.systems_id=".$this->systems_id." AND a.sx BETWEEN ".($this->sx-$this->lss_range)." AND ".($this->sx+$this->lss_range)." AND a.sy BETWEEN ".($this->sy-$this->lss_range)." AND ".($this->sy+$this->lss_range)." GROUP BY a.sx,a.sy ORDER BY a.sy,a.sx"); }

	function get_lss_sensordata() { return $this->db->query("SELECT a.ships_id,a.user_id,a.rumps_id,b.user,b.allys_id,c.mode,d.type FROM stu_sectorflights as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_contactlist as c ON c.user_id=".$this->uid." AND c.recipient=a.user_id LEFT JOIN stu_ally_relationship as d ON ((d.allys_id1=a.allys_id AND d.allys_id2=".$this->sess['allys_id'].") OR (d.allys_id2=a.allys_id AND d.allys_id1=".$this->sess['allys_id'].")) WHERE a.systems_id=0 AND a.cloak='0' AND a.cx BETWEEN ".($this->cx-$this->lss_range)." AND ".($this->cx+$this->lss_range)." AND a.cy BETWEEN ".($this->cy-$this->lss_range)." AND ".($this->cy+$this->lss_range)." AND a.user_id!=".$this->uid." GROUP BY a.ships_id ORDER BY a.ships_id"); }

	function get_lss_sensor_detail($shipId) { return $this->db->query("SELECT cx,cy,sx,sy,UNIX_TIMESTAMP(date) as date_tsp FROM stu_sectorflights WHERE ships_id=".$shipId." AND systems_id=0 AND cloak='0' AND cx BETWEEN ".($this->cx-$this->lss_range)." AND ".($this->cx+$this->lss_range)." AND cy BETWEEN ".($this->cy-$this->lss_range)." AND ".($this->cy+$this->lss_range)." ORDER BY date DESC,fieldcount DESC LIMIT 1",4); }

	function get_lss_enemy_sensordata() { return $this->db->query("SELECT a.cx,a.cy,a.type,a.faction_id,a.is_border,b.color,COUNT(c.ships_id) as sc FROM stu_map as a LEFT JOIN stu_factions as b ON b.faction_id=a.faction_id LEFT JOIN stu_sectorflights as c ON c.systems_id=0 AND c.cx=a.cx AND c.cy=a.cy AND c.cloak='0' AND c.user_id!=".$this->uid." WHERE a.cx BETWEEN ".($this->cx-$this->lss_range)." AND ".($this->cx+$this->lss_range)." AND a.cy BETWEEN ".($this->cy-$this->lss_range)." AND ".($this->cy+$this->lss_range)." GROUP BY a.cx,a.cy ORDER BY a.cy,a.cx"); }

	function dedock($target)
	{
		$data = $this->db->query("SELECT id,user_id,name,dock FROM stu_ships WHERE id=".$target." LIMIT 1",4);
		if ($data == 0) return;
		if ($this->id != $data['dock']) return;
		$return = shipexception(array("eps" => 1,"crew" => $this->min_crew),$this);
		if ($return['code'] == 1) return $return['msg'];
		
		$this->db->query("UPDATE stu_ships SET dock=0 WHERE id=".$data['id']." LIMIT 1");
		$this->db->query("UPDATE stu_ships SET eps=eps-1 WHERE id=".$this->id." LIMIT 1");
		if ($this->uid != $data['user_id']) $this->send_pm($this->uid,$data['user_id'],"Die ".$data['name']." wurde von der ".$this->name." abgedockt",3);
		else $add = "<br>->> <a href=?p=ship&s=ss&id=".$data['id'].">Zur ".$data['name']." wechseln</a>";
		return "Die ".$data['name']." wurde abgedockt".$add;	
	}
	
	function getshuttles() { return $this->db->query("SELECT a.goods_id,a.count,b.rumps_id,b.name FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE b.rumps_id>0 AND a.ships_id=".$this->id." ORDER BY b.rumps_id"); }

	function getoldshuttles() { return $this->db->query("SELECT a.goods_id,a.count,b.rumps_id,b.name FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE b.rumps_id=0 AND a.ships_id=".$this->id." ORDER BY b.rumps_id"); }

	function launchshuttle($rump)
	{
		$shu = $this->db->query("SELECT rumps_id,plans_id,shuttle_type,goods_id,name FROM stu_shuttle_types WHERE rumps_id>0 AND rumps_id=".$rump." LIMIT 1",4);
		if ($shu == 0) return;
		if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id=".$shu['goods_id']." LIMIT 1",1) == 0) return;
		$data = $this->db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id=".$shu['plans_id']." LIMIT 1",4);
		if ($data == 0) return;
		if ($this->uid > 100 && $this->warpcore < $data['wkkap']) return "Zum Start des Shuttles wird eine Warpkernladung von mindestens ".$data['wkkap']." benötigt";
		$return = shipexception(array("schilde_status" => 0,"cloak" => 0,"warpstate" => 0,"eps" => ($data['reaktor']+1),"crew" => $this->min_crew),$this);
		if ($return['code'] == 1) return $return['msg'];
		$rump = $this->db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$shu['rumps_id'],4);
		if ($rump['min_crew'] > $this->crew-$this->min_crew) return "Zum Start des Shuttles werden mindestens ".$rump[min_crew]." freie Crewmitglieder benötigt";
		if ($rump['warpable'] != 1 && $this->systems_id == 0) return "Dieser Shuttletyp kann nur innerhalb von Systemen gestartet werden";
		$i=1;
		while($i<=11)
		{
			if ($data["m".$i] == 0)
			{
				$i++;
				continue;
			}
			$dat = $this->db->query("SELECT * FROM stu_modules WHERE module_id=".$data["m".$i]." LIMIT 1",4);
			$huelle += $dat['huelle']*$rump["m".$i."c"];
			$schilde += $dat['schilde']*$rump["m".$i."c"];
			$eps += $dat['eps']*$rump["m".$i."c"];
			if ($i == 4)
			{
				$lss = $dat['lss']+($rump["m".$dat['type']."c"] - 1);
				$kss = $dat['kss']+($rump["m".$dat['type']."c"] - 1);
			}
			if ($i == 6)
			{
				$weapon = $this->getweaponbyid($data["m".$i]);
				$phaser = round($weapon['strength'] * (1 + (log($rump["m".$i."c"]) / log(2))/3));
				$vari = $weapon['varianz'];
			}
			if ($dat['stellar'] == 1) $stellar = 1;
			$i++;
		}
		if ($data['m5'] > 0 && $data['m11'] > 0) $warp = 1;
		if ($data['m9'] > 0) $cloak = 1; 
		$batt = $rump["m8c"]*2;
		$this->lowerstorage($this->id,$shu[goods_id],1);
		$sn = $this->db->query("SELECT ships_id,shuttle_id,name FROM stu_ships_shuttles WHERE ships_id=".$this->id." LIMIT 1",4);
		if ($sn != 0)
		{
			$id = $sn['shuttle_id'];
			$this->db->query("INSERT INTO stu_ships (id,user_id,rumps_id,plans_id,systems_id,cx,cy,sx,sy,direction,name,warpcore,warpable,cloakable,max_eps,max_batt,huelle,max_huelle,max_schilde,lss_range,kss_range,max_crew,min_crew,phaser,cfield,points,lastmaintainance) VALUES ('".$sn['shuttle_id']."','".$this->uid."','".$data[rumps_id]."','".$data[plans_id]."',".($this->systems_id > 0 ? "'".$this->systems_id."','".$this->cx."','".$this->cy."','".$this->sx."','".$this->sy."'" : "'0','".$this->cx."','".$this->cy."','0','0'").",'1','".addslashes($sn['name'])."','".($data[wkkap])."','".($warp == 1 ? 1 : '')."','".($cloak == 1 ? 1 : '')."','".$eps."','".$batt."','".$huelle."','".$huelle."','".$schilde."','".$lss."','".$kss."','".$rump[max_crew]."','".$rump[min_crew]."','".$phaser."','".$this->cfield."','0','".time()."')",5);
			$this->db->query("DELETE FROM stu_ships_shuttles WHERE shuttle_id=".$sn['shuttle_id']." AND ships_id=".$sn['ships_id']." LIMIT 1");
		}
		else $id = $this->db->query("INSERT INTO stu_ships (user_id,rumps_id,plans_id,systems_id,cx,cy,sx,sy,direction,name,warpcore,warpable,cloakable,max_eps,max_batt,huelle,max_huelle,max_schilde,lss_range,kss_range,max_crew,min_crew,phaser,cfield,points,lastmaintainance) VALUES ('".$this->uid."','".$data[rumps_id]."','".$data[plans_id]."',".($this->systems_id > 0 ? "'".$this->systems_id."','".$this->cx."','".$this->cy."','".$this->sx."','".$this->sy."'" : "'0','".$this->cx."','".$this->cy."','0','0'").",'1','".addslashes("Shuttle der ".stripslashes($this->name))."','".($data[wkkap])."','".($warp == 1 ? 1 : '')."','".($cloak == 1 ? 1 : '')."','".$eps."','".$batt."','".$huelle."','".$huelle."','".$schilde."','".$lss."','".$kss."','".$rump[max_crew]."','".$rump[min_crew]."','".$phaser."','".$this->cfield."','0','".time()."')",5);
		$this->db->query("UPDATE stu_ships SET eps=".$data['reaktor'].",crew=".$rump['min_crew']." WHERE id=".$id." LIMIT 1");
		if ($this->uid < 101) $data['wkkap'] = 0;
		$this->db->query("UPDATE stu_ships SET eps=eps-".($data['reaktor']+1).",crew=crew-".$rump['min_crew'].",warpcore=warpcore-".$data['wkkap']." WHERE id=".$this->id." LIMIT 1");
		return $rump[name]." gestartet";
	}
	
	function landshuttle ($target)
	{
		global $_GET;
		$_GET['id'] = $target['id'];
		if ($this->is_shuttle != 1) return;
		$return = shipexception(array("eps" => 2,"traktor" => 0,"nbs" => 1,"crew" => $this->min_crew),$this);
		if ($return[code] == 1) return $return['msg'];
		$target = $this->db->query("SELECT a.id,a.name,a.user_id,a.cx,a.cy,a.systems_id,a.sx,a.sy,a.schilde_status,a.cloak,a.warp,a.eps,a.max_eps,a.warpcore,a.crew,a.max_crew,b.storage,b.is_shuttle,b.max_shuttles,b.max_shuttle_type,b.max_cshuttle_type,c.wkkap FROM stu_ships as a LEFT JOIN stu_rumps as b ON b.rumps_id=a.rumps_id LEFT JOIN stu_ships_buildplans as c ON c.plans_id=a.plans_id WHERE a.id=".$target." LIMIT 1",4);
		if ($target['user_id'] != $this->uid) return;
		if ($target == 0) return;
		if (checksector($target) == 0) return;
		$return = shipexception(array("warpstate" => 0,"schilde_status" => 0,"cloak" => 0,"crew" => $target['min_crew']),$target);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->shuttle_type > $target['max_shuttle_type']) return "Dieser Shuttle-Typ kann auf diesem Schiff nicht landen";
		if ($target['crew'] + $this->crew > $target['max_crew']) return "Es sind nicht genügend Crewquartiere auf dem Schiff vorhanden";
		$stor_sum = $this->db->query("SELECT SUM(count) FROM stu_ships_storage WHERE ships_id=".$target['id'],1);
		$shu = $this->db->query("SELECT goods_id,converts_to FROM stu_shuttle_types WHERE rumps_id=".$this->rumps_id." LIMIT 1",4);
		if ($target['storage'] <= $stor_sum) return "Kein freier Laderaum vorhanden";
		if ($target['is_shuttle'] == 1) return "Auf einem Shuttle kann kein Shuttle gelandet werden";
		if ($target['max_shuttles'] == 0) return "Dieses Schiff kann keine Shuttles aufnehmen";
		if ($target['max_shuttles'] <= $this->db->query("SELECT SUM(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$target[id]." AND !ISNULL(b.shuttle_type)",1)) return "Die Shuttlerampe ist belegt";
		if ($target['max_cshuttle_type'] <= $this->db->query("SELECT COUNT(a.count) FROM stu_ships_storage as a LEFT JOIN stu_shuttle_types as b USING(goods_id) WHERE a.ships_id=".$target[id]." AND !ISNULL(b.shuttle_type) AND a.goods_id!=".$shu[goods_id],1)) return "Die Maximalzahl an ladbaren Shuttletypen wurde erreicht";
		$stor_sum++;
		$result = $this->db->query("SELECT goods_id,count FROM stu_ships_storage WHERE ships_id=".$this->id);
		while($data=mysql_fetch_assoc($result))
		{
			if ($stor_sum + $data['count'] > $target['storage'])
			{
				$data['count'] = $target['storage']-$stor_sum;
				$this->upperstorage($target['id'],$data['goods_id'],$data['count']);
				break;
			}
			$stor_sum += $data['count'];
			$this->upperstorage($target['id'],$data['goods_id'],$data['count']);
		}
		if ($this->warpcore + $target['warpcore'] > $target['wkkap']) $this->warpcore = $target['wkkap']-$target['warpcore'];
		if ($this->eps-2 + $target['eps'] > $target['max_eps']) $this->eps = $target['max_eps']-$target['eps'];
		if ($this->eps > $target['max_eps'] - $target['eps']) $this->eps = $target['max_eps'] - $target['eps'];
		$this->db->query("UPDATE stu_ships SET eps=eps+".$this->eps.",warpcore=warpcore+".$this->warpcore.",crew=crew+".$this->crew." WHERE id=".$target['id']." LIMIT 1"); 
		$this->upperstorage($target['id'],$shu['converts_to'],1);
		$this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$this->id);
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE ships_id=".$this->id." LIMIT 1");
		$this->db->query("DELETE FROM stu_ships_subsystems WHERE ships_id=".$this->id);
		$this->db->query("DELETE FROM stu_ships WHERE id=".$this->id." LIMIT 1");
		$this->db->query("INSERT INTO stu_ships_shuttles (ships_id,shuttle_id,name) VALUES ('".$target['id']."','".$this->id."','".addslashes($this->name)."')");
		$_GET['a'] = "";
		return "Die ".$this->name." ist auf der ".$target['name']." gelandet";
	}

	function maintainshuttle($good)
	{
		$shu = $this->db->query("SELECT rumps_id,plans_id,shuttle_type,goods_id,name,converts_to,maintain_epscost FROM stu_shuttle_types WHERE rumps_id=0 AND goods_id=".$good." LIMIT 1",4);
		if ($shu == 0) return;
		if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$this->id." AND goods_id=".$shu['goods_id']." LIMIT 1",1) == 0) return;
		$return = shipexception(array("eps" => $shu['maintain_epscost'],"crew" => $this->min_crew),$this);
		if ($return[code] == 1) return $return['msg'];
		$this->lowerstorage($this->id,$shu['goods_id'],1);
		$this->upperstorage($this->id,$shu['converts_to'],1);
		$this->db->query("UPDATE stu_ships SET eps=eps-".$shu['maintain_epscost']." WHERE id=".$this->id." LIMIT 1");
		return "Ein Shuttle wurde gewartet";
	}

	function docktraktorship()
	{
		$return = shipexception(array("eps" => 1,"nbs" => 1,"crew" => $this->min_crew),$this);
		if ($return[code] == 1) return $return['msg'];
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships WHERE dock=".$this->id,1) >= $this->slots) return "Es sind bereits alle Dockplätze belegt";
		$data = $this->db->query("SELECT id,user_id,name,fleets_id FROM stu_ships WHERE id=".$this->traktor." AND traktormode='2' LIMIT 1",4);
		if ($data == 0) return;
		if ($data['fleets_id'] > 0) return "Schiffe aus einer Flotte können nicht angedockt werden";
		$this->db->query("UPDATE stu_ships SET dock=".$this->id.",traktor=0,traktormode='0' WHERE id=".$this->traktor." LIMIT 1");
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode='0',eps=eps-1 WHERE id=".$this->id." LIMIT 1");
		if ($this->uid != $data['user_id']) $this->send_pm($this->uid,$data['user_id'],"Die ".$this->name." hat die ".$data['name']." mit Hilfe des Traktorstrahls angedockt",3);
		return "Die ".stripslashes($data['name'])." wurde angedockt";
	}

	function getPossibleStations() { return $this->db->query("SELECT a.rumps_id,a.name FROM stu_rumps as a LEFT JOIN stu_rumps_user as b ON a.rumps_id=b.rumps_id AND b.user_id=".$this->uid." WHERE (b.rumps_id>=10 AND b.rumps_id<=14) OR b.rumps_id=9001 OR b.rumps_id=9002 OR b.rumps_id=9003 OR b.rumps_id=9004 OR b.rumps_id=9005"); }

	function getmodbylvl($minlvl,$maxlvl,$type) { return $this->db->query("SELECT a.module_id,a.special_id1,a.special_id2,a.type,a.level,a.name,a.huelle,a.schilde,a.reaktor,a.wkkap,a.eps,a.evade_val,a.lss,a.kss,cloak_val,detect_val,a.hit_val,a.torps,a.points,a.warp_capability,a.buildtime,a.maintaintime,b.count FROM stu_modules as a LEFT JOIN stu_ships_storage as b ON a.module_id=b.goods_id AND b.ships_id=".$this->id." LEFT JOIN stu_researched as c ON a.research_id=c.research_id AND c.user_id=".$this->uid." WHERE a.type=".$type." AND a.level>=".$minlvl." AND a.level<=".$maxlvl." AND (a.viewable='1' OR !ISNULL(b.count)) AND (a.research_id=0 OR (a.research_id>0 AND (!ISNULL(c.user_id) OR !ISNULL(b.count)))) ORDER BY a.type,a.level"); }

	function getmodbyid($minlvl,$maxlvl,$type,$id) { return $this->db->query("SELECT a.module_id,a.special_id1,a.special_id2,a.type,a.level,a.name,a.huelle,a.schilde,a.reaktor,a.wkkap,a.eps,a.evade_val,a.cloak_val,a.detect_val,a.hit_val,a.lss,a.kss,a.torps,a.warp_capability,a.points,a.buildtime,a.maintaintime,b.count FROM stu_modules as a LEFT JOIN stu_ships_storage as b ON a.module_id=b.goods_id AND b.ships_id=".$this->id." LEFT JOIN stu_researched as c ON a.research_id=c.research_id AND c.user_id=".$this->uid." WHERE a.type=".$type." AND a.level>=".$minlvl." AND a.level<=".$maxlvl." AND (a.viewable='1' OR !ISNULL(b.count)) AND (a.research_id=0 OR (a.research_id>0 AND (!ISNULL(c.user_id) OR !ISNULL(b.count)))) AND a.module_id=".$id,4); }

	function getpossiblebuildplans()
	{
		global $_GET;
		return $this->db->query("SELECT a.plans_id,a.name,COUNT(b.id) as idc FROM stu_ships_buildplans as a LEFT JOIN stu_ships as b ON a.plans_id=b.plans_id
			WHERE a.user_id=".$this->uid." AND a.rumps_id=".$_GET["stat"]." AND a.m1='".$_GET[m1]."' AND a.m2='".$_GET[m2]."' AND a.m3='".$_GET[m3]."' AND
			 a.m4='".$_GET[m4]."' AND a.m5='".$_GET[m5]."' AND a.m6='".$_GET[m6]."' AND a.m7='".$_GET[m7]."' AND a.m8='".$_GET[m8]."' AND
			 a.m9='".$_GET[m9]."' AND a.m10='".$_GET[m10]."' AND a.m11='".$_GET[m11]."' GROUP BY a.plans_id LIMIT 1",4);
	}

	function buildstation()
	{
		global $_GET,$col;
		$this->rump = $col->rump;
		if ($this->sess['wpo'] > 0) return "Stationsbau ist noch für ".$this->sess['wpo']." Runde(n) gesperrt";
		if ($this->eps < $this->rump['eps_cost']) return "Zum Stationsbau werden ".$this->rump['eps_cost']." Energie benötigt - Vorhanden sind nur ".$this->eps;
		if ($this->db->query("SELECT COUNT(*) FROM stu_ships_buildprogress WHERE ships_id=".$this->id." AND user_id=".$this->uid,1) != 0) return "Es wird bereits eine Station gebaut";
		if ($this->db->query("SELECT id FROM stu_ships WHERE dock=".$this->id." AND rumps_id=5 AND user_id=".$this->uid,1) == 0) return "Um die Station zu bauen muss ein Workbee angedockt sein";
		if ($this->uid > 100 && $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE rumps_id=".$this->rump['rumps_id']." AND user_id=".$this->uid,1) >= 3) return "Es können nur 3 Stationen dieses Typs gebaut werden";
		if (($this->rump['id'] == 9001 || $this->rump['id'] == 9002 || $this->rump['id'] == 9003 || $this->rump['id'] == 9004 || $this->rump['id'] == 9005) && $this->db->query("SELECT COUNT(*) FROM stu_ships WHERE (rumps_id=9001 OR rumps_id=9002 OR rumps_id=9003 OR rumps_id=9004 OR rumps_id=9005) AND user_id=".$this->uid,1) >= 3) return "Es können nur 3 Versorgungsposten gebaut werden";
		if ($this->rump['m5c'] == 0 && check_int($_GET['m5']) && $_GET['m5'] != 0) die(show_error(902));
		$i = 1;
		while($i <= 10)
		{
			if (($i == 9 || $i == 10 || $i == 7 || $i == 5 || $i == 6) && (!check_int($_GET["m".$i]) || $_GET["m".$i] == 0))
			{
				$i++;
				continue;
			}
			if (!check_int($_GET["m".$i])) return "Es wurde kein Modul des Typs ".getmodtypedescr($i)." ausgewählt";
			$mod = $this->getmodbyid($this->rump["m".$i."minlvl"],$this->rump["m".$i."maxlvl"],$i,$_GET["m".$i]);
			if ($mod == 0) die(show_error(902));
			$bm[] = $mod;
			if ($mod['count'] < $this->rump["m".$i."c"]) return "Es werden ".$this->rump["m".$i."c"]." ".$mod[name]." Module benötigt - Vorhanden sind nur ".(!$mod['count'] ? 0 : $mod['count']);
			$i++;
			$mc[] = array("goods_id" => $mod[module_id],"type" => $mod[type]);
		}
		$plan = $this->getpossiblebuildplans();
		if ($plan == 0)
		{
			!$_GET[npn] ? $pn = $this->rump[name]." ".date("d.m.Y H:i") : $pn = $_GET[npn];
			$id = $this->db->query("INSERT INTO stu_ships_buildplans (rumps_id,user_id,name,m1,m2,m3,m4,m5,m6,m7,m8,m9,m10,m11) VALUES ('".$this->rump[rumps_id]."','".$this->uid."','".$pn."','".$_GET[m1]."','".$_GET[m2]."','".$_GET[m3]."','".$_GET[m4]."','".$_GET[m5]."','".$_GET[m6]."','".$_GET[m7]."','".$_GET[m8]."','".$_GET[m9]."','".$_GET[m10]."','".$_GET[m11]."')",5);
		}
		else $id = $plan[plans_id];
		$result = $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_ships_storage as c ON a.goods_id=c.goods_id AND c.ships_id=".$this->id." WHERE a.rumps_id=".$_GET["stat"]);
		while($cost=mysql_fetch_assoc($result))
		{
			if ($cost[vcount] < $cost['count'])
			{
				return "Es werden ".$cost['count']." ".$cost[name]." benötigt - Vorhanden sind nur ".(!$cost[vcount] ? 0 : $cost[vcount]);
			}
		}
		foreach($bm as $key => $data)
		{
			$huelle += $data[huelle]*$this->rump["m".$data[type]."c"];
			$bz += $data[buildtime];
			$points += $data[points];
			$schilde += $data[schilde]*$this->rump["m".$data[type]."c"];
			$main += $data[maintaintime];
			$reaktor += $data[reaktor]*$this->rump["m".$data[type]."c"];
			$wkkap += $data[wkkap]*$this->rump["m".$data[type]."c"];
			$eps += $data['eps']*$this->rump["m".$data[type]."c"];
			$evade += $data[evade_val];
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
			if ($data['type'] == 3 && $data['special_id1'] > 0)
			{
				$rea_mul = $this->db->query("SELECT reaktor_multiplier FROM stu_modules_special WHERE special_id=".$data['special_id1']." LIMIT 1",1);
			}
		}
		if (!$weapon)
		{
			$vari = 0;
			$phaser = 0;
		}
		$points = round(($this->rump[wp] * $points) / 10,1);
		$wp_v = $this->db->query("SELECT SUM(lastrw) FROM stu_colonies WHERE user_id=".$this->uid,1)-$this->db->query("SELECT SUM(points) FROM stu_ships WHERE user_id=".$this->uid,1);
		if ($points > $wp_v && $this->uid > 100)
		{
			return "Es werden ".$points." Wirtschaftspunkte benötigt - Vorhanden sind nur ".($wp_v < 0 ? 0 : $wp_v);
		}
		$result = $this->db->query("SELECT a.goods_id,a.count,b.name,c.count as vcount FROM stu_rumps_buildcost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_ships_storage as c ON a.goods_id=c.goods_id AND c.ships_id=".$this->id." WHERE a.rumps_id=".$_GET["stat"]);
		while($cost=mysql_fetch_assoc($result))
		{
			$this->lowerstorage($this->id,$cost[goods_id],$cost['count']);
		}
		foreach($mc as $key => $cost) $this->lowerstorage($this->id,$cost[goods_id],$this->rump["m".$cost[type]."c"]);
		$reaktor += $this->rump[reaktor];
		$batt = $this->rump["m8c"]*2;
		$evade = $this->rump[evade_val];
		if ($rea_mul) $reaktor = round($reaktor*$rea_mul);
		$main = 0;
		$bz += $this->rump["buildtime"];
		$time = time()+$bz;
		if (check_int($_GET[m9])) $cloak = 1;
		if (check_int($_GET[m5]) && check_int($_GET[m11])) $warp = 1;
		$this->db->query("INSERT INTO stu_ships_buildprogress (ships_id,user_id,rumps_id,plans_id,buildtime,huelle,schilde,warpable,cloakable,phaser,eps,lss,kss,points) VALUES ('".$this->id."','".$this->uid."','".$this->rump[rumps_id]."','".$id."','".$time."','".round($huelle)."','".round($schilde)."','".$warp."','".$cloak."','".$phaser."','".$eps."','".$lss."','".$kss."','".$points."')");
		$this->db->query("UPDATE stu_ships_buildplans SET evade=".$evade.",treffer=".$hit.",reaktor=".$reaktor.",wkkap=".$wkkap.",max_torps=".$torps.",maintaintime=".$main.",buildtime=".$bz." WHERE plans_id=".$id);
		$this->db->query("COMMIT");
		$this->db->query("UPDATE stu_ships SET eps=eps-".$this->rump[eps_cost]." WHERE id=".$this->id);
		return "Station (".$this->rump[name].") wird gebaut. Fertigstellung: ".date("d.m.Y H:i",$time);
	}

	function get_ship_maintainance_cost($data) { return $this->db->query("SELECT ROUND(SUM(a.count)/4) as gcount,a.goods_id,b.name,c.count as vcount FROM stu_modules_cost as a LEFT JOIN stu_goods as b USING(goods_id) LEFT JOIN stu_ships_storage as c ON a.goods_id=c.goods_id AND c.ships_id=".$this->id." WHERE b.view='1' AND (a.module_id=".$data['m1']." OR module_id=".$data['m2']." OR module_id=".$data['m3']." OR module_id=".$data['m4']." OR module_id=".$data['m5']." OR module_id=".$data['m6']." OR module_id=".$data['m7']." OR module_id=".$data['m8']." OR module_id=".$data['m9']." OR module_id=".$data['m10']." OR module_id=".$data['m11'].") GROUP BY a.goods_id ORDER BY b.sort"); }

	function get_maintainance_ships() { return $this->db->query("SELECT a.id,a.rumps_id,a.name,a.user_id,a.huelle,a.max_huelle,a.lastmaintainance,b.maintaintime,b.m1,b.m2,b.m3,b.m4,b.m5,b.m6,b.m7,b.m8,b.m9,b.m10,b.m11,c.user,d.name as rname,d.eps_cost FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_user as c ON a.user_id=c.id LEFT JOIN stu_rumps as d ON a.rumps_id=d.rumps_id WHERE a.systems_id=".$this->systems_id." AND a.sx=".$this->sx." AND a.sy=".$this->sy."  AND (ISNULL(d.trumfield) OR d.trumfield='' OR d.trumfield='0') AND d.slots=0 AND d.is_shuttle='0' AND a.user_id>100 AND a.cloak='0' AND a.plans_id!=1 AND a.dock=".$this->id." ORDER BY a.lastmaintainance+b.maintaintime"); }

	function maintain_ship($shipid)
	{
		$return = shipexception(array("crew" => $this->min_crew),$this);
		if ($return['code'] == 1) return $return['msg'];
		if ($this->sess['wpo'] > 0) return "Schiffbau und -Wartung sind noch für ".$this->sess['wpo']." Runde(n) gesperrt";
		if ($this->rumps_id < 9001 || $this->rumps_id > 9005) return "Zur Wartung wird ein Versorgungsposten benötigt";
		if ($this->db->query("SELECT COUNT(*) FROM stu_colonies_maintainance WHERE station_id=".$this->id,1) >= 1) return "Es wird bereits ein Schiff gewartet";
		$data = $this->db->query("SELECT a.id,a.fleets_id,a.rumps_id,a.user_id,a.name,a.cloak,a.schilde_status,a.huelle,a.max_huelle,a.systems_id,a.sx,a.sy,a.alvl,a.still,a.dock,a.maintain,b.buildtime,b.m1,b.m2,b.m3,b.m4,b.m5,b.m6,b.m7,b.m8,b.m9,b.m10,b.m11,c.eps_cost,c.slots,c.is_shuttle FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b USING(plans_id) LEFT JOIN stu_rumps as c ON c.rumps_id=b.rumps_id WHERE a.id=".$shipid,4);
		if ($data == 0) return;
		if (checksector($data) == 0) return;
		if ($data['vac_active'] == 1) return "Der Siedler befindet sich im Urlaubsmodus";
		if ($data['schilde_status'] == 1) return "Schiff kann nicht gewartet werden (Grund: Schilde des Schiffs sind aktiviert)";
		if ($data['cloak'] == 1) return;
		if ($data['slots'] > 0) return;
		if ($data['is_shuttle'] == 1) return;
		if ($data['dock'] != $this->id) return "Das Schiff muss an der Station angedockt sein";
		if ($data['maintain'] > 0) return "Dieses Schiff wird bereits gewartet";
		if ($data['user_id'] != $this->uid && $data['alvl'] > 1) return "Fremde Schiffe in Alarmbereitschaft können nicht gewartet werden";
		if ($this->eps < round($data['eps_cost']/4)) return "Für die Wartung wird ".round($data['eps_cost']/4)." Energie benötigt - Vorhanden ist nur ".$this->eps;
		$result = $this->get_ship_maintainance_cost($data);
		$time = time()+round($data['buildtime']/4);
		while ($value = mysql_fetch_assoc($result))
		{
			if (!$value['gcount']) continue;
			if ($value['gcount'] > $value['vcount'])
			{
				$err = 1;
				$msg = "Für die Wartung wird ".$value['gcount']." ".$value['name']." benötigt - Vorhanden ist nur ".(!$value['vcount'] ? 0 : $value['vcount']);
				break;
			}
		}
		if ($err == 1)
		{
			return $msg;
		}
		$result = $this->get_ship_maintainance_cost($data);
		while ($value = mysql_fetch_assoc($result))
		{
			$this->lowerstorage($this->id,$value['goods_id'],$value['gcount']);
		}
		$this->db->query("UPDATE stu_ships SET still=0,maintain=".$time.",wea_phaser='0',wea_torp='0' WHERE id=".$data['id']." LIMIT 1");
		$this->db->query("UPDATE stu_ships SET traktor=0,traktormode=NULL WHERE traktor=".$data['id']." OR id=".$data['id']." LIMIT 2");
		$this->db->query("UPDATE stu_ships SET eps=eps-".round($data['eps_cost']/4)." WHERE id=".$this->id." LIMIT 1");
		$this->db->query("INSERT INTO stu_colonies_maintainance (ships_id,maintaintime,station_id) VALUES ('".$data['id']."','".$time."','".$this->id."')");
		if ($data['user_id'] != $this->uid) $this->send_pm($this->uid,$data['user_id'],"Die ".$data['name']." wurde am Versorgungsposten ".$this->name." gewartet",3);
		return "Die ".$data['name']." wird gewartet - Abschluß der Arbeiten: ".date("d.m.Y H:i",$time);
	}
	
	function get_private_logbook(&$id) { return $this->db->query("SELECT UNIX_TIMESTAMP(date) as date_tsp,text FROM stu_ships_logs WHERE ships_id=".$id." AND type='4' ORDER BY date DESC LIMIT 5"); }

	function addtorkn()
	{
		$this->db->query("UPDATE stu_ships SET is_rkn=".$this->sess['race']." WHERE id=".$this->id." LIMIT 1");
		return "Das Schiff wurde dem RPG hinzugefügt";
	}}
?>
