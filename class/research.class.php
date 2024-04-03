<?php
class research
{
	function research()
	{
		global $db,$_SESSION;
		$this->db = $db;
		$this->uid = $_SESSION["uid"];
		$this->sess = $_SESSION;
	}

	function generateresearchtree()
	{
		$this->research = "";
		$this->result = $this->db->query("SELECT a.*,b.mode,b.depends_on,c.user_id,d.user_id as efu FROM stu_research as a LEFT JOIN stu_research_depencies as b ON a.research_id=b.research_id AND b.mode='1' LEFT JOIN stu_researched as c ON b.depends_on=c.research_id AND c.user_id=".$this->uid." LEFT JOIN stu_researched as d ON a.research_id=d.research_id AND d.user_id=".$this->uid." WHERE (a.faction='' OR a.faction='".$this->sess['race']."') ORDER BY a.sort ASC");
		
		// $this->showdominion = $this->db->query("SELECT research_id FROM stu_researched WHERE user_id = ".$this->uid." AND research_id = 8001",1);
		
		$this->avail[1] = array();
		while($data=mysql_fetch_assoc($this->result))
		{
			if ($data['mode'] == 1 && !$data['user_id'])
			{
				$this->research[$data['research_id']] = 1;
				continue;
			}
			$result = $this->db->query("SELECT COUNT(a.research_id) as uc, COUNT(b.research_id) as vc FROM stu_research_depencies as a LEFT JOIN stu_researched as b ON a.depends_on=b.research_id AND b.user_id=".$this->uid." WHERE a.research_id=".$data['research_id']." AND a.mode='2' GROUP BY a.research_id",4);
			if ($result['uc'] > 0 && ($result['vc'] == 0 || !$result['vc'])) continue;
			if ($data['sdc'] > 0 && $this->db->query("SELECT COUNT(a.research_id) FROM stu_research_depencies as a LEFT JOIN stu_researched as b ON a.depends_on=b.research_id AND a.mode='2' WHERE a.research_id=".$data['research_id']." AND b.user_id=".$this->uid." GROUP BY a.research_id",1) == 0) continue;
			if (!$data['user_id'] && $this->db->query("SELECT research_id FROM stu_researched_disables WHERE research_id=".$data['research_id']." AND user_id=".$this->uid,1) != 0) continue;
			if ($this->research[$data['research_id']] != 1) $this->research[$data['research_id']] = $data;
			if ($data['efu'] > 0)
			{
				$result = $this->db->query("SELECT research_id FROM stu_research_depencies WHERE depends_on=".$data['research_id']." AND mode='0'");
				while($dat=mysql_fetch_assoc($result)) $this->research[$dat['research_id']] = 1;
			}
		}
		foreach ($this->research as $key => $value)
		{
			if ($value == 1) continue;
			if ($value[efu] > 0) $this->var_b[] = $value;
			else $this->var_a[] = $value;
		}
		unset($this->research);
	}
	
	function getUserFactions() {
		$factions = array();
		$facs = $this->db->query("SELECT DISTINCT(faction) FROM `stu_colonies` WHERE user_id = ".$this->uid."");
		while ($fac = mysql_fetch_assoc($facs))
		{
			array_push($factions,$fac[faction]);
		}
		
		return $factions;
	}
	
	
	function createResearchList()
	{
		global $_SESSION;	
		$reslist = array();
	
		$result = $this->db->query("SELECT * FROM stu_research_categories WHERE 1 ORDER BY sort ASC");
		$activeID = $this->db->query("SELECT research_id FROM stu_research_active WHERE user_id = ".$this->uid."",1);
		$activity = $this->db->query("SELECT * FROM stu_research_active WHERE user_id = ".$this->uid."",4);

		$factions = $this->getUserFactions();
		
		while($cat=mysql_fetch_assoc($result))
		{

			$cat[active] = false;
			$cat[numdone] = 0;
			$cat[researches] = array();
			if ($activeID > 0) $cat[open] = false;
			
			$resres = $this->db->query("SELECT * FROM stu_research WHERE category = '".$cat[id]."' ORDER BY sort ASC");
			while($res=mysql_fetch_assoc($resres))
			{
				if (!($res[faction] == 0) && !in_array($res[faction], $factions)) continue;
				// if ($res[faction] > 0 && $res[faction] != $_SESSION[race]) continue;
				
				$dependency =  $this->db->query("SELECT depends_on FROM stu_research_depencies WHERE research_id = ".$res[research_id]."",1);
				if ($dependency > 0) {
					$havedep = $this->db->query("SELECT * FROM stu_researched WHERE user_id = ".$this->uid." AND research_id = ".$dependency."",1);
					if (!$havedep) continue;
				}

				
				$researched = $this->db->query("SELECT * FROM stu_researched WHERE user_id = ".$this->uid." AND research_id = ".$res[research_id]."",1);
				
				if ($researched != 0) $res[done] = 1;
				else $res[done] = 0;
				
				if ($activeID == $res[research_id]) $res[active] = 1;
				else $res[active] = 0;
				

				if ($res[active]) {
					$res[total] = $activity[total];
					$res[progress] = $activity[progress];
					$cat[active] = true;
					$cat[open] = true;
				}
				array_push($cat[researches],$res);
				// TODO: filter unavailable
				
				if ($res[done]) $cat[numdone]++;
				
				if (($activeID == 0) && (!$res[progress] || $res[progress] == 0)) $cat[open] = true;
			}
			if (count($cat[researches]) == $cat[numdone]) $cat[open] = false;
			if ($cat[limit] > 0 && $cat[numdone] >= $cat[limit]) $cat[open] = false;
			array_push($reslist,$cat);
		}
		return $reslist;
	}
	
	
	
	
	
	function loadresearch($r_id)
	{
		$data = $this->db->query("SELECT a.research_id,a.faction,a.name,a.description,a.cost,a.effecttype,a.rumps_id,a.build_id,a.mod_id,a.removable,a.category,b.user_id FROM stu_research as a LEFT JOIN stu_researched as b ON a.research_id=b.research_id AND b.user_id=".$this->uid." WHERE (a.faction='0' OR a.faction='".$this->sess['race']."') AND a.research_id=".$r_id." LIMIT 1",4);
		// $result = $this->db->query("SELECT a.research_id,a.depends_on,a.mode,b.user_id FROM stu_research_depencies as a LEFT JOIN stu_researched as b ON a.depends_on=b.research_id AND b.user_id=".$this->uid." WHERE a.mode='0' AND a.research_id=".$data['research_id']);
		// while($dat=mysql_fetch_assoc($result))
		// {
			// if ($dat['user_id'] > 0 && $data[removable] != 1)
			// {
				// $this->research = 0;
				// return;
			// }
		// }
		// $result = $this->db->query("SELECT a.research_id,a.depends_on,a.mode,b.user_id FROM stu_research_depencies as a LEFT JOIN stu_researched as b ON a.depends_on=b.research_id AND b.user_id=".$this->uid." WHERE a.mode='1' AND a.research_id=".$data['research_id']);
		// while($dat=mysql_fetch_assoc($result))
		// {
			// if (!$dat['user_id'] > 0)
			// {
				// $this->research = 0;
				// return;
			// }
		// }
		// $result = $this->db->query("SELECT COUNT(a.research_id) as uc, COUNT(b.research_id) as vc FROM stu_research_depencies as a LEFT JOIN stu_researched as b ON a.depends_on=b.research_id AND b.user_id=".$this->uid." WHERE a.research_id=".$data['research_id']." AND a.mode='2' GROUP BY a.research_id LIMIT 1",4);
		// if ($result['uc'] > 0 && ($result['vc'] == 0 || !$result['vc']))
		// {
			// $this->research = 0;
			// return;
		// }
		
		// $this->showdominion = $this->db->query("SELECT research_id FROM stu_researched WHERE user_id = ".$this->uid." AND research_id = 8001",1);
		
		$this->research = $data;
	}

	function doresearch($r_id)
	{
		global $_SESSION;

		$this->loadresearch($r_id);
		if ($this->research == 0 || $this->research[user_id] == $this->uid) return;
		if ($this->sess['level'] < 5) return "Forschung ist erst ab Kolonisationslevel 5 möglich";

		$activeID = $this->db->query("SELECT research_id FROM stu_research_active WHERE user_id = ".$this->uid."",1);
		$catlimit = $this->db->query("SELECT `limit` FROM stu_research_categories WHERE id = ".$this->research[category]."",1);

		if ($catlimit > 0) {
			$num = $this->db->query("SELECT b.* FROM stu_research as a LEFT JOIN stu_researched as b ON a.research_id = b.research_id WHERE a.category = ".$this->research[category]." AND b.user_id = ".$this->uid."",3);
			if ($num >= $catlimit) return "Limit für diese Kategorie (".$catlimit.") wurde erreicht!";
		}
	
		if ($activeID && $activeID > 0) return "Es läuft bereits eine Forschung.";
		
		
		// TODO: faction check
		if ($this->research[faction] > 0 && $this->research[faction] != $_SESSION['race']) return "FACTION";

		
		$this->db->query("INSERT INTO stu_research_active (`research_id` ,`user_id` ,`progress` ,`total`) VALUES ('".$r_id."', '".$this->uid."', '0', '".$this->research[cost]."');");


		return "Forschung ".$this->research['name']." wurde gestartet!";
	}
	
	function deleteresearch($r_id)
	{
		$this->loadresearch($r_id);
		if ($this->research == 0 || !$this->research[user_id]) return;
		if ($this->sess['level'] < 5) return "Forschung ist erst ab Kolonisationslevel 5 möglich";

		$activeID = $this->db->query("SELECT research_id FROM stu_research_active WHERE user_id = ".$this->uid."",1);
		global $_SESSION;	
		if ($activeID && $activeID > 0) return "Es läuft bereits eine Forschung.";
		// if ($this->research[faction] > 0 && $this->research[faction] != $_SESSION['race']) return "FACTION";

		
		$this->db->query("INSERT INTO stu_research_active (`research_id` ,`user_id` ,`progress` ,`total`,`removing`) VALUES ('".$r_id."', '".$this->uid."', '0', '".round($this->research[cost]/2)."',1);");


		// $this->db->query("INSERT INTO stu_researched (research_id,user_id) VALUES ('".$r_id."','".$this->uid."')");		
		
		// $this->db->query("INSERT INTO stu_researched (research_id,user_id) VALUES ('".$r_id."','".$this->uid."')");		
		// $this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('6','".$this->uid."','".addslashes("Wir besitzen nun das Wissen über die Grundstrukturen, um das in Gorn-Technologie verwendete Gradonium herzustellen. Allerdings sind wir bei der Entwicklung einer praktischen Anwendung der Technologie auf Probleme gestoßen...<br><br>Die Daten, die uns vorliegen, sind für eine Rekonstruktion der Gorn-Module noch unzureichend. Wir benötigen daher einen genauen Scan eines Schiffes der Gorn, um die internen Energiesysteme reproduzieren zu können.")."','1',NOW())");
		// if ($this->research['rumps_id'] > 0) $this->db->query("INSERT INTO stu_rumps_user (rumps_id,user_id) VALUES ('".$this->research['rumps_id']."','".$this->uid."')");

		return "Entfernen der Forschung wurde gestartet!";
	}
	
	function breakresearch()
	{
		$activeID = $this->db->query("SELECT research_id FROM stu_research_active WHERE user_id = ".$this->uid."",1);
		if ($activeID && $activeID > 0) {
			$this->db->query("DELETE FROM stu_research_active WHERE user_id = ".$this->uid.";");
			return "Forschung wurde abgebrochen.";
		}
		else {
			return "Es ist keine Forschung aktiv!";
		}


	}	
	
	
	function getbinfo($b_id)
	{
		$op .= "<table>
		<tr>";

		$res = $this->db->query("SELECT a.buildings_id,a.name,a.lager,a.eps_cost,a.eps,a.eps_proc,a.bev_pro,a.bev_use,a.level,a.integrity,a.points,a.schilde,a.bclimit,a.blimit,a.upgrade_from,a.buildtime,b.name as upname FROM stu_buildings as a LEFT JOIN stu_buildings as b ON a.upgrade_from=b.buildings_id WHERE a.buildings_id = ".$b_id."");
		while ($data=mysql_fetch_assoc($res))
		{
			$field = $this->db->query("SELECT type FROM stu_field_build WHERE buildings_id=".$data[buildings_id]." AND type<200 ORDER BY type LIMIT 1",1);
			$cost = $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_cost as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
			$goods = $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_buildings_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.buildings_id=".$data[buildings_id]." ORDER BY b.sort");
			$op .= "<td valign=top width=200><table class=tcal cellspacing=1 cellpadding=1>
			<tr>
				<th>".stripslashes($data[name])."</th>
			</tr>
			<tr>
				<td><div align=center><img src=gfx/buildings/".$data[buildings_id]."/".($field == 0 ? 1 : $field).".gif></div><br>";
				$j = 0;
				$result = $this->db->query("SELECT type FROM stu_field_build WHERE type<200 AND buildings_id=".$data[buildings_id]);
				while($dat=mysql_fetch_assoc($result))
				{
					$j++;
					$op .= "<img src=gfx/fields/".$dat[type].".gif width=16 height=16>&nbsp;";
					if ($j%7 == 0) $op .= "<br>";
				}
					$op .= "</td>
			</tr>
			<tr>
			<td>Ab Level: ".$data[level]."<br>";
			if ($data[bev_pro] > 0) $op .= "<img src=gfx/bev/bev_free_1_1.gif title='Wohnraum'> +".$data[bev_pro]."<br>";
			if ($data[bev_use] > 0) $op .= "<img src=gfx/bev/bev_used_1_1.gif title='Benötige Arbeiter'> ".$data[bev_use]."<br>";
			if ($data[lager] > 0) $op .= "<img src=gfx/buttons/lager.gif title='Lagerraum'> +".$data[lager]."<br>";
			if ($data[eps] > 0) $op .= "<img src=gfx/buttons/e_trans1.gif title='Energiespeicher'> +".$data[eps]."<br>";
			if ($data[eps_proc] != 0) $op .= "<img src=gfx/buttons/e_trans2.gif title='Energie'> ".($data[eps_proc] > 0 ? "+".$data[eps_proc] : $data[eps_proc])."<br>";
			if ($data[points] > 0) $op .= "<img src=gfx/buttons/points.gif title='Wirtschaft'> +".$data[points]."<br>";
			$op .= "Integrität: ".$data[integrity]."<br>
			<img src=gfx/buttons/time.gif title='Bauzeit'> ".gen_time($data[buildtime]);
			if ($data[blimit] > 0) $op .= "<br>Limit (global): ".$data[blimit];
			if ($data[bclimit] > 0) $op .= "<br>Limit (pro Kolonie): ".$data[bclimit];
			if ($data[upgrade_from] > 0) $op .= "<br>Upgrade von: ".$data[upname];
			$op .= "</td>
			</tr>";
			if (mysql_num_rows($goods) > 0)
			{
				$op .= "<tr>
				<td><u>Waren</u><br>";
				while($g=mysql_fetch_assoc($goods)) $op .= "<img src=gfx/goods/".$g[goods_id].".gif title='".$g[name]."'> ".($g['count'] > 0 ? "+".$g['count'] : $g['count'])."<br>";
				$op .= "</td>
				</tr>";
			}
			$op .= "<tr>
				<td><u>Baukosten</u><br>
				<img src=gfx/buttons/e_trans2.gif title='Energie'> ".$data[eps_cost]."<br>";
				while($c=mysql_fetch_assoc($cost)) $op .= "<img src=gfx/goods/".$c[goods_id].".gif title='".$c[name]."'> ".$c['count']."<br>";
				$op .= "</td>
			</tr></table></td><td width=40></td>";
			$i++;
			if ($i%4==0) $op .= "</tr><tr>";
		}
		$op .= "</tr></table>";
		return $op;
	}

	function getmodulespecial($data,$slot)
	{
		if ($data[torp_type] > 0)
		{
			return "";
		}
		$return = "";
		if ($data[wtype] > 0)
		{
			if ($slot == 1)
			{
			    	return "<img src=gfx/specials/wd_".$data[wtype].".gif> ".getWeaponTypeDescription($data[wtype])."-Waffe";
			}
			elseif ($slot == 2)
			{
				if ($data[special_id1] == 0) return "";
				$mod = $this->db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id1],4);
				return "<img src=gfx/specials/".$data[special_id1].".gif title=\"".$mod[description]."\"> ".$mod[name];
			}
			else
			{
				if ($data[special_id2] == 0) return "";
				$mod = $this->db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id2],4);
				return "<img src=gfx/specials/".$data[special_id2].".gif title=\"".$mod[description]."\"> ".$mod[name];
			}
		}
		else
		{
			if ($slot == 1)
			{
				//if ($data[special_id1] == 0) return "<img src=".$gfx."/specials/0.gif> Keines";
				if ($data[special_id1] == 0) return "";
				$mod = $this->db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id1],4);
				return "<img src=gfx/specials/".$data[special_id1].".gif title=\"".$mod[description]."\"> ".$mod[name];
			}
			elseif ($slot == 2)
			{
				if ($data[special_id2] == 0) return "";
				$mod = $this->db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id2],4);
				return "<img src=gfx/specials/".$data[special_id2].".gif title=\"".$mod[description]."\"> ".$mod[name];
			}
		}
		return "";
	}

	function getminfo($m_id)
	{
		$op = "";

		$result = $this->db->query("SELECT a.*,b.strength,b.varianz,b.wtype,b.pulse,b.critical FROM stu_modules as a left join stu_weapons as b on a.module_id = b.module_id WHERE a.module_id = ".$m_id."");
		while($data=mysql_fetch_assoc($result))
		{
			$vals = "<table class=tsec border=0><tr>";
			$vals .= "<td width=200 class=th><img src=gfx/buttons/points.gif title='Punkte'> ".$data[points]."</td>
			<td width=200 class=th>".$this->getmodulespecial($data,1)."</td>
			<td width=200 class=th>".$this->getmodulespecial($data,2)."</td>
			<td  class=th>".$this->getmodulespecial($data,0)."</td></tr><tr>";
			if ($data[type] == 1) 
			{
				$vals .= "<td class=th colspan=5><img src=gfx/buttons/modul_1.gif title='Hülle'> ".$data[huelle]."</td>";
			}
			elseif($data[type] == 2)
			{
				$vals .= "<td class=th colspan=5><img src=gfx/buttons/modul_2.gif title='Schilde'> ".$data[schilde]."</td>";
			}
			elseif($data[type] == 3)
			{
				$vals .= "<td class=th colspan=5><img src=gfx/buttons/modul_3.gif title='Trefferchance'> ".$data[hit_val]."%</td>";
			}
			elseif($data[type] == 4)
			{
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/modul_4.gif title='KSS'> ".$data[kss]."</td>";
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/modul_4.gif title='LSS'> ".$data[lss]."</td>";
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/tarn1.gif title='Enttarnchance'> ".$data[detect_val]."%</td>";
				$vals .= "<td class=th colspan=2><img src=gfx/buttons/modul_3.gif title='Trefferchance'> ".$data[hit_val]."%</td>";
			}
			elseif($data[type] == 5)
			{
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/e_trans1.gif title='Reaktor'> ".$data[reaktor]."</td>";
				$vals .= "<td class=th colspan=4><img src=gfx/buttons/modul_5.gif title='Ladung'> ".$data[wkkap]."</td>";
			}
			elseif($data[type] == 6)
			{
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/modul_6.gif title='Schaden'> ".$data[strength]."</td>";
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/x1.gif title='Kritisch'> ".$data[critical]."%</td>";
				if ($data[pulse] > 0) {
					$vals .= "<td class=th colspan=1><img src=gfx/buttons/modul_3.gif title='Trefferchance'> ".$data[hit_val]."%</td>";
					$vals .= "<td class=th colspan=1><img src=gfx/buttons/modul_6.gif title='Pulse'> ".$data[pulse]."</td>";
				}
				else $vals .= "<td class=th colspan=1><img src=gfx/buttons/modul_3.gif title='Trefferchance'> ".$data[hit_val]."%</td>";
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/ascan1.gif title='Varianz'> ".$data[varianz]."%</td>";
			}
			elseif($data[type] == 7)
			{
			$vals .= "<td class=th colspan=5><img src=gfx/buttons/modul_7.gif title='Ausweichen'> ".$data[evade_val]."%</td>";
			}
			elseif($data[type] == 8)
			{
				$vals .= "<td class=th colspan=5><img src=gfx/buttons/modul_8.gif title='EPS'> ".$data[eps]."</td>";
			}
			elseif($data[type] == 11)
			{
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/modul_11.gif title='Warpfaktor'> ".$data[warp_capability]."</td>";
				$vals .= "<td class=th colspan=4><img src=gfx/buttons/e_trans1.gif title='Warpkostenreduzierung'> ".$data[warp_cost]."</td>";
			}
			elseif($data[type] == 9)
			{
				if ($data[module_id] == 11001) $vals .= "<td class=th colspan=5><img src=gfx/buttons/tarn2.gif title='Tarnung'> Tarnung<br>Benoetigt Spezial-Slot der Klasse 2</td>";
				if ($data[module_id] == 11002) $vals .= "<td class=th colspan=5><img src=gfx/buttons/map2.gif title='Astrometrie'> Astrometrie<br>Benoetigt Spezial-Slot der Klasse 1</td>";
			}
			elseif($data[type] == 10)
			{
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/damaged_10.gif title='Max. Torpedoklasse'> ".$data[torp_type]."</td>";
				$vals .= "<td class=th colspan=1><img src=gfx/buttons/stern1.gif title='Torpedos pro Salve'> ".$data[torp_fire_amount]."</td>";
				$vals .= "<td class=th colspan=3><img src=gfx/buttons/lager.gif title='Torpedostauraum'> ".$data[torps]."</td>";
			}
			$vals .= "</tr></table>";


	
			$op .= "<table class=tcal><tr><td class=mml><img src=gfx/goods/".$data[module_id].".gif title=\"".$data[name]."\"><font color=#44CC44> ".$data[name]."</font></td></tr><tr><td width=100%>".$vals."</td></tr></table><br>";

		}

		return $op;
	}
}
?>