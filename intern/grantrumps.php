<?php

	$gfx = "http://www.stuniverse.de/gfx/";
	

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;


	
	

echo "<html>
<head>
	<title>Star Trek Universe</title>
<link rel=\"STYLESHEET\" type=\"text/css\" href=../gfx/css/6.css>
</head>
<body>";


	$result = $db->query("SELECT w.*,m.level FROM stu_weapons as w left join stu_modules as m on w.module_id = m.module_id WHERE 1 ORDER by m.level, m.module_id;");
	// $result = $db->query("SELECT * FROM stu_colonies WHERE 1");
	
	// $db->query("REPLACE INTO `stu_rumps_user` (`rumps_id`, `user_id`) SELECT rumps_id, '102' as user_id FROM stu_rumps WHERE race > 0;";
	
	
		function dmg($nm,$min,$max,$salvos,$hit,$crit) {
			echo $nm." "."DMG: ".( (0.5*$max + 0.5*$min)*$salvos * $hit * (1+$crit))."<br>";
		}

	$pl = 0;
	while ($d = mysql_fetch_assoc($result)) {
		
		if ($pl != $d[level]) {
			echo "<br>";
			$pl = $d[level];
		}
		if ($d[mindmg] == 0) $d[mindmg] = 36;
		if ($d[maxdmg] == 0) $d[maxdmg] = 50;
		
		dmg($d[name],$d[mindmg],$d[maxdmg],$d[salvos],$d[hitchance]/100,$d[critical]/100);
		
		
	}
	
				echo "<br>";			echo "<br>";			echo "<br>";			echo "<br>";	
		dmg("PH",28,32,3,0.95,0.05);
		dmg("RD",27,33,3,0.95,0.05);
		dmg("KD",35,40,3,0.75,0.05);
		
		
		dmg("PP",23,30,4,0.85,0.10);
		dmg("RP",21,32,4,0.85,0.10);
		dmg("KP",40,45,3,0.70,0.10);
		
		
		
		
		
		
		

echo "</body>";


?>
