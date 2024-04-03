<?php
include_once("../../inc/func.inc.php");
include_once("../../inc/config.inc.php");
include_once("../../class/db.class.php");
$db = new db;

$result = $db->query("SELECT a.*,b.cx,b.cy FROM stu_sys_map as a LEFT JOIN stu_systems as b ON b.systems_id=a.systems_id LEFT JOIN stu_map_values as c ON c.sx=a.sx AND c.sy=a.sy AND c.systems_id=a.systems_id WHERE (a.type=11 OR a.type=12) ANd ISNULL(c.systems_id) ORDER BY RAND()");

srand();
while($data=mysql_fetch_assoc($result))
{
	echo $data['systems_id']." - ".$data[sx]." - ".$data[sy]."<br>";
	if ($data[type] == 11)
	{
		$ir = rand(10,15);
		$kel = rand(0,30);
		$ni = rand(0,3);
		$mag = rand(0,3);
		$tal = rand(0,3);
		$gal = rand(0,3);
	}
	else
	{
		$ir = rand(15,20);
		$kel = rand(0,5);
		$ni = rand(0,5);
		$mag = rand(0,5);
		$tal = rand(0,5);
		$gal = rand(0,5);
	}
	flush();
	$db->query("INSERT INTO stu_map_values (sx,sy,systems_id,chance_20,chance_21,chance_22,chance_23,chance_24,chance_25) VALUES ('".$data[sx]."','".$data[sy]."','".$data[systems_id]."','".($ir/10)."','".($kel/10)."','".($ni/10)."','".($mag/10)."','".($tal/10)."','".($gal/10)."')");
}
?>