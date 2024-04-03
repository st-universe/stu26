<?php
	include("204.php");
	
	$data[sizew] = 7;
	$data[sizeh] = 5;
	$hasground = 0;
	
	foreach($phase as $k=> $v) {
		$phase[$k][num] = 0.66 * $phase[$k][num];
	}
?>