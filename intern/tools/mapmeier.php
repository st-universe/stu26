<?php
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

$j = 1;
$x = 160;
$y = 141;
$ny = 160;
while($j<=$x)
{
	for($i=$y;$i<=$ny;$i++)
	{
		$db->query("INSERT INTO stu_map (cx,cy,type) VALUES ('".$j."','".$i."','1')");
		echo $j." | ".$i."<br>";
	}
	$j++;
}

?>