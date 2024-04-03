<?php
	$data[details] = "Klasse X - Lava";

	$data[sizew] = 9;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 11;
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
	

	
	$phase[$phases][mode] = "nocluster";
	$phase[$phases][description] = "Vulkane";
	$phase[$phases][num] = $vulkane;
	$phase[$phases][from] = array("11");
	$phase[$phases][to]   = array("28");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(28);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;	
	
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("11");
	$phase[$phases][to]   = array("35");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;
	
	$phase[$phases][mode] = "nocluster";
	$phase[$phases][description] = "Vulkan aktivierung";
	$phase[$phases][num] = 2;
	$phase[$phases][from] = array("28");
	$phase[$phases][to]   = array("17");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(202);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;		
	
	
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Krater";
	$phase[$phases][num] = rand(3,6);
	$phase[$phases][from] = array("11");
	$phase[$phases][to]   = array("13");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;	
	
	
	// Orbit Phases

	
	
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
	$uphase[$uphases][description] = "Magma";
	$uphase[$uphases][num] = $ufels;
	$uphase[$uphases][from] = array("0" => "79");
	$uphase[$uphases][to]   = array("0" => "76");
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 10;	
	$uphases++;	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>