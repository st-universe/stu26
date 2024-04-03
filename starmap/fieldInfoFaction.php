<?php
header("Content-Type: text/html; charset=iso-8859-1");
include_once("../inc/func.inc.php");
include_once("../inc/config.inc.php");
include_once("../class/db.class.php");

include_once("factions.php");

$db = new db;


@session_start();

if ($_SESSION["login"] != 1) exit;
if (!$_GET["x"] || !check_int($_GET["x"])) exit;
if (!$_GET["y"] || !check_int($_GET["y"])) exit;

$x = $_GET["x"];
$y = $_GET["y"];


$gfx = $_SESSION[gfx_path];
// if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "../gfx/";
if ($gfx == "gfx/" || $gfx == "gfx") $gfx = "gfx/";

	$fieldinfo = $db->query("SELECT a.*,b.*,c.name as regionname, c.faction as faction, c.status as status FROM stu_map as a LEFT JOIN stu_map_ftypes as b on a.type = b.type LEFT JOIN stu_map_regions as c ON a.region = c.id WHERE a.cx = ".$_GET["x"]." AND a.cy = ".$_GET["y"].";",4);
	// $fieldinfo = mysql_fetch_assoc($field);

	// $faction = rand(1,4)-1;
	$fac = $faction[$x][$y];
	

//	$factionadd = "<br><br>Zugehörigkeit gemäß Witch-Proklamation:<br><img src=\"".$gfx."/rassen/".$fac."s.gif\" /> <font color=".getfactioncolor($fac).">".getofficialfactionname($fac)."</font>";
	
// "<br><img src=\"".$gfx."/rassen/".$fac."s.gif\" /> <font color=".getfactioncolor($fac).">".getofficialfactionname($fac)."</font>";	

	$info = "<br><br><table>";
	

	$info .= "<tr><td>Region: </td><td>".$fieldinfo['regionname']."</td></tr>";
	$info .= "<tr><td>Zugehörigkeit: </td><td><img src=\"".$gfx."/rassen/".$fieldinfo['faction']."s.gif\" /> <font color=".getfactioncolor($fieldinfo['faction']).">".getofficialfactionname($fieldinfo['faction'])."</font></td></tr>";
	
	if ($fieldinfo['status'] == 'fixed')
		$info .= "<tr><td>Status:</td><td>Nicht einnehmbar</td></tr>";
	if ($fieldinfo['status'] == 'core')
		$info .= "<tr><td>Status:</td><td>Kernregion</td></tr>";
	
	
	$info .= "</table>";
	
	
	echo "<div style=\"border:1px solid #8897cf;background:#000000;padding:5px;\"><img src=\"".$gfx."/map/".$fieldinfo[type].".gif\" /> ".$_GET["x"]."|".$_GET["y"].": ".$fieldinfo[name]."".$info."</div>";	
	

	
?>