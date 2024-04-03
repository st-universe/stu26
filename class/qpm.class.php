<?php
class qpm
{
	function __construct()
	{
		global $db, $_SESSION;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->sess = $_SESSION;
	}

	function get_ecall_count()
	{
		return $this->db->query("SELECT COUNT(*) FROM stu_ships_ecalls WHERE UNIX_TIMESTAMP(date)>" . (time() - 86400), 1);
	}

	function send_pm($sender, $recipient, $text, $type)
	{
		$this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('" . $sender . "','" . $recipient . "','" . addslashes($text) . "','" . $type . "',NOW())");
		if ($type == 1) {
			$dat = $this->db->query("SELECT user,email_not,email FROM stu_user WHERE id=" . $recipient . " LIMIT 1", 4);
			if ($dat['email_not'] != 1) return;
			mail($dat['email'], "Neue Private Nachricht", "Hallo " . stripslashes($dat['user']) . ". Es ist eine neue private Nachricht von " . stripslashes($this->db->query("SELECT user FROM stu_user WHERE id=" . $sender . " LIMIT 1", 1)) . " f�r Dich eingetroffen<br><br>" . $text . "", "From: Star Trek Universe <automail@changeme.de>
Content-Type: text/html");
		}
	}
}