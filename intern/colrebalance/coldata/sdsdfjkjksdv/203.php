<?php
	$data[details] = "Klasse O - Basisklasse Ozean";

	$data[sizew] = 9;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 5;
	$odata[basefield] = 100;
	$udata[basefield] = 79;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	// config
	
	$land    = rand(28,32);
	$berge   = rand(5,6);
	$korall  = rand(3,4);
	$seicht  = rand(6,8);
	$bäume   = rand(8,10);
	
	
	$ufels   = rand(4,7);
	$uwasser = 5;
	
	
	// Surface Phases
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Korallen";
	$phase[$phases][num] = $korall;
	$phase[$phases][from] = array("0" => "5");
	$phase[$phases][to]   = array("0" => "65");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 1;	
	$phase[$phases][fragmentation] = 250000;	
	$phases++;
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Landmassen";
	$phase[$phases][num] = $land;
	$phase[$phases][from] = array("0" => "5");
	$phase[$phases][to]   = array("0" => "1");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(65);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 100;	
	$phases++;

	$phase[$phases][mode] = "forced adjacency";
	$phase[$phases][description] = "Seichtes Wasser";
	$phase[$phases][num] = $seicht;
	$phase[$phases][from] = array("0" => "5");
	$phase[$phases][to]   = array("0" => "4");
	$phase[$phases][adjacent] = array(1);
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 200;	
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
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Bäume";
	$phase[$phases][num] = $bäume;
	$phase[$phases][from] = array("0" => "1");
	$phase[$phases][to]   = array("0" => "2");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
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
	$uphase[$uphases][num] = $ufels;
	$uphase[$uphases][from] = array("0" => "79");
	$uphase[$uphases][to]   = array("0" => "71");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 10;	
	$uphases++;	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>