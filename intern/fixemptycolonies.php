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


	
	function setColonyFields($id,$encodedFields) {
		global $db;
		if ($encodedFields != "XX") {
			$i = 1;
			while (strlen($encodedFields) > 0) {
			
				$nextfield = hexdec(substr($encodedFields,0,2));
				$encodedFields = substr($encodedFields,2);
			
				 $db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$id."','".$i."','".$nextfield."')");
				 $i++;
			}
		}
	}





	// $result = $db->query("SELECT * FROM stu_colonies WHERE 1");
	$result = $db->query("SELECT DISTINCT(a.colonies_id) as id, b.user_id, b.fieldstring FROM stu_colonies_fielddata as a LEFT JOIN stu_colonies as b ON a.colonies_id = b.id WHERE a.type = 0 AND b.user_id = 1");
	while($data=mysql_fetch_assoc($result))
	{
		echo "<br>".$data[id]." ".$data[fieldstring];
		$db->query("DELETE FROM stu_colonies_fielddata WHERE colonies_id=".$data[id]);
		setColonyFields($data[id],$data[fieldstring]);
	}










echo "</body>";


?>
