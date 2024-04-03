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



	$cg = new ColonyGenerator();


		
		echo "<br>".$cg->encode($cg->generateColony(201,0)); 
		echo "<br>".$cg->encode($cg->generateColony(202,0)); 
		echo "<br>".$cg->encode($cg->generateColony(203,0)); 
		echo "<br>".$cg->encode($cg->generateColony(204,0)); 
		echo "<br>".$cg->encode($cg->generateColony(205,0)); 
		echo "<br>".$cg->encode($cg->generateColony(206,0)); 
		echo "<br>".$cg->encode($cg->generateColony(207,0)); 
		echo "<br>".$cg->encode($cg->generateColony(209,0)); 
		echo "<br>".$cg->encode($cg->generateColony(210,0)); 
		





echo "</body>";


?>
