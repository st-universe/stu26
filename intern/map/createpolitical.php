<?php
// header('Content-type: image/png'); 
// $debug = 1;
include_once("/var/www/st-universe.eu/inc/config.inc.php");
include_once($global_path."/class/db.class.php");

include_once($global_path."/starmap/factions.php");

include_once($global_path."/inc/func.inc.php");

$db = new db;

		
		$width = 5;
		$spacer = 1;		
		
// echo $global_path."gfx/starmap/faction1.png";
		
		$im = imagecreatefrompng($global_path."gfx/starmap/fulldesaturated.png");
		
		
		
function findPart($x,$y,$im) {
	
	// echo "<br>".(floor(($x-1) / 40)+1).(floor(($y-1) / 40)+1);
	return $im[(floor(($x-1) / 40)+1).(floor(($y-1) / 40)+1)];
	
	// switch ( (($x % 40)+1).(($y % 40)+1) ) {
		
				
		
		// default: return $im[11];
	// }
	
	
}
	
function dist($x1,$y1,$x2,$y2) {
	return sqrt( ($x1 - $x2)*($x1 - $x2) + ($y1 - $y2)*($y1 - $y2)  );
}
	
		// $qry = "SELECT * FROM  `stu_map` WHERE cx <= 40 AND cy <= 40;";
		// $result = $db->query("SELECT * FROM  `stu_map` WHERE 1;");
		
		
		
		$factionpic['black'] = imagecreatefrompng($global_path."gfx/starmap/black.png");
		$factionpic['1core'] = imagecreatefrompng($global_path."gfx/starmap/faction1-core.png");
		$factionpic['1held'] = imagecreatefrompng($global_path."gfx/starmap/faction1-held.png");
		$factionpic['1occupied'] = imagecreatefrompng($global_path."gfx/starmap/faction1-held.png");
		$factionpic['2core'] = imagecreatefrompng($global_path."gfx/starmap/faction2-core.png");
		$factionpic['2held'] = imagecreatefrompng($global_path."gfx/starmap/faction2-held.png");
		$factionpic['2occupied'] = imagecreatefrompng($global_path."gfx/starmap/faction2-held.png");
		$factionpic['3core'] = imagecreatefrompng($global_path."gfx/starmap/faction3-core.png");
		$factionpic['3held'] = imagecreatefrompng($global_path."gfx/starmap/faction3-held.png");
		$factionpic['3occupied'] = imagecreatefrompng($global_path."gfx/starmap/faction3-held.png");
		$factionpic['9fixed'] = imagecreatefrompng($global_path."gfx/starmap/faction9-fixed.png");
		
		
		$factionpic[7] = imagecreatefrompng($global_path."gfx/starmap/faction1_contested2.png");
		
		
		$facpic = imagecreatefrompng($global_path."gfx/starmap/faction1_contested.png");
		
		$fields = $db->query("SELECT * FROM stu_map as m LEFT JOIN stu_map_regions as r ON m.region = r.id;");
		
		$rrrq = $db->query("SELECT * FROM stu_map_regions;");
		
		
		$borderpic['u']  = imagecreatefrompng($global_path."gfx/starmap/borderu.png");
		$borderpic['d']  = imagecreatefrompng($global_path."gfx/starmap/borderd.png");
		$borderpic['l']  = imagecreatefrompng($global_path."gfx/starmap/borderl.png");
		$borderpic['r']  = imagecreatefrompng($global_path."gfx/starmap/borderr.png");
		$borderpic['x']  = imagecreatefrompng($global_path."gfx/starmap/border.png");
		
		
		
		$iconpic['core'] = imagecreatefrompng($global_path."gfx/icons/conflict-core.png");
		$iconpic['attack'] = imagecreatefrompng($global_path."gfx/icons/conflict-attack.png");
		$iconpic['clock'] = imagecreatefrompng($global_path."gfx/icons/conflict-clock.png");
		$iconpic['defend'] = imagecreatefrompng($global_path."gfx/icons/conflict-defend.png");
		$iconpic['occupied'] = imagecreatefrompng($global_path."gfx/icons/conflict-occupied.png");
		$iconpic['target'] = imagecreatefrompng($global_path."gfx/icons/conflict-target.png");
		
		
		$regions = array();
		$map = array();
		while($field = mysql_fetch_assoc($fields)) {		
			$map[$field['cx']][$field['cy']] = $field;
			array_push($regions,$field['region']);
			$regions = array_unique($regions);
			
		}
		$regions = array_values($regions);
		
		if ($debug) {
			echo "<br><br>Regions:";
			print_r($regions);			
		}		
		
		
		while($rr = mysql_fetch_assoc($rrrq)) {		
			$rrr[$rr['id']] = $rr;	
		}
		
		
		if ($debug) {
			echo "<br><br>RRR:";
			print_r($rrr);			
		}		
		
		
		
		
		
		
		
		for ($x = 1; $x <= 120; $x++) {
			for ($y = 1; $y <= 120; $y++) {
				
				$field = $map[$x][$y];
				// $fac = $field['faction'];
				if ($field['faction'] > 0 && $factionpic[$field['faction'].$field['status']]) imagecopyresampled ($im, $factionpic[$field['faction'].$field['status']], (($width+$spacer)*((($x-1))))+1, (($width+$spacer)*((($y-1))))+1 , 0 ,0 , $width , $width, 5 , 5);	
				
				
				
				
			}			
		}

		
		for ($x = 1; $x <= 120; $x++) {
			for ($y = 1; $y <= 120; $y++) {
				if ($map[$x][$y]['status'] == 'fixed') continue;

				if ($map[$x-1][$y]['region'] != $map[$x][$y]['region']) imagecopyresampled ($im, $borderpic['l'], (($width+$spacer)*((($x-1)))), (($width+$spacer)*((($y-1)))), 0 ,0 , $width+2 , $width+2, $width+2 , $width+2);	
				if ($map[$x+1][$y]['region'] != $map[$x][$y]['region']) imagecopyresampled ($im, $borderpic['r'], (($width+$spacer)*((($x-1)))), (($width+$spacer)*((($y-1)))), 0 ,0 , $width+2 , $width+2, $width+2 , $width+2);	
				if ($map[$x][$y-1]['region'] != $map[$x][$y]['region']) imagecopyresampled ($im, $borderpic['u'], (($width+$spacer)*((($x-1)))), (($width+$spacer)*((($y-1)))), 0 ,0 , $width+2 , $width+2, $width+2 , $width+2);	
				if ($map[$x][$y+1]['region'] != $map[$x][$y]['region']) imagecopyresampled ($im, $borderpic['d'], (($width+$spacer)*((($x-1)))), (($width+$spacer)*((($y-1)))), 0 ,0 , $width+2 , $width+2, $width+2 , $width+2);	
			}			
		}
		
		


		$quads = array();
		foreach($regions as $r) {
			$quads[$r] = array();
		}
		
		for ($x = 2; $x <= 119; $x++) {
			for ($y = 2; $y <= 119; $y++) {
				$region = $map[$x][$y]['region'];
				
				if ($map[$x-1][$y-1]['region'] == $region)
				if ($map[$x  ][$y-1]['region'] == $region)
				if ($map[$x+1][$y-1]['region'] == $region)
				if ($map[$x-1][$y  ]['region'] == $region)
					
				if ($map[$x+1][$y  ]['region'] == $region)
				if ($map[$x-1][$y+1]['region'] == $region)
				if ($map[$x  ][$y+1]['region'] == $region)
				if ($map[$x+1][$y+1]['region'] == $region)
						{
							$quad = array();
							$quad['x'] = $x;
							$quad['y'] = $y;
							
							array_push($quads[$region],$quad);
						}
			}			
		}		
		if ($debug) {
			echo "<br><br>Quads:";
			print_r($quads);			
		}
		
		$middles = array();		
		foreach($regions as $r) {
			$middles[$r]['x'] = 0;
			$middles[$r]['y'] = 0;
			$middles[$r]['c'] = 0;
		}
		
		for ($x = 1; $x <= 120; $x++) {
			for ($y = 1; $y <= 120; $y++) {
				// if ($map[$x][$y]['status'] == 'fixed') continue;

				$middles[$map[$x][$y]['region']]['x'] += $x;
				$middles[$map[$x][$y]['region']]['y'] += $y;
				$middles[$map[$x][$y]['region']]['c'] += 1;
			}			
		}
		
		foreach($regions as $r) {
			$middles[$r]['mx'] = round($middles[$r]['x'] / $middles[$r]['c']);
			$middles[$r]['my'] = round($middles[$r]['y'] / $middles[$r]['c']);
		}
		
		if ($debug) {
			echo "<br><br>Middles:";
			print_r($middles);			
		}

		
		foreach($regions as $rk => $r) {
			$mx = $middles[$r]['mx'];
			$my = $middles[$r]['my'];

			$best = -1;
			$dist = 100000;
			
			foreach($quads[$r] as $k => $q) {
				$d = dist($mx,$my,$q['x'],$q['y']);
				if ($d < $dist) {
					$dist = $d;
					$best = $k;
				}
				
			}
			
			$regionquad[$r] = $quads[$r][$best];
			
		}
		
		
		
		
		
		imagepng($im,$global_path."gfx/starmap/political/full.png");	
		// imagepng($im);	
		
		
		function isRelevant($region,$faction) {
			global $rrr;
			
			// echo "<br>Rel: ".$region."-".$faction."-".$rrr[$region]['faction'];
			
			$nbs = explode(",",$rrr[$region]['neighbours']);


			if ($rrr[$region]['status'] == "fixed") return false;
			if ($rrr[$region]['status'] == "objective") return false;
			
			if ($rrr[$region]['faction'] == $faction) return true;
			if ($rrr[$region]['attacker'] == $faction) return true;
			
			foreach($nbs as $n) {
				if ($rrr[$n]['faction'] == $faction) return true;
			}
			
			return false;
		}
		
		
		// function mapStatus($region,$race) {
			// $s = $region['status'];
			// if ($region['status'] == "core" && $region['attacker'] == 0) return "core";
			// if ($region['status'] == "core" && $region['attacker'] != 0) return "occupied";

			// if ($region['status'] == "free" && $region['attacker'] == 0) return "target";
			// if ($region['status'] == "free" && $region['attacker'] != 0) return "attack";

			// if ($region['status'] == "held" && $region['faction'] != $race) return "target";
			// if ($region['status'] == "held" && $region['faction'] == $race && $region['attacker'] != 0) return "attack";
			
			// if ($region['status'] == "occupied" && $region['faction'] != $race) return "target";
			// if ($region['status'] == "occupied" && $region['faction'] == $race && $region['attacker'] != 0) return "attack";			
			
			// if ($region['status'] == "contested" && $region['attacker'] != 0) return "attack";
			
			// return "defend";
		// }
		
		
		
		function facMap($race) {
			global $im, $global_path, $regionquad,$width,$spacer,$factionpic,$iconpic,$rrr;
			
			
			$factionmap = imagecreatetruecolor(721, 721);
			imagecopy($factionmap, $im, 0, 0, 0, 0, 721, 721);			
			
			
			foreach($regionquad as $r => $q) {
				
				if (isRelevant($r,$race)) {
				
					$x = $q['x'] - 1;
					$y = $q['y'] - 1;
					
					$tx = (($width+$spacer)*((($x-1)))) + 1;
					$ty = (($width+$spacer)*((($y-1)))) + 1;
					
					$sec = $rrr[$r];
					$status = sectorStateIcon(sectorStateString($sec['status'], $sec['counter'], $sec['faction'], $sec['status'], $race));
					
					
					imagecopyresampled ($factionmap, $factionpic['black'], $tx, $ty, 0 ,0 , 17, 17, 5, 5);	
					imagecopyresampled ($factionmap, $iconpic[$status], $tx, $ty, 0 ,0 , 17, 17, 30, 30);	
				}
			}
			
			imagepng($factionmap,$global_path."gfx/starmap/political/faction".$race.".png");
		}
		
		
		
		
		
		
		facMap(1);
		facMap(2);
		facMap(3);
		
		

		
		
		
		
		
		
		if ($debug) {
			echo "<br><br>RegionQuads:";
			print_r($regionquad);			
		}
		
		

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		

		
		
		// echo "</body>";
?>

