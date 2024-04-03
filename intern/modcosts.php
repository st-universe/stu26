<?php

	$gfx = "http://www.stuniverse.de/gfx/";
	

    include_once("../inc/func.inc.php");
    include_once("../inc/config.inc.php");
    include_once($global_path."/class/db.class.php");
    $db = new db;


	
	

	$users = $db->query("SELECT * FROM `stu_modules_cost` ORDER BY `module_id`,`goods_id` ASC ");
	while($u=mysql_fetch_assoc($users)) {
	
		print_r("(".$u['module_id'].",\t".$u['goods_id'].",\t".$u['count']."),<br>");
	}









?>
