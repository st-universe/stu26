<?php
// Konfigurationsdatei und Funktionen laden
include_once("config.inc.php");
include_once("func.inc.php");

// Einbinden und laden der Klassen
include_once("db.class.php");
$db = new db;

$i = 120;
$result = $db->query("SELECT systems_id FROM stu_systems");
while($dat = mysql_fetch_assoc($result))
{
	$db->query("UPDATE stu_systems SET systems_id=".$i." WHERE systems_id=".$dat['systems_id']);
	$db->query("UPDATE stu_sys_map SET systems_id=".$i." WHERE systems_id=".$dat['systems_id']);
	$i++;
}
?>