<?php
	$data[details] = "Klasse L - Basisklasse Wald";

	$data[sizew] = 10;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 1;
	$odata[basefield] = 100;
	$udata[basefield] = 81;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		
			
	

	// config
	
	$wasser  = rand(6,8);
	$berge   = rand(10,13);
	// $sumpf   = rand(5,7);
	$bäume   = rand(18,22);
	$jungle   = rand(7,9);
	
	$uwasser 	= rand(3,5);
	$uerz	 	= rand(5,6);
	$umagma	 	= 3;
	
	// Surface Phases

	// Surface Phases
	
	// $phase[$phases][mode] = "polar seeding north";
	// $phase[$phases][description] = "Polkappe N";
	// $phase[$phases][num] = 4;
	// $phase[$phases][from] = array("0" => "1");
	// $phase[$phases][to]   = array("0" => "18");
	// $phase[$phases][adjacent] = 0;
	// $phase[$phases][noadjacent] = 0;
	// $phase[$phases][noadjacentlimit] = 0;	
	// $phase[$phases][fragmentation] = 2;	
	// $phases++;

	// $phase[$phases][mode] = "polar seeding south";
	// $phase[$phases][description] = "Polkappe S";
	// $phase[$phases][num] = 4;
	// $phase[$phases][from] = array("0" => "1");
	// $phase[$phases][to]   = array("0" => "18");
	// $phase[$phases][adjacent] = 0;
	// $phase[$phases][noadjacent] = 0;
	// $phase[$phases][noadjacentlimit] = 0;	
	// $phase[$phases][fragmentation] = 2;	
	// $phases++;
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Wasserflächen";
	$phase[$phases][num] = $wasser;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "5");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(121);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 800;	
	$phases++;
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "31");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array("0" => "5");
	$phase[$phases][noadjacentlimit] = 1;	
	$phase[$phases][fragmentation] = 15;	
	$phases++;
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Bäume";
	$phase[$phases][num] = $bäume;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "2");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array("0" => "401");
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 20;	
	$phases++;

	$phase[$phases][mode] = "equatorial";
	$phase[$phases][description] = "Jungle seeding";
	$phase[$phases][num] = 2;
	$phase[$phases][from] = array("0" => "2");
	$phase[$phases][to]   = array("0" => "46");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array("0" => "401");
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 20;	
	$phases++;	
	
	// $phase[$phases][mode] = "forced adjacency";
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Jungle";
	$phase[$phases][num] = $jungle-2;
	$phase[$phases][from] = array("0" => "2");
	$phase[$phases][to]   = array("0" => "46");
	$phase[$phases][adjacent] = array("0" => "46");
	$phase[$phases][noadjacent] = array("0" => "401");
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;		
	
	// Orbit Phases
	
	// $ophase[$ophases][mode] = "lower orbit";
	// $ophase[$ophases][description] = "Lower Orbit";
	// $ophase[$ophases][num] = 9;
	// $ophase[$ophases][from] = array("0" => "100");
	// $ophase[$ophases][to]   = array("0" => "110");
	// $ophase[$ophases][adjacent] = 0;
	// $ophase[$phases][noadjacent] = 0;
	// $ophase[$ophases][noadjacentlimit] = 0;	
	// $ophase[$ophases][fragmentation] = 2;	
	// $ophases++;
	
	
	// Underground Phases
	
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