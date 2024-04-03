<?php
class main
{
	function __construct()
	{
		global $db, $dbd;
		$this->db = $db;

		if ($dbd['database'] != "changeme") {
			$this->pc = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE id>100", 1);
			$this->opc = $this->db->query("SELECT COUNT(id) FROM stu_user WHERE id>100 AND aktiv='1' AND UNIX_TIMESTAMP(lastaction)>" . (time() - 300), 1);
			$this->cr = $this->db->query("SELECT runde FROM stu_game_rounds ORDER BY runde DESC LIMIT 1", 1);
			$state = $this->db->query("SELECT value FROM stu_game_vars WHERE var='state'", 1);
			if ($state == 1) $this->state = "Online";
			if ($state == 2) $this->state = "Tick";
			if ($state == 3) $this->state = "Wartung";
			$this->chatc = $this->db->query("SELECT value FROM stu_game_vars WHERE var='chat'", 1);
		}
	}

	function override_db_data()
	{
		global $dbd;
		$dbd['database'] = "changeme";
	}

	function get_changelog()
	{
		$this->chlog = $this->db->query("SELECT a.topic_id,a.topic_title,b.username FROM phpbb_topics as a LEFT JOIN phpbb_users as b ON b.user_id=a.topic_poster WHERE a.forum_id=3 ORDER BY a.topic_time DESC LIMIT 4");
	}

	function register()
	{
		global $_SESSION;
		// $userid = $this->db->query("INSERT INTO stu_user (user,search_user,login,pass,email,race,aktiv,skin,lastaction,tick) VALUES ('".$_SESSION["ud"][name]."','".strip_tags(str_replace("'","",$_SESSION["ud"][name]))."','".$_SESSION["ud"][login]."','".md5($_SESSION["ud"][pwd])."','".$_SESSION["ud"][email]."','".$_SESSION["ud"][faction]."',NULL,'".$_SESSION["ud"][faction]."',NOW(),'".rand(1,8)."')",5);
		$userid = $this->db->query("INSERT INTO stu_user (user,search_user,login,pass,email,race,aktiv,skin,lastaction,tick) VALUES ('" . $_SESSION["ud"][name] . "','" . strip_tags(str_replace("'", "", $_SESSION["ud"][name])) . "','" . $_SESSION["ud"][login] . "','" . md5($_SESSION["ud"][pwd]) . "','" . $_SESSION["ud"][email] . "','" . $_SESSION["ud"][faction] . "',NULL,'6',NOW(),'" . rand(1, 8) . "')", 5);
		$actcode = substr(md5($_SESSION["ud"][email] . $userid), 0, 10);
		$this->db->query("UPDATE stu_user SET actcode='" . $actcode . "' WHERE id=" . $userid);
		mail($_SESSION["ud"][email], "STU Anmeldung", "Hallo " . $_SESSION["ud"][login] . "<br><br>
		Du erh�lst hiermit Deinem Aktivierungscode f�r STU. Klicke einfach auf den Link<br>
		<a href=http://www.stuniverse.de/index.php?p=act&actcode=" . $actcode . "&user=" . $userid . " target=_blank>Aktivierung</a><br><br>
		(Fall der Link nicht klickbar ist: http://www.stuniverse.de/index.php?p=act&actcode=" . $actcode . "&user=" . $userid . " )<br><br>
		Viel Spa� mit Star Trek Universe.<br><br>
		Mit freundlichen Gr��en<br><br>
		Das STU-Team", "From: Star Trek Universe <automail@changeme.de>
Content-Type: text/html");
		unset($_SESSION["ud"]);
		unset($_SESSION[step]);
	}

	function activateuser($uid, $act)
	{
		$user = $this->db->query("SELECT id,email FROM stu_user WHERE id=" . $uid . " AND actcode='" . $act . "'", 4);
		if ($user == 0) die();
		if (substr(md5($user[email] . $user[id]), 0, 10) != $act) die();
		$this->db->query("UPDATE stu_user SET aktiv='1',actcode='' WHERE id=" . $user[id] . " LIMIT 1");
	}

	function getlatestnews()
	{
		return $this->db->query("SELECT UNIX_TIMESTAMP(date) as date_tsp,subject,text,refs,pic,color FROM stu_news ORDER BY date DESC LIMIT 3");
	}
}
