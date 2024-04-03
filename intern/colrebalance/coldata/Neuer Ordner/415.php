<?php
	$data[details] = "Klasse P - Basisklasse Eis";

	$bonusdata = array(BONUS_WENER,BONUS_WENER,BONUS_DEUT,BONUS_WFOOD,BONUS_WFOOD);
	
	$data[sizew] = 7;
	$data[sizeh] = 5;

	$hasground = 0;
	
	$data[basefield] = 221;
	$odata[basefield] = 900;
	$udata[basefield] = 802;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	// config
	
	$land   = rand(29,30);
	$berge  = rand(7,10);
	$eisf   = rand(3,5);
	
	$uerde  = rand(4,8);
	$ueis   = rand(4,5);
	
	
	
	
	
	// Surface Phases
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Landmassen";
	$phase[$phases][num] = $land;
	$phase[$phases][from] = array(221);
	$phase[$phases][to]   = array(501);
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 1;	
	$phases++;

	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("0" => "501");
	$phase[$phases][to]   = array("0" => "704");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array();
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 15;	
	$phases++;
	
	$phase[$phases][mode] = "nocluster";
	$phase[$phases][description] = "Eisformation";
	$phase[$phases][num] = $eisf;
	$phase[$phases][from] = array(501);
	$phase[$phases][to]   = array(511);
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;
	
	
	
	
	
	$uphase[$uphases][mode] = "normal";
	$uphase[$uphases][description] = "Erde";
	$uphase[$uphases][num] = $uerde;
	$uphase[$uphases][from] = array(802);
	$uphase[$uphases][to]   = array(801);
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 15;	
	$uphases++;
	
	$uphase[$uphases][mode] = "normal";
	$uphase[$uphases][description] = "Eis";
	$uphase[$uphases][num] = $ueis;
	$uphase[$uphases][from] = array(802);
	$uphase[$uphases][to]   = array(821);
	$uphase[$uphases][adjacent] = 0;
	$uphase[$uphases][noadjacent] = 0;
	$uphase[$uphases][noadjacentlimit] = 0;	
	$uphase[$uphases][fragmentation] = 25;	
	$uphases++;
	
	
	
?>