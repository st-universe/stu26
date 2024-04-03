<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

@session_start();

if ($_SESSION["login"] != 1) exit;

$db = new db;

$result = $db->query("SELECT COUNT(id) as npms,type FROM stu_pms WHERE recip_user=".$_SESSION['uid']." AND new='1' AND recip_del='0' GROUP BY type ORDER BY type");
if (mysql_num_rows($result) > 0)
{
	while ($data=mysql_fetch_assoc($result)) ${"npm".$data['type']} = $data['npms'];
	mysql_free_result($result);
}
if ($npm1 > 0 || $npm2 > 0 || $npm3 > 0 || $npm4 > 0 || $npm4 > 5 )
{
	echo "&nbsp;&nbsp;<img src=".$_SESSION['gfx_path']."/buttons/icon/pm_in.gif border=0 name=pe2 title='Es sind neue Nachrichten eingetroffen'> ";
	if ($npm1 && $npm1 > 0) echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=1 title=\"Privat\" style=\"color: #FF0000; text-decoration: blink;\">".$npm1."</a> | ";
	else echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=1 title=\"Privat\">0</a> | ";
	if ($npm2 && $npm2 > 0) echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=2 title=\"Handel\" style=\"color: #FF0000; text-decoration: blink;\">".$npm2."</a> | ";
	else echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=2 title=\"Handel\">0</a> | ";
	if ($npm3 && $npm3 > 0) echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=3 title=\"Schiffe\" style=\"color: #FF0000; text-decoration: blink;\">".$npm3."</a> | ";
	else echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=3 title=\"Schiffe\">0</a> | ";
	if ($npm4 && $npm4 > 0) echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=4 title=\"Kolonien\" style=\"color: #FF0000; text-decoration: blink;\">".$npm4."</a> | ";
	else echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=4 title=\"Kolonien\">0</a> | ";
	if ($npm5 && $npm5 > 0) echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=5 title=\"Stationen\" style=\"color: #FF0000; text-decoration: blink;\">".$npm5."</a>";
	else echo "<a href=?p=comm&s=pe><a href=?p=comm&s=pe&cat=5 title=\"Stationen\">0</a>";
}

?>