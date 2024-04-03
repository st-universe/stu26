<?php
	$data[details] = "Klasse O - Basisklasse Ozean";

	$data[sizew] = 10;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 40;
	$odata[basefield] = 100;
	$udata[basefield] = 81;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	// config
	
	$schelf  = rand(48,50);
	$land    = rand(25,27);
	$berge   = rand(6,7);
	$korall  = rand(3,4);
	$seicht  = rand(6,8);
	$bäume   = rand(8,10);
	
	
	$uwasser 	= rand(3,4);
	$uerz	 	= rand(5,6);
	$umagma	 	= 3;
	
	
	//Surface Phases
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Seeding";
	$phase[$phases][num] = $schelf ;
	$phase[$phases][from] = array("0" => "40");
	$phase[$phases][to]   = array("0" => "41");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 1;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;

	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "raise land";
	$phase[$phases][num] = $land;
	$phase[$phases][from] = array("0" => "41");
	$phase[$phases][to]   = array("0" => "1");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array("0" => "40");
	$phase[$phases][noadjacentlimit] = 1;	
	$phase[$phases][fragmentation] = 212;	
	$phases++;
	

	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Korallen";
	$phase[$phases][num] = $korall;
	$phase[$phases][from] = array("0" => "41");
	$phase[$phases][to]   = array("0" => "42");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 1;	
	$phase[$phases][fragmentation] = 250000;	
	$phases++;
	

	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "31");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 1;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;
	

	
	
	
	
	
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