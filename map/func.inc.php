<?php
function show_error($eId = 0)
{
	if ($eId == 0) global $eId;
	global $_POST, $_SESSION, $_GET, $sess;
	if (!check_int($eId));
	switch ($eId) {
		case 100:
			$txt = "Verbindung zur Datenbank fehlgeschlagen";
			addlog($eId, 0, $txt, 1);
			$sess->logout();
			break;
		case 101:
			$txt = "Es wurde kein User mit diesem Loginnamen gefunden";
			addlog($eId, 0, $txt, 9);
			break;
		case 102:
			$txt = "Der User wurde noch nicht aktiviert";
			break;
		case 103:
			$txt = "Der Account wurde aufgrund eines Regelversto�es gesperrt";
			addlog($eId, $_SESSION["uid"], $txt, 8);
			$sess->logout();
			break;
		case 104:
			$txt = "Falsches Passwort";
			addlog($eId, 0, $txt . " " . $_POST["login"] . "/" . $_POST["pass"], 7);
			break;
		case 105:
			$txt = "Du wurdest ausgeloggt";
			break;
		case 106:
			$txt = "Session abgelaufen. Bitte neu einloggen";
			break;
		case 107:
			$txt = "Der Urlaubsmodus ist noch aktiv und kann fr�hestens am " . date("d.m.Y H:i", $_SESSION[vac_blocktime]) . " beendet werden";
			break;
		case 200:
			$txt = "Ein allgemeiner Fehler is aufgetreten";
			break;
		case 201:
			$txt = "Du musst zuerst Level 2 erreichen. Dazu musst Du einen Klasse-M Planeten koloniseren";
			break;
		case 900:
			$txt = "Im Moment ist der Rundenwechsel aktiv";
			break;
		case 901:
			$txt = "Im Moment ist der Wartungsmodus aktiv";
			break;
		case 902:
			$txt = "Es wurde ein Betrugsversuch festgestellt. Deine Daten wurden an die Admins �bermittelt";
			addlog($eId, $_SESSION["uid"], "<font color=FF0000>Betrugsversuch!</font><br>Seite: " . $_GET[p] . " - Sektion: " . $_GET[s], 1);
			$sess->logout();
			break;
		case 903:
			$txt = "Der Urlaubsmodus wurde aktiviert.<br>Du wurdest automatisch ausgeloggt";
			addlog($eId, $_SESSION["uid"], "Urlaubsmodus aktiviert", 1);
			$sess->logout();
			break;
	}
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
		<title>Star Trek Universe</title>
		<link rel="STYLESHEET" type="text/css" href=gfx/css/style.css>
	</head>
	<body bgcolor=#000000>
	<table align=center bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr><th align=center><b>Fehler ' . $eId . '</b></th></tr>
	<tr><td>' . $txt . '<br><br>
	<a href=http://www.stuniverse.de title="Star Trek Universe">Star Trek Universe</a></td></tr>
	</table>
	</body>
	</html>';
	exit;
}
function addlog($eId, $uid, $txt, $lvl = 1)
{
	global $loglvl, $global_path;
	$ip = getenv("REMOTE_ADDR");
	$filename = date("d_m_y");
	$logfile = fopen($global_path . "intern/log/" . $filename . ".log", "a+");
	if ($lvl <= $loglvl) fwrite($logfile, "[" . date("H:i:s") . "]%-%" . $ip . "%-%" . $uid . "%-%" . $eId . "%-%" . addslashes($txt) . "\n");
	@fclose($logfile);
}
function addsyslog($id, $txt)
{
	if ($id == 101) $u = "Changeme1";
	if ($id == 102) $u = "Changeme2";
	if ($id == 1) $u = "System";
	$logfile = fopen($global_path . "intern/syslog/sys.log", "a+");
	fwrite($logfile, "<tr><td>" . date("d.m H:i") . "</td><td>" . $txt . "</td><td>" . $u . "</td></tr>\n");
	@fclose($logfile);
}
function meldung($txt)
{
	echo "<table bgcolor=#262323 cellspacing=1 cellpadding=1>
	<tr>
	<th>Meldung</th>
	</tr>
	<tr>
	<td>" . stripslashes($txt) . "</td>
	</tr></table><br>";
}
function format_string($string)
{
	$string = str_replace("\"", "", $string);
	$string = str_replace("style", "", $string);
	$string = strip_tags($string, "<b></b><i></i><font></font>");
	return addslashes($string);
}
function pageheader($txt)
{
	echo "<table width=100% bgcolor=#262323 cellspacing=1 cellpadding=1><tr><th>" . $txt . "</th></tr></table><br>";
}
function vdam($arr)
{
	if (is_object($arr)) {
		if ($arr->trumfield == 1) return "t/";
		round((100 / $arr->max_huelle) * $arr->huelle) < 40 ? $d = "d/" : $d = "";
	} else {
		if ($arr[trumfield] == 1) return "t/";
		round((100 / $arr[max_huelle]) * $arr[huelle]) < 40 ? $d = "d/" : $d = "";
	}
	return $d;
}
function vtrak($shipId)
{
	global $db, $gfx;
	$result = $db->query("SELECT rumps_id,name,huelle,max_huelle FROM stu_ships WHERE id=" . $shipId, 4);
	return "<img src=" . $gfx . "/ships/" . vdam($result[huelle], $result[max_huelle]) . $result[rumps_id] . ".gif> " . stripslashes($result[name]);
}
function checksector($tar)
{
	global $ship;
	if ($tar[cloak] == 1) return 0;
	if ($ship->systems_id > 0 || $tar[systems_id] > 0) {
		if ($ship->systems_id != $tar[systems_id]) return 0;
		if ($ship->sx != $tar[sx] || $ship->sy != $tar[sy]) return 0;
		return 1;
	}
	if ($ship->cx != $tar[cx] || $ship->cy != $tar[cy]) return 0;
	return 1;
}
function checkcolsector($tar)
{
	global $col;
	if ($tar[cloak] == 1) return 0;
	if ($col->systems_id > 0 || $tar[systems_id] > 0) {
		if ($col->systems_id != $tar[systems_id]) return 0;
		if ($col->sx != $tar[sx] || $col->sy != $tar[sy]) return 0;
		return 1;
	}
	if ($col->cx != $tar[cx] || $col->cy != $tar[cy]) return 0;
	return 1;
}
function ftit($txt)
{
	return strip_tags(stripslashes($txt));
}
function get_dn_state($i, $chg, $mode, $fid, $is_moon)
{
	if (!$is_moon && ($fid < 19 || $fid > 72)) return "";
	if ($mode == 1) {
		$retak = "";
		$retnu = "n/";
	}
	if ($mode == 2) {
		$retak = "n/";
		$retnu = "";
	}
	if ($i == 1 && $chg < 3600) return $retnu;
	if ($i == 2 && $chg < 3200) return $retnu;
	if ($i == 3 && $chg < 2800) return $retnu;
	if ($i == 4 && $chg < 2400) return $retnu;
	if ($i == 5 && $chg < 2000) return $retnu;
	if ($i == 6 && $chg < 1600) return $retnu;
	if ($i == 7 && $chg < 1200) return $retnu;
	if ($i == 8 && $chg < 800) return $retnu;
	if ($i == 9 && $chg < 400) return $retnu;
	return $retak;
}
function gen_time($time)
{
	if ($time <= 0) return "0m";
	$sek = $time - (floor($time / 60) * 60);
	$min = floor($time / 60) - (floor(floor($time / 60) / 60) * 60);
	$hour = floor(floor($time / 60) / 60);
	return $hour . "h " . $min . "m " . $sek . "s";
}
function setyear()
{
	return (date("Y", time()) + 375);
}
function check_int($var)
{
	if (!is_numeric($var)) return FALSE;
	if ($var < 0) return FALSE;
	return TRUE;
}
function check_html_tags($string)
{
	// Font
	if (substr_count($string, "<font") != substr_count($string, "</font>")) return FALSE;
	// Bold
	if (substr_count($string, "<b>") != substr_count($string, "</b>")) return FALSE;
	// Italic
	if (substr_count($string, "<i>") != substr_count($string, "</i>")) return FALSE;
	return TRUE;
}
function getnamebyfield($id)
{
	switch ($id) {
		default:
			return "Wiese";
		case 1:
			return "Wiese";
			break;
		case 2:
			return "Wald";
			break;
		case 5:
			return "Wasserfl�che";
			break;
		case 6:
			return "Eisfl�che";
			break;
		case 7:
			return "W�ste";
			break;
		case 31:
			return "Berg";
			break;
		case 33:
			return "Eisformation";
			break;
		case 71:
			return "Gestein";
			break;
		case 74:
			return "Eisschicht";
			break;
		case 100:
			return "Orbitfeld";
			break;
	}
}
function getonm($name, $pic)
{
	return "onmouseover=cp('" . $name . "','" . $pic . "2') onmouseout=cp('" . $name . "','" . $pic . "1')";
}

function getbuildinghelp($id)
{
	return "(<a href=ld.php?s=bh&id=" . $id . " target=bottom>?</a>)";
}

function getterraforminghelp($vfeld, $zfeld)
{
	return "(<a href=ld.php?s=th&vfeld=" . $vfeld . "&zfeld=" . $zfeld . " target=bottom>?</a>)";
}

function shipexception($iarr, $c_var)
{
	if (is_object($c_var)) foreach ($c_var as $key => $value) $cd[$key] = $value;
	else $cd = $c_var;
	$return[code] = 0;
	foreach ($iarr as $key => $value) {
		switch ($key) {
			case "nbs":
				if ($value != $cd[nbs]) return array("code" => 1, "msg" => "Die " . ($cd[systems_id] > 1 ? "Kurzstreckensensoren" : "Nahbereichssensoren") . " sind nicht aktiviert");
				break;
			case "eps":
				if ($value == -1 && $cd[eps] == 0) return array("code" => 1, "msg" => "Es wird mindestens 1 Energie ben�tigt");
				if ($value > $cd[eps]) return array("code" => 1, "msg" => "Es wird " . $value . " Energie ben�tigt");
				break;
			case "cloak":
				if ($value != $cd[cloak]) return array("code" => 1, "msg" => "Die Tarnung ist aktiviert");
				break;
			case "phaser":
				if ($cd[phaser] == 0) return array("code" => 1, "msg" => "Es ist kein Waffenmodul auf diesem Schiff installiert");
				break;
			case "slots":
				if ($cd[slots] > 0) return array("code" => 1, "msg" => "Eine Station kann nicht bewegt werden");
				break;
			case "traktor":
				if ($cd[traktormode] == 1) return array("code" => 1, "msg" => "Der Traktorstrahl ist aktiviert");
				if ($cd[traktormode] == 2) return array("code" => 1, "msg" => "Das Schiff wird von einem Traktorstrahl gehalten");
				break;
			case "warp":
				if ($value != $cd[warpable]) return array("code" => 1, "msg" => "F�r diese Aktion wird Warpantrieb ben�tigt");
				break;
			case "system":
				if ($cd[systems_id] == 0) return array("code" => 1, "msg" => "Das Schiff befindet sich in keinem System");
				break;
			case "schilde_status":
				if ($cd[schilde_status] == 1) return array("code" => 1, "msg" => "Die Schilde sind aktiviert");
				break;
			case "schilde_load":
				if ($cd[schilde] >= $value) return array("code" => 1, "msg" => "Die Schilde sind bereits vollst�ndig geladen");
				break;
			case "crew":
				if ($cd[crew] < $value) return array("code" => 1, "msg" => "Es werden " . $value . " Crewmitglieder ben�tigt - vorhanden sind nur " . $cd[crew]);
				break;
		}
	}
	return $return;
}

function getroundtime($rtg)
{
	// EDIT -> Abfrage der aktuellen Runde einbauen
	$lr = 12;
	$rd = time() + (floor($rtg / 5) * 86400);
	$rtg -= floor($rtg / 5) * 5;
	if ($rtg > 0) {
		switch ($lr) {
			case 12:
				$rd += $rtg * 10800;
				break;
			case 15:
				if ($rd < 4) $rd += $rtg * 10800;
				else $rd += ($rtg - 1) * 10800 + 43200;
				break;
			case 18:
				if ($rd < 3) $rd += $rtg * 10800;
				else $rd += ($rtg - 1) * 10800 + 43200;
				break;
			case 21:
				if ($rd < 2) $rd += $rtg * 10800;
				else $rd += ($rtg - 1) * 10800 + 43200;
				break;
			case 0:
				$rd += ($rtg - 1) * 10800 + 43200;
				break;
		}
	}
	return getrounddate($rd);
}
function getrounddate($date)
{
	if (date("H", $date) >= 0 && date("H", $date) < 12) return date("d.m", $date) . " 0 Uhr";
	if (date("H", $date) >= 12 && date("H", $date) < 15) return date("d.m", $date) . " 12 Uhr";
	if (date("H", $date) >= 15 && date("H", $date) < 18) return date("d.m", $date) . " 15 Uhr";
	if (date("H", $date) >= 18 && date("H", $date) < 21) return date("d.m", $date) . " 18 Uhr";
	if (date("H", $date) >= 21) return date("d.m", $date + 4000) . " 21 Uhr";
}
function getnextlevel()
{
	global $_SESSION;
	switch ($_SESSION[level]) {
		case 0:
			return;
		case 1:
			return;
		case 2:
			return array("lvl" => 3, "wp" => 10, "ship" => 0, "quest" => 0);
		case 3:
			return array("lvl" => 4, "wp" => 25, "ship" => 0, "quest" => 1);
		case 4:
			return array("lvl" => 5, "wp" => 50, "ship" => 1, "quest" => 3);
		case 5:
			return array("lvl" => 6, "wp" => 90, "ship" => 1, "quest" => 6);
		case 6:
			return array("lvl" => 7, "wp" => 160, "ship" => 3, "quest" => 10);
	}
}