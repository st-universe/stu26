<?php
class log 
{
	function log()
	{
		global $db;
		$this->db = $db;
	}
	
	function deleteLogType($type)
	{
		$this->result = $this->db->query("DELETE FROM log_crons WHERE type = '".$type."'");
	}	
	
	function enterLog($type,$text)
	{
		$this->db->query("INSERT INTO log_crons (type,entry,time) VALUES ('".$type."','".addslashes($text)."',NOW())");
	}
		
	
	
	
	
}
?>