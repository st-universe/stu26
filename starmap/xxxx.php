<?php
header("Content-Type: text/html; charset=iso-8859-1");


$csv = array_map('str_getcsv', file("witch.csv"));









	foreach($csv as $c) {
		switch($c[2].$c[3].$c[4].$c[5]) {
			case "1000": $fac = "0"; break;
			case "0100": $fac = "1"; break;
			case "0010": $fac = "2"; break;
			case "0001": $fac = "3"; break;
		}

		echo "<br>#faction[".$c[0]."][".$c[1]."] = ".$fac.";";
	}


	// print_r($csv);






	
?>