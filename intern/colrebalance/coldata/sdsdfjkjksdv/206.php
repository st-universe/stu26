<?php
	$data[details] = "Klasse H - Basisklasse Wüste";

	$data[sizew] = 9;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 7;
	$odata[basefield] = 100;
	$udata[basefield] = 79;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	
	// config
	
	$felsen = rand(28,32);
	$berge  = rand(10,14);
	$dünen  = rand(10,12);
	
	$erde   = rand(4,5);
	
	
	
	
	
	// Surface Phases
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Fels";
	$phase[$phases][num] = $felsen;
	$phase[$phases][from] = array("0" => "7");
	$phase[$phases][to]   = array("0" => "19");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;

	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("0" => "19");
	$phase[$phases][to]   = array("0" => "32");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 5;	
	$phases++;
	
	$phase[$phases][mode] = "forced adjacency";
	$phase[$phases][description] = "Dünen";
	$phase[$phases][num] = $dünen;
	$phase[$phases][from] = array( 7,19);
	$phase[$phases][to]   = array(48,49);
	$phase[$phases][adjacent] = array( 7, 48, 49);
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 50;	
	$phases++;	
	
	
	// Orbit Phases
	
	// $ophase[$ophases][mode] = "lower orbit";
	// $ophase[$ophases][description] = "Lower Orbit";
	// $ophase[$ophases][num] = 9;
	// $ophase[$ophases][from] = array("0" => "100");
	// $ophase[$ophases][to]   = array("0" => "113");
	// $ophase[$ophases][adjacent] = 0;
	// $ophase[$phases][noadjacent] = 0;
	// $ophase[$ophases][noadjacentlimit] = 0;	
	// $ophase[$ophases][fragmentation] = 2;	
	// $ophases++;	
	
	
	
	
	$uphase[$uphases][mode] = "normal";
	$uphase[$uphases][description] = "Erde";
	$uphase[$uphases][num] = $erde;
	$uphase[$uphases][from] = array(79);
	$uphase[$uphases][to]   = array(71);
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 15;	
	$uphases++;
	
	
	
	
	
?>