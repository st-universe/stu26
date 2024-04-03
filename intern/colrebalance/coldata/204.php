<?php
	$data[details] = "Klasse H - Basisklasse �dland";

	$data[sizew] = 10;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 9;
	$odata[basefield] = 100;
	$udata[basefield] = 81;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	// config
	
	$eisn    = rand(4,5);
	$eiss    = rand(4,5);
	
	$berge   = rand(13,16);
	$w�ste   = rand(8,12);
	$felsf   = rand(6,8);
	
	
	$uwasser 	= 0;
	$uerz	 	= rand(4,5);
	$umagma	 	= 3;
	
	
	// Surface Phases
	
	$phase[$phases][mode] = "polar seeding north";
	$phase[$phases][description] = "Polkappe N";
	$phase[$phases][num] = $eisn;
	$phase[$phases][from] = array("0" => "9");
	$phase[$phases][to]   = array("0" => "6");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 2;	
	$phases++;

	$phase[$phases][mode] = "polar seeding south";
	$phase[$phases][description] = "Polkappe S";
	$phase[$phases][num] = $eiss;
	$phase[$phases][from] = array("0" => "9");
	$phase[$phases][to]   = array("0" => "6");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 2;	
	$phases++;
	
	$phase[$phases][mode] = "equatorial";
	$phase[$phases][description] = "W�sten";
	$phase[$phases][num] = $w�ste;
	$phase[$phases][from] = array("0" => "9");
	$phase[$phases][to]   = array("0" => "8");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array("0" => "5");
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 12;	
	$phases++;

	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("0" => "9");
	$phase[$phases][to]   = array("0" => "34");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 20;	
	$phases++;
	



	$phase[$phases][mode] = "nocluster";
	$phase[$phases][description] = "Felsformation";
	$phase[$phases][num] = $felsf;
	$phase[$phases][from] = array("0" => "9");
	$phase[$phases][to]   = array("0" => "15");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array();
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
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