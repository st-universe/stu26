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

if (!check_int($_GET[gid]) || $_GET[gid] == 0) exit;




function goodText($id) {
	switch($id) {
		case 0: return "Wird für fast alle Vorgänge benötigt.";
		case 1: return "Konsumiert von Einwohnern. 10 Einwohner essen 1 Nahrung pro Tick.";
		case 2: return "Grundlegende Materialien und Werkzeuge, die für fast alle Konstruktionen benötigt werden.";
		case 3: return "Alleine wenig nützlich, werden diese Chemikalien meist zu anderen Waren weiterverarbeitet.";
		case 4: return "Das Glas der Zukunft. Wird für viele Konstruktionen benötigt.";
		case 5: return "Dieses schwere Wasserstoff-Isotop wird hauptsächlich zur Energiegewinnung in Fusions- und Warpreaktoren benötigt.";
		case 6: return "Typischerweise Anti-Deuterium. Wird von Warpreaktoren verbraucht und für die Konstruktion von Photonentorpedos benötigt.";
		case 7: return "Dihydrogen-Monoxid. Wird für die meisten Formen von Leben benötigt. Hydroponik und Deuteriumextraktion sind die häufigsten industriellen Anwendungen.";
		case 8: return "Ein seltener Kristall, der die hoch-energetischen Reaktionen in Materie/Antimaterie-Auslöschungen regulieren kann.";
		case 9: return "Sammelbegriff für Elemente, die oft nur in Spuren vorkommen, aber von industrieller Bedeutung sind.";
		case 11: return "Gestein mit hohem Metall-Gehalt. Muss weiterverarbeitet werden.";
		case 19: return "Tritanium in Reinform. Seine Verarbeitung zu Tritanium-Legierung ist ein hochkomplexer Prozess.";
		case 21: return "Eine sehr widerstandsfähige Metall-Legierung, die für viele Konstruktionen benötigt wird.";
		case 29: return "Tritanium-Legierung hält selbst extremsten Umständen stand. Es wird daher für moderne Schiffspanzerungen verwendet.";
		case 30: return "Allgemeine Komponente in vielen einfacheren Schiffssystemen.";
		case 31: return "Bestandteil von modernen Computer-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.";
		case 32: return "Bestandteil von Hochenergie-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.";
		case 33: return "Bestandteil von Schiffs-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.";
		case 34: return "Bestandteil von Schiffs-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.";
		case 35: return "Bestandteil von Schiffs-Systemen. Wird zur Herstellung von Schiffsmodulen verwendet.";
		case 41: return "Seltener Rohstoff, der zur Herstellung von Isolinearen Chips benötigt wird.";
		case 42: return "Seltener Rohstoff, der zur Herstellung von Hochenergie-Plasma benötigt wird.";
		case 43: return "Seltener Rohstoff, der zur Herstellung von Metaphasen-Konvertern benötigt wird.";
		case 44: return "Seltener Rohstoff, der zur Herstellung von Subraum-Spulen benötigt wird.";
		case 45: return "Seltener Rohstoff, der zur Herstellung von Partikel-Emittern benötigt wird.";		
		case 66: return "Eine imaginäre High-Tech-Komponente, die irgendwas cooles macht.";

		default: return "Keine Beschreibung vorhanden.";
		
	}
}	

function goodName($id) {
	switch($id) {
		case 0: return "Energie";
		case 1: return "Nahrung";
		case 2: return "Baumaterial";
		case 3: return "Chemikalien";
		case 4: return "Transparentes Aluminium";
		case 5: return "Deuterium";
		case 6: return "Antimaterie";
		case 7: return "Wasser";
		case 8: return "Dilithium";
		case 9: return "Seltene Erden";
		case 11: return "Erz";
		case 19: return "Tritanium";
		case 21: return "Duranium";
		case 29: return "Tritanium-Legierung";
		case 30: return "Einfache Schaltkreise";
		case 31: return "Isolineare Chips";
		case 32: return "Hochenergie-Plasma";
		case 33: return "Metaphasen-Konverter";
		case 34: return "Subraum-Spulen";
		case 35: return "Partikel-Emitter";
		case 41: return "Nitrium";
		case 42: return "Kemocite";
		case 43: return "Verterium";
		case 44: return "Cortenit";
		case 45: return "Bilitrium";
		case 66: return "Transphasen-Fluxkompensator";
		default: return "???";
		
	}
}	
function getGoodInfo($id) {
	global $gfx;
	$s = "<img class=goodpic src=".$gfx."/goods/".$id.".gif> ";

	
	$s .= "<b>".goodName($id)."</b><br><br>".goodText($id);
	
	return $s;
}	
	


	
	if ($_GET[gid] > 1000) {
		echo "<div style=\"width:610px; text-align:left;\">";
		echo floatingPanel(3,"Modul-Daten","slist",$gfx."/buttons/icon/shipparts.gif","".composeModuleInfo($_GET[gid])."");	
		echo "</div>";
	} elseif ($_GET[gid] > 79) {
		echo "<div style=\"width:610px; text-align:left;\">";
		echo floatingPanel(2,"Torpedo-Daten","slist",$gfx."/buttons/icon/torpedo.gif","".composeTorpedoInfo($_GET[gid])."");	
		echo "</div>";
	} else {
		echo "<div style=\"width:400px; text-align:left;\">";
		echo floatingPanel(4,"Waren-Beschreibung","slist",$gfx."/buttons/icon/info.gif","<div style=\"padding:4px;\">".getGoodInfo($_GET[gid])."</div>");	
		echo "</div>";
	}






?>