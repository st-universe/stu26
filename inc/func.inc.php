<?php
function show_error($eId=0)
{
	if ($eId == 0) global $eId;
	global $_POST,$_SESSION,$_GET,$sess;
	if (!check_int($eId));
	switch($eId)
	{
		case 100:
			$txt = "Verbindung zur Datenbank fehlgeschlagen";
			addlog($eId,0,$txt,1);
			if ($sess) $sess->logout();
			break;
		case 101:
			$txt = "Es wurde kein User mit diesem Loginnamen gefunden oder das Passwort ist inkorrekt.";
			addlog($eId,0,$txt,9);
			break;
		case 102:
			$txt = "Der User wurde noch nicht aktiviert";
			break;
		case 103:
			$txt = "Der Account wurde aufgrund eines vermeintlichen Regelverstoßes gesperrt. Details findest du im Gerichtsforum unter <a href='http://forum.stuniverse.de/viewforum.php?f=19'><font color='#0000ff'>http://forum.stuniverse.de/viewforum.php?f=19</font></a>";
			addlog($eId,$_SESSION['uid'],$txt,6);
			if ($sess) $sess->logout();
			break;
		case 104:
			$txt = "Es wurde kein User mit diesem Loginnamen gefunden oder das Passwort ist inkorrekt.";
			addlog($eId,0,$txt." ".$_POST['login']."/".$_POST['pass'],7);
			break;
		case 105:
			$txt = "Du wurdest ausgeloggt";
			break;
		case 106:
			$txt = "Session abgelaufen. Bitte neu einloggen";
			session_destroy();
			break;
		case 107:
			$txt = "Der Urlaubsmodus ist aktiv und kann frühestens am ".date("d.m.Y H:i",$_SESSION['vac_blocktime'])." beendet werden";
			break;
		case 108:
			$txt = "Die Accountlöschung wurde bestätigt. Ein Login ist nicht mehr möglich";
			break;
		case 109:
			$txt = "Dieser Account wurde gelöscht";
			$_SESSION['login'] = 0;
			break;
		case 200:
			$txt = "Ein allgemeiner Fehler is aufgetreten";
			break;
		case 201:
			$txt = "Du musst zuerst Level 2 erreichen. Dazu musst Du einen Klasse-M Planeten koloniseren";
			break;
		case 900:
			$txt = "Im Moment ist der Rundenwechsel aktiv";
			if ($_POST['pm'] == 1 && $_POST['recipient'])
			{
				$txt .= "<br /><br /><div style=\"color: #ff0000\">Achtung!</div>
				Die abgeschickte Nachricht wurde durch den Tick blockiert!<br />Bitte warte ein paar Minuten und klicke dann auf \"Nachricht senden\"<br/>
				<form action=main.php method=post name=pm><input type=hidden name=pm value=1>
				<input type=hidden name=p value=comm><input type=hidden name=s value=nn>
				<input type=hidden name=recipient value=".$_POST['recipient']."><input type=hidden name=text value=\"".addslashes(stripslashes($_POST['text']))."\">
				<input type=hidden name=repl value=\"".$_GET['repl']."\">
				<input type=submit value=\"Nachricht senden\" class=button></form>";
			}
			break;
		case 901:
			$txt = "Im Moment ist der Wartungsmodus aktiv";
			break;
		case 902:
			$txt = "Unerwartete Eingabe, Eintrag im Fehler-Log wurde erstellt.";
			addlog($eId,$_SESSION['uid'],"<font color=FF0000>Betrugsversuch!</font><br>Seite: ".$_GET['p']." - Sektion: ".$_GET['s'],1);
			$sess->logout();
			break;
		case 903:
			$txt = "Der Urlaubsmodus wurde aktiviert.<br>Du wurdest automatisch ausgeloggt";
			addlog($eId,$_SESSION['uid'],"Urlaubsmodus aktiviert",1);
			$sess->logout();
			break;
	}
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
		<title>Star Trek Universe</title>
		<link rel="STYLESHEET" type="text/css" href=gfx/css/6.css>
	</head>
	<body bgcolor=#000000>
	<table align=center bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th align=center><b>Meldung '.$eId.'</b></th></tr>
	<tr><td>'.$txt.'<br><br>';
	if ($eId == 901 || $eId == 900) echo '<a href=?p=desk>Aktualisieren</a><br><br>';
	echo '<a href=http://www.stuniverse.de title="Star Trek Universe" target="_parent">Star Trek Universe</a> - <a href="http://forum.stuniverse.de" target="_blank">STU-Forum</a></td></tr>
	</table>
	</body>
	</html>';
	exit;
}
function addlog($eId,$uid,$txt,$lvl=1)
{
	global $loglvl,$global_path;
	$ip = getenv("REMOTE_ADDR");
	$filename = date("d_m_y");
	$txt = addslashes(str_replace("\n","<br>",$txt));
	$logfile = fopen($global_path."intern/log/".$filename.".log","a+");
	if ($lvl <= $loglvl) fwrite($logfile,"[".date("H:i:s")."]%-%".$ip."%-%".$uid."%-%".$eId."%-%".$txt."\n");
	@fclose($logfile);
}
function meldung($txt)
{
	global $gfx;
	// echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	// <tr>
	// <th>Meldung</th>
	// </tr>
	// <tr>
	// <td>".stripslashes($txt)."</td>
	// </tr></table><br>";
	echo "<div>
	".fixedPanel(3,"Meldung","meldung",$gfx."/buttons/icon/exclamation.gif","<div style=\"padding:4px;\">".stripslashes($txt)."</div>")."
	</div>";
}
function format_string($string)
{
	$string = str_replace("style","",$string);
	$string = strip_tags($string,"<b></b><u></u><i></i><font></font>");
	return $string;
}
function pageheader($txt)
{
	global $db,$_GET,$u,$_SESSION,$gfx,$g;
	// Vars
	$npm1 = 0;
	$npm2 = 0;
	$npm3 = 0;
	$npm4 = 0;
	$npm5 = 0;
	// Info-Header
	if ($_GET['s'] != "nz")
	{
		if (($_GET['p'] == "ship" || $_GET['p'] == "stat") && $_GET['id']) $coadd = "&shd=".$_GET['id'];
		// Kolonieliste erzeugen
		$result = $u->getcols();
		while($data=mysql_fetch_assoc($result))
		{
			$onm = "onmouseover=\"return overlib('<table class=tcal><th>Kolonieinformationen ".ftit($data['name'])."</th><tr><td>Koordinaten: x|y: ".$data['sx']."|".$data['sy']."<br>System: ".ftit($data['sname'])." (x|y: ".$data['cx']."|".$data['cy'].")<br>Energie: ".$data['eps']."/".$data['max_eps']."<br>Lager: ".(!$data['sc'] ? 0 : $data['sc'])."/".$data['max_storage'].($data['max_schilde'] > 0 ? "<br>Schilde: ".$data['schilde']."/".$data['max_schilde'] : "")."</td></tr></table>', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', ABOVE, CELLPAD, 0, 0, 0, 0, CENTER);\" onmouseout=\"nd();\"";
			// $cls.= "<a href=?p=colony&s=sc&id=".$data['id'].$coadd."><img src=".$gfx."/planets/".$data['colonies_classes_id']."s.gif width=20 height=20 border=0 ".$onm."></a>&nbsp;";
			if ($data['colonies_classes_id'] > 300) 	$cl .= "<tr><td style=\"width:30px;height:30px;text-align:center;\"><a href=?p=colony&s=sc&id=".$data['id'].$coadd."><img src=".$gfx."/planets/".$data['colonies_classes_id'].".gif border=0 width=20 height=20 title=\"".ftit($data['name'])."\" ".$onm."></a></td><td>".renderstatusbar($data['eps'],$data['max_eps'],"yel")."<br>".renderstatusbar($data['sc'],$data['max_storage'],"gre").($data['max_schilde'] > 0 ? "<br>".rendershieldstatusbar($data['schilde_status'],$data['schilde'],$data['max_schilde']) : "")."</td><td><a href=?p=colony&s=loi&id=".$data['id']." ".getHover('col'.$data['id'],'inactive/n/text','hover/g/text')."><img src=".$gfx."/buttons/inactive/n/text.gif border=0 name=col".$data['id']." title=\"Schiffe im Orbit\"></a></td></tr>";
			else 										$cl .= "<tr><td style=\"width:30px;height:30px;text-align:center;\"><a href=?p=colony&s=sc&id=".$data['id'].$coadd."><img src=".$gfx."/planets/".$data['colonies_classes_id'].".gif border=0 width=30 height=30 title=\"".ftit($data['name'])."\" ".$onm."></a></td><td>".renderstatusbar($data['eps'],$data['max_eps'],"yel")."<br>".renderstatusbar($data['sc'],$data['max_storage'],"gre").($data['max_schilde'] > 0 ? "<br>".rendershieldstatusbar($data['schilde_status'],$data['schilde'],$data['max_schilde']) : "")."</td><td><a href=?p=colony&s=loi&id=".$data['id']." ".getHover('col'.$data['id'],'inactive/n/text','hover/g/text')."><img src=".$gfx."/buttons/inactive/n/text.gif border=0 name=col".$data['id']." title=\"Schiffe im Orbit\"></a></td></tr>";
			
		}
		echo "<div class=\"colinfo\">
		<table class=\"tcal\">".$cl."</table></div>";
		// echo "<div class=\"mainpageheader\"><table class=\"tcal\"><tr><td style=\"font-weight: bold;\" width=\"200\"><div id=\"pmcheck\">";
		$result = $db->query("SELECT COUNT(id) as npms,type FROM stu_pms WHERE recip_user=".$_SESSION['uid']." AND new='1' AND recip_del='0' GROUP BY type ORDER BY type");
		if (mysql_num_rows($result) > 0)
		{
			while ($data=mysql_fetch_assoc($result)) ${"npm".$data['type']} = $data['npms'];
			mysql_free_result($result);
		}
		
		$pmsstring = "";
		
		
		if ($npm1 > 0) 
			$pmsstring .= "<span class=shortcut><a class=newpm href=?p=comm&s=pe&cat=1 title=\"Neue private Nachrichten!\"><img src=".$gfx."/buttons/active/w/pm_in.gif> ".$npm1."</a></span>";
		else 
			$pmsstring .= "";

		if ($npm2 > 0) 
			$pmsstring .= "<span class=shortcut><a class=newpm href=?p=comm&s=pe&cat=2 title=\"Neue Handels-Nachrichten!\"><img src=".$gfx."/buttons/active/w/trade.gif> ".$npm2."</a></span>";
		else 
			$pmsstring .= "";
		
		if ($npm3 > 0) 
			$pmsstring .= "<span class=shortcut><a class=newpm href=?p=comm&s=pe&cat=3 title=\"Neue Schiffs-Nachrichten!\"><img src=".$gfx."/buttons/active/w/ship.gif> ".$npm3."</a></span>";
		else 
			$pmsstring .= "";
		
		if ($npm4 > 0) 
			$pmsstring .= "<span class=shortcut><a class=newpm href=?p=comm&s=pe&cat=4 title=\"Neue Kolonie-Nachrichten!\"><img src=".$gfx."/buttons/active/w/planet.gif> ".$npm4."</a></span>";
		else 
			$pmsstring .= "";
		

		// echo $pmsstring;
		
		// echo "</div></td><td width=\"200\">".$cls;
		// $rp = $g->getresearchpoints();
		// $sd = $g->showdominion();
		// echo "</td><td width=\"600\">";
		$alldone = false;
		switch($_SESSION['level']) {
		
			case 1: 
					$critarray = array();
					array_push($critarray,checkLevelCriterion($_SESSION['level'],1,$_SESSION['uid']));
					array_push($critarray,checkLevelCriterion($_SESSION['level'],2,$_SESSION['uid']));
					array_push($critarray,checkLevelCriterion($_SESSION['level'],3,$_SESSION['uid']));
					array_push($critarray,checkLevelCriterion($_SESSION['level'],4,$_SESSION['uid']));
					$alldone = true;
					foreach($critarray as $v) if (!$v['done']) $alldone = false;
					
					// if (!$alldone) {
						// echo "<b>Für Level 2:</b> ";					
						// foreach($critarray as $v) echo displayCriterion($v);
					// } else {
						// echo "<a href=?p=main&a=gnl><img src=".$gfx."/buttons/stern1.gif> <b><font color='#66ff66'>Level 2 erreicht! Klicke hier, um aufzusteigen!</font></b></a>";
					// }
					break;
			case 2: 
					$critarray = array();
					array_push($critarray,checkLevelCriterion($_SESSION['level'],1,$_SESSION['uid']));
					array_push($critarray,checkLevelCriterion($_SESSION['level'],2,$_SESSION['uid']));
					$alldone = true;
					foreach($critarray as $v) if (!$v['done']) $alldone = false;
					
					// if (!$alldone) {
						// echo "<b>Für Level 3:</b> ";					
						// foreach($critarray as $v) echo displayCriterion($v);
					// } else {
						// echo "<a href=?p=main&a=gnl><img src=".$gfx."/buttons/stern1.gif> <b><font color='#66ff66'>Level 3 erreicht! Klicke hier, um aufzusteigen!</font></b></a>";
					// }
					break;	
			case 3: 
					$critarray = array();
					array_push($critarray,checkLevelCriterion($_SESSION['level'],1,$_SESSION['uid']));
					array_push($critarray,checkLevelCriterion($_SESSION['level'],2,$_SESSION['uid']));
					array_push($critarray,checkLevelCriterion($_SESSION['level'],3,$_SESSION['uid']));
					$alldone = true;
					foreach($critarray as $v) if (!$v['done']) $alldone = false;
					
					// if (!$alldone) {
						// echo "<b>Für Level 4:</b> ";					
						// foreach($critarray as $v) echo displayCriterion($v);
					// } else {
						// echo "<a href=?p=main&a=gnl><img src=".$gfx."/buttons/stern1.gif> <b><font color='#66ff66'>Level 4 erreicht! Klicke hier, um aufzusteigen!</font></b></a>";
					// }
					break;	
			case 4: 
					$critarray = array();
					array_push($critarray,checkLevelCriterion($_SESSION['level'],1,$_SESSION['uid']));
					array_push($critarray,checkLevelCriterion($_SESSION['level'],2,$_SESSION['uid']));
					array_push($critarray,checkLevelCriterion($_SESSION['level'],3,$_SESSION['uid']));
					$alldone = true;
					foreach($critarray as $v) if (!$v['done']) $alldone = false;
					
					// if (!$alldone) {
						// echo "<b>Für Level 5:</b> ";					
						// foreach($critarray as $v) echo displayCriterion($v);
					// } else {
						// echo "<a href=?p=main&a=gnl><img src=".$gfx."/buttons/stern1.gif> <b><font color='#66ff66'>Level 5 erreicht! Klicke hier, um aufzusteigen!</font></b></a>";
					// }
					break;
			default: echo "";
		}

		if ($alldone) 
			$lvlstring = "<span class=shortcut><a class=newpm href=?p=main&a=gnl><img src=".$gfx."/buttons/active/g/super.gif> Lvl!</a></span>";
		else 
			$lvlstring = "";
		
		
		if (isset($research))
			$researchstring = "<span class=shortcut><a class=newpm href=?p=comm&s=pe&cat=4 title=\"Neue Kolonie-Nachrichten!\"><img src=".$gfx."/buttons/active/g/super.gif> Lvl!</a></span>";
		else 
			$researchstring = "";
		
		
		$planets = $db->query("SELECT COUNT(id) as cols FROM stu_colonies WHERE user_id=".$_SESSION['uid']." AND colonies_classes_id <'300'",1);
		$moons = $db->query("SELECT COUNT(id) as cols FROM stu_colonies WHERE user_id=".$_SESSION['uid']." AND colonies_classes_id >'300'",1);
		
		// switch($_SESSION['level']) {
		
			// case 3: 
					// if ($planets+$moons != 2) {
						// echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Ungenutztes Kolonielimit:</b>";
						// if ($planets < 1) echo "<img src=".$gfx."/planets/201s.gif title=\"Planeten kolonisiert\" width=24px height=24px><font color='#ff6666'>".$planets."/1</font> ";
						// if ($moons < 1) echo "<img src=".$gfx."/planets/301s.gif title=\"Monde kolonisiert\" width=24px height=24px><font color='#ff6666'>".$moons."/1</font> ";
					// }
					// break;	
			// case 4: 
					// if ($planets+$moons != 4) {
						// echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Ungenutztes Kolonielimit:</b>";
						// if ($planets < 2) echo "<img src=".$gfx."/planets/201s.gif title=\"Planeten kolonisiert\" width=24px height=24px><font color='#ff6666'>".$planets."/2</font> ";
						// if ($moons < 2) echo "<img src=".$gfx."/planets/301s.gif title=\"Monde kolonisiert\" width=24px height=24px><font color='#ff6666'>".$moons."/2</font> ";
					// }
					// break;	
			// case 5:
					// if ($planets+$moons < 10) {
						// echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Ungenutztes Kolonielimit:</b>";
						// if ($planets < 4) echo "<img src=".$gfx."/planets/201s.gif title=\"Planeten kolonisiert\" width=24px height=24px><font color='#ff6666'>".$planets."/4</font> ";
						// if ($moons < 6) echo "<img src=".$gfx."/planets/301s.gif title=\"Monde kolonisiert\" width=24px height=24px><font color='#ff6666'>".$moons."/6</font> ";
					// }
					// break;	
			// default: echo "";
		// }

		
		// echo "</td>";
		// echo "<td style=\"text-align: right;\"><img src=".$gfx."/buttons/time.gif title=\"Serverzeit\"> <span id=\"uhrzeit\"></span></td></tr></table>
		// </div>";
		
		$hasmessage = 0;
		if ($lvlstring) $hasmessage = 1;
		if ($researchstring) $hasmessage = 1;
		if ($pmsstring) $hasmessage = 1;
		
		echo "<div class=\"mainpageheader menuColor1 headerbox".($hasmessage ? " alert" : "")."\" style=\"margin-bottom:5px;\"><table style=\"width:100%;\"><tr><th>".strip_tags($txt,"<a></a>")."</th><th style=\"vertical-align:middle;width:360px;text-align:right;\">".$lvlstring.$researchstring.$pmsstring."</th></tr></table></div>";
		
		echo "<div class=mainpagecontent>";
		unset($rp);
	}
	else echo "<div style=\"position: absolute; left: 0px; top: 0px; right: 0px;\">";
	
}

function getColonyLimit($lvl) {
	switch($lvl) {
	
		case 0: return array("p" => 1, "m" => 0);
		case 1: return array("p" => 1, "m" => 0);
		case 2: return array("p" => 1, "m" => 0);
		case 3: return array("p" => 1, "m" => 1);
		case 4: return array("p" => 2, "m" => 2);
		case 5: return array("p" => 4, "m" => 6);
	}
	return 0;
}


function getFieldFaction($x,$y,$system) {
	global $db;
	if ($system > 0) {
		$sys = $db->query("SELECT * FROM stu_systems WHERE systems_id = ".$system." LIMIT 1;",4);
		$x = $sys['cx'];
		$y = $sys['cy'];
	} 
	$region = $db->query("SELECT a.*,b.* FROM stu_map as a LEFT JOIN stu_map_regions as b on a.region = b.id WHERE a.cx=".$x." AND a.cy=".$y." LIMIT 1;",4);
	return $region['faction'];
	
}






function ColorHSLToRGB($h, $s, $l){

        $r = $l;
        $g = $l;
        $b = $l;
        $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
        if ($v > 0){
              $m;
              $sv;
              $sextant;
              $fract;
              $vsf;
              $mid1;
              $mid2;

              $m = $l + $l - $v;
              $sv = ($v - $m ) / $v;
              $h *= 6.0;
              $sextant = floor($h);
              $fract = $h - $sextant;
              $vsf = $v * $sv * $fract;
              $mid1 = $m + $vsf;
              $mid2 = $v - $vsf;

              switch ($sextant)
              {
                    case 0:
                          $r = $v;
                          $g = $mid1;
                          $b = $m;
                          break;
                    case 1:
                          $r = $mid2;
                          $g = $v;
                          $b = $m;
                          break;
                    case 2:
                          $r = $m;
                          $g = $v;
                          $b = $mid1;
                          break;
                    case 3:
                          $r = $m;
                          $g = $mid2;
                          $b = $v;
                          break;
                    case 4:
                          $r = $mid1;
                          $g = $m;
                          $b = $v;
                          break;
                    case 5:
                          $r = $v;
                          $g = $m;
                          $b = $mid2;
                          break;
              }
        }
        return array('r' => $r * 255.0, 'g' => $g * 255.0, 'b' => $b * 255.0);
}

function fractionalColor($frac) {
	$h = 1 - $frac;
	$cols = ColorHSLToRGB((0.35 * $h),0.9,0.5);
	$dR = str_pad(dechex(round($cols['r'])), 2, "0", STR_PAD_LEFT);
	$dG = str_pad(dechex(round($cols['g'])), 2, "0", STR_PAD_LEFT);
	$dB = str_pad(dechex(round($cols['b'])), 2, "0", STR_PAD_LEFT);
	return $dR.$dG.$dB;
}


function modvalname($type) {

	switch($type) {
		case "hull": return "Hüllenstärke";
		case "shield": return "Schildstärke";
		case "shieldreg": return "Schildregeneration";
		case "eps": return "EPS";
		case "core": return "Warpkernladung";
		case "reactor": return "Reaktorleistung";
		case "hitchance": return "Trefferchance";
		case "sensor": return "Sensorreichweite";
		case "warpdrive": return "Max. Warpantrieb";
		case "warpregen": return "Warpantrieb/Tick";
		case "colonize": return "Ermöglicht Kolonisierung";
		case "nocost": return "Kostenlos";
		case "col_ore": return "Erzabbau/Tick";
		case "col_deut": return "Deuteriumabbau/Tick";
		case "max_crew": return "Max. Crew";
		case "min_crew": return "Min. Crew";
		case "map": return "Ermöglicht Kartographierung";
		case "scansub": return "Ermöglicht Subraumscans";
		case "scanchr": return "Ermöglicht Chronitonscans";
		case "cloak": return "Tarnvorrichtung";
		case "useschr": return "Chroniton-Basiert";
		case "detect": return "Chroniton-Emitter";
		case "storage": return "Laderaum";
		case "evade": return "Ausweichchance";
		case "armor": return "Panzerung";
		default: return "Unbekannter Typ: ".$type;
	}
}


function modvaliconsss($type) {

	switch($type) {
		case "hull": return "ship";
		case "shield": return "shield";
		case "shieldreg": return "shieldplus";
		case "eps": return "energy";
		case "core": return "warpcore";
		case "reactor": return "energyplus";
		case "hitchance": return "ai";
		case "sensor": return "scanarea";
		case "warpdrive": return "warp";
		case "warpregen": return "energysystems";
		case "colonize": return "planet";
		case "nocost": return "yes";

		case "max_crew": return "alliance";
		case "min_crew": return "alliance";
		case "map": return "map";

		case "evade": return "no";
		case "armor": return "armor";
		default: return "info";
	}
}

function modvalicon($type) {
	global $gfx;
	
	return "<img src=".$gfx."/buttons/icon/".modvaliconsss($type).".gif>";
}

function modvalmapping($type,$count) {
	
	switch($type) {
		case "cloak" :		return "<font color=#33ff33/>".modvalname($type)."</font>";
		case "useschr" :	return "<font color=#ff3333/>".modvalname($type)."</font>";
		
		default: 
			if ($count  > 1) return "<font color=#33ff33/>+".$count." ".modvalname($type)."</font>";
			if ($count == 1 && ($type != "warpregen")) return "<font color=#33ff33/>".modvalname($type)."</font>";
			if ($count == 1 && ($type == "warpregen")) return "<font color=#33ff33/>+".$count." ".modvalname($type)."</font>";
			if ($count <= 0) return "<font color=#ff3333/>".$count." ".modvalname($type)."</font>";	
	}
	
	
	
}


function applyModifiersForModule($data,$moduleid) {
	global $db;	

	// case "hull": return "Hüllenstärke";
	// case "shield": return "Schildstärke";
	// case "eps": return "EPS";
	// case "core": return "Warpkernladung";
	// case "reactor": return "Reaktorleistung";
	// case "hitchance": return "Trefferchance";
	// case "sensor": return "Sensorreichweite";
	// case "warpdrive": return "Max. Warpantrieb";
	// case "warpregen": return "Warpantrieb/Tick";
	// case "colonize": return "Kolonisierung";
	// case "col_ore": return "Erzabbau/Tick";
	// case "col_deut": return "Deuteriumabbau/Tick";	
	
	$res = $db->query("SELECT * FROM stu_modules_special WHERE modules_id=".$moduleid."",0);
	while($special=mysql_fetch_assoc($res)) {
		if ($special['type'] == "hull") 		$data['huelle'] 			+= $special['value'];
		if ($special['type'] == "shield") 	$data['schilde'] 			+= $special['value'];
		if ($special['type'] == "eps") 		$data['eps'] 				+= $special['value'];
		if ($special['type'] == "core") 		$data['warpcore'] 		+= $special['value'];
		if ($special['type'] == "reactor") 	$data['reaktor'] 			+= $special['value'];
		// if ($special[type] == "hitchance") 	$data['eps'] 				+= $special['value'];
		if ($special['type'] == "sensor") 	$data['lss_range']		+= $special['value'];
		if ($special['type'] == "sensor") 	$data['kss_range']	 	+= $special['value'];
		if ($special['type'] == "warpdrive") 	$data['warpfields'] 		+= $special['value'];
		if ($special['type'] == "warpregen") 	$data['warpfield_regen'] 	+= $special['value'];
		if ($special['type'] == "max_crew") 	$data['max_crew'] 		+= $special['value'];
		if ($special['type'] == "min_crew") 	$data['min_crew'] 		+= $special['value'];
		if ($special['type'] == "cloak") 		$data['cloak'] 			+= $special['value'];
		if ($special['type'] == "storage") 	$data['storage'] 			+= $special['value'];
		if ($special['type'] == "evade") 	$data['evade'] 			+= $special['value'];
		if ($special['type'] == "hitchance") 	$data['hitchance'] 			+= $special['value'];
		if ($special['type'] == "armor") 	$data['armor'] 			+= $special['value'];
	}
		
	return $data;
}

function getShipValuesWithMods($rumpid,$modules) {
	global $db;	

	$rump = $db->query("SELECT * FROM stu_rumps WHERE rumps_id=".$rumpid." LIMIT 1",4);

	$ship = $rump;
	foreach($modules as $mod) {
		if ($mod == 0) continue;
		$ship = applyModifiersForModule($ship,$mod);
	}
	return $ship;
}

function getShipValuesForBuildplan($plan) {
	global $db;

	$plan = $db->query("SELECT * FROM stu_ships_buildplans WHERE plans_id=".$plan." LIMIT 1",4);
	if (!$plan['plans_id']) return array();

	$modules = array();
	if ($plan['m1'] != 0) array_push($modules,$plan['m1']);
	if ($plan['m2'] != 0) array_push($modules,$plan['m2']);
	if ($plan['m3'] != 0) array_push($modules,$plan['m3']);
	if ($plan['m4'] != 0) array_push($modules,$plan['m4']);
	if ($plan['m5'] != 0) array_push($modules,$plan['m5']);
	if ($plan['w1'] != 0) array_push($modules,$plan['w1']);
	if ($plan['w2'] != 0) array_push($modules,$plan['w2']);
	if ($plan['s1'] != 0) array_push($modules,$plan['s1']);
	if ($plan['s2'] != 0) array_push($modules,$plan['s2']);

	return getShipValuesWithMods($plan[rumps_id],$modules);
}



	function stepFleetLimit($c, $v, $w) {
		$steps = array(25,50,100,150,200,300,400,500,600,9999);
		
		
		$level = 1;
		
		foreach($steps as $step) {
			$miss_c = max(0, $step - $c);
			$miss_v = max(0, $step - $v);
			$miss_w = max(0, $step - $w);

			if (($miss_c + $miss_v + $miss_w) > 0) {
				break;
			}
			$level++;
		}
		
		$ret = array();
		$ret['miss_c'] = $miss_c;
		$ret['miss_v'] = $miss_v;
		$ret['miss_w'] = $miss_w;
		
		$ret['level'] = $level;
		$ret['step'] = $step;
		$ret['battleships'] = $level * 42;
		$ret['civilianships'] = 8 + $level * 3;
		
		return $ret;
	}













	function coordSort($a,$b) {
		
		$va = 1000 * $a['y'] + $a['x'];
		$vb = 1000 * $b['y'] + $b['x'];
		
		if ($va < $vb) return -1;
		return 1;
	}

	function addSensorLegend($arr) {
		
		$minx = 1000;
		$maxx =    0;
		$miny = 1000;
		$maxy =    0;
		
		$newarray = array();
		foreach ($arr['fields'] as $k => $v) {
			if ($v['x'] < $minx) $minx = $v['x'];
			if ($v['x'] > $maxx) $maxx = $v['x'];
			if ($v['y'] < $miny) $miny = $v['y'];
			if ($v['y'] > $maxy) $maxy = $v['y'];
			array_push($newarray,$v);
		}
		
		for ($i=$minx; $i <= $maxx; $i++) {
			$field['x'] = $i;
			$field['y'] = 0;
			$field['type'] = 0;
			$field['onclick'] = false;
			$field['class'] = "fieldlegend";
			$field['display'] = $i;
			array_push($newarray,$field);
		}
		for ($i=$miny; $i <= $maxy; $i++) {
			$field['x'] = 0;
			$field['y'] = $i;
			$field['type'] = 0;
			$field['onclick'] = false;
			$field['class'] = "fieldlegend";
			$field['display'] = $i;
			array_push($newarray,$field);
		}
		
		$field['x'] = 0;
		$field['y'] = 0;
		$field['type'] = 0;
		$field['onclick'] = false;
		$field['class'] = "fieldlegend";
		$field['display'] = "";
		array_push($newarray,$field);
		
		usort($newarray,"coordSort");
		$arr['fields'] = $newarray;
		return $arr;
	}

	function renderSensors($arr) {
		global $gfx;
		$lssstring = "<table class=\"mapfields suppressMenuColors\"><tr>";

		$yd = -1;
		
		foreach($arr['fields'] as $field) {
			if ($field['y'] != $yd && $yd != -1) 
				$lssstring .= "</tr>"; 
			if ($field['y'] != $yd) 
				$lssstring .= "<tr>"; 
			
			$yd = $field['y'];

			$lssstring .= "<td class=".$field['class']." ".($field['onclick'] ? "onclick=\"".$field['onclick']."\"" : "")." style=\"background-image:url('".$gfx."/map/".$field['type'].".gif');".($result['desaturated'] ? "background-blend-mode: luminosity;" : "")."\" >".$field['display']."</td>";
			$border = "";
			$cloak = "";
		}

		$lssstring .= "</tr></table>";
		return $lssstring;
	}




	function fixedPanel($subtype,$name,$id,$pic,$content,$class="menuColor") {
		global $gfx;
		// $subtype = 1;
		$m =  "<div id='%IDfixed' class='headerbox ".$class.$subtype."'>
			 	<table cellspacing=1 cellpadding=1\" style=\"width: 100%;height:25px;\">
					<tr>
						<th><img src=\"%PIC\" title=\"%NAME\"> %NAME</th>
					</tr>
				</table>
			  </div>		
			  <div id='%ID' class='contentbox ".$class.$subtype."'>";
			  
			  
		if (is_array($content)) {
			$m .= "<table cellspacing=1 cellpadding=1 style=\"width: 100%;\">";
	
			foreach($content as $cval) {
				$m .= $cval;
			}
		
			$m .= "</table>";
		} else {
			$m .= $content;
		}
		$m .= "</div>";
		
		$m = str_replace("%NAME", $name, $m);
		$m = str_replace("%ID", $id, $m);
		$m = str_replace("%PIC", $pic, $m);
		
		return $m;
	}
	
	function twodechex($i) {
		if ($i < 16) return "0".dechex(round($i));
		return dechex(round($i));
	}
	
	function darkerColor($base,$ratio) {
		$r = $ratio * hexdec(substr($base,1,2));
		$g = $ratio * hexdec(substr($base,3,2));
		$b = $ratio * hexdec(substr($base,5,2));
		
		return "#".twodechex($r).twodechex($g).twodechex($b);
		
	}
	
	
	function coloredSimplePanel($color,$name,$id,$pic,$content) {
		global $gfx;
		
		$cback		= darkerColor($color,0.25);
		$cbord		= darkerColor($color,0.7);
		$cdark		= darkerColor($color,0.35);
		$cfontnorm	= $color;
		$cfonthead	= $color;
	
	
	
	
		$m .=  "<div id='%IDfixed' style=\"display:block;width:100%;vertical-align:top;text-align:left;background-color:".$cback.";border:1px solid ".$cbord.";color:".$cfontnorm.";\">
			 	<table cellspacing=1 cellpadding=1\" style=\"width: 100%;height:25px;\">
					<tr>
						<th style=\"background:none;color:".$cfonthead.";\"><img src=\"%PIC\" title=\"%NAME\"> %NAME</th>
					</tr>
				</table>
			  </div>		
			  <div id='%ID' style=\"display:block;margin-left:16px;width:calc(100% - 16px);background-color: #000000;border:1px solid ".$cdark.";color:".$cfontnorm.";\">";
			  
			  
		if (is_array($content)) {
			$m .= "<table cellspacing=1 cellpadding=1 style=\"width: 100%;\">";
	
			foreach($content as $cval) {
				$m .= $cval;
			}
		
			$m .= "</table>";
		} else {
			$m .= $content;
		}
		$m .= "</div>";
		
		$m = str_replace("%NAME", $name, $m);
		$m = str_replace("%ID", $id, $m);
		$m = str_replace("%PIC", $pic, $m);
		
		return $m;
	}
	
	function floatingPanel($subtype,$name,$id,$pic,$content,$closable=0) {
		global $gfx;
		// $subtype = 1;
		$m =  "<div id='%IDfixed' draggable=\"true\" class='headerbox menuColor".$subtype."' style=\"position: absolute;z-index:5;\" onMouseOver=\"switch_drag_on();\" onMouseOut=\"switch_drag_off();\">
			 	<table cellspacing=1 cellpadding=1\" style=\"width: 100%;height:25px;\">
					<tr>
						<th><img src=\"%PIC\" title=\"%NAME\"> %NAME</th>";
						if ($closable) $m .= "<th class=\"closemarker\"><a href=\"javascript:void(0);\" onclick=\"hideContent();\"></a> </th>";
					$m .= "</tr>
				</table>
			  </div>		
				<div id='%IDfixed' class='headerbox menuColor".$subtype." popshadow' style=\"position: relative;z-index:-5;\">
			 	<table cellspacing=1 cellpadding=1\">
					<tr>
						<th><img src=\"%PIC\" title=\"%NAME\"> %NAME</th>
					</tr>
				</table>
			  </div>			  
			  <div id='%ID' class='contentbox menuColor".$subtype." popshadow' style=\"position: relative;z-index:4;\">";
			  
			  
		if (is_array($content)) {
			$m .= "<table cellspacing=1 cellpadding=1 style=\"width: 100%;\">";
	
			foreach($content as $cval) {
				$m .= $cval;
			}
		
			$m .= "</table>";
		} else {
			$m .= $content;
		}
		$m .= "</div>";
		
		$m = str_replace("%NAME", $name, $m);
		$m = str_replace("%ID", $id, $m);
		$m = str_replace("%PIC", $pic, $m);
		
		return $m;
	}
	
	function dropDownMenu($subtype,$name,$id,$pic,$content,$open=0,$togglecall=0) {
		global $gfx;
		// $subtype = 1;
		$m =  "<div id='%IDtoggle' onClick=\"toggle_visibility('%ID');toggle_highlighted(this);%TOGGLE\" class='headerbox %ACTIVATION menuColor".$subtype."' style='cursor: pointer;'>
			 	<table cellspacing=1 cellpadding=1 style=\"width: 100%;height:25px;\">
					<tr>
						<th style=\"vertical-align:center;\"><img src=\"%PIC\"> %NAME</th>
						<th class=\"statemarker\"></th>
					</tr>
				</table>
			  </div>		
			  <div id='%ID' class='contentbox menuColor".$subtype."' style='display: %DISPLAY;'>
				<table bgcolor=#262323 cellspacing=1 cellpadding=1 style=\"width: 100%;\">";
	
		foreach($content as $cval) {
			$m .= $cval;
		}
		
		$m .= "</table></div>";
		
		$m = str_replace("%NAME", $name, $m);
		$m = str_replace("%ID", $id, $m);
		$m = str_replace("%PIC", $pic, $m);
		
		if ($open == 0) {
			$m = str_replace("%DISPLAY", "none", $m);
			$m = str_replace("%DISNUM", "2", $m);
			$m = str_replace("%ACTIVATION", "closed", $m);
		} else {
			$m = str_replace("%DISPLAY", "block", $m);			
			$m = str_replace("%DISNUM", "1", $m);	
			$m = str_replace("%ACTIVATION", "", $m);			
		}
		
		if ($togglecall) {
			$m = str_replace("%TOGGLE", $togglecall, $m);
		} else {
			$m = str_replace("%TOGGLE", "", $m);				
		}
		return $m;
	}

	function dropDownMenuOption($left,$right=0,$params=0,$leftwidth="50%",$rightwidth="50%") {
		global $gfx;
		
		if ($params) {
			$formheader = "<form action=main.php>";
			foreach ($params as $k => $v) {
				$formheader .= "<input type=hidden name=\"".$k."\" value=\"".$v."\">";
			}
		}
		$m = $formheader."<tr>";
		if ($right) {
			$m .= "<td style=\"width:".$leftwidth."\">".$left."</td>";
			$m .= "<td style=\"width:".$rightwidth."\">".$right."</td>";
		} else {
			$m .= "<td style=\"width:100%\" colspan=2>".$left."</td>";
		}
		$m .= "</tr>";
		
		if ($params) $m .= "</form>";
		return $m;
	}


	

	
	
	
	
	
	function beamingPageInfoDisplay($entity,$isSource,$isOwned) {
		global $gfx;
		
		
		$displaydata = "<table width=100% border=0>";

		
		if ($entity['type'] == "planet")
			$displaydata .= "<tr><td colspan=2 style=\"text-align:center;vertical-align:middle;height:70px;\"><img src='".$gfx."/planets/".$entity['class'].".gif'></td></tr>";
		else
			$displaydata .= "<tr><td colspan=2 style=\"text-align:center;vertical-align:middle;height:70px;\"><img src='".$gfx."/ships/".$entity['class'].".gif'></td></tr>";
		
		
		
		$displaydata .= "<tr><td width=120 style=\"padding-left: 2px;\"><img src='".$gfx."/buttons/icon/energy.gif'> Energie</td><td style=\"text-align:center;padding:4px;\">".darkuniversalstatusbar($entity['eps'],$entity['max_eps'],"yel",0,200)."<br>".$entity['eps']."/".$entity['max_eps']."</td></tr>";
		
		if ($entity['type'] == "planet")
			$displaydata .= "<tr><td width=120 style=\"padding-left: 2px;\"><img src='".$gfx."/buttons/icon/storage.gif'> Lagerplatz</td><td style=\"text-align:center;padding:4px;\">".darkuniversalstatusbar($entity['storage'],$entity['max_storage'],"whi",0,200)."<br>".$entity['storage']."/".$entity['max_storage']."</td></tr>";
		else
			$displaydata .= "<tr><td width=120 style=\"padding-left: 2px;\"><img src='".$gfx."/buttons/icon/storage.gif'> Frachtraum</td><td style=\"text-align:center;padding:4px;\">".darkuniversalstatusbar($entity['storage'],$entity['max_storage'],"whi",0,200)."<br>".$entity['storage']."/".$entity['max_storage']."</td></tr>";

		if ($entity['type'] == "planet")
			$displaydata .= "<tr><td width=120 style=\"padding-left: 2px;\"><img src='".$gfx."/buttons/icon/crew".$_SESSION['race'].".gif'> Freie Arbeiter</td><td style=\"text-align:center;padding:4px;\">".darkuniversalstatusbar($entity['crew'],$entity['max_crew'],"gre",0,200)."<br>".$entity['crew']."/".$entity['max_crew']."</td></tr>";
		else
			$displaydata .= "<tr><td width=120 style=\"padding-left: 2px;\"><img src='".$gfx."/buttons/icon/crew".$_SESSION['race'].".gif'> Crew</td><td style=\"text-align:center;padding:4px;\">".darkuniversalstatusbar($entity['crew'],$entity['max_crew'],"gre",0,200)."<br>".$entity['crew']."/".$entity['max_crew']."</td></tr>";
		
		
		$displaydata .= "<tr><td width=120 style=\"padding-left: 2px;\"><img src='".$gfx."/buttons/icon/beam.gif'> Kapazität</td><td style=\"text-align:center;padding:4px;\">".$entity['beam_crew']." Crew, ".$entity['beam_good']." Waren</td></tr>";
		
		$displaydata .= "</table>";
		
		
		if ($isSource)	return fixedPanel(2,$entity['name'],"entity".$isSource,$gfx."/buttons/icon/".$entity['type'].".gif",$displaydata);
		else			return fixedPanel(4,$entity['name'],"entity".$isSource,$gfx."/buttons/icon/".$entity['type'].".gif",$displaydata);
	}
	
	
	
	function beamingPageGoodsDisplay($entity,$isSource,$isOwned) {
		global $gfx;
		
		
		$displaydata = "<table width=100% border=0>";

		$displaydata .= "<tr>";
		
		$i = 0;
		if ($isOwned) {
			$displaydata .= "<tr>";
			if ($isSource)
				$displaydata .= "<td style=\"padding:2px;height:30px;text-align:left;width:33%;\"><img src='".$gfx."/icons/crew".$_SESSION['race'].".gif' style=\"vertical-align:middle;\"> <input name=\"cb\" size=\"4\" type=\"text\" style=\"text-align:right;\"> / ".$entity['crew']."</td>";
			else
				$displaydata .= "<td style=\"padding:2px;height:30px;text-align:left;width:33%;\"><img src='".$gfx."/icons/crew".$_SESSION['race'].".gif' style=\"vertical-align:middle;\"> ".$entity['crew']."</td>";
			$i++;
		}
		
		
		foreach($entity['goods'] as $k => $v) {
			
			if ($i % 3 == 0)
				$displaydata .= "<tr>";
			
			$i++;
			
			if ($isSource)
				$displaydata .= "<td style=\"padding:2px;height:30px;text-align:left;width:33%;\">".goodPic($k)." <input name=\"good[]\" value=\"".$k."\" type=\"hidden\"/><input name=\"value[]\" size=\"4\" type=\"text\" style=\"text-align:right;\"> / ".$v."</td>";
			else 
				$displaydata .= "<td style=\"padding:2px;height:30px;text-align:left;width:33%;\">".goodPic($k)." ".$v."</td>";
			
			
			if ($i % 3 == 0)
				$displaydata .= "</tr>";
		}
		if ($i % 3 == 1) {
			$displaydata .= "<td colspan=2 style=\"padding:0px;width:66%;\"></td></tr>";
		}
		if ($i % 3 == 2) {
			$displaydata .= "<td colspan=1 style=\"padding:0px;width:33%;\"></td></tr>";
		}		
		
		
		$displaydata .= "</table>";
		
		
		if ($entity['type'] == "planet")	return fixedPanel(2,"Lager","goods".$isSource,$gfx."/buttons/icon/storage.gif",$displaydata);
		else								return fixedPanel(4,"Frachtraum","goods".$isSource,$gfx."/buttons/icon/storage.gif",$displaydata);
	}
	
	
	
	function beamingPage($source,$target,$control) {
		
		global $gfx;
		
		
		
		$controldiv = "<div style=\"width:100%;text-align:center;padding:10px;\">";
		
		if ($control['mode'] == "to") {
			$controldiv .= "<a href='".$control['directionlink']."' ".getHover("alt","active/n/dir_r","hover/w/dir_l")."><img name=alt src=".$gfx."/buttons/active/n/dir_r.gif border=0 title=\"Richtung ändern\" ><br>Richtung ändern</a>";
		}
		if ($control['mode'] == "from") {
			$controldiv .= "<a href='".$control['directionlink']."' ".getHover("alt","active/n/dir_l","hover/w/dir_r")."><img name=alt src=".$gfx."/buttons/active/n/dir_l.gif border=0 title=\"Richtung ändern\" ><br>Richtung ändern</a>";
		}
		
		$controldiv .= "<br><br><input type=submit class=button value=\"Beamen\" style=\"width:80px;height:30px;\">";
		
		$controldiv .= "</div>";
		
		
		if ($control['mode'] == "to") {
			echo "<table class=tablelayout>";
			
			echo "<tr>";
			echo "<td class=tablelayout style=\"width:500px; vertical-align:top;\">";
			echo beamingPageInfoDisplay($source,true,true);	
			echo "</td>";
			echo "<td rowspan=2 class=tablelayout style=\"width:200px; vertical-align:top; padding-top: 100px;\">";
			echo fixedPanel(3,"Beamen","slist",$gfx."/buttons/icon/beam.gif",$controldiv);	
			echo "</td>";
			echo "<td class=tablelayout style=\"width:500px; vertical-align:top;\">";
			echo beamingPageInfoDisplay($target,false,$source['owner'] == $target['owner']);		
			echo "</td>";		
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class=tablelayout style=\"width:500px; vertical-align:top;\">";
			echo beamingPageGoodsDisplay($source,true,true);	
			echo "</td>";
			echo "<td class=tablelayout style=\"width:500px; vertical-align:top;\">";
			echo beamingPageGoodsDisplay($target,false,$source['owner'] == $target['owner']);		
			echo "</td>";		
			echo "</tr>";
			
			echo "</table>";
		}
		
		if ($control['mode'] == "from") {
			echo "<table class=tablelayout>";
			
			echo "<tr>";
			echo "<td class=tablelayout style=\"width:500px; vertical-align:top;\">";
			echo beamingPageInfoDisplay($source,true,true);	
			echo "</td>";
			echo "<td rowspan=2 class=tablelayout style=\"width:200px; vertical-align:top; padding-top: 100px;\">";
			echo fixedPanel(3,"Beamen","slist",$gfx."/buttons/icon/beam.gif",$controldiv);	
			echo "</td>";
			echo "<td class=tablelayout style=\"width:500px; vertical-align:top;\">";
			echo beamingPageInfoDisplay($target,false,$source['owner'] == $target['owner']);		
			echo "</td>";		
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class=tablelayout style=\"width:500px; vertical-align:top;\">";
			echo beamingPageGoodsDisplay($source,false,true);	
			echo "</td>";
			echo "<td class=tablelayout style=\"width:500px; vertical-align:top;\">";
			echo beamingPageGoodsDisplay($target,true,$source['owner'] == $target['owner']);		
			echo "</td>";		
			echo "</tr>";
			
			echo "</table>";
		}

	}
	
		// $source['class']		= "203";
	// $source['name']         = "Kolonie";
	// $source['owner']		=  102;
	// $source['storage']		=  500;
	// $source['max_storage']  = 2000;
	// $source['crew']			= 180;
	// $source['max_crew']     = 200;
	// $source['eps'] 			=  50;
	// $source['max_eps'] 		= 100;
	// $source['beam_good']	= 100;
	// $source['beam_crew'] 	= 100;
	
	function getColonyBeamInfo($id) {
		global $db;
		
		$res = $db->query("SELECT name, user_id as owner, colonies_classes_id as class, max_storage, bev_free as crew, bev_max as max_crew, eps, max_eps FROM stu_colonies WHERE id = ".$id." LIMIT 1;",4);
		$res['type'] = "planet";
		$res['goods'] = getColonyStorage($id);
		$res['beam_good'] = 30;
		$res['beam_crew'] = 5;
		foreach($res['goods'] as $k => $v) {
			$res['storage'] += $v;
		}
		return $res;
	}
	function getShipBeamInfo($id) {
		global $db;
		
		$res = $db->query("SELECT name, user_id as owner, rumps_id as class, storage as max_storage, crew, max_crew, eps, max_eps FROM stu_ships WHERE id = ".$id." LIMIT 1;",4);
		$res['type'] = "ship";
		$res['goods'] = getShipStorage($id);
		$res['beam_good'] = "[ToDo]";
		$res['beam_crew'] = "[ToDo]";
		foreach($res['goods'] as $k => $v) {
			$res['storage'] += $v;
		}
		return $res;
	}
	
	
	
	function getColonyStorage($id) {
		global $db;
		
		$storage = array();
		$res = $db->query("SELECT * FROM stu_colonies_storage WHERE colonies_id = ".$id." ORDER BY goods_id ASC;");
		while ($data = mysql_fetch_assoc($res)) {
			$storage[$data['goods_id']] = $data['count'];
		}
		return $storage;
	}
	function getShipStorage($id) {
		global $db;
		
		$storage = array();
		$res = $db->query("SELECT * FROM stu_ships_storage WHERE ships_id = ".$id." ORDER BY goods_id ASC;");
		while ($data = mysql_fetch_assoc($res)) {
			$storage[$data['goods_id']] = $data['count'];
		}
		return $storage;
	}
	
	
	
	
	
	
	
	function displayCriterion($arr) {
		global $gfx;
		// if (!$arr['picresize']) return "<img src=".$gfx.$arr['pic']." title=\"".$arr['str']."\">".($arr['done'] ? "<font color='#66ff66'>" : "<font color='#ff6666'>")."".$arr['cnt']."/".$arr['req']."</font> ";
		return "<tr><td style=\"width:30px;padding:4px;\"><img src=".$gfx.$arr['pic']." title=\"".$arr['str']."\" width=30px height=30px></td><td style=\"width:100px;text-align:center;\">".($arr['done'] ? "<font color='#66ff66'>" : "<font color='#ff6666'>")."".$arr['cnt']." / ".$arr['req']."</font></td><td>&nbsp;".$arr['str']."</td> ";
	}

	function checkLevelCriterion($level,$criterionid,$user) {
	
		global $db;
		$ret = array();
		$ret['pic'] = "";
		$ret['done'] = false;
		$ret['cnt'] = 1;
		$ret['req'] = 1;
		$ret['str'] = "";
		$ret['picresize'] = true;
		
		$race = $db->query("SELECT race FROM stu_user WHERE id = ".$user."",1);

		switch($level.$criterionid) {
			
			case "01" : $qry = "SELECT * FROM stu_colonies WHERE colonies_classes_id < 300 AND user_id = ".$user."";
						$ret['str'] = "Besiedelte Kolonie";
						$ret['pic'] = "/planets/201s.gif";
						$ret['req'] = 1;
						$ret['cnt'] = $db->query($qry,3);
						break;	
			case "11" :
						$qry = "SELECT a.* FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b on a.colonies_id = b.id WHERE a.buildings_id = 2 AND b.user_id = ".$user." AND a.aktiv < 2";
						$ret['str'] = "Gebaute Farmen";
						$ret['pic'] = "/buildings/2/0.png";
						$ret['req'] = 2;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "12" :
						$qry = "SELECT a.* FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b on a.colonies_id = b.id WHERE a.buildings_id = 5 AND b.user_id = ".$user." AND a.aktiv < 2";
						$ret['str'] = "Gebaute Solarzellen";
						$ret['pic'] = "/buildings/5/0.png";
						$ret['req'] = 4;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "13" :
						$qry = "SELECT a.* FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b on a.colonies_id = b.id WHERE a.buildings_id = 3 AND b.user_id = ".$user." AND a.aktiv < 2";
						$ret['str'] = "Gebaute Baracken";
						$ret['pic'] = "/buildings/3/0.png";
						$ret['req'] = 2;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "14" :
						$qry = "SELECT a.* FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b on a.colonies_id = b.id WHERE a.buildings_id = 6 AND b.user_id = ".$user." AND a.aktiv < 2";
						$ret['str'] = "Gebaute Baumaterialfabriken";
						$ret['pic'] = "/buildings/6/0.png";
						$ret['req'] = 2;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "21" :
						$qry = "SELECT a.* FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b on a.colonies_id = b.id WHERE a.buildings_id = 14 AND b.user_id = ".$user." AND a.aktiv < 2";
						$ret['str'] = "Gebaute Deuterium-Extraktoren";
						$ret['pic'] = "/buildings/14/0.png";
						$ret['req'] = 2;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "22" :
						$qry = "SELECT a.* FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b on a.colonies_id = b.id WHERE a.buildings_id = 16 AND b.user_id = ".$user." AND a.aktiv < 2";
						$ret['str'] = "Gebaute Leichte Fusionsreaktoren";
						$ret['pic'] = "/buildings/16/0.png";
						$ret['req'] = 1;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "31" :
						$qry = "SELECT a.* FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b on a.colonies_id = b.id WHERE a.buildings_id = 15 AND b.user_id = ".$user." AND a.aktiv < 2";
						$ret['str'] = "Gebaute Minen";
						$ret['pic'] = "/buildings/15/0.png";
						$ret['req'] = 3;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "32" :
						$qry = "SELECT a.* FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b on a.colonies_id = b.id WHERE a.buildings_id = 7 AND b.user_id = ".$user." AND a.aktiv < 2";
						$ret['str'] = "Gebaute Duraniumanlagen";
						$ret['pic'] = "/buildings/7/0.png";
						$ret['req'] = 1;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "33" :
						$qry = "SELECT * FROM stu_colonies WHERE colonies_classes_id > 300 AND user_id = ".$user."";
						$ret['str'] = "Besiedelter Mond";
						$ret['pic'] = "/planets/301s.gif";
						$ret['req'] = 1;
						$ret['cnt'] = $db->query($qry,3);
						break;		
			case "41" :
						$qry = "SELECT a.* FROM stu_ships as a LEFT JOIN stu_ships_buildplans as b on a.plans_id = b.plans_id WHERE b.plans_id > 123 AND a.user_id = ".$user."";
						$ret['str'] = "Gebaute Schiffe";
						$ret['pic'] = "/buildings/51/0.png";
						$ret['req'] = 1;
						$ret['cnt'] = $db->query($qry,3);
						break;
			case "42" :
						$qry = "SELECT SUM(bev_work+bev_free) as bevs FROM stu_colonies WHERE user_id = ".$user."";
						$ret['str'] = "Einwohner insgesamt";
						$ret['pic'] = "/icons/crew".$race.".gif";
						$ret['picresize'] = false;
						$ret['req'] = 1000;
						$ret['cnt'] = $db->query($qry,1);
						break;	
			case "43" :
						$qry = "SELECT (bev_work) as bevs FROM stu_colonies WHERE (colonies_classes_id = 204 OR  colonies_classes_id = 205  OR  colonies_classes_id = 206 or colonies_classes_id = 304 OR  colonies_classes_id = 305 OR  colonies_classes_id = 306) AND user_id = ".$user." ORDER BY bev_work desc LIMIT 1";
						$ret['str'] = "Arbeiter auf größter Wüsten-, Tundra- oder Ödland-Kolonie";
						$ret['pic'] = "/planets/206s.gif";						
						$ret['req'] = 200;
						$ret['cnt'] = $db->query($qry,1);
						break;
			// case "51" :
						// $ret['str'] = "Unschöner Blocker";
						// $ret['pic'] = "/systems/981.png";
						// $ret['req'] = 1;
						// $ret['cnt'] = 0;
						// break;						
			default: return 0;
		}
		
		if ($ret['cnt'] >= $ret['req']) $ret['done'] = true;		
		return $ret;
		
	}



	
function shipPic($id,$isCloaked,$isTrumfield,$dmgPercent) {
	
	if ($isCloaked==1) return "cloak/".$id;
	return "".$id;
}
	
	
function vdam(&$arr)
{
	if (is_object($arr))
	{
		if ($arr->trumfield == 1) return "t/";
	}
	else
	{
		if ($arr['trumfield'] == 1) return "t/";
	}
	return "";
	if (is_object($arr))
	{
		if ($arr->trumfield == 1) return "t/";
		round((100/$arr->max_huelle)*$arr->huelle) < 40 ? $d = "d/" : $d = "";
	}
	else
	{
		if ($arr['trumfield'] == 1) return "t/";
		round((100/$arr['max_huelle'])*$arr['huelle']) < 40 ? $d = "d/" : $d = "";
	}
	return $d;
}
function vtrak($shipId)
{
	global $db,$gfx;
	$result = $db->query("SELECT rumps_id,name,huelle,max_huelle FROM stu_ships WHERE id=".$shipId." LIMIT 1",4);
	return "<img src=".$gfx."/ships/".vdam($result).$result['rumps_id'].".gif> ".stripslashes($result['name']);
}
function checksector($tar)
{
	global $ship;
	if ($tar['cloak'] == 1) return 0;
	if ($ship->systems_id > 0 || $tar['systems_id'] > 0)
	{
		if ($ship->systems_id != $tar['systems_id']) return 0;
		if ($ship->sx != $tar['sx'] || $ship->sy != $tar['sy']) return 0;
		return 1;
	}
	if ($ship->cx != $tar['cx'] || $ship->cy != $tar['cy']) return 0;
	return 1;
}
function checkcolsector(&$tar)
{
	global $col;
	if ($tar['cloak'] == 1) return 0;
	if ($col->systems_id > 0 || $tar['systems_id'] > 0)
	{
		if ($col->systems_id != $tar['systems_id']) return 0;
		if ($col->sx != $tar['sx'] || $col->sy != $tar['sy']) return 0;
		return 1;
	}
	if ($col->cx != $tar['cx'] || $col->cy != $tar['cy']) return 0;
	return 1;
}
function checkcolsectors(&$tar)
{
	global $col;
	if ($col->systems_id > 0 || $tar['systems_id'] > 0)
	{
		if ($col->systems_id != $tar['systems_id']) return 0;
		if ($col->sx != $tar['sx'] || $col->sy != $tar['sy']) return 0;
		return 1;
	}
	return 1;
}
function ftit($txt) { return str_replace("'","",strip_tags(stripslashes($txt))); }
function get_dn_state($i,$chg,$mode,$fid,$is_moon,$class)
{
	// if ($class == 9 || $class == 29) return;
	// if (!$is_moon && ($fid < 19 || $fid > 72)) return "";
	// if ($is_moon && $fid < 15) return "";
	// if ($class == 10) return "n";
	// if ($mode == 1) { $retak = ""; $retnu = "n"; }
	// if ($mode == 2) { $retak = "n"; $retnu = ""; }
	// if ($i == 1 && $chg < 3600) return $retnu;
	// if ($i == 2 && $chg < 3200) return $retnu;
	// if ($i == 3 && $chg < 2800) return $retnu;
	// if ($i == 4 && $chg < 2400) return $retnu;
	// if ($i == 5 && $chg < 2000) return $retnu;
	// if ($i == 6 && $chg < 1600) return $retnu;
	// if ($i == 7 && $chg < 1200) return $retnu;
	// if ($i == 8 && $chg < 800) return $retnu;
	// if ($i == 9 && $chg < 400) return $retnu;
	// return $retak;
	return "";
}
function gen_time($time)
{
	if ($time <= 0) return "0m";
	if ($time > 86400)
	{
		$day = floor($time/86400);
		$time -= $day*86400;
		$t = $day."t ";
	}
	$sek = $time - (floor($time/60)*60);
	$min = floor($time/60) - (floor(floor($time/60)/60)*60);
	$hour = floor(floor($time/60)/60);
	return $t.$hour."h ".$min."m ".$sek."s";
}
function setyear($year) { return $year+374; }
function check_int(&$var)
{
	if ($var == "") return FALSE;
	if (preg_match("/[^0-9]/",$var)) return FALSE;
	if ($var < 0) return FALSE;
	return TRUE;
}
function check_html_tags($string)
{
	// Font
	if (substr_count($string,"<font") != substr_count($string,"</font>")) return FALSE;
	// Bold
	if (substr_count($string,"<b>") != substr_count($string,"</b>")) return FALSE;
	// Italic
	if (substr_count($string,"<i>") != substr_count($string,"</i>")) return FALSE;
	return TRUE;
}
function getnamebyfield($id)
{
		
	switch($id) {
			case  1: return 'Wiese';
			case  2: return 'Wald';
			case  3: return 'Nadelwald';
			case 46: return 'Dschungel';
			case  5: return 'See';
			// case  4: return 'Seichtes Wasser';
			case 40: return 'Ozean';
			case 41: return 'Schelfmeer';
			case 42: return 'Korallenriff';
			case 44: return 'Abgetragenes Korallenriff';
			case 45: return 'Aufgeschüttetes Land';
			case 20: return 'Eisdecke';
			case  7: return 'Wüste';
			case  8: return 'Wüste';
			case 48: return 'Sanddünen';
			case 49: return 'Sanddünen';
			case 50: return 'Sanddünen';
			case  6: return 'Eis';
			case 14: return 'Eisformationen';
			case 16: return 'Eiswasser';
			case  9: return 'Ödland';
			case 18: return 'Tundra';
			case 15: return 'Felsformationen';
			case 47: return 'Felsspalten';
			case 31:
			case 32:
			case 33:
			case 34:
			case 36:
			case 35: return 'Berge';
			case 19: return 'Fels';
			case 10: return 'Fels';
			case 12: return 'Krater';

			case 79: return 'Fels';
			case 70: return 'Untergrund';
			case 71: return 'Untergrund';
			case 72: return 'Untergrund';
			case 73: return 'Untergrund';
			case 74: return 'Untergrund';

			
			case 81: return 'Untergrund-Fels';
			case 82: return 'Erzader';
			case 83: return 'Magmaeinschluss';
			case 84: return 'Untergrund-Wasser';
			
			
			case 100: return 'Weltraum';
			case 110:
			case 111:
			case 112:
			case 113:
			case 114:
			case 115:
			case 116:
			case 117:
			case 118:
			case 119: return 'Weltraum';
			
			
			case 200: return 'Lavagestein';
			case 201: return 'Berge';
			case 202: return 'Erloschener Vulkan';
			case 211:
			case 212:
			case 213:
			case 214:
			case 215:
			case 216:
			case 217: return 'Aktiver Vulkan';
			case 205: return 'Versiegelter Lavastrom';
			case 203: return 'Abgetragener Vulkan';
			case 204: return 'Kontrollierter Vulkan';
			
			case 223: 
			case 225: 
			case 226: 
			case 229: 
			case 230: 
			case 232: 
			case 241: 
			case 242: 
			case 243: 
			case 244: return 'Lavastrom';
			case 245: 
			case 246: 
			case 247: 
			case 248: return 'Lavasee';
			case  11: return 'Ebene';
			case  12:
			case  13: return 'Krater';
			case  17: return 'Aktiver Vulkan';
			case  28: return 'Erloschener Vulkan';
			
			case 1000: return 'Unfertig';
			default: return '???';
		
	}
	
}
function getsnamebyfield($id)
{
	switch ($id)
	{
		default:
			return "Fehler, unbekannter Feldtyp";
		case 1:
			return "Klasse 1 Equipment-Slot";
		case 2:
			return "Klasse 2 Equipment-Slot";
		case 3:
			return "Klasse 3 Equipment-Slot";
		case 4:
			return "Energiesystem-Slot";
		case 5:
			return "Defensivsystem-Slot";
		case 6:
			return "Lagerraum-Slot";
		case 9:
			return "Automatisierter Sensorenphalanx-Slot";
		case 11:
			return "Epsilon-Klasse";
		case 21:
			return "Antares-Klasse";

		case 12:
			return "Vodius-Klasse";
		case 22:
			return "T'Mera-Klasse";

		case 13:
			return "Jolpa'Law-Klasse";
		case 23:
			return "Tong'Duj-Klasse";

		case 14:
			return "Miral-Klasse";
		case 24:
			return "Groumal-Klasse";

		case 15:
			return "Tomax-Klasse";
		case 25:
			return "Bestan-Klasse";

		case 16:
			return "Python-Klasse";
		case 26:
			return "Turtle-Klasse";
	}
}

function getHover($name,$from,$to) { return "onmouseover=cp('".$name."','buttons/".$to."') onmouseout=cp('".$name."','buttons/".$from."')"; }



function getonm($name,$pic) { return "onmouseover=cp('".$name."','".$pic."2') onmouseout=cp('".$name."','".$pic."1')"; }

function getronm($name,$pic) { return "onmouseover=cp('".$name."','".$pic."1') onmouseout=cp('".$name."','".$pic."2')"; }

function getbuildinghelp($id,$fid) { return "(<a href=\"javascript:void(0);\" onClick=\"getbinfo(".$fid.",".$id.");\">?</a>)"; }

function shipexception($iarr,$c_var)
{
	if (is_object($c_var)) foreach($c_var as $key => $value) $cd[$key] = $value;
	else $cd = $c_var;
	$return[code] = 0;
	foreach($iarr as $key => $value)
	{
		switch ($key)
		{
			case "nbs":
				if ($value != $cd['nbs']) return array("code" => 1,"msg" => "Die Nahbereichssensoren sind nicht aktiviert");
				break;
			case "eps":
				if ($value == -1 && $cd['eps'] == 0) return array("code" => 1,"msg" => "Es wird mindestens 1 Energie benötigt");
				if ($value > $cd['eps']) return array("code" => 1,"msg" => "Es wird ".$value." Energie benötigt");
				break;
			case "cloak":
				if (($value == 0 && $cd['cloak'] == 1) || ($value == 1 && ($cd['cloak'] == 0 || $cd['cloak'] > 1))) return array("code" => 1,"msg" => "Die Tarnung ist aktiviert");
				break;
			case "phaser":
				if ($cd['phaser'] == 0) return array("code" => 1,"msg" => "Es ist kein Waffenmodul auf diesem Schiff installiert");
				break;
			case "slots":
				if ($cd['slots'] > 0) return array("code" => 1,"msg" => "Eine Station kann nicht bewegt werden");
				break;
			case "traktor":
				if ($cd['traktormode'] == 1) return array("code" => 1,"msg" => "Der Traktorstrahl ist aktiviert");
				if ($cd['traktormode'] == 2) return array("code" => 1,"msg" => "Das Schiff wird von einem Traktorstrahl gehalten");
				break;
			case "warp":
				if ($value != $cd['warpable']) return array("code" => 1,"msg" => "Für diese Aktion wird Warpantrieb benötigt");
				break;
			case "system":
				if ($cd['systems_id'] == 0) return array("code" => 1,"msg" => "Das Schiff befindet sich in keinem System");
				break;
			case "schilde_status":
				if ($cd['schilde_status'] == 1) return array("code" => 1,"msg" => "Die Schilde sind aktiviert");
				break;
			case "schilde_load":
				if ($cd['schilde'] >= $value) return array("code" => 1,"msg" => "Die Schilde sind bereits vollständig geladen");
				break;
			case "schilde":
				if ($cd['schilde'] == 0) return array("code" => 1,"msg" => "Die Schilde sind nicht aufgeladen");
				break;
			case "crew":
				if ($cd['crew'] < $value) return array("code" => 1,"msg" => "Es werden ".$value." Crewmitglieder benötigt - vorhanden sind nur ".$cd[crew]);
				break;
			case "torp_type":
				if ($value == -1 && $cd['torp_type'] == 0) return array("code" => 1,"msg" => "Das Schiff hat keine Torpedos geladen");
				break;
			case "warpstate":
				if ($cd['warp'] == 0 && $value == 1) return array("code" => 1,"msg" => "Das Schiff muss sich im Warp befinden");
				if ($cd['warp'] == 1 && $value == 0) return array("code" => 1,"msg" => "Das Schiff muss sich außerhalb des Warp befinden");
				break;
			case "umode":
				if ($value == 0 && $cd['vac'] == 1) return array("code" => 1,"msg" => "Der Siedler befindet sich zur Zeit im Urlaubsmodus");
				break;
			case "dock":
				if ($cd['dock'] != $value)  return array("code" => 1,"msg" => "Das Schiff ist angedockt");
				break;
		}
	}
	return $return;
}

function getroundtime($rtg)
{
	global $db,$_SESSION;
	$lr = $db->query("SELECT HOUR(start) FROM stu_game_rounds ORDER BY start DESC LIMIT 1",1);
	if ($rtg >= 5) $rd = time()+(floor($rtg/5)*86400);
	else $rd = time();
	$rtg -= floor($rtg/5)*5;
	//echo "\t=> ".$lr." - ".$rtg." - ".date("d.m.Y H:i",$rd)."<br>";
	if ($rtg > 0)
	{
		switch ($lr)
		{
			case 12:
				$rd += $rtg*10800;
				break;
			case 15:
				if ($rtg < 4) $rd += $rtg*10800;
				else $rd += ($rtg-1)*10800+43200;
				break;
			case 18:
				if ($rtg < 3) $rd += $rtg*10800;
				else $rd += ($rtg-1)*10800+43200;
				break;
			case 21:
				if ($rtg < 2) $rd += $rtg*10800;
				else $rd += ($rtg-1)*10800+43200;
				break;
			case 0:
				$rd += ($rtg-1)*10800+((12-date("G"))*3600);
				break;
		}
	}
	//echo "\t=> ".date("d.m.Y H:i",$rd)."<br>";
	return getrounddate($rd);
}
function getrounddate($date)
{
	$hour = date("H",$date);
	if ($hour >= 0 && $hour < 12) return date("d.m",$date)." 0 Uhr";
	if ($hour >= 12 && $hour < 15) return date("d.m",$date)." 12 Uhr";
	if ($hour >= 15 && $hour < 18) return date("d.m",$date)." 15 Uhr";
	if ($hour >= 18 && $hour < 21) return date("d.m",$date)." 18 Uhr";
	if ($hour >= 21) return date("d.m",$date+4000)." 21 Uhr";
}
function getnextlevel()
{
	global $_SESSION;
	switch ($_SESSION[level])
	{
		case 0: return;
		case 1: return;
		case 2:
			return array("lvl" => 3,"wp" => 4.5,"ship" => 0,"cols" => 0,"moon" => 0);
		case 3:
			return array("lvl" => 4,"wp" => 15,"ship" => 0,"cols" => 1,"moon" => 1);
		case 4:
			return array("lvl" => 5,"wp" => 0,"ship" => 2,"work" => 75);
		case 5:
			return array("lvl" => 6,"wp" => 50,"ship" => 0,"cols" => 1,"moon" => 1,"fo" => 10);
	}
}
function gethpbyfaction($faction)
{
	switch($faction)
	{
		case 3:
			return "a.id=8 OR a.id=9 OR a.id=10";
		case 1:
			return "a.id=11 OR a.id=12 OR a.id=13";
		case 2:
			return "a.id=14 OR a.id=15 OR a.id=16";
		case 4:
			return "a.id=17 OR a.id=18 OR a.id=19";
		case 5:
			return "a.id=20 OR a.id=21 OR a.id=22";
		case 6:
			return "a.id=23 OR a.id=24 OR a.id=25";
	}
}




function getcolonizefield($class_id)
{
	switch($class_id)
	{
		case 204: return 9;
		case 304: return 9;
		case 205: return 18;
		case 305: return 18;
		case 206: return 19;
		case 306: return 19;
		case 209: return 6;
		case 309: return 6;
		case 210: return 10;
		case 310: return 10;
		case 207: return 200;
		case 307: return 200;
		default:
			return 1;
	}
}

function getmodtypedescr($type)
{
	switch ($type)
	{
		case 1:
			return "Hülle";
		case 2:
			return "Schilde";
		case 3:
			return "Warpkern";
		case 4:
			return "Antrieb";
		case 5:
			return "Sensoren";
		case 6:
			return "Waffe";
		case 7:
			return "Spezialmodul";
		// case 8:
			// return "EPS-Leitungen";
		// case 9:
			// return "Spezial";
		// case 10:
			// return "Torpedorampe";
		// case 11:
			// return "Warpantrieb";
		default: return "FEHLER";
	}
}
function getrashipchance($sc)
{
	return 5;
}
function universalstatusbar($val,$maxval,$color,$change,$width)
{
	global $gfx;
	if ($val > $maxval) $val = $maxval;
	if ($change+$val > $maxval) $change = $maxval-$val;
	if (($change < 0) && ($change * (-1) > $val)) $change = $val;
	if ($maxval != 0) $frac_atm = round(100 * $val/$maxval);
	else $frac_atm = 0;
	if ($maxval != 0) $frac_chg = round(100 * $change/$maxval);
	else $frac_chg = 0;

	if ($change >= 0) $bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=6 width=".ceil($width * $frac_atm/100)." style=\"vertical-align:middle;\">";
	if ($change > 0)  $bar .= "<img src=".$gfx."/buttons/hu_gre.gif height=6 width=".floor($width*$frac_chg/100)." style=\"vertical-align:middle;\">";
	if ($change < 0) {
		$bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=6 width=".ceil($width * ($frac_atm+$frac_chg)/100)." style=\"vertical-align:middle;\">";
		$bar .= "<img src=".$gfx."/buttons/hu_red.gif height=6 width=".floor(-1*$width*$frac_chg/100)." style=\"vertical-align:middle;\">";
		$bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".floor($width*(100-($frac_atm))/100)." style=\"vertical-align:middle;\">";
	} else {
		if ($val+$change < $maxval) $bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".floor($width*(100-($frac_atm+$frac_chg))/100)." style=\"vertical-align:middle;\">";
	}
	return $bar;
}
function darkuniversalstatusbar($val,$maxval,$color,$change,$width)
{
	global $gfx;
	if ($val > $maxval) $val = $maxval;
	if ($change+$val > $maxval) $change = $maxval-$val;
	if (($change < 0) && ($change * (-1) > $val)) $change = $val;
	if ($maxval != 0) $frac_atm = round(100 * $val/$maxval);
	else $frac_atm = 0;
	if ($maxval != 0) $frac_chg = round(100 * $change/$maxval);
	else $frac_chg = 0;

	if ($change >= 0) $bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=6 width=".ceil($width * $frac_atm/100)." style=\"vertical-align:middle;\">";
	if ($change > 0)  $bar .= "<img src=".$gfx."/buttons/hu_gre.gif height=6 width=".floor($width*$frac_chg/100)." style=\"vertical-align:middle;\">";
	if ($change < 0) {
		$bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=6 width=".ceil($width * ($frac_atm+$frac_chg)/100)." style=\"vertical-align:middle;\">";
		$bar .= "<img src=".$gfx."/buttons/hu_red.gif height=6 width=".floor(-1*$width*$frac_chg/100)." style=\"vertical-align:middle;\">";
		$bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".floor($width*(100-($frac_atm))/100)." style=\"vertical-align:middle;\">";
	} else {
		if ($val+$change < $maxval) $bar .= "<img src=".$gfx."/buttons/hu_drk.gif height=6 width=".floor($width*(100-($frac_atm+$frac_chg))/100)." style=\"vertical-align:middle;\">";
	}
	return $bar;
}
function biguniversalstatusbar($val,$maxval,$color,$change,$width)
{
	global $gfx;
	if ($val > $maxval) $val = $maxval;
	if ($change+$val > $maxval) $change = $maxval-$val;
	if (($change < 0) && ($change * (-1) > $val)) $change = $val;
	if ($maxval != 0) $frac_atm = round(100 * $val/$maxval);
	else $frac_atm = 0;
	if ($maxval != 0) $frac_chg = round(100 * $change/$maxval);
	else $frac_chg = 0;

	if ($change >= 0) $bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=10 width=".ceil($width * $frac_atm/100).">";
	if ($change > 0)  $bar .= "<img src=".$gfx."/buttons/hu_gre.gif height=10 width=".floor($width*$frac_chg/100).">";
	if ($change < 0) {
		$bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=10 width=".ceil($width * ($frac_atm+$frac_chg)/100).">";
		$bar .= "<img src=".$gfx."/buttons/hu_red.gif height=10 width=".floor(-1*$width*$frac_chg/100).">";
		$bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=10 width=".floor($width*(100-($frac_atm))/100).">";
	} else {
		if ($val+$change < $maxval) $bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=10 width=".floor($width*(100-($frac_atm+$frac_chg))/100).">";
	}
	return $bar;
}
function bigdarkuniversalstatusbar($val,$maxval,$color,$change,$width)
{
	global $gfx;
	if ($val > $maxval) $val = $maxval;
	if ($change+$val > $maxval) $change = $maxval-$val;
	if (($change < 0) && ($change * (-1) > $val)) $change = $val;
	if ($maxval != 0) $frac_atm = round(100 * $val/$maxval);
	else $frac_atm = 0;
	if ($maxval != 0) $frac_chg = round(100 * $change/$maxval);
	else $frac_chg = 0;

	if ($change >= 0) $bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=10 width=".ceil($width * $frac_atm/100).">";
	if ($change > 0)  $bar .= "<img src=".$gfx."/buttons/hu_gre.gif height=10 width=".floor($width*$frac_chg/100).">";
	if ($change < 0) {
		$bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=10 width=".ceil($width * ($frac_atm+$frac_chg)/100).">";
		$bar .= "<img src=".$gfx."/buttons/hu_red.gif height=10 width=".floor(-1*$width*$frac_chg/100).">";
		$bar .= "<img src=".$gfx."/buttons/hu_drk.gif height=10 width=".floor($width*(100-($frac_atm))/100).">";
	} else {
		if ($val+$change < $maxval) $bar .= "<img src=".$gfx."/buttons/hu_drk.gif height=10 width=".floor($width*(100-($frac_atm+$frac_chg))/100).">";
	}
	return $bar;
}
function renderhuellstatusbar(&$huell,&$maxhuell)
{
	global $gfx;
	if ($huell > $maxhuell) $huell = $maxhuell;
	$pro = round((100/$maxhuell)*$huell);
	if ($pro > 60) $ad = "gre";
	if ($pro <= 60) $ad = "yel";
	if ($pro <=25) $ad = "red";
	$bar = "<img src=".$gfx."/buttons/hu_".$ad.".gif height=6 width=".ceil($pro/2).">";
	if ($pro < 100) $bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".floor((100-$pro)/2).">";
	return $bar;
}
function rendershieldstatusbar(&$active,&$shields,&$maxshields)
{
	global $gfx;
	if ($shields > $maxshields) $shields = $maxshields;
	$pro = @round((100/$maxshields)*$shields);
	$bar = "<img src=".$gfx."/buttons/sh_".($active == 1 ? "blu" : "dblu").".gif height=6 width=".ceil($pro/2).">";
	if ($pro < 100) $bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".floor((100-$pro)/2).">";
	return $bar;
}
function getpercentage($val,$maxval)
{
	if ($val > $maxval) $val = $maxval;
	return @round((100/$maxval)*$val);
}

function renderstatusbar($val,$maxval,$color)
{
	global $gfx;
	if ($val > $maxval) $val = $maxval;
	$pro = @round((100/$maxval)*$val);
	$bar = "<img src=".$gfx."/buttons/hu_".$color.".gif height=6 width=".ceil($pro/2).">";
	if ($pro < 100) $bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".floor((100-$pro)/2).">";
	return $bar;
}
function renderepsstatusbar(&$eps,&$maxeps)
{
	global $gfx;
	if ($eps > $maxeps) $eps = $maxeps;
	$pro = @round((100/$maxeps)*$eps);
	$bar = "<img src=".$gfx."/buttons/hu_yel.gif height=6 width=".ceil($pro/2).">";
	if ($pro < 100) $bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".floor((100-$pro)/2).">";
	return $bar;
}
function generatesubsystemlistbyarr($data,$shipId)
{
	global $db;
	$result = $db->query("SELECT system_id FROM stu_ships_subsystems WHERE ships_id=".$shipId);
	while($dat=mysql_fetch_assoc($result)) $not[$dat['system_id']] = 1;
	$i = 2;
	while ($i<=11)
	{
		if ($i == 3)
		{
			$i++;
			continue;
		}
		if (($i == 5 || $i == 11) && $data['warpable'] != 0)
		{
			$i++;
			continue;
		}
		if ($i == 6 && $data['phaser'] == 0)
		{
			$i++;
			continue;
		}
		if ($i == 7 && $data['slots'] > 0)
		{
			$i++;
			continue;
		}
		if ($i == 9 && $data['cloakable'] != 1)
		{
			$i++;
			continue;
		}
		if ($i == 10 && $data['m10'] == 0)
		{
			$i++;
			continue;
		}
		if ($i == 11 && $data['m11'] == 0)
		{
			$i++;
			continue;
		}
		if ($not[$i] == 1)
		{
			$i++;
			continue;
		}
		$ret[] = $i;
		$i++;
	}
	return $ret;
}
function rendermodulepic($id,$name) {
	global $gfx;
	return "<img title=\"".$name."\" src=".$gfx."/goods/".$id.".gif>";
}
function getmodulelist()
{
	global $ship,$gfx;
	if ($ship->rumps_id == 9) return;
	
	
	$mods = array();
	$names = array();
	if ($ship->mod_m1 > 0) array_push($mods,$ship->mod_m1);
	if ($ship->mod_m2 > 0) array_push($mods,$ship->mod_m2);
	if ($ship->mod_m3 > 0) array_push($mods,$ship->mod_m3);
	if ($ship->mod_m4 > 0) array_push($mods,$ship->mod_m4);
	if ($ship->mod_m5 > 0) array_push($mods,$ship->mod_m5);
	
	if ($ship->mod_w1 > 0) array_push($mods,$ship->mod_w1);
	if ($ship->mod_w2 > 0) array_push($mods,$ship->mod_w2);

	if ($ship->mod_s1 > 0) array_push($mods,$ship->mod_s1);
	if ($ship->mod_s2 > 0) array_push($mods,$ship->mod_s2);
	

	foreach($mods as $m) {
		$name = $ship->db->query("SELECT name FROM stu_modules WHERE module_id = ".$m." LIMIT 1;",1);
		$names[$m] = $name;
	}
	$ret = "";
	
	foreach($mods as $m) {
		$ret .= "<img src=".$gfx."/goods/".$m.".gif title=\"".$names[$m]."\">&nbsp;";
	}	

	return $ret;
}
function getdammodulelist()
{
	global $ship,$gfx,$db;
	if ($ship->rumps_id == 9) return;
	$result = $db->query("SELECT system_id,date FROM stu_ships_subsystems WHERE ships_id=".$ship->id." ORDER BY system_id LIMIT 11");
	while($data=mysql_fetch_assoc($result)) $ret .= "<img src=".$gfx."/buttons/damaged_".$data['system_id'].".gif title=\"Reparatur dauert voraussichtlich bis ".date("d.m.Y H:i",$data['date'])."\">&nbsp;";
	return $ret;
}
function getuserrelationship($arr)
{
	if ($arr['type'] == 1 || $arr['mode'] == 3) return "<font color=#FF0000>Feind</font>";
	if ($arr['type'] > 2 || $arr['mode'] == 1) return "<font color=Green>Freund</font>";
	global $_SESSION;
	if ($arr['allys_id'] > 0 && $arr['allys_id'] == $_SESSION['allys_id']) return "<font color=Green>Freund</font>";
	return "<font color=#FFFFFF>Neutral</font>";
}
function rendercolonyshields($active,$schilde,$max)
{
	global $gfx;
	$pro = @round((100/$max)*$schilde);
	$bar = "<img src=".$gfx."/buttons/sh_".($active == 1 ? "blu" : "dblu").".gif height=6 width=".(ceil($pro)*2).">";
	if ($pro < 100) $bar .= "<img src=".$gfx."/buttons/hu_grey.gif height=6 width=".(floor((100-$pro))*2).">";
	return $bar;
}
function getWeaponDeviceData($id)
{
	global $db;
	switch ($id)
	{
		case 404: case 414: case 424:
			$data = $db->query("SELECT name,goods_id,damage as dmg,varianz,critical,hitchance FROM stu_torpedo_types WHERE torp_type=2",4);
			$data['rounds'] = 2;
			return $data;
		case 405: case 415: case 425:
			$data = $db->query("SELECT name,goods_id,damage as dmg,varianz,critical,hitchance FROM stu_torpedo_types WHERE torp_type=6",4);
			$data['rounds'] = 2;
			return $data;
		case 406: case 416: case 426:
			$data = $db->query("SELECT name,goods_id,damage as dmg,varianz,critical,hitchance FROM stu_torpedo_types WHERE torp_type=4",4);
			$data['rounds'] = 2;
			return $data;
		case 402: case 412: case 422:
			return array("dmg" => 12,"rounds" => 2,"varianz" => 20,"hitchance" => 95,"critical" => 5,"type" => 9);
	}
}
function getSystemDamageChance($data)
{
	return 0;
	// global $_SESSION;
	// if ($data['maintaintime'] == 0) return 0;
	// $over = time()-($data['lastmaintainance']+$data['maintaintime']);
	// if ($over <= 0) return;
	// if ($over < 86400) return 5;
	// if ($over < 432000) return 15;
	// if ($over < 864000) return 40;
	// if ($over < 1296000) return 75;
	// if ($over >= 1296000) return rand(75,100);
}
function getWeaponTypeDescription($type)
{
	switch ($type)
	{
		case 1:
			return "Phaser";
		case 2:
			return "Disruptor";
		case 3:
			return "Positron";
		case 4:
			return "Plasma";
		case 5:
			return "Partikel";
		case 6:
			return "Fusion";
		case 7:
			return "Laser";
		case 8:
			return "Energiedisruptor";
		case 9:
			return "Polaron";
	}
}
function getgravenergy(&$grav)
{
	return round ( 9.2 * (log(1.2 + $grav) / log(2.4)) - ($grav*$grav)/12);
}
function damageship($dmg,$huelle,$schilde,$schilde_status)
{
	if ($schilde_status == 1)
	{
		$schilde-=$dmg;
		if ($schilde <= 0)
		{
			$fm .= "<br>- Schilde brechen zusammen";
			$dmg=abs($schilde);
			$schilde_status = 0;
			$schilde = 0;
		}
		else
		{
			$fm .= "<br>- Schilde halten - Status: ".$schilde;
			$dmg = 0;
		}
	}
	if ($dmg > 0)
	{
		$huelle-=$dmg;
		if ($huelle > 0) $fm .= "<br>- Hülle bei ".$huelle;
	}
	return array("msg" => $fm,"huelle" => $huelle,"schilde" => $schilde,"schilde_status" => $schilde_status);
}
function getweatherdescr($type)
{
	switch($type)
	{
		case 1:
			return "Sonnig";
		case 2:
			return "Bewölkt";
		case 3:
			return "Regnerisch";
		case 4:
			return "Gewitter";
		case 5:
			return "Schneefall";
		case 6:
			return "Schneesturm";
		case 7:
			return "Sandsturm";
		case 8:
			return "Säureregen";
	}
}
function knparser(&$string)
{
	global $db,$_SESSION;
	preg_match_all("/\[ai(\d+)\]/m",$string,$regs);
	$count = count($regs[0]);
	$i = 0;
	while($i<$count)
	{
		if (check_int($regs[1][$i]))
		{
			$result = $db->query("SELECT allys_id FROM stu_allylist WHERE allys_id=".$regs[1][$i],1);
			if ($result == 0) $rpl = "";
			else $rpl = "(<a href=?p=ally&s=de&id=".$result.">?</a>)";
		}
		else $rpl = "[err]";
		$string = preg_replace("/\[ai".$regs[1][$i]."\]/m",$rpl,$string);
		$i++;
	}
	preg_match_all("/\[si(\d+)\]/m",$string,$regs);
	$count = count($regs[0]);
	$i = 0;
	while($i<$count)
	{
		if (check_int($regs[1][$i]))
		{
			$result = $db->query("SELECT id FROM stu_user WHERE id=".$regs[1][$i],1);
			if ($result == 0) $rpl = "";
			else $rpl = "(<a href=\"javascript:void(0);\" onClick=\"opensi(".$result.");\">?</a>)";
		}
		else $rpl = "[err]";
		$string = preg_replace("/\[si".$regs[1][$i]."\]/m",$rpl,$string);
		$i++;
	}
	return $string;
}
function parse_knrefs(&$string="")
{
	global $db;
	if ($string == "") return;
	preg_match_all("/(\d+),/s",$string.",",$regs);
	$count = count($regs[0]);
	if ($count == 0) return;
	if ($count > 7) $count = 7;
	$ret = "<div style=\"color: #848484; font-size: 7pt;\"><b>Bezüge:</b><br>";
	$i=0;
	while($i<$count)
	{
		if ($db->query("SELECT id FROM stu_kn WHERE id=".$regs[1][$i],1) == 0)
		{
			$i++;
			continue;
		}
		$ret .= "<a href=?p=comm&s=sb&sbn=".$regs[1][$i]." style=\"color: #848484; font-size: 7pt;\">".$regs[1][$i]."</a>";
		if ($i<$count-1) $ret .= ", ";
		$i++;
	}
	$ret .= "</div>";
	return $ret;
}
function iscolcent($building)
{
	switch($building)
	{
		case 1;
			return TRUE;		
	}
	return FALSE;
}
function getwprange($lvl,$wp)
{
	$st = "<div>".$wp."</div>";
	return $st;
}

function getmaintainrange($lvl,$wp)
{
	switch($lvl)
	{
		case 0:
			return "<div style=\"color: #00ff00\">Sehr gering</div>";
		case 1:
			if ($wp >= 0 && $wp <= 13) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 13 && $wp <= 20) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 20 && $wp <= 27) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 27 && $wp <= 34) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 34) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		case 2:
			if ($wp >= 0 && $wp <= 40) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 40 && $wp <= 47) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 47 && $wp <= 54) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 54 && $wp <= 61) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 61) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		case 3:
			if ($wp >= 0 && $wp <= 65) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 65 && $wp <= 72) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 72 && $wp <= 80) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 80 && $wp <= 88) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 88) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		case 4:
			if ($wp >= 0 && $wp <= 90) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 90 && $wp <= 96) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 96 && $wp <= 105) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 105 && $wp <= 116) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 116) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		case 5:
			if ($wp >= 0 && $wp <= 100) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 100 && $wp <= 115) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 115 && $wp <= 125) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 125 && $wp <= 135) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 135) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		
	}
}
function getbzrange($lvl,$wp)
{
	switch($lvl)
	{
		case 0:
			return "<div style=\"color: #00ff00\">Sehr gering</div>";
		case 1:
			if ($wp >= 0 && $wp <= 44) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 44 && $wp <= 52) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 52 && $wp <= 59) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 59 && $wp <= 65) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 65) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		case 2:
			if ($wp >= 0 && $wp <= 60) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 60 && $wp <= 65) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 65 && $wp <= 72) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 72 && $wp <= 80) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 80) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		case 3:
			if ($wp >= 0 && $wp <= 75) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 75 && $wp <= 82) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 82 && $wp <= 90) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 90 && $wp <= 96) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 96) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		case 4:
			if ($wp >= 0 && $wp <= 90) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 90 && $wp <= 96) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 96 && $wp <= 105) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 105 && $wp <= 114) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 114) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		case 5:
			if ($wp >= 0 && $wp <= 100) return "<div style=\"color: #00ff00\">Sehr gering</div>";
			if ($wp > 100 && $wp <= 120) return "<div style=\"color: #8aff00\">Gering</div>";
			if ($wp > 120 && $wp <= 140) return "<div style=\"color: #ffff00\">Mittel</div>";
			if ($wp > 140 && $wp <= 160) return "<div style=\"color: #ff8b00\">Hoch</div>";
			if ($wp > 160) return "<div style=\"color: #FF0000;\">Sehr hoch</div>";
		
	}
}

function showjwindow($content,$opt="")
{
	return "return overlib('".$content."', BGCOLOR, '#8897cf', TEXTCOLOR, '#8897cf', CELLPAD, 0, 0, 0, 0, CENTER".$opt.");";
}

function getfactionname($fac)
{
	switch($fac)
	{
		case 1:
			return "Föderationsangehörige";
		case 2:
			return "Romulaner";
		case 3:
			return "Klingonen";
		case 4:
			return "Sternenflotten-Ingenieure";
		case 5:
			return "Angehörige des Tal Shiar";
		case 6:
			return "Angehörige des Haus des Martok";
		case 7:
			return "Cardassianer";
		case 7:
			return "Evora";			
		default:
			return "Irgendjemand";
	}
}
function getshortfactionname($fac)
{
	switch($fac)
	{
		case 1:
			return "Föderation";
		case 2:
			return "Romulaner";
		case 3:
			return "Klingonen";
		case 4:
			return "Sternenflotten-Ingenieure";
		case 5:
			return "Tal Shiar";
		case 6:
			return "Haus des Martok";
		case 7:
			return "Cardassianer";
		case 9:
			return "Evora";				
		default:
			return "Irgendjemand";
	}
}
function getofficialfactionname($fac)
{
	switch(intval($fac))
	{
		case 0:
			return "Neutral";		
		case 1:
			return "Vereinte Föderation der Planeten";
		case 2:
			return "Romulanisches Sternenimperium";
		case 3:
			return "Klingonisches Imperium";
		case 4:
			return "Sternenflotten-Ingenieurs-Corps";
		case 5:
			return "Tal Shiar";
		case 6:
			return "Haus des Martok";
		case 7:
			return "Cardassianische Union";
		case 9:
			return "Evora-Protektorat";			
		default:
			return "Irgendjemand";
	}
}

function getFormatedFactionName($fac) {
	return "<font color='".getfactioncolor($fac)."'>".getofficialfactionname($fac)."</font>";
}

function getfactioncolor($fac)
{
	switch($fac)
	{
		case 1:
			return "#5588cc";
		case 2:
			return "#229922";
		case 3:
			return "#992222";
		case 4:
			return "#ccccaa";
		case 5:
			return "#666666";
		case 7: 
			return "#cc7722";
		case 9: 
			return "#cccc99";			
		default:
			return "#999999";
	}
}
function getfactionfloatcolor($fac)
{
	switch($fac)
	{
		case 1:
			return "rgba(85, 136, 204, 0.5)";
		case 2:
			return "rgba(34, 153, 34, 0.5)";
		case 3:
			return "rgba(153, 34, 34, 0.5)";
		case 9: 
			return "rgba(204, 204, 153, 0.5)";			
		default:
			return "rgba(120, 120, 120, 0.5)";
	}
}
function geteffectname($effect)
{
	switch($effect)
	{
		case "research":
			return "Forschungspunkte";
		case "fleet":
			return "Flottenpunkte";		
		case "pcrew":
			return "Crewpunkte";		
		case "pmaintain":
			return "Wartungspunkte";		
		case "psupply":
			return "Versorgungspunkte";					
		case "school":
			return "Ausbildung";				
		default:
			return "NO_DEF:".$effect;
	}
}

function getawardname($id)
{
	switch($id)
	{
		case 1:
			return "Spender während der Weihnachts-Spenden-Aktion";
		case 2:
			return "Auszeichnung des Verekkianischen Symposions für Sprachen";
		case 201:
			return "Experte für Son'a-Technologie";
		case 202:
			return "Experte für Kessok-Technologie";
		case 203:
			return "Experte für Gorn-Technologie";
		case 204:
			return "Experte für Breen-Technologie";
		case 900:
			return "STU-Admin";
		case 901:
			return "STU-Wohltäter";
		case 1002:
			return "Verdienstorden der Föderation";
		case 2011:
			return "Teilnahme an der Schlacht von Creative";
		case 2012:
			return "Sieg in der Schlacht von Creative";
		case 2013:
			return "Herausragende Leistung in der Schlacht von Creative";
		case 2022:
			return "Teilnahme an der Schlacht um die CDS Hutet";
		case 3011:
			return "Teilnahme an der Schlacht von Creative";
		case 3012:
			return "Sieg in der Schlacht von Creative";
		case 3013:
			return "Herausragende Leistung in der Schlacht von Creative";
		case 3022:
			return "Teilnahme an der Schlacht um die CDS Hutet";
		case 4022:
			return "Teilnahme an der Schlacht um die CDS Hutet";
		case 9002:
			return "Crew der SS Livingston";
	}
}
function getracename($race,$subrace=0)
{
	switch(($race*10)+$subrace)
	{
		case 10: 
			 return "Föderation";
		case 11: 
			 return "Mensch";
		case 12: 
			 return "Vulkanier";
		case 13: 
			 return "Andorianer";
		case 14: 
			 return "Tellarite";
		case 20: 
			 return "Romulaner";
		case 21: 
			 return "Remaner";
		case 30: 
			 return "Klingone";
		case 40: 
			 return "Cardassianer";
		case 50: 
			 return "Ferengi";
		case 60: 
			 return "Gorn";
		case 70: 
			 return "Verekkianer";
		case 80: 
			 return "Kessok";
		case 100: 
			 return "Son'a";
		case 110: 
			 return "Breen";
		case 200: 
			 return "Orion Syndikat";
		default: 
			 return "Unbekannt";
	}
}

function isenemy(&$fac1_uid,&$fac1_aid,&$fac2_uid,&$fac2_aid)
{
	if ($fac1_uid == $fac2_uid) return FALSE;
	$return = FALSE;
	global $db;
	if ($db->query("SELECT mode FROM stu_contactlist WHERE user_id=".$fac1_uid." AND recipient=".$fac2_uid." LIMIT 1",1) == 3) $return = TRUE;
	if ($fac1_aid > 0 && $db->query("SELECT type FROM stu_ally_relationship WHERE (allys_id1=".$fac1_aid." AND allys_id2=".$fac2_aid.") OR (allys_id1=".$fac2_aid." AND allys_id2=".$fac1_aid.") LIMIT 1",1) == 1) $return = TRUE;
	return $return;
}
function isfriend(&$fac1_uid,&$fac1_aid,&$fac2_uid,&$fac2_aid)
{
	if ($fac1_uid == $fac2_uid) return TRUE;
	$return = FALSE;
	global $db;
	if ($db->query("SELECT mode FROM stu_contactlist WHERE user_id=".$fac1_uid." AND recipient=".$fac2_uid." LIMIT 1",1) == 1) $return = TRUE;
	if ($fac1_aid > 0 && ($db->query("SELECT type FROM stu_ally_relationship WHERE (allys_id1=".$fac1_aid." AND allys_id2=".$fac2_aid.") OR (allys_id1=".$fac2_aid." AND allys_id2=".$fac1_aid.") LIMIT 1",1) == 4) || $fac1_aid == $fac2_aid) $return = TRUE;
	return $return;
}
function checkfactionkn()
{
	return 1;
}
function get_weapon_damage(&$mods)
{
	switch($mods)
	{
		case 1: return 1.0;
		case 2: return 1.1;
		case 3: return 1.2;
		case 4: return 1.25;
		case 5: return 1.25;
		default: return 1.5;
	}
}
function tarnkosten($rump)
{

	$rclass = floor($rump/100);

	switch ($rclass) {
		case 11: return 2;
		case 12: return 2;
		case 13: return 2;
		case 14: return 3;
		case 15: return 3;
		case 16: return 4;
		case 17: return 4;
		case 18: return 5;
		default: return 3;
	}
	return 0;
}



function buildingpic($bid,$ftype) {


	switch($bid."-".$ftype)
	{
		case "1-1": return 1;
		case "101-1": return 1;
		case "102-1": return 1;
		case "1-9": return 9;
		case "101-9": return 9;
		case "102-9": return 9;
		
		default: return 0;
	}
}

function colclassdescription($c) {


	switch($c %100)
	{
		case "1": return "Erdähnlich";
		case "2": return "Wald";
		case "3": return "Ozean";
		case "4": return "Ödland";
		case "5": return "Tundra";
		case "6": return "Wüste";
		case "7": return "Lava";
		case "8": return "Treibhaus";
		case "9": return "Eis";
		case "10": return "Fels";
		
		default: return "Unbekannt";
	}
}

function colclassexamples($c) {


	switch($c)
	{
		case "201": return "6464646464646464646464646464646464646464021f0606060206282828010202020101012828280201010101070101012801010701070101070128021f1f011f1f1f01012802020606021f06060202484a49484949474a48474847474a4a4747474847";
		case "202": return "64646464646464646464646464646464646464641f022e01012e010102021f1f0101052e010101020101012e2e2e010101051f05021f2e011f01021f02021f1f012e05011f1f0205012e0501050201014949474947484a484a474a484747484748474a4a";
		case "203": return "6464646464646464646464646464646464646464012901292901292a2828012929012929012928281f1f2a01290129012928291f012a011f29292828010101011f1f1f2a282829280129010101292828484a48474a4a4748474847484747494a49494748";
		case "204": return "646464646464646464646464646464646464646422060f09092206060609220909090f09090909090f08222208080809090f09092222220808080808090f0f22090922222222060606092209222209064749474848474748474747494847474749484747";
		case "205": return "64646464646464646464646464646464646464640606060606060612061212121f1212121f031f03031203120312121f1f1212121f1203031f1f0312121f031203120312121f060606060606061206064847474747474749484747474948494847474747";
		case "206": return "646464646464646464646464646464646464646413131313130707303007132f2f1313200707300713132f203020202020201313132007302020070713203107070720200707130730070707300730304a4747484747484947494847484a474749484747";
		case "207": return "6464646464646464646464646464646464646464cac8c9c9c8c9c8c8c8cac8c9c9c9c9d4e8c8c8c8cac8c9d3c8c8f1cac8c8e2e6f4f1c8c8c8c8c8cad9f4c9c9c9c8c8d3c8c8f1c9cac9c8c8c8dff4c84948474747484848474849474749474749474947";
		case "208": return "";
		case "209": return "64646464646464646464646464646464646464640606060e0614141414140606060621141414141421060e062121140e0e14210606062121060606140606062121060e060e14140e06210e21060614064847474847474a47474a48474749474a4847474a";
		case "210": return "64646464646464646464747474747474747474740a0a0a0c0a0a0a0a0a0a0c0c0a0a0a0c24240c0a0a240a240c240a0a0a0a0a24242424240a0a0a0c0a240a0a24240a0a0a0c0a0c0a0a0c0a0a0a0a0c4847484847474747474747474747474847484748";
		
		default: return "64";
	}





}


function formatDmg($type,$min,$max,$salvos,$flat=0) {
	global $gfx;
	
	$color = "FFFFFF";
	switch(trim($type)) {
		case "phaser": 		$color = "FFEE66";	break;		
		case "disruptor":	$color = "66FFAA";	break;			
		case "plasma":		$color = "33AA44";	break;			
		case "laser":		$color = "FF3333";	break;			
		case "kinetic": 	$color = "EE6666";	break;
		case "environment":	$color = "FFAA99"; break;
		default: 			$color = "EEEEEE";	break;
	}
	if ($flat) return "<font color='#".$color."'>".$min." ".damageTypeDescription($type)."</font>";
	
	if ($max != 0) return "<font color='#".$color."'>".($salvos>0?$salvos."x ":"").$min."-".$max." ".damageTypeDescription($type)."</font>";
	else		   return "<font color='#".$color."'>".($salvos>0?$salvos."x ":"")."Torpedo</font>";
}

function damageTypeDescription($t) {
	global $gfx;

	switch(trim($t))
	{
		case "phaser": 		return "Phaser-Schaden";			
		case "disruptor":	return "Disruptor-Schaden";			
		case "plasma":		return "Plasma-Schaden";			
		case "laser":		return "Laser-Schaden";			
		case "kinetic": 	return "Explosions-Schaden";
		case "environment":	return "Einflug-Schaden";
		default: 			return "Schaden";
	}
}

function weaponTypeDescription($t) {
	global $gfx;

	switch(trim($t))
	{
		case "beam": 		return "<img src=".$gfx."/buttons/icon/weapon_array.gif style=\"vertical-align:middle;\"> Strahlenbank";			
		case "pulse":		return "<img src=".$gfx."/buttons/icon/weapon_pulse.gif style=\"vertical-align:middle;\"> Pulswaffe";			
		case "cannon":		return "<img src=".$gfx."/buttons/icon/weapon_cannon.gif style=\"vertical-align:middle;\"> Kanone";			
		case "torpedo": 	return "<img src=".$gfx."/buttons/icon/weapon_torpedo.gif style=\"vertical-align:middle;\"> Torpedorampe";
		default: 			return "<img src=".$gfx."/buttons/icon/info.gif style=\"vertical-align:middle;\"> Unbekannter Typ: ".$t;
	}
}

function specialTypeDescription($t) {
	global $gfx;

	switch(trim($t))
	{
		case "lab": 		return "<img src=".$gfx."/buttons/icon/research.gif style=\"vertical-align:middle;\"> Labor";
		case "colony": 		return "<img src=".$gfx."/buttons/icon/planet.gif style=\"vertical-align:middle;\"> Kolonisierung";
		case "cloak": 		return "<img src=".$gfx."/buttons/icon/cloak.gif style=\"vertical-align:middle;\"> Tarnvorrichtung";			
		case "acloak": 		return "<img src=".$gfx."/buttons/icon/cloak.gif style=\"vertical-align:middle;\"> Verb. Tarnvorrichtung";
		case "freight":		return "<img src=".$gfx."/buttons/icon/storage.gif style=\"vertical-align:middle;\"> Frachtraum";			
		case "collector": 	return "<img src=".$gfx."/buttons/icon/asteroids.gif style=\"vertical-align:middle;\"> Sammler";
		case "detect": 		return "<img src=".$gfx."/buttons/icon/scanarea.gif style=\"vertical-align:middle;\"> Tarnentdeckung";
		default: 			return "<img src=".$gfx."/buttons/icon/info.gif style=\"vertical-align:middle;\"> Unbekannter Typ: ".$t;
	}
}




function moduleLevelString($type,$l) {
	if ($type == 7) return "Level: <b>-</b>";
	switch($l)
	{
		case 1: 		return "Level: <b><font color='#33ee33'>".$l."</font></b>";
		case 2: 		return "Level: <b><font color='#eeee33'>".$l."</font></b>";
		case 3: 		return "Level: <b><font color='#ee7733'>".$l."</font></b>";
		case 4: 		return "Level: <b><font color='#ee3333'>".$l."</font></b>";
		default: 		return "Level: <b><font color='#ffffff'>".$l."</font></b>";
	}
}
function moduleSlotString($type,$subtype) {
	global $gfx;
	if ($type == 7) {
		return "Slot: ".specialTypeDescription($subtype)." (Spezial)";
	}
	if ($type == 6) {
		return "Slot: ".weaponTypeDescription($subtype)." (Waffe)";
	}	
	switch ($type) {
		case 1: return "Slot: <img src=".$gfx."/buttons/icon/armor.gif style=\"vertical-align:middle;\"> Panzerung";
		case 2: return "Slot: <img src=".$gfx."/buttons/icon/shield.gif style=\"vertical-align:middle;\"> Schilde";
		case 3: return "Slot: <img src=".$gfx."/buttons/icon/warpcore.gif style=\"vertical-align:middle;\"> Warpkern";
		case 4: return "Slot: <img src=".$gfx."/buttons/icon/warp.gif style=\"vertical-align:middle;\"> Antrieb";
		case 5: return "Slot: <img src=".$gfx."/buttons/icon/scan.gif style=\"vertical-align:middle;\"> Sensoren";
	}
}

function composeModuleInfo($id) {
	global $gfx, $db;
	

	$m = $db->query("SELECT * FROM stu_modules WHERE module_id=".$id." AND viewable='1'",4);
		
	$specials = array();
		
	$sdata = $db->query("SELECT * FROM stu_modules_special WHERE modules_id=".$id."");
	while($s=mysql_fetch_assoc($sdata)) {
		array_push($specials,$s);
	}
	
	$wdata = $db->query("SELECT * FROM stu_weapons WHERE module_id=".$id."",4);

	$res = "<table border=0 style=\"width:100%;\">";
	
	$res .= "<tr><td colspan=2 style=\"padding:4px;\"><img src=".$gfx."/goods/".$id.".gif><b> ".$m['name']."</b></td></tr>";
	$res .= "<tr>";
		$res .= "<td width=50% style=\"padding:4px;\">".moduleSlotString($m['type'],$m['subtype'])."</td>";
		$res .= "<td width=50% style=\"padding:4px;\">". moduleLevelString($m['type'],$m['level'])."</td>";

	$res .= "</tr>";
	
	
	if ($wdata) {
		
		
		$res .= "<tr>";
		$res .= "<td width=50% style=\"padding:4px;\">".formatDmg($wdata[dtype],$wdata[mindmg],$wdata[maxdmg],0)."</td>";
		
		if ($wdata['wtype'] == 'beam') $res .= "<td width=50% style=\"padding:4px;\">Feuermodus: Streuend</td>";
		if ($wdata['wtype'] == 'pulse') $res .= "<td width=50% style=\"padding:4px;\">Feuermodus: Fokussiert</td>";
		if ($wdata['wtype'] == 'cannon') $res .= "<td width=50% style=\"padding:4px;\">Feuermodus: Fokussiert</td>";
		if ($wdata['wtype'] == 'torpedo') $res .= "<td width=50% style=\"padding:4px;\">Feuermodus: Streuend</td>";
		
		$res .=  "</tr>";
				
		
		$res .= "<tr>";
		$res .= "<td width=50% style=\"padding:4px;\">Schüsse pro Salve: ".$wdata['salvos']."</td>";
		$res .= "<td width=50% style=\"padding:4px;\">Energie pro Salve: ".$wdata['ecost']."</td>";
		$res .= "</tr>";
		
		$res .= "<tr>";
		$res .= "<td width=50% style=\"padding:4px;\">Trefferchance: ".$wdata[hitchance]."%</td>";
		if ($wdata[wtype] != "torpedo") $res .= "<td width=50% style=\"padding:4px;\">Kritisch: ".$wdata['critical']."%</td>";
		else							$res .= "<td width=50% style=\"padding:4px;\"></td>";
		$res .= "</tr>";		
		
	}
	
	
	if (count($specials) > 0) {
		foreach ($specials as $spec) {
			$res .= "<tr><td colspan=2 style=\"text-align:center;padding:4px;\">".modvalmapping($spec[type],$spec[value])."</td></tr>";
		}
	} else {
		$res .= "<tr><td colspan=2 style=\"text-align:center;padding:4px;\">Keine Besonderheiten</td></tr>";
	}
	
	
	$res .= "</table>";
	
	return $res;
}



function composeTorpedoInfo($id) {
	global $gfx, $db;
	
	$m = $db->query("SELECT * FROM stu_torpedo_types WHERE torp_type=".$id." ",4);

	$res = "<table border=0 style=\"width:100%;\">";
	
	$res .= "<tr><td colspan=2 style=\"padding:4px;\"><img src=".$gfx."/goods/".$id.".gif><b> ".$m['name']."</b></td></tr>";

		$res .= "<tr>";
		$res .= "<td width=50% style=\"padding:4px;\">".formatDmg($m[dtype],$m[mindmg],$m[maxdmg],0)."</td>";
		$res .= "<td width=50% style=\"padding:4px;\">Kritisch: ".$m['critical']."%</td>";
		$res .= "</tr>";		
		
	$res .= "</table>";
	
	return $res;
}


function plusminus($v) {
	if ($v > 0) return "<span class=\"valueplus\">+".$v."</span>";
	if ($v < 0) return "<span class=\"valueminus\">".$v."</span>";
	if ($v == 0) return "<span class=\"valuenull\">0</span>";
}

function composeBuildingInfo($id,$buildinfo=1,$colclass=0) {
	global $gfx, $db;
	
	$b = $db->query("SELECT * FROM stu_buildings WHERE buildings_id=".$id." ",4);
	if (!$b) return "";
	$res = "<table border=0 style=\"width:100%;\">";
	
	$res .= "<tr><th colspan=2 style=\"padding:4px;text-align:center;\"><b>".$b['name']."</b></th></tr>";

	$res .= "<tr>";
	$res .= "<td colspan=2 style=\"padding:4px;text-align:center;\"><img src=".$gfx."/buildings/".$b['buildings_id']."/0.png></td>";
	$res .= "</tr>";		
		


		
	if ($b['bev_pro'] > 0) {
		$res .= "<tr>";
		$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('bev_pro')."</td>";
		$res .= "<td style=\"padding:4px;\">".$b['bev_pro']."</td>";
		$res .= "</tr>";
	}		
	if ($b['bev_use'] > 0) {
		$res .= "<tr>";
		$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('bev_use')."</td>";
		$res .= "<td style=\"padding:4px;\">".$b['bev_use']."</td>";
		$res .= "</tr>";
	}
		

		
	if ($b['eps'] > 0) {
		$res .= "<tr>";
		$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('eps')."</td>";
		$res .= "<td style=\"padding:4px;\">".$b['eps']."</td>";
		$res .= "</tr>";
	}
	if ($b['lager'] > 0) {
		$res .= "<tr>";
		$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('storage')."</td>";
		$res .= "<td style=\"padding:4px;\">".$b['lager']."</td>";
		$res .= "</tr>";
	}
	


		$q = $db->query("SELECT * FROM stu_buildings_goods WHERE buildings_id=".$id." ORDER by goods_id ASC;");
		$r = $db->query("SELECT * FROM stu_buildings_effects WHERE buildings_id=".$id." ORDER by count ASC;");
		$bonus = $db->query("SELECT * FROM stu_colonies_bonus WHERE buildings_id=".$id." AND colonies_classes_id = ".$colclass." ORDER by goods_id ASC;",4);
		
		if ($b['eps_proc'] || mysql_num_rows($q) > 0 || mysql_num_rows($r) > 0) {
			$res .= "<tr><td colspan=2 style=\"padding:4px;text-align:center;\"><b>Produktion</td></tr>";		
		}
		
		if ($b['eps_proc'] != 0) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('energy')."</td>";
			if ($bonus && $bonus['goods_id'] == 0)
				$res .= "<td style=\"padding:4px;\">".plusminus($b['eps_proc']+$bonus['count'])." (Bonus)</td>";
			else 
				$res .= "<td style=\"padding:4px;\">".plusminus($b['eps_proc'])."</td>";
			$res .= "</tr>";
		}
		while ($good = mysql_fetch_assoc($q)) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".goodPic($good['goods_id'])."</td>";
			if ($bonus && $bonus['goods_id'] == $good['goods_id'])
				$res .= "<td style=\"padding:4px;\">".plusminus($good['count']+$bonus['count'])." (Bonus)</td>";
			else 
				$res .= "<td style=\"padding:4px;\">".plusminus($good['count'])."</td>";			
			$res .= "</tr>";
		}
		while ($effect = mysql_fetch_assoc($r)) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic($effect['type'])."</td>";
			$res .= "<td style=\"padding:4px;\">".plusminus($effect['count'])."</td>";
			$res .= "</tr>";
		}

		if ($id == 54) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".goodPic(1)."</td>";
			$res .= "<td style=\"padding:4px;\">".plusminus(2)." pro ".buildPic(2)."</td>";
			$res .= "</tr>";			
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".goodPic(1)."</td>";
			$res .= "<td style=\"padding:4px;\">".plusminus(2)." pro ".buildPic(9)."</td>";
			$res .= "</tr>";			
			
		}
		
		
	if ($buildinfo) {
	
		
		
		$res .= "<tr><td colspan=2 style=\"padding:4px;text-align:center;\"><b>Bau-Informationen</td></tr>";	
		
		$q = $db->query("SELECT * FROM stu_field_build WHERE buildings_id=".$id." ORDER by type ASC;");
		
		$i = 0;
		$fs = "";
		while ($field = mysql_fetch_assoc($q)) {
			$fs .= fieldPic($field['type'])." ";
			if ($i % 8 == 7) $fs .= "<br>";
			$i++;
			
		}
		$res .= "<tr>";
		$res .= "<td colspan=2 style=\"padding:4px;text-align:center;\">".$fs."</td>";
		$res .= "</tr>";
		
		if ($b['buildtime'] > 0) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('buildtime')."</td>";
			$res .= "<td style=\"padding:4px;\">".round($b['buildtime']/3600,1)." Stunden</td>";
			$res .= "</tr>";
		}	
		if ($b['research_id'] > 0) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('research')."</td>";
			$res .= "<td style=\"padding:4px;\">Benötigt Forschung</td>";
			$res .= "</tr>";	
		}			
		
		if ($b['blimit'] > 0) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('blimit')."</td>";
			$res .= "<td style=\"padding:4px;\">Limit: ".$b['blimit']." pro Spieler</td>";
			$res .= "</tr>";
		}
		
		if ($b['bclimit'] > 0) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('bclimit')."</td>";
			$res .= "<td style=\"padding:4px;\">Limit: ".$b['bclimit']." pro Kolonie</td>";
			$res .= "</tr>";
		}
		
		
		$q = $db->query("SELECT * FROM stu_buildings_cost WHERE buildings_id=".$id." ORDER by goods_id ASC;");
		
		$res .= "<tr><td colspan=2 style=\"padding:4px;text-align:center;\"><b>Baukosten</td></tr>";	
		

		
		if ($b['eps_cost'] != 0) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".infoPic('energy')."</td>";
			$res .= "<td style=\"padding:4px;\">".$b['eps_cost']."</td>";
			$res .= "</tr>";
		}
		while ($good = mysql_fetch_assoc($q)) {
			$res .= "<tr>";
			$res .= "<td style=\"padding:4px;text-align:center;width:36px;\">".goodPic($good['goods_id'])."</td>";
			$res .= "<td style=\"padding:4px;\">".$good['count']."</td>";
			$res .= "</tr>";
		}
	}
	
	$res .= "</table>";
	
	return $res;
}


			// if ($region['status'] == "core" && $region['attacker'] == 0) return "core";
			// if ($region['status'] == "core" && $region['attacker'] != 0) return "occupied";

			// if ($region['status'] == "free" && $region['attacker'] == 0) return "target";
			// if ($region['status'] == "free" && $region['attacker'] != 0) return "attack";

			// if ($region['status'] == "held" && $region['faction'] != $race) return "target";
			// if ($region['status'] == "held" && $region['faction'] == $race && $region['attacker'] != 0) return "attack";
			
			// if ($region['status'] == "occupied" && $region['faction'] != $race) return "target";
			// if ($region['status'] == "occupied" && $region['faction'] == $race && $region['attacker'] != 0) return "attack";			
			
			// if ($region['status'] == "contested" && $region['attacker'] != 0) return "attack";
			
			
	function sectorStateString($state, $counter, $owner, $attacker, $race) {
		if ($state == "free") {
			return "empty";
		}
		if ($state == "core") {
			return "core";
		}
		if ($state == "contested") {
			return "contested";
		}
		if ($state == "occupied") {
			return "occupied";
		}
		if ($state == "held") {
			if ($owner == $race) return "held";
			else return "target";
		}
		return "occupied";
	}


	function sectorStateChange($state, $counter, $owner, $attacker, $dominant) {
		
		$res['counter'] = $counter;
		$res['status']   = $state;
		$res['faction'] = $owner;
		$res['attacker'] = $attacker;
		
		switch($state."-c".$counter."-o".$owner."-a".$attacker."-d".$dominant) {
			
			case "free-c0-o0-a0-d1":		
			case "free-c0-o0-a0-d2":
			case "free-c0-o0-a0-d3":
										$res['status'] 		= "contested";
										$res['faction']		= 0;
										$res['attacker'] 	= $dominant;
										$res['counter']		= 3;
										break;
										
										
			case "held-c3-o1-a0-d0":
			case "held-c3-o2-a0-d0":
			case "held-c3-o3-a0-d0":	
			case "held-c2-o1-a0-d0":
			case "held-c2-o2-a0-d0":
			case "held-c2-o3-a0-d0":			
										$res['counter']		= $res['counter']-1;
										break;
										
			case "held-c1-o1-a0-d1":
			case "held-c1-o2-a0-d2":
			case "held-c1-o3-a0-d3":	
			case "held-c2-o1-a0-d1":
			case "held-c2-o2-a0-d2":
			case "held-c2-o3-a0-d3":		
			case "held-c0-o1-a0-d1":
			case "held-c0-o2-a0-d2":
			case "held-c0-o3-a0-d3":		
			
										$res['counter']		= $res['counter']+1;
										break;
										
										
										
			case "held-c1-o1-a0-d0":
			case "held-c1-o2-a0-d0":
			case "held-c1-o3-a0-d0":
			case "held-c0-o1-a0-d0":
			case "held-c0-o2-a0-d0":
			case "held-c0-o3-a0-d0":			
										$res['status'] 		= "free";
										$res['attacker'] 	= 0;
										$res['faction'] 	= 0;
										$res['counter']		= 0;
										break;
										
										
										
												
			case "held-c3-o1-a0-d2":
			case "held-c3-o1-a0-d3":
			case "held-c3-o2-a0-d1":
			case "held-c3-o2-a0-d3":
			case "held-c3-o3-a0-d1":
			case "held-c3-o3-a0-d2":			
			
			case "held-c2-o1-a0-d2":
			case "held-c2-o1-a0-d3":
			case "held-c2-o2-a0-d1":
			case "held-c2-o2-a0-d3":
			case "held-c2-o3-a0-d1":
			case "held-c2-o3-a0-d2":	

			case "held-c1-o1-a0-d2":
			case "held-c1-o1-a0-d3":
			case "held-c1-o2-a0-d1":
			case "held-c1-o2-a0-d3":
			case "held-c1-o3-a0-d1":
			case "held-c1-o3-a0-d2":			
										$res['status'] 		= "occupied";
										$res['attacker'] 	= $dominant;
										$res['counter']		= 3;
										break;
										
										
										
			case "contested-c3-o0-a1-d1":
			case "contested-c3-o0-a2-d2":
			case "contested-c3-o0-a3-d3":
			case "contested-c2-o0-a1-d1":
			case "contested-c2-o0-a2-d2":
			case "contested-c2-o0-a3-d3":			
										$res['counter']		= $res['counter']-1;
										break;
			case "contested-c1-o0-a1-d1":
			case "contested-c1-o0-a2-d2":
			case "contested-c1-o0-a3-d3":			
										$res['status'] 		= "held";
										$res['faction']		= $dominant;
										$res['attacker'] 	= 0;
										$res['counter']		= 0;
										break;
		
			case "contested-c3-o0-a1-d2":
			case "contested-c2-o0-a1-d2":
			case "contested-c1-o0-a1-d2":
			case "contested-c3-o0-a1-d3":
			case "contested-c2-o0-a1-d3":
			case "contested-c1-o0-a1-d3":
			
			case "contested-c3-o0-a2-d1":
			case "contested-c2-o0-a2-d1":
			case "contested-c1-o0-a2-d1":
			case "contested-c3-o0-a2-d3":
			case "contested-c2-o0-a2-d3":
			case "contested-c1-o0-a2-d3":
			
			case "contested-c3-o0-a3-d1":
			case "contested-c2-o0-a3-d1":
			case "contested-c1-o0-a3-d1":
			case "contested-c3-o0-a3-d2":
			case "contested-c2-o0-a3-d2":
			case "contested-c1-o0-a3-d2":			
										$res['status'] 		= "free";
										$res['faction']		= 0;
										$res['attacker'] 	= 0;
										$res['counter']		= 0;
										break;
			
			case "occupied-c3-o1-a2-d2":
			case "occupied-c3-o1-a3-d3":
			case "occupied-c3-o2-a1-d1":
			case "occupied-c3-o2-a3-d3":
			case "occupied-c3-o3-a1-d1":
			case "occupied-c3-o3-a2-d2":
			
			case "occupied-c2-o1-a2-d2":
			case "occupied-c2-o1-a3-d3":
			case "occupied-c2-o2-a1-d1":
			case "occupied-c2-o2-a3-d3":
			case "occupied-c2-o3-a1-d1":
			case "occupied-c2-o3-a2-d2":
			
			case "occupied-c3-o1-a2-d3":
			case "occupied-c3-o1-a3-d2":
			case "occupied-c3-o2-a1-d3":
			case "occupied-c3-o2-a3-d1":
			case "occupied-c3-o3-a1-d2":
			case "occupied-c3-o3-a2-d1":
			
			case "occupied-c2-o1-a2-d3":
			case "occupied-c2-o1-a3-d2":
			case "occupied-c2-o2-a1-d3":
			case "occupied-c2-o2-a3-d1":
			case "occupied-c2-o3-a1-d2":
			case "occupied-c2-o3-a2-d1":			
										$res['attacker'] 	= $dominant;			
										$res['counter']		= $res['counter']-1;
										break;
										
			case "occupied-c1-o1-a2-d2":
			case "occupied-c1-o1-a3-d3":
			case "occupied-c1-o2-a1-d1":
			case "occupied-c1-o2-a3-d3":
			case "occupied-c1-o3-a1-d1":
			case "occupied-c1-o3-a2-d2":	

			case "occupied-c1-o1-a2-d3":
			case "occupied-c1-o1-a3-d2":
			case "occupied-c1-o2-a1-d3":
			case "occupied-c1-o2-a3-d1":
			case "occupied-c1-o3-a1-d2":
			case "occupied-c1-o3-a2-d1":			
										$res['status'] 		= "contested";
										$res['faction']		= 0;
										$res['attacker'] 	= $dominant;
										$res['counter']		= 3;
										break;
										
			case "occupied-c3-o1-a2-d1":
			case "occupied-c3-o1-a3-d1":
			case "occupied-c3-o2-a1-d2":
			case "occupied-c3-o2-a3-d2":
			case "occupied-c3-o3-a1-d3":
			case "occupied-c3-o3-a2-d3":
			
			case "occupied-c2-o1-a2-d1":
			case "occupied-c2-o1-a3-d1":
			case "occupied-c2-o2-a1-d2":
			case "occupied-c2-o2-a3-d2":
			case "occupied-c2-o3-a1-d3":
			case "occupied-c2-o3-a2-d3":
			
			case "occupied-c1-o1-a2-d1":
			case "occupied-c1-o1-a3-d1":
			case "occupied-c1-o2-a1-d2":
			case "occupied-c1-o2-a3-d2":
			case "occupied-c1-o3-a1-d3":
			case "occupied-c1-o3-a2-d3":
										$res['status'] 		= "held";
										$res['faction']		= $owner;
										$res['attacker'] 	= 0;
										$res['counter']		= 0;
										break;
										
		}
		
		
	
		return $res;
	}
	
	function renderSectorState($state) {
		global $gfx;
		switch ($state) {
			case "held":		return "<font color='#1add84'><img src='".$gfx."/icons/conflict-defend.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>Gehalten</b></font>";
			case "core":		return "<font color='#ffffff'><img src='".$gfx."/icons/conflict-core.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>Kern-Cluster</b></font>";
			case "empty":		return "<font color='#ef962d'><img src='".$gfx."/icons/conflict-target.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>Einnehmbar</b></font>";
			case "target":		return "<font color='#ef962d'><img src='".$gfx."/icons/conflict-target.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>Angreifbar</b></font>";
			case "contested":	return "<font color='#dd1a1a'><img src='".$gfx."/icons/conflict-attack.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>Umkämpft</b></font>";
			case "occupied":	return "<font color='#dd1a1a'><img src='".$gfx."/icons/conflict-occupied.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>Besetzt</b></font>";
			case "free":	    return "<font color='#ef962d'><img src='".$gfx."/icons/conflict-target.png' style=\"vertical-align:middle;height:20px;width:20px;\"> <b>Unbeansprucht</b></font>";
			default:			return "???";
		}
	}

	function sectorStateIcon($state) {
		global $gfx;
		switch ($state) {
			case "held":		return "defend";
			case "core":		return "core";
			case "empty":		return "target";
			case "target":		return "target";
			case "contested":	return "attack";
			case "occupied":	return "occupied";
			default:			return "???";
		}
	}


	function noMouseoverGoodPic($id) {
		global $gfx;
		return "<img class=goodpic src='".$gfx."/goods/".$id.".gif'>";
	}	
	function goodPic($id) {
		global $gfx;
		if ($id == 0) return "<img class=goodpic src='".$gfx."/goods/".$id.".gif'>";
		return "<img class=goodpic src='".$gfx."/goods/".$id.".gif' onmouseover=\"showGood(this,".$id.");\" onmouseout=\"hideInfo();\">";
	}
	function infoPic($id) {
		global $gfx,$_SESSION;
		switch ($id) {
			case "bev_pro":		return "<img class=goodpic src='".$gfx."/bev/blank/0f.png' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "bev_use":		return "<img class=goodpic src='".$gfx."/bev/crew/".$_SESSION['race']."m.png' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "eps":			return "<img class=goodpic src='".$gfx."/icons/eps.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "storage":		return "<img class=goodpic src='".$gfx."/icons/storage.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "buildtime":	return "<img class=goodpic src='".$gfx."/icons/clock.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "blimit":		return "<img class=goodpic src='".$gfx."/icons/stopr.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "bclimit":		return "<img class=goodpic src='".$gfx."/icons/stopg.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "energy":		return "<img class=goodpic src='".$gfx."/icons/energy.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "research":	return "<img class=goodpic src='".$gfx."/icons/research.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "pmaintain":	return "<img class=goodpic src='".$gfx."/icons/pmaintain.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "psupply":		return "<img class=goodpic src='".$gfx."/icons/psupply.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			case "pcrew":		return "<img class=goodpic src='".$gfx."/icons/pcrew.gif' onmouseover=\"showMiscInfo(this,'".$id."');\" onmouseout=\"hideInfo();\">";
			default:			return "???";
		}
	}

	function fieldPic($id) {
		global $gfx;
		return "<img src='".$gfx."/fields/".$id.".gif' onmouseover=\"showField(this,'".$id."');\" onmouseout=\"hideInfo();\">";
	}
	function buildPic($id) {
		global $gfx;
		// return "<img class=buildpic src='".$gfx."/buildings/".$id."/0.png' onmouseover=\"showBuilding(this,".$id.");\" onmouseout=\"hideInfo();\">";
		return "<img class=buildpic src='".$gfx."/buildings/".$id."/0.png'>";
	}
	
function debugHelper($o) {
	echo "<div style=\"width:700px;float:right;\"><b>DEBUG:</b><br><br>";
	print_r($o);
	echo "</div>";
}






function getgoodname($id)
{
	global $db;
	$result = $db->query("SELECT name FROM stu_goods WHERE goods_id=".$id,1);
	return $result;
}
?>