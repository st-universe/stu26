<?php
	$data[details] = "Klasse L - Basisklasse Wald";

	$data[sizew] = 9;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 1;
	$odata[basefield] = 100;
	$udata[basefield] = 79;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		
			
	

	// config
	
	$wasser  = rand(8,11);
	$berge   = rand(5,6);
	// $sumpf   = rand(5,7);
	$bäume   = rand(15,22);
	
	$uerde   = rand(4,7);
	
	
	// Surface Phases

	// Surface Phases
	
	$phase[$phases][mode] = "polar seeding north";
	$phase[$phases][description] = "Polkappe N";
	$phase[$phases][num] = 4;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "18");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 2;	
	$phases++;

	$phase[$phases][mode] = "polar seeding south";
	$phase[$phases][description] = "Polkappe S";
	$phase[$phases][num] = 4;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "18");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 2;	
	$phases++;
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Wasserflächen";
	$phase[$phases][num] = $wasser;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "5");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(121);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 8;	
	$phases++;
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "31");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array("0" => "5");
	$phase[$phases][noadjacentlimit] = 1;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Bäume";
	$phase[$phases][num] = $bäume;
	$phase[$phases][from] = array("0" => "1", "1" => "18");
	$phase[$phases][to]   = array("0" => "2", "1" => "3");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array("0" => "401");
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 12;	
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
	$uphase[$uphases][from] = array("0" => "79");
	$uphase[$uphases][to]   = array("0" => "73");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 2;	
	$uphases++;
	
	$uphase[$uphases][mode] = "normal";
	$uphase[$uphases][description] = "Untergrundfels";
	$uphase[$uphases][num] = $uerde;
	$uphase[$uphases][from] = array("0" => "79");
	$uphase[$uphases][to]   = array("0" => "71");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 10;	
	$uphases++;	
	
	
	
	
	
	
	
	

	
?>