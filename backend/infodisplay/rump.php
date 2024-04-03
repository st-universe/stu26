<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;


@session_start();

if ($_SESSION["login"] != 1) exit;

$gfx = $_SESSION[gfx_path];
// if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

// if (!check_int($_GET[rid]) || $_GET[rid] == 0) exit;

$res = $db->query("SELECT * FROM stu_rumps WHERE rumps_id = '".$_GET[rid]."' LIMIT 1",4);

if (!$res[rumps_id]) exit;


$rul = $db->query("SELECT rumps_id FROM stu_rumps_user WHERE rumps_id = '".$_GET[rid]."' AND user_id = ".$_SESSION["uid"]." LIMIT 1;",1);
$rus = $db->query("SELECT rumps_id FROM stu_rumps_scans WHERE rumps_id = '".$_GET[rid]."' AND user_id = ".$_SESSION["uid"]." LIMIT 1;",1);
$ruc = $db->query("SELECT rumps_id FROM stu_research WHERE rumps_id = '".$_GET[rid]."' AND (faction='0' OR faction='".$_SESSION["race"]."') LIMIT 1;",1);

if (!$rul && !$rus && !$ruc) exit;

// function getrace($r) {
	// switch($r)
	// {
		// case "1": return "Föderation";
		// case "2": return "Romulaner";
		// case "3": return "Klingonen";
		// case "4": return "Cardassianer";
		// case "5": return "Ferengi";
		// case "7": return "Cardassianer";
		// default: return "Unbekannt";
	// }
// }
function getrtype($t,$s) {
	switch($t.$s)
	{
		case "05": return "Frachter";
		case "06": return "Großraumfrachter";
		case "07": return "Transporter";
		case "11": return "Jäger";
		case "21": return "Eskortschiff";
		case "29": return "Bird of Prey";
		case "24": return "Erkundungsschiff";
		case "31": return "Zerstörer";
		case "39": return "Artillerieschiff";
		case "41": return "Kreuzer";
		case "44": return "Forschungsschiff";
		case "52": return "Warbird";
		case "54": return "Forschungsschiff";
		
		default: return "Unbekannt";
	}
}
function rmodlevel($num) {
	switch($num)
	{
		case "0": return "-";
		case "1": return "<font color=#00ff00>Level ".$num."</font>";
		case "2": return "<font color=#ffff00>Level ".$num."</font>";
		case "3": return "<font color=#ff8800>Level ".$num."</font>";
		case "4": return "<font color=#ff0000>Level ".$num."</font>";
		case "5": return "<font color=#ff00ff>Level ".$num."</font>";
		default: return "Level ".$num;
	}
}

function valueline($pic,$name,$value) {
	global $gfx;
	if ($value != 0) echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/".$pic.".gif> ".$name.":</td><td style=\"text-align: right;vertical-align: bottom;\">".$value."</td></tr>";
}

echo "<table class=tcal style=\"border: 1px groove #8897cf;\">";

echo "<tr>";

echo "<td style=\" width:250px;height:500px;vertical-align:top;\">";
	echo "<table>";
	echo "<tr><td colspan=2 style=\"text-align:center;\"><br><img src='".$gfx."ships/".$res[rumps_id].".gif' title=\"".$res[name]."\"><br><b>".$res[name]."</b></td></tr>";
	// echo "<tr><td colspan=2 style=\"text-align:center;\"><br><img src='gfx/ships/".$res[rumps_id].".gif' title=\"".$res[name]."\"><br><b>".$res[name]."</b></td></tr>";
	echo "<tr><td colspan=2>&nbsp;</td></tr>";
	echo "<tr><td width=60>Rasse:</td><td width=190>".getshortfactionname($res[race])."</td></tr>";
	echo "<tr><td width=60>Typ:</td><td width=190>".getrtype($res[type],$res[subtype])."</td></tr>";
	echo "</table>";
echo "</td>";



// echo "<td style=\" width:250px;height:500px;vertical-align:top;\">";
	// echo "<table>";
	// echo "<tr><td colspan=2 style=\"text-align:center;\">Basiswerte:</td></tr>";
	// echo "<tr><td colspan=2>&nbsp;</td></tr>";
	// valueline("integ","Hülle",$res[huelle]);
	// valueline("shld","Schilde",$res[schilde]);
	// echo "<tr><td colspan=2>&nbsp;</td></tr>";
	// valueline("eps","EPS",$res[eps]);	
	// valueline("warpk","Warpkern",$res[warpcore]);
	// valueline("wkp2","Reaktor",$res[reaktor]);
	// valueline("repli2","Lebenserhaltung",$res[eps_drain]);		
	// echo "<tr><td colspan=2>&nbsp;</td></tr>";
	// valueline("lss2","Sensorreichweite",$res[lss_range]);		
	// valueline("warp2","Warpantrieb",$res[warpfields]);	
	// valueline("warpsys","Warp/Tick",$res[warpfield_regen]);
	// valueline("maint1","Ausweichen",$res[evade_val]);	
	// echo "<tr><td colspan=2>&nbsp;</td></tr>";
	// valueline("crew","Min. Crew",$res[min_crew]);
	// valueline("crew","Max. Crew",$res[max_crew]);		
	// echo "<tr><td colspan=2>&nbsp;</td></tr>";
	// valueline("lager","Frachtraum",$res[storage]);
	// valueline("b_from2","Waren/Energie",$res[beamgood]);
	// valueline("crew","Crew/Energie",$res[beamcrew]);
	// echo "</table>";
// echo "</td>";


echo "<td style=\" width:250px;height:500px;vertical-align:top;\">";
	echo "<table>";
	echo "<tr><td colspan=2 style=\"text-align:center;\">Betrieb:</td></tr>";
	valueline("fleet","Flottenpunkte",$res['fleetpoints']);
	valueline("crew","Min. Crew",$res[min_crew]);
	valueline("crew","Max. Crew",$res[max_crew]);		
	valueline("repli2","Lebenserhaltung",$res[eps_drain]);	
	echo "<tr><td colspan=2>&nbsp;</td></tr>";
	echo "<tr><td colspan=2 style=\"text-align:center;\">Frachtraum & Transport:</td></tr>";
	valueline("lager","Frachtraum",$res[storage]);
	valueline("b_from2","Waren/Energie",$res[beamgood]);
	valueline("crew","Crew/Energie",$res[beamcrew]);
	echo "<tr><td colspan=2>&nbsp;</td></tr>";
	echo "<tr><td colspan=2 style=\"text-align:center;\">Bonus-Werte:</td></tr>";
	valueline("integ","Hülle",$res[huelle]);
	valueline("shld","Schilde",$res[schilde]);
	valueline("eps","EPS",$res[eps]);	
	valueline("warpk","Warpkern",$res[warpcore]);
	valueline("wkp2","Reaktor",$res[reaktor]);	
	valueline("lss2","Sensorreichweite",$res[lss_range]);		
	valueline("warp2","Warpantrieb",$res[warpfields]);	
	valueline("warpsys","Warp/Tick",$res[warpfield_regen]);
	valueline("maint1","Ausweichen",$res[evade_val]);		
	echo "</table>";
echo "</td>";




$w1types = explode(',',$res[w1_types]);
$w2types = explode(',',$res[w2_types]);
$s1types = explode(',',$res[s1_types]);
$s2types = explode(',',$res[s2_types]);

echo "<td style=\" width:250px;height:500px;vertical-align:top;\">";
	echo "<table>";
	echo "<tr><td colspan=2 style=\"text-align:center;\">Module:</td></tr>";
	echo "<tr><td colspan=2>&nbsp;</td></tr>";
	echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_1.gif> Hülle:</td><td style=\"text-align: right;vertical-align: bottom;\">".rmodlevel($res[m1_lvl])."</td></tr>";
	echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_2.gif> Schilde:</td><td style=\"text-align: right;vertical-align: bottom;\">".rmodlevel($res[m2_lvl])."</td></tr>";
	echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_3.gif> Warpkern:</td><td style=\"text-align: right;vertical-align: bottom;\">".rmodlevel($res[m3_lvl])."</td></tr>";
	echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_4.gif> Antrieb:</td><td style=\"text-align: right;vertical-align: bottom;\">".rmodlevel($res[m4_lvl])."</td></tr>";
	echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_5.gif> Sensoren:</td><td style=\"text-align: right;vertical-align: bottom;\">".rmodlevel($res[m5_lvl])."</td></tr>";
	echo "<tr><td colspan=2>&nbsp;</td></tr>";
	echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_6.gif> Primärwaffe:</td><td style=\"text-align: right;vertical-align: bottom;\">".rmodlevel($res[w1_lvl])."</td></tr>";
	echo "<tr><td colspan=2>";
	foreach($w1types as $wtype) {
		if ($wtype != "") echo "&nbsp;&nbsp;".weaponTypeDescription($wtype)."<br>";
	}
	echo "</td></tr>";	
	echo "<tr><td colspan=2>&nbsp;</td></tr>";
	echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_10.gif> Sekundärwaffe:</td><td style=\"text-align: right;vertical-align: bottom;\">".rmodlevel($res[w2_lvl])."</td></tr>";
	echo "<tr><td colspan=2>";
	foreach($w2types as $wtype) {
		if ($wtype != "") echo "&nbsp;&nbsp;".weaponTypeDescription($wtype)."<br>";
	}
	echo "</td></tr>";		
	
	if ($s1types[0] != "") {
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr><td colspan=2 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_7.gif> Spezialmodule (Slot 1):</td></tr>";
		echo "<tr><td colspan=2>";
		foreach($s1types as $wtype) {
			if ($wtype != "") echo "&nbsp;&nbsp;".specialTypeDescription($wtype)."<br>";
		}
		echo "</td></tr>";		
	}
	if ($s2types[0] != "") {
		echo "<tr><td colspan=2>&nbsp;</td></tr>";
		echo "<tr><td colspan=2 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/modul_7.gif> Spezialmodule (Slot 2):</td></tr>";
		echo "<tr><td colspan=2>";
		foreach($s2types as $wtype) {
			if ($wtype != "") echo "&nbsp;&nbsp;".specialTypeDescription($wtype)."<br>";
		}
		echo "</td></tr>";		
	}	
	echo "</table>";
echo "</td>";


echo "</tr>";

echo "</table>";

// print_r($w1types);
// print_r($res);



?>