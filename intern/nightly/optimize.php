<?php
include_once("/srv/www/stu_sys/webroot/inc/config.inc.php");
include_once($global_path . "/class/db.class.php");
$db = new db;

// Datenbank optimieren
$result = mysql_listtables("changeme", $db->dblink);
for ($i = 0; $i < mysql_num_rows($result); $i++) $db->query("OPTIMIZE TABLE " . mysql_tablename($result, $i));
