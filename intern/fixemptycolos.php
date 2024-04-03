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
			$i = 0;
			while (strlen($encodedFields) > 0) {
			
				$nextfield = hexdec(substr($encodedFields,0,2));
				$encodedFields = substr($encodedFields,2);
			
				 $db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$id."','".$i."','".$nextfield."')");
				 $i++;
			}
		}
	}





	$eresult = $db->query("SELECT * FROM stu_colonies_fielddata WHERE field_id = 20 AND type = 0");
	while($edata=mysql_fetch_assoc($eresult))
	{
	
		$o = $edata[colonies_id];

		$result = $db->query("SELECT * FROM stu_colonies WHERE id = ".$o."");
		while($data=mysql_fetch_assoc($result))
		{
			echo "<br>".$data[id]." ".$data[fieldstring];
			$db->query("DELETE FROM stu_colonies_fielddata WHERE colonies_id=".$data[id]);
			setColonyFields($data[id],$data[fieldstring]);
		}

	}










echo "</body>";


?>
