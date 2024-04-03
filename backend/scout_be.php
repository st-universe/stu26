<?php
header("Content-Type: text/html; charset=iso-8859-1");
if ($_GET[sp] != "e2c967a8136b099b5c87a06da128afb1")
{
	echo 0;
	exit;
}
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

if (!check_int($_GET[ui]) || strlen($_GET[up]) != 32)
{
	echo 0;
	exit;
}
$db = new db;
$data = $db->query("SELECT a.id,a.user,a.race,b.name,b.homepage FROM stu_user as a LEFT JOIN stu_allylist as b USING(allys_id) WHERE a.id=".$_GET[ui]." AND a.pass='".$_GET[up]."'",4);
if ($data == 0)
{
	echo 0;
	exit;
}
echo "1\n".$data[id]."\n".stripslashes($data[user])."\n".(!$data[name] ? 0 : stripslashes($data[name]))."\n".(!$data[homepage] ? 0 : $data[homepage]);
?>