<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	$gfx = "http://www.stuniverse.de/gfx/";
	



		$result = $db->query("SELECT id FROM stu_colonies WHERE colonies_classes_id = 10");
		while($data=mysql_fetch_assoc($result))
		{
			// $data[type] == 3
			$db->query("UPDATE stu_colonies_fielddata SET type = 115 WHERE (field_id > 9) AND (field_id < 19) AND colonies_id = ".$data[id]."");

		}







?>
