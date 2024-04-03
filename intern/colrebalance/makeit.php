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


	
	function setColonyFields($id,$encodedFields) {
		global $db;
		if ($encodedFields != "XX") {
			$i = 1;
			while (strlen($encodedFields) > 0) {
			
				$nextfield = hexdec(substr($encodedFields,0,2));
				$encodedFields = substr($encodedFields,2);
			
				 // $db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$id."','".$i."','".$nextfield."')");
				 $i++;
			}
		}
	}





	$result = $db->query("SELECT * FROM stu_colonies WHERE flag = 0 LIMIT 100;");
	// $result = $db->query("SELECT * FROM stu_colonies WHERE user_id = 102");
	
	$cg = new ColonyGenerator();
	
	while($data=mysql_fetch_assoc($result))
	{
		
		
		$newfields = $cg->encode($cg->generateColony($data['colonies_classes_id'],0)); 
		
		

		
		echo "<br>".$data[id]."<br>&nbsp;&nbsp;&nbsp;&nbsp;".$newfields;
		
	
	}










echo "</body>";


?>
