<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

// Anomalien setzen

$db->query("TRUNCATE TABLE stu_map_special");


$result = $db->query("SELECT cx,cy FROM stu_map WHERE type=6");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,20) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,cx,cy) VALUES ('1','".$data[cx]."','".$data[cy]."')");
}

$result = $db->query("SELECT systems_id,sx,sy FROM stu_sys_map WHERE type=6");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,20) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,systems_id,sx,sy) VALUES ('1','".$data[systems_id]."','".$data[sx]."','".$data[sy]."')");
}

$result = $db->query("SELECT cx,cy FROM stu_map WHERE type=15");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,20) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,cx,cy) VALUES ('2','".$data[cx]."','".$data[cy]."')");
}

$result = $db->query("SELECT systems_id,sx,sy FROM stu_sys_map WHERE type=15");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,20) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,systems_id,sx,sy) VALUES ('2','".$data[systems_id]."','".$data[sx]."','".$data[sy]."')");
}

$result = $db->query("SELECT cx,cy FROM stu_map WHERE type=4");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,20) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,cx,cy) VALUES ('4','".$data[cx]."','".$data[cy]."')");
}

$result = $db->query("SELECT systems_id,sx,sy FROM stu_sys_map WHERE type=4");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,20) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,systems_id,sx,sy) VALUES ('4','".$data[systems_id]."','".$data[sx]."','".$data[sy]."')");
}

$result = $db->query("SELECT cx,cy FROM stu_map WHERE type=10");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,20) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,cx,cy) VALUES ('3','".$data[cx]."','".$data[cy]."')");
}

$result = $db->query("SELECT systems_id,sx,sy FROM stu_sys_map WHERE type=10");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,20) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,systems_id,sx,sy) VALUES ('3','".$data[systems_id]."','".$data[sx]."','".$data[sy]."')");
}

$result = $db->query("SELECT cx,cy FROM stu_map WHERE type=1");
while($data=mysql_fetch_assoc($result))
{
	if (rand(1,80) != 1) continue;
	$db->query("INSERT INTO stu_map_special (type,cx,cy) VALUES ('5','".$data[cx]."','".$data[cy]."')");
}

?>
