<?php
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;



class MultiList
{
	var $nrofentitys;
	var $entity;

	function MultiList()
	{
		$this->nrofentitys = 0;
	}

	function newEntity()
	{
		$this->nrofentitys++;
		$this->entity[$this->nrofentitys] = new MultiEntity();
	}

	function addEntity($obj)
	{
		$this->nrofentitys++;
		$this->entity[$this->nrofentitys] = $obj;	
	}

	function addToNewest($id,$pass,$name)
	{
		if (!$this->entity[$this->nrofentitys]->containsId($id)) $this->entity[$this->nrofentitys]->addJerk($id,$pass,$name);
	}

	function mergeList()
	{
		$t = new MultiList();

		for ($i = 1; $i <= $this->nrofentitys; $i++) $taken[$i] = 0;

		for ($i = 1; $i <= $this->nrofentitys; $i++)
		{
			if ($taken[$i] == 1) continue;
			$t->newEntity();
			$t->entity[$t->nrofentitys]->mergeEntity($this->entity[$i]);
			for ($k = $i+1; $k <= $this->nrofentitys; $k++)
			{
				if ($taken[$k] == 0) 
				{
					if ($t->entity[$t->nrofentitys]->isEqual($this->entity[$k]))
					{
						$taken[$k] = 1;
						$t->entity[$t->nrofentitys]->mergeEntity($this->entity[$k]);
					}
				}

			}
		}

		for ($i = 1; $i <= $t->nrofentitys; $i++)
		{
			$t->entity[$i]->updatePmCounter();
			$t->entity[$i]->updatePassCollision();
		}

		return $t;
	}

	function show()
	{
		for ($i = 1; $i <= $this->nrofentitys;$i++)
		{
			echo "<br>".$this->entity[$i]->show();
		}

	}
}


class MultiEntity
{
	var $idlist;
	var $passwordlist;
	var $pms;
	var $privpms;
	var $accounts;
	var $passcollides;

	function MultiEntity()
	{
		$this->accounts = 0;
		$this->pms = 0;
	}

	function addJerk($jerk)
	{
		$this->accounts++;	
		$this->idlist[$this->accounts] = $jerk['id'];
		$this->passwordlist[$this->accounts] = $jerk['pass'];
		$this->usernames[$this->accounts] = $jerk['user'];
		$this->logins[$this->accounts] = $jerk['login'];
		$this->emails[$this->accounts] = $jerk['email'];
	}

	function containsId($id)
	{
		for ($i = 1; $i <= $this->accounts; $i++)
		{
			if ($this->idlist[$i] == $id) {
				return true;
			}
		}
		return false;
	}

	function isEqual($obj)
	{
		for ($i = 1; $i <= $this->accounts; $i++)
		{
			if ($obj->containsId($this->idlist[$i])) return true;
		}
		return false;
	}

	function mergeEntity($obj)
	{
		for ($i = 1; $i <= $obj->accounts; $i++)
		{
			$id = $obj->idlist[$i];
			if (!$this->containsId($id)) {
				$jerk = array();
				$jerk[id] = $id;
				$jerk[pass] = $obj->passwordlist[$i];
				$jerk[user] = $obj->usernames[$i];
				$jerk[login] = $obj->logins[$i];
				$jerk[email] = $obj->emails[$i];
				$this->addJerk($jerk);
			}
		}
	}

	function updatePmCounter()
	{
		global $db;
		$pms = 0;
		$privpms = 0;
		for ($i = 1; $i <= $this->accounts;$i++)
		{
			$id1 = $this->idlist[$i];
			for ($k = $i+1; $k <= $this->accounts; $k++)
			{
				$id2 = $this->idlist[$k];
				$pms += $db->query("SELECT COUNT(id) FROM stu_pms WHERE ((recip_user=".$id1." AND send_user=".$id2.") OR (recip_user=".$id2." AND send_user=".$id1.")) AND type !='1' ",1);
				$privpms += $db->query("SELECT COUNT(id) FROM stu_pms WHERE ((recip_user=".$id1." AND send_user=".$id2.") OR (recip_user=".$id2." AND send_user=".$id1.")) AND type ='1'",1);
			}
		}

		$this->pms = $pms;
		$this->privpms = $privpms;
	}

	function updatePassCollision()
	{
		$this->passcollides = 0;
		for ($i = 1; $i <= $this->accounts;$i++)
		{
			$p1 = $this->passwordlist[$i];
			for ($k = $i+1; $k <= $this->accounts; $k++)
			{
				$p2 = $this->passwordlist[$k];
				if ($p1 == $p2) $this->passcollides++;
			}
		}
	}

	function show()
	{
		$r = "";
		for ($i = 1; $i <= $this->accounts; $i++)
		{
			$r .= "<br>(".$this->idlist[$i].") ".$this->usernames[$i];
			$r .= "<br> - <font color=bbbbbb>".$this->logins[$i]." - ".$this->emails[$i]."</font>";
		}		
		if ($this->passcollides > 0) {
			$r .= "<br> - <font color=#ff0000>Passwort-Kollision</font>: ".$this->passcollides;
			for ($i = 1; $i <= $this->accounts; $i++)
			{
				$r .= "<br> - - ".$this->passwordlist[$i];
			}	
		}
		if ($this->pms > 0) $r .= "<br> - <font color=#886600>Interaktionen</font>: ".$this->pms;
		if ($this->privpms > 0) $r .= "<br> - <font color=#006600>Private Nachrichten</font>: ".$this->privpms;
		$r .= "<br><a href=multitb.php?p=ips";
		
		if ($this->accounts >= 1) $r.= "&l1=".$this->idlist[1];
		if ($this->accounts >= 2) $r.= "&l2=".$this->idlist[2];
		if ($this->accounts >= 3) $r.= "&l3=".$this->idlist[3];
		if ($this->accounts >= 4) $r.= "&l4=".$this->idlist[4];
		if ($this->accounts >= 5) $r.= "&l5=".$this->idlist[5];
		
		$r .= ">IP-Überschneidungen anzeigen</a>";
		$r .= "<br><a href=multitb.php?p=pms";
		
		if ($this->accounts >= 1) $r.= "&l1=".$this->idlist[1];
		if ($this->accounts >= 2) $r.= "&l2=".$this->idlist[2];
		if ($this->accounts >= 3) $r.= "&l3=".$this->idlist[3];
		if ($this->accounts >= 4) $r.= "&l4=".$this->idlist[4];
		if ($this->accounts >= 5) $r.= "&l5=".$this->idlist[5];
		
		$r .= ">PM-Verkehr anzeigen</a>";
		
		$r .= "<br><form action=\"multitib.php\">";
		
		$r .= "";
		
		$r .= "\n<input type=\"checkbox\" name=\"really[]\" value=\"".$this->idlist[1]."\" onClick=\"document.getElementById('sperrdiv".$this->idlist[1]."').style.display = 'block';\">&nbsp;";

		
		$r .= "<div id=\"sperrdiv".$this->idlist[1]."\" style=\"float:left;display:none;\">";
		$r .= "<a href=multitb.php?p=sperr";
		
		if ($this->accounts >= 1) $r.= "&l1=".$this->idlist[1];
		if ($this->accounts >= 2) $r.= "&l2=".$this->idlist[2];
		if ($this->accounts >= 3) $r.= "&l3=".$this->idlist[3];
		if ($this->accounts >= 4) $r.= "&l4=".$this->idlist[4];
		if ($this->accounts >= 5) $r.= "&l5=".$this->idlist[5];
		
		$r .= ">Accounts sperren</a></div>";
		
		return $r;
	}
}

$frame = 14*86400;
$miniframe = 30*60;



$p = $_GET['p'];

if (!$p) $p = "main";



$l1 = $_GET['l1'];
$l2 = $_GET['l2'];
$l3 = $_GET['l3'];
$l4 = $_GET['l4'];
$l5 = $_GET['l5'];




$users = 5;
$userids = array();

if (!$l5) {
	$l5 = 0;
	$users--;
} else $userids[5] = $l5;
if (!$l4) {
	$l4 = 0;
	$users--;
} else $userids[4] = $l4;
if (!$l3) {
	$l3 = 0;
	$users--;
} else $userids[3] = $l3;
if (!$l2) {
	$l2 = 0;
	$users--;
} else $userids[2] = $l2;
if (!$l1) {
	$l1 = 0;
	$users--;
} else $userids[1] = $l1;

ksort($userids);

if ($p == "main") {

$mlist = new MultiList();

$result = $db->query("SELECT a.*,COUNT(DISTINCT a.user_id) as uic,b.allys_id,b.user,b.pass,b.login,b.email FROM stu_user_iptable as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE UNIX_TIMESTAMP(a.start)>=".(time()-($frame))." AND a.user_id>102 GROUP BY ip");
while($data=mysql_fetch_assoc($result))
{
	if ($data[uic] == 1) continue;

	$res = $db->query("SELECT a.*,UNIX_TIMESTAMP(a.start) as stime,b.pass,b.search_user as user,b.login,b.email FROM stu_user_iptable as a LEFT JOIN stu_user as b ON b.id=a.user_id WHERE a.ip='".$data[ip]."' AND UNIX_TIMESTAMP(a.start)>=".(time()-($frame))." AND a.user_id>102 ORDER BY a.user_id");

	$ti = 0;
	unset($tarray);
	while($dat=mysql_fetch_assoc($res))
	{
		$ti++;
		$tarray[$ti][time] = $dat[stime];
		$tarray[$ti][pass] = $dat[pass];
		$tarray[$ti][id] = $dat[user_id];
		$tarray[$ti][user] = $dat[user];
		$tarray[$ti][login] = $dat[login];
		$tarray[$ti][email] = $dat[email];
	}
	//echo "<br>CROSS REFERENCING IP: ".$data[ip]." - ENTRIES FOUND: ".$ti;


	for ($i = 0; $i <= $ti; $i++)
	{
		for ($k = $i+1; $k <= $ti; $k++)
		{
			if ($tarray[$i][id] == 	$tarray[$k][id]) continue;
			if (($tarray[$i][time] > ($tarray[$k][time]-$miniframe)) && ($tarray[$i][time] < ($tarray[$k][time]+$miniframe)))
			{
				$et = new MultiEntity();
				$et->addJerk($tarray[$i]);
				$et->addJerk($tarray[$k]);
				$mlist->addEntity($et);
			}
		}		
	}

}

$mlist = $mlist->mergeList();

$mlist->show();

}


function usercolor($i) {

	if ($i == 1) return "#880000";
	if ($i == 2) return "#008800";
	if ($i == 3) return "#000088";
	if ($i == 4) return "#660066";
	if ($i == 5) return "#006666";
}

$outputlog = array();

function addToLog($s) {
	global $outputlog;
	array_push($outputlog,"<br>".$s);
}
function showLog() {
	global $outputlog;
	foreach($outputlog as $s) echo $s;
}


function iptostring($ipdata) {

	global $userids;
	$s = "";
	
	for($i = 1; $i < 5; $i++) {
		if ($userids[$i] == $ipdata[user_id]) {
			$bla = $i;
			break;
		}
	}
	
	$s .= "<font color=".usercolor($bla).">".$ipdata[start]." - ".$ipdata[agent]."</font>";

	return $s;
}


if ($p == "ips") {

	echo "IP-Überschneidungen für:";
	
	echo "<br>";
	
	$search = "(";
	
	for ($i = 1; $i <= $users; $i++) {
		echo "<br>";
		
		echo "<font color=".usercolor($i).">";
		
		echo "(".$userids[$i].") ";
		$name = $db->query("SELECT user FROM stu_user WHERE id =".$userids[$i]." LIMIT 1",1);
		echo $name;
		echo "</font>";
		
		if ($i != 1) $search .= " OR ";
		$search .= "user_id = ".$userids[$i];
		
		$backsearch[$userids[$i]] = $i;
	}
	
	$search .= ")";
	
	// echo "<br>".$search;
	
	$ips = array();
	$ipcount = 0;
	$result = $db->query("SELECT *,UNIX_TIMESTAMP(start) as startstamp FROM stu_user_iptable WHERE ".$search." ORDER BY start ASC");
	while($data=mysql_fetch_assoc($result)) {

		$ips[$ipcount] = $data;
		$ipcount++;
	}

	// showLog();
	
	$timeframe = 30*60;
	
	
	$ipmatrix = array();
	$dvmatrix = array();
	for($i = 1; $i <= $users; $i++) 
		for ($j = $i+1; $j <= $users; $j++) {
			$ipmatrix[$i][$j] = 0;
			$dvmatrix[$i][$j] = 0;
		}
		





	$totalcoll = 0;

		
	for($i = 0; $i < $ipcount; $i++) {
		$idata = $ips[$i];
	
		$collide="";
		for ($j = 0; $j < $ipcount; $j++) {
			$jdata = $ips[$j];
		

		
		
		
			// echo "<br>".$jdata[startstamp];
			if ($idata[user_id] == $jdata[user_id]) {
				// echo ".";
				continue;
			}
			
			if ($jdata[startstamp] < $idata[startstamp]) {
				// echo "<";
				continue;
			}
		

						
			if ($jdata[startstamp] > $idata[startstamp] + $timeframe) {
				// echo "+";
				break;
			}
			
			if ($idata[ip] != $jdata[ip]) {
				// echo ".";
				continue;
			}		
			
			// echo "<br>".$jdata[ip]."<->".$idata[ip];
			
			$collide .= iptostring($jdata)."<br>";
			
			if ($idata[user_id] > $jdata[user_id]) $ipmatrix[$backsearch[$jdata[user_id]]][$backsearch[$idata[user_id]]] += 1;
			else $ipmatrix[$backsearch[$idata[user_id]]][$backsearch[$jdata[user_id]]] += 1;
			
			$totalcoll++;
			
			if ($idata[agent] == $jdata[agent]) {
				if ($idata[user_id] > $jdata[user_id]) $dvmatrix[$backsearch[$jdata[user_id]]][$backsearch[$idata[user_id]]] += 1;
				else $dvmatrix[$backsearch[$idata[user_id]]][$backsearch[$jdata[user_id]]] += 1;		

				$totaldvcoll++;				
			}
			
			
		}
	
		// echo $totalcoll."<br>";
	
		if ($collide != "") {
		
		
			addToLog($idata[ip]);
			addToLog(iptostring($idata));
			addToLog($collide);
			// addToLog("");
		}

	}


	echo "<br><br>Multi-Index: ".round($totaldvcoll/$ipcount,4);
	
	echo "<br><br>Überschneidungs-Index: ".round($totaldvcoll/$totalcoll,4);





	echo "<br><br>Überschneidungen:";

	for($i = 1; $i <= $users; $i++) {
		for ($j = $i+1; $j <= $users; $j++) {
		
			if ($ipmatrix[$i][$j] > 0) echo "<br><font color=".usercolor($i).">".$userids[$i]."</font>"." <-> "."<font color=".usercolor($j).">".$userids[$j]."</font> : ".$ipmatrix[$i][$j];
		
		}
	}
	
	echo "<br><br>Überschneidungen + Agent:";

	for($i = 1; $i <= $users; $i++) {
		for ($j = $i+1; $j <= $users; $j++) {
		
			if ($dvmatrix[$i][$j] > 0) echo "<br><font color=".usercolor($i).">".$userids[$i]."</font>"." <-> "."<font color=".usercolor($j).">".$userids[$j]."</font> : ".$dvmatrix[$i][$j];
		
		}
	}
	
	echo "<br><br>Details:";
	
	showLog();
	
	
	
	
	
	
	
	
	
	
		echo "<br><br><a href=multitb.php>zurück</a>";
	
	
	
	
	
}






if ($p == "pms") {

	echo "PM-Verkehr für:";
	
	echo "<br>";
	
	$searchrec = "(";
	$searchsen = "(";
	
	for ($i = 1; $i <= $users; $i++) {
		echo "<br>";
		
		echo "<font color=".usercolor($i).">";
		
		echo "(".$userids[$i].") ";
		$name = $db->query("SELECT user FROM stu_user WHERE id =".$userids[$i]." LIMIT 1",1);
		echo $name;
		echo "</font>";
		
		if ($i != 1) $searchrec .= " OR ";
		if ($i != 1) $searchsen .= " OR ";
		$searchrec .= "recip_user = ".$userids[$i];
		$searchsen .= "send_user = ".$userids[$i];
		
		$backsearch[$userids[$i]] = $i;
	}
	
	$searchrec .= ")";
	$searchsen .= ")";
	

	$search = $searchrec." AND ".$searchsen;
	
	// echo $search;
	
	
	$pmcount = 0;
	$privpm = 0;
	
	
	
	$pvmatrix = array();
	$iamatrix = array();
	for($i = 1; $i <= $users; $i++) 
		for ($j = $i+1; $j <= $users; $j++) {
			$pvmatrix[$i][$j] = 0;
			$iamatrix[$i][$j] = 0;
		}	
	
	
	
	
	$result = $db->query("SELECT *,UNIX_TIMESTAMP(date) as dt FROM stu_pms WHERE ".$search." ORDER BY date ASC");
	while($data=mysql_fetch_assoc($result)) {


		$pmcount++;
		
		
		if ($data[type] == 1) {
			$privpm++;
			if ($data[send_user] > $data[recip_user]) $pvmatrix[$backsearch[$data[recip_user]]][$backsearch[$data[send_user]]] += 1;
			if ($data[send_user] < $data[recip_user]) $pvmatrix[$backsearch[$data[send_user]]][$backsearch[$data[recip_user]]] += 1;	
		} else {
			if ($data[send_user] > $data[recip_user]) $iamatrix[$backsearch[$data[recip_user]]][$backsearch[$data[send_user]]] += 1;
			if ($data[send_user] < $data[recip_user]) $iamatrix[$backsearch[$data[send_user]]][$backsearch[$data[recip_user]]] += 1;
		}
		
		
		
		
	}
	
	echo "<br><br>Interaktionen:";

	for($i = 1; $i <= $users; $i++) {
		for ($j = $i+1; $j <= $users; $j++) {
		
			echo "<br><font color=".usercolor($i).">".$userids[$i]."</font>"." <-> "."<font color=".usercolor($j).">".$userids[$j]."</font> : ".$iamatrix[$i][$j];
		
		}
	}
	
	echo "<br><br>Private Nachrichten:";

	for($i = 1; $i <= $users; $i++) {
		for ($j = $i+1; $j <= $users; $j++) {
		
			echo "<br><font color=".usercolor($i).">".$userids[$i]."</font>"." <-> "."<font color=".usercolor($j).">".$userids[$j]."</font> : ".$pvmatrix[$i][$j];
		
		}
	}
	
	
		echo "<br><br><a href=multitb.php>zurück</a>";
}


if ($p == "sperr") {



echo "Sperren von:";
	
	echo "<br>";
	
	$search = "(";
	
	for ($i = 1; $i <= $users; $i++) {
		echo "<br>";
		
		echo "<font color=".usercolor($i).">";
		
		echo "(".$userids[$i].") ";
		$name = $db->query("SELECT user FROM stu_user WHERE id =".$userids[$i]." LIMIT 1",1);
		echo $name;
		echo "</font>";
		
		if ($i != 1) $search .= " OR ";
		$search .= "user_id = ".$userids[$i];
		
		$backsearch[$userids[$i]] = $i;
	}


	for ($i = 1; $i <= $users; $i++) {
		
		$db->query("UPDATE stu_user SET aktiv='2',vac_active='1' WHERE id =".$userids[$i]." LIMIT 1",1);
	
	}


	echo "<br><br><a href=multitb.php>zurück</a>";
}


















?>