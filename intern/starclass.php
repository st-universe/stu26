<?php
class starmap
{
	// PHP4-Kompatibel
	function starmap()
	{
		global $db;
		$this->db = $db;
		$this->mf = 0;
		$this->blocks = 0;
	}

	/* PHP5-Code
	function __construct()
	{
		global $db;
		$this->db = $db;
	}*/

	function clear()
	{
		$this->mf = 0;
		$this->blocks = 0;
	}

	function getblocks()
	{
		$this->blocks = $this->db->query("SELECT COUNT(cx) as cx,COUNT(cy) as cy FROM stu_map",4);
	}

	function loadmap()
	{
		global $_GET;
		$this->mf = $this->db->query("SELECT a.cx,a.cy,a.type,a.faction_id,a.is_border,b.systems_id,b.name,c.id,d.color,d.darker_color,d.name as fname FROM stu_map as a LEFT JOIN stu_systems as b
									  USING(cx,cy) LEFT JOIN stu_ships as c ON a.cx=c.cx AND a.cy=c.cy AND c.rumps_id=9100 LEFT JOIN stu_factions as d USING(faction_id) WHERE a.cx<=".($_GET["x"]*20)." AND a.cx>".(($_GET["x"]-1)*20)." AND 
									  a.cy<=".($_GET["y"]*20)." AND a.cy>".(($_GET["y"]-1)*20)." ORDER BY a.cy,a.cx");
	}

	function loadsystem()
	{
		global $_GET;
		$this->mf = $this->db->query("SELECT sx,sy,type,race FROM stu_sys_map WHERE systems_id=".$_GET[id]." ORDER BY sy,sx");
		$this->sd = $this->db->query("SELECT * FROM stu_systems WHERE systems_id=".$_GET[id],4);
	}

	function loadfield($mode)
	{
		global $_GET;
		if ($mode == "block") $this->fd = $this->db->query("SELECT b.* FROM stu_map as a LEFT JOIN stu_map_ftypes as b USING(type) WHERE a.cx=".$_GET[cx]." AND a.cy=".$_GET[cy],4);
		if ($mode == "sys") $this->fd = $this->db->query("SELECT b.* FROM stu_sys_map as a LEFT JOIN stu_map_ftypes as b USING(type) WHERE a.sx=".$_GET[sx]." AND a.sy=".$_GET[sy]." AND a.systems_id=".$_GET[id],4);
	}

	function loadpossiblefields()
	{
		global $_GET;
		if ($_GET[id] > 0) $this->mr = $this->db->query("SELECT * FROM stu_map_ftypes WHERE (ISNULL(is_system) OR (type>20 AND type<=30)) AND type!=".$this->fd[type]." ORDER BY type");
		else $this->mr = $this->db->query("SELECT * FROM stu_map_ftypes WHERE colonies_classes_id=0 AND type!=".$this->fd[type]." ORDER BY type");
		$this->fr = $this->db->query("SELECT * FROM stu_factions ORDER BY faction_id");
		$this->fs = $this->db->query("SELECT * FROM stu_systems WHERE cx=0 AND cy=0 ORDER BY systems_id");
	}

	function setnewfield($mode)
	{
		global $_GET;
		if ($mode == "sys")
		{
			$this->db->query("UPDATE stu_sys_map SET type=".$_GET[nf]." WHERE sx=".$_GET[sx]." AND sy=".$_GET[sy]." AND systems_id=".$_GET[id]);
			$this->db->query("DELETE FROM stu_map_values WHERE sx=".$_GET[sx]." AND sy=".$_GET[sy]." AND systems_id=".$_GET[id]);
			if ($_GET[nf] == 11 || $_GET[nf] == 12)
			{
				$ir = rand(60,80);
				$r = 100-$ir;
				$bs = rand(1,5);
				if ($bs == 1)
				{
					$kel = rand(0,$r);
					$r = 100-$ir-$kel;
				}
				if ($bs == 2)
				{
					$ni = rand(0,$r);
					$r = 100-$ir-$ni;
				}
				if ($bs == 3)
				{
					$mag = rand(0,$r);
					$r = 100-$ir-$mag;
				}
				if ($bs == 4)
				{
					$tal = rand(0,$r);
					$r = 100-$ir-$tal;
				}
				if ($bs == 5)
				{
					$gal = rand(0,$r);
					$r = 100-$ir-$gal;
				}
				if (!$kel)
				{
					$kel = rand(0,$r);
					$r -= $kel;
				}
				if (!$ni)
				{
					$ni = rand(0,$r);
					$r -= $ni;
				}
				if (!$gal)
				{
					$gal = rand(0,$r);
					$r -= $gal;
				}
				if (!$tal)
				{
					$tal = rand(0,$r);
					$r -= $tal;
				}
				if (!$mag)
				{
					$mag = rand(0,$r);
					$r -= $mag;
				}
				$this->db->query("INSERT INTO stu_map_values (sx,sy,systems_id,chance_20,chance_21,chance_22,chance_23,chance_24,chance_25) VALUES ('".$_GET[sx]."','".$_GET[sy]."','".$_GET[id]."','".$ir."','".$kel."','".$ni."','".$mag."','".$tal."','".$gal."')");
			}
		}
		if ($mode == "block") $this->db->query("UPDATE stu_map SET type=".$_GET[nf]." WHERE cx=".$_GET[cx]." AND cy=".$_GET[cy]);
	}

	function fieldissystem($type)
	{
		if ($this->db->query("SELECT is_system FROM stu_map_ftypes WHERE type=".$type,1) == 1) return TRUE;
		return FALSE;
	}

	function fieldiscolony($type)
	{
		return $this->db->query("SELECT colonies_classes_id FROM stu_map_ftypes WHERE type=".$type,1);
	}

	function newsystem()
	{
		global $_GET;
		if (!$_GET[nss] || !$_GET[sn]) return;
		$i = 1;
		$j = 1;
		$sysid = $this->db->query("INSERT INTO stu_systems (cx,cy,sr,type,name) VALUES ('".$_GET[cx]."','".$_GET[cy]."','".$_GET[nss]."','".$_GET[nf]."','".addslashes($_GET[sn])."')",5);
		while($i<=$_GET[nss])
		{
			while($j<=$_GET[nss])
			{
				$this->db->query("INSERT INTO stu_sys_map (sx,sy,systems_id,type) VALUES ('".$i."','".$j."','".$sysid."','1')");
				$j++;
			}
			$i++;
			$j = 1;
		}
		if ($_GET[nf] < 2000) $this->db->query("UPDATE stu_sys_map SET type=".$_GET[nf]." WHERE sx=".round($_GET[nss]/2)." AND sy=".round($_GET[nss]/2)." AND systems_id=".$sysid);
	}

	function addcolony($cid)
	{
		global $_GET,$global_path;
		include_once($global_path."inc/gencol.inc.php");
		if ($this->db->query("SELECT id FROM stu_colonies WHERE sx=".$_GET[sx]." AND sy=".$_GET[sy]." AND systems_id=".$_GET[id],1) > 0) return 0;
		$fd = $this->db->query("SELECT type FROM stu_map_ftypes WHERE colonies_classes_id=".$cid,1);
		$this->db->query("UPDATE stu_sys_map SET type=".$fd." WHERE sx=".$_GET[sx]." AND sy=".$_GET[sy]." AND systems_id=".$_GET[id]);
		switch($cid)
		{
			case 1:
				$g = rand(90,110);
				break;
			case 2:
				$g = rand(85,115);
				break;
			case 3:
				$g = rand(90,110);
				break;
			case 4:
				$g = rand(75,95);
				break;
			case 5:
				$g = rand(90,120);
				break;
			case 6:
				$g = rand(90,120);
				break;
			case 7:
				$g = rand(100,140);
				break;
			case 8:
				$g = rand(110,150);
				break;
			case 9:
				$g = rand(700,900);
				break;
			case 10:
				$g = rand(90,110);
				break;
			case 20:
				$g = rand(50,75);
				break;
			case 21:
				$g = rand(50,75);
				break;
			case 22:
				$g = rand(50,75);
				break;
			case 23:
				$g = rand(30,60);
				break;
			case 24:
				$g = rand(30,75);
				break;
			case 25:
				$g = rand(30,75);
				break;
			case 26:
				$g = rand(40,85);
				break;
			case 27:
				$g = rand(60,90);
				break;
			case 28:
				$g = rand(25,65);
				break;
			case 29:
				$g = rand(500,700);
				break;
		}
		$rot = rand(15000,70000);
		$id = $this->db->query("INSERT INTO stu_colonies (colonies_classes_id,sx,sy,systems_id,user_id,gravitation,rotation,dn_change) VALUES ('".$cid."','".$_GET[sx]."','".$_GET[sy]."','".$_GET[id]."','1','".($g/100)."','".$rot."','".(time()+$rot)."')",5);
		generate_colony($id,$cid);
		return;
	}

	function loadoverallmap() { $this->mf = $this->db->query("SELECT cx,cy,type,faction_id,is_border FROM stu_map ORDER BY cy,cx"); }

	function setfaction($fac,$border="")
	{
		global $_GET;
		$this->db->query("UPDATE stu_map SET faction_id=".$fac.",is_border='".$border."' WHERE cx=".$_GET[cx]." AND cy=".$_GET[cy]."");
	}

	function setsystem($id,$x,$y)
	{
		$type = $this->db->query("SELECT type FROM stu_systems WHERE systems_id=".$id,1);
		$this->db->query("UPDATE stu_systems SET cx=".$x.",cy=".$y." WHERE systems_id=".$id);
		$this->db->query("UPDATE stu_map SET type=".$type." WHERE cx=".$x." AND cy=".$y);
	}
}
?>