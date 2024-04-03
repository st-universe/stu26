<?php

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;

	$gfx = "http://www.stuniverse.de/gfx/";
	



		$result = $db->query("SELECT * FROM stu_ships_buildplans WHERE 1");
		while($data=mysql_fetch_assoc($result))
		{
			echo "<br>".$data[name];
			echo "<br>".$data[m1]." ".$data[m2]." ".$data[m3]." ".$data[m4]." ".$data[m5]." ".$data[m6]." ".$data[m7]." ".$data[m8]." ".$data[m9]." ".$data[m10]." ".$data[m11]."";

			$m1 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m1]." LIMIT 1",1);
			$m2 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m2]." LIMIT 1",1);
			$m3 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m3]." LIMIT 1",1);
			$m4 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m4]." LIMIT 1",1);
			$m5 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m5]." LIMIT 1",1);
			$m6 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m6]." LIMIT 1",1);
			$m7 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m7]." LIMIT 1",1);
			$m8 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m8]." LIMIT 1",1);
			$m9 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m9]." LIMIT 1",1);
			$m10 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m10]." LIMIT 1",1);
			$m11 = $db->query("SELECT points FROM stu_modules WHERE module_id = ".$data[m11]." LIMIT 1",1);


			echo "---------------".$m1." ".$m2." ".$m3." ".$m4." ".$m5." ".$m6." ".$m7." ".$m8." ".$m9." ".$m10." ".$m11."";

			$rump = $db->query("SELECT wp FROM stu_rumps WHERE rumps_id = ".$data[rumps_id]." LIMIT 1",1);
			$sum = $m1+$m2+$m3+$m4+$m5+$m6+$m7+$m8+$m9+$m10+$m11;
			$calcpoints = round($rump * $sum/10,1);

			echo "<br>".$data[wpoints]." vs ".$calcpoints;

			$db->query("UPDATE stu_ships_buildplans set wpoints = ".$calcpoints." WHERE plans_id = $data[plans_id] LIMIT 1");


			echo "<br><br>";

		}







?>
