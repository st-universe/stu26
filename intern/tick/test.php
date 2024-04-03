<?php
include_once("../../inc/config.inc.php");
include_once($global_path."/class/db.class.php");
$db = new db;

include_once($global_path."/class/log.class.php");
$log = new log;

$log->deleteLogType("test");

$log->enterLog("test","test");






?>
