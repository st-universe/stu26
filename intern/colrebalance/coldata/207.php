<?php
	$data[details] = "Klasse X - Lava";

	$data[sizew] = 10;
	$data[sizeh] = 6;

	$hasground = 1;
	
	$data[basefield] = 200;
	$odata[basefield] = 100;
	$udata[basefield] = 81;

	$phases = 0;
	$ophases = 0;
	$uphases = 0;
		

	
	// config
	
	$eisn    = rand(3,4);
	$eiss    = ($eisn == 4 ? 3 : rand(3,4));
	
	$berge   = rand(12,16);
	$vulkane = rand(8,10);
	
	$vulk1 = 3;
	$vulk2 = rand(0,1);
	$vulk4 = 1 - $vulk2;
	
	$felsf   = rand(6,8);
	
	
	
	$streams = 1 * $vulk1 + 2 * $vulk2 + 4 * $vulk4;
	$streamfields = rand(10,14);
	$remstreams = $streamfields;
	
	$streamarray = array();
	for($i = 0; $i < $streams; $i++) {
		$remstreams--;
		$streamarray[$i] = 1;
	}

	$k = 0;
	while(($remstreams > 0) && ($k < 25)) {
		$k++;
		$selected = rand(0,count($streamarray)-1);
		if ($streamarray[$selected] < 5) {
			$remstreams--;
			$streamarray[$selected]++;
		}
	}
	rsort($streamarray);
	// print_r($streamarray);
		
	
	$uwasser 	= 0;
	$uerz	 	= rand(4,5);
	$umagma	 	= rand(4,5);

	// Surface Phases
	

	
	$phase[$phases][mode] = "nocluster";
	$phase[$phases][description] = "Vulkane";
	$phase[$phases][num] = $vulkane;
	$phase[$phases][from] = array("200");
	$phase[$phases][to]   = array("202");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(202);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;	
	
	$phase[$phases][mode] = "strict polar";
	$phase[$phases][description] = "Polar block";
	$phase[$phases][num] = 20;
	$phase[$phases][from] = array("202");
	$phase[$phases][to]   = array("301");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;	
	

	
	$phase[$phases][mode] = "large noadjacent";
	$phase[$phases][description] = "Vulkan Aktivierung 4";
	$phase[$phases][num] = $vulk4;
	$phase[$phases][from] = array("202");
	$phase[$phases][to]   = array("217");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;	
		
	$phase[$phases][mode] = "large noadjacent";
	$phase[$phases][description] = "Vulkan Aktivierung 2";
	$phase[$phases][num] = $vulk2;
	$phase[$phases][from] = array("202","202");
	$phase[$phases][to]   = array("215","216");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(217,216,215);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;	
	
	$phase[$phases][mode] = "large noadjacent";
	$phase[$phases][description] = "Vulkan Aktivierung 1";
	$phase[$phases][num] = $vulk1;
	$phase[$phases][from] = array("202","202","202","202");
	$phase[$phases][to]   = array("211","212","213","214");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = array(217,216,215,214,213,212,211);
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;	
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "block reverse";
	$phase[$phases][num] = 20;
	$phase[$phases][from] = array("301");
	$phase[$phases][to]   = array("202");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 0;	
	$phases++;
	
	$phase[$phases][mode] = "river";
	$phase[$phases][description] = "forced Lavastrom";
	$phase[$phases][num] = 100;
	$phase[$phases][from] 			= array("200","200","200","200","200","200","200",  "200","200","200","200","200");
	$phase[$phases][to]   			= array( "16", "16", "16", "16", "16", "16", "16",   "16", "16", "16", "16", "16");
	$phase[$phases][adjacent] 		= array("212","214","216","216","217","217","215",  "211","213","215","217","217");
	$phase[$phases][adjacencydir] 	= array( "ea", "we", "ea", "we", "ea", "we", "ea",   "so", "no", "so", "so", "no");
	$phase[$phases][noadjacent] 	 = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] 	 = 0;	
	$phases++;		
	
	
	
	
	for ($i = 0; $i < count($streamarray); $i++) {
	// for ($i = 0; $i < 1; $i++) {
	
		$phase[$phases][mode] = "normal";
		$phase[$phases][description] = "draw 1";
		$phase[$phases][num] = 1;
		$phase[$phases][from] = array( "16");
		$phase[$phases][to]   = array("301");
		$phase[$phases][adjacent] = 0;
		$phase[$phases][noadjacent] = 0;
		$phase[$phases][noadjacentlimit] = 0;	
		$phase[$phases][fragmentation] = 0;	
		$phases++;
	
		for ($j = 1; $j < $streamarray[$i]; $j++) {
			$phase[$phases][mode] = "forced adjacency";
			$phase[$phases][description] = "draw ".($i+1);
			$phase[$phases][num] = 1;
			$phase[$phases][from] = array("200");
			$phase[$phases][to]   = array(300 + $j + 1);
			$phase[$phases][adjacent] = array(300 + $j);;
			$phase[$phases][noadjacent] = 0;
			$phase[$phases][noadjacentlimit] = 0;	
			$phase[$phases][fragmentation] = 0;	
			$phases++;
		}
		
		$phase[$phases][mode] = "connecting";
		$phase[$phases][description] = "lava stream connecting transform";
		$phase[$phases][to]   	= array("221","222","223","224","225","226","231","233","235","237");
		$phase[$phases][origin] = array("211","212","213","214","215","216","217");
		$phases++;
	}
	
	
	// $phase[$phases][mode] = "normal";
	// $phase[$phases][description] = "lavaseen";
	// $phase[$phases][num] = 2;
	// $phase[$phases][from] = array("241","242","243","244");
	// $phase[$phases][to]   = array("245","246","247","248");
	// $phase[$phases][adjacent] = 0;
	// $phase[$phases][noadjacent] = 0;
	// $phase[$phases][noadjacentlimit] = 0;	
	// $phase[$phases][fragmentation] = 0;	
	// $phases++;
	
	
	// $phase[$phases][mode] = "river";
	// $phase[$phases][description] = "Lavastrom";
	// $phase[$phases][num] = 10;
	// $phase[$phases][from] 			= array("200","200","200","200");
	// $phase[$phases][to]   			= array( "16", "16", "16", "16");
	// $phase[$phases][adjacent] 		= array( "16", "16", "16", "16");
	// $phase[$phases][adjacencydir] 	= array( "we", "ea", "no", "so");
	// $phase[$phases][noadjacent] 	 = 0;
	// $phase[$phases][noadjacentlimit] = 0;	
	// $phase[$phases][fragmentation] 	 = 0;	
	// $phases++;	

	
	// $phase[$phases][mode] = "river";
	// $phase[$phases][description] = "Lavastrom";
	// $phase[$phases][num] = 15;
	// $phase[$phases][from] 			= array("200","200","200","200","200","200","200","200",  "200","200","200","200","200","200","200","200","200","200","200","200",  "200","200","200","200","200","200","200","200","200","200","200");
	// $phase[$phases][to]   			= array("223","224","225","226","223","224","225","226",  "221","221","221","221","221","221","221","221","221","221","221","221",  "222","222","222","222","222","222","222","222","222","222","222");
	// $phase[$phases][adjacent] 		= array("221","221","221","221","222","222","222","222",  "212","214","216","216","217","217","221","221","223","224","225","226",  "211","213","215","217","217","222","222","223","224","225","226");
	// $phase[$phases][adjacencydir] 	= array( "we", "we", "ea", "ea", "no", "so", "so", "no",   "ea", "we", "ea", "we", "ea", "we", "ea", "we", "ea", "ea", "we", "we",   "so", "no", "so", "so", "no", "so", "no", "so", "no", "no", "so");
	// $phase[$phases][noadjacent] 	 = 0;
	// $phase[$phases][noadjacentlimit] = 0;	
	// $phase[$phases][fragmentation] 	 = 0;	
	// $phases++;	
	
	// $phase[$phases][mode] = "river";
	// $phase[$phases][description] = "Endstücke gerade";
	// $phase[$phases][num] = 100;
	// $phase[$phases][from] 			= array("221","221","222","222",	"221","221","222","222",	"221","221","222",	"221","222","222",	"221","221","222",	"221","222","222",	"221","222",	"221","221");
	// $phase[$phases][to]   			= array("232","236","234","238",	"232","236","234","238",	"232","236","234",	"232","234","238",	"232","236","238",	"236","234","238",	"232","234",	"238","234");
	// $phase[$phases][adjacent] 		= array("200","200","200","200",	"202","202","202","202",	"211","211","211",	"212","212","212",	"213","213","213",	"214","214","214",	"215","215",	"216","216");
	// $phase[$phases][adjacencydir] 	= array( "we", "ea", "no", "so",	 "we", "ea", "no", "so",	 "we", "ea", "no",	 "we", "no", "so",	 "we", "ea", "so",	 "ea", "no", "so",	 "we", "no",	 "so", "no");
	// $phase[$phases][noadjacent] 	 = 0;
	// $phase[$phases][noadjacentlimit] = 0;	
	// $phase[$phases][fragmentation] 	 = 0;	
	// $phases++;		
	
	// $phase[$phases][mode] = "river";
	// $phase[$phases][description] = "Endstücke krumm";
	// $phase[$phases][num] = 100;
	// $phase[$phases][from] 			= array("221","221","222","222",	"221","221","222","222",	"221","221","222",	"221","222","222",	"221","221","222",	"221","222","222",	"221","222",	"221","221");
	// $phase[$phases][to]   			= array("232","236","234","238",	"232","236","234","238",	"232","236","234",	"232","234","238",	"232","236","238",	"236","234","238",	"232","234",	"238","234");
	// $phase[$phases][adjacent] 		= array("200","200","200","200",	"202","202","202","202",	"211","211","211",	"212","212","212",	"213","213","213",	"214","214","214",	"215","215",	"216","216");
	// $phase[$phases][adjacencydir] 	= array( "we", "ea", "no", "so",	 "we", "ea", "no", "so",	 "we", "ea", "no",	 "we", "no", "so",	 "we", "ea", "so",	 "ea", "no", "so",	 "we", "no",	 "so", "no");
	// $phase[$phases][noadjacent] 	 = 0;
	// $phase[$phases][noadjacentlimit] = 0;	
	// $phase[$phases][fragmentation] 	 = 0;	
	// $phases++;		
	
	
	
	
	$phase[$phases][mode] = "normal";
	$phase[$phases][description] = "Berge";
	$phase[$phases][num] = $berge;
	$phase[$phases][from] = array("200");
	$phase[$phases][to]   = array("201");
	$phase[$phases][adjacent] = 0;
	$phase[$phases][noadjacent] = 0;
	$phase[$phases][noadjacentlimit] = 0;	
	$phase[$phases][fragmentation] = 10;	
	$phases++;
	
	
	
	
	
	
	
	// Orbit Phases
	
	// $ophase[$ophases][mode] = "lower orbit";
	// $ophase[$ophases][description] = "Lower Orbit";
	// $ophase[$ophases][num] = 10;
	// $ophase[$ophases][from] = array("0" => "100");
	// $ophase[$ophases][to]   = array("0" => "114");
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