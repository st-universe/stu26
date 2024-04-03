<?php
	$data[details] = "Klasse X - Lava";

	$data[sizew] = 9;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 10;
	$odata[basefield] = 100;
	$udata[basefield] = 79;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	// config
	
	$eisn    = rand(3,4);
	$eiss    = ($eisn == 4 ? 3 : rand(3,4));
	
	$berge   = rand(12,16);
	$vulkane = rand(8,10);
	
	
	$felsf   = rand(6,8);
	


	
	$ufels   = rand(3,4);

	// Surface Phases
	

	
	
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("10");
	$phase[$phases][to]   = array("36");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;
	
	
	$phase[$phases][mode] = "nocluster";
	$phase[$phases][description] = "Krater";
	$phase[$phases][num] = $vulkane;
	$phase[$phases][from] = array("10");
	$phase[$phases][to]   = array("12");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;	
	
	
	// Orbit Phases
	
	$ophase[$ophases][mode] = "lower orbit";
	$ophase[$ophases][description] = "Lower Orbit";
	$ophase[$ophases][num] = 10;
	$ophase[$ophases][from] = array("0" => "100");
	$ophase[$ophases][to]   = array("0" => "116");
	$ophase[$ophases][adjacent] = 0;
	$ophase[$phases][noadjacent] = 0;
	$ophase[$ophases][noadjacentlimit] = 0;	
	$ophase[$ophases][fragmentation] = 2;	
	$ophases++;
	
	
	// Underground Phases
	
	// $uphase[$uphases][mode] = "normal";
	// $uphase[$uphases][description] = "Untergrundeis";
	// $uphase[$uphases][num] = $ueis;
	// $uphase[$uphases][from] = array("0" => "71");
	// $uphase[$uphases][to]   = array("0" => "74");
	// $uphase[$uphases][adjacent] = 0;
	// $uphase[$uphases][noadjacent] = 0;
	// $uphase[$uphases][noadjacentlimit] = 0;	
	// $uphase[$uphases][fragmentation] = 2;	
	// $uphases++;
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>