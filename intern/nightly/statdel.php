<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

include_once($global_path."/inc/gencol.inc.php");

$result = $db->query("SELECT a.id as sid,b.id as uid FROM `stu_stations` as a left join stu_user as b on a.user_id = b.id WHERE ISNULL(b.id)");
//$result = $db->query("SELECT id,user,allys_id FROM stu_user WHERE delmark='2'");
while($data=mysql_fetch_assoc($result))
{
	if (!$data[uid]) {
		echo "<br>a ".$data[sid]." - ".$data[uid];
		$db->query("DELETE FROM stu_stations WHERE id=".$data['sid']." LIMIT 1");
		$db->query("DELETE FROM stu_stations_fielddata WHERE stations_id=".$data['sid']);

		$db->query("DELETE FROM stu_stations_storage WHERE stations_id=".$data['sid']);
		
	}
	
}

?>
