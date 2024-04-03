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
		//$this->blocks = $this->db->query("SELECT COUNT(cx) as cx,COUNT(cy) as cy FROM stu_map",4);
	}

	function loadmap()
	{
		global $_GET;
		//$this->mf = $this->db->query("SELECT a.cx,a.cy,a.type,a.race,b.systems_id,b.name FROM stu_map as a LEFT JOIN stu_systems as b
		//							  USING(cx,cy) WHERE a.cx<=".($_GET["x"]*20)." AND a.cx>".(($_GET["x"]-1)*20)." AND 
		//							  a.cy<=".($_GET["y"]*20)." AND a.cy>".(($_GET["y"]-1)*20)." ORDER BY a.cy,a.cx");
	}

	function loadsystem()
	{
		global $_GET;
		$this->mf = $this->db->query("SELECT sx,sy,type,race FROM stu_sys_map WHERE systems_id=".$_GET[id]." ORDER BY sy,sx");
		$this->sd = $this->db->query("SELECT * FROM stu_systems WHERE systems_id=".$_GET[id],4);
	}
	
	function getcolstats()
	{
		$this->stl = $this->db->query("SELECT a.type,COUNT(b.type) as cc FROM stu_map_ftypes as a LEFT JOIN stu_sys_map as b USING(type) LEFT JOIN stu_systems as c ON b.systems_id=c.systems_id AND c.blocked='1'  AND !ISNULL(c.blocked) WHERE a.colonies_classes_id>0 AND c.blocked='1'  AND !ISNULL(c.blocked) GROUP BY a.type ORDER BY a.colonies_classes_id");
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
		if ($_GET[id] > 0) $this->mr = $this->db->query("SELECT * FROM stu_map_ftypes WHERE type!=".$this->fd[type]." AND in_system='1' ORDER BY type");
		else $this->mr = $this->db->query("SELECT * FROM stu_map_ftypes WHERE colonies_classes_id=0 AND type!=".$this->fd[type]." ORDER BY type");
	}

	function setnewfield($mode)
	{
		global $_GET;
		if ($mode == "sys") $this->db->query("UPDATE stu_sys_map SET type=".$_GET[nf]." WHERE sx=".$_GET[sx]." AND sy=".$_GET[sy]." AND systems_id=".$_GET[id]);
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
		if ($_GET[nss] > 20) $_GET[nss] = 20;
		$i = 1;
		$j = 1;
		$sysid = $this->db->query("INSERT INTO stu_systems (sr,type,name,autor) VALUES ('".$_GET[nss]."','".$_GET[nf]."','".addslashes($_GET[sn])."','".addslashes($_GET[nn])."')",5);
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
		return $sysid;
	}

	function loadpossiblesystems() { $this->sf = $this->db->query("SELECT * FROM stu_map_ftypes WHERE is_system='1'"); }

	function loadsystems() { $this->sl = $this->db->query("SELECT * FROM stu_systems WHERE blocked='1'"); }

	function setunb($id) { $this->db->query("UPDATE stu_systems SET blocked='' WHERE systems_id=".$id); }
}
?>