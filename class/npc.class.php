<?php
class npc extends qpm
{
	function npc()
	{
		global $db,$_SESSION;
		$this->db = $db;
		$this->uid = $_SESSION[uid];
		$this->sess = $_SESSION;
	}

	function loadbuildplans() { $this->result = $this->db->query("SELECT a.plans_id,a.rumps_id,a.name,a.m1,a.m2,a.m3,a.m4,a.m5,a.m6,a.m7,a.m8,a.m9,a.m10,a.m11,a.wpoints,a.hidden,COUNT(b.id)+COUNT(DISTINCT(c.rumps_id)) as idc FROM stu_ships_buildplans as a LEFT JOIN stu_ships as b ON b.plans_id=a.plans_id AND b.user_id=".$this->uid." LEFT JOIN stu_ships_buildprogress as c ON c.plans_id=a.plans_id AND c.user_id=".$this->uid." LEFT JOIN stu_rumps as d ON a.rumps_id=d.rumps_id WHERE a.user_id=".$this->uid." GROUP BY a.plans_id ORDER BY a.hidden,a.rumps_id,a.plans_id"); }

	function getship($plan,$x,$y,$uid=0)
	{
		if ($uid == 0) $uid = $this->uid;
		if ($plan == -1)
		{
			$this->db->query("INSERT INTO stu_ships (rumps_id,user_id,cx,cy,direction,name,max_eps,huelle,max_huelle,cfield)
		 	VALUES ('9','".$uid."','".$x."','".$y."','1','Konstrukt','400','50','50','".$this->db->query("SELECT type FROM stu_map WHERE cx=".$x." AND cy=".$y." LIMIT 1",1)."')");
		 	return "Konstrukt errichtet";
		}
		$data = $this->db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id=".$plan." AND user_id=".$uid,4);
		if ($data == 0) return;
		$rump = $this->db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$data[rumps_id],4);
		$i=1;
		while($i<=11)
		{
			if ($data["m".$i] == 0)
			{
				$i++;
				continue;
			}
			$dat = $this->db->query("SELECT * FROM stu_modules WHERe module_id=".$data["m".$i],4);
			if ($dat[type] == 1) $ev_mul = $data[evade_val];
			else $evade += $dat[evade_val];
			$huelle += $dat[huelle]*$rump["m".$i."c"];
			$schilde += $dat[schilde]*$rump["m".$i."c"];
			$eps += $dat[eps]*$rump["m".$i."c"];
			if ($i == 4)
			{
				$lss = $dat[lss]+($rump["m".$dat[type]."c"] - 1);
				$kss = $dat[kss]+($rump["m".$dat[type]."c"] - 1);
			}
			if ($i == 6)
			{
				$weapon = $this->db->query("SELECT * FROM stu_weapons WHERE module_id=".$data["m".$i],4);
				$phaser = round($weapon[strength] * (1 + (log($rump["m".$i."c"]) / log(2))/3));
				$vari = $weapon[varianz];
			}
			if ($dat[stellar] == 1) $stellar = 1;
			$i++;
		}
		if ($data[m9] > 0) $cloak = 1;
		if ($data[m5] > 0 && $data[m11] > 0) $warp = 1;
		$evade += $rump[evade_val];
		$evade = $evade * $ev_mul;
		$batt = $rump["m8c"]*2;
		$id = $this->db->query("INSERT INTO stu_ships (user_id,rumps_id,plans_id,systems_id,cx,cy,sx,sy,direction,name,warpable,cloakable,max_eps,max_batt,huelle,max_huelle,max_schilde,lss_range,kss_range,max_crew,min_crew,phaser,cfield,points,lastmaintainance) VALUES ('".$uid."','".$data[rumps_id]."','".$data[plans_id]."','0','".$x."','".$y."','0','0','1','Schiff','".($warp == 1 ? 1 : '')."','".($cloak == 1 ? 1 : '')."','".$eps."','".$batt."','".$huelle."','".$huelle."','".$schilde."','".$lss."','".$kss."','".$rump[max_crew]."','".$rump[min_crew]."','".$phaser."','1','".$points."','".time()."')",5);
		$this->db->query("UPDATE stu_ships SET eps=max_eps,batt=max_batt,schilde=max_schilde,crew=max_crew WHERE id=".$id." LIMIT 1");
		$this->db->query("INSERT INTO stu_ships_logdata (ships_id,user_id,name,buildtime) VALUES ('".$id."','".$uid."','Schiff','".time()."')");
		return "Schiff erstellt";
	}

	function repairship($target)
	{
		global $ship;
		$this->s = $ship;
		$data = $this->db->query("SELECT id,user_id,name,dock,huelle,max_huelle FROM stu_ships WHERE id=".$target,4);
		if ($data == 0) return;
		if ($this->s->eps < 30) return "Zum reparieren wird 30 Energie benötigt";
		if ($data['dock'] != $this->s->id) return "Dieses Schiff ist nicht an der Station angedockt";
		if ($data['user_id'] != $this->uid) $this->send_pm($this->uid,$data['user_id'],"Die ".$this->s->name." hat die ".$data['name']." repariert",3);
		$this->db->query("UPDATE stu_ships SET huelle=max_huelle WHERE id=".$target." LIMIT 1");
		$this->db->query("UPDATE stu_ships SET eps=eps-30 WHERE id=".$this->s->id);
		return "Die ".stripslashes($data['name'])." wurde repariert";
	}

	function maintainship($target)
	{
		global $ship;
		$this->s = $ship;
		$data = $this->db->query("SELECT id,user_id,name,dock,huelle,max_huelle FROM stu_ships WHERE id=".$target,4);
		if ($data == 0) return;
		if ($this->s->eps < 30) return "Zum warten wird 20 Energie benötigt";
		if ($data['dock'] != $this->s->id) return "Dieses Schiff ist nicht an der Station angedockt";
		if ($data['user_id'] != $this->uid) $this->send_pm($this->uid,$data['user_id'],"Die ".$this->s->name." hat die ".$data['name']." gewartet",3);
		$this->db->query("UPDATE stu_ships SET lastmaintainance=".time()." WHERE id=".$target." LIMIT 1");
		$this->db->query("UPDATE stu_ships SET eps=eps-20 WHERE id=".$this->s->id);
		return "Die ".stripslashes($data['name'])." wurde gewartet";
	}

	function addgoods($good,$count,$user_id)
	{
		if (!check_int($count) || $count == 0) continue;
		$res = $this->db->query("UPDATE stu_trade_goods SET count=count+".$count." WHERE goods_id=".$good." AND offer_id=0 AND user_id=".$user_id,6);
		if ($res == 0) $this->db->query("INSERT INTO stu_trade_goods (user_id,goods_id,count,date) VALUES ('".$user_id."','".$good."','".$count."',NOW())");
		return "Ware erstellt";
	}
	
	function getcontacts() { return $this->db->query("SELECT a.recipient,a.rkn,a.deny_hp,b.user,b.race,b.vac_active,COUNT(c.id) as rid FROM stu_npc_contactlist as a LEFT JOIN stu_user as b ON b.id=a.recipient LEFT JOIN stu_faction_kn as c ON c.user_id=b.id WHERE a.user_id=".$this->uid." GROUP BY b.id"); }

	function changecontact($userId,$rkn,$hp)
	{
		$this->db->query("UPDATE stu_npc_contactlist SET rkn='".$rkn."',deny_hp='".$hp."' WHERE recipient=".$userId." AND user_id=".$this->uid);
		if ($rkn == 1) $this->db->query("UPDATE stu_user SET is_rkn='".($this->uid-9)."' WHERE id=".$userId." AND race=".($this->uid-9)." LIMIT 1");
		else $this->db->query("UPDATE stu_user SET is_rkn='0' WHERE id=".$userId." AND race=".($this->uid-9)." LIMIT 1");
	}

	function setcontact($id)
	{
		if ($this->db->query("SELECT id FROM stu_user WHERE id=".$id,1) == 0 || $id == $this->uid) return;
		$this->db->query("INSERT INTO stu_npc_contactlist (user_id,recipient) VALUES ('".$this->uid."','".$id."')");
		
	}
	
	function delcontact($id)
	{
		if ($this->db->query("SELECT rkn FROM stu_npc_contactlist WHERE user_id=".$this->uid." AND recipient=".$id." LIMIT 1",1) == 1)
		{
			$this->db->query("UPDATE stu_user SET is_rkn='0' WHERE id=".$id." LIMIT 1");
			$this->db->query("UPDATE stu_ships SET is_rkn=0 WHERE user_id=".$id);
		}
		$this->db->query("DELETE FROM stu_npc_contactlist WHERE recipient=".$id." AND user_id=".$this->uid);
	}
	
	function getrpgships() { return $this->db->query("SELECT a.rumps_id,a.user_id,a.fleets_id,a.name,a.huelle,a.max_huelle,a.schilde_status,a.schilde,a.max_schilde,a.eps,a.max_eps,a.systems_id,a.cx,a.cy,a.sx,a.sy,b.name as sname,b.type as stype,c.user,c.vac_active,d.slots FROM stu_ships as a LEFT JOIN stu_systems as b ON b.systems_id=a.systems_id LEFT JOIN stu_user as c ON c.id=a.user_id LEFT JOIN stu_rumps as d ON d.rumps_id=.a.rumps_id WHERE a.is_rkn=".$this->sess['race']." ORDER BY a.fleets_id DESC,d.slots DESC,a.user_id"); }
}
?>
