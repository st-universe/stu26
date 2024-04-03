<?php
class comm
{
	function comm()
	{
		global $db, $_SESSION;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->user = $_SESSION['user'];
		$this->knlz = $_SESSION['kn_lez'];
		$this->sess = $_SESSION;
		$this->knblock = $_SESSION['knblock'];


		$this->knPageSize = 5;
	}



	function typeLimiter($t, $prefix = "")
	{
		switch ($t) {
			case "shiplog":
				return "1";
				// case "story": return "(".$prefix."type = 'story' OR ".$prefix."type = 'informal' OR ".$prefix."type = 'official' OR ".$prefix."type = 'alliance' OR ".$prefix."type = 'race' OR ".$prefix."type = 'main' OR ".$prefix."type = 'system')";
				// case "informal": return "(".$prefix."type = 'informal' OR ".$prefix."type = 'official' OR ".$prefix."type = 'alliance' OR ".$prefix."type = 'race' OR ".$prefix."type = 'main' OR ".$prefix."type = 'system')";
				// case "official": return "(".$prefix."type = 'official' OR ".$prefix."type = 'alliance' OR ".$prefix."type = 'race' OR ".$prefix."type = 'main' OR ".$prefix."type = 'system')";
				// case "alliance": return "(".$prefix."type = 'alliance' OR ".$prefix."type = 'race' OR ".$prefix."type = 'main' OR ".$prefix."type = 'system')";
				// case "race": return "(".$prefix."type = 'race' OR ".$prefix."type = 'main' OR ".$prefix."type = 'system')";
				// case "main": return "(".$prefix."type = 'main' OR ".$prefix."type = 'system')";
				// case "system": return "(".$prefix."type = 'system')";
			default:
				return "1";
		}
	}

	function getAllLogShips()
	{



		$ships = array();

		$ship = array('name' => "USS Furz (1337) von Changeme (102)", 'id' => 1000);
		array_push($ships, $ship);
		return $ships;
	}




	function getSKN($mark, $type)
	{

		$res = array();
		$res['currentPage'] = 0;
		$res['totalPages'] = 0;
		$res['messages'] = array();



		return $res;
	}






	function getknbymark($m, $t = "official")
	{
		return $this->db->query("SELECT a.id,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.type,a.user_id,a.official,a.rating,a.votes as rv,a.refe,UNIX_TIMESTAMP(a.lastedit) as le,b.race,b.subrace,UNIX_TIMESTAMP(b.lastaction) as lastaction,b.propic,b.id as ruser_id,c.rating as ur FROM stu_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_kn_rating as c ON c.kn_id=a.id AND c.user_id=" . $this->uid . " WHERE " . $this->typeLimiter($t, "a.") . " ORDER BY a.date ASC LIMIT " . $m . ",5");
	}

	function getknrating($id)
	{
		return $this->db->query("SELECT ROUND(SUM(rating)/COUNT(kn_id)) as rat,COUNT(kn_id) as votes FROM stu_kn_rating WHERE kn_id=" . $id . " GROUP BY kn_id", 4);
	}

	function agetknbymark($m)
	{
		return $this->db->query("SELECT a.id,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,UNIX_TIMESTAMP(a.lastedit) as le,b.race,b.subrace,UNIX_TIMESTAMP(b.lastaction) as lastaction,b.propic,b.id as ruser_id FROM stu_ally_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.allys_id=" . $this->sess['allys_id'] . " ORDER BY a.date ASC LIMIT " . $m . ",5");
	}

	function rgetknbymark($m)
	{
		return $this->db->query("SELECT a.id,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,UNIX_TIMESTAMP(a.lastedit) as le,b.race,b.subrace,UNIX_TIMESTAMP(b.lastaction) as lastaction,b.propic,b.id as ruser_id FROM stu_faction_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.faction='" . $this->sess['race'] . "' ORDER BY a.date ASC LIMIT " . $m . ",5");
	}

	function getknbyuser($userid, $m)
	{
		return $this->db->query("SELECT a.id,a.type,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,a.official,a.rating,a.votes as rv,a.refe,b.race,b.subrace,UNIX_TIMESTAMP(b.lastaction) as lastaction,b.propic,b.id as ruser_id FROM stu_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.user_id=" . $userid . " GROUP BY a.id ORDER BY a.date ASC LIMIT " . $m . ",5");
	}

	function getlzcount()
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_kn WHERE id>" . $this->knlz, 1);
	}

	function agetlzcount()
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_ally_kn WHERE allys_id=" . $this->sess['allys_id'] . " AND id>" . $this->sess['akn_lez'], 1);
	}

	function rgetlzcount()
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_faction_kn WHERE faction='" . $this->sess['race'] . "' AND id>" . $this->sess['rkn_lez'], 1);
	}

	function canWriteAllyPost()
	{
		if ($this->uid < 100) return 0;
		$r = 0;
		$r += $this->db->query("SELECT COUNT(allys_id) FROM stu_allylist WHERE praes_user_id='" . $this->uid . "'", 1);
		$r += $this->db->query("SELECT COUNT(allys_id) FROM stu_allylist WHERE vize_user_id='" . $this->uid . "'", 1);
		$r += $this->db->query("SELECT COUNT(allys_id) FROM stu_allylist WHERE auss_user_id='" . $this->uid . "'", 1);
		return $r;
	}

	function asetlz($lz)
	{
		global $_SESSION;
		$this->db->query("UPDATE stu_user SET akn_lez=" . $lz . " WHERE id=" . $this->uid . " LIMIT 1");
		$_SESSION['akn_lez'] = $lz;
		return "Lesezeichen bei Beitrag " . $lz . " gesetzt";
	}

	function rsetlz($lz)
	{
		global $_SESSION;
		$this->db->query("UPDATE stu_user SET rkn_lez=" . $lz . " WHERE id=" . $this->uid . " LIMIT 1");
		$_SESSION['rkn_lez'] = $lz;
		return "Lesezeichen bei Beitrag " . $lz . " gesetzt";
	}

	function setlz($lz)
	{
		global $_SESSION;
		$this->db->query("UPDATE stu_user SET kn_lez=" . $lz . " WHERE id=" . $this->uid . " LIMIT 1");
		$_SESSION['kn_lez'] = $lz;
		return "Lesezeichen bei Beitrag " . $lz . " gesetzt";
	}

	function addknmsg($titel = "", $text, $refe = "", $rpg = "", $lz = "", $type = "official")
	{
		global $global_path;
		$block = $this->db->query("SELECT knblock FROM stu_user WHERE id=" . $this->uid . " LIMIT 1", 1);
		if ($block == 1) return "KN-Zugriff wurde gesperrt.";

		// if ($this->db->query("SELECT level FROM stu_user WHERE id=".$this->uid." LIMIT 1",1) < 3) return "Zum Erstellen eines KN-Posts wird Level 4 ben�tigt.";

		include_once($global_path . "/inc/inputfilter.inc.php");
		$filter = new InputFilter(array("b", "i", "br"), array(), 0, 0);
		$text = $filter->process($text);
		$id = $this->db->query("INSERT INTO stu_kn (titel,text,date,username,user_id,refe,official,type) VALUES ('" . str_replace("\"", "", addslashes($titel)) . "','" . addslashes($text) . "',NOW(),'" . addslashes($this->user) . "','" . $this->uid . "','" . $refe . "','" . ($rpg == 1 ? "1" : "NULL") . "','" . $type . "')", 5);
		if ($lz == 1) $this->setlz($id);
		return "Beitrag hinzugef�gt";
	}

	function aaddknmsg($titel = "", $text, $lz = "")
	{
		global $global_path;
		include_once($global_path . "/inc/inputfilter.inc.php");
		$filter = new InputFilter(array("b", "i", "br"), array(), 0, 0);
		$text = $filter->process($text);
		$id = $this->db->query("INSERT INTO stu_ally_kn (allys_id,titel,text,date,username,user_id) VALUES ('" . $this->sess[allys_id] . "','" . str_replace("\"", "", addslashes($titel)) . "','" . addslashes($text) . "',NOW(),'" . addslashes($this->user) . "','" . $this->uid . "')", 5);
		if ($lz == 1) $this->asetlz($id);
		return "Beitrag hinzugef�gt";
	}

	function arddknmsg($titel = "", $text, $lz)
	{
		global $global_path;
		$block = $this->db->query("SELECT knblock FROM stu_user WHERE id=" . $this->uid . " LIMIT 1", 1);
		if ($block == 1) return "KN-Zugriff wurde gesperrt.";

		if ($this->db->query("SELECT level FROM stu_user WHERE id=" . $this->uid . " LIMIT 1", 1) < 3) return "Zum Erstellen eines KN-Posts wird Level 4 ben�tigt.";

		include_once($global_path . "/inc/inputfilter.inc.php");
		$filter = new InputFilter(array("b", "i", "br"), array(), 0, 0);
		$text = $filter->process($text);
		$id = $this->db->query("INSERT INTO stu_faction_kn (faction,titel,text,date,username,user_id) VALUES ('" . $this->sess[race] . "','" . str_replace("\"", "", addslashes($titel)) . "','" . addslashes($text) . "',NOW(),'" . addslashes($this->user) . "','" . $this->uid . "')", 5);
		if ($lz == 1) $this->rsetlz($id);
		return "Beitrag hinzugef�gt";
	}

	function getknmsgbyid($id)
	{
		return $this->db->query("SELECT a.id,a.type,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,a.refe,a.type,b.race,b.subrace,b.propic,UNIX_TIMESTAMP(b.lastaction) as lastaction,b.id as ruser_id FROM stu_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.id=" . $id);
	}

	function agetknmsgbyid($id)
	{
		return $this->db->query("SELECT a.id,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,b.race,b.subrace,b.propic,UNIX_TIMESTAMP(b.lastaction) as lastaction FROM stu_ally_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.allys_id=" . $this->sess['allys_id'] . " AND a.id=" . $id);
	}

	function rgetknmsgbyid($id)
	{
		return $this->db->query("SELECT a.id,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,b.race,b.subrace,b.propic,UNIX_TIMESTAMP(b.lastaction) as lastaction FROM stu_faction_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.faction='" . $this->sess['race'] . "' AND a.id=" . $id);
	}

	function getknmsgbystring($string)
	{
		$ss = "WHERE ";
		if (substr_count($string, " ") > 0) {
			$ex = explode(" ", $string);
			if (count($ex) == 0) return 0;
			for ($i = 0; $i < count($ex); $i++) {
				$ss .= "a.text LIKE '%" . addslashes($ex[$i]) . "%'";
				if ($ex[$i + 1] != "") $ss .= " AND ";
			}
		} else $ss .= "a.text LIKE '%" . addslashes($string) . "%'";
		return $this->db->query("SELECT a.id,a.type,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,a.refe,a.type,b.race,b.subrace,b.propic,UNIX_TIMESTAMP(b.lastaction) as lastaction,b.id as ruser_id FROM stu_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id " . $ss . " ORDER BY a.id DESC LIMIT 100;");
	}

	function agetknmsgbystring($string)
	{
		$ss = "WHERE ";
		if (substr_count($string, " ") > 0) {
			$ex = explode(" ", $string);
			if (count($ex) == 0) return 0;
			for ($i = 0; $i < count($ex); $i++) {
				$ss .= "a.text LIKE '%" . addslashes($ex[$i]) . "%'";
				if ($ex[$i + 1] != "") $ss .= " AND ";
			}
		} else $ss .= "a.text LIKE '%" . addslashes($string) . "%'";
		return $this->db->query("SELECT a.id,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,b.race,b.subrace,b.propic,UNIX_TIMESTAMP(b.lastaction) as lastaction FROM stu_ally_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id " . $ss . " AND a.allys_id=" . $this->sess['allys_id'] . " ORDER BY a.id DESC");
	}

	function rgetknmsgbystring($string)
	{
		$ss = "WHERE ";
		if (substr_count($string, " ") > 0) {
			$ex = explode(" ", $string);
			if (count($ex) == 0) return 0;
			for ($i = 0; $i < count($ex); $i++) {
				$ss .= "a.text LIKE '%" . addslashes($ex[$i]) . "%'";
				if ($ex[$i + 1] != "") $ss .= " AND ";
			}
		} else $ss .= "a.text LIKE '%" . addslashes($string) . "%'";
		return $this->db->query("SELECT a.id,a.titel,a.text,UNIX_TIMESTAMP(a.date) as date,a.username,a.user_id,b.propic,b.race,b.subrace,UNIX_TIMESTAMP(b.lastaction) as lastaction FROM stu_faction_kn as a LEFT JOIN stu_user as b ON a.user_id=b.id " . $ss . " AND a.faction='" . $this->sess['race'] . "' ORDER BY a.id DESC");
	}

	function editknmsg($titel = "", $text, $refe = "", $id)
	{
		$filter = new InputFilter(array("b", "i", "br"), array(), 0, 0);
		$text = $filter->process($text);
		$this->db->query("UPDATE stu_kn SET titel='" . addslashes($titel) . "',text='" . addslashes($text) . "',refe='" . $refe . "',lastedit=NOW() WHERE id=" . $id);
	}

	function aeditknmsg($titel = "", $text, $id)
	{
		$filter = new InputFilter(array("b", "i", "br"), array(), 0, 0);
		$text = $filter->process($text);
		$this->db->query("UPDATE stu_ally_kn SET titel='" . addslashes($titel) . "',text='" . addslashes($text) . "',lastedit=NOW() WHERE allys_id=" . $this->sess['allys_id'] . " AND id=" . $id);
	}

	function reditknmsg($titel = "", $text, $id)
	{
		$filter = new InputFilter(array("b", "i", "br"), array(), 0, 0);
		$text = $filter->process($text);
		$this->db->query("UPDATE stu_faction_kn SET titel='" . addslashes($titel) . "',text='" . addslashes($text) . "',lastedit=NOW() WHERE faction='" . $this->sess['race'] . "' AND id=" . $id);
	}

	function getpms($cat, $pa)
	{
		return $this->db->query("SELECT a.id,a.send_user,a.text,UNIX_TIMESTAMP(a.date) as date,a.new,a.replied,b.user,b.race,b.propic FROM stu_pms as a LEFT JOIN stu_user as b ON a.send_user=b.id WHERE a.recip_user=" . $this->uid . " AND a.type='" . $cat . "' AND a.recip_del='0' ORDER BY a.id DESC LIMIT " . (($pa - 1) * 10) . ",10");
	}

	function getupms($userId, $pa)
	{
		if (!check_int($userId)) return 0;
		$this->spmc = $this->db->query("SELECT COUNT(id) FROM stu_pms WHERE ((recip_user=" . $this->uid . " AND send_user=" . $userId . " AND recip_del='0') || (send_user=" . $this->uid . " AND recip_user=" . $userId . " AND send_del='0')) AND type='1'", 1);
		return $this->db->query("SELECT a.id,a.send_user,a.text,UNIX_TIMESTAMP(a.date) as date,a.new,a.replied,b.user,b.race,b.propic FROM stu_pms as a LEFT JOIN stu_user as b ON a.send_user=b.id WHERE ((a.recip_user=" . $this->uid . " AND a.send_user=" . $userId . " AND a.recip_del='0') || (a.send_user=" . $this->uid . " AND a.recip_user=" . $userId . " AND a.send_del='0')) AND a.type='1' ORDER BY a.id DESC LIMIT " . (($pa - 1) * 10) . ",10");
	}

	function getsearchpms($string, $pa)
	{
		if (strlen($string) < 3) {
			$this->spmc = 0;
			return 0;
		}
		$ss = "AND ";
		if (substr_count($string, " ") > 0) {
			$ex = explode(" ", $string);
			if (count($ex) == 0) return 0;
			for ($i = 0; $i < count($ex); $i++) {
				$ss .= "a.text LIKE '%" . addslashes($ex[$i]) . "%'";
				if ($ex[$i + 1] != "") $ss .= " AND ";
			}
		} else $ss .= "a.text LIKE '%" . addslashes($string) . "%'";
		$this->spmc = $this->db->query("SELECT COUNT(a.id) FROM stu_pms as a LEFT JOIN stu_user as b ON a.send_user=b.id WHERE a.recip_user=" . $this->uid . " AND type='1' AND recip_del='0' " . $ss, 1);
		return $this->db->query("SELECT a.id,a.send_user,a.text,UNIX_TIMESTAMP(a.date) as date,a.new,a.replied,b.user,b.race,b.propic FROM stu_pms as a LEFT JOIN stu_user as b ON a.send_user=b.id WHERE a.recip_user=" . $this->uid . " AND type='1' AND recip_del='0' " . $ss . " ORDER BY a.id DESC LIMIT " . (($pa - 1) * 10) . ",10");
	}

	function markasread(&$id)
	{
		return $this->db->query("UPDATE stu_pms SET new=0 WHERE id=" . $id . " LIMIT 1");
	}

	function markasunread(&$id)
	{
		return $this->db->query("UPDATE stu_pms SET new=1 WHERE id=" . $id . " LIMIT 1");
	}

	function sendpm($sender, $recipient, $text, $type, $rpl = 0)
	{
		global $global_path;
		include_once($global_path . "/inc/inputfilter.inc.php");
		$filter = new InputFilter(array("font", "b", "i", "br"), array("color"), 0, 0);
		$text = $filter->process($text);
		$this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('" . $sender . "','" . $recipient . "','" . addslashes($text) . "','" . $type . "',NOW())");
		if ($rpl > 0 && check_int($rpl)) $this->db->query("UPDATE stu_pms SET replied='1' WHERE recip_user=" . $this->uid . " AND id=" . $rpl . " LIMIT 1");
		if ($type == 1) {
			$dat = $this->db->query("SELECT user,email_not,email FROM stu_user WHERE id=" . $recipient . " LIMIT 1", 4);
			if ($dat['email_not'] != 1) return;
			mail($dat['email'], "Neue Private Nachricht", "Hallo " . stripslashes($dat['user']) . ". Es ist eine neue private Nachricht von " . stripslashes($this->db->query("SELECT user FROM stu_user WHERE id=" . $sender, 1)) . " f�r Dich eingetroffen<br><br>" . $text . "", "From: Star Trek Universe <automail@changeme.de>
Content-Type: text/html");
		}
	}

	function sendpm2($sender, $recipient, $text, $type, $rpl = 0)
	{
		global $global_path;
		$this->db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('" . $sender . "','" . $recipient . "','" . addslashes($text) . "','" . $type . "',NOW())");
		if ($rpl > 0 && check_int($rpl)) $this->db->query("UPDATE stu_pms SET replied='1' WHERE recip_user=" . $this->uid . " AND id=" . $rpl . " LIMIT 1");
		if ($type == 1) {
			$dat = $this->db->query("SELECT user,email_not,email FROM stu_user WHERE id=" . $recipient . " LIMIT 1", 4);
			if ($dat['email_not'] != 1) return;
			mail($dat['email'], "Neue Private Nachricht", "Hallo " . stripslashes($dat['user']) . ". Es ist eine neue private Nachricht von " . stripslashes($this->db->query("SELECT user FROM stu_user WHERE id=" . $sender, 1)) . " f�r Dich eingetroffen<br><br>" . $text . "", "From: Star Trek Universe <automail@changeme.de>
Content-Type: text/html");
		}
	}

	function delpm($id)
	{
		$this->db->query("UPDATE stu_pms SET new='0',recip_del='1' WHERE recip_user=" . $this->uid . " AND id=" . $id . " LIMIT 1");
	}

	function delapm($cat)
	{
		$this->db->query("UPDATE stu_pms SET new='0',recip_del='1' WHERE recip_user=" . $this->uid . " AND type=" . $cat);
	}

	function delspm($id)
	{
		$this->db->query("UPDATE stu_pms SET send_del='1' WHERE send_user=" . $this->uid . " AND id=" . $id);
	}

	function delaspm($cat)
	{
		$this->db->query("UPDATE stu_pms SET new='0',send_del='1' WHERE send_user=" . $this->uid . " AND type='" . $cat . "'");
	}

	function getsendpms($cat, $m)
	{
		return $this->db->query("SELECT a.id,a.send_user,a.recip_user,a.text,UNIX_TIMESTAMP(a.date) as date,b.user FROM stu_pms as a LEFT JOIN stu_user as b ON a.recip_user=b.id WHERE a.send_user=" . $this->uid . " AND type='" . $cat . "' AND send_del='0' ORDER BY a.id DESC LIMIT " . $m . ",10");
	}

	function markallasread($cat)
	{
		$this->db->query("UPDATE stu_pms SET new=0 WHERE recip_user=" . $this->uid . " AND type=" . $cat);
	}

	function getnewpmcount()
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_pms WHERE recip_user=" . $this->uid . " AND new=1", 1);
	}

	function getnewpmccat($cat)
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_pms WHERE type=" . $cat . " AND new='1' AND recip_user='" . $this->uid . "'", 1);
	}

	function getpmccat($cat)
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_pms WHERE type=" . $cat . " AND recip_del='0' AND recip_user='" . $this->uid . "'", 1);
	}

	function getapmccat($cat)
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_pms WHERE type=" . $cat . " AND send_del='0' AND send_user='" . $this->uid . "'", 1);
	}

	function getcontacts()
	{
		return $this->db->query("SELECT a.recipient,a.mode,a.comment,b.user FROM stu_contactlist as a LEFT JOIN stu_user as b ON a.recipient=b.id WHERE a.user_id=" . $this->uid . " ORDER BY a.recipient");
	}

	function getpmbyid($id)
	{
		return $this->db->query("SELECT a.id,a.send_user,a.recip_user,a.text,UNIX_TIMESTAMP(a.date) as date,b.user,b.race,b.propic FROM stu_pms as a LEFT JOIN stu_user as b ON b.id=a.send_user WHERE a.id=" . $id . " AND a.recip_user=" . $this->uid . " AND a.recip_del='0' LIMIT 1", 4);
	}

	function setcontact($id, $mode)
	{
		if ($this->db->query("SELECT id FROM stu_user WHERE id=" . $id, 1) == 0 || $id == $this->uid) return 0;
		$result = $this->db->query("SELECT user_id FROM stu_contactlist WHERE user_id=" . $this->uid . " AND recipient=" . $id . " LIMIT 1", 1);
		if ($result == 0) {
			$this->db->query("INSERT INTO stu_contactlist (user_id,recipient,mode) VALUES ('" . $this->uid . "','" . $id . "','" . $mode . "')");
			if ($mode == 3) $this->sendpm($this->uid, $id, "Der Siedler betrachtet Dich ab jetzt als Feind", 1);
		} else {
			$omode = $this->db->query("SELECT mode FROM stu_contactlist WHERE user_id=" . $this->uid . " AND recipient=" . $id . " LIMIT 1", 1);
			$res = $this->db->query("UPDATE stu_contactlist SET mode='" . $mode . "' WHERE user_id=" . $this->uid . " AND recipient=" . $id . " LIMIT 1", 6);
			if ($omode != 3 && $res > 0 && $mode == 3) $this->sendpm($this->uid, $id, "Der Siedler betrachtet Dich ab jetzt als Feind", 1);
		}
		return 1;
	}

	function delcontact($id)
	{
		$this->db->query("DELETE FROM stu_contactlist WHERE user_id=" . $this->uid . " AND recipient=" . $id);
	}

	function loadecalls()
	{
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE UNIX_TIMESTAMP(date)<" . (time() - 86400));
		$this->result = $this->db->query("SELECT a.user_id,a.ships_id,a.text,UNIX_TIMESTAMP(date) as date,b.user,c.name FROM stu_ships_ecalls as a LEFT JOIN stu_user as b ON a.user_id=b.id LEFT JOIN stu_ships as c ON a.ships_id=c.id ORDER BY a.date DESC");
	}

	function delnr()
	{
		global $_GET;
		$this->db->query("DELETE FROM stu_ships_ecalls WHERE user_id=" . $this->uid . " AND ships_id=" . $_GET['ds'] . " LIMIT 1");
		return "Notruf gel�scht";
	}

	function checkfactionkn()
	{
		if ($this->uid < 100) return 1;
		switch ($this->sess['race']) {
			case 1:
				$npc = 10;
				break;
			case 2:
				$npc = 11;
				break;
			case 3:
				$npc = 12;
				break;
			case 4:
				$npc = 13;
				break;
			case 5:
				$npc = 14;
				break;
			case 9:
				$npc = 0;
				break;
		}
		//return $this->db->query("SELECT rkn FROM stu_npc_contactlist WHERE user_id=".$npc." AND recipient=".$this->uid." LIMIT 1",1);
		return 1;
	}

	function knvote($id, $rat)
	{
		if ($rat != 1 && $rat != 2 && $rat != 3 && $rat != 4 && $rat != 5) return;
		if ($this->db->query("SELECT rating FROM stu_kn_rating WHERE user_id=" . $this->uid . " AND kn_id=" . $id, 1) != 0) return;
		if ($this->sess["level"] < 4 || $this->uid < 101) return;
		$data = $this->db->query("SELECT UNIX_TIMESTAMP(date) as date_tsp,user_id FROM stu_kn WHERE id=" . $id, 4);
		if ($data[date_tsp] < time() - 86400) return;
		if ($data[user_id] == $this->uid) return;
		$this->db->query("INSERT INTO stu_kn_rating (kn_id,user_id,rating,date) VALUES ('" . $id . "','" . $this->uid . "','" . $rat . "',NOW())");
	}

	function getenemyuser()
	{
		return $this->db->query("SELECT a.user_id,b.user FROM stu_contactlist as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.mode='3' AND a.recipient=" . $this->uid . " ORDER BY a.user_id");
	}

	function getfriendlyuser()
	{
		return $this->db->query("SELECT a.user_id,b.user FROM stu_contactlist as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.mode='1' AND a.recipient=" . $this->uid . " ORDER BY a.user_id");
	}

	function getignorelist()
	{
		return $this->db->query("SELECT a.recipient,b.user FROM stu_ignorelist as a LEFT JOIN stu_user as b ON a.recipient=b.id WHERE a.user_id=" . $this->uid);
	}

	function setignore($id)
	{
		if ($this->db->query("SELECT id FROM stu_user WHERE id=" . $id, 1) == 0 || $id == $this->uid) return 0;
		$result = $this->db->query("SELECT recipient FROM stu_ignorelist WHERE user_id=" . $this->uid . " AND recipient=" . $id, 1);
		if ($result == 0) $this->db->query("INSERT INTO stu_ignorelist (user_id,recipient) VALUES ('" . $this->uid . "','" . $id . "')");
		return 1;
	}

	function delignore($id)
	{
		$this->db->query("DELETE FROM stu_ignorelist WHERE user_id=" . $this->uid . " AND recipient=" . $id . " LIMIT 1");
	}

	function getignoreuser()
	{
		return $this->db->query("SELECT a.user_id,b.user FROM stu_ignorelist as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.recipient=" . $this->uid);
	}

	function getknposts()
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_kn", 1);
	}

	function getuknposts($userid)
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_kn WHERE user_id=" . $userid, 1);
	}

	function getaknposts()
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_ally_kn WHERE allys_id=" . $this->sess['allys_id'], 1);
	}

	function getrknposts()
	{
		return $this->db->query("SELECT COUNT(id) FROM stu_faction_kn WHERE faction='" . $this->sess['race'] . "'", 1);
	}

	function checkallypresident($uid, $allyid)
	{
		return $this->db->query("SELECT allys_id FROM stu_allylist WHERE allys_id=" . $allyid . " AND (praes_user_id=" . $uid . " OR vize_user_id=" . $uid . ")", 1);
	}

	function delaknmsg($id, $allyid)
	{
		$this->db->query("DELETE FROM stu_ally_kn WHERE id=" . $id . " AND allys_id=" . $allyid . " LIMIT 1");
		return "Beitrag gel�scht";
	}

	function getpmprefix(&$ex1, &$ex2)
	{
		$s1 = $this->db->query("SELECT id,name,systems_id,sx,sy,cx,cy,cloak FROM stu_ships WHERE id=" . $ex1 . " AND user_id=" . $this->uid . " LIMIT 1", 4);
		$s2 = $this->db->query("SELECT id,name,systems_id,sx,sy,cx,cy,cloak FROM stu_ships WHERE id=" . $ex2 . " LIMIT 1", 4);
		if ($s2['cloak'] == 1 && $this->db->query("SELECT ships_id FROM stu_ships_decloaked WHERE ships_id=" . $ex2 . " AND user_id=" . $this->uid . " AND UNIX_TIMESTAMP(date)=0", 1) != $ex2) return;
		if ($s1['systems_id'] == $s2['systems_id'] && $s1['systems_id'] > 0) {
			if ($s1['sx'] != $s2['sx'] || $s1['sy'] != $s2['sy']) return;
		} elseif ($s1['cx'] != $s2['cx'] || $s1['cy'] != $s2['cy']) return;
		return "Die " . stripslashes(strip_tags($s1['name'])) . " hat der " . stripslashes(strip_tags($s2['name'])) . " in Sektor " . ($s1['systems_id'] > 0 ? $s1['sx'] . "|" . $s1['sy'] . " (" . $this->db->query("SELECT name FROM stu_systems WHERE systems_id=" . $s1['systems_id'], 1) . "-System " . $s1['cx'] . "|" . $s1['cy'] . ")" : $s1['cx'] . "|" . $s1['cy']) . " eine Nachricht geschickt\n\n";
	}

	function get_other_kn_posts($userId, $knId)
	{
		$result = $this->db->query("SELECT id,official FROM stu_kn WHERE user_id=" . $userId . " AND id!=" . $knId . " ORDER BY id DESC LIMIT 10");
		if (mysql_num_rows($result) == 0) return;
		$frm = "<br><div style=\"color: #848484; font-size: 7pt;\"><b>Weitere:</b><br>";
		while ($data = mysql_fetch_assoc($result)) {
			$frm .= "<a href=?p=comm&s=sb&sbn=" . $data['id'] . " style=\"color: #848484; font-size: 7pt;\">" . $data['id'] . " (" . ($data['official'] == 1 ? "R" : "O") . ")</a><br>";
		}
		$frm .= "</div>";
		return $frm;
	}

	function get_ecall_count()
	{
		return $this->db->query("SELECT COUNT(*) FROM stu_ships_ecalls WHERE UNIX_TIMESTAMP(date)>" . (time() - 86400), 1);
	}

	function update_contact_comment($userId, $comment)
	{
		$this->db->query("UPDATE stu_contactlist SET comment='" . addslashes(str_replace("\"", "", $comment)) . "' WHERE user_id=" . $this->uid . " AND recipient='" . $userId . "' LIMIT 1");
	}

	function get_rpg_players()
	{
		return $this->db->query("SELECT a.user_id,a.recipient,b.user,b.race FROM stu_npc_contactlist as a LEFT JOIN stu_user as b ON b.id=a.recipient WHERE a.rkn='1' AND b.race+9=a.user_id AND a.user_id BETWEEN 10 AND 15 GROUP BY a.recipient ORDER BY a.user_id,a.recipient");
	}

	function get_rpg_player_count($userId)
	{
		return $this->db->query("SELECT COUNT(user_id) FROM stu_npc_contactlist WHERE user_id=" . $userId . " AND rkn='1'", 1);
	}
}