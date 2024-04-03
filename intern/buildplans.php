<?php
session_start();
include_once("../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;
if (!$_GET[p] || $_GET[p] == "ma")
{
	@session_destroy();
	@session_start();
	$result = $db->query("SELECT a.plans_id,a.rumps_id,a.name,b.user FROM stu_ships_buildplans as a LEFT JOIN stu_user as b ON a.user_id=b.id WHERE a.user_id<100 ORDER BY a.plans_id");
	echo "<html>
	<head>
	<title>Baupläne</title>
	</head>
	</html>
	<b>NPC Baupläne</b><br>";
	while($data=mysql_fetch_assoc($result)) echo "<a href=?p=ep&id=".$data['rumps_id']."&pid=".$data[plans_id]."><img src=../gfx/ships/".$data[rumps_id].".gif border=0> Bauplan ".$data[name]." für NPC ".$data[user]."</a><br>";
	echo "<br><b>Neuen anlegen</b><form action=buildplans.php method=get>
	<input type=hidden name=p value=np>Name:<input type=text size=20 name=bn> <input type=submit value=Ausstatten>
	</form>
	<a href=?p=np>Neuen Bauplan anlegen</a></html>";
}
if ($_GET[p] == "np" && !$_GET["id"])
{
	if ($_SESSION[m1])
	{
		session_destroy();
		session_start();
	}
	if (strlen($_GET[bn]) < 2) $_GET[bn] = "Schiff ".date("d.m.Y H:i");
	$_SESSION[name] = $_GET[bn];
	$result = $db->query("SELECT * FROM stu_rumps ORDER BY sort,rumps_id");
	echo "<html>
	<head>
	<title>Baupläne</title>
	</head>
	</html>
	<b>Neuen Bauplan erstellen - Rumpf wählen</b><br><table><tr>";
	$i = 0;
	while($data=mysql_fetch_assoc($result))
	{
		if ($i%4==0) echo "</tr><tr>";
		echo "<td><a href=?p=np&id=".$data[rumps_id]."><img src=../gfx/ships/".$data[rumps_id].".gif border=0> ".$data[name]."</a></td>";
		$i++;
	}
	echo "</tr></table>";
}
if ($_GET[p] == "np" && $_GET["id"])
{
	$data = $db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$_GET[id],4);
	if ($_GET[m1]) $_SESSION[m1] = $_GET[m1];
	if ($_GET[m2]) $_SESSION[m2] = $_GET[m2];
	if ($_GET[m3]) $_SESSION[m3] = $_GET[m3];
	if ($_GET[m4]) $_SESSION[m4] = $_GET[m4];
	if ($_GET[m5]) $_SESSION[m5] = $_GET[m5];
	if ($_GET[m6]) $_SESSION[m6] = $_GET[m6];
	if ($_GET[m7]) $_SESSION[m7] = $_GET[m7];
	if ($_GET[m8]) $_SESSION[m8] = $_GET[m8];
	if ($_GET[m9]) $_SESSION[m9] = $_GET[m9];
	if ($_GET[m10]) $_SESSION[m10] = $_GET[m10];
	if ($_GET[m11]) $_SESSION[m11] = $_GET[m11];
	
	if ($_GET[nm1] == 1) unset($_SESSION[m1]);
	if ($_GET[nm2] == 1) unset($_SESSION[m2]);
	if ($_GET[nm3] == 1) unset($_SESSION[m3]);
	if ($_GET[nm4] == 1) unset($_SESSION[m4]);
	if ($_GET[nm5] == 1) unset($_SESSION[m5]);
	if ($_GET[nm6] == 1) unset($_SESSION[m6]);
	if ($_GET[nm7] == 1) unset($_SESSION[m7]);
	if ($_GET[nm8] == 1) unset($_SESSION[m8]);
	if ($_GET[nm9] == 1) unset($_SESSION[m9]);
	if ($_GET[nm10] == 1) unset($_SESSION[m10]);
	if ($_GET[nm11] == 1) unset($_SESSION[m11]);
	
	if (!$_SESSION[m1])
	{
		echo "<b>Hüllenmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=1 AND level>=".$data["m1minlvl"]." AND level<=".$data["m1maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m1=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Hülle: ".($data[m1c]*$dat[huelle]).")<br>";
		exit;
	}
	if (!$_SESSION[m2])
	{
		echo "<b>Schildmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=2 AND level>=".$data["m2minlvl"]." AND level<=".$data["m2maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m2=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Schilde: ".($data[m2c]*$dat[schilde]).")<br>";
		exit;
	}
	if (!$_SESSION[m3])
	{
		echo "<b>Computermodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=3 AND level>=".$data["m3minlvl"]." AND level<=".$data["m3maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m3=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m4])
	{
		echo "<b>Sensorenmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=4 AND level>=".$data["m4minlvl"]." AND level<=".$data["m4maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m4=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m5] && $data[m5c] > 0)
	{
		echo "<b>Warpkernmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=5 AND level>=".$data["m5minlvl"]." AND level<=".$data["m5maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m5=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Reaktor: ".$dat[reaktor]." | Kapazität: ".$dat[wkkap].")<br>";
		exit;
	}
	if (!$_SESSION[m6] && $data[m6c] > 0)
	{
		echo "<b>Waffenmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=6 AND level>=".$data["m6minlvl"]." AND level<=".$data["m6maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res))
		{
			$weapon = $db->query("SELECT wtype,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$dat[module_id],4);
			$phaser = round($weapon[strength] * (1 + (log($data[m6c]) / log(2))/3));
			echo "<a href=?p=np&id=".$_GET["id"]."&m6=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Stärke: ".$phaser." | Abweichung: ".$weapon[varianz]."%)<br>";
		}
		exit;
	}
	if (!$_SESSION[m7] && $data[m7c] > 0)
	{
		echo "<b>Antriebsmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=7 AND level>=".$data["m7minlvl"]." AND level<=".$data["m7maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m7=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m8])
	{
		echo "<b>EPSmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=8 AND level>=".$data["m8minlvl"]." AND level<=".$data["m8maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m8=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (EPS: ".($data[m8c]*$dat[eps]).")<br>";
		exit;
	}
	if (!$_SESSION[m9] && $data[m9c] > 0)
	{
		echo "<b>Tarnungsmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=9 AND level>=".$data["m9minlvl"]." AND level<=".$data["m9maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m9=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m10] && $data[m10c] > 0)
	{
		echo "<b>Torpedorampenmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=10 AND level>=".$data["m10minlvl"]." AND level<=".$data["m10maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m10=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Torpedos: ".$dat[torps]." | Typ: ".$dat[torp_type].")<br>";
		exit;
	}
	if (!$_SESSION[m11] && $data[m11c] > 0)
	{
		echo "<b>Warpantriebsmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=11 AND level>=".$data["m11minlvl"]." AND level<=".$data["m11maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&m11=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m5]) $_SESSION[m5] = 0;
	if (!$_SESSION[m6]) $_SESSION[m6] = 0;
	if (!$_SESSION[m7]) $_SESSION[m7] = 0;
	if (!$_SESSION[m9]) $_SESSION[m9] = 0;
	if (!$_SESSION[m10]) $_SESSION[m10] = 0;
	if (!$_SESSION[m11]) $_SESSION[m11] = 0;
	
	if ($_GET[a] == "b")
	{
		if ($db->query("SELECT a.plans_id,a.name FROM stu_ships_buildplans as a LEFT JOIN stu_ships as b ON a.plans_id=b.plans_id WHERE a.rumps_id=".$_GET["id"]." AND a.m1='".$_SESSION[m1]."' AND a.m2='".$_SESSION[m2]."' AND a.m3='".$_SESSION[m3]."' AND a.m4='".$_SESSION[m4]."' AND a.m5='".$_SESSION[m5]."' AND a.m6='".$_SESSION[m6]."' AND a.m7='".$_SESSION[m7]."' AND a.m8='".$_SESSION[m8]."' AND a.m9='".$_SESSION[m9]."' AND a.m10='".$_SESSION[m10]."' AND a.m11='".$_SESSION[m11]."' AND a.user_id=".$_GET[npc],4) != 0) echo "<b>Dieser Bauplan existiert bereits</b>";
		else
			{
			$stellar = 0;
			while($i<=11)
			{
				if (!$_SESSION["m".$i] || $_SESSION["m".$i] == 0)
				{
					$i++;
					continue;
				}
				$dat = $db->query("SELECT * FROM stu_modules WHERE module_id=".$_SESSION["m".$i],4);
				$huelle += $dat[huelle]*$data["m".$i."c"];
				$bz += $dat[buildtime];
				$points += $dat[points];
				$schilde += $dat[schilde]*$data["m".$i."c"];
				$maintain += $dat[maintaintime];
				$reaktor += $dat[reaktor]*$data["m".$i."c"];;
				$wkkap += $dat[wkkap]*$data["m".$i."c"];;
				$eps += $dat[eps]*$data["m".$i."c"];
				if ($dat[type] == 1)
				{
					if ($dat[special_id1] == 1)
					{
						$ev_mul = 1.1;
						$hit_mul = 1.1;
					}
					if ($dat[special_id1] == 2)
					{
						$ev_mul = 0.9;
						$hit_mul = 0.9;
					}
				}
				$hit += $dat[hit_val];
				$torps += $dat[torps]*$data["m".$i."c"];;
				$detect += $dat[detect_val];
				$cloak += $dat[cloak_val];
				$lss = $dat[lss]+($data["m".$i."c"] - 1);
				$kss = $dat[kss]+($data["m".$i."c"] - 1);
				if ($i == 6)
				{
					$weapon = $db->query("SELECT wtype,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$dat[module_id],4);
					$phaser = round($weapon[strength] * (1 + (log($data["m6c"]) / log(2))/3));
					$vari = $weapon[varianz];
				}
				if ($dat[stellar] == 1) $stellar = 1;
				$i++;
			}
			if (!$weapon) $phaser = 0;
			$points = round(($data[wp] * $points) / 10);
			if (!$reaktor) $reaktor = $data[reaktor];
			$evade += $data[evade_val];
			if (!$ev_mul) $ev_mul = 1;
			$evade = round($evade * $ev_mul);
			if (!$hit_mul) $hit_mul = 1;
			$hit = round($hit * $hit_mul);
			$batt = $data["m8c"]*2;
			$bz = round($data[buildtime]*$bz/1000);
			$main = round($data[maintaintime]*(2- ($maintain/1100)));
			$time = time()+$bz;
			if ($_SESSION[m9]) $cloak = 1;
			if ($_SESSION[m5] && $_SESSION[m11]) $warp = 1;
			$plan = $db->query("INSERT INTO stu_ships_buildplans (rumps_id,user_id,name,m1,m2,m3,m4,m5,m6,m7,m8,m9,m10,m11) VALUES ('".$data[rumps_id]."','".$_GET[npc]."','".addslashes($_SESSION[name])."','".$_SESSION[m1]."','".$_SESSION[m2]."','".$_SESSION[m3]."','".$_SESSION[m4]."','".$_SESSION[m5]."','".$_SESSION[m6]."','".$_SESSION[m7]."','".$_SESSION[m8]."','".$_SESSION[m9]."','".$_SESSION[m10]."','".$_SESSION[m11]."')",5);
			if ($plan > 0) $db->query("UPDATE stu_ships_buildplans SET evade=".$evade.",treffer=".$hit.",reaktor=".$reaktor.",wkkap=".$wkkap.",max_torps=".$torps.",maintaintime=".$main.",buildtime=".$bz.",stellar='".$stellar."',sensor_val=".$detect.",cloak_val=".$cloak." WHERE plans_id=".$plan);
			echo "<b>Bauplan eingetragen</b>";
		}
	}
	echo "<table><tr><td width=300><b>Ausstattung</b><br><br>";
	$i=1;
	$points=0;
	while($i<=11)
	{
		if (!$_SESSION["m".$i] || $_SESSION["m".$i] == 0)
		{
			$i++;
			continue;
		}
		$dat = $db->query("SELECT * FROM stu_modules WHERE module_id=".$_SESSION["m".$i],4);
		$huelle += $dat[huelle]*$data["m".$i."c"];
		$points += $dat[points];
		$schilde += $dat[schilde]*$data["m".$i."c"];
		$reaktor += $dat[reaktor]*$data["m".$i."c"];;
		$wkkap += $dat[wkkap]*$data["m".$i."c"];;
		$eps += $dat[eps]*$data["m".$i."c"];
		if ($i == 6)
		{
			$weapon = $db->query("SELECT wtype,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$dat[module_id],4);
			$phaser = round($weapon[strength] * (1 + (log($data["m6c"]) / log(2))/3));
			$vari = $weapon[varianz];
		}
		echo "<a href=?p=np&nm".$i."=1&id=".$_GET[id]."><img src=../gfx/goods/".$dat[module_id].".gif border=0 title=\"Ersetzen?\"> ".$dat[name]."</a><br>";
		$i++;
	}
	$points = round(($data[wp] * $points) / 10);
	echo "</td><td valign=top width=200><b>Daten</b><br><br>Hülle: ".$huelle."<br>Schilde: ".$schilde."<br>EPS: ".$eps." (".($data[m8c]*2).")<br>Reaktor: ".$reaktor." (".$wkkap.")<br>Punkte: ".$points."</td><td valign=top><b>NPC auswählen</b><br><br>";
	$result = $db->query("SELECT id,user FROM stu_user WHERe id<100 ORDER BY id");
	while($dat = mysql_fetch_assoc($result)) echo "<a href=?p=np&id=".$_GET[id]."&a=b&npc=".$dat[id].">".$dat[user]."</a><br>";
	echo "</td></tr></table>";
}
if ($_GET[p] == "ep" && $_GET["id"])
{
	$data = $db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$_GET[id],4);
	if (!$_SESSION[user_id])
	{
		$dat = $db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id=".$_GET[pid],4);
		$_SESSION[m1] = $dat[m1];
		$_SESSION[m2] = $dat[m2];
		$_SESSION[m3] = $dat[m3];
		$_SESSION[m4] = $dat[m4];
		$_SESSION[m5] = $dat[m5];
		$_SESSION[m6] = $dat[m6];
		$_SESSION[m7] = $dat[m7];
		$_SESSION[m8] = $dat[m8];
		$_SESSION[m9] = $dat[m9];
		$_SESSION[m10] = $dat[m10];
		$_SESSION[m11] = $dat[m11];
		$_SESSION[user_id] = $dat[user_id];
	}
	
	if ($_GET[m1]) $_SESSION[m1] = $_GET[m1];
	if ($_GET[m2]) $_SESSION[m2] = $_GET[m2];
	if ($_GET[m3]) $_SESSION[m3] = $_GET[m3];
	if ($_GET[m4]) $_SESSION[m4] = $_GET[m4];
	if ($_GET[m5]) $_SESSION[m5] = $_GET[m5];
	if ($_GET[m6]) $_SESSION[m6] = $_GET[m6];
	if ($_GET[m7]) $_SESSION[m7] = $_GET[m7];
	if ($_GET[m8]) $_SESSION[m8] = $_GET[m8];
	if ($_GET[m9]) $_SESSION[m9] = $_GET[m9];
	if ($_GET[m10]) $_SESSION[m10] = $_GET[m10];
	if ($_GET[m11]) $_SESSION[m11] = $_GET[m11];
	
	if ($_GET[nm1] == 1) unset($_SESSION[m1]);
	if ($_GET[nm2] == 1) unset($_SESSION[m2]);
	if ($_GET[nm3] == 1) unset($_SESSION[m3]);
	if ($_GET[nm4] == 1) unset($_SESSION[m4]);
	if ($_GET[nm5] == 1) unset($_SESSION[m5]);
	if ($_GET[nm6] == 1) unset($_SESSION[m6]);
	if ($_GET[nm7] == 1) unset($_SESSION[m7]);
	if ($_GET[nm8] == 1) unset($_SESSION[m8]);
	if ($_GET[nm9] == 1) unset($_SESSION[m9]);
	if ($_GET[nm10] == 1) unset($_SESSION[m10]);
	if ($_GET[nm11] == 1) unset($_SESSION[m11]);
	
	if (!$_SESSION[m1])
	{
		echo "<b>Hüllenmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=1 AND level>=".$data["m1minlvl"]." AND level<=".$data["m1maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m1=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Hülle: ".($data[m1c]*$dat[huelle]).")<br>";
		exit;
	}
	if (!$_SESSION[m2])
	{
		echo "<b>Schildmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=2 AND level>=".$data["m2minlvl"]." AND level<=".$data["m2maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m2=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Schilde: ".($data[m2c]*$dat[schilde]).")<br>";
		exit;
	}
	if (!$_SESSION[m3])
	{
		echo "<b>Computermodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=3 AND level>=".$data["m3minlvl"]." AND level<=".$data["m3maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m3=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m4])
	{
		echo "<b>Sensorenmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=4 AND level>=".$data["m4minlvl"]." AND level<=".$data["m4maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m4=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m5] && $data[m5c] > 0)
	{
		echo "<b>Warpkernmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=5 AND level>=".$data["m5minlvl"]." AND level<=".$data["m5maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m5=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Reaktor: ".$dat[reaktor]." | Kapazität: ".$dat[wkkap].")<br>";
		exit;
	}
	if (!$_SESSION[m6] && $data[m6c] > 0)
	{
		echo "<b>Waffenmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=6 AND level>=".$data["m6minlvl"]." AND level<=".$data["m6maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res))
		{
			$weapon = $db->query("SELECT wtype,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$dat[module_id],4);
			$phaser = round($weapon[strength] * (1 + (log($data[m6c]) / log(2))/3));
			echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m6=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Stärke: ".$phaser." | Abweichung: ".$weapon[varianz]."%)<br>";
		}
		exit;
	}
	if (!$_SESSION[m7] && $data[m7c] > 0)
	{
		echo "<b>Antriebsmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=7 AND level>=".$data["m7minlvl"]." AND level<=".$data["m7maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m7=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m8])
	{
		echo "<b>EPSmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=8 AND level>=".$data["m8minlvl"]." AND level<=".$data["m8maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m8=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (EPS: ".($data[m8c]*$dat[eps]).")<br>";
		exit;
	}
	if (!$_SESSION[m9] && $data[m9c] > 0)
	{
		echo "<b>Tarnungsmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=9 AND level>=".$data["m9minlvl"]." AND level<=".$data["m9maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=np&id=".$_GET["id"]."&pid=".$_GET[pid]."&m9=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m10] && $data[m10c] > 0)
	{
		echo "<b>Torpedorampenmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=10 AND level>=".$data["m10minlvl"]." AND level<=".$data["m10maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m10=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a> (Torpedos: ".$dat[torps]." | Typ: ".$dat[torp_type].")<br>";
		exit;
	}
	if (!$_SESSION[m11] && $data[m11c] > 0)
	{
		echo "<b>Warpantriebsmodul wählen</b><br><br>";
		$res = $db->query("SELECT * FROM stu_modules WHERE type=11 AND level>=".$data["m11minlvl"]." AND level<=".$data["m11maxlvl"]." ORDER BY level");
		while($dat=mysql_fetch_assoc($res)) echo "<a href=?p=ep&id=".$_GET["id"]."&pid=".$_GET[pid]."&m11=".$dat[module_id]."> <img src=../gfx/goods/".$dat[module_id].".gif border=0> ".$dat[name]."</a><br>";
		exit;
	}
	if (!$_SESSION[m5]) $_SESSION[m5] = 0;
	if (!$_SESSION[m6]) $_SESSION[m6] = 0;
	if (!$_SESSION[m7]) $_SESSION[m7] = 0;
	if (!$_SESSION[m9]) $_SESSION[m9] = 0;
	if (!$_SESSION[m10]) $_SESSION[m10] = 0;
	if (!$_SESSION[m11]) $_SESSION[m11] = 0;
	
	if ($_GET[a] == "b")
	{
		$stellar = 0;
		while($i<=11)
		{
			if (!$_SESSION["m".$i] || $_SESSION["m".$i] == 0)
			{
				$i++;
				continue;
			}
			$dat = $db->query("SELECT * FROM stu_modules WHERE module_id=".$_SESSION["m".$i],4);
			$huelle += $dat[huelle]*$data["m".$i."c"];
			$bz += $dat[buildtime];
			$points += $dat[points];
			$schilde += $dat[schilde]*$data["m".$i."c"];
			$maintain += $dat[maintaintime];
			$reaktor += $dat[reaktor]*$data["m".$i."c"];;
			$wkkap += $dat[wkkap]*$data["m".$i."c"];;
			$eps += $dat[eps]*$data["m".$i."c"];
			if ($dat[type] == 1)
			{
				if ($dat[special_id1] == 1)
				{
					$ev_mul = 1.1;
					$hit_mul = 1.1;
				}
				if ($dat[special_id1] == 2)
				{
					$ev_mul = 0.9;
					$hit_mul = 0.9;
				}
			}
			$hit += $dat[hit_val];
			$torps += $dat[torps]*$data["m".$i."c"];;
			$detect += $dat[detect_val];
			$cloak += $dat[cloak_val];
			if ($i == 4)
			{
				$lss = $dat[lss]+($data["m".$i."c"] - 1);
				$kss = $dat[kss]+($data["m".$i."c"] - 1);
			}
			if ($i == 6)
			{
				$weapon = $db->query("SELECT wtype,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$dat[module_id],4);
				$phaser = round($weapon[strength] * (1 + (log($data["m6c"]) / log(2))/3));
				$vari = $weapon[varianz];
			}
			if ($dat[stellar] == 1) $stellar = 1;
			$i++;
		}
		if (!$weapon) $phaser = 0;
		$points = round(($data[wp] * $points) / 10);
		if (!$reaktor) $reaktor = $data[reaktor];
		$evade += $data[evade_val];
		if (!$ev_mul) $ev_mul = 1;
		$evade = round($evade * $ev_mul);
		if (!$hit_mul) $hit_mul = 1;
		$hit = round($hit * $hit_mul);
		$batt = $data["m8c"]*2;
		$bz = round($data[buildtime]*$bz/1000);
		$main = round($data[maintaintime]*(2- ($maintain/1100)));
		$time = time()+$bz;
		if ($_SESSION[m9]) $cloak = 1;
		if ($_SESSION[m5] && $_SESSION[m11]) $warp = 1;
		$db->query("UPDATE stu_ships_buildplans SET evade=".$evade.",treffer=".$hit.",reaktor=".$reaktor.",wkkap=".$wkkap.",max_torps=".$torps.",maintaintime=".$main.",buildtime=".$bz.",stellar='".$stellar."',sensor_val=".$detect.",cloak_val=".$cloak.",m1=".$_SESSION[m1].",m2=".$_SESSION[m2].",m3=".$_SESSION[m3].",m4=".$_SESSION[m4].",m5=".$_SESSION[m5].",m6=".$_SESSION[m6].",m7=".$_SESSION[m7].",m8=".$_SESSION[m8].",m9=".$_SESSION[m9].",m10=".$_SESSION[m10].",m11=".$_SESSION[m11]." WHERE plans_id=".$_GET[pid]);
		$db->query("UPDATE stu_ships SET huelle=".$huelle.",max_huelle=".$huelle.",schilde=".$schilde.",max_schilde=".$schilde.",eps=".$eps.",max_eps=".$eps.",kss_range=".$kss.",lss_range=".$lss.",batt=".$batt.",max_batt=".$batt." WHERE plans_id=".$_GET[pid]." AND user_id=".$_SESSION[user_id]);
		echo "<b>Bauplan aktualisiert</b>";
	}
	echo "<table><tr><td width=300><b>Ausstattung</b><br><br>";
	$i=1;
	$points=0;
	while($i<=11)
	{
		if (!$_SESSION["m".$i] || $_SESSION["m".$i] == 0)
		{
			$i++;
			continue;
		}
		$dat = $db->query("SELECT * FROM stu_modules WHERE module_id=".$_SESSION["m".$i],4);
		$huelle += $dat[huelle]*$data["m".$i."c"];
		$points += $dat[points];
		$schilde += $dat[schilde]*$data["m".$i."c"];
		$reaktor += $dat[reaktor]*$data["m".$i."c"];;
		$wkkap += $dat[wkkap]*$data["m".$i."c"];;
		$eps += $dat[eps]*$data["m".$i."c"];
		if ($i == 6)
		{
			$weapon = $db->query("SELECT wtype,pulse,varianz,strength,shields_through,critical,mgoods_id,mcount FROM stu_weapons WHERE module_id=".$dat[module_id],4);
			$phaser = round($weapon[strength] * (1 + (log($data["m6c"]) / log(2))/3));
			$vari = $weapon[varianz];
		}
		echo "<a href=?p=ep&nm".$i."=1&id=".$_GET[id]."&pid=".$_GET[pid]."><img src=../gfx/goods/".$dat[module_id].".gif border=0 title=\"Ersetzen?\"> ".$dat[name]."</a><br>";
		$i++;
	}
	$points = round(($data[wp] * $points) / 10);
	echo "</td><td valign=top width=200><b>Daten</b><br><br>Hülle: ".$huelle."<br>Schilde: ".$schilde."<br>EPS: ".$eps." (".($data[m8c]*2).")<br>Reaktor: ".$reaktor." (".$wkkap.")<br>Punkte: ".$points."</td><td valign=top><b>NPC auswählen</b><br><br>";
	echo "<a href=?p=ep&id=".$_GET[id]."&a=b&pid=".$_GET[pid].">Update</a><br>";
	echo "</td></tr></table>";
}
?>