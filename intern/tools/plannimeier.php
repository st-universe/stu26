<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/inc/func.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

include_once($global_path."/intern/starclass.php");
$star = new starmap;


$result = $db->query("SELECT a.*,b.colonies_classes_id FROM stu_sys_map as a LEFT JOIN stu_map_ftypes as b ON b.type=a.type LEFT JOIN stu_colonies as c ON c.systems_id=a.systems_id AND c.sx=a.sx AND c.sy=a.sy WHERE b.colonies_classes_id>0 AND ISNULL(c.id)");

#$result = $db->query("SELECT a.*,b.colonies_classes_id FROM stu_sys_map as a LEFT JOIN stu_map_ftypes as b ON b.type=a.type LEFT JOIN stu_systems as c ON c.systems_id=a.systems_id WHERE c.cy>120 AND b.colonies_classes_id>0");
while($data=mysql_fetch_assoc($result))
{
	$_GET[sx] = $data[sx];
	$_GET[sy] = $data[sy];
	$_GET[id] = $data[systems_id];
	$star->addcolony($data[colonies_classes_id]);
	echo $data[systems_id]." - ".$data[sx]." - ".$data[sy]."<br>";
}
?>
