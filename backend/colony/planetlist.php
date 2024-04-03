<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;
@session_start();

if ($_SESSION['login'] != 1) exit;
if (!check_int($_GET['id'])) exit;

$gfx = $_SESSION['gfx_path'];
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../../gfx/";

$col = $db->query("SELECT id,name,gravitation,colonies_classes_id,systems_id FROM stu_colonies WHERE id=".$_GET['id']." AND user_id=".$_SESSION['uid']." LIMIT 1",4);
if ($col == 0) exit;



	function getPostfix($s) {		
		$a = explode(" ",$s);
		return $a[count($a)-1];
	}


	function getPlanetNumbers($a) {
		
		
		$last = substr($a, -1);
		

		
		switch($last) {
			case "a": $moon = 1; break;
			case "b": $moon = 2; break;
			case "c": $moon = 3; break;
			case "d": $moon = 4; break;
			case "e": $moon = 5; break;
			case "f": $moon = 6; break;
			case "g": $moon = 7; break;
			case "h": $moon = 8; break;
			case "i": $moon = 9; break;
			default: $moon = 0; break;
		}
		
		switch($last) {
			case "a": 
			case "b":
			case "c":
			case "d":
			case "e":
			case "f":
			case "g":
			case "h":
			case "i": $planet = substr($a, 0, strlen($a)-1); break;
			default: $planet = $a;
			
		}
		$r['p'] = $planet;
		$r['m'] = $moon;
		$r['n'] = $a;
		return $r;
	}
	

	$colonies = $db->query("SELECT c.id,c.planet_name,c.colonies_classes_id,c.user_id,l.name as cname,l.research_id FROM stu_colonies AS c LEFT JOIN stu_colonies_classes as l ON c.colonies_classes_id = l.colonies_classes_id WHERE c.systems_id=".$col['systems_id'].";");


	$cols = array();
	
	$maxm = 0;
	
	while ($c = mysql_fetch_assoc($colonies)) {
		
		$r = getPlanetNumbers(getPostfix($c['planet_name']));	
		$c['p'] = $r['p'];
		$c['m'] = $r['m'];
		$c['n'] = $r['n'];
		
		$maxm = max($r['m'],$maxm);
		$maxp = max($r['p'],$maxp);
		$cols[$r['p']][$r['m']] = $c;
	}
	
	$content .= "<table cellspacing=10 style=\"background:#000000;\">";
	
	for($p = 1; $p <= $maxp; $p++) {
		$content .= "<tr>";
		for($m = 0; $m <= $maxm; $m++) {
			
			$class = "";
			
			if (!$cols[$p][$m]) {
				$content .= "<td class='planetlistnone' ></td>";
			} else {
				$c = $cols[$p][$m];
				
				if ($cols[$p][$m]['user_id'] == 1) $class = "free";
				if ($cols[$p][$m]['user_id'] != 1) $class = "taken";
				if ($cols[$p][$m]['id'] == $_GET['id']) $class = "this";
				if ($cols[$p][$m]['research_id'] != 0) $class = "impossible";

			
				if ($c['colonies_classes_id'] < 300) {
					
					if ($c['colonies_classes_id'] == 231 || $c['colonies_classes_id'] == 232 || $c['colonies_classes_id'] == 233)
						$ring = "<img src=\"../../gfx/planets/".$c['colonies_classes_id']."r.png\"/>";
					else
						$ring = "";
					$planpic = "<div style=\"width:120px;height:50px;background-repeat: no-repeat;background-position: center;background-image: url('../../gfx/planets/".$c['colonies_classes_id'].".gif'); background-size: 50px 50px;\">".$ring."</div>";	
				}
				else {
					$planpic = "<div style=\"width:60px;height:30px;background-repeat: no-repeat;background-position: center;background-image: url('../../gfx/planets/".$c['colonies_classes_id'].".gif'); background-size: 24px; 24px;\"></div>";						
				}
				$plan = "<div style=\"text-align:center;border:1px solid #ff0000;\">".$c['n']." : ".$c['cname']."<br>".$planpic."<br>".$c['sx']."</div>";				
				$content .= "<td class='planetlist".$class."' onmouseover=\"showColonyShortInfo(this,".$c['id'].",".$_GET['id'].");\" onmouseout=\"hideInfo();\">".$planpic."</td>";
			}
		}
		$content .= "</tr>";
	}
	
	
	
	
	$content .= "</table>";


	
	
	
	
	
	
	
	echo "<div style=\"text-align:left;\">";
	echo floatingPanel(3,"Planeten in diesem System","plist",$gfx."/buttons/icon/planet.gif",$content,1);	
	echo "</div>";

?>
