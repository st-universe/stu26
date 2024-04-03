<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	$gfx = "http://www.stuniverse.de/gfx/";
	
	function getmodulespecial($data,$slot)
	{
	    global $gfx;
	    global $db;
		if ($data[torp_type] > 0)
		{
			return "";
		}
		$return = "";
		if ($data[wtype] > 0)
		{
			if ($slot == 1)
			{
			    	return "<img src=".$gfx."/specials/wd_".$data[wtype].".gif> ".getWeaponTypeDescription($data[wtype])."-Waffe";
			}
			elseif ($slot == 2)
			{
				if ($data[special_id1] == 0) return "";
				$mod = $db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id1],4);
				return "<img src=".$gfx."/specials/".$data[special_id1].".gif title=\"".$mod[description]."\"> ".$mod[name];
			}
			else
			{
				if ($data[special_id2] == 0) return "";
				$mod = $db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id2],4);
				return "<img src=".$gfx."/specials/".$data[special_id2].".gif title=\"".$mod[description]."\"> ".$mod[name];
			}
		}
		else
		{
			if ($slot == 1)
			{
				//if ($data[special_id1] == 0) return "<img src=".$gfx."/specials/0.gif> Keines";
				if ($data[special_id1] == 0) return "";
				$mod = $db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id1],4);
				return "<img src=".$gfx."/specials/".$data[special_id1].".gif title=\"".$mod[description]."\"> ".$mod[name];
			}
			elseif ($slot == 2)
			{
				if ($data[special_id2] == 0) return "";
				$mod = $db->query("SELECT name,description FROM stu_modules_special WHERE special_id=".$data[special_id2],4);
				return "<img src=".$gfx."/specials/".$data[special_id2].".gif title=\"".$mod[description]."\"> ".$mod[name];
			}
		}
		return "";
	}
	$op = "<html><head><title>Star Trek Universe Modulwerte</title><link rel='STYLESHEET' type='text/css' href='http://www.stuniverse.de/main/gfx/style.css'>
	<style>
	td.l {
        font-size: 7pt;
        color: #ffffff;
        width : 28px;
        height : 30px;
        border: 2px #000000;
}	
table.tsec {
	background-color: #000000;
	width: 100%;
	border-collapse: separate;
	border-spacing: 0px;
}
</style>
	</head><body  bgcolor=#202020>";
	
	function getmt($type)
	{
		
		$op = "";
		global $gfx;
		global $db;

		if ($type != 12)
		{
		$result = $db->query("SELECT a.*,b.strength,b.varianz,b.wtype,b.pulse,b.critical FROM stu_modules as a left join stu_weapons as b on a.module_id = b.module_id WHERE a.type = ".$type." AND a.viewable='1' ORDER BY a.level, a.module_id");
		while($data=mysql_fetch_assoc($result))
		{
			$vals = "<table class=tsec border=0><tr>";
			$vals .= "<td width=200 class=th><img src=".$gfx."/buttons/points.gif title='Punkte'> ".$data[points]."</td>
			<td width=200 class=th>".getmodulespecial($data,1)."</td>
			<td width=200 class=th>".getmodulespecial($data,2)."</td>
			<td  class=th>".getmodulespecial($data,0)."</td></tr><tr>";
			if ($data[type] == 1) 
			{
				$vals .= "<td class=th colspan=5><img src=".$gfx."/buttons/modul_1.gif title='Hülle'> ".$data[huelle]."</td>";
			}
			elseif($data[type] == 2)
			{
				$vals .= "<td class=th colspan=5><img src=".$gfx."/buttons/modul_2.gif title='Schilde'> ".$data[schilde]."</td>";
			}
			elseif($data[type] == 3)
			{
				$vals .= "<td class=th colspan=5><img src=".$gfx."/buttons/modul_3.gif title='Trefferchance'> ".$data[hit_val]."%</td>";
			}
			elseif($data[type] == 4)
			{
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/modul_4.gif title='KSS'> ".$data[kss]."</td>";
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/modul_4.gif title='LSS'> ".$data[lss]."</td>";
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/tarn1.gif title='Enttarnchance'> ".$data[detect_val]."%</td>";
				$vals .= "<td class=th colspan=2><img src=".$gfx."/buttons/modul_3.gif title='Trefferchance'> ".$data[hit_val]."%</td>";
			}
			elseif($data[type] == 5)
			{
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/e_trans1.gif title='Reaktor'> ".$data[reaktor]."</td>";
				$vals .= "<td class=th colspan=4><img src=".$gfx."/buttons/modul_5.gif title='Ladung'> ".$data[wkkap]."</td>";
			}
			elseif($data[type] == 6)
			{
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/modul_6.gif title='Schaden'> ".$data[strength]."</td>";
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/x1.gif title='Kritisch'> ".$data[critical]."%</td>";
				if ($data[pulse] > 0) {
					$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/modul_3.gif title='Trefferchance'> ".$data[hit_val]."%</td>";
					$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/modul_6.gif title='Pulse'> ".$data[pulse]."</td>";
				}
				else $vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/modul_3.gif title='Trefferchance'> ".$data[hit_val]."%</td>";
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/ascan1.gif title='Varianz'> ".$data[varianz]."%</td>";
			}
			elseif($data[type] == 7)
			{
			$vals .= "<td class=th colspan=5><img src=".$gfx."/buttons/modul_7.gif title='Ausweichen'> ".$data[evade_val]."%</td>";
			}
			elseif($data[type] == 8)
			{
				$vals .= "<td class=th colspan=5><img src=".$gfx."/buttons/modul_8.gif title='EPS'> ".$data[eps]."</td>";
			}
			elseif($data[type] == 11)
			{
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/modul_11.gif title='Warpfaktor'> ".$data[warp_capability]."</td>";
				$vals .= "<td class=th colspan=4><img src=".$gfx."/buttons/e_trans1.gif title='Warpkostenreduzierung'> ".$data[warp_cost]."</td>";
			}
			elseif($data[type] == 9)
			{
				if ($data[module_id] == 11001) $vals .= "<td class=th colspan=5><img src=".$gfx."/buttons/tarn2.gif title='Tarnung'> Tarnung<br>Benoetigt Spezial-Slot der Klasse 2</td>";
				if ($data[module_id] == 11002) $vals .= "<td class=th colspan=5><img src=".$gfx."/buttons/map2.gif title='Astrometrie'> Astrometrie<br>Benoetigt Spezial-Slot der Klasse 1</td>";
			}
			elseif($data[type] == 10)
			{
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/damaged_10.gif title='Max. Torpedoklasse'> ".$data[torp_type]."</td>";
				$vals .= "<td class=th colspan=1><img src=".$gfx."/buttons/stern1.gif title='Torpedos pro Salve'> ".$data[torp_fire_amount]."</td>";
				$vals .= "<td class=th colspan=3><img src=".$gfx."/buttons/lager.gif title='Torpedostauraum'> ".$data[torps]."</td>";
			}
			$vals .= "</tr></table>";


	
			$op .= "<table class=tcal><tr><td class=mml><img src=".$gfx."/goods/".$data[module_id].".gif title=\"".$data[name]."\"><font color=#44CC44> ".$data[name]."</font></td></tr><tr><td width=100%>".$vals."</td></tr></table><br>";

		}
		}
		else
		{


		$result = $db->query("SELECT * FROM stu_torpedo_types ORDER by type, goods_id ASC");
		while($data=mysql_fetch_assoc($result))
		{
			$vals = "<table class=tsec border=0><tr>";
			$vals .= "<td width=200 class=th><img src=".$gfx."/buttons/ftp_".$data[torp_type]."_2.gif title='Schaden'> ".$data[damage]."</td>
			<td width=200 class=th><img src=".$gfx."/buttons/modul_3.gif title='Trefferchance'> ".$data[hitchance]."</td>
			<td width=200 class=th><img src=".$gfx."/buttons/ascan1.gif title='Varianz'> ".$data[varianz]."</td>
			<td  class=th><img src=".$gfx."/buttons/x1.gif title='Kritisch'> ".$data[critical]."</td></tr><tr>";

			$vals .= "</tr></table>";


	
			$op .= "<table class=tcal><tr><td class=mml><img src=".$gfx."/goods/".$data[goods_id].".gif title=\"".$data[name]."\"><font color=#44CC44> ".$data[name]."</font></td></tr><tr><td width=100%>".$vals."</td></tr></table><br>";
		}

		}
		return $op;
	}


	for ($i = 1; $i <= 12; $i++)
	{
		$op=getmt($i);



   		//$op.= "</body></html>";
		unlink($global_path."/inc/lists/modl".$i.".html");
		$fp = fopen($global_path."/inc/lists/modl".$i.".html","a+");
		fwrite($fp,$op);
		fclose($fp);
	}






















?>
