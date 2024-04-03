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
			
				 $db->query("INSERT INTO stu_colonies_fielddata (colonies_id,field_id,type) VALUES ('".$id."','".$i."','".$nextfield."')");
				 $i++;
			}
		}
	}





	$result = $db->query("SELECT * FROM stu_colonies WHERE flag = 0 LIMIT 200;");
	// $result = $db->query("SELECT * FROM stu_colonies WHERE user_id = 102");
	
	$cg = new ColonyGenerator();
	
	while($data=mysql_fetch_assoc($result))
	{
		
		if (($data[colonies_classes_id] > 220) && ($data[colonies_classes_id] < 300)) {
			$db->query("UPDATE stu_colonies SET flag=1 WHERE id=".$data[id]);
			echo "<br>".$data[id]."<br>&nbsp;&nbsp;&nbsp;&nbsp;GASGIANT";			
			continue;
		}
		if (($data[colonies_classes_id] == 208) || ($data[colonies_classes_id] == 308)) {
			$db->query("UPDATE stu_colonies SET flag=1 WHERE id=".$data[id]);
			echo "<br>".$data[id]."<br>&nbsp;&nbsp;&nbsp;&nbsp;VENUSIAN";			
			continue;
		}		
		$newfields = $cg->encode($cg->generateColony($data['colonies_classes_id'],0)); 
		
		

		
		echo "<br>".$data[id]."<br>&nbsp;&nbsp;&nbsp;&nbsp;".$newfields;
		
		
		if ($data[user_id] > 100) {
			$buildings = $db->query("SELECT COUNT(*) FROM stu_colonies_fielddata WHERE colonies_id = ".$data[id]." AND buildings_id > 0;",1);
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;".$buildings." Buildings";
		

			
			$e =  500 + 200 * $buildings;
			$b =  50 * $buildings + $db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id = ".$data[id]." AND goods_id = 2;",1);;
			$a =  50 * $buildings + $db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id = ".$data[id]." AND goods_id = 4;",1);;
			$d =  50 * $buildings + $db->query("SELECT count FROM stu_colonies_storage WHERE colonies_id = ".$data[id]." AND goods_id = 21;",1);;
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;".$b." BM";
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;".$a." A";
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;".$d." D";
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;".$e." E";
			
			$db->query("REPLACE INTO stu_colonies_storage (`colonies_id`, `goods_id`, `count`) VALUES ('".$data[id]."', '2', '".$b."');");
			$db->query("REPLACE INTO stu_colonies_storage (`colonies_id`, `goods_id`, `count`) VALUES ('".$data[id]."', '4', '".$a."');");
			$db->query("REPLACE INTO stu_colonies_storage (`colonies_id`, `goods_id`, `count`) VALUES ('".$data[id]."', '21', '".$d."');");
			
			$db->query("UPDATE stu_colonies SET fieldstring = '".$newfields."' WHERE id=".$data[id]);
			$db->query("UPDATE stu_colonies SET eps = ".$e.", max_eps = 150, max_storage = 5000, bev_max = 50, bev_work = 0, bev_free = 500, flag=1 WHERE id=".$data[id]);
			
		} else {
			echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;Unclaimed";
			$db->query("UPDATE stu_colonies SET fieldstring = '".$newfields."', flag=1 WHERE id=".$data[id]);
		}
		
		$db->query("DELETE FROM stu_colonies_fielddata WHERE colonies_id=".$data[id]);
		setColonyFields($data[id],$newfields);
		
		if ($data[user_id] > 100) {
			$field = $db->query("SELECT field_id FROM stu_colonies_fielddata WHERE colonies_id = ".$data[id]." AND (type = 1 OR type = 9 OR type = 18 OR type = 19 OR type = 6 OR type = 200 OR type = 10) ORDER BY RAND() LIMIT 1;",1);
			$db->query("UPDATE stu_colonies_fielddata SET buildings_id = 1, integrity = 100, aktiv = 1 WHERE colonies_id=".$data[id]." AND field_id = ".$field." LIMIT 1;");
		}
	}










echo "</body>";


?>
