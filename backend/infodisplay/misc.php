<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;

@session_start();

if ($_SESSION["login"] != 1) exit;

$gfx = $_SESSION[gfx_path];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

// if (!check_int($_GET[gid]) || $_GET[gid] == 0) exit;


	function info($id) {
		switch ($id) {
			case "bev_pro":		return "Wohnraum bietet Platz f�r potenzielle neue Einwohner, die nach und nach einwandern.";
			case "bev_use":		return "Zum Betrieb von Geb�uden werden Arbeiter ben�tigt.";
			case "eps":			return "Erh�ht den Energiespeicher.";
			case "storage":		return "Erh�ht die Lagerkapazit�t.";
			case "buildtime":	return "Die zum Bau ben�tigte Zeit.";
			case "blimit":		return "Dieses Geb�ude hat ein Limit, wie oft es pro Spieler baubar ist.";
			case "bclimit":		return "Dieses Geb�ude hat ein Limit, wie oft es pro Kolonie baubar ist.";
			case "energy":		return "Wird f�r fast alle Vorg�nge ben�tigt.";
			case "research":	return "Forschungspunkte werden beim Tick auf laufende Forschungen angerechnet, um diese zu erforschen.";
			case "pcrew":		return "Repr�sentiert Ausbildung und Unterhalt von Schiffscrews. Erh�ht das Schiffslimit in Verbindung mit Wartungs- und Versorgungspunkten.";
			case "pmaintain":	return "Repr�sentiert Wartung und generelle Instandhaltung von Schiffen. Erh�ht das Schiffslimit in Verbindung mit Crew- und Versorgungspunkten.";
			case "psupply":		return "Repr�sentiert Versorgung von Schiffen. Erh�ht das Schiffslimit in Verbindung mit Crew- und Wartungsspunkten.";
			default:			return "???";
		}
	}
	
	function name($id) {
		switch ($id) {
			case "bev_pro":		return "Wohnraum";
			case "bev_use":		return "Arbeiter";
			case "eps":			return "EPS";
			case "storage":		return "Lager";
			case "buildtime":	return "Bauzeit";
			case "blimit":		return "Baulimit: Spieler";
			case "bclimit":		return "Baulimit: Kolonie";
			case "energy":		return "Energie";
			case "research":	return "Forschungspunkte";
			case "pmaintain":	return "Wartungspunkte";
			case "psupply":		return "Versorgungspunkte";
			case "pcrew":		return "Crewpunkte";
			default:			return "???";
		}
	}
	
	
	function pic($id) {
		switch ($id) {
			case "bev_pro":		return "/bev/blank/0f.png";
			case "bev_use":		return "/bev/crew/".$_SESSION['race']."m.png";
			case "buildtime":	return "/icons/clock.gif";
			case "blimit":		return "/icons/stopr.gif";
			case "bclimit":		return "/icons/stopg.gif";
			default:			return "/icons/".$id.".gif";
		}
	}	
	
	function getInfo($id) {
	global $gfx;
	$s = "<img class=goodpic src=".$gfx.pic($id)."> ";

	
	$s .= "<b>".name($id)."</b><br><br>".info($id);
	
	return $s;
}

	echo "<div style=\"width:400px; text-align:left;\">";
	echo floatingPanel(4,"Info","slist",$gfx."/buttons/icon/info.gif","<div style=\"padding:4px;\">".getInfo($_GET[gid])."</div>");	
	echo "</div>";
	





?>