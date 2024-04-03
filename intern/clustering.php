<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	echo "<body bgcolor=black><font color=#5050aa>";

function pc($i) {
	return (($i-1)*10 + 5);
}

function arrToIn($arr,$add = 999) {
	$res = "systems_id NOT IN (";
	foreach ($arr as $d) {
		$res .= $d['systems_id'].", ";
	}
	$res .= $add.") ";
	return $res;
}
function arrToInY($arr,$add = 999) {
	$res = "systems_id IN (";
	foreach ($arr as $d) {
		$res .= $d['systems_id'].", ";
	}
	$res .= $add.") ";
	return $res;
}
function raceColor($i) {
	switch($i) {
		case 1: return "#197DCB";
		case 2: return "#107D08";
		case 3: return "#D01412";
		case 9: return "#ffff00";
		default: return "#555555";
	}
	
}


function dist($a,$b) {
	global $systems;
	return ( (($systems[$a]['cx']-$systems[$b]['cx'])*($systems[$a]['cx']-$systems[$b]['cx'])) + (($systems[$a]['cy']-$systems[$b]['cy'])*($systems[$a]['cy']-$systems[$b]['cy'])) );
}
function fdist($a,$b) {
	global $systems;
	return ( (($a['cx']-$systems[$b]['cx'])*($a['cx']-$systems[$b]['cx'])) + (($a['cy']-$systems[$b]['cy'])*($a['cy']-$systems[$b]['cy'])) );
}
function weight($node,$arr) {
	global $systems;
	
	$w = 0;
	foreach($arr as $k) {
		if ($node == $k) continue;
		$w += dist($node,$k);
	}
	return $w;
}
function lowest($arr) {
	global $systems;
	$scores = array();
	
	foreach($arr as $n) {
		$scores[$n] = weight($n,$arr);
	}
	asort($scores);
	// print_r($scores);
	reset($scores);
	return key($scores);
}
function nearest($node,$arr) {
	global $systems;
	foreach($arr as $n) {
		$scores[$n] = dist($node,$n);
	}
	asort($scores);
	reset($scores);
	return key($scores);
}
function fnearest($node,$arr) {
	global $systems;
	foreach($arr as $n) {
		$scores[$n] = fdist($node,$n);
	}
	asort($scores);
	reset($scores);
	return key($scores);
}




	$allsystems = array();
	$systems = array();

	$removed = array();

	$head = array();
	$fixedpositions = array();
	
	$pos['race'] = 9;
	$pos['cx'] = 60;
	$pos['cy'] = 60;
	$pos['bag'] = 5;
	array_push($fixedpositions,$pos);
	
	$pos['race'] = 1;
	$pos['cx'] = 30;
	$pos['cy'] = 65;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);
	
	$pos['race'] = 1;
	$pos['cx'] = 20;
	$pos['cy'] = 30;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);
	
	$pos['race'] = 1;
	$pos['cx'] = 15;
	$pos['cy'] = 105;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);	
	
	$pos['race'] = 2;
	$pos['cx'] = 80;
	$pos['cy'] = 40;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);
	
	$pos['race'] = 2;
	$pos['cx'] = 70;
	$pos['cy'] = 20;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);
	
	$pos['race'] = 2;
	$pos['cx'] = 100;
	$pos['cy'] = 60;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);	
	
	$pos['race'] = 3;
	$pos['cx'] = 70;
	$pos['cy'] = 90;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);
	
	$pos['race'] = 3;
	$pos['cx'] = 105;
	$pos['cy'] = 105;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);
	
	$pos['race'] = 3;
	$pos['cx'] = 50;
	$pos['cy'] = 105;
	$pos['bag'] = 3;
	array_push($fixedpositions,$pos);	
	
	
	
	$khead = array();
	
	
	
	
	$res = $db->query("SELECT * FROM stu_systems WHERE ".arrToIn($removed)." order by systems_id asc");

	while($data=mysql_fetch_assoc($res))
	{
		unset($sys);
		$allsystems[$data['systems_id']] = $data;
		$sys = $data;
		$nrst = $db->query("SELECT * FROM stu_systems WHERE ".arrToIn($removed,$data['systems_id'])." order by ( ((cx - ".$data['cx'].")*(cx - ".$data['cx'].")) + ((cy - ".$data['cy'].")*(cy - ".$data['cy'].")) ) asc LIMIT 1;",4);
		
		$sys['nearest'] = $nrst;
		$systems[$sys['systems_id']] = $sys;
	}

	
	
	foreach($fixedpositions as $fx) {
		$sys = $db->query("SELECT * FROM stu_systems WHERE 1 order by ( ((cx - ".$fx['cx'].")*(cx - ".$fx['cx'].")) + ((cy - ".$fx['cy'].")*(cy - ".$fx['cy'].")) ) asc LIMIT 1;",4);
		$sys['race'] = $fx['race'];
		$systems[$sys['systems_id']] = $sys;
		$allsystems[$sys['systems_id']] = $sys;
		
		$head[$sys['systems_id']] = $sys['systems_id'];

		$nrst = $db->query("SELECT * FROM stu_systems WHERE ".arrToIn($removed,$sys['systems_id'])." order by ( ((cx - ".$sys['cx'].")*(cx - ".$sys['cx'].")) + ((cy - ".$sys['cy'].")*(cy - ".$sys['cy'].")) ) asc LIMIT ".$fx['bag'].";");		
		while($n=mysql_fetch_assoc($nrst))
		{
			$n['race'] = $fx['race'];
			$systems[$n['systems_id']] = $n;
			// array_push
			// $removed[$n['systems_id']] = $n;
			$head[$n['systems_id']] = $sys['systems_id'];
		}
	}
	

	
	
	$fixedclusters = array();
	
	
	
	array_push($fixedclusters,array(194,206,207));
	array_push($fixedclusters,array(201,210,211,200));
	array_push($fixedclusters,array(154,157,168,172,178));
	array_push($fixedclusters,array(165,174,179,182,184));
	array_push($fixedclusters,array(202,197,191,186));
	array_push($fixedclusters,array(134,132,128,136,146));
	array_push($fixedclusters,array(138,135,143,139,146));
	array_push($fixedclusters,array(120,117,103,113,126));
	array_push($fixedclusters,array(108,102,111,129));
	array_push($fixedclusters,array(5,20,1,8));
	array_push($fixedclusters,array(42,48,64,36,61));
	array_push($fixedclusters,array(84,89,98,99,90));
	array_push($fixedclusters,array(124,133,131,140));
	array_push($fixedclusters,array(149,145,137,141));
	array_push($fixedclusters,array(2,12,19,10));
	array_push($fixedclusters,array(204,209,198,205,189));
	array_push($fixedclusters,array(208,196,193,192,203));
	array_push($fixedclusters,array(147,152,158,171));
	array_push($fixedclusters,array(185,175,160,159));
	array_push($fixedclusters,array(167,161,162,163));
	array_push($fixedclusters,array(153,156,164));
	array_push($fixedclusters,array(130,142,144,148,151));
	array_push($fixedclusters,array(106,97,123,127));
	array_push($fixedclusters,array(106,97,123,127));
	array_push($fixedclusters,array(9,17,22,28));
	array_push($fixedclusters,array(72,96,82,91,62));
	array_push($fixedclusters,array(121,115,112,101));
	array_push($fixedclusters,array(79,92,83,93,80));
	array_push($fixedclusters,array(74,73,65,67,88));
	array_push($fixedclusters,array(27,26,13,14,3));
	array_push($fixedclusters,array(25,23,21,18,11));
	array_push($fixedclusters,array(6,7,15,4,16));
	array_push($fixedclusters,array(24,31,33,38));
	array_push($fixedclusters,array(57,68,77,75,81));
	array_push($fixedclusters,array(40,53,50,41,44));
	array_push($fixedclusters,array(35,39,43,47,52));
	array_push($fixedclusters,array(45,51,55,58));
	array_push($fixedclusters,array(76,85,95,70));
	array_push($fixedclusters,array(63,69,71,78,86));
	
	
	
	
	
	
	
	foreach ($fixedclusters as $arr) {
		
		$nhead = lowest($arr);

		foreach($arr as $id) {
			$head[$id] = $nhead;
		}
		
	}
	
	
	
	$sysarrl = array();
	$clusters = array();
	foreach ($systems as $s) {
		array_push($sysarrl,$s['systems_id']);
		array_push($clusters,$head[$s['systems_id']]);
	}
	
	$clusters = array_unique($clusters);
	
	
	
	
	// print_r($clusters);
	
	
	
	

	
	$fr = $db->query("SELECT * FROM stu_map WHERE type < 100 or type > 199;");
	$fields = array();
	while ($f = mysql_fetch_assoc($fr)) {
		
		
		
		
		// $nearest = fnearest($f,$sysarrl);
		
		// $f['nearest'] = $nearest;
		
		
		array_push($fields,$f);		
		
		// $db->query("UPDATE stu_map SET region=".$head[$nearest]." WHERE cx=".$f['cx']." ANd cy=".$f['cy'].";");
	}

	foreach ($systems as $s) {
		array_push($sysarrl,$s['systems_id']);
		array_push($clusters,$head[$s['systems_id']]);
	}
	
	$clusters = array_unique($clusters);
	
	
	
	
	
	
	
	foreach ($clusters as $c) {
		$sys = $systems[$c];
		if ($sys['race'] > 0)
			$db->query("REPLACE INTO `stu_map_regions` (`id`, `name`, `faction`, `status`) VALUES ('".$c."', '".$sys['name']."-Cluster', '".$sys['race']."', 'core');");			
		else
			$db->query("REPLACE INTO `stu_map_regions` (`id`, `name`, `faction`, `status`) VALUES ('".$c."', '".$sys['name']."-Cluster', '0', 'free');");
			
		// break;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	


	
	//----------------------------------------------------------
	$clusters = 5;
	//----------------------------------------------------------
	
		// $choices = array();
	// foreach ($systems as $k => $data) {
		// array_push($choices,$k);
	// }
	// shuffle($choices);
	
	
	// $centroids = array();
	
	// $headsys = array();
	
	// for ($i = 0; $i < $clusters; $i++) {
		// $id = array_shift($choices);
		// array_push($headsys,$systems[$id]);
		// array_push($centroids,$id);
		// $head[$id] = $id;
	// }
	
	// foreach($systems as $s) {		
		// $head[$s['systems_id']] = nearest($s['systems_id'],$centroids);
	// }
	
	
	// for ($k = 0; $k < 100; $k++) {
	
	
	// $lowest = array();
	// foreach($centroids as $c) {
		// $kids = array();
		// foreach($head as $n => $h) {
			// if ($h == $c) array_push($kids,$n);
		// }
		// $best = lowest($kids);

		
		// array_push($lowest,$systems[$best]);
		// $head[$c] = $best;
	// }
	
	// $centroids = array();
	// foreach ($lowest as $s) {
		// array_push($centroids, $s['systems_id']);
	// }

	// foreach($head as $k => $v) {
		// if (in_array($v,$centroids)) continue;		
		// $head[$k] = $head[$v];
	// }
	
	
	// }
	
	
	
	// print_r($removed);
	
	echo "<center><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"1200px\" height=\"1200px\"><title>Titel der Datei</title><desc>Beschreibung/Textalternative zum Inhalt.</desc>";
	


	foreach ($fields as $f) {
		echo "<line x1='".pc($f['cx'])."' y1='".pc($f['cy'])."' x2='".pc($allsystems[$f['nearest']]['cx'])."' y2='".pc($allsystems[$f['nearest']]['cy'])."' stroke='#003300'/>";

	}

	
	foreach ($systems as $sys) {
		if ($head[$sys['systems_id']] != 0) continue;
		echo "<line x1='".pc($sys['cx'])."' y1='".pc($sys['cy'])."' x2='".pc($sys['nearest']['cx'])."' y2='".pc($sys['nearest']['cy'])."' stroke='#00ee00'/>";

	}
	
	foreach ($head as $k => $v) {

		$fr = $allsystems[$k];
		$to = $allsystems[$v];
	
		echo "<line x1='".pc($fr['cx'])."' y1='".pc($fr['cy'])."' x2='".pc($to['cx'])."' y2='".pc($to['cy'])."' stroke='#888844'/>";

	}	
	
	foreach ($systems as $sys) {
		echo "<circle cx='".pc($sys['cx'])."' cy='".pc($sys['cy'])."' r='10' stroke='".racecolor($sys['race'])."' fill='#000000'/>";
	}
	
	foreach ($systems as $sys) {
		if ($head[$sys['systems_id']] != 0)
			// echo "<text x='".(pc($sys['cx'])+6)."' y='".(pc($sys['cy'])+20)."' r='10' fill='#666666' font-size='12'>".$sys['systems_id']."</text>";
			echo "<text x='".(pc($sys['cx'])-10)."' y='".(pc($sys['cy'])-5)."' r='10' fill='#aaaaaa' font-size='12'>".$sys['name']."</text>";
		else
			echo "<text x='".(pc($sys['cx'])+6)."' y='".(pc($sys['cy'])+20)."' r='10' fill='#00ffff' font-size='12'>".$sys['systems_id']."</text>";
			// echo "<text x='".(pc($sys['cx']))."' y='".(pc($sys['cy']))."' r='10' fill='#00ffff' font-size='12'>".$sys['name']."</text>";
	}	
	// foreach ($headsys as $sys) {
		// echo "<circle cx='".pc($sys['cx'])."' cy='".pc($sys['cy'])."' r='10' stroke='".racecolor($sys['race'])."' fill='#ffffff'/>";
		// echo "<text x='".pc($sys['cx'])."' y='".(pc($sys['cy'])+10)."' r='10' fill='#00ffff'>".$sys['weight']."</text>";
		// echo "<text x='".pc($sys['cx'])."' y='".(pc($sys['cy'])+10)."' r='10' fill='#00ffff'>".$sys['systems_id']."</text>";
	// }
	
	// foreach ($lowest as $sys) {
		// echo "<circle cx='".pc($sys['cx'])."' cy='".pc($sys['cy'])."' r='5' stroke='".racecolor($sys['race'])."' fill='#ff00ff'/>";
	// }
	
	// foreach ($removed as $sys) {
		// echo "<circle cx='".pc($sys['cx'])."' cy='".pc($sys['cy'])."' r='10' stroke='".racecolor($sys['race'])."' fill='#000000'/>";
		// echo "<text x='".pc($sys['cx'])."' y='".(pc($sys['cy'])+10)."' r='10' fill='#00ffff'>".$sys['systems_id']."</text>";
	// }
	
	
	echo "</svg></center>";
	echo "</body>";
	
?>
