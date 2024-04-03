<?php
	$data[details] = "Tundra";

	$data[sizew] = 9;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 18;
	$odata[basefield] = 100;
	$udata[basefield] = 71;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	// config
	
	
	$eisn    = rand(8,9);
	$eiss    = rand(8,9);
	
	$berge   = rand(8,12);
	$wüste   = rand(8,12);
	$felsf   = rand(6,8);
	$trees = rand(7,8);
	
	$ufels   = rand(6,10);
	$ueis = 0;
	
	
	// Surface Phases
	
	$phase[$phases][mode] = "polar seeding north";
	$phase[$phases][description] = "Polkappe N";
	$phase[$phases][num] = $eisn;
	$phase[$phases][from] = array("0" => "18");
	$phase[$phases][to]   = array("0" => "6");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 2;	
	$phases++;

	$phase[$phases][mode] = "polar seeding south";
	$phase[$phases][description] = "Polkappe S";
	$phase[$phases][num] = $eiss;
	$phase[$phases][from] = array("0" => "18");
	$phase[$phases][to]   = array("0" => "6");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 2;	
	$phases++;
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("0" => "18");
	$phase[$phases][to]   = array("0" => "31");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 20;	
	$phases++;
	
	$phase[$phases][mode] = "equatorial";
	$phase[$phases][description] = "Tree Seed";
	$phase[$phases][num] = 2;
	$phase[$phases][from] = array("0" => "18");
	$phase[$phases][to]   = array("0" => "3");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array("0" => "5");
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 25;	
	$phases++;

	$phase[$phases][mode] = "forced adjacency";
	$phase[$phases][description] = "Trees";
	$phase[$phases][num] = $trees;
	$phase[$phases][from] = array("0" => "18");
	$phase[$phases][to]   = array("0" => "3");
	$phase[$phases][adjacent] = array("0" => "18");
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 25;	
	$phases++;

	
	
	
	// Orbit Phases
	
	// $ophase[$ophases][mode] = "lower orbit";
	// $ophase[$ophases][description] = "Lower Orbit";
	// $ophase[$ophases][num] = 10;
	// $ophase[$ophases][from] = array("0" => "100");
	// $ophase[$ophases][to]   = array("0" => "111");
	// $ophase[$ophases][adjacent] = 0;
	// $ophase[$phases][noadjacent] = 0;
	// $ophase[$ophases][noadjacentlimit] = 0;	
	// $ophase[$ophases][fragmentation] = 2;	
	// $ophases++;
	
	
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
	
	$uphase[$uphases][mode] = "normal";
	$uphase[$uphases][description] = "Untergrundfels";
	$uphase[$uphases][num] = $ufels;
	$uphase[$uphases][from] = array("0" => "71");
	$uphase[$uphases][to]   = array("0" => "79");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 10;	
	$uphases++;	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>