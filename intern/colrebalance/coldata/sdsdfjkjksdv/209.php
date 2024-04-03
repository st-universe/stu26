<?php
	$data[details] = "Klasse P - Basisklasse Eis";

	$data[sizew] = 9;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 20;
	$odata[basefield] = 100;
	$udata[basefield] = 79;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	// config
	
	$land   = rand(42,45);
	$berge  = rand(9,12);
	$eisf   = rand(5,8);
	
	// $uerde  = rand(4,8);
	$ueis   = rand(6,8);
	
	
	
	
	
	// Surface Phases
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Landmassen";
	$phase[$phases][num] = $land;
	$phase[$phases][from] = array(20);
	$phase[$phases][to]   = array(6);
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;

	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("0" => "6");
	$phase[$phases][to]   = array("0" => "33");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(221);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 15;	
	$phases++;
	
	$phase[$phases][mode] = "nocluster";
	$phase[$phases][description] = "Eisformation";
	$phase[$phases][num] = $eisf;
	$phase[$phases][from] = array(6);
	$phase[$phases][to]   = array(14);
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;
	
	
	
	
	// Orbit Phases
	
	// $ophase[$ophases][mode] = "lower orbit";
	// $ophase[$ophases][description] = "Lower Orbit";
	// $ophase[$ophases][num] = 9;
	// $ophase[$ophases][from] = array("0" => "100");
	// $ophase[$ophases][to]   = array("0" => "112");
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
	$uphase[$uphases][description] = "Untergrundeis";
	$uphase[$uphases][num] = $ueis;
	$uphase[$uphases][from] = array("0" => "79");
	$uphase[$uphases][to]   = array("0" => "74");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 10;	
	$uphases++;	
	
	
	
?>