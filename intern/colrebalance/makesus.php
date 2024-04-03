<?php

	$gfx = "http://www.stuniverse.de/gfx/";
	

    include_once("../../inc/func.inc.php");
    include_once("../../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	include("colgen.class.php");
	
	

echo "<html>
<head>
	<title>Star Trek Universe</title>
<link rel=\"STYLESHEET\" type=\"text/css\" href=../gfx/css/6.css>
</head>
<body>";

	function sendPm($user,$text,$type) {
		global $db;
		$db->query("INSERT INTO stu_pms (send_user,recip_user,text,type,date) VALUES ('2','".$user."','".addslashes($text)."','".$type."',NOW())");
	}


	$result = $db->query("SELECT * FROM stu_user WHERE id > 100;");
	// $result = $db->query("SELECT * FROM stu_colonies WHERE user_id = 102");
	
	// $cg = new ColonyGenerator();
	
	while($data=mysql_fetch_assoc($result))
	{
		
		
		$message = "Das Kolonie-Rebalance-Script hat alle Kolonien resetted und neu generiert. Fuer jedes verlorene Gebaeude wurden 200 Energie,50 Baumaterial, 50 Aluminium und 50 Duranium gutgeschrieben. Außerdem wurden pauschal jeder Kolonie 500 Einwohner spendiert.<br>";
		
		$message .= "Der Hauptteil des Rebalancings ist damit abgeschlossen. Es gibt aber noch viele offene Baustellen. Die neuen Planetenboni fuer Gebaeude (Einsehbar in der Datenbank) werden im Koloniebildschirm zB noch nicht angerechnet.<br>";
		$message .= "<br>Kolonieticks sind bis Mittwoch Abend ausgesetzt. Ab dann laufen sie stuendlich. Ihr solltet bis dahin lauffaehige Kolonien bauen koennen. Ums noch einfacher zu machen, kostet Terraforming momentan keine Waren, und alle Terraforming- und Bauvorgaenge sind in 30 sekunden erledigt.";

		
		echo "<br>".$data[id]."";
		sendPm($data[id],$message,4);
		
	
	}



echo "</body>";


?>
