<?php
class ally extends qpm
{
	function ally()
	{
		global $db,$_SESSION;
		$this->db = $db;
		$this->uid = $_SESSION['uid'];
		$this->sess = $_SESSION;
		$this->ar = 0;
		$this->data = 0;
	}

	function getallylist() { $this->ar = $this->db->query("SELECT a.allys_id,a.name,COUNT(b.id) as mc,c.user FROM stu_allylist as a LEFT JOIN stu_user as b USING(allys_id) LEFT JOIN stu_user as c ON a.praes_user_id=c.id GROUP BY a.allys_id ORDER BY a.allys_id"); }

	function loadally($id) { $this->data = $this->db->query("SELECT a.allys_id,a.name,a.praes_user_id,a.vize_user_id,a.auss_user_id,a.homepage,a.descr,b.user FROM stu_allylist as a LEFT JOIN stu_user as b ON a.praes_user_id=b.id WHERE a.allys_id=".$id,4);	}

	function getmembers($id) { $this->ar = $this->db->query("SELECT id,user,UNIX_TIMESTAMP(lastaction) as date,race,subrace FROM stu_user WHERE allys_id=".$id." ORDER BY id"); }

	function newally()
	{
		global $_GET;
		
		// if ($this->db->query("SELECT level FROM stu_user WHERE id=".$this->uid." LIMIT 1",1) < 4) return "Zum Erstellen einer Allianz wird Level 4 benötigt.";
		
		
		$filter = new InputFilter(array("font","b","i"), array("color"), 0, 0);
		$name = format_string($filter->process($_GET['name']));
		$filter = new InputFilter(array("font","b","i","br","img","div"), array("color","src","align"), 0, 0);
		$desc = $filter->process($_GET['desc']);
		$this->db->query("START TRANSACTION");
		$aid = $this->db->query("INSERT INTO stu_allylist (name,praes_user_id,homepage,descr) VALUES ('".$name."','".$_SESSION['uid']."','".addslashes($_GET['homep'])."','".addslashes($desc)."')",5);
		if (!$aid || !is_numeric($aid))
		{
			$this->db->query("ROLLBACK");
			return "Fehler";
		}
		$this->db->query("UPDATE stu_user SET allys_id=".$aid." WHERE id=".$_SESSION['uid']);
		$this->db->query("COMMIT");
		$_SESSION['allys_id'] = $aid;
		return "Allianz erstellt";
	}

	function joinally($allyid)
	{
		global $_SESSION;
		if ($this->db->query("SELECT allys_id FROM stu_ally_invitations WHERE user_id=".$this->uid." AND allys_id=".$allyid,1) == 0) return "Es besteht kein Angebot dieser Allianz";
		$this->db->query("UPDATE stu_user SET allys_id=".$allyid." WHERE id=".$this->uid);
		$_SESSION['allys_id'] = $allyid;
		$this->db->query("DELETE FROM stu_ally_invitations WHERE user_id=".$this->uid);
		$this->db->query("INSERT INTO stu_ally_kn (user_id,username,titel,text,date,allys_id) VALUES ('1','Niemand','Neues Allianzmitglied','".addslashes($this->sess['user'])." ist der Allianz beigetreten',NOW(),'".$_SESSION['allys_id']."')");
		return "Du bist der Allianz beigetreten";
	}

	function delally()
	{
		global $_SESSION;
		$this->db->query("START TRANSACTION");
		$aid = $this->db->query("DELETE FROM stu_allylist WHERE praes_user_id=".$this->uid,6);
		if ($aid == 0)
		{
			$this->db->query("ROLLBACK");
			return;
		}
		$this->db->query("DELETE FROM stu_ally_relationship WHERE allys_id1=".$_SESSION['allys_id']." OR allys_id2=".$_SESSION['allys_id']);
		$this->db->query("UPDATE stu_user SET allys_id=0 WHERE allys_id=".$_SESSION['allys_id']);
		$this->db->query("COMMIT");
		$this->db->query("DELETE FROM stu_ally_invitations WHERE allys_id=".$_SESSION['allys_id']);
		return "Die Allianz wurde gelöscht";
	}

	function delfromally($uid)
	{
		if ($this->data['praes_user_id'] == $uid) return "Der Präsident kann nicht aus der Allianz geworfen werden";
		if ($this->db->query("SELECT id FROM stu_user WHERE id=".$uid." AND allys_id=".$this->data['allys_id']." LIMIT 1",1) == 0) exit;
		$this->db->query("START TRANSACTION");
		$this->db->query("UPDATE stu_user SET allys_id=0 WHERE id=".$uid);
		$this->db->query("UPDATE stu_allylist SET vize_user_id=0 WHERE vize_user_id=".$uid);
		$this->db->query("UPDATE stu_allylist SET auss_user_id=0 WHERE auss_user_id=".$uid);
		$this->db->query("COMMIT");
		$this->send_pm(1,$uid,addslashes("Du wurdest aus der Allianz ".$this->data['name']." entfernt"),1);
		return "Der Siedler wurde aus der Allianz entfernt";
	}

	function checkbez($selfid,$tarid) { return $this->db->query("SELECT type FROM stu_ally_relationship WHERE (allys_id1=".$selfid." AND allys_id2=".$tarid.") OR (allys_id2=".$selfid." AND allys_id1=".$tarid.")",1); }

	function loadrelationships() { $this->result = $this->db->query("SELECT a.allys_id1,a.allys_id2,a.type,UNIX_TIMESTAMP(a.date) as date_tsp,b.name,c.name as name2 FROM stu_ally_relationship as a LEFT JOIN stu_allylist as b ON a.allys_id1=b.allys_id LEFT JOIN stu_allylist as c ON a.allys_id2=c.allys_id WHERE a.allys_id1=".$this->sess['allys_id']." OR a.allys_id2=".$this->sess['allys_id']." ORDER BY type"); }

	function editrelationships($ally,$mode)
	{
		foreach($ally as $key => $value)
		{
			if (!check_int($value) || (!check_int($mode[$key]) && $mode[$key] != "del")) continue;
			if ($mode[$key] == "del") $result .= $this->delrelationship($value)."<br>";
			else $result .= $this->editrelationship($value,$mode[$key])."<br>";
		}
		return $result;
	}

	function delrelationship($ally)
	{
		$data = $this->db->query("SELECT name,praes_user_id,vize_user_id,auss_user_id FROM stu_allylist WHERE allys_id=".$ally,4);
		if ($data == 0) return;
		$text = $this->sess['user']." hat die Beziehung zu Deiner Allianz gelöscht";
		$this->send_pm($this->uid,$data['praes_user_id'],$text,1);
		if ($data['auss_user_id'] != 0) $this->send_pm($this->uid,$data['auss_user_id'],$text,1);
		$type = $this->db->query("SELECT type FROM stu_ally_relationship WHERE (allys_id1=".$this->sess['allys_id']." AND allys_id2=".$ally.") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$ally.")",1);
		if ($type == 2) $t = "Handelsvertrag";
		if ($type == 3) $t = "Freundschaftsabkommen";
		if ($type == 4) $t = "Bündnis";
		if ($type == 5) $t = "Frieden";
		$this->db->query("DELETE FROM stu_ally_relationship WHERE (allys_id1=".$this->sess['allys_id']." AND allys_id2=".$ally.") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$ally.")");
		$tx = "Die Allianz ".$this->data['name']." hat den Vertrag (".$t.") mit der Allianz ".$data['name']." aufgekündigt";
		$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg) VALUES ('".$tx."',NOW(),'3','".ftit(str_replace("\"","",$tx))."')");
		return "Beziehung zur Allianz ".$data['name']." wurde gelöscht";
	}

	function editrelationship($ally,$type)
	{
		$data = $this->db->query("SELECT name,praes_user_id,vize_user_id,auss_user_id FROM stu_allylist WHERE allys_id=".$ally,4);
		if ($data == 0) return;
		if ($ally == $this->sess['allys_id']) return;
		if ($type == 1)
		{
			$this->db->query("DELETE FROM stu_ally_relationship WHERE (allys_id1=".$this->sess['allys_id']." AND allys_id2=".$ally.") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$ally.")");
			$this->db->query("INSERT INTO stu_ally_relationship (allys_id1,allys_id2,type,date) VALUES ('".$this->sess['allys_id']."','".$ally."','1',NOW())");
			$text = $this->sess['user']." hat Deiner Allianz den Krieg erklärt";
			$tx = "Die Allianz ".addslashes($this->db->query("SELECT name FROM stu_allylist WHERE allys_id=".$this->sess['allys_id']."",1))." hat der Allianz ".addslashes($data['name'])." den Krieg erklärt";
			$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg) VALUES ('".$tx."',NOW(),'3','".ftit(str_replace("\"","",$tx))."')");
			$this->send_pm($this->uid,$data['praes_user_id'],$text,1);
			if ($data['auss_user_id'] != 0) $this->send_pm($this->uid,$data['auss_user_id'],$text,1);
			return "Der Allianz ".$data['name']." wurde der Krieg erklärt";
		}
		if ($type == 5 && $this->db->query("SELECT allys_id1 FROM stu_ally_relationship WHERE (allys_id1=".$this->sess['allys_id']." AND allys_id2=".$ally.") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$ally.")",1) == 0) return;
		if ($this->db->query("SELECT allys_id1 FROM stu_ally_relationship WHERE type='1' AND ((allys_id1=".$this->sess['allys_id']." AND allys_id2=".$ally.") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$ally."))",1) > 0 && $type != 5) return "Du musst dieser Allianz erst den Frieden anbieten";
		$this->db->query("DELETE FROM stu_ally_relationship WHERE ((allys_id1=".$this->sess['allys_id']." AND allys_id2=".$ally.") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$ally.")) AND UNIX_TIMESTAMP(date)=0");
		$this->db->query("INSERT INTO stu_ally_relationship (allys_id1,allys_id2,type) VALUES ('".$this->sess['allys_id']."','".$ally."','".$type."')");
		if ($type == 2) $t = "Handelsvertrag";
		if ($type == 3) $t = "Freundschaftsabkommen";
		if ($type == 4) $t = "Bündnis";
		if ($type == 5) $t = "Frieden";
		$text = $this->sess['user']." hat Deiner Allianz einen Vertrag (".$t.") angeboten";
		$this->send_pm($this->uid,$data['praes_user_id'],$text,1);
		if ($data['auss_user_id'] != 0) $this->send_pm($this->uid,$data['auss_user_id'],$text,1);
		return "Der Allianz ".$data['name']." wurde ein Vertrag angeboten";
	}

	function takerelationship($ally)
	{
		$data = $this->db->query("SELECT a.allys_id1,a.allys_id2,a.type,b.name,b.praes_user_id,b.auss_user_id FROM stu_ally_relationship as a LEFT JOIN stu_allylist as b ON a.allys_id1=b.allys_id WHERE a.allys_id1=".$ally." AND a.allys_id2=".$this->sess['allys_id']." AND (UNIX_TIMESTAMP(a.date)=0 OR UNIX_TIMESTAMP(a.date) IS NULL)",4);
		if ($data == 0) return;
		if ($data['type'] == 5)
		{
			$name = $this->db->query("SELECT name FROM stu_allylist WHERE allys_id=".$this->sess['allys_id'],1);
			$tx = "Der Krieg zwischen den Allianzen ".addslashes($name)." und ".addslashes($data['name'])." wurde beendet";
			$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg) VALUES ('".$tx."',NOW(),'3','".ftit(str_replace("\"","",$tx))."')");
			$this->db->query("DELETE FROM stu_ally_relationship WHERE (allys_id1=".$this->sess['allys_id']." AND allys_id2=".$ally.") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$ally.")");
			$text = "Der Krieg mit der Allianz ".addslashes($name)." wurde beendet";
			$this->send_pm($this->uid,$data['praes_user_id'],$text,1);
			if ($data['auss_user_id'] != 0) $this->send_pm($this->uid,$data['auss_user_id'],$text,1);
			return "Der Krieg mit der Allianz ".$data['name']." wurde beendet";
		}
		if ($data['type'] == 2) $t = "einen Handelsvertrag abgeschlossen";
		if ($data['type'] == 3) $t = "ein Freundschaftsabkommen geschlossen";
		if ($data['type'] == 4) $t = "ein Bündnis abgeschlossen";
		$name = $this->db->query("SELECT name FROM stu_allylist WHERE allys_id=".$this->sess['allys_id'],1);
		$text = "Die Allianz ".$name." hat den Vertrag angenommen";
		$this->send_pm($this->uid,$data['praes_user_id'],$text,1);
		if ($data['auss_user_id'] != 0) $this->send_pm($this->uid,$data['auss_user_id'],$text,1);
		$tx = "Die Allianz ".addslashes($name)." hat mit der Allianz ".addslashes($data['name'])." ".$t;
		$this->db->query("INSERT INTO stu_history (message,date,type,ft_msg) VALUES ('".$tx."',NOW(),'3','".ftit(str_replace("\"","",$tx))."')");
		$this->db->query("DELETE FROM stu_ally_relationship WHERE (allys_id1=".$ally." AND allys_id2=".$this->sess['allys_id'].") OR (allys_id2=".$ally." AND allys_id1=".$this->sess['allys_id'].")");
		$this->db->query("INSERT INTO stu_ally_relationship (allys_id1,allys_id2,type,date) VALUES ('".$ally."','".$this->sess['allys_id']."','".$data['type']."',NOW())");
		return "Angebot angenommen";
	}

	function deloffer($ally)
	{
		$data = $this->db->query("SELECT a.allys_id1,a.allys_id2,a.type,b.name,b.praes_user_id,b.auss_user_id FROM stu_ally_relationship as a LEFT JOIN stu_allylist as b ON a.allys_id1=b.allys_id WHERE a.allys_id1=".$ally." AND a.allys_id2=".$this->sess['allys_id']." AND UNIX_TIMESTAMP(a.date)=0",4);
		if ($data == 0) return;
		$text = $this->sess['user']." hat das Vertragsangebot abgelehnt";
		$this->send_pm($this->uid,$data['praes_user_id'],$text,1);
		if ($data['auss_user_id'] != 0) $this->send_pm($this->uid,$data['auss_user_id'],$text,1);
		$this->db->query("DELETE FROM stu_ally_relationship WHERE ((allys_id1=".$this->sess['allys_id']." AND allys_id2=".$ally.") OR (allys_id2=".$this->sess['allys_id']." AND allys_id1=".$ally.")) AND UNIX_TIMESTAMP(date)=0");
		return "Das Vertragsangebot der Allianz ".$data['name']." wurde abgelehnt";
	}

	function loadremrelationships($allyid) { $this->result = $this->db->query("SELECT a.allys_id1,a.allys_id2,a.type,UNIX_TIMESTAMP(a.date) as date_tsp,b.name,c.name as name2 FROM stu_ally_relationship as a LEFT JOIN stu_allylist as b ON b.allys_id=a.allys_id1 LEFT JOIN stu_allylist as c ON c.allys_id=a.allys_id2 WHERE (a.allys_id1=".$allyid." OR a.allys_id2=".$allyid.") AND (UNIX_TIMESTAMP(a.date) > 0 || a.type='1') ORDER BY a.type"); }

	function inviteuser($userid)
	{
		$data = $this->db->query("SELECT id,allys_id,vac_active FROM stu_user WHERE id=".$userid,4);
		if ($data == 0) return;
		if ($data['allys_id'] > 0) return "Dieser Siedler befindet sich bereits in einer Allianz";
		if ($data[vac_active] == 1) return "Dieser Siedler befindet sich im Urlaubsmodus";
		if ($this->db->query("SELECT COUNT(user_id) FROM stu_ally_invitations WHERE allys_id=".$this->sess['allys_id'],1) == 2) return "Es sind maximal 2 Angebote gleichzeitig möglich";
		if ($this->db->query("SELECT recipient FROM stu_ignorelist WHERE user_id=".$userid." AND recipient=".$this->uid,1) > 0) return "Du wirst von diesem Siedler ignoriert";
		if ($this->db->query("SELECT allys_id FROM stu_ally_invitations WHERE allys_id=".$this->sess['allys_id']." AND user_id=".$userid,1) > 0) return "Diesem Siedler wurde bereits ein Angebot gemacht";
		$this->send_pm($this->uid,$userid,"<b>Allianz-Einladung</b><br><br>Der Siedler ".addslashes($this->sess['user'])." hat Dich in die Allianz ".$this->data['name']." eingeladen.<br>Möchtest Du:<br><a href=?p=ally&s=ma&a=aj&id=".$this->sess['allys_id']." style=\"color: #25c219;\">Das Angebot annehmen</a><br><a href=?p=ally&a=decj&id=".$this->sess['allys_id']." style=\"color: #FF0000;\">Das Angebot ablehnen</a><br><a href=?p=comm&s=il&sent=1&nc=".$this->uid.">Den Absender ignorieren</a>",1);
		$this->db->query("INSERT INTO stu_ally_invitations (allys_id,user_id,date) VALUES ('".$this->sess['allys_id']."','".$userid."',NOW())");
		return "Der Siedler wurde eingeladen";
	}

	function getinvitations($allyid) { return $this->db->query("SELECT a.user_id,b.user FROM stu_ally_invitations as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.allys_id=".$allyid); }

	function delinvitation($userid)
	{
		$this->db->query("DELETE FROM stu_ally_invitations WHERE allys_id=".$this->sess['allys_id']." AND user_id=".$userid." LIMIT 1");
		return "Das Angebot wurde gelöscht";
	}

	function decInvitation($allyid)
	{
		$res = $this->db->query("DELETE FROM stu_ally_invitations WHERE user_id=".$this->uid." AND allys_id=".$allyid,6);
		if ($res == 0) return;
		$this->send_pm($this->uid,$this->db->query("SELECT praes_user_id FROM stu_allylist WHERE allys_id=".$allyid,1),"Der Siedler ".addslashes($this->sess['user'])." hat das Beitrittsangebot abgelehnt",1);	
		return "Das Angebot wurde abgelehnt";
	}

	function get_member_data_by_id($id) { $this->ar = $this->db->query("SELECT a.id,a.user,a.race,a.subrace,UNIX_TIMESTAMP(a.lastaction) as lastaction,a.vac_active,COUNT(DISTINCT(b.id)) as cols,COUNT(DISTINCT(c.id)) as ships FROM stu_user as a LEFT JOIN stu_colonies as b ON a.id=b.user_id LEFT JOIN stu_ships as c ON c.user_id=b.user_id WHERE a.allys_id=".$id." GROUP BY a.id"); }

	function check_alliance($id)
	{
		if (!check_int($id)) exit;
		if ($this->db->query("SELECT type FROM stu_ally_relationship WHERE ((allys_id1=".$id." AND allys_id2=".$this->sess['allys_id'].") OR (allys_id2=".$id." AND allys_id1=".$this->sess['allys_id'].")) AND type='4'",1) == 0) exit;
	}

	function get_ally_by_id($id) { return $this->db->query("SELECT allys_id,name FROM stu_allylist WHERE allys_id=".$id,4); }
	
	function getallyshipsdetails() { return $this->db->query("SELECT a.rumps_id,a.user_id,a.fleets_id,a.name,a.huelle,a.max_huelle,a.schilde_status,a.schilde,a.max_schilde,a.eps,a.max_eps,a.systems_id,a.cx,a.cy,a.sx,a.sy,b.name as sname,b.type as stype,c.user,c.vac_active,d.slots FROM stu_ships as a LEFT JOIN stu_systems as b ON b.systems_id=a.systems_id LEFT JOIN stu_user as c ON c.id=a.user_id LEFT JOIN stu_rumps as d ON d.rumps_id=.a.rumps_id WHERE c.allys_id=".$this->data['allys_id']." ORDER BY a.fleets_id DESC,d.slots DESC,a.user_id"); }





	function getcolemergencies($allyid) { 
		$result = $this->db->query("SELECT a.*,b.*,c.user,d.cx,d.cy,d.name as sysname FROM stu_colonies_actions as a LEFT OUTER JOIN stu_colonies as b on a.colonies_id = b.id LEFT OUTER JOIN stu_user as c on b.user_id = c.id LEFT OUTER JOIN stu_systems as d on b.systems_id = d.systems_id WHERE c.allys_id = ".$allyid." AND (a.var='fblock' OR a.var='fattack')");
		if (mysql_num_rows($result) == 0) return "";
		$msg = "<table class=tcal width=100%><th><b><font color=red>Kolonie-Notrufe:</font></b></th>";
		while($data=mysql_fetch_assoc($result)) {

			$msg .= "<tr><td><table width=100% border=0><tr><td width=32><img src='http://www.stuniverse.de/gfx/planets/".$data[colonies_classes_id].".gif' border=0></td>";
			$msg .= "<td>".$data[name]." bei ".$data[sx]."|".$data[sy]." im ".$data[sysname]."-System (".$data[cx]."|".$data[cy].") von ".stripslashes($data[user])."";
			if ($data['var'] == 'fblock') $msg .= "<br><img src='http://www.stuniverse.de/gfx/buttons/x2.gif' border=0><font color='#CCCCCC'> Steht unter Blockade.</font>";
			else $msg .= "<br><img src='http://www.stuniverse.de/gfx/buttons/leavecol2.gif' border=0><font color='#FF0000'> Wird angegriffen!</font>";

			$msg .= "</td></tr></table></td></tr>";
		}


		$msg .= "</table>";
		return $msg;

	}























}
?>
