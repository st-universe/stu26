<?php
class nagus extends qpm
{
	function nagus()
	{
		global $db,$_SESSION,$gfx;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->gfx = $gfx;
		$this->sess = $_SESSION;
	}
	
	function getofferlist()
	{
		$this->result = $this->db->query("SELECT a.*,b.* FROM stu_nagus_deals as a left join stu_rumps as b on a.rumps_id = b.rumps_id ORDER BY a.sort ASC");
	}	
	
	function lowerstorage($good,$count,$user)
	{
		$result = $this->db->query("UPDATE stu_trade_goods SET count=count-".$count." WHERE goods_id=".$good." AND offer_id=0 AND user_id=".$user." AND count>".$count,6);
		if ($result == 0) $this->db->query("DELETE FROM stu_trade_goods WHERE goods_id=".$good." AND offer_id=0 AND user_id=".$user);
	}
	
	function upperstat($count)
	{
		$result = $this->db->query("UPDATE stu_game_vars SET value=value+".$count." WHERE var='nagusdili' LIMIT 1",6);
	}
	
	function grantrump($id)
	{
		$result = $this->db->query("INSERT INTO `stu_rumps_user` (`rumps_id` ,`user_id`) VALUES ('".$id."' ,'".$this->uid."');",6);
	}	
	function takeoffer($id)
	{
		$data = $this->db->query("SELECT * FROM stu_nagus_deals WHERE rumps_id=".$id." LIMIT 1",4);
		if ($data == 0) return;
		
		
		$userlevel = $this->db->query("SELECT level FROM stu_user WHERE id=".$this->uid." LIMIT 1",1);
		if ($userlevel != 6) return "Dieses geschäft ist erst ab Level 6 möglich.";
		
		// checken ob bekannt
		$rdata = $this->db->query("SELECT * FROM stu_rumps_user WHERE rumps_id=".$id." AND user_id = ".$this->uid." LIMIT 1",4);
		if ($rdata != 0) return "Dieses Schiff ist bereits bekannt.";		

		$rumpname = $this->db->query("SELECT name FROM stu_rumps WHERE rumps_id=".$id." LIMIT 1",1);
		$userrace = $this->db->query("SELECT race FROM stu_user WHERE id=".$this->uid." LIMIT 1",1);
		
		
		
		if ($data['race'] != 0) {
			if ($data['race'] != $userrace) return "Dieses Angebot ist für deine Rasse nicht verfügbar.";
		}
		
		$c = $this->db->query("SELECT count FROM stu_trade_goods WHERE offer_id=0 AND user_id=".$this->uid." AND goods_id=8 LIMIT 1",1);
		
		
		if ($c < $data['cost']) return "Für diese Transaktion werden ".($data['cost'])." Dilithium benötigt (".$c." vorhanden)";
		
		$this->db->query("START TRANSACTION");
		$this->lowerstorage(8,$data['cost'],$this->uid);
		$this->upperstat($data['cost']);
		$this->grantrump($data['rumps_id']);
		$this->db->query("COMMIT");
		
		return "Konstruktionspläne (".$rumpname.") wurden für ".$data['cost']." Dilithium gekauft. Der Nagus dankt!";


		// $this->send_pm($this->uid,$data['user_id'],$pm,2);
		// return $pm;
	}
	
	
	
	
	
	
	
	
	
}
?>