<?php
	include("209.php");
	
	$data[sizew] = 7;
	$data[sizeh] = 5;
	$hasground = 1;
	
	foreach($phase as $k=> $v) {
		$phase[$k][num] = 0.55 * $phase[$k][num];
	}
	
	// Underground Phases
	unset($uphase);
	$uphases = 0;
	
	$uwasser 	= rand(1,2);
	$uerz	 	= rand(2,3);
	$umagma	 	= 1;
	
	$uphase[$uphases][mode] = "normal";
	$uphase[$uphases][description] = "Untergrundwasser";
	$uphase[$uphases][num] = $uwasser;
	$uphase[$uphases][from] = array("0" => "81");
	$uphase[$uphases][to]   = array("0" => "84");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 1000;	
	$uphases++;
	
	$uphase[$uphases][mode] = "normal";
	$uphase[$uphases][description] = "Untergrundfels";
	$uphase[$uphases][num] = $uerz;
	$uphase[$uphases][from] = array("0" => "81");
	$uphase[$uphases][to]   = array("0" => "82");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 1000;	
	$uphases++;	
	
	$uphase[$uphases][mode] = "normal";
	$uphase[$uphases][description] = "Untergrundmagma";
	$uphase[$uphases][num] = $umagma;
	$uphase[$uphases][from] = array("0" => "81");
	$uphase[$uphases][to]   = array("0" => "83");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 1000;	
	$uphases++;	
	
		
?>