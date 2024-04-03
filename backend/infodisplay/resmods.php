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

if (!check_int($_GET[rid]) || $_GET[rid] == 0) exit;

$res = $db->query("SELECT * FROM stu_research WHERE research_id = ".$_GET[rid]." LIMIT 1",4);

if ($res[mod_id] == "") exit;


$mods = explode(" ",$res[mod_id]);




function getrace($r) {
	switch($r)
	{
		case "1": return "Föderation";
		case "2": return "Romulaner";
		case "3": return "Klingonen";
		case "4": return "Cardassianer";
		case "5": return "Ferengi";
		
		default: return "Unbekannt";
	}
}


function getModValues($modtype) {
	global $db;

		$m = $db->query("SELECT * FROM stu_modules WHERE module_id=".$modtype." AND viewable='1'",4);
		
		$ele = array();
		$ele[name] = $m[name];
		$ele[id] = $m[module_id];
		$ele[specials] = array();
		
		$sdata = $db->query("SELECT * FROM stu_modules_special WHERE modules_id=".$modtype."");
		while($s=mysql_fetch_assoc($sdata)) {
			array_push($ele[specials],$s);
		}
		if ($m[type] == 6) {
			$wdata = $db->query("SELECT * FROM stu_weapons WHERE module_id=".$modtype." LIMIT 1;",4);
			$ele[weapon] = $wdata;
		}

	return $ele;
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
	echo "<tr><td width=150 style=\"vertical-align: bottom;\"><img src=".$gfx."/buttons/".$pic.".gif> ".$name.":</td><td style=\"text-align: right;vertical-align: bottom;\">".$value."</td></tr>";
}


function showmod($data) {
	global $gfx;

echo "<center><table class=tcal style=\"width:600px;border: 1px solid #4c4c4c;\"><tr><td style=\"height:26px; width:550px;\"><img src=../gfx/goods/".$data[id].".gif>&nbsp;".$data[name]."</td></tr><tr>";

		if ($data[weapon]) {
			
			echo "<td style=\"text-align:right; height:26px;\">";
				echo weaponTypeDescription($data[weapon][wtype]).": ".formatDmg($data[weapon][dtype],$data[weapon][mindmg],$data[weapon][maxdmg],$data[weapon][salvos]);
				if ($data[weapon][wtype] != "torpedo") 	echo "<br>".$data[weapon][ecost]." Energiekosten, ".$data[weapon][hitchance]."% Treffer, ".$data[weapon][critical]."% Kritisch";
				else 									echo "<br>".$data[weapon][ecost]." Energiekosten, ".$data[weapon][hitchance]."% Treffer";
			echo "</td></tr>";
		
		} else {
		echo "<td style=\"text-align:right; height:26px;\">";
			if (count($data[specials]) > 0) {
				foreach ($data[specials] as $spec) {
					echo modvalmapping($spec[type],$spec[value])."<br>";
				}
			} else {
				echo "Keine Besonderheiten";
			}
		echo "</td></tr>";
		}
	echo "</table></center>";	
}





 echo "<div style=\"border: 1px groove #8897cf; background-color: #000000; width:610px; text-align:center;\">";
	echo "<br>";
	foreach($mods as $modid) {

		$data = getModValues($modid);

		showmod($data);
		echo "<br><br>";

	}


echo "</div>";



?>