<?php
class sess
{
	function sess()
	{
		global $db,$_POST,$_SESSION;
		if (!$_SESSION['uid'] && !is_array($_POST)) die(show_error(106));
		$this->db = $db;
		if (!$_POST['login'] && !$_POST['pass'] && (!$_SESSION['uid'] || !$_SESSION['login'])) $this->logout;
		if (check_int($_SESSION['uid'])) $this->chklogin();
	}

	function login()
	{
		global $_POST,$_SESSION,$PHPSESSID;
		$result = $this->db->query("SELECT a.id,a.user,a.pass,a.email,a.allys_id,a.race,a.subrace,a.aktiv,a.level,a.npc_type,a.gfx_path,a.propic,a.email_not,a.kn_lez,a.akn_lez,a.rkn_lez,a.skin,UNIX_TIMESTAMP(a.lastaction) as lastaction,a.vac_active,a.delmark,a.vac_blocktime,a.vac_possible,a.research_konstruktion,a.research_technik,a.research_verarbeitung,a.research_dominion,a.sort_mode,a.sort_way,a.wp_overrounds,a.lav_not,b.icq,b.description,b.rkn_text,a.knblock,a.disable_background FROM stu_user as a LEFT JOIN stu_user_profiles as b ON a.id=b.user_id WHERE a.login='".addslashes(str_replace("\"","",$_POST['login']))."' LIMIT 1",4);
		if ($result == 0) return 101;
		if ($result['aktiv'] == 0) return 102;
		if ($result['aktiv'] == 2) return 103;
		if ($result['pass'] != md5($_POST['pass'])) return 104;
		if ($result['delmark'] == 2) return 108;
		!$result['vac_possible'] ? $_SESSION['vac_possible'] = 0 : $_SESSION['vac_possible'] = $result['vac_possible'];
		if ($result["vac_active"] == 1 || (($result["vac_active"] == 0) && ($result['vac_blocktime'] > 0)))
		{
			$_SESSION['vac_blocktime'] = $result['vac_blocktime'];
			if (($result["vac_active"] == 1) && ($result['vac_blocktime'] > time())) return 107;
			$this->db->query("UPDATE stu_user SET vac_active=NULL,vac_blocktime=0 WHERE id=".$result['id']." LIMIT 1");
			$_SESSION['vacmsg'] = "<font color=#FF0000>Der Urlaubsmodus wurde deaktiviert</font>";
		}
		$_SESSION['uid'] = $result['id'];
		$_SESSION['user'] = stripslashes($result['user']);
		$_SESSION['email'] = $result['email'];
		$_SESSION['logintime'] = time();
		$_SESSION['race'] = $result['race'];
		$_SESSION['subrace'] = $result['subrace'];
		$_SESSION['gfx_path'] = $result['gfx_path'];
		$_SESSION['propic'] = $result['propic'];
		$_SESSION['kn_lez'] = $result['kn_lez'];
		$_SESSION['akn_lez'] = $result['akn_lez'];
		$_SESSION['rkn_lez'] = $result['rkn_lez'];
		$_SESSION['skin'] = $result['skin'];
		$_SESSION['allys_id'] = $result['allys_id'];
		$_SESSION['level'] = $result['level'];
		$_SESSION['npc_type'] = $result['npc_type'];
		$_SESSION['vac_possible'] = $result['vac_possible'];
		$_SESSION['r_konstruktion'] = $result['research_konstruktion'];
		$_SESSION['r_technik'] = $result['research_technik'];
		$_SESSION['r_verarbeitung'] = $result['research_verarbeitung'];
		$_SESSION['r_dominion'] = $result['research_dominion'];
		$_SESSION['icq'] = $result['icq'];
		$_SESSION['description'] = stripslashes($result['description']);
		$_SESSION['email_not'] = $result['email_not'];
		$_SESSION['lastaction'] = $result['lastaction'];
		$_SESSION['sort_mode'] = $result['sort_mode'];
		$_SESSION['sort_way'] = $result['sort_way'];
		$_SESSION['wpo'] = $result['wp_overrounds'];
		$_SESSION['lav_not'] = $result['lav_not'];
		$_SESSION['rpgp'] = 0;
		$_SESSION['is_rkn'] = checkfactionkn();
		$_SESSION['rkn_text'] = $result['rkn_text'];
		$_SESSION['login'] = 1;
		$_SESSION['knblock'] = $result['knblock'];
		$_SESSION['disable_background'] = $result['disable_background'];
		$_SESSION['pagesess'] = $this->genPageSession($_SESSION['logintime']);
		// Login verzeichnen
		$this->db->query("INSERT INTO stu_user_iptable (user_id,ip,session,agent,start) VALUES ('".$result['id']."','".getenv("REMOTE_ADDR")."','".$PHPSESSID."','".getenv("HTTP_USER_AGENT")."',NOW())");
		return 0;
	}

	function logout()
	{
		global $eId;
		session_destroy();
		$eId = 105;
	}

	function chklogin()
	{
		global $_SESSION,$PHPSESSID;
		$this->db->query("UPDATE stu_user SET lastaction=NOW() WHERE id=".$_SESSION['uid']." LIMIT 1");
		$this->db->query("UPDATE stu_user_iptable SET end=NOW() WHERE session='".$PHPSESSID."' LIMIT 1");
		if (!check_int($_SESSION['uid']) || $_SESSION['uid'] == 0) show_error(106);
		$data = $this->db->query("SELECT allys_id,research_konstruktion,research_technik,research_verarbeitung,research_dominion,wp_overrounds,aktiv,vac_active,vac_blocktime FROM stu_user WHERE id=".$_SESSION['uid']." LIMIT 1",4);
		if ($data == 0) show_error(109);
		if ($data["vac_active"] == 1 || $data['vac_blocktime'] > 0) {
			$this->db->query("UPDATE stu_user SET vac_active=NULL,vac_blocktime=0 WHERE id=".$_SESSION['uid']." LIMIT 1");
			$_SESSION['vacmsg'] = "<font color=#FF0000>Der Urlaubsmodus wurde deaktiviert</font>";
			header('main.php');
		}
		if ($data['aktiv'] == 2)
		{
			die(show_error(103));
		}
		$_SESSION['r_konstruktion'] = $data['research_konstruktion'];
		$_SESSION['r_technik'] = $data['research_technik'];
		$_SESSION['r_verarbeitung'] = $data['research_verarbeitung'];
		$_SESSION['r_dominion'] = $data['research_dominion'];
		$_SESSION['allys_id'] = $data['allys_id'];
		$_SESSION['wpo'] = $data['wp_overrounds'];
		$_SESSION['lastaction'] = time();
		$_SESSION['rpgp'] = 0;
		$_SESSION['is_rkn'] = checkfactionkn();
		$_SESSION['preps'] = $_SESSION['pagesess'];
		$_SESSION['pagesess'] = $this->genPageSession($_SESSION['logintime']);
		return 0;
	}

	function genPageSession(&$lgtime) {
		$t = microtime();
		return substr(md5($t.$lgtime),rand(0,1),rand(8,9));
	}
}
?>
