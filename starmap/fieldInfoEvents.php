<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");
$db = new db;


@session_start();

if ($_SESSION["login"] != 1) exit;
if (!$_GET["x"] || !check_int($_GET["x"])) exit;
if (!$_GET["y"] || !check_int($_GET["y"])) exit;

$gfx = $_SESSION[gfx_path];
// if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

	$fieldinfo = $db->query("SELECT a.*,b.* FROM stu_map as a LEFT JOIN stu_map_ftypes as b on a.type = b.type WHERE a.cx = ".$_GET["x"]." AND a.cy = ".$_GET["y"].";",4);
	// $fieldinfo = mysql_fetch_assoc($field);

	// $faction = rand(1,4)-1;
	$faction = 3;
	

	$eventstring = "<br><br>Ereignisse:<table>";
	
	$events = $db->query("SELECT * FROM `stu_history` WHERE type = 1 AND coords_x = ".$_GET["x"]." AND coords_y = ".$_GET["y"]." AND date > (NOW() - INTERVAL 7 DAY) ORDER BY date DESC;");
	
	while ($ev = mysql_fetch_assoc($events)) {
		$eventstring .= "<tr><td><b>".$ev[date]."</b>:</td><td> ".stripslashes($ev[message])."</td></tr>";
	}
	$eventstring .= "</table>";
	
	echo "<div style=\"border:1px solid #8897cf;background:#000000;padding:5px;\"><img src=\"".$gfx."/map/".$fieldinfo[type].".gif\" /> ".$_GET["x"]."|".$_GET["y"].": ".$fieldinfo[name]."".$eventstring."</div>";	
	

	
?>