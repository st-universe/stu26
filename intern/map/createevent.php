<?php
// header('Content-type: image/png'); 
include_once("/var/www/st-universe.eu/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

		
		$width = 5;
		$spacer = 1;		
		

		
		$im = imagecreatefrompng("/var/www/st-universe.eu/gfx/starmap/fulldesaturated.png");
		
		
		
function findPart($x,$y,$im) {
	
	// echo "<br>".(floor(($x-1) / 40)+1).(floor(($y-1) / 40)+1);
	return $im[(floor(($x-1) / 40)+1).(floor(($y-1) / 40)+1)];
	
	// switch ( (($x % 40)+1).(($y % 40)+1) ) {
		
				
		
		// default: return $im[11];
	// }
	
	
}
		
		$result = $db->query("SELECT * FROM `stu_history` WHERE type = 1 AND coords_x > 0 AND date > (NOW() - INTERVAL 7 DAY);");
		
		
		$c[1][1] = imagecreatefrompng("/var/www/st-universe.eu/gfx/starmap/red1.png");
		$c[1][2] = imagecreatefrompng("/var/www/st-universe.eu/gfx/starmap/red2.png");
		$c[1][3] = imagecreatefrompng("/var/www/st-universe.eu/gfx/starmap/red3.png");
		$c[2][1] = imagecreatefrompng("/var/www/st-universe.eu/gfx/starmap/yellow1.png");
		$c[2][2] = imagecreatefrompng("/var/www/st-universe.eu/gfx/starmap/yellow2.png");
		$c[2][3] = imagecreatefrompng("/var/www/st-universe.eu/gfx/starmap/yellow3.png");
		
		$evs = array();
		while ($ev = mysql_fetch_assoc($result)) {
		
			$evs[$ev[coords_x]][$ev[coords_y]][$ev[color]]++;
		}
		for ($x = 1; $x <= 120; $x++) {
			
			for ($y = 1; $y <= 120; $y++) {
				


				$color = 0;
				if ($evs[$x][$y][1] > 0) $color = 1;
				else if ($evs[$x][$y][2] > 0) $color = 2;
				
				$severity = 0;
				if ($evs[$x][$y][$color] > 0) $severity++;
				if ($evs[$x][$y][$color] > 3) $severity++;
				if ($evs[$x][$y][$color] > 5) $severity++;
			
				if ($severity > 0) {					
					imagecopyresampled ($im, $c[$color][$severity], (($width+$spacer)*((($x-1))))+1, (($width+$spacer)*((($y-1))))+1 , 0 ,0 , $width , $width, 5 , 5);
					
				}
			}

			
		}
		
		

		imagedestroy($c[1][1]);
		imagedestroy($c[1][2]);
		imagedestroy($c[1][3]);
		imagedestroy($c[2][1]);
		imagedestroy($c[2][2]);
		imagedestroy($c[2][3]);		


			imagepng($im,"/var/www/st-universe.eu/gfx/starmap/event/full.png");	
			// imagepng($im);	
			imagedestroy($im);	
		
		
		
		// echo "</body>";
?>

