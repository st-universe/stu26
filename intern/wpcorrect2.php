<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	$gfx = "http://www.stuniverse.de/gfx/";
	



		$result = $db->query("SELECT * FROM stu_ships WHERE 1");
		while($data=mysql_fetch_assoc($result))
		{
			echo "<br>".$data[name];


			$rump = $db->query("SELECT wpoints FROM stu_ships_buildplans WHERE plans_id = ".$data[plans_id]." LIMIT 1",1);
			echo "<br>".$rump." - ".$data[points];

			$db->query("UPDATE stu_ships set points = ".$rump." WHERE id = $data[id] LIMIT 1");


			echo "<br><br>";

		}







?>
