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


	$result = $db->query("SELECT * FROM stu_colonies WHERE colonies_classes_id = 210 OR colonies_classes_id = 310;");
	// $result = $db->query("SELECT * FROM stu_colonies WHERE user_id = 102");
	
	$cg = new ColonyGenerator();
	
	while($data=mysql_fetch_assoc($result))
	{
		$encodedFields = $data['fieldstring'];
		$corrected = "";
		while (strlen($encodedFields) > 0) {
		
			$nextfield = hexdec(substr($encodedFields,0,2));
			$encodedFields = substr($encodedFields,2);
			
			if ($nextfield == 116) $nextfield = 100;
			
			if ($nextfield < 16) $c = "0";
			else $c = "";
			
			$c .= dechex($nextfield);
			
			$corrected .= $c;
		}
		// $newfields = $cg->encode($cg->generateColony($data['colonies_classes_id'],0)); 
		
		

		
		echo "<br>".$data[id]."<br>&nbsp;&nbsp;&nbsp;&nbsp;".$data['fieldstring']."<br>&nbsp;&nbsp;&nbsp;&nbsp;".$corrected;
		
		$db->query("UPDATE stu_colonies SET fieldstring = '".$corrected."' WHERE id=".$data[id]);
	
	}


echo "</body>";


?>
