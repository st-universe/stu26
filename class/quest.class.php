<?php
class quest
{
	// PHP4-Kompatibel
	function quest()
	{
		global $db,$_SESSION;
		$this->db = $db;
		$this->sess = $_SESSION;
		$this->uid = $_SESSION[uid];
	}

	/* PHP5-Code
	function __construct()
	{
		global $db,$_SESSION;
		$this->db = $db;
		$this->sess = $_SESSION;
		$this->uid = $_SESSION[uid]
	}*/

	function getquestsbyshipid($ship_id) { $this->result = $this->db->query("SELECT a.quest_id,a.titel,a.danger FROM stu_quests as a LEFT JOIN stu_quests_user as b ON a.quest_id=b.quest_id AND b.user_id=".$this->uid." WHERE a.ships_id=".$ship_id." AND a.minlvl<='".$this->sess[level]."' AND (a.race='0' || a.race='".$this->sess[race]."') AND ISNULL(b.user_id) AND a.user_id=0 AND a.startable='1' ORDER BY a.quest_id"); }

	function getshipqueststatus($target,$ship_id) { $this->result = $this->db->query("SELECT quest_id,titel FROM stu_quests WHERE ((ships_id=".$target." AND tar_ships_id=0) OR (tar_ships_id=".$target.")) AND user_id=".$this->uid); }

	function getcolqueststatus($target,$ship_id) { $this->result = $this->db->query("SELECT quest_id,titel FROM stu_quests WHERE ((colonies_id=".$target." AND tar_colonies_id=0) OR (tar_colonies_id=".$target.")) AND user_id=".$this->uid); }

	function checkuserquest($user_id,$quest_id) { return $this->db->query("SELECT user_id FROM stu_quests_user WHERE quest_id=".$quest_id." AND user_id=".$user_id,1); }

	function loadquest($target,$quest_id)
	{
		global $gfx;
		$this->data = $this->db->query("SELECT a.quest_id,a.titel,a.type,a.welcome_msg,a.maxtime,a.danger FROM stu_quests as a LEFT JOIN stu_quests_user as b ON a.quest_id=b.quest_id AND b.user_id=".$this->uid." WHERE a.ships_id=".$target." AND a.quest_id=".$quest_id." AND a.minlvl<='".$this->sess[level]."' AND (a.race='0' || a.race='".$this->sess[race]."') AND ISNULL(b.user_id) AND a.user_id=0",4);
		if ($this->data == 0) die(show_error(902));
		if ($this->data[type] == 1)
		{
			$result = $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_quests_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.quest_id=".$this->data[quest_id]." AND mode='1' ORDER BY a.goods_id");
			while($data=mysql_fetch_assoc($result)) $this->data[deliver] .= "<b>Besorge</b><br><img src=".$gfx."/goods/".$data[goods_id].".gif title=\"".ftit($data[name])."\"> ".$data['count'];
		}
		$result = $this->db->query("SELECT a.goods_id,a.count,b.name FROM stu_quests_goods as a LEFT JOIN stu_goods as b USING(goods_id) WHERE a.quest_id=".$this->data[quest_id]." AND mode='2' ORDER BY a.goods_id");
		while($data=mysql_fetch_assoc($result)) $this->data[present] .= "<b>Belohnung</b><br><img src=".$gfx."/goods/".$data[goods_id].".gif title=\"".ftit($data[name])."\"> ".$data['count'];
	}

	function takequest($quest_id)
	{
		if ($this->checkuserquest($this->uid,$quest_id) > 0) die(show_error(902));
		$data = $this->db->query("SELECT quest_id,titel,depends_on FROM stu_quests WHERE minlvl<='".$this->sess[level]."' AND quest_id=".$quest_id." AND startable='1' AND user_id=0",4);
		if ($data == 0) die(show_error(902));
		if ($data[depends_on] > 0 && $this->checkuserquest($this->uid,$data[depends_on]) > 0) die(show_error(902));
		$this->db->query("UPDATE stu_quests SET user_id=".$this->uid.",user_time=".time()." WHERE quest_id=".$quest_id);
		return "Quest \"".$data[titel]."\" akzeptiert";
	}

	function checkstorage($ship_id,$goods_id,$count)
	{
		if ($this->db->query("SELECT count FROM stu_ships_storage WHERE ships_id=".$ship_id." AND goods_id=".$goods_id,1) < $count) return FALSE;
		return TRUE;
	}

	function lowerstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count-".$count." WHERE ships_id=".$id." AND goods_id=".$good." AND count>".$count,6);
		if ($result == 0) $this->db->query("DELETE FROM stu_ships_storage WHERE ships_id=".$id." AND goods_id=".$good);
	}

	function upperstorage($id,$good,$count)
	{
		$result = $this->db->query("UPDATE stu_ships_storage SET count=count+".$count." WHERE ships_id=".$id." AND goods_id=".$good,6);
		if ($result == 0) $this->db->query("INSERT INTO stu_ships_storage (ships_id,goods_id,count) VALUES ('".$id."','".$good."','".$count."')");
	}

	function finishquest($ship_id,$target,$quest_id)
	{
		global $_GET;
		$_GET[m] == "c" ? $data = $this->db->query("SELECT quest_id,titel,type,goodbye_msg FROM stu_quests WHERE user_id=".$this->uid." AND ((colonies_id=".$target." AND tar_colonies_id=0) OR (tar_colonies_id=".$target."))",4) : $data = $this->db->query("SELECT quest_id,titel,type,goodbye_msg FROM stu_quests WHERE user_id=".$this->uid." AND ((ships_id=".$target." AND tar_ships_id=0) OR (tar_ships_id=".$target."))",4);
		if ($data == 0) die(show_error(902));
		if ($data[type] == 1 || $data[type] == 3)
		{
			$result = $this->db->query("SELECT goods_id,count FROM stu_quests_goods WHERE mode='1' AND quest_id=".$data[quest_id]);
			while($t=mysql_fetch_assoc($result))
			{
				if (!$this->checkstorage($ship_id,$t[goods_id],$t['count']))
				{
					$false = 1;
					break;
				}
			}
		}
		if ($false == 1) return "Das Quest wurde noch nicht erfüllt";
		$result = $this->db->query("SELECT goods_id,count FROM stu_quests_goods WHERE mode='1' AND quest_id=".$data[quest_id]);
		while($t=mysql_fetch_assoc($result)) $this->lowerstorage($ship_id,$t[goods_id],$t['count']);
		$result = $this->db->query("SELECT goods_id,count FROM stu_quests_goods WHERE mode='2' AND quest_id=".$data[quest_id]);
		while($t=mysql_fetch_assoc($result)) $this->upperstorage($ship_id,$t[goods_id],$t['count']);
		$this->db->query("UPDATE stu_quests SET user_id=0,user_time=0 WHERE quest_id=".$quest_id);
		$this->db->query("INSERT INTO stu_quests_user (quest_id,user_id) VALUES ('".$quest_id."','".$this->uid."')");
		return nl2br(str_replace("[name]",$this->sess[user],$data[goodbye_msg]))."<br><br>Quest \"".$data[titel]."\" erfolgreich beendet";
	}

	function loadquestsbyuser() { $this->result = $this->db->query("SELECT quest_id,titel,maxtime,danger,user_time FROM stu_quests WHERE user_id=".$this->uid); }
}
?>