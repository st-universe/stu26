<?php
class logbook
{
	function __construct()
	{
		global $_GET,$_SESSION,$db;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		if (check_int($_GET['id'])) $this->load_ship_data($_GET['id']);
	}
	
	function load_available_logs() { return $this->db->query("SELECT a.ships_id,a.name,a.buildtime,a.destroytime,COUNT(b.log_id) as lc FROM stu_ships_logdata as a LEFT JOIN stu_ships_logs as b USING(ships_id) WHERE a.user_id=".$this->uid." GROUP BY a.ships_id ORDER BY a.ships_id"); }

	function load_ship_data($shipId)
	{
		$this->ship = $this->db->query("SELECT id,name,rumps_id,user_id FROM stu_ships WHERE id=".$shipId." AND user_id=".$this->uid." LIMIT 1",4);
		if ($this->ship == 0)
		{
			$this->ship = $this->db->query("SELECT ships_id as id,name,user_id FROM stu_ships_logdata WHERE ships_id=".$shipId." AND user_id=".$this->uid." LIMIT 1",4);
			if ($this->ship == 0) die();
		}
	}

	function load_complete_log($cn)
	{
		if (!$this->ship) die();
		return $this->db->query("SELECT log_id,text,UNIX_TIMESTAMP(date) as date_tsp,type FROM stu_ships_logs WHERE ships_id=".$this->ship['id']." ORDER BY date DESC LIMIT ".$cn.",50");
	}
	
	function load_complete_log_count() { return $this->db->query("SELECT COUNT(log_id) FROM stu_ships_logs WHERE ships_id=".$this->ship['id'],1); }
	
	function load_log_by_type($type,$cn)
	{
		if (!$this->ship) die();
		return $this->db->query("SELECT log_id,text,UNIX_TIMESTAMP(date) as date_tsp,type FROM stu_ships_logs WHERE ships_id=".$this->ship['id']." AND type='".$type."' ORDER BY date DESC LIMIT ".$cn.",50");
	}

	function load_log_count_by_type($type) { return $this->db->query("SELECT COUNT(log_id) FROM stu_ships_logs WHERE ships_id=".$this->ship['id']." AND type='".$type."'",1); }

	function delete_logs($shipId,$arr)
	{
		foreach($arr as $key => $value)
		{
			$this->db->query("DELETE FROM stu_ships_logs WHERE log_id=".$value." AND user_id=".$this->uid." AND ships_id=".$shipId." LIMIT 1");
		}
		return "Logs wurden gelöscht";
	}
}
?>