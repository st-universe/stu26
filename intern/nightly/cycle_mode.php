<?php
include_once("../../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

// Setzt bzw löscht den Wartungszustand
$result = $db->query("SELECT value FROM stu_game_vars WHERE var='state'",1);
if ($result == 1) $db->query("UPDATE stu_game_vars SET value='3' WHERE var='state'");
if ($result == 3) $db->query("UPDATE stu_game_vars SET value='1' WHERE var='state'");
?>
