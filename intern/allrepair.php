<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	$gfx = "http://www.stuniverse.de/gfx/";
	



		$result = $db->query("SELECT * FROM stu_buildings WHERE 1");
		while($data=mysql_fetch_assoc($result))
		{
			echo "<br>".$data[name]." (".$data[buildings_id].") - ".$data[integrity];
			$db->query("UPDATE stu_colonies_fielddata SET integrity=".$data[integrity]." WHERE buildings_id = ".$data[buildings_id]."");

		}







?>
